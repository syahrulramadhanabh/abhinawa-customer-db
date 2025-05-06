<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Load necessary models and helpers
        $this->load->model('Customer_model');
        $this->load->model('Update_history_model');
        $this->load->helper('url');
        $this->load->helper('html');
        $this->load->library('session');
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    public function index() {
        $status_counts = $this->Customer_model->get_customer_status_counts();
    
        // Total counts for other metrics
        $data['total_customers'] = $this->Customer_model->get_customer_count();
        $data['total_suppliers'] = $this->Customer_model->get_supplier_count();
        $data['active_customers'] = $status_counts['active'];
        $data['suspend_customers'] = $status_counts['suspend'];
        $data['nonaktif_customers'] = $status_counts['nonaktif'];
        $data['updates'] = $this->Update_history_model->get_all_updates();

        $this->load->view('template/header');
        $this->load->view('admin/halaman', $data);
        $this->load->view('template/footer', $data);
        }
}