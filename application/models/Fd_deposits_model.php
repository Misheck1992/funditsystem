<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Fixed Deposit Deposits Model
 * Handles CRUD operations for FD deposits
 */
class Fd_deposits_model extends CI_Model
{
    public $table = 'fd_deposits';
    public $id = 'id';
    public $order = 'DESC';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all deposits
     */
    function get_all()
    {
        $this->db->order_by($this->id, $this->order);
        return $this->db->get($this->table)->result();
    }

    /**
     * Get deposit by ID
     */
    function get_by_id($id)
    {
        $this->db->where($this->id, $id);
        return $this->db->get($this->table)->row();
    }

    /**
     * Get deposit by deposit number
     */
    function get_by_number($deposit_number)
    {
        $this->db->where('deposit_number', $deposit_number);
        return $this->db->get($this->table)->row();
    }

    /**
     * Get total rows with optional search
     */
    function total_rows($q = NULL, $status = NULL)
    {
        if ($q) {
            $this->db->group_start();
            $this->db->like('d.deposit_number', $q);
            $this->db->or_like('c.customer_number', $q);
            $this->db->or_like('c.first_name', $q);
            $this->db->or_like('c.last_name', $q);
            $this->db->group_end();
        }
        if ($status) {
            $this->db->where('d.status', $status);
        }
        $this->db->from($this->table . ' d');
        $this->db->join('fd_customers c', 'c.id = d.customer_id', 'left');
        return $this->db->count_all_results();
    }

    /**
     * Get paginated data with search
     */
    function get_limit_data($limit, $start = 0, $q = NULL, $status = NULL)
    {
        $this->db->select('d.*, c.customer_number, c.first_name, c.last_name, c.phone_number');
        if ($q) {
            $this->db->group_start();
            $this->db->like('d.deposit_number', $q);
            $this->db->or_like('c.customer_number', $q);
            $this->db->or_like('c.first_name', $q);
            $this->db->or_like('c.last_name', $q);
            $this->db->group_end();
        }
        if ($status) {
            $this->db->where('d.status', $status);
        }
        $this->db->from($this->table . ' d');
        $this->db->join('fd_customers c', 'c.id = d.customer_id', 'left');
        $this->db->order_by('d.' . $this->id, $this->order);
        $this->db->limit($limit, $start);
        return $this->db->get()->result();
    }

