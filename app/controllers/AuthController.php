<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

include_once APPPATH . "controllers/BaseController.php";

class AuthController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        die("<h2 align='center' style='padding-top: 200px'>Welcome To CodeIgniter 2.0.2 (RBS-Version)</h2>");
        $this->load->model('user');
    }

    public function index()
    {
        $this->register();
    }

    public function register()
    {
        $this->load->model('validator');
        $this->validator->setupRegisterValidation();

        $this->data['class'] = 'signup';
        $this->data['selectedTab'] = 'register';

        if ($this->redux_auth->logged_in()) {

            $this->session->set_flashdata('message', 'You are already logged in. Please log out to register with a different name.');
            $this->session->set_flashdata('messageType', 'info');

            $this->redirectToHome();
        }

        if ($this->form_validation->run() == false) {

            $this->load->view('auth/login', $this->data);

        } else {

            $this->load->model('client');

            $email = $this->input->post('email');
            $username = $this->input->post('username');
            $password = $this->input->post('password');

            $register = $this->redux_auth->register($username, $password, $email);

            if ($register) {

                $this->client->processRegister($username);

                $this->session->set_flashdata('message', 'You have successfully registered. Please login below.');
                $this->session->set_flashdata('messageType', 'success');

                redirect('auth/login');

            } else {

                $this->session->set_flashdata('message', 'Something went wrong, please try again or contact the helpdesk.');
                $this->session->set_flashdata('messageType', 'error');

                redirect('auth/register');

            }
        }
    }

    public function login()
    {
        $this->load->model('validator');
        $this->validator->setupLoginValidation();

        $this->data['class'] = 'signup';
        $this->data['selectedTab'] = 'login';

        if ($this->redux_auth->logged_in()) {

            $this->session->set_flashdata('message', 'You are already logged in.');
            $this->session->set_flashdata('messageType', 'info');

            $this->redirectToHome();
        }

        if ($this->form_validation->run() == true) {

            $username = $this->input->post('username');
            $password = $this->input->post('password');

            $this->config->set_item('identity', 'username');
            $login = $this->redux_auth->login($username, $password);

            if ($login) {
                $this->redirectToHome();
            } else {
                $this->data['errorLogin'] = true;
                $this->data['login_error'] = 'The username/password was incorrect. Please try again.';
            }
        }

        $this->load->view('auth/login', $this->data);
    }

    public function activate($code = '')
    {
        if ($code) {
           $this->session->set_flashdata('message', 'Invalid Activation Code');
           $this->session->set_flashdata('messageType', 'error');
           redirect('auth/register');
        }

        $activate = $this->redux_auth->activate($code);

        if ($activate){
            $this->session->set_flashdata('message', 'Your Account is now activated, please login');
            $this->session->set_flashdata('messageType', 'success');
            redirect('auth/login');
        } else {
            $this->session->set_flashdata('message', 'Your account is already activated or doesn\'t need activating');
            $this->session->set_flashdata('messageType', 'error');
            redirect('auth/login');
        }
    }

    public function changePassword()
    {
        $this->data['current'] = 'edit-profile';
        $this->load->model('validator');
        $this->validator->setupChangePasswordValidation();

        if ($this->form_validation->run() == true) {

            $oldPassword = $this->input->post('old_password');
            $newPassword = $this->input->post('new_password');

            $identity = $this->session->userdata($this->config->item('identity'));
            $change = $this->redux_auth->change_password($identity, $oldPassword, $newPassword);

            if ($change) {
                $this->session->set_flashdata('message', 'Password Changed Succesfully. You can login with your new password.');
                $this->session->set_flashdata('messageType', 'success');
                $this->redux_auth->logout();
                redirect('auth/login');
            } else {
                $this->session->set_flashdata('message', 'Sorry Password Change Failed');
                $this->session->set_flashdata('messageType', 'error');
            }
        }

        $this->layout->view('auth/change_password', $this->data);
    }

    public function forgetPassword()
    {
        if ($this->redux_auth->logged_in()) {

            $this->session->set_flashdata('message', 'You are already logged in.');
            $this->session->set_flashdata('messageType', 'info');

            $this->redirectToHome();
        }

        $this->load->model('validator');
        $this->validator->setupForgetPasswordValidation();

        $this->data['class'] = 'signup';

        if ($this->form_validation->run() == true) {

            $email = $this->input->post('email');
            $forgotten = $this->redux_auth->forgotten_password($email);

            if ($forgotten) {
                $this->session->set_flashdata('message', 'A verification email has been sent, please check your inbox.');
                $this->session->set_flashdata('messageType', 'success');
                redirect('auth/forgetPassword');
            } else {
                $this->session->set_flashdata('message', 'Sorry, this email address does not belong in our system.');
                $this->session->set_flashdata('messageType', 'error');
                redirect('auth/forgetPassword');
            }
        }

        $this->load->view('auth/forgotten_password', $this->data);
    }

    public function recoverPassword($code = '')
    {
        if (!$code) {
           $this->session->set_flashdata('message', 'Invalid Verification Code');
           $this->session->set_flashdata('messageType', 'error');
           redirect('auth/register');
        }

        $forgot = $this->redux_auth->forgotten_password_complete($code);

        if ($forgot) {
            $this->session->set_flashdata('message', 'Form now your password is <strong>'.$this->redux_auth_model->new_password.'</strong>. You can change it, if you feel like.');
            $this->session->set_flashdata('messageType', 'success');
            redirect('auth/changePassword');
        } else {
            $this->session->set_flashdata('message', 'The code you entered was incorrect. Please check your email again.');
            $this->session->set_flashdata('messageType', 'error');
            redirect('auth/forgetPassword');
        }
    }

    public function profile()
    {
        $this->data['current'] = 'edit-profile';
        if ($this->redux_auth->logged_in()) {
            $this->data['profile'] = $this->redux_auth->profile();
            $this->layout->view('auth/profile', $this->data);
        } else {
            redirect('auth/login');
        }
    }

    public function editProfile()
    {
        $this->data['current'] = 'edit-profile';
        if ($this->redux_auth->logged_in() === false) {
            $this->session->set_flashdata('message', 'Please log in to access your backoffice.');
            $this->session->set_flashdata('messageType', 'error');
            redirect('auth/login');
        }

        $this->load->model('validator');
        $this->load->model('redux_auth_model');
        $this->validator->setupEditProfileValidation();
        $this->data['profile'] = $this->redux_auth->profile();

        if ($this->form_validation->run() == false) {

            $this->layout->view('auth/edit_profile', $this->data);

        } else {

            $data['full_name'] = $this->input->post('full_name');
            $data['phone'] = $this->input->post('phone');
            $update = $this->redux_auth_model->updateMeta($data, $this->data['user']->username);

            if ($update) {

                $this->session->set_flashdata('message', 'You have successfully updated profile information.');
                $this->session->set_flashdata('messageType', 'success');

                redirect('client');

            } else {

                $this->session->set_flashdata('message', 'Something went wrong, please try again or contact the helpdesk.');
                $this->session->set_flashdata('messageType', 'error');

                redirect('client');
            }
        }
    }

    public function logout()
    {
        $this->redux_auth->logout();
        $this->session->set_flashdata('message', 'You have successfully been logged out');
        $this->session->set_flashdata('messageType', 'success');
        redirect('auth/login');
    }

    public function username_check($username)
    {
        $check = $this->redux_auth_model->username_check($username);

        if ($check) {
            $this->form_validation->set_message('username_check', 'The username "' . $username . '" already exists.');
            return false;
        } else {
            return true;
        }
    }

    public function email_check($email)
    {
        $check = $this->redux_auth_model->email_check($email);

        if ($check) {
            $this->form_validation->set_message('email_check', 'The email "' . $email . '" already exists.');
            return false;
        } else {
            return true;
        }
    }

    private function redirectToHome()
    {
        if (!isset($this->data['user'])) {
            $this->data['user'] = $this->redux_auth->profile();
        }

        if ($this->data['user']->group == 'Client') {
            redirect('jobs');
        } else {
            redirect('jobs');
        }
    }
}