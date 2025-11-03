<?php

namespace HeimrichHannot\Submissions\Migration;

use Contao\CoreBundle\Migration\AbstractMigration;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

class V1ToV2Migration extends AbstractMigration
{
    public const TL_FORM_REFACTOR_NAMES_MAP = [
        'storeAsSubmission' => 'huhSub_storeSubmission',
        'submissionArchive' => 'huhSub_submissionArchive',
        'huhSubAddOptIn' => 'huhSub_optIn',
        'huhSubOptInNotification' => 'huhSub_optInNotification',
        'huhSubOptInJumpTo' => 'huhSub_optInJumpTo',
        'huhSubOptInTokenInvalidJumpTo' => 'huhSub_optInTokenInvalidJumpTo',
        'huhSubOptInField' => 'huhSub_optInField',
    ];

    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function shouldRun(): bool
    {
        $schemaManger = $this->connection->createSchemaManager();

        if (!$schemaManger->tablesExist(['tl_form'])) {
            return false;
        }

        $columns = $schemaManger->listTableColumns('tl_form');

        foreach (self::TL_FORM_REFACTOR_NAMES_MAP as $oldName => $newName) {
            if (isset($columns[$oldName]) || isset($columns[\strtolower($oldName)])) {
                return true;
            }
        }

        return false;
    }

    public function run(): MigrationResult
    {
        $schemaManger = $this->connection->createSchemaManager();

        $columns = $schemaManger->listTableColumns('tl_form');

        foreach (self::TL_FORM_REFACTOR_NAMES_MAP as $oldName => $newName) {
            $oldCol = $columns[$oldName] ?? $columns[\strtolower($oldName)] ?? null;
            $newCol = $columns[$newName] ?? $columns[\strtolower($newName)] ?? null;

            if (!$oldCol || $newCol) {
                continue;
            }

            $sqlDeclation = $oldCol->getType()->getSQLDeclaration(
                $oldCol->toArray(),
                $this->connection->getDatabasePlatform()
            );

            $this->connection->executeStatement(
                \sprintf(
                    'ALTER TABLE tl_form CHANGE %s %s %s',
                    $oldName,
                    $newName,
                    $sqlDeclation
                )
            );
        }

        return $this->createResult(
            true,
            'Migrated tl_form table'
        );
    }
}
