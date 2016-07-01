<?php

$arrLang = &$GLOBALS['TL_LANG']['tl_submission_archive'];

/**
 * Fields
 */
$arrLang['title'] = array('Title', 'Geben Sie hier bitte den Titel ein.');
$arrLang['parentTable'] = array('Parent table', 'Wählen Sie hier eine Elterntabelle aus.');
$arrLang['parentField'] = array('Label field', 'Wählen Sie hier ein Feld der Elterntabelle aus, das als Beschriftungsfeld dienen soll.');
$arrLang['pid'] = array('Parent entity', 'Wählen Sie hier eine Elternentität aus.');
$arrLang['submissionFields'] = array('Fields', 'Wählen Sie hier die Felder aus, die Einsendungen dieses Archiv erhalten sollen.');
$arrLang['titlePattern'] = array('Title pattern', 'Geben Sie hier ein Muster für die Titel der Einsendungen in der Form "%field1% %field2%" ein.');
$arrLang['nc_submission'] = array('Send notification', 'Wählen Sie hier eine Benachrichtigung aus, die nach dem erfolgreichen Einsenden verschickt werden soll.');
$arrLang['nc_confirmation'] = array('Send a confirmation notification', 'Wählen Sie hier die Benachrichtigung aus, die als Bestätigung an den Autoren der Einsendung verschickt werden soll.');

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
