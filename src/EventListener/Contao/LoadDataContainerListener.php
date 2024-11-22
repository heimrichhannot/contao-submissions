<?php

namespace HeimrichHannot\Submissions\EventListener\Contao;

use Contao\Controller;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\Input;
use Contao\System;
use HeimrichHannot\Submissions\Model\SubmissionArchiveModel;
use HeimrichHannot\UtilsBundle\Util\Utils;

#[AsHook("loadDataContainer")]
readonly class LoadDataContainerListener
{
    public function __construct(
        private Utils $utils
    ) {}

    public function __invoke(string $table): void
    {
        switch ($table) {
            case 'tl_submission':
                $this->loadTlSubmission();
                break;
            case 'tl_submission_archive':
                $this->loadTlSubmissionArchive();
                break;
        }
    }

    protected function loadTlSubmission(): void
    {
        $dca = &$GLOBALS['TL_DCA']['tl_submission'];
        $fields = [];

        foreach ($dca['fields'] as $field => $data)
        {
            $isSubmissionField = !\filter_var(
                $data['eval']['noSubmissionField'] ?? false,
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            );

            if ($isSubmissionField) {
                $fields[] = $field;
            }
        }

        PaletteManipulator::create()
            ->addField($fields, 'submission_legend', PaletteManipulator::POSITION_APPEND)
            ->applyToPalette('default', 'tl_submission');
    }

    protected function loadTlSubmissionArchive(): void
    {
        $id = Input::get('id');

        if (!$id
            || Input::get('act') !== 'delete'
            || Input::get('do') !== 'submission'
            || Input::get('table'))
        {
            return;
        }

        Controller::loadDataContainer('tl_submission_archive');
        System::loadLanguageFile('tl_submission_archive');

        $dca = &$GLOBALS['TL_DCA']['tl_submission_archive'];

        if (!$this->utils->container()->isBackend()) {
            return;
        }

        if (!$submissionArchive = SubmissionArchiveModel::findByPk($id)) {
            return;
        }

        if (!$parentTable = $submissionArchive->parentTable) {
            return;
        }

        $dca['config']['ptable'] = $parentTable;
    }
}