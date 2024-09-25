<?php

$arrLang = &$GLOBALS['TL_LANG']['tl_submission_archive'];

/**
 * Fields
 */
$arrLang['title'] = ['Titolo', 'Inserire un titolo.'];
$arrLang['parentTable'] = ['Tabella genitore', 'Selezionare la tabella genitore.'];
$arrLang['parentField'] = ['Campo della legenda', 'Selezionare un campo della tabella genitore che faccia funzione di legenda.'];
$arrLang['pid'] = ['Entità genitore', 'Selezionare l\'entità genitore.'];
$arrLang['submissionFields'] = ['Campi', 'Selezionare i campi dell\archivio che dovranno essere inclusi nel documento da inviare.'];
$arrLang['titlePattern'] = ['Pattern titolo', 'Inserire un pattern per il titolo nel formato "%campo1% %campo2%".'];

$arrLang['addAttachmentConfig'] = ['Impostazioni per gli allegati', 'Modificare le impostazioni per gli allegati.'];
$arrLang['attachmentUploadFolder'] = ['Selezionare la directory per l\'upload', 'Inserire una directory di upload nella quale verranno inseriti gli allegati.'];
$arrLang['attachmentMaxFiles'] = ['Numero massimo di allegati', 'Indicare il numero massimo di allegati permessi.'];
$arrLang['attachmentMaxUploadSize'] = ['Dimensione massima degli allegati (in MB)', 'Indicare la dimensione massima per gli allegati, in megabytes.'];
$arrLang['attachmentExtensions'] = ['Tipi di file permessi', 'Qui potete inserire una lista di tipi di file, separati da virgole, che potranno essere caricati.'];
$arrLang['attachmentFieldType'] = ['Tipo di campo per gli allegati', 'Selezionare il tipo di campo che devono avere gli allegati nel Backend.'];
$arrLang['attachmentSubFolderPattern'] = ['Separare gli allegati in sottocartelle (Pattern)', 'Inserire un pattern di traduzione tra allegati e sottocartelle. Gli allegati verranno spostati nelle sottocartelle corrispondenti. Lasciare vuoto per raggruppare tutti gli allegati nella cartella di upload.'];


$arrLang['nc_submission'] = ['Invio notifica', 'Selezionare una notifica che verrà spedita in caso di invio eseguito correttamente.'];
$arrLang['nc_confirmation'] = ['Invio notifica di conferma', 'Selezionare qui una notifica che verrà inviata all\'autore del documento.'];

/**
 * Legends
 */
$arrLang['general_legend'] = 'Impostazioni generali';
$arrLang['fields_legend'] = 'Campi del documento';
$arrLang['notification_legend'] = 'Campi della conferma';
$arrLang['clean_legend'] = 'Ripulire';
$arrLang['attachment_legend'] = 'Impostazioni per gli allegati';

/**
 * Buttons
 */
$arrLang['new'] = ['Nuovo archivio documenti', 'Creazione di un nuovo archivio documenti'];
$arrLang['edit'] = ['Modifica archivio documenti', 'Modifica l\'archivio documenti con ID %s'];
$arrLang['editheader'] = ['Modifica le impostazioni dell\'archivio documenti', 'Modifica le impostazioni dell\'archivio documenti con ID %s'];
$arrLang['copy'] = ['Duplica archivio documenti', 'Duplica l\'archivio documenti con ID %s'];
$arrLang['delete'] = ['Cancella archivio documenti', 'Cancella l\'archivio documenti con ID %s'];
$arrLang['show'] = ['Dettagli archivio documenti', 'Mostra i dettagli dell\'archivio documenti con ID %s'];

/**
 * References
 */
$arrLang['reference']['attachmentFieldType']['checkbox'] = 'Checkbox';
$arrLang['reference']['attachmentFieldType']['radio'] = 'Radio-Button';
