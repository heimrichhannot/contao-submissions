<?php

declare(strict_types=1);

namespace HeimrichHannot\Submissions\Tests\Manager;

use HeimrichHannot\Submissions\Manager\SubmissionDataManager;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SubmissionDataManagerTest extends TestCase
{
    public function testStoresOnlyUnmappedFieldsInJson(): void
    {
        $data = [
            'email' => 'test@example.org',
            'published' => '1',
            'virtual' => 'value',
            'question' => 'Antwort',
        ];
        $fields = [
            'email' => [
                'sql' => 'text',
            ],
            'published' => [
                'eval' => [
                    'noSubmissionField' => true,
                ],
            ],
            'virtual' => [],
        ];

        $stored = (new SubmissionDataManager())->prepareForStorage($data, $fields);

        self::assertSame('test@example.org', $stored['email']);
        self::assertSame('1', $stored['published']);
        self::assertSame('value', $stored['virtual']);
        self::assertArrayNotHasKey('question', $stored);
        self::assertSame([
            'question' => 'Antwort',
        ], json_decode($stored['additionalData'], true, flags: JSON_THROW_ON_ERROR));
        self::assertSame('Antwort', $data['question']);
    }

    public function testIgnoresSubmittedStorageColumnAndStoresNullWithoutAdditionalFields(): void
    {
        $stored = (new SubmissionDataManager())->prepareForStorage(
            [
                'email' => 'test@example.org',
                'additionalData' => '{"injected":true}',
                'additionalDataMeta' => '{"fake":true}',
                'additionalDataView' => 'fake',
            ],
            [
                'email' => [],
                'additionalData' => [],
            ],
        );

        self::assertSame([
            'email' => 'test@example.org',
            'additionalData' => null,
        ], $stored);
    }

    public function testPreservesEmptyValuesAndEncodesNumericFieldNamesAsObject(): void
    {
        $data = [
            '' => '',
            '0' => '0',
            'empty' => null,
            'false' => false,
            'number' => 42,
            'unicode' => 'Grüße 🌍',
        ];
        $stored = (new SubmissionDataManager())->prepareForStorage($data, []);

        self::assertStringStartsWith('{', $stored['additionalData']);
        self::assertSame($data, json_decode($stored['additionalData'], true, flags: JSON_THROW_ON_ERROR));

        $numericFields = (new SubmissionDataManager())->prepareForStorage(['first', 'second'], []);
        self::assertStringStartsWith('{', $numericFields['additionalData']);
    }

    #[DataProvider('uuidValues')]
    public function testNormalizesBinaryUuidsBeforeEncoding(mixed $value, mixed $expected): void
    {
        $stored = (new SubmissionDataManager())->prepareForStorage([
            'freeField' => $value,
        ], []);

        self::assertSame([
            'freeField' => $expected,
        ], json_decode($stored['additionalData'], true, flags: JSON_THROW_ON_ERROR));
    }

    public static function uuidValues(): iterable
    {
        $uuid = '00112233-4455-1677-8899-aabbccddeeff';
        $binary = hex2bin('00112233445516778899aabbccddeeff');

        yield 'binary' => [$binary, $uuid];
        yield 'string' => [$uuid, $uuid];
        yield 'ordinary string with 16 bytes' => ['abcdefghijklmnop', 'abcdefghijklmnop'];
        yield 'nested arrays' => [[
            'files' => [
                2 => $binary,
                'empty' => null,
            ],
        ], [
            'files' => [
                2 => $uuid,
                'empty' => null,
            ],
        ]];
        yield 'serialized arrays' => [serialize([$binary]), serialize([$uuid])];
        yield 'nested serialized arrays' => [
            serialize([
                'rows' => [serialize([$binary])],
            ]), serialize([
                'rows' => [serialize([$uuid])],
            ])];
        yield 'serialized array in array' => [[
            'files' => serialize([$binary]),
        ], [
            'files' => serialize([$uuid]),
        ]];
        yield 'empty serialized array' => ['a:0:{}', 'a:0:{}'];
    }

    public function testLeavesMappedBinaryValuesUntouched(): void
    {
        $binary = hex2bin('00112233445516778899aabbccddeeff');
        $stored = (new SubmissionDataManager())->prepareForStorage([
            'file' => $binary,
        ], [
            'file' => [],
        ]);

        self::assertSame([
            'file' => $binary,
            'additionalData' => null,
        ], $stored);
    }

    public function testRejectsUnencodableAdditionalValues(): void
    {
        $this->expectException(\JsonException::class);

        (new SubmissionDataManager())->prepareForStorage([
            'invalid' => "\xFF",
        ], []);
    }
}
