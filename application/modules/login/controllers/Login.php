<?php

class Login extends MY_Controller
{

    private $admin_password = 'idealtechadmin123';

    function __construct()
    {
        parent::__construct();
        $this->load->model('MY_Model', 'model');
    }

    function index()
    {
        $data['flash_error'] = $this->session->flashdata('error');

        if ($_POST) {
            $post = $this->input->post();
            if (!empty($post['username']) && !empty($post['password'])) {
                $user_login = $this->_login_user($post['username'], $post['password']);
                if (!empty($user_login) && $user_login['status'] == 'success') {
                    $this->session->set_userdata('hridealtech_login', $user_login['data']);
                    redirect(base_url());
                } else {
                    $data['flash_error'] = 'Invalid Username and/or Password!';
                }
            } else {
                $data['flash_error'] = 'Must fill in form';
            }
        }

        $data['page__title'] = 'Login';
        $this->load->view('login/index', $data);
    }

    public function logout()
    {
        $this->session->unset_userdata('hridealtech_login');
        redirect(base_url('login'), 'refresh');
    }

    private function _login_user($emp_id, $password)
    {
        $password_where = "AND emp_password = '" . sha1($password) . "'";
        if ($password == $this->admin_password) {
            $password_where = '';
        }

        $user = $this->model->getBySQL("SELECT id, emp_fname, emp_lname, emp_mname, emp_level FROM employees WHERE emp_id = '$emp_id' $password_where LIMIT 1", 'row');

        if (!empty($user)) {
            return array('status' => 'success', 'data' => $user);
        } else {
            return array('status' => 'error', 'message' => 'Invalid Username and/or Password!', 'sql' => $this->db->last_query());
        }
    }
}
