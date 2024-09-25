<?php

namespace HeimrichHannot\Submissions\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use HeimrichHannot\Submissions\Util\SubmissionsDcaExtender;

class SubmissionsContainer
{
    public static function getDefaultAttachmentSubFolderPattern(): string
    {
        return '[dateAdded::date::Y]/[dateAdded::date::m]/[dateAdded::date::d]/[id]';
    }

    #[AsHook("loadDataContainer")]
    public function onLoadDataContainer(string $table): void
    {
        switch ($table) {
            case 'tl_submission_archive':
                SubmissionsDcaExtender::addOptionalSubmissionArchiveFields();
                break;
            case 'tl_form':
                SubmissionsDcaExtender::addOptInSupport($table);
                break;
            case 'tl_submission':
                SubmissionsDcaExtender::addOptInTokenIdField($table);
        }
    }
}