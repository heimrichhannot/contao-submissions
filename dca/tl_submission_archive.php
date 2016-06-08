<?php

$GLOBALS['TL_DCA']['tl_submission_archive'] = array
(
	'config' => array
	(
		'dataContainer'     => 'Table',
		'ctable'            => array('tl_submission'),
		'switchToEdit'                => true,
		'enableVersioning'  => true,
		'onload_callback' => array
		(
			array('tl_submission_archive', 'checkPermission')
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
	'list' => array
	(
		'label' => array
		(
			'fields' => array('title'),
			'format' => '%s'
		),
		'sorting'           => array
		(
			'mode'                  => 1,
			'fields'                => array('title'),
			'headerFields'          => array('title'),
			'panelLayout'           => 'filter;search,limit'
		),
		'global_operations' => array
		(
			'all'    => array
			(
				'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'       => 'act=select',
				'class'      => 'header_edit_all',
				'attributes' => 'onclick="Backend.getScrollOffset();"'
			),
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_submission_archive']['edit'],
				'href'                => 'table=tl_submission',
				'icon'                => 'edit.gif'
			),
			'editheader' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_submission_archive']['editheader'],
				'href'                => 'act=edit',
				'icon'                => 'header.gif',
				'button_callback'     => array('tl_submission_archive', 'editHeader')
			),
			'copy' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_submission_archive']['copy'],
				'href'                => 'act=copy',
				'icon'                => 'copy.gif',
				'button_callback'     => array('tl_submission_archive', 'copyArchive')
			),
			'delete' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_submission_archive']['copy'],
				'href'                => 'act=delete',
				'icon'                => 'delete.gif',
				'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
				'button_callback'     => array('tl_submission_archive', 'deleteArchive')
			),
			'show' => array
			(
				'label'               => &$GLOBALS['TL_LANG']['tl_submission_archive']['show'],
				'href'                => 'act=show',
				'icon'                => 'show.gif'
			),
		)
	),
	'palettes' => array
	(
		'__selector__' => array('addCleaner'),
		'default' => '{general_legend},title,parentTable,parentField,pid;{fields_legend},submissionFields,titlePattern;' .
			'{notification_legend},nc_submission,nc_confirmation;{clean_legend},addCleaner;'
	),
	'subpalettes' => array(
		'addCleaner' => 'cleanerMaxAge,cleanerPeriod'
	),
	'fields'   => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'parentTable' => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_submission_archive']['parentTable'],
			'inputType'        => 'select',
			'options_callback' => array('\HeimrichHannot\Haste\Dca\General', 'getDataContainers'),
			'sql'              => "varchar(255) NOT NULL default ''",
			'eval'             => array('tl_class' => 'w50', 'chosen' => true, 'submitOnChange' => true,
										'includeBlankOption' => true)
		),
		'parentField' => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_submission_archive']['parentField'],
			'inputType'        => 'select',
			'options_callback' => array('tl_submission_archive', 'getParentFields'),
			'sql'              => "varchar(255) NOT NULL default ''",
			'eval'             => array('tl_class' => 'w50', 'chosen' => true, 'submitOnChange' => true,
										'includeBlankOption' => true)
		),
		'pid' => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_submission_archive']['pid'],
			'inputType'        => 'select',
			'options_callback' => array('tl_submission_archive', 'getParentEntitiesAsOptions'),
			'sql'              => "int(10) unsigned NOT NULL default '0'",
			'eval'             => array('tl_class' => 'w50', 'chosen' => true, 'includeBlankOption' => true)
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
			'eval'                    => array('rgxp'=>'datim', 'doNotCopy' => true),
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'title' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_submission_archive']['title'],
			'exclude'                 => true,
			'search'                  => true,
			'sorting'                 => true,
			'flag'                    => 1,
			'inputType'               => 'text',
			'eval'                    => array('mandatory' => true, 'tl_class'=>'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'submissionFields' => array
		(
			'label'            => &$GLOBALS['TL_LANG']['tl_submission_archive']['submissionFields'],
			'exclude'          => true,
			'inputType'        => 'checkboxWizard',
			'options_callback' => array('HeimrichHannot\Submissions\Submissions', 'getFieldsAsOptions'),
			'eval'             => array('multiple' => true, 'tl_class' => 'w50 clr'),
			'sql'              => "blob NULL"
		),
		'titlePattern' => array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_submission_archive']['titlePattern'],
			'exclude'                 => true,
			'inputType'               => 'text',
			'eval'                    => array('maxlength'=>255, 'tl_class' => 'w50'),
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'nc_submission' => array
		(
			'label'                     => &$GLOBALS['TL_LANG']['tl_submission_archive']['nc_submission'],
			'exclude'                   => true,
			'inputType'                 => 'select',
			'options_callback'          => array('HeimrichHannot\Submissions\Submissions', 'getNotificationsAsOptions'),
			'eval'                      => array('includeBlankOption'=>true, 'chosen'=>true, 'tl_class'=>'w50'),
			'sql'                       => "int(10) unsigned NOT NULL default '0'"
		),
		'nc_confirmation' => array
		(
			'label'                     => &$GLOBALS['TL_LANG']['tl_submission_archive']['nc_confirmation'],
			'exclude'                   => true,
			'inputType'                 => 'select',
			'options_callback'          => array('HeimrichHannot\Submissions\Submissions', 'getConfirmationNotificationsAsOptions'),
			'eval'                      => array('includeBlankOption'=>true, 'chosen'=>true, 'tl_class'=>'w50'),
			'sql'                       => "int(10) unsigned NOT NULL default '0'"
		),
		'addCleaner' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_submission_archive']['addCleaner'],
			'exclude'                 => true,
			'inputType'               => 'checkbox',
			'eval'                    => array('submitOnChange' => true, 'tl_class' => 'w50'),
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'cleanerMaxAge' => array(
			'label'                   => &$GLOBALS['TL_LANG']['tl_submission_archive']['cleanerMaxAge'],
			'exclude'                 => true,
			'inputType'               => 'timePeriod',
			'options'                 => array('m', 'h', 'd'),
			'reference'               => &$GLOBALS['TL_LANG']['tl_submission_archive']['cleanerMaxAge'],
			'eval'                    => array('mandatory'=> true, 'tl_class' => 'w50 clr'),
			'sql'                     => "blob NULL"
		),
		'cleanerPeriod' => array
		(
			'label'                     => &$GLOBALS['TL_LANG']['tl_submission_archive']['cleanerPeriod'],
			'exclude'                   => true,
			'inputType'                 => 'select',
			'options'                   => array('minutely', 'hourly', 'daily', 'weekly', 'monthly'),
			'reference'                 => &$GLOBALS['TL_LANG']['tl_submission_archive']['cleanerPeriod'],
			'eval'                      => array('mandatory' => true, 'includeBlankOption'=>true, 'tl_class'=>'w50'),
			'sql'                       => "varchar(32) NOT NULL default ''"
		)
	)
);

