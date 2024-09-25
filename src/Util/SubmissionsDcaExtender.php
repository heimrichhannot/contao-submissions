<?php

namespace HeimrichHannot\Submissions\Util;

use HeimrichHannot\Haste\Dca\General;
use HeimrichHannot\Submissions\Submissions;

class SubmissionsDcaExtender
{
    public static function addOptionalSubmissionArchiveFields()
    {
        // this ads attachment related config fields if multifileupload/HeimrichHannotContaoMultiFileUploadBundle is active
        // maybe we don't need this in the future
        // todo: move from LoadDataContainerListener
    }

    public static function addOptInTokenIdField(string $table): void
    {
        if (!isset($GLOBALS['TL_DCA'][$table])) {
            throw new \InvalidArgumentException(sprintf('Table "%s" does not exist in $GLOBALS[\'TL_DCA\']', $table));
        }

        $fields = &$GLOBALS['TL_DCA'][$table]['fields'];

        $fields['huhSubOptInTokenId'] = [
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'text',
            'eval'      => ['tl_class' => 'w50 clr'],
            'sql'       => "varchar(32) NOT NULL default ''"
        ];

        $fields['huhSubOptInCache'] = [
            'exclude'   => true,
            'filter'    => true,
            'inputType' => 'text',
            'eval'      => ['tl_class' => 'w50 clr'],
            'sql'       => "blob NULL"
        ];
    }

    public static function addOptInSupport(string $table): void
    {
        if (!isset($GLOBALS['TL_DCA'][$table])) {
            throw new \InvalidArgumentException(sprintf('Table "%s" does not exist in $GLOBALS[\'TL_DCA\']', $table));
        }

        $dca = &$GLOBALS['TL_DCA'][$table];
        $fields = &$GLOBALS['TL_DCA'][$table]['fields'];

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

        if ($table === 'tl_form')
        {
            $dca['subpalettes']['storeAsSubmission'] = \str_replace(
                'submissionArchive',
                'submissionArchive,huhSubAddOptIn',
                $dca['subpalettes']['storeAsSubmission']
            );
            $dca['palettes']['__selector__'][]       = 'huhSubAddOptIn';
            $dca['subpalettes']['huhSubAddOptIn']    = 'huhSubOptInNotification,huhSubOptInJumpTo,huhSubOptInField,huhSubOptInTokenInvalidJumpTo';
        }
    }
}