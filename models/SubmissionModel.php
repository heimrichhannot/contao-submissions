<?php

namespace HeimrichHannot\Submissions;

use HeimrichHannot\FormHybrid\DC_Hybrid;
use HeimrichHannot\Haste\Dca\General;
use HeimrichHannot\Haste\Util\Arrays;
use HeimrichHannot\Haste\Util\Environment;
use HeimrichHannot\Haste\Util\FormSubmission;
use HeimrichHannot\Haste\Util\Url;
use HeimrichHannot\NotificationCenterPlus\NotificationCenterPlus;
use NotificationCenter\Model\Notification;

class SubmissionModel extends \Model
{

	protected static $strTable = 'tl_submission';

	public static function getArchiveParent($intSubmission)
	{
		if (($objSubmissionArchive = static::getArchive($intSubmission)) !== null &&
			$objSubmissionArchive->parentTable && $objSubmissionArchive->pid)
		{
			if (($objArchiveParent = General::getModelInstance($objSubmissionArchive->parentTable, $objSubmissionArchive->pid)) !== null)
			{
				return $objArchiveParent;
			}
		}
	}

	public static function getArchive($intSubmission)
	{
		if (($objSubmission = SubmissionModel::findByPk($intSubmission)) !== null)
		{
			if (($objSubmissionArchive = $objSubmission->getRelated('pid')) !== null)
			{
				return $objSubmissionArchive;
			}
		}
	}

	public static function sendSubmissionNotification($intSubmission, $arrTokens = array())
	{
		$intSubmission = $intSubmission ?: \Input::get('id');

		if (($objSubmissionArchive = SubmissionModel::getArchive($intSubmission)) !== null)
		{
			if ($objSubmissionArchive->nc_submission)
			{
				static::sendNotification($intSubmission, $objSubmissionArchive->nc_submission, $arrTokens);
			}
		}
	}

	public static function sendConfirmationNotificationBe(\DataContainer $objDc)
	{
		if (($objSubmission = static::findByPk($objDc->id)) !== null)
		{
			static::sendConfirmationNotification($objSubmission->id);

			\Message::addConfirmation($GLOBALS['TL_LANG']['MSC']['confirmationNotificationSent']);
			\Controller::redirect(Url::addQueryString('id=' . $objSubmission->pid, Url::removeQueryString(array('key'))));
		}
	}

	public static function sendConfirmationNotification($intSubmission, $arrTokens = array())
	{
		if (($objSubmissionArchive = SubmissionModel::getArchive($intSubmission)) !== null)
		{
			if ($objSubmissionArchive->nc_confirmation)
			{
				static::sendNotification($intSubmission, $objSubmissionArchive->nc_confirmation, $arrTokens);
			}
		}
	}

	public static function sendNotification($intSubmission, $intNotification, $arrTokens = array())
	{
		$arrTokens += static::generateTokens($intSubmission);

		if (($objNotification = Notification::findByPk($intNotification)) !== null)
		{
			$objNotification->submission = $intSubmission;
			$objNotification->send($arrTokens, $GLOBALS['TL_LANGUAGE']);
		}
	}

	public static function generateTokens($intSubmission, $arrFields = array())
	{
		$arrTokens = array();
		\Controller::loadDataContainer('tl_submission');
		\System::loadLanguageFile('tl_submission');

		$arrDca = &$GLOBALS['TL_DCA']['tl_submission'];

		if (($objSubmission = SubmissionModel::findByPk($intSubmission)) !== null)
		{
			$objDc = new DC_Hybrid('tl_submission');
			$objDc->activeRecord = $objSubmission;
			
			// fields
			if (($objSubmissionArchive = $objSubmission->getRelated('pid')) !== null)
			{
				\Controller::loadDataContainer('tl_submission');
				
				$arrFields = empty($arrFields) ? deserialize($objSubmissionArchive->submissionFields, true) : $arrFields;

				if (isset($GLOBALS['TL_HOOKS']['preGenerateSubmissionTokens']) &&
					is_array($GLOBALS['TL_HOOKS']['preGenerateSubmissionTokens'])) {
					foreach ($GLOBALS['TL_HOOKS']['preGenerateSubmissionTokens'] as $arrCallback) {
						\System::importStatic($arrCallback[0])->$arrCallback[1]($objSubmission, $objSubmissionArchive, $arrFields);
					}
				}
			}
			
			$arrSubmissionData = static::prepareData($objSubmission, 'tl_submission', $GLOBALS['TL_DCA']['tl_submission'], $objDc, $arrFields);
			
			$arrTokens = static::tokenizeData($arrSubmissionData);
			
			// salutation
			$arrTokens['salutation_submission'] = NotificationCenterPlus::createSalutation($GLOBALS['TL_LANGUAGE'], array(
				'gender' => $arrTokens['form_value_gender'],
				'title' => $arrTokens['form_value_title'],
				'lastname' => $arrTokens['form_value_lastname']
			));
		}

		return $arrTokens;
	}

