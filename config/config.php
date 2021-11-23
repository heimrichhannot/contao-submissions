<?php

/**
 * Backend modules
 */
$GLOBALS['BE_MOD']['content']['submission'] = [
    'tables'            => ['tl_submission_archive', 'tl_submission'],
    'icon'              => 'system/modules/submissions/assets/img/icon_submission.png',
    'send_confirmation' => ['HeimrichHannot\Submissions\SubmissionModel', 'sendConfirmationNotificationBe']
];

if (in_array('multifileupload', \ModuleLoader::getActive())) {
    $GLOBALS['BE_MOD']['content']['submission']['export_csv'] = \HeimrichHannot\Exporter\ModuleExporter::getBackendModule();
    $GLOBALS['BE_MOD']['content']['submission']['export_xls'] = \HeimrichHannot\Exporter\ModuleExporter::getBackendModule();
} elseif (version_compare(VERSION, '4.1', '>=') && in_array(\HeimrichHannot\ContaoExporterBundle\HeimrichHannotContaoExporterBundle::class,
        \Contao\System::getContainer()->getParameter('kernel.bundles'), true)) {
    $GLOBALS['BE_MOD']['content']['submission']['export_csv'] = ['huh.exporter.action.backendexport', 'export'];
    $GLOBALS['BE_MOD']['content']['submission']['export_xls'] = ['huh.exporter.action.backendexport', 'export'];
}


