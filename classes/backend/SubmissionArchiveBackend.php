<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Submissions\Backend;

use HeimrichHannot\Submissions\SubmissionArchiveModel;
use HeimrichHannot\Submissions\SubmissionModel;
use HeimrichHannot\Submissions\Submissions;
use HeimrichHannot\Submissions\Util\Tokens;

class SubmissionArchiveBackend extends \Backend
{
	public function onCreate($strTable, $insertID, $arrSet, \DataContainer $dc)
	{
		if(($objModel = SubmissionArchiveModel::findByPk($insertID)) === null)
		{
			return;
		}
		
		if(($uuid = Submissions::getDefaultAttachmentSRC()) !== null && \Validator::isUuid($uuid))
		{
			$objModel->attachmentUploadFolder = class_exists('Contao\StringUtil') ? \StringUtil::uuidToBin($uuid) : \String::uuidToBin($uuid);
		}
		
		$objModel->save();
	}
	
	public function setAttachmentUploadFolder($varValue, \DataContainer $dc)
	{
		if ($varValue == '')
		{
			return Submissions::getDefaultAttachmentSRC();
		}
		
		return $varValue;
	}
	
	public static function getParentFields(\DataContainer $objDc)
	{
		if ($objDc->activeRecord->parentTable) {
			return \HeimrichHannot\Haste\Dca\General::getFields($objDc->activeRecord->parentTable, false, 'text');
		}
	}
	
	public static function getParentEntitiesAsOptions(\DataContainer $objDc)
	{
		$arrOptions = array();
		
		if ($objDc->activeRecord->parentTable && $objDc->activeRecord->parentField
			&& ($objSubmissionArchives =
				\HeimrichHannot\Submissions\SubmissionArchiveModel::findByParentTable($objDc->activeRecord->parentTable)) !== null
		) {
			$arrUsedPids = $objSubmissionArchives->fetchEach('pid');
			
			if ($intPosition = array_search($objDc->activeRecord->pid, $arrUsedPids)) {
				unset($arrUsedPids[$intPosition]);
			}
			
			$strWhere = '';
			
			if (!empty($arrUsedPids)) {
				$strWhere = ' WHERE id NOT IN (' . implode(',', $arrUsedPids) . ')';
			}
			
			$objItems = \Database::getInstance()->execute(
				'SELECT id, ' . $objDc->activeRecord->parentField . ' FROM ' .
				$objDc->activeRecord->parentTable . $strWhere
			);
			
			$arrOptions = array_combine($objItems->fetchEach('id'), $objItems->fetchEach($objDc->activeRecord->parentField));
		}
		
		return $arrOptions;
	}
	
