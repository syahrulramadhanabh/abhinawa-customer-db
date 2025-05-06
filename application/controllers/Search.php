<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Load required models
        $this->load->model([
            'Search_model',
            'Update_history_model',
            'Customer_model',
            'Supplier_model',
            'Service_type_model'
        ]);
        
        // Check authentication
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    public function index() {
        // Get search parameters
        $search_query = $this->input->get('user', TRUE);
        $search_category = $this->input->get('category', TRUE) ?: 'all';

        // Initialize data array with default values
        $data = [
            'user' => $search_query,
            'search_category' => $search_category,
            'customer_groups' => [],
            'customers' => [],
            'suppliers' => [],
            'supplier_detail' => [], // Add supplier_detail to the initial data array
            'service_types' => [],
            'updates' => [],
            'error' => ''
        ];

        // Only perform search if query is provided
        if (!empty($search_query)) {
            try {
                // Get search results
                $search_results = $this->Search_model->get_search_results($search_category, $search_query);
                
                // Merge search results with data array
                $data = array_merge($data, $search_results);
                
                // Get update history
                $data['updates'] = $this->Update_history_model->get_all_updates();
            } catch (Exception $e) {
                $data['error'] = 'An error occurred while performing the search.';
                log_message('error', 'Search error: ' . $e->getMessage());
            }
        }

        // Load views
        $this->_load_views($data);
    }

    private function _load_views($data) {
        $this->load->view('template/header');
        $this->load->view('search/global_search_form', $data);
        $this->load->view('search/global_search_results', $data);
        $this->load->view('template/footer');
    }

    // Optional: Add method for AJAX search if needed
    public function ajax_search() {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $search_query = $this->input->get('user', TRUE);
        $search_category = $this->input->get('category', TRUE) ?: 'all';

        try {
            $results = [];
            if (!empty($search_query)) {
                $results = $this->Search_model->get_search_results($search_category, $search_query);
            }
            
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'data' => $results
                ]));
        } catch (Exception $e) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'error' => 'An error occurred while performing the search.'
                ]));
        }
    }
}
