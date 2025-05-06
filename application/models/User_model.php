<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function get_all_users() {
        $this->db->select('users.id, users.username, users.created_at, roles.role_name');
        $this->db->from('users');
        $this->db->join('roles', 'users.role_id = roles.id');
        $query = $this->db->get();
        return $query->result();
    }

    public function create_user($username, $password, $role_id) {
        $data = [
            'username' => $username,
            'password' => $password,
            'role_id' => $role_id
        ];
        $this->db->insert('users', $data);
    }

    public function get_user_by_id($id) {
        return $this->db->get_where('users', ['id' => $id])->row();
    }

    public function update_role($id, $role_id) {
        $this->db->where('id', $id);
        $this->db->update('users', ['role_id' => $role_id]);
    }

    public function delete_user($id) {
        $this->db->delete('users', ['id' => $id]);
    }
}
