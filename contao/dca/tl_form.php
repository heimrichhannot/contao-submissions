<?php

use HeimrichHannot\Haste\Dca\General;
use HeimrichHannot\Submissions\Submissions;

$dca = &$GLOBALS['TL_DCA']['tl_form'];
$fields = &$dca['fields'];

/**
 * Palettes
 */
$dca['palettes']['__selector__'][] = 'storeAsSubmission';
$dca['palettes']['__selector__'][] = 'huhSubAddOptIn';

$dca['palettes']['default'] = str_replace(',storeValues', ',storeValues,storeAsSubmission', $dca['palettes']['default']);

$dca['subpalettes']['storeAsSubmission'] = 'submissionArchive,huhSubAddOptIn';
$dca['subpalettes']['huhSubAddOptIn']    = 'huhSubOptInNotification,huhSubOptInJumpTo,huhSubOptInField,huhSubOptInTokenInvalidJumpTo';

/**
 * Fields
 */
$fields['storeAsSubmission'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_form']['storeAsSubmission'],
    'exclude'   => true,
    'filter'    => true,
    'inputType' => 'checkbox',
    'eval'      => ['submitOnChange' => true],
    'sql'       => "char(1) NOT NULL default ''"
];

$fields['submissionArchive'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_form']['submissionArchive'],
    'exclude'    => true,
    'search'     => true,
    'inputType'  => 'select',
    'foreignKey' => 'tl_submission_archive.title',
    'relation'   => ['type' => 'hasOne', 'table' => 'tl_submission_archive'],
    'eval'       => ['chosen' => true, 'tl_class' => 'w50', "mandatory" => true],
    'sql'        => ['type' => 'integer', 'notnull' => true, 'unsigned' => true, 'default' => 0]
];

$fields['huhSubAddOptIn'] = [
    'exclude'   => true,
    'filter'    => true,
    'inputType' => 'checkbox',
    'eval'      => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
    'sql'       => "char(1) NOT NULL default ''"
];

$fields['huhSubOptInNotification'] = [
    'exclude'          => true,
    'search'           => true,
    'inputType'        => 'select',
    'options_callback' => static function () {
        return Submissions::getNotificationOptionsByType(Submissions::NOTIFICATION_TYPE_OPTIN);
    },
    'eval'             => ['chosen' => true, 'tl_class' => 'w50', "mandatory" => true],
    'sql'              => ['type' => 'integer', 'notnull' => true, 'unsigned' => true, 'default' => 0]
];

$fields['huhSubOptInJumpTo'] = [
    'exclude'    => true,
    'inputType'  => 'pageTree',
    'foreignKey' => 'tl_page.title',
    'eval'       => ['fieldType' => 'radio', 'tl_class' => 'clr'],
    'sql'        => "int(10) unsigned NOT NULL default 0",
    'relation'   => ['type' => 'hasOne', 'load' => 'lazy']
];

$fields['huhSubOptInTokenInvalidJumpTo']       = [
    'exclude'    => true,
    'inputType'  => 'pageTree',
    'foreignKey' => 'tl_page.title',
    'eval'       => ['fieldType' => 'radio', 'tl_class' => 'clr'],
    'sql'        => "int(10) unsigned NOT NULL default 0",
    'relation'   => ['type' => 'hasOne', 'load' => 'lazy']
];

$fields['huhSubOptInField'] = [
    'inputType'        => 'select',
    'options_callback' => static function() {
        return General::getFields('tl_submission', false, ['checkbox'], [], false);
    },
    'default'          => 'published',
    'sql'              => "varchar(64) NOT NULL default ''",
    'eval'             => [
        'tl_class'           => 'w50',
        'chosen'             => true,
        'includeBlankOption' => true,
    ],
];
