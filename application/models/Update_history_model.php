<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Update_history_model extends CI_Model {

    // Fungsi untuk menyimpan riwayat pembaruan baru
    public function save_update_history($version, $changes, $author) {
        $data = [
            'version' => $version,
            'update_date' => date('Y-m-d'),
            'changes' => $changes,
            'author' => $author
        ];

        $this->db->insert('update_history', $data);
    }

    // Fungsi untuk mengambil semua riwayat pembaruan
    public function get_all_updates() {
        $query = $this->db->get('update_history');
        return $query->result();
    }

    // Fungsi untuk menyimpan koreksi
    public function save_correction($id, $koreksi) {
        $data = [
            'koreksi' => $koreksi
        ];

        $this->db->where('id', $id);
        $this->db->update('update_history', $data);
    }

    // Fungsi untuk mengambil data pembaruan berdasarkan ID
    public function get_update_by_id($id) {
        $this->db->where('id', $id);
        return $this->db->get('update_history')->row();
    }
}
