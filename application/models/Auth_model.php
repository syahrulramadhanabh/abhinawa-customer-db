<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {

    public function check_user($username, $password) {
        $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
        $query = $this->db->query($sql, array($username, $password));
        return $query->row(); // mengembalikan objek pengguna dengan role_id
    }

    public function update_password($user_id, $new_password) {
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $this->db->query($sql, array($new_password, $user_id));
    }
}
