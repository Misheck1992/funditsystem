<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Loan_products extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Loan_products_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));

        if ($q <> '') {
            $config['base_url'] = base_url() . 'loan_products/index?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'loan_products/index?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'loan_products/index';
            $config['first_url'] = base_url() . 'loan_products/index';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Loan_products_model->total_rows($q);
        $loan_products = $this->Loan_products_model->get_all();

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'loan_products_data' => $loan_products,
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
        $this->load->view('admin/header');
        $this->load->view('loan_products/loan_products_list', $data);
        $this->load->view('admin/footer');
    }
    
       public function edit_list()
    {
        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));

        if ($q <> '') {
            $config['base_url'] = base_url() . 'loan_products/inde?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'loan_products/inde?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'loan_products/inde';
            $config['first_url'] = base_url() . 'loan_products/inde';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Loan_products_model->total_rows($q);
        $loan_products = $this->Loan_products_model->get_all();

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'loan_products_data' => $loan_products,
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
        $this->load->view('admin/header');
        $this->load->view('loan_products/loan_product_edit_list', $data);
        $this->load->view('admin/footer');
    }
       public function delete_list()
    {
        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));

        if ($q <> '') {
            $config['base_url'] = base_url() . 'loan_products/index?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'loan_products/index?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'loan_products/index';
            $config['first_url'] = base_url() . 'loan_products/index';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Loan_products_model->total_rows($q);
        $loan_products = $this->Loan_products_model->get_all();

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'loan_products_data' => $loan_products,
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
        $this->load->view('admin/header');
        $this->load->view('loan_products/loan_product_delete_list', $data);
        $this->load->view('admin/footer');
    }

    public function read($id)
    {
        $row = $this->Loan_products_model->get_by_id($id);
        if ($row) {
            $data = array(
                'loan_product_id' => $row->loan_product_id,
                'product_name' => $row->product_name,
                'abbreviation' => $row->abbreviation,
                'interest' => $row->interest,
                'frequency' => $row->frequency,
                'penalty' => $row->penalty,
                'penalty_threshold' => $row->penalty_threshold,
                'penalty_charge_type' => $row->penalty_charge_type,
                'penalty_fixed_charge_below' => $row->penalty_fixed_charge_below,
                'penalty_variable_charge_below' => $row->penalty_variable_charge_below,
                'penalty_fixed_charge_above' => $row->penalty_fixed_charge_above,
                'penalty_variable_charge_above' => $row->penalty_variable_charge_above,
                'loan_processing_fee_threshold' => $row->loan_processing_fee_threshold,
                'processing_charge_type' => $row->processing_charge_type,
                'processing_fixed_charge_above' => $row->processing_fixed_charge_above,
                'processing_variable_charge_above' => $row->processing_variable_charge_above,
                'processing_fixed_charge_below' => $row->processing_fixed_charge_below,
                'processing_variable_charge_below' => $row->processing_variable_charge_below,
                'minimum_principal' => $row->minimum_principal,
                'maximum_principal' => $row->maximum_principal,
           		'interest_min' => $row->interest_min,
                'interest_max' => $row->interest_max,
                'added_by' => $row->added_by,
                'date_created' => $row->date_created,
            );
            $this->load->view('admin/header');
            $this->load->view('loan_products/loan_products_read', $data);
            $this->load->view('admin/footer');

        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
//            redirect(site_url('loan_products'));
        }
    }

    public function create()
    {
        $data = array(
            'button' => 'Create',
            'action' => site_url('loan_products/create_action'),
            'loan_product_id' => set_value('loan_product_id'),
            'product_name' => set_value('product_name'),
            'abbreviation' => set_value('abbreviation'),
            'interest' => set_value('interest'),
            'frequency' => set_value('frequency'),
            'penalty' => set_value('penalty'),
            'penalty_threshold' => set_value('penalty_threshold'),
            'penalty_charge_type' => set_value('penalty_charge_type'),
            'penalty_fixed_charge_below' => set_value('penalty_fixed_charge_below'),
            'penalty_variable_charge_below' => set_value('penalty_variable_charge_below'),
            'penalty_fixed_charge_above' => set_value('penalty_fixed_charge_above'),
            'penalty_variable_charge_above' => set_value('penalty_variable_charge_above'),
            'loan_processing_fee_threshold' => set_value('loan_processing_fee_threshold'),
            'processing_charge_type' => set_value('processing_charge_type'),
            'processing_fixed_charge_above' => set_value('processing_fixed_charge_above'),
            'processing_variable_charge_above' => set_value('processing_variable_charge_above'),
            'processing_fixed_charge_below' => set_value('processing_fixed_charge_below'),
            'processing_variable_charge_below' => set_value('processing_variable_charge_below'),
            'minimum_principal' => set_value('minimum_principal'),
            'maximum_principal' => set_value('maximum_principal'),
            'interest_min' => set_value('interest_min'),
            'interest_max' => set_value('interest_max'),
            'grace_period' => set_value('grace_period'),
            'added_by' => set_value('added_by'),
            'date_created' => set_value('date_created'),
            'calculation_type' => set_value('calculation_type'),
        );
        $this->load->view('admin/header');
        $this->load->view('loan_products/loan_products_form', $data);
        $this->load->view('admin/footer');
    }

    public function create_action()
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            $data = array(
                'product_name' => $this->input->post('product_name',TRUE),
                'abbreviation' => $this->input->post('abbreviation',TRUE),
                'interest' => $this->input->post('interest',TRUE),
                'frequency' => $this->input->post('frequency',TRUE),
                'calculation_type' => $this->input->post('calculation_type',TRUE),

                'penalty_threshold' => $this->input->post('penalty_threshold',TRUE),
                'penalty_charge_type_above' => $this->input->post('penalty_charge_type_above',TRUE),
                'penalty_charge_type_below' => $this->input->post('penalty_charge_type_below',TRUE),
                'penalty_fixed_charge_below' => $this->input->post('penalty_fixed_charge_below',TRUE),
                'penalty_variable_charge_below' => $this->input->post('penalty_variable_charge_below',TRUE),
                'penalty_fixed_charge_above' => $this->input->post('penalty_fixed_charge_above',TRUE),
                'penalty_variable_charge_above' => $this->input->post('penalty_variable_charge_above',TRUE),
                'loan_processing_fee_threshold' => $this->input->post('loan_processing_fee_threshold',TRUE),
                'processing_charge_type_above' => $this->input->post('processing_charge_type_above',TRUE),
                'processing_charge_type_below' => $this->input->post('processing_charge_type_below',TRUE),
                'processing_fixed_charge_above' => $this->input->post('processing_fixed_charge_above',TRUE),
                'processing_variable_charge_above' => $this->input->post('processing_variable_charge_above',TRUE),
                'processing_fixed_charge_below' => $this->input->post('processing_fixed_charge_below',TRUE),
                'processing_variable_charge_below' => $this->input->post('processing_variable_charge_below',TRUE),
                'minimum_principal' => $this->input->post('minimum_principal',TRUE),
                'maximum_principal' => $this->input->post('maximum_principal',TRUE),
                'interest_min' => $this->input->post('interest_min',TRUE),
                'interest_max' => $this->input->post('interest_max',TRUE),
                 'grace_period' =>  $this->input->post('grace_period',TRUE),
                'added_by' => $this->session->userdata('user_id'),

            );

            $this->Loan_products_model->insert($data);
            $this->session->set_flashdata('message', 'Create Record Success');
            redirect(site_url('loan_products'));
        }
    }

    public function update($id)
    {
        $row = $this->Loan_products_model->get_by_id($id);

        if ($row) {
            $data = array(
                'button' => 'Update',
                'action' => site_url('loan_products/update_action'),
                'loan_product_id' => set_value('loan_product_id', $row->loan_product_id),
                'product_name' => set_value('product_name', $row->product_name),
                'abbreviation' => set_value('abbreviation', $row->abbreviation),
                'interest' => set_value('interest', $row->interest),
                'frequency' => set_value('frequency', $row->frequency),
                'calculation_type' => set_value('calculation_type', $row->penalty),
                'penalty' => set_value('penalty', $row->penalty),
                'penalty_threshold' => set_value('penalty_threshold', $row->penalty_threshold),
               
                'penalty_fixed_charge_below' => set_value('penalty_fixed_charge_below', $row->penalty_fixed_charge_below),
                'penalty_variable_charge_below' => set_value('penalty_variable_charge_below', $row->penalty_variable_charge_below),
                'penalty_fixed_charge_above' => set_value('penalty_fixed_charge_above', $row->penalty_fixed_charge_above),
                'penalty_variable_charge_above' => set_value('penalty_variable_charge_above', $row->penalty_variable_charge_above),
                'loan_processing_fee_threshold' => set_value('loan_processing_fee_threshold', $row->loan_processing_fee_threshold),
            
                'processing_fixed_charge_above' => set_value('processing_fixed_charge_above', $row->processing_fixed_charge_above),
                'processing_variable_charge_above' => set_value('processing_variable_charge_above', $row->processing_variable_charge_above),
                'processing_fixed_charge_below' => set_value('processing_fixed_charge_below', $row->processing_fixed_charge_below),
                'processing_variable_charge_below' => set_value('processing_variable_charge_below', $row->processing_variable_charge_below),
                'minimum_principal' => set_value('minimum_principal', $row->minimum_principal),
                'maximum_principal' => set_value('maximum_principal', $row->maximum_principal),
               'interest_min' => set_value('interest_min', $row->interest_min),
                'interest_max' => set_value('interest_max', $row->interest_max),
                 'grace_period' =>  set_value('grace_period',$row->grace_period),
                'added_by' => set_value('added_by', $row->added_by),
                'date_created' => set_value('date_created', $row->date_created),
            );
            $this->load->view('admin/header');
            $this->load->view('loan_products/loan_products_form', $data);
            $this->load->view('admin/footer');
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('loan_products'));
        }
    }

    public function update_action()
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('loan_product_id', TRUE));
        } else {
            $data = array(
                'product_name' => $this->input->post('product_name',TRUE),
                'interest' => $this->input->post('interest',TRUE),
                'frequency' => $this->input->post('frequency',TRUE),
                'penalty' => $this->input->post('penalty',TRUE),
                'penalty_threshold' => $this->input->post('penalty_threshold',TRUE),
           
                'penalty_fixed_charge_below' => $this->input->post('penalty_fixed_charge_below',TRUE),
                'penalty_variable_charge_below' => $this->input->post('penalty_variable_charge_below',TRUE),
                'penalty_fixed_charge_above' => $this->input->post('penalty_fixed_charge_above',TRUE),
                'penalty_variable_charge_above' => $this->input->post('penalty_variable_charge_above',TRUE),
                'loan_processing_fee_threshold' => $this->input->post('loan_processing_fee_threshold',TRUE),
             
                'processing_fixed_charge_above' => $this->input->post('processing_fixed_charge_above',TRUE),
                'processing_variable_charge_above' => $this->input->post('processing_variable_charge_above',TRUE),
                'processing_fixed_charge_below' => $this->input->post('processing_fixed_charge_below',TRUE),
                'processing_variable_charge_below' => $this->input->post('processing_variable_charge_below',TRUE),
                'minimum_principal' => $this->input->post('minimum_principal',TRUE),
                'maximum_principal' => $this->input->post('maximum_principal',TRUE),
                 'grace_period' =>  $this->input->post('grace_period',TRUE),
                'added_by' => $this->input->post('added_by',TRUE),
                'date_created' => $this->input->post('date_created',TRUE),
            );

            $this->Loan_products_model->update($this->input->post('loan_product_id', TRUE), $data);
            $this->session->set_flashdata('message', 'Update Record Success');
            redirect(site_url('loan_products'));
        }
    }

    public function delete($id)
    {
        $row = $this->Loan_products_model->get_by_id($id);

        if ($row) {
            $this->Loan_products_model->delete($id);
            $this->session->set_flashdata('message', 'Delete Record Success');
            redirect(site_url('loan_products'));
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('loan_products'));
        }
    }

    public function _rules()
    {
        $this->form_validation->set_rules('product_name', 'product name', 'trim|required');
        $this->form_validation->set_rules('interest', 'interest', 'trim|required|numeric');
        $this->form_validation->set_rules('frequency', 'frequency', 'trim|required');
         $this->form_validation->set_rules('minimum_principal', 'minimum principal', 'trim|required|numeric');
        $this->form_validation->set_rules('maximum_principal', 'maximum principal', 'trim|required|numeric');
       

        $this->form_validation->set_rules('loan_product_id', 'loan_product_id', 'trim');
        $this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

