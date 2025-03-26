<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Previous_loans_model extends CI_Model
{

    public $table = 'previous_loans';
    public $id = 'p_lid';
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
		$this->db->select("*")->from($this->table);
        $this->db->like('p_lid', $q);
	$this->db->or_like('previous_loans.customer_id', $q);
	$this->db->or_like('previous_loans.loan_effective_date', $q);
	$this->db->or_like('previous_loans.loan_end_date', $q);
	$this->db->or_like('previous_loans.amount', $q);
	$this->db->or_like('previous_loans.status', $q);
//	$this->db->or_like('added_by', $q);
//	$this->db->or_like('stamp', $q);

        return $this->db->count_all_results();
    }

    // get data with limit and search
    function get_limit_data($limit, $start = 0, $q = NULL) {
        $this->db->order_by($this->id, $this->order);
		$this->db->select("*")->from($this->table);
        $this->db->like('p_lid', $q);
	$this->db->or_like('previous_loans.customer_id', $q);
	$this->db->or_like('previous_loans.loan_effective_date', $q);
	$this->db->or_like('previous_loans.loan_end_date', $q);
	$this->db->or_like('previous_loans.amount', $q);
	$this->db->or_like('previous_loans.status', $q);
//	$this->db->or_like('added_by', $q);
//	$this->db->or_like('stamp', $q);
	$this->db->limit($limit, $start);
        return $this->db->get()->result();
    }

    // insert data
    function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
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

