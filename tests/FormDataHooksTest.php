<?php

declare(strict_types=1);

namespace HeimrichHannot\Submissions\Tests;

use Contao\CoreBundle\OptIn\OptIn;
use Contao\Database;
use Contao\Form;
use Contao\System;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use HeimrichHannot\FormTypeBundle\FormType\FormTypeCollection;
use HeimrichHannot\Submissions\EventListener\Contao\FormDataHooks;
use HeimrichHannot\Submissions\FormType\SubmissionType;
use HeimrichHannot\Submissions\Manager\NotificationManager;
use HeimrichHannot\Submissions\Manager\SimpleTokensManager;
use HeimrichHannot\Submissions\Manager\SubmissionDataManager;
use HeimrichHannot\UtilsBundle\EventListener\DcaField\DateAddedFieldListener;
use HeimrichHannot\UtilsBundle\Util\Utils;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class FormDataHooksTest extends TestCase
{
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function testStoresFormDataAndUploadReferencesThroughContao(): void
    {
        $url = getenv('SUBMISSIONS_TEST_DATABASE_URL');
        if (!$url) {
            self::markTestSkipped('Set SUBMISSIONS_TEST_DATABASE_URL to test the Contao storage hook.');
        }

        $connection = DriverManager::getConnection((new DsnParser([
            'mysql' => 'pdo_mysql',
        ]))->parse($url));
        $cache = sys_get_temp_dir() . '/submissions-test-' . bin2hex(random_bytes(6));
        mkdir($cache . '/contao/dca', 0777, true);
        mkdir($cache . '/contao/config', 0777, true);
        file_put_contents($cache . '/contao/config/config.php', '<?php');
        file_put_contents($cache . '/contao/dca/tl_submission.php', '<?php require ' . var_export(\dirname(__DIR__) . '/contao/dca/tl_submission.php', true) . ';');

        $container = new ContainerBuilder();
        $container->setParameter('kernel.project_dir', \dirname(__DIR__));
        $container->setParameter('kernel.cache_dir', $cache);
        $container->setParameter('kernel.debug', false);
        $container->set('request_stack', new RequestStack());
        $container->set('database_connection', $connection);
        System::setContainer($container);

        $connection->executeStatement('CREATE TEMPORARY TABLE tl_submission (id INT AUTO_INCREMENT PRIMARY KEY, pid INT, dateAdded INT, tstamp INT, uuid BINARY(16), email VARCHAR(64), huhSub_optInTokenId VARCHAR(32), document BINARY(16), legacyColumn VARCHAR(64), additionalData JSON NULL, additionalDataMeta JSON NULL)');

        try {
            $types = new FormTypeCollection($connection);
            $types->addType(new SubmissionType());
            $hook = new FormDataHooks(
                // Unused by storage; PHPUnit 10 cannot double readonly classes.
                (new \ReflectionClass(NotificationManager::class))->newInstanceWithoutConstructor(),
                $this->createStub(OptIn::class),
                $this->createStub(RouterInterface::class),
                $this->createStub(SimpleTokensManager::class),
                $this->createStub(Utils::class),
                $types,
                new SubmissionDataManager(),
            );
            $form = new class extends Form {
                public function __construct()
                {
                    $this->arrData = [
                        'id' => 1,
                        'formType' => SubmissionType::TYPE,
                        'huhSub_submissionArchive' => 12,
                        'huhSub_optIn' => false,
                    ];
                }
            };
            $uuid = '00112233-4455-1677-8899-aabbccddeeff';
            $submitted = [
                'uuid' => hex2bin('00112233445516778899aabbccddeeff'),
                'email' => 'test@example.org',
                'question' => 'Antwort',
                'legacyColumn' => 'without DCA',
                'document' => 'files/example.pdf',
                'attachment' => 'files/extra.pdf',
                'attachments' => serialize(['files/extra.pdf', null]),
            ];
            $files = [
                'document' => [
                    'uuid' => $uuid,
                ],
                'attachment' => [
                    'name' => 'Bewerbung.pdf',
                    'uuid' => $uuid,
                ],
                'attachments' => [
                    [
                        'uuid' => $uuid,
                    ],
                    [],
                ],
            ];
            $select = new \Contao\FormSelect([
                'name' => 'question',
                'type' => 'select',
                'options' => [[
                    'value' => 'Antwort',
                    'label' => 'Dynamische Antwort',
                ], [
                    'value' => 'other',
                    'label' => 'Nicht ausgewählt',
                ]],
            ]);
            $hook->onValidateFormField($select, 'auto_form_1', [], $form);
            $select->options = [[
                'value' => 'Antwort',
                'label' => 'Dynamische Antwort',
            ], [
                'value' => 'other',
                'label' => 'Nicht ausgewählt',
            ]];
            $multiple = new \Contao\FormSelect([
                'name' => 'interests',
                'type' => 'select',
                'multiple' => true,
                'options' => [[
                    'value' => 'a',
                    'label' => 'Musik',
                ], [
                    'value' => 'b',
                    'label' => 'Sport',
                ]],
            ]);
            $submitted['interests'] = ['a', 'b'];
            $hook->onValidateFormField($multiple, 'auto_form_1', [], $form);
            $submitted['birthday'] = 1731854100;
            $hook->onValidateFormField(new \Contao\FormText([
                'name' => 'birthday',
                'rgxp' => 'date',
            ]), 'auto_form_1', [], $form);
            $duplicate = clone $select;
            $duplicate->name = 'question_duplicate_1';
            $duplicate->value = 'Antwort';
            $hook->onValidateFormField($duplicate, 'auto_form_1', [], $form);
            $hook->onPrepareFormData($submitted, [
                'question' => 'Meine Frage',
                'interests[]' => 'Interessen',
            ], [], $form, $files);

            \Contao\Controller::loadDataContainer('tl_submission');
            (new DateAddedFieldListener($container))->onLoadDataContainer('tl_submission');
            $GLOBALS['TL_DCA']['tl_submission']['fields']['document'] = [
                'sql' => 'binary(16) NULL',
            ];
            self::assertTrue($GLOBALS['TL_DCA']['tl_submission']['fields']['additionalData']['eval']['noSubmissionField']);

            $stored = $hook->onStoreFormData($submitted, $form);
            Database::getInstance()->prepare('INSERT INTO tl_submission %s')->set($stored)->execute();
            $row = $connection->fetchAssociative('SELECT * FROM tl_submission');

            self::assertSame(12, (int) $row['pid']);
            self::assertGreaterThan(0, (int) $row['dateAdded']);
            self::assertSame('test@example.org', $row['email']);
            self::assertNull($row['legacyColumn']);
            self::assertSame($submitted['uuid'], $row['document']);
            self::assertSame([
                'question' => 'Antwort',
                'legacyColumn' => 'without DCA',
                'attachment' => $uuid,
                'attachments' => serialize([$uuid, null]),
                'interests' => ['a', 'b'],
                'birthday' => 1731854100,
            ], json_decode($row['additionalData'], true, flags: JSON_THROW_ON_ERROR));
            self::assertSame('files/extra.pdf', $submitted['attachment']);
            $meta = json_decode($row['additionalDataMeta'], true, flags: JSON_THROW_ON_ERROR);
            self::assertSame('date', $meta['birthday']['rgxp'] ?? null);
            self::assertSame('Meine Frage', $meta['question']['label']);
            self::assertSame([
                'a' => 'Musik',
                'b' => 'Sport',
            ], $meta['interests']['options']);
            self::assertSame('Interessen', $meta['interests']['label']);
            self::assertSame([
                'Antwort' => 'Dynamische Antwort',
            ], $meta['question_duplicate_1']['options']);
            self::assertSame('Bewerbung.pdf', $meta['attachment']['options'][$uuid]);
            self::assertSame([
                'Antwort' => 'Dynamische Antwort',
            ], $meta['question']['options']);
            $select->options = [[
                'value' => 'Antwort',
                'label' => 'Geändert',
            ]];
            self::assertSame('Dynamische Antwort', $meta['question']['options']['Antwort']);

            $form->formType = 'unrelated';
            self::assertSame($submitted, $hook->onStoreFormData($submitted, $form));
        } finally {
            $connection->executeStatement('DROP TEMPORARY TABLE tl_submission');
            $connection->close();
            unlink($cache . '/contao/dca/tl_submission.php');
            unlink($cache . '/contao/config/config.php');
            rmdir($cache . '/contao/dca');
            rmdir($cache . '/contao/config');
            rmdir($cache . '/contao');
            rmdir($cache);
        }
    }
}
