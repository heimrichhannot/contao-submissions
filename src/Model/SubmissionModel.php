<?php

namespace HeimrichHannot\Submissions\Model;

use Contao\Model;
use Contao\System;
use HeimrichHannot\FormHybrid\DC_Hybrid;
use HeimrichHannot\Haste\Dca\DC_HastePlus;
use HeimrichHannot\Haste\Util\FormSubmission;
use HeimrichHannot\Haste\Util\Salutations;
use HeimrichHannot\Haste\Util\Url;
use HeimrichHannot\Submissions\Manager\TokenManager;
use NotificationCenter\Model\Notification;

/**
 * @property int $id
 * @property int $tstamp
 * @property int $dateAdded
 */
class SubmissionModel extends Model
{
    protected static $strTable = 'tl_submission';

    /**
     * @var Model[] Cache for parent entities
     */
    protected static array $archiveParentsCache = [];

    public function getArchive(): SubmissionArchiveModel|Model|null
    {
        return SubmissionArchiveModel::findByPk($this->pid);
    }

    public static function findByParent(
        string $table,
        int    $pid,
        bool   $publishedOnly = false,
        array  $options = []
    ) {
        $archives = SubmissionArchiveModel::findByParent($table, $pid);
        if ($archives === null) {
            return null;
        }

        $findBy = ['tl_submission.pid=?'];

        if ($publishedOnly) {
            $findBy[] = 'tl_submission.published=1';
        }

        return static::findBy($findBy, [$archives->id], $options);
    }

    public static function getArchiveParent(int $submission): Model|null
    {
        $archive = static::legacyGetArchive($submission);

        if (!$archive)
        {
            return null;
        }

        if (!$archive->parentTable || !$archive->pid)
        {
            return null;
        }

        if (isset(static::$archiveParentsCache[$archive->id]))
        {
            return static::$archiveParentsCache[$archive->id];
        }

        $modelClass = Model::getClassFromTable($archive->parentTable);

        if (!\class_exists($modelClass))
        {
            return null;
        }

        $archiveParent = $modelClass::findByPk($archive->pid);

        if ($archiveParent === null)
        {
            return null;
        }

        static::$archiveParentsCache[$archive->id] = $archiveParent;

        return $archiveParent;
    }

    /* =============================================
     * TODO: Move the following into a manager class
     * ============================================= */

    public static function legacyGetArchive($intSubmission): SubmissionArchiveModel|Model|null
    {
        $submission = SubmissionModel::findByPk($intSubmission);

        if (!$submission)
        {
            return null;
        }

        $submissionArchive = $submission->getRelated('pid');

        if (!$submissionArchive)
        {
            return null;
        }

        return $submissionArchive;
    }

    public static function sendSubmissionNotification($submissionId = null, $arrTokens = []): void
    {
        $submissionId = $submissionId ?: \Input::get('id');

        $submissionArchive = SubmissionModel::legacyGetArchive($submissionId);

        if (!$submissionArchive || !$submissionArchive->nc_submission)
        {
            return;
        }

        static::sendNotification($submissionId, $submissionArchive->nc_submission, $arrTokens);
    }

    public static function sendConfirmationNotificationBe(\DataContainer $objDc): void
    {
        $submission = static::findByPk($objDc->id);

        if (!$submission)
        {
            return;
        }

        static::sendConfirmationNotification($submission->id);

        \Message::addConfirmation($GLOBALS['TL_LANG']['MSC']['confirmationNotificationSent'] ?? 'Confirmation notification sent.');
        \Controller::redirect(Url::addQueryString('id=' . $submission->pid, Url::removeQueryString(['key'])));
    }

    public static function sendConfirmationNotification($intSubmission, $arrTokens = []): void
    {
        $submissionArchive = SubmissionModel::legacyGetArchive($intSubmission);

        if (!$submissionArchive || !$submissionArchive->nc_confirmation)
        {
            return;
        }

        static::sendNotification($intSubmission, $submissionArchive->nc_confirmation, $arrTokens);
    }

    public static function sendNotification(int|string $submissionId, int|string $notificationId, $tokens = []): void
    {
        $tokens += TokenManager::generateTokens($submissionId);

        $notification = Notification::findByPk($notificationId);

        $notification->submission = $submissionId;
        $notification->send($tokens, $GLOBALS['TL_LANGUAGE']);
    }

    /**
     * Creates a new submission in a certain archive and assigns a logged in member (if existing)
     *
     * @param $pid
     * @param $member
     *
     * @return SubmissionModel
     */
    public static function create(int|string $pid, int|string $member = null): SubmissionModel
    {
        $submission               = new SubmissionModel();
        $submission->pid          = $pid;
        $submission->dateAdded    = time();
        $submission->memberAuthor = $member;

        $submission->save();

        $onCreateCallbacks = $GLOBALS['TL_DCA'][static::$strTable]['config']['oncreate_callback'] ?? [];

        if (!empty($onCreateCallbacks))
        {
            $dc = new DC_HastePlus(static::$strTable);
            $dc->id = $submission->id;
            $dc->activeRecord = $submission;

            foreach ($onCreateCallbacks as $callback)
            {
                if (is_array($callback) && \sizeof($callback) == 2)
                {
                    [$class, $method] = $callback;
                    System::importStatic($class)->{$method}(SubmissionModel::getTable(), $submission->id, $submission->row(), $dc);
                }
                elseif (is_callable($callback))
                {
                    $callback(SubmissionModel::getTable(), $submission->id, $submission->row(), $dc);
                }
            }
        }

        return $submission;
    }
}
