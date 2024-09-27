<?php

namespace HeimrichHannot\Submissions\DataContainer;

use Contao\Controller;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Intl\Countries;
use Contao\DataContainer;
use Contao\Date;
use Contao\DC_Table;
use Contao\StringUtil;
use Contao\System;
use Doctrine\DBAL\Connection;
use HeimrichHannot\FormTypeBundle\Event\FieldOptionsEvent;
use HeimrichHannot\FormTypeBundle\Event\StoreFormDataEvent;
use HeimrichHannot\Submissions\Model\SubmissionArchiveModel;
use HeimrichHannot\Submissions\Model\SubmissionModel;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;

class SubmissionContainer
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Countries $countries,
        private readonly RequestStack $requestStack,
        private readonly Utils $utils,
    ) {}

    #[AsCallback(table: 'tl_submission', target: 'config.oncreate')]
    public function onCreateCallback(string $table, int $id, array $fields, DataContainer $dc): void
        // this is only relevant for creating submissions in the backend
    {
        $this->connection->executeStatement(
            "UPDATE tl_submission SET submissionLanguage=? WHERE id=?",
            [$this->getLocale(), $id]
        );
    }

    #[AsEventListener('huh.form_type.huh_submission.store_form_data')]
    public function onFormTypeCreate(StoreFormDataEvent $event): void
    {
        $data = $event->getData();

        if (empty($data['submissionLanguage'])) {
            $data['submissionLanguage'] = $this->getLocale();
        }

        $event->setData($data);
    }

    protected function getLocale(): string
    {
        return $this->requestStack->getCurrentRequest()?->getLocale() ?? 'en';
    }

    #[AsCallback(table: 'tl_submission', target: 'config.onload')]
    public function onLoadCallback(DataContainer $dc): void
    {
        $this->modifyPalette($dc);
    }

    protected function modifyPalette(DataContainer $dc): void
    {
        Controller::loadDataContainer('tl_submission');
        $dca = &$GLOBALS['TL_DCA']['tl_submission'];

        $submission = SubmissionModel::findByPk($dc->id);
        if (!$submission instanceof SubmissionModel) {
            return;
        }

        $archive = $submission->getArchive();
        if (!$archive instanceof SubmissionArchiveModel) {
            return;
        }

        $submissionFields = StringUtil::deserialize($archive->submissionFields, true);

        // remove subpalette fields from $submissionFields
        foreach ($dca['subpalettes'] ?? [] as $value)
        {
            $subpaletteFields = $this->utils->dca()->getPaletteFields($dc->table, $value);
            $submissionFields = \array_diff($submissionFields, $subpaletteFields);
        }

        // todo: remove fields that are not selected in the archive

        $pm = PaletteManipulator::create()
            ->addLegend('submission_legend', '');

        foreach ($submissionFields as $field) {
            $pm->addField($field, 'submission_legend', PaletteManipulator::POSITION_APPEND);
        }

        $pm->applyToPalette('default', 'tl_submission');

        // mandatory overrides
        $mandatoryOverrides = StringUtil::deserialize($archive->submissionFieldsMandatoryOverride, true);

        foreach ($mandatoryOverrides as $override) {
            $dca['fields'][$override['field']]['eval']['mandatory'] = $override['mandatory'];
        }
    }

    #[AsCallback(table: 'tl_submission', target: 'fields.country.options')]
    public function getCountryOptions(): array
    {
        return $this->countries->getCountries();
    }

    #[AsEventListener('huh.form_type.huh_submission.country.options')]
    public function getFromTypeCountryOptions(FieldOptionsEvent $event): void
    {
        $event->setOptionsByKeyValue($this->countries->getCountries());
        $event->setEmptyOption(true);
    }

    #[AsCallback(table: 'tl_submission', target: 'list.sorting.child_record')]
    public function onSortingChildRecordCallback(array $record): string
    {
        $genHtml = function($title) use ($record) {
            return \sprintf(
                '<div class="tl_content_left">%s <span style="color:#b3b3b3; padding-left:3px">[%s]</span></div>',
                $title,
                Date::parse(\Config::get('datimFormat'), \trim($record['dateAdded']))
            );
        };

        $submission = SubmissionModel::findByPk($record['id']);
        $submissionArchive = $submission?->getArchive();

        if (!$submission instanceof SubmissionModel
            || !$submissionArchive instanceof SubmissionArchiveModel
            || !$submissionArchive->titlePattern)
        {
            return $genHtml($record['id'] ?: '');
        }

        $dca = &$GLOBALS['TL_DCA']['tl_submission'];

        $dc = new DC_Table('tl_submission');
        $dc->id = $submission->id;
        $dc->activeRecord = $submission;

        if (\method_exists($this->utils, 'formatter'))
            // if utils v3 is used
        {
            $formatter = function ($dc, $field, $value) {
                return $this->utils->formatter()->formatDcaFieldValue($dc, $field, $value);
            };
        }
        else // if utils v2 is used
        {
            $formatter = function ($dc, $field, $value) {
                /** @var \HeimrichHannot\UtilsBundle\Form\FormUtil $formUtil */
                $formUtil = System::getContainer()->get('huh.utils.form');
                return $formUtil->prepareSpecialValueForOutput($field, $value, $dc);
            };
        }

        $pregReplaceCallback = function ($matches) use ($submission, $dca, $dc, $formatter) {
            $field = $dca['fields'][$matches[1]] ?? [];
            $value = $submission->{$matches[1]} ?? null;
            return $formatter($dc, $field, $value);
        };

        $title = $submissionArchive->titlePattern;
        $title = \str_replace('%%', '__PERCENT__', $title);
        $title = \preg_replace_callback('/%([^%]+)%/i', $pregReplaceCallback, $title);
        $title = \str_replace('__PERCENT__', '%', $title);

        return $genHtml($title);
    }
}