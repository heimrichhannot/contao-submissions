<?php

$arrDca = &$GLOBALS['TL_DCA']['tl_submission'];

$arrDca = array
(
	'config'   => array
	(
		'dataContainer'     => 'Table',
		'ptable'            => 'tl_submission_archive',
		'enableVersioning'  => true,
		'doNotCopyRecords'  => true,
		'onload_callback'   => array
		(
			array('HeimrichHannot\Haste\Dca\General', 'setDateAdded', true),
			array('HeimrichHannot\Submissions\Backend\SubmissionBackend', 'checkPermission'),
			array('HeimrichHannot\Submissions\Backend\SubmissionBackend', 'modifyPalette', true),
		),
		'onsubmit_callback' => array
		(
			array('HeimrichHannot\Submissions\Backend\SubmissionBackend', 'moveAttachments'),
		),
		'sql'               => array
		(
			'keys' => array
			(
				'id' => 'primary',
			),
		),
	),
	'list'     => array
	(
		'label'             => array
		(
			'fields' => array('id'),
			'format' => '%s',
		),
		'sorting'           => array
		(
			'mode'                  => 4,
			'fields'                => array('dateAdded DESC'),
			'headerFields'          => array('title'),
			'panelLayout'           => 'filter;search,limit',
			'child_record_callback' => array('HeimrichHannot\Submissions\Backend\SubmissionBackend', 'listChildren'),
			'filter'                => array(array('tstamp>?', 0)),
		),
		'global_operations' => array
		(
			'export_csv' => \HeimrichHannot\Exporter\ModuleExporter::getGlobalOperation(
				'export_csv',
				$GLOBALS['TL_LANG']['MSC']['export_csv'],
				'system/modules/exporter/assets/img/icon_export.png'
			),
			'export_xls' => \HeimrichHannot\Exporter\ModuleExporter::getGlobalOperation(
				'export_xls',
				$GLOBALS['TL_LANG']['MSC']['export_xls'],
				'system/modules/exporter/assets/img/icon_export.png'
			),
			'all'        => array
			(
				'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'       => 'act=select',
				'class'      => 'header_edit_all',
				'attributes' => 'onclick="Backend.getScrollOffset();"',
			),
		),
		'operations'        => array
		(
			'edit'              => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_submission']['edit'],
				'href'  => 'act=edit',
				'icon'  => 'edit.gif',
			),
			'copy'              => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_submission']['copy'],
				'href'  => 'act=copy',
				'icon'  => 'copy.gif',
			),
			'delete'            => array
			(
				'label'      => &$GLOBALS['TL_LANG']['tl_submission']['delete'],
				'href'       => 'act=delete',
				'icon'       => 'delete.gif',
				'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
			),
			'toggle'            => array
			(
				'label'           => &$GLOBALS['TL_LANG']['tl_submission']['toggle'],
				'icon'            => 'visible.gif',
				'attributes'      => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback' => array('HeimrichHannot\Submissions\Backend\SubmissionBackend', 'toggleIcon'),
			),
			'send_confirmation' => array
			(
				'label'           => &$GLOBALS['TL_LANG']['tl_submission']['send_confirmation'],
				'icon'            => 'system/modules/submissions/assets/img/icon_send_confirmation.png',
				'href'            => 'key=send_confirmation',
				'attributes'      => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['sendConfirmationConfirm'] . '\'))return false;Backend.getScrollOffset()"',
				'button_callback' => array('HeimrichHannot\Submissions\Backend\SubmissionBackend', 'sendConfirmation'),
			),
			'show'              => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_submission']['show'],
				'href'  => 'act=show',
				'icon'  => 'show.gif',
			),
		),
	),
	'palettes' => array(
		'default' => '{general_legend},authorType,author;' .
					 '{submission_legend},gender,academicTitle,firstname,lastname,dateOfBirth,street,' .
					 'postal,city,country,email,phone,fax,notes,captcha,attachments;{publish_legend},published;',
	),
	'fields'   => array
	(
		'id'             => array
		(
			'sql' => "int(10) unsigned NOT NULL auto_increment",
		),
		'pid'            => array
		(
			'foreignKey' => 'tl_submission_archive.title',
			'sql'        => "int(10) unsigned NOT NULL default '0'",
			'relation'   => array('type' => 'belongsTo', 'load' => 'eager'),
		),
		'tstamp'         => array
		(
			'sql' => "int(10) unsigned NOT NULL default '0'",
		),
		'dateAdded'      => array
		(
			'label'   => &$GLOBALS['TL_LANG']['MSC']['dateAdded'],
			'sorting' => true,
			'flag'    => 6,
			'eval'    => array('rgxp' => 'datim', 'doNotCopy' => true),
			'sql'     => "int(10) unsigned NOT NULL default '0'",
		),
		'type' => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission']['type'],
			'exclude'   => true,
			'filter'    => true,
			'inputType' => 'select',
			'reference' => &$GLOBALS['TL_LANG']['tl_submission']['reference']['type'],
			'eval'      => array('includeBlankOption' => true, 'mandatory' => true, 'tl_class' => 'w50'),
			'sql'       => "varchar(64) NOT NULL default ''"
		),
		'gender'         => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission']['gender'],
			'exclude'   => true,
			'inputType' => 'select',
			'options'   => array('male', 'female'),
			'reference' => &$GLOBALS['TL_LANG']['MSC'],
			'eval'      => array('mandatory' => true, 'tl_class' => 'w50 clr'),
			'sql'       => "varchar(32) NOT NULL default ''",
		),
		'academicTitle'  => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission']['academicTitle'],
			'exclude'   => true,
			'inputType' => 'select',
			'options'   => array('Dr.', 'Prof.'),
			'eval'      => array(
				'maxlength'          => 255,
				'includeBlankOption' => true,
				'tl_class'           => 'w50',
			),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'additionalTitle' => array(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission']['additionalTitle'],
			'exclude'   => true,
			'search'    => true,
			'inputType' => 'text',
			'eval'      => array('maxlength' => 255, 'tl_class' => 'w50'),
			'sql'       => "varchar(255) NOT NULL default ''"
		),
		'firstname'      => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission']['firstname'],
			'exclude'   => true,
			'search'    => true,
			'sorting'   => true,
			'flag'      => 1,
			'inputType' => 'text',
			'eval'      => array(
				'mandatory' => true,
				'maxlength' => 255,
				'tl_class'  => 'w50',
			),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'lastname'       => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission']['lastname'],
			'exclude'   => true,
			'search'    => true,
			'sorting'   => true,
			'flag'      => 1,
			'inputType' => 'text',
			'eval'      => array(
				'mandatory' => true,
				'maxlength' => 255,
				'tl_class'  => 'w50',
			),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'company'        => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission']['company'],
			'exclude'   => true,
			'search'    => true,
			'sorting'   => true,
			'flag'      => 1,
			'inputType' => 'text',
			'eval'      => array('maxlength' => 255, 'tl_class' => 'w50'),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'dateOfBirth'    => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission']['dateOfBirth'],
			'exclude'   => true,
			'inputType' => 'text',
			'eval'      => array('datepicker' => true, 'rgxp' => 'date', 'tl_class' => 'w50 wizard'),
			'sql'       => "varchar(10) NOT NULL default ''",
		),
		'street'         => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission']['street'],
			'exclude'   => true,
			'search'    => true,
			'inputType' => 'text',
			'eval'      => array('maxlength' => 255, 'tl_class' => 'w50'),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'street2'         => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission']['street2'],
			'exclude'   => true,
			'search'    => true,
			'inputType' => 'text',
			'eval'      => array('maxlength' => 255, 'tl_class' => 'w50'),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'postal'         => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission']['postal'],
			'exclude'   => true,
			'search'    => true,
			'inputType' => 'text',
			'eval'      => array('maxlength' => 32, 'tl_class' => 'w50'),
			'sql'       => "varchar(32) NOT NULL default ''",
		),
		'city'           => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission']['city'],
			'exclude'   => true,
			'filter'    => true,
			'search'    => true,
			'sorting'   => true,
			'inputType' => 'text',
			'eval'      => array('maxlength' => 255, 'tl_class' => 'w50'),
			'sql'       => "varchar(255) NOT NULL default ''",
		),
		'country'        => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission']['country'],
			'exclude'   => true,
			'filter'    => true,
			'sorting'   => true,
			'inputType' => 'select',
			'options'   => \System::getCountries(),
			'eval'      => array(
				'includeBlankOption'        => true,
				'chosen'                    => true,
				'autoCompletionHiddenField' => true,
				'tl_class'                  => 'w50',
			),
			'sql'       => "varchar(2) NOT NULL default ''",
		),
		'email'          => array
		(
			'label'         => &$GLOBALS['TL_LANG']['tl_submission']['email'],
			'exclude'       => true,
			'search'        => true,
			'inputType'     => 'text',
			'save_callback' => array(array('HeimrichHannot\Haste\Dca\General', 'lowerCase')),
			'eval'          => array(
				'mandatory'                 => true,
				'maxlength'                 => 255,
				'autoCompletionHiddenField' => true,
				'rgxp'                      => 'email',
				'decodeEntities'            => true,
				'tl_class'                  => 'w50',
			),
			'sql'           => "varchar(255) NOT NULL default ''",
		),
		'phone'          => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission']['phone'],
			'exclude'   => true,
			'search'    => true,
			'inputType' => 'text',
			'eval'      => array(
				'maxlength'      => 64,
				'rgxp'           => 'phone',
				'decodeEntities' => true,
				'tl_class'       => 'w50',
			),
			'sql'       => "varchar(64) NOT NULL default ''",
		),
		'fax'          => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission']['fax'],
			'exclude'   => true,
			'search'    => true,
			'inputType' => 'text',
			'eval'      => array(
				'maxlength'      => 64,
				'rgxp'           => 'phone',
				'decodeEntities' => true,
				'tl_class'       => 'w50',
			),
			'sql'       => "varchar(64) NOT NULL default ''",
		),
		'subject' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_submission']['subject'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength' => 255, 'tl_class' => 'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'notes'          => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission']['notes'],
			'exclude'   => true,
			'inputType' => 'textarea',
			'eval'      => array('tl_class' => 'long clr'),
			'sql'       => "text NULL",
		),
		'agreement'      => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission']['agreement'],
			'exclude'   => true,
			'filter'    => true,
			'inputType' => 'checkbox',
			'eval'      => array('mandatory' => true, 'tl_class' => 'w50', 'doNotCopy' => true),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'privacy'      => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission']['privacy'],
			'exclude'   => true,
			'filter'    => true,
			'inputType' => 'checkbox',
			'eval'      => array('mandatory' => true, 'tl_class' => 'w50', 'doNotCopy' => true),
			'sql'       => "char(1) NOT NULL default ''",
		),
		'published'      => array
		(
			'label'     => &$GLOBALS['TL_LANG']['tl_submission']['published'],
			'exclude'   => true,
			'filter'    => true,
			'inputType' => 'checkbox',
			'eval'      => array('tl_class' => 'w50', 'doNotCopy' => true),
			'sql'       => "char(1) NOT NULL default ''",
		),
		// misc
		'captcha'        => array(
			'label'     => $GLOBALS['TL_LANG']['MSC']['securityQuestion'],
			'inputType' => 'captcha',
			'eval'      => array(
				'mandatory' => true,
				'required'  => true,
				'tableless' => true,
			),
		),
		'formHybridBlob' => array
		(
			'sql' => "blob NULL",
		)
	),
);

// add attachment field
if (in_array('multifileupload', \ModuleLoader::getActive()))
{
	$arrDca['fields']['attachments'] = array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_submission']['attachments'],
		'exclude'   => true,
		'inputType' => 'multifileupload',
		'eval'      => array(
			'explanation'    => &$GLOBALS['TL_LANG']['tl_submission']['attachmentsExplanation'],
			'tl_class'       => 'clr',
			'filesOnly'      => true,
			'maxFiles'       => 5,
			'fieldType'      => 'checkbox',
			'extensions'     => \Config::get('uploadTypes'),
			'maxUploadSize'  => 10,
			'uploadFolder'   => \HeimrichHannot\Submissions\Submissions::getDefaultAttachmentSRC(),
			'addRemoveLinks' => true,
			'multiple'       => true,
		),
		'sql'       => "blob NULL",
	);
}

\HeimrichHannot\Haste\Dca\General::addAuthorFieldAndCallback('tl_submission');