<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Documents extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Documents_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));
        
        if ($q <> '') {
            $config['base_url'] = base_url() . 'documents/index.html?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'documents/index.html?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'documents/index.html';
            $config['first_url'] = base_url() . 'documents/index.html';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Documents_model->total_rows($q);
        $documents = $this->Documents_model->get_limit_data($config['per_page'], $start, $q);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'documents_data' => $documents,
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
        $this->load->view('documents/documents_list', $data);
    }

    public function read($id) 
    {
        $row = $this->Documents_model->get_by_id($id);
        if ($row) {
            $data = array(
		'id' => $row->id,
		'p_lid' => $row->p_lid,
		'document_name' => $row->document_name,
		'file_link' => $row->file_link,
		'stamp' => $row->stamp,
	    );
            $this->load->view('documents/documents_read', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('documents'));
        }
    }

    public function create() 
    {
        $data = array(
            'button' => 'Create',
            'action' => site_url('documents/create_action'),
	    'id' => set_value('id'),
	    'p_lid' => set_value('p_lid'),
	    'document_name' => set_value('document_name'),
	    'file_link' => set_value('file_link'),
	    'stamp' => set_value('stamp'),
	);
        $this->load->view('documents/documents_form', $data);
    }
    
    public function create_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            $data = array(
		'p_lid' => $this->input->post('p_lid',TRUE),
		'document_name' => $this->input->post('document_name',TRUE),
		'file_link' => $this->input->post('file_link',TRUE),
		'stamp' => $this->input->post('stamp',TRUE),
	    );

            $this->Documents_model->insert($data);
            $this->session->set_flashdata('message', 'Create Record Success');
            redirect(site_url('documents'));
        }
    }
    
    public function update($id) 
    {
        $row = $this->Documents_model->get_by_id($id);

        if ($row) {
            $data = array(
                'button' => 'Update',
                'action' => site_url('documents/update_action'),
		'id' => set_value('id', $row->id),
		'p_lid' => set_value('p_lid', $row->p_lid),
		'document_name' => set_value('document_name', $row->document_name),
		'file_link' => set_value('file_link', $row->file_link),
		'stamp' => set_value('stamp', $row->stamp),
	    );
            $this->load->view('documents/documents_form', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('documents'));
        }
    }
    
    public function update_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('id', TRUE));
        } else {
            $data = array(
		'p_lid' => $this->input->post('p_lid',TRUE),
		'document_name' => $this->input->post('document_name',TRUE),
		'file_link' => $this->input->post('file_link',TRUE),
		'stamp' => $this->input->post('stamp',TRUE),
	    );

            $this->Documents_model->update($this->input->post('id', TRUE), $data);
            $this->session->set_flashdata('message', 'Update Record Success');
            redirect(site_url('documents'));
        }
    }
    
    public function delete($id) 
    {
        $row = $this->Documents_model->get_by_id($id);

        if ($row) {
            $this->Documents_model->delete($id);
            $this->session->set_flashdata('message', 'Delete Record Success');
            redirect(site_url('documents'));
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('documents'));
        }
    }

    public function _rules() 
    {
	$this->form_validation->set_rules('p_lid', 'p lid', 'trim|required');
	$this->form_validation->set_rules('document_name', 'document name', 'trim|required');
	$this->form_validation->set_rules('file_link', 'file link', 'trim|required');
	$this->form_validation->set_rules('stamp', 'stamp', 'trim|required');

	$this->form_validation->set_rules('id', 'id', 'trim');
	$this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

/* End of file Documents.php */
/* Location: ./application/controllers/Documents.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2022-09-19 07:06:12 */
/* http://harviacode.com */