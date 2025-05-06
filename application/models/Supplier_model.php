<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Supplier_model extends CI_Model {

    // Mendapatkan supplier berdasarkan ID, termasuk data detail dari tabel supplier_detail
    public function get_supplier_by_id($id) {
        // Mengambil data supplier utama
        $this->db->select('*');
        $this->db->from('suppliers');
        $this->db->where('kdsupplier', $id);
        $query = $this->db->get();
        $supplier = $query->row();

        if ($supplier) {
            // Mengambil data detail untuk supplier terkait dari supplier_detail
            $supplier->details = $this->get_supplier_details($id);
        }

        return $supplier;
    }

    // Fungsi untuk memeriksa keberadaan supplier berdasarkan nama
    public function supplier_exists($nama_supplier) {
        $this->db->from('suppliers');
        $this->db->where('nama_supplier', $nama_supplier);
        return $this->db->count_all_results() > 0;
    }
    
    public function get_all_suppliers() {
        return $this->db->get('suppliers')->result();
    }

    // Update data supplier di tabel suppliers
    public function update_supplier($id, $data) {
        $supplier_data = [
            'kdsupplier' => $data['kdsupplier'] ?? NULL,
            'nama_supplier' => $data['nama_supplier'] ?? NULL,
        ];

        $this->db->where('kdsupplier', $id);
        return $this->db->update('suppliers', $supplier_data);
    }

    // Hapus supplier dari tabel suppliers dan detail terkait di supplier_detail
    public function delete_supplier($id) {
        // Hapus data di supplier_detail terlebih dahulu
        $this->db->where('kdsupplier', $id);
        $this->db->delete('supplier_detail');

        // Hapus data di suppliers
        $this->db->where('kdsupplier', $id);
        return $this->db->delete('suppliers');
    }

    public function get_all_service_types() {
        return $this->db->get('service_types')->result();
    }
    
    public function insert_supplier($data) {
        if ($this->supplier_exists($data['nama_supplier'])) {
            return false;
        }
        return $this->db->insert('suppliers', $data);
    }
    public function get_supplier_by_kdsupplier($kdsupplier) {
        $query = $this->db->get_where('suppliers', ['kdsupplier' => $kdsupplier]);
        return $query->row();
    }
    
    public function get_supplier_details($kdsupplier) {
        $query = $this->db->get_where('supplier_detail', ['kdsupplier' => $kdsupplier]);
        return $query->result();
    }
    public function get_supplier_summary() {
        $this->db->select('kdsupplier, nama_supplier');
        $query = $this->db->get('suppliers');
        return $query->result();
    }
    public function insert_supplier_detail($data) {
        $this->db->insert('supplier_detail', $data);
    }
    
}