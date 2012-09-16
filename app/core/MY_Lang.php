<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class MY_Lang extends CI_Lang
{
    protected $rtl = 0;
    public function makeJS()
    {
        $a = array();
        foreach($this->language['js'] AS $k=>$v)
        {
            if (is_array($v)) {
                $a[] = "{$k}: {$v[0]}";
            } else {
                $a[] = "{$k}: \"". str_replace('"','\\"',$v). "\"";
            }
        }

        $t = array();
        foreach($this->get('days_min') as $v) { $t[] = '"'.str_replace('"','\\"',$v).'"'; }
        $a[] = "daysMin: [". implode(',', $t). "]";
        $t = array();
        foreach($this->get('days_long') as $v) { $t[] = '"'.str_replace('"','\\"',$v).'"'; }
        $a[] = "daysLong: [". implode(',', $t). "]";
        $t = array();
        foreach($this->get('months_long') as $v) { $t[] = '"'.str_replace('"','\\"',$v).'"'; }
        $a[] = "monthsLong: [". implode(',', $t). "]";
        $a[] = $this->_2js('tags');
        $a[] = $this->_2js('tasks');
        $a[] = $this->_2js('f_past');
        $a[] = $this->_2js('f_today');
        $a[] = $this->_2js('f_soon');
        return "{\n". implode(",\n", $a). "\n}";
    }

    public function _2js($v)
    {
        return "$v: \"". str_replace('"','\\"',$this->get($v)). "\"";
    }

    public function get($key)
    {
        if (!empty($this->language['inc'][$key])) return $this->language['inc'][$key];
        return $key;
    }

    public function rtl()
    {
        return $this->rtl ? 1 : 0;
    }

    // --------------------------------------------------------------------

    /**
     * Fetch a single line of text from the language array
     *
     * @access    public
     * @param     string    $line    the language line
     * @return    string
     */
    public function line($line = '')
    {
        if (empty($line)) {
            $line = false;
        } elseif (!empty($this->language[$line])) {
            $line = $this->language[$line];
        } elseif (!empty($this->language['inc'][$line])) {
            $line = $this->language['inc'][$line];
        } else {
            $line = false;
        }

        // Because killer robots like unicorns!
        if ($line === FALSE) {
            log_message('error', 'Could not find the language line "'.$line.'"');
        }

        return $line;
    }
}