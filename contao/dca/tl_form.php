<?php

use Contao\CoreBundle\DataContainer\PaletteManipulator;

$dca = &$GLOBALS['TL_DCA']['tl_form'];
$fields = &$dca['fields'];

/**
 * Palettes
 */
$dca['palettes']['__selector__'][] = 'huhSub_storeSubmission';
$dca['palettes']['__selector__'][] = 'huhSub_optIn';

$dca['subpalettes']['huhSub_storeSubmission'] = 'huhSub_submissionArchive,huhSub_optIn';
$dca['subpalettes']['huhSub_optIn']    = 'huhSub_optInNotification,huhSub_optInJumpTo,huhSub_optInField,huhSub_optInTokenInvalidJumpTo';


/**
 * Fields
 */
$fields['huhSub_storeSubmission'] = [
    'exclude'   => true,
    'filter'    => true,
    'inputType' => 'checkbox',
    'eval'      => ['submitOnChange' => true],
    'sql'       => "char(1) NOT NULL default ''"
];

$fields['huhSub_submissionArchive'] = [
    'exclude'    => true,
    'search'     => true,
    'inputType'  => 'select',
    'foreignKey' => 'tl_submission_archive.title',
    'relation'   => ['type' => 'hasOne', 'table' => 'tl_submission_archive'],
    'eval'       => ['chosen' => true, 'tl_class' => 'w50 clr', "mandatory" => true],
    'sql'        => ['type' => 'integer', 'notnull' => true, 'unsigned' => true, 'default' => 0]
];

$fields['huhSub_optIn'] = [
    'exclude'   => true,
    'filter'    => true,
    'inputType' => 'checkbox',
    'eval'      => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
    'sql'       => "char(1) NOT NULL default ''"
];

$fields['huhSub_optInNotification'] = [
    'exclude'   => true,
    'search'    => true,
    'inputType' => 'select',
    'eval'      => ['chosen' => true, 'tl_class' => 'w50 clr', "mandatory" => false],
    'sql'       => ['type' => 'integer', 'notnull' => true, 'unsigned' => true, 'default' => 0]
];

$fields['huhSub_optInJumpTo'] = [
    'exclude'    => true,
    'inputType'  => 'pageTree',
    'foreignKey' => 'tl_page.title',
    'eval'       => ['fieldType' => 'radio', 'tl_class' => 'w50'],
    'sql'        => "int(10) unsigned NOT NULL default 0",
    'relation'   => ['type' => 'hasOne', 'load' => 'lazy']
];

$fields['huhSub_optInField'] = [
    'inputType'        => 'select',
    'default'          => 'published',
    'sql'              => "varchar(64) NOT NULL default ''",
    'eval'             => [
        'tl_class'           => 'w50 clr',
        'chosen'             => true,
        'includeBlankOption' => true,
    ],
];

$fields['huhSub_optInTokenInvalidJumpTo']       = [
    'exclude'    => true,
    'inputType'  => 'pageTree',
    'foreignKey' => 'tl_page.title',
    'eval'       => ['fieldType' => 'radio', 'tl_class' => 'w50'],
    'sql'        => "int(10) unsigned NOT NULL default 0",
    'relation'   => ['type' => 'hasOne', 'load' => 'lazy']
];
