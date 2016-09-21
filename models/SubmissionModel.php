<?php

namespace HeimrichHannot\Submissions;

use HeimrichHannot\FormHybrid\DC_Hybrid;
use HeimrichHannot\FormHybrid\Submission;
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

	public static function findSubmissionsByParent($strTable, $intPid, $blnPublishedOnly = false, array $arrOptions = array())
	{
		if (($objSubmissionArchives = SubmissionArchiveModel::findByParent($strTable, $intPid)) !== null)
		{
			if (!$blnPublishedOnly)
			{
				return static::findByPid($objSubmissionArchives->id, $arrOptions);
			}
			else
			{
				return static::findBy(
					array('tl_submission.published=1', 'tl_submission.pid=?'), array($objSubmissionArchives->id), $arrOptions);
			}
		}

		return null;
	}

	public static function getArchiveParent($intSubmission)
	{
		if (($objSubmissionArchive = static::getArchive($intSubmission)) !== null
			&& $objSubmissionArchive->parentTable
			&& $objSubmissionArchive->pid
		)
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
			$objDc               = new DC_Hybrid('tl_submission');
			$objDc->activeRecord = $objSubmission;

			// fields
			if (($objSubmissionArchive = $objSubmission->getRelated('pid')) !== null)
			{
				\Controller::loadDataContainer('tl_submission');

				$arrFields = empty($arrFields) ? deserialize($objSubmissionArchive->submissionFields, true) : $arrFields;

				if (isset($GLOBALS['TL_HOOKS']['preGenerateSubmissionTokens'])
					&& is_array($GLOBALS['TL_HOOKS']['preGenerateSubmissionTokens'])
				)
				{
					foreach ($GLOBALS['TL_HOOKS']['preGenerateSubmissionTokens'] as $arrCallback)
					{
						\System::importStatic($arrCallback[0])->$arrCallback[1]($objSubmission, $objSubmissionArchive, $arrFields);
					}
				}
			}

			$arrSubmissionData = static::prepareData($objSubmission, 'tl_submission', $GLOBALS['TL_DCA']['tl_submission'], $objDc, $arrFields, Submissions::getSkipFields());

			$arrTokens = static::tokenizeData($arrSubmissionData);

			// salutation
			$arrTokens['salutation_submission'] = NotificationCenterPlus::createSalutation(
				$GLOBALS['TL_LANGUAGE'],
				array(
					'gender'   => $arrTokens['form_value_gender'],
					'title'    => $arrTokens['form_value_title'],
					'lastname' => $arrTokens['form_value_lastname'],
				)
			);

			$arrTokens['tl_submission'] = $objSubmission->id;
		}

		return $arrTokens;
	}

	/**
	 * @deprecated Use HeimrichHannot\Haste\Util\FormSubmission::prepareData()
	 * @param \Model $objSubmission
	 * @param        $strTable
	 * @param array  $arrDca
	 * @param        $objDc
	 * @param array  $arrFields
	 *
	 * @return array
	 */
	public static function prepareData(\Model $objSubmission, $strTable, array $arrDca = array(), $objDc = null, array $arrFields = array(), array $arrSkipFields = array())
	{
		return FormSubmission::prepareData($objSubmission, $strTable, $arrDca, $objDc, $arrFields, $arrSkipFields);
	}

	/**
	 * @deprecated Use HeimrichHannot\Haste\Util\FormSubmission::prepareDataField()
	 * @param $strName
	 * @param $varValue
	 * @param $arrData
	 * @param $strTable
	 * @param $objDc
	 *
	 * @return array
	 */
	public static function prepareDataField($strName, $varValue, $arrData, $strTable, $objDc)
	{
		return FormSubmission::prepareDataField($strName, $varValue, $arrData, $strTable, $objDc);
	}

	/**
	 * @deprecated Use HeimrichHannot\Haste\Util\FormSubmission::tokenizeData()
	 *
	 * @param array  $arrSubmissionData
	 * @param string $strPrefix
	 *
	 * @return array
	 */
	public static function tokenizeData(array $arrSubmissionData = array(), $strPrefix = 'form')
	{
		return FormSubmission::tokenizeData($arrSubmissionData, $strPrefix);
	}

	public static function generateEntityTokens(\Model $objEntity, array $arrDca, $objDc, $arrFields = array())
	{
		return static::tokenizeData(static::prepareData($objEntity, 'tl_submission', $arrDca, $objDc, $arrFields));
	}

	/**
	 * Creates a new submission in a certain archive and assigns a logged in member (if existing)
	 *
	 * @param $intPid
	 * @param $intMember
	 *
	 * @return SubmissionModel
	 */
	public static function create($intPid, $intMember = null)
	{
		$objSubmission               = new static();
		$objSubmission->pid          = $intPid;
		$objSubmission->dateAdded    = time();
		$objSubmission->memberAuthor = $intMember;

		$objSubmission->save();

		return $objSubmission;
	}
}
