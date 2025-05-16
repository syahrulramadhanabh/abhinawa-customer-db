<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('supplier_model');
        $this->load->model('Customer_model');
        $this->load->model('Service_type_model');
        $this->load->model('Update_history_model');
        $this->load->model('Role_model');
        $this->load->library('form_validation');
        $this->load->library('upload');

        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        } 
    }

    public function index() {
        // Only fetch kdsupplier and nama_supplier for the main list
        $data['updates'] = $this->Update_history_model->get_all_updates();
        $data['suppliers'] = $this->supplier_model->get_supplier_summary(); // Adjusted to only fetch summary fields
        
        $this->load->view('template/header');
        $this->load->view('supplier/supplier_list', $data); // Only displays kdsupplier and nama_supplier
        $this->load->view('template/footer');
    }

    public function details($kdsupplier) {
        // Load supplier details for a specific supplier
        $data['updates'] = $this->Update_history_model->get_all_updates();
        $data['supplier'] = $this->supplier_model->get_supplier_by_kdsupplier($kdsupplier);
        $data['supplier_details'] = $this->supplier_model->get_supplier_details($kdsupplier); // Fetches all details except kdsupplier and nama_supplier

        $this->load->view('template/header');
        $this->load->view('supplier/supplier_detail', $data); 
        $this->load->view('template/footer');
    }
    
    public function create() {
        // Load the view to display the create supplier form
        $this->load->view('template/header');
        $this->load->view('supplier/supplier_create');
        $this->load->view('template/footer');
    }

    public function store() {
        // Load form validation library
        $this->load->library('form_validation');

        // Set validation rules
        $this->form_validation->set_rules('kdsupplier', 'Kode Supplier', 'required|is_unique[suppliers.kdsupplier]');
        $this->form_validation->set_rules('nama_supplier', 'Nama Supplier', 'required');

        if ($this->form_validation->run() == FALSE) {
            // If validation fails, reload the form
            $this->load->view('template/header');
            $this->load->view('supplier/supplier_create');
            $this->load->view('template/footer');
        } else {
            // If validation passes, save the supplier data
            $data = [
                'kdsupplier' => $this->input->post('kdsupplier'),
                'nama_supplier' => $this->input->post('nama_supplier')
            ];

            // Insert the data into the suppliers table
            $this->db->insert('suppliers', $data);

            // Redirect to the supplier list or a success page
            redirect('index.php/supplier/list');
        }
    }
    public function edit($id) {
        $data['updates'] = $this->Update_history_model->get_all_updates();
        $data['supplier'] = $this->supplier_model->get_supplier_by_id($id);
        $data['suppliers'] = $this->supplier_model->get_all_suppliers(); // Menyediakan daftar supplier untuk dropdown
        $data['service_types'] = $this->Service_type_model->get_all_service_types();
        
        $this->load->view('template/header');
        $this->load->view('supplier/supplier_edit', $data);
        $this->load->view('template/footer', $data);
    }
    

    public function update($id) {
        $data = [
            'kdsupplier' => $this->input->post('kdsupplier'),
            'nama_supplier' => $this->input->post('nama_supplier'),
        ];

        $this->supplier_model->update_supplier($id, $data);
        $this->session->set_flashdata('success', 'Supplier and details updated successfully.');
        redirect('supplier');
    }    

    public function delete($id) {
        if ($this->supplier_model->delete_supplier($id)) {
            $this->session->set_flashdata('success', 'Supplier and related data deleted successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete supplier.');
        }

        redirect('supplier'); 
    }

    public function view_suppliers() {
        $allowed_roles = [1, 2, 3]; 
        if (!$this->session->userdata('logged_in') || !in_array($this->session->userdata('role_id'), $allowed_roles)) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses ke halaman ini.');
            redirect('auth'); 
        }
        $data['updates'] = $this->Update_history_model->get_all_updates();
        $data['suppliers'] = $this->supplier_model->get_all_suppliers();
        $data['title'] = 'Supplier List User';
        
        $this->load->view('template/header', $data);
        $this->load->view('supplier/supplier_list_user', $data); 
        $this->load->view('template/footer', $data);
    }

    // Function to upload file if needed
private function upload_file($field_name) {
    $config['upload_path'] = './uploads/';
    $config['allowed_types'] = 'pdf|docx|jpg|jpeg|png';
    $config['max_size'] = 2048; // 2MB
    $this->load->library('upload', $config);

    if (!$this->upload->do_upload($field_name)) {
        return NULL;
    }
    return $this->upload->data('file_name');
}

public function add_supplier_detail($kdsupplier) {
    $this->form_validation->set_rules('cid_supplier', 'CID Supplier', 'required|is_unique[supplier_detail.cid_supplier]');
    $this->form_validation->set_rules('start_service', 'Start Service', 'required');
    $this->form_validation->set_rules('end_service', 'End Service', 'required');
    $this->form_validation->set_rules('service_type_supplier', 'Service Type', 'required');

    if ($this->form_validation->run() === FALSE) {
        // Load the form with validation errors
        $data['supplier'] = $this->supplier_model->get_supplier_by_kdsupplier($kdsupplier);
        $this->load->view('template/header');
        $this->load->view('supplier/add_supplier_detail', $data);
        $this->load->view('template/footer');
    } else {
        // Define file upload configuration
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'pdf|docx|jpg|jpeg|png';
        $config['max_size'] = 2048; // 2MB

        $this->load->library('upload', $config);

        // Initialize file variables
        $sdn = NULL;
        $topology = NULL;
        $eskalasi_matrix = NULL;

        // Handle file upload for 'sdn'
        if (!empty($_FILES['sdn']['name'])) {
            $config['file_name'] = 'sdn_' . time();  // Unique file name
            $this->upload->initialize($config);

            if ($this->upload->do_upload('sdn')) {
                $sdn = $this->upload->data('file_name');
            } else {
                echo $this->upload->display_errors();  // Display error if upload fails
                return;
            }
        }

        // Handle file upload for 'topology'
        if (!empty($_FILES['topology']['name'])) {
            $config['file_name'] = 'topology_' . time();
            $this->upload->initialize($config);

            if ($this->upload->do_upload('topology')) {
                $topology = $this->upload->data('file_name');
            } else {
                echo $this->upload->display_errors();
                return;
            }
        }

        // Handle file upload for 'eskalasi_matrix'
        if (!empty($_FILES['eskalasi_matrix']['name'])) {
            $config['file_name'] = 'eskalasi_matrix_' . time();
            $this->upload->initialize($config);

            if ($this->upload->do_upload('eskalasi_matrix')) {
                $eskalasi_matrix = $this->upload->data('file_name');
            } else {
                echo $this->upload->display_errors();
                return;
            }
        }

        // Prepare data for insertion
        $data = [
            'kdsupplier' => $kdsupplier,
            'cid_supplier' => $this->input->post('cid_supplier'),
            'start_service' => $this->input->post('start_service'),
            'end_service' => $this->input->post('end_service'),
            'service_type_supplier' => $this->input->post('service_type_supplier'),
            'sdn' => $sdn,
            'topology_supplier' => $topology,
            'eskalasi_matrix' => $eskalasi_matrix,
            'contact' => $this->input->post('contact')
        ];

        // Insert into database
        $this->supplier_model->insert_supplier_detail($data);

        // Redirect back to supplier details page
        redirect('supplier/details/' . $kdsupplier);
    }
}
}