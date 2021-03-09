<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @author  Thomas Körner <t.koerner@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Submissions\EventListener;


use Contao\Config;
use Contao\ModuleLoader;
use HeimrichHannot\Submissions\Backend\SubmissionArchiveBackend;
use HeimrichHannot\Submissions\Submissions;

class LoadDataContainerListener
{
    /**
     * @Hook("loadDataContainer")
     * @param string $table
     */
    public function onLoadDataContainer($table): void
    {
        switch ($table) {
            case 'tl_submission_archive':
                $this->addOptionalSubmissionArchiveFields();
                break;
        }
    }

    public function addOptionalSubmissionArchiveFields()
    {
        // add attachment related config fields
        $activeBundles = ModuleLoader::getActive();
        $arrDca = &$GLOBALS['TL_DCA']['tl_submission_archive'];
        if (in_array('multifileupload', $activeBundles) || in_array('HeimrichHannotContaoMultiFileUploadBundle', $activeBundles)) {
            /**
             * Palettes
             */
            $arrDca['palettes']['__selector__'][] = 'addAttachmentConfig';
            $arrDca['palettes']['default']        =
                str_replace('{notification_legend}', '{attachment_legend},addAttachmentConfig;{notification_legend}', $arrDca['palettes']['default']);


            /**
             * Subpalettes
             */
            $arrDca['subpalettes']['addAttachmentConfig'] =
                'attachmentUploadFolder,attachmentMaxFiles,attachmentMaxUploadSize,attachmentExtensions,attachmentFieldType,attachmentSubFolderPattern';

            /**
             * Fields
             */
            $arrFields = [
                'addAttachmentConfig'        => [
                    'label'     => &$GLOBALS['TL_LANG']['tl_submission_archive']['addAttachmentConfig'],
                    'exclude'   => true,
                    'inputType' => 'checkbox',
                    'eval'      => ['submitOnChange' => true],
                    'sql'       => "char(1) NOT NULL default ''",
                ],
                'attachmentUploadFolder'     => [
                    'label'         => &$GLOBALS['TL_LANG']['tl_submission_archive']['attachmentUploadFolder'],
                    'exclude'       => true,
                    'inputType'     => 'fileTree',
                    'save_callback' => [
                        [SubmissionArchiveBackend::class, 'setAttachmentUploadFolder'],
                    ],
                    'eval'          => ['filesOnly' => false, 'fieldType' => 'radio', 'mandatory' => true],
                    'sql'           => "binary(16) NULL",
                ],
                'attachmentMaxFiles'         => [
                    'label'     => &$GLOBALS['TL_LANG']['tl_submission_archive']['attachmentMaxFiles'],
                    'exclude'   => true,
                    'default'   => 5,
                    'inputType' => 'text',
                    'eval'      => ['rgxp' => 'digit', 'mandatory' => true, 'tl_class' => 'w50'],
                    'sql'       => "int(3) unsigned NOT NULL default '0'",
                ],
                'attachmentMaxUploadSize'    => [
                    'label'     => &$GLOBALS['TL_LANG']['tl_submission_archive']['attachmentMaxUploadSize'],
                    'exclude'   => true,
                    'default'   => 10,
                    'inputType' => 'text',
                    'eval'      => ['rgxp' => 'digit', 'mandatory' => true, 'tl_class' => 'w50'],
                    'sql'       => "int(10) unsigned NOT NULL default '0'",
                ],
                'attachmentExtensions'       => [
                    'label'     => &$GLOBALS['TL_LANG']['tl_submission_archive']['attachmentExtensions'],
                    'exclude'   => true,
                    'default'   => Config::get('uploadTypes'),
                    'inputType' => 'text',
                    'eval'      => ['mandatory' => true, 'tl_class' => 'w50'],
                    'sql'       => "text NULL",
                ],
                'attachmentFieldType'        => [
                    'label'     => &$GLOBALS['TL_LANG']['tl_submission_archive']['attachmentFieldType'],
                    'exclude'   => true,
                    'default'   => 'checkbox',
                    'options'   => ['checkbox', 'radio'],
                    'reference' => &$GLOBALS['TL_LANG']['tl_submission_archive']['reference']['attachmentFieldType'],
                    'inputType' => 'radio',
                    'eval'      => ['mandatory' => true, 'tl_class' => 'w50'],
                    'sql'       => "varchar(8) NOT NULL default ''",
                ],
                'attachmentSubFolderPattern' => [
                    'label'     => &$GLOBALS['TL_LANG']['tl_submission_archive']['attachmentSubFolderPattern'],
                    'exclude'   => true,
                    'inputType' => 'text',
                    'eval'      => ['maxlength' => 255, 'tl_class' => 'w50', 'preserveTags' => true],
                    'sql'       => "varchar(128) COLLATE utf8_bin NOT NULL default '"
                        . Submissions::getDefaultAttachmentSubFolderPattern() . "'",
                ],
            ];

            $arrDca['fields'] = array_merge($arrDca['fields'], $arrFields);
        }
    }
}
