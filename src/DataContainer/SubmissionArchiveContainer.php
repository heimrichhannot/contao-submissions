<?php

namespace HeimrichHannot\Submissions\DataContainer;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use HeimrichHannot\Submissions\Manager\DcaManager;

readonly class SubmissionArchiveContainer
{
    public function __construct(
        private DcaManager $dcaManager,
    ) {}

    #[AsCallback(table: 'tl_submission_archive', target: 'config.onload')]
    public function onConfigOnload(?DataContainer $dc = null): void
    {
        if (version_compare(ContaoCoreBundle::getVersion(), '5.0', '>=')) {
            // make icons compatible with Contao 5.0+
            $GLOBALS['TL_DCA']['tl_submission_archive']['list']['operations']['editheader']['icon'] = 'edit.svg';
            $GLOBALS['TL_DCA']['tl_submission_archive']['list']['operations']['edit']['icon'] = 'children.svg';
        }
    }

    #[AsCallback(table: 'tl_submission_archive', target: 'fields.submissionFields.options')]
    public function getSubmissionFieldsOptions(DataContainer $dc): array
    {
        $fields = $this->dcaManager->getSubmissibleFields('tl_submission');

        return \array_combine($fields, $fields);
    }
}