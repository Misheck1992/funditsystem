<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Corporate_customers_model extends CI_Model
{

    public $table = 'corporate_customers';
    public $id = 'id';
    public $order = 'DESC';

    function __construct()
    {
        parent::__construct();
        $this->ensure_new_columns();
    }

    /**
     * Ensure new columns exist in corporate_customers table
     */
    private function ensure_new_columns() {
        // Add financial_year_end column
        if (!$this->db->field_exists('financial_year_end', $this->table)) {
            $this->db->query("ALTER TABLE {$this->table} ADD COLUMN financial_year_end DATE DEFAULT NULL AFTER website");
        }
        // Add casual_employees column
        if (!$this->db->field_exists('casual_employees', $this->table)) {
            $this->db->query("ALTER TABLE {$this->table} ADD COLUMN casual_employees INT DEFAULT 0 AFTER financial_year_end");
        }
        // Add permanent_employees column
        if (!$this->db->field_exists('permanent_employees', $this->table)) {
            $this->db->query("ALTER TABLE {$this->table} ADD COLUMN permanent_employees INT DEFAULT 0 AFTER casual_employees");
        }
        // Add linked_individual_id column for individual-to-corporate linkage
        if (!$this->db->field_exists('linked_individual_id', $this->table)) {
            $this->db->query("ALTER TABLE {$this->table} ADD COLUMN linked_individual_id INT DEFAULT NULL");
        }
    }
	function count_it() {

		$this->db->from($this->table);
		return $this->db->count_all_results();
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
	function update2($id, $data)
	{

		$this->db->where('ClientId', $id);
		$this->db->update($this->table, $data);
	}
    
    // get total rows
    function total_rows($q = NULL) {
        $this->db->like('id', $q);
        $this->db->group_start();
	$this->db->or_like('EntityName', $q);
	$this->db->or_like('DateOfIncorporation', $q);
	$this->db->or_like('RegistrationNumber', $q);
	$this->db->or_like('EntityType', $q);
	$this->db->or_like('ClientId', $q);
	$this->db->or_like('TaxIdentificationNumber', $q);
        $this->db->or_like('company_certificate', $q);
        $this->db->or_like('tax_id_doc', $q);
        $this->db->or_like('proof_physical_address', $q);
        $this->db->or_like('financial_statement', $q);
	$this->db->or_like('Country', $q);
	$this->db->or_like('Branch', $q);
	$this->db->or_like('Status', $q);
        $this->db->or_like('nature_of_business', $q);
        $this->db->or_like('industry_sector', $q);
        $this->db->or_like('street', $q);
        $this->db->or_like('city_town', $q);
        $this->db->or_like('province', $q);
        $this->db->or_like('postal_code', $q);
        $this->db->or_like('phone_number', $q);
        $this->db->or_like('contact_email', $q);
        $this->db->or_like('website', $q);
	$this->db->or_like('LastUpdatedOn', $q);
	$this->db->or_like('CreatedOn', $q);
		$this->db->group_end();
		$this->db->from($this->table)
			->where('approval_status','Approved');
        return $this->db->count_all_results();
    }

    // get data with limit and search
    function get_limit_data($limit, $start = 0, $q = NULL) {
        $this->db->order_by($this->id, $this->order);
        $this->db->like('id', $q);
        $this->db->group_start();
	$this->db->or_like('EntityName', $q);
	$this->db->or_like('DateOfIncorporation', $q);
	$this->db->or_like('RegistrationNumber', $q);
	$this->db->or_like('EntityType', $q);
	$this->db->or_like('ClientId', $q);
	$this->db->or_like('TaxIdentificationNumber', $q);
        $this->db->or_like('company_certificate', $q);
        $this->db->or_like('tax_id_doc', $q);
        $this->db->or_like('proof_physical_address', $q);
        $this->db->or_like('financial_statement', $q);
	$this->db->or_like('Country', $q);
	$this->db->or_like('Branch', $q);
	$this->db->or_like('Status', $q);
        $this->db->or_like('nature_of_business', $q);
        $this->db->or_like('industry_sector', $q);
        $this->db->or_like('street', $q);
        $this->db->or_like('city_town', $q);
        $this->db->or_like('province', $q);
        $this->db->or_like('postal_code', $q);
        $this->db->or_like('phone_number', $q);
        $this->db->or_like('contact_email', $q);
        $this->db->or_like('website', $q);
	$this->db->or_like('LastUpdatedOn', $q);
	$this->db->or_like('CreatedOn', $q);
		$this->db->group_end();
		$this->db->from($this->table)
			->where('approval_status','Approved');
	$this->db->limit($limit, $start);
        return $this->db->get()->result();
    }

    // insert data
    function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }
    function get_status($id)
    {

        $this->db->where('approval_status',$id);
        $this->db->order_by($this->id, $this->order);
        return $this->db->get($this->table)->result();
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

    // search corporate customers
    function search($term, $limit = 10)
    {
        $this->db->select('id, ClientId, EntityName, RegistrationNumber, contact_email, phone_number, linked_individual_id');
        $this->db->group_start();
        $this->db->like('EntityName', $term);
        $this->db->or_like('ClientId', $term);
        $this->db->or_like('RegistrationNumber', $term);
        $this->db->or_like('contact_email', $term);
        $this->db->or_like('phone_number', $term);
        $this->db->group_end();
        $this->db->limit($limit);
        return $this->db->get($this->table)->result();
    }

    // get corporate customer(s) linked to an individual
    function get_by_linked_individual($individual_id)
    {
        $this->db->where('linked_individual_id', $individual_id);
        return $this->db->get($this->table)->result();
    }

}

/* End of file Corporate_customers_model.php */
/* Location: ./application/models/Corporate_customers_model.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2021-05-05 22:08:22 */
/* http://harviacode.com */
