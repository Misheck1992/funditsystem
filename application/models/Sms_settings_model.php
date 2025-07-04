<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sms_settings_model extends CI_Model
{

    public $table = 'sms_settings';
    public $id = 'id';
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
        $this->db->like('id', $q);
	$this->db->or_like('customer_approval', $q);
	$this->db->or_like('group_approval', $q);
	$this->db->or_like('loan_disbursement', $q);
	$this->db->or_like('before_notice', $q);
	$this->db->or_like('before_notice_period', $q);
	$this->db->or_like('arrears', $q);
	$this->db->or_like('arrears_age', $q);
	$this->db->or_like('customer_app_pass_recovery', $q);
	$this->db->or_like('customer_birthday_notify', $q);
	$this->db->or_like('loan_payment_notification', $q);
	$this->db->or_like('deposit_withdraw_notification', $q);
	$this->db->from($this->table);
        return $this->db->count_all_results();
    }

    // get data with limit and search
    function get_limit_data($limit, $start = 0, $q = NULL) {
        $this->db->order_by($this->id, $this->order);
        $this->db->like('id', $q);
	$this->db->or_like('customer_approval', $q);
	$this->db->or_like('group_approval', $q);
	$this->db->or_like('loan_disbursement', $q);
	$this->db->or_like('before_notice', $q);
	$this->db->or_like('before_notice_period', $q);
	$this->db->or_like('arrears', $q);
	$this->db->or_like('arrears_age', $q);
	$this->db->or_like('customer_app_pass_recovery', $q);
	$this->db->or_like('customer_birthday_notify', $q);
	$this->db->or_like('loan_payment_notification', $q);
	$this->db->or_like('deposit_withdraw_notification', $q);
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

/* End of file Sms_settings_model.php */
/* Location: ./application/models/Sms_settings_model.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2022-05-29 03:47:48 */
/* http://harviacode.com */