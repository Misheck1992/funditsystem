<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Previous_loans extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Previous_loans_model');
        $this->load->model('Documents_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));
        
        if ($q <> '') {
            $config['base_url'] = base_url() . 'previous_loans/index?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'previous_loans/index?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'previous_loans/index';
            $config['first_url'] = base_url() . 'previous_loans/index';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Previous_loans_model->total_rows($q);
        $previous_loans = $this->Previous_loans_model->get_limit_data($config['per_page'], $start, $q);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'previous_loans_data' => $previous_loans,
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
		$menu_toggle['toggles'] = 23;
		$this->load->view('admin/header',$menu_toggle);
        $this->load->view('previous_loans/previous_loans_list', $data);
		$this->load->view('admin/footer');
    }

    public function read($id) 
    {
        $row = $this->Previous_loans_model->get_by_id($id);
        if ($row) {
            $data = array(
		'p_lid' => $row->p_lid,
		'customer_id' => $row->customer_id,
		'loan_effective_date' => $row->loan_effective_date,
		'loan_end_date' => $row->loan_end_date,
		'amount' => $row->amount,
		'amount_paid' => $row->amount_paid,
		'description' => $row->description,
		'status' => $row->status,
		'added_by' => $row->added_by,
		'stamp' => $row->stamp,
	    );
			$menu_toggle['toggles'] = 23;
			$this->load->view('admin/header',$menu_toggle);
            $this->load->view('previous_loans/previous_loans_read', $data);
			$this->load->view('admin/footer');
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('previous_loans'));
        }
    }

    public function create() 
    {
        $data = array(
            'button' => 'Create',
            'action' => site_url('previous_loans/create_action'),
	    'p_lid' => set_value('p_lid'),
	    'customer_id' => set_value('customer_id'),
	    'loan_effective_date' => set_value('loan_effective_date'),
	    'loan_end_date' => set_value('loan_end_date'),
	    'amount' => set_value('amount'),
	    'amount_paid' => set_value('amount_paid'),
	    'description' => set_value('description'),
	    'status' => set_value('status'),
	    'added_by' => set_value('added_by'),
	    'stamp' => set_value('stamp'),
	);
		$menu_toggle['toggles'] = 23;
		$this->load->view('admin/header',$menu_toggle);
        $this->load->view('previous_loans/previous_loans_form', $data);
		$this->load->view('admin/footer');
    }
    
    public function create_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            $data = array(
		'customer_id' => $this->input->post('customer_id',TRUE),
		'loan_effective_date' => $this->input->post('loan_effective_date',TRUE),
		'loan_end_date' => $this->input->post('loan_end_date',TRUE),
		'amount' => $this->input->post('amount',TRUE),
		'amount_paid' => $this->input->post('amount_paid',TRUE),
		'description' => $this->input->post('description',TRUE),
		'status' => $this->input->post('status',TRUE),
	                    );

          $id = $this->Previous_loans_model->insert($data);


			$this->Documents_model->abc($this->input->post('filen'),$this->input->post('first_name',TRUE), $id);
            $this->toaster->success('Create doc Success');
            redirect(site_url('previous_loans'));
        }
    }
    
    public function update($id) 
    {
        $row = $this->Previous_loans_model->get_by_id($id);

        if ($row) {
            $data = array(
                'button' => 'Update',
                'action' => site_url('previous_loans/update_action'),
		'p_lid' => set_value('p_lid', $row->p_lid),
		'customer_id' => set_value('customer_id', $row->customer_id),
		'loan_effective_date' => set_value('loan_effective_date', $row->loan_effective_date),
		'loan_end_date' => set_value('loan_end_date', $row->loan_end_date),
		'amount' => set_value('amount', $row->amount),
		'status' => set_value('status', $row->status),
		'added_by' => set_value('added_by', $row->added_by),
		'stamp' => set_value('stamp', $row->stamp),
	    );
            $this->load->view('previous_loans/previous_loans_form', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('previous_loans'));
        }
    }
    
    public function update_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('p_lid', TRUE));
        } else {
            $data = array(
		'customer_id' => $this->input->post('customer_id',TRUE),
		'loan_effective_date' => $this->input->post('loan_effective_date',TRUE),
		'loan_end_date' => $this->input->post('loan_end_date',TRUE),
		'amount' => $this->input->post('amount',TRUE),
		'status' => $this->input->post('status',TRUE),
		'added_by' => $this->input->post('added_by',TRUE),
		'stamp' => $this->input->post('stamp',TRUE),
	    );

            $this->Previous_loans_model->update($this->input->post('p_lid', TRUE), $data);
            $this->session->set_flashdata('message', 'Update Record Success');
            redirect(site_url('previous_loans'));
        }
    }
    
    public function delete($id) 
    {
        $row = $this->Previous_loans_model->get_by_id($id);

        if ($row) {
            $this->Previous_loans_model->delete($id);
            $this->session->set_flashdata('message', 'Delete Record Success');
            redirect(site_url('previous_loans'));
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('previous_loans'));
        }
    }

    public function _rules() 
    {
	$this->form_validation->set_rules('customer_id', 'customer id', 'trim|required');
	$this->form_validation->set_rules('loan_effective_date', 'loan effective date', 'trim|required');
	$this->form_validation->set_rules('loan_end_date', 'loan end date', 'trim|required');
	$this->form_validation->set_rules('amount', 'amount taken', 'trim|required|numeric');
	$this->form_validation->set_rules('amount_paid', 'amount paid', 'trim|required|numeric');
	$this->form_validation->set_rules('status', 'status', 'trim|required');


	$this->form_validation->set_rules('p_lid', 'p_lid', 'trim');
	$this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}


