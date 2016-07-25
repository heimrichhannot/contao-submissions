<?php

$arrLang = &$GLOBALS['TL_LANG']['tl_submission'];

/**
 * Fields
 */
$arrLang['authorType'] = array('Author type', 'Choose the type of author.');
$arrLang['authorType'][\HeimrichHannot\Submissions\Submissions::AUTHOR_TYPE_MEMBER] = 'Member (frontend)';
$arrLang['authorType'][\HeimrichHannot\Submissions\Submissions::AUTHOR_TYPE_USER] = 'User (backend)';
$arrLang['author'] = array('Author', 'This field contains the author of the submission.');
$arrLang['gender'] = array('Gender', 'Choose the gender here.');
$arrLang['academicTitle'] = array('Academic title', 'Type in an academic title.');
$arrLang['firstname'] = array('First name', 'Type in a first name.');
$arrLang['lastname'] = array('Last name', 'Type in a last name.');
$arrLang['dateOfBirth'] = array('Date of birth', 'Type in the date of birth.');
$arrLang['street'] = array('Street', 'Type in the street.');
$arrLang['postal'] = array('Postal code', 'Type in the postal code.');
$arrLang['city'] = array('City', 'Type in the city.');
$arrLang['country'] = array('Country', 'Type in the country.');
$arrLang['email'] = array('Email address', 'Type in the email address.');
$arrLang['phone'] = array('Phobnbe', 'Type in the phone number.');
$arrLang['notes'] = array('Notes', 'Type in the notes.');
$arrLang['company'] = array('Company', 'Type in the company.');
$arrLang['published'] = array('Activated', 'Choose this option to activate the submission.');


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
