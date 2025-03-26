<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Masspayments_model extends CI_Model
{

    public $table = 'massrepayments';

    public $id = 'massrepayment_id';
    public $order = 'DESC';

    function __construct()
    {
        parent::__construct();
    }
    // get all
    function get_all_processed()
    {

        $this->db->select("*")
            ->from($this->table);

        $this->db->where('massrepayment_status','processed');


        return $this->db->get()->result();
    }


    function get_all_deposited()
    {
        $this->db->distinct();
        $this->db->select('loan_number')

            ->from($this->table);

           $this->db->where('massrepayment_status','deposited');


        return $this->db->get()->result();
    }
    function get_all_repaid()
    {

        $this->db->select("*")
            ->from($this->table)
            ->join('loan','loan.loan_id = massrepayments.mass_loan_id')
            ->join('loan_products','loan_products.loan_product_id =massrepayments.mass_loan_product');
        //->join('individual_customers','individual_customers.id = loan.loan_customer');

            $this->db->where('massrepayment_status','payment_made');


            return $this->db->get()->result();
    }

    function get_all_imported()
    {

        $this->db->select("*")
            ->from($this->table)
            ->join('loan','loan.loan_number = massrepayments.loan_number');
           // ->join('loan_products','loan_products.loan_product_id =massrepayments.mass_loan_product_id');
        //->join('individual_customers','individual_customers.id = loan.loan_customer');

        $this->db->where('massrepayment_status','imported');


        return $this->db->get()->result();
    }

    function get_all_processed_list()
    {

        $this->db->select("*")

            ->from($this->table)
            ->join('loan','loan.loan_id = massrepayments.mass_loan_id')
            ->join('loan_products','loan_products.loan_product_id =massrepayments.mass_loan_product_id');

             $this->db->where('massrepayment_status','processed');


            return $this->db->get()->result();
    }

    function get_all_deposited_list()
    {

        $this->db->select("*")

            ->from($this->table)
            ->join('loan','loan.loan_id = massrepayments.mass_loan_id')
            ->join('loan_products','loan_products.loan_product_id =massrepayments.mass_loan_product_id');

        $this->db->where('massrepayment_status','deposited');


        return $this->db->get()->result();
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

