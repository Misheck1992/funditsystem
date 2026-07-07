<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Settings extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Settings_model');
        $this->load->library('form_validation');
    }


    
    public function update($id)
    {
        $row = $this->Settings_model->get_by_id($id);

        if ($row) {
            $data = array(
                'button' => 'Update',
                'action' => site_url('settings/update_action'),
		'settings_id' => set_value('settings_id', $row->settings_id),
		'logo' => set_value('logo', $row->logo),
		'address' => set_value('address', $row->address),
		'reg_fee_new' => set_value('reg_fee_new', $row->reg_fee_new),
		'reg_fee_old' => set_value('reg_fee_old', $row->reg_fee_old),
		'require_reg_fee' => set_value('require_reg_fee', $row->require_reg_fee),
		'arrears_grace' => set_value('arrears_grace', $row->arrears_grace),
		'phone_number' => set_value('phone_number', $row->phone_number),
		'company_name' => set_value('company_name', $row->company_name),
		'company_email' => set_value('company_email', $row->company_email),
		'currency' => set_value('currency', $row->currency),
		'time_zone' => set_value('time_zone', $row->time_zone),
		'tax' => set_value('tax', $row->tax),
		'defaulter_durations' => set_value('defaulter_durations', $row->defaulter_durations),
		// SMTP Email Settings
		'protocal' => set_value('protocal', $row->protocal),
		'email_host' => set_value('email_host', $row->email_host),
		'email_port' => set_value('email_port', $row->email_port),
		'email_user' => set_value('email_user', $row->email_user),
		'email_pass' => $row->email_pass, // Don't use set_value for password
	    );
			$this->load->view('admin/header');
            $this->load->view('settings/settings_form', $data);
			$this->load->view('admin/footer');
        } else {
			$this->toaster->error('Opps, settings were not retrieved updated');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
    
    public function update_action()
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('settings_id', TRUE));
        } else {
            $data = array(
		'logo' => $this->input->post('logo',TRUE),
		'address' => $this->input->post('address',TRUE),
		'arrears_grace' => $this->input->post('arrears_grace',TRUE),
		'require_reg_fee' => $this->input->post('require_reg_fee',TRUE),
		'reg_fee_old' => $this->input->post('reg_fee_old',TRUE),
		'reg_fee_new' => $this->input->post('reg_fee_new',TRUE),
		'phone_number' => $this->input->post('phone_number',TRUE),
		'company_name' => $this->input->post('company_name',TRUE),
		'company_email' => $this->input->post('company_email',TRUE),
		'currency' => $this->input->post('currency',TRUE),
		'time_zone' => $this->input->post('time_zone',TRUE),
		'tax' => $this->input->post('tax',TRUE),
		'defaulter_durations' => $this->input->post('defaulter_durations',TRUE),
		// SMTP Email Settings
		'protocal' => $this->input->post('protocal',TRUE),
		'email_host' => $this->input->post('email_host',TRUE),
		'email_port' => $this->input->post('email_port',TRUE),
		'email_user' => $this->input->post('email_user',TRUE),
	    );

	    // Only update password if a new one was provided
	    $email_pass = $this->input->post('email_pass', TRUE);
	    if (!empty($email_pass)) {
	        $data['email_pass'] = $email_pass;
	    }

			$logger = array(

				'user_id' => $this->session->userdata('user_id'),
				'activity' => 'Update system settings eg company logo, address, phone number, email etc'

			);
			log_activity($logger);

            $this->Settings_model->update($this->input->post('settings_id', TRUE), $data);
			$this->toaster->success('Success, settings were updated');
			redirect($_SERVER['HTTP_REFERER']);
        }
    }

    /**
     * Send a test email to verify SMTP settings
     */
    public function test_email()
    {
        $this->load->helper('common_queries');

        // Get logged in user's email
        $user_id = $this->session->userdata('user_id');
        $user = $this->db->get_where('employees', array('id' => $user_id))->row();

        if (!$user || empty($user->EmailAddress)) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Your account does not have an email address configured.'
            ));
            return;
        }

        $to = $user->EmailAddress;
        $subject = 'FundIt Test Email - SMTP Configuration';

        $body = '
            <h2 style="color: #1e3a5f;">SMTP Configuration Test</h2>
            <p>Hello ' . htmlspecialchars($user->Firstname) . ',</p>
            <p>This is a test email to verify that your SMTP email settings are configured correctly.</p>
            <p>If you received this email, your email configuration is working properly!</p>
            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 20px 0;">
            <p style="color: #6b7280; font-size: 12px;">
                <strong>Test Details:</strong><br>
                Date: ' . date('Y-m-d H:i:s') . '<br>
                Recipient: ' . htmlspecialchars($to) . '
            </p>
        ';

        $result = send_templated_email($to, $subject, $body);

        echo json_encode($result);
    }
    


    public function _rules() 
    {
	$this->form_validation->set_rules('logo', 'logo', 'trim|required');
	$this->form_validation->set_rules('address', 'address', 'trim|required');
	$this->form_validation->set_rules('phone_number', 'phone number', 'trim|required');
	$this->form_validation->set_rules('company_name', 'company name', 'trim|required');
	$this->form_validation->set_rules('company_email', 'company email', 'trim|required');
	$this->form_validation->set_rules('currency', 'currency', 'trim|required');
	$this->form_validation->set_rules('time_zone', 'time zone', 'trim|required');

	$this->form_validation->set_rules('settings_id', 'settings_id', 'trim');
	$this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

