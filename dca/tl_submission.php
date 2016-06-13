<?php

$GLOBALS['TL_DCA']['tl_submission'] = array
(
	'config'   => array
	(
		'dataContainer'     => 'Table',
		'ptable'            => 'tl_submission_archive',
		'enableVersioning'  => true,
		'onload_callback' => array
		(
			array('tl_submission', 'checkPermission'),
			array('tl_submission', 'initPalette'),
		),
		'onsubmit_callback' => array
		(
			array('HeimrichHannot\Haste\Dca\General', 'setDateAdded'),
		),
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary'
			)
		)
	),
	'list'     => array
	(
		'label' => array
		(
			'fields' => array('id'),
			'format' => '%s'
		),
		'sorting'           => array
		(
			'mode'                  => 4,
			'fields'                => array('dateAdded DESC'),
			'headerFields'          => array('title'),
			'panelLayout'           => 'filter;search,limit',
			'child_record_callback' => array('tl_submission', 'listChildren')
		),
		'global_operations' => array
		(
			'export_csv' => \HeimrichHannot\Exporter\ModuleExporter::getGlobalOperation('export_csv',
				$GLOBALS['TL_LANG']['MSC']['export_csv'],
				'system/modules/exporter/assets/img/icon_export.png'),
			'export_xls' => \HeimrichHannot\Exporter\ModuleExporter::getGlobalOperation('export_xls',
				$GLOBALS['TL_LANG']['MSC']['export_xls'],
				'system/modules/exporter/assets/img/icon_export.png'),
			'all'    => array
			(
				'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'       => 'act=select',
				'class'      => 'header_edit_all',
				'attributes' => 'onclick="Backend.getScrollOffset();"'
			)
		),
		'operations' => array
		(
			'edit'   => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_submission']['edit'],
				'href'  => 'act=edit',
				'icon'  => 'edit.gif'
			),
			'copy'   => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_submission']['copy'],
				'href'  => 'act=copy',
				'icon'  => 'copy.gif'
			),
			'delete' => array
			(
				'label'      => &$GLOBALS['TL_LANG']['tl_submission']['delete'],
				'href'       => 'act=delete',
				'icon'       => 'delete.gif',
				'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'toggle' => array
			(
				'label'           => &$GLOBALS['TL_LANG']['tl_submission']['toggle'],
				'icon'            => 'visible.gif',
				'attributes'      => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
				'button_callback' => array('tl_submission', 'toggleIcon')
			),
			'send_confirmation' => array
			(
				'label'           => &$GLOBALS['TL_LANG']['tl_submission']['send_confirmation'],
				'icon'            => 'system/modules/submissions/assets/img/icon_send_confirmation.png',
				'href'            => 'key=send_confirmation',
				'attributes'      => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['sendConfirmationConfirm'] . '\'))return false;Backend.getScrollOffset()"',
				'button_callback' => array('tl_submission', 'sendConfirmation')
			),
			'show'   => array
			(
				'label' => &$GLOBALS['TL_LANG']['tl_submission']['show'],
				'href'  => 'act=show',
				'icon'  => 'show.gif'
			)
		)
	),
	'palettes' => array(
		'default' => '{general_legend},userAuthor,memberAuthor;{submission_legend},submissionFields;{publish_legend},published;'
	),
	'fields'   => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'pid' => array
		(
			'foreignKey'              => 'tl_submission_archive.title',
			'sql'                     => "int(10) unsigned NOT NULL default '0'",
			'relation'                => array('type' => 'belongsTo', 'load' => 'eager')
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'dateAdded' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['MSC']['dateAdded'],
			'sorting'                 => true,
			'flag'                    => 6,
			'eval'                    => array('rgxp' => 'datim', 'doNotCopy' => true),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'userAuthor' => array
		(
			'label'      => &$GLOBALS['TL_LANG']['tl_submission']['userAuthor'],
			'default'    => BackendUser::getInstance()->id,
			'exclude'    => true,
			'search'     => true,
			'filter'     => true,
			'sorting'    => true,
			'flag'       => 11,
			'inputType'  => 'select',
			'foreignKey' => 'tl_user.name',
			'eval'       => array('doNotCopy' => true, 'chosen' => true, 'includeBlankOption' => true,
								  'tl_class'  => 'w50 clr'
			),
			'sql'        => "int(10) unsigned NOT NULL default '0'",
			'relation'   => array('type' => 'hasOne', 'load' => 'eager')
		),
		'memberAuthor' => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_submission']['memberAuthor'],
			'exclude'          => true,
			'search'           => true,
			'filter'           => true,
			'sorting'          => true,
			'flag'             => 11,
			'inputType'        => 'select',
			'options_callback' => array('HeimrichHannot\Haste\Dca\General', 'getMembersAsOptions'),
			'eval'             => array('doNotCopy' => true, 'chosen' => true, 'includeBlankOption' => true,
										'tl_class'  => 'w50'
			),
			'sql'              => "int(10) unsigned NOT NULL default '0'",
		),
		'gender' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_submission']['gender'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options'                 => array('male', 'female'),
			'reference'               => &$GLOBALS['TL_LANG']['MSC'],
			'eval'                    => array('mandatory' => true, 'includeBlankOption' => true,
											   'autoCompletionHiddenField' => true, 'tl_class' => 'w50 clr'),
			'sql'                     => "varchar(32) NOT NULL default ''"
		),
		'academicTitle' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_submission']['academicTitle'],
			'exclude'                 => true,
			'inputType'               => 'select',
			'options'                 => array('Dr.', 'Prof.'),
			'eval'                    => array('maxlength' => 255, 'includeBlankOption' => true,
											   'autoCompletionHiddenField' => true, 'tl_class' => 'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'firstname' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_submission']['firstname'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('mandatory' => true, 'maxlength' => 255,
											   'autoCompletionHiddenField' => true, 'tl_class' => 'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'lastname' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_submission']['lastname'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('mandatory' => true, 'maxlength' => 255,
											   'autoCompletionHiddenField' => true, 'tl_class' => 'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'dateOfBirth' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_submission']['dateOfBirth'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('datepicker' => true, 'rgxp' => 'date', 'tl_class' => 'w50 wizard'),
			'sql'                     => "varchar(10) NOT NULL default ''"
		),
		'street' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_submission']['street'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength' => 255, 'autoCompletionHiddenField' => true, 'tl_class' => 'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'postal' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_submission']['postal'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>32, 'autoCompletionHiddenField' => true, 'tl_class' => 'w50'),
			'sql'                     => "varchar(32) NOT NULL default ''"
		),
		'city' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_submission']['city'],
			'exclude'                 => true,
			'filter'                  => true,
			'search'                  => true,
			'sorting'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength' => 255, 'autoCompletionHiddenField' => true, 'tl_class' => 'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'country' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_submission']['country'],
			'exclude'                 => true,
			'filter'                  => true,
			'sorting'                 => true,
			'inputType'               => 'select',
			'options'                 => \System::getCountries(),
			'eval'                    => array('includeBlankOption' => true, 'chosen' => true, 'autoCompletionHiddenField' => true,
											   'tl_class' => 'w50'),
			'sql'                     => "varchar(2) NOT NULL default ''"
		),
		'email' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_submission']['email'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'save_callback'           => array(array('HeimrichHannot\Haste\Dca\General', 'lowerCase')),
			'eval'                    => array('mandatory' => true, 'maxlength' => 255, 'autoCompletionHiddenField' => true,
											   'rgxp' => 'email', 'decodeEntities' => true, 'tl_class' => 'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'phone' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_submission']['phone'],
			'exclude'                 => true,
			'search'                  => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>64, 'rgxp' => 'phone', 'decodeEntities' => true,
											   'autoCompletionHiddenField' => true, 'tl_class' => 'w50'),
			'sql'                     => "varchar(64) NOT NULL default ''"
		),
		'notes' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_submission']['notes'],
			'exclude'                 => true,
			'inputType'               => 'textarea',
			'eval'                    => array('tl_class'=>'long clr'),
			'sql'                     => "text NULL"
		),
		'published' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_submission']['published'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('tl_class' => 'w50', 'doNotCopy' => true),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		// misc
		'captcha' => array(
			'label' 		=> $GLOBALS['TL_LANG']['MSC']['securityQuestion'],
			'inputType' 	=> 'captcha',
			'eval' => array(
				'mandatory' => true,
				'required' => true,
				'tableless' => true
			)
		),
		'formHybridBlob' => array
		(
			'sql' => "blob NULL"
		)
	)
);

