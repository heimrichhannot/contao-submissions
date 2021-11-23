<?php

namespace HeimrichHannot\Submissions\EventListener;

use Contao\Database;
use Contao\Form;

class FormGeneratorListener
{
    public function onPrepareFormData(array &$submittedData, array $labels, array $fields, Form $form): void
    {
        if ($form->storeAsSubmission && $form->submissionArchive) {
            $form->storeValues = '1';
            $form->targetTable = 'tl_submission';
        }
    }

    public function onStoreFormData(array $data, Form $form): array
    {
        // Remove fields that not exist
        $data = array_intersect_key($data, array_flip(Database::getInstance()->getFieldNames('tl_submission')));
        $data['pid'] = $form->submissionArchive;
        $data['dateAdded'] = $data['tstamp'] = time();
        return $data;
    }

    public function onProcessFormData(array $submittedData, array $formData, ?array $files, array $labels, Form $form): void
    {
        return;
    }
}