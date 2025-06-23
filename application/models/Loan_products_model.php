<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Loan_products_model extends CI_Model
{

    public $table = 'loan_products';
    public $id = 'loan_product_id';
    public $order = 'DESC';

    function __construct()
    {
        parent::__construct();
    }

    // get all
    function get_all()
    {
        $this->db->order_by($this->id, $this->order);
        return $this->db->get($this->table)->result();
    }

    // get data by id
    function get_by_id($id)
    {
        $this->db->where($this->id, $id);
        return $this->db->get($this->table)->row();
    }

    // get total rows
    function total_rows($q = NULL) {
        // Only apply search filters if $q is not null or empty
        if (!empty($q)) {
            $this->db->like('loan_product_id', $q);
            $this->db->or_like('product_name', $q);
            $this->db->or_like('interest', $q);
            $this->db->or_like('frequency', $q);
            $this->db->or_like('penalty', $q);
            $this->db->or_like('penalty_threshold', $q);
            $this->db->or_like('penalty_charge_type_below', $q);
            $this->db->or_like('penalty_charge_type_above', $q);
            $this->db->or_like('penalty_fixed_charge_below', $q);
            $this->db->or_like('penalty_variable_charge_below', $q);
            $this->db->or_like('penalty_fixed_charge_above', $q);
            $this->db->or_like('penalty_variable_charge_above', $q);
            $this->db->or_like('loan_processing_fee_threshold', $q);
            $this->db->or_like('processing_charge_type_above', $q);
            $this->db->or_like('processing_charge_type_below', $q);
            $this->db->or_like('processing_fixed_charge_above', $q);
            $this->db->or_like('processing_variable_charge_above', $q);
            $this->db->or_like('processing_fixed_charge_below', $q);
            $this->db->or_like('processing_variable_charge_below', $q);
            $this->db->or_like('minimum_principal', $q);
            $this->db->or_like('maximum_principal', $q);
            $this->db->or_like('interest_min', $q);
            $this->db->or_like('interest_max', $q);
            $this->db->or_like('added_by', $q);
            $this->db->or_like('date_created', $q);
        }
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    // get data with limit and search
    function get_limit_data($limit, $start = 0, $q = NULL) {
        $this->db->order_by($this->id, $this->order);

        // Only apply search filters if $q is not null or empty
        if (!empty($q)) {
            $this->db->like('loan_product_id', $q);
            $this->db->or_like('product_name', $q);
            $this->db->or_like('interest', $q);
            $this->db->or_like('frequency', $q);
            $this->db->or_like('penalty', $q);
            $this->db->or_like('penalty_threshold', $q);
            $this->db->or_like('penalty_fixed_charge_below', $q);
            $this->db->or_like('penalty_variable_charge_below', $q);
            $this->db->or_like('penalty_fixed_charge_above', $q);
            $this->db->or_like('penalty_variable_charge_above', $q);
            $this->db->or_like('loan_processing_fee_threshold', $q);
            $this->db->or_like('processing_fixed_charge_above', $q);
            $this->db->or_like('processing_variable_charge_above', $q);
            $this->db->or_like('processing_fixed_charge_below', $q);
            $this->db->or_like('processing_variable_charge_below', $q);
            $this->db->or_like('minimum_principal', $q);
            $this->db->or_like('maximum_principal', $q);
            $this->db->or_like('interest_min', $q);
            $this->db->or_like('interest_max', $q);
            $this->db->or_like('added_by', $q);
            $this->db->or_like('date_created', $q);
        }

        $this->db->limit($limit, $start);
        return $this->db->get($this->table)->result();
    }

    // insert data
    function insert($data)
    {
        $this->db->insert($this->table, $data);
    }

    // update data
    function update($id, $data)
    {
        $this->db->where($this->id, $id);
        $this->db->update($this->table, $data);
    }

    // delete data
    function delete($id)
    {
        $this->db->where($this->id, $id);
        $this->db->delete($this->table);
    }
}