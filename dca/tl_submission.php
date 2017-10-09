<?php

$arrDca = &$GLOBALS['TL_DCA']['tl_submission'];

$arrDca = [
    'config'   => [
        'dataContainer'     => 'Table',
        'ptable'            => 'tl_submission_archive',
        'enableVersioning'  => true,
        'doNotCopyRecords'  => true,
        'onload_callback'   => [
            ['HeimrichHannot\Haste\Dca\General', 'setDateAdded', true],
            ['HeimrichHannot\Submissions\Backend\SubmissionBackend', 'checkPermission'],
            ['HeimrichHannot\Submissions\Backend\SubmissionBackend', 'modifyPalette', true],
        ],
        'onsubmit_callback' => [
            ['HeimrichHannot\Submissions\Backend\SubmissionBackend', 'moveAttachments'],
        ],
        'sql'               => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],
    'list'     => [
        'label'             => [
            'fields' => ['id'],
            'format' => '%s',
        ],
        'sorting'           => [
            'mode'                  => 4,
            'fields'                => ['dateAdded DESC'],
            'headerFields'          => ['title'],
            'panelLayout'           => 'filter;search,limit',
            'child_record_callback' => ['HeimrichHannot\Submissions\Backend\SubmissionBackend', 'listChildren'],
            'filter'                => [['tstamp>?', 0]],
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
            'edit'              => [
                'label' => &$GLOBALS['TL_LANG']['tl_submission']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ],
            'copy'              => [
                'label' => &$GLOBALS['TL_LANG']['tl_submission']['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif',
            ],
            'delete'            => [
                'label'      => &$GLOBALS['TL_LANG']['tl_submission']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                                . '\'))return false;Backend.getScrollOffset()"',
            ],
            'toggle'            => [
                'label'           => &$GLOBALS['TL_LANG']['tl_submission']['toggle'],
                'icon'            => 'visible.gif',
                'attributes'      => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'button_callback' => ['HeimrichHannot\Submissions\Backend\SubmissionBackend', 'toggleIcon'],
            ],
            'send_confirmation' => [
                'label'           => &$GLOBALS['TL_LANG']['tl_submission']['send_confirmation'],
                'icon'            => 'system/modules/submissions/assets/img/icon_send_confirmation.png',
                'href'            => 'key=send_confirmation',
                'attributes'      => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['sendConfirmationConfirm']
                                     . '\'))return false;Backend.getScrollOffset()"',
                'button_callback' => ['HeimrichHannot\Submissions\Backend\SubmissionBackend', 'sendConfirmation'],
            ],
            'show'              => [
                'label' => &$GLOBALS['TL_LANG']['tl_submission']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
        ],
    ],
    'palettes' => [
        'default' => '{general_legend},authorType,author;' . '{submission_legend},gender,academicTitle,additionalTitle,firstname,lastname,company,dateOfBirth,street,street2,'
                     . 'postal,city,country,email,phone,fax,subject,notes,agreement,privacy,captcha,attachments;{publish_legend},published;',
    ],
    'fields'   => [
        'id'              => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ],
        'pid'             => [
            'foreignKey' => 'tl_submission_archive.title',
            'sql'        => "int(10) unsigned NOT NULL default '0'",
            'relation'   => ['type' => 'belongsTo', 'load' => 'eager'],
        ],
        'tstamp'          => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'dateAdded'       => [
            'label'   => &$GLOBALS['TL_LANG']['MSC']['dateAdded'],
            'sorting' => true,
            'flag'    => 6,
            'eval'    => ['rgxp' => 'datim', 'doNotCopy' => true],
            'sql'     => "int(10) unsigned NOT NULL default '0'",
        ],
        'type'            => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['type'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'select',
            'reference' => &$GLOBALS['TL_LANG']['tl_submission']['reference']['type'],
            'eval'      => ['includeBlankOption' => true, 'mandatory' => true, 'tl_class' => 'w50'],
            'sql'       => "varchar(64) NOT NULL default ''"
        ],
        'gender'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['gender'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => ['male', 'female'],
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'eval'      => ['mandatory' => true, 'tl_class' => 'w50 clr', 'substituteField' => true, 'includeBlankOption' => true],
            'sql'       => "varchar(10) NOT NULL default ''",
        ],
        'academicTitle'   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['academicTitle'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => ['Dr.', 'Prof.'],
            'eval'      => [
                'maxlength'          => 20,
                'includeBlankOption' => true,
                'tl_class'           => 'w50',
                'substituteField'    => true
            ],
            'sql'       => "varchar(20) NOT NULL default ''",
        ],
        'additionalTitle' => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['additionalTitle'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 128, 'tl_class' => 'w50', 'substituteField' => true],
            'sql'       => "varchar(128) NOT NULL default ''"
        ],
        'firstname'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['firstname'],
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => 1,
            'inputType' => 'text',
            'eval'      => [
                'mandatory'       => true,
                'maxlength'       => 64,
                'tl_class'        => 'w50',
                'substituteField' => true
            ],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'lastname'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['lastname'],
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => 1,
            'inputType' => 'text',
            'eval'      => [
                'mandatory'       => true,
                'maxlength'       => 64,
                'tl_class'        => 'w50',
                'substituteField' => true
            ],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'company'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['company'],
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => 1,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 128, 'tl_class' => 'w50', 'substituteField' => true],
            'sql'       => "varchar(128) NOT NULL default ''",
        ],
        'dateOfBirth'     => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['dateOfBirth'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => ['datepicker' => true, 'rgxp' => 'date', 'tl_class' => 'w50 wizard', 'substituteField' => true],
            'sql'       => "varchar(10) NOT NULL default ''",
        ],
        'street'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['street'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'tl_class' => 'w50', 'substituteField' => true],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'street2'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['street2'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'tl_class' => 'w50', 'substituteField' => true],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'postal'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['postal'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 5, 'tl_class' => 'w50', 'substituteField' => true],
            'sql'       => "varchar(5) NOT NULL default ''",
        ],
        'city'            => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['city'],
            'exclude'   => true,
            'filter'    => true,
            'search'    => true,
            'sorting'   => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 32, 'tl_class' => 'w50', 'substituteField' => true],
            'sql'       => "varchar(32) NOT NULL default ''",
        ],
        'country'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['country'],
            'exclude'   => true,
            'filter'    => true,
            'sorting'   => true,
            'inputType' => 'select',
            'options'   => \System::getCountries(),
            'eval'      => [
                'includeBlankOption'        => true,
                'chosen'                    => true,
                'autoCompletionHiddenField' => true,
                'tl_class'                  => 'w50',
                'substituteField'           => true
            ],
            'sql'       => "varchar(2) NOT NULL default ''",
        ],
        'email'           => [
            'label'         => &$GLOBALS['TL_LANG']['tl_submission']['email'],
            'exclude'       => true,
            'search'        => true,
            'inputType'     => 'text',
            'save_callback' => [['HeimrichHannot\Haste\Dca\General', 'lowerCase']],
            'eval'          => [
                'mandatory'                 => true,
                'maxlength'                 => 64,
                'autoCompletionHiddenField' => true,
                'rgxp'                      => 'email',
                'decodeEntities'            => true,
                'tl_class'                  => 'w50',
                'substituteField'           => true
            ],
            'sql'           => "varchar(64) NOT NULL default ''",
        ],
        'phone'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['phone'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => [
                'maxlength'       => 32,
                'rgxp'            => 'phone',
                'decodeEntities'  => true,
                'tl_class'        => 'w50',
                'substituteField' => true
            ],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'fax'             => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['fax'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => [
                'maxlength'       => 32,
                'rgxp'            => 'phone',
                'decodeEntities'  => true,
                'tl_class'        => 'w50',
                'substituteField' => true
            ],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'subject'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['subject'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 128, 'tl_class' => 'w50'],
            'sql'       => "varchar(128) NOT NULL default ''"
        ],
        'notes'           => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['notes'],
            'exclude'   => true,
            'inputType' => 'textarea',
            'eval'      => ['tl_class' => 'long clr'],
            'sql'       => "text NULL",
        ],
        'agreement'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['agreement'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => ['mandatory' => true, 'tl_class' => 'w50', 'doNotCopy' => true],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'privacy'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['privacy'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => ['mandatory' => true, 'tl_class' => 'w50', 'doNotCopy' => true],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        'published'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['published'],
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'checkbox',
            'eval'      => ['tl_class' => 'w50', 'doNotCopy' => true],
            'sql'       => "char(1) NOT NULL default ''",
        ],
        // misc
        'captcha'         => [
            'label'     => $GLOBALS['TL_LANG']['MSC']['securityQuestion'],
            'inputType' => 'captcha',
            'eval'      => [
                'mandatory' => true,
                'required'  => true,
                'tableless' => true,
            ],
        ],
        'formHybridBlob'  => [
            'label' => &$GLOBALS['TL_LANG']['tl_submission']['formHybridBlob'],
            'sql'   => "blob NULL",
        ],
        'billingGender'   => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['gender'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => ['male', 'female'],
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'eval'      => ['mandatory' => true, 'tl_class' => 'w50 clr', 'substituteField' => true],
            'sql'       => "varchar(10) NOT NULL default ''",
        ],
        'billingFirstname'       => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['firstname'],
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => 1,
            'inputType' => 'text',
            'eval'      => [
                'mandatory'       => true,
                'maxlength'       => 64,
                'tl_class'        => 'w50',
                'substituteField' => true
            ],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'billingLastname'        => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['lastname'],
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => 1,
            'inputType' => 'text',
            'eval'      => [
                'mandatory'       => true,
                'maxlength'       => 64,
                'tl_class'        => 'w50',
                'substituteField' => true
            ],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'billingCompany'         => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['company'],
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'flag'      => 1,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 128, 'tl_class' => 'w50', 'substituteField' => true],
            'sql'       => "varchar(128) NOT NULL default ''",
        ],
        'billingStreet'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['street'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 64, 'tl_class' => 'w50', 'substituteField' => true],
            'sql'       => "varchar(64) NOT NULL default ''",
        ],
        'billingPostal'          => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['postal'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 5, 'tl_class' => 'w50', 'substituteField' => true],
            'sql'       => "varchar(5) NOT NULL default ''",
        ],
        'billingCity'            => [
            'label'     => &$GLOBALS['TL_LANG']['tl_submission']['city'],
            'exclude'   => true,
            'filter'    => true,
            'search'    => true,
            'sorting'   => true,
            'inputType' => 'text',
            'eval'      => ['maxlength' => 32, 'tl_class' => 'w50', 'substituteField' => true],
            'sql'       => "varchar(32) NOT NULL default ''",
        ],
    ],
];

