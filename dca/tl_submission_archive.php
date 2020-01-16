<?php

$arrDca = &$GLOBALS['TL_DCA']['tl_submission_archive'];

$arrDca = [
    'config'      => [
        'dataContainer'     => 'Table',
        'ctable'            => ['tl_submission'],
        'switchToEdit'      => true,
        'enableVersioning'  => true,
        'oncreate_callback' => [
            ['HeimrichHannot\Submissions\Backend\SubmissionArchiveBackend', 'onCreate'],
        ],
        'onload_callback'   => [
            ['HeimrichHannot\Submissions\Backend\SubmissionArchiveBackend', 'checkPermission'],
        ],
        'onsubmit_callback' => [
            ['HeimrichHannot\Haste\Dca\General', 'setDateAdded'],
        ],
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
        'sorting'           => [
            'mode'         => 1,
            'fields'       => ['title'],
            'headerFields' => ['title'],
            'panelLayout'  => 'filter;search,limit',
        ],
        'global_operations' => [
            'all' => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ],
        ],
        'operations'        => [
            'edit'       => [
                'label' => &$GLOBALS['TL_LANG']['tl_submission_archive']['edit'],
                'href'  => 'table=tl_submission',
                'icon'  => 'edit.gif',
            ],
            'editheader' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_submission_archive']['editheader'],
                'href'            => 'act=edit',
                'icon'            => 'header.gif',
                'button_callback' => ['HeimrichHannot\Submissions\Backend\SubmissionArchiveBackend', 'editHeader'],
            ],
            'copy'       => [
                'label'           => &$GLOBALS['TL_LANG']['tl_submission_archive']['copy'],
                'href'            => 'act=copy',
                'icon'            => 'copy.gif',
                'button_callback' => ['HeimrichHannot\Submissions\Backend\SubmissionArchiveBackend', 'copyArchive'],
            ],
            'delete'     => [
                'label'           => &$GLOBALS['TL_LANG']['tl_submission_archive']['copy'],
                'href'            => 'act=delete',
                'icon'            => 'delete.gif',
                'attributes'      => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                                     . '\'))return false;Backend.getScrollOffset()"',
                'button_callback' => ['HeimrichHannot\Submissions\Backend\SubmissionArchiveBackend', 'deleteArchive'],
            ],
            'show'       => [
                'label' => &$GLOBALS['TL_LANG']['tl_submission_archive']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
        ],
    ],
    'palettes'    => [
        '__selector__' => [],
        'default'      => '{general_legend},title,parentTable,parentField,pid;{fields_legend},submissionFields,titlePattern;'
                          . '{notification_legend},nc_submission,nc_confirmation;',
    ],
    'subpalettes' => [],
    'fields'      => [
        'id'               => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ],
        'parentTable'      => [
            'label'            => &$GLOBALS['TL_LANG']['tl_submission_archive']['parentTable'],
            'inputType'        => 'select',
            'options_callback' => ['\HeimrichHannot\Haste\Dca\General', 'getDataContainers'],
            'sql'              => "varchar(255) NOT NULL default ''",
            'eval'             => [
                'tl_class'           => 'w50',
                'chosen'             => true,
                'submitOnChange'     => true,
                'includeBlankOption' => true,
            ],
        ],
        'parentField'      => [
            'label'            => &$GLOBALS['TL_LANG']['tl_submission_archive']['parentField'],
            'inputType'        => 'select',
            'options_callback' => ['HeimrichHannot\Submissions\Backend\SubmissionArchiveBackend', 'getParentFields'],
            'sql'              => "varchar(255) NOT NULL default ''",
            'eval'             => [
                'tl_class'           => 'w50',
                'chosen'             => true,
                'submitOnChange'     => true,
                'includeBlankOption' => true,
            ],
        ],
        'pid'              => [
            'label'            => &$GLOBALS['TL_LANG']['tl_submission_archive']['pid'],
            'inputType'        => 'select',
            'options_callback' => ['HeimrichHannot\Submissions\Backend\SubmissionArchiveBackend', 'getParentEntitiesAsOptions'],
            'sql'              => "int(10) unsigned NOT NULL default '0'",
            'eval'             => ['tl_class' => 'w50', 'chosen' => true, 'includeBlankOption' => true],
        ],
        'tstamp'           => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'dateAdded'        => [
            'label'   => &$GLOBALS['TL_LANG']['MSC']['dateAdded'],
            'sorting' => true,
            'flag'    => 6,
            'eval'    => ['rgxp' => 'datim', 'doNotCopy' => true],
            'sql'     => "int(10) unsigned NOT NULL default '0'",
        ],
        'title'            => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission_archive']['title'],
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => 1,
            'inputType' => 'text',
            'eval'      => ['mandatory' => true, 'maxlength' => 128, 'tl_class' => 'w50'],
            'sql'       => "varchar(128) NOT NULL default ''",
        ],
        'submissionFields' => [
            'label'            => &$GLOBALS['TL_LANG']['tl_submission_archive']['submissionFields'],
            'exclude'          => true,
            'inputType'        => 'checkboxWizard',
            'options_callback' => ['HeimrichHannot\Submissions\Submissions', 'getFieldsAsOptions'],
            'eval'             => ['multiple' => true, 'tl_class' => 'wizard'],
            'sql'              => "blob NULL",
        ],
        'titlePattern'     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission_archive']['titlePattern'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 128, 'tl_class' => 'w50'],
            'sql'       => "varchar(128) NOT NULL default ''",
        ],
        'nc_submission'    => [
            'label'            => &$GLOBALS['TL_LANG']['tl_submission_archive']['nc_submission'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => ['HeimrichHannot\Submissions\Submissions', 'getNotificationsAsOptions'],
            'eval'             => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
            'sql'              => "int(10) unsigned NOT NULL default '0'",
        ],
        'nc_confirmation'  => [
            'label'            => &$GLOBALS['TL_LANG']['tl_submission_archive']['nc_confirmation'],
            'exclude'          => true,
            'inputType'        => 'select',
            'options_callback' => ['HeimrichHannot\Submissions\Submissions', 'getConfirmationNotificationsAsOptions'],
            'eval'             => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
            'sql'              => "int(10) unsigned NOT NULL default '0'",
        ],
    ],
];
