<?php

namespace HeimrichHannot\Submissions\EventListener\Contao;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\StringUtil;
use Contao\Validator;
use HeimrichHannot\Submissions\Util\Tokens;
use HeimrichHannot\UtilsBundle\Util\Utils;
use NotificationCenter\Model\Gateway;
use NotificationCenter\Model\Message;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsHook("sendNotificationMessage")]
readonly class SendNotificationMessageListener
{
    public function __construct(
        private RequestStack $requestStack,
        private Utils        $utils
    ) {}

    public function __invoke(Message $message, array &$tokens, ?string $language, Gateway $gatewayModel): bool
    {
        if (!isset($tokens['formconfig_id']) || !($tokens['formconfig_storeAsSubmission'] ?? false)) {
            return true;
        }

        if (\json_encode($tokens) === false && Validator::isBinaryUuid($formUuid = $tokens['form_uuid'] ?? null))
        {
            $uuid = StringUtil::binToUuid($formUuid);
            $tokens['raw_data'] = \str_replace($formUuid, $uuid, $tokens['raw_data']);
            $tokens['raw_data_filled'] = \str_replace($formUuid, $uuid, $tokens['raw_data_filled']);
            $tokens['form_uuid'] = $uuid;
        }

        $addOptIn = $tokens['formconfig_huhSubAddOptIn'] ?? false;
        $optInId = $tokens['formconfig_optInIdentifier'] ?? null;
        $request = $this->requestStack->getCurrentRequest();

        if ($addOptIn && $optInId && $request)
        {
            $tokens['optInToken'] = $optInId;
            $tokens['optInUrl'] = \sprintf('%s?=%s', $request->getSchemeAndHttpHost(), $optInId);
        }

        if (\json_encode($tokens) === false)
        {
            $this->utils->container()->log(
                \sprintf("The message '%s' (ID %s) contains invalid tokens!", $message->title, $message->id),
                __METHOD__,
                TL_ERROR
            );
            $tokens = Tokens::cleanInvalidTokens($tokens);
        }

        return true;
    }
}