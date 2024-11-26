<?php

namespace HeimrichHannot\Submissions\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\Model\Collection;
use Doctrine\DBAL\Connection;
use HeimrichHannot\Submissions\Model\SubmissionArchiveModel;
use HeimrichHannot\UtilsBundle\Util\DcaUtil\GetDcaFieldsOptions;
use HeimrichHannot\UtilsBundle\Util\Utils;

readonly class SubmissionArchiveContainer
{
    public function __construct(
        private Connection $connection,
        private Utils      $utils
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

        return $this->utils->dca()->getDcaFields(
            $dc->activeRecord->parentTable,
            GetDcaFieldsOptions::create()
                ->setAllowedInputTypes(['text'])
        );
    }

    #[AsCallback(table: 'tl_submission_archive', target: 'fields.pid.options')]
    public function getPidOptions(\DataContainer $dc)
    {
        if (!$dc->activeRecord->parentTable || !$dc->activeRecord->parentField) {
            return [];
        }

        $archives = SubmissionArchiveModel::findByParentTable($dc->activeRecord->parentTable);
        if (!$archives instanceof Collection) {
            return [];
        }

        $pids = $archives->fetchEach('pid');

        if (false !== $pos = \array_search($dc->activeRecord->pid, $pids)) {
            unset($pids[$pos]);
        }

        $sqlWhere = 'TRUE';

        if (!empty($pids))
        {
            $sqlPids = \array_map('\intval', \array_filter(\array_unique($pids)));
            $sqlPids = \implode(',', $sqlPids);
            $sqlWhere = "id NOT IN ($sqlPids)";
        }

        try {
            $items = $this->connection->executeQuery(<<<SQL
                SELECT id, {$dc->activeRecord->parentField} 
                  FROM {$dc->activeRecord->parentTable} 
                 WHERE $sqlWhere
            SQL)?->fetchAllAssociative();
        } catch (\Exception) {
            return [];
        }

        if (!$items) {
            return [];
        }

        $itemIds = \array_column($items, 'id');
        $itemFields = \array_column($items, $dc->activeRecord->parentField);

        return \array_combine($itemIds, $itemFields);
    }

    #[AsCallback(table: 'tl_submission_archive', target: 'fields.submissionFields.options')]
    public function getSubmissionFieldsOptions(): array
    {
        $fields = $this->utils->dca()->getDcaFields(
            'tl_submission',
            GetDcaFieldsOptions::create()
        );

        // remove fields that are not allowed in submissions
        $noSubmissionFields = $this->utils->dca()->getDcaFields(
            'tl_submission',
            GetDcaFieldsOptions::create()
                ->setEvalConditions(['noSubmissionField' => true])
        );

        return \array_diff($fields, $noSubmissionFields);
    }
}