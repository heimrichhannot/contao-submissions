<?php

declare(strict_types=1);

namespace HeimrichHannot\Submissions\Tests;

use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\Form;
use Contao\FormFieldsetStart;
use Contao\FormFieldsetStop;
use Contao\FormText;
use Contao\Input;
use Contao\System;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use HeimrichHannot\FormTypeBundle\Event\StoreFormDataEvent;
use HeimrichHannot\Submissions\EventListener\Integration\FieldsetDuplicationListener;
use HeimrichHannot\Submissions\Manager\SubmissionDataManager;
use InspiredMinds\ContaoFieldsetDuplication\EventListener\FormHookListener;
use InspiredMinds\ContaoFieldsetDuplication\Helper\FieldHelper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class FieldsetDuplicationListenerTest extends TestCase
{
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    #[DataProvider('storagePipelines')]
    public function testGroupsValidatedDuplicatedWidgetsInsteadOfRawRequestValues(bool $nativeStoreHook): void
    {
        $database = null;
        if ($nativeStoreHook) {
            $url = getenv('SUBMISSIONS_TEST_DATABASE_URL');
            if (!$url) {
                self::markTestSkipped('Set SUBMISSIONS_TEST_DATABASE_URL to test the native fieldset storage hook.');
            }
            $database = DriverManager::getConnection((new DsnParser([
                'mysql' => 'pdo_mysql',
            ]))->parse($url));
            $database->executeStatement('CREATE TEMPORARY TABLE tl_submission (id INT PRIMARY KEY)');
        }
        $requestStack = new RequestStack();
        $requestStack->push(new Request([], [
            'FORM_SUBMIT' => 'auto_form_7',
            'message' => "\xFF",
            'message_duplicate_1' => '{{raw}}',
            'unrelated_duplicate_9' => 'unvalidated',
        ]));
        $cache = sys_get_temp_dir() . '/submissions-widgets-' . bin2hex(random_bytes(6));
        mkdir($cache . '/contao/config', 0777, true);
        file_put_contents($cache . '/contao/config/config.php', '<?php');
        $container = new ContainerBuilder();
        $container->setParameter('kernel.project_dir', \dirname(__DIR__));
        $container->setParameter('kernel.cache_dir', $cache);
        $container->setParameter('kernel.debug', false);
        $container->set('request_stack', $requestStack);
        if ($database) {
            $container->set('database_connection', $database);
        }
        $scopeMatcher = $this->createStub(ScopeMatcher::class);
        $scopeMatcher->method('isBackendRequest')->willReturn(false);
        $container->set('contao.routing.scope_matcher', $scopeMatcher);
        System::setContainer($container);

        try {
            $rows = [
                [
                    'id' => 1,
                    'sorting' => 128,
                    'type' => 'fieldsetStart',
                    'name' => 'details',
                    'allowDuplication' => true,
                ],
                [
                    'id' => 2,
                    'sorting' => 256,
                    'type' => 'text',
                    'name' => 'message',
                ],
                [
                    'id' => 3,
                    'sorting' => 384,
                    'type' => 'fieldsetStop',
                    'name' => '',
                ],
            ];
            $fields = [
                new FormFieldsetStart($rows[0]),
                new FormText($rows[1] + [
                    'decodeEntities' => true,
                ]),
                new FormFieldsetStop($rows[2]),
            ];
            $form = new class extends Form {
                public function __construct()
                {
                    $this->arrData = [
                        'id' => 7,
                        'targetTable' => 'tl_submission',
                    ];
                }
            };
            $helper = new FieldHelper();
            $fields = (new FormHookListener($requestStack, $helper))->onCompileFormFields($fields, 'auto_form_7', $form);
            $processed = [];

            foreach ($fields as $widget) {
                if ($widget instanceof FormText) {
                    $widget->validate();
                    self::assertFalse($widget->hasErrors());
                    $processed[$widget->name] = $widget->value;
                    Input::setPost($widget->name, null);
                }
            }

            self::assertSame(['message', 'message_duplicate_1'], array_keys($processed));
            $connection = $this->createStub(Connection::class);
            $connection->method('fetchAllAssociative')->willReturn($rows);
            $listener = new FieldsetDuplicationListener($requestStack, $connection, new EventDispatcher());
            $listener->setFieldHelper($helper);
            if ($nativeStoreHook) {
                $processed = $listener->onStoreFormData($processed, $form);
                $processed = (new FormHookListener($requestStack, $helper))->onStoreFormData($processed, $form);
                self::assertArrayNotHasKey('message_duplicate_1', $processed);
            }
            $event = new StoreFormDataEvent($processed, $form, []);
            $listener->onStoreFormDataEvent($event);

            self::assertSame([
                1 => [
                    'message' => "\u{FFFD}",
                ],
                2 => [
                    'message' => '&#123;&#123;raw&#125;&#125;',
                ],
            ], $event->getData()['details']);
            $stored = (new SubmissionDataManager())->prepareForStorage($event->getData(), []);
            self::assertSame($event->getData(), json_decode($stored['additionalData'], true, flags: JSON_THROW_ON_ERROR));
        } finally {
            if ($database) {
                $database->executeStatement('DROP TEMPORARY TABLE tl_submission');
                $database->close();
            }
            unlink($cache . '/contao/config/config.php');
            rmdir($cache . '/contao/config');
            rmdir($cache . '/contao');
            rmdir($cache);
        }
    }

    public static function storagePipelines(): iterable
    {
        yield 'validated values' => [false];
        yield 'native storage hook' => [true];
    }
}
