<?php

use HeimrichHannot\Submissions\FormType\SubmissionType;

$lang = &$GLOBALS['TL_LANG']['tl_form'];

$lang['huh_submissions_legend'] = 'Submissions-Einstellungen';

$lang['huhSub_storeSubmission'] = ['Als Einsendung speichern', 'Übermittelte Formulardaten als Einsendung speichern. Diese Option überschreibt die Einstellung von "Eingabe speichern"!'];
$lang['huhSub_submissionArchive'] = ['Einsendungsarchiv', 'Das Einsendungsarchiv, in welchem die Formulardaten gespeichert werden sollen.'];

$lang['huhSub_optIn'] = ['Double Opt-in aktivieren', 'Soll für dieses Formular ein Double Opt-in verwendet werden?'];
$lang['huhSub_optInNotification'] = [
    'Double Opt-in Benachrichtigung',
    'Wählen Sie hier die Opt-in-Benachrichtigung aus. Diese wird vor der eigentlichen Benachrichtigung verschickt. Der Opt-In-Link wird im Token `optInUrl` übergeben.',
];
$lang['huhSub_optInJumpTo'] = ['Double Opt-in Erfolg-Weiterleitungsseite', 'Wählen Sie die Seite aus, auf welche nach erfolgreichem Double Opt-in weitergeleitet werden soll.'];
$lang['huhSub_optInTokenInvalidJumpTo'] = ['"Token bereits bestätigt"-Weiterleitungsseite', 'Wählen Sie die Seite aus, auf welche weitergeleitet werden soll, wenn der Token bereits bestätigt wurde.'];
$lang['huhSub_optInField'] = ['Double Opt-in Bestätigungsfeld', 'Wählen Sie hier ein Feld aus, welches bei erfolgreichem Double Opt-in auf true gesetzt werden soll.'];

$lang['FORMTYPE'][SubmissionType::TYPE] = 'Einsendung';
