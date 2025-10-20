<?php

use Contao\DC_Table;
use HeimrichHannot\UtilsBundle\Dca\DateAddedField;

$dca = &$GLOBALS['TL_DCA']['tl_submission_archive'];

DateAddedField::register('tl_submission')->setEvalValue('noSubmissionField', true);

// todo: implement permission check

$dca = [
    'config'      => [
        'dataContainer'     => DC_Table::class,
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
        'label' => [
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
            'editheader' => [
                'href' => 'act=edit',
                'icon' => 'header.svg',
            ],
            'edit' => [
                'href' => 'table=tl_submission',
                'icon' => 'edit.svg',
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
        'default' => '{general_legend},title,titlePattern;'
            . '{fields_legend},submissionFields,submissionFieldsMandatoryOverride;'
            . '{advanced_legend},allowExport',
    ],
    'subpalettes' => [],
    'fields'      => [
        'id' => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
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
            'eval'      => ['mandatory' => true, 'maxlength' => 255],
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
            'eval'      => ['maxlength' => 256],
            'sql'       => "varchar(256) NOT NULL default ''",
        ],
        'allowExport' => [
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50'],
            'sql'       => [
                'type'    => 'boolean',
                'default' => '0',
            ],
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
        */
    ],
];
