<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Update_history extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Update_history_model');
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    // Fungsi untuk menampilkan form tambah riwayat pembaruan
    public function add_update() {
        $this->load->view('template/header');
        $this->load->view('update_history/add');
        $this->load->view('template/footer');
    }

    // Fungsi untuk menyimpan pembaruan baru
    public function save_update() {
        $version = $this->input->post('version');
        $changes = $this->input->post('changes');
        $author = $this->input->post('author');

        $this->Update_history_model->save_update_history($version, $changes, $author);

        redirect('update_history/index');
    }

    // Fungsi untuk menampilkan semua riwayat pembaruan
    public function index() {
        $data['updates'] = $this->Update_history_model->get_all_updates();
        $this->load->view('template/header');
        $this->load->view('update_history/view', $data);
        $this->load->view('template/footer', $data);
    }

    // Fungsi untuk menampilkan form tambah koreksi
    public function add_correction($id) {
        $data['update'] = $this->Update_history_model->get_update_by_id($id);
        $this->load->view('template/header');
        $this->load->view('update_history/add_correction', $data);
        $this->load->view('template/footer', $data);
    }

    // Fungsi untuk menyimpan koreksi
    public function save_correction() {
        $id = $this->input->post('id');
        $koreksi = $this->input->post('koreksi');

        $this->Update_history_model->save_correction($id, $koreksi);

        redirect('update_history/index');
    }
}
