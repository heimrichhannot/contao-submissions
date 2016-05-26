<?php

namespace HeimrichHannot\Submissions;

class SubmissionArchiveModel extends \Model
{

	protected static $strTable = 'tl_submission_archive';

	public static function findByParent($strTable, $intPid)
	{
		return static::findBy(array('parentTable=?', 'pid=?'), array($strTable, $intPid));
	}

	public static function findSubmissionsByParent($strTable, $intPid, $blnPublishedOnly = false)
	{
		if (($objSubmissionArchives = static::findByParent($strTable, $intPid)) !== null)
		{
			if (!$blnPublishedOnly)
			{
				return SubmissionModel::findByPid($objSubmissionArchives->id);
			}
			else
			{
				return SubmissionModel::findBy(
					array('tl_submission.published=1', 'tl_submission.pid=?'), array($objSubmissionArchives->id));
			}
		}
	}

	public static function getSubmissionFieldsByParent($strTable, $intPid)
	{
		if (($objSubmissionArchive = static::findByParent($strTable, $intPid)) !== null)
		{
			return deserialize($objSubmissionArchive->submissionFields, true);
		}
	}

}