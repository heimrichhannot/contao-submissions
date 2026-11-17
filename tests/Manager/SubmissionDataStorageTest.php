<?php

declare(strict_types=1);

namespace HeimrichHannot\Submissions\Tests\Manager;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use HeimrichHannot\Submissions\Manager\SubmissionDataManager;
use PHPUnit\Framework\TestCase;

class SubmissionDataStorageTest extends TestCase
{
    public function testJsonColumnRoundTrip(): void
    {
        $url = getenv('SUBMISSIONS_TEST_DATABASE_URL');
        if (!$url) {
            self::markTestSkipped('Set SUBMISSIONS_TEST_DATABASE_URL to test storage in a temporary database table.');
        }

        $connection = DriverManager::getConnection((new DsnParser([
            'mysql' => 'pdo_mysql',
        ]))->parse($url));
        $jsonType = $connection->getDatabasePlatform()->getJsonTypeDeclarationSQL([
            'notnull' => false,
        ]);
        $connection->executeStatement('CREATE TEMPORARY TABLE submission_json_test (email VARCHAR(64), additionalData ' . $jsonType . ' NULL)');

        try {
            $data = (new SubmissionDataManager())->prepareForStorage(
                [
                    'email' => 'test@example.org',
                    'file' => hex2bin('00112233445516778899aabbccddeeff'),
                ],
                [
                    'email' => [],
                ],
            );
            $connection->insert('submission_json_test', $data);
            $row = $connection->fetchAssociative('SELECT * FROM submission_json_test');

            self::assertSame('test@example.org', $row['email']);
            self::assertSame([
                'file' => '00112233-4455-1677-8899-aabbccddeeff',
            ], json_decode($row['additionalData'], true, flags: JSON_THROW_ON_ERROR));
        } finally {
            $connection->executeStatement('DROP TEMPORARY TABLE submission_json_test');
            $connection->close();
        }
    }
}
