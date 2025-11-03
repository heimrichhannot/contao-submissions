<?php

namespace HeimrichHannot\Submissions\Model;

use Contao\CoreBundle\OptIn\OptInTokenInterface;
use Contao\Model;

/**
 * @property int $id
 * @property int $tstamp
 * @property int $dateAdded
 */
class SubmissionModel extends Model
{
    protected static $strTable = 'tl_submission';

    public function archive(): SubmissionArchiveModel|Model|null
    {
        return $this->getRelated('pid');
    }

    public static function findOneByOptInToken(OptInTokenInterface $token): ?Model
    {
        return static::findOneBy('huhSub_optInTokenId', $token->getIdentifier());
    }
}
