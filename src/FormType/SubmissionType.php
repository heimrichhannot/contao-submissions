<?php

namespace HeimrichHannot\Submissions\FormType;

use Contao\Controller;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\DataContainer;
use Contao\FormModel;
use Contao\StringUtil;
use HeimrichHannot\FormTypeBundle\FormType\AbstractFormType;
use HeimrichHannot\Submissions\Model\SubmissionArchiveModel;

class SubmissionType extends AbstractFormType
{
    public const TYPE = 'huh_submission';

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

    public function getDefaultFields(FormModel $formModel): array
    {
        if (!$formModel->huhSub_submissionArchive) {
            return [];
        }

        $archive = SubmissionArchiveModel::findByPk($formModel->huhSub_submissionArchive);
        if (!$archive) {
            return [];
        }

        $fields = StringUtil::deserialize($archive->submissionFields, true);

        Controller::loadDataContainer('tl_submission');
        Controller::loadLanguageFile('tl_submission');

        $return = [];
        foreach ($fields as $field) {
            $fieldConfig = [
                'name' => $field,
                'label' => $GLOBALS['TL_DCA']['tl_submission']['fields'][$field]['label'][0] ?? '',
                'type' => 'text',
            ];
            $this->fieldType($field, $fieldConfig);
            $return[] = $fieldConfig;
        }
        $return[] = [
            'type' => 'captcha',
        ];
        $return[] = [
            'type' => 'submit',
            'slabel' => 'Anmelden',
        ];

        return $return;
    }

    private function fieldType(string $fieldName, array &$fieldConfig): void
    {
        $field = $GLOBALS['TL_DCA']['tl_submission']['fields'][$fieldName] ?? [];
        if (empty($field)) {
            return;
        }

        switch ($field['inputType']) {
            case 'select':
                $fieldConfig['type'] = 'select';
                $options = [];
                if (isset($field['options']) && is_array($field['options'])) {
                    foreach ($field['options'] as $key => $option) {
                        $value = is_string($key)
                            ? $key
                            : (($field['eval']['isAssociative'] ?? false) ? $key : $option);

                        $label = $field['reference'][$value] ?? $option;
                        $options[] = [
                            'value' => $value,
                            'label' => $label,
                        ];
                    }
                }
                $fieldConfig['options'] = serialize($options);
                break;
        }
    }
}
