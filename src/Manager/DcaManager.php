<?php

namespace HeimrichHannot\Submissions\Manager;

use HeimrichHannot\UtilsBundle\Util\DcaUtil\GetDcaFieldsOptions;
use HeimrichHannot\UtilsBundle\Util\Utils;

class DcaManager
{
    public function __construct(
        private readonly Utils $utils
    ) {}

    public function getSubmissibleFields(string $table): array
    {
        $fields = $this->utils->dca()->getDcaFields(
            $table,
            GetDcaFieldsOptions::create()
        );

        // remove fields that are not allowed in submissions
        $noSubmissionFields = $this->utils->dca()->getDcaFields(
            $table,
            GetDcaFieldsOptions::create()
                ->setEvalConditions(['noSubmissionField' => true])
        );

        return \array_diff($fields, $noSubmissionFields);
    }
}