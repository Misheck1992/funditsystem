<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Corporate_shareholders_model extends CI_Model
{

    public $table = 'corporate_shareholders';
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
    function get_members($id)
    {
//        $this->db->order_by($this->id, $this->order);
        $this->db->select('*')->from($this->table)->join('corporate_customers','corporate_customers.id=corporate_shareholders.shareholder_id')
            ->where('corporate_id',$id);
        return $this->db->get()->result();
    }
    function check($id,$customer)
    {

        $this->db->select('*')->from($this->table)
            ->where('corporate_id',$id)
            ->where('shareholder_id',$customer);
        return $this->db->get()->row();
    }
    function add_members($arr,$data){
        if($arr){

            for($i=0;$i <count($arr);$i++){


                $menu=array(
                    'corporate_id'=>$data,
                    'shareholder_id'=>$arr[$i],
                    'percentage_value'=>$arr[$i],

                );

                $this->db->insert($this->table,$menu);


            }
            return true;

        }

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
        $this->db->or_like('corporate_id', $q);
        $this->db->or_like('shareholder_id', $q);
        $this->db->or_like('percentage_value', $q);
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    // get data with limit and search
    function get_limit_data($limit, $start = 0, $q = NULL) {
        $this->db->order_by($this->id, $this->order);
        $this->db->or_like('corporate_id', $q);
        $this->db->or_like('shareholder_id', $q);
        $this->db->or_like('percentage_value', $q);
        $this->db->limit($limit, $start);
        return $this->db->get($this->table)->result();
    }

    // insert data
    function insert($data)
    {
        $this->db->insert($this->table, $data);
    }
    // Insert batch function for multiple shareholders
    function insert_batch($data)
    {
        return $this->db->insert_batch($this->table, $data);
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

    // delete by corporate_id
    function delete_by_corporate_id($corporate_id)
    {
        $this->db->where('corporate_id', $corporate_id);
        $this->db->delete($this->table);
    }

}

/* End of file Customer_groups_model.php */
/* Location: ./application/models/Customer_groups_model.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2021-12-23 06:07:11 */
/* http://harviacode.com */
