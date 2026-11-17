<?php

namespace HeimrichHannot\Submissions\Manager;

use Contao\StringUtil;
use Contao\Validator;

class SubmissionDataManager
{
    public function prepareForStorage(array $data, array $fields): array
    {
        unset($data['additionalData']);

        $additionalData = array_diff_key($data, $fields);
        $data = array_intersect_key($data, $fields);
        $data['additionalData'] = $additionalData
            ? json_encode((object) $this->ensureStringUuids($additionalData), JSON_THROW_ON_ERROR)
            : null;

        return $data;
    }

    /**
     * Mirrors Contao 5.7, including preservation of serialized widget values.
     */
    private function ensureStringUuids(mixed $data): mixed
    {
        if (method_exists(StringUtil::class, 'ensureStringUuids')) {
            return StringUtil::ensureStringUuids($data);
        }

        if (!\is_string($data) && !\is_array($data)) {
            return $data;
        }

        $deserialized = StringUtil::deserialize($data);

        if (\is_array($deserialized)) {
            $processed = array_map($this->ensureStringUuids(...), $deserialized);

            return \is_string($data) ? serialize($processed) : $processed;
        }

        return Validator::isBinaryUuid($data) ? StringUtil::binToUuid($data) : $data;
    }
}
