<?php

namespace HeimrichHannot\Submissions\DataContainer;

use Contao\Config;
use Contao\Controller;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Intl\Countries;
use Contao\DataContainer;
use Contao\Date;
use Contao\DC_Table;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use HeimrichHannot\FormTypeBundle\Event\FieldOptionsEvent;
use HeimrichHannot\FormTypeBundle\Event\StoreFormDataEvent;
use HeimrichHannot\Submissions\Model\SubmissionArchiveModel;
use HeimrichHannot\Submissions\Model\SubmissionModel;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;

readonly class SubmissionContainer
{
    public function __construct(
        private Connection $connection,
        private Countries $countries,
        private RequestStack $requestStack,
        private Utils $utils,
    ) {
    }

    /**
     * @noinspection PhpUnused
     */
    #[AsCallback(table: 'tl_submission', target: 'config.oncreate')]
    public function onCreateCallback(string $table, int $id, array $fields, DataContainer $dc): void
    // this is only relevant for creating submissions in the backend
    {
        $this->connection->executeStatement(
            'UPDATE tl_submission SET submissionLanguage=? WHERE id=?',
            [$this->getLocale(), $id]
        );
    }

    #[AsEventListener('huh.form_type.huh_submission.store_form_data')]
    public function onFormTypeCreate(StoreFormDataEvent $event): void
    {
        $data = $event->getData();

        if (!empty($data['email'])) {
            $data['email'] = \mb_strtolower((string) $data['email']);
        }

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
        Controller::loadDataContainer('tl_submission');
        $dca = &$GLOBALS['TL_DCA']['tl_submission'];

        $submission = SubmissionModel::findByPk($dc->id);
        if (!$submission instanceof SubmissionModel) {
            return;
        }

        $archive = $submission->archive();
        if (!$archive instanceof SubmissionArchiveModel) {
            return;
        }

        $submissionFields = StringUtil::deserialize($archive->submissionFields, true);

        // remove subpalette fields from $submissionFields
        foreach ($dca['subpalettes'] ?? [] as $value) {
            $subpaletteFields = $this->utils->dca()->getPaletteFields($dc->table, $value);
            $submissionFields = \array_diff($submissionFields, $subpaletteFields);
        }

        PaletteManipulator::create()
            ->addLegend('submission_legend', '')
            ->addField($submissionFields, 'submission_legend', PaletteManipulator::POSITION_APPEND)
            ->applyToPalette('default', 'tl_submission');

        // mandatory overrides
        $mandatoryOverrides = StringUtil::deserialize($archive->submissionFieldsMandatoryOverride, true);

        foreach ($mandatoryOverrides as $override) {
            if (!empty($dca['fields'][$override['field'] ?? null]) && isset($override['mandatory'])) {
                $dca['fields'][$override['field'] ?? null]['eval']['mandatory'] = $override['mandatory'] ? '1' : '';
            }
        }
    }

    /**
     * @noinspection PhpUnused
     */
    #[AsCallback(table: 'tl_submission', target: 'fields.country.options')]
    public function getCountryOptions(): array
    {
        return $this->countries->getCountries();
    }

    #[AsEventListener('huh.form_type.huh_submission.country.options')]
    public function getFormTypeCountryOptions(FieldOptionsEvent $event): void
    {
        $event->setOptionsByKeyValue($this->countries->getCountries());
        $event->setEmptyOption(true);
    }

    /**
     * @noinspection PhpUnused
     */
    #[AsCallback(table: 'tl_submission', target: 'list.sorting.child_record')]
    public function onSortingChildRecordCallback(array $record): string
    {
        $genHtml = (fn ($title) => \sprintf(
            '<div class="tl_content_left">%s <span style="color:#b3b3b3; padding-left:3px">[%s]</span></div>',
            $title,
            Date::parse(Config::get('datimFormat'), \trim((string) $record['dateAdded']))
        ));

        $submission = SubmissionModel::findByPk($record['id']);
        $submissionArchive = $submission?->archive();

        if (!$submission instanceof SubmissionModel
            || !$submissionArchive instanceof SubmissionArchiveModel
            || !$submissionArchive->titlePattern) {
            return $genHtml($record['id'] ?: '');
        }

        $dca = &$GLOBALS['TL_DCA']['tl_submission'];

        $dc = new DC_Table('tl_submission');
        $dc->id = $submission->id;
        $dc->activeRecord = $submission;

        $pregReplaceCallback = function ($matches) use ($submission, $dc) {
            $fieldName = $matches[1];
            $value = $submission->{$fieldName} ?? null;

            return $this->utils->formatter()->formatDcaFieldValue($dc, $fieldName, $value);
        };

        $title = $submissionArchive->titlePattern;
        $title = \str_replace('%%', '__PERCENT__', $title);
        $title = \preg_replace_callback('/%([^%]+)%/i', $pregReplaceCallback, $title);
        $title = \str_replace('__PERCENT__', '%', $title);
        $title = \trim($title);

        return $genHtml($title);
    }

    /**
     * @noinspection PhpUnused
     */
    #[AsCallback(table: 'tl_submission', target: 'list.label.group')]
    public function onListLabelGroupCallback(
        string $group,
        ?string $mode,
        string $field,
        array $recordData,
        DataContainer $dc,
    ): string {
        return \sprintf(
            '<div class="tl_content_left">%s</div>',
            Date::parse(Config::get('dateFormat'), $recordData['dateAdded'])
        );
    }
}
