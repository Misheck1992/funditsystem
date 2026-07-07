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
        $q = $this->input->get('q', TRUE);
        $start = intval($this->input->get('start'));

        if ($q <> '') {
            $config['base_url'] = base_url() . 'loan_products/index?q=' . $q;
            $config['first_url'] = base_url() . 'loan_products/index?q=' . $q;
        } else {
            $config['base_url'] = base_url() . 'loan_products/index';
            $config['first_url'] = base_url() . 'loan_products/index';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Loan_products_model->total_rows($q);

        // Fix: Use get_limit_data instead of get_all for pagination
        $loan_products = $this->Loan_products_model->get_limit_data($config['per_page'], $start, $q);

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
            // Fix: Corrected typo from 'inde' to 'edit_list'
            $config['base_url'] = base_url() . 'loan_products/edit_list?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'loan_products/edit_list?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'loan_products/edit_list';
            $config['first_url'] = base_url() . 'loan_products/edit_list';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Loan_products_model->total_rows($q);

        // Fix: Use get_limit_data instead of get_all for pagination
        $loan_products = $this->Loan_products_model->get_limit_data($config['per_page'], $start, $q);

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
            // Fix: Corrected to use 'delete_list' instead of 'index'
            $config['base_url'] = base_url() . 'loan_products/delete_list?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'loan_products/delete_list?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'loan_products/delete_list';
            $config['first_url'] = base_url() . 'loan_products/delete_list';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Loan_products_model->total_rows($q);

        // Fix: Use get_limit_data instead of get_all for pagination
        $loan_products = $this->Loan_products_model->get_limit_data($config['per_page'], $start, $q);

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

    public function create()
    {
        $data = array(
            'button' => 'Create',
            'action' => site_url('Loan_products/create_action'),
            'loan_product_id' => '',
            'product_name' => set_value('product_name', ''),
            'abbreviation' => set_value('abbreviation', ''),
            'interest' => set_value('interest', ''),
            'frequency' => set_value('frequency', ''),
            'calculation_type' => set_value('calculation_type', ''),
            'penalty' => set_value('penalty', ''),
            'penalty_threshold' => set_value('penalty_threshold', '0'),
            'penalty_charge_type_below' => set_value('penalty_charge_type_below', ''),
            'penalty_charge_type_above' => set_value('penalty_charge_type_above', ''),
            'penalty_fixed_charge_below' => set_value('penalty_fixed_charge_below', '0'),
            'penalty_variable_charge_below' => set_value('penalty_variable_charge_below', '0'),
            'penalty_fixed_charge_above' => set_value('penalty_fixed_charge_above', '0'),
            'penalty_variable_charge_above' => set_value('penalty_variable_charge_above', '0'),
            'loan_processing_fee_threshold' => set_value('loan_processing_fee_threshold', '0'),
            'processing_charge_type_above' => set_value('processing_charge_type_above', ''),
            'processing_charge_type_below' => set_value('processing_charge_type_below', ''),
            'processing_fixed_charge_above' => set_value('processing_fixed_charge_above', '0'),
            'processing_variable_charge_above' => set_value('processing_variable_charge_above', '0'),
            'processing_fixed_charge_below' => set_value('processing_fixed_charge_below', '0'),
            'processing_variable_charge_below' => set_value('processing_variable_charge_below', '0'),
            'minimum_principal' => set_value('minimum_principal', '0'),
            'maximum_principal' => set_value('maximum_principal', '0'),
            'interest_min' => set_value('interest_min', '0'),
            'interest_max' => set_value('interest_max', '0'),
            'grace_period' => set_value('grace_period', '0'),
        );
        $this->load->view('admin/header');
        $this->load->view('loan_products/loan_products_form', $data);
        $this->load->view('admin/footer');
    }

    public function create_action()
    {
        $this->form_validation->set_rules('product_name', 'Product Name', 'required');
        $this->form_validation->set_rules('interest', 'Interest', 'required');
        $this->form_validation->set_rules('frequency', 'Frequency', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            $data = array(
                'product_name' => $this->input->post('product_name', TRUE),
                'abbreviation' => $this->input->post('abbreviation', TRUE),
                'interest' => $this->input->post('interest', TRUE),
                'frequency' => $this->input->post('frequency', TRUE),
                'calculation_type' => $this->input->post('calculation_type', TRUE),
                'penalty_threshold' => $this->input->post('penalty_threshold', TRUE),
                'penalty_charge_type_below' => $this->input->post('penalty_charge_type_below', TRUE),
                'penalty_charge_type_above' => $this->input->post('penalty_charge_type_above', TRUE),
                'penalty_fixed_charge_below' => $this->input->post('penalty_fixed_charge_below', TRUE),
                'penalty_variable_charge_below' => $this->input->post('penalty_variable_charge_below', TRUE),
                'penalty_fixed_charge_above' => $this->input->post('penalty_fixed_charge_above', TRUE),
                'penalty_variable_charge_above' => $this->input->post('penalty_variable_charge_above', TRUE),
                'loan_processing_fee_threshold' => $this->input->post('loan_processing_fee_threshold', TRUE),
                'processing_charge_type_above' => $this->input->post('processing_charge_type_above', TRUE),
                'processing_charge_type_below' => $this->input->post('processing_charge_type_below', TRUE),
                'processing_fixed_charge_above' => $this->input->post('processing_fixed_charge_above', TRUE),
                'processing_variable_charge_above' => $this->input->post('processing_variable_charge_above', TRUE),
                'processing_fixed_charge_below' => $this->input->post('processing_fixed_charge_below', TRUE),
                'processing_variable_charge_below' => $this->input->post('processing_variable_charge_below', TRUE),
                'minimum_principal' => $this->input->post('minimum_principal', TRUE),
                'maximum_principal' => $this->input->post('maximum_principal', TRUE),
                'interest_min' => $this->input->post('interest_min', TRUE),
                'interest_max' => $this->input->post('interest_max', TRUE),
                'grace_period' => $this->input->post('grace_period', TRUE),
                'added_by' => $this->session->userdata('user_id'),
                'date_created' => date('Y-m-d H:i:s'),
            );

            $this->Loan_products_model->insert($data);
            $this->session->set_flashdata('message', 'Loan product created successfully');
            redirect(site_url('Loan_products'));
        }
    }

    public function update($id)
    {
        $row = $this->Loan_products_model->get_by_id($id);

        if ($row) {
            $data = array(
                'button' => 'Update',
                'action' => site_url('Loan_products/update_action'),
                'loan_product_id' => $row->loan_product_id,
                'product_name' => set_value('product_name', $row->product_name),
                'abbreviation' => set_value('abbreviation', isset($row->abbreviation) ? $row->abbreviation : ''),
                'interest' => set_value('interest', $row->interest),
                'frequency' => set_value('frequency', $row->frequency),
                'calculation_type' => set_value('calculation_type', isset($row->calculation_type) ? $row->calculation_type : ''),
                'penalty' => set_value('penalty', isset($row->penalty) ? $row->penalty : ''),
                'penalty_threshold' => set_value('penalty_threshold', $row->penalty_threshold),
                'penalty_charge_type_below' => set_value('penalty_charge_type_below', $row->penalty_charge_type_below),
                'penalty_charge_type_above' => set_value('penalty_charge_type_above', $row->penalty_charge_type_above),
                'penalty_fixed_charge_below' => set_value('penalty_fixed_charge_below', $row->penalty_fixed_charge_below),
                'penalty_variable_charge_below' => set_value('penalty_variable_charge_below', $row->penalty_variable_charge_below),
                'penalty_fixed_charge_above' => set_value('penalty_fixed_charge_above', $row->penalty_fixed_charge_above),
                'penalty_variable_charge_above' => set_value('penalty_variable_charge_above', $row->penalty_variable_charge_above),
                'loan_processing_fee_threshold' => set_value('loan_processing_fee_threshold', $row->loan_processing_fee_threshold),
                'processing_charge_type_above' => set_value('processing_charge_type_above', $row->processing_charge_type_above),
                'processing_charge_type_below' => set_value('processing_charge_type_below', $row->processing_charge_type_below),
                'processing_fixed_charge_above' => set_value('processing_fixed_charge_above', $row->processing_fixed_charge_above),
                'processing_variable_charge_above' => set_value('processing_variable_charge_above', $row->processing_variable_charge_above),
                'processing_fixed_charge_below' => set_value('processing_fixed_charge_below', $row->processing_fixed_charge_below),
                'processing_variable_charge_below' => set_value('processing_variable_charge_below', $row->processing_variable_charge_below),
                'minimum_principal' => set_value('minimum_principal', $row->minimum_principal),
                'maximum_principal' => set_value('maximum_principal', $row->maximum_principal),
                'interest_min' => set_value('interest_min', $row->interest_min),
                'interest_max' => set_value('interest_max', $row->interest_max),
                'grace_period' => set_value('grace_period', isset($row->grace_period) ? $row->grace_period : '0'),
            );
            $this->load->view('admin/header');
            $this->load->view('loan_products/loan_products_form', $data);
            $this->load->view('admin/footer');
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('Loan_products'));
        }
    }

    public function update_action()
    {
        $this->form_validation->set_rules('product_name', 'Product Name', 'required');
        $this->form_validation->set_rules('interest', 'Interest', 'required');
        $this->form_validation->set_rules('frequency', 'Frequency', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('loan_product_id', TRUE));
        } else {
            $data = array(
                'product_name' => $this->input->post('product_name', TRUE),
                'abbreviation' => $this->input->post('abbreviation', TRUE),
                'interest' => $this->input->post('interest', TRUE),
                'frequency' => $this->input->post('frequency', TRUE),
                'calculation_type' => $this->input->post('calculation_type', TRUE),
                'penalty_threshold' => $this->input->post('penalty_threshold', TRUE),
                'penalty_charge_type_below' => $this->input->post('penalty_charge_type_below', TRUE),
                'penalty_charge_type_above' => $this->input->post('penalty_charge_type_above', TRUE),
                'penalty_fixed_charge_below' => $this->input->post('penalty_fixed_charge_below', TRUE),
                'penalty_variable_charge_below' => $this->input->post('penalty_variable_charge_below', TRUE),
                'penalty_fixed_charge_above' => $this->input->post('penalty_fixed_charge_above', TRUE),
                'penalty_variable_charge_above' => $this->input->post('penalty_variable_charge_above', TRUE),
                'loan_processing_fee_threshold' => $this->input->post('loan_processing_fee_threshold', TRUE),
                'processing_charge_type_above' => $this->input->post('processing_charge_type_above', TRUE),
                'processing_charge_type_below' => $this->input->post('processing_charge_type_below', TRUE),
                'processing_fixed_charge_above' => $this->input->post('processing_fixed_charge_above', TRUE),
                'processing_variable_charge_above' => $this->input->post('processing_variable_charge_above', TRUE),
                'processing_fixed_charge_below' => $this->input->post('processing_fixed_charge_below', TRUE),
                'processing_variable_charge_below' => $this->input->post('processing_variable_charge_below', TRUE),
                'minimum_principal' => $this->input->post('minimum_principal', TRUE),
                'maximum_principal' => $this->input->post('maximum_principal', TRUE),
                'interest_min' => $this->input->post('interest_min', TRUE),
                'interest_max' => $this->input->post('interest_max', TRUE),
                'grace_period' => $this->input->post('grace_period', TRUE),
            );

            $this->Loan_products_model->update($this->input->post('loan_product_id', TRUE), $data);
            $this->session->set_flashdata('message', 'Loan product updated successfully');
            redirect(site_url('Loan_products'));
        }
    }

    public function delete($id)
    {
        $row = $this->Loan_products_model->get_by_id($id);

        if ($row) {
            $this->Loan_products_model->delete($id);
            $this->session->set_flashdata('message', 'Loan product deleted successfully');
            redirect(site_url('Loan_products'));
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('Loan_products'));
        }
    }

    public function read($id)
    {
        $row = $this->Loan_products_model->get_by_id($id);

        if ($row) {
            $data = array(
                'loan_products' => $row,
            );
            $this->load->view('admin/header');
            $this->load->view('loan_products/loan_products_read', $data);
            $this->load->view('admin/footer');
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('Loan_products'));
        }
    }
}