// add attachment field
if (in_array('multifileupload', \ModuleLoader::getActive()))
{
    $arrDca['fields']['attachments'] = [
        'label'     => &$GLOBALS['TL_LANG']['tl_submission']['attachments'],
        'exclude'   => true,
        'inputType' => 'multifileupload',
        'eval'      => [
            'explanation'    => &$GLOBALS['TL_LANG']['tl_submission']['attachmentsExplanation'],
            'tl_class'       => 'clr',
            'filesOnly'      => true,
            'maxFiles'       => 5,
            'fieldType'      => 'checkbox',
            'extensions'     => \Config::get('uploadTypes'),
            'maxUploadSize'  => '10MiB',
            'uploadFolder'   => \HeimrichHannot\Submissions\Submissions::getDefaultAttachmentSRC(),
            'addRemoveLinks' => true,
            'multiple'       => true,
        ],
        'sql'       => "blob NULL",
    ];
}

\HeimrichHannot\Haste\Dca\General::addAuthorFieldAndCallback('tl_submission');

if (in_array('exporter', \ModuleLoader::getActive()))
{
    $arrDca['list']['global_operations']['export_csv'] = \HeimrichHannot\Exporter\ModuleExporter::getGlobalOperation(
        'export_csv',
        $GLOBALS['TL_LANG']['MSC']['export_csv'],
        'system/modules/exporter/assets/img/icon_export.png'
    );

    $arrDca['list']['global_operations']['export_xls'] = \HeimrichHannot\Exporter\ModuleExporter::getGlobalOperation(
        'export_xls',
        $GLOBALS['TL_LANG']['MSC']['export_xls'],
        'system/modules/exporter/assets/img/icon_export.png'
    );
}
