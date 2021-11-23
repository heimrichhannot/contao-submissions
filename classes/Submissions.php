<?php

namespace HeimrichHannot\Submissions;

use Contao\Database;
use Contao\DataContainer;
use HeimrichHannot\Haste\Dca\General;

class Submissions extends \Controller
{
    const NOTIFICATION_TYPE_SUBMISSIONS = 'submissions';

    const NOTIFICATION_TYPE_FORM_SUBMISSION = 'submission_form';
    const NOTIFICATION_TYPE_CONFIRMATION    = 'submission_confirmation';

    const PALETTE_DEFAULT = '{general_legend},authorType,author;{submission_legend},submissionFields;{publish_legend},published;';

    public static function getDefaultAttachmentSubFolderPattern()
    {
        return '[dateAdded::date::Y]/[dateAdded::date::m]/[dateAdded::date::d]/[id]';
    }

    public static function getDefaultAttachmentSRC($blnReturnPath = false)
    {
        $objFolder = new \Folder('files/submissions/uploads');

        if ($blnReturnPath) {
            return $objFolder->path;
        }

        if (\Validator::isUuid($objFolder->getModel()->uuid)) {
            return class_exists('Contao\StringUtil')
                ? \StringUtil::binToUuid($objFolder->getModel()->uuid)
                : \StringUtil::binToUuid(
                    $objFolder->getModel()->uuid
                );
        }

        return null;
    }

    public static function getFieldsAsOptions(\DataContainer $objDc)
    {
        return static::getFields();
    }

    public static function getSkipFields()
    {
        return [
            'dateAdded',
            'id',
            'pid',
            'published',
            'formHybridBlob',
            'tstamp',
            'memberAuthor',
            'userAuthor',
            'checkedIn',
            'checkInDatime',
        ];
    }

    public static function getFields($varInputType = [])
    {
        $arrOptions = [];

        foreach (General::getFields('tl_submission', false, $varInputType, [], false) as $strField) {
            if (!in_array($strField, static::getSkipFields())) {
                $arrOptions[] = $strField;
            }
        }

        if (empty($arrOptions) && TL_MODE == 'BE' && \Input::get('do') == 'submission') {
            \Message::addInfo($GLOBALS['TL_LANG']['MSC']['noSubmissionFields']);
        }

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
        return static::getNotificationOptionsByType(Submissions::NOTIFICATION_TYPE_FORM_SUBMISSION);
    }

    public static function getConfirmationNotificationsAsOptions()
    {
        return static::getNotificationOptionsByType(Submissions::NOTIFICATION_TYPE_CONFIRMATION);
    }

    public function setCurrentLanguage(string $table, int $id, array $fields, DataContainer $dc)
    {
        Database::getInstance()->prepare("UPDATE tl_submission SET submissionLanguage=? WHERE id=?")->execute($GLOBALS['TL_LANGUAGE'], $id);
    }

    private static function getNotificationOptionsByType($strType)
    {
        $arrChoices       = [];
        $objNotifications = \Database::getInstance()->execute("SELECT id,title FROM tl_nc_notification WHERE type='$strType' ORDER BY title");

        while ($objNotifications->next()) {
            $arrChoices[$objNotifications->id] = $objNotifications->title;
        }

        return $arrChoices;
    }
}
