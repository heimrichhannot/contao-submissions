<?php

$arrDca = &$GLOBALS['TL_DCA']['tl_submission_archive'];

$arrDca = array
(
	'config'      => array
	(
		'dataContainer'     => 'Table',
		'ctable'            => array('tl_submission'),
		'switchToEdit'      => true,
		'enableVersioning'  => true,
		'onload_callback'   => array
		(
			array('HeimrichHannot\Submissions\Backend\SubmissionArchiveBackend', 'checkPermission'),
		),
		'onsubmit_callback' => array
		(
			array('HeimrichHannot\Haste\Dca\General', 'setDateAdded'),
		),
		'sql'               => array
		(
			'keys' => array
			(
				'id' => 'primary',
			),
		),
	),
	'list'        => array
	(
		'label'             => array
		(
			'fields' => array('title'),
			'format' => '%s',
		),
		'sorting'           => array
		(
			'mode'         => 1,
			'fields'       => array('title'),
			'headerFields' => array('title'),
			'panelLayout'  => 'filter;search,limit',
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'       => 'act=select',
				'class'      => 'header_edit_all',
				'attributes' => 'onclick="Backend.getScrollOffset();"',
			),
		),
		'operations'        => array
		(
			'edit'       => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_submission_archive']['edit'],
				'href'  => 'table=tl_submission',
				'icon'  => 'edit.gif',
			),
			'editheader' => array
			(
				'label'           => &$GLOBALS['TL_LANG']['tl_submission_archive']['editheader'],
				'href'            => 'act=edit',
				'icon'            => 'header.gif',
				'button_callback' => array('HeimrichHannot\Submissions\Backend\SubmissionArchiveBackend', 'editHeader'),
			),
			'copy'       => array
			(
				'label'           => &$GLOBALS['TL_LANG']['tl_submission_archive']['copy'],
				'href'            => 'act=copy',
				'icon'            => 'copy.gif',
				'button_callback' => array('HeimrichHannot\Submissions\Backend\SubmissionArchiveBackend', 'copyArchive'),
			),
			'delete'     => array
			(
				'label'           => &$GLOBALS['TL_LANG']['tl_submission_archive']['copy'],
				'href'            => 'act=delete',
				'icon'            => 'delete.gif',
				'attributes'      => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
				'button_callback' => array('HeimrichHannot\Submissions\Backend\SubmissionArchiveBackend', 'deleteArchive'),
			),
			'show'       => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_submission_archive']['show'],
				'href'  => 'act=show',
				'icon'  => 'show.gif',
			),
		),
	),
	'palettes'    => array
	(
		'__selector__' => array(),
		'default'      => '{general_legend},title,parentTable,parentField,pid;{fields_legend},submissionFields,titlePattern;' .
						  '{notification_legend},nc_submission,nc_confirmation;',
	),
	'subpalettes' => array(),
	'fields'      => array
	(
		'id'               => array
		(
			'sql' => "int(10) unsigned NOT NULL auto_increment",
		),
		'parentTable'      => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_submission_archive']['parentTable'],
			'inputType'        => 'select',
			'options_callback' => array('\HeimrichHannot\Haste\Dca\General', 'getDataContainers'),
			'sql'              => "varchar(255) NOT NULL default ''",
			'eval'             => array(
				'tl_class'           => 'w50',
				'chosen'             => true,
				'submitOnChange'     => true,
				'includeBlankOption' => true,
			),
		),
		'parentField'      => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_submission_archive']['parentField'],
			'inputType'        => 'select',
			'options_callback' => array('HeimrichHannot\Submissions\Backend\SubmissionArchiveBackend', 'getParentFields'),
			'sql'              => "varchar(255) NOT NULL default ''",
			'eval'             => array(
				'tl_class'           => 'w50',
				'chosen'             => true,
				'submitOnChange'     => true,
				'includeBlankOption' => true,
			),
		),
		'pid'              => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_submission_archive']['pid'],
			'inputType'        => 'select',
			'options_callback' => array('HeimrichHannot\Submissions\Backend\SubmissionArchiveBackend', 'getParentEntitiesAsOptions'),
			'sql'              => "int(10) unsigned NOT NULL default '0'",
			'eval'             => array('tl_class' => 'w50', 'chosen' => true, 'includeBlankOption' => true),
		),
		'tstamp'           => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'",
		),
		'dateAdded'        => array
		(
			'label'   => &$GLOBALS['TL_LANG']['MSC']['dateAdded'],
			'sorting' => true,
			'flag'    => 6,
			'eval'    => array('rgxp' => 'datim', 'doNotCopy' => true),
			'sql'     => "int(10) unsigned NOT NULL default '0'",
		),
		'title'            => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission_archive']['title'],
			'exclude'   => true,
			'search'    => true,
			'sorting'   => true,
			'flag'      => 1,
			'inputType' => 'text',
			'eval'      => array('mandatory' => true, 'tl_class' => 'w50'),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'submissionFields' => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_submission_archive']['submissionFields'],
			'exclude'          => true,
			'inputType'        => 'checkboxWizard',
			'options_callback' => array('HeimrichHannot\Submissions\Submissions', 'getFieldsAsOptions'),
			'eval'             => array('multiple' => true, 'tl_class' => 'w50 clr'),
			'sql'              => "blob NULL",
		),
		'titlePattern'     => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission_archive']['titlePattern'],
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => array('maxlength' => 255, 'tl_class' => 'w50'),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'nc_submission'    => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_submission_archive']['nc_submission'],
			'exclude'          => true,
			'inputType'        => 'select',
			'options_callback' => array('HeimrichHannot\Submissions\Submissions', 'getNotificationsAsOptions'),
			'eval'             => array('includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'),
			'sql'              => "int(10) unsigned NOT NULL default '0'",
		),
		'nc_confirmation'  => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_submission_archive']['nc_confirmation'],
			'exclude'          => true,
			'inputType'        => 'select',
			'options_callback' => array('HeimrichHannot\Submissions\Submissions', 'getConfirmationNotificationsAsOptions'),
			'eval'             => array('includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'),
			'sql'              => "int(10) unsigned NOT NULL default '0'",
		),
	),
);

