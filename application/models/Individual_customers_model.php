<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Individual_customers_model extends CI_Model
{

    public $table = 'individual_customers';
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
    
    function get_all_active()
    {
        $this->db->order_by($this->id, $this->order);
		$this->db->where('approval_status','Approved');
        return $this->db->get($this->table)->result();
    }
    function get_selective($id)
    {
        $this->db->order_by($this->id, $this->order);
        $this->db->where('added_by',$id);
        $this->db->where('customer_type','individual');
        return $this->db->get($this->table)->result();
    }

    function get_group_customers()
    {
        $this->db->select("*")
            ->from('loan')
            ->join('groups','groups.group_id =loan.loan_customer')

			->join('individual_customers','individual_customers.id = loan.loan_customer');


        $this->db->where('loan.customer_type','group');

        $this->db->order_by('loan.loan_id', 'DESC');
        return $this->db->get()->result();
    }


    function get_status($id)
    {
        
        $this->db->where('approval_status',$id);
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

	$this->db->or_like('ClientId', $q);
	$this->db->or_like('Title', $q);
	$this->db->or_like('Firstname', $q);
	$this->db->or_like('Middlename', $q);
	$this->db->or_like('Lastname', $q);
	$this->db->or_like('Gender', $q);
	$this->db->or_like('DateOfBirth', $q);
	$this->db->or_like('EmailAddress', $q);
	$this->db->or_like('PhoneNumber', $q);
	$this->db->or_like('AddressLine1', $q);
	$this->db->or_like('AddressLine2', $q);
	$this->db->or_like('AddressLine3', $q);
	$this->db->or_like('Province', $q);
	$this->db->or_like('City', $q);
	$this->db->or_like('Country', $q);
	$this->db->or_like('ResidentialStatus', $q);
	$this->db->or_like('Profession', $q);
	$this->db->or_like('SourceOfIncome', $q);
	$this->db->or_like('GrossMonthlyIncome', $q);
	$this->db->or_like('Branch', $q);
	$this->db->or_like('LastUpdatedOn', $q);
	$this->db->or_like('CreatedOn', $q);

	$this->db->from($this->table)
		->where('approval_status','Approved');

        return $this->db->count_all_results();
    }
    function count_it() {

	$this->db->from($this->table);
        return $this->db->count_all_results();
    }
    function count_active($from,$to) {

	$this->db->from($this->table)->where('approval_status','Approved');
		if($from !="" && $to !=""){
			$this->db->where('CreatedOn BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');

		}
        return $this->db->count_all_results();
    }

    // get data with limit and search
    function get_limit_data($limit, $start = 0, $q = NULL) {
        $this->db->order_by($this->id, $this->order);
        $this->db->select("*");
        $this->db->like('id', $q);

	$this->db->or_like('ClientId', $q);
	$this->db->or_like('Title', $q);
	$this->db->or_like('Firstname', $q);
	$this->db->or_like('Middlename', $q);
	$this->db->or_like('Lastname', $q);
	$this->db->or_like('Gender', $q);
	$this->db->or_like('DateOfBirth', $q);
	$this->db->or_like('EmailAddress', $q);
	$this->db->or_like('PhoneNumber', $q);
	$this->db->or_like('AddressLine1', $q);
	$this->db->or_like('AddressLine2', $q);
	$this->db->or_like('AddressLine3', $q);
	$this->db->or_like('Province', $q);
	$this->db->or_like('City', $q);
	$this->db->or_like('Country', $q);
	$this->db->or_like('ResidentialStatus', $q);
	$this->db->or_like('Profession', $q);
	$this->db->or_like('SourceOfIncome', $q);
	$this->db->or_like('GrossMonthlyIncome', $q);
	$this->db->or_like('Branch', $q);
	$this->db->or_like('LastUpdatedOn', $q);
	$this->db->or_like('CreatedOn', $q);

	$this->db->limit($limit, $start);
		$this->db->from($this->table)
			->where('approval_status','Approved');
        return $this->db->get()->result();
    }
    function get_filter_export($q,$status,$user,$country,$gender, $from, $to)
    {
        $this->db->order_by('individual_customers.id', $this->order);

        $this->db->select('*,geo_countries.name as geoname,employees.Firstname as efname, employees.Lastname as elname,individual_customers.Firstname as cfname, individual_customers.Lastname as clname,individual_customers.Middlename as cmname, individual_customers.Gender as cgender,individual_customers.DateOfBirth as cdob, individual_customers.EmailAddress as cemail, individual_customers.PhoneNumber as cphonee, individual_customers.marital  as cmarital')->from($this->table);


        $this->db->join('employees','employees.id=individual_customers.added_by');
        $this->db->join('geo_countries','geo_countries.code=individual_customers.Country','left');
        if($gender !=""){
            $this->db->where('individual_customers.Gender',$gender);
        }
        if($status !=""){
            $this->db->where('individual_customers.approval_status',$status);
        }
        if($user !=""){
            $this->db->where('individual_customers.added_by',$user);
        }
        if($country !=""){
            $this->db->where('individual_customers.Country',$country);
        }
        if($from !="" && $to !=""){
            $this->db->where('CreatedOn BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');
        }
        if($q !=""){
            $this->db->group_start();
            $this->db->like('individual_customers.id', $q);
            $this->db->or_like('individual_customers.ClientId', $q);
            $this->db->or_like('individual_customers.Title', $q);
            $this->db->or_like('individual_customers.Firstname', $q);
            $this->db->or_like('individual_customers.Middlename', $q);
            $this->db->or_like('individual_customers.Lastname', $q);
            $this->db->or_like('individual_customers.Gender', $q);
            $this->db->or_like('individual_customers.DateOfBirth', $q);
            $this->db->or_like('individual_customers.EmailAddress', $q);
            $this->db->or_like('individual_customers.PhoneNumber', $q);
            $this->db->or_like('individual_customers.AddressLine1', $q);
            $this->db->or_like('individual_customers.AddressLine2', $q);
            $this->db->or_like('individual_customers.AddressLine3', $q);
            $this->db->or_like('individual_customers.Province', $q);
            $this->db->or_like('individual_customers.City', $q);
            $this->db->or_like('individual_customers.Country', $q);
            $this->db->or_like('individual_customers.ResidentialStatus', $q);
            $this->db->or_like('individual_customers.Profession', $q);
            $this->db->or_like('individual_customers.SourceOfIncome', $q);
            $this->db->or_like('individual_customers.GrossMonthlyIncome', $q);
            $this->db->or_like('individual_customers.Branch', $q);
            $this->db->or_like('individual_customers.LastUpdatedOn', $q);
            $this->db->or_like('individual_customers.CreatedOn', $q);
            $this->db->group_end();
        }



        return $this->db->get()->result();


    }
    function get_filter($limit, $q,$status,$user,$country,$gender, $from, $to, $start = 0)
    {

        $this->db->order_by('individual_customers.id', $this->order);

        $this->db->select('*,geo_countries.name as geoname,employees.Firstname as efname, employees.Lastname as elname,individual_customers.Firstname as cfname, individual_customers.Lastname as clname,individual_customers.Middlename as cmname, individual_customers.Gender as cgender,individual_customers.DateOfBirth as cdob, individual_customers.EmailAddress as cemail, individual_customers.PhoneNumber as cphonee, individual_customers.marital  as cmarital')->from($this->table);


        $this->db->join('employees','employees.id=individual_customers.added_by');
        $this->db->join('geo_countries','geo_countries.code=individual_customers.Country','left');
        if($gender !=""){
            $this->db->where('individual_customers.Gender',$gender);
        }
        if($status !=""){
            $this->db->where('individual_customers.approval_status',$status);
        }
        if($user !=""){
            $this->db->where('individual_customers.added_by',$user);
        }
        if($country !=""){
            $this->db->where('individual_customers.Country',$country);
        }
        if($from !="" && $to !=""){
            $this->db->where('CreatedOn BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');
        }
        if($q !=""){
            $this->db->group_start();
            $this->db->like('individual_customers.id', $q);
            $this->db->or_like('individual_customers.ClientId', $q);
            $this->db->or_like('individual_customers.Title', $q);
            $this->db->or_like('individual_customers.Firstname', $q);
            $this->db->or_like('individual_customers.Middlename', $q);
            $this->db->or_like('individual_customers.Lastname', $q);
            $this->db->or_like('individual_customers.Gender', $q);
            $this->db->or_like('individual_customers.DateOfBirth', $q);
            $this->db->or_like('individual_customers.EmailAddress', $q);
            $this->db->or_like('individual_customers.PhoneNumber', $q);
            $this->db->or_like('individual_customers.AddressLine1', $q);
            $this->db->or_like('individual_customers.AddressLine2', $q);
            $this->db->or_like('individual_customers.AddressLine3', $q);
            $this->db->or_like('individual_customers.Province', $q);
            $this->db->or_like('individual_customers.City', $q);
            $this->db->or_like('individual_customers.Country', $q);
            $this->db->or_like('individual_customers.ResidentialStatus', $q);
            $this->db->or_like('individual_customers.Profession', $q);
            $this->db->or_like('individual_customers.SourceOfIncome', $q);
            $this->db->or_like('individual_customers.GrossMonthlyIncome', $q);
            $this->db->or_like('individual_customers.Branch', $q);
            $this->db->or_like('individual_customers.LastUpdatedOn', $q);
            $this->db->or_like('individual_customers.CreatedOn', $q);
            $this->db->group_end();
        }

        $this->db->limit($limit, $start);
//        echo $this->db->last_query();
        return $this->db->get()->result();

    }
    function get_filter_rows($q,$status,$user,$country,$gender, $from, $to)
    {


        $this->db->select('*,geo_countries.name as geoname,employees.Firstname as efname, employees.Lastname as elname,individual_customers.Firstname as cfname, individual_customers.Lastname as clname,individual_customers.Middlename as cmname, individual_customers.Gender as cgender,individual_customers.DateOfBirth as cdob, individual_customers.EmailAddress as cemail')->from($this->table);


        $this->db->join('employees','employees.id=individual_customers.added_by');
        $this->db->join('geo_countries','geo_countries.code=individual_customers.Country','left');


        if($gender !=""){
            $this->db->where('individual_customers.Gender',$gender);
        }
        if($status !=""){
            $this->db->where('individual_customers.approval_status',$status);
        }
        if($user !=""){
            $this->db->where('individual_customers.added_by',$user);
        }
        if($country !=""){
            $this->db->where('individual_customers.Country',$country);
        }
        if($from !="" && $to !=""){
            $this->db->where('CreatedOn BETWEEN "'. date('Y-m-d', strtotime($from)). '" and "'. date('Y-m-d', strtotime($to)).'"');
        }
        if($q !=""){
            $this->db->group_start();
            $this->db->like('individual_customers.id', $q);
            $this->db->or_like('individual_customers.ClientId', $q);
            $this->db->or_like('individual_customers.Title', $q);
            $this->db->or_like('individual_customers.Firstname', $q);
            $this->db->or_like('individual_customers.Middlename', $q);
            $this->db->or_like('individual_customers.Lastname', $q);
            $this->db->or_like('individual_customers.Gender', $q);
            $this->db->or_like('individual_customers.DateOfBirth', $q);
            $this->db->or_like('individual_customers.EmailAddress', $q);
            $this->db->or_like('individual_customers.PhoneNumber', $q);
            $this->db->or_like('individual_customers.AddressLine1', $q);
            $this->db->or_like('individual_customers.AddressLine2', $q);
            $this->db->or_like('individual_customers.AddressLine3', $q);
            $this->db->or_like('individual_customers.Province', $q);
            $this->db->or_like('individual_customers.City', $q);
            $this->db->or_like('individual_customers.Country', $q);
            $this->db->or_like('individual_customers.ResidentialStatus', $q);
            $this->db->or_like('individual_customers.Profession', $q);
            $this->db->or_like('individual_customers.SourceOfIncome', $q);
            $this->db->or_like('individual_customers.GrossMonthlyIncome', $q);
            $this->db->or_like('individual_customers.Branch', $q);
            $this->db->or_like('individual_customers.LastUpdatedOn', $q);
            $this->db->or_like('individual_customers.CreatedOn', $q);
            $this->db->group_end();
        }

        return $this->db->count_all_results();

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
    
          // update data cofi table
    function updateCofi($id, $data)
    {
        $this->db->where($this->id, $id);
        $this->db->update('smeloans', array('customer_id'=>$data));
    }
    
    function update2($id)
    {
        $this->db->where('ClientId', $id);
        $this->db->update($this->table,array('approval_status'=>'Approved'));
    }

    // delete data
    function delete($id)
    {
        $this->db->where($this->id, $id);
        $this->db->delete($this->table);
    }

}


