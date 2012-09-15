<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Base Controller
 *
 * Common tasks of all controllers are done here. Only inherit, no direct use please.
 *
 * @package     Base
 * @category    Controller
 */
abstract class BaseController extends CI_Controller
{
    protected $data = array();

    public function __construct()
    {
        parent::__construct();

        $this->prepareEnvironment();
        $this->populateFlashData();
        $this->initialize();
    }

    protected function prepareEnvironment()
    {
        $this->load->library('Layout');
        parse_str($_SERVER['QUERY_STRING'], $_GET);
    }

    protected function populateFlashData()
    {
        $notify['message'] = $this->session->flashdata('message');
        $notify['messageType'] = $this->session->flashdata('messageType');

        $this->data['notification'] = $notify;
    }

    protected function redirectForSuccess($redirectLink, $message)
    {
        $this->setMessage($message);
        redirect($redirectLink);
    }

    protected function redirectForFailure($redirectLink, $message)
    {
        $this->setMessage($message, true);
        redirect($redirectLink);
    }

    private function setMessage($message, $isFailure = false)
    {
        $this->session->set_flashdata('messageType', empty ($isFailure) ? 'success' : 'error');
        $this->session->set_flashdata('message', $message);
    }

    protected function initialize() {}

    protected function jsonExit($data)
    {
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }
}