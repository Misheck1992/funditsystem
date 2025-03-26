<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Rescheduled_payments extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Rescheduled_payments_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));
        
        if ($q <> '') {
            $config['base_url'] = base_url() . 'rescheduled_payments/index.html?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'rescheduled_payments/index.html?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'rescheduled_payments/index.html';
            $config['first_url'] = base_url() . 'rescheduled_payments/index.html';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Rescheduled_payments_model->total_rows($q);
        $rescheduled_payments = $this->Rescheduled_payments_model->get_limit_data($config['per_page'], $start, $q);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'rescheduled_payments_data' => $rescheduled_payments,
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
        $this->load->view('rescheduled_payments/rescheduled_payments_list', $data);
    }

    public function read($id) 
    {
        $row = $this->Rescheduled_payments_model->get_by_id($id);
        if ($row) {
            $data = array(
		'rescheduled_payments_id' => $row->rescheduled_payments_id,
		'loan_id' => $row->loan_id,
		'customer_id' => $row->customer_id,
		'customer_type' => $row->customer_type,
		'payment_number' => $row->payment_number,
		'payment_amount' => $row->payment_amount,
		'payment_date' => $row->payment_date,
		'pay_status' => $row->pay_status,
		'paid_amount' => $row->paid_amount,
		'p_stamp' => $row->p_stamp,
	    );
            $this->load->view('rescheduled_payments/rescheduled_payments_read', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('rescheduled_payments'));
        }
    }

    public function create() 
    {
        $data = array(
            'button' => 'Create',
            'action' => site_url('rescheduled_payments/create_action'),
	    'rescheduled_payments_id' => set_value('rescheduled_payments_id'),
	    'loan_id' => set_value('loan_id'),
	    'customer_id' => set_value('customer_id'),
	    'customer_type' => set_value('customer_type'),
	    'payment_number' => set_value('payment_number'),
	    'payment_amount' => set_value('payment_amount'),
	    'payment_date' => set_value('payment_date'),
	    'pay_status' => set_value('pay_status'),
	    'paid_amount' => set_value('paid_amount'),
	    'p_stamp' => set_value('p_stamp'),
	);
        $this->load->view('rescheduled_payments/rescheduled_payments_form', $data);
    }
    
    public function create_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            $data = array(
		'loan_id' => $this->input->post('loan_id',TRUE),
		'customer_id' => $this->input->post('customer_id',TRUE),
		'customer_type' => $this->input->post('customer_type',TRUE),
		'payment_number' => $this->input->post('payment_number',TRUE),
		'payment_amount' => $this->input->post('payment_amount',TRUE),
		'payment_date' => $this->input->post('payment_date',TRUE),
		'pay_status' => $this->input->post('pay_status',TRUE),
		'paid_amount' => $this->input->post('paid_amount',TRUE),
		'p_stamp' => $this->input->post('p_stamp',TRUE),
	    );

            $this->Rescheduled_payments_model->insert($data);
            $this->session->set_flashdata('message', 'Create Record Success');
            redirect(site_url('rescheduled_payments'));
        }
    }
    
    public function update($id) 
    {
        $row = $this->Rescheduled_payments_model->get_by_id($id);

        if ($row) {
            $data = array(
                'button' => 'Update',
                'action' => site_url('rescheduled_payments/update_action'),
		'rescheduled_payments_id' => set_value('rescheduled_payments_id', $row->rescheduled_payments_id),
		'loan_id' => set_value('loan_id', $row->loan_id),
		'customer_id' => set_value('customer_id', $row->customer_id),
		'customer_type' => set_value('customer_type', $row->customer_type),
		'payment_number' => set_value('payment_number', $row->payment_number),
		'payment_amount' => set_value('payment_amount', $row->payment_amount),
		'payment_date' => set_value('payment_date', $row->payment_date),
		'pay_status' => set_value('pay_status', $row->pay_status),
		'paid_amount' => set_value('paid_amount', $row->paid_amount),
		'p_stamp' => set_value('p_stamp', $row->p_stamp),
	    );
            $this->load->view('rescheduled_payments/rescheduled_payments_form', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('rescheduled_payments'));
        }
    }
    
    public function update_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('rescheduled_payments_id', TRUE));
        } else {
            $data = array(
		'loan_id' => $this->input->post('loan_id',TRUE),
		'customer_id' => $this->input->post('customer_id',TRUE),
		'customer_type' => $this->input->post('customer_type',TRUE),
		'payment_number' => $this->input->post('payment_number',TRUE),
		'payment_amount' => $this->input->post('payment_amount',TRUE),
		'payment_date' => $this->input->post('payment_date',TRUE),
		'pay_status' => $this->input->post('pay_status',TRUE),
		'paid_amount' => $this->input->post('paid_amount',TRUE),
		'p_stamp' => $this->input->post('p_stamp',TRUE),
	    );

            $this->Rescheduled_payments_model->update($this->input->post('rescheduled_payments_id', TRUE), $data);
            $this->session->set_flashdata('message', 'Update Record Success');
            redirect(site_url('rescheduled_payments'));
        }
    }
    
    public function delete($id) 
    {
        $row = $this->Rescheduled_payments_model->get_by_id($id);

        if ($row) {
            $this->Rescheduled_payments_model->delete($id);
            $this->session->set_flashdata('message', 'Delete Record Success');
            redirect(site_url('rescheduled_payments'));
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('rescheduled_payments'));
        }
    }

    public function _rules() 
    {
	$this->form_validation->set_rules('loan_id', 'loan id', 'trim|required');
	$this->form_validation->set_rules('customer_id', 'customer id', 'trim|required');
	$this->form_validation->set_rules('customer_type', 'customer type', 'trim|required');
	$this->form_validation->set_rules('payment_number', 'payment number', 'trim|required');
	$this->form_validation->set_rules('payment_amount', 'payment amount', 'trim|required|numeric');
	$this->form_validation->set_rules('payment_date', 'payment date', 'trim|required');
	$this->form_validation->set_rules('pay_status', 'pay status', 'trim|required');
	$this->form_validation->set_rules('paid_amount', 'paid amount', 'trim|required|numeric');
	$this->form_validation->set_rules('p_stamp', 'p stamp', 'trim|required');

	$this->form_validation->set_rules('rescheduled_payments_id', 'rescheduled_payments_id', 'trim');
	$this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

/* End of file Rescheduled_payments.php */
/* Location: ./application/controllers/Rescheduled_payments.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2023-03-09 14:09:42 */
/* http://harviacode.com */