	public function checkPermission()
	{
		$objUser     = \BackendUser::getInstance();
		$objSession  = \Session::getInstance();
		$objDatabase = \Database::getInstance();
		
		if ($objUser->isAdmin) {
			return;
		}
		
		// Set root IDs
		if (!is_array($objUser->submissionss) || empty($objUser->submissionss)) {
			$root = array(0);
		} else {
			$root = $objUser->submissionss;
		}
		
		$GLOBALS['TL_DCA']['tl_submission_archive']['list']['sorting']['root'] = $root;
		
		// Check permissions to add archives
		if (!$objUser->hasAccess('create', 'submissionsp')) {
			$GLOBALS['TL_DCA']['tl_submission_archive']['config']['closed'] = true;
		}
		
		// Check current action
		switch (\Input::get('act')) {
			case 'create':
			case 'select':
				// Allow
				break;
			
			case 'edit':
				// Dynamically add the record to the user profile
				if (!in_array(\Input::get('id'), $root)) {
					$arrNew = $objSession->get('new_records');
					
					if (is_array($arrNew['tl_submission_archive']) && in_array(\Input::get('id'), $arrNew['tl_submission_archive'])) {
						// Add permissions on user level
						if ($objUser->inherit == 'custom' || !$objUser->groups[0]) {
							$objUser = $objDatabase->prepare("SELECT submissionss, submissionsp FROM tl_user WHERE id=?")
								->limit(1)
								->execute($objUser->id);
							
							$arrModulep = deserialize($objUser->submissionsp);
							
							if (is_array($arrModulep) && in_array('create', $arrModulep)) {
								$arrModules   = deserialize($objUser->submissionss);
								$arrModules[] = \Input::get('id');
								
								$objDatabase->prepare("UPDATE tl_user SET submissionss=? WHERE id=?")
									->execute(serialize($arrModules), $objUser->id);
							}
						} // Add permissions on group level
						elseif ($objUser->groups[0] > 0) {
							$objGroup = $objDatabase->prepare("SELECT submissionss, submissionsp FROM tl_user_group WHERE id=?")
								->limit(1)
								->execute($objUser->groups[0]);
							
							$arrModulep = deserialize($objGroup->submissionsp);
							
							if (is_array($arrModulep) && in_array('create', $arrModulep)) {
								$arrModules   = deserialize($objGroup->submissionss);
								$arrModules[] = \Input::get('id');
								
								$objDatabase->prepare("UPDATE tl_user_group SET submissionss=? WHERE id=?")
									->execute(serialize($arrModules), $objUser->groups[0]);
							}
						}
						
						// Add new element to the user object
						$root[]                = \Input::get('id');
						$objUser->submissionss = $root;
					}
				}
			// No break;
			
			case 'copy':
			case 'delete':
			case 'show':
				if (!in_array(\Input::get('id'), $root) || (\Input::get('act') == 'delete' && !$objUser->hasAccess('delete', 'submissionsp'))) {
					$this->log('Not enough permissions to ' . \Input::get('act') . ' submission_archive ID "' . \Input::get('id') . '"', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;
			
			case 'editAll':
			case 'deleteAll':
			case 'overrideAll':
				$session = $objSession->getData();
				if (\Input::get('act') == 'deleteAll' && !$objUser->hasAccess('delete', 'submissionsp')) {
					$session['CURRENT']['IDS'] = array();
				} else {
					$session['CURRENT']['IDS'] = array_intersect($session['CURRENT']['IDS'], $root);
				}
				$objSession->setData($session);
				break;
			
			default:
				if (strlen(\Input::get('act'))) {
					$this->log('Not enough permissions to ' . \Input::get('act') . ' submission_archive archives', __METHOD__, TL_ERROR);
					$this->redirect('contao/main.php?act=error');
				}
				break;
		}
	}
	
	public function editHeader($row, $href, $label, $title, $icon, $attributes)
	{
		return \BackendUser::getInstance()->canEditFieldsOf('tl_submission_archive')
			? '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars($title) . '"' . $attributes . '>' . \Image::getHtml($icon, $label) . '</a> '
			: \Image::getHtml(
				preg_replace('/\.gif$/i', '_.gif', $icon)
			) . ' ';
	}
	
	public function copyArchive($row, $href, $label, $title, $icon, $attributes)
	{
		return \BackendUser::getInstance()->hasAccess('create', 'submissionsp')
			? '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars($title) . '"' . $attributes . '>' . \Image::getHtml($icon, $label) . '</a> '
			: \Image::getHtml(
				preg_replace('/\.gif$/i', '_.gif', $icon)
			) . ' ';
	}
	
	public function deleteArchive($row, $href, $label, $title, $icon, $attributes)
	{
		return \BackendUser::getInstance()->hasAccess('delete', 'submissionsp')
			? '<a href="' . $this->addToUrl($href . '&amp;id=' . $row['id']) . '" title="' . specialchars($title) . '"' . $attributes . '>' . \Image::getHtml($icon, $label) . '</a> '
			: \Image::getHtml(
				preg_replace('/\.gif$/i', '_.gif', $icon)
			) . ' ';
	}
}