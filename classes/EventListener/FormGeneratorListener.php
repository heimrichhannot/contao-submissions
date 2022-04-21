<?php

namespace HeimrichHannot\Submissions\EventListener;

use Contao\Controller;
use Contao\Database;
use Contao\Environment;
use Contao\Form;
use Contao\StringUtil;
use Contao\System;
use Contao\Validator;
use HeimrichHannot\Submissions\SubmissionModel;
use NotificationCenter\Model\Gateway;
use NotificationCenter\Model\Message;

class FormGeneratorListener
{
    /**
     * @internal
     */
    const OPTIN_TOKEN_PREFIX = 'huhSu-';

    public function onPrepareFormData(array &$submittedData, array $labels, array $fields, Form $form): void
    {
        if ($form->storeAsSubmission && $form->submissionArchive) {
            $form->storeValues = '1';
            $form->targetTable = 'tl_submission';

            if (!isset($data['uuid'])) {
                $submittedData['uuid'] = Database::getInstance()->getUuid();
            }

            if (version_compare(VERSION, '4.7', '>=')) {
                if ($form->huhSubAddOptIn && $form->huhSubOptInNotification) {
                    $form->nc_notification = $form->huhSubOptInNotification;

                    $token = System::getContainer()->get('contao.opt-in')
                        ->create(
                            static::OPTIN_TOKEN_PREFIX,
                            $submittedData['email'] ?? $GLOBALS['TL_ADMIN_EMAIL'] ?? 'contao@example.org',
                            []
                        );
                    $form->optInIdentifier = $token->getIdentifier();
                    $submittedData['optInTokenId'] = $token->getIdentifier();
                }
            }
        }
    }

    public function onStoreFormData(array $data, Form $form): array
    {
        if ($form->storeAsSubmission && $form->submissionArchive) {
            $data['pid'] = $form->submissionArchive;
            $data['dateAdded'] = $data['tstamp'] = time();
            $data['huhSubOptInTokenId'] = $data['optInTokenId'] ?? '';

            // Remove fields that not exist
            $data = array_intersect_key($data, array_flip(Database::getInstance()->getFieldNames('tl_submission')));

            if (!empty($_SESSION['FILES'])) {
                Controller::loadDataContainer('tl_submission');
                foreach ($_SESSION['FILES'] as $field=>$fieldData) {
                    if (isset($data[$field]) && isset($GLOBALS['TL_DCA']['tl_submission']['fields'][$field])) {
                        $data[$field] = StringUtil::uuidToBin($fieldData['uuid']);

                        if (($GLOBALS['TL_DCA']['tl_submission']['fields'][$field]['eval']['fieldType'] ?? false ) === 'checkbox'
                            || ($GLOBALS['TL_DCA']['tl_submission']['fields'][$field]['eval']['multiple'] ?? false) === true
                        ) {
                            $data[$field] = serialize([$fieldData['uuid']]);
                        }
                    }
                }
            }
        }

        return $data;
    }

    public function onProcessFormData(array &$submittedData, array $formData, ?array $files, array $labels, Form $form): void
    {
        if (isset($submittedData['uuid']) && Validator::isBinaryUuid($submittedData['uuid'])) {
            $submittedData['uuid'] = StringUtil::binToUuid($submittedData['uuid']);
        }

        if (version_compare(VERSION, '4.7', '>=')) {
            if ($form->storeAsSubmission && $form->submissionArchive
                && $form->huhSubAddOptIn && $form->huhSubOptInNotification && isset($submittedData['optInTokenId']))
            {
                $submission = SubmissionModel::findBy(["huhSubOptInTokenId=?"], [$submittedData['optInTokenId']]);
                if (!$submission || $submission->count() > 1) {
                    System::log("Could not fetch submission for given token.", __METHOD__, TL_ERROR);
                    return;
                }
                $submission->huhSubOptInCache = serialize([
                    'labels' => $labels,
                    'files' => $files,
                    'form' => $form->id
                ]);
                $submission->save();
            }
        }
    }

    public function onSendNotificationMessage(Message $message, array &$tokens, string $language, Gateway $gatewayModel): bool
    {
        if (!isset($tokens['formconfig_id']) || !($tokens['formconfig_storeAsSubmission'] ?? false)) {
            return true;
        }

        if (false === json_encode($tokens)) {

            if (Validator::isBinaryUuid($tokens['form_uuid'])) {
                $uuid = StringUtil::binToUuid($tokens['form_uuid']);
                $tokens['raw_data'] = str_replace($tokens['form_uuid'], $uuid, $tokens['raw_data']);
                $tokens['raw_data_filled'] = str_replace($tokens['form_uuid'], $uuid, $tokens['raw_data_filled']);
                $tokens['form_uuid'] = $uuid;
            }

            if (false === json_encode($tokens)) {
                System::log(
                    sprintf("The message '%s' (ID %s) contains invalid tokens!", $message->title, $message->id),
                    __METHOD__,
                    TL_ERROR
                );
            }
        }

        if (version_compare(VERSION, '4.7', '>=')) {
            if (($tokens['formconfig_huhSubAddOptIn'] ?? false) && isset($tokens['formconfig_optInIdentifier'])) {
                $tokens['optInToken'] = $tokens['formconfig_optInIdentifier'];
                $tokens['optInUrl'] = Environment::get('base').'?token='.$tokens['formconfig_optInIdentifier'];
            }
        }

        return true;
    }

}
