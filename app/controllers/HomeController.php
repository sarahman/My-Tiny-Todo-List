<?php

/**
 * Home Controller
 */

include_once APPPATH . 'Controllers/BaseController.php';
class HomeController extends BaseController
{
    public function index()
    {
        $this->load->view('home/index', $this->data);
    }

    public function loadLists()
    {
        $this->load->model('todolist');
        $result = $this->todolist->get(array('isLogged' => $this->session->userdata('username')));
        $this->jsonExit($result);
    }

    public function loadTasks()
    {

    }

    public function getJSData()
    {
        header('Content-type: text/javascript; charset=utf-8');
        echo $this->lang->makeJS();
    }
}
