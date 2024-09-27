<?php

use HeimrichHannot\Submissions\Model\SubmissionArchiveModel;
use HeimrichHannot\Submissions\Model\SubmissionModel;

$GLOBALS['BE_MOD']['content']['huh_submissions'] = [
    'tables' => ['tl_submission_archive', 'tl_submission'],
    'icon' => 'bundles/heimrichhannotsubmissions/img/icon.png',
];

$GLOBALS['TL_MODELS']['tl_submission']         = SubmissionModel::class;
$GLOBALS['TL_MODELS']['tl_submission_archive'] = SubmissionArchiveModel::class;
