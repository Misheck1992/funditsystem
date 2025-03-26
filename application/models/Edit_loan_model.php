<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Edit_loan_model extends CI_Model
{

	public $table = 'edit_loan';
	
	public $id = 'edit_loan_id';
	public $order = 'DESC';

	function __construct()
	{
		parent::__construct();
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
	function update1($id, $data)
	{
		$this->db->where('loan_customer', $id);
		$this->db->update($this->table, $data);
	}

	// delete data
	function delete($id)
	{
		$this->db->where($this->id, $id);
		$this->db->delete($this->table);
	}

	// delete data
	function delete_data($id)
	{
		$this->db->where($this->id, $id);
		$this->db->delete($this->table_d);
	}
}

