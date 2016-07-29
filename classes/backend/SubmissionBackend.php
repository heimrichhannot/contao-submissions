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

use HeimrichHannot\Haste\Util\Arrays;
use HeimrichHannot\Haste\Util\Files;
use HeimrichHannot\Submissions\Util\Tokens;

class SubmissionBackend extends \Backend
{
	public function listChildren($arrRow)
	{
		$strTitle = $arrRow['id'];
		
		if (($objSubmission = \HeimrichHannot\Submissions\SubmissionModel::findByPk($arrRow['id'])) !== null && ($objSubmissionArchive = $objSubmission->getRelated('pid')) !== null) {
			$strTitle = preg_replace_callback(
				'@%([^%]+)%@i',
				function ($arrMatches) use ($objSubmission) {
					return $objSubmission->{$arrMatches[1]};
				},
				$objSubmissionArchive->titlePattern
			);
		}
		
		return '<div class="tl_content_left">' . $strTitle . ' <span style="color:#b3b3b3; padding-left:3px">[' .
			   \Date::parse(\Config::get('datimFormat'), trim($arrRow['dateAdded'])) . ']</span></div>';
	}
	
	public function sendConfirmation($row, $href, $label, $title, $icon, $attributes)
	{
		if (($objSubmission = \HeimrichHannot\Submissions\SubmissionModel::findByPk($row['id'])) !== null) {
			if (($objSubmissionArchive = $objSubmission->getRelated('pid')) !== null && $objSubmissionArchive->nc_confirmation) {
				$href = $this->addToUrl($href);
				$href = \HeimrichHannot\Haste\Util\Url::addQueryString('id=' . $row['id'], $href);
				
				return '<a href="' . $href . '" title="' . specialchars($title) . '"' . $attributes . '>' .
					   \Image::getHtml($icon, $label) . '</a> ';
			}
		}
	}
	
