<?php

class Admin extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
        $this->load->model('Loan_model');
        $this->load->model('User_access_model');
	}
	public function index(){
		// Get all loan products for dynamic display
		$data['loan_products'] = get_active_loan_products();

		$this->load->view('admin/header');
		$this->load->view('admin/index', $data);
		$this->load->view('admin/footer');
	}
	
		public function sms_send(){
	    $phone = $_POST['phone'];
	    $msg = $_POST['msg'];
	   $r= send_sms1($phone, $msg);
	   //$data['phone'] = $phone;
	   //$data['msg'] = $msg;
	  
	   //echo json_encode($data);
	   echo $r;
	}
	function mail_api(){
	    $to = $this->input->post('to');
	      $subject = $this->input->post('subject');
	      $body = $this->input->post('body');
	  // the message


// use wordwrap() if lines are longer than 70 characters
$body = wordwrap($body,70);
$res = array();
// send email
if(mail($to,$subject,$body)){
    $res['status'] = 'success';
    $res['message'] ='Email was sent';

    //$result = $this->User_access_model->check_user_email($to);

    $data = array(
        'row_data' => $this->User_access_model->get_by_id_use_email($to),

    );
    //redirect('admin/reset_password_get_code');
    $this->load->view('forget_code', $data);

}else{
     $res['status'] = 'error';
    $res['message'] ='Email was not sent';
    $this->load->view('forget');



}
//echo json_encode($res);

	}
	
	function mail_send(){
	   $url = 'https://fin.infocustech-mw.com/Admin/mail_api/';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$data = array(
    'to' => 'calebkalagho@gmail.com',
    'subject' => 'Password reset',
    'body' => 'hi, new message caleb done'
);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

$response = curl_exec($ch);
curl_close($ch);

echo $response;

	}

	/**
	 * Setup method to add Collateral Report menu item
	 * Visit: http://localhost/fundit/Admin/setup_collateral_report_menu
	 */
	public function setup_collateral_report_menu() {
		// Check if menu item already exists
		$existing = $this->db->get_where('menuitems', array('method' => 'Reports/collateral_report'))->row();

		if ($existing) {
			echo "Collateral Report menu item already exists (ID: {$existing->id})";
			return;
		}

		// Find the Reports menu ID
		$reports_menu = $this->db->like('label', 'Report')->get('menu')->row();

		if (!$reports_menu) {
			echo "Error: Reports menu not found. Please add it manually.";
			return;
		}

		// Get the next sort order
		$this->db->select_max('sortt');
		$this->db->where('mid', $reports_menu->id);
		$max_sort = $this->db->get('menuitems')->row();
		$next_sort = ($max_sort->sortt ?? 0) + 1;

		// Insert the menu item
		$data = array(
			'mid' => $reports_menu->id,
			'label' => 'Collateral Report',
			'method' => 'Reports/collateral_report',
			'fa_icon' => 'fa fa-shield',
			'sortt' => $next_sort,
			'active' => 1,
			'show_menu' => 'Yes'
		);

		$this->db->insert('menuitems', $data);
		$insert_id = $this->db->insert_id();

		if ($insert_id) {
			echo "Success! Collateral Report menu item added (ID: {$insert_id}). <br>";
			echo "Note: You may need to grant access to users via User Access settings.";
		} else {
			echo "Error: Failed to insert menu item. " . $this->db->error()['message'];
		}
	}
}
