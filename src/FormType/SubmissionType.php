<?php

namespace HeimrichHannot\Submissions\FormType;

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

    public function onload(DataContainer $dataContainer, FormModel $formModel): void {}
}