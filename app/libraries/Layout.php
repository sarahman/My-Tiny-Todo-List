<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Layout
{
    public $registry;
    public $layout;

    public function Layout($layout = "layouts/main")
    {
        $this->obj = & get_instance();
        $this->setLayout($layout);
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function view($view, $data=null, $return=false)
    {
        $data['content_for_layout'] = $this->obj->load->view($view,$data,true);

        if (empty ($return)) {
            $this->obj->load->view($this->layout,$data, false);

        } else {
            $output = $this->obj->load->view($this->layout,$data, true);
            return $output;
        }
    }
}