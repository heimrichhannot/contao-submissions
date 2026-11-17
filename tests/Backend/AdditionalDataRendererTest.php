<?php

declare(strict_types=1);

namespace HeimrichHannot\Submissions\Tests\Backend;

use HeimrichHannot\Submissions\Manager\AdditionalDataRenderer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class AdditionalDataRendererTest extends TestCase
{
    private function renderer(): AdditionalDataRenderer
    {
        $loader = new FilesystemLoader();
        $loader->addPath(\dirname(__DIR__, 2) . '/contao/templates', 'Contao');
        $twig = new Environment($loader, [
            'autoescape' => 'html',
            'strict_variables' => true,
        ]);
        $translator = new Translator('de');
        $translator->addLoader('array', new ArrayLoader());
        $translator->addResource('array', require \dirname(__DIR__, 2) . '/translations/contao_tl_submission.de.php', 'de', 'contao_tl_submission');

        return new AdditionalDataRenderer($twig, $translator);
    }

    public function testRendersSnapshotsListsTablesAndEscapedTextWithRealTwig(): void
    {
        $html = $this->renderer()->render(json_encode([
            'topic' => 'tech',
            'interests' => serialize(['a', 'b']),
            'people' => [
                1 => [
                    'firstName' => '<Anna>',
                ],
                2 => [
                    'firstName' => 'Ben',
                ],
            ],
            'notes' => "<script>alert(1)</script>\n{{danger}}",
            'file' => '00112233-4455-1677-8899-aabbccddeeff',
        ]), json_encode([
            'file' => [
                'label' => 'Anhang',
                'options' => [
                    '00112233-4455-1677-8899-aabbccddeeff' => 'Bewerbung.pdf',
                ],
            ],
            'topic' => [
                'label' => 'Beratung',
                'type' => 'select',
                'options' => [
                    'tech' => 'Technische Beratung',
                ],
            ],
            'interests' => [
                'label' => 'Interessen',
                'options' => [
                    'a' => 'Musik',
                    'b' => 'Sport',
                ],
            ],
            'people' => [
                'label' => 'Teilnehmer',
            ],
            'firstName' => [
                'label' => 'Vorname',
            ],
        ]));

        foreach (['Beratung', 'Technische Beratung', 'Musik', 'Sport', 'Teilnehmer', 'Vorname', '&lt;Anna&gt;', 'Bewerbung.pdf'] as $text) {
            self::assertStringContainsString($text, $html);
        }
        self::assertStringContainsString('<thead>', $html);
        self::assertStringContainsString('<ul ', $html);
        self::assertStringNotContainsString('<script>', $html);
        self::assertStringContainsString('&lt;script&gt;', $html);
        self::assertStringContainsString('{{danger}}', $html);
        self::assertStringNotContainsString('<textarea', $html);
        self::assertStringNotContainsString('<input', $html);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function testDisplaysRealContaoEncodedInputAsSafeReadableText(): void
    {
        $stack = new \Symfony\Component\HttpFoundation\RequestStack();
        $stack->push(new \Symfony\Component\HttpFoundation\Request([], [
            'answer' => 'a < b {{foo}} <script>x</script>',
        ]));
        $container = new \Symfony\Component\DependencyInjection\ContainerBuilder();
        $container->set('request_stack', $stack);
        $scope = $this->createStub(\Contao\CoreBundle\Routing\ScopeMatcher::class);
        $scope->method('isBackendRequest')->willReturn(false);
        $container->set('contao.routing.scope_matcher', $scope);
        \Contao\System::setContainer($container);
        $html = $this->renderer()->render([
            'answer' => \Contao\Input::post('answer', true),
        ], null);
        self::assertStringContainsString('a &lt; b {{foo}}', $html);
        self::assertStringContainsString('&lt;script&gt;', $html);
        self::assertStringNotContainsString('<script>', $html);
        $encoded = \Contao\Input::post('answer', true);
        $caption = $this->renderer()->render([
            'choice' => $encoded,
        ], [
            'choice' => [
                'options' => [
                    $encoded => 'Gespeicherte Auswahl',
                ],
            ],
        ]);
        self::assertStringContainsString('Gespeicherte Auswahl', $caption);
    }

    public function testCallbackRendersCurrentRecordAndBlankOptionsStayEmpty(): void
    {
        $dc = $this->createStub(\Contao\DataContainer::class);
        $dc->method('getCurrentRecord')->willReturn([
            'additionalData' => '{"choice":""}',
            'additionalDataMeta' => '{"choice":{"label":"Auswahl","options":{"":"Bitte wählen"}}}',
        ]);
        $callback = new \HeimrichHannot\Submissions\EventListener\DataContainer\Submission\FieldsAdditionalDataViewInputFieldListener($this->renderer());
        $html = $callback($dc);
        self::assertStringContainsString('Auswahl', $html);
        self::assertStringContainsString('—', $html);
        self::assertStringNotContainsString('Bitte wählen', $html);
    }

    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function testFormatsDateSnapshotsThroughRealContaoAndTwig(): void
    {
        date_default_timezone_set('UTC');
        $GLOBALS['TL_CONFIG']['dateFormat'] = 'd.m.Y';
        $GLOBALS['TL_CONFIG']['timeFormat'] = 'H:i';
        $GLOBALS['TL_CONFIG']['datimFormat'] = 'd.m.Y H:i';
        $timestamp = (new \Contao\Date('17.11.2024 14:35', 'd.m.Y H:i'))->tstamp;
        $data = [
            'birthday' => $timestamp,
            'arrival' => $timestamp,
            'appointment' => $timestamp,
            'number' => $timestamp,
        ];
        $metadata = [
            'birthday' => [
                'rgxp' => 'date',
            ],
            'arrival' => [
                'rgxp' => 'time',
            ],
            'appointment' => [
                'rgxp' => 'datim',
            ],
        ];
        $html = $this->renderer()->render($data, $metadata);
        self::assertStringContainsString('>17.11.2024</span>', $html);
        self::assertStringContainsString('>14:35</span>', $html);
        self::assertStringContainsString('>17.11.2024 14:35</span>', $html);
        self::assertStringContainsString('>' . $timestamp . '</span>', $html);
    }

    public function testHandlesOldEmptyAndInvalidData(): void
    {
        $renderer = $this->renderer();
        self::assertSame('', $renderer->render(null, null));
        self::assertSame('', $renderer->render('{}', null));
        $html = $renderer->render('{"someField": "0", "empty": null, "boolean": false}', 'broken');
        self::assertStringContainsString('Some Field', $html);
        self::assertStringContainsString('0', $html);
        self::assertStringContainsString('Nein', $html);
        self::assertStringContainsString('—', $html);
        self::assertStringContainsString('konnten nicht gelesen werden', $renderer->render('broken', null));
        self::assertStringContainsString('konnten nicht gelesen werden', $renderer->render('0', null));
        self::assertStringContainsString('Some Field', $renderer->render([
            'someField' => [
                'child' => 'value',
            ],
        ], [
            'child' => 'invalid',
        ]));
    }
}
