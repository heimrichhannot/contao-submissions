<?php

$arrLang = &$GLOBALS['TL_LANG']['tl_nc_notification'];

$arrLang['type'][\HeimrichHannot\Submissions\Submissions::NOTIFICATION_TYPE_SUBMISSIONS] = 'Einsendungen';
$arrLang['type'][\HeimrichHannot\Submissions\Submissions::NOTIFICATION_TYPE_FORM_SUBMISSION] =
	['Formularübertragungen', 'Dieser Benachrichtigungstyp wird nach erfolgreichem Verschicken einer Einsendung genutzt.'];
$arrLang['type'][\HeimrichHannot\Submissions\Submissions::NOTIFICATION_TYPE_CONFIRMATION]
	= ['Bestätigung', 'Dieser Benachrichtigungstyp wird zur Bestätigung einer Einsendung genutzt und kann zusätzlich nachträglich verschickt werden.'];
$arrLang['type'][\HeimrichHannot\Submissions\Submissions::NOTIFICATION_TYPE_OPTIN]
	= ['Opt-In Benachrichtigung (Formulargenerator)', 'Dieser Benachrichtigungstyp wird für Opt-In-Nachrichten des Formulargenerators verwendet.'];