<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Loan_files extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Loan_files_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));
        
        if ($q <> '') {
            $config['base_url'] = base_url() . 'loan_files/index.html?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'loan_files/index.html?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'loan_files/index.html';
            $config['first_url'] = base_url() . 'loan_files/index.html';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Loan_files_model->total_rows($q);
        $loan_files = $this->Loan_files_model->get_limit_data($config['per_page'], $start, $q);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'loan_files_data' => $loan_files,
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
        $this->load->view('loan_files/loan_files_list', $data);
    }

    public function read($id) 
    {
        $row = $this->Loan_files_model->get_by_id($id);
        if ($row) {
            $data = array(
		'file_id' => $row->file_id,
		'loan_id' => $row->loan_id,
		'file_name' => $row->file_name,
		'real_file' => $row->real_file,
		'file_stamp' => $row->file_stamp,
	    );
            $this->load->view('loan_files/loan_files_read', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('loan_files'));
        }
    }

    public function create() 
    {
        $data = array(
            'button' => 'Create',
            'action' => site_url('loan_files/create_action'),
	    'file_id' => set_value('file_id'),
	    'loan_id' => set_value('loan_id'),
	    'file_name' => set_value('file_name'),
	    'real_file' => set_value('real_file'),
	    'file_stamp' => set_value('file_stamp'),
	);
        $this->load->view('loan_files/loan_files_form', $data);
    }
    
    public function create_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            $data = array(
		'loan_id' => $this->input->post('loan_id',TRUE),
		'file_name' => $this->input->post('file_name',TRUE),
		'real_file' => $this->input->post('real_file',TRUE),
		'file_stamp' => $this->input->post('file_stamp',TRUE),
	    );

            $this->Loan_files_model->insert($data);
            $this->session->set_flashdata('message', 'Create Record Success');
            redirect(site_url('loan_files'));
        }
    }
    
    public function update($id) 
    {
        $row = $this->Loan_files_model->get_by_id($id);

        if ($row) {
            $data = array(
                'button' => 'Update',
                'action' => site_url('loan_files/update_action'),
		'file_id' => set_value('file_id', $row->file_id),
		'loan_id' => set_value('loan_id', $row->loan_id),
		'file_name' => set_value('file_name', $row->file_name),
		'real_file' => set_value('real_file', $row->real_file),
		'file_stamp' => set_value('file_stamp', $row->file_stamp),
	    );
            $this->load->view('loan_files/loan_files_form', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('loan_files'));
        }
    }
    
    public function update_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('file_id', TRUE));
        } else {
            $data = array(
		'loan_id' => $this->input->post('loan_id',TRUE),
		'file_name' => $this->input->post('file_name',TRUE),
		'real_file' => $this->input->post('real_file',TRUE),
		'file_stamp' => $this->input->post('file_stamp',TRUE),
	    );

            $this->Loan_files_model->update($this->input->post('file_id', TRUE), $data);
            $this->session->set_flashdata('message', 'Update Record Success');
            redirect(site_url('loan_files'));
        }
    }
    
    public function delete($id) 
    {
        $row = $this->Loan_files_model->get_by_id($id);

        if ($row) {
            $this->Loan_files_model->delete($id);
            $this->session->set_flashdata('message', 'Delete Record Success');
            redirect(site_url('loan_files'));
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('loan_files'));
        }
    }

    public function _rules() 
    {
	$this->form_validation->set_rules('loan_id', 'loan id', 'trim|required');
	$this->form_validation->set_rules('file_name', 'file name', 'trim|required');
	$this->form_validation->set_rules('real_file', 'real file', 'trim|required');
	$this->form_validation->set_rules('file_stamp', 'file stamp', 'trim|required');

	$this->form_validation->set_rules('file_id', 'file_id', 'trim');
	$this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

/* End of file Loan_files.php */
/* Location: ./application/controllers/Loan_files.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2023-02-07 03:23:20 */
/* http://harviacode.com */