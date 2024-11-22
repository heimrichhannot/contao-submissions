<?php

namespace HeimrichHannot\Submissions\EventListener\Contao;

use Contao\Controller;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\OptIn\OptIn;
use Contao\Database;
use Contao\Form;
use Contao\StringUtil;
use Contao\Validator;
use HeimrichHannot\Submissions\Config\OptInConfig;
use HeimrichHannot\Submissions\Model\SubmissionModel;
use HeimrichHannot\Submissions\Util\Tokens;
use HeimrichHannot\UtilsBundle\Util\Utils;

class FormDataHooks
{
    public function __construct(
        private readonly OptIn $optIn,
        private readonly Utils $utils
    ) {}

    #[AsHook("prepareFormData")]
    public function onPrepareFormData(array &$submittedData, array $labels, array $fields, Form $form): void
    {
        if (!$form->storeAsSubmission || !$form->submissionArchive) {
            return;
        }

        $form->storeValues = '1';
        $form->targetTable = 'tl_submission';

        $submittedData['uuid'] ??= Database::getInstance()->getUuid();

        if (!$form->huhSubAddOptIn || !$form->huhSubOptInNotification) {
            return;
        }

        $form->nc_notification = $form->huhSubOptInNotification;

        $token = $this->optIn->create(
            OptInConfig::TOKEN_PREFIX,
            $submittedData['email'] ?? $GLOBALS['TL_ADMIN_EMAIL'] ?? 'contao@example.org',
            []
        );
        $form->optInIdentifier = $token->getIdentifier();
        $submittedData['optInTokenId'] = $token->getIdentifier();
    }

    #[AsHook("processFormData")]
    public function onProcessFormData(
        array  &$submittedData,
        array  $formData,
        ?array $files,
        array  $labels,
        Form   $form
    ): void {
        if (!empty($submittedData['uuid']) && Validator::isBinaryUuid($submittedData['uuid'])) {
            $submittedData['uuid'] = StringUtil::binToUuid($submittedData['uuid']);
        }

        $submittedData = Tokens::addAttachmentTokens($submittedData, $files);

        if (!$form->storeAsSubmission || !$form->submissionArchive) {
            return;
        }

        $optInTokenId = $submittedData['optInTokenId'] ?? null;

        if (!$form->huhSubAddOptIn || !$form->huhSubOptInNotification || empty($optInTokenId)) {
            return;
        }

        $submission = SubmissionModel::findBy(["huhSubOptInTokenId=?"], [$submittedData['optInTokenId']]);

        if (!$submission || $submission->count() > 1) {
            $this->utils->container()->log('Could not fetch submission for given token.', __METHOD__, TL_ERROR);
            return;
        }

        $submission->huhSubOptInCache = \serialize([
            'labels' => $labels,
            'files' => $files,
            'form' => $form->id
        ]);

        $submission->save();
    }

    #[AsHook("storeFormData")]
    public function onStoreFormData(array $data, Form $form): array
    {
        if (!$form->storeAsSubmission || !$form->submissionArchive) {
            return $data;
        }

        $data['pid'] = $form->submissionArchive;
        $data['dateAdded'] = $data['tstamp'] = time();
        $data['huhSubOptInTokenId'] = $data['optInTokenId'] ?? '';

        // Remove fields that not exist
        $data = \array_intersect_key($data, \array_flip(Database::getInstance()->getFieldNames('tl_submission')));

        if (!empty($_SESSION['FILES']))
        {
            Controller::loadDataContainer('tl_submission');

            foreach ($_SESSION['FILES'] as $field=>$fieldData)
            {
                if (empty($data[$field]) || empty($GLOBALS['TL_DCA']['tl_submission']['fields'][$field])) {
                    continue;
                }

                $data[$field] = StringUtil::uuidToBin($fieldData['uuid']);

                $multiple = (bool)($GLOBALS['TL_DCA']['tl_submission']['fields'][$field]['eval']['multiple'] ?? false);
                $fieldType = $GLOBALS['TL_DCA']['tl_submission']['fields'][$field]['eval']['fieldType'] ?? null;

                if ($multiple || $fieldType === 'checkbox') {
                    $data[$field] = \serialize([$fieldData['uuid']]);
                }
            }
        }

        return $data;
    }
}