<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2016 Heimrich & Hannot GmbH
 *
 * @author  Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\Submissions\Util;


use Contao\StringUtil;
use Contao\Validator;

class Tokens
{
    public static function replace($strBuffer, \HeimrichHannot\Submissions\SubmissionModel $objSubmission)
    {
        $tokens = preg_split('/\[(([^\[\]]*)*)\]/', $strBuffer, -1, PREG_SPLIT_DELIM_CAPTURE);

        $strBuffer = '';

        for ($_rit = 0, $_cnt = count($tokens); $_rit < $_cnt; $_rit += 3) {
            $strBuffer .= $tokens[$_rit];
            $strToken  = $tokens[$_rit + 1];

            // Skip empty tokens
            if ($tokens == '') {
                continue;
            }

            // Run the replacement again if there are more tags (see #4402)
            if (strpos($strToken, '[') !== false) {
                $strToken = static::replace($strToken, $objSubmission);
            }

            $arrParams = explode('::', $strToken);

            $strField = $arrParams[0];
            $varValue = $objSubmission->{$strField};

            if ($arrParams[1]) {
                $varValue = static::transform($varValue, $strField, array_slice($arrParams, 1, count($arrParams)), $objSubmission);
            }

            $strBuffer .= $varValue;
        }

        return \Controller::replaceInsertTags($strBuffer);
    }


    public static function transform($varValue, $strField, $arrParams, \HeimrichHannot\Submissions\SubmissionModel $objSubmission)
    {
        switch ($arrParams[0]) {
            case 'date':
                $varValue = \Date::parse($arrParams[1], $varValue);
                break;
        }

        return $varValue;
    }

    /**
     * Convert or remove invalid tokens for notification center
     *
     * @param array $tokens
     * @return array
     */
    public static function cleanInvalidTokens(array $tokens): array
    {
        if (false !== json_encode($tokens)) {
            return $tokens;
        }

        foreach ($tokens as $k => $v) {
            if (false !== json_encode($v)) {
                continue;
            }

            if (Validator::isBinaryUuid($v)) {
                $tokens[$k] = $v = StringUtil::binToUuid($v);
            }

            if (false === json_encode($v)) {
                unset($tokens[$k]);
            }
        }

        return $tokens;
    }
}