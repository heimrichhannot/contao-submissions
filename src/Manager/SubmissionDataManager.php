<?php

namespace HeimrichHannot\Submissions\Manager;

use Contao\StringUtil;
use Contao\Validator;

/**
 * @internal
 */
class SubmissionDataManager
{
    public function prepareForStorage(array $data, array $fields): array
    {
        unset($data['additionalData'], $data['additionalDataMeta'], $data['additionalDataView']);

        $additionalData = array_diff_key($data, $fields);
        $data = array_intersect_key($data, $fields);
        $data['additionalData'] = $additionalData
            ? json_encode((object) $this->ensureStringUuids($additionalData), JSON_THROW_ON_ERROR)
            : null;

        return $data;
    }

    public function createMetadata(array $data, array $labels, array $widgets, array $files = []): array
    {
        $metadata = [];
        foreach ($widgets as $widgetName => $widget) {
            $name = \array_key_exists($widgetName, $data) ? $widgetName : preg_replace('/\[\]$/', '', (string) $widgetName);
            $value = StringUtil::deserialize(\array_key_exists($name, $data) ? $data[$name] : $widget->value);
            $selected = array_map('strval', array_filter(\is_array($value) ? $value : [$value], static fn ($v) => \is_scalar($v) || null === $v));
            $options = [];
            $collect = function (array $entries) use (&$collect, &$options, $selected): void {
                foreach ($entries as $entry) {
                    if (!\is_array($entry)) {
                        continue;
                    }
                    if (\array_key_exists('value', $entry) && isset($entry['label']) && '' !== (string) $entry['value'] && !($entry['group'] ?? false) && \in_array((string) $entry['value'], $selected, true)) {
                        $options[(string) $entry['value']] = strip_tags(StringUtil::decodeEntities((string) $entry['label']));
                    } else {
                        $collect($entry);
                    }
                }
            };
            $widgetOptions = $widget->options;
            $collect(\is_array($widgetOptions) ? $widgetOptions : []);
            $metadata[$name] = [
                'label' => strip_tags(StringUtil::decodeEntities((string) ($labels[$name] ?? $labels[$widgetName] ?? $widget->label ?? ''))),
                'type' => (string) ($widget->type ?? ''),
                'rgxp' => (string) $widget->rgxp,
                'options' => (object) $options,
            ];
        }
        foreach ($labels as $name => $label) {
            $name = \array_key_exists($name, $data) ? $name : preg_replace('/\[\]$/', '', (string) $name);
            $metadata[$name] ??= [
                'label' => strip_tags(StringUtil::decodeEntities((string) $label)),
            ];
        }

        foreach ($files as $name => $uploads) {
            foreach (isset($uploads['uuid']) ? [$uploads] : $uploads as $upload) {
                if (!\is_array($upload) || empty($upload['uuid']) || !isset($upload['name'])) {
                    continue;
                }
                $uuid = Validator::isBinaryUuid($upload['uuid']) ? StringUtil::binToUuid($upload['uuid']) : $upload['uuid'];
                $metadata[$name] ??= [
                    'label' => strip_tags(StringUtil::decodeEntities((string) ($labels[$name] ?? ''))),
                    'type' => 'upload',
                ];
                $options = (array) ($metadata[$name]['options'] ?? []);
                $options[$uuid] = (string) $upload['name'];
                $metadata[$name]['options'] = (object) $options;
            }
        }

        return $metadata;
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
