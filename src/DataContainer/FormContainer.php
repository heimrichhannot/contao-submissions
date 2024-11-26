<?php

namespace HeimrichHannot\Submissions\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use HeimrichHannot\Submissions\NotificationType\OptInChallengeNotificationType;
use HeimrichHannot\UtilsBundle\Util\DcaUtil\GetDcaFieldsOptions;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Terminal42\NotificationCenterBundle\NotificationCenter;

readonly class FormContainer
{
    public function __construct(
        private NotificationCenter $notificationCenter,
        private Utils              $utils
    ) {}

    #[AsCallback(table: 'tl_form', target: 'fields.huhSub_optInField.options')]
    public function getHuhSubOptInFieldOptions(): array
    {
        return $this->utils->dca()->getDcaFields(
            'tl_submission',
            GetDcaFieldsOptions::create()
                ->setLocalizeLabels(false)
                ->setAllowedInputTypes(['checkbox'])
                ->setSkipSorting(true)
        );
    }

    #[AsCallback(table: 'tl_form', target: 'fields.huhSub_optInNotification.options')]
    public function getHuhSubOptInNotificationOptions(): array
    {
        return $this->notificationCenter->getNotificationsForNotificationType(OptInChallengeNotificationType::NAME);
    }
}