<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role_model extends CI_Model {

    public function get_all_roles() {
        $query = $this->db->get('roles');
        return $query->result();
    }
}