class tl_submission extends \Backend
{

	public function listChildren($arrRow)
	{
		$strTitle = $arrRow['id'];

		if (($objSubmission = \HeimrichHannot\Submissions\SubmissionModel::findByPk($arrRow['id'])) !== null &&
			($objSubmissionArchive = $objSubmission->getRelated('pid')) !== null)
		{
			$strTitle = preg_replace_callback('@%([^%]+)%@i', function ($arrMatches) use ($objSubmission) {
				return $objSubmission->{$arrMatches[1]};
			}, $objSubmissionArchive->titlePattern);
		}

		return '<div class="tl_content_left">' . $strTitle . ' <span style="color:#b3b3b3; padding-left:3px">[' .
				\Date::parse(Config::get('datimFormat'), trim($arrRow['dateAdded'])) . ']</span></div>';
	}

	public function sendConfirmation($row, $href, $label, $title, $icon, $attributes)
	{
		if (($objSubmission = \HeimrichHannot\Submissions\SubmissionModel::findByPk($row['id'])) !== null)
		{
			if (($objSubmissionArchive = $objSubmission->getRelated('pid')) !== null && $objSubmissionArchive->nc_confirmation)
			{
				$href = $this->addToUrl($href);
				$href = \HeimrichHannot\Haste\Util\Url::addQueryString('id=' . $row['id'], $href);

				return '<a href="' . $href . '" title="' . specialchars($title) . '"' . $attributes . '>' .
							Image::getHtml($icon, $label) . '</a> ';
			}
		}
	}

