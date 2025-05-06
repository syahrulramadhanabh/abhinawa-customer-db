<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class service_type extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('service_type_model');
        $this->load->model('Update_history_model');
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    public function index() {
        $data['updates'] = $this->Update_history_model->get_all_updates();
        $data['service_types'] = $this->service_type_model->get_all_service_types();
        $this->load->view('template/header');
        $this->load->view('service_type/service_type_list', $data);
        $this->load->view('template/footer', $data);
    }
    
    public function view() {
        $data['updates'] = $this->Update_history_model->get_all_updates();
        $data['service_types'] = $this->service_type_model->get_all_service_types();
        $this->load->view('template/header');
        $this->load->view('service_type/service_type_list_user', $data);
        $this->load->view('template/footer', $data);
    }
    public function add() {
        $data = [
            'service_name' => $this->input->post('service_name'),
            'description' => $this->input->post('description')
        ];
        $this->service_type_model->insert_service_type($data);
        $this->session->set_flashdata('success', 'Service type added successfully.');
    
        redirect('service_type');
    }
    
    public function update($id) {
        // Retrieve data from POST request
        $data = [
            'service_name' => $this->input->post('service_name'),
            'description' => $this->input->post('description')
        ];
    
        // Update in database and check result
        $updated = $this->service_type_model->update_service_type($id, $data);
        if ($updated) {
            // Set success message and redirect
            $this->session->set_flashdata('success', 'Service type updated successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to update service type.');
        }
        
        redirect('service_type'); 
    }

    public function delete($id) {
        $this->service_type_model->delete_service_type($id);
        $this->session->set_flashdata('success', 'Service type deleted successfully.');
        redirect('service_type'); 
    }
    
    public function edit($id) {
        $data['updates'] = $this->Update_history_model->get_all_updates();
        $data['service_type'] = $this->service_type_model->get_service_type_by_id($id);
        $this->load->view('template/header');
        $this->load->view('service_type/service_type_edit', $data);
        $this->load->view('template/footer', $data);
    }
}
