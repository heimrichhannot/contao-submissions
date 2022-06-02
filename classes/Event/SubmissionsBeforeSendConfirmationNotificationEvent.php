<?php

namespace HeimrichHannot\Submissions\Event;

use Contao\FormModel;
use HeimrichHannot\Submissions\SubmissionModel;
use Symfony\Contracts\EventDispatcher\Event;

class SubmissionsBeforeSendConfirmationNotificationEvent extends Event
{
    /** @var SubmissionModel */
    private $submission;

    /** @var array */
    private $submissionCache;

    /** @var FormModel */
    private $formModel;

    /** @var array */
    private $submissionData;

    public function __construct(SubmissionModel $submission, array $submissionCache, FormModel $formModel, array $submissionData)
    {
        $this->submission = $submission;
        $this->submissionCache = $submissionCache;
        $this->formModel = $formModel;
        $this->submissionData = $submissionData;
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

    /**
     * Additional value will be added to the submission data for notification
     *
     * @return array
     */
    public function getSubmissionData(): array
    {
        return $this->submissionData;
    }

    /**
     * Additional value will be added to the submission data for notification
     *
     * @param array $submissionData
     */
    public function setSubmissionData(array $submissionData): void
    {
        $this->submissionData = $submissionData;
    }

    /**
     * Add a single value. If key already exist, it will be overridden.
     *
     * Additional value will be added to the submission data for notification
     */
    public function addSubmissionData(string $key, string $value): void
    {
        $this->submissionData[$key] = $value;
    }


}