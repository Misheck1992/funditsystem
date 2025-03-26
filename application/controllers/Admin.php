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
		$this->load->view('admin/header');
		$this->load->view('admin/index');
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
}
