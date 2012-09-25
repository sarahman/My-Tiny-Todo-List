<?php

/**
 * Home Controller
 */

include_once APPPATH . 'Controllers/BaseController.php';
class HomeController extends BaseController
{
    public function index()
    {
        $this->data['is_readonly'] = $this->is_readonly();
        $this->load->view('home/index', $this->data);
    }

    public function getJSData()
    {
        header('Content-type: text/javascript; charset=utf-8');
        echo "mytinytodo.lang.init(". $this->lang->makeJS() .");";
    }

    public function getTagCloud()
    {
        $this->load->model('tinylist');
        $data = $this->uri->uri_to_assoc();
        $this->checkReadAccess((int)$data['list'], $this->tinylist);

        $this->load->model('tag');
        $tagCloud = $this->tag->getTagCloud($data);
        $this->jsonExit($tagCloud);
    }
}