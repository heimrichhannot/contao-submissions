<?php

namespace HeimrichHannot\Submissions\Manager;

use HeimrichHannot\UtilsBundle\Util\DcaUtil\GetDcaFieldsOptions;
use HeimrichHannot\UtilsBundle\Util\Utils;

readonly class DcaManager
{
    public function __construct(
        private Utils $utils
    ) {}

    /**
     * Retrieves the list of fields from the specified table that are allowed
     * to be used in submissions. Removes fields explicitly flagged as not
     * permissible for submissions.
     *
     * @param string $table The name of the table to retrieve fields from.
     *
     * @return array An array of field names allowed for submissions.
     */
    public function getSubmissibleFields(string $table): array
    {
        $allFields = $this->utils->dca()->getDcaFields(
            $table,
            GetDcaFieldsOptions::create()
        );

        // remove fields that are not allowed in submissions
        $noSubmissionFields = $this->utils->dca()->getDcaFields(
            $table,
            GetDcaFieldsOptions::create()
                ->setEvalConditions(['noSubmissionField' => true])
        );

        if (empty($noSubmissionFields)) {
            return $allFields;
        }

        return \array_diff($allFields, $noSubmissionFields);
    }
}