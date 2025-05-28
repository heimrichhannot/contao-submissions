<?php

namespace HeimrichHannot\Submissions\DataContainer;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\FormModel;
use HeimrichHannot\Submissions\FormType\SubmissionType;
use HeimrichHannot\Submissions\NotificationType\OptInChallengeNotificationType;
use HeimrichHannot\UtilsBundle\Util\DcaUtil\GetDcaFieldsOptions;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Terminal42\NotificationCenterBundle\NotificationCenter;

readonly class FormContainer
{
    public function __construct(
        private NotificationCenter $notificationCenter,
        private Utils              $utils,
    ) {}

    public function onConfigOnload(?DataContainer $dc): void
    {
        if (!$dc?->id) {
            return;
        }

        $formModel = FormModel::findByPk($dc->id);
        if (!$formModel) {
            return;
        }

        if (SubmissionType::TYPE === $formModel->formType) {
            return;
        }

        PaletteManipulator::create()
            ->addLegend('huh_submissions_legend', 'store_legend')
            ->addField('huhSub_storeSubmission', 'huh_submissions_legend', PaletteManipulator::POSITION_APPEND)
            ->applyToPalette('default', 'tl_form');
    }

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