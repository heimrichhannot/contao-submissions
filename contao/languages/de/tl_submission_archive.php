<?php

$arrLang = &$GLOBALS['TL_LANG']['tl_submission_archive'];

/**
 * Fields
 */
$arrLang['title']            = ['Titel', 'Geben Sie hier bitte den Titel ein.'];
$arrLang['parentTable']      = ['Elterntabelle', 'Wählen Sie hier eine Elterntabelle aus.'];
$arrLang['parentField']      = ['Beschriftungsfeld', 'Wählen Sie hier ein Feld der Elterntabelle aus, das als Beschriftungsfeld dienen soll.'];
$arrLang['pid']              = ['Elternentität', 'Wählen Sie hier eine Elternentität aus.'];
$arrLang['submissionFields'] = ['Felder', 'Wählen Sie hier die Felder aus, die Einsendungen dieses Archiv erhalten sollen.'];

$arrLang['submissionFieldsMandatoryOverride']              = ['Pflichtfeldeigenschaft überschreiben', 'Hier können Sie einzelne Felder - abweichend von Ihrer Standardeinstellung - als Pflichtfelder deklarieren bzw. die Eigenschaft entfernen.'];
$arrLang['submissionFieldsMandatoryOverride']['field']     = ['Feld', ''];
$arrLang['submissionFieldsMandatoryOverride']['mandatory'] = ['Pflichtfeld', ''];

$arrLang['titlePattern'] = ['Titelmuster', 'Geben Sie hier ein Muster für die Titel der Einsendungen in der Form "%field1% %field2%" ein.'];

$arrLang['addAttachmentConfig']        = ['Einstellungen für Anlagen anpassen', 'Passen Sie die Einstellungen für Dateianlagen an.'];
$arrLang['attachmentUploadFolder']     = ['Upload-Verzeichnis auswählen', 'Geben Sie ein individuelles Upload-Verzeichnis an in dass Dateianhänge abgelegt werden sollen.'];
$arrLang['attachmentMaxFiles']         = ['Maximale Anzahl von Anlagen', 'Legen Sie fest, wieviele Dateien als Anlage eingereicht werden können.'];
$arrLang['attachmentMaxUploadSize']    = ['Maximale Dateigröße von Anlagen (in MB)', 'Legen Sie fest, welche Dateiegröße als Anlagen haben dürfen.'];
$arrLang['attachmentExtensions']       = ['Erlaubte Dateitypen', 'Hier können Sie eine kommagetrennte Liste von Dateitypen eingeben, die hochgeladen werden dürfen.'];
$arrLang['attachmentFieldType']        = ['Feldtyp von Anlagen', 'Wählen Sie aus welchen Feldtyp die Anlagen im Backend haben sollen.'];
$arrLang['attachmentSubFolderPattern'] = ['Anlagen in Unterordner verschieben (Muster)', 'Geben Sie ein Muster an das auf Basis der Einsendung in ein Unterverzeichnis übersetzt werden soll. Anlagen werden anschließend in das Verzeichnis verschoben. Leer lassen um Anlagen im Upload-Verzeichnis zu belassen.'];


$arrLang['nc_submission']   = ['Benachrichtigung versenden', 'Wählen Sie hier eine Benachrichtigung aus, die nach dem erfolgreichen Einsenden verschickt werden soll.'];
$arrLang['nc_confirmation'] = ['Benachrichtigung zur Bestätigung versenden', 'Wählen Sie hier die Benachrichtigung aus, die als Bestätigung an den Autoren der Einsendung verschickt werden soll.'];

/**
 * Legends
 */
$arrLang['general_legend']      = 'Allgemeine Einstellungen';
$arrLang['fields_legend']       = 'Einsendungsfelder';
$arrLang['notification_legend'] = 'Benachrichtigungen';
$arrLang['clean_legend']        = 'Säuberung';
$arrLang['attachment_legend']   = 'Einstellungen für Dateianlagen';

/**
 * Buttons
 */
$arrLang['new']        = ['Neues Einsendungsarchiv', 'Einsendungsarchiv erstellen'];
$arrLang['edit']       = ['Einsendungsarchiv bearbeiten', 'Einsendungsarchiv ID %s bearbeiten'];
$arrLang['editheader'] = ['Einsendungsarchiv-Einstellungen bearbeiten', 'Einsendungsarchiv-Einstellungen ID %s bearbeiten'];
$arrLang['copy']       = ['Einsendungsarchiv duplizieren', 'Einsendungsarchiv ID %s duplizieren'];
$arrLang['delete']     = ['Einsendungsarchiv löschen', 'Einsendungsarchiv ID %s löschen'];
$arrLang['show']       = ['Einsendungsarchiv Details', 'Einsendungsarchiv-Details ID %s anzeigen'];

/**
 * References
 */
$arrLang['reference']['attachmentFieldType']['checkbox'] = 'Checkbox';
$arrLang['reference']['attachmentFieldType']['radio']    = 'Radio-Button';
