<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class MY_Email extends CI_Email
{
    public function __construct()
    {
        parent::__construct();

        $CI = & get_instance();
        $config = $CI->config->item('email');
        $this->initialize($config);
    }
}