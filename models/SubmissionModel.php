<?php

namespace HeimrichHannot\Submissions;

use Contao\Model;
use Contao\System;
use HeimrichHannot\FormHybrid\DC_Hybrid;
use HeimrichHannot\Haste\Dca\DC_HastePlus;
use HeimrichHannot\Haste\Dca\General;
use HeimrichHannot\Haste\Util\FormSubmission;
use HeimrichHannot\Haste\Util\Salutations;
use HeimrichHannot\Haste\Util\Url;
use NotificationCenter\Model\Notification;

/**
 * @property int $id
 * @property int $tstamp
 * @property int $dateAdded
 */
class SubmissionModel extends Model
{

    protected static $strTable = 'tl_submission';

    protected static $arrArchiveParentsCache = [];

    public static function findSubmissionsByParent($strTable, $intPid, $blnPublishedOnly = false, array $arrOptions = [])
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
                    ['tl_submission.published=1', 'tl_submission.pid=?'],
                    [$objSubmissionArchives->id],
                    $arrOptions
                );
            }
        }

        return null;
    }

    public static function getArchiveParent($intSubmission)
    {
        if (($objSubmissionArchive = static::getArchive($intSubmission)) === null)
        {
            return null;
        }

        if ($objSubmissionArchive->parentTable && $objSubmissionArchive->pid)
        {
            if (isset(static::$arrArchiveParentsCache[$objSubmissionArchive->id]))
            {
                return static::$arrArchiveParentsCache[$objSubmissionArchive->id];
            }

            if (($objArchiveParent = General::getModelInstance($objSubmissionArchive->parentTable, $objSubmissionArchive->pid)) !== null)
            {
                static::$arrArchiveParentsCache[$objSubmissionArchive->id] = $objArchiveParent;

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

    public static function sendSubmissionNotification($intSubmission, $arrTokens = [])
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
            \Controller::redirect(Url::addQueryString('id=' . $objSubmission->pid, Url::removeQueryString(['key'])));
        }
    }

    public static function sendConfirmationNotification($intSubmission, $arrTokens = [])
    {
        if (($objSubmissionArchive = SubmissionModel::getArchive($intSubmission)) !== null)
        {
            if ($objSubmissionArchive->nc_confirmation)
            {
                static::sendNotification($intSubmission, $objSubmissionArchive->nc_confirmation, $arrTokens);
            }
        }
    }

    public static function sendNotification($intSubmission, $intNotification, $arrTokens = [])
    {
        $arrTokens += static::generateTokens($intSubmission);

        if (($objNotification = Notification::findByPk($intNotification)) !== null)
        {
            $objNotification->submission = $intSubmission;
            $objNotification->send($arrTokens, $GLOBALS['TL_LANGUAGE']);
        }
    }

    public static function generateTokens($intSubmission, $arrFields = [])
    {
        $arrTokens = [];
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
                        \System::importStatic($arrCallback[0])->{$arrCallback[1]}($objSubmission, $objSubmissionArchive, $arrFields);
                    }
                }
            }

            $arrSubmissionData = static::prepareData(
                $objSubmission,
                'tl_submission',
                $GLOBALS['TL_DCA']['tl_submission'],
                $objDc,
                $arrFields,
                Submissions::getSkipFields()
            );

            $arrTokens = static::tokenizeData($arrSubmissionData);

            // salutation
            $arrTokens['salutation_submission'] = Salutations::createSalutation(
                $GLOBALS['TL_LANGUAGE'],
                [
                    'gender'   => $arrTokens['form_value_gender'],
                    'title'    => $arrTokens['form_value_academicTitle'] ?: $arrTokens['form_value_title'],
                    'firstname' => $arrTokens['form_value_firstname'],
                    'lastname' => $arrTokens['form_value_lastname'],
                ]
            );

            $arrTokens['tl_submission'] = $objSubmission->id;
        }

        return $arrTokens;
    }

    /**
     * @deprecated Use HeimrichHannot\Haste\Util\FormSubmission::prepareData()
     *
     * @param \Model $objSubmission
     * @param        $strTable
     * @param array  $arrDca
     * @param        $objDc
     * @param array  $arrFields
     *
     * @return array
     */
    public static function prepareData(
        \Model $objSubmission,
        $strTable,
        array $arrDca = [],
        $objDc = null,
        array $arrFields = [],
        array $arrSkipFields = []
    ) {
        return FormSubmission::prepareData($objSubmission, $strTable, $arrDca, $objDc, $arrFields, $arrSkipFields);
    }

    /**
     * @deprecated Use HeimrichHannot\Haste\Util\FormSubmission::prepareDataField()
     *
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
    public static function tokenizeData(array $arrSubmissionData = [], $strPrefix = 'form')
    {
        return FormSubmission::tokenizeData($arrSubmissionData, $strPrefix);
    }

    public static function generateEntityTokens(\Model $objEntity, array $arrDca, $objDc, $arrFields = [])
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

        if(is_array($GLOBALS['TL_DCA'][static::$strTable]['config']['oncreate_callback'])) {
            $dc = new DC_HastePlus(static::$strTable);
            $dc->id = $objSubmission->id;
            $dc->activeRecord = $objSubmission;

            foreach ($GLOBALS['TL_DCA'][static::$strTable]['config']['oncreate_callback'] as $callback) {
                if (is_array($callback)) {
                    System::importStatic($callback[0]);
                    $callbackObj = System::importStatic($callback[0]);
                    $callbackObj->{$callback[1]}(static::$strTable, $objSubmission->id, $objSubmission->row(), $dc);
                } elseif (is_callable($callback)) {
                    $callback(static::$strTable, $objSubmission->id, $objSubmission->row(), $dc);
                }
            }
        }

        return $objSubmission;
    }
}
