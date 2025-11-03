<?php

/**
 * Extend default palette.
 */

use Contao\CoreBundle\DataContainer\PaletteManipulator;

PaletteManipulator::create()
    ->addLegend('submissions_legend', 'amg_legend', PaletteManipulator::POSITION_AFTER)
    ->addField('submissionss', 'submissions_legend', PaletteManipulator::POSITION_APPEND)
    ->addField('submissionsp', 'submissions_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_user_group');

/*
 * Add fields to tl_user_group
 */
$GLOBALS['TL_DCA']['tl_user_group']['fields']['submissionss'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'foreignKey' => 'tl_submission_archive.title',
    'eval' => [
        'multiple' => true,
    ],
    'sql' => 'blob NULL',
];

$GLOBALS['TL_DCA']['tl_user_group']['fields']['submissionsp'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'options' => ['create', 'delete'],
    'reference' => &$GLOBALS['TL_LANG']['MSC'],
    'eval' => [
        'multiple' => true,
    ],
    'sql' => 'blob NULL',
];
