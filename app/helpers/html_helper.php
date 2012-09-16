<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * escapeTags
 *
 * Escape tags in the given string.
 *
 * @access    public
 * @param     string
 * @return    string
 */
if (!function_exists('escapeTags')) {
    function escapeTags($s)
    {
        $c1 = chr(1);
        $c2 = chr(2);
        $s = preg_replace("~<b>([\s\S]*?)</b>~i", "${c1}b${c2}\$1${c1}/b${c2}", $s);
        $s = preg_replace("~<i>([\s\S]*?)</i>~i", "${c1}i${c2}\$1${c1}/i${c2}", $s);
        $s = preg_replace("~<u>([\s\S]*?)</u>~i", "${c1}u${c2}\$1${c1}/u${c2}", $s);
        $s = preg_replace("~<s>([\s\S]*?)</s>~i", "${c1}s${c2}\$1${c1}/s${c2}", $s);
        $s = str_replace(array($c1, $c2), array('<','>'), htmlspecialchars($s));
        return $s;
    }
}

/* End of file html_helper.php */
/* Location: ./app/helpers/html_helper.php */