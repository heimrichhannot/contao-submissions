<?php

namespace HeimrichHannot\Submissions\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\Model\Collection;
use Doctrine\DBAL\Connection;
use HeimrichHannot\Submissions\Manager\DcaManager;
use HeimrichHannot\Submissions\Model\SubmissionArchiveModel;

readonly class SubmissionArchiveContainer
{
    public function __construct(
        private Connection $connection,
        private DcaManager $dcaManager,
    ) {}

    /** @noinspection PhpUnused */
    #[AsCallback(table: 'tl_submission_archive', target: 'fields.parentTable.options')]
    public function getParentTableOptions(): array
    {
        $dca = [];

        foreach ($GLOBALS['BE_MOD'] as /*$beModCategory =>*/ $beModList)
        {
            foreach ($beModList as /*$beModName =>*/ $beMod)
            {
                if (!\is_array($beMod) || !isset($beMod['tables']) || !\is_array($beMod['tables']))
                {
                    continue;
                }

                foreach ($beMod['tables'] as $table)
                {
                    $dca[] = $table;
                }
            }
        }

        $dca = \array_unique($dca);
        \asort($dca);
        return \array_values($dca);
    }

    #[AsCallback(table: 'tl_submission_archive', target: 'fields.parentField.options')]
    public function getParentFieldOptions(DataContainer $dc): array
    {
        if (!$dc->activeRecord->parentTable) {
            return [];
        }

        $fields = $this->dcaManager->getSubmissibleFields($dc->activeRecord->parentTable);

        return \array_combine($fields, $fields);
    }

    #[AsCallback(table: 'tl_submission_archive', target: 'fields.pid.options')]
    public function getPidOptions(DataContainer $dc)
    {
        $record = $dc->activeRecord;
        $pTable = $record->parentTable;
        $pField = $record->parentField;

        if (!$pTable || !$pField) {
            return [];
        }

        $archives = SubmissionArchiveModel::findByParentTable($pTable);
        if (!$archives instanceof Collection) {
            return [];
        }

        $pids = $archives->fetchEach('pid');

        if (false !== $pos = \array_search($record->pid, $pids)) {
            unset($pids[$pos]);
        }

        $sqlWhere = 'TRUE';

        if (!empty($pids))
        {
            $sqlPids = \array_map('\intval', \array_filter(\array_unique($pids)));
            $sqlPids = \implode(',', $sqlPids);
            $sqlWhere = "id NOT IN ($sqlPids)";
        }

        try
        {
            $items = $this->connection->executeQuery(<<<SQL
                SELECT id, $pField as field
                  FROM $pTable
                 WHERE $sqlWhere
            SQL)?->fetchAllAssociative();
        }
        catch (\Exception)
        {
            return [];
        }

        if (!$items) {
            return [];
        }

        $itemIds = \array_column($items, 'id');
        $itemFields = \array_column($items, 'field');

        return \array_combine($itemIds, $itemFields);
    }

    #[AsCallback(table: 'tl_submission_archive', target: 'fields.submissionFields.options')]
    public function getSubmissionFieldsOptions(): array
    {
        $fields = $this->dcaManager->getSubmissibleFields('tl_submission');

        return \array_combine($fields, $fields);
    }
}