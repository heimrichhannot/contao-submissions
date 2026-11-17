<?php

namespace HeimrichHannot\Submissions\Manager;

use Contao\Config;
use Contao\Date;
use Contao\StringUtil;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class AdditionalDataRenderer
{
    public function __construct(
        private readonly Environment $twig,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function render(array|string|null $data, array|string|null $metadata): string
    {
        $error = false;
        try {
            $values = \is_array($data) ? $data : json_decode(null === $data || '' === $data ? '{}' : $data, true, flags: JSON_THROW_ON_ERROR);
            if (!\is_array($values)) {
                throw new \JsonException();
            }
        } catch (\JsonException) {
            $values = [];
            $error = true;
        }
        try {
            $meta = \is_array($metadata) ? $metadata : json_decode($metadata ?: '{}', true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            $meta = [];
        }
        $meta = array_filter(\is_array($meta) ? $meta : [], 'is_array');
        foreach ($meta as &$config) {
            if (!\is_string($config['label'] ?? null)) {
                unset($config['label']);
            }
            $config['options'] = array_filter((array) ($config['options'] ?? []), 'is_scalar');
        }
        unset($config);
        if (!$values && !$error) {
            return '';
        }

        $rows = [];
        foreach ($values as $name => $value) {
            $config = \is_array($meta[$name] ?? null) ? $meta[$name] : [];
            $rows[] = [
                'label' => ($config['label'] ?? '') ?: $this->humanize((string) $name),
                'node' => $this->node($value, $config, \is_array($meta) ? $meta : [], 0),
            ];
        }

        return $this->twig->render('@Contao/backend/submission_additional_data.html.twig', [
            'rows' => $rows,
            'error' => $error ? $this->text('error') : null,
        ]);
    }

    private function node(mixed $value, array $config, array $metadata, int $depth): array
    {
        if ($depth > 12) {
            return [
                'kind' => 'text',
                'value' => $this->text('depth'),
            ];
        }
        $value = StringUtil::deserialize($value);
        if (\is_array($value)) {
            $numeric = !array_filter(array_keys($value), static fn ($key) => !\is_int($key));
            if ($value && $numeric && !array_filter($value, static fn ($row) => !\is_array($row))) {
                $columns = [];
                foreach ($value as $row) {
                    foreach (array_keys($row) as $key) {
                        $columns[$key] = ($metadata[$key]['label'] ?? '') ?: $this->humanize((string) $key);
                    }
                }
                $rows = [];
                foreach ($value as $index => $row) {
                    $cells = [];
                    foreach ($columns as $key => $label) {
                        $specific = (string) $key . '_duplicate_' . ((int) $index - 1);
                        $fieldMeta = $metadata[$specific] ?? $metadata[$key] ?? [];
                        $cells[] = $this->node($row[$key] ?? null, \is_array($fieldMeta) ? $fieldMeta : [], $metadata, $depth + 1);
                    }
                    $rows[] = $cells;
                }

                return [
                    'kind' => 'table',
                    'columns' => $columns,
                    'rows' => $rows,
                ];
            }
            $children = [];
            foreach ($value as $key => $item) {
                $children[] = [
                    'label' => $numeric ? null : (($metadata[$key]['label'] ?? '') ?: $this->humanize((string) $key)),
                    'node' => $this->node($item, $numeric ? $config : ($metadata[$key] ?? []), $metadata, $depth + 1),
                ];
            }

            return [
                'kind' => 'list',
                'children' => $children,
            ];
        }
        if (null === $value || '' === $value) {
            $value = '—';
        } elseif (\is_scalar($value) && isset($config['options'][(string) $value])) {
            $value = $config['options'][(string) $value];
        } elseif (\is_bool($value)) {
            $value = $this->text($value ? 'yes' : 'no');
        } elseif (\in_array($config['rgxp'] ?? null, ['date', 'time', 'datim'], true) && is_numeric($value)) {
            $value = Date::parse((string) Config::get($config['rgxp'] . 'Format'), (int) $value);
        } elseif (\is_string($value)) {
            $value = StringUtil::decodeEntities($value);
        }

        return [
            'kind' => 'text',
            'value' => null === $value || '' === $value ? '—' : (string) $value,
        ];
    }

    private function humanize(string $key): string
    {
        return ucfirst(trim(preg_replace('/[_-]+|(?<=[a-z])(?=[A-Z])/', ' ', StringUtil::decodeEntities($key))));
    }

    private function text(string $key): string
    {
        return $this->translator->trans('tl_submission.additionalDataView.' . $key, [], 'contao_tl_submission');
    }
}
