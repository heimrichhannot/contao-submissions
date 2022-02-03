<?php

namespace HeimrichHannot\Submissions\Event;

use Contao\FormModel;
use HeimrichHannot\Submissions\SubmissionModel;
use Symfony\Contracts\EventDispatcher\Event;

class SubmissionsBeforeSendConfirmationNotificationEvent extends Event
{
    /** @var SubmissionModel  */
    private $submission;

    /** @var array  */
    private $submissionCache;

    /** @var FormModel  */
    private $formModel;

    public function __construct(SubmissionModel $submission, array $submissionCache, FormModel $formModel)
    {
        $this->submission = $submission;
        $this->submissionCache = $submissionCache;
        $this->formModel = $formModel;
    }

    /**
     * @return SubmissionModel
     */
    public function getSubmission(): SubmissionModel
    {
        return $this->submission;
    }

    /**
     * @return array
     */
    public function getSubmissionCache(): array
    {
        return $this->submissionCache;
    }

    /**
     * @return FormModel
     */
    public function getFormModel(): FormModel
    {
        return $this->formModel;
    }
}