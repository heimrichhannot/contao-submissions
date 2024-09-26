<?php

namespace HeimrichHannot\Submissions\DataContainer;

use Contao\Controller;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Intl\Countries;
use Contao\DataContainer;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use HeimrichHannot\FormTypeBundle\Event\FieldOptionsEvent;
use HeimrichHannot\Submissions\Model\SubmissionArchiveModel;
use HeimrichHannot\Submissions\Model\SubmissionModel;
use HeimrichHannot\UtilsBundle\Util\Utils;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;

class SubmissionsContainer
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
        $locale = $this->requestStack->getCurrentRequest()?->getLocale() ?? 'en';

        $this->connection->executeStatement(
            "UPDATE tl_submission SET submissionLanguage=? WHERE id=?",
            [$locale, $id]
        );
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

        $submission = SubmissionModel::findByPk($dca->id);
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

        $dca['palettes']['default'] = \str_replace(
            'submissionFields',
            implode(',', $submissionFields),
            '{general_legend},authorType,author;{submission_legend},submissionFields;{publish_legend},published;'
        );

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
}