<?php

namespace HeimrichHannot\Submissions\Helper;

use Contao\StringUtil;

class StringHelper
{
    /**
     * Text filter options
     */
    const NO_TAGS = 1;
    const NO_BREAKS = 2;
    const NO_EMAILS = 4;
    const NO_INSERTTAGS = 8;
    const NO_ENTITIES = 16;

    /**
     * Convert the given array or string to plain text using given options
     *
     * @param mixed $varValue
     * @param int   $options
     *
     * @return mixed
     */
    public static function convertToText($varValue, $options)
    {
        if (is_array($varValue)) {
            foreach ($varValue as $k => $v) {
                $varValue[$k] = static::convertToText($v, $options);
            }

            return $varValue;
        }

        if ($options & static::NO_ENTITIES) {
            $varValue = StringUtil::restoreBasicEntities($varValue);
            $varValue = html_entity_decode($varValue);

            // Convert non-breaking to regular white space
            $varValue = str_replace("\xC2\xA0", ' ', $varValue);

            // Remove invisible control characters and unused code points
            $varValue = preg_replace('/[\pC]/u', '', $varValue);
        }

        // Replace friendly email before stripping tags
        if (!($options & static::NO_EMAILS)) {
            $arrEmails = array();
            preg_match_all('{<.+@.+\.[A-Za-z]+>}', $varValue, $arrEmails);

            if (!empty($arrEmails[0])) {
                foreach ($arrEmails[0] as $k => $v) {
                    $varValue = str_replace($v, '%email' . $k . '%', $varValue);
                }
            }
        }

        // Remove HTML tags but keep line breaks for <br> and <p>
        if ($options & static::NO_TAGS) {
            $varValue = strip_tags(preg_replace('{(?!^)<(br|p|/p).*?/?>\n?(?!$)}is', "\n", $varValue));
        }

        if ($options & static::NO_INSERTTAGS) {
            $varValue = StringUtil::stripInsertTags($varValue);
        }

        // Remove line breaks (e.g. for subject)
        if ($options & static::NO_BREAKS) {
            $varValue = str_replace(array("\r", "\n"), '', $varValue);
        }

        // Restore friendly email after stripping tags
        if (!($options & static::NO_EMAILS) && !empty($arrEmails[0])) {
            foreach ($arrEmails[0] as $k => $v) {
                $varValue = str_replace('%email' . $k . '%', $v, $varValue);
            }
        }

        return $varValue;
    }
}