<?php

$arrLang = &$GLOBALS['TL_LANG']['tl_nc_notification'];

$arrLang['type'][\HeimrichHannot\Submissions\Submissions::NOTIFICATION_TYPE_SUBMISSIONS] = 'Einsendungen';
$arrLang['type'][\HeimrichHannot\Submissions\Submissions::NOTIFICATION_TYPE_FORM_SUBMISSION] =
	array('Formularübertragungen', 'Dieser Benachrichtigungstyp wird nach erfolgreichem Verschicken einer Einsendung genutzt.');
$arrLang['type'][\HeimrichHannot\Submissions\Submissions::NOTIFICATION_TYPE_CONFIRMATION]
	= array('Bestätigung', 'Dieser Benachrichtigungstyp wird zur Bestätigung einer Einsendung genutzt und kann zusätzlich nachträglich verschickt werden.');