<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Customer_assets extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Customer_assets_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));
        
        if ($q <> '') {
            $config['base_url'] = base_url() . 'customer_assets/index.html?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'customer_assets/index.html?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'customer_assets/index.html';
            $config['first_url'] = base_url() . 'customer_assets/index.html';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Customer_assets_model->total_rows($q);
        $customer_assets = $this->Customer_assets_model->get_limit_data($config['per_page'], $start, $q);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'customer_assets_data' => $customer_assets,
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
        $this->load->view('customer_assets/customer_assets_list', $data);
    }

    public function read($id) 
    {
        $row = $this->Customer_assets_model->get_by_id($id);
        if ($row) {
            $data = array(
		'customer_asset_id' => $row->customer_asset_id,
		'customer' => $row->customer,
		'asset' => $row->asset,
	    );
            $this->load->view('customer_assets/customer_assets_read', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('customer_assets'));
        }
    }

    public function create() 
    {
        $data = array(
            'button' => 'Create',
            'action' => site_url('customer_assets/create_action'),
	    'customer_asset_id' => set_value('customer_asset_id'),
	    'customer' => set_value('customer'),
	    'asset' => set_value('asset'),
	);
        $this->load->view('customer_assets/customer_assets_form', $data);
    }
    
    public function create_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            $data = array(
		'customer' => $this->input->post('customer',TRUE),
		'asset' => $this->input->post('asset',TRUE),
	    );

            $this->Customer_assets_model->insert($data);
            $this->session->set_flashdata('message', 'Create Record Success');
            redirect(site_url('customer_assets'));
        }
    }
    
    public function update($id) 
    {
        $row = $this->Customer_assets_model->get_by_id($id);

        if ($row) {
            $data = array(
                'button' => 'Update',
                'action' => site_url('customer_assets/update_action'),
		'customer_asset_id' => set_value('customer_asset_id', $row->customer_asset_id),
		'customer' => set_value('customer', $row->customer),
		'asset' => set_value('asset', $row->asset),
	    );
            $this->load->view('customer_assets/customer_assets_form', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('customer_assets'));
        }
    }
    
    public function update_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('customer_asset_id', TRUE));
        } else {
            $data = array(
		'customer' => $this->input->post('customer',TRUE),
		'asset' => $this->input->post('asset',TRUE),
	    );

            $this->Customer_assets_model->update($this->input->post('customer_asset_id', TRUE), $data);
            $this->session->set_flashdata('message', 'Update Record Success');
            redirect(site_url('customer_assets'));
        }
    }
    
    public function delete($id) 
    {
        $row = $this->Customer_assets_model->get_by_id($id);

        if ($row) {
            $this->Customer_assets_model->delete($id);
            $this->session->set_flashdata('message', 'Delete Record Success');
            redirect(site_url('customer_assets'));
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('customer_assets'));
        }
    }

    public function _rules() 
    {
	$this->form_validation->set_rules('customer', 'customer', 'trim|required');
	$this->form_validation->set_rules('asset', 'asset', 'trim|required');

	$this->form_validation->set_rules('customer_asset_id', 'customer_asset_id', 'trim');
	$this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

/* End of file Customer_assets.php */
/* Location: ./application/controllers/Customer_assets.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2022-10-04 08:24:14 */
/* http://harviacode.com */