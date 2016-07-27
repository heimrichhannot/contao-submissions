<?php

$arrLang = &$GLOBALS['TL_LANG']['tl_submission'];

/**
 * Fields
 */
$arrLang['authorType'] = array('Autorentyp', 'Wählen Sie hier den Typ des Autoren aus.');
$arrLang['authorType'][\HeimrichHannot\Submissions\Submissions::AUTHOR_TYPE_NONE] = 'Kein Autor';
$arrLang['authorType'][\HeimrichHannot\Submissions\Submissions::AUTHOR_TYPE_MEMBER] = 'Mitglied (Frontend)';
$arrLang['authorType'][\HeimrichHannot\Submissions\Submissions::AUTHOR_TYPE_USER] = 'Benutzer (Backend)';
$arrLang['author'] = array('Autor', 'Dieses Feld beinhaltet den Autoren der Einsendung.');
$arrLang['gender'] = array('Geschlecht', 'Geben Sie hier das Geschlecht ein.');
$arrLang['academicTitle'] = array('Akademischer Titel', 'Geben Sie hier den akademischen Titel ein.');
$arrLang['firstname'] = array('Vorname', 'Geben Sie hier den Vornamen ein.');
$arrLang['lastname'] = array('Nachname', 'Geben Sie hier den Nachnamen ein.');
$arrLang['company'] = array('Firma', 'Geben Sie hier die Firma ein.');
$arrLang['dateOfBirth'] = array('Geburtsdatum', 'Geben Sie hier das Geburtsdatum ein.');
$arrLang['street'] = array('Straße', 'Geben Sie hier die Straße ein.');
$arrLang['postal'] = array('Postleitzahl', 'Geben Sie hier die Postleitzahl ein.');
$arrLang['city'] = array('Ort', 'Geben Sie hier den Ort ein.');
$arrLang['country'] = array('Land', 'Geben Sie hier das Land ein.');
$arrLang['email'] = array('E-Mail-Adresse', 'Geben Sie hier die E-Mail-Adresse ein.');
$arrLang['phone'] = array('Telefon', 'Geben Sie hier die Telefonnummer ein.');
$arrLang['fax'] = array('Fax', 'Geben Sie hier die Faxnummer ein.');
$arrLang['notes'] = array('Anmerkungen', 'Geben Sie hier Anmerkungen ein.');
$arrLang['published'] = array('Aktiviert', 'Wählen Sie diese Option, um die Einsendung zu aktivieren.');


/**
 * Legends
 */
$arrLang['general_legend'] = 'Allgemeine Einstellungen';
$arrLang['submission_legend'] = 'Einsendung';
$arrLang['publish_legend'] = 'Status';


/**
 * Buttons
 */
$arrLang['new'] = array('Neue Einsendung', 'Einsendung erstellen');
$arrLang['edit'] = array('Einsendung bearbeiten', 'Einsendung ID %s bearbeiten');
$arrLang['copy'] = array('Einsendung duplizieren', 'Einsendung ID %s duplizieren');
$arrLang['delete'] = array('Einsendung löschen', 'Einsendung ID %s löschen');
$arrLang['toggle'] = array('Einsendung veröffentlichen', 'Einsendung ID %s veröffentlichen/verstecken');
$arrLang['send_confirmation'] = array('Bestätigungsbenachrichtigung verschicken', 'Bestätigungsbenachrichtigung für Einsendung ID %s verschicken');
$arrLang['show'] = array('Einsendung-Details', 'Einsendung-Details ID %s anzeigen');