	public function checkPermission()
	{
		$objUser     = \BackendUser::getInstance();
		$objSession  = \Session::getInstance();
		$objDatabase = \Database::getInstance();
		
		if ($objUser->isAdmin) {
			return;
		}
		
		// Set the root IDs
		if (!is_array($objUser->submissionss) || empty($objUser->submissionss)) {
			$root = array(0);
		} else {
			$root = $objUser->submissionss;
		}
		
		$id = strlen(\Input::get('id')) ? \Input::get('id') : CURRENT_ID;
		
		if (\Input::get('key') == 'send_confirmation') {
			return;
		}
		
		// Check current action
		switch (\Input::get('act')) {
			case 'paste':
				// Allow
				break;
			
			case 'create':
				if (!strlen(\Input::get('pid')) || !in_array(\Input::get('pid'), $root)) {
					\Controller::log('Not enough permissions to create submission items in submission archive ID "' . \Input::get('pid') . '"', 'tl_submission checkPermission', TL_ERROR);
					\Controller::redirect('contao/main.php?act=error');
				}
				break;
			
			case 'cut':
			case 'copy':
				if (!in_array(\Input::get('pid'), $root)) {
					\Controller::log(
						'Not enough permissions to ' . \Input::get('act') . ' submission item ID "' . $id . '" to submission archive ID "' . \Input::get('pid') . '"',
						'tl_submission checkPermission',
						TL_ERROR
					);
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
				
				if ($objArchive->numRows < 1) {
					\Controller::log('Invalid submission item ID "' . $id . '"', 'tl_submission checkPermission', TL_ERROR);
					\Controller::redirect('contao/main.php?act=error');
				}
				
				if (!in_array($objArchive->pid, $root)) {
					\Controller::log(
						'Not enough permissions to ' . \Input::get('act') . ' submission item ID "' . $id . '" of submission archive ID "' . $objArchive->pid . '"',
						'tl_submission checkPermission',
						TL_ERROR
					);
					\Controller::redirect('contao/main.php?act=error');
				}
				break;
			
			case 'select':
			case 'editAll':
			case 'deleteAll':
			case 'overrideAll':
			case 'cutAll':
			case 'copyAll':
				if (!in_array($id, $root)) {
					\Controller::log('Not enough permissions to access submission archive ID "' . $id . '"', 'tl_submission checkPermission', TL_ERROR);
					\Controller::redirect('contao/main.php?act=error');
				}
				
				$objArchive = $objDatabase->prepare("SELECT id FROM tl_submission WHERE pid=?")->execute($id);
				
				if ($objArchive->numRows < 1) {
					\Controller::log('Invalid submission archive ID "' . $id . '"', 'tl_submission checkPermission', TL_ERROR);
					\Controller::redirect('contao/main.php?act=error');
				}
				
				$session                   = $objSession->getData();
				$session['CURRENT']['IDS'] = array_intersect($session['CURRENT']['IDS'], $objArchive->fetchEach('id'));
				$objSession->setData($session);
				break;
			
			default:
				if (strlen(\Input::get('act'))) {
					\Controller::log('Invalid command "' . \Input::get('act') . '"', 'tl_submission checkPermission', TL_ERROR);
					\Controller::redirect('contao/main.php?act=error');
				} elseif (!in_array($id, $root)) {
					\Controller::log('Not enough permissions to access submission archive ID ' . $id, 'tl_submission checkPermission', TL_ERROR);
					\Controller::redirect('contao/main.php?act=error');
				}
				break;
		}
	}
	
	public function toggleIcon($row, $href, $label, $title, $icon, $attributes)
	{
		$objUser = \BackendUser::getInstance();
		
		if (strlen(\Input::get('tid'))) {
			$this->toggleVisibility(\Input::get('tid'), (\Input::get('state') == 1));
			\Controller::redirect($this->getReferer());
		}
		
		// Check permissions AFTER checking the tid, so hacking attempts are logged
		if (!$objUser->isAdmin && !$objUser->hasAccess('tl_submission::published', 'alexf')) {
			return '';
		}
		
		$href .= '&amp;tid=' . $row['id'] . '&amp;state=' . ($row['published'] ? '' : 1);
		
		if (!$row['published']) {
			$icon = 'invisible.gif';
		}
		
		return '<a href="' . $this->addToUrl($href) . '" title="' . specialchars($title) . '"' . $attributes . '>' . \Image::getHtml($icon, $label) . '</a> ';
	}
	
	public function toggleVisibility($intId, $blnVisible)
	{
		$objUser     = \BackendUser::getInstance();
		$objDatabase = \Database::getInstance();
		
		// Check permissions to publish
		if (!$objUser->isAdmin && !$objUser->hasAccess('tl_submission::published', 'alexf')) {
			\Controller::log('Not enough permissions to publish/unpublish item ID "' . $intId . '"', 'tl_submission toggleVisibility', TL_ERROR);
			\Controller::redirect('contao/main.php?act=error');
		}
		
		$objVersions = new \Versions('tl_submission', $intId);
		$objVersions->initialize();
		
		// Trigger the save_callback
		if (is_array($GLOBALS['TL_DCA']['tl_submission']['fields']['published']['save_callback'])) {
			foreach ($GLOBALS['TL_DCA']['tl_submission']['fields']['published']['save_callback'] as $callback) {
				$this->import($callback[0]);
				$blnVisible = $this->$callback[0]->$callback[1]($blnVisible, $this);
			}
		}
		
		// Update the database
		$objDatabase->prepare("UPDATE tl_submission SET tstamp=" . time() . ", published='" . ($blnVisible ? 1 : '') . "' WHERE id=?")
			->execute($intId);
		
		$objVersions->create();
		\Controller::log(
			'A new version of record "tl_submission.id=' . $intId . '" has been created' . $this->getParentEntries('tl_submission', $intId),
			'tl_submission toggleVisibility()',
			TL_GENERAL
		);
	}
	
	public function modifyPalette(\DataContainer $objDc, $blnFrontend=false)
	{
		\Controller::loadDataContainer('tl_submission');
		$arrDca = &$GLOBALS['TL_DCA']['tl_submission'];
		
		if (($objSubmission = \HeimrichHannot\Submissions\SubmissionModel::findByPk($objDc->id)) === null)
		{
			return false;
		}
		
		if ($objSubmission->authorType == \HeimrichHannot\Submissions\Submissions::AUTHOR_TYPE_NONE)
		{
			unset($arrDca['fields']['author']);
		}
		
		if ($objSubmission->authorType == \HeimrichHannot\Submissions\Submissions::AUTHOR_TYPE_USER) {
			$arrDca['fields']['author']['options_callback'] = array('HeimrichHannot\Haste\Dca\User', 'getUsersAsOptions');
		}
		
		if (($objSubmissionArchive = $objSubmission->getRelated('pid')) === null)
		{
			return false;
		}
		
		$arrDca['palettes']['defaultBackup'] = $arrDca['palettes']['default'];
		
		$arrSubmissionFields = deserialize($objSubmissionArchive->submissionFields, true);
		
		// remove subpalette fields from arrSubmissionFields
		if (is_array($arrDca['subpalettes'])) {
			foreach ($arrDca['subpalettes'] as $key => $value) {
				$arrSubpaletteFields = \HeimrichHannot\FormHybrid\FormHelper::getPaletteFields($objDc->table, $value);
				
				if (!is_array($arrSubpaletteFields)) {
					continue;
				}
				
				$arrSubmissionFields = array_diff($arrSubmissionFields, $arrSubpaletteFields);
			}
		}
		
		$arrDca['palettes']['default'] = str_replace(
			'submissionFields',
			implode(',', $arrSubmissionFields),
			\HeimrichHannot\Submissions\Submissions::PALETTE_DEFAULT
		);
		
		// overwrite attachment config with archive
		if(isset($arrDca['fields']['attachments']) && $objSubmissionArchive->addAttachmentConfig)
		{
			$arrConfig = Arrays::filterByPrefixes($objSubmissionArchive->row(), array('attachment'));
			
			foreach ($arrConfig as $strKey => $value)
			{
				$strKey = lcfirst(str_replace('attachment', '', $strKey));
				
				$arrDca['fields']['attachments']['eval'][$strKey] = $value;
			}
		}
	}
	
	public function moveAttachments(\DataContainer $objDc)
	{
		if (($objSubmission = \HeimrichHannot\Submissions\SubmissionModel::findByPk($objDc->id)) === null)
		{
			return false;
		}
		
		if (($objSubmissionArchive = $objSubmission->getRelated('pid')) === null)
		{
			return false;
		}
		
		if(!$objSubmission->attachments)
		{
			return false;
		}
		
		$strSubFolder = Tokens::replace($objSubmissionArchive->attachmentSubFolderPattern, $objSubmission);
		$objFileModels = \FilesModel::findMultipleByUuids(deserialize($objSubmission->attachments, true));
		
		if($objFileModels === null || $strSubFolder == '')
		{
			return false;
		}
		
		$strFolder = $objSubmissionArchive->attachmentUploadFolder;
		
		if(\Validator::isUuid($objSubmissionArchive->attachmentUploadFolder))
		{
			if(($strFolder = Files::getPathFromUuid($objSubmissionArchive->attachmentUploadFolder)) === null)
			{
				return false;
			}
		}
		
		$strTarget = rtrim($strFolder, '/') . '/' . ltrim($strSubFolder, '/');
		
		while($objFileModels->next())
		{
			if(!file_exists(TL_ROOT . '/' . $objFileModels->path))
			{
				continue;
			}

			$objFile = new \File($objFileModels->path, true);
			$objFile->renameTo($strTarget . '/' . basename($objFile->value));
		}
	}
}