<?php

declare(strict_types=1);

namespace HeimrichHannot\Submissions\Tests\Backend;

use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\System;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use HeimrichHannot\Submissions\Controller\Backend\ExportController;
use HeimrichHannot\Submissions\Manager\AdditionalDataRenderer;
use HeimrichHannot\Submissions\Model\SubmissionArchiveModel;
use HeimrichHannot\UtilsBundle\Util\FormatterUtil;
use HeimrichHannot\UtilsBundle\Util\Utils;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\Translator;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class ExportControllerTest extends TestCase
{
    public function testStreamsAdditionalColumnsAcrossAllArchiveRecords(): void
    {
        $url = getenv('SUBMISSIONS_TEST_DATABASE_URL');
        if (!$url) {
            self::markTestSkipped('Requires SUBMISSIONS_TEST_DATABASE_URL.');
        }
        $connection = DriverManager::getConnection((new DsnParser([
            'mysql' => 'pdo_mysql',
        ]))->parse($url));
        $connection->executeStatement('CREATE TEMPORARY TABLE tl_submission (id INT, pid INT, email VARCHAR(100), dateAdded VARCHAR(20), additionalData JSON, additionalDataMeta JSON)');
        $insert = function (int $id, int $pid, array $data, array $meta) use ($connection): void {
            $connection->insert('tl_submission', [
                'id' => $id,
                'pid' => $pid,
                'email' => 'test@example.org',
                'dateAdded' => '123',
                'additionalData' => json_encode($data),
                'additionalDataMeta' => json_encode($meta),
            ]);
        };
        $insert(1, 7, [
            'topic' => 'a',
            'interests' => serialize(['m', 's']),
            'notes' => "Zeile 1; \"Zitat\"\nZeile 2\\",
            'lateCaption' => 'früh',
        ], [
            'topic' => [
                'label' => '',
                'options' => [
                    'a' => 'Technik',
                ],
            ],
            'interests' => [
                'label' => 'Interessen',
                'options' => [
                    'm' => 'Musik',
                    's' => 'Sport',
                ],
            ],
            'notes' => [
                'label' => 'Notizen',
            ],
        ]);
        $insert(2, 7, [
            'topic' => 'a',
            'other' => 'neu',
            'lateCaption' => 'spät',
            'participants' => [
                1 => [
                    'name' => 'Anna',
                ],
                2 => [
                    'name' => 'Ben',
                ],
            ],
        ], [
            'topic' => [
                'label' => 'Beratung',
                'options' => [
                    'a' => 'Neue Option',
                ],
            ],
            'lateCaption' => [
                'label' => 'Nachgereicht',
            ],
            'other' => [
                'label' => 'Beratung',
            ],
            'participants' => [
                'label' => 'Teilnehmer',
            ],
            'name' => [
                'label' => 'Vorname',
            ],
        ]);
        $insert(3, 99, [
            'secret' => 'hidden',
        ], [
            'secret' => [
                'label' => 'Secret',
            ],
        ]);
        $insert(4, 7, [
            'legacyField' => '=1+1',
            'topic' => 'a',
        ], [
            'topic' => [
                'label' => 'Umbenannt',
                'options' => [
                    'a' => 'Späte Option',
                ],
            ],
        ]);
        $translator = new Translator('en');
        $formatter = $this->createStub(FormatterUtil::class);
        // Existing DCA formatting is unrelated; additional-data formatting runs through the real implementation.
        $formatter->method('formatDcaFieldValue')->willReturnCallback(static fn ($dc, $key, $value) => $value);
        $locator = new ContainerBuilder();
        $locator->set(FormatterUtil::class, $formatter);
        $utils = new Utils($locator);
        $controller = new ExportController($connection, $utils, $translator, new AdditionalDataRenderer(new Environment(new ArrayLoader()), $translator));
        $auth = $this->createStub(AuthorizationCheckerInterface::class);
        $auth->method('isGranted')->willReturn(true);
        $container = new ContainerBuilder();
        $container->set('security.authorization_checker', $auth);
        $controller->setContainer($container);
        $archive = new class extends SubmissionArchiveModel {
            public function __construct()
            {
                $this->arrData = [
                    'id' => 7,
                    'allowExport' => true,
                    'submissionFields' => serialize(['email']),
                ];
            }

            public function disableExport(): void
            {
                $this->arrData['allowExport'] = false;
            }
        };
        $languages = new \ReflectionProperty(System::class, 'arrLanguageFiles');
        $previous = $languages->getValue();
        $language = $GLOBALS['TL_LANGUAGE'] ?? null;
        $GLOBALS['TL_LANGUAGE'] = 'en';
        $languages->setValue(null, [
            'tl_submission' => [
                'en' => 'en',
            ],
        ]);
        $dca = $GLOBALS['TL_DCA'] ?? [];
        $GLOBALS['TL_DCA']['tl_submission']['fields']['email']['label'] = ['Email'];
        $GLOBALS['TL_DCA']['tl_submission']['fields']['dateAdded']['label'] = ['Date'];
        try {
            $response = $controller($archive);
            // A submission arriving after header discovery belongs in the next export.
            $insert(5, 7, [
                'newField' => 'late',
            ], [
                'newField' => [
                    'label' => 'Late',
                ],
            ]);
            ob_start();
            try {
                $response->sendContent();
                $csv = ob_get_contents();
            } finally {
                ob_end_clean();
            }
            self::assertStringStartsWith("\xEF\xBB\xBF", $csv);
            self::assertSame('text/csv; charset=UTF-8', $response->headers->get('Content-Type'));
            $stream = fopen('php://memory', 'w+');
            fwrite($stream, substr($csv, 3));
            rewind($stream);
            $rows = [];
            while (false !== ($row = fgetcsv($stream, separator: ';', escape: ''))) {
                $rows[] = $row;
            }
            fclose($stream);
            self::assertSame(['Email', 'Date', 'Beratung', 'Interessen', 'Notizen', 'Nachgereicht', 'Beratung (other)', 'Teilnehmer', 'Legacy Field'], $rows[0]);
            self::assertSame(['test@example.org', '123', 'Technik', "Musik\nSport", "Zeile 1; \"Zitat\"\nZeile 2\\", 'früh', '', '', ''], $rows[1]);
            self::assertSame(['test@example.org', '123', 'Neue Option', '', '', 'spät', 'neu', "1. Vorname: Anna\n2. Vorname: Ben", ''], $rows[2]);
            self::assertSame(['test@example.org', '123', 'Späte Option', '', '', '', '', '', "'=1+1"], $rows[3]);
            self::assertCount(4, $rows);
            $archive->disableExport();
            $this->expectException(AccessDeniedException::class);
            $controller($archive);
        } finally {
            $languages->setValue(null, $previous);
            $GLOBALS['TL_LANGUAGE'] = $language;
            $GLOBALS['TL_DCA'] = $dca;
            $connection->executeStatement('DROP TEMPORARY TABLE tl_submission');
            $connection->close();
        }
    }
}
