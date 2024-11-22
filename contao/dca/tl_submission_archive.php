<?php

use HeimrichHannot\UtilsBundle\Dca\DateAddedField;

$dca = &$GLOBALS['TL_DCA']['tl_submission_archive'];

DateAddedField::register('tl_submission');

$dca = [
    'config'      => [
        'dataContainer'     => 'Table',
        'ctable'            => ['tl_submission'],
        'switchToEdit'      => true,
        'enableVersioning'  => true,
        'sql'               => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],
    'list'        => [
        'label'             => [
            'fields' => ['title'],
            'format' => '%s',
        ],
        'sorting' => [
            'mode' => 1,
            'fields' => ['title'],
            'headerFields' => ['title'],
            'panelLayout' => 'filter;search,limit',
        ],
        'global_operations' => [
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ],
        ],
        'operations' => [
            'edit' => [
                'href' => 'table=tl_submission',
                'icon' => 'edit.svg',
            ],
            'editheader' => [
                'href' => 'act=edit',
                'icon' => 'header.svg',
            ],
            'copy' => [
                'href' => 'act=copy',
                'icon' => 'copy.svg',
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null)
                    . '\'))return false;Backend.getScrollOffset()"',
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg',
            ],
        ],
    ],
    'palettes'    => [
        '__selector__' => [],
        'default' => '{general_legend},title,parentTable,parentField,pid;'
            . '{fields_legend},submissionFields,titlePattern,submissionFieldsMandatoryOverride;'
            . '{notification_legend},nc_submission,nc_confirmation;',
    ],
    'subpalettes' => [],
    'fields'      => [
        'id' => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ],
        'parentTable' => [
            'inputType' => 'select',
            'sql' => "varchar(255) NOT NULL default ''",
            'eval' => [
                'tl_class' => 'w50',
                'chosen' => true,
                'submitOnChange' => true,
                'includeBlankOption' => true,
            ],
        ],
        'parentField' => [
            'inputType' => 'select',
            'sql' => "varchar(255) NOT NULL default ''",
            'eval' => [
                'tl_class' => 'w50',
                'chosen' => true,
                'submitOnChange' => true,
                'includeBlankOption' => true,
            ],
        ],
        'pid' => [
            'inputType' => 'select',
            'sql' => "int(10) unsigned NOT NULL default '0'",
            'eval' => ['tl_class' => 'w50', 'chosen' => true, 'includeBlankOption' => true],
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'title'                             => [
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => 1,
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'submissionFields' => [
            'exclude' => true,
            'inputType' => 'checkboxWizard',
            'eval' => ['multiple' => true, 'tl_class' => 'wizard'],
            'sql' => "blob NULL",
        ],
        'titlePattern' => [
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 128],
            'sql'       => "varchar(128) NOT NULL default ''",
        ],
        /*>>>
        todo: override mandatory property of fields
        <<<*
        'submissionFieldsMandatoryOverride' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission_archive']['submissionFieldsMandatoryOverride'],
            'inputType' => 'multiColumnEditor',
            'eval'      => [
                'tl_class'          => 'long clr',
                'multiColumnEditor' => [
                    'minRowCount' => 0,
                    'fields' => [
                        'field'     => [
                            'inputType'        => 'select',
                            'eval'             => ['groupStyle' => 'width: 48%', 'chosen' => true],
                            'options_callback' => ['HeimrichHannot\Submissions\Submissions', 'getFieldsAsOptions']
                        ],
                        'mandatory' => [
                            'inputType' => 'checkbox',
                            'eval'             => ['groupStyle' => 'width: 20%'],
                        ],
                    ],
                ],
            ],
            'sql'       => "blob NULL",
        ],
        /*>>>
        todo: implement notifications
        <<<*
        'nc_submission'                     => [
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => ['HeimrichHannot\Submissions\Submissions', 'getNotificationsAsOptions'],
            'eval'             => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
            'sql'              => "int(10) unsigned NOT NULL default '0'",
        ],
        'nc_confirmation'                   => [
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => ['HeimrichHannot\Submissions\Submissions', 'getConfirmationNotificationsAsOptions'],
            'eval'             => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
            'sql'              => "int(10) unsigned NOT NULL default '0'",
        ],
        /*<=== \[T]/ ===>*/
    ],
];
