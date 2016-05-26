<?php

namespace HeimrichHannot\Submissions;

class SubmissionsCleaner extends \Controller
{
	public static function runMinutelyCleaner()
	{
		static::runCleaner('minutely');
	}

	public static function runHourlyCleaner()
	{
		static::runCleaner('hourly');
	}

	public static function runWeeklyCleaner()
	{
		static::runCleaner('weekly');
	}

	public static function runDailyCleaner()
	{
		static::runCleaner('daily');
	}

	public static function runCleaner($strPeriod)
	{
		if (($objSubmissionArchives = SubmissionArchiveModel::findBy(
				array('addCleaner = ?', 'cleanerPeriod = ?'),
				array('1', $strPeriod))) !== null)
		{
			while ($objSubmissionArchives->next())
			{
				$arrMaxAge = deserialize($objSubmissionArchives->cleanerMaxAge, true);

				$intFactor = 1;
				switch ($arrMaxAge['unit'])
				{
					case 'm':
						$intFactor = 60;
						break;
					case 'h':
						$intFactor = 60*60;
						break;
					case 'd':
						$intFactor = 24*60*60;
						break;
				}

				$intMaxInterval = $arrMaxAge['value'] * $intFactor;

				if (($objInactiveSubmissions = SubmissionModel::findBy(
						array('published != 1', 'UNIX_TIMESTAMP() > tl_submission.dateAdded + ?'),
						array($intMaxInterval))) !== null)
				{
					while ($objInactiveSubmissions->next())
					{
						$intId = $objInactiveSubmissions->id;

						if ($objInactiveSubmissions->delete())
						{
							\System::log(
								sprintf('Deleted inactive submission ID %s. Allowed submission archive\'s max age (%s%s) elapsed.',
									$intId, $arrMaxAge['value'], $arrMaxAge['unit']),
								__METHOD__, TL_CRON);
						}
						else
						{
							\System::log(
								sprintf('Error removing inactive submission ID %s. Trying again next time the cron runs.',
									$intId, $arrMaxAge['value'], $arrMaxAge['unit']),
								__METHOD__, TL_ERROR);
						}
					}
				}

				die();
			}
		}
	}
}
