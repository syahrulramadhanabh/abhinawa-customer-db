<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session'); 
        $this->load->helper('url');
        $this->load->model('auth_model');
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
        }
        $this->load->view('auth/login');
    }

    public function login() {
        $username = $this->input->post('username');
        $password = md5($this->input->post('password')); // Gunakan MD5 untuk hashing password (lebih baik menggunakan bcrypt di masa depan)
        $user = $this->auth_model->check_user($username, $password);
    
        if ($user) {
            $session_data = [
                'username' => $user->username,
                'role_id' => $user->role_id, // Menyimpan role_id di session
                'logged_in' => true
            ];
            $this->session->set_userdata($session_data);
            redirect('dashboard');
        } else {
            $this->session->set_flashdata('login_error', 'Username atau password salah.');
            redirect('auth');
        }
    }

    public function change_password() {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth'); // Redirect ke login jika belum login
        }
        $this->load->view('template/header');
        $this->load->view('auth/change_password');
        $this->load->view('template/footer');
    }

    // Handle password update
    public function update_password() {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth'); // Redirect ke login jika belum login
        }

        $username = $this->session->userdata('username');
        $old_password = $this->input->post('old_password');
        $new_password = $this->input->post('new_password');
        $confirm_password = $this->input->post('confirm_password');

        // Memastikan bahwa password baru dan konfirmasi password cocok
        if ($new_password !== $confirm_password) {
            $this->session->set_flashdata('error', 'Password baru dan konfirmasi password tidak cocok.');
            redirect('auth/change_password');
        }

        // Verifikasi password lama
        $user = $this->auth_model->check_user($username, md5($old_password));
        if (!$user) {
            $this->session->set_flashdata('error', 'Password lama salah.');
            redirect('auth/change_password');
        }

        // Update password jika password lama benar
        $this->auth_model->update_password($user->id, md5($new_password));
        $this->session->set_flashdata('success', 'Password berhasil diubah.');
        redirect('auth/change_password');
    }
    
    public function logout() {
        $this->session->unset_userdata(['username', 'role_id', 'logged_in']);
        $this->session->sess_destroy();
        redirect('auth');
    }
}
