<?php defined('BASEPATH') || exit('No direct script access allowed');

/**
 * parse due date
 *
 * @access    public
 * @return    integer
 */
if (!function_exists('parse_duedate')) {
    function parse_duedate($s)
    {
        $CI = &get_instance();
        $df2 = $CI->config->item('dateformat2');
        if (max((int)strpos($df2, 'n'), (int)strpos($df2, 'm')) > max((int)strpos($df2, 'd'), (int)strpos($df2, 'j'))) $formatDayFirst = true;
        else $formatDayFirst = false;

        $y = $m = $d = 0;
        if (preg_match("|^(\d+)-(\d+)-(\d+)\b|", $s, $ma)) {
            $y = (int)$ma[1];
            $m = (int)$ma[2];
            $d = (int)$ma[3];
        }
        elseif (preg_match("|^(\d+)\/(\d+)\/(\d+)\b|", $s, $ma))
        {
            if ($formatDayFirst) {
                $d = (int)$ma[1];
                $m = (int)$ma[2];
                $y = (int)$ma[3];
            } else {
                $m = (int)$ma[1];
                $d = (int)$ma[2];
                $y = (int)$ma[3];
            }
        }
        elseif (preg_match("|^(\d+)\.(\d+)\.(\d+)\b|", $s, $ma)) {
            $d = (int)$ma[1];
            $m = (int)$ma[2];
            $y = (int)$ma[3];
        }
        elseif (preg_match("|^(\d+)\.(\d+)\b|", $s, $ma)) {
            $d = (int)$ma[1];
            $m = (int)$ma[2];
            $a = explode(',', date('Y,m,d'));
            if ($m < (int)$a[1] || ($m == (int)$a[1] && $d < (int)$a[2])) $y = (int)$a[0] + 1;
            else $y = (int)$a[0];
        }
        elseif (preg_match("|^(\d+)\/(\d+)\b|", $s, $ma))
        {
            if ($formatDayFirst) {
                $d = (int)$ma[1];
                $m = (int)$ma[2];
            } else {
                $m = (int)$ma[1];
                $d = (int)$ma[2];
            }
            $a = explode(',', date('Y,m,d'));
            if ($m < (int)$a[1] || ($m == (int)$a[1] && $d < (int)$a[2])) $y = (int)$a[0] + 1;
            else $y = (int)$a[0];
        }
        else return null;
        if ($y < 100) $y = 2000 + $y;
        elseif ($y < 1000 || $y > 2099) $y = 2000 + (int)substr((string)$y, -2);
        if ($m > 12) $m = 12;
        $maxdays = daysInMonth($m, $y);
        if ($m < 10) $m = '0' . $m;
        if ($d > $maxdays) $d = $maxdays;
        elseif ($d < 10) $d = '0' . $d;
        return "$y-$m-$d";
    }
}

/**
 * Prepare due date
 *
 * @access    public
 * @return    integer
 */
if (!function_exists('prepare_duedate')) {
    function prepare_duedate($duedate)
    {
        $CI = &get_instance();

        $a = array('class' => '', 'str' => '', 'formatted' => '', 'timestamp' => 0);
        if ($duedate == '') {
            return $a;
        }
        $ad = explode('-', $duedate);
        $at = explode('-', date('Y-m-d'));
        $a['timestamp'] = mktime(0, 0, 0, $ad[1], $ad[2], $ad[0]);
        $diff = mktime(0, 0, 0, $ad[1], $ad[2], $ad[0]) - mktime(0, 0, 0, $at[1], $at[2], $at[0]);

        if ($diff < -604800 && $ad[0] == $at[0]) {
            $a['class'] = 'past';
            $a['str'] = formatDate3($CI->config->item('dateformatshort'), (int)$ad[0], (int)$ad[1], (int)$ad[2], $CI);
        }
        elseif ($diff < -604800) {
            $a['class'] = 'past';
            $a['str'] = formatDate3($CI->config->item('dateformat2'), (int)$ad[0], (int)$ad[1], (int)$ad[2], $CI);
        }
        elseif ($diff < -86400) {
            $a['class'] = 'past';
            $a['str'] = sprintf($CI->lang->line('daysago'), ceil(abs($diff) / 86400));
        }
        elseif ($diff < 0) {
            $a['class'] = 'past';
            $a['str'] = $CI->lang->line('yesterday');
        }
        elseif ($diff < 86400) {
            $a['class'] = 'today';
            $a['str'] = $CI->lang->line('today');
        }
        elseif ($diff < 172800) {
            $a['class'] = 'today';
            $a['str'] = $CI->lang->line('tomorrow');
        }
        elseif ($diff < 691200) {
            $a['class'] = 'soon';
            $a['str'] = sprintf($CI->lang->line('indays'), ceil($diff / 86400));
        }
        elseif ($ad[0] == $at[0]) {
            $a['class'] = 'future';
            $a['str'] = formatDate3($CI->config->item('dateformatshort'), (int)$ad[0], (int)$ad[1], (int)$ad[2], $CI);
        }
        else {
            $a['class'] = 'future';
            $a['str'] = formatDate3($CI->config->item('dateformat2'), (int)$ad[0], (int)$ad[1], (int)$ad[2], $CI);
        }

        $a['formatted'] = formatTime($CI->config->item('dateformat2'), $a['timestamp']);

        return $a;
    }
}

