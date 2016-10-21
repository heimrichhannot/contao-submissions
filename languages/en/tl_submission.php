<?php

$arrLang = &$GLOBALS['TL_LANG']['tl_submission'];

/**
 * Fields
 */
$arrLang['type'] = array('Type', 'Choose the type of the submission here.');
$arrLang['authorType'] = array('Author type', 'Choose the type of author.');
$arrLang['authorType'][\HeimrichHannot\Submissions\Submissions::AUTHOR_TYPE_MEMBER] = 'Member (frontend)';
$arrLang['authorType'][\HeimrichHannot\Submissions\Submissions::AUTHOR_TYPE_USER] = 'User (backend)';
$arrLang['author'] = array('Author', 'This field contains the author of the submission.');
$arrLang['gender'] = array('Gender', 'Choose the gender here.');
$arrLang['academicTitle'] = array('Academic title', 'Type in an academic title.');
$arrLang['additionalTitle'] = array('Additional title', 'Type in any additional title here.');
$arrLang['firstname'] = array('First name', 'Type in a first name.');
$arrLang['lastname'] = array('Last name', 'Type in a last name.');
$arrLang['dateOfBirth'] = array('Date of birth', 'Type in the date of birth.');
$arrLang['street'] = array('Street', 'Type in the street.');
$arrLang['street2'] = array('Additional street information', 'Type in additional street information here.');
$arrLang['postal'] = array('Postal code', 'Type in the postal code.');
$arrLang['city'] = array('City', 'Type in the city.');
$arrLang['country'] = array('Country', 'Type in the country.');
$arrLang['email'] = array('Email address', 'Type in the email address.');
$arrLang['phone'] = array('Phone', 'Type in the phone number.');
$arrLang['fax'] = array('Fax', 'Type in the fax number.');
$arrLang['subject'] = array('Subject', 'Type in a subject here.');
$arrLang['notes'] = array('Notes', 'Type in the notes.');
$arrLang['company'] = array('Company', 'Type in the company.');
$arrLang['agreement'] = array('Accept terms and conditions', 'Choose this option if the user has agreed to the terms & conditions.');
$arrLang['privacy'] = array('Accept privacy policy', 'Choose this option if the user has agreed to the privacy policy.');
$arrLang['attachments'] = array('Attachments', 'Add file attachments.');
$arrLang['published'] = array('Activated', 'Choose this option to activate the submission.');
$arrLang['formHybridBlob'] = array('FormHybrid BLOB', 'This field contains changes, that are not supposed to be finally saved into the record (e.g. multistep forms).');

/**
 * Legends
 */
$arrLang['general_legend'] = 'General';
$arrLang['submission_legend'] = 'Submission';
$arrLang['publish_legend'] = 'State';


/**
 * Buttons
 */
$arrLang['new'] = array('New submission', 'Create a submission');
$arrLang['edit'] = array('Edit submission', 'Edit submission ID %s');
$arrLang['copy'] = array('submission duplizieren', 'Copy submission ID %s');
$arrLang['delete'] = array('Delete submission', 'Delete submission ID %s');
$arrLang['toggle'] = array('Activate submission', 'Activate/deactivate submission ID %s');
$arrLang['send_confirmation'] = array('Send confirmation notification', 'Send confirmation notification for submission ID %s');
$arrLang['show'] = array('Submission details', 'Show submission details ID %s');
