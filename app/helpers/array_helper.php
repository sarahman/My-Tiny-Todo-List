<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * htmlarray
 *
 * Prepare array with the help of htmlarray_ref function
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	mixed	the prepared array
 */
if ( ! function_exists('htmlarray'))
{
    function htmlarray($a, $exclude=null)
    {
        htmlarray_ref($a, $exclude);
        return $a;
    }
}

// ------------------------------------------------------------------------

/**
 * htmlarray_ref
 *
 * Convert special characters to HTML entities in the array
 * with excluding the desired array keys.
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	mixed	the desired array
 */
if ( ! function_exists('htmlarray_ref'))
{
	function htmlarray_ref(&$a, $exclude=null)
    {
        if(!$a) return;
        if(!is_array($a)) {
            $a = htmlspecialchars($a);
            return;
        }
        reset($a);
        if($exclude && !is_array($exclude)) $exclude = array($exclude);
        foreach($a as $k=>$v)
        {
            if(is_array($v)) $a[$k] = htmlarray($v, $exclude);
            elseif(!$exclude) $a[$k] = htmlspecialchars($v);
            elseif(!in_array($k, $exclude)) $a[$k] = htmlspecialchars($v);
        }
        return;
    }
}

/* End of file array_helper.php */
/* Location: ./app/helpers/array_helper.php */