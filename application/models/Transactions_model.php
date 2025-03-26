<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Transactions_model extends CI_Model
{

    public $table = 'transactions';
    public $id = 'transaction_id';
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
    function get_by_loan($id)
    {
        $this->db->where('loan_id', $id);
        return $this->db->get($this->table)->row();
    }
       function search($id)
    {
        $this->db->where('transactions.loan_id', $id);
        $this->db->where('transaction_type !=','4');
        $this->db->join('employees','employees.id=transactions.added_by');
        $this->db->join('loan', 'loan.loan_id=transactions.loan_id');
        $this->db->order_by('payment_number', ASC);
        return $this->db->get($this->table)->result();
    }
    function by_id($id)
    {
        $this->db->where('transactions.ref', $id);

        $this->db->join('transaction_type','transaction_type.transaction_type_id=transactions.transaction_type');

        return $this->db->get($this->table)->result();
    }
	
	public function sum_admin_charges($from,$to){
		$this->db->select('SUM(amount) as amount');
		$this->db->from('transactions')->where('transaction_type','1');

		// $this->db->join('lend_payments','lend_payments.borrower_loan_id=lend_borrower_loans.id');

		if($from !="" && $to !=""){
			$this->db->where('date_stamp BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

		}

		return $this->db->get()->row();
	}
	public function get_expenses($from,$to)
	{

		$this->db->select("*")
			->from($this->table)
			->join('loan', 'loan.loan_id=transactions.loan_id')
			->join('transaction_type', 'transaction_type.transaction_type_id=transactions.transaction_type')
			->join('employees', 'employees.id = transactions.added_by');
		$this->db->where('transactions.transaction_type', '4');

		if ($from != "" && $to != "") {
			$this->db->where('date_stamp BETWEEN "' . date('Y-m-d', strtotime($from)) . '" and "' . date('Y-m-d', strtotime($to)) . '"');

		}
		$re = $this->db->get()->result();
		return $re;
	}
	public function sum_admin_charges_late($from,$to){
		$this->db->select('SUM(amount) as amount');
		$this->db->from('transactions')->where('transaction_type','2');

		// $this->db->join('lend_payments','lend_payments.borrower_loan_id=lend_borrower_loans.id');

		if($from !="" && $to !=""){
			$this->db->where('date_stamp BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

		}

		return $this->db->get()->row();
	}public function sum_expenses($from,$to){
		$this->db->select('SUM(amount) as amount');
		$this->db->from('transactions')->where('transaction_type','4');

		// $this->db->join('lend_payments','lend_payments.borrower_loan_id=lend_borrower_loans.id');

		if($from !="" && $to !=""){
			$this->db->where('date_stamp BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

		}

		return $this->db->get()->row();
	}
    // get total rows
    function total_rows($q = NULL) {
        $this->db->like('transaction_id', $q);
	$this->db->or_like('ref', $q);
	$this->db->or_like('loan_id', $q);
	$this->db->or_like('amount', $q);
	$this->db->or_like('payment_number', $q);
	$this->db->or_like('date_stamp', $q);
	$this->db->from($this->table);
        return $this->db->count_all_results();
    }

    // get data with limit and search
    function get_limit_data($limit, $start = 0, $q = NULL) {
        $this->db->order_by($this->id, $this->order);
        $this->db->like('transaction_id', $q);
	$this->db->or_like('ref', $q);
	$this->db->or_like('loan_id', $q);
	$this->db->or_like('amount', $q);
	$this->db->or_like('payment_number', $q);
	$this->db->or_like('date_stamp', $q);
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
    function  report($type,$loan,$from,$to){

    	$this->db->select("*")
			->from($this->table)
			->join('loan','loan.loan_id=transactions.loan_id')
			->join('transaction_type','transaction_type.transaction_type_id=transactions.transaction_type')
		->join('employees','employees.id = transactions.added_by');
    	if($type !="All"){
    		$this->db->where('transactions.transaction_type',$type);
						 }
    	if($loan !="All"){
    		$this->db->where('loan.loan_product',$loan);
		}
		if($from !="" && $to !=""){
			$this->db->where('date_stamp BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

		}
		$re= $this->db->get()->result();
    	return $re;
	}


}


