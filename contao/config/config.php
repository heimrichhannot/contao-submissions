<?php

use HeimrichHannot\Submissions\Model\SubmissionArchiveModel;
use HeimrichHannot\Submissions\Model\SubmissionModel;

/**
 * Backend modules
 */
$GLOBALS['BE_MOD']['content']['huh_submissions'] = [
    'tables' => ['tl_submission_archive', 'tl_submission']
];


/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_submission']         = SubmissionModel::class;
$GLOBALS['TL_MODELS']['tl_submission_archive'] = SubmissionArchiveModel::class;


/**
 * Permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'submissionss';
$GLOBALS['TL_PERMISSIONS'][] = 'submissionsp';
