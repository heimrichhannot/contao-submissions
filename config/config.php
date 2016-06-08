<?php

/**
* Backend modules
*/
$GLOBALS['BE_MOD']['content']['submission'] = array(
	'tables' => array('tl_submission_archive', 'tl_submission'),
	'icon'   => 'system/modules/submissions/assets/img/icon_submission.png',
	'send_confirmation' => array('HeimrichHannot\Submissions\SubmissionModel', 'sendConfirmationNotificationBe'),
	'export_csv' => \HeimrichHannot\Exporter\ModuleExporter::getBackendModule(),
	'export_xls' => \HeimrichHannot\Exporter\ModuleExporter::getBackendModule()
);

/**
 * Notification Center Notification Types
 */
$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE'] = array_merge_recursive(
	(array) $GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE'],
	array(
		\HeimrichHannot\Submissions\Submissions::NOTIFICATION_TYPE_SUBMISSIONS => array(
			\HeimrichHannot\Submissions\Submissions::NOTIFICATION_TYPE_FORM_SUBMISSION => array(
				'recipients'           => array('form_value_*', 'form_plain_*', 'admin_email'),
				'email_subject'        => array('form_value_*', 'form_plain_*', 'admin_email'),
				'email_text'           => array('formsubmission', 'formsubmission_all', 'form_submission_*',
												'form_value_*', 'form_plain_*', 'salutation_submission', 'admin_email'),
				'email_html'           => array('formsubmission', 'formsubmission_all', 'form_submission_*',
												'form_value_*', 'form_plain_*', 'salutation_submission', 'admin_email'),
				'file_name'            => array('form_value_*', 'form_plain_*', 'admin_email'),
				'file_content'         => array('form_value_*', 'form_plain_*', 'admin_email'),
				'email_sender_name'    => array('form_value_*', 'form_plain_*', 'admin_email'),
				'email_sender_address' => array('form_value_*', 'form_plain_*', 'admin_email'),
				'email_recipient_cc'   => array('form_value_*', 'form_plain_*', 'admin_email'),
				'email_recipient_bcc'  => array('form_value_*', 'form_plain_*', 'admin_email'),
				'email_replyTo'        => array('form_value_*', 'form_plain_*', 'admin_email'),
				'attachment_tokens'    => array('form_value_*', 'form_plain_*'),
			)
		),
		\HeimrichHannot\Submissions\Submissions::NOTIFICATION_TYPE_SUBMISSIONS => array(
			\HeimrichHannot\Submissions\Submissions::NOTIFICATION_TYPE_CONFIRMATION => array(
				'recipients'           => array('event_*', 'form_value_*', 'form_plain_*', 'admin_email'),
				'email_subject'        => array('event_*', 'event_date', 'event_datime', 'form_value_*',
					'form_plain_*', 'admin_email'),
				'email_text'           => array('event_date', 'event_datime', 'event_*', 'formsubmission',
					'formsubmission_all', 'form_submission_*', 'form_value_*', 'form_plain_*',
					'salutation_submission', 'admin_email'),
				'email_html'           => array('event_*', 'event_date', 'event_datime', 'formsubmission',
					'formsubmission_all', 'form_submission_*', 'form_value_*', 'form_plain_*',
					'salutation_submission', 'admin_email'),
				'file_name'            => array('event_*', 'form_value_*', 'form_plain_*', 'admin_email'),
				'file_content'         => array('event_*', 'form_value_*', 'form_plain_*', 'admin_email'),
				'email_sender_name'    => array('event_*', 'form_value_*', 'form_plain_*', 'admin_email'),
				'email_sender_address' => array('event_*', 'form_value_*', 'form_plain_*', 'admin_email'),
				'email_recipient_cc'   => array('event_*', 'form_value_*', 'form_plain_*', 'admin_email'),
				'email_recipient_bcc'  => array('event_*', 'form_value_*', 'form_plain_*', 'admin_email'),
				'email_replyTo'        => array('event_*', 'form_value_*', 'form_plain_*', 'admin_email'),
				'attachment_tokens'    => array('confirmation_pdf', 'event_*', 'form_value_*', 'form_plain_*'),
			)
		)
	)
);

/**
 * Crons
 */
$GLOBALS['TL_CRON']['minutely']['runMinutelyCleaner']	= array('HeimrichHannot\Submissions\SubmissionsCleaner', 'runMinutelyCleaner');
$GLOBALS['TL_CRON']['hourly']['runHourlyCleaner']		= array('HeimrichHannot\Submissions\SubmissionsCleaner', 'runHourlyCleaner');
$GLOBALS['TL_CRON']['daily']['runDailyCleaner']			= array('HeimrichHannot\Submissions\SubmissionsCleaner', 'runDailyCleaner');
$GLOBALS['TL_CRON']['weekly']['runWeeklyCleaner']		= array('HeimrichHannot\Submissions\SubmissionsCleaner', 'runWeeklyCleaner');
$GLOBALS['TL_CRON']['monthly']['runMonthlyCleaner']		= array('HeimrichHannot\Submissions\SubmissionsCleaner', 'runMonthlyCleaner');

/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_submission'] = '\HeimrichHannot\Submissions\SubmissionModel';
$GLOBALS['TL_MODELS']['tl_submission_archive'] = '\HeimrichHannot\Submissions\SubmissionArchiveModel';

/**
 * Add permissions
 */
$GLOBALS['TL_PERMISSIONS'][] = 'submissionss';
$GLOBALS['TL_PERMISSIONS'][] = 'submissionsp';