<?php

namespace HeimrichHannot\Submissions\EventListener\DataContainer\Submission;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use HeimrichHannot\Submissions\Manager\AdditionalDataRenderer;

#[AsCallback(table: 'tl_submission', target: 'fields.additionalDataView.input_field')]
readonly class FieldsAdditionalDataViewInputFieldListener
{
    public function __construct(
        private AdditionalDataRenderer $renderer,
    ) {
    }

    public function __invoke(DataContainer $dc): string
    {
        $record = $dc->getCurrentRecord() ?? [];

        return $this->renderer->render($record['additionalData'] ?? null, $record['additionalDataMeta'] ?? null);
    }
}
