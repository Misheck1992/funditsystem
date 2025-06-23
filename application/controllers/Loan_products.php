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

    // ... rest of your controller methods remain the same
}