	public function checkPermission()
	{
		$objUser = \BackendUser::getInstance();
		$objSession = \Session::getInstance();
		$objDatabase = \Database::getInstance();

		if ($objUser->isAdmin)
		{
			return;
		}

		// Set the root IDs
		if (!is_array($objUser->submissionss) || empty($objUser->submissionss))
		{
			$root = array(0);
		}
		else
		{
			$root = $objUser->submissionss;
		}

		$id = strlen(Input::get('id')) ? Input::get('id') : CURRENT_ID;

		if (\Input::get('key') == 'send_confirmation')
			return;

		// Check current action
		switch (Input::get('act'))
		{
			case 'paste':
				// Allow
				break;

			case 'create':
				if (!strlen(Input::get('pid')) || !in_array(Input::get('pid'), $root))
				{
					\Controller::log('Not enough permissions to create submission items in submission archive ID "'.Input::get('pid').'"', 'tl_submission checkPermission', TL_ERROR);
					\Controller::redirect('contao/main.php?act=error');
				}
				break;

			case 'cut':
			case 'copy':
				if (!in_array(Input::get('pid'), $root))
				{
					\Controller::log('Not enough permissions to '.Input::get('act').' submission item ID "'.$id.'" to submission archive ID "'.Input::get('pid').'"', 'tl_submission checkPermission', TL_ERROR);
					\Controller::redirect('contao/main.php?act=error');
				}
				// NO BREAK STATEMENT HERE

			case 'edit':
			case 'show':
			case 'delete':
			case 'toggle':
			case 'feature':
				$objArchive = $objDatabase->prepare("SELECT pid FROM tl_submission WHERE id=?")
					->limit(1)->execute($id);

				if ($objArchive->numRows < 1)
				{
					\Controller::log('Invalid submission item ID "'.$id.'"', 'tl_submission checkPermission', TL_ERROR);
					\Controller::redirect('contao/main.php?act=error');
				}

				if (!in_array($objArchive->pid, $root))
				{
					\Controller::log('Not enough permissions to '.Input::get('act').' submission item ID "'.$id.'" of submission archive ID "'.$objArchive->pid.'"', 'tl_submission checkPermission', TL_ERROR);
					\Controller::redirect('contao/main.php?act=error');
				}
			break;

			case 'select':
			case 'editAll':
			case 'deleteAll':
			case 'overrideAll':
			case 'cutAll':
			case 'copyAll':
				if (!in_array($id, $root))
				{
					\Controller::log('Not enough permissions to access submission archive ID "'.$id.'"', 'tl_submission checkPermission', TL_ERROR);
					\Controller::redirect('contao/main.php?act=error');
				}

				$objArchive = $objDatabase->prepare("SELECT id FROM tl_submission WHERE pid=?")->execute($id);

				if ($objArchive->numRows < 1)
				{
					\Controller::log('Invalid submission archive ID "'.$id.'"', 'tl_submission checkPermission', TL_ERROR);
					\Controller::redirect('contao/main.php?act=error');
				}

				$session = $objSession->getData();
				$session['CURRENT']['IDS'] = array_intersect($session['CURRENT']['IDS'], $objArchive->fetchEach('id'));
				$objSession->setData($session);
				break;

			default:
				if (strlen(Input::get('act')))
				{
					\Controller::log('Invalid command "'.Input::get('act').'"', 'tl_submission checkPermission', TL_ERROR);
					\Controller::redirect('contao/main.php?act=error');
				}
				elseif (!in_array($id, $root))
				{
					\Controller::log('Not enough permissions to access submission archive ID ' . $id, 'tl_submission checkPermission', TL_ERROR);
					\Controller::redirect('contao/main.php?act=error');
				}
				break;
		}
	}

	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		$objUser = \BackendUser::getInstance();

