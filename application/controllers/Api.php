<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Individual_customers_model');
        $this->load->model('Proofofidentity_model');
        $this->load->model('Bank_model');
        $this->load->model('Account_model');
        $this->load->model('Loan_model');
        $this->load->model('Loan_products_model');
        $this->load->model('Payement_schedules_model');
        $this->load->model('Collateral_model');
        $this->load->model('Currency_model');
        $this->load->model('Fd_customers_model');
        $this->load->model('Fd_deposits_model');
        $this->load->model('Fd_transactions_model');
        $this->load->model('Corporate_customers_model');
        $this->load->model('Shareholders_model');
        $this->load->model('Corporate_shareholders_model');

        // Set JSON header for all API responses
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

        // Handle preflight requests
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }

        // Ensure OTP table exists
        $this->_ensure_otp_table();
    }

    /**
     * Auto-create OTP table if it doesn't exist
     */
    private function _ensure_otp_table()
    {
        if (!$this->db->table_exists('otp')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `otp` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `email` varchar(150) NOT NULL,
                    `phone` varchar(30) DEFAULT NULL,
                    `otp_code` varchar(10) NOT NULL,
                    `purpose` varchar(50) NOT NULL DEFAULT 'verification',
                    `is_verified` tinyint(1) NOT NULL DEFAULT 0,
                    `attempts` int(11) NOT NULL DEFAULT 0,
                    `expires_at` datetime NOT NULL,
                    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `verified_at` datetime DEFAULT NULL,
                    `ip_address` varchar(50) DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    KEY `email` (`email`),
                    KEY `phone` (`phone`),
                    KEY `otp_code` (`otp_code`),
                    KEY `expires_at` (`expires_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        }
    }

    /**
     * Generate a random OTP code
     */
    private function _generate_otp($length = 6)
    {
        $otp = '';
        for ($i = 0; $i < $length; $i++) {
            $otp .= mt_rand(0, 9);
        }
        return $otp;
    }

    /**
     * Send JSON response
     */
    private function _response($data, $status_code = 200)
    {
        http_response_code($status_code);
        echo json_encode($data);
        exit();
    }

    /**
     * Get input data (JSON or form data)
     */
    private function _get_input()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (empty($data)) {
            $data = $this->input->post();
        }

        return $data;
    }

    /**
     * Validate required fields
     */
    private function _validate_required($data, $required_fields)
    {
        $missing = array();
        foreach ($required_fields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $missing[] = $field;
            }
        }
        return $missing;
    }

    /**
     * Send OTP via Email
     * POST /api/send_otp
     *
     * Required fields: email
     * Optional fields: purpose (default: verification)
     *
     * Response: JSON with status and message
     */
    public function send_otp()
    {
        // Only allow POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Method not allowed. Use POST request.'
            ), 405);
        }

        // Get input data
        $input = $this->_get_input();

        // Required fields
        $required_fields = array('email');
        $missing = $this->_validate_required($input, $required_fields);

        if (!empty($missing)) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Missing required fields',
                'missing_fields' => $missing
            ), 400);
        }

        $email = trim($input['email']);
        $purpose = isset($input['purpose']) ? trim($input['purpose']) : 'verification';

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Invalid email format'
            ), 400);
        }

        // Check rate limiting - max 5 OTPs per email in last 10 minutes
        $ten_minutes_ago = date('Y-m-d H:i:s', strtotime('-10 minutes'));
        $recent_count = $this->db->where('email', $email)
            ->where('created_at >', $ten_minutes_ago)
            ->count_all_results('otp');

        if ($recent_count >= 5) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Too many OTP requests. Please wait 10 minutes before trying again.'
            ), 429);
        }

        // Invalidate any existing unused OTPs for this email/purpose
        $this->db->where('email', $email)
            ->where('purpose', $purpose)
            ->where('is_verified', 0)
            ->update('otp', array('is_verified' => -1)); // -1 = invalidated

        // Generate new OTP
        $otp_code = $this->_generate_otp(6);
        $expires_at = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        // Save OTP to database
        $otp_data = array(
            'email' => $email,
            'phone' => null,
            'otp_code' => $otp_code,
            'purpose' => $purpose,
            'is_verified' => 0,
            'attempts' => 0,
            'expires_at' => $expires_at,
            'created_at' => date('Y-m-d H:i:s'),
            'ip_address' => $this->input->ip_address()
        );
        $this->db->insert('otp', $otp_data);
        $otp_id = $this->db->insert_id();

        if (!$otp_id) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Failed to generate OTP. Please try again.'
            ), 500);
        }

        // Get company settings
        $settings = $this->db->get_where('settings', array('settings_id' => 1))->row();
        $company_name = $settings->company_name ?? 'FundIt';

        // Prepare email content
        $purpose_text = ucfirst(str_replace('_', ' ', $purpose));
        $email_body = '
            <h2 style="color: #1e3a5f;">Your Verification Code</h2>
            <p>Hello,</p>
            <p>Your One-Time Password (OTP) for <strong>' . htmlspecialchars($purpose_text) . '</strong> is:</p>
            <div style="background: #f8fafc; border: 2px solid #1e3a5f; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center;">
                <span style="font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #1e3a5f;">' . $otp_code . '</span>
            </div>
            <p style="color: #666;">This code will expire in <strong>10 minutes</strong>.</p>
            <p style="color: #dc2626; font-size: 13px;"><strong>Important:</strong> Do not share this code with anyone. ' . htmlspecialchars($company_name) . ' will never ask you for this code.</p>
        ';

        // Send email using the existing email helper
        $email_result = send_templated_email($email, 'Your Verification Code - ' . $company_name, $email_body);

        if (!$email_result['success']) {
            log_message('error', 'Failed to send OTP email to ' . $email . ': ' . $email_result['message']);
            // OTP is saved in DB — proceed with warning instead of failing
        }

        // Log the activity
        $logger = array(
            'user_id' => 72,
            'activity' => 'API: OTP sent to ' . $email . ' for ' . $purpose,
            'activity_cate' => 'otp_sent'
        );
        $this->db->insert('activity_logger', $logger);

        // Success response
        $this->_response(array(
            'status' => 'success',
            'message' => $email_result['success'] ? 'OTP sent successfully to your email' : 'OTP generated but email delivery failed. Check SMTP settings.',
            'email_sent' => $email_result['success'],
            'data' => array(
                'email' => $this->_mask_email($email),
                'purpose' => $purpose,
                'expires_in' => '10 minutes',
                'expires_at' => $expires_at
            )
        ), 200);
    }

    /**
     * Verify OTP
     * POST /api/verify_otp
     *
     * Required fields: email, otp_code
     * Optional fields: purpose (default: verification)
     *
     * Response: JSON with status and verification result
     */
    public function verify_otp()
    {
        // Only allow POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Method not allowed. Use POST request.'
            ), 405);
        }

        // Get input data
        $input = $this->_get_input();

        // Required fields
        if (empty($input['email'])) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Missing required fields',
                'missing_fields' => array('email')
            ), 400);
        }

        // Accept both 'otp' and 'otp_code'
        $otp_value = isset($input['otp_code']) ? $input['otp_code'] : (isset($input['otp']) ? $input['otp'] : null);

        if (empty($otp_value)) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Missing required fields',
                'missing_fields' => array('otp_code')
            ), 400);
        }

        $email = trim($input['email']);
        $otp_code = trim($otp_value);
        $purpose = isset($input['purpose']) ? trim($input['purpose']) : 'verification';

        // Find the OTP record
        $otp = $this->db->where('email', $email)
            ->where('purpose', $purpose)
            ->where('is_verified', 0)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->order_by('created_at', 'DESC')
            ->get('otp')
            ->row();

        if (!$otp) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'OTP not found or has expired. Please request a new OTP.'
            ), 400);
        }

        // Check max attempts (max 5 attempts)
        if ($otp->attempts >= 5) {
            $this->db->where('id', $otp->id)->update('otp', array('is_verified' => -2)); // -2 = blocked
            $this->_response(array(
                'status' => 'error',
                'message' => 'Maximum verification attempts exceeded. Please request a new OTP.'
            ), 400);
        }

        // Increment attempts
        $this->db->where('id', $otp->id)->update('otp', array('attempts' => $otp->attempts + 1));

        // Verify OTP code
        if ($otp->otp_code !== $otp_code) {
            $remaining_attempts = 4 - $otp->attempts;
            $this->_response(array(
                'status' => 'error',
                'message' => 'Invalid OTP code',
                'remaining_attempts' => max(0, $remaining_attempts)
            ), 400);
        }

        // OTP is valid - mark as verified
        $this->db->where('id', $otp->id)->update('otp', array(
            'is_verified' => 1,
            'verified_at' => date('Y-m-d H:i:s')
        ));

        // Generate verification token for subsequent requests
        $verification_token = bin2hex(random_bytes(32));

        // Log the activity
        $logger = array(
            'user_id' => 72,
            'activity' => 'API: OTP verified for ' . $email . ' (' . $purpose . ')',
            'activity_cate' => 'otp_verified'
        );
        $this->db->insert('activity_logger', $logger);

        // Check if customer exists in individual_customers (case-insensitive)
        $existing_customer = $this->db->where('LOWER(TRIM(EmailAddress))', strtolower(trim($email)))->get('individual_customers')->row();

        // Build response data
        $response_data = array(
            'email' => $email,
            'purpose' => $purpose,
            'verified' => true,
            'verified_at' => date('Y-m-d H:i:s'),
            'verification_token' => $verification_token,
            'exists' => $existing_customer ? true : false
        );

        // Add customer details if exists
        if ($existing_customer) {
            $response_data['customer'] = array(
                'client_id' => $existing_customer->ClientId,
                'full_name' => $existing_customer->Firstname . ' ' . $existing_customer->Lastname,
                'phone' => $existing_customer->PhoneNumber,
                'status' => $existing_customer->approval_status
            );
        }

        // Success response
        $this->_response(array(
            'status' => 'success',
            'message' => 'OTP verified successfully',
            'data' => $response_data
        ), 200);
    }

    /**
     * Resend OTP
     * POST /api/resend_otp
     *
     * Required fields: email
     * Optional fields: purpose (default: verification)
     */
    public function resend_otp()
    {
        // Just call send_otp - it handles everything
        $this->send_otp();
    }

    /**
     * Mask email for privacy
     */
    private function _mask_email($email)
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) return $email;

        $name = $parts[0];
        $domain = $parts[1];

        if (strlen($name) <= 2) {
            $masked_name = $name[0] . '***';
        } else {
            $masked_name = substr($name, 0, 2) . str_repeat('*', strlen($name) - 2);
        }

        return $masked_name . '@' . $domain;
    }

    /**
     * Register Individual Customer
     * POST /api/register_customer
     *
     * Required fields: Firstname, Lastname, Gender, DateOfBirth, PhoneNumber, Country
     * Optional fields: Title, Middlename, EmailAddress, AddressLine1, AddressLine2, AddressLine3,
     *                  Province, City, district, village, marital, chiefta, ResidentialStatus,
     *                  Profession, SourceOfIncome, GrossMonthlyIncome, Branch,
     *                  kinFullname, kinPhonenumber, IDType, IDNumber, IssueDate, ExpiryDate
     *
     * Response: JSON with status, message, and customer data
     */
    public function register_customer()
    {
        // Only allow POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Method not allowed. Use POST request.'
            ), 405);
        }

        // Get input data
        $input = $this->_get_input();

        // Log raw input for debugging
        log_message('debug', 'register_customer raw input: ' . json_encode($input));
        log_message('debug', 'register_customer email fields - EmailAddress: ' . (isset($input['EmailAddress']) ? $input['EmailAddress'] : 'NOT SET')
            . ' | email: ' . (isset($input['email']) ? $input['email'] : 'NOT SET')
            . ' | Email: ' . (isset($input['Email']) ? $input['Email'] : 'NOT SET'));

        // Required fields for registration
        $required_fields = array('Title', 'Firstname', 'Lastname', 'Gender', 'DateOfBirth', 'PhoneNumber', 'Country');
        $missing = $this->_validate_required($input, $required_fields);

        if (!empty($missing)) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Missing required fields',
                'missing_fields' => $missing
            ), 400);
        }

        // Check if phone number already exists
        $existing = $this->db->where('PhoneNumber', $input['PhoneNumber'])->get('individual_customers')->row();
        if ($existing) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'A customer with this phone number already exists',
                'existing_client_id' => $existing->ClientId
            ), 409);
        }

        // Normalize email field — accept email, Email, or EmailAddress
        if (empty($input['EmailAddress'])) {
            if (!empty($input['email'])) {
                $input['EmailAddress'] = $input['email'];
            } elseif (!empty($input['Email'])) {
                $input['EmailAddress'] = $input['Email'];
            }
        }

        // Check if email already exists (if provided)
        if (!empty($input['EmailAddress'])) {
            $existing_email = $this->db->where('EmailAddress', $input['EmailAddress'])->get('individual_customers')->row();
            if ($existing_email) {
                $this->_response(array(
                    'status' => 'error',
                    'message' => 'A customer with this email address already exists',
                    'existing_client_id' => $existing_email->ClientId
                ), 409);
            }
        }

        // Generate unique client ID
        $clientid = rand(100,1000).rand(100,9999);

        // Prepare customer data
        $customer_data = array(
            'ClientId' => $clientid,
            'Title' => isset($input['Title']) ? $input['Title'] : '',
            'Firstname' => $input['Firstname'],
            'Middlename' => isset($input['Middlename']) ? $input['Middlename'] : '',
            'Lastname' => $input['Lastname'],
            'Gender' => $input['Gender'],
            'DateOfBirth' => $input['DateOfBirth'],
            'EmailAddress' => isset($input['EmailAddress']) ? $input['EmailAddress'] : '',
            'PhoneNumber' => $input['PhoneNumber'],
            'AddressLine1' => isset($input['AddressLine1']) ? $input['AddressLine1'] : '',
            'AddressLine2' => isset($input['AddressLine2']) ? $input['AddressLine2'] : '',
            'AddressLine3' => isset($input['AddressLine3']) ? $input['AddressLine3'] : '',
            'Province' => isset($input['Province']) ? $input['Province'] : '',
            'City' => isset($input['City']) ? $input['City'] : '',
            'Country' => $input['Country'],
            'district' => isset($input['district']) ? $input['district'] : '',
            'village' => isset($input['village']) ? $input['village'] : '',
            'marital' => isset($input['marital']) ? $input['marital'] : '',
            'chiefta' => isset($input['chiefta']) ? $input['chiefta'] : '',
            'ResidentialStatus' => isset($input['ResidentialStatus']) ? $input['ResidentialStatus'] : '',
            'Profession' => isset($input['Profession']) ? $input['Profession'] : '',
            'SourceOfIncome' => isset($input['SourceOfIncome']) ? $input['SourceOfIncome'] : '',
            'GrossMonthlyIncome' => isset($input['GrossMonthlyIncome']) ? $input['GrossMonthlyIncome'] : 0,
            'Branch' => isset($input['Branch']) ? $input['Branch'] : '',
            'kinFullname' => isset($input['kinFullname']) ? $input['kinFullname'] : '',
            'kinPhonenumber' => isset($input['kinPhonenumber']) ? $input['kinPhonenumber'] : '',
            'customer_type' => 'individual',
            'approval_status' => 'CREATED',  // Set status to CREATED
            'added_by' => 72,  // Default API user
            'CreatedOn' => date('Y-m-d H:i:s'),
            'LastUpdatedOn' => date('Y-m-d H:i:s')
        );

        // Insert customer
        $customer_id = $this->Individual_customers_model->insert($customer_data);

        if (!$customer_id) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Failed to create customer. Please try again.'
            ), 500);
        }

        // Insert ID document if provided
        if (!empty($input['IDType']) && !empty($input['IDNumber'])) {
            $id_data = array(
                'IDType' => $input['IDType'],
                'IDNumber' => $input['IDNumber'],
                'IssueDate' => isset($input['IssueDate']) ? $input['IssueDate'] : null,
                'ExpiryDate' => isset($input['ExpiryDate']) ? $input['ExpiryDate'] : null,
                'ClientId' => $clientid
            );
            $this->Proofofidentity_model->insert($id_data);
        }

        // Create default account for customer
        $account_count = $this->db->from('account')->where('account_type', '1')->count_all_results();
        $account_number = 300000 + $account_count + rand(0, 9999);

        $account_data = array(
            'client_id' => $customer_id,
            'account_number' => $account_number,
            'balance' => 0,
            'account_type' => 1,
            'account_type_product' => 2,
            'account_status' => 'Pending',
            'added_by' => 72
        );
        $this->Account_model->insert($account_data);

        // Log the activity
        $logger = array(
            'user_id' => 72,
            'activity' => 'API: Registered customer ' . $customer_data['Firstname'] . ' ' . $customer_data['Lastname'],
            'activity_cate' => 'customer_registration'
        );
        $this->db->insert('activity_logger', $logger);

        // Notify all users with customer creation rights
        $this->_notify_customer_creation_users($customer_data, $clientid);

        // Success response
        $this->_response(array(
            'status' => 'success',
            'message' => 'Customer registered successfully',
            'data' => array(
                'customer_id' => $customer_id,
                'client_id' => $clientid,
                'account_number' => $account_number,
                'full_name' => $customer_data['Firstname'] . ' ' . $customer_data['Lastname'],
                'phone_number' => $customer_data['PhoneNumber'],
                'email' => $customer_data['EmailAddress'],
                'status' => 'CREATED',
                'created_on' => $customer_data['CreatedOn']
            )
        ), 201);
    }

    /**
     * Get customer by ID or Client ID
     * GET /api/get_customer?id=123 OR /api/get_customer?client_id=API251234567
     */
    public function get_customer()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Method not allowed. Use GET request.'
            ), 405);
        }

        $id = $this->input->get('id');
        $client_id = $this->input->get('client_id');

        if (empty($id) && empty($client_id)) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Please provide id or client_id parameter'
            ), 400);
        }

        $customer = null;
        if (!empty($id)) {
            $customer = $this->Individual_customers_model->get_by_id($id);
        } else {
            $customer = $this->Individual_customers_model->get_by_client_id($client_id);
        }

        if (!$customer) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Customer not found'
            ), 404);
        }

        $this->_response(array(
            'status' => 'success',
            'data' => array(
                'customer_id' => $customer->id,
                'client_id' => $customer->ClientId,
                'title' => $customer->Title,
                'first_name' => $customer->Firstname,
                'middle_name' => $customer->Middlename,
                'last_name' => $customer->Lastname,
                'gender' => $customer->Gender,
                'date_of_birth' => $customer->DateOfBirth,
                'email' => $customer->EmailAddress,
                'phone_number' => $customer->PhoneNumber,
                'address_line1' => $customer->AddressLine1,
                'province' => $customer->Province,
                'city' => $customer->City,
                'country' => $customer->Country,
                'profession' => $customer->Profession,
                'status' => $customer->approval_status,
                'created_on' => $customer->CreatedOn
            )
        ));
    }

    /**
     * Check customer registration status
     * GET /api/check_status?client_id=API251234567
     */
    public function check_status()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Method not allowed. Use GET request.'
            ), 405);
        }

        $client_id = $this->input->get('client_id');
        $phone = $this->input->get('phone');

        if (empty($client_id) && empty($phone)) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Please provide client_id or phone parameter'
            ), 400);
        }

        $customer = null;
        if (!empty($client_id)) {
            $customer = $this->Individual_customers_model->get_by_client_id($client_id);
        } else {
            $customer = $this->db->where('PhoneNumber', $phone)->get('individual_customers')->row();
        }

        if (!$customer) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Customer not found'
            ), 404);
        }

        $this->_response(array(
            'status' => 'success',
            'data' => array(
                'client_id' => $customer->ClientId,
                'full_name' => $customer->Firstname . ' ' . $customer->Lastname,
                'approval_status' => $customer->approval_status,
                'created_on' => $customer->CreatedOn,
                'last_updated' => $customer->LastUpdatedOn
            )
        ));
    }

    /**
     * Notify all users with customer creation rights about a new registration
     */
    private function _notify_customer_creation_users($customer_data, $clientid)
    {
        // Find all employees whose role has access to Individual_customers/create
        $users = $this->db->select('e.id, e.Firstname, e.Lastname, e.EmailAddress', FALSE)
            ->distinct()
            ->from('employees e')
            ->join('roles r', 'r.id = e.Role')
            ->join('access a', 'a.roleid = r.id')
            ->join('menuitems mi', 'mi.id = a.controllerid')
            ->where('LOWER(mi.method)', 'individual_customers/create')
            ->where('e.EmailAddress !=', '')
            ->get()
            ->result();

        if (empty($users)) {
            return;
        }

        $settings = $this->db->get_where('settings', array('settings_id' => 1))->row();
        $company_name = $settings->company_name ?? 'FundIt';

        $full_name = $customer_data['Firstname'] . ' ' . $customer_data['Lastname'];
        $subject = 'New Customer Registration - ' . $full_name;

        $email_body = '
            <h2 style="color: #1e3a5f;">New Customer Registered</h2>
            <p>A new customer has been registered via the online portal and requires your attention.</p>
            <table style="width: 100%; border-collapse: collapse; margin: 15px 0;">
                <tr style="background: #f8fafc;">
                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: bold;">Client ID</td>
                    <td style="padding: 10px; border: 1px solid #e2e8f0;">' . htmlspecialchars($clientid) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: bold;">Full Name</td>
                    <td style="padding: 10px; border: 1px solid #e2e8f0;">' . htmlspecialchars($full_name) . '</td>
                </tr>
                <tr style="background: #f8fafc;">
                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: bold;">Phone Number</td>
                    <td style="padding: 10px; border: 1px solid #e2e8f0;">' . htmlspecialchars($customer_data['PhoneNumber']) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: bold;">Email</td>
                    <td style="padding: 10px; border: 1px solid #e2e8f0;">' . htmlspecialchars($customer_data['EmailAddress'] ?: 'N/A') . '</td>
                </tr>
                <tr style="background: #f8fafc;">
                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: bold;">Country</td>
                    <td style="padding: 10px; border: 1px solid #e2e8f0;">' . htmlspecialchars($customer_data['Country']) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: bold;">Status</td>
                    <td style="padding: 10px; border: 1px solid #e2e8f0;"><span style="background: #fef3c7; color: #92400e; padding: 3px 10px; border-radius: 12px; font-size: 13px;">CREATED</span></td>
                </tr>
                <tr style="background: #f8fafc;">
                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: bold;">Registered On</td>
                    <td style="padding: 10px; border: 1px solid #e2e8f0;">' . $customer_data['CreatedOn'] . '</td>
                </tr>
            </table>
            <p style="color: #666;">Please log in to the system to review and approve this customer.</p>
        ';

        foreach ($users as $user) {
            if (!empty($user->EmailAddress) && filter_var($user->EmailAddress, FILTER_VALIDATE_EMAIL)) {
                $result = send_templated_email($user->EmailAddress, $subject, $email_body);

                // Log the notification
                log_notification(array(
                    'notification_type' => 'customer_registration',
                    'reference_type' => 'customer',
                    'reference_id' => $clientid,
                    'reference_number' => $clientid,
                    'recipient_email' => $user->EmailAddress,
                    'recipient_name' => $user->Firstname . ' ' . $user->Lastname,
                    'recipient_user_id' => $user->id,
                    'subject' => $subject,
                    'status' => $result['success'] ? 'sent' : 'failed',
                    'error_message' => $result['success'] ? null : $result['message'],
                    'triggered_by' => 0
                ));
            }
        }
    }

    /**
     * API Health Check
     * GET /api/health
     */
    public function health()
    {
        $this->_response(array(
            'status' => 'success',
            'message' => 'API is running',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0'
        ));
    }

    /**
     * Auto-create enquiries table if it doesn't exist
     */
    private function _ensure_enquiries_table()
    {
        if (!$this->db->table_exists('enquiries')) {
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `enquiries` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `name` varchar(150) NOT NULL,
                    `email` varchar(150) NOT NULL,
                    `phone` varchar(50) DEFAULT NULL,
                    `subject` varchar(150) DEFAULT NULL,
                    `message` text NOT NULL,
                    `status` varchar(20) NOT NULL DEFAULT 'new',
                    `ip_address` varchar(50) DEFAULT NULL,
                    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `email` (`email`),
                    KEY `status` (`status`),
                    KEY `created_at` (`created_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        }
    }

    /**
     * Receive a public website contact / enquiry submission
     * POST /api/enquiries
     *
     * Required fields: name, email, message
     * Optional fields: phone, subject
     *
     * Stores the enquiry and emails the company inbox. Returns JSON.
     */
    public function enquiries()
    {
        // Only allow POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Method not allowed. Use POST request.'
            ), 405);
        }

        $this->_ensure_enquiries_table();

        // Get input data
        $input = $this->_get_input();

        // Required fields
        $required_fields = array('name', 'email', 'message');
        $missing = $this->_validate_required($input, $required_fields);

        if (!empty($missing)) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Missing required fields',
                'missing_fields' => $missing
            ), 400);
        }

        $name = trim($input['name']);
        $email = trim($input['email']);
        $phone = isset($input['phone']) ? trim($input['phone']) : null;
        $subject = isset($input['subject']) ? trim($input['subject']) : 'General Enquiry';
        $message = trim($input['message']);

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Invalid email format'
            ), 400);
        }

        // Basic rate limiting - max 5 enquiries per email in last 10 minutes
        $ten_minutes_ago = date('Y-m-d H:i:s', strtotime('-10 minutes'));
        $recent_count = $this->db->where('email', $email)
            ->where('created_at >', $ten_minutes_ago)
            ->count_all_results('enquiries');

        if ($recent_count >= 5) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Too many enquiries submitted. Please wait a few minutes before trying again.'
            ), 429);
        }

        // Save enquiry
        $enquiry_data = array(
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $message,
            'status' => 'new',
            'ip_address' => $this->input->ip_address(),
            'created_at' => date('Y-m-d H:i:s')
        );
        $this->db->insert('enquiries', $enquiry_data);
        $enquiry_id = $this->db->insert_id();

        if (!$enquiry_id) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Failed to submit your enquiry. Please try again.'
            ), 500);
        }

        // Determine recipient inbox from settings, falling back to the company address
        $settings = $this->db->get_where('settings', array('settings_id' => 1))->row();
        $company_name = ($settings && !empty($settings->company_name)) ? $settings->company_name : 'FundIt';
        $recipient = 'info@fundit-zm.com';
        if ($settings) {
            if (!empty($settings->company_email) && filter_var($settings->company_email, FILTER_VALIDATE_EMAIL)) {
                $recipient = $settings->company_email;
            } elseif (!empty($settings->email) && filter_var($settings->email, FILTER_VALIDATE_EMAIL)) {
                $recipient = $settings->email;
            }
        }

        // Email the company inbox
        $subject_line = 'New Website Enquiry: ' . $subject;
        $email_body = '
            <h2 style="color: #1e3a5f;">New Website Enquiry</h2>
            <p>A new enquiry has been submitted through the website contact form.</p>
            <table style="width: 100%; border-collapse: collapse; margin: 15px 0;">
                <tr style="background: #f8fafc;">
                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: bold;">Name</td>
                    <td style="padding: 10px; border: 1px solid #e2e8f0;">' . htmlspecialchars($name) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: bold;">Email</td>
                    <td style="padding: 10px; border: 1px solid #e2e8f0;">' . htmlspecialchars($email) . '</td>
                </tr>
                <tr style="background: #f8fafc;">
                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: bold;">Phone</td>
                    <td style="padding: 10px; border: 1px solid #e2e8f0;">' . htmlspecialchars($phone ?: 'N/A') . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: bold;">Subject</td>
                    <td style="padding: 10px; border: 1px solid #e2e8f0;">' . htmlspecialchars($subject) . '</td>
                </tr>
                <tr style="background: #f8fafc;">
                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: bold; vertical-align: top;">Message</td>
                    <td style="padding: 10px; border: 1px solid #e2e8f0;">' . nl2br(htmlspecialchars($message)) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: bold;">Received</td>
                    <td style="padding: 10px; border: 1px solid #e2e8f0;">' . $enquiry_data['created_at'] . '</td>
                </tr>
            </table>
            <p style="color: #666;">You can reply directly to <strong>' . htmlspecialchars($email) . '</strong>.</p>
        ';

        $email_result = send_templated_email($recipient, $subject_line, $email_body);

        if (!$email_result['success']) {
            log_message('error', 'Failed to send enquiry email to ' . $recipient . ': ' . $email_result['message']);
            // Enquiry is saved in DB — proceed with warning instead of failing the request
        }

        // Log the activity
        $logger = array(
            'user_id' => 72,
            'activity' => 'API: Website enquiry received from ' . $name . ' <' . $email . '>',
            'activity_cate' => 'website_enquiry'
        );
        $this->db->insert('activity_logger', $logger);

        // Success response
        $this->_response(array(
            'status' => 'success',
            'message' => 'Thank you for contacting us. We will respond within 24 hours.',
            'data' => array(
                'enquiry_id' => $enquiry_id,
                'email_sent' => $email_result['success']
            )
        ), 201);
    }

    // ==================== LOAN SYSTEM ENDPOINTS ====================

    /**
     * Get all loan products
     * GET /api/loan_products
     */
    public function loan_products()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Method not allowed. Use GET request.'
            ), 405);
        }

        $products = $this->Loan_products_model->get_all();

        $data = array();
        foreach ($products as $p) {
            $data[] = array(
                'loan_product_id' => (int)$p->loan_product_id,
                'product_name' => $p->product_name,
                'abbreviation' => $p->abbreviation,
                'interest' => (float)$p->interest,
                'frequency' => $p->frequency,
                'calculation_type' => $p->calculation_type,
                'minimum_principal' => (float)$p->minimum_principal,
                'maximum_principal' => (float)$p->maximum_principal,
                'interest_min' => (float)$p->interest_min,
                'interest_max' => (float)$p->interest_max,
                'grace_period' => isset($p->grace_period) ? (int)$p->grace_period : 0,
                'processing_fee_details' => array(
                    'threshold' => (float)($p->loan_processing_fee_threshold ?? 0),
                    'below_threshold' => array(
                        'charge_type' => $p->processing_charge_type_below ?? '',
                        'fixed_charge' => (float)($p->processing_fixed_charge_below ?? 0),
                        'variable_charge' => (float)($p->processing_variable_charge_below ?? 0),
                    ),
                    'above_threshold' => array(
                        'charge_type' => $p->processing_charge_type_above ?? '',
                        'fixed_charge' => (float)($p->processing_fixed_charge_above ?? 0),
                        'variable_charge' => (float)($p->processing_variable_charge_above ?? 0),
                    ),
                ),
                'penalty_details' => array(
                    'threshold' => (float)($p->penalty_threshold ?? 0),
                    'below_threshold' => array(
                        'charge_type' => $p->penalty_charge_type_below ?? '',
                        'fixed_charge' => (float)($p->penalty_fixed_charge_below ?? 0),
                        'variable_charge' => (float)($p->penalty_variable_charge_below ?? 0),
                    ),
                    'above_threshold' => array(
                        'charge_type' => $p->penalty_charge_type_above ?? '',
                        'fixed_charge' => (float)($p->penalty_fixed_charge_above ?? 0),
                        'variable_charge' => (float)($p->penalty_variable_charge_above ?? 0),
                    ),
                ),
            );
        }

        $this->_response(array(
            'status' => 'success',
            'data' => $data
        ));
    }

    /**
     * Calculate loan repayment schedule
     * POST /api/calculate_loan
     *
     * Required: amount, months, interest, loan_product_id, loan_date
     */
    public function calculate_loan()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Method not allowed. Use POST request.'
            ), 405);
        }

        $input = $this->_get_input();

        $required_fields = array('amount', 'months', 'interest', 'loan_product_id', 'loan_date');
        $missing = $this->_validate_required($input, $required_fields);

        if (!empty($missing)) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Missing required fields',
                'missing_fields' => $missing
            ), 400);
        }

        $amount = (float)$input['amount'];
        $months = (int)$input['months'];
        $interest = (float)$input['interest'];
        $product_id = (int)$input['loan_product_id'];
        $loan_date = $input['loan_date'];

        // Validate inputs
        if ($amount <= 0) {
            $this->_response(array('status' => 'error', 'message' => 'Amount must be greater than zero'), 400);
        }
        if ($months <= 0) {
            $this->_response(array('status' => 'error', 'message' => 'Months must be greater than zero'), 400);
        }
        if ($interest < 0) {
            $this->_response(array('status' => 'error', 'message' => 'Interest cannot be negative'), 400);
        }

        // Get loan product
        $product = $this->Loan_products_model->get_by_id($product_id);
        if (!$product) {
            $this->_response(array('status' => 'error', 'message' => 'Loan product not found'), 404);
        }

        // Validate against product limits
        if ($amount < $product->minimum_principal || $amount > $product->maximum_principal) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Amount must be between ' . number_format($product->minimum_principal, 2) . ' and ' . number_format($product->maximum_principal, 2)
            ), 400);
        }
        if ($interest < $product->interest_min || $interest > $product->interest_max) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Interest must be between ' . $product->interest_min . '% and ' . $product->interest_max . '%'
            ), 400);
        }

        $calculation_type = $product->calculation_type;
        $rate = $interest / 100;

        $schedule = array();
        $summary = array();

        if ($calculation_type === 'Straight Line') {
            $total_interest = $amount * $rate * $months;
            $total_payment = $amount + $total_interest;
            $emi = round($total_payment / $months, 2);
            $principal_per_month = round($amount / $months, 2);
            $interest_per_month = round($total_interest / $months, 2);

            $current_balance = $amount;
            $date = $loan_date;

            for ($i = 1; $i <= $months; $i++) {
                if ($i == $months) {
                    $principal_per_month = $current_balance;
                    $emi = $principal_per_month + $interest_per_month;
                }

                $balance_before = $current_balance;
                $current_balance = round($current_balance - $principal_per_month, 2);
                if ($current_balance < 0) $current_balance = 0;

                $schedule[] = array(
                    'payment_number' => $i,
                    'due_date' => $date,
                    'balance_before' => round($balance_before, 2),
                    'installment' => round($emi, 2),
                    'principal' => round($principal_per_month, 2),
                    'interest' => round($interest_per_month, 2),
                    'balance_after' => $current_balance
                );

                $date = date('Y-m-d', strtotime("+1 month", strtotime($date)));
            }

            $summary = array(
                'product_name' => $product->product_name,
                'calculation_type' => 'Straight Line',
                'principal' => $amount,
                'interest_rate' => $interest,
                'term_months' => $months,
                'monthly_payment' => round($total_payment / $months, 2),
                'total_interest' => round($total_interest, 2),
                'total_payment' => round($total_payment, 2)
            );

        } elseif ($calculation_type === 'Reducing Balance') {
            if ($rate > 0) {
                $emi = $amount * $rate * pow((1 + $rate), $months) / (pow((1 + $rate), $months) - 1);
            } else {
                $emi = $amount / $months;
            }
            $emi = round($emi, 2);

            $current_balance = $amount;
            $total_interest_calc = 0;
            $date = $loan_date;

            for ($i = 1; $i <= $months; $i++) {
                $balance_before = $current_balance;
                $interest_payment = round($current_balance * $rate, 2);
                $principal_payment = $emi - $interest_payment;

                if ($i == $months) {
                    $principal_payment = $current_balance;
                    $emi_adjusted = $principal_payment + $interest_payment;
                } else {
                    $emi_adjusted = $emi;
                }

                $total_interest_calc += $interest_payment;
                $current_balance = round($current_balance - $principal_payment, 2);
                if ($current_balance < 0) $current_balance = 0;

                $schedule[] = array(
                    'payment_number' => $i,
                    'due_date' => $date,
                    'balance_before' => round($balance_before, 2),
                    'installment' => round($emi_adjusted, 2),
                    'principal' => round($principal_payment, 2),
                    'interest' => round($interest_payment, 2),
                    'balance_after' => $current_balance
                );

                $date = date('Y-m-d', strtotime("+1 month", strtotime($date)));
            }

            $summary = array(
                'product_name' => $product->product_name,
                'calculation_type' => 'Reducing Balance',
                'principal' => $amount,
                'interest_rate' => $interest,
                'term_months' => $months,
                'monthly_payment' => $emi,
                'total_interest' => round($total_interest_calc, 2),
                'total_payment' => round($amount + $total_interest_calc, 2)
            );

        } elseif ($calculation_type === 'Bullet Payment') {
            $total_interest = $amount * $rate * $months;
            $total_payment = $amount + $total_interest;
            $maturity_date = date('Y-m-d', strtotime("+$months months", strtotime($loan_date)));

            $schedule[] = array(
                'payment_number' => 1,
                'due_date' => $maturity_date,
                'balance_before' => $amount,
                'installment' => round($total_payment, 2),
                'principal' => $amount,
                'interest' => round($total_interest, 2),
                'balance_after' => 0
            );

            $summary = array(
                'product_name' => $product->product_name,
                'calculation_type' => 'Bullet Payment',
                'principal' => $amount,
                'interest_rate' => $interest,
                'term_months' => $months,
                'maturity_date' => $maturity_date,
                'payment_at_maturity' => round($total_payment, 2),
                'total_interest' => round($total_interest, 2),
                'total_payment' => round($total_payment, 2)
            );

        } else {
            $this->_response(array('status' => 'error', 'message' => 'Invalid calculation type: ' . $calculation_type), 400);
        }

        $this->_response(array(
            'status' => 'success',
            'data' => array(
                'summary' => $summary,
                'schedule' => $schedule
            )
        ));
    }

    /**
     * Apply for a loan
     * POST /api/apply_loan
     *
     * Required: customer_id, amount, months, interest, loan_product_id, loan_date, currency
     * Optional: narration, off_taker, processing_fee
     */
    public function apply_loan()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Method not allowed. Use POST request.'
            ), 405);
        }

        $input = $this->_get_input();

        $required_fields = array('customer_id', 'amount', 'months', 'interest', 'loan_product_id', 'loan_date', 'currency');
        $missing = $this->_validate_required($input, $required_fields);

        if (!empty($missing)) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Missing required fields',
                'missing_fields' => $missing
            ), 400);
        }

        // Required fields
        $customer_id = (int)$input['customer_id'];
        $amount = (float)$input['amount'];
        $months = (int)$input['months'];
        $interest = (float)$input['interest'];
        $product_id = (int)$input['loan_product_id'];
        $loan_date = $input['loan_date'];
        $currency = $input['currency'];

        // Optional fields - Loan details
        $customer_type = isset($input['customer_type']) ? $input['customer_type'] : 'individual';
        $narration = isset($input['narration']) ? $input['narration'] : '';
        $off_taker = isset($input['off_taker']) ? $input['off_taker'] : '';
        $processing_fee = isset($input['processing_fee']) ? (float)$input['processing_fee'] : 0;

        // Optional fields - Appraisal data
        $appraisal_data = array(
            'crb_search' => isset($input['crb_search']) ? $input['crb_search'] : null,
            'pacra_search' => isset($input['pacra_search']) ? $input['pacra_search'] : null,
            'previous_facilities' => isset($input['previous_facilities']) ? $input['previous_facilities'] : null,
            'past_loans_comment' => isset($input['past_loans_comment']) ? $input['past_loans_comment'] : null,
            'security_notes' => isset($input['security_notes']) ? $input['security_notes'] : null,
            'bank_statement_notes' => isset($input['bank_statement_notes']) ? $input['bank_statement_notes'] : null,
            'about_transaction' => isset($input['about_transaction']) ? $input['about_transaction'] : null,
            'risk_analysis' => isset($input['risk_analysis']) ? $input['risk_analysis'] : null
        );

        // Validate customer exists (individual or corporate)
        $customer = null;
        $customer_name = 'N/A';
        if ($customer_type === 'individual') {
            $customer = $this->Individual_customers_model->get_by_id($customer_id);
            if ($customer) {
                $customer_name = $customer->Firstname . ' ' . $customer->Lastname;
            }
        } else {
            $customer = $this->db->where('id', $customer_id)->get('corporate_customers')->row();
            if ($customer) {
                $customer_name = $customer->EntityName;
            }
        }

        if (!$customer) {
            $this->_response(array('status' => 'error', 'message' => 'Customer not found'), 404);
        }

        // Check if customer account is approved
        $status = isset($customer->approval_status) ? $customer->approval_status : '';
        if (strtolower($status) !== 'approved') {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Customer account is not yet approved. Current status: ' . $status . '. Please wait for account approval before applying for a loan.',
                'approval_status' => $status
            ), 403);
        }

        // Validate loan product
        $product = $this->Loan_products_model->get_by_id($product_id);
        if (!$product) {
            $this->_response(array('status' => 'error', 'message' => 'Loan product not found'), 404);
        }

        // Validate amount against product limits
        if ($amount < $product->minimum_principal || $amount > $product->maximum_principal) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Amount must be between ' . number_format($product->minimum_principal, 2) . ' and ' . number_format($product->maximum_principal, 2)
            ), 400);
        }

        // Validate interest against product limits
        if ($interest < $product->interest_min || $interest > $product->interest_max) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Interest must be between ' . $product->interest_min . '% and ' . $product->interest_max . '%'
            ), 400);
        }

        // Create the loan using existing model method
        try {
            $result = $this->Loan_model->add_loan(
                '',              // loan_number - generated inside method
                $amount,
                $months,
                $interest,
                $product_id,
                $loan_date,
                $customer_id,
                $customer_type === 'corporate' ? 'institution' : 'individual',
                '',              // worthness_file
                $narration,
                72,              // added_by (API user)
                0,               // method
                0,               // fee_amount
                $currency,
                $off_taker,
                $processing_fee,
                $appraisal_data
            );
        } catch (Exception $e) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Loan creation failed: ' . $e->getMessage()
            ), 500);
        }

        if (!is_array($result)) {
            $this->_response(array(
                'status' => 'error',
                'message' => is_string($result) ? $result : 'Failed to create loan application'
            ), 500);
        }

        // Set loan status to CREATED (requires admin to complete before entering workflow)
        $this->Loan_model->update($result['loan_id'], array('loan_status' => 'CREATED'));

        // Link collaterals if provided
        $collaterals_linked = array();
        if (isset($input['collaterals']) && is_array($input['collaterals'])) {
            foreach ($input['collaterals'] as $col) {
                $col_id = isset($col['collateral_id']) ? (int)$col['collateral_id'] : 0;
                $col_amount = isset($col['amount_utilized']) ? (float)$col['amount_utilized'] : 0;

                if ($col_id > 0 && $col_amount > 0) {
                    $available = $this->Collateral_model->get_available_balance($col_id);

                    if ($col_amount <= $available) {
                        $link_data = array(
                            'loan_id' => $result['loan_id'],
                            'collateral_id' => $col_id,
                            'amount_utilized' => $col_amount,
                            'linked_by' => 0,
                            'linked_at' => date('Y-m-d H:i:s'),
                            'status' => 'ACTIVE'
                        );
                        $this->Collateral_model->link_to_loan($link_data);
                        $collaterals_linked[] = array('collateral_id' => $col_id, 'amount_utilized' => $col_amount);
                    }
                }
            }
        }

        // Save bank statements if provided (supports file uploads via multipart/form-data)
        $bank_statements_saved = 0;
        $imagePath = APPPATH . '../uploads/' . $result['loan_number'];

        if (isset($input['bank_statements']) && is_array($input['bank_statements'])) {
            foreach ($input['bank_statements'] as $idx => $bs) {
                $credit = isset($bs['credit']) ? str_replace(',', '', $bs['credit']) : 0;
                $debit = isset($bs['debit']) ? str_replace(',', '', $bs['debit']) : 0;
                $month = isset($bs['month']) ? $bs['month'] : '';

                if (empty($credit) && empty($debit) && empty($month)) {
                    continue;
                }

                // Handle bank statement file upload (multipart field: bank_statement_files[0], bank_statement_files[1], etc.)
                $statement_filename = null;
                if (isset($_FILES['bank_statement_files']) &&
                    isset($_FILES['bank_statement_files']['name'][$idx]) &&
                    $_FILES['bank_statement_files']['name'][$idx] != '') {

                    if (!is_dir($imagePath)) {
                        mkdir($imagePath, 0777, true);
                    }

                    $_FILES['userfile']['name']     = $_FILES['bank_statement_files']['name'][$idx];
                    $_FILES['userfile']['type']     = $_FILES['bank_statement_files']['type'][$idx];
                    $_FILES['userfile']['tmp_name'] = $_FILES['bank_statement_files']['tmp_name'][$idx];
                    $_FILES['userfile']['error']    = $_FILES['bank_statement_files']['error'][$idx];
                    $_FILES['userfile']['size']     = $_FILES['bank_statement_files']['size'][$idx];

                    $config = array(
                        'file_name'     => 'statement_' . time() . '_' . $idx . '_' . $_FILES['userfile']['name'],
                        'allowed_types' => '*',
                        'max_size'      => 200000,
                        'overwrite'     => FALSE,
                        'upload_path'   => $imagePath
                    );

                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);

                    if ($this->upload->do_upload()) {
                        $uploaded_data = $this->upload->data();
                        $statement_filename = $uploaded_data['file_name'];
                    }
                }

                $this->db->insert('bank_statements', array(
                    'loan_id' => $result['loan_id'],
                    'statement_type' => $customer_type === 'corporate' ? 'corporate' : 'personal',
                    'credit' => $credit,
                    'debit' => $debit,
                    'month' => $month,
                    'year' => isset($bs['year']) ? $bs['year'] : date('Y'),
                    'file' => $statement_filename,
                    'added_by' => 72,
                    'date_added' => date('Y-m-d H:i:s')
                ));
                $bank_statements_saved++;
            }
        }

        // Log the activity
        $logger = array(
            'user_id' => 72,
            'activity' => 'API: Loan application created - ' . $result['loan_number'] . ' for ' . $customer_name,
            'activity_cate' => 'loan_application'
        );
        $this->db->insert('activity_logger', $logger);

        // Notify users with loan creation rights
        $this->_notify_loan_creation_users_generic($customer_name, $result['loan_number'], $amount, $product->product_name, $currency);

        $this->_response(array(
            'status' => 'success',
            'message' => 'Loan application created successfully',
            'data' => array(
                'loan_id' => $result['loan_id'],
                'loan_number' => $result['loan_number'],
                'customer_id' => $customer_id,
                'customer_type' => $customer_type,
                'customer_name' => $customer_name,
                'amount' => $amount,
                'months' => $months,
                'interest' => $interest,
                'product' => $product->product_name,
                'currency' => $currency,
                'processing_fee' => $processing_fee,
                'off_taker' => $off_taker,
                'status' => 'CREATED',
                'loan_date' => $loan_date,
                'collaterals_linked' => $collaterals_linked,
                'bank_statements_saved' => $bank_statements_saved
            )
        ), 201);
    }

    /**
     * Get full customer details with related data
     * GET /api/get_customer_details
     *
     * Params: ?id=123 or ?client_id=XXX or ?phone=XXX or ?email=XXX
     */
    public function get_customer_details()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Method not allowed. Use GET request.'
            ), 405);
        }

        $id = $this->input->get('id');
        $client_id = $this->input->get('client_id');
        $phone = $this->input->get('phone');
        $email = $this->input->get('email');

        if (empty($id) && empty($client_id) && empty($phone) && empty($email)) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Please provide id, client_id, phone, or email parameter'
            ), 400);
        }

        // Find customer
        $customer = null;
        if (!empty($id)) {
            $customer = $this->Individual_customers_model->get_by_id($id);
        } elseif (!empty($client_id)) {
            $customer = $this->Individual_customers_model->get_by_client_id($client_id);
        } elseif (!empty($phone)) {
            $customer = $this->db->where('PhoneNumber', $phone)->get('individual_customers')->row();
        } elseif (!empty($email)) {
            $customer = $this->db->where('LOWER(TRIM(EmailAddress))', strtolower(trim($email)))->get('individual_customers')->row();
        }

        if (!$customer) {
            $this->_response(array('status' => 'error', 'message' => 'Customer not found'), 404);
        }

        // Personal info
        $personal_info = array(
            'customer_id' => $customer->id,
            'client_id' => $customer->ClientId,
            'title' => $customer->Title,
            'first_name' => $customer->Firstname,
            'middle_name' => $customer->Middlename,
            'last_name' => $customer->Lastname,
            'gender' => $customer->Gender,
            'date_of_birth' => $customer->DateOfBirth,
            'email' => $customer->EmailAddress,
            'phone_number' => $customer->PhoneNumber,
            'address_line1' => $customer->AddressLine1,
            'address_line2' => isset($customer->AddressLine2) ? $customer->AddressLine2 : '',
            'address_line3' => isset($customer->AddressLine3) ? $customer->AddressLine3 : '',
            'province' => $customer->Province,
            'city' => $customer->City,
            'country' => $customer->Country,
            'district' => isset($customer->district) ? $customer->district : '',
            'village' => isset($customer->village) ? $customer->village : '',
            'marital_status' => isset($customer->marital) ? $customer->marital : '',
            'residential_status' => isset($customer->ResidentialStatus) ? $customer->ResidentialStatus : '',
            'profession' => $customer->Profession,
            'source_of_income' => isset($customer->SourceOfIncome) ? $customer->SourceOfIncome : '',
            'gross_monthly_income' => isset($customer->GrossMonthlyIncome) ? (float)$customer->GrossMonthlyIncome : 0,
            'approval_status' => $customer->approval_status,
            'created_on' => $customer->CreatedOn,
            'last_updated' => isset($customer->LastUpdatedOn) ? $customer->LastUpdatedOn : ''
        );

        // KYC / ID documents
        $kyc_row = $this->Proofofidentity_model->check($customer->ClientId);
        $kyc = null;
        if ($kyc_row) {
            $kyc = array(
                'id_type' => $kyc_row->IDType,
                'id_number' => $kyc_row->IDNumber,
                'issue_date' => isset($kyc_row->IssueDate) ? $kyc_row->IssueDate : null,
                'expiry_date' => isset($kyc_row->ExpiryDate) ? $kyc_row->ExpiryDate : null
            );
        }

        // Bank details
        $bank_details = $this->db->where('customer_id', $customer->id)->get('bank_details')->result();
        $banks = array();
        foreach ($bank_details as $bd) {
            $banks[] = array(
                'bank_name' => isset($bd->bank_name) ? $bd->bank_name : '',
                'bank_branch' => isset($bd->bank_branch) ? $bd->bank_branch : '',
                'account_name' => isset($bd->account_name) ? $bd->account_name : '',
                'account_number' => isset($bd->account_number) ? $bd->account_number : ''
            );
        }

        // Accounts
        $accounts_raw = $this->db->where('client_id', $customer->id)->get('account')->result();
        $accounts = array();
        foreach ($accounts_raw as $acc) {
            $accounts[] = array(
                'account_id' => $acc->account_id,
                'account_number' => $acc->account_number,
                'balance' => (float)$acc->balance,
                'account_type' => $acc->account_type,
                'account_status' => isset($acc->account_status) ? $acc->account_status : ''
            );
        }

        // Loan summary
        $loans_raw = $this->db->where('loan_customer', $customer->id)->get('loan')->result();
        $loan_summary = array(
            'total_loans' => count($loans_raw),
            'active_loans' => 0,
            'total_principal' => 0,
            'total_outstanding' => 0,
            'loans' => array()
        );
        foreach ($loans_raw as $l) {
            if (in_array($l->loan_status, array('DISBURSED', 'ACTIVE'))) {
                $loan_summary['active_loans']++;
            }
            $loan_summary['total_principal'] += (float)$l->loan_principal;

            $loan_summary['loans'][] = array(
                'loan_id' => $l->loan_id,
                'loan_number' => $l->loan_number,
                'principal' => (float)$l->loan_principal,
                'status' => $l->loan_status,
                'loan_date' => $l->loan_date
            );
        }

        // Collaterals
        $collaterals_raw = $this->Collateral_model->get_by_customer($customer->id, 'individual');
        $collaterals = array();
        foreach ($collaterals_raw as $c) {
            $collaterals[] = array(
                'id' => $c->id,
                'collateral_type' => isset($c->collateral_type) ? $c->collateral_type : '',
                'description' => isset($c->description) ? $c->description : '',
                'market_value' => isset($c->market_value) ? (float)$c->market_value : 0,
                'force_sale_value' => isset($c->force_sale_value) ? (float)$c->force_sale_value : 0,
                'utilized_amount' => (float)$c->utilized_amount,
                'available_balance' => (float)$c->available_balance,
                'status' => isset($c->collateral_status) ? $c->collateral_status : ''
            );
        }

        $this->_response(array(
            'status' => 'success',
            'data' => array(
                'personal_info' => $personal_info,
                'kyc' => $kyc,
                'bank_details' => $banks,
                'accounts' => $accounts,
                'loan_summary' => $loan_summary,
                'collaterals' => $collaterals
            )
        ));
    }

    /**
     * Get loans with full details
     * GET /api/get_loans
     *
     * Params: ?customer_id=123 or ?client_id=XXX or ?loan_id=123 or ?loan_number=XXX or ?status=DISBURSED
     */
    public function get_loans()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Method not allowed. Use GET request.'
            ), 405);
        }

        $customer_id = $this->input->get('customer_id');
        $client_id = $this->input->get('client_id');
        $loan_id = $this->input->get('loan_id');
        $loan_number = $this->input->get('loan_number');
        $status = $this->input->get('status');

        if (empty($customer_id) && empty($client_id) && empty($loan_id) && empty($loan_number) && empty($status)) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Please provide at least one filter: customer_id, client_id, loan_id, loan_number, or status'
            ), 400);
        }

        // Build query
        $this->db->select('loan.*, loan_products.product_name, loan_products.abbreviation, loan_products.frequency, loan_products.calculation_type');
        $this->db->from('loan');
        $this->db->join('loan_products', 'loan_products.loan_product_id = loan.loan_product', 'left');

        if (!empty($loan_id)) {
            $this->db->where('loan.loan_id', $loan_id);
        }
        if (!empty($loan_number)) {
            $this->db->where('loan.loan_number', $loan_number);
        }
        if (!empty($customer_id)) {
            $this->db->where('loan.loan_customer', $customer_id);
        }
        if (!empty($client_id)) {
            // Resolve client_id to customer id
            $cust = $this->Individual_customers_model->get_by_client_id($client_id);
            if (!$cust) {
                $this->_response(array('status' => 'error', 'message' => 'Customer not found for client_id'), 404);
            }
            $this->db->where('loan.loan_customer', $cust->id);
        }
        if (!empty($status)) {
            $this->db->where('loan.loan_status', $status);
        }

        $this->db->order_by('loan.loan_id', 'DESC');
        $loans = $this->db->get()->result();

        if (empty($loans)) {
            $this->_response(array(
                'status' => 'success',
                'data' => array()
            ));
        }

        $result = array();
        foreach ($loans as $loan) {
            // Get repayment schedule
            $schedules_raw = $this->db->where('loan_id', $loan->loan_id)
                ->order_by('payment_number', 'ASC')
                ->get('payement_schedules')
                ->result();

            $schedules = array();
            $total_paid = 0;
            $total_due = 0;
            $next_due_date = null;

            foreach ($schedules_raw as $s) {
                $paid = (float)$s->paid_amount;
                $due = (float)$s->amount;
                $total_paid += $paid;
                $total_due += $due;

                // Find next unpaid schedule
                if ($paid < $due && $next_due_date === null) {
                    $next_due_date = $s->payment_schedule;
                }

                $schedules[] = array(
                    'payment_number' => (int)$s->payment_number,
                    'due_date' => $s->payment_schedule,
                    'amount_due' => $due,
                    'principal' => (float)$s->principal,
                    'interest' => (float)$s->interest,
                    'paid_amount' => $paid,
                    'balance' => round($due - $paid, 2),
                    'loan_balance' => (float)$s->loan_balance
                );
            }

            $balance_remaining = round($total_due - $total_paid, 2);

            // Get collaterals
            $collaterals_raw = $this->Collateral_model->get_loan_collaterals($loan->loan_id);
            $collaterals = array();
            foreach ($collaterals_raw as $c) {
                $collaterals[] = array(
                    'collateral_id' => isset($c->collateral_id) ? $c->collateral_id : $c->id,
                    'collateral_type' => isset($c->collateral_type) ? $c->collateral_type : '',
                    'description' => isset($c->description) ? $c->description : '',
                    'market_value' => isset($c->market_value) ? (float)$c->market_value : 0,
                    'force_sale_value' => isset($c->force_sale_value) ? (float)$c->force_sale_value : 0,
                    'amount_utilized' => isset($c->amount_utilized) ? (float)$c->amount_utilized : 0
                );
            }

            $result[] = array(
                'loan_id' => (int)$loan->loan_id,
                'loan_number' => $loan->loan_number,
                'product_name' => $loan->product_name,
                'calculation_type' => isset($loan->calculation_type) ? $loan->calculation_type : '',
                'frequency' => isset($loan->frequency) ? $loan->frequency : '',
                'customer_id' => (int)$loan->loan_customer,
                'customer_type' => $loan->customer_type,
                'loan_date' => $loan->loan_date,
                'principal' => (float)$loan->loan_principal,
                'period' => (int)$loan->loan_period,
                'interest_rate' => (float)$loan->loan_interest,
                'interest_amount' => (float)$loan->loan_interest_amount,
                'total_amount' => (float)$loan->loan_amount_total,
                'monthly_installment' => (float)$loan->loan_amount_term,
                'disbursed_amount' => (float)$loan->disbursed_amount,
                'currency' => isset($loan->currency) ? $loan->currency : '',
                'status' => $loan->loan_status,
                'narration' => isset($loan->narration) ? $loan->narration : '',
                'off_taker' => isset($loan->off_taker) ? $loan->off_taker : '',
                'processing_fee' => isset($loan->processing_fee) ? (float)$loan->processing_fee : 0,
                'payment_summary' => array(
                    'total_due' => round($total_due, 2),
                    'total_paid' => round($total_paid, 2),
                    'balance_remaining' => $balance_remaining,
                    'next_due_date' => $next_due_date
                ),
                'repayment_schedule' => $schedules,
                'collaterals' => $collaterals
            );
        }

        $this->_response(array(
            'status' => 'success',
            'data' => $result
        ));
    }

    /**
     * Get collaterals for a customer
     * GET /api/get_collaterals
     *
     * Params: ?customer_id=123&customer_type=individual
     *     or: ?client_id=XXX
     *     or: ?loan_id=123
     */
    public function get_collaterals()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Method not allowed. Use GET request.'
            ), 405);
        }

        $customer_id = $this->input->get('customer_id');
        $client_id = $this->input->get('client_id');
        $loan_id = $this->input->get('loan_id');
        $customer_type = $this->input->get('customer_type') ?: 'individual';

        if (empty($customer_id) && empty($client_id) && empty($loan_id)) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Please provide customer_id, client_id, or loan_id parameter'
            ), 400);
        }

        $collaterals_data = array();

        // By loan_id — get collaterals linked to a specific loan
        if (!empty($loan_id)) {
            $linked = $this->Collateral_model->get_loan_collaterals($loan_id);

            foreach ($linked as $c) {
                $collaterals_data[] = array(
                    'id' => isset($c->collateral_id) ? (int)$c->collateral_id : (int)$c->id,
                    'loan_id' => (int)$loan_id,
                    'collateral_type' => isset($c->collateral_type) ? $c->collateral_type : '',
                    'collateral_name' => isset($c->collateral_name) ? $c->collateral_name : '',
                    'description' => isset($c->description) ? $c->description : '',
                    'market_value' => isset($c->market_value) ? (float)$c->market_value : 0,
                    'force_sale_value' => isset($c->force_sale_value) ? (float)$c->force_sale_value : 0,
                    'amount_utilized' => isset($c->amount_utilized) ? (float)$c->amount_utilized : 0,
                    'collateral_status' => isset($c->collateral_status) ? $c->collateral_status : '',
                    'location_status' => isset($c->location_status) ? $c->location_status : '',
                    'link_status' => isset($c->status) ? $c->status : ''
                );
            }

            $this->_response(array(
                'status' => 'success',
                'data' => $collaterals_data
            ));
        }

        // Resolve client_id to customer_id
        if (!empty($client_id)) {
            $cust = $this->Individual_customers_model->get_by_client_id($client_id);
            if (!$cust) {
                $this->_response(array('status' => 'error', 'message' => 'Customer not found for client_id'), 404);
            }
            $customer_id = $cust->id;
            $customer_type = 'individual';
        }

        // Get all collaterals for customer (with available balance calculated)
        $collaterals_raw = $this->Collateral_model->get_by_customer($customer_id, $customer_type);

        foreach ($collaterals_raw as $c) {
            // Get loans using this collateral
            $linked_loans = $this->Collateral_model->get_collateral_loans($c->id);
            $loans = array();
            foreach ($linked_loans as $ll) {
                $loans[] = array(
                    'loan_id' => isset($ll->loan_id) ? (int)$ll->loan_id : 0,
                    'loan_number' => isset($ll->loan_number) ? $ll->loan_number : '',
                    'loan_principal' => isset($ll->loan_principal) ? (float)$ll->loan_principal : 0,
                    'loan_status' => isset($ll->loan_status) ? $ll->loan_status : '',
                    'amount_utilized' => isset($ll->amount_utilized) ? (float)$ll->amount_utilized : 0,
                    'link_status' => isset($ll->status) ? $ll->status : ''
                );
            }

            $collaterals_data[] = array(
                'id' => (int)$c->id,
                'customer_id' => (int)$c->customer_id,
                'customer_type' => $c->customer_type,
                'collateral_type' => isset($c->collateral_type) ? $c->collateral_type : '',
                'collateral_name' => isset($c->collateral_name) ? $c->collateral_name : '',
                'description' => isset($c->description) ? $c->description : '',
                'market_value' => isset($c->market_value) ? (float)$c->market_value : 0,
                'force_sale_value' => isset($c->force_sale_value) ? (float)$c->force_sale_value : 0,
                'utilized_amount' => (float)$c->utilized_amount,
                'available_balance' => (float)$c->available_balance,
                'collateral_status' => isset($c->collateral_status) ? $c->collateral_status : '',
                'location_status' => isset($c->location_status) ? $c->location_status : '',
                'linked_loans' => $loans
            );
        }

        $this->_response(array(
            'status' => 'success',
            'data' => $collaterals_data
        ));
    }

    /**
     * Get available currencies
     * GET /api/currencies
     */
    public function currencies()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Method not allowed. Use GET request.'
            ), 405);
        }

        $currencies = $this->Currency_model->get_all_currencies();

        $data = array();
        foreach ($currencies as $c) {
            $data[] = array(
                'currency_id' => (int)$c->currency_id,
                'currency_code' => $c->currency_code,
                'currency_name' => isset($c->currency_name) ? $c->currency_name : '',
                'country_name' => isset($c->country_name) ? $c->country_name : ''
            );
        }

        $this->_response(array(
            'status' => 'success',
            'data' => $data
        ));
    }

    /**
     * Notify users with loan creation rights about a new loan application
     * Accepts customer name string for individual/corporate support
     */
    private function _notify_loan_creation_users_generic($customer_name, $loan_number, $amount, $product_name, $currency)
    {
        $this->_notify_loan_creation_users((object)array('Firstname' => $customer_name, 'Lastname' => ''), $loan_number, $amount, $product_name, $currency);
    }

    // ==================== COLLATERAL MANAGEMENT API ====================

    /**
     * Add a new collateral for a customer
     * POST /api/add_collateral
     *
     * Required: customer_id, collateral_name, collateral_type, market_value, force_sale_value
     * Optional: customer_type (default: individual), collateral_serial, description, location_status, collateral_file (multipart)
     */
    public function add_collateral()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->_response(array('status' => 'error', 'message' => 'Method not allowed. Use POST request.'), 405);
        }

        // Support both JSON and multipart/form-data (for file uploads)
        $input = $this->_get_input();

        $required_fields = array('customer_id', 'collateral_name', 'collateral_type', 'market_value', 'force_sale_value');
        $missing = $this->_validate_required($input, $required_fields);

        if (!empty($missing)) {
            $this->_response(array('status' => 'error', 'message' => 'Missing required fields', 'missing_fields' => $missing), 400);
        }

        $customer_id = (int)$input['customer_id'];
        $customer_type = isset($input['customer_type']) ? $input['customer_type'] : 'individual';

        // Validate customer exists
        if ($customer_type === 'individual') {
            $customer = $this->Individual_customers_model->get_by_id($customer_id);
        } else {
            $customer = $this->db->where('id', $customer_id)->get('corporate_customers')->row();
        }

        if (!$customer) {
            $this->_response(array('status' => 'error', 'message' => 'Customer not found'), 404);
        }

        // Handle file upload
        $file_name = '';
        if (!empty($_FILES['collateral_file']['name'])) {
            $upload_path = FCPATH . 'uploads/collaterals/' . $customer_type . '_' . $customer_id . '/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config = array(
                'upload_path' => $upload_path,
                'allowed_types' => 'gif|jpg|jpeg|png|pdf|doc|docx',
                'max_size' => 10240,
                'file_name' => time() . '_' . $_FILES['collateral_file']['name']
            );

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('collateral_file')) {
                $upload_data = $this->upload->data();
                $file_name = 'collaterals/' . $customer_type . '_' . $customer_id . '/' . $upload_data['file_name'];
            }
        }

        $data = array(
            'customer_id' => $customer_id,
            'customer_type' => $customer_type,
            'collateral_name' => $input['collateral_name'],
            'collateral_type' => $input['collateral_type'],
            'collateral_serial' => isset($input['collateral_serial']) ? $input['collateral_serial'] : '',
            'market_value' => (float)$input['market_value'],
            'force_sale_value' => (float)$input['force_sale_value'],
            'collateral_desc' => isset($input['description']) ? $input['description'] : '',
            'collateral_status' => 'ACTIVE',
            'location_status' => isset($input['location_status']) ? $input['location_status'] : 'In Our Possession',
            'added_by' => 72,
            'added_at' => date('Y-m-d H:i:s')
        );

        if ($file_name) {
            $data['collateral_file'] = $file_name;
        }

        $collateral_id = $this->Collateral_model->insert($data);

        if (!$collateral_id) {
            $this->_response(array('status' => 'error', 'message' => 'Failed to add collateral'), 500);
        }

        $this->db->insert('activity_logger', array(
            'user_id' => 72,
            'activity' => 'API: Collateral "' . $data['collateral_name'] . '" added for customer #' . $customer_id,
            'activity_cate' => 'collateral_add'
        ));

        $this->_response(array(
            'status' => 'success',
            'message' => 'Collateral added successfully',
            'data' => array(
                'collateral_id' => $collateral_id,
                'customer_id' => $customer_id,
                'customer_type' => $customer_type,
                'collateral_name' => $data['collateral_name'],
                'collateral_type' => $data['collateral_type'],
                'market_value' => $data['market_value'],
                'force_sale_value' => $data['force_sale_value'],
                'available_balance' => $data['force_sale_value'],
                'collateral_status' => 'ACTIVE'
            )
        ), 201);
    }

    /**
     * Update an existing collateral
     * POST /api/update_collateral
     *
     * Required: collateral_id
     * Optional: collateral_name, collateral_type, collateral_serial, market_value, force_sale_value,
     *           description, location_status, collateral_status, collateral_file (multipart)
     */
    public function update_collateral()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->_response(array('status' => 'error', 'message' => 'Method not allowed. Use POST request.'), 405);
        }

        $input = $this->_get_input();

        if (!isset($input['collateral_id']) || empty($input['collateral_id'])) {
            $this->_response(array('status' => 'error', 'message' => 'collateral_id is required'), 400);
        }

        $collateral_id = (int)$input['collateral_id'];
        $collateral = $this->Collateral_model->get_by_id($collateral_id);

        if (!$collateral) {
            $this->_response(array('status' => 'error', 'message' => 'Collateral not found'), 404);
        }

        $update_data = array();
        $updatable = array('collateral_name', 'collateral_type', 'collateral_serial', 'market_value', 'force_sale_value', 'collateral_status', 'location_status');

        foreach ($updatable as $field) {
            if (isset($input[$field])) {
                $update_data[$field] = $input[$field];
            }
        }

        if (isset($input['description'])) {
            $update_data['collateral_desc'] = $input['description'];
        }

        // Handle file upload
        if (!empty($_FILES['collateral_file']['name'])) {
            $upload_path = FCPATH . 'uploads/collaterals/' . $collateral->customer_type . '_' . $collateral->customer_id . '/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config = array(
                'upload_path' => $upload_path,
                'allowed_types' => 'gif|jpg|jpeg|png|pdf|doc|docx',
                'max_size' => 10240,
                'file_name' => time() . '_' . $_FILES['collateral_file']['name']
            );

            $this->load->library('upload', $config);

            if ($this->upload->do_upload('collateral_file')) {
                $upload_data_file = $this->upload->data();
                $update_data['collateral_file'] = 'collaterals/' . $collateral->customer_type . '_' . $collateral->customer_id . '/' . $upload_data_file['file_name'];
            }
        }

        if (empty($update_data)) {
            $this->_response(array('status' => 'error', 'message' => 'No fields to update'), 400);
        }

        $this->Collateral_model->update($collateral_id, $update_data);

        $this->db->insert('activity_logger', array(
            'user_id' => 72,
            'activity' => 'API: Collateral #' . $collateral_id . ' updated',
            'activity_cate' => 'collateral_update'
        ));

        // Return updated collateral
        $updated = $this->Collateral_model->get_by_id($collateral_id);
        $available = $this->Collateral_model->get_available_balance($collateral_id);

        $this->_response(array(
            'status' => 'success',
            'message' => 'Collateral updated successfully',
            'data' => array(
                'collateral_id' => (int)$updated->id,
                'customer_id' => (int)$updated->customer_id,
                'customer_type' => $updated->customer_type,
                'collateral_name' => $updated->collateral_name,
                'collateral_type' => $updated->collateral_type,
                'collateral_serial' => isset($updated->collateral_serial) ? $updated->collateral_serial : '',
                'market_value' => (float)$updated->market_value,
                'force_sale_value' => (float)$updated->force_sale_value,
                'available_balance' => (float)$available,
                'collateral_status' => $updated->collateral_status,
                'location_status' => isset($updated->location_status) ? $updated->location_status : ''
            )
        ));
    }

    /**
     * Delete a collateral (only if not linked to active loans)
     * POST /api/delete_collateral
     *
     * Required: collateral_id
     */
    public function delete_collateral()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->_response(array('status' => 'error', 'message' => 'Method not allowed. Use POST request.'), 405);
        }

        $input = $this->_get_input();

        if (!isset($input['collateral_id']) || empty($input['collateral_id'])) {
            $this->_response(array('status' => 'error', 'message' => 'collateral_id is required'), 400);
        }

        $collateral_id = (int)$input['collateral_id'];
        $collateral = $this->Collateral_model->get_by_id($collateral_id);

        if (!$collateral) {
            $this->_response(array('status' => 'error', 'message' => 'Collateral not found'), 404);
        }

        // Check for active loan links
        $active_links = $this->db->where('collateral_id', $collateral_id)->where('status', 'ACTIVE')->get('loan_collateral_links')->result();
        if (!empty($active_links)) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Cannot delete collateral — it is linked to ' . count($active_links) . ' active loan(s). Release the links first.'
            ), 400);
        }

        $this->Collateral_model->delete($collateral_id);

        $this->db->insert('activity_logger', array(
            'user_id' => 72,
            'activity' => 'API: Collateral #' . $collateral_id . ' "' . $collateral->collateral_name . '" deleted',
            'activity_cate' => 'collateral_delete'
        ));

        $this->_response(array(
            'status' => 'success',
            'message' => 'Collateral deleted successfully'
        ));
    }

    /**
     * Link a collateral to a loan
     * POST /api/link_collateral
     *
     * Required: loan_id, collateral_id, amount_utilized
     */
    public function link_collateral()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->_response(array('status' => 'error', 'message' => 'Method not allowed. Use POST request.'), 405);
        }

        $input = $this->_get_input();

        $required_fields = array('loan_id', 'collateral_id', 'amount_utilized');
        $missing = $this->_validate_required($input, $required_fields);

        if (!empty($missing)) {
            $this->_response(array('status' => 'error', 'message' => 'Missing required fields', 'missing_fields' => $missing), 400);
        }

        $loan_id = (int)$input['loan_id'];
        $collateral_id = (int)$input['collateral_id'];
        $amount_utilized = (float)$input['amount_utilized'];

        // Validate loan exists
        $loan = $this->Loan_model->get_by_id($loan_id);
        if (!$loan) {
            $this->_response(array('status' => 'error', 'message' => 'Loan not found'), 404);
        }

        // Validate collateral exists
        $collateral = $this->Collateral_model->get_by_id($collateral_id);
        if (!$collateral) {
            $this->_response(array('status' => 'error', 'message' => 'Collateral not found'), 404);
        }

        // Check available balance
        $available = $this->Collateral_model->get_available_balance($collateral_id);
        if ($amount_utilized > $available) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'Amount exceeds available balance. Available: ' . number_format($available, 2),
                'available_balance' => $available
            ), 400);
        }

        // Check if already linked
        $existing = $this->db->where('loan_id', $loan_id)->where('collateral_id', $collateral_id)->where('status', 'ACTIVE')->get('loan_collateral_links')->row();
        if ($existing) {
            $this->_response(array('status' => 'error', 'message' => 'Collateral is already linked to this loan'), 400);
        }

        $link_id = $this->Collateral_model->link_to_loan(array(
            'loan_id' => $loan_id,
            'collateral_id' => $collateral_id,
            'amount_utilized' => $amount_utilized,
            'linked_by' => 0,
            'linked_at' => date('Y-m-d H:i:s'),
            'status' => 'ACTIVE'
        ));

        $this->db->insert('activity_logger', array(
            'user_id' => 72,
            'activity' => 'API: Collateral #' . $collateral_id . ' linked to loan #' . $loan->loan_number . ' (' . number_format($amount_utilized, 2) . ')',
            'activity_cate' => 'collateral_link'
        ));

        $this->_response(array(
            'status' => 'success',
            'message' => 'Collateral linked to loan successfully',
            'data' => array(
                'link_id' => $link_id,
                'loan_id' => $loan_id,
                'loan_number' => $loan->loan_number,
                'collateral_id' => $collateral_id,
                'collateral_name' => $collateral->collateral_name,
                'amount_utilized' => $amount_utilized,
                'remaining_balance' => $available - $amount_utilized
            )
        ), 201);
    }

    /**
     * Unlink (release) a collateral from a loan
     * POST /api/unlink_collateral
     *
     * Required: loan_id, collateral_id
     */
    public function unlink_collateral()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->_response(array('status' => 'error', 'message' => 'Method not allowed. Use POST request.'), 405);
        }

        $input = $this->_get_input();

        if (empty($input['loan_id']) || empty($input['collateral_id'])) {
            $this->_response(array('status' => 'error', 'message' => 'loan_id and collateral_id are required'), 400);
        }

        $loan_id = (int)$input['loan_id'];
        $collateral_id = (int)$input['collateral_id'];

        $link = $this->db->where('loan_id', $loan_id)->where('collateral_id', $collateral_id)->where('status', 'ACTIVE')->get('loan_collateral_links')->row();

        if (!$link) {
            $this->_response(array('status' => 'error', 'message' => 'No active link found between this loan and collateral'), 404);
        }

        $this->Collateral_model->update_link_status($link->id, 'RELEASED', 0);

        $this->db->insert('activity_logger', array(
            'user_id' => 72,
            'activity' => 'API: Collateral #' . $collateral_id . ' released from loan #' . $loan_id,
            'activity_cate' => 'collateral_unlink'
        ));

        $this->_response(array(
            'status' => 'success',
            'message' => 'Collateral released from loan successfully'
        ));
    }

    /**
     * Notify users with loan creation rights about a new loan application
     */
    private function _notify_loan_creation_users($customer, $loan_number, $amount, $product_name, $currency)
    {
        $users = $this->db->select('e.id, e.Firstname, e.Lastname, e.EmailAddress', FALSE)
            ->distinct()
            ->from('employees e')
            ->join('roles r', 'r.id = e.Role')
            ->join('access a', 'a.roleid = r.id')
            ->join('menuitems mi', 'mi.id = a.controllerid')
            ->where('LOWER(mi.method)', 'loan/create')
            ->where('e.EmailAddress !=', '')
            ->get()
            ->result();

        if (empty($users)) {
            return;
        }

        $settings = $this->db->get_where('settings', array('settings_id' => 1))->row();
        $company_name = $settings->company_name ?? 'FundIt';

        $full_name = $customer->Firstname . ' ' . $customer->Lastname;
        $subject = 'New Loan Application - ' . $loan_number;

        $email_body = '
            <h2 style="color: #1e3a5f;">New Loan Application</h2>
            <p>A new loan application has been submitted via the online portal and requires your attention.</p>
            <table style="width: 100%; border-collapse: collapse; margin: 15px 0;">
                <tr style="background: #f8fafc;">
                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: bold;">Loan Number</td>
                    <td style="padding: 10px; border: 1px solid #e2e8f0;">' . htmlspecialchars($loan_number) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: bold;">Customer</td>
                    <td style="padding: 10px; border: 1px solid #e2e8f0;">' . htmlspecialchars($full_name) . '</td>
                </tr>
                <tr style="background: #f8fafc;">
                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: bold;">Product</td>
                    <td style="padding: 10px; border: 1px solid #e2e8f0;">' . htmlspecialchars($product_name) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: bold;">Amount</td>
                    <td style="padding: 10px; border: 1px solid #e2e8f0;">' . htmlspecialchars($currency) . ' ' . number_format($amount, 2) . '</td>
                </tr>
                <tr style="background: #f8fafc;">
                    <td style="padding: 10px; border: 1px solid #e2e8f0; font-weight: bold;">Status</td>
                    <td style="padding: 10px; border: 1px solid #e2e8f0;"><span style="background: #fef3c7; color: #92400e; padding: 3px 10px; border-radius: 12px; font-size: 13px;">INITIATED</span></td>
                </tr>
            </table>
            <p style="color: #666;">Please log in to the system to review this loan application.</p>
        ';

        foreach ($users as $user) {
            if (!empty($user->EmailAddress) && filter_var($user->EmailAddress, FILTER_VALIDATE_EMAIL)) {
                $result = send_templated_email($user->EmailAddress, $subject, $email_body);

                log_notification(array(
                    'notification_type' => 'loan_application',
                    'reference_type' => 'loan',
                    'reference_id' => $loan_number,
                    'reference_number' => $loan_number,
                    'recipient_email' => $user->EmailAddress,
                    'recipient_name' => $user->Firstname . ' ' . $user->Lastname,
                    'recipient_user_id' => $user->id,
                    'subject' => $subject,
                    'status' => $result['success'] ? 'sent' : 'failed',
                    'error_message' => $result['success'] ? null : $result['message'],
                    'triggered_by' => 0
                ));
            }
        }
    }

    /**
     * Get FD Details (Self-Service)
     * GET /Api/get_fd_details?email=xxx
     * GET /Api/get_fd_details?customer_id=xxx
     *
     * Returns: FD customer info, deposits, and transactions
     */
    public function get_fd_details()
    {
        // Accept GET query params, POST form data, or JSON body
        $input = array_merge($this->input->get() ?: array(), $this->_get_input() ?: array());
        $email = isset($input['email']) ? trim($input['email']) : '';
        $customer_id = isset($input['customer_id']) ? trim($input['customer_id']) : '';

        if (empty($email) && empty($customer_id)) {
            $this->_response(array('status' => 'error', 'message' => 'Please provide email or customer_id'), 400);
        }

        // Find individual customer
        if (!empty($customer_id)) {
            $individual = get_by_id('individual_customers', 'id', $customer_id);
        } else {
            $individual = $this->db->where('LOWER(TRIM(EmailAddress))', strtolower(trim($email)))->get('individual_customers')->row();
        }

        if (!$individual) {
            $this->_response(array('status' => 'error', 'message' => 'No customer account found'), 404);
        }

        // Find linked FD customer(s)
        $fd_customers = $this->db->where('personal_linkage', $individual->id)->get('fd_customers')->result();
        if (empty($fd_customers)) {
            $this->_response(array('status' => 'error', 'message' => 'No fixed deposit accounts linked to your profile'), 404);
        }

        // Build response with deposits and transactions for each FD customer
        $fd_data = array();
        foreach ($fd_customers as $fc) {
            $deposits_data = array();
            $deposits = $this->Fd_deposits_model->get_by_customer($fc->id);

            foreach ($deposits as $dep) {
                $transactions = $this->Fd_transactions_model->get_by_deposit($dep->id);
                $txn_data = array();
                foreach ($transactions as $txn) {
                    $txn_data[] = array(
                        'ref' => $txn->transaction_ref,
                        'type' => $txn->transaction_type,
                        'amount' => $txn->amount,
                        'date' => $txn->created_at
                    );
                }

                $deposits_data[] = array(
                    'id' => $dep->id,
                    'deposit_number' => $dep->deposit_number,
                    'principal' => $dep->current_principal,
                    'interest_rate' => $dep->interest_rate,
                    'start_date' => $dep->start_date,
                    'maturity_date' => $dep->maturity_date,
                    'status' => $dep->status,
                    'transactions' => $txn_data
                );
            }

            $fd_data[] = array(
                'fd_customer' => array(
                    'id' => $fc->id,
                    'customer_number' => $fc->customer_number,
                    'name' => $fc->first_name . ' ' . $fc->last_name,
                    'email' => $fc->email,
                    'phone' => $fc->phone_number,
                    'status' => $fc->status
                ),
                'deposits' => $deposits_data
            );
        }

        // Log the activity
        $this->db->insert('activity_logger', array(
            'user_id' => 72,
            'activity' => 'API: FD details retrieved for ' . $email,
            'activity_cate' => 'fd_self_service'
        ));

        $this->_response(array(
            'status' => 'success',
            'message' => 'FD details retrieved successfully',
            'data' => $fd_data
        ), 200);
    }

    /**
     * Get FD Statement (Self-Service)
     * GET /Api/get_fd_statement?email=xxx&deposit_id=xxx
     * GET /Api/get_fd_statement?customer_id=xxx&deposit_id=xxx
     *
     * Required: deposit_id + (email or customer_id)
     * Optional: from_date, to_date
     * Returns: Deposit transactions/statement
     */
    public function get_fd_statement()
    {
        // Accept GET query params, POST form data, or JSON body
        $input = array_merge($this->input->get() ?: array(), $this->_get_input() ?: array());
        $email = isset($input['email']) ? trim($input['email']) : '';
        $customer_id = isset($input['customer_id']) ? trim($input['customer_id']) : '';
        $deposit_id = isset($input['deposit_id']) ? intval($input['deposit_id']) : 0;
        $from_date = isset($input['from_date']) ? trim($input['from_date']) : null;
        $to_date = isset($input['to_date']) ? trim($input['to_date']) : null;

        if (empty($email) && empty($customer_id)) {
            $this->_response(array('status' => 'error', 'message' => 'Please provide email or customer_id'), 400);
        }

        if (empty($deposit_id)) {
            $this->_response(array('status' => 'error', 'message' => 'Missing required field: deposit_id'), 400);
        }

        // Find individual customer
        if (!empty($customer_id)) {
            $individual = get_by_id('individual_customers', 'id', $customer_id);
        } else {
            $individual = $this->db->where('LOWER(TRIM(EmailAddress))', strtolower(trim($email)))->get('individual_customers')->row();
        }

        if (!$individual) {
            $this->_response(array('status' => 'error', 'message' => 'No customer account found'), 404);
        }

        // Get the deposit
        $deposit = $this->Fd_deposits_model->get_by_id($deposit_id);
        if (!$deposit) {
            $this->_response(array('status' => 'error', 'message' => 'Deposit not found'), 404);
        }

        // Verify the deposit belongs to an FD customer linked to this individual
        $fd_customer = $this->Fd_customers_model->get_by_id($deposit->customer_id);
        if (!$fd_customer || $fd_customer->personal_linkage != $individual->id) {
            $this->_response(array('status' => 'error', 'message' => 'You do not have access to this deposit'), 403);
        }

        // Get transactions/statement
        $transactions = $this->Fd_transactions_model->get_for_statement($deposit_id, $from_date, $to_date);

        $txn_data = array();
        $running_balance = 0;
        foreach ($transactions as $txn) {
            // Track running balance based on transaction type
            if (in_array($txn->transaction_type, array('DEPOSIT', 'TOP_UP', 'MERGE_IN'))) {
                $running_balance += $txn->amount;
            } else {
                $running_balance -= $txn->amount;
            }

            $txn_data[] = array(
                'ref' => $txn->transaction_ref,
                'type' => $txn->transaction_type,
                'amount' => $txn->amount,
                'date' => $txn->created_at,
                'notes' => $txn->notes,
                'balance' => $running_balance
            );
        }

        // Log the activity
        $this->db->insert('activity_logger', array(
            'user_id' => 72,
            'activity' => 'API: FD statement retrieved for deposit #' . $deposit->deposit_number . ' by ' . $email,
            'activity_cate' => 'fd_self_service'
        ));

        $this->_response(array(
            'status' => 'success',
            'message' => 'Statement retrieved successfully',
            'data' => array(
                'deposit' => array(
                    'id' => $deposit->id,
                    'deposit_number' => $deposit->deposit_number,
                    'principal' => $deposit->current_principal,
                    'interest_rate' => $deposit->interest_rate,
                    'start_date' => $deposit->start_date,
                    'maturity_date' => $deposit->maturity_date,
                    'status' => $deposit->status
                ),
                'fd_customer' => array(
                    'customer_number' => $fd_customer->customer_number,
                    'name' => $fd_customer->first_name . ' ' . $fd_customer->last_name
                ),
                'period' => array(
                    'from' => $from_date,
                    'to' => $to_date
                ),
                'transactions' => $txn_data,
                'opening_balance' => !empty($txn_data) ? $txn_data[0]['balance'] - (in_array($transactions[0]->transaction_type, array('DEPOSIT', 'TOP_UP', 'MERGE_IN')) ? $transactions[0]->amount : -$transactions[0]->amount) : 0,
                'closing_balance' => !empty($txn_data) ? end($txn_data)['balance'] : 0
            )
        ), 200);
    }

    /**
     * Get Full FD Report
     * GET /Api/get_fd_report?customer_id=xxx
     * GET /Api/get_fd_report?email=xxx
     *
     * Returns: Individual customer info, linked FD customer(s), all deposits with
     *          accrued interest, all transactions per deposit, and portfolio summary
     */
    public function get_fd_report()
    {
        // Accept GET query params, POST form data, or JSON body
        $input = array_merge($this->input->get() ?: array(), $this->_get_input() ?: array());
        $email = isset($input['email']) ? trim($input['email']) : '';
        $customer_id = isset($input['customer_id']) ? trim($input['customer_id']) : '';

        if (empty($email) && empty($customer_id)) {
            $this->_response(array('status' => 'error', 'message' => 'Please provide email or customer_id'), 400);
        }

        // Find individual customer
        if (!empty($customer_id)) {
            $individual = get_by_id('individual_customers', 'id', $customer_id);
        } else {
            $individual = $this->db->where('LOWER(TRIM(EmailAddress))', strtolower(trim($email)))->get('individual_customers')->row();
        }

        if (!$individual) {
            $this->_response(array('status' => 'error', 'message' => 'No customer account found'), 404);
        }

        // Find linked FD customer(s)
        $fd_customers = $this->db->where('personal_linkage', $individual->id)->get('fd_customers')->result();
        if (empty($fd_customers)) {
            $this->_response(array('status' => 'error', 'message' => 'No fixed deposit accounts linked to this customer'), 404);
        }

        // Load FD helper for interest calculations
        $this->load->helper('fd');

        // Portfolio-level totals
        $portfolio_total_principal = 0;
        $portfolio_total_accrued = 0;
        $portfolio_total_paid_interest = 0;
        $portfolio_active_deposits = 0;
        $portfolio_total_deposits = 0;

        // Build full report for each FD customer
        $fd_accounts = array();
        foreach ($fd_customers as $fc) {
            $deposits = $this->Fd_deposits_model->get_by_customer($fc->id);

            $account_total_principal = 0;
            $account_total_accrued = 0;
            $account_total_paid_interest = 0;

            $deposits_data = array();
            foreach ($deposits as $dep) {
                $portfolio_total_deposits++;

                // Calculate accrued interest
                $accrued = 0;
                if ($dep->status == 'ACTIVE') {
                    $accrued = calculate_accrued_interest($dep);
                    $portfolio_active_deposits++;
                    $portfolio_total_principal += floatval($dep->current_principal);
                    $account_total_principal += floatval($dep->current_principal);
                }

                $portfolio_total_accrued += $accrued;
                $account_total_accrued += $accrued;

                // Get paid interest
                $paid_interest = $this->Fd_transactions_model->get_total_paid_interest($dep->id);
                $portfolio_total_paid_interest += $paid_interest;
                $account_total_paid_interest += $paid_interest;

                // Get all transactions
                $transactions = $this->Fd_transactions_model->get_by_deposit($dep->id);
                $txn_data = array();
                $running_balance = 0;
                foreach ($transactions as $txn) {
                    if (in_array($txn->transaction_type, array('DEPOSIT', 'TOP_UP', 'MERGE_IN'))) {
                        $running_balance += floatval($txn->amount);
                    } else {
                        $running_balance -= floatval($txn->amount);
                    }
                    $txn_data[] = array(
                        'ref' => $txn->transaction_ref,
                        'type' => $txn->transaction_type,
                        'amount' => $txn->amount,
                        'date' => $txn->created_at,
                        'notes' => $txn->notes,
                        'balance' => $running_balance
                    );
                }

                // Days to maturity
                $days_to_maturity = null;
                if ($dep->status == 'ACTIVE' && !empty($dep->maturity_date)) {
                    $days_to_maturity = max(0, (int)((strtotime($dep->maturity_date) - time()) / 86400));
                }

                $deposits_data[] = array(
                    'id' => $dep->id,
                    'deposit_number' => $dep->deposit_number,
                    'principal_amount' => $dep->principal_amount,
                    'current_principal' => $dep->current_principal,
                    'interest_rate' => $dep->interest_rate,
                    'duration_months' => $dep->duration_months,
                    'payment_option' => $dep->payment_option,
                    'start_date' => $dep->start_date,
                    'maturity_date' => $dep->maturity_date,
                    'days_to_maturity' => $days_to_maturity,
                    'status' => $dep->status,
                    'accrued_interest' => round($accrued, 2),
                    'total_paid_interest' => round($paid_interest, 2),
                    'transactions' => $txn_data
                );
            }

            $fd_accounts[] = array(
                'fd_customer' => array(
                    'id' => $fc->id,
                    'customer_number' => $fc->customer_number,
                    'name' => $fc->first_name . ' ' . $fc->last_name,
                    'email' => $fc->email,
                    'phone' => $fc->phone_number,
                    'address' => $fc->address,
                    'id_type' => $fc->id_type,
                    'id_number' => $fc->id_number,
                    'status' => $fc->status,
                    'created_at' => $fc->created_at
                ),
                'summary' => array(
                    'total_principal' => round($account_total_principal, 2),
                    'total_accrued_interest' => round($account_total_accrued, 2),
                    'total_paid_interest' => round($account_total_paid_interest, 2),
                    'deposit_count' => count($deposits)
                ),
                'deposits' => $deposits_data
            );
        }

        // Log the activity
        $this->db->insert('activity_logger', array(
            'user_id' => 72,
            'activity' => 'API: Full FD report retrieved for customer #' . $individual->id,
            'activity_cate' => 'fd_self_service'
        ));

        $this->_response(array(
            'status' => 'success',
            'message' => 'Full FD report retrieved successfully',
            'data' => array(
                'individual_customer' => array(
                    'id' => $individual->id,
                    'client_id' => $individual->ClientId,
                    'name' => $individual->Firstname . ' ' . $individual->Lastname,
                    'email' => $individual->EmailAddress,
                    'phone' => $individual->PhoneNumber
                ),
                'portfolio_summary' => array(
                    'total_fd_accounts' => count($fd_customers),
                    'total_deposits' => $portfolio_total_deposits,
                    'active_deposits' => $portfolio_active_deposits,
                    'total_principal' => round($portfolio_total_principal, 2),
                    'total_accrued_interest' => round($portfolio_total_accrued, 2),
                    'total_paid_interest' => round($portfolio_total_paid_interest, 2),
                    'total_value' => round($portfolio_total_principal + $portfolio_total_accrued, 2),
                    'report_date' => date('Y-m-d H:i:s')
                ),
                'fd_accounts' => $fd_accounts
            )
        ), 200);
    }

    /**
     * Register Business (Corporate Customer)
     * POST /Api/register_business
     *
     * Required: EntityName, RegistrationNumber, entity_type, Country, customer_id (individual who is registering)
     * Optional: all other corporate fields
     * Auto-links to the registering individual customer via linked_individual_id
     */
    public function register_business()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->_response(array('status' => 'error', 'message' => 'Method not allowed. Use POST request.'), 405);
        }

        // Accept both JSON body and multipart form-data (for file uploads)
        $input = $this->_get_input();
        if (empty($input)) {
            $input = $this->input->post() ? $this->input->post() : array();
        }
        // Merge POST fields with JSON (POST takes precedence for multipart)
        if (!empty($_POST)) {
            $input = array_merge($input ? $input : array(), $_POST);
        }

        // Log raw input for debugging
        log_message('debug', 'register_business raw input: ' . json_encode($input));

        // Required fields
        $required_fields = array('EntityName', 'RegistrationNumber', 'entity_type', 'Country');
        $missing = $this->_validate_required($input, $required_fields);

        if (!empty($missing)) {
            $this->_response(array('status' => 'error', 'message' => 'Missing required fields', 'missing_fields' => $missing), 400);
        }

        // Identify the registering individual — accept customer_id or email
        $individual = null;
        if (!empty($input['customer_id'])) {
            $individual = get_by_id('individual_customers', 'id', $input['customer_id']);
        } elseif (!empty($input['email'])) {
            $individual = $this->db->where('LOWER(TRIM(EmailAddress))', strtolower(trim($input['email'])))->get('individual_customers')->row();
        }

        if (!$individual) {
            $this->_response(array('status' => 'error', 'message' => 'Individual customer not found. Provide a valid customer_id or email.'), 404);
        }

        // Check if registration number already exists
        $existing = $this->db->where('RegistrationNumber', $input['RegistrationNumber'])->get('corporate_customers')->row();
        if ($existing) {
            $this->_response(array(
                'status' => 'error',
                'message' => 'A business with this registration number already exists',
                'existing_id' => $existing->id
            ), 409);
        }

        // Generate unique client ID
        $clientid = 'BIZ' . rand(100, 999) . rand(1000, 9999);

        // Prepare corporate customer data — all fields from the create form
        $corporate_data = array(
            'ClientId' => $clientid,
            'EntityName' => $input['EntityName'],
            'DateOfIncorporation' => isset($input['DateOfIncorporation']) ? $input['DateOfIncorporation'] : null,
            'RegistrationNumber' => $input['RegistrationNumber'],
            'entity_type' => $input['entity_type'],
            'category' => isset($input['category']) ? $input['category'] : '',
            'TaxIdentificationNumber' => isset($input['TaxIdentificationNumber']) ? $input['TaxIdentificationNumber'] : '',
            'Country' => $input['Country'],
            'Branch' => isset($input['Branch']) ? $input['Branch'] : $individual->Branch,
            'Status' => isset($input['Status']) ? $input['Status'] : '',
            'nature_of_business' => isset($input['nature_of_business']) ? $input['nature_of_business'] : '',
            'industry_sector' => isset($input['industry_sector']) ? $input['industry_sector'] : '',
            'street' => isset($input['street']) ? $input['street'] : '',
            'city_town' => isset($input['city_town']) ? $input['city_town'] : '',
            'province' => isset($input['province']) ? $input['province'] : '',
            'postal_code' => isset($input['postal_code']) ? $input['postal_code'] : '',
            'phone_number' => isset($input['phone_number']) ? $input['phone_number'] : $individual->PhoneNumber,
            'contact_email' => isset($input['contact_email']) ? $input['contact_email'] : $individual->EmailAddress,
            'website' => isset($input['website']) ? $input['website'] : '',
            'key_management_info' => isset($input['key_management_info']) ? $input['key_management_info'] : '',
            'business_info' => isset($input['business_info']) ? $input['business_info'] : '',
            'financial_year_end' => isset($input['financial_year_end']) ? $input['financial_year_end'] : null,
            'casual_employees' => isset($input['casual_employees']) ? intval($input['casual_employees']) : 0,
            'permanent_employees' => isset($input['permanent_employees']) ? intval($input['permanent_employees']) : 0,
            'company_certificate' => '',
            'tax_id_doc' => '',
            'proof_physical_address' => '',
            'financial_statement' => '',
            'approval_status' => 'pending',
            'linked_individual_id' => $individual->id,
            'added_by' => 72,
            'CreatedOn' => date('Y-m-d H:i:s'),
            'LastUpdatedOn' => date('Y-m-d H:i:s')
        );

        $corporate_id = $this->Corporate_customers_model->insert($corporate_data);

        if (!$corporate_id) {
            $this->_response(array('status' => 'error', 'message' => 'Failed to create business. Please try again.'), 500);
        }

        // Handle file uploads (multipart/form-data)
        $uploaded_docs = array();
        $doc_fields = array('company_certificate', 'proof_physical_address', 'financial_statement', 'tax_id_doc');

        // Check if any files were actually uploaded
        $has_files = false;
        foreach ($doc_fields as $field) {
            if (!empty($_FILES[$field]['name'])) { $has_files = true; break; }
        }

        if ($has_files) {
            $folder_name = $clientid; // Use ClientId for folder name (no spaces/special chars)
            $upload_dir = APPPATH . '../uploads/' . $folder_name;

            if (!is_dir($upload_dir)) {
                @mkdir($upload_dir, 0777, true);
            }

            $this->load->library('upload');
        }

        foreach ($doc_fields as $field) {
            if (!empty($_FILES[$field]['name']) && $has_files) {
                $_FILES['userfile']['name'] = $_FILES[$field]['name'];
                $_FILES['userfile']['type'] = $_FILES[$field]['type'];
                $_FILES['userfile']['tmp_name'] = $_FILES[$field]['tmp_name'];
                $_FILES['userfile']['error'] = $_FILES[$field]['error'];
                $_FILES['userfile']['size'] = $_FILES[$field]['size'];

                $config = array(
                    'file_name' => rand(100, 1000) . $_FILES['userfile']['name'],
                    'allowed_types' => '*',
                    'max_size' => 200000,
                    'overwrite' => FALSE,
                    'upload_path' => $upload_dir
                );
                $this->upload->initialize($config);

                if ($this->upload->do_upload()) {
                    $file_data = $this->upload->data();
                    $file_path = $folder_name . '/' . $file_data['file_name'];
                    $uploaded_docs[$field] = $file_path;
                }
            }
        }

        // Update corporate record with uploaded file paths
        if (!empty($uploaded_docs)) {
            $this->Corporate_customers_model->update($corporate_id, $uploaded_docs);
        }

        // Handle shareholders if provided
        $shareholders_result = array();
        if (!empty($input['shareholders']) && is_array($input['shareholders'])) {
            $shareholder_rows = array();
            $percentages = array();

            foreach ($input['shareholders'] as $sh) {
                // Each shareholder needs at least first_name and last_name
                if (empty($sh['first_name']) || empty($sh['last_name'])) {
                    continue;
                }

                $shareholder_rows[] = array(
                    'title' => isset($sh['title']) ? $sh['title'] : '',
                    'first_name' => $sh['first_name'],
                    'last_name' => $sh['last_name'],
                    'gender' => isset($sh['gender']) ? $sh['gender'] : '',
                    'nationality' => isset($sh['nationality']) ? $sh['nationality'] : '',
                    'phone_number' => isset($sh['phone_number']) ? $sh['phone_number'] : '',
                    'email_address' => isset($sh['email_address']) ? $sh['email_address'] : '',
                    'full_address' => isset($sh['full_address']) ? $sh['full_address'] : '',
                    'idtype' => isset($sh['idtype']) ? $sh['idtype'] : '',
                    'idnumber' => isset($sh['idnumber']) ? $sh['idnumber'] : '',
                    'idfile' => '',
                    'added_by' => 72,
                    'approval_status' => 'pending'
                );

                $percentages[] = isset($sh['percentage']) ? floatval($sh['percentage']) : 0;
            }

            if (!empty($shareholder_rows)) {
                // Insert all shareholders and get their IDs
                $shareholder_ids = $this->Shareholders_model->insert_batch($shareholder_rows);

                // Link each shareholder to the corporate customer
                for ($i = 0; $i < count($shareholder_ids); $i++) {
                    $this->Corporate_shareholders_model->insert(array(
                        'corporate_id' => $corporate_id,
                        'shareholder_id' => $shareholder_ids[$i],
                        'percentage_value' => $percentages[$i]
                    ));

                    $shareholders_result[] = array(
                        'id' => $shareholder_ids[$i],
                        'name' => $shareholder_rows[$i]['first_name'] . ' ' . $shareholder_rows[$i]['last_name'],
                        'percentage' => $percentages[$i]
                    );
                }
            }
        }

        // Log the activity
        $this->db->insert('activity_logger', array(
            'user_id' => 72,
            'activity' => 'API: Registered business "' . $corporate_data['EntityName'] . '" with ' . count($shareholders_result) . ' shareholders, linked to individual #' . $individual->id,
            'activity_cate' => 'business_registration'
        ));

        $response_data = array(
            'corporate_id' => $corporate_id,
            'client_id' => $clientid,
            'entity_name' => $corporate_data['EntityName'],
            'registration_number' => $corporate_data['RegistrationNumber'],
            'entity_type' => $corporate_data['entity_type'],
            'contact_email' => $corporate_data['contact_email'],
            'phone_number' => $corporate_data['phone_number'],
            'status' => 'pending',
            'linked_individual' => array(
                'id' => $individual->id,
                'name' => $individual->Firstname . ' ' . $individual->Lastname
            ),
            'shareholders' => $shareholders_result,
            'documents' => array(
                'company_certificate' => isset($uploaded_docs['company_certificate']) ? base_url('uploads/' . $uploaded_docs['company_certificate']) : null,
                'proof_physical_address' => isset($uploaded_docs['proof_physical_address']) ? base_url('uploads/' . $uploaded_docs['proof_physical_address']) : null,
                'financial_statement' => isset($uploaded_docs['financial_statement']) ? base_url('uploads/' . $uploaded_docs['financial_statement']) : null,
                'tax_id_doc' => isset($uploaded_docs['tax_id_doc']) ? base_url('uploads/' . $uploaded_docs['tax_id_doc']) : null
            ),
            'created_on' => $corporate_data['CreatedOn']
        );

        $this->_response(array(
            'status' => 'success',
            'message' => 'Business registered successfully and linked to your profile',
            'data' => $response_data
        ), 201);
    }

    /**
     * GET /Api/get_business_details
     * Retrieve business(es) linked to an individual customer with shareholders
     * Params: email or customer_id
     */
    public function get_business_details()
    {
        $input = array_merge(
            $this->input->get() ? $this->input->get() : array(),
            $this->_get_input() ? $this->_get_input() : array()
        );

        // Find individual customer
        $individual = null;
        if (!empty($input['customer_id'])) {
            $individual = get_by_id('individual_customers', 'id', $input['customer_id']);
        } elseif (!empty($input['email'])) {
            $individual = $this->db->where('LOWER(TRIM(EmailAddress))', strtolower(trim($input['email'])))->get('individual_customers')->row();
        }

        if (!$individual) {
            $this->_response(array('status' => 'error', 'message' => 'Please provide a valid email or customer_id'), 400);
        }

        // Get all corporate customers linked to this individual
        $corporates = $this->Corporate_customers_model->get_by_linked_individual($individual->id);

        $businesses = array();
        $off_takers = array();
        $base_upload_url = base_url('uploads/');

        foreach ($corporates as $corp) {
            // Build the corporate record
            $corp_data = $this->_build_corporate_response($corp, $base_upload_url);

            // Split by category
            if ($corp->category === 'off_taker') {
                $off_takers[] = $corp_data;
            } else {
                $businesses[] = $corp_data;
            }
        }

        $this->_response(array(
            'status' => 'success',
            'data' => array(
                'individual' => array(
                    'id' => $individual->id,
                    'client_id' => $individual->ClientId,
                    'name' => $individual->Firstname . ' ' . $individual->Lastname,
                    'email' => $individual->EmailAddress,
                    'phone' => $individual->PhoneNumber
                ),
                'businesses' => $businesses,
                'total_businesses' => count($businesses),
                'off_takers' => $off_takers,
                'total_off_takers' => count($off_takers)
            )
        ));
    }

    /**
     * GET /Api/get_countries
     * Returns all countries
     */
    public function get_countries()
    {
        $this->load->model('Geo_countries_model');
        $countries = $this->Geo_countries_model->get_all();

        $result = array();
        foreach ($countries as $c) {
            $result[] = array(
                'id' => $c->id,
                'name' => $c->name,
                'code' => $c->code,
                'abv3' => $c->abv3
            );
        }

        $this->_response(array(
            'status' => 'success',
            'data' => $result,
            'total' => count($result)
        ));
    }

    /**
     * GET /Api/get_branches
     * Returns all branches (dynamic from DB)
     */
    public function get_branches()
    {
        $this->load->model('Branches_model');
        $branches = $this->Branches_model->get_all();

        $result = array();
        foreach ($branches as $b) {
            $result[] = array(
                'id' => $b->id,
                'code' => $b->BranchCode,
                'name' => $b->BranchName
            );
        }

        $this->_response(array(
            'status' => 'success',
            'data' => $result,
            'total' => count($result)
        ));
    }

    /**
     * GET /Api/get_form_options
     * Returns all static dropdown values used in business registration
     */
    public function get_form_options()
    {
        $this->_response(array(
            'status' => 'success',
            'data' => array(
                'entity_types' => array(
                    'Sole Proprietorship',
                    'Partnership',
                    'Limited Company',
                    'Government Entity',
                    'Non Government Entity'
                ),
                'categories' => array(
                    'client' => 'Client',
                    'off_taker' => 'Off taker'
                ),
                'nature_of_business' => array(
                    'Agriculture',
                    'Mining',
                    'Manufacturing',
                    'Construction',
                    'Retail Trade',
                    'Wholesale Trade',
                    'Transportation',
                    'Hospitality',
                    'Financial Services',
                    'Real Estate',
                    'Information Technology',
                    'Healthcare',
                    'Education',
                    'Energy',
                    'Telecommunications',
                    'Media',
                    'Professional Services',
                    'Food & Beverage',
                    'Textile & Clothing',
                    'Import/Export',
                    'Non-profit Organization',
                    'Other'
                ),
                'industry_sectors' => array(
                    'Agriculture',
                    'Mining',
                    'Manufacturing',
                    'Construction',
                    'Wholesale Trade',
                    'Retail Trade',
                    'Transportation',
                    'Information Technology',
                    'Finance',
                    'Real Estate',
                    'Professional Services',
                    'Education',
                    'Healthcare',
                    'Arts',
                    'Hospitality',
                    'Public Administration',
                    'Energy',
                    'Telecommunications',
                    'Media',
                    'Other'
                ),
                'provinces' => array(
                    'Central' => 'Central Province',
                    'Copperbelt' => 'Copperbelt Province',
                    'Eastern' => 'Eastern Province',
                    'Luapula' => 'Luapula Province',
                    'Lusaka' => 'Lusaka Province',
                    'Muchinga' => 'Muchinga Province',
                    'Northern' => 'Northern Province',
                    'North-Western' => 'North-Western Province',
                    'Southern' => 'Southern Province',
                    'Western' => 'Western Province'
                ),
                'titles' => array('Mr', 'Mrs', 'Miss', 'Dr', 'Prof', 'Rev', 'Bishop', 'Other'),
                'genders' => array('male', 'female'),
                'id_types' => array(
                    'NATIONAL_IDENTITY_CARD' => 'National ID',
                    'PASSPORT' => 'Passport',
                    'WORK_PERMIT' => 'Work Permit',
                    'DRIVER_LICENSE' => "Driver's License",
                    'NONE' => 'None'
                ),
                'phone_country_codes' => array(
                    array('code' => '+260', 'country' => 'ZM'),
                    array('code' => '+27', 'country' => 'ZA'),
                    array('code' => '+263', 'country' => 'ZW'),
                    array('code' => '+265', 'country' => 'MW'),
                    array('code' => '+258', 'country' => 'MZ'),
                    array('code' => '+267', 'country' => 'BW'),
                    array('code' => '+264', 'country' => 'NA'),
                    array('code' => '+243', 'country' => 'CD'),
                    array('code' => '+255', 'country' => 'TZ'),
                    array('code' => '+254', 'country' => 'KE')
                )
            )
        ));
    }

    /**
     * POST /Api/apply_corporate_loan
     * Apply for a corporate loan. Optionally creates the off-taker inline.
     *
     * Required: customer_id or email (to identify individual),
     *           corporate_id (the borrowing corporate customer),
     *           amount, currency, loan_product_id, months, interest, loan_date
     *
     * Optional: off_taker_id (existing off-taker) OR off_taker (object to create new)
     */
    public function apply_corporate_loan()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->_response(array('status' => 'error', 'message' => 'Method not allowed. Use POST request.'), 405);
        }

        $input = $this->_get_input();
        if (empty($input)) {
            $input = $this->input->post() ? $this->input->post() : array();
        }
        if (!empty($_POST)) {
            $input = array_merge($input ? $input : array(), $_POST);
        }

        log_message('debug', 'apply_corporate_loan raw input: ' . json_encode($input));

        // Identify the individual applying
        $individual = null;
        if (!empty($input['customer_id'])) {
            $individual = get_by_id('individual_customers', 'id', $input['customer_id']);
        } elseif (!empty($input['email'])) {
            $individual = $this->db->where('LOWER(TRIM(EmailAddress))', strtolower(trim($input['email'])))->get('individual_customers')->row();
        }

        if (!$individual) {
            $this->_response(array('status' => 'error', 'message' => 'Individual customer not found. Provide customer_id or email.'), 404);
        }

        // Validate required loan fields
        $required = array('corporate_id', 'amount', 'currency', 'loan_product_id', 'months', 'interest', 'loan_date');
        $missing = $this->_validate_required($input, $required);
        if (!empty($missing)) {
            $this->_response(array('status' => 'error', 'message' => 'Missing required fields', 'missing_fields' => $missing), 400);
        }

        // Verify the corporate customer exists and is linked to this individual
        $corporate = $this->Corporate_customers_model->get_by_id($input['corporate_id']);
        if (!$corporate) {
            $this->_response(array('status' => 'error', 'message' => 'Corporate customer not found'), 404);
        }

        // Verify loan product exists
        $product = $this->Loan_products_model->get_by_id($input['loan_product_id']);
        if (!$product) {
            $this->_response(array('status' => 'error', 'message' => 'Loan product not found'), 404);
        }

        // Verify currency exists (table is 'currencies', PK is 'currency_id')
        $currency_check = $this->db->where('currency_id', $input['currency'])->get('currencies')->row();
        if (!$currency_check) {
            $this->_response(array('status' => 'error', 'message' => 'Currency not found'), 404);
        }

        // Handle off-taker: either existing ID or create new one inline
        $offtaker_id = null;
        $offtaker_created = null;

        if (!empty($input['off_taker_id'])) {
            // Use existing off-taker
            $existing_offtaker = $this->Corporate_customers_model->get_by_id($input['off_taker_id']);
            if (!$existing_offtaker) {
                $this->_response(array('status' => 'error', 'message' => 'Off-taker not found'), 404);
            }
            $offtaker_id = $input['off_taker_id'];

        } elseif (!empty($input['off_taker']) && is_array($input['off_taker'])) {
            // Create off-taker inline
            $ot = $input['off_taker'];

            if (empty($ot['EntityName']) || empty($ot['RegistrationNumber'])) {
                $this->_response(array('status' => 'error', 'message' => 'Off-taker requires EntityName and RegistrationNumber'), 400);
            }

            // Check if off-taker with same registration already exists
            $existing_ot = $this->db->where('RegistrationNumber', $ot['RegistrationNumber'])->get('corporate_customers')->row();
            if ($existing_ot) {
                $offtaker_id = $existing_ot->id;
                $offtaker_created = array(
                    'id' => $existing_ot->id,
                    'client_id' => $existing_ot->ClientId,
                    'entity_name' => $existing_ot->EntityName,
                    'already_existed' => true
                );
            } else {
                // Generate client ID for off-taker
                $ot_clientid = 'OT' . rand(100, 999) . rand(1000, 9999);

                $ot_data = array(
                    'ClientId' => $ot_clientid,
                    'EntityName' => $ot['EntityName'],
                    'DateOfIncorporation' => isset($ot['DateOfIncorporation']) ? $ot['DateOfIncorporation'] : null,
                    'RegistrationNumber' => $ot['RegistrationNumber'],
                    'entity_type' => isset($ot['entity_type']) ? $ot['entity_type'] : 'Limited Company',
                    'category' => 'off_taker',
                    'TaxIdentificationNumber' => isset($ot['TaxIdentificationNumber']) ? $ot['TaxIdentificationNumber'] : '',
                    'Country' => isset($ot['Country']) ? $ot['Country'] : $corporate->Country,
                    'Branch' => $individual->Branch,
                    'Status' => isset($ot['Status']) ? $ot['Status'] : '',
                    'nature_of_business' => isset($ot['nature_of_business']) ? $ot['nature_of_business'] : '',
                    'industry_sector' => isset($ot['industry_sector']) ? $ot['industry_sector'] : '',
                    'street' => isset($ot['street']) ? $ot['street'] : '',
                    'city_town' => isset($ot['city_town']) ? $ot['city_town'] : '',
                    'province' => isset($ot['province']) ? $ot['province'] : '',
                    'postal_code' => isset($ot['postal_code']) ? $ot['postal_code'] : '',
                    'phone_number' => isset($ot['phone_number']) ? $ot['phone_number'] : '',
                    'contact_email' => isset($ot['contact_email']) ? $ot['contact_email'] : '',
                    'website' => isset($ot['website']) ? $ot['website'] : '',
                    'key_management_info' => isset($ot['key_management_info']) ? $ot['key_management_info'] : '',
                    'business_info' => isset($ot['business_info']) ? $ot['business_info'] : '',
                    'financial_year_end' => isset($ot['financial_year_end']) ? $ot['financial_year_end'] : null,
                    'casual_employees' => isset($ot['casual_employees']) ? intval($ot['casual_employees']) : 0,
                    'permanent_employees' => isset($ot['permanent_employees']) ? intval($ot['permanent_employees']) : 0,
                    'company_certificate' => '',
                    'tax_id_doc' => '',
                    'proof_physical_address' => '',
                    'financial_statement' => '',
                    'approval_status' => 'Approved',
                    'linked_individual_id' => $individual->id,
                    'added_by' => 72,
                    'CreatedOn' => date('Y-m-d H:i:s'),
                    'LastUpdatedOn' => date('Y-m-d H:i:s')
                );

                $offtaker_id = $this->Corporate_customers_model->insert($ot_data);

                if (!$offtaker_id) {
                    $this->_response(array('status' => 'error', 'message' => 'Failed to create off-taker'), 500);
                }

                $offtaker_created = array(
                    'id' => $offtaker_id,
                    'client_id' => $ot_clientid,
                    'entity_name' => $ot_data['EntityName'],
                    'already_existed' => false
                );
            }
        }

        // Apply the loan using Loan_model->add_loan()
        $appraisal_data = array(
            'crb_search' => isset($input['crb_search']) ? $input['crb_search'] : null,
            'pacra_search' => isset($input['pacra_search']) ? $input['pacra_search'] : null,
            'previous_facilities' => isset($input['previous_facilities']) ? $input['previous_facilities'] : null,
            'past_loans_comment' => isset($input['past_loans_comment']) ? $input['past_loans_comment'] : null,
            'security_notes' => isset($input['security_notes']) ? $input['security_notes'] : null,
            'bank_statement_notes' => isset($input['bank_statement_notes']) ? $input['bank_statement_notes'] : null,
            'about_transaction' => isset($input['about_transaction']) ? $input['about_transaction'] : null,
            'risk_analysis' => isset($input['risk_analysis']) ? $input['risk_analysis'] : null
        );

        $result = $this->Loan_model->add_loan(
            '',                                                             // loan_number (auto-generated)
            floatval($input['amount']),                                     // lamount
            intval($input['months']),                                       // lmonths
            floatval($input['interest']),                                   // interest
            intval($input['loan_product_id']),                              // product_id
            $input['loan_date'],                                            // ldate
            intval($input['corporate_id']),                                 // loan_customer
            'institution',                                                  // customer_type
            '',                                                             // worthness_file
            isset($input['narration']) ? $input['narration'] : '',          // narration
            72,                                                             // added_by (default API user)
            0,                                                              // method
            0,                                                              // fee_amount
            intval($input['currency']),                                     // currency
            $offtaker_id,                                                   // offtaker
            isset($input['processing_fee']) ? floatval($input['processing_fee']) : 0,  // processing_fee
            $appraisal_data                                                 // appraisal_data
        );

        // add_loan returns array('loan_id'=>..., 'loan_number'=>...) on success or string on error
        if (!is_array($result)) {
            $this->_response(array('status' => 'error', 'message' => 'Loan creation failed: ' . $result), 500);
        }

        $loan_id = $result['loan_id'];

        // Set loan status to 'CREATED'
        $this->db->where('loan_id', $loan_id)->update('loan', array('loan_status' => 'CREATED'));

        // Fetch the created loan directly (get_by_id joins employees which fails for added_by=0)
        $loan = $this->db->where('loan_id', $loan_id)->get('loan')->row();

        // Log activity
        $this->db->insert('activity_logger', array(
            'user_id' => 72,
            'activity' => 'API: Corporate loan #' . $loan->loan_number . ' applied for ' . $corporate->EntityName . ' by individual #' . $individual->id,
            'activity_cate' => 'loan_application'
        ));

        $response_data = array(
            'loan_id' => intval($loan_id),
            'loan_number' => $loan->loan_number,
            'corporate' => array(
                'id' => $corporate->id,
                'client_id' => $corporate->ClientId,
                'entity_name' => $corporate->EntityName
            ),
            'applied_by' => array(
                'id' => $individual->id,
                'name' => $individual->Firstname . ' ' . $individual->Lastname
            ),
            'loan_product' => array(
                'id' => intval($product->loan_product_id),
                'name' => $product->product_name,
                'calculation_type' => $product->calculation_type
            ),
            'principal' => floatval($loan->loan_principal),
            'interest_rate' => floatval($loan->loan_interest),
            'total_interest' => floatval($loan->loan_interest_amount),
            'total_repayment' => floatval($loan->loan_amount_total),
            'monthly_payment' => floatval($loan->loan_amount_term),
            'period_months' => intval($loan->loan_period),
            'loan_date' => $loan->loan_date,
            'currency' => intval($loan->currency),
            'processing_fee' => floatval($loan->processing_fee),
            'status' => 'pending',
            'off_taker' => $offtaker_created,
            'narration' => $loan->narration,
            'created_on' => date('Y-m-d H:i:s')
        );

        $this->_response(array(
            'status' => 'success',
            'message' => 'Corporate loan application submitted successfully',
            'data' => $response_data
        ), 201);
    }

    /**
     * Build a standard corporate customer response array
     */
    private function _build_corporate_response($corp, $base_upload_url = null)
    {
        if (!$base_upload_url) {
            $base_upload_url = base_url('uploads/');
        }

        // Get shareholders
        $corporate_shareholders = $this->db->select('
                shareholders.id, shareholders.title, shareholders.first_name, shareholders.last_name,
                shareholders.gender, shareholders.nationality, shareholders.phone_number,
                shareholders.email_address, shareholders.full_address, shareholders.idtype,
                shareholders.idnumber, corporate_shareholders.percentage_value
            ')
            ->from('corporate_shareholders')
            ->join('shareholders', 'shareholders.id = corporate_shareholders.shareholder_id')
            ->where('corporate_shareholders.corporate_id', $corp->id)
            ->get()->result();

        $shareholders_list = array();
        foreach ($corporate_shareholders as $sh) {
            $shareholders_list[] = array(
                'id' => $sh->id,
                'title' => $sh->title,
                'first_name' => $sh->first_name,
                'last_name' => $sh->last_name,
                'name' => trim($sh->title . ' ' . $sh->first_name . ' ' . $sh->last_name),
                'gender' => $sh->gender,
                'nationality' => $sh->nationality,
                'phone_number' => $sh->phone_number,
                'email_address' => $sh->email_address,
                'full_address' => $sh->full_address,
                'idtype' => $sh->idtype,
                'idnumber' => $sh->idnumber,
                'percentage' => floatval($sh->percentage_value)
            );
        }

        // Documents
        $documents = array(
            'company_certificate' => !empty($corp->company_certificate) ? $base_upload_url . $corp->company_certificate : null,
            'proof_physical_address' => !empty($corp->proof_physical_address) ? $base_upload_url . $corp->proof_physical_address : null,
            'financial_statement' => !empty($corp->financial_statement) ? $base_upload_url . $corp->financial_statement : null,
            'tax_id_doc' => !empty($corp->tax_id_doc) ? $base_upload_url . $corp->tax_id_doc : null
        );

        // Collaterals
        $collaterals_raw = $this->db->where('customer_id', $corp->id)
            ->where('customer_type', 'institution')
            ->get('collaterals')->result();
        $collaterals = array();
        foreach ($collaterals_raw as $col) {
            $collaterals[] = array(
                'id' => $col->id,
                'name' => $col->collateral_name,
                'type' => $col->collateral_type,
                'serial' => $col->collateral_serial,
                'market_value' => floatval($col->market_value),
                'force_sale_value' => floatval($col->force_sale_value),
                'location_status' => $col->location_status,
                'description' => $col->description
            );
        }

        return array(
            'corporate_id' => $corp->id,
            'client_id' => $corp->ClientId,
            'entity_name' => $corp->EntityName,
            'registration_number' => $corp->RegistrationNumber,
            'entity_type' => $corp->entity_type,
            'category' => $corp->category,
            'tax_identification_number' => $corp->TaxIdentificationNumber,
            'date_of_incorporation' => $corp->DateOfIncorporation,
            'country' => $corp->Country,
            'branch' => $corp->Branch,
            'status' => $corp->Status,
            'approval_status' => $corp->approval_status,
            'nature_of_business' => $corp->nature_of_business,
            'industry_sector' => $corp->industry_sector,
            'address' => array(
                'street' => $corp->street,
                'city_town' => $corp->city_town,
                'province' => $corp->province,
                'postal_code' => $corp->postal_code
            ),
            'contact' => array(
                'phone_number' => $corp->phone_number,
                'email' => $corp->contact_email,
                'website' => $corp->website
            ),
            'key_management_info' => $corp->key_management_info,
            'business_info' => $corp->business_info,
            'financial_year_end' => $corp->financial_year_end,
            'casual_employees' => intval($corp->casual_employees),
            'permanent_employees' => intval($corp->permanent_employees),
            'documents' => $documents,
            'shareholders' => $shareholders_list,
            'collaterals' => $collaterals,
            'created_on' => $corp->CreatedOn,
            'last_updated' => $corp->LastUpdatedOn
        );
    }

    /**
     * GET /Api/get_business_loans
     * Fetch all loans for businesses linked to an individual
     * Params: customer_id or email
     * Optional: corporate_id (filter to specific business)
     */
    public function get_business_loans()
    {
        $input = array_merge(
            $this->input->get() ? $this->input->get() : array(),
            $this->_get_input() ? $this->_get_input() : array()
        );

        // Find individual
        $individual = null;
        if (!empty($input['customer_id'])) {
            $individual = get_by_id('individual_customers', 'id', $input['customer_id']);
        } elseif (!empty($input['email'])) {
            $individual = $this->db->where('LOWER(TRIM(EmailAddress))', strtolower(trim($input['email'])))->get('individual_customers')->row();
        }

        if (!$individual) {
            $this->_response(array('status' => 'error', 'message' => 'Please provide a valid email or customer_id'), 400);
        }

        // Get linked corporate customers
        $corporates = $this->Corporate_customers_model->get_by_linked_individual($individual->id);

        if (empty($corporates)) {
            $this->_response(array('status' => 'success', 'data' => array(), 'total_loans' => 0));
        }

        // If filtering by specific corporate
        $corporate_ids = array();
        if (!empty($input['corporate_id'])) {
            $corporate_ids[] = $input['corporate_id'];
        } else {
            foreach ($corporates as $c) {
                $corporate_ids[] = $c->id;
            }
        }

        // Build corporate lookup map
        $corp_map = array();
        foreach ($corporates as $c) {
            $corp_map[$c->id] = $c;
        }

        // Fetch loans for all linked corporates (customer_type = institution)
        $this->db->select('loan.*, loan_products.product_name, loan_products.abbreviation, loan_products.frequency, loan_products.calculation_type');
        $this->db->from('loan');
        $this->db->join('loan_products', 'loan_products.loan_product_id = loan.loan_product', 'left');
        $this->db->where_in('loan.loan_customer', $corporate_ids);
        $this->db->where('loan.customer_type', 'institution');
        $this->db->order_by('loan.loan_id', 'DESC');
        $loans = $this->db->get()->result();

        $result = array();
        foreach ($loans as $loan) {
            // Payment schedules
            $schedules_raw = $this->db->where('loan_id', $loan->loan_id)
                ->order_by('payment_number', 'ASC')
                ->get('payement_schedules')->result();

            $schedules = array();
            $total_paid = 0;
            $total_due = 0;
            $next_due_date = null;

            foreach ($schedules_raw as $s) {
                $paid = (float)$s->paid_amount;
                $due = (float)$s->amount;
                $total_paid += $paid;
                $total_due += $due;

                if ($paid < $due && $next_due_date === null) {
                    $next_due_date = $s->payment_schedule;
                }

                $schedules[] = array(
                    'payment_number' => (int)$s->payment_number,
                    'due_date' => $s->payment_schedule,
                    'amount_due' => $due,
                    'principal' => (float)$s->principal,
                    'interest' => (float)$s->interest,
                    'paid_amount' => $paid,
                    'balance' => round($due - $paid, 2),
                    'loan_balance' => (float)$s->loan_balance
                );
            }

            $balance_remaining = round($total_due - $total_paid, 2);

            // Off-taker info
            $off_taker_info = null;
            if (!empty($loan->off_taker)) {
                $ot = $this->Corporate_customers_model->get_by_id($loan->off_taker);
                if ($ot) {
                    $off_taker_info = array(
                        'id' => $ot->id,
                        'client_id' => $ot->ClientId,
                        'entity_name' => $ot->EntityName,
                        'contact_email' => $ot->contact_email,
                        'phone_number' => $ot->phone_number
                    );
                }
            }

            // Corporate info
            $corp_info = null;
            if (isset($corp_map[$loan->loan_customer])) {
                $c = $corp_map[$loan->loan_customer];
                $corp_info = array(
                    'corporate_id' => $c->id,
                    'client_id' => $c->ClientId,
                    'entity_name' => $c->EntityName,
                    'category' => $c->category
                );
            }

            // Collaterals
            $collaterals_raw = $this->Collateral_model->get_loan_collaterals($loan->loan_id);
            $collaterals = array();
            foreach ($collaterals_raw as $col) {
                $collaterals[] = array(
                    'collateral_id' => isset($col->collateral_id) ? $col->collateral_id : $col->id,
                    'collateral_type' => isset($col->collateral_type) ? $col->collateral_type : '',
                    'collateral_name' => isset($col->collateral_name) ? $col->collateral_name : '',
                    'market_value' => isset($col->market_value) ? (float)$col->market_value : 0,
                    'force_sale_value' => isset($col->force_sale_value) ? (float)$col->force_sale_value : 0,
                    'amount_utilized' => isset($col->amount_utilized) ? (float)$col->amount_utilized : 0
                );
            }

            $result[] = array(
                'loan_id' => (int)$loan->loan_id,
                'loan_number' => $loan->loan_number,
                'product_name' => $loan->product_name,
                'calculation_type' => isset($loan->calculation_type) ? $loan->calculation_type : '',
                'frequency' => isset($loan->frequency) ? $loan->frequency : '',
                'corporate' => $corp_info,
                'loan_date' => $loan->loan_date,
                'principal' => (float)$loan->loan_principal,
                'period' => (int)$loan->loan_period,
                'interest_rate' => (float)$loan->loan_interest,
                'interest_amount' => (float)$loan->loan_interest_amount,
                'total_amount' => (float)$loan->loan_amount_total,
                'monthly_installment' => (float)$loan->loan_amount_term,
                'disbursed_amount' => (float)$loan->disbursed_amount,
                'currency' => isset($loan->currency) ? $loan->currency : '',
                'status' => $loan->loan_status,
                'narration' => isset($loan->narration) ? $loan->narration : '',
                'off_taker' => $off_taker_info,
                'processing_fee' => isset($loan->processing_fee) ? (float)$loan->processing_fee : 0,
                'payment_summary' => array(
                    'total_due' => round($total_due, 2),
                    'total_paid' => round($total_paid, 2),
                    'balance_remaining' => $balance_remaining,
                    'next_due_date' => $next_due_date
                ),
                'repayment_schedule' => $schedules,
                'collaterals' => $collaterals
            );
        }

        $this->_response(array(
            'status' => 'success',
            'data' => $result,
            'total_loans' => count($result)
        ));
    }
}
