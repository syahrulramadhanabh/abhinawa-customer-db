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
            $this->load->library('email', $this->config->item('email'));

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
            // Validasi group_id
            if (!$group_id || !is_numeric($group_id)) {
                $this->session->set_flashdata('error', 'Invalid group ID specified.');
                redirect('customer/index');
                return;
            }

            // Ambil data yang diperlukan
            $data['suppliers']      = $this->Customer_model->get_all_suppliers();
            $data['service_types']  = $this->Customer_model->get_all_service_types();

            // Ambil cid_supplier per kdsupplier
            $supplier_cid = [];
            foreach ($data['suppliers'] as $supplier) {
                $supplier_cid[$supplier->kdsupplier] = 
                    $this->Customer_model->get_cid_suppliers($supplier->kdsupplier);
            }
            $data['supplier_cid'] = $supplier_cid;

            $data['group_id']      = $group_id;

            // Load views
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
                'sla'                 => $this->input->post('sla'),
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
        
            // Load the edit customer view
            $this->load->view('template/header');
            $this->load->view('customer/edit_customer', $data);
            $this->load->view('template/footer', $data);
        }
        
        public function update_customer($customer_id) {
            // Cek role
            if ($this->session->userdata('role_id') != 1) {
                $this->session->set_flashdata('error', 'Unauthorized access.');
                redirect('customer/index');
                return;
            }
        
            // Ambil data lama untuk cek perubahan status
            $old = $this->Customer_model->get_customer_by_id($customer_id);
            $old_status = $old->status;
        
            // Siapkan data baru
            $data = [
                'customer'        => $this->input->post('customer'),
                'kdsupplier'      => $this->input->post('kdsupplier'),
                'cid_supp'        => $this->input->post('cid_supp'),
                'service_type_id' => $this->input->post('service_type_id'),
                'start_date'      => $this->input->post('start_date'),
                'end_date'        => $this->input->post('end_date'),
                'status'          => $this->input->post('status'),
            ];
        
            // File upload seperti sebelumnya...
            if (!empty($_FILES['no_so']['name']))      $data['no_so']   = $this->upload_file('no_so');
            if (!empty($_FILES['no_sdn']['name']))     $data['no_sdn']  = $this->upload_file('no_sdn');
            if (!empty($_FILES['topology']['name']))   $data['topology']= $this->upload_file('topology');
        
            // Update di database
            $this->Customer_model->update_customer($customer_id, $data);
        
            // **Notifikasi jika status berubah**
            $new_status = $data['status'];
            if ($old_status != $new_status) {
                $this->notify_status_change($customer_id, $old_status, $new_status);
            }
        
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
        private function get_service_type_name($service_type_id)
        {
            $row = $this->db
                        ->select('service_name')
                        ->from('service_types')
                        ->where('id', $service_type_id)
                        ->get()
                        ->row();
        
            return $row ? $row->service_name : 'Unknown Service';
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
                show_error('Customer not found', 404);
            }
            $noticeDate = date('j F Y');
            // Bangun HTML message dengan end_date dari database
            $html_message = <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
          <meta charset="utf-8">
          <style>
            @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap");
            body { font-family: "Poppins", sans-serif; background:#f4f6f8; margin:0; padding:20px; }
            .container { background:#fff; max-width:600px; margin:auto; padding:30px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); }
            .header { text-align:center; margin-bottom:30px; }
            .header h1 { font-weight:600; color:#333; margin:0; }
            .header p { color:#666; margin-top:8px; }
            p, ul { color:#555; line-height:1.6; }
            ul { margin-left:20px; margin-bottom:16px; }
            .footer { font-size:14px; color:#888; text-align:center; margin-top:30px; }
            a { color:#007bff; text-decoration:none; }
          </style>
        </head>
        <body>
          <div class="container">
            <div class="header">
              <h1>Service Termination Notice</h1>
              <p><em>One-Month Advance Notification</em></p>
            </div>
        
            <p>Dear <strong>{$c->customer}</strong>,</p>
        
            <p>
              In accordance with our service agreement between <strong>PT Abhinawa Sumberdaya Asia</strong> and your organization, please consider this letter as a formal one-month notice that your service will be terminated effective <strong>{$c->end_date}</strong>. We kindly request that you complete any necessary data migrations, backups, or final reconciliations prior to this date to ensure a smooth transition.
            </p>
        
            <p><strong>Termination Details:</strong></p>
            <ul>
              <li><strong>Customer Name:</strong> {$c->customer}</li>
              <li><strong>CID:</strong> {$c->cid_abh}</li>
              <li><strong>Group ID:</strong> {$c->customer_group_id}</li>
              <li><strong>Service:</strong> {$this->get_service_type_name($c->service_type_id)}</li>
              <li><strong>Notice Date:</strong> {$noticeDate}</li>
              <li><strong>Effective Termination Date:</strong> {$c->end_date}</li>
            </ul>
        
            <p>
              If you have any questions or require further assistance, please do not hesitate to contact our support team at <a href="mailto:noc@abhinawa.co.id">noc@abhinawa.co.id</a> or call us at (021) 1234-5678.
            </p>
        
            <p>Thank you for your attention and for the opportunity to serve you.</p>
        
            <p>
              Sincerely,<br>
              <strong>Customer Success Team<br>PT Abhinawa Sumberdaya Asia</strong>
            </p>
        
            <div class="footer">
              PT Abhinawa Sumberdaya Asia • Menara Kadin Indonesia, Jl. H. R. Rasuna Said, RT.1/RW.2, Kuningan, Kuningan Tim., Kecamatan Setiabudi, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12950 • Email: noc@abhinawa.co.id • Telp: (021) 1234-5678
            </div>
          </div>
        </body>
        </html>
        HTML;
        
            // Kirim email + Telegram
            $this->send_email_and_notify(
                "One-Month Service Termination Notice – {$c->customer}",
                $html_message,
                'termination_notification',
                $customer_id
            );
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

    // Styled notification with Poppins font and brand colors
    $html  = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Customer Onboarded</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #f4f6f8;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
            overflow: hidden;
        }
        .card-header {
            background-color: #2E86C1;
            color: #fff;
            padding: 16px 20px;
            font-size: 20px;
            font-weight: 600;
        }
        .card-body {
            padding: 20px;
        }
        .card-body p {
            margin: 12px 0;
            line-height: 1.5;
        }
        .label {
            color: #2E86C1;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            🎉 New Customer Onboarded
        </div>
        <div class="card-body">
            <p>New customer onboarded under <span class="label">Group ' . htmlspecialchars($c->customer_group_id) . '</span>. Let’s give them our best!</p>
            <p><span class="label">SID:</span> ' . htmlspecialchars($c->cid_abh) . '</p>
            <p><span class="label">Service:</span> ' . htmlspecialchars($this->get_service_type_name($c->service_type_id)) . '</p>
            <p><span class="label">Start Date:</span> ' . date('j F Y', strtotime($c->start_date)) . '</p>
            <p><span class="label">End Date:</span> ' . date('j F Y', strtotime($c->end_date)) . '</p>
            <p>Team, let’s stay sharp and ready to support. If any issues arise, address them promptly. Let’s keep up the great work! 💪</p>
        </div>
    </div>
</body>
</html>';

    $this->send_email_and_notify(
        '🆕 New Customer Onboarded – ' . htmlspecialchars($c->customer),
        $html,
        'new_customer_notification',
        $customer_id
    );
}



public function test_email()
{
    $to_param = $this->input->get('to');

    if ($to_param) {
        $recipients = array_map('trim', explode(',', $to_param));
    } else {
        $recipients = [
            'syahrul@abhinawa.co.id',
            'anis@abhinawa.co.id',
            'daulay@abhinawa.co.id',
        ];
    }

    // Siapkan email
    $this->email->clear();
    $this->email->from('syahrul@c-tech.id', 'System Administrator - Abhinawa');
    $this->email->to($recipients);  
    $this->email->subject('Abhinawa Customer Database - Test Email Delivery - ' . date('Y-m-d H:i:s'));
    $this->email->message(
        '<p>Ini email test untuk memverifikasi konfigurasi SMTP.</p>' .
        '<p>Waktu kirim: ' . date('Y-m-d H:i:s') . '</p>'
    );

    if ($this->email->send()) {
        echo "<h2 style='color:green;'>SUCCESS</h2>";
        echo "<p>Email berhasil dikirim ke:<br><strong>"
           . implode(', ', $recipients)
           . "</strong></p>";
    } else {
        echo "<h2 style='color:red;'>FAILURE</h2>";
        echo "<pre>"
           . htmlspecialchars($this->email->print_debugger(
               ['headers','subject','body','message']
             ))
           . "</pre>";
    }
}

private function notify_status_change($customer_id, $old_status, $new_status)
{
    $labels = [
        1 => 'Active',
        2 => 'Suspend',
        3 => 'Inactive',
        4 => 'Terminated',
    ];

    $c = $this->Customer_model->get_customer_by_id($customer_id);
    if (!$c) {
        log_message('error', "notify_status_change(): ID {$customer_id} not found");
        return;
    }

    $old_label = isset($labels[$old_status]) ? $labels[$old_status] : 'Unknown';
    $new_label = isset($labels[$new_status]) ? $labels[$new_status] : 'Unknown';

    // If status is changed to Suspend, send a formal suspension notice
    if ($new_status === 2) {
        $noticeDate     = date('j F Y');                        // e.g. 14 May 2025
        $effectiveDate  = date('Y-m-d', strtotime('+1 day'));   // e.g. 2025-05-15

        $subject = "Service Suspension Notice – " . htmlspecialchars($c->customer);

        $html  = '<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Service Suspension Notice</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: "Poppins", sans-serif;
      background-color: #f4f6f8;
      color: #333;
      padding: 20px;
      margin: 0;
    }
    .container {
      background: #fff;
      max-width: 600px;
      margin: 0 auto;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      overflow: hidden;
    }
    .header {
      background-color: #E74C3C;
      color: #fff;
      padding: 16px;
      font-size: 18px;
      font-weight: 600;
    }
    .body {
      padding: 20px;
      line-height: 1.6;
    }
    .body p {
      margin: 12px 0;
    }
    .details {
      width: 100%;
      border-collapse: collapse;
      margin: 16px 0;
    }
    .details th,
    .details td {
      padding: 8px 12px;
      border: 1px solid #ddd;
      text-align: left;
    }
    .details th {
      background-color: #f0f0f0;
      font-weight: 600;
    }
    .footer {
      padding: 16px 20px;
      background-color: #f4f6f8;
      text-align: center;
      font-size: 14px;
      color: #777;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      Service Suspension Notice
    </div>
    <div class="body">
      <p>Dear ' . htmlspecialchars($c->customer) . ',</p>

      <p>In accordance with our service agreement between PT Abhinawa Sumberdaya Asia and your organization, we regret to inform you that your service will be <strong>temporarily suspended</strong> effective <strong>' . $effectiveDate . '</strong>.</p>

      <p>Please review the details below:</p>

      <table class="details">
        <tr>
          <th>Customer Name</th>
          <td>' . htmlspecialchars($c->customer) . '</td>
        </tr>
        <tr>
          <th>CID</th>
          <td>' . htmlspecialchars($c->cid_abh) . '</td>
        </tr>
        <tr>
          <th>Group ID</th>
          <td>' . htmlspecialchars($c->customer_group_id) . '</td>
        </tr>
        <tr>
          <th>Service</th>
          <td>' . htmlspecialchars($this->get_service_type_name($c->service_type_id)) . '</td>
        </tr>
        <tr>
          <th>Notice Date</th>
          <td>' . $noticeDate . '</td>
        </tr>
        <tr>
          <th>Effective Suspension Date</th>
          <td>' . $effectiveDate . '</td>
        </tr>
      </table>

      <p>To avoid further disruptions, we kindly advise you to reach out to our Sales Team to resolve any outstanding issues or to discuss available options. You can contact them at <a href="mailto:sales@abhinawa.co.id">sales@abhinawa.co.id</a> or call (021) 5678-9101.</p>

      <p>For technical support, please contact <a href="mailto:noc@abhinawa.co.id">noc@abhinawa.co.id</a> or (021) 1234-5678.</p>

      <p>Thank you for your attention and understanding.</p>

      <p>Sincerely,<br>
         Customer Success Team<br>
         PT Abhinawa Sumberdaya Asia
      </p>
    </div>
    <div class="footer">
      &copy; ' . date('Y') . ' PT Abhinawa Sumberdaya Asia. All rights reserved.
    </div>
  </div>
</body>
</html>';

        $this->send_email_and_notify(
            $subject,
            $html,
            'status_change_notification',
            $customer_id
        );

        return;
    }

    // Fallback for other status changes
    $html  = "<h2>🔄 Customer Status Changed</h2>";
    $html .= "<p><strong>Customer:</strong> {$c->customer}<br>";
    $html .= "<strong>CID:</strong> {$c->cid_abh}<br>";
    $html .= "<strong>Group:</strong> {$c->customer_group_id}<br>";
    $html .= "<strong>Old Status:</strong> {$old_label}<br>";
    $html .= "<strong>New Status:</strong> {$new_label}<br>";
    $html .= "<strong>Time:</strong> " . date('j M Y H:i:s') . "</p>";

    $subject = "Customer {$c->customer} Status: {$old_label} → {$new_label}";

    $this->send_email_and_notify(
        $subject,
        $html,
        'status_change_notification',
        $customer_id
    );
}

private function send_email_and_notify($subject, $html_message, $notification_type, $customer_id = null)
{
    // 1. Kirim email
    $this->email->clear();
    $this->email->from('syahrul@c-tech.id', '[Development] - Abhinawa Customer Notification');
    $this->email->to([
        'syahrul@abhinawa.co.id',
        'daulay@abhinawa.co.id',
        'yuniar@abhinawa.co.id',
        'aulia.putri@abhinawa.co.id',
        'toni@abhinawa.co.id',
    ]);
    $this->email->subject($subject);
    $this->email->message($html_message);

    if ( ! $this->email->send()) {
        // jika gagal, kirim Telegram sebagai fallback
        $debug = $this->email->print_debugger(['headers','subject','body']);
        $this->send_telegram("<b>🚨 EMAIL ERROR 🚨</b>\nSubject: {$subject}\n\n" . htmlentities($debug));
        $log_subject = 'email_error';
    } else {
        // jika sukses, kirim Telegram konfirmasi
        $this->send_telegram("<b>✅ EMAIL SENT ✅</b>\nSubject: {$subject}");
        $log_subject = 'email_success';
    }

    // 2. Simpan log ke database (jika customer_id diberikan)
    if ($customer_id !== null) {
        $this->db->insert('notification_logs', [
            'customer_id'       => $customer_id,
            'notification_type' => $notification_type,
            'subject'           => $log_subject,
            'sent_at'           => date('Y-m-d H:i:s'),
            'sent_by'           => $this->session->userdata('user_id'),
        ]);
    }
} 
}