		if (strlen(Input::get('tid')))
		{
			$this->toggleVisibility(Input::get('tid'), (Input::get('state') == 1));
			\Controller::redirect($this->getReferer());
		}

		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if (!$objUser->isAdmin && !$objUser->hasAccess('tl_submission::published', 'alexf'))
		{
			return '';
		}

		$href .= '&amp;tid='.$row['id'].'&amp;state='.($row['published'] ? '' : 1);

		if (!$row['published'])
		{
			$icon = 'invisible.gif';
		}

		return '<a href="'.$this->addToUrl($href).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ';
	}

	public function toggleVisibility($intId, $blnVisible)
	{
		$objUser = \BackendUser::getInstance();
		$objDatabase = \Database::getInstance();

		// Check permissions to publish
		if (!$objUser->isAdmin && !$objUser->hasAccess('tl_submission::published', 'alexf'))
		{
			\Controller::log('Not enough permissions to publish/unpublish item ID "'.$intId.'"', 'tl_submission toggleVisibility', TL_ERROR);
			\Controller::redirect('contao/main.php?act=error');
		}

		$objVersions = new Versions('tl_submission', $intId);
		$objVersions->initialize();

		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_submission']['fields']['published']['save_callback']))
		{
			foreach ($GLOBALS['TL_DCA']['tl_submission']['fields']['published']['save_callback'] as $callback)
			{
				$this->import($callback[0]);
				$blnVisible = $this->$callback[0]->$callback[1]($blnVisible, $this);
			}
		}

		// Update the database
		$objDatabase->prepare("UPDATE tl_submission SET tstamp=". time() .", published='" . ($blnVisible ? 1 : '') . "' WHERE id=?")
			->execute($intId);

		$objVersions->create();
		\Controller::log('A new version of record "tl_submission.id='.$intId.'" has been created'.$this->getParentEntries('tl_submission', $intId), 'tl_submission toggleVisibility()', TL_GENERAL);
	}

	public function initPalette(\DataContainer $objDc)
	{
		\Controller::loadDataContainer('tl_submission');
		$arrDca = &$GLOBALS['TL_DCA']['tl_submission'];

		if (($objSubmission = \HeimrichHannot\Submissions\SubmissionModel::findByPk($objDc->id)) !== null)
		{
			if (($objSubmissionArchive = $objSubmission->getRelated('pid')) !== null)
			{
				$arrDca['palettes']['defaultBackup'] = $arrDca['palettes']['default'];

				$arrDca['palettes']['default'] = str_replace('submissionFields', implode(',',
						deserialize($objSubmissionArchive->submissionFields, true)), $arrDca['palettes']['default']);
			}
		}
	}

}
