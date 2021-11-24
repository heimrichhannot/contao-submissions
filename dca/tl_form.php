<?php

$dca = &$GLOBALS['TL_DCA']['tl_form'];

$dca['palettes']['__selector__'][]       = 'storeAsSubmission';
$dca['subpalettes']['storeAsSubmission'] = 'submissionArchive';

$dca['palettes']['default'] = str_replace(',storeValues', ',storeValues,storeAsSubmission', $dca['palettes']['default']);

$fields = [
    'storeAsSubmission' => [
        'label'     => &$GLOBALS['TL_LANG']['tl_form']['storeAsSubmission'],
        'exclude'   => true,
        'filter'    => true,
        'inputType' => 'checkbox',
        'eval'      => ['submitOnChange' => true],
        'sql'       => "char(1) NOT NULL default ''"
    ],
    'submissionArchive' => [
        'label'     => &$GLOBALS['TL_LANG']['tl_form']['submissionArchive'],
        'exclude'    => true,
        'search'     => true,
        'inputType'  => 'select',
        'foreignKey' => 'tl_submission_archive.title',
        'relation'   => ['type' => 'hasOne', 'table' => 'tl_submission_archive'],
        'eval'       => ['chosen' => true, 'tl_class' => 'w50', "mandatory" => true],
        'sql'        => ['type' => 'integer', 'notnull' => true, 'unsigned' => true, 'default' => 0]
    ],
];

$dca['fields'] = array_merge($dca['fields'], $fields);