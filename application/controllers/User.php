<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('role_model');
        $this->load->model('Update_history_model');
        $this->load->library('session');
        $this->load->helper('url');
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    public function index() {
        $data['users'] = $this->user_model->get_all_users();
        $this->load->view('template/header');
        $this->load->view('user/user_list', $data);
        $this->load->view('template/footer', $data);
    }

    public function create() {
        $data['roles'] = $this->role_model->get_all_roles();
        $this->load->view('template/header');
        $this->load->view('user/user_create', $data);
        $this->load->view('template/footer', $data);
    }

    public function store() {
        $username = $this->input->post('username');
        $password = md5($this->input->post('password'));
        $role_id = $this->input->post('role_id');

        $this->user_model->create_user($username, $password, $role_id);
        $this->session->set_flashdata('success', 'User berhasil ditambahkan.');
        redirect('user');
    }

    public function edit($id) {
        $data['user'] = $this->user_model->get_user_by_id($id);
        $data['roles'] = $this->role_model->get_all_roles();
        $this->load->view('template/header');
        $this->load->view('user/user_edit', $data);
        $this->load->view('template/footer', $data);
    }

    public function update($id) {
        $role_id = $this->input->post('role_id');
        $this->user_model->update_role($id, $role_id);
        $this->session->set_flashdata('success', 'Role user berhasil diupdate.');
        redirect('user');
    }

    public function delete($id) {
        $this->user_model->delete_user($id);
        $this->session->set_flashdata('success', 'User berhasil dihapus.');
        redirect('user');
    }
}
