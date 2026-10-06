<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Customer_api extends  CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('mithenga', 'common_queries'));
        $this->load->model('Customer_access_model');
        $this->load->model('Account_model');
        $this->load->library('form_validation');
    }
    public function savings_balance()
    {
        $res = array();
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'GET') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = check_auth();
            if ($check_auth_client) {
                $response = check_auth_user();
                if ($response['status'] == 200) {
                    //$resp = $this->Branch_model->get_all();
                    $user_id = $this->input->get_request_header('USER-ID', TRUE);
                    $get = get_by_id('individual_customers','PhoneNumber',$user_id);
                    if (!empty($get->id)) {
                        $balance = $this->Account_model->get_customer_balance($get->id);
 if (!empty($balance)) {
     $res['status'] = 'success';
     $res['data'] = number_format($balance->balance, 2);
 }else{
     $res['status'] = 'error';
     $res['message'] = 'Sorry you do not have active savings account';
 }
                    } else {
                        $res['status'] = 'error';
                        $res['message'] = 'There is an error';
                    }
                    echo json_encode($res);
                }else{
                    echo json_encode($response);
                }
            }
        }
    }
    public function transfer()
    {
        $res = array();
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = check_auth();
            if ($check_auth_client) {
                $response = check_auth_user();
                if ($response['status'] == 200) {
                    //$resp = $this->Branch_model->get_all();
                    $user_id = $this->input->get_request_header('USER-ID', TRUE);
                    $recepient = $this->input->post('receiver');
                    $amount = $this->input->post('amount');
                    $get = get_by_id('individual_customers','PhoneNumber',$user_id);
                    if (!empty($get->id)) {
                        $balance = $this->Account_model->get_customer_balance($get->id);
 if (!empty($balance)) {
     $res['status'] = 'success';
     $res['data'] = number_format($balance->balance, 2);
 }else{
     $res['status'] = 'error';
     $res['message'] = 'Sorry you do not have active savings account';
 }
                    } else {
                        $res['status'] = 'error';
                        $res['message'] = 'There is an error';
                    }
                    echo json_encode($res);
                }else{
                    echo json_encode($response);
                }
            }
        }
    }

    public function login()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $res = array();

        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {

            $check_auth_client = check_auth();

            if ($check_auth_client) {
                $params = $_REQUEST;

                $login_method = strtolower((string) $this->input->post('login_method'));
                if (!in_array($login_method, array('email', 'whatsapp'), TRUE)) $login_method = 'whatsapp';
                $identifier = $this->input->post('identifier');
                if ($identifier === NULL || $identifier === '') {
                    $identifier = $login_method === 'email' ? $this->input->post('email') : $this->input->post('phone_number');
                }
                $password = $this->input->post('password');

                if ($login_method === 'email') {
                    $customer = $this->db->where('LOWER(TRIM(EmailAddress))', strtolower(trim((string) $identifier)))->get('individual_customers')->row();
                    $username = $customer ? $customer->PhoneNumber : '';
                } else {
                    $username = normalize_mithenga_phone($identifier);
                    $customer = $this->db->where('PhoneNumber', $username)->get('individual_customers')->row();
                    if (!$customer) $customer = $this->db->where('PhoneNumber', (string) $identifier)->get('individual_customers')->row();
                    if ($customer) $username = $customer->PhoneNumber;
                }

                    $response = $this->Customer_access_model->login($username, $password);
                    if (isset($response['status']) && (int) $response['status'] === 200 && $customer && !empty($customer->EmailAddress)) {
                        $body = '<h2>New Customer Portal Login</h2><p>Hello ' . htmlspecialchars($customer->Firstname) . ',</p><p>Your FundIt customer portal account was signed in on ' . date('d M Y H:i') . '.</p><p>If this was not you, contact FundIt immediately.</p>';
                        send_templated_email($customer->EmailAddress, 'FundIt Login Notification', $body);
                    }

                    if (isset($response['status']) && (int) $response['status'] === 200) {
                        log_activity(array(
                            'user_id' => 0,
                            'activity' => 'Customer Portal login: ' . ($customer ? trim($customer->Firstname . ' ' . $customer->Lastname) . ' (' . $customer->ClientId . ')' : $username),
                            'activity_cate' => 'customer_portal_login'
                        ));
                    }

                    echo json_encode($response);
               

            } else {
                json_output(401, array('status' => 401, 'message' => 'App not authorised.'));
            }
        }

    }


}



?>
