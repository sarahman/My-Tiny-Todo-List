<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class MY_Config extends CI_Config
{
    public function item($item, $index = '')
    {
        $result = parent::item($item, $index);
        if (isset($result)) {
            return $result;
        }

        return parent::item($item, 'params');
    }
}