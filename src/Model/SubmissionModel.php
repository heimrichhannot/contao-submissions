<?php

namespace HeimrichHannot\Submissions\Model;

use Contao\CoreBundle\OptIn\OptInTokenInterface;
use Contao\Model;
use Contao\System;
use HeimrichHannot\Submissions\Manager\TokenManager;

/**
 * @property int $id
 * @property int $tstamp
 * @property int $dateAdded
 */
class SubmissionModel extends Model
{
    protected static $strTable = 'tl_submission';

    public function getArchive(): SubmissionArchiveModel|Model|null
    {
        return SubmissionArchiveModel::findByPk($this->pid);
    }

    public static function findOneByOptInToken(OptInTokenInterface $token): Model|null
    {
        return static::findOneBy('huhSub_optInTokenId', $token->getIdentifier());
    }
}