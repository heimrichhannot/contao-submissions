<?php

use HeimrichHannot\Submissions\Config\NotificationConfig;
use HeimrichHannot\Submissions\Model\SubmissionArchiveModel;
use HeimrichHannot\Submissions\Model\SubmissionModel;

/**
 * Backend modules
 */
$GLOBALS['BE_MOD']['content']['huh_submissions'] = [
    'tables' => ['tl_submission_archive', 'tl_submission'],
    'icon' => 'bundles/heimrichhannotsubmissions/img/icon_submission.png',
    // 'send_confirmation' => ['HeimrichHannot\Submissions\SubmissionModel', 'sendConfirmationNotificationBe'] // todo: still required?
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


/**
 * Notification Center
 */
$notificationTypes = [
    NotificationConfig::TYPE_SUBMISSIONS => [
        NotificationConfig::TYPE_FORM_SUBMISSION => [
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
        NotificationConfig::TYPE_CONFIRMATION => [
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
        NotificationConfig::TYPE_OPT_IN => [
            'recipients'           => ['admin_email', 'form_*', 'formconfig_*'],
            'email_subject'        => ['form_*', 'formconfig_*', 'admin_email'],
            'email_text'           => ['form_*', 'formconfig_*', 'formlabel_*', 'raw_data', 'raw_data_filled', 'admin_email', 'optInToken', 'optInUrl'],
            'email_html'           => ['form_*', 'formconfig_*', 'formlabel_*', 'raw_data', 'raw_data_filled', 'admin_email', 'optInToken', 'optInUrl'],
            'file_name'            => ['form_*', 'formconfig_*', 'admin_email'],
            'file_content'         => ['form_*', 'formconfig_*', 'formlabel_*', 'raw_data', 'raw_data_filled', 'admin_email', 'optInToken', 'optInUrl'],
            'email_sender_name'    => ['admin_email', 'form_*', 'formconfig_*'],
            'email_sender_address' => ['admin_email', 'form_*', 'formconfig_*'],
            'email_recipient_cc'   => ['admin_email', 'form_*', 'formconfig_*'],
            'email_recipient_bcc'  => ['admin_email', 'form_*', 'formconfig_*'],
            'email_replyTo'        => ['admin_email', 'form_*', 'formconfig_*'],
            'attachment_tokens'    => ['form_*', 'formconfig_*'],
        ]
    ]
];

$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE'] = \array_merge_recursive(
    (array) ($GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE'] ?? []),
    $notificationTypes
);
