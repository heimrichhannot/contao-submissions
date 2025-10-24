<?php

namespace HeimrichHannot\Submissions\EventListener\Integration;

use Codefog\HasteBundle\Formatter;
use Doctrine\DBAL\Connection;
use HeimrichHannot\FormTypeBundle\Event\FieldOptionsEvent;
use HeimrichHannot\FormTypeBundle\Event\LoadFormFieldEvent;
use HeimrichHannot\FormTypeBundle\Event\StoreFormDataEvent;
use InspiredMinds\ContaoFieldsetDuplication\Helper\FieldHelper;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class FieldsetDuplicationListener
{
    private FieldHelper $fieldHelper;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly Connection $connection,
        private readonly Formatter $formatter,
        private readonly EventDispatcherInterface $eventDispatcher,
    )
    {
    }

    public function setFieldHelper(FieldHelper $helper): void
    {
        $this->fieldHelper = $helper;
    }

    /**
     * We need to restore the options added through an options event listener for duplicated fields
     */
    #[AsEventListener('huh.form_type.huh_submission.load_form_field')]
    public function onLoadFormFieldEvent(LoadFormFieldEvent $event): void
    {
        if (!isset($this->fieldHelper)) {
            return;
        }

        $widget = $event->getWidget();
        $name = $widget->name;
        if (!str_contains($name, '_duplicate_')) {
            return;
        }

        if (!\in_array($widget->type, ['select', 'radio', 'checkbox'])) {
            return;
        }

        $intPos = strpos($name, '_duplicate_');
        $originalName = substr($name, 0, $intPos);


        if (!\is_array($arrOptions = $widget->options) || empty($arrOptions))
        {
            $arrOptions = [];
        }

        /** @var FieldOptionsEvent $event */
        $event = $this->eventDispatcher->dispatch(
            new FieldOptionsEvent($widget, $event->getForm(), $arrOptions),
            \sprintf(
                'huh.form_type.%s.%s.options',
                $event->getForm()->formType,
                $originalName
            )
        );

        if ($event->isDirty())
        {
            $options = $event->getOptions();

            if ($event->isEmptyOption())
            {
                $options = \array_merge([
                    $event->createOptions('', $event->getEmptyOptionLabel())
                ], $options);
            }

            $widget->options = $options;
        }
    }

    #[AsEventListener('huh.form_type.huh_submission.store_form_data')]
    public function onStoreFormDataEvent(StoreFormDataEvent $event): void
    {
        if (!isset($this->fieldHelper)) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return;
        }

        $allFields = $this->connection->fetchAllAssociative(
            'SELECT * 
            FROM tl_form_field 
            WHERE pid=? AND invisible=? 
            ORDER BY sorting',
            [$event->getForm()->id, '']
        );

        $duplicateFields = $this->getDuplicateFields($allFields);
        $postData = $request->request->all();
        $storeData = $event->getData();

        foreach ($duplicateFields as $fieldset) {
            $fieldsetFields = [];

            // Collect the fields
            foreach ($fieldset['fields'] as $field) {
                foreach ($postData as $name => $value) {
                    if (preg_match('/^('.preg_quote($field['name']).')(_duplicate_(\d+))?$/', $name, $matches)) {
                        $index = (int) ($matches[3] ?? 0) + 1;
                        $fieldsetFields[$index][$field['name']] = $value;
                    }
                }
            }

            $storeData[$fieldset['fieldset']['name']] = $fieldsetFields;
        }

        $event->setData($storeData);
    }

    public function onFieldOptionsListener()
    {

    }

    public function getDuplicateFields(array $allFields): array
    {
        static $duplicateFields = null;

        $depth = 0;

        if (!\is_array($duplicateFields)) {
            $duplicateFields = [];
            $fieldsetGroup = null;

            foreach ($allFields as $field) {
                if ($this->fieldHelper->isFieldsetStart((object) $field)) {
                    $depth++;
                    if ($depth > 1) {
                        continue;
                    }
                    if (!($field['allowDuplication'] ?? false)) {
                        continue;
                    }

                    $fieldsetGroup = $field['name'];

                    $duplicateFields[$fieldsetGroup] = [
                        'fieldset' => $field,
                        'fields' => [],
                    ];

                    continue;
                }

                if ($this->fieldHelper->isFieldsetStop((object) $field)) {
                    $depth--;
                    if ($depth >= 1) {
                        continue;
                    }
                    $fieldsetGroup = null;
                    continue;
                }

                if (null !== $fieldsetGroup) {
                    $duplicateFields[$fieldsetGroup]['fields'][] = $field;
                }
            }
        }

        return $duplicateFields;
    }
}