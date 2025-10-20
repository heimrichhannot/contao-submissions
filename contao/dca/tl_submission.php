<?php

use Contao\DataContainer;
use Contao\DC_Table;
use HeimrichHannot\UtilsBundle\Dca\DateAddedField;
use Terminal42\NotificationCenterBundle\Token\TokenContext;

$dca = &$GLOBALS['TL_DCA']['tl_submission'];

DateAddedField::register('tl_submission')->setEvalValue('noSubmissionField', true);

// todo: check permission

$dca = [
    'config'   => [
        'dataContainer'     => DC_Table::class,
        'ptable'            => 'tl_submission_archive',
        'enableVersioning'  => true,
        'doNotCopyRecords'  => true,
        'sql'               => [
            'keys' => [
                'id' => 'primary',
                'uuid' => 'unique',
            ],
        ],
    ],
    'list' => [
        'label' => [
            'fields' => ['id'],
            'format' => '%s',
        ],
        'sorting' => [
            'mode'         => 4,
            'fields'       => ['dateAdded DESC'],
            'headerFields' => ['title'],
            'panelLayout'  => 'filter;search,limit',
            'filter'       => [['tstamp>?', 0]],
        ],
        'global_operations' => [
            'all' => [
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();"',
            ],
            'export' => [
                'icon' => 'bundles/heimrichhannotsubmissions/backend/img/export.svg',
            ],
        ],
        'operations' => [
            'edit',
            'copy',
            'delete',
            'toggle' => [
                'href'         => 'act=toggle&amp;field=published',
                'icon'         => 'visible.svg',
                'showInHeader' => true,
            ],
            'show',
        ],
    ],
    'palettes' => [
        'default' => '{submission_legend};{publish_legend},published;'
    ],
    'fields'   => [
        'id' => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
            'eval' => [
                'noSubmissionField' => true,
            ],
        ],
        'pid' => [
            'foreignKey' => 'tl_submission_archive.title',
            'sql' => "int(10) unsigned NOT NULL default '0'",
            'relation' => ['type' => 'belongsTo', 'load' => 'eager'],
            'eval' => [
                'noSubmissionField' => true,
            ],
        ],
        'uuid' => [
            'sql' => "binary(16) NULL",
            'eval' => [
                'noSubmissionField' => true,
            ],
        ],
        'tstamp' => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
            'eval' => [
                'noSubmissionField' => true,
            ],
        ],
        'published' => [
            'exclude' => true,
            'toggle' => true,
            'filter' => true,
            'flag' => DataContainer::SORT_INITIAL_LETTER_ASC,
            'inputType' => 'checkbox',
            'eval' => [
                'tl_class' => 'w50',
                'doNotCopy' => true,
                'noSubmissionField' => true,
            ],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'type' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'select',
            'reference' => &$GLOBALS['TL_LANG']['tl_submission']['reference']['type'],
            'eval' => [
                'includeBlankOption' => true,
                'mandatory' => true,
                'tl_class' => 'w50',
            ],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'gender' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'select',
            'options' => ['male', 'female', 'divers'],
            'reference' => &$GLOBALS['TL_LANG']['MSC']['salutation'],
            'eval' => [
                'mandatory' => true,
                'tl_class' => 'w50 clr',
                'substituteField' => true,
                'includeBlankOption' => true,
            ],
            'sql' => "varchar(10) NOT NULL default ''",
        ],
        'academicTitle' => [
            'exclude' => true,
            'inputType' => 'select',
            'options' => ['Dr.', 'Prof.'],
            'eval' => [
                'maxlength' => 20,
                'includeBlankOption' => true,
                'tl_class' => 'w50',
                'substituteField' => true,
            ],
            'sql' => "varchar(20) NOT NULL default ''",
        ],
        'firstname' => [
            'exclude' => true,
            'search' => true,
            'sorting' => true,
            'flag' => 1,
            'inputType' => 'text',
            'eval' => [
                'mandatory' => true,
                'maxlength' => 64,
                'tl_class' => 'w50',
                'substituteField' => true,
            ],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'lastname' => [
            'exclude' => true,
            'search' => true,
            'sorting' => true,
            'flag' => 1,
            'inputType' => 'text',
            'eval' => [
                'mandatory' => true,
                'maxlength' => 64,
                'tl_class' => 'w50',
                'substituteField' => true,
            ],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'company' => [
            'exclude' => true,
            'search' => true,
            'sorting' => true,
            'flag' => 1,
            'inputType' => 'text',
            'eval' => ['maxlength' => 128, 'tl_class' => 'w50', 'substituteField' => true],
            'sql' => "varchar(128) NOT NULL default ''",
        ],
        'position' => [
            'exclude' => true,
            'search' => true,
            'sorting' => true,
            'flag' => 1,
            'inputType' => 'text',
            'eval' => ['maxlength' => 128, 'tl_class' => 'w50', 'substituteField' => true],
            'sql' => "varchar(128) NOT NULL default ''",
        ],
        'dateOfBirth' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => [
                'datepicker' => true,
                'rgxp' => 'date',
                'tl_class' => 'w50 wizard',
                'substituteField' => true,
            ],
            'sql' => "varchar(10) NOT NULL default ''",
        ],
        'street' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 64, 'tl_class' => 'w50', 'substituteField' => true],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'streetNumber' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 64, 'tl_class' => 'w50', 'substituteField' => true],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'street2' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 64, 'tl_class' => 'w50', 'substituteField' => true],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'postal' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 5, 'tl_class' => 'w50', 'substituteField' => true],
            'sql' => "varchar(5) NOT NULL default ''",
        ],
        'city' => [
            'exclude' => true,
            'filter' => true,
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 32, 'tl_class' => 'w50', 'substituteField' => true],
            'sql' => "varchar(32) NOT NULL default ''",
        ],
        'country' => [
            'exclude' => true,
            'filter' => true,
            'sorting' => true,
            'inputType' => 'select',
            'eval' => [
                'includeBlankOption' => true,
                'chosen' => true,
                'autoCompletionHiddenField' => true,
                'tl_class' => 'w50',
                'substituteField' => true,
            ],
            'sql' => "varchar(2) NOT NULL default ''",
        ],
        'email' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => [
                'mandatory' => true,
                'maxlength' => 64,
                'autoCompletionHiddenField' => true,
                'rgxp' => 'email',
                'decodeEntities' => true,
                'tl_class' => 'w50',
                'substituteField' => true,
            ],
            'nc_context' => TokenContext::Email,
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'phone' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => [
                'maxlength' => 32,
                'rgxp' => 'phone',
                'decodeEntities' => true,
                'tl_class' => 'w50',
                'substituteField' => true,
            ],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'fax' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => [
                'maxlength' => 32,
                'rgxp' => 'phone',
                'decodeEntities' => true,
                'tl_class' => 'w50',
                'substituteField' => true,
            ],
            'sql' => "varchar(64) NOT NULL default ''",
        ],
        'subject' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 128, 'tl_class' => 'w50'],
            'sql' => "varchar(128) NOT NULL default ''",
        ],
        'message' => [
            'exclude' => true,
            'inputType' => 'textarea',
            'eval' => ['tl_class' => 'long clr'],
            'sql' => "text NULL",
        ],
        'agreement' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['mandatory' => true, 'tl_class' => 'w50', 'doNotCopy' => true],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'privacy' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['mandatory' => true, 'tl_class' => 'w50', 'doNotCopy' => true],
            'sql' => "char(1) NOT NULL default ''",
        ],
        'submissionLanguage' => [
            'exclude' => true,
            'filter' => true,
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'readonly' => true],
            'sql' => "varchar(4) NOT NULL default ''",
        ],
        'huhSub_optInTokenId' => [
            'exclude' => true,
            'filter' => false,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 clr', 'readonly' => true, 'noSubmissionField' => true],
            'sql' => "varchar(32) NOT NULL default ''",
        ],
        'huhSub_optInCache' => [
            'exclude' => true,
            'filter' => false,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50 clr', 'noSubmissionField' => true],
            'sql' => "blob NULL",
        ]
    ],
];

// todo: onloaddatacontainer hook if exporter is installed
// System::getContainer()->get('huh.utils.array')->insertInArrayByName(
//     $dca['list']['global_operations'],
//     'all',
//     [
//         'export_csv' => \Contao\System::getContainer()
//             ->get('huh.exporter.action.backendexport')
//             ->getGlobalOperation('export_csv', ($GLOBALS['TL_LANG']['MSC']['export_csv'] ?? 'Export CSV')),
//
//         'export_xls' => \Contao\System::getContainer()
//             ->get('huh.exporter.action.backendexport')
//             ->getGlobalOperation('export_xls', $GLOBALS['TL_LANG']['MSC']['export_xls'] ?? 'Export XLS')
//     ]
// );
