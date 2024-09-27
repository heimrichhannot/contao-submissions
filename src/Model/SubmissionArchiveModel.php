<?php

namespace HeimrichHannot\Submissions\Model;

use Contao\Model;
use Contao\Model\Collection;
use Contao\StringUtil;

class SubmissionArchiveModel extends Model
{
    protected static $strTable = 'tl_submission_archive';

    public static function findByParent(string $table, int $pid): Collection|static|array|null
    {
        return static::findBy(['parentTable=?', 'pid=?'], [$table, $pid]);
    }

    public static function findByParentTable(string $table): Collection|static|array|null
    {
        return static::findBy('parentTable', $table);
    }

    public static function getSubmissionFieldsByParent(string $table, int $pid): ?array
    {
        $submissionArchiveModel = static::findByParent($table, $pid);

        if ($submissionArchiveModel !== null)
        {
            return StringUtil::deserialize($submissionArchiveModel->submissionFields, true);
        }

        return null;
    }

    public static function getParentEntity(int $archiveId): ?Model
    {
        $archiveModel = static::findByPk($archiveId);

        if ($archiveModel === null)
        {
            return null;
        }

        if (!$archiveModel->parentTable || !$archiveModel->pid)
        {
            return null;
        }

        $modelClass = Model::getClassFromTable($archiveModel->parentTable);

        if (!\class_exists($modelClass))
        {
            return null;
        }

        return $modelClass::findByPk($archiveModel->pid);
    }
}