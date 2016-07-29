<?php

$arrLang = &$GLOBALS['TL_LANG']['tl_submission_archive'];

/**
 * Fields
 */
$arrLang['title'] = array('Title', 'Enter a title.');
$arrLang['parentTable'] = array('Parent table', 'Select a parent table.');
$arrLang['parentField'] = array('Label field', 'Select a field in the parent table that serves as a label field.');
$arrLang['pid'] = array('Parent entity', 'Select a parent entity.');
$arrLang['submissionFields'] = array('Fields', 'Here you can select the fields that submissions will have.');
$arrLang['titlePattern'] = array('Title pattern', 'Enter a pattern for the title of the submissions in the form of "%field1% %field2%".');

$arrLang['addAttachmentConfig'] = array('Customize attachment settings', 'Adjust the settings for file attachments.');
$arrLang['attachmentUploadFolder'] = array('Select upload directory', 'Enter an individual upload directory where attachments will be stored.');
$arrLang['attachmentMaxFiles'] = array('Maximum number of attachments', 'Specify how many files can be submitted as an attachment.');
$arrLang['attachmentMaxUploadSize'] = array('Maximum file size of attachments (in MB)', 'Specify the file size of attachments.');
$arrLang['attachmentExtensions'] = array('Allowed file types', 'Specify a comma separated list of file types that can be uploaded.');
$arrLang['attachmentFieldType'] = array('Fieldtype of attachments', 'Select what type of field the attachment field has in the back end.');
$arrLang['attachmentSubFolderPattern'] = array('Move attachments into subfolders (pattern)', 'Enter a pattern which is to be translated on the basis of the submission in a subdirectory. Attachments will be moved to the directory. Leave blank if you don`t want attachments to be moved.');

$arrLang['nc_submission'] = array('Send notification', 'Select an notification to be sent after the successful sending.');
$arrLang['nc_confirmation'] = array('Send a confirmation notification', 'Select the notification that will be sent as an acknowledgment to the authors of the submission.');

/**
 * Legends
 */
$arrLang['general_legend'] = 'General';
$arrLang['fields_legend'] = 'Submission fields';
$arrLang['notification_legend'] = 'Notifications';
$arrLang['clean_legend'] = 'Cleaning';

/**
 * Buttons
 */
$arrLang['new'] = array('New submission archive', 'Create a submission archive');
$arrLang['edit'] = array('Edit submission archive', 'Edit submission archive ID %s');
$arrLang['editheader'] = array('Edit submission archive settings', 'Edit submission archive settings ID %s');
$arrLang['copy'] = array('Copy submission archive', 'Copy submission archive ID %s');
$arrLang['delete'] = array('Delete submission archive', 'Delete submission archive ID %s');
$arrLang['show'] = array('Submission archive details', 'Show submission archive details ID %s');
