<?php

namespace HeimrichHannot\Submissions\Manager;

use Contao\StringUtil;
use HeimrichHannot\Submissions\Model\SubmissionModel;

class TokenManager
{
    const SKIP_FIELDS = [
        'dateAdded',
        'id',
        'pid',
        'published',
        'formHybridBlob',
        'tstamp',
        'memberAuthor',
        'userAuthor',
        'checkedIn',
        'checkInDatime',
    ];

    public static function generateTokens(int|string $submissionId, $fields = []): array
    {
        $submission = SubmissionModel::findByPk($submissionId);

        if (!$submission) {
            return [];
        }

        $tokens = [];

        \Controller::loadDataContainer('tl_submission');
        \System::loadLanguageFile('tl_submission');

        $dca = &$GLOBALS['TL_DCA']['tl_submission'];

        $dc               = new DC_Hybrid('tl_submission');
        $dc->activeRecord = $submission;

        // fields
        $archive = $submission->getRelated('pid');
        if ($archive)
        {
            \Controller::loadDataContainer('tl_submission');

            $fields = empty($fields) ? StringUtil::deserialize($archive->submissionFields, true) : $fields;

            if (is_array($preGenHooks = $GLOBALS['TL_HOOKS']['preGenerateSubmissionTokens'] ?? null))
            {
                foreach ($preGenHooks as [$class, $method])
                {
                    \System::importStatic($class)->{$method}($submission, $archive, $fields);
                }
            }
        }

        $arrSubmissionData = FormSubmission::prepareData(
            $submission,
            'tl_submission',
            $GLOBALS['TL_DCA']['tl_submission'],
            $dc,
            $fields,
            TokenManager::SKIP_FIELDS
        );

        $tokens = FormSubmission::tokenizeData($arrSubmissionData);

        // salutation
        $tokens['salutation_submission'] = Salutations::createSalutation(
            $GLOBALS['TL_LANGUAGE'],
            [
                'gender'   => $tokens['form_value_gender'],
                'title'    => $tokens['form_value_academicTitle'] ?: $tokens['form_value_title'],
                'firstname' => $tokens['form_value_firstname'],
                'lastname' => $tokens['form_value_lastname'],
            ]
        );

        $tokens['tl_submission'] = $submission->id;

        return $tokens;
    }
}