<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Access_model extends CI_Model
{

    public $table = 'access';
    public $id = 'id';
    public $order = 'DESC';

    function __construct()
    {
        parent::__construct();
    }
    function get_all_acces($idd){
        $this->db->order_by($this->id, $this->order);
        $this->db->where('roleid', $idd);
        return $this->db->get($this->table)->result();
    }
    function remove_menu($arr,$data){
        if($arr){
            for($i=0;$i <count($arr);$i++){

                //print_r($arr[$i]);
                //print($data);
                $menu=array(
                    'roleid'=>$data,
                    'controllerid'=>$arr[$i]
                );
                $this->db->select('*');
                $this->db->from('access');
                $this->db->where('roleid',$data);
                $this->db->where('controllerid',$arr[$i]);
                $query=$this->db->get()->row_array();
                if ($query){
                    $this->db->where('id', $query['id']);
                    $this->db->delete('access', $menu);
//                if($query->controllerid==$arr[$i]){
//                    $this->db->where('id', $query['id']);
//                    $this->db->update('access', $menu);
//
//                }else {
//                    $this->db->where('id',$query['id']);
//                    $this->db->delete('access',$menu);
//                }

                }
//                else{
//                    $this->db->insert('access',$menu);
//
//                }
            }
            return true;

        }
        else{
            echo "array not passed";
        }
    }
    function add_assets($arr,$data){
        if(empty($arr)){
            $this->db->where('roleid',$data)->delete($this->table);
            for($i=0;$i <count($arr);$i++){

                //print_r($arr[$i]);
                //print($data);
                $menu=array(
                    'customer'=>$data,
                    'asset'=>$arr[$i]
                );
                $this->db->select('*');
                $this->db->from('access');
                $this->db->where('roleid',$data);
                $this->db->where('controllerid',$arr[$i]);
                $query=$this->db->get()->row_array();
                if ($query){
                    $this->db->where('id', $query['id']);
                    $this->db->delete('access', $menu);


                }

            }
            return true;

        }
        else{
            echo "array not passed";
        }
    }
    function assets($arr,$data){
        if(!empty($arr)){
        	$this->db->where('customer',$data)->delete('customer_assets');
            for($i=0;$i <count($arr);$i++){


                $menu=array(
                    'customer'=>$data,
                    'asset'=>$arr[$i],

                );

                    $this->db->insert('customer_assets',$menu);


            }
            return true;

        }

    }
    function training($arr,$data){
        if(!empty($arr)){
        	$this->db->where('customer',$data)->delete('training');
            for($i=0;$i <count($arr);$i++){


                $menu=array(
                    'customer'=>$data,
                    'training'=>$arr[$i],

                );

                    $this->db->insert('training',$menu);


            }
            return true;

        }

    }
    function check_menu($arr,$data){
        if($arr){
        	$this->db->where('roleid',$data)->delete($this->table);
            for($i=0;$i <count($arr);$i++){


                $menu=array(
                    'roleid'=>$data,
                    'controllerid'=>$arr[$i],
					'system_date' =>$this->session->userdata('system_date'),
					'added_by'=>$this->session->userdata('user_id')
                );

                    $this->db->insert('access',$menu);


            }
            return true;

        }
        else{
            echo "array not passed";
        }
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
	$this->db->or_like('roleid', $q);
	$this->db->or_like('controllerid', $q);
	$this->db->from($this->table);
        return $this->db->count_all_results();
    }

    // get data with limit and search
    function get_limit_data($limit, $start = 0, $q = NULL) {
        $this->db->order_by($this->id, $this->order);
        $this->db->like('id', $q);
	$this->db->or_like('roleid', $q);
	$this->db->or_like('controllerid', $q);
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
function update_auth($id, $data)
    {
        $this->db->where('approval_edits', $id);
        $this->db->update('approval_edits', $data);
    }

    // delete data
    function delete($id)
    {
        $this->db->where($this->id, $id);
        $this->db->delete($this->table);
    }

}

