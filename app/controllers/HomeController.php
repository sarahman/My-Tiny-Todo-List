<?php

/**
 * Home Controller
 */

include_once APPPATH . 'Controllers/BaseController.php';
class HomeController extends BaseController
{
    protected function initialize()
    {
        if (!$this->session->userdata('username')) {
            redirect('users/login');
        }
    }

    public function index()
    {
        $this->load->model('user');
        $this->data['username'] = $this->session->userdata('name');
        $this->layout->view('home/index', $this->data);
    }
}
