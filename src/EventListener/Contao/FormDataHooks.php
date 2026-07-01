<?php

namespace HeimrichHannot\Submissions\EventListener\Contao;

use Contao\Controller;
use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\CoreBundle\OptIn\OptIn;
use Contao\Database;
use Contao\Form;
use Contao\FormModel;
use Contao\StringUtil;
use Contao\System;
use Contao\Validator;
use HeimrichHannot\FormTypeBundle\FormType\FormTypeCollection;
use HeimrichHannot\Submissions\Config\OptInConfig;
use HeimrichHannot\Submissions\FormType\SubmissionType;
use HeimrichHannot\Submissions\Manager\NotificationManager;
use HeimrichHannot\Submissions\Manager\SimpleTokensManager;
use HeimrichHannot\Submissions\Model\SubmissionModel;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

readonly class FormDataHooks
{
    public function __construct(
        private NotificationManager $notificationManager,
        private OptIn $optIn,
        private RouterInterface $router,
        private SimpleTokensManager $simpleTokensManager,
        private Utils $utils,
        private FormTypeCollection $formTypeCollection,
    ) {
    }

    #[AsHook('prepareFormData')]
    public function onPrepareFormData(array &$submittedData, array $labels, array $fields, Form $form): void
    {
        if (!$this->preCheck($form)) {
            return;
        }

        $form->storeValues = '1';
        $form->targetTable = 'tl_submission';

        $submittedData['uuid'] ??= Database::getInstance()->getUuid();

        // Prepare OPT-IN
        if (!$form->huhSub_optIn || !$form->huhSub_optInNotification) {
            return;
        }

        $form->nc_notification = '0';

        $token = $this->optIn->create(
            OptInConfig::TOKEN_PREFIX,
            $submittedData['email'] ?? $GLOBALS['TL_ADMIN_EMAIL'] ?? 'contao@example.org',
            []
        );
        $form->optInIdentifier = $token->getIdentifier();
        $submittedData['huhSub_optInTokenId'] = $token->getIdentifier();
    }

    #[AsHook('processFormData', priority: 200)]
    public function onProcessFormData(
        array &$submittedData,
        array &$formData,
        ?array $files,
        array $labels,
        Form $form,
    ): void {
        if (!$this->preCheck($form)) {
            return;
        }

        if (!empty($submittedData['uuid']) && Validator::isBinaryUuid($submittedData['uuid'])) {
            $submittedData['uuid'] = StringUtil::binToUuid($submittedData['uuid']);
        }

        $attachmentTokens = $this->simpleTokensManager->generateAttachmentTokens($files);
        $submittedData = \array_merge($submittedData, $attachmentTokens);

        if (!$form->huhSub_optIn || !$form->huhSub_optInNotification) {
            return;
        }

        $optInTokenId = $submittedData['huhSub_optInTokenId'] ?? null;
        if (!$optInTokenId) {
            return;
        }

        $formData['nc_notification'] = null;

        $optInToken = $this->optIn->find($optInTokenId);
        if (!$optInToken) {
            return;
        }

        $submission = SubmissionModel::findOneByOptInToken($optInToken);

        if (!$submission) {
            $this->utils->container()->log('Could not fetch submission for given token.', __METHOD__, 'TL_ERROR');

            return;
        }

        $submission->huhSub_optInCache = \serialize([
            'labels' => $labels,
            'files' => $files,
            'form' => $form->id,
        ]);

        $submission->save();

        $optInUrl = $this->router->generate('huh_submissions_opt_in', [
            'formId' => $form->id,
            'tokenIdentifier' => $optInToken->getIdentifier(),
            'from' => ($GLOBALS['objPage'] ?? System::getContainer()->get('contao.routing.page_finder')?->getCurrentPage())?->id,
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->notificationManager->send(
            $submittedData,
            $formData,
            $files,
            $labels,
            $form,
            $optInToken->getIdentifier(),
            $optInUrl
        );
    }

    #[AsHook('storeFormData')]
    public function onStoreFormData(array $data, Form $form): array
    {
        if (!$this->preCheck($form)) {
            return $data;
        }

        $data['pid'] = $form->huhSub_submissionArchive;
        $data['dateAdded'] = $data['tstamp'] = time();
        $data['huhSub_optInTokenId'] ??= '';

        // Remove fields that do not exist
        $data = \array_intersect_key($data, \array_flip(Database::getInstance()->getFieldNames('tl_submission')));

        if (empty($_SESSION['FILES'])) {
            return $data;
        }

        Controller::loadDataContainer('tl_submission');

        foreach ($_SESSION['FILES'] as $field => $fieldData) {
            if (empty($data[$field]) || empty($GLOBALS['TL_DCA']['tl_submission']['fields'][$field])) {
                continue;
            }

            $data[$field] = StringUtil::uuidToBin($fieldData['uuid']);

            $multiple = (bool) ($GLOBALS['TL_DCA']['tl_submission']['fields'][$field]['eval']['multiple'] ?? false);
            $fieldType = $GLOBALS['TL_DCA']['tl_submission']['fields'][$field]['eval']['fieldType'] ?? null;

            if ($multiple || 'checkbox' === $fieldType) {
                $data[$field] = \serialize([$fieldData['uuid']]);
            }
        }

        return $data;
    }

    private function preCheck(Form|FormModel $form): bool
    {
        $type = $this->formTypeCollection->getType($form->formType);

        if (!$type) {
            return false;
        }

        if ($type instanceof SubmissionType) {
            return true;
        }

        if ($form->huhSub_storeSubmission && $form->huhSub_submissionArchive) {
            return true;
        }

        return false;
    }
}
