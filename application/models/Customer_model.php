<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_model extends CI_Model {

    // Mendapatkan semua grup pelanggan
    public function get_all_customer_groups() {
        return $this->db->get('customer_groups')->result();
    }

    // Mendapatkan pelanggan berdasarkan ID grup dengan informasi jenis layanan dan pemasok
    public function get_customers_by_group($group_id) {
        $sql = "SELECT customers.*, suppliers.nama_supplier, supplier_detail.cid_supplier AS supplier_cid, 
                       customer_groups.group_name, service_types.service_name AS service_type_name
                FROM customers
                LEFT JOIN suppliers ON customers.kdsupplier = suppliers.kdsupplier
                LEFT JOIN supplier_detail ON suppliers.kdsupplier = supplier_detail.kdsupplier
                LEFT JOIN customer_groups ON customers.customer_group_id = customer_groups.id
                LEFT JOIN service_types ON customers.service_type_id = service_types.id
                WHERE customers.customer_group_id = ?";
                
        return $this->db->query($sql, array($group_id))->result();
    }
    

    public function get_customer_groups($limit, $start, $search = '') {
        $this->db->select('customer_groups.*, supplier_detail.cid_supplier AS supplier_cid');
        $this->db->from('customer_groups');
        $this->db->join('suppliers', 'customer_groups.kdsupplier = suppliers.kdsupplier', 'left');
        $this->db->join('supplier_detail', 'suppliers.kdsupplier = supplier_detail.kdsupplier', 'left');

        if (!empty($search)) {
            $this->db->group_start()
                     ->like('customer_groups.group_name', $search)
                     ->or_like('supplier_detail.cid_supplier', $search)
                     ->group_end();
        }

        $this->db->limit($limit, $start);
        return $this->db->get()->result();
    }

    public function count_customer_groups($search = '') {
        $this->db->from('customer_groups');
        $this->db->join('suppliers', 'customer_groups.kdsupplier = suppliers.kdsupplier', 'left');
        $this->db->join('supplier_detail', 'suppliers.kdsupplier = supplier_detail.kdsupplier', 'left');

        if (!empty($search)) {
            $this->db->group_start()
                     ->like('customer_groups.group_name', $search)
                     ->or_like('supplier_detail.cid_supplier', $search)
                     ->group_end();
        }

        return $this->db->count_all_results();
    }

    // Mendapatkan semua pemasok dengan detail tambahan
    public function get_all_suppliers() {
        return $this->db->get('suppliers')->result();
    }

    // Mendapatkan semua jenis layanan
    public function get_all_service_types() {
        return $this->db->get('service_types')->result();
    }

    public function insert_customer($data) {
        return $this->db->insert('customers', $data);
    }

    public function get_customer_count() {
        return $this->db->count_all('customers');
    }

    public function get_supplier_count() {
        return $this->db->count_all('suppliers');
    }

    public function get_customer_status_count($status) {
        $this->db->where('status', $status);
        $this->db->from('customers');
        return $this->db->count_all_results();
    }

    public function get_unused_cid_suppliers() {
        // Query untuk menemukan semua cid_supplier di supplier_detail yang belum digunakan di customers
        $sql = "SELECT supplier_detail.cid_supplier 
                FROM supplier_detail
                LEFT JOIN customers ON supplier_detail.cid_supplier = customers.cid_supp 
                WHERE customers.cid_supp IS NULL";
        return $this->db->query($sql)->result();
    }

    public function get_customer_by_id($customer_id) {
        return $this->db->get_where('customers', ['id' => $customer_id])->row();
    }
    
    public function update_customer($customer_id, $data) {
        $this->db->where('id', $customer_id);
        return $this->db->update('customers', $data);
    }
    
    public function delete_customer($customer_id) {
        $this->db->where('id', $customer_id);
        return $this->db->delete('customers');
    }

    public function get_customer_status_counts() {
        $active_count = 0;
        $suspend_count = 0;
        $nonaktif_count = 0;
    
        $today = new DateTime();
    
        // Mendapatkan semua pelanggan
        $customers = $this->db->get('customers')->result();
    
        // Periksa status setiap pelanggan berdasarkan tanggal mulai dan berakhir
        foreach ($customers as $customer) {
            if (!empty($customer->start_date) && !empty($customer->end_date)) {
                $start_date = new DateTime($customer->start_date);
                $end_date = new DateTime($customer->end_date);
                $end_date_minus_one = clone $end_date;
                $end_date_minus_one->modify('-1 day');
    
                if ($today >= $start_date && $today <= $end_date_minus_one) {
                    $active_count++;
                } elseif ($today == $end_date) {
                    $suspend_count++;
                } elseif ($today > $end_date) {
                    $nonaktif_count++;
                }
            }
        }
    
        // Mengembalikan hasil perhitungan status
        return [
            'active' => $active_count,
            'suspend' => $suspend_count,
            'nonaktif' => $nonaktif_count
        ];
    }
        public function supplier_exists($kdsupplier) {
            $this->db->where('kdsupplier', $kdsupplier);  // Pastikan kita mengecek kdsupplier
            $query = $this->db->get('suppliers');
            return $query->num_rows() > 0; // Jika ada, return true
        }
    
    
}
