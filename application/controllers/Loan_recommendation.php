<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Loan_recommendation extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Loan_recommendation_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));
        
        if ($q <> '') {
            $config['base_url'] = base_url() . 'loan_recommendation/index.html?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'loan_recommendation/index.html?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'loan_recommendation/index.html';
            $config['first_url'] = base_url() . 'loan_recommendation/index.html';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Loan_recommendation_model->total_rows($q);
        $loan_recommendation = $this->Loan_recommendation_model->get_limit_data($config['per_page'], $start, $q);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'loan_recommendation_data' => $loan_recommendation,
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
        $this->load->view('loan_recommendation/loan_recommendation_list', $data);
    }

    public function read($id) 
    {
        $row = $this->Loan_recommendation_model->get_by_id($id);
        if ($row) {
            $data = array(
		'loan_recomendation_id' => $row->loan_recomendation_id,
		'loan_id' => $row->loan_id,
		'application_form' => $row->application_form,
		'application_form_comment' => $row->application_form_comment,
		'letter_from_auth' => $row->letter_from_auth,
		'letter_from_auth_comment' => $row->letter_from_auth_comment,
		'commitment_fee' => $row->commitment_fee,
		'commitment_fee_comment' => $row->commitment_fee_comment,
		'land_evidence' => $row->land_evidence,
		'land_evidence_comment' => $row->land_evidence_comment,
		'offtaker_evidence' => $row->offtaker_evidence,
		'offtaker_evidence_comment' => $row->offtaker_evidence_comment,
		'training_recieved' => $row->training_recieved,
		'training_recieved_comment' => $row->training_recieved_comment,
		'loans_owed' => $row->loans_owed,
		'loans_owed_comment' => $row->loans_owed_comment,
		'community_character' => $row->community_character,
		'community_character_comment' => $row->community_character_comment,
		'date_stamp' => $row->date_stamp,
	    );
            $this->load->view('loan_recommendation/loan_recommendation_read', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('loan_recommendation'));
        }
    }

    public function create() 
    {
        $data = array(
            'button' => 'Create',
            'action' => site_url('loan_recommendation/create_action'),
	    'loan_recomendation_id' => set_value('loan_recomendation_id'),
	    'loan_id' => set_value('loan_id'),
	    'application_form' => set_value('application_form'),
	    'application_form_comment' => set_value('application_form_comment'),
	    'letter_from_auth' => set_value('letter_from_auth'),
	    'letter_from_auth_comment' => set_value('letter_from_auth_comment'),
	    'commitment_fee' => set_value('commitment_fee'),
	    'commitment_fee_comment' => set_value('commitment_fee_comment'),
	    'land_evidence' => set_value('land_evidence'),
	    'land_evidence_comment' => set_value('land_evidence_comment'),
	    'offtaker_evidence' => set_value('offtaker_evidence'),
	    'offtaker_evidence_comment' => set_value('offtaker_evidence_comment'),
	    'training_recieved' => set_value('training_recieved'),
	    'training_recieved_comment' => set_value('training_recieved_comment'),
	    'loans_owed' => set_value('loans_owed'),
	    'loans_owed_comment' => set_value('loans_owed_comment'),
	    'community_character' => set_value('community_character'),
	    'community_character_comment' => set_value('community_character_comment'),
	    'date_stamp' => set_value('date_stamp'),
	);
        $this->load->view('loan_recommendation/loan_recommendation_form', $data);
    }
    
    public function create_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            $data = array(
		'loan_id' => $this->input->post('loan_id',TRUE),
		'application_form' => $this->input->post('application_form',TRUE),
		'application_form_comment' => $this->input->post('application_form_comment',TRUE),
		'letter_from_auth' => $this->input->post('letter_from_auth',TRUE),
		'letter_from_auth_comment' => $this->input->post('letter_from_auth_comment',TRUE),
		'commitment_fee' => $this->input->post('commitment_fee',TRUE),
		'commitment_fee_comment' => $this->input->post('commitment_fee_comment',TRUE),
		'land_evidence' => $this->input->post('land_evidence',TRUE),
		'land_evidence_comment' => $this->input->post('land_evidence_comment',TRUE),
		'offtaker_evidence' => $this->input->post('offtaker_evidence',TRUE),
		'offtaker_evidence_comment' => $this->input->post('offtaker_evidence_comment',TRUE),
		'training_recieved' => $this->input->post('training_recieved',TRUE),
		'training_recieved_comment' => $this->input->post('training_recieved_comment',TRUE),
		'loans_owed' => $this->input->post('loans_owed',TRUE),
		'loans_owed_comment' => $this->input->post('loans_owed_comment',TRUE),
		'community_character' => $this->input->post('community_character',TRUE),
		'community_character_comment' => $this->input->post('community_character_comment',TRUE),
		'date_stamp' => $this->input->post('date_stamp',TRUE),
	    );

            $this->Loan_recommendation_model->insert($data);
            $this->session->set_flashdata('message', 'Create Record Success');
            redirect(site_url('loan_recommendation'));
        }
    }
    
    public function update($id) 
    {
        $row = $this->Loan_recommendation_model->get_by_id($id);

        if ($row) {
            $data = array(
                'button' => 'Update',
                'action' => site_url('loan_recommendation/update_action'),
		'loan_recomendation_id' => set_value('loan_recomendation_id', $row->loan_recomendation_id),
		'loan_id' => set_value('loan_id', $row->loan_id),
		'application_form' => set_value('application_form', $row->application_form),
		'application_form_comment' => set_value('application_form_comment', $row->application_form_comment),
		'letter_from_auth' => set_value('letter_from_auth', $row->letter_from_auth),
		'letter_from_auth_comment' => set_value('letter_from_auth_comment', $row->letter_from_auth_comment),
		'commitment_fee' => set_value('commitment_fee', $row->commitment_fee),
		'commitment_fee_comment' => set_value('commitment_fee_comment', $row->commitment_fee_comment),
		'land_evidence' => set_value('land_evidence', $row->land_evidence),
		'land_evidence_comment' => set_value('land_evidence_comment', $row->land_evidence_comment),
		'offtaker_evidence' => set_value('offtaker_evidence', $row->offtaker_evidence),
		'offtaker_evidence_comment' => set_value('offtaker_evidence_comment', $row->offtaker_evidence_comment),
		'training_recieved' => set_value('training_recieved', $row->training_recieved),
		'training_recieved_comment' => set_value('training_recieved_comment', $row->training_recieved_comment),
		'loans_owed' => set_value('loans_owed', $row->loans_owed),
		'loans_owed_comment' => set_value('loans_owed_comment', $row->loans_owed_comment),
		'community_character' => set_value('community_character', $row->community_character),
		'community_character_comment' => set_value('community_character_comment', $row->community_character_comment),
		'date_stamp' => set_value('date_stamp', $row->date_stamp),
	    );
            $this->load->view('loan_recommendation/loan_recommendation_form', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('loan_recommendation'));
        }
    }
    
    public function update_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('loan_recomendation_id', TRUE));
        } else {
            $data = array(
		'loan_id' => $this->input->post('loan_id',TRUE),
		'application_form' => $this->input->post('application_form',TRUE),
		'application_form_comment' => $this->input->post('application_form_comment',TRUE),
		'letter_from_auth' => $this->input->post('letter_from_auth',TRUE),
		'letter_from_auth_comment' => $this->input->post('letter_from_auth_comment',TRUE),
		'commitment_fee' => $this->input->post('commitment_fee',TRUE),
		'commitment_fee_comment' => $this->input->post('commitment_fee_comment',TRUE),
		'land_evidence' => $this->input->post('land_evidence',TRUE),
		'land_evidence_comment' => $this->input->post('land_evidence_comment',TRUE),
		'offtaker_evidence' => $this->input->post('offtaker_evidence',TRUE),
		'offtaker_evidence_comment' => $this->input->post('offtaker_evidence_comment',TRUE),
		'training_recieved' => $this->input->post('training_recieved',TRUE),
		'training_recieved_comment' => $this->input->post('training_recieved_comment',TRUE),
		'loans_owed' => $this->input->post('loans_owed',TRUE),
		'loans_owed_comment' => $this->input->post('loans_owed_comment',TRUE),
		'community_character' => $this->input->post('community_character',TRUE),
		'community_character_comment' => $this->input->post('community_character_comment',TRUE),
		'date_stamp' => $this->input->post('date_stamp',TRUE),
	    );

            $this->Loan_recommendation_model->update($this->input->post('loan_recomendation_id', TRUE), $data);
            $this->session->set_flashdata('message', 'Update Record Success');
            redirect(site_url('loan_recommendation'));
        }
    }
    
    public function delete($id) 
    {
        $row = $this->Loan_recommendation_model->get_by_id($id);

        if ($row) {
            $this->Loan_recommendation_model->delete($id);
            $this->session->set_flashdata('message', 'Delete Record Success');
            redirect(site_url('loan_recommendation'));
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('loan_recommendation'));
        }
    }

    public function _rules() 
    {
	$this->form_validation->set_rules('loan_id', 'loan id', 'trim|required');
	$this->form_validation->set_rules('application_form', 'application form', 'trim|required');
	$this->form_validation->set_rules('application_form_comment', 'application form comment', 'trim|required');
	$this->form_validation->set_rules('letter_from_auth', 'letter from auth', 'trim|required');
	$this->form_validation->set_rules('letter_from_auth_comment', 'letter from auth comment', 'trim|required');
	$this->form_validation->set_rules('commitment_fee', 'commitment fee', 'trim|required');
	$this->form_validation->set_rules('commitment_fee_comment', 'commitment fee comment', 'trim|required');
	$this->form_validation->set_rules('land_evidence', 'land evidence', 'trim|required');
	$this->form_validation->set_rules('land_evidence_comment', 'land evidence comment', 'trim|required');
	$this->form_validation->set_rules('offtaker_evidence', 'offtaker evidence', 'trim|required');
	$this->form_validation->set_rules('offtaker_evidence_comment', 'offtaker evidence comment', 'trim|required');
	$this->form_validation->set_rules('training_recieved', 'training recieved', 'trim|required');
	$this->form_validation->set_rules('training_recieved_comment', 'training recieved comment', 'trim|required');
	$this->form_validation->set_rules('loans_owed', 'loans owed', 'trim|required');
	$this->form_validation->set_rules('loans_owed_comment', 'loans owed comment', 'trim|required');
	$this->form_validation->set_rules('community_character', 'community character', 'trim|required');
	$this->form_validation->set_rules('community_character_comment', 'community character comment', 'trim|required');
	$this->form_validation->set_rules('date_stamp', 'date stamp', 'trim|required');

	$this->form_validation->set_rules('loan_recomendation_id', 'loan_recomendation_id', 'trim');
	$this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

