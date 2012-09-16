<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * generateUUID
 *
 * Generate a universal unique ID.
 *
 * @access    public
 * @return    string
 */
if (!function_exists('generateUUID')) {

    /* found in comments on http://www.php.net/manual/en/function.uniqid.php#94959 */
    function generateUUID()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
          mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
          mt_rand(0, 0x0fff) | 0x4000,
          mt_rand(0, 0x3fff) | 0x8000,
          mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}

// ------------------------------------------------------------------------

/**
 * quoteForLike
 *
 * This function adds slashes into a string containing this wildcard(%).
 *
 * @access    public
 * @param     string
 * @param     string
 * @return    string
 */
if (!function_exists('quoteForLike')) {
    function quoteForLike($format, $s)
    {
        $s = str_replace(array('%','_'), array('\%','\_'), addslashes($s));
        return '\''. sprintf($format, $s). '\'';
    }
}

/* End of file db_helper.php */
/* Location: ./app/helpers/db_helper.php */