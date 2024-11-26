<?php

namespace HeimrichHannot\Submissions\EventListener;

use Contao\StringUtil;
use Contao\System;
use Contao\Validator;
use HeimrichHannot\Submissions\SubmissionModel;
use HeimrichHannot\Submissions\Util\Tokens;
use NotificationCenter\Model\Gateway;
use NotificationCenter\Model\Message;

class FormGeneratorListener
{
    public function onSendNotificationMessage(Message $message, array &$tokens, ?string $language, Gateway $gatewayModel): bool
    {
        if (!isset($tokens['formconfig_id']) || !($tokens['formconfig_huhSub_storeSubmission'] ?? false)) {
            return true;
        }

        if (false === json_encode($tokens)) {
            if (Validator::isBinaryUuid($tokens['form_uuid'])) {
                $uuid = StringUtil::binToUuid($tokens['form_uuid']);
                $tokens['raw_data'] = str_replace($tokens['form_uuid'], $uuid, $tokens['raw_data']);
                $tokens['raw_data_filled'] = str_replace($tokens['form_uuid'], $uuid, $tokens['raw_data_filled']);
                $tokens['form_uuid'] = $uuid;
            }
        }

        if (version_compare(VERSION, '4.7', '>=')) {
            if (($tokens['formconfig_huhSub_optIn'] ?? false)
                && isset($tokens['formconfig_optInIdentifier'])
                && System::getContainer()->get('request_stack')->getCurrentRequest()
            ) {
                $base = System::getContainer()->get('request_stack')->getCurrentRequest()->getSchemeAndHttpHost();
                $tokens['optInToken'] = $tokens['formconfig_optInIdentifier'];
                $tokens['optInUrl'] = $base.'?token='.$tokens['formconfig_optInIdentifier'];
            }
        }

        if (false === json_encode($tokens)) {
            System::log(
                sprintf("The message '%s' (ID %s) contains invalid tokens!", $message->title, $message->id),
                __METHOD__,
                TL_ERROR
            );
            $tokens = Tokens::cleanInvalidTokens($tokens);
        }

        return true;
    }

}
