<?php

/**
* Backend modules
*/
$GLOBALS['BE_MOD']['content']['submission'] = [
    'tables' => ['tl_submission_archive', 'tl_submission'],
    'icon'   => 'system/modules/submissions/assets/img/icon_submission.png',
    'send_confirmation' => ['HeimrichHannot\Submissions\SubmissionModel', 'sendConfirmationNotificationBe']
];

if (in_array('exporter', \ModuleLoader::getActive()))
{
    $GLOBALS['BE_MOD']['content']['submission']['export_csv'] = \HeimrichHannot\Exporter\ModuleExporter::getBackendModule();
    $GLOBALS['BE_MOD']['content']['submission']['export_xls'] = \HeimrichHannot\Exporter\ModuleExporter::getBackendModule();
}

/**
 * Notification Center Notification Types
 */
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE'] = array_merge_recursive(
    (array) $GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE'],
    [
		\HeimrichHannot\Submissions\Submissions::NOTIFICATION_TYPE_SUBMISSIONS => [
            \HeimrichHannot\Submissions\Submissions::NOTIFICATION_TYPE_FORM_SUBMISSION => [
                'recipients'           => ['form_value_*', 'form_plain_*', 'admin_email'],
                'email_subject'        => ['form_value_*', 'form_plain_*', 'admin_email', 'env_*', 'page_*', 'user_*', 'date', 'last_update'],
                'email_text'           => [
                    'formsubmission', 'formsubmission_all', 'form_submission_*',
                    'form_value_*', 'form_plain_*', 'salutation_submission', 'admin_email', 'env_*', 'page_*', 'user_*', 'date', 'last_update'
                ],
                'email_html'           => [
                    'formsubmission', 'formsubmission_all', 'form_submission_*',
                    'form_value_*', 'form_plain_*', 'salutation_submission', 'admin_email', 'env_*', 'page_*', 'user_*', 'date', 'last_update'
                ],
                'file_name'            => ['form_value_*', 'form_plain_*', 'admin_email'],
                'file_content'         => ['form_value_*', 'form_plain_*', 'admin_email'],
                'email_sender_name'    => ['form_value_*', 'form_plain_*', 'admin_email'],
                'email_sender_address' => ['form_value_*', 'form_plain_*', 'admin_email'],
                'email_recipient_cc'   => ['form_value_*', 'form_plain_*', 'admin_email'],
                'email_recipient_bcc'  => ['form_value_*', 'form_plain_*', 'admin_email'],
                'email_replyTo'        => ['form_value_*', 'form_plain_*', 'admin_email'],
                'attachment_tokens'    => ['form_value_*', 'form_plain_*'],
            ],
            \HeimrichHannot\Submissions\Submissions::NOTIFICATION_TYPE_CONFIRMATION => [
                'recipients'           => ['form_value_*', 'form_plain_*', 'admin_email'],
                'email_subject'        => [
                    'form_value_*',
                    'form_plain_*', 'admin_email', 'env_*', 'page_*', 'user_*', 'date', 'last_update'
                ],
                'email_text'           => [
                    'formsubmission',
                    'formsubmission_all', 'form_submission_*', 'form_value_*', 'form_plain_*',
                    'salutation_submission', 'admin_email', 'env_*', 'page_*', 'user_*', 'date', 'last_update'
                ],
                'email_html'           => [
                    'formsubmission',
                    'formsubmission_all', 'form_submission_*', 'form_value_*', 'form_plain_*',
                    'salutation_submission', 'admin_email', 'env_*', 'page_*', 'user_*', 'date', 'last_update'
                ],
                'file_name'            => ['event_*', 'form_value_*', 'form_plain_*', 'admin_email'],
                'file_content'         => ['event_*', 'form_value_*', 'form_plain_*', 'admin_email'],
                'email_sender_name'    => ['event_*', 'form_value_*', 'form_plain_*', 'admin_email'],
                'email_sender_address' => ['event_*', 'form_value_*', 'form_plain_*', 'admin_email'],
                'email_recipient_cc'   => ['event_*', 'form_value_*', 'form_plain_*', 'admin_email'],
                'email_recipient_bcc'  => ['event_*', 'form_value_*', 'form_plain_*', 'admin_email'],
                'email_replyTo'        => ['event_*', 'form_value_*', 'form_plain_*', 'admin_email'],
                'attachment_tokens'    => ['confirmation_pdf', 'event_*', 'form_value_*', 'form_plain_*'],
            ]
        ]
    ]
);

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_submission'] = '\HeimrichHannot\Submissions\SubmissionModel';
$GLOBALS['TL_MODELS']['tl_submission_archive'] = '\HeimrichHannot\Submissions\SubmissionArchiveModel';

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['loadDataContainer']['submissions_setPTableForDelete'] = ['HeimrichHannot\Submissions\Backend\SubmissionArchiveBackend', 'setPTableForDelete'];

/**
 * Add permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'submissionss';
$GLOBALS['TL_PERMISSIONS'][] = 'submissionsp';