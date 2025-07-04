<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Customer_groups_model extends CI_Model
{

    public $table = 'customer_groups';
    public $id = 'customer_group_id';
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
        $this->db->order_by($this->id, $this->order);
        $this->db->select('*')->from($this->table)->join('individual_customers','individual_customers.id=customer_groups.customer')
			->where('group_id',$id);
        return $this->db->get()->result();
    }
    function check($id,$customer)
    {

        $this->db->select('*')->from($this->table)
			->where('group_id',$id)
			->where('customer',$customer);
        return $this->db->get()->row();
    }
	function add_members($arr,$data){
		if($arr){

			for($i=0;$i <count($arr);$i++){


				$menu=array(
					'group_id'=>$data,
					'customer'=>$arr[$i],

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
        $this->db->like('customer_group_id', $q);
	$this->db->or_like('customer', $q);
	$this->db->or_like('group_id', $q);
	$this->db->or_like('date_joined', $q);
	$this->db->from($this->table);
        return $this->db->count_all_results();
    }

    // get data with limit and search
    function get_limit_data($limit, $start = 0, $q = NULL) {
        $this->db->order_by($this->id, $this->order);
        $this->db->like('customer_group_id', $q);
	$this->db->or_like('customer', $q);
	$this->db->or_like('group_id', $q);
	$this->db->or_like('date_joined', $q);
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

/* End of file Customer_groups_model.php */
/* Location: ./application/models/Customer_groups_model.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2021-12-23 06:07:11 */
/* http://harviacode.com */
