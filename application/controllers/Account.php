<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Account extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Account_model');
        $this->load->model('Transaction_model');
        $this->load->model('Tellering_model');
        $this->load->model('Vault_cashier_pends_model');
        $this->load->model('Cashier_vault_pends_model');
		$this->load->model('Individual_customers_model');
        $this->load->library('form_validation');
    }
	public function cash_transaction(){
		$tid="TR-S".rand(100,9999).date('Y').date('m').date('d');
		$result = array();
		$get_account = $this->Tellering_model->get_teller_account($this->session->userdata('user_id'));
		if(empty($get_account)){
			$result['status']= 'error';
			$result['message'] = "You are not authorized to do this transaction";
		}else{
			$teller_account = $get_account->account;

			$account = $this->input->post('account');
			$amount = $this->input->post('amount');
			$mode = $this->input->post('transaction_mode');
            $paid_date = $this->input->post('paid_date');
			$res =	$this->Account_model->cash_transaction($teller_account,$account,$amount,$mode,$tid,$paid_date);
			if($res=='success'){
				$result['status']= 'success';
				$result['data'] = array(
					'account'=>$account,
					'mode'=>$mode,
					'amount'=> number_format($amount,2),
					'transid'=>$tid,
					'reciept_no'=> '1'

				);
			}elseif($res=='teller'){
				$result['status']= 'error';
				$result['message'] = "Teller does not have enough fund, please request from vault";
			}elseif($res=='customer'){
				$result['status']= 'error';
				$result['message'] = "Customer does not have enough fund";
			}else{
				$result['status']= 'error';
				$result['message'] = "Internal errors";
			}
		}


		echo json_encode($result);
	}
	public function journal(){
    	$this->load->view('admin/header');
    	$this->load->view('account/journal');
    	$this->load->view('admin/footer');

	}

	public function accept_credit_teller($id){
		$row = $this->Vault_cashier_pends_model->get_by_id($id);

		if ($row) {
		$res=	$this->Account_model->vault_to_teller($row->vault_account,$row->teller_account,$row->amount);
		if($res == 'success'){
			$this->Vault_cashier_pends_model->update($id,array('status'=>'Approved','approved_by'=>$this->session->userdata('user_id')));
			$this->toaster->success('Success, Approval was successful');
			redirect($_SERVER['HTTP_REFERER']);
		}else{

			$this->toaster->error('Error,  Cashier does not have enough balance ,Approval was  not successful');
			redirect($_SERVER['HTTP_REFERER']);
		}

		} else {
			$this->toaster->error('error, record not found');
			redirect($_SERVER['HTTP_REFERER']);
		}

	}
	public function credit_teller(){
		$vault_account = $this->input->post('vault_account');

		$account = $this->input->post('account');
		$amount = $this->input->post('amount');
		$user = $this->Tellering_model->get_mine($account);

		$data = array(
			'vault_account' => $this->input->post('vault_account',TRUE),
			'teller_account' => $account,
			'amount' => $amount,
			'creator' => $this->session->userdata('user_id'),
			'teller' => $user->teller,


		);
		$this->Vault_cashier_pends_model->insert($data);
//	 	$res =	$this->Accounts_model->post_transaction($vault_account,$account,$amount);
		$this->toaster->success('Success, transfer initiated');
		redirect($_SERVER['HTTP_REFERER']);
	}
	public function get_teller_transaction($id){
		$re = $this->Transaction_model->get_my_transaction($id);

		$output = '';
		$output .='<table class="table table-bordered">';
		$output .= '<thead class="bg-primary text-white">
						<tr>
						<td>Trans ID</td>
						<td>System Time</td>
						<td>Teller Account No</td>
						<td>Credit</td>
						<td>Debit</td>
						<td>Balance</td>
						<td>Customer Acc</td>
						</tr>
					</thead>
					<tbody>
					';
		if(!empty($re)){
			$tcredit = 0;
			$tdebit = 0;
			foreach ($re as $dd){
				$tcredit += $dd->credit;
				$tdebit += $dd->debit;
				$output.= '
				<tr>
				<td>'.$dd->transaction_id.'</td>
				<td>'.$dd->system_time.'</td>
				<td>'.$dd->teller_account.'</td>
				<td>'.$dd->credit.'</td>
				<td>'.$dd->debit.'</td>
				<td>'.$dd->balance.'</td>
				<td>'.$dd->customer_account.'</td>
				</tr>
				
				';
			}
		}

		$output.='
<tfoot>
<tr>
<td></td>
<td></td>
<td></td>
<td>'.number_format($tcredit,2).'</td>
<td>'.number_format($tdebit,2).'</td>
<td>'.number_format($tcredit-$tdebit,2).'</td>
<td></td>
</tr>
</tfoot>
</tbody>
</table>';

		echo $output;
	}
	function get_teller_balance($id){

		$get_account = $this->Tellering_model->get_teller_account($id);
		echo json_encode(array('status'=>'success','balance'=>$get_account->balance));
	}
    public function index()
    {
        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));
        
        if ($q <> '') {
            $config['base_url'] = base_url() . 'account/index.html?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'account/index.html?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'account/index.html';
            $config['first_url'] = base_url() . 'account/index.html';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Account_model->total_rows($q);
        $account = $this->Account_model->get_limit_data($config['per_page'], $start, $q);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'account_data' => $account,
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
        $this->load->view('account/account_list', $data);
    }

    public function read($id) 
    {
        $row = $this->Account_model->get_by_id($id);
        if ($row) {
            $data = array(
		'account_id' => $row->account_id,
		'client_id' => $row->client_id,
		'account_number' => $row->account_number,
		'balance' => $row->balance,
		'account_type' => $row->account_type,
		'account_type_product' => $row->account_type_product,
		'added_by' => $row->added_by,
		'date_added' => $row->date_added,
	    );
            $this->load->view('account/account_read', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('account'));
        }
    }
	public function savings(){
		$data['account_data'] =$this->Account_model->get_all();
		$menu_toggle['toggles'] = 46;
		$this->load->view('admin/header', $menu_toggle);
		$this->load->view('account/savings_accounts',$data);
		$this->load->view('admin/footer');
	}
	public function edit(){
		$data['account_data'] =$this->Account_model->get_all();
		$menu_toggle['toggles'] = 46;
		$this->load->view('admin/header', $menu_toggle);
		$this->load->view('account/savings_accounts_edit',$data);
		$this->load->view('admin/footer');
	}
	public function to_delete(){
		$data['account_data'] =$this->Account_model->get_all();
		$menu_toggle['toggles'] = 46;
		$this->load->view('admin/header', $menu_toggle);
		$this->load->view('account/savings_accounts_delete',$data);
		$this->load->view('admin/footer');
	}
	public function to_deactivate(){
		$data['account_data'] =$this->Account_model->get_all();
		$menu_toggle['toggles'] = 46;
		$this->load->view('admin/header', $menu_toggle);
		$this->load->view('account/savings_accounts_block',$data);
		$this->load->view('admin/footer');
	}
	function get_vv($id){
$d = $this->Account_model->get_account($id);
echo json_encode(array('status'=>'success','data'=>$d));
	}
	public function teller(){

		$this->load->view('admin/header');
		$this->load->view('account/cashier');
		$this->load->view('admin/footer');
	}
    public function create() 
    {
        $data = array(
            'button' => 'Create',
            'action' => site_url('account/create_action'),
	    'account_id' => set_value('account_id'),
	    'client_id' => set_value('client_id'),
	    'account_number' => set_value('account_number'),
	    'balance' => set_value('balance'),
	    'account_type' => set_value('account_type'),
	    'account_type_product' => set_value('account_type_product'),
	    'added_by' => set_value('added_by'),
	    'date_added' => set_value('date_added'),
			'customers' =>$this->Individual_customers_model->get_all_active()
	);
		$menu_toggle['toggles'] = 46;
		$this->load->view('admin/header', $menu_toggle);
        $this->load->view('account/account_form', $data);
		$this->load->view('admin/footer');
    }
	function search(){
		$res = array();
		$search_group = $this->input->post('agroup');
		$search_code = $this->input->post('searchcode');
		$search_by =  $this->input->post('search_by');

		$result = $this->Account_model->search($search_code,$search_by,$search_group);

		if(!empty($result)){
			$res['status']= 'success';
			$res['data'] = $result;
			$res['message'] = ' data found';
		}else{
			$res['status']= 'success';
			$res['data'] = "";
			$res['message'] = ' data not found';
		}
		echo json_encode($res);


	}
    public function create_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            $data = array(
		'client_id' => $this->input->post('client_id',TRUE),
		'account_number' => $this->input->post('account_number',TRUE),
		'balance' => 0,
		'account_type' => 1,
		'account_type_product' => $this->input->post('account_type_product',TRUE),
		'added_by' => $this->session->userdata('user_id'),

	    );

            $this->Account_model->insert($data);
            $this->session->set_flashdata('message', 'Create Record Success');
            redirect(site_url('account/savings'));
        }
    }
    
    public function update($id) 
    {
        $row = $this->Account_model->get_by_id($id);

        if ($row) {
            $data = array(
                'button' => 'Update',
                'action' => site_url('account/update_action'),
		'account_id' => set_value('account_id', $row->account_id),
		'client_id' => set_value('client_id', $row->client_id),
		'account_number' => set_value('account_number', $row->account_number),
		'balance' => set_value('balance', $row->balance),
		'account_type' => set_value('account_type', $row->account_type),
		'account_type_product' => set_value('account_type_product', $row->account_type_product),
		'added_by' => set_value('added_by', $row->added_by),
		'date_added' => set_value('date_added', $row->date_added),
	    );
            $this->load->view('account/account_form', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('account/savings'));
        }
    }
    

    
    public function delete($id) 
    {
        $row = $this->Account_model->get_by_id($id);

        if ($row) {
            $this->Account_model->delete($id);
            $this->toaster->success('Delete Record Success');
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->toaster->error('Record Not Found');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
    public function block($id)
    {
        $row = $this->Account_model->get_by_id($id);

        if ($row) {
        	$data = array('account_status'=>"Closed");
            $this->Account_model->update($id, $data);
            $this->toaster->success('message', 'Delete Record Success');
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->toaster->error('Record Not Found');
			redirect($_SERVER['HTTP_REFERER']);
        }
    }public function unblock($id)
    {
        $row = $this->Account_model->get_by_id($id);

        if ($row) {
        	$data = array('account_status'=>"Active");
            $this->Account_model->update($id, $data);
            $this->toaster->success('message', 'Delete Record Success');
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->toaster->error('Record Not Found');
			redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function _rules() 
    {
	$this->form_validation->set_rules('client_id', 'client id', 'trim|required');
	$this->form_validation->set_rules('account_number', 'account number', 'trim|required');

	$this->form_validation->set_rules('account_type_product', 'account type product', 'trim|required');


	$this->form_validation->set_rules('account_id', 'account_id', 'trim');
	$this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

