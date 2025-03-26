<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Individual_customers extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Individual_customers_model');
        $this->load->model('Corporate_customers_model');
		$this->load->model('Branches_model');
		$this->load->model('Loan_model');
		$this->load->model('Account_model');
		$this->load->model('Access_model');
		$this->load->model('Geo_countries_model');
		$this->load->model('Proofofidentity_model');
        $this->load->model('Bank_model');
//		$this->load->model('Chart_of_accounts_model');
        $this->load->library('form_validation');
    }
	public function products($id){
    	$data = array(
    		'id'=>$id
		);
		$this->template->set('title', 'Core Banking | Customer products');
		$this->template->load('template', 'contents' ,'individual_customers/product',$data);
	}
function send_s(){
//    $s =   send_sms('+265992046150','Hello Testing agritrust');
    $s =   get_balance();
    var_dump($s);
}
function bulkactions(){

        $users = $this->input->post('users');
    $rowCount = count($users);
    for ($i = 0; $i < $rowCount; $i ++) {
        $row = $this->Individual_customers_model->get_by_id($users[$i]);
        $notify = get_by_id('sms_settings', 'id', '1');

            $this->Individual_customers_model->update($users[$i], array('approval_status' => 'Approved'));
            $this->Account_model->update_approval($users[$i], array('account_status' => 'Active'));
            if ($notify->customer_approval == 'Yes') {
                send_sms($row->PhoneNumber, 'Dear customer, Account your application has been approved, you can call numbers below for more');
            }


    }
    $this->toaster->success('Customers were approved successfully');
    redirect(site_url('individual_customers/approve'));
}
function bulkreject(){

        $users = $this->input->post('users');
    $rowCount = count($users);
    for ($i = 0; $i < $rowCount; $i ++) {
        $row = $this->Individual_customers_model->get_by_id($users[$i]);
        $notify = get_by_id('sms_settings', 'id', '1');

            $this->Individual_customers_model->update($users[$i], array('approval_status' => 'Rejected'));

            if ($notify->customer_approval == 'Yes') {
                send_sms($row->PhoneNumber, 'Dear customer, Your account application has been rejected, you can call numbers below for more');
            }


    }
    $this->toaster->success('Customers were approved successfully');
    redirect(site_url('individual_customers/approve'));
}
function get_ta($id){
        $output = "";
        $data = get_all_by_id('ta','district_id', $id);
        foreach ($data as $dd){
            $output .='<option value="'.$dd->ta_name.'">'.$dd->ta_name.'</option>';
        }
        echo  $output;
}
	public function index()
    {
        $q = $this->input->get('q', TRUE);
        $start = intval($this->input->get('start'));
        $user = $this->input->get('user');
        $country = $this->input->get('country');
        $status = $this->input->get('status');
        $gender = $this->input->get('gender');
        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $search = $this->input->get('search');
      
        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Individual_customers_model->get_filter_rows($q,$status,$user,$country,$gender, $from, $to);
        $individual_customers = $this->Individual_customers_model->get_filter($config['per_page'], $start,$q,$status,$user,$country,$gender, $from, $to);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'individual_customers_data' => $individual_customers,
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
		$menu_toggle['toggles'] = 43;


        if($search=="filter"){
            $this->load->view('admin/header',$menu_toggle);
            $this->load->view('individual_customers/individual_customers_list',$data);
            $this->load->view('admin/footer');
        }elseif($search=='export pdf'){
            $data['individual_customers_data'] = $this->Individual_customers_model->get_filter_export($q,$status,$user,$country,$gender, $from, $to);
            $data['officer'] = ($user=="All") ? "All Officers" : get_by_id('employees','id',$user)->Firstname;
//			$data['product'] =($product=="All") ? "All Products" : get_by_id('loan_products','loan_product_id',$product)->product_name;
            $data['from'] = $from;
            $data['to'] = $to;
            $this->load->library('Pdf');
            $html = $this->load->view('individual_customers/individual_customers_list', $data,true);
            $this->pdf->createPDF($html, "customer report as on".date('Y-m-d'), true,'A4','landscape');
        } elseif($search=='export excel'){
            $data_excel = $this->Individual_customers_model->get_filter_export($q,$status,$user,$country,$gender, $from, $to);

            $this->excel($data_excel);
        }else{
            $this->load->view('admin/header',$menu_toggle);
            $this->load->view('individual_customers/individual_customers_list',$data);
            $this->load->view('admin/footer');
        }
    }

    public function index1()
    {
        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));

        if ($q <> '') {
            $config['base_url'] = base_url() . 'individual_customers/index?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'individual_customers/index?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'individual_customers/index';
            $config['first_url'] = base_url() . 'individual_customers/index';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Individual_customers_model->total_rows($q);
        $individual_customers = $this->Individual_customers_model->get_limit_data($config['per_page'], $start, $q);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'individual_customers_data' => $individual_customers,
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
		$menu_toggle['toggles'] = 43;
		$this->load->view('admin/header',$menu_toggle);
		$this->load->view('individual_customers/individual_customers_list',$data);
		$this->load->view('admin/footer');
    }
	public function edit()
    {
        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));

        if ($q <> '') {
            $config['base_url'] = base_url() . 'individual_customers/edit?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'individual_customers/edit?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'individual_customers/edit';
            $config['first_url'] = base_url() . 'individual_customers/edit';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Individual_customers_model->total_rows($q);
        $individual_customers = $this->Individual_customers_model->get_limit_data($config['per_page'], $start, $q);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'individual_customers_data' => $this->Individual_customers_model->get_all(),
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
		$menu_toggle['toggles'] = 43;
		$this->load->view('admin/header',$menu_toggle);
		$this->load->view('individual_customers/individual_customers_toedit',$data);
		$this->load->view('admin/footer');
    }
    public function to_delete()
    {
        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));

        if ($q <> '') {
            $config['base_url'] = base_url() . 'individual_customers/edit?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'individual_customers/edit?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'individual_customers/edit';
            $config['first_url'] = base_url() . 'individual_customers/to_delete';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Individual_customers_model->total_rows($q);
        $individual_customers = $this->Individual_customers_model->get_limit_data($config['per_page'], $start, $q);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'individual_customers_data' => $this->Individual_customers_model->get_all(),
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
		$menu_toggle['toggles'] = 43;
		$this->load->view('admin/header',$menu_toggle);
		$this->load->view('individual_customers/individual_customers_delete',$data);
		$this->load->view('admin/footer');
    }
    public function kyc_edit()
    {
        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));

        if ($q <> '') {
            $config['base_url'] = base_url() . 'individual_customers/kyc_edit?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'individual_customers/kyc_edit?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'individual_customers/kyc_edit';
            $config['first_url'] = base_url() . 'individual_customers/kyc_edit';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Individual_customers_model->total_rows($q);
        $individual_customers = $this->Individual_customers_model->get_limit_data($config['per_page'], $start, $q);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'individual_customers_data' => $this->Individual_customers_model->get_all(),
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
		$menu_toggle['toggles'] = 43;
		$this->load->view('admin/header',$menu_toggle);
		$this->load->view('individual_customers/individual_customers_kyc_edit',$data);
		$this->load->view('admin/footer');
    }
    public function my_customers()
    {


        $data = array(
            'individual_customers_data' => $this->Individual_customers_model->get_selective($this->session->userdata('user_id')),

        );
		$menu_toggle['toggles'] = 43;
		$this->load->view('admin/header', $menu_toggle);
		$this->load->view('individual_customers/my_customers',$data);
		$this->load->view('admin/footer');
    }
    public function approve()
    {


        $data = array(
            'individual_customers_data' => $this->Individual_customers_model->get_status('Not Approved')

        );
		$menu_toggle['toggles'] = 43;
		$this->load->view('admin/header',$menu_toggle);
		$this->load->view('individual_customers/approve',$data);
		$this->load->view('admin/footer');
    }
    public function approved()
    {


        $data = array(
            'individual_customers_data' =>  $this->Individual_customers_model->get_status('Approved')

        );
		$menu_toggle['toggles'] = 43;
		$this->load->view('admin/header', $menu_toggle);
		$this->load->view('individual_customers/track',$data);
		$this->load->view('admin/footer');
    }
    public function rejected()
    {


        $data = array(
            'individual_customers_data' =>  $this->Individual_customers_model->get_status('Rejected')

        );
		$menu_toggle['toggles'] = 43;
		$this->load->view('admin/header', $menu_toggle);
		$this->load->view('individual_customers/rejected',$data);
		$this->load->view('admin/footer');
    }

    public function read($id) 
    {
        $row = $this->Individual_customers_model->get_by_id($id);
        if ($row) {
            $data = array(
		'id' => $row->id,
		'ClientId' => $row->ClientId,
		'Title' => $row->Title,
		'Firstname' => $row->Firstname,
		'Middlename' => $row->Middlename,
		'Lastname' => $row->Lastname,
		'Gender' => $row->Gender,
		'DateOfBirth' => $row->DateOfBirth,
		'EmailAddress' => $row->EmailAddress,
		'PhoneNumber' => $row->PhoneNumber,
		'AddressLine1' => $row->AddressLine1,
		'AddressLine2' => $row->AddressLine2,
		'AddressLine3' => $row->AddressLine3,
		'village' => $row->village,

		'Province' => $row->Province,
		'City' => $row->City,
		'Country' => $row->Country,
		'ResidentialStatus' => $row->ResidentialStatus,
		'Profession' => $row->Profession,
		'SourceOfIncome' => $row->SourceOfIncome,
		'GrossMonthlyIncome' => $row->GrossMonthlyIncome,
		'Branch' => $row->Branch,
		'LastUpdatedOn' => $row->LastUpdatedOn,
		'CreatedOn' => $row->CreatedOn,
	    );
			$menu_toggle['toggles'] = 43;
			$this->load->view('admin/header',$menu_toggle);
			$this->load->view('individual_customers/individual_customers_read',$data);
			$this->load->view('admin/footer');
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('individual_customers'));
        }
    }
    public function view($id)
    {
        $row = $this->Individual_customers_model->get_by_id($id);
        if ($row) {
            $data = array(
		'id' => $row->id,
		'ClientId' => $row->ClientId,
		'Title' => $row->Title,
		'Firstname' => $row->Firstname,
		'Middlename' => $row->Middlename,
		'Lastname' => $row->Lastname,
		'Gender' => $row->Gender,
		'DateOfBirth' => $row->DateOfBirth,
		'EmailAddress' => $row->EmailAddress,
		'PhoneNumber' => $row->PhoneNumber,
		'AddressLine1' => $row->AddressLine1,
		'AddressLine2' => $row->AddressLine2,
		'AddressLine3' => $row->AddressLine3,
		'village' => $row->village,

		'Province' => $row->Province,
		'City' => $row->City,
		'Country' => $row->Country,

		'Profession' => $row->Profession,
		'SourceOfIncome' => $row->SourceOfIncome,
		'GrossMonthlyIncome' => $row->GrossMonthlyIncome,
		'Branch' => $row->Branch,
		'LastUpdatedOn' => $row->LastUpdatedOn,
		'CreatedOn' => $row->CreatedOn,
	    );
			$menu_toggle['toggles'] = 43;
			$this->load->view('admin/header', $menu_toggle);
			$this->load->view('individual_customers/view',$data);
			$this->load->view('admin/footer');
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('individual_customers'));
        }
    } public function view_kyc($id)
    {
        $row = $this->Individual_customers_model->get_by_id($id);
        if ($row) {
            $data = array(
		'id' => $row->id,
		'ClientId' => $row->ClientId,
		'Title' => $row->Title,
		'Firstname' => $row->Firstname,
		'Middlename' => $row->Middlename,
		'Lastname' => $row->Lastname,
		'Gender' => $row->Gender,
		'DateOfBirth' => $row->DateOfBirth,
		'EmailAddress' => $row->EmailAddress,
		'PhoneNumber' => $row->PhoneNumber,
		'AddressLine1' => $row->AddressLine1,
		'AddressLine2' => $row->AddressLine2,
		'AddressLine3' => $row->AddressLine3,
		'village' => $row->village,

		'Province' => $row->Province,
		'City' => $row->City,
		'Country' => $row->Country,
		'ResidentialStatus' => $row->ResidentialStatus,
		'Profession' => $row->Profession,
		'SourceOfIncome' => $row->SourceOfIncome,
		'GrossMonthlyIncome' => $row->GrossMonthlyIncome,
		'Branch' => $row->Branch,
		'LastUpdatedOn' => $row->LastUpdatedOn,
		'CreatedOn' => $row->CreatedOn,
	    );
			$menu_toggle['toggles'] = 43;
			$this->load->view('admin/header', $menu_toggle);
			$this->load->view('individual_customers/view_kyc',$data);
			$this->load->view('admin/footer');
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('individual_customers'));
        }
    }
    public function report($id)
    {
        $row = $this->Individual_customers_model->get_by_id($id);
        if ($row) {
            $data = array(
                'id' => $row->id,
                'ClientId' => $row->ClientId,
                'Title' => $row->Title,
                'Firstname' => $row->Firstname,
                'Middlename' => $row->Middlename,
                'Lastname' => $row->Lastname,
                'Gender' => $row->Gender,
                'DateOfBirth' => $row->DateOfBirth,
                'EmailAddress' => $row->EmailAddress,
                'PhoneNumber' => $row->PhoneNumber,
                'AddressLine1' => $row->AddressLine1,
                'AddressLine2' => $row->AddressLine2,
                'AddressLine3' => $row->AddressLine3,
                'village' => $row->village,

                'Province' => $row->Province,
                'City' => $row->City,
                'Country' => $row->Country,
                
                'Profession' => $row->Profession,
                'SourceOfIncome' => $row->SourceOfIncome,
                'GrossMonthlyIncome' => $row->GrossMonthlyIncome,
                'Branch' => $row->Branch,
                'LastUpdatedOn' => $row->LastUpdatedOn,
                'CreatedOn' => $row->CreatedOn,
            );
			$this->load->library('Pdf');
			$html = $this->load->view('individual_customers/report', $data,true);
			$this->pdf->createPDF($html, "Customer report as on".date('Y-m-d'), true,'A4','landscape');


        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('individual_customers'));
        }
    }
    public function view_customer($id)
    {
    	$res = array();
        $row = $this->Individual_customers_model->get_by_id($id);
        $loans = get_user_loan_individual($id);
        $get_settings = get_by_id('settings','settings_id', '1');
        if(!empty($loans)){
            $fee_amount = $get_settings->reg_fee_old;
        }else{
            $fee_amount = $get_settings->reg_fee_new;
        }
        if ($row) {
			$kyc = $this->Proofofidentity_model->check($row->ClientId);
            $data = array(
            	'loan'=>$loans,
            	'kyc'=>$kyc,

		'id' => $row->id,
		'ClientId' => $row->ClientId,
		'Title' => $row->Title,
		'Firstname' => $row->Firstname,
		'Middlename' => $row->Middlename,
		'Lastname' => $row->Lastname,
		'Gender' => $row->Gender,
		'DateOfBirth' => $row->DateOfBirth,
		'EmailAddress' => $row->EmailAddress,
		'PhoneNumber' => $row->PhoneNumber,
		'AddressLine1' => $row->AddressLine1,
		'AddressLine2' => $row->AddressLine2,
		'AddressLine3' => $row->AddressLine3,
		'village' => $row->village,

		'Province' => $row->Province,
		'feeamount' => $fee_amount,
		'City' => $row->City,
		'Country' => $row->Country,
		'ResidentialStatus' => $row->ResidentialStatus,
		'Profession' => $row->Profession,
		'SourceOfIncome' => $row->SourceOfIncome,
		'GrossMonthlyIncome' => $row->GrossMonthlyIncome,
		'Branch' => $row->Branch,
		'LastUpdatedOn' => $row->LastUpdatedOn,
		'CreatedOn' => $row->CreatedOn,
	    );
			$res['status'] = 'success';
			$res['data'] = $data;
		} else {
			$res['status'] = 'error';

		}
		echo json_encode($res);
    }
    public function get_details($id)
    {
        $row = $this->Individual_customers_model->get_by_id($id);
        if ($row) {
            $data = array(
		'id' => $row->id,
		'ClientId' => $row->ClientId,
		'Title' => $row->Title,
		'Firstname' => $row->Firstname,
		'Middlename' => $row->Middlename,
		'Lastname' => $row->Lastname,
		'Gender' => $row->Gender,
		'DateOfBirth' => $row->DateOfBirth,
		'EmailAddress' => $row->EmailAddress,
		'PhoneNumber' => $row->PhoneNumber,
		'AddressLine1' => $row->AddressLine1,
		'AddressLine2' => $row->AddressLine2,
		'AddressLine3' => $row->AddressLine3,
		'village' => $row->village,

		'Province' => $row->Province,
		'City' => $row->City,
		'Country' => $row->Country,
		'ResidentialStatus' => $row->ResidentialStatus,
		'Profession' => $row->Profession,
		'SourceOfIncome' => $row->SourceOfIncome,
		'GrossMonthlyIncome' => $row->GrossMonthlyIncome,
		'Branch' => $row->Branch,
		'LastUpdatedOn' => $row->LastUpdatedOn,
		'CreatedOn' => $row->CreatedOn,
	    );
			$this->load->view('admin/header');
			$this->load->view('individual_customers/individual_customers_read',$data);
			$this->load->view('admin/footer');
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('individual_customers'));
        }
    }

    public function create()
    {
        $data = array(
            'button' => 'Create',
            'action' => site_url('individual_customers/create_action'),
            'id' => set_value('id'),
            'ClientId' => set_value('ClientId'),
            'Title' => set_value('Title'),
            'Firstname' => set_value('Firstname'),
            'Middlename' => set_value('Middlename'),
            'Lastname' => set_value('Lastname'),
            'Gender' => set_value('Gender'),
            'DateOfBirth' => set_value('DateOfBirth'),
            'EmailAddress' => set_value('EmailAddress'),
            'PhoneNumber' => set_value('PhoneNumber'),
            'kinFullname' => set_value('kinFullname'),

            'kinPhonenumber' => set_value('kinPhonenumber'),
            'AddressLine1' => set_value('AddressLine1'),
            'AddressLine2' => set_value('AddressLine2'),
            'AddressLine3' => set_value('AddressLine3'),
            'Province' => set_value('Province'),
            'village' => set_value('village'),
            'epa' => set_value('epa'),
            'marital' => set_value('marital'),
            'able_to_write' => set_value('able_to_write'),
            'able_to_read' => set_value('able_to_read'),
            'section' => set_value('section'),
            'house_head' => set_value('house_head'),
            'education_level' => set_value('education_level'),
            'noonwhatsap' => set_value('noonwhatsap'),
            'company_name' => set_value('company_name'),
            'processing_fee' => set_value('processing_fee'),
            'City' => set_value('City'),
            'Country' => set_value('Country'),
            'ResidentialStatus' => set_value('ResidentialStatus'),


            'Profession' => set_value('Profession'),
            'SourceOfIncome' => set_value('SourceOfIncome'),
            'account_name' => set_value('account_name'),
            'account_number' => set_value('account_number'),
            'bank_name' => set_value('bank_name'),
            'bank_branch' => set_value('bank_branch'),
            'GrossMonthlyIncome' => set_value('GrossMonthlyIncome'),
            'Branch' => set_value('Branch'),
            'LastUpdatedOn' => set_value('LastUpdatedOn'),
            'CreatedOn' => set_value('CreatedOn'),

        );
        $menu_toggle['toggles'] = 43;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('individual_customers/individual_customers_form',$data);
        $this->load->view('admin/footer');
    }

    public function create_group()
    {
        $data = array(
            'button' => 'Create',
            'action' => site_url('individual_customers/create_action_group'),
	    'id' => set_value('id'),
	    'ClientId' => set_value('ClientId'),
	    'Title' => set_value('Title'),
	    'Firstname' => set_value('Firstname'),
	    'Middlename' => set_value('Middlename'),
	    'Lastname' => set_value('Lastname'),
	    'Gender' => set_value('Gender'),
	    'DateOfBirth' => set_value('DateOfBirth'),
	    'EmailAddress' => set_value('EmailAddress'),
	    'PhoneNumber' => set_value('PhoneNumber'),
	    'kinFullname' => set_value('kinFullname'),
	  
	    'kinPhonenumber' => set_value('kinPhonenumber'),
	    'AddressLine1' => set_value('AddressLine1'),
	    'AddressLine2' => set_value('AddressLine2'),
	    'AddressLine3' => set_value('AddressLine3'),
	    'Province' => set_value('Province'),
	    'village' => set_value('village'),
	    'epa' => set_value('epa'),
	    'marital' => set_value('marital'),
	    'able_to_write' => set_value('able_to_write'),
	    'able_to_read' => set_value('able_to_read'),
	    'section' => set_value('section'),
	    'house_head' => set_value('house_head'),
	    'education_level' => set_value('education_level'),
	    'noonwhatsap' => set_value('noonwhatsap'),
	    'company_name' => set_value('company_name'),
	    'processing_fee' => set_value('processing_fee'),
	    'City' => set_value('City'),
	    'Country' => set_value('Country'),
	    'ResidentialStatus' => set_value('ResidentialStatus'),

	 
	    'Profession' => set_value('Profession'),
	    'SourceOfIncome' => set_value('SourceOfIncome'),
	    'account_name' => set_value('account_name'),
	    'account_number' => set_value('account_number'),
            'bank_name' => set_value('bank_name'),
	    'bank_branch' => set_value('bank_branch'),
            'GrossMonthlyIncome' => set_value('GrossMonthlyIncome'),
            'Branch' => set_value('Branch'),
            'LastUpdatedOn' => set_value('LastUpdatedOn'),
            'CreatedOn' => set_value('CreatedOn'),

        );
		$menu_toggle['toggles'] = 43;
		$this->load->view('admin/header', $menu_toggle);
		$this->load->view('individual_customers/group_customer_form',$data);
		$this->load->view('admin/footer');
    }
    
    public function create_action() 
    {
    	$dd = $this->Individual_customers_model->count_it();
    	$d1 = $this->Corporate_customers_model->count_it();
    	$clientid = rand(100,1000).rand(100,9999);
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            $data = array(
		'ClientId' => $clientid,
		'Title' => $this->input->post('Title',TRUE),
		'Firstname' => $this->input->post('Firstname',TRUE),
		'Middlename' => $this->input->post('Middlename',TRUE),
		'Lastname' => $this->input->post('Lastname',TRUE),
		'Gender' => $this->input->post('Gender',TRUE),
		'DateOfBirth' => $this->input->post('DateOfBirth',TRUE),
		'EmailAddress' => $this->input->post('EmailAddress',TRUE),
		 'kinFullname' => $this->input->post('kinFullname',TRUE),
	   
	    'kinPhonenumber' =>$this->input->post('kinPhonenumber',TRUE),
		'PhoneNumber' => $this->input->post('PhoneNumber',TRUE),
		'AddressLine1' => $this->input->post('AddressLine1',TRUE),
		'AddressLine2' => $this->input->post('AddressLine2',TRUE),
		'AddressLine3' => $this->input->post('AddressLine3',TRUE),
		'Province' => $this->input->post('Province',TRUE),
	
             'village' => $this->input->post('village',TRUE),
              'marital' =>$this->input->post('marital',TRUE),
		'City' => $this->input->post('City',TRUE),
		'Country' => 'Malawi',
		'ResidentialStatus' => $this->input->post('ResidentialStatus',TRUE),
		'Profession' => $this->input->post('Profession',TRUE),
		'SourceOfIncome' => $this->input->post('SourceOfIncome',TRUE),
		'GrossMonthlyIncome' => $this->input->post('GrossMonthlyIncome',TRUE),

		'Branch' => $this->input->post('Branch',TRUE),
                'customer_type' => 'individual',
		'added_by' => $this->session->userdata('user_id')

	    );
			$logger = array(

				'user_id' => $this->session->userdata('user_id'),
				'activity' => 'Register a customer'.$data['Firstname'].' '.$data['Lastname'],
				        	
					'activity_cate' => 'customer_registration',


			);
			log_activity($logger);

            $insert_id =  $this->Individual_customers_model->insert($data);

            $at = get_all_by_id('account','account_type','1');
            $ct = 1;
            foreach ($at as $cc){
                $ct ++;
            }
            $account = 300000+$ct.rand(0,9999);
            $data = array(
                'client_id' => $insert_id,
                'account_number' => $account,
                'balance' => 0,
                'account_type' => 1,
                'account_type_product' => 2,

                'added_by' => $this->session->userdata('user_id'),

            );

            $this->Account_model->insert($data);
            $this->Access_model->assets($this->input->post('assets',TRUE),$insert_id);
            $this->Access_model->assets($this->input->post('trainings',TRUE),$insert_id);
			$data = array(
				'IDType' => $this->input->post('IDType',TRUE),
				'IDNumber' => $this->input->post('IDNumber',TRUE),
				'IssueDate' => $this->input->post('IssueDate',TRUE),
				'ExpiryDate' => $this->input->post('ExpiryDate',TRUE),
				'ClientId' => $clientid,
				'photograph' => $this->input->post('photograph',TRUE),
				'signature' => $this->input->post('signature',TRUE),
				'Id_back' => $this->input->post('Id_back',TRUE),
				'id_front' => $this->input->post('id_front',TRUE),
			);
			$this->Proofofidentity_model->insert($data);

            foreach ($this->input->post('account_name') as $key => $value) {
                $data = array(
                    'customer_id' => $insert_id, // Replace with the actual customer reference
                    'account_name' => $this->input->post('account_name')[$key],
                    'account_number' => $this->input->post('account_number')[$key],
                    'bank_name' => $this->input->post('bank_name')[$key],
                    'bank_branch' => $this->input->post('bank_branch')[$key],
            );
            
                // Insert into the `bank_details` table
                $this->Bank_model->insert($data);
            }
			$this->toaster->success('Success, customer was created  pending Approval');
            redirect(site_url('individual_customers/my_customers'));
        }
    }



    public function create_action_group()
    {
        $dd = $this->Individual_customers_model->count_it();
        $d1 = $this->Corporate_customers_model->count_it();
        $clientid = rand(100,1000).rand(100,9999);
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            $data = array(
                'ClientId' => $clientid,
                'Title' => $this->input->post('Title',TRUE),
                'Firstname' => $this->input->post('Firstname',TRUE),
                'Middlename' => $this->input->post('Middlename',TRUE),
                'Lastname' => $this->input->post('Lastname',TRUE),
                'Gender' => $this->input->post('Gender',TRUE),
                'DateOfBirth' => $this->input->post('DateOfBirth',TRUE),
                'EmailAddress' => $this->input->post('EmailAddress',TRUE),
                'kinFullname' => $this->input->post('kinFullname',TRUE),

                'kinPhonenumber' =>$this->input->post('kinPhonenumber',TRUE),
                'PhoneNumber' => $this->input->post('PhoneNumber',TRUE),
                'AddressLine1' => $this->input->post('AddressLine1',TRUE),
                'AddressLine2' => $this->input->post('AddressLine2',TRUE),
                'AddressLine3' => $this->input->post('AddressLine3',TRUE),
                'Province' => $this->input->post('Province',TRUE),

                'village' => $this->input->post('village',TRUE),
                'marital' =>$this->input->post('marital',TRUE),
                'City' => $this->input->post('City',TRUE),
                'Country' => 'Malawi',
                'ResidentialStatus' => $this->input->post('ResidentialStatus',TRUE),
                'account_name' => $this->input->post('account_name',TRUE),
                'account_number' =>$this->input->post('account_number',TRUE),
                'bank_name' => $this->input->post('bank_name',TRUE),
                'bank_branch' => $this->input->post('bank_branch',TRUE),

                'Profession' => $this->input->post('Profession',TRUE),
                'SourceOfIncome' => $this->input->post('SourceOfIncome',TRUE),
                'GrossMonthlyIncome' => $this->input->post('GrossMonthlyIncome',TRUE),

                'Branch' => $this->input->post('Branch',TRUE),
                'customer_type' => 'group',
                'added_by' => $this->session->userdata('user_id')

            );
            $logger = array(

                'user_id' => $this->session->userdata('user_id'),
                'activity' => 'Register a customer'.$data['Firstname'].' '.$data['Lastname'],

                'activity_cate' => 'customer_registration',


            );
            log_activity($logger);

            $insert_id =  $this->Individual_customers_model->insert($data);

            $at = get_all_by_id('account','account_type','1');
            $ct = 1;
            foreach ($at as $cc){
                $ct ++;
            }
            $account = 300000+$ct.rand(0,9999);
            $data = array(
                'client_id' => $insert_id,
                'account_number' => $account,
                'balance' => 0,
                'account_type' => 1,
                'account_type_product' => 2,

                'added_by' => $this->session->userdata('user_id'),

            );

            $this->Account_model->insert($data);
            $this->Access_model->assets($this->input->post('assets',TRUE),$insert_id);
            $this->Access_model->assets($this->input->post('trainings',TRUE),$insert_id);
            $data = array(
                'IDType' => $this->input->post('IDType',TRUE),
                'IDNumber' => $this->input->post('IDNumber',TRUE),
                'IssueDate' => $this->input->post('IssueDate',TRUE),
                'ExpiryDate' => $this->input->post('ExpiryDate',TRUE),
                'ClientId' => $clientid,
                'photograph' => $this->input->post('photograph',TRUE),
                'signature' => $this->input->post('signature',TRUE),
                'Id_back' => $this->input->post('Id_back',TRUE),
                'id_front' => $this->input->post('id_front',TRUE),
            );
            $this->Proofofidentity_model->insert($data);
            $this->toaster->success('Success, customer was created  pending Approval');
            redirect(site_url('individual_customers/my_customers'));
        }
    }

    public function create_actionCofi()
    {
        
    	
    	 $sampleDataCofi = get_all_sme_cofi();
    	 $addescofi=$this->session->userdata('user_id');
    	 $cofibranch=6;
    	 $country='MW';

    	  foreach ( $sampleDataCofi as $rowdistinctcofi){


    	 

    	     $rowcofi=get_all_sme_cofi_by_id($rowdistinctcofi->IDNumber);
    	     $dd = $this->Individual_customers_model->count_it();
    	$d1 = $this->Corporate_customers_model->count_it();
    	     
    	$clientid = (20100000)+($dd+$d1+1);
        $idcofi=$rowcofi->id;
        $strdateofbirth =  strval($rowcofi->DateofBirth);
         $dob = date('Y-m-d', $strdateofbirth );
       

                  $data = array(
		'ClientId' => $clientid,
		'Title' => $rowcofi->Salutation,
		'Firstname' => $rowcofi->FirstName,
		'Middlename' => $rowcofi->MiddleName,
		'Lastname' => $rowcofi->Surname,
		'Gender' => $rowcofi->Gender,
		'DateOfBirth' =>  $dob,
		'EmailAddress' => $rowcofi->EmailAddress,
		'PhoneNumber' => $rowcofi->PhoneNo,
		'AddressLine1' => $rowcofi->ResidentialAddress,
		'AddressLine2' => $rowcofi->PostalAddress,
		'AddressLine3' => $rowcofi->PostalAddress,
		'Province' => $rowcofi->HomeDistrict,
		'village' => $rowcofi->Village,
		'City' => $rowcofi->ResidentialDistrict,
		'Country' => $country,
		
		
		'Profession' => $rowcofi->ProfessionOccupation,
		
	
		'Branch' => $cofibranch,
		'added_by' => $addescofi,
		'approval_status' => 'Approved'

	    );
		

            $insert_id =  $this->Individual_customers_model->insert($data);
            
            
            $logger = array(

				'user_id' => $addescofi,
				'activity' => 'added customer',
				'activity' => 'Register a customer'.$data['Firstname'].' '.$data['Lastname']

			);
			log_activity($logger);

            //update
            $sampleDataCofiup =get_all_sme_cofi_by_all($rowdistinctcofi->IDNumber);
            foreach ( $sampleDataCofiup as $rowdistinctcofiup){
                
               
             $this->Individual_customers_model->updateCofi($rowdistinctcofiup->id, $insert_id);
            }

            $at = get_all_by_id('account','account_type','1');
            $ct = 1;
            foreach ($at as $cc){
                $ct ++;
            }
            $account = 3060000+$ct;
            $data = array(
                'client_id' => $insert_id,
                'account_number' => $account,
                'balance' => 0,
                'account_type' => 1,
                'account_type_product' => 2,

                'added_by' => $addescofi,

            );

            $this->Account_model->insert($data);
    }
    	  
    }
    
    
    
    
    
    
    public function update($id) 
    {
        $row = $this->Individual_customers_model->get_by_id($id);

        if ($row) {
            $data = array(
                'button' => 'Update',
                'action' => site_url('individual_customers/update_action'),
		'id' => set_value('id', $row->id),
		'ClientId' => set_value('ClientId', $row->ClientId),
		'Title' => set_value('Title', $row->Title),
		'Firstname' => set_value('Firstname', $row->Firstname),
		'Middlename' => set_value('Middlename', $row->Middlename),
		'Lastname' => set_value('Lastname', $row->Lastname),
		'Gender' => set_value('Gender', $row->Gender),
		'DateOfBirth' => set_value('DateOfBirth', $row->DateOfBirth),
		'EmailAddress' => set_value('EmailAddress', $row->EmailAddress),
		'PhoneNumber' => set_value('PhoneNumber', $row->PhoneNumber),
		'AddressLine1' => set_value('AddressLine1', $row->AddressLine1),
		'AddressLine2' => set_value('AddressLine2', $row->AddressLine2),
		'AddressLine3' => set_value('AddressLine3', $row->AddressLine3),
		'Province' => set_value('Province', $row->Province),
		'village' => set_value('Province', $row->village),
		'City' => set_value('City', $row->City),
		'Country' => set_value('Country', $row->Country),
		'ResidentialStatus' => set_value('ResidentialStatus', $row->ResidentialStatus),
		'Profession' => set_value('Profession', $row->Profession),
		'SourceOfIncome' => set_value('SourceOfIncome', $row->SourceOfIncome),
		'GrossMonthlyIncome' => set_value('GrossMonthlyIncome', $row->GrossMonthlyIncome),
		'Branch' => set_value('Branch', $row->Branch),
		'LastUpdatedOn' => set_value('LastUpdatedOn', $row->LastUpdatedOn),
		'CreatedOn' => set_value('CreatedOn', $row->CreatedOn),
	    );
			$menu_toggle['toggles'] = 43;
			$this->load->view('admin/header', $menu_toggle);
			$this->load->view('individual_customers/individual_customers_edit',$data);
			$this->load->view('admin/footer');

			    } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('individual_customers'));
        }
    }
    
    public function update_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('id', TRUE));
        } else {
            $data = array(

		'Title' => $this->input->post('Title',TRUE),
		'Firstname' => $this->input->post('Firstname',TRUE),
		'Middlename' => $this->input->post('Middlename',TRUE),
		'Lastname' => $this->input->post('Lastname',TRUE),
		'Gender' => $this->input->post('Gender',TRUE),
		'DateOfBirth' => $this->input->post('DateOfBirth',TRUE),
		'EmailAddress' => $this->input->post('EmailAddress',TRUE),
		'PhoneNumber' => $this->input->post('PhoneNumber',TRUE),
		'AddressLine1' => $this->input->post('AddressLine1',TRUE),
		'AddressLine2' => $this->input->post('AddressLine2',TRUE),
		'AddressLine3' => $this->input->post('AddressLine3',TRUE),
		'Province' => $this->input->post('Province',TRUE),
		'village' => $this->input->post('village',TRUE),
		'City' => $this->input->post('City',TRUE),
		'Country' => $this->input->post('Country',TRUE),
		'ResidentialStatus' => $this->input->post('ResidentialStatus',TRUE),

		'Profession' => $this->input->post('Profession',TRUE),
		'SourceOfIncome' => $this->input->post('SourceOfIncome',TRUE),
		'GrossMonthlyIncome' => $this->input->post('GrossMonthlyIncome',TRUE),
		'Branch' => $this->input->post('Branch',TRUE),
		'LastUpdatedOn' => $this->input->post('LastUpdatedOn',TRUE),
		'CreatedOn' => $this->input->post('CreatedOn',TRUE),
	    );
			$logger = array(

				'user_id' => $this->session->userdata('user_id'),
				'activity' => 'Update customer info of'.' '.$data['Firstname'].' '.$data['Lastname'],
				    	'activity_cate' => 'updating'

			);
			log_activity($logger);

			 $this->Individual_customers_model->update($this->input->post('id', TRUE),$data);

			$this->toaster->success('Success, employee was created please pending Approval');


            redirect(site_url('individual_customers'));
        }
    }
    
    public function delete($id) 
    {
        $row = $this->Individual_customers_model->get_by_id($id);

        if ($row) {

			$logger = array(

				'user_id' => $this->session->userdata('user_id'),
				'activity' => 'Delete a customer',
				    	
					'activity_cate' => 'deletion'

			);
			log_activity($logger);
            $this->Individual_customers_model->delete($id);
            $this->toaster->success('Delete Record Success');
            redirect($_SERVER["HTTP_REFERER"]);
        } else {
			$this->toaster->error('Delete Record Failed');
			redirect($_SERVER["HTTP_REFERER"]);
        }
    }
    public function approval_action($id)
    {
        $row = $this->Individual_customers_model->get_by_id($id);
    $notify = get_by_id('sms_settings','id','1');
        if ($row) {
            $this->Individual_customers_model->update($id,array('approval_status'=>'Approved'));
            $this->Account_model->update_approval($id,array('account_status'=>'Active'));
            if($notify->customer_approval=='Yes'){
                send_sms($row->PhoneNumber,'Dear customer, registration has been approved, you can call numbers below for more');
            }
            	$logger = array(

				'user_id' => $this->session->userdata('user_id'),
				'activity' => 'customer approved',
					'activity_cate' => 'customer_approval',

			);
			log_activity($logger);
            $this->toaster->success('Customer was approved successfully');
            redirect(site_url('individual_customers/approve'));
        } else {

        }
    }
 public function reject_action($id)
    {
        $row = $this->Individual_customers_model->get_by_id($id);

        if ($row) {
            $this->Individual_customers_model->update($id,array('approval_status'=>'Rejected'));
            $this->toaster->success('Customer was approved successfully');
            redirect(site_url('individual_customers/approve'));
        } else {

        }
    }

    public function _rules() 
    {

	$this->form_validation->set_rules('Title', 'title', 'trim|required');
	$this->form_validation->set_rules('Firstname', 'firstname', 'trim|required');

	$this->form_validation->set_rules('Lastname', 'lastname', 'trim|required');
	$this->form_validation->set_rules('Gender', 'gender', 'trim|required');
	$this->form_validation->set_rules('DateOfBirth', 'dateofbirth', 'trim|required');




	$this->form_validation->set_rules('Province', 'TA', 'trim|required');
	$this->form_validation->set_rules('City', 'city', 'trim|required');


	$this->form_validation->set_rules('Profession', 'profession', 'trim|required');
	$this->form_validation->set_rules('SourceOfIncome', 'sourceofincome', 'trim|required');
	$this->form_validation->set_rules('GrossMonthlyIncome', 'grossmonthlyincome', 'trim|required|numeric');
	$this->form_validation->set_rules('Branch', 'branch', 'trim|required');


	$this->form_validation->set_rules('id', 'id', 'trim');
	$this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }
    public function excel($result )
    {
        $namaFile = "Customer_report".DATE('Y-m-d H:i:s');
        $output ='<table style="border-collapse: collapse; width: 100%; border: 2px solid black;" border="1" >
                       
                        <tr>

                            <th>ClientId</th>
                            <th>Title</th>
                            <th>First name</th>
                           
                            <th>Last name</th>
                            <th>Gender</th>
                            <th>DateOfBirth</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Marital status</th>
                            <th>Officer</th>
                            <th>Customer Status</th>

                            <th>CreatedOn</th>
                            
                        </tr>
                       
                        ';

                        foreach ($result as  $individual_customers)
                        {


                        $output .='
                            <tr>

                                <td>'. $individual_customers->ClientId .'</td>
                                <td>'.$individual_customers->Title .'</td>
                                <td>'.$individual_customers->cfname .'</td>
                                <td>'.$individual_customers->cmname .'</td>
                                <td>'.$individual_customers->cgender .'</td>
                                <td>'.$individual_customers->cdob .'</td>
                                <td>'.$individual_customers->cemail  .'</td>
                                <td>'.$individual_customers->cphonee  .'</td>
                                <td>'.$individual_customers->cmarital  .'</td>
                                <td>'.$individual_customers->efname.' '.$individual_customers->elname .'</td>

                                <td>'.$individual_customers->approval_status.'</td>
                                <td>'.$individual_customers->CreatedOn .'</td>
                                
                            </tr>
                         ';
                        }
                     $output .='
                       
                    </table>';
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;filename=" . $namaFile . ".xls");
        header("Content-Transfer-Encoding: binary ");
        echo $output;
    }
    public function excekl($result )
    {
        print_r($result);
        exit();
        $rr = array(
            (object) array('ClientId' => 1, 'Value' => 1000),
            (object) array('ClientId' => 2, 'Value' => 1500),
            (object) array('ClientId' => 3, 'Value' => 800)
        );

        $this->load->helper('exportexcel_helper');
        $namaFile = "customers-dump.xls";
        $judul = "individual_customers";
        $tablehead = 0;
        $tablebody = 1;
        $nourut = 1;
        //penulisan header
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment;filename=" . $namaFile . "");
        header("Content-Transfer-Encoding: binary ");

        xlsBOF();

        $kolomhead = 0;
        xlsWriteLabel($tablehead, $kolomhead++, "No");
        xlsWriteLabel($tablehead, $kolomhead++, "ClientId");



        foreach ($rr as $data) {
            $kolombody = 0;

            //ubah xlsWriteLabel menjadi xlsWriteNumber untuk kolom numeric
            xlsWriteNumber($tablebody, $kolombody++, $nourut);
            xlsWriteLabel($tablebody, $kolombody++, $data->ClientId);
            $tablebody++;
            $nourut++;
        }

        xlsEOF();
        exit();
    }
}

