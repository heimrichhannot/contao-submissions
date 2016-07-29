<?php

$arrLang = &$GLOBALS['TL_LANG']['tl_submission_archive'];

/**
 * Fields
 */
$arrLang['title'] = array('Titel', 'Geben Sie hier bitte den Titel ein.');
$arrLang['parentTable'] = array('Elterntabelle', 'Wählen Sie hier eine Elterntabelle aus.');
$arrLang['parentField'] = array('Beschriftungsfeld', 'Wählen Sie hier ein Feld der Elterntabelle aus, das als Beschriftungsfeld dienen soll.');
$arrLang['pid'] = array('Elternentität', 'Wählen Sie hier eine Elternentität aus.');
$arrLang['submissionFields'] = array('Felder', 'Wählen Sie hier die Felder aus, die Einsendungen dieses Archiv erhalten sollen.');
$arrLang['titlePattern'] = array('Titelmuster', 'Geben Sie hier ein Muster für die Titel der Einsendungen in der Form "%field1% %field2%" ein.');

$arrLang['addAttachmentConfig'] = array('Einstellungen für Anlagen anpassen', 'Passen Sie die Einstellungen für Dateianlagen an.');
$arrLang['attachmentUploadFolder'] = array('Upload-Verzeichnis auswählen', 'Geben Sie ein individuelles Upload-Verzeichnis an in dass Dateianhänge abgelegt werden sollen.');
$arrLang['attachmentMaxFiles'] = array('Maximale Anzahl von Anlagen', 'Legen Sie fest, wieviele Dateien als Anlage eingereicht werden können.');
$arrLang['attachmentMaxUploadSize'] = array('Maximale Dateigröße von Anlagen (in MB)', 'Legen Sie fest, welche Dateiegröße als Anlagen haben dürfen.');
$arrLang['attachmentExtensions'] = array('Erlaubte Dateitypen', 'Hier können Sie eine kommagetrennte Liste von Dateitypen eingeben, die hochgeladen werden dürfen.');
$arrLang['attachmentFieldType'] = array('Feldtyp von Anlagen', 'Wählen Sie aus welchen Feldtyp die Anlagen im Backend haben sollen.');
$arrLang['attachmentSubFolderPattern'] = array('Anlagen in Unterordner verschieben (Muster)', 'Geben Sie ein Muster an das auf Basis der Einsendung in ein Unterverzeichnis übersetzt werden soll. Anlagen werden anschließend in das Verzeichnis verschoben. Leer lassen um Anlagen im Upload-Verzeichnis zu belassen.');


$arrLang['nc_submission'] = array('Benachrichtigung versenden', 'Wählen Sie hier eine Benachrichtigung aus, die nach dem erfolgreichen Einsenden verschickt werden soll.');
$arrLang['nc_confirmation'] = array('Benachrichtigung zur Bestätigung versenden', 'Wählen Sie hier die Benachrichtigung aus, die als Bestätigung an den Autoren der Einsendung verschickt werden soll.');

/**
 * Legends
 */
$arrLang['general_legend'] = 'Allgemeine Einstellungen';
$arrLang['fields_legend'] = 'Einsendungsfelder';
$arrLang['notification_legend'] = 'Benachrichtigungen';
$arrLang['clean_legend'] = 'Säuberung';
$arrLang['attachment_legend'] = 'Einstellungen für Dateianlagen';

/**
 * Buttons
 */
$arrLang['new'] = array('Neues Einsendungsarchiv', 'Einsendungsarchiv erstellen');
$arrLang['edit'] = array('Einsendungsarchiv bearbeiten', 'Einsendungsarchiv ID %s bearbeiten');
$arrLang['editheader'] = array('Einsendungsarchiv-Einstellungen bearbeiten', 'Einsendungsarchiv-Einstellungen ID %s bearbeiten');
$arrLang['copy'] = array('Einsendungsarchiv duplizieren', 'Einsendungsarchiv ID %s duplizieren');
$arrLang['delete'] = array('Einsendungsarchiv löschen', 'Einsendungsarchiv ID %s löschen');
$arrLang['show'] = array('Einsendungsarchiv Details', 'Einsendungsarchiv-Details ID %s anzeigen');

/**
 * References
 */
$arrLang['reference']['attachmentFieldType']['checkbox'] = 'Checkbox';
$arrLang['reference']['attachmentFieldType']['radio'] = 'Radio-Button';