<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Rescheduled_payments_model extends CI_Model
{

    public $table = 'rescheduled_payments';
    public $id = 'rescheduled_payments_id';
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
    public function new_pay($loan_number,$pay_number,$amount){
        $this->db->select("*")->from($this->table);
        $this->db->where('loan_id', $loan_number);
        $this->db->where('payment_number', $pay_number);
        $get_real_amount = $this->db->get()->row();
        $to_pay = $get_real_amount->payment_amount - $get_real_amount->paid_amount ;


        if(intval($to_pay) > intval($amount) ){


            $final_paid = $amount + $get_real_amount->paid_amount ;
            $data = array(
                'partial_paid'=>'YES',

                'paid_amount'=>$final_paid
            );
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $pay_number);
            $this->db->update($this->table,$data);

//            $transaction = array(
//                'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
//                'loan_id' => $loan_number,
//                'amount' => $final_paid,
//                'payment_number' => $pay_number,
//                'transaction_type' => 3,
//                'payment_proof' => 'null',
//                'added_by' => $this->session->userdata('user_id')
//
//            );
//            $this->db->insert('transactions',$transaction);
            return true;

        }
        elseif(intval($to_pay) === intval($amount)){


            $new_to_pay = $amount;
            $final_paid = $new_to_pay + $get_real_amount->paid_amount ;

            $data = array(
                'partial_paid'=>'NO',
                'pay_status'=>'PAID',
                'paid_amount'=>$final_paid
            );
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $pay_number);
            $this->db->update($this->table,$data);
            $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id_rescheduled'=>$pay_number+1));
            $count_schedules = $this->count_payments($loan_number);
            if(intval($count_schedules) == intval($pay_number)){
                $this->db->where('loan_id', $loan_number)->
                update('loan',array('loan_status'=>'CLOSED'));
            }
//            $transaction = array(
//                'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
//                'loan_id' => $loan_number,
//                'amount' => $final_paid,
//                'payment_number' => $pay_number,
//                'transaction_type' => 3,
//                'payment_proof' => 'null',
//                'added_by' => $this->session->userdata('user_id')
//
//            );
//            $this->db->insert('transactions',$transaction);
            return true;

        }
        else{

        }
    }
    public function new__late_pay($loan_number,$pay_number,$amount){
        $this->db->select("*")->from($this->table);
        $this->db->where('loan_id', $loan_number);
        $this->db->where('payment_number', $pay_number);
        $get_real_amount = $this->db->get()->row();
        $to_pay = $get_real_amount->payment_amount - $get_real_amount->paid_amount ;


        if(intval($to_pay) > intval($amount) ){


            $final_paid = $amount + $get_real_amount->paid_amount ;
            $data = array(
                'partial_paid'=>'YES',

                'paid_amount'=>$final_paid
            );
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $pay_number);
            $this->db->update($this->table,$data);

//            $transaction = array(
//                'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
//                'loan_id' => $loan_number,
//                'amount' => $final_paid,
//                'payment_number' => $pay_number,
//                'transaction_type' => 3,
//                'payment_proof' => 'null',
//                'added_by' => $this->session->userdata('user_id')
//
//            );
//            $this->db->insert('transactions',$transaction);
            return true;

        }
        elseif(intval($to_pay) === intval($amount)){


            $new_to_pay = $amount;
            $final_paid = $new_to_pay + $get_real_amount->paid_amount ;

            $data = array(
                'partial_paid'=>'NO',
                'pay_status'=>'PAID',
                'paid_amount'=>$final_paid
            );
            $this->db->where('loan_id', $loan_number);
            $this->db->where('payment_number', $pay_number);
            $this->db->update($this->table,$data);
            $this->db->where('loan_id',$loan_number)->update('loan',array('next_payment_id_rescheduled'=>$pay_number+1));
            $count_schedules = $this->count_payments($loan_number);
            if(intval($count_schedules) == intval($pay_number)){
                $this->db->where('loan_id', $loan_number)->
                update('loan',array('loan_status'=>'CLOSED'));
            }
//            $transaction = array(
//                'ref' => "GF.".date('Y').date('m').date('d').'.'.rand(100,999),
//                'loan_id' => $loan_number,
//                'amount' => $final_paid,
//                'payment_number' => $pay_number,
//                'transaction_type' => 3,
//                'payment_proof' => 'null',
//                'added_by' => $this->session->userdata('user_id')
//
//            );
//            $this->db->insert('transactions',$transaction);
            return true;

        }
        else{

        }
    }


    function count_payments($loan_number){
        $this->db->select("*")->from($this->table);
        $this->db->where('loan_id', $loan_number);
        return $this->db->count_all_results();
    }
    // get data by id
    function get_by_id($id)
    {
        $this->db->where($this->id, $id);
        return $this->db->get($this->table)->row();
    }
    
    // get total rows
    function total_rows($q = NULL) {
        $this->db->like('rescheduled_payments_id', $q);
	$this->db->or_like('loan_id', $q);
	$this->db->or_like('customer_id', $q);
	$this->db->or_like('customer_type', $q);
	$this->db->or_like('payment_number', $q);
	$this->db->or_like('payment_amount', $q);
	$this->db->or_like('payment_date', $q);
	$this->db->or_like('pay_status', $q);
	$this->db->or_like('paid_amount', $q);
	$this->db->or_like('p_stamp', $q);
	$this->db->from($this->table);
        return $this->db->count_all_results();
    }

    // get data with limit and search
    function get_limit_data($limit, $start = 0, $q = NULL) {
        $this->db->order_by($this->id, $this->order);
        $this->db->like('rescheduled_payments_id', $q);
	$this->db->or_like('loan_id', $q);
	$this->db->or_like('customer_id', $q);
	$this->db->or_like('customer_type', $q);
	$this->db->or_like('payment_number', $q);
	$this->db->or_like('payment_amount', $q);
	$this->db->or_like('payment_date', $q);
	$this->db->or_like('pay_status', $q);
	$this->db->or_like('paid_amount', $q);
	$this->db->or_like('p_stamp', $q);
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

/* End of file Rescheduled_payments_model.php */
/* Location: ./application/models/Rescheduled_payments_model.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2023-03-09 14:09:42 */
/* http://harviacode.com */