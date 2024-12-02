<?php

namespace HeimrichHannot\Submissions\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use HeimrichHannot\Submissions\Manager\DcaManager;

readonly class SubmissionArchiveContainer
{
    public function __construct(
        private DcaManager $dcaManager,
    ) {}

    #[AsCallback(table: 'tl_submission_archive', target: 'fields.submissionFields.options')]
    public function getSubmissionFieldsOptions(DataContainer $dc): array
    {
        $fields = $this->dcaManager->getSubmissibleFields('tl_submission');

        return \array_combine($fields, $fields);
    }
}