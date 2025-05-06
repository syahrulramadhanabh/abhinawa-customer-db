<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class service_type_model extends CI_Model {

    // Mendapatkan semua jenis layanan
    public function get_all_service_types() {
        $sql = "SELECT * FROM service_types";
        $query = $this->db->query($sql);
        return $query->result();
    }

    // Menambahkan jenis layanan
    public function insert_service_type($data) {
        $sql = "INSERT INTO service_types (service_name, description) VALUES (?, ?)";
        return $this->db->query($sql, array($data['service_name'], $data['description']));
    }

    // Mendapatkan data jenis layanan berdasarkan ID
    public function get_service_type_by_id($id) {
        $sql = "SELECT * FROM service_types WHERE id = ?";
        $query = $this->db->query($sql, array($id));
        return $query->row();
    }

    // Mengedit jenis layanan
    public function update_service_type($id, $data) {
        $sql = "UPDATE service_types SET service_name = ?, description = ? WHERE id = ?";
        return $this->db->query($sql, array($data['service_name'], $data['description'], $id));
    }

    // Menghapus jenis layanan
    public function delete_service_type($id) {
        $sql = "DELETE FROM service_types WHERE id = ?";
        return $this->db->query($sql, array($id));
    }

    public function get_description_by_id($service_type_id) {
            $this->db->select('description');
            $this->db->where('id', $service_type_id);
            $query = $this->db->get('service_types');
    
            if ($query->num_rows() > 0) {
                return $query->row()->description;
            } else {
                return 'Description not available';
            }
        }
}
