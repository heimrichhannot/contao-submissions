<?php

namespace HeimrichHannot\Submissions\EventListener;

use Contao\Database;
use Contao\Environment;
use Contao\Form;
use Contao\System;
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

            return $data;
        }
    }

    public function onProcessFormData(array $submittedData, array $formData, ?array $files, array $labels, Form $form): void
    {
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
        if (version_compare(VERSION, '4.7', '>=')) {
            if (!isset($tokens['formconfig_id'])) {
                return true;
            }
            if (
                !($tokens['formconfig_storeAsSubmission'] ?? false)
                || !($tokens['formconfig_huhSubAddOptIn'] ?? false)
                || !isset($tokens['formconfig_optInIdentifier'])
            ) {
                return true;
            }

            $tokens['optInToken'] = $tokens['formconfig_optInIdentifier'];
            $tokens['optInUrl'] = Environment::get('base').'?token='.$tokens['formconfig_optInIdentifier'];
        }

        return true;
    }

}
