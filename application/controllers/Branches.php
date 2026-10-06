<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Branches extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Branches_model');
        $this->load->library('form_validation');
    }

	public function sendsms(){
	   $re= send_sms('0994099461','Hello testing SMS FInrealm');
	   print_r($re);
	}

    public function index()
    {
        $search = $this->input->get('q', TRUE);
        $q = $search === NULL ? '' : urldecode((string) $search);
        $start = intval($this->input->get('start'));
        
        if ($q <> '') {
            $config['base_url'] = base_url() . 'branches/index?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'branches/index?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'branches/index';
            $config['first_url'] = base_url() . 'branches/index';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Branches_model->total_rows($q);
        $branches = $this->Branches_model->get_limit_data($config['per_page'], $start, $q);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'branches_data' => $this->Branches_model->get_all(),
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
		$this->load->view('admin/header');
		$this->load->view('branches/branches_list',$data);
		$this->load->view('admin/footer');

    }

    public function read($id) 
    {
        $row = $this->Branches_model->get_by_id($id);
        if ($row) {
            $data = array(
		'id' => $row->id,
		'BranchCode' => $row->BranchCode,
		'BranchName' => $row->BranchName,
		'AddressLine1' => $row->AddressLine1,
		'AddressLine2' => $row->AddressLine2,
		'City' => $row->City,
		'Stamp' => $row->Stamp,
	    );
            $this->load->view('branches/branches_read', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('branches'));
        }
    }

    public function create() 
    {
        $data = array(
            'button' => 'Create',
            'action' => site_url('branches/create_action'),
	    'id' => set_value('id'),
	    'BranchCode' => set_value('BranchCode'),
	    'BranchName' => set_value('BranchName'),
	    'AddressLine1' => set_value('branch_address_line1'),
	    'AddressLine2' => set_value('branch_address_line2'),
	    'City' => set_value('City'),
	    'Stamp' => set_value('Stamp'),
	);
		$this->load->view('admin/header');
		$this->load->view('branches/branches_form',$data);
		$this->load->view('admin/footer');
    }
    
    public function create_action() 
    {
        $this->_rules(TRUE);

        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            $data = array(
		'BranchCode' => $this->input->post('BranchCode',TRUE),
		'Code' => rand(100,9999),
		'BranchName' => $this->input->post('BranchName',TRUE),
		'AddressLine1' => $this->input->post('branch_address_line1', TRUE),
		'AddressLine2' => $this->input->post('branch_address_line2', TRUE),
		'City' => $this->input->post('City',TRUE),

	    );

            if ($this->Branches_model->insert($data)) {
                $this->toaster->success('Success, branch was created successfully');
                redirect(site_url('branches'));
                return;
            }

            log_message('error', 'Branch creation failed: ' . json_encode($this->db->error()));
            $this->toaster->error('Branch could not be created. Please try again.');
            $this->create();
        }
    }
    
    public function update($id) 
    {
        $row = $this->Branches_model->get_by_id($id);

        if ($row) {
            $data = array(
                'button' => 'Update',
                'action' => site_url('branches/update_action'),
		'id' => set_value('id', $row->id),
		'BranchCode' => set_value('BranchCode', $row->BranchCode),
		'BranchName' => set_value('BranchName', $row->BranchName),
		'AddressLine1' => set_value('branch_address_line1', $row->AddressLine1),
		'AddressLine2' => set_value('branch_address_line2', $row->AddressLine2),
		'City' => set_value('City', $row->City),
		'Stamp' => set_value('Stamp', $row->Stamp),
	    );
			$this->load->view('admin/header');
			$this->load->view('branches/branches_form',$data);
			$this->load->view('admin/footer');
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('branches'));
        }
    }
    
    public function update_action() 
    {
        $this->_rules(FALSE);

        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('id', TRUE));
        } else {
            $data = array(
		'BranchCode' => $this->input->post('BranchCode',TRUE),
		'BranchName' => $this->input->post('BranchName',TRUE),
		'AddressLine1' => $this->input->post('branch_address_line1', TRUE),
		'AddressLine2' => $this->input->post('branch_address_line2', TRUE),
		'City' => $this->input->post('City',TRUE),
	    );

            if ($this->Branches_model->update($this->input->post('id', TRUE), $data)) {
                $this->toaster->success('Success, branch was updated successfully');
                redirect(site_url('branches'));
                return;
            }

            log_message('error', 'Branch update failed: ' . json_encode($this->db->error()));
            $this->toaster->error('Branch could not be updated. Please try again.');
            $this->update($this->input->post('id', TRUE));
        }
    }
    
    public function delete($id) 
    {
        $row = $this->Branches_model->get_by_id($id);

        if ($row) {
            $this->Branches_model->delete($id);
            $this->session->set_flashdata('message', 'Delete Record Success');
            redirect(site_url('branches'));
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('branches'));
        }
    }

    public function _rules($creating = FALSE)
    {
	$branch_code_rules = 'trim|required|integer';
	if ($creating) {
		$branch_code_rules .= '|is_unique[branches.BranchCode]';
	}
	$this->form_validation->set_rules(
		'BranchCode',
		'Branch Code',
		$branch_code_rules,
		array('is_unique' => 'That Branch Code already exists. Please use a different code.')
	);
	$this->form_validation->set_rules('BranchName', 'Branch Name', 'trim|required|max_length[255]');

	$this->form_validation->set_rules('branch_address_line1', 'Address Line 1', 'trim|max_length[200]');
	$this->form_validation->set_rules('branch_address_line2', 'Address Line 2', 'trim|max_length[200]');
	$this->form_validation->set_rules('City', 'City', 'trim|required|max_length[255]');


	$this->form_validation->set_rules('id', 'id', 'trim');
	$this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