/**
 * Notification Center Notification Types
 */
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE'] = array_merge_recursive(
    (array)$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE'],
    [
        \HeimrichHannot\Submissions\Submissions::NOTIFICATION_TYPE_SUBMISSIONS => [
            \HeimrichHannot\Submissions\Submissions::NOTIFICATION_TYPE_FORM_SUBMISSION => [
                'recipients'           => ['form_value_*', 'form_plain_*', 'admin_email'],
                'email_subject'        => ['form_value_*', 'form_plain_*', 'admin_email', 'env_*', 'page_*', 'user_*', 'date', 'last_update'],
                'email_text'           => [
                    'formsubmission',
                    'formsubmission_all',
                    'form_submission_*',
                    'form_value_*',
                    'form_plain_*',
                    'salutation_submission',
                    'admin_email',
                    'env_*',
                    'page_*',
                    'user_*',
                    'date',
                    'last_update'
                ],
                'email_html'           => [
                    'formsubmission',
                    'formsubmission_all',
                    'form_submission_*',
                    'form_value_*',
                    'form_plain_*',
                    'salutation_submission',
                    'admin_email',
                    'env_*',
                    'page_*',
                    'user_*',
                    'date',
                    'last_update'
                ],
                'file_name'            => ['form_value_*', 'form_plain_*', 'admin_email'],
                'file_content'         => ['form_value_*', 'form_plain_*', 'admin_email'],
                'email_sender_name'    => ['form_value_*', 'form_plain_*', 'admin_email'],
                'email_sender_address' => ['form_value_*', 'form_plain_*', 'admin_email'],
                'email_recipient_cc'   => ['form_value_*', 'form_plain_*', 'admin_email'],
                'email_recipient_bcc'  => ['form_value_*', 'form_plain_*', 'admin_email'],
                'email_replyTo'        => ['form_value_*', 'form_plain_*', 'admin_email'],
                'attachment_tokens'    => ['form_value_*', 'form_plain_*', 'ics_attachment_token'],
            ],
            \HeimrichHannot\Submissions\Submissions::NOTIFICATION_TYPE_CONFIRMATION    => [
                'recipients'            => ['form_value_*', 'form_plain_*', 'admin_email'],
                'email_subject'         => [
                    'form_value_*',
                    'form_plain_*',
                    'admin_email',
                    'env_*',
                    'page_*',
                    'user_*',
                    'date',
                    'last_update'
                ],
                'email_text'            => [
                    'formsubmission',
                    'formsubmission_all',
                    'form_submission_*',
                    'form_value_*',
                    'form_plain_*',
                    'salutation_submission',
                    'admin_email',
                    'env_*',
                    'page_*',
                    'user_*',
                    'date',
                    'last_update'
                ],
                'email_html'            => [
                    'formsubmission',
                    'formsubmission_all',
                    'form_submission_*',
                    'form_value_*',
                    'form_plain_*',
                    'salutation_submission',
                    'admin_email',
                    'env_*',
                    'page_*',
                    'user_*',
                    'date',
                    'last_update'
                ],
                'file_name'             => ['event_*', 'form_value_*', 'form_plain_*', 'admin_email'],
                'file_content'          => ['event_*', 'form_value_*', 'form_plain_*', 'admin_email'],
                'email_sender_name'     => ['event_*', 'form_value_*', 'form_plain_*', 'admin_email'],
                'email_sender_address'  => ['event_*', 'form_value_*', 'form_plain_*', 'admin_email'],
                'email_recipient_cc'    => ['event_*', 'form_value_*', 'form_plain_*', 'admin_email'],
                'email_recipient_bcc'   => ['event_*', 'form_value_*', 'form_plain_*', 'admin_email'],
                'email_replyTo'         => ['event_*', 'form_value_*', 'form_plain_*', 'admin_email'],
                'attachment_tokens'     => ['confirmation_pdf', 'event_*', 'form_value_*', 'form_plain_*', 'ics_attachment_token'],
                'ics_title_field'       => ['event_*', 'form_value_*', 'form_plain_*'],
                'ics_description_field' => ['event_*', 'form_value_*', 'form_plain_*'],
                'ics_street_field'      => ['event_*', 'form_value_*', 'form_plain_*'],
                'ics_postal_field'      => ['event_*', 'form_value_*', 'form_plain_*'],
                'ics_city_field'        => ['event_*', 'form_value_*', 'form_plain_*'],
                'ics_country_field'     => ['event_*', 'form_value_*', 'form_plain_*'],
                'ics_location_field'    => ['event_*', 'form_value_*', 'form_plain_*'],
                'ics_url_field'         => ['event_*', 'form_value_*', 'form_plain_*'],
                'ics_start_date_field'  => ['event_*', 'form_value_*', 'form_plain_*'],
                'ics_end_date_field'    => ['event_*', 'form_value_*', 'form_plain_*'],
                'ics_add_time_field'    => ['event_*', 'form_value_*', 'form_plain_*'],
                'ics_start_time_field'  => ['event_*', 'form_value_*', 'form_plain_*'],
                'ics_end_time_field'    => ['event_*', 'form_value_*', 'form_plain_*'],
            ],
            \HeimrichHannot\Submissions\Submissions::NOTIFICATION_TYPE_OPTIN => [
                'recipients'           => ['form_value_*', 'form_plain_*', 'admin_email'],
                'email_subject'        => ['form_value_*', 'form_plain_*', 'admin_email', 'env_*', 'page_*', 'user_*', 'date', 'last_update'],
                'email_text'           => [
                    'formsubmission',
                    'formsubmission_all',
                    'form_submission_*',
                    'form_value_*',
                    'form_plain_*',
                    'salutation_submission',
                    'admin_email',
                    'env_*',
                    'page_*',
                    'user_*',
                    'date',
                    'last_update'
                ],
                'email_html'           => [
                    'formsubmission',
                    'formsubmission_all',
                    'form_submission_*',
                    'form_value_*',
                    'form_plain_*',
                    'salutation_submission',
                    'admin_email',
                    'env_*',
                    'page_*',
                    'user_*',
                    'date',
                    'last_update'
                ],
                'file_name'            => ['form_value_*', 'form_plain_*', 'admin_email'],
                'file_content'         => ['form_value_*', 'form_plain_*', 'admin_email'],
                'email_sender_name'    => ['form_value_*', 'form_plain_*', 'admin_email'],
                'email_sender_address' => ['form_value_*', 'form_plain_*', 'admin_email'],
                'email_recipient_cc'   => ['form_value_*', 'form_plain_*', 'admin_email'],
                'email_recipient_bcc'  => ['form_value_*', 'form_plain_*', 'admin_email'],
                'email_replyTo'        => ['form_value_*', 'form_plain_*', 'admin_email'],
                'attachment_tokens'    => ['form_value_*', 'form_plain_*', 'ics_attachment_token'],
            ]
        ]
    ]
);

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_submission']         = '\HeimrichHannot\Submissions\SubmissionModel';
$GLOBALS['TL_MODELS']['tl_submission_archive'] = '\HeimrichHannot\Submissions\SubmissionArchiveModel';

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['loadDataContainer']['submissions_setPTableForDelete'] = ['HeimrichHannot\Submissions\Backend\SubmissionArchiveBackend', 'setPTableForDelete'];
$GLOBALS['TL_HOOKS']['loadDataContainer']['huh_submissions']                = [\HeimrichHannot\Submissions\EventListener\LoadDataContainerListener::class, 'onLoadDataContainer'];

$GLOBALS['TL_HOOKS']['prepareFormData']['huh_submissions']                = [\HeimrichHannot\Submissions\EventListener\FormGeneratorListener::class, 'onPrepareFormData'];
$GLOBALS['TL_HOOKS']['storeFormData']['huh_submissions']                = [\HeimrichHannot\Submissions\EventListener\FormGeneratorListener::class, 'onStoreFormData'];
$GLOBALS['TL_HOOKS']['processFormData']['huh_submissions']                = [\HeimrichHannot\Submissions\EventListener\FormGeneratorListener::class, 'onProcessFormData'];

/**
 * Add permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'submissionss';
$GLOBALS['TL_PERMISSIONS'][] = 'submissionsp';
