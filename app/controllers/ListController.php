<?php

/**
 * List Controller
 */

include_once APPPATH . 'Controllers/BaseController.php';
class ListController extends BaseController
{
    public function loadLists()
    {
        $this->load->model('tinylist');
        $result = $this->tinylist->get(array('isLogged' => $this->session->userdata('username')));
        $this->jsonExit($result);
    }

    public function add()
    {
        $this->load->model('tinylist');
        $this->checkWriteAccess(null, $this->tinylist);
        $listId = $this->tinylist->add($_POST);
        $result = $this->tinylist->get(array('id' => $listId));
        $this->jsonExit($result);
    }

    public function rename()
    {
        $this->load->model('tinylist');
        $this->checkWriteAccess(null, $this->tinylist);
        $listId = $this->tinylist->modify($_POST);
        $result = $this->tinylist->get(array('id' => $listId));
        $this->jsonExit($result);
    }

    public function delete()
    {
        $this->load->model('tinylist');
        $this->checkWriteAccess(null, $this->tinylist);
        $this->tinylist->delete(array('id' => $this->input->post('list')));
        $result = $this->tinylist->get();
        $this->jsonExit($result);
    }
}