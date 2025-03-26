<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tellering extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Tellering_model');
        $this->load->model('Account_model');
        $this->load->model('Vault_cashier_pends_model');
        $this->load->model('Cashier_vault_pends_model');
        $this->load->model('Payement_schedules_model');
		$this->load->model('Loan_model');
		$this->load->model('Employees_model');
		$this->load->model('Individual_customers_model');
		$this->load->model('Transactions_model');
        $this->load->model('Transaction_model');
		$this->load->model('Global_config_model');
		$this->load->model('Borrowed_repayements_model');
        $this->load->library('form_validation');
    }

    public function index()
    {
        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));
        
        if ($q <> '') {
            $config['base_url'] = base_url() . 'tellering/index.html?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'tellering/index.html?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'tellering/index.html';
            $config['first_url'] = base_url() . 'tellering/index.html';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Tellering_model->total_rows($q);
        $tellering = $this->Tellering_model->get_limit_data($config['per_page'], $start, $q);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'tellering_data' => $tellering,
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
        $this->load->view('tellering/tellering_list', $data);
    }

    public function read($id) 
    {
        $row = $this->Tellering_model->get_by_id($id);
        if ($row) {
            $data = array(
		'id' => $row->id,
		'teller' => $row->teller,
		'account' => $row->account,
		'date_time' => $row->date_time,
	    );
            $this->load->view('tellering/tellering_read', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('tellering'));
        }
    }

    public function track_transactions_view() 
    {

             $this->load->view('admin/header');
             $this->load->view('tellering/transactions_view');
             $this->load->view('admin/footer');
         
     }
    public function initiate_delete_payment()
    {

        $this->load->view('admin/header');
        $this->load->view('tellering/delete_payment_view');
        $this->load->view('admin/footer');

    }

    public function recomend_delete_payment()
    {

        $data['loan_data'] = $this->Transaction_model->recomended_delete_transactions();

        $this->load->view('admin/header');
        $this->load->view('tellering/recommended_deleted',$data);
        $this->load->view('admin/footer');


    }

       public  function update_recommend_delete($loannumber)
       {
           date_default_timezone_set("Africa/Blantyre");
           $date = new DateTime("now");

           $curr_date = $date->format('Y-m-d');



           $loandata=get_by_id('loan','loan_id',$loannumber );

           if(!empty($loandata)){
               $data = array(
                   'loan_id' => $loandata->loan_id,
                   'is_initiated' => 'yes',
                   'initiated_date' =>  $curr_date,
                   'initiated_by'=>$this->session->userdata('user_id'),
                   'reason_for_deleting' => 'Deleting payments',
                   'is_recommended' => 'yes',
                   'recomended_date ' =>  $curr_date,
                   'recomended_by '=>$this->session->userdata('user_id'),

               );
               $this->Tellering_model->insert_delete_payments($data);
               redirect(site_url('tellering/recommended_delete_payment/'.$loandata->loan_number));


           }
           else{
               redirect(site_url('tellering/initiate_delete_payment'));
           }

       }

    public function recommended_delete_payment()
    {
        $data['loan_data'] = $this->Transaction_model->recomended_delete_transactions();

        $this->load->view('admin/header');
        $this->load->view('tellering/recommended_deleted',$data);
        $this->load->view('admin/footer');

    }

        public function Approved_delete_payment()
    {

        $data['loan_data'] = $this->Transaction_model->recomended_delete_transactions();
        $this->load->view('admin/header');
        $this->load->view('tellering/approved_deleted',$data);
        $this->load->view('admin/footer');

    }
    public function track_transaction() 
   {

    $loannumber = $this->input->POST('loannumber');
		
		$search = $this->input->POST('search');
		if($search=="filter"){
			$data['loan_data'] = $this->Transaction_model->track_transactions($loannumber);
			$this->load->view('admin/header');
			$this->load->view('tellering/transactions',$data);
			$this->load->view('admin/footer');
		}elseif($search=='pdf'){
			$data['loan_data'] = $this->Transaction_model->track_transactions($loannumber);
			
			$this->load->library('Pdf');
			$html = $this->load->view('reports/transactions_pdf', $data,true);
			$this->pdf->createPDF($html, "transactions report as on".date('Y-m-d'), true,'A4','landscape');
		}elseif($search=='excel'){

		}else {
			$data['loan_data'] = $this->Transaction_model->report($loannumber);
			$this->load->view('admin/header');
			$this->load->view('tellering/transactions', $data);
			$this->load->view('admin/footer');
		}
	}

    public function create() 
    {
        $data = array(
            'button' => 'Create',
            'action' => site_url('tellering/create_action'),
	    'id' => set_value('id'),
	    'teller' => set_value('teller'),
	    'account' => set_value('account'),
	    'date_time' => set_value('date_time'),
	);
        $this->load->view('tellering/tellering_form', $data);
    }

	public function create_action()
	{
		$this->_rules();

		if ($this->form_validation->run() == FALSE) {
			redirect($_SERVER['HTTP_REFERER']);
		} else {
			$data = array(
				'teller' => $this->input->post('teller',TRUE),
				'account' => $this->input->post('account',TRUE),

			);

			$this->Tellering_model->insert($data);
			$this->toaster->success('Teller Assigned to an account successfully');
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
    
    public function update($id) 
    {
        $row = $this->Tellering_model->get_by_id($id);

        if ($row) {
            $data = array(
                'button' => 'Update',
                'action' => site_url('tellering/update_action'),
		'id' => set_value('id', $row->id),
		'teller' => set_value('teller', $row->teller),
		'account' => set_value('account', $row->account),
		'date_time' => set_value('date_time', $row->date_time),
	    );
            $this->load->view('tellering/tellering_form', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('tellering'));
        }
    }
    
    public function update_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('id', TRUE));
        } else {
            $data = array(
		'teller' => $this->input->post('teller',TRUE),
		'account' => $this->input->post('account',TRUE),
		'date_time' => $this->input->post('date_time',TRUE),
	    );

            $this->Tellering_model->update($this->input->post('id', TRUE), $data);
            $this->session->set_flashdata('message', 'Update Record Success');
            redirect(site_url('tellering'));
        }
    }
    
    public function delete($id) 
    {
        $row = $this->Tellering_model->get_by_id($id);

        if ($row) {
            $this->Tellering_model->delete($id);
            $this->session->set_flashdata('message', 'Delete Record Success');
            redirect(site_url('tellering'));
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('tellering'));
        }
    }
	public function move_to_teller(){
    	$this->load->view('admin/header');
    	$this->load->view('tellering/move_to_teller');
    	$this->load->view('admin/footer');

	}
    public function _rules() 
    {
	$this->form_validation->set_rules('teller', 'teller', 'trim|required');
	$this->form_validation->set_rules('account', 'account', 'trim|required');


	$this->form_validation->set_rules('id', 'id', 'trim');
	$this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

