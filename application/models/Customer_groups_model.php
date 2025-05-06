<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class customer_groups_model extends CI_Model {

    // Get all customer groups
    public function get_all_customer_groups() {
        $sql = "SELECT * FROM customer_groups";
        $query = $this->db->query($sql);
        return $query->result();
    }

    // Get customer group by ID
    public function get_customer_group_by_id($id) {
        $sql = "SELECT * FROM customer_groups WHERE id = ?";
        $query = $this->db->query($sql, array($id));
        return $query->row();
    }

    // Insert new customer group
    public function insert_customer_group($data) {
        $sql = "INSERT INTO customer_groups (group_name, description) VALUES (?, ?)";
        return $this->db->query($sql, array($data['group_name'], $data['description']));
    }

    // Update customer group by ID
    public function update_customer_group($id, $data) {
        $sql = "UPDATE customer_groups SET group_name = ?, description = ? WHERE id = ?";
        return $this->db->query($sql, array($data['group_name'], $data['description'], $id));
    }

    // Delete customer group by ID
    public function delete_customer_group($id) {
        $sql = "DELETE FROM customer_groups WHERE id = ?";
        return $this->db->query($sql, array($id));
    }
}