// ------------------------------------------------------------------------

/**
 * timestampToDatetime
 *
 * This function convert timestamp into datetime format.
 *
 * @access    public
 * @param    string
 * @return    string
 */
if (!function_exists('timestampToDatetime')) {
    function timestampToDatetime($timestamp)
    {
        $CI = &get_instance();
        $format = $CI->config->item('dateformat') . ' ' . ($CI->config->item('clock') == 12 ? 'g:i A' : 'H:i');
        return formatTime($format, $timestamp);
    }
}

// ------------------------------------------------------------------------

/**
 * formatDate3
 *
 * Format date into a given format.
 *
 * @access    public
 * @param    string    the chosen format
 * @param    string
 * @param    string
 * @param    string
 * @param    mixed
 * @return    string
 */
if (!function_exists('formatDate3')) {
    function formatDate3($format, $ay, $am, $ad, $CI)
    {
        # F - month long, M - month short
        # m - month 2-digit, n - month 1-digit
        # d - day 2-digit, j - day 1-digit
        $ml = $CI->lang->line('months_long');
        $ms = $CI->lang->line('months_short');
        $Y = $ay;
        $y = $Y < 2010 ? '0' . ($Y - 2000) : $Y - 2000;
        $n = $am;
        $m = $n < 10 ? '0' . $n : $n;
        $F = $ml[$am - 1];
        $M = $ms[$am - 1];
        $j = $ad;
        $d = $j < 10 ? '0' . $j : $j;
        return strtr($format, array('Y' => $Y, 'y' => $y, 'F' => $F, 'M' => $M, 'n' => $n, 'm' => $m, 'd' => $d, 'j' => $j));
    }
}

// ------------------------------------------------------------------------

/**
 * formatTime
 *
 * Format timestamp into a given format.
 *
 * @access    public
 * @param     string    the chosen format
 * @param     integer    Unix timestamp
 * @return    string
 */
if (!function_exists('formatTime')) {
    function formatTime($format, $timestamp = 0)
    {
        $CI = &get_instance();
        if ($timestamp == 0) $timestamp = time();
        $newformat = strtr($format, array('F' => '%1', 'M' => '%2'));
        $adate = explode(',', date('n,' . $newformat, $timestamp), 2);
        $s = $adate[1];
        if ($newformat != $format) {
            $am = (int)$adate[0];
            $ml = $CI->lang->line('months_long');
            $ms = $CI->lang->line('months_short');
            $F = $ml[$am - 1];
            $M = $ms[$am - 1];
            $s = strtr($s, array('%1' => $F, '%2' => $M));
        }
        return $s;
    }
}

// ------------------------------------------------------------------------

/**
 * date2int
 *
 * Format date with zeros in month and day parts.
 *
 * @access    public
 * @param     string
 * @return    integer
 */
if (!function_exists('date2int')) {
    function date2int($d)
    {
        if (!$d) return 33330000;
        $ad = explode('-', $d);
        $s = $ad[0];
        if (strlen($ad[1]) < 2) $s .= "0$ad[1]"; else $s .= $ad[1];
        if (strlen($ad[2]) < 2) $s .= "0$ad[2]"; else $s .= $ad[2];
        return (int)$s;
    }
}

// ------------------------------------------------------------------------

/**
 * daysInMonth
 *
 * Find number of days of a given month.
 *
 * @access    public
 * @param     string
 * @param     integer
 * @return    string
 */
if (!function_exists('daysInMonth')) {
    function daysInMonth($m, $y = 0)
    {
        if ($y == 0) $y = (int)date('Y');
        $a = array(1 => 31, (($y - 2000) % 4 ? 28 : 29), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        if (isset($a[$m])) return $a[$m]; else return 0;
    }
}

/* End of file date_helper.php */
/* Location: ./app/helpers/date_helper.php */