// add attachment related config fields
if (in_array('multifileupload', \ModuleLoader::getActive())) {
	/**
	 * Palettes
	 */
	$arrDca['palettes']['__selector__'][] = 'addAttachmentConfig';
	$arrDca['palettes']['default']        = str_replace('titlePattern;', 'titlePattern;{attachment_legend},addAttachmentConfig;', $arrDca['palettes']['default']);
	
	
	/**
	 * Subpalettes
	 */
	$arrDca['subpalettes']['addAttachmentConfig'] = 'attachmentUploadFolder,attachmentMaxFiles,attachmentMaxUploadSize,attachmentExtensions,attachmentFieldType,attachmentSubFolderPattern';
	
	/**
	 * Fields
	 */
	$arrFields = array
	(
		'addAttachmentConfig'        => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission_archive']['addAttachmentConfig'],
			'exclude'   => true,
			'inputType' => 'checkbox',
			'eval'      => array('submitOnChange' => true),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'attachmentUploadFolder'     => array
		(
			'label'         => &$GLOBALS['TL_LANG']['tl_submission_archive']['attachmentUploadFolder'],
			'exclude'       => true,
			'inputType'     => 'fileTree',
			'save_callback' => array
			(
				array('HeimrichHannot\Submissions\Backend\SubmissionArchiveBackend', 'setAttachmentUploadFolder'),
			),
			'eval'          => array('filesOnly' => false, 'fieldType' => 'radio', 'mandatory' => true),
			'sql'           => "binary(16) NULL",
		),
		'attachmentMaxFiles'         => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission_archive']['attachmentMaxFiles'],
			'exclude'   => true,
			'default'   => 5,
			'inputType' => 'text',
			'eval'      => array('rgxp' => 'digit', 'mandatory' => true, 'tl_class' => 'w50'),
			'sql'       => "int(3) unsigned NOT NULL default '0'",
		),
		'attachmentMaxUploadSize'    => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission_archive']['attachmentMaxUploadSize'],
			'exclude'   => true,
			'default'   => 10,
			'inputType' => 'text',
			'eval'      => array('rgxp' => 'digit', 'mandatory' => true, 'tl_class' => 'w50'),
			'sql'       => "int(10) unsigned NOT NULL default '0'",
		),
		'attachmentExtensions'       => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission_archive']['attachmentExtensions'],
			'exclude'   => true,
			'default'   => \Config::get('uploadTypes'),
			'inputType' => 'text',
			'eval'      => array('mandatory' => true, 'tl_class' => 'w50'),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'attachmentFieldType'        => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission_archive']['attachmentFieldType'],
			'exclude'   => true,
			'default'   => 'checkbox',
			'options'   => array('checkbox', 'radio'),
			'reference' => &$GLOBALS['TL_LANG']['tl_submission_archive']['reference']['attachmentFieldType'],
			'inputType' => 'radio',
			'eval'      => array('mandatory' => true, 'tl_class' => 'w50'),
			'sql'       => "varchar(8) NOT NULL default ''",
		),
		'attachmentSubFolderPattern' => array
		(
			'label'         => &$GLOBALS['TL_LANG']['tl_submission_archive']['attachmentSubFolderPattern'],
			'exclude'       => true,
			'inputType'     => 'text',
			'eval'          => array('maxlength' => 255, 'tl_class' => 'w50', 'preserveTags' => true),
			'sql'           => "varchar(128) COLLATE utf8_bin NOT NULL default '" . HeimrichHannot\Submissions\Submissions::getDefaultAttachmentSubFolderPattern() . "'",
		),
	);
	
	$arrDca['fields'] = array_merge($arrDca['fields'], $arrFields);
}