    /**
     * Insert new deposit
     */
    function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update deposit
     */
    function update($id, $data)
    {
        $this->db->where($this->id, $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Delete deposit
     */
    function delete($id)
    {
        $this->db->where($this->id, $id);
        return $this->db->delete($this->table);
    }

    /**
     * Generate unique deposit number
     */
    function generate_deposit_number()
    {
        $this->db->select_max('id');
        $query = $this->db->get($this->table);
        $row = $query->row();

        $next_id = ($row && $row->id) ? $row->id + 1 : 1;
        return 'FDD' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get deposits by customer
     */
    function get_by_customer($customer_id)
    {
        $this->db->where('customer_id', $customer_id);
        $this->db->order_by($this->id, $this->order);
        return $this->db->get($this->table)->result();
    }

    /**
     * Get active deposits by customer
     */
    function get_active_by_customer($customer_id)
    {
        $this->db->where('customer_id', $customer_id);
        $this->db->where('status', 'ACTIVE');
        $this->db->order_by($this->id, $this->order);
        return $this->db->get($this->table)->result();
    }

    /**
     * Get all active deposits
     */
    function get_active_deposits()
    {
        $this->db->select('d.*, c.customer_number, c.first_name, c.last_name, c.phone_number');
        $this->db->from($this->table . ' d');
        $this->db->join('fd_customers c', 'c.id = d.customer_id', 'left');
        $this->db->where('d.status', 'ACTIVE');
        $this->db->order_by('d.' . $this->id, $this->order);
        return $this->db->get()->result();
    }

    /**
     * Get deposits maturing within X days
     */
    function get_maturing_deposits($days = 30)
    {
        $future_date = date('Y-m-d', strtotime("+{$days} days"));

        $this->db->select('d.*, c.customer_number, c.first_name, c.last_name, c.phone_number, c.email');
        $this->db->from($this->table . ' d');
        $this->db->join('fd_customers c', 'c.id = d.customer_id', 'left');
        $this->db->where('d.status', 'ACTIVE');
        $this->db->where('d.maturity_date <=', $future_date);
        $this->db->where('d.maturity_date >=', date('Y-m-d'));
        $this->db->order_by('d.maturity_date', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get matured deposits (past maturity date but still active)
     */
    function get_matured_deposits()
    {
        $this->db->select('d.*, c.customer_number, c.first_name, c.last_name, c.phone_number');
        $this->db->from($this->table . ' d');
        $this->db->join('fd_customers c', 'c.id = d.customer_id', 'left');
        $this->db->where('d.status', 'ACTIVE');
        $this->db->where('d.maturity_date <', date('Y-m-d'));
        $this->db->order_by('d.maturity_date', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get deposit with customer data
     */
    function get_deposit_with_customer($id)
    {
        $this->db->select('d.*, c.customer_number, c.first_name, c.last_name, c.phone_number, c.email, c.gender, c.province, c.address, c.id_type, c.id_number');
        $this->db->from($this->table . ' d');
        $this->db->join('fd_customers c', 'c.id = d.customer_id', 'left');
        $this->db->where('d.id', $id);
        return $this->db->get()->row();
    }

    /**
     * Calculate maturity date
     */
    function calculate_maturity_date($start_date, $months)
    {
        $date = new DateTime($start_date);
        $date->modify("+{$months} months");
        return $date->format('Y-m-d');
    }

    /**
     * Get total principal for active deposits
     */
    function get_total_active_principal()
    {
        $this->db->select_sum('current_principal');
        $this->db->where('status', 'ACTIVE');
        $result = $this->db->get($this->table)->row();
        return $result->current_principal ? $result->current_principal : 0;
    }

    /**
     * Get total accrued interest for active deposits
     */
    function get_total_accrued_interest()
    {
        $this->db->select_sum('accrued_interest');
        $this->db->where('status', 'ACTIVE');
        $result = $this->db->get($this->table)->row();
        return $result->accrued_interest ? $result->accrued_interest : 0;
    }

    /**
     * Count deposits by status
     */
    function count_by_status($status)
    {
        $this->db->where('status', $status);
        return $this->db->count_all_results($this->table);
    }

    /**
     * Count all deposits
     */
    function count_all()
    {
        return $this->db->count_all($this->table);
    }

    /**
     * Get deposits for merge (active deposits by customer excluding one)
     */
    function get_deposits_for_merge($customer_id, $exclude_id = null)
    {
        $this->db->where('customer_id', $customer_id);
        $this->db->where('status', 'ACTIVE');
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        $this->db->order_by('deposit_number', 'ASC');
        return $this->db->get($this->table)->result();
    }

    /**
     * Get summary statistics
     */
    function get_summary_stats()
    {
        $stats = array();

        // Total active deposits
        $this->db->where('status', 'ACTIVE');
        $stats['active_count'] = $this->db->count_all_results($this->table);

        // Total principal
        $this->db->select_sum('current_principal', 'total');
        $this->db->where('status', 'ACTIVE');
        $result = $this->db->get($this->table)->row();
        $stats['total_principal'] = $result->total ? $result->total : 0;

        // Total accrued interest
        $this->db->select_sum('accrued_interest', 'total');
        $this->db->where('status', 'ACTIVE');
        $result = $this->db->get($this->table)->row();
        $stats['total_accrued'] = $result->total ? $result->total : 0;

        // Maturing in 30 days
        $future_date = date('Y-m-d', strtotime('+30 days'));
        $this->db->where('status', 'ACTIVE');
        $this->db->where('maturity_date <=', $future_date);
        $this->db->where('maturity_date >=', date('Y-m-d'));
        $stats['maturing_soon'] = $this->db->count_all_results($this->table);

        return $stats;
    }
}
