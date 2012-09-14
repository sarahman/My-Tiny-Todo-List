<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class MY_Pagination extends CI_Pagination
{
    protected $values = array();
    protected $CI;

    public function __construct()
    {
        parent::__construct();
        $this->CI = & get_instance();

        $this->values['per_page'] = $this->CI->config->item('rowsPerPage');
        $this->values['first_link'] = '&laquo; Prev';
        $this->values['prev_link'] = '&laquo; Prev';
        $this->values['next_link'] = 'Next &raquo;';
        $this->values['last_link'] = 'Last &raquo;';
        //$this->values['uri_segment'] = 3;
        $this->values['cur_tag_open'] = '&nbsp;&nbsp;<a href="" class="number current">';
        $this->values['cur_tag_close'] = '</a>&nbsp;';
        $this->values['num_tag_open'] = '&nbsp; ';
        $this->values['num_tag_close'] = '';
    }

    public function setOptions($options)
    {
        $this->values['base_url'] = $options['baseUrl'];
        $this->values['total_rows'] = $options['numRows'];
        $this->values['uri_segment'] = empty($options['segmentValue']) ? 3 : $options['segmentValue'];
        $this->initialize($this->values);
    }
}