class tl_submission_archive extends \Backend
{

	public static function getParentFields(\DataContainer $objDc)
	{
		if ($objDc->activeRecord->parentTable)
			return \HeimrichHannot\Haste\Dca\General::getFields($objDc->activeRecord->parentTable, false, 'text');
	}

	public static function getParentEntitiesAsOptions(\DataContainer $objDc)
	{
		$arrOptions = array();

		if ($objDc->activeRecord->parentTable && $objDc->activeRecord->parentField && ($objSubmissionArchives =
				\HeimrichHannot\Submissions\SubmissionArchiveModel::findByParentTable($objDc->activeRecord->parentTable)) !== null)
		{
			$arrUsedPids = $objSubmissionArchives->fetchEach('pid');

			if ($intPosition = array_search($objDc->activeRecord->pid, $arrUsedPids))
				unset($arrUsedPids[$intPosition]);

			$strWhere = '';

			if (!empty($arrUsedPids))
			{
				$strWhere = ' WHERE id NOT IN (' . implode(',', $arrUsedPids) . ')';
			}

			$objItems = \Database::getInstance()->execute('SELECT id, ' . $objDc->activeRecord->parentField . ' FROM ' .
				$objDc->activeRecord->parentTable . $strWhere);

			$arrOptions = array_combine($objItems->fetchEach('id'), $objItems->fetchEach($objDc->activeRecord->parentField));
		}

		return $arrOptions;
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

		// Set root IDs
		if (!is_array($objUser->submissionss) || empty($objUser->submissionss))
		{
			$root = array(0);
		}
		else
		{
			$root = $objUser->submissionss;
		}

		$GLOBALS['TL_DCA']['tl_submission_archive']['list']['sorting']['root'] = $root;

		// Check permissions to add archives
		if (!$objUser->hasAccess('create', 'submissionsp'))
		{
			$GLOBALS['TL_DCA']['tl_submission_archive']['config']['closed'] = true;
		}

		// Check current action
		switch (\Input::get('act'))
		{
			case 'create':
			case 'select':
				// Allow
				break;

			case 'edit':
				// Dynamically add the record to the user profile
				if (!in_array(\Input::get('id'), $root))
				{
					$arrNew = $objSession->get('new_records');

					if (is_array($arrNew['tl_submission_archive']) && in_array(\Input::get('id'), $arrNew['tl_submission_archive']))
					{
						// Add permissions on user level
						if ($objUser->inherit == 'custom' || !$objUser->groups[0])
						{
							$objUser = $objDatabase->prepare("SELECT submissionss, submissionsp FROM tl_user WHERE id=?")
									->limit(1)
									->execute($objUser->id);

							$arrModulep = deserialize($objUser->submissionsp);

							if (is_array($arrModulep) && in_array('create', $arrModulep))
							{
								$arrModules = deserialize($objUser->submissionss);
								$arrModules[] = \Input::get('id');

								$objDatabase->prepare("UPDATE tl_user SET submissionss=? WHERE id=?")
										->execute(serialize($arrModules), $objUser->id);
							}
						}

						// Add permissions on group level
						elseif ($objUser->groups[0] > 0)
						{
							$objGroup = $objDatabase->prepare("SELECT submissionss, submissionsp FROM tl_user_group WHERE id=?")
									->limit(1)
									->execute($objUser->groups[0]);

							$arrModulep = deserialize($objGroup->submissionsp);

							if (is_array($arrModulep) && in_array('create', $arrModulep))
							{
								$arrModules = deserialize($objGroup->submissionss);
								$arrModules[] = \Input::get('id');

								$objDatabase->prepare("UPDATE tl_user_group SET submissionss=? WHERE id=?")
										->execute(serialize($arrModules), $objUser->groups[0]);
							}
						}

						// Add new element to the user object
						$root[] = \Input::get('id');
						$objUser->submissionss = $root;
					}
				}
			// No break;

			case 'copy':
			case 'delete':
			case 'show':
				if (!in_array(\Input::get('id'), $root) || (\Input::get('act') == 'delete' && !$objUser->hasAccess('delete', 'submissionsp')))
				{
					$this->log('Not enough permissions to '.\Input::get('act').' submission_archive ID "'.\Input::get('id').'"', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;

			case 'editAll':
			case 'deleteAll':
			case 'overrideAll':
				$session = $objSession->getData();
				if (\Input::get('act') == 'deleteAll' && !$objUser->hasAccess('delete', 'submissionsp'))
				{
					$session['CURRENT']['IDS'] = array();
				}
				else
				{
					$session['CURRENT']['IDS'] = array_intersect($session['CURRENT']['IDS'], $root);
				}
				$objSession->setData($session);
				break;

			default:
				if (strlen(\Input::get('act')))
				{
					$this->log('Not enough permissions to '.\Input::get('act').' submission_archive archives', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;
		}
	}

	public function editHeader($row, $href, $label, $title, $icon, $attributes)
	{
		return \BackendUser::getInstance()->canEditFieldsOf('tl_submission_archive') ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : \Image::getHtml(preg_replace('/\.gif$/i', '_.gif', $icon)).' ';
	}

	public function copyArchive($row, $href, $label, $title, $icon, $attributes)
	{
		return \BackendUser::getInstance()->hasAccess('create', 'submissionsp') ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : \Image::getHtml(preg_replace('/\.gif$/i', '_.gif', $icon)).' ';
	}

	public function deleteArchive($row, $href, $label, $title, $icon, $attributes)
	{
		return \BackendUser::getInstance()->hasAccess('delete', 'submissionsp') ? '<a href="'.$this->addToUrl($href.'&amp;id='.$row['id']).'" title="'.specialchars($title).'"'.$attributes.'>'.Image::getHtml($icon, $label).'</a> ' : \Image::getHtml(preg_replace('/\.gif$/i', '_.gif', $icon)).' ';
	}
}
