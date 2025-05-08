<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class customer extends CI_Controller {
    private $telegram_bot_token = '7228263278:AAFPwxKRz87ZQSGW-o5wb4srQnCk9xu22Vo';
    private $telegram_chat_id  = '-4682244113';
        public function __construct() {
            parent::__construct();
            $this->load->model('Customer_model');
            $this->load->library('session');
            $this->load->helper('url');
            $this->load->helper('form');
            $this->load->model('Role_model');
            $this->load->model('Update_history_model');
            $this->load->library('pagination');
            $this->load->library('email');
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
        
        public function store_customer()
        {
            // Ambil data dari form
            $data = [
                'customer'            => $this->input->post('customer'),
                'customer_group_id'   => $this->input->post('group_id'),
                'kdsupplier'          => $this->input->post('kdsupplier'),
                'cid_supp'            => $this->input->post('cid_supp'),
                'cid_abh'             => $this->input->post('cid_abh'),
                'no_so'               => $this->upload_file('no_so'),
                'no_sdn'              => $this->upload_file('no_sdn'),
                'topology'            => $this->upload_file('topology'),
                'service_type_id'     => $this->input->post('service_type_id'),
                'start_date'          => $this->input->post('start_date'),
                'end_date'            => $this->input->post('end_date'),
            ];
        
            // Insert dan ambil ID
            $new_id = $this->Customer_model->insert_customer($data);
            if ($new_id) {
                // Kirim notifikasi untuk customer baru
                $this->notify_new_customer($new_id);
        
                $this->session->set_flashdata('success', 'Customer added and notification sent.');
            } else {
                $this->session->set_flashdata('error', 'Failed to add customer.');
            }
        
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
            $this->load->model('service_type_model');
            $description = $this->service_type_model->get_description_by_id($service_type_id);
        
            echo $description;
        }        

        public function check_service_end_dates()
        {
            $customers = $this->Customer_model->get_customers_with_end_date_today();
    
            foreach ($customers as $c) {
                $message  = "<b>🚨 Service End Today 🚨</b>\n";
                $message .= "<b>Customer:</b> {$c->customer}\n";
                $message .= "<b>CID:</b> {$c->cid_abh}\n";
                $message .= "<b>Service:</b> " . $this->get_service_type_name($c->service_type_id) . "\n";
                $message .= "<b>Ends:</b> " . date('j M Y', strtotime($c->end_date)) . "\n\n";
                $message .= "Please take necessary actions.";
    
                if ($this->send_telegram($message)) {
                    $this->log_notification($c->id, 'service_end_notification', 'Telegram sent');
                }
                // kalau gagal, sudah tercatat di send_telegram()
            }
    
            // Redirect atau tampilkan flash
            $this->session->set_flashdata('success', 'Service-end notifications processed.');
            redirect('customer');
        }
    
        // Kirim notifikasi terminasi untuk satu customer
        public function notify_termination($customer_id)
        {
            $c = $this->Customer_model->get_customer_by_id($customer_id);
            if (! $c) {
                $this->session->set_flashdata('error', 'Customer not found.');
                redirect('customer');
                return;
            }
    
            $message  = "<b>📤 Service Termination</b>\n";
            $message .= "<b>Customer:</b> {$c->customer}\n";
            $message .= "<b>CID:</b> {$c->cid_abh}\n";
            $message .= "<b>Service:</b> " . $this->get_service_type_name($c->service_type_id) . "\n";
            $message .= "<b>Start Date:</b> " . date('j M Y', strtotime($c->start_date)) . "\n";
            $message .= "<b>End Date:</b> " . date('j M Y', strtotime($c->end_date)) . "\n";
            $message .= "<b>Terminated Notification:</b> " . date('j M Y') . "\n\n";
            $message .= "Please follow termination procedures.";
    
            if ($this->send_telegram($message)) {
                $this->log_notification($customer_id, 'termination_notification', 'Telegram sent');
                $this->session->set_flashdata('success', 'Termination notification sent via Telegram.');
            } else {
                $this->session->set_flashdata('error', 'Failed to send Telegram notification. Check logs.');
            }
    
            redirect('customer/group_details/' . $c->customer_group_id);
        }
    
        // Ambil nama service type berdasarkan ID
        private function get_service_type_name($service_type_id)
        {
            $row = $this->db->get_where('service_types', ['id' => $service_type_id])->row();
            return $row ? $row->service_name : 'Unknown Service';
        }
    
        // Kirim pesan ke Telegram via Bot API
        private function send_telegram($message)
        {
            $url = "https://api.telegram.org/bot{$this->telegram_bot_token}/sendMessage";
            $payload = [
                'chat_id'                  => $this->telegram_chat_id,
                'text'                     => $message,
                'parse_mode'               => 'HTML',
                'disable_web_page_preview' => true,
            ];
    
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT        => 10,
            ]);
    
            $resp  = curl_exec($ch);
            $error = curl_error($ch);
            curl_close($ch);
    
            if ($error) {
                log_message('error', "Telegram curl error: $error");
                return false;
            }
    
            $json = json_decode($resp, true);
            if (empty($json['ok'])) {
                log_message('error', 'Telegram API error: ' . print_r($json, true));
                return false;
            }
    
            return true;
        }
    
        // Simpan log notifikasi ke database
        private function log_notification($customer_id, $notification_type, $subject = null)
        {
            $this->db->insert('notification_logs', [
                'customer_id'       => $customer_id,
                'notification_type' => $notification_type,
                'subject'           => $subject,
                'sent_at'           => date('Y-m-d H:i:s'),
                'sent_by'           => $this->session->userdata('user_id'),
            ]);
        }
    
        // Kirim notifikasi untuk customer baru
        private function notify_new_customer($customer_id)
        {
            $c = $this->Customer_model->get_customer_by_id($customer_id);
            if (! $c) {
                log_message('error', "notify_new_customer(): customer ID {$customer_id} not found");
                return;
            }
    
            $message  = "<b>🆕 New Customer Added</b>\n";
            $message .= "<b>Customer:</b> {$c->customer}\n";
            $message .= "<b>CID:</b> {$c->cid_abh}\n";
            $message .= "<b>Group:</b> {$c->group_id}\n";
            $message .= "<b>Service:</b> " . $this->get_service_type_name($c->service_type_id) . "\n";
            $message .= "<b>Start Date:</b> " . date('j M Y', strtotime($c->start_date)) . "\n";
            $message .= "<b>End Date:</b> " . date('j M Y', strtotime($c->end_date)) . "\n\n";
            $message .= "Please review the new customer entry.";
    
            if ($this->send_telegram($message)) {
                $this->log_notification($customer_id, 'new_customer_notification', 'Telegram sent');
            } else {
                log_message('error', "New-customer Telegram send failed for ID {$customer_id}");
            }
        }
    } 