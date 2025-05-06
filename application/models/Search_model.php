<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Search_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_search_results($search_category, $search_query) {
        $search_query = $this->db->escape_like_str($search_query);

        // Initialize results array
        $results = [
            'customer_groups' => [],
            'customers' => [],
            'suppliers' => [],
            'supplier_detail' => [],
            'service_types' => []
        ];

        // If specific category is selected, only query that table
        if ($search_category !== 'all' && array_key_exists($search_category, $this->_get_search_configs())) {
            $config = $this->_get_search_configs()[$search_category];
            $results[$config['result_key']] = $this->_execute_search($config, $search_query);
            return $results;
        }

        // Search all categories
        foreach ($this->_get_search_configs() as $config) {
            $results[$config['result_key']] = $this->_execute_search($config, $search_query);
        }

        return $results;
    }

    private function _execute_search($config, $search_query) {
        $this->db->select($config['select']);
        $this->db->from($config['table']);

        // Handle multiple search fields
        if (is_array($config['search_field'])) {
            $this->db->group_start();
            foreach ($config['search_field'] as $field) {
                $this->db->or_like($field, $search_query);
            }
            $this->db->group_end();
        } else {
            $this->db->like($config['search_field'], $search_query);
        }

        // Add joins if defined
        if (isset($config['joins']) && is_array($config['joins'])) {
            foreach ($config['joins'] as $join) {
                $this->db->join($join[0], $join[1], $join[2]);
            }
        }

        // Add limit for performance
        $this->db->limit(100);

        return $this->db->get()->result();
    }

    private function _get_search_configs() {
        return [
            'customer_group' => [
                'table' => 'customer_groups',
                'select' => 'id, group_name',
                'search_field' => 'group_name',
                'result_key' => 'customer_groups'
            ],
            'customer' => [
                'table' => 'customers',
                'select' => 'id, customer, kdsupplier, cid_supp',  
                'search_field' => ['customer', 'kdsupplier', 'cid_supp'],  
                'result_key' => 'customers'
            ],
            'supplier' => [
                'table' => 'suppliers',
                'select' => 'kdsupplier, nama_supplier',
                'search_field' => 'nama_supplier',
                'result_key' => 'suppliers'
            ],
            'supplier_detail' => [
                'table' => 'supplier_detail',
                'select' => 'cid_supplier, start_service, end_service, service_type_supplier, sdn, topology_supplier, contact, eskalasi_matrix',
                'search_field' => 'cid_supplier',
                'result_key' => 'supplier_detail'
            ],
            'service_type' => [
                'table' => 'service_types',
                'select' => 'service_name, service_type',
                'search_field' => 'service_type',
                'result_key' => 'service_types'
            ]
        ];
    }
}
