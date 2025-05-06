<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_groups extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('customer_groups_model');
        $this->load->model('Update_history_model');
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    // Display all customer groups
    public function index() {
        $data['updates'] = $this->Update_history_model->get_all_updates();
        $data['customer_groups'] = $this->customer_groups_model->get_all_customer_groups();
        $this->load->view('template/header');
        $this->load->view('customer_groups/customer_groups_list', $data);
        $this->load->view('template/footer', $data);
    }

    // Display form to add a new customer group
    public function create() {
        $data['updates'] = $this->Update_history_model->get_all_updates();
        $this->load->view('template/header');
        $this->load->view('customer_groups/customer_groups_create');
        $this->load->view('template/footer', $data);
    }

    // Save new customer group to database
    public function store() {
        $data = [
            'group_name' => $this->input->post('group_name'),
            'description' => $this->input->post('description')
        ];
        $this->customer_groups_model->insert_customer_group($data);
        $this->session->set_flashdata('success', 'Customer group added successfully.');
        redirect('customer_groups');
    }

    // Display form to edit an existing customer group
    public function edit($id) {
        $data['updates'] = $this->Update_history_model->get_all_updates();
        $data['customer_group'] = $this->customer_groups_model->get_customer_group_by_id($id);
        $this->load->view('template/header');
        $this->load->view('customer_groups/customer_groups_edit', $data);
        $this->load->view('template/footer', $data);
    }

    // Update customer group details in the database
    public function update($id) {
        $data = [
            'group_name' => $this->input->post('group_name'),
            'description' => $this->input->post('description')
        ];
        $this->customer_groups_model->update_customer_group($id, $data);
        $this->session->set_flashdata('success', 'Customer group updated successfully.');
        redirect('customer_groups');
    }

    // Delete customer group
    public function delete($id) {
        $this->customer_groups_model->delete_customer_group($id);
        $this->session->set_flashdata('success', 'Customer group deleted successfully.');
        redirect('customer_groups');
    }
}
