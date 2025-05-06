<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class customer extends CI_Controller {

        public function __construct() {
            parent::__construct();
            $this->load->model('Customer_model');
            $this->load->library('session');
            $this->load->helper('url');
            $this->load->helper('form');
            $this->load->model('Role_model');
            $this->load->model('Update_history_model');
            $this->load->library('pagination');
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }    

public function index() {
    $search = $this->input->get('search');

    $config = [
        'base_url' => base_url('customer/index'),
        'total_rows' => $this->Customer_model->count_customer_groups($search),
        'per_page' => 5,
        'page_query_string' => TRUE,
        'query_string_segment' => 'page',
        'reuse_query_string' => TRUE,
        'full_tag_open' => '<nav><ul class="pagination justify-content-center">',
        'full_tag_close' => '</ul></nav>',
        'first_tag_open' => '<li class="page-item">',
        'first_tag_close' => '</li>',
        'last_tag_open' => '<li class="page-item">',
        'last_tag_close' => '</li>',
        'next_tag_open' => '<li class="page-item">',
        'next_tag_close' => '</li>',
        'prev_tag_open' => '<li class="page-item">',
        'prev_tag_close' => '</li>',
        'cur_tag_open' => '<li class="page-item active"><a href="#" class="page-link">',
        'cur_tag_close' => '</a></li>',
        'num_tag_open' => '<li class="page-item">',
        'num_tag_close' => '</li>',
        'attributes' => ['class' => 'page-link']
    ];
    
    $this->pagination->initialize($config);

    // Get the page number
    $page = $this->input->get('page');
    $page = ($page) ? $page : 0;

    // Fetch customer groups with search and pagination
    $data['customer_groups'] = $this->Customer_model->get_customer_groups($config['per_page'], $page, $search);
    $data['pagination'] = $this->pagination->create_links();
    $data['search'] = $search;
    $data['updates'] = $this->Update_history_model->get_all_updates();

    $this->load->view('template/header');
    $this->load->view('customer/customer_group_list', $data);
    $this->load->view('template/footer', $data);
}
        public function group_details($group_id) {
            $data['customers'] = $this->Customer_model->get_customers_by_group($group_id);
            $data['roles'] = $this->Role_model->get_all_roles();
            $data['group_id'] = $group_id;
            
            $data['role_id'] = $this->session->userdata('role_id');  
        
            $this->load->view('template/header');
            $this->load->view('customer/group_details', $data);
            $this->load->view('template/footer', $data);
        }        
    
        public function add_customer($group_id = null) {
            // Check if group_id is valid; if not, redirect or show error
            if (!$group_id || !is_numeric($group_id)) {
                $this->session->set_flashdata('error', 'Invalid group ID specified.');
                redirect('customer/index');
                return;
            }
        
            // Fetch necessary data
            $data['suppliers'] = $this->Customer_model->get_all_suppliers();
            $data['service_types'] = $this->Customer_model->get_all_service_types();
            $data['unused_cid_suppliers'] = $this->Customer_model->get_unused_cid_suppliers();
            $data['group_id'] = $group_id;
        
            // Load the view and pass data
            $this->load->view('template/header');
            $this->load->view('customer/add_customer', $data);
            $this->load->view('template/footer', $data);
        }        
        
        public function store_customer() {
            $data = [
                'customer' => $this->input->post('customer'),
                'customer_group_id' => $this->input->post('group_id'),
                'kdsupplier' => $this->input->post('kdsupplier'),
                'cid_supp' => $this->input->post('cid_supp'),
                'cid_abh' => $this->input->post('cid_abh'),
                'no_so' => $this->upload_file('no_so'),
                'no_sdn' => $this->upload_file('no_sdn'),
                'topology' => $this->upload_file('topology'),
                'service_type_id' => $this->input->post('service_type_id'),
                'start_date' => $this->input->post('start_date'),
                'end_date' => $this->input->post('end_date')
            ];
        
            if ($this->Customer_model->insert_customer($data)) {
                echo "Customer added successfully.";
            } else {
                $error = $this->db->error();
                echo "Insert failed: " . $error['message'];
            }
        
            // Comment out redirect for testing
             redirect('customer/group_details/' . $data['customer_group_id']);
        }        
        
        
        private function upload_file($field_name) {
            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = 'pdf';
            $config['max_size'] = 2048; // 2MB limit
        
            $this->load->library('upload', $config);
        
            if ($this->upload->do_upload($field_name)) {
                return $this->upload->data('file_name');
            } else {
                $this->session->set_flashdata('error', 'File upload failed: ' . $this->upload->display_errors());
                return null;
            }
        }
        
        public function edit_customer($customer_id) {
            // Check if the user has the required role
            if ($this->session->userdata('role_id') != 1) {
                $this->session->set_flashdata('error', 'Unauthorized access.');
                redirect('customer/index');
                return;
            }
        
            // Fetch customer and required data
            $data['customer'] = $this->Customer_model->get_customer_by_id($customer_id);
            $data['suppliers'] = $this->Customer_model->get_all_suppliers();
            $data['service_types'] = $this->Customer_model->get_all_service_types();
            $data['unused_cid_suppliers'] = $this->Customer_model->get_unused_cid_suppliers();
        
            // Load the edit customer view
            $this->load->view('template/header');
            $this->load->view('customer/edit_customer', $data);
            $this->load->view('template/footer', $data);
        }
        
        public function update_customer($customer_id) {
            // Check if the user has the required role
            if ($this->session->userdata('role_id') != 1) {
                $this->session->set_flashdata('error', 'Unauthorized access.');
                redirect('customer/index');
                return;
            }
        
            $data = [
                'customer' => $this->input->post('customer'),
                'kdsupplier' => $this->input->post('kdsupplier'),
                'cid_supp' => $this->input->post('cid_supp'),
                'service_type_id' => $this->input->post('service_type_id'),
                'start_date' => $this->input->post('start_date'),
                'end_date' => $this->input->post('end_date'),
                'status' => $this->input->post('status'),
            ];
        
            // Handle file uploads
            if (!empty($_FILES['no_so']['name'])) {
                $data['no_so'] = $this->upload_file('no_so');
            }
            if (!empty($_FILES['no_sdn']['name'])) {
                $data['no_sdn'] = $this->upload_file('no_sdn');
            }
            if (!empty($_FILES['topology']['name'])) {
                $data['topology'] = $this->upload_file('topology');
            }
        
            
            $this->Customer_model->update_customer($customer_id, $data);
            $this->session->set_flashdata('success', 'Customer updated successfully.');
            redirect('customer/group_details/' . $this->input->post('group_id'));
        }
        
        public function delete_customer($customer_id) {
            // Check if the user has the required role
            if ($this->session->userdata('role_id') != 1) {
                $this->session->set_flashdata('error', 'Unauthorized access.');
                redirect('customer');
                return;
            }
        
            $this->Customer_model->delete_customer($customer_id);
            $this->session->set_flashdata('success', 'Customer deleted successfully.');
            redirect('customer');
        }
        public function get_service_type_description($service_type_id) {
            // Load the service type model (make sure you have this model)
            $this->load->model('service_type_model');
            $description = $this->service_type_model->get_description_by_id($service_type_id);
            
            // Return the description as plain text for use in the modal
            echo $description;
        }        
                        
    }        
    