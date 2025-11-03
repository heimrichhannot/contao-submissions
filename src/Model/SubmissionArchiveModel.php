<?php

namespace HeimrichHannot\Submissions\Model;

use Contao\Model;

/**
 * @property int    $id
 * @property int    $tstamp
 * @property string $title
 * @property string $submissionFields
 * @property int    $dateAdded
 * @property string $titlePattern
 * @property bool   $allowExport
 */
class SubmissionArchiveModel extends Model
{
    protected static $strTable = 'tl_submission_archive';
}
