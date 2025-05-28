<?php

namespace HeimrichHannot\Submissions\FormType;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\DataContainer;
use Contao\FormModel;
use HeimrichHannot\FormTypeBundle\FormType\AbstractFormType;

class SubmissionType extends AbstractFormType
{
    const TYPE = 'huh_submission';

    public function getType(): string
    {
        return static::TYPE;
    }

    public function onload(DataContainer $dataContainer, FormModel $formModel): void
    {
        PaletteManipulator::create()
            ->removeField('storeValues')
            ->removeField('huhSub_storeSubmission')
            ->addLegend('huh_submissions_legend', 'title_legend', PaletteManipulator::POSITION_AFTER)
            ->addField('huhSub_submissionArchive', 'huh_submissions_legend', PaletteManipulator::POSITION_APPEND)
            ->addField('huhSub_optIn', 'huh_submissions_legend', PaletteManipulator::POSITION_APPEND)
            ->applyToPalette('default', 'tl_form');
    }
}