<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Fixed Deposit Customers Model
 * Handles CRUD operations for FD customers
 */
class Fd_customers_model extends CI_Model
{
    public $table = 'fd_customers';
    public $id = 'id';
    public $order = 'DESC';

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all customers
     */
    function get_all()
    {
        $this->db->order_by($this->id, $this->order);
        return $this->db->get($this->table)->result();
    }

    /**
     * Get customer by ID
     */
    function get_by_id($id)
    {
        $this->db->where($this->id, $id);
        return $this->db->get($this->table)->row();
    }

    /**
     * Get customer by customer number
     */
    function get_by_number($customer_number)
    {
        $this->db->where('customer_number', $customer_number);
        return $this->db->get($this->table)->row();
    }

    /**
     * Get total rows with optional search
     */
    function total_rows($q = NULL)
    {
        if ($q) {
            $this->db->group_start();
            $this->db->like('customer_number', $q);
            $this->db->or_like('first_name', $q);
            $this->db->or_like('last_name', $q);
            $this->db->or_like('phone_number', $q);
            $this->db->or_like('email', $q);
            $this->db->or_like('id_number', $q);
            $this->db->group_end();
        }
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    /**
     * Get paginated data with search
     */
    function get_limit_data($limit, $start = 0, $q = NULL)
    {
        if ($q) {
            $this->db->group_start();
            $this->db->like('customer_number', $q);
            $this->db->or_like('first_name', $q);
            $this->db->or_like('last_name', $q);
            $this->db->or_like('phone_number', $q);
            $this->db->or_like('email', $q);
            $this->db->or_like('id_number', $q);
            $this->db->group_end();
        }
        $this->db->order_by($this->id, $this->order);
        $this->db->limit($limit, $start);
        return $this->db->get($this->table)->result();
    }

    /**
     * Insert new customer
     */
    function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update customer
     */
    function update($id, $data)
    {
        $this->db->where($this->id, $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Delete customer
     */
    function delete($id)
    {
        $this->db->where($this->id, $id);
        return $this->db->delete($this->table);
    }

    /**
     * Generate unique customer number
     */
    function generate_customer_number()
    {
        $this->db->select_max('id');
        $query = $this->db->get($this->table);
        $row = $query->row();

        $next_id = ($row && $row->id) ? $row->id + 1 : 1;
        return 'FDC' . str_pad($next_id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get active customers
     */
    function get_active_customers()
    {
        $this->db->where('status', 'ACTIVE');
        $this->db->order_by('first_name', 'ASC');
        return $this->db->get($this->table)->result();
    }

    /**
     * Get customers for dropdown
     */
    function get_for_dropdown()
    {
        $this->db->select('id, customer_number, first_name, last_name');
        $this->db->where('status', 'ACTIVE');
        $this->db->order_by('first_name', 'ASC');
        return $this->db->get($this->table)->result();
    }

    /**
     * Search customers
     */
    function search($term, $limit = 10)
    {
        $this->db->select('id, customer_number, first_name, last_name, phone_number');
        $this->db->group_start();
        $this->db->like('customer_number', $term);
        $this->db->or_like('first_name', $term);
        $this->db->or_like('last_name', $term);
        $this->db->or_like('phone_number', $term);
        $this->db->group_end();
        $this->db->where('status', 'ACTIVE');
        $this->db->limit($limit);
        return $this->db->get($this->table)->result();
    }

    /**
     * Count all customers
     */
    function count_all()
    {
        return $this->db->count_all($this->table);
    }

    /**
     * Count active customers
     */
    function count_active()
    {
        $this->db->where('status', 'ACTIVE');
        return $this->db->count_all_results($this->table);
    }

    /**
     * Get FD customer linked to an individual customer
     */
    function get_by_personal_linkage($individual_id)
    {
        $this->db->where('personal_linkage', $individual_id);
        return $this->db->get($this->table)->row();
    }

    /**
     * Get FD customer by email
     */
    function get_by_email($email)
    {
        $this->db->where('email', $email);
        return $this->db->get($this->table)->row();
    }
}
