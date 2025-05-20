<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Customer_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ambil semua grup pelanggan
     *
     * @return array
     */
    public function get_all_customer_groups()
    {
        return $this->db->get('customer_groups')->result();
    }
    public function get_all_customers() {
        $query = $this->db
            ->select('*')           // termasuk kolom sla
            ->from('customers')
            ->get();
        return $query->result();
    }
    /**
     * Ambil semua customer dalam satu grup beserta info tambahan
     *
     * @param  int   $group_id
     * @return array
     */
    public function get_customers_by_group($group_id)
    {
        $sql = "SELECT 
                    c.*, 
                    s.nama_supplier, 
                    sd.cid_supplier AS supplier_cid, 
                    cg.group_name, 
                    st.service_name AS service_type_name
                FROM customers c
                LEFT JOIN suppliers s  ON c.kdsupplier           = s.kdsupplier
                LEFT JOIN supplier_detail sd ON c.cid_supp       = sd.cid_supplier
                LEFT JOIN customer_groups cg ON c.customer_group_id = cg.id
                LEFT JOIN service_types st  ON c.service_type_id    = st.id
                WHERE c.customer_group_id = ?";
        return $this->db->query($sql, [$group_id])->result();
    }

    /**
     * Ambil data grup untuk pagination dan search
     */
    public function get_customer_groups($limit, $start, $search = '')
    {
        $this->db->select('cg.*, sd.cid_supplier AS supplier_cid')
                 ->from('customer_groups cg')
                 ->join('suppliers s', 'cg.kdsupplier = s.kdsupplier', 'left')
                 ->join('supplier_detail sd','s.kdsupplier = sd.kdsupplier','left');

        if ($search !== '') {
            $this->db->group_start()
                     ->like('cg.group_name', $search)
                     ->or_like('sd.cid_supplier', $search)
                     ->group_end();
        }

        $this->db->limit($limit, $start);
        return $this->db->get()->result();
    }

    /**
     * Hitung jumlah grup untuk pagination
     */
    public function count_customer_groups($search = '')
    {
        $this->db->from('customer_groups cg')
                 ->join('suppliers s', 'cg.kdsupplier = s.kdsupplier', 'left')
                 ->join('supplier_detail sd','s.kdsupplier = sd.kdsupplier','left');

        if ($search !== '') {
            $this->db->group_start()
                     ->like('cg.group_name', $search)
                     ->or_like('sd.cid_supplier', $search)
                     ->group_end();
        }

        return $this->db->count_all_results();
    }

    /**
     * Ambil semua supplier
     *
     * @return array
     */
            public function get_all_suppliers() {
                $this->db->select('kdsupplier, nama_supplier');
                $query = $this->db->get('suppliers');
                return $query->result();
            }

            public function get_cid_suppliers($kdsupplier) {
                $this->db->select('cid_supplier');
                $this->db->where('kdsupplier', $kdsupplier);
                $query = $this->db->get('supplier_detail');
                return $query->result();
            }



    /**
     * Ambil semua jenis layanan
     *
     * @return array
     */
    public function get_all_service_types()
    {
        return $this->db->get('service_types')->result();
    }

    /**
     * Insert customer baru dan kembalikan insert_id
     *
     * @param  array       $data
     * @return int|false
     */
    public function insert_customer(array $data)
    {
        if ($this->db->insert('customers', $data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    /**
     * Ambil total customer (untuk dashboard/statistik)
     */
    public function get_customer_count()
    {
        return $this->db->count_all('customers');
    }

    public function get_supplier_count()
    {
        return $this->db->count_all('suppliers');
    }

    public function get_customer_status_count($status)
    {
        return $this->db->where('status', $status)
                        ->from('customers')
                        ->count_all_results();
    }

    /**
     * Ambil CID supplier yang belum dipakai di table customers
     */
    public function get_unused_cid_suppliers()
    {
        $sql = "SELECT sd.cid_supplier
                FROM supplier_detail sd
                LEFT JOIN customers c ON sd.cid_supplier = c.cid_supp
                WHERE c.cid_supp IS NULL";
        return $this->db->query($sql)->result();
    }

    /**
     * Ambil satu customer by ID
     *
     * @param  int      $customer_id
     * @return object|null
     */
    public function get_customer_by_id($customer_id)
    {
        return $this->db->get_where('customers', ['id' => $customer_id])->row();
    }

    /**
     * Update data customer
     */
    public function update_customer($customer_id, array $data)
    {
        return $this->db->where('id', $customer_id)
                        ->update('customers', $data);
    }

    /**
     * Hapus customer
     */
    public function delete_customer($customer_id)
    {
        return $this->db->where('id', $customer_id)
                        ->delete('customers');
    }

    /**
     * Hitung status customer (Active, Suspend, Nonaktif)
     */
    public function get_customer_status_counts()
    {
        $active = $suspend = $nonaktif = 0;
        $today  = new DateTime();
        $all    = $this->db->get('customers')->result();

        foreach ($all as $c) {
            if ($c->start_date && $c->end_date) {
                $start = new DateTime($c->start_date);
                $end   = new DateTime($c->end_date);
                if ($today >= $start && $today <  $end) {
                    $active++;
                } elseif ($today == $end) {
                    $suspend++;
                } elseif ($today > $end) {
                    $nonaktif++;
                }
            }
        }

        return ['active'=>$active, 'suspend'=>$suspend, 'nonaktif'=>$nonaktif];
    }

    /**
     * Cek apakah supplier sudah ada
     */
    public function supplier_exists($kdsupplier)
    {
        return (bool)$this->db->where('kdsupplier',$kdsupplier)
                              ->from('suppliers')
                              ->count_all_results();
    }

    /**
     * Ambil semua customer yang end_date = hari ini
     *
     * @return array
     */
    public function get_customers_with_end_date_today()
    {
        $today = date('Y-m-d');
        return $this->db
            ->select('
                c.id,
                c.customer,
                c.cid_abh,
                c.service_type_id,
                st.service_name AS service_type_name,
                c.start_date,
                c.end_date,
                c.customer_group_id,
                cg.group_name
            ')
            ->from('customers c')
            ->join('service_types st',       'c.service_type_id  = st.id', 'left')
            ->join('customer_groups cg',      'c.customer_group_id= cg.id','left')
            ->where('c.end_date', $today)
            ->get()
            ->result();
    }

    /**
     * Ambil semua customer yang start_date = hari ini (untuk notifikasi baru)
     *
     * @return array
     */
    public function get_customers_added_today()
    {
        $today = date('Y-m-d');
        return $this->db->where('start_date', $today)
                        ->get('customers')
                        ->result();
    }

} // end class Customer_model
