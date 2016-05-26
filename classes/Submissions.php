<?php

namespace HeimrichHannot\Submissions;

use Contao\DC_Table;
use Haste\Util\Url;
use HeimrichHannot\EventRegistration\EventRegistration;
use HeimrichHannot\Haste\Dca\General;
use HeimrichHannot\Haste\Util\Files;
use HeimrichHannot\NotificationCenterPlus\NotificationCenterPlus;
use NotificationCenter\Model\Notification;

class Submissions extends \Controller
{
	const NOTIFICATION_TYPE_SUBMISSIONS		= 'submissions';
	const NOTIFICATION_TYPE_CONFIRMATION	= 'submission_confirmation';

	public static function getFieldsAsOptions(\DataContainer $objDc)
	{
		return static::getFields();
	}

	public static function getSkipFields()
	{
		return array(
			'dateAdded',
			'id',
			'pid',
			'published',
			'captcha',
			'formHybridBlob',
			'tstamp',
			'memberAuthor',
			'userAuthor',
			'checkedIn',
			'checkInDatime'
		);
	}

	public static function getFields($varInputType = array())
	{
		$arrOptions = array();

		foreach (\HeimrichHannot\Haste\Dca\General::getFields('tl_submission', false, $varInputType, array(), false) as $strField)
		{
			if (!in_array($strField, static::getSkipFields()))
				$arrOptions[] = $strField;
		}

		if (empty($arrOptions) && TL_MODE == 'BE' && \Input::get('do') == 'submission')
			\Message::addInfo($GLOBALS['TL_LANG']['MSC']['noSubmissionFields']);

		return $arrOptions;
	}

	public static function getCheckboxFieldsAsOptions()
	{
		return static::getFields('checkbox');
	}

	public static function getPasswordFieldsAsOptions()
	{
		return static::getFields('password');
	}

	public static function getNotificationsAsOptions()
	{
		return NotificationCenterPlus::getNotificationsAsOptions(Submissions::NOTIFICATION_TYPE_SUBMISSIONS);
	}

	public static function getConfirmationNotificationsAsOptions()
	{
		return NotificationCenterPlus::getNotificationsAsOptions(Submissions::NOTIFICATION_TYPE_CONFIRMATION);
	}
}
