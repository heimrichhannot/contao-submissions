<?php

namespace HeimrichHannot\Submissions\EventListener\Contao;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;

#[AsHook("loadDataContainer")]
class LoadDataContainerListener
{
    public function __invoke(string $table): void
    {
        if ($table !== 'tl_submission') {
            return;
        }

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
}