<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Training extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Training_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));
        
        if ($q <> '') {
            $config['base_url'] = base_url() . 'training/index.html?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'training/index.html?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'training/index.html';
            $config['first_url'] = base_url() . 'training/index.html';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Training_model->total_rows($q);
        $training = $this->Training_model->get_limit_data($config['per_page'], $start, $q);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'training_data' => $training,
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
        $this->load->view('training/training_list', $data);
    }

    public function read($id) 
    {
        $row = $this->Training_model->get_by_id($id);
        if ($row) {
            $data = array(
		'trainings_id' => $row->trainings_id,
		'training' => $row->training,
		'customer' => $row->customer,
	    );
            $this->load->view('training/training_read', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('training'));
        }
    }

    public function create() 
    {
        $data = array(
            'button' => 'Create',
            'action' => site_url('training/create_action'),
	    'trainings_id' => set_value('trainings_id'),
	    'training' => set_value('training'),
	    'customer' => set_value('customer'),
	);
        $this->load->view('training/training_form', $data);
    }
    
    public function create_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            $data = array(
		'trainings_id' => $this->input->post('trainings_id',TRUE),
		'training' => $this->input->post('training',TRUE),
		'customer' => $this->input->post('customer',TRUE),
	    );

            $this->Training_model->insert($data);
            $this->session->set_flashdata('message', 'Create Record Success');
            redirect(site_url('training'));
        }
    }
    
    public function update($id) 
    {
        $row = $this->Training_model->get_by_id($id);

        if ($row) {
            $data = array(
                'button' => 'Update',
                'action' => site_url('training/update_action'),
		'trainings_id' => set_value('trainings_id', $row->trainings_id),
		'training' => set_value('training', $row->training),
		'customer' => set_value('customer', $row->customer),
	    );
            $this->load->view('training/training_form', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('training'));
        }
    }
    
    public function update_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('', TRUE));
        } else {
            $data = array(
		'trainings_id' => $this->input->post('trainings_id',TRUE),
		'training' => $this->input->post('training',TRUE),
		'customer' => $this->input->post('customer',TRUE),
	    );

            $this->Training_model->update($this->input->post('', TRUE), $data);
            $this->session->set_flashdata('message', 'Update Record Success');
            redirect(site_url('training'));
        }
    }
    
    public function delete($id) 
    {
        $row = $this->Training_model->get_by_id($id);

        if ($row) {
            $this->Training_model->delete($id);
            $this->session->set_flashdata('message', 'Delete Record Success');
            redirect(site_url('training'));
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('training'));
        }
    }

    public function _rules() 
    {
	$this->form_validation->set_rules('trainings_id', 'trainings id', 'trim|required');
	$this->form_validation->set_rules('training', 'training', 'trim|required');
	$this->form_validation->set_rules('customer', 'customer', 'trim|required');

	$this->form_validation->set_rules('', '', 'trim');
	$this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

/* End of file Training.php */
/* Location: ./application/controllers/Training.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2022-10-04 08:24:06 */
/* http://harviacode.com */