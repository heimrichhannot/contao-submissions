<?php

namespace HeimrichHannot\Submissions\Event;

use Contao\FormModel;
use HeimrichHannot\Submissions\Model\SubmissionModel;
use Symfony\Contracts\EventDispatcher\Event;

class SubmissionsBeforeSendConfirmationNotificationEvent extends Event
{
    public function __construct(
        private readonly FormModel $formModel,
        private readonly SubmissionModel $submission,
        private readonly array $submissionCache,
        private array $submissionData,
    ) {
    }

    public function getFormModel(): FormModel
    {
        return $this->formModel;
    }

    public function getSubmission(): SubmissionModel
    {
        return $this->submission;
    }

    public function getSubmissionCache(): array
    {
        return $this->submissionCache;
    }

    public function getSubmissionData(): array
    {
        return $this->submissionData;
    }

    public function setSubmissionData(array $submissionData): void
    {
        $this->submissionData = $submissionData;
    }

    /**
     * Add a single value to the submission data.
     * If key already exist, it will be overridden.
     */
    public function addSubmissionData(string $key, string $value): void
    {
        $this->submissionData[$key] = $value;
    }
}
