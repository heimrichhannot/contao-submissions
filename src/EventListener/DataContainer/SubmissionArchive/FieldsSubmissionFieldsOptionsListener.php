<?php

namespace HeimrichHannot\Submissions\EventListener\DataContainer\SubmissionArchive;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use HeimrichHannot\Submissions\Manager\DcaManager;

#[AsCallback(table: 'tl_submission_archive', target: 'fields.submissionFields.options')]
readonly class FieldsSubmissionFieldsOptionsListener
{
    public function __construct(
        private DcaManager $dcaManager,
    ) {
    }

    public function __invoke(DataContainer $dc): array
    {
        $fields = $this->dcaManager->getSubmissibleFields('tl_submission');

        return \array_combine($fields, $fields);
    }
}