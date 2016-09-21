<?php

namespace HeimrichHannot\Submissions;

class SubmissionArchiveModel extends \Model
{

	protected static $strTable = 'tl_submission_archive';

	public static function findByParent($strTable, $intPid)
	{
		return static::findBy(array('parentTable=?', 'pid=?'), array($strTable, $intPid));
	}

	/**
	 * @deprecated use SubmissionModel::findSubmissionsByParent()
	 * @param      $strTable
	 * @param      $intPid
	 * @param bool $blnPublishedOnly
	 *
	 * @return mixed
	 */
	public static function findSubmissionsByParent($strTable, $intPid, $blnPublishedOnly = false)
	{
		return SubmissionModel::findSubmissionsByParent($strTable, $intPid, $blnPublishedOnly);
	}

	public static function getSubmissionFieldsByParent($strTable, $intPid)
	{
		if (($objSubmissionArchive = static::findByParent($strTable, $intPid)) !== null)
		{
			return deserialize($objSubmissionArchive->submissionFields, true);
		}
	}

}