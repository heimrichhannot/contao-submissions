<?php

/**
 * Extend default palette
 */
$GLOBALS['TL_DCA']['tl_user']['palettes']['extend'] = str_replace('fop;', 'fop;{submissions_legend},submissionss,submissionsp;', $GLOBALS['TL_DCA']['tl_user']['palettes']['extend']);
$GLOBALS['TL_DCA']['tl_user']['palettes']['custom'] = str_replace('fop;', 'fop;{submissions_legend},submissionss,submissionsp;', $GLOBALS['TL_DCA']['tl_user']['palettes']['custom']);


/**
 * Add fields to tl_user_group
 */
$GLOBALS['TL_DCA']['tl_user']['fields']['submissionss'] = [
    'label'                   => &$GLOBALS['TL_LANG']['tl_user']['submissionss'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'foreignKey'              => 'tl_submission_archive.title',
    'eval'                    => ['multiple' => true],
    'sql'                     => "blob NULL"];

$GLOBALS['TL_DCA']['tl_user']['fields']['submissionsp'] = [
    'label'                   => &$GLOBALS['TL_LANG']['tl_user']['submissionsp'],
    'exclude'                 => true,
    'inputType'               => 'checkbox',
    'options'                 => ['create', 'delete'],
    'reference'               => &$GLOBALS['TL_LANG']['MSC'],
    'eval'                    => ['multiple' => true],
    'sql'                     => "blob NULL"];
