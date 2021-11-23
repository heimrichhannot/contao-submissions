<?php

namespace HeimrichHannot\Submissions\EventListener;

use Contao\Form;
use HeimrichHannot\Submissions\SubmissionModel;

class ProcessFormDataListener
{
    public function __invoke(array $submittedData, array $formData, ?array $files, array $labels, Form $form): void
    {
        if ($form->getModel()->alias === 'kontakt') {

            $arrSet = array();

            // Add the timestamp
            if ($this->Database->fieldExists('tstamp', $this->targetTable))
            {
                $arrSet['tstamp'] = time();
            }


            $submission = new SubmissionModel();
            $submission->setRow($submittedData);
            $submission->tstamp = $submission->dateAdded = time();
            $submission->save();
        }
    }
}