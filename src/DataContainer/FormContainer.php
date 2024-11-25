<?php

namespace HeimrichHannot\Submissions\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\Database;
use HeimrichHannot\Submissions\Config\NotificationConfig;
use HeimrichHannot\UtilsBundle\Util\DcaUtil\GetDcaFieldsOptions;
use HeimrichHannot\UtilsBundle\Util\Utils;

class FormContainer
{
    public function __construct(
        private readonly Utils $utils
    ) {}

    #[AsCallback(table: 'tl_form', target: 'fields.huhSubOptInField.options')]
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

    #[AsCallback(table: 'tl_form', target: 'fields.huhSubOptInNotification.options')]
    public function getHuhSubOptInNotificationOptions(): array
    {
        return $this->getNCChoicesByType(NotificationConfig::TYPE_OPT_IN);
    }

    protected function getNCChoicesByType(string $type): array
    {
        $choices       = [];
        $notifications = Database::getInstance()
            ->execute("SELECT id,title FROM tl_nc_notification WHERE type='$type' ORDER BY title");

        while ($notifications->next()) {
            $choices[$notifications->id] = $notifications->title;
        }

        return $choices;
    }
}