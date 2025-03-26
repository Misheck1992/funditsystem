<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Payment_method extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Payment_method_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));
        
        if ($q <> '') {
            $config['base_url'] = base_url() . 'payment_method/index?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'payment_method/index?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'payment_method/index';
            $config['first_url'] = base_url() . 'payment_method/index';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Payment_method_model->total_rows($q);
        $payment_method = $this->Payment_method_model->get_limit_data($config['per_page'], $start, $q);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'payment_method_data' => $payment_method,
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
        $this->load->view('admin/header');
        $this->load->view('payment_method/payment_method_list', $data);
        $this->load->view('admin/footer');
    }

    public function read($id) 
    {
        $row = $this->Payment_method_model->get_by_id($id);
        if ($row) {
            $data = array(
		'payment_method' => $row->payment_method,
		'payment_method_name' => $row->payment_method_name,
		'description' => $row->description,
		'payment_method_stamp' => $row->payment_method_stamp,
	    );
            $this->load->view('payment_method/payment_method_read', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('payment_method'));
        }
    }

    public function create() 
    {
        $data = array(
            'button' => 'Create',
            'action' => site_url('payment_method/create_action'),
	    'payment_method' => set_value('payment_method'),
	    'payment_method_name' => set_value('payment_method_name'),
	    'description' => set_value('description'),
	    'payment_method_stamp' => set_value('payment_method_stamp'),
	);
        $this->load->view('admin/header');
        $this->load->view('payment_method/payment_method_form', $data);
        $this->load->view('admin/footer');
    }
    
    public function create_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            $data = array(
		'payment_method_name' => $this->input->post('payment_method_name',TRUE),
		'description' => $this->input->post('description',TRUE),
	
	    );

            $this->Payment_method_model->insert($data);
            $this->session->set_flashdata('success', 'Create Record Success');
            redirect(site_url('payment_method'));
        }
    }
    
    public function update($id) 
    {
        $row = $this->Payment_method_model->get_by_id($id);

        if ($row) {
            $data = array(
                'button' => 'Update',
                'action' => site_url('payment_method/update_action'),
		'payment_method' => set_value('payment_method', $row->payment_method),
		'payment_method_name' => set_value('payment_method_name', $row->payment_method_name),
		'description' => set_value('description', $row->description),
		'payment_method_stamp' => set_value('payment_method_stamp', $row->payment_method_stamp),
	    );
            $this->load->view('admin/header');
            $this->load->view('payment_method/payment_method_form', $data);
            $this->load->view('admin/footer');
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('payment_method'));
        }
    }
    
    public function update_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('payment_method', TRUE));
        } else {
            $data = array(
		'payment_method_name' => $this->input->post('payment_method_name',TRUE),
		'description' => $this->input->post('description',TRUE),

	    );

            $this->Payment_method_model->update($this->input->post('payment_method', TRUE), $data);
            $this->session->set_flashdata('success', 'Update Record Success');
            redirect(site_url('payment_method'));
        }
    }
    
    public function delete($id) 
    {
        $row = $this->Payment_method_model->get_by_id($id);

        if ($row) {
            $this->Payment_method_model->delete($id);
            $this->session->set_flashdata('message', 'Delete Record Success');
            redirect(site_url('payment_method'));
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('payment_method'));
        }
    }

    public function _rules() 
    {
	$this->form_validation->set_rules('payment_method_name', 'payment method name', 'trim|required');


	$this->form_validation->set_rules('payment_method', 'payment_method', 'trim');
	$this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