	public static function prepareData(\Model $objSubmission, $strTable, array $arrDca, $objDc, $arrFields=array())
	{
		$arrSubmissionData = array();
		$arrRow = $objSubmission->row();

		if (empty($arrFields))
			$arrFields = array_keys($arrRow);

		foreach ($arrFields as $strName)
		{
			$varValue = $arrRow[$strName];
			if(empty($varValue)) continue;

			$arrData = $arrDca['fields'][$strName];
			
			$arrFieldData = static::prepareDataField($strName, $varValue, $arrData, $strTable, $objDc);

			$arrSubmissionData[$strName] = $arrFieldData;
			$strSubmission = $arrFieldData['submission'];

			$varValue = deserialize($varValue);

			// multicolumnwizard support
			if ($arrData['inputType'] == 'multiColumnWizard') {
				foreach ($varValue as $arrSet) {
					if (!is_array($arrSet)) {
						continue;
					}

					// new line
					$strSubmission .= "\n";

					foreach ($arrSet as $strSetName => $strSetValue) {
						$arrSetData   = $arrData['eval']['columnFields'][$strSetName];
						$arrFieldData = static::prepareDataField($strSetName, $strSetValue, $arrSetData, $strTable, $objDc);
						// intend new line
						$strSubmission .= "\t" . $arrFieldData['submission'];
					}

					// new line
					$strSubmission .= "\n";
				}
			}

			$arrSubmissionData['submission_all'] .= $strSubmission;

			if(in_array($strName, $arrFields) && !in_array($strName, Submissions::getSkipFields()))
			{
				$arrSubmissionData['submission'] .= $strSubmission;
			}
		}

		return $arrSubmissionData;
	}

	public static function prepareDataField($strName, $varValue, $arrData, $strTable, $objDc)
	{
		$strLabel = isset($arrData['label'][0]) ? $arrData['label'][0] : $strName;

		$strOutput = FormSubmission::prepareSpecialValueForPrint($varValue, $arrData, $strTable ?: 'tl_submission', $objDc);

		$varValue = deserialize($varValue);

		if (is_array($varValue))
		{
			$varValue = Arrays::flattenArray($varValue);

			$varValue = array_filter($varValue); // remove empty elements

			$varValue = implode(', ', $varValue);
		}

		$strSubmission = $strLabel . ": " . $strOutput . "\n";

		return array('value' => $varValue, 'output' => $strOutput, 'submission' => $strSubmission);
	}

	public static function tokenizeData(array $arrSubmissionData = array(), $strPrefix = 'form')
	{
		$arrTokens = array();

		foreach($arrSubmissionData as $strName => $arrData)
		{
			if(!is_array($arrData))
			{
				if ($strName != 'submission' && $strName != 'submission_all' && !is_object($arrData))
				{
					$arrTokens[$strName] = $arrData;
					continue;
				}
				else
				{
					continue;
				}
			}

			foreach($arrData as $strType => $varValue)
			{
				switch($strType)
				{
					case 'output':
						$arrTokens[$strPrefix . '_' . $strName] = $varValue;
						$arrTokens[$strPrefix . '_plain_' . $strName] =
							\HeimrichHannot\Haste\Util\StringUtil::convertToText(\StringUtil::decodeEntities($varValue), true);
						break;
					case 'value':
						// check for values causing notification center's json_encode call to fail (unprintable characters like binary!)
						if (ctype_print($varValue))
						{
							$arrTokens[$strPrefix . '_value_' . $strName] = $varValue;
						}
						break;
					case 'submission':
						$arrTokens[$strPrefix . '_submission_' . $strName] = rtrim($varValue, "\n");
						break;
				}
			}
		}

		// token: ##formsubmission_all##
		if(isset($arrSubmissionData['submission_all']))
		{
			$arrTokens[$strPrefix . 'submission_all'] = $arrSubmissionData['submission_all'];
		}

		// token: ##formsubmission##
		if(isset($arrSubmissionData['submission']))
		{
			$arrTokens[$strPrefix . 'submission'] = $arrSubmissionData['submission'];
		}

		// prepare attachments


		return $arrTokens;
	}

	public static function generateEntityTokens(\Model $objEntity, array $arrDca, $objDc, $arrFields=array())
	{
		return static::tokenizeData(static::prepareData($objEntity, 'tl_submission', $arrDca, $objDc, $arrFields));
	}

	/**
	 * Creates a new submission in a certain archive and assigns a logged in member (if existing)
	 * @param $intPid
	 * @param $intMember
	 *
	 * @return SubmissionModel
	 */
	public static function create($intPid, $intMember = null)
	{
		$objSubmission = new static();
		$objSubmission->pid = $intPid;
		$objSubmission->dateAdded = time();
		$objSubmission->memberAuthor = $intMember;

		$objSubmission->save();

		return $objSubmission;
	}
}
