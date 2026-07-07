<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Fixed Deposits Controller
 * Manages fixed deposit customers, deposits, and operations
 */
class Fixed_deposits extends CI_Controller
{
    function __construct()
    {
        parent::__construct();

        // Load models
        $this->load->model('Fd_customers_model');
        $this->load->model('Fd_deposits_model');
        $this->load->model('Fd_transactions_model');
        $this->load->model('Fd_schedules_model');
        $this->load->model('Fd_accruals_model');

        // Load libraries
        $this->load->library('form_validation');
        $this->load->library('pagination');

        // Load helpers
        $this->load->helper('fd');

        // Create tables if not exists
        $this->_create_tables();
    }

    /**
     * Create database tables if they don't exist
     */
    private function _create_tables()
    {
        // Check if main table exists
        if (!$this->db->table_exists('fd_customers')) {
            // fd_customers table with extended fields
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `fd_customers` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `customer_number` varchar(20) NOT NULL,
                    `first_name` varchar(100) NOT NULL,
                    `last_name` varchar(100) NOT NULL,
                    `gender` enum('Male','Female') NOT NULL,
                    `date_of_birth` date NULL,
                    `province` varchar(50) NOT NULL,
                    `district` varchar(100) NULL,
                    `city` varchar(100) NULL,
                    `address` text,
                    `email` varchar(150),
                    `phone_number` varchar(20) NOT NULL,
                    `alt_phone_number` varchar(20) NULL,
                    `occupation` varchar(100) NULL,
                    `employer` varchar(150) NULL,
                    `source_of_funds` varchar(200) NULL,
                    `id_type` varchar(50),
                    `id_number` varchar(50),
                    `id_expiry_date` date NULL,
                    `nrc_photo` varchar(255) NULL,
                    `proof_of_income` varchar(255) NULL,
                    `nok_name` varchar(150) NULL,
                    `nok_relationship` varchar(50) NULL,
                    `nok_phone` varchar(20) NULL,
                    `nok_address` text NULL,
                    `nok_id_number` varchar(50) NULL,
                    `bank_name` varchar(100) NULL,
                    `bank_account_number` varchar(50) NULL,
                    `bank_branch` varchar(100) NULL,
                    `status` enum('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
                    `created_by` int NOT NULL,
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` datetime ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `customer_number` (`customer_number`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        } else {
            // Add new columns if they don't exist
            $this->_add_column_if_not_exists('fd_customers', 'date_of_birth', 'date NULL AFTER `gender`');
            $this->_add_column_if_not_exists('fd_customers', 'district', 'varchar(100) NULL AFTER `province`');
            $this->_add_column_if_not_exists('fd_customers', 'city', 'varchar(100) NULL AFTER `district`');
            $this->_add_column_if_not_exists('fd_customers', 'alt_phone_number', 'varchar(20) NULL AFTER `phone_number`');
            $this->_add_column_if_not_exists('fd_customers', 'occupation', 'varchar(100) NULL AFTER `alt_phone_number`');
            $this->_add_column_if_not_exists('fd_customers', 'employer', 'varchar(150) NULL AFTER `occupation`');
            $this->_add_column_if_not_exists('fd_customers', 'source_of_funds', 'varchar(200) NULL AFTER `employer`');
            $this->_add_column_if_not_exists('fd_customers', 'id_expiry_date', 'date NULL AFTER `id_number`');
            $this->_add_column_if_not_exists('fd_customers', 'nrc_photo', 'varchar(255) NULL AFTER `id_expiry_date`');
            $this->_add_column_if_not_exists('fd_customers', 'proof_of_income', 'varchar(255) NULL AFTER `nrc_photo`');
            $this->_add_column_if_not_exists('fd_customers', 'nok_name', 'varchar(150) NULL AFTER `proof_of_income`');
            $this->_add_column_if_not_exists('fd_customers', 'nok_relationship', 'varchar(50) NULL AFTER `nok_name`');
            $this->_add_column_if_not_exists('fd_customers', 'nok_phone', 'varchar(20) NULL AFTER `nok_relationship`');
            $this->_add_column_if_not_exists('fd_customers', 'nok_address', 'text NULL AFTER `nok_phone`');
            $this->_add_column_if_not_exists('fd_customers', 'nok_id_number', 'varchar(50) NULL AFTER `nok_address`');
            $this->_add_column_if_not_exists('fd_customers', 'bank_name', 'varchar(100) NULL AFTER `nok_id_number`');
            $this->_add_column_if_not_exists('fd_customers', 'bank_account_number', 'varchar(50) NULL AFTER `bank_name`');
            $this->_add_column_if_not_exists('fd_customers', 'bank_branch', 'varchar(100) NULL AFTER `bank_account_number`');
            $this->_add_column_if_not_exists('fd_customers', 'personal_linkage', 'int NULL AFTER `bank_branch`');
        }

        if (!$this->db->table_exists('fd_deposits')) {

            // fd_deposits table
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `fd_deposits` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `deposit_number` varchar(20) NOT NULL,
                    `customer_id` int NOT NULL,
                    `principal_amount` decimal(18,2) NOT NULL,
                    `current_principal` decimal(18,2) NOT NULL,
                    `interest_rate` decimal(5,2) NOT NULL COMMENT 'Annual rate %',
                    `penalty_rate` decimal(5,2) NOT NULL DEFAULT 0 COMMENT 'Penalty % on interest for early withdrawal',
                    `start_date` date NOT NULL,
                    `maturity_date` date NOT NULL,
                    `duration_months` int NOT NULL COMMENT '3,6,9,12,etc',
                    `payment_option` enum('QUARTERLY','AT_MATURITY') DEFAULT 'QUARTERLY',
                    `accrued_interest` decimal(18,2) DEFAULT 0,
                    `paid_interest` decimal(18,2) DEFAULT 0,
                    `last_accrual_date` date,
                    `status` enum('ACTIVE','MATURED','CLOSED','MERGED') DEFAULT 'ACTIVE',
                    `merged_to_id` int DEFAULT NULL,
                    `notes` text,
                    `created_by` int NOT NULL,
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` datetime ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `deposit_number` (`deposit_number`),
                    KEY `customer_id` (`customer_id`),
                    CONSTRAINT `fk_fd_customer` FOREIGN KEY (`customer_id`) REFERENCES `fd_customers` (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");

            // fd_interest_accruals table (optional - for audit trail)
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `fd_interest_accruals` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `deposit_id` int NOT NULL,
                    `accrual_date` date NOT NULL,
                    `principal_balance` decimal(18,2) NOT NULL,
                    `daily_rate` decimal(10,8) NOT NULL,
                    `interest_amount` decimal(18,4) NOT NULL,
                    `quarter` tinyint NOT NULL COMMENT '1,2,3,4',
                    `year` int NOT NULL,
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `deposit_date` (`deposit_id`, `accrual_date`),
                    KEY `quarter_year` (`quarter`, `year`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");

            // fd_quarterly_schedules table
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `fd_quarterly_schedules` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `deposit_id` int NOT NULL,
                    `quarter` tinyint NOT NULL,
                    `year` int NOT NULL,
                    `quarter_start` date NOT NULL,
                    `quarter_end` date NOT NULL,
                    `deposit_start_in_quarter` date NOT NULL,
                    `days_in_quarter` int NOT NULL,
                    `deposit_days` int NOT NULL,
                    `expected_interest` decimal(18,2) DEFAULT 0,
                    `paid_interest` decimal(18,2) DEFAULT 0,
                    `penalty_amount` decimal(18,2) DEFAULT 0,
                    `status` enum('PENDING','PAID','PARTIAL','SKIPPED') DEFAULT 'PENDING',
                    `payment_date` date,
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `deposit_quarter` (`deposit_id`, `quarter`, `year`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");

            // fd_transactions table
            $this->db->query("
                CREATE TABLE IF NOT EXISTS `fd_transactions` (
                    `id` int NOT NULL AUTO_INCREMENT,
                    `deposit_id` int NOT NULL,
                    `transaction_ref` varchar(30) NOT NULL,
                    `transaction_type` enum('DEPOSIT','INTEREST_PAYMENT','PRINCIPAL_WITHDRAWAL','PENALTY','CLOSURE','MERGE_OUT','MERGE_IN','TOP_UP') NOT NULL,
                    `amount` decimal(18,2) NOT NULL,
                    `principal_before` decimal(18,2),
                    `principal_after` decimal(18,2),
                    `interest_before` decimal(18,2),
                    `interest_after` decimal(18,2),
                    `penalty_amount` decimal(18,2) DEFAULT 0,
                    `quarter` tinyint,
                    `year` int,
                    `notes` text,
                    `created_by` int NOT NULL,
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `transaction_ref` (`transaction_ref`),
                    KEY `deposit_id` (`deposit_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
        }

        // Add currency column to deposits if not exists
        $this->_add_column_if_not_exists('fd_deposits', 'currency', "varchar(10) DEFAULT 'ZMW' AFTER `customer_id`");
    }

    /**
     * Add column to table if it doesn't exist
     */
    private function _add_column_if_not_exists($table, $column, $definition)
    {
        $result = $this->db->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
        if ($result->num_rows() == 0) {
            $this->db->query("ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition}");
        }
    }

    /**
     * Dashboard - Overview page
     */
    public function index()
    {
        $data['page_title'] = 'Fixed Deposits Dashboard';

        // Get summary statistics
        $data['stats'] = $this->Fd_deposits_model->get_summary_stats();
        $data['customer_count'] = $this->Fd_customers_model->count_active();

        // Get maturing deposits (next 30 days)
        $data['maturing_deposits'] = $this->Fd_deposits_model->get_maturing_deposits(30);

        // Get overdue interest payments
        $data['overdue_payments'] = $this->Fd_schedules_model->get_overdue_payments();

        // Get upcoming payments
        $data['upcoming_payments'] = $this->Fd_schedules_model->get_upcoming_payments(30);

        // Get recent transactions
        $data['recent_transactions'] = $this->Fd_transactions_model->get_recent(10);

        $this->load->view('admin/header');
        $this->load->view('fixed_deposits/index', $data);
        $this->load->view('admin/footer');
    }

    // ==================== CUSTOMER MANAGEMENT ====================

    /**
     * List all customers
     */
    public function customers()
    {
        $q = $this->input->get('q', TRUE);
        $start = intval($this->input->get('start'));

        $config['base_url'] = base_url() . 'Fixed_deposits/customers?q=' . urlencode($q ?? '');
        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Fd_customers_model->total_rows($q);
        $this->pagination->initialize($config);

        $data['customers'] = $this->Fd_customers_model->get_limit_data($config['per_page'], $start, $q);
        $data['q'] = $q;
        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $config['total_rows'];
        $data['start'] = $start;
        $data['page_title'] = 'FD Customers';

        $this->load->view('admin/header');
        $this->load->view('fixed_deposits/customers_list', $data);
        $this->load->view('admin/footer');
    }

    /**
     * Create customer form
     */
    public function customer_create()
    {
        $data['page_title'] = 'Add FD Customer';
        $data['action'] = site_url('Fixed_deposits/customer_create_action');
        $data['button'] = 'Save Customer';

        // Form field defaults - Personal Info
        $data['customer_number'] = $this->Fd_customers_model->generate_customer_number();
        $data['first_name'] = set_value('first_name', '');
        $data['last_name'] = set_value('last_name', '');
        $data['gender'] = set_value('gender', '');
        $data['date_of_birth'] = set_value('date_of_birth', '');
        $data['province'] = set_value('province', '');
        $data['district'] = set_value('district', '');
        $data['city'] = set_value('city', '');
        $data['address'] = set_value('address', '');
        $data['email'] = set_value('email', '');
        $data['phone_number'] = set_value('phone_number', '');
        $data['alt_phone_number'] = set_value('alt_phone_number', '');

        // Employment/Income
        $data['occupation'] = set_value('occupation', '');
        $data['employer'] = set_value('employer', '');
        $data['source_of_funds'] = set_value('source_of_funds', '');

        // Identification
        $data['id_type'] = set_value('id_type', '');
        $data['id_number'] = set_value('id_number', '');
        $data['id_expiry_date'] = set_value('id_expiry_date', '');
        $data['nrc_photo'] = '';
        $data['proof_of_income'] = '';

        // Next of Kin
        $data['nok_name'] = set_value('nok_name', '');
        $data['nok_relationship'] = set_value('nok_relationship', '');
        $data['nok_phone'] = set_value('nok_phone', '');
        $data['nok_address'] = set_value('nok_address', '');
        $data['nok_id_number'] = set_value('nok_id_number', '');

        // Bank Details
        $data['bank_name'] = set_value('bank_name', '');
        $data['bank_account_number'] = set_value('bank_account_number', '');
        $data['bank_branch'] = set_value('bank_branch', '');

        $this->load->view('admin/header');
        $this->load->view('fixed_deposits/customer_form', $data);
        $this->load->view('admin/footer');
    }

    /**
     * Process customer creation
     */
    public function customer_create_action()
    {
        $this->_customer_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->customer_create();
        } else {
            $customer_number = $this->input->post('customer_number', TRUE);

            // Handle file uploads
            $nrc_photo = $this->_upload_file('nrc_photo', $customer_number);
            $proof_of_income = $this->_upload_file('proof_of_income', $customer_number);

            $data = array(
                'customer_number' => $customer_number,
                'first_name' => $this->input->post('first_name', TRUE),
                'last_name' => $this->input->post('last_name', TRUE),
                'gender' => $this->input->post('gender', TRUE),
                'date_of_birth' => $this->input->post('date_of_birth', TRUE) ?: null,
                'province' => $this->input->post('province', TRUE),
                'district' => $this->input->post('district', TRUE),
                'city' => $this->input->post('city', TRUE),
                'address' => $this->input->post('address', TRUE),
                'email' => $this->input->post('email', TRUE),
                'phone_number' => $this->input->post('phone_number', TRUE),
                'alt_phone_number' => $this->input->post('alt_phone_number', TRUE),
                'occupation' => $this->input->post('occupation', TRUE),
                'employer' => $this->input->post('employer', TRUE),
                'source_of_funds' => $this->input->post('source_of_funds', TRUE),
                'id_type' => $this->input->post('id_type', TRUE),
                'id_number' => $this->input->post('id_number', TRUE),
                'id_expiry_date' => $this->input->post('id_expiry_date', TRUE) ?: null,
                'nrc_photo' => $nrc_photo,
                'proof_of_income' => $proof_of_income,
                'nok_name' => $this->input->post('nok_name', TRUE),
                'nok_relationship' => $this->input->post('nok_relationship', TRUE),
                'nok_phone' => $this->input->post('nok_phone', TRUE),
                'nok_address' => $this->input->post('nok_address', TRUE),
                'nok_id_number' => $this->input->post('nok_id_number', TRUE),
                'bank_name' => $this->input->post('bank_name', TRUE),
                'bank_account_number' => $this->input->post('bank_account_number', TRUE),
                'bank_branch' => $this->input->post('bank_branch', TRUE),
                'status' => 'ACTIVE',
                'created_by' => $this->session->userdata('user_id')
            );

            $this->Fd_customers_model->insert($data);
            $this->session->set_flashdata('message', 'Customer created successfully');
            redirect('Fixed_deposits/customers');
        }
    }

    /**
     * Handle file upload for customer documents
     */
    private function _upload_file($field_name, $customer_number)
    {
        if (empty($_FILES[$field_name]['name'])) {
            return null;
        }

        // Create upload directory
        $upload_path = './uploads/fd_customers/' . $customer_number . '/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, TRUE);
        }

        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'jpg|jpeg|png|pdf';
        $config['max_size'] = 5120; // 5MB
        $config['encrypt_name'] = TRUE;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ($this->upload->do_upload($field_name)) {
            $upload_data = $this->upload->data();
            return 'uploads/fd_customers/' . $customer_number . '/' . $upload_data['file_name'];
        }

        return null;
    }

    /**
     * View customer details
     */
    public function customer_view($id)
    {
        $customer = $this->Fd_customers_model->get_by_id($id);
        if (!$customer) {
            $this->session->set_flashdata('error', 'Customer not found');
            redirect('Fixed_deposits/customers');
        }

        $data['customer'] = $customer;
        $data['deposits'] = $this->Fd_deposits_model->get_by_customer($id);
        $data['page_title'] = 'View Customer: ' . $customer->first_name . ' ' . $customer->last_name;

        // Load linked individual customer if set
        $data['linked_individual'] = null;
        if (!empty($customer->personal_linkage)) {
            $data['linked_individual'] = get_by_id('individual_customers', 'id', $customer->personal_linkage);
        }

        // Calculate totals
        $data['total_principal'] = 0;
        $data['total_accrued'] = 0;
        foreach ($data['deposits'] as $deposit) {
            if ($deposit->status == 'ACTIVE') {
                $data['total_principal'] += $deposit->current_principal;
                $data['total_accrued'] += calculate_accrued_interest($deposit);
            }
        }

        $this->load->view('admin/header');
        $this->load->view('fixed_deposits/customer_view', $data);
        $this->load->view('admin/footer');
    }

    /**
     * Update customer form
     */
    public function customer_update($id)
    {
        $customer = $this->Fd_customers_model->get_by_id($id);
        if (!$customer) {
            $this->session->set_flashdata('error', 'Customer not found');
            redirect('Fixed_deposits/customers');
        }

        $data['page_title'] = 'Edit FD Customer';
        $data['action'] = site_url('Fixed_deposits/customer_update_action');
        $data['button'] = 'Update Customer';
        $data['id'] = $id;

        // Form field values - Personal Info
        $data['customer_number'] = $customer->customer_number;
        $data['first_name'] = set_value('first_name', $customer->first_name);
        $data['last_name'] = set_value('last_name', $customer->last_name);
        $data['gender'] = set_value('gender', $customer->gender);
        $data['date_of_birth'] = set_value('date_of_birth', $customer->date_of_birth ?? '');
        $data['province'] = set_value('province', $customer->province);
        $data['district'] = set_value('district', $customer->district ?? '');
        $data['city'] = set_value('city', $customer->city ?? '');
        $data['address'] = set_value('address', $customer->address);
        $data['email'] = set_value('email', $customer->email);
        $data['phone_number'] = set_value('phone_number', $customer->phone_number);
        $data['alt_phone_number'] = set_value('alt_phone_number', $customer->alt_phone_number ?? '');

        // Employment/Income
        $data['occupation'] = set_value('occupation', $customer->occupation ?? '');
        $data['employer'] = set_value('employer', $customer->employer ?? '');
        $data['source_of_funds'] = set_value('source_of_funds', $customer->source_of_funds ?? '');

        // Identification
        $data['id_type'] = set_value('id_type', $customer->id_type);
        $data['id_number'] = set_value('id_number', $customer->id_number);
        $data['id_expiry_date'] = set_value('id_expiry_date', $customer->id_expiry_date ?? '');
        $data['nrc_photo'] = $customer->nrc_photo ?? '';
        $data['proof_of_income'] = $customer->proof_of_income ?? '';

        // Next of Kin
        $data['nok_name'] = set_value('nok_name', $customer->nok_name ?? '');
        $data['nok_relationship'] = set_value('nok_relationship', $customer->nok_relationship ?? '');
        $data['nok_phone'] = set_value('nok_phone', $customer->nok_phone ?? '');
        $data['nok_address'] = set_value('nok_address', $customer->nok_address ?? '');
        $data['nok_id_number'] = set_value('nok_id_number', $customer->nok_id_number ?? '');

        // Bank Details
        $data['bank_name'] = set_value('bank_name', $customer->bank_name ?? '');
        $data['bank_account_number'] = set_value('bank_account_number', $customer->bank_account_number ?? '');
        $data['bank_branch'] = set_value('bank_branch', $customer->bank_branch ?? '');

        $this->load->view('admin/header');
        $this->load->view('fixed_deposits/customer_form', $data);
        $this->load->view('admin/footer');
    }

    /**
     * Process customer update
     */
    public function customer_update_action()
    {
        $this->_customer_rules();

        $id = $this->input->post('id', TRUE);

        if ($this->form_validation->run() == FALSE) {
            $this->customer_update($id);
        } else {
            $customer = $this->Fd_customers_model->get_by_id($id);
            $customer_number = $customer->customer_number;

            // Handle file uploads (only update if new file uploaded)
            $nrc_photo = $this->_upload_file('nrc_photo', $customer_number);
            $proof_of_income = $this->_upload_file('proof_of_income', $customer_number);

            $data = array(
                'first_name' => $this->input->post('first_name', TRUE),
                'last_name' => $this->input->post('last_name', TRUE),
                'gender' => $this->input->post('gender', TRUE),
                'date_of_birth' => $this->input->post('date_of_birth', TRUE) ?: null,
                'province' => $this->input->post('province', TRUE),
                'district' => $this->input->post('district', TRUE),
                'city' => $this->input->post('city', TRUE),
                'address' => $this->input->post('address', TRUE),
                'email' => $this->input->post('email', TRUE),
                'phone_number' => $this->input->post('phone_number', TRUE),
                'alt_phone_number' => $this->input->post('alt_phone_number', TRUE),
                'occupation' => $this->input->post('occupation', TRUE),
                'employer' => $this->input->post('employer', TRUE),
                'source_of_funds' => $this->input->post('source_of_funds', TRUE),
                'id_type' => $this->input->post('id_type', TRUE),
                'id_number' => $this->input->post('id_number', TRUE),
                'id_expiry_date' => $this->input->post('id_expiry_date', TRUE) ?: null,
                'nok_name' => $this->input->post('nok_name', TRUE),
                'nok_relationship' => $this->input->post('nok_relationship', TRUE),
                'nok_phone' => $this->input->post('nok_phone', TRUE),
                'nok_address' => $this->input->post('nok_address', TRUE),
                'nok_id_number' => $this->input->post('nok_id_number', TRUE),
                'bank_name' => $this->input->post('bank_name', TRUE),
                'bank_account_number' => $this->input->post('bank_account_number', TRUE),
                'bank_branch' => $this->input->post('bank_branch', TRUE)
            );

            // Only update file paths if new files were uploaded
            if ($nrc_photo) {
                $data['nrc_photo'] = $nrc_photo;
            }
            if ($proof_of_income) {
                $data['proof_of_income'] = $proof_of_income;
            }

            $this->Fd_customers_model->update($id, $data);
            $this->session->set_flashdata('message', 'Customer updated successfully');
            redirect('Fixed_deposits/customers');
        }
    }

    /**
     * Customer validation rules
     */
    private function _customer_rules()
    {
        $this->form_validation->set_rules('first_name', 'First Name', 'trim|required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
        $this->form_validation->set_rules('gender', 'Gender', 'trim|required');
        $this->form_validation->set_rules('province', 'Province', 'trim|required');
        $this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email');
        $this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

    // ==================== DEPOSIT MANAGEMENT ====================

    /**
     * List all deposits
     */
    public function deposits()
    {
        $q = $this->input->get('q', TRUE);
        $status = $this->input->get('status', TRUE);
        $start = intval($this->input->get('start'));

        $config['base_url'] = base_url() . 'Fixed_deposits/deposits?q=' . urlencode($q ?? '') . '&status=' . ($status ?? '');
        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Fd_deposits_model->total_rows($q, $status);
        $this->pagination->initialize($config);

        $data['deposits'] = $this->Fd_deposits_model->get_limit_data($config['per_page'], $start, $q, $status);
        $data['q'] = $q;
        $data['status'] = $status;
        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $config['total_rows'];
        $data['start'] = $start;
        $data['page_title'] = 'All Deposits';

        $this->load->view('admin/header');
        $this->load->view('fixed_deposits/deposits_list', $data);
        $this->load->view('admin/footer');
    }

    /**
     * Create deposit form
     */
    public function deposit_create($customer_id = null)
    {
        $data['page_title'] = 'New Fixed Deposit';
        $data['action'] = site_url('Fixed_deposits/deposit_create_action');
        $data['button'] = 'Create Deposit';

        // Get customers for dropdown
        $data['customers'] = $this->Fd_customers_model->get_for_dropdown();
        $data['selected_customer'] = $customer_id;

        // Form field defaults
        $data['deposit_number'] = $this->Fd_deposits_model->generate_deposit_number();
        $data['customer_id'] = set_value('customer_id', $customer_id);
        $data['principal_amount'] = set_value('principal_amount', '');
        $data['interest_rate'] = set_value('interest_rate', '');
        $data['penalty_rate'] = set_value('penalty_rate', '10');
        $data['start_date'] = set_value('start_date', date('Y-m-d'));
        $data['duration_months'] = set_value('duration_months', '12');
        $data['payment_option'] = set_value('payment_option', 'QUARTERLY');
        $data['notes'] = set_value('notes', '');

        $this->load->view('admin/header');
        $this->load->view('fixed_deposits/deposit_form', $data);
        $this->load->view('admin/footer');
    }

    /**
     * Process deposit creation
     */
    public function deposit_create_action()
    {
        $this->_deposit_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->deposit_create($this->input->post('customer_id', TRUE));
        } else {
            $principal = floatval($this->input->post('principal_amount', TRUE));
            $start_date = $this->input->post('start_date', TRUE);
            $duration = intval($this->input->post('duration_months', TRUE));
            $interest_rate = floatval($this->input->post('interest_rate', TRUE));
            $maturity_date = calculate_maturity_date($start_date, $duration);

            $deposit_data = array(
                'deposit_number' => $this->input->post('deposit_number', TRUE),
                'customer_id' => $this->input->post('customer_id', TRUE),
                'principal_amount' => $principal,
                'current_principal' => $principal,
                'interest_rate' => $interest_rate,
                'penalty_rate' => $this->input->post('penalty_rate', TRUE),
                'start_date' => $start_date,
                'maturity_date' => $maturity_date,
                'duration_months' => $duration,
                'payment_option' => $this->input->post('payment_option', TRUE),
                'accrued_interest' => 0,
                'paid_interest' => 0,
                'status' => 'ACTIVE',
                'notes' => $this->input->post('notes', TRUE),
                'created_by' => $this->session->userdata('user_id')
            );

            $deposit_id = $this->Fd_deposits_model->insert($deposit_data);

            // Generate quarterly schedule if payment option is QUARTERLY
            if ($deposit_data['payment_option'] == 'QUARTERLY') {
                $this->Fd_schedules_model->generate_quarterly_schedule(
                    $deposit_id,
                    $start_date,
                    $maturity_date,
                    $principal,
                    $interest_rate
                );
            }

            // Create initial transaction
            $current_quarter = get_current_quarter();
            $transaction_data = array(
                'deposit_id' => $deposit_id,
                'transaction_ref' => $this->Fd_transactions_model->generate_ref(),
                'transaction_type' => 'DEPOSIT',
                'amount' => $principal,
                'principal_before' => 0,
                'principal_after' => $principal,
                'interest_before' => 0,
                'interest_after' => 0,
                'quarter' => $current_quarter['quarter'],
                'year' => $current_quarter['year'],
                'notes' => 'Initial deposit',
                'created_by' => $this->session->userdata('user_id')
            );
            $this->Fd_transactions_model->insert($transaction_data);

            $this->session->set_flashdata('message', 'Fixed deposit created successfully');
            redirect('Fixed_deposits/deposit_view/' . $deposit_id);
        }
    }

    /**
     * View deposit details
     */
    public function deposit_view($id)
    {
        $deposit = $this->Fd_deposits_model->get_deposit_with_customer($id);
        if (!$deposit) {
            $this->session->set_flashdata('error', 'Deposit not found');
            redirect('Fixed_deposits/deposits');
        }

        $data['deposit'] = $deposit;
        $data['schedules'] = $this->Fd_schedules_model->get_by_deposit($id);
        $data['transactions'] = $this->Fd_transactions_model->get_by_deposit($id);

        // Calculate current accrued interest on-demand
        $data['current_accrued'] = calculate_accrued_interest($deposit);

        // Calculate days to maturity
        $today = new DateTime();
        $maturity = new DateTime($deposit->maturity_date);
        $data['days_to_maturity'] = $today > $maturity ? 0 : $today->diff($maturity)->days;

        $data['page_title'] = 'Deposit: ' . $deposit->deposit_number;

        $this->load->view('admin/header');
        $this->load->view('fixed_deposits/deposit_view', $data);
        $this->load->view('admin/footer');
    }

    /**
     * Deposit statement
     */
    public function deposit_statement($id)
    {
        $deposit = $this->Fd_deposits_model->get_deposit_with_customer($id);
        if (!$deposit) {
            $this->session->set_flashdata('error', 'Deposit not found');
            redirect('Fixed_deposits/deposits');
        }

        $from_date = $this->input->get('from_date', TRUE);
        $to_date = $this->input->get('to_date', TRUE);

        $data['deposit'] = $deposit;
        $data['transactions'] = $this->Fd_transactions_model->get_for_statement($id, $from_date, $to_date);
        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['page_title'] = 'Statement: ' . $deposit->deposit_number;

        $this->load->view('admin/header');
        $this->load->view('fixed_deposits/deposit_statement', $data);
        $this->load->view('admin/footer');
    }

    /**
     * Deposit validation rules
     */
    private function _deposit_rules()
    {
        $this->form_validation->set_rules('customer_id', 'Customer', 'trim|required');
        $this->form_validation->set_rules('principal_amount', 'Principal Amount', 'trim|required|numeric|greater_than[0]');
        $this->form_validation->set_rules('interest_rate', 'Interest Rate', 'trim|required|numeric|greater_than[0]');
        $this->form_validation->set_rules('penalty_rate', 'Penalty Rate', 'trim|required|numeric');
        $this->form_validation->set_rules('start_date', 'Start Date', 'trim|required');
        $this->form_validation->set_rules('duration_months', 'Duration', 'trim|required|integer|greater_than[2]');
        $this->form_validation->set_rules('payment_option', 'Payment Option', 'trim|required');
        $this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

    // ==================== OPERATIONS ====================

    /**
     * Withdrawal form
     */
    public function withdraw($deposit_id)
    {
        $deposit = $this->Fd_deposits_model->get_deposit_with_customer($deposit_id);
        if (!$deposit || $deposit->status != 'ACTIVE') {
            $this->session->set_flashdata('error', 'Invalid deposit or not active');
            redirect('Fixed_deposits/deposits');
        }

        $data['deposit'] = $deposit;
        $data['current_accrued'] = calculate_accrued_interest($deposit);
        $data['page_title'] = 'Withdraw from ' . $deposit->deposit_number;
        $data['action'] = site_url('Fixed_deposits/withdraw_action');

        // Calculate penalty preview
        $data['penalty_preview'] = calculate_penalty($data['current_accrued'], $deposit->penalty_rate);
        $data['interest_after_penalty'] = $data['current_accrued'] - $data['penalty_preview'];

        // WHT (Withholding Tax) @ 15%
        $data['wht_rate'] = 15;
        $data['wht_amount'] = $data['interest_after_penalty'] * ($data['wht_rate'] / 100);
        $data['net_interest'] = $data['interest_after_penalty'] - $data['wht_amount'];

        $this->load->view('admin/header');
        $this->load->view('fixed_deposits/withdraw_form', $data);
        $this->load->view('admin/footer');
    }

    /**
     * Process withdrawal
     *
     * Business Rules:
     * - FULL withdrawal: Customer gets full principal + (accrued interest - penalty). Deposit CLOSED.
     * - PARTIAL withdrawal: Customer gets requested amount + (accrued interest - penalty).
     *   Old deposit CLOSED. NEW FACILITY created with remaining principal.
     */
    public function withdraw_action()
    {
        $deposit_id = $this->input->post('deposit_id', TRUE);
        $deposit = $this->Fd_deposits_model->get_by_id($deposit_id);

        if (!$deposit || $deposit->status != 'ACTIVE') {
            $this->session->set_flashdata('error', 'Invalid deposit');
            redirect('Fixed_deposits/deposits');
        }

        $withdrawal_type = $this->input->post('withdrawal_type', TRUE);
        $withdraw_amount = floatval($this->input->post('withdraw_amount', TRUE));

        // Calculate current accrued interest
        $accrued_interest = calculate_accrued_interest($deposit);
        $current_quarter = get_current_quarter();

        // Check if early withdrawal (before maturity or before quarter end)
        $quarter_dates = get_quarter_dates($current_quarter['quarter'], $current_quarter['year']);
        $is_before_maturity = strtotime(date('Y-m-d')) < strtotime($deposit->maturity_date);
        $days_to_quarter_end = floor((strtotime($quarter_dates['end']) - strtotime(date('Y-m-d'))) / 86400);
        $is_before_quarter_end = $days_to_quarter_end > 7;

        // Penalty applies if before maturity OR before quarter end
        $penalty_amount = 0;
        if ($is_before_maturity || $is_before_quarter_end) {
            $penalty_amount = calculate_penalty($accrued_interest, $deposit->penalty_rate);
        }

        $net_interest = $accrued_interest - $penalty_amount;

        // WHT (Withholding Tax) @ 15%
        $wht_rate = 15;
        $wht_amount = $net_interest * ($wht_rate / 100);
        $net_interest_after_wht = $net_interest - $wht_amount;

        if ($withdrawal_type == 'FULL') {
            // ==================== FULL WITHDRAWAL ====================
            // Customer gets: Full Principal + Net Interest (after penalty and WHT)
            // Deposit is CLOSED

            $withdraw_amount = $deposit->current_principal;
            $total_payout = $withdraw_amount + $net_interest_after_wht;

            // Record penalty transaction if applicable
            if ($penalty_amount > 0) {
                $penalty_tx = array(
                    'deposit_id' => $deposit_id,
                    'transaction_ref' => $this->Fd_transactions_model->generate_ref(),
                    'transaction_type' => 'PENALTY',
                    'amount' => $penalty_amount,
                    'principal_before' => $deposit->current_principal,
                    'principal_after' => $deposit->current_principal,
                    'interest_before' => $accrued_interest,
                    'interest_after' => $net_interest,
                    'penalty_amount' => $penalty_amount,
                    'quarter' => $current_quarter['quarter'],
                    'year' => $current_quarter['year'],
                    'notes' => 'Early withdrawal penalty (' . $deposit->penalty_rate . '% on K ' . number_format($accrued_interest, 2) . ' interest)',
                    'created_by' => $this->session->userdata('user_id')
                );
                $this->Fd_transactions_model->insert($penalty_tx);
            }

            // Pay out net interest (after WHT)
            if ($net_interest > 0) {
                $interest_tx = array(
                    'deposit_id' => $deposit_id,
                    'transaction_ref' => $this->Fd_transactions_model->generate_ref(),
                    'transaction_type' => 'INTEREST_PAYMENT',
                    'amount' => $net_interest_after_wht,
                    'principal_before' => $deposit->current_principal,
                    'principal_after' => $deposit->current_principal,
                    'interest_before' => $net_interest,
                    'interest_after' => 0,
                    'wht_amount' => $wht_amount,
                    'quarter' => $current_quarter['quarter'],
                    'year' => $current_quarter['year'],
                    'notes' => 'Interest payment on full withdrawal | WHT @' . $wht_rate . '% = K ' . number_format($wht_amount, 2),
                    'created_by' => $this->session->userdata('user_id')
                );
                $this->Fd_transactions_model->insert($interest_tx);
            }

            // Principal withdrawal/closure transaction
            $withdraw_tx = array(
                'deposit_id' => $deposit_id,
                'transaction_ref' => $this->Fd_transactions_model->generate_ref(),
                'transaction_type' => 'CLOSURE',
                'amount' => $withdraw_amount,
                'principal_before' => $deposit->current_principal,
                'principal_after' => 0,
                'interest_before' => 0,
                'interest_after' => 0,
                'penalty_amount' => $penalty_amount,
                'quarter' => $current_quarter['quarter'],
                'year' => $current_quarter['year'],
                'notes' => $this->input->post('notes', TRUE) ?: 'Full principal withdrawal - deposit closed',
                'created_by' => $this->session->userdata('user_id')
            );
            $this->Fd_transactions_model->insert($withdraw_tx);

            // Close the deposit
            $this->Fd_deposits_model->update($deposit_id, array(
                'current_principal' => 0,
                'accrued_interest' => 0,
                'paid_interest' => $deposit->paid_interest + $net_interest,
                'status' => 'CLOSED'
            ));

            $message = 'Deposit CLOSED. Total payout: K ' . number_format($total_payout, 2);
            $message .= ' (Principal: K ' . number_format($withdraw_amount, 2);
            $message .= ' + Interest: K ' . number_format($net_interest_after_wht, 2);
            $message .= ' | WHT: K ' . number_format($wht_amount, 2);
            if ($penalty_amount > 0) {
                $message .= ' | Penalty: K ' . number_format($penalty_amount, 2);
            }
            $message .= ')';

            $this->session->set_flashdata('message', $message);
            redirect('Fixed_deposits/deposit_view/' . $deposit_id);

        } else {
            // ==================== PARTIAL WITHDRAWAL ====================
            // Customer gets: Requested Amount + Net Interest (after penalty)
            // Old deposit is CLOSED
            // NEW FACILITY created with remaining principal

            if ($withdraw_amount >= $deposit->current_principal) {
                $this->session->set_flashdata('error', 'Withdrawal amount must be less than principal. Use full withdrawal to close deposit.');
                redirect('Fixed_deposits/withdraw/' . $deposit_id);
            }

            $remaining_principal = $deposit->current_principal - $withdraw_amount;
            $total_payout = $withdraw_amount + $net_interest_after_wht;

            // Record penalty transaction if applicable
            if ($penalty_amount > 0) {
                $penalty_tx = array(
                    'deposit_id' => $deposit_id,
                    'transaction_ref' => $this->Fd_transactions_model->generate_ref(),
                    'transaction_type' => 'PENALTY',
                    'amount' => $penalty_amount,
                    'principal_before' => $deposit->current_principal,
                    'principal_after' => $deposit->current_principal,
                    'interest_before' => $accrued_interest,
                    'interest_after' => $net_interest,
                    'penalty_amount' => $penalty_amount,
                    'quarter' => $current_quarter['quarter'],
                    'year' => $current_quarter['year'],
                    'notes' => 'Early partial withdrawal penalty (' . $deposit->penalty_rate . '% on K ' . number_format($accrued_interest, 2) . ' interest)',
                    'created_by' => $this->session->userdata('user_id')
                );
                $this->Fd_transactions_model->insert($penalty_tx);
            }

            // Pay out net interest (after WHT)
            if ($net_interest > 0) {
                $interest_tx = array(
                    'deposit_id' => $deposit_id,
                    'transaction_ref' => $this->Fd_transactions_model->generate_ref(),
                    'transaction_type' => 'INTEREST_PAYMENT',
                    'amount' => $net_interest_after_wht,
                    'principal_before' => $deposit->current_principal,
                    'principal_after' => $deposit->current_principal,
                    'interest_before' => $net_interest,
                    'interest_after' => 0,
                    'wht_amount' => $wht_amount,
                    'quarter' => $current_quarter['quarter'],
                    'year' => $current_quarter['year'],
                    'notes' => 'Interest payment on partial withdrawal | WHT @' . $wht_rate . '% = K ' . number_format($wht_amount, 2),
                    'created_by' => $this->session->userdata('user_id')
                );
                $this->Fd_transactions_model->insert($interest_tx);
            }

            // Partial withdrawal transaction - closing old deposit
            $withdraw_tx = array(
                'deposit_id' => $deposit_id,
                'transaction_ref' => $this->Fd_transactions_model->generate_ref(),
                'transaction_type' => 'CLOSURE',
                'amount' => $withdraw_amount,
                'principal_before' => $deposit->current_principal,
                'principal_after' => 0,
                'interest_before' => 0,
                'interest_after' => 0,
                'penalty_amount' => $penalty_amount,
                'quarter' => $current_quarter['quarter'],
                'year' => $current_quarter['year'],
                'notes' => 'Partial withdrawal - K ' . number_format($remaining_principal, 2) . ' transferred to new facility',
                'created_by' => $this->session->userdata('user_id')
            );
            $this->Fd_transactions_model->insert($withdraw_tx);

            // Close the OLD deposit
            $this->Fd_deposits_model->update($deposit_id, array(
                'current_principal' => 0,
                'accrued_interest' => 0,
                'paid_interest' => $deposit->paid_interest + $net_interest,
                'status' => 'CLOSED'
            ));

            // ==================== CREATE NEW FACILITY ====================
            $new_start_date = date('Y-m-d');
            $new_maturity_date = calculate_maturity_date($new_start_date, $deposit->duration_months);

            $new_deposit_data = array(
                'deposit_number' => $this->Fd_deposits_model->generate_deposit_number(),
                'customer_id' => $deposit->customer_id,
                'principal_amount' => $remaining_principal,
                'current_principal' => $remaining_principal,
                'interest_rate' => $deposit->interest_rate,
                'penalty_rate' => $deposit->penalty_rate,
                'start_date' => $new_start_date,
                'maturity_date' => $new_maturity_date,
                'duration_months' => $deposit->duration_months,
                'payment_option' => $deposit->payment_option,
                'accrued_interest' => 0,
                'paid_interest' => 0,
                'status' => 'ACTIVE',
                'notes' => 'New facility created from partial withdrawal of ' . $deposit->deposit_number . '. Original principal: K ' . number_format($deposit->current_principal, 2) . ', Withdrawn: K ' . number_format($withdraw_amount, 2),
                'created_by' => $this->session->userdata('user_id')
            );

            $new_deposit_id = $this->Fd_deposits_model->insert($new_deposit_data);

            // Generate quarterly schedule for new deposit if QUARTERLY
            if ($deposit->payment_option == 'QUARTERLY') {
                $this->Fd_schedules_model->generate_quarterly_schedule(
                    $new_deposit_id,
                    $new_start_date,
                    $new_maturity_date,
                    $remaining_principal,
                    $deposit->interest_rate
                );
            }

            // Record initial deposit transaction for new facility
            $new_deposit_tx = array(
                'deposit_id' => $new_deposit_id,
                'transaction_ref' => $this->Fd_transactions_model->generate_ref(),
                'transaction_type' => 'DEPOSIT',
                'amount' => $remaining_principal,
                'principal_before' => 0,
                'principal_after' => $remaining_principal,
                'interest_before' => 0,
                'interest_after' => 0,
                'quarter' => $current_quarter['quarter'],
                'year' => $current_quarter['year'],
                'notes' => 'New facility from partial withdrawal of ' . $deposit->deposit_number,
                'created_by' => $this->session->userdata('user_id')
            );
            $this->Fd_transactions_model->insert($new_deposit_tx);

            $message = 'Partial withdrawal processed successfully!<br>';
            $message .= '<strong>Payout:</strong> K ' . number_format($total_payout, 2);
            $message .= ' (K ' . number_format($withdraw_amount, 2) . ' principal + K ' . number_format($net_interest_after_wht, 2) . ' interest';
            $message .= ' | WHT: K ' . number_format($wht_amount, 2);
            if ($penalty_amount > 0) {
                $message .= ' | Penalty: K ' . number_format($penalty_amount, 2);
            }
            $message .= ')<br>';
            $message .= '<strong>New Facility:</strong> ' . $new_deposit_data['deposit_number'] . ' created with K ' . number_format($remaining_principal, 2);

            $this->session->set_flashdata('message', $message);
            redirect('Fixed_deposits/deposit_view/' . $new_deposit_id);
        }
    }

    /**
     * Pay interest form
     * Interest can ONLY be withdrawn WITHOUT penalty at quarter end
     * Before quarter end = penalty applies
     */
    public function pay_interest($deposit_id)
    {
        $deposit = $this->Fd_deposits_model->get_deposit_with_customer($deposit_id);
        if (!$deposit || $deposit->status != 'ACTIVE') {
            $this->session->set_flashdata('error', 'Invalid deposit or not active');
            redirect('Fixed_deposits/deposits');
        }

        $data['deposit'] = $deposit;
        $data['current_accrued'] = calculate_accrued_interest($deposit);

        // Get current quarter info
        $current_quarter = get_current_quarter();
        $quarter_dates = get_quarter_dates($current_quarter['quarter'], $current_quarter['year']);
        $data['quarter_end'] = $quarter_dates['end'];
        $data['current_quarter'] = $current_quarter;

        // Check if we're at quarter end (within 7 days of quarter end or past it)
        $days_to_quarter_end = floor((strtotime($quarter_dates['end']) - strtotime(date('Y-m-d'))) / 86400);
        $data['is_quarter_end'] = ($days_to_quarter_end <= 7 && $days_to_quarter_end >= -7);
        $data['days_to_quarter_end'] = $days_to_quarter_end;

        // Calculate penalty if not at quarter end
        $data['penalty_preview'] = 0;
        $data['interest_after_penalty'] = $data['current_accrued'];
        if (!$data['is_quarter_end'] && $data['current_accrued'] > 0) {
            $data['penalty_preview'] = calculate_penalty($data['current_accrued'], $deposit->penalty_rate);
            $data['interest_after_penalty'] = $data['current_accrued'] - $data['penalty_preview'];
        }

        // WHT (Withholding Tax) @ 15%
        $data['wht_rate'] = 15;
        $data['wht_amount'] = $data['interest_after_penalty'] * ($data['wht_rate'] / 100);
        $data['net_interest'] = $data['interest_after_penalty'] - $data['wht_amount'];

        // Get pending schedules for QUARTERLY payment option
        if ($deposit->payment_option == 'QUARTERLY') {
            $data['pending_schedules'] = $this->Fd_schedules_model->get_pending_payments($deposit_id);
        } else {
            $data['pending_schedules'] = array();
        }

        $data['page_title'] = 'Pay Interest - ' . $deposit->deposit_number;
        $data['action'] = site_url('Fixed_deposits/pay_interest_action');

        $this->load->view('admin/header');
        $this->load->view('fixed_deposits/pay_interest_form', $data);
        $this->load->view('admin/footer');
    }

    /**
     * Process interest payment
     * Interest withdrawal: NO penalty at quarter end, PENALTY if before quarter end
     */
    public function pay_interest_action()
    {
        $deposit_id = $this->input->post('deposit_id', TRUE);
        $schedule_id = $this->input->post('schedule_id', TRUE);
        $payment_amount = floatval($this->input->post('payment_amount', TRUE));

        $deposit = $this->Fd_deposits_model->get_by_id($deposit_id);

        if (!$deposit) {
            $this->session->set_flashdata('error', 'Invalid deposit');
            redirect('Fixed_deposits/deposits');
        }

        // Calculate current accrued interest
        $current_accrued = calculate_accrued_interest($deposit);

        $current_quarter = get_current_quarter();
        $quarter_dates = get_quarter_dates($current_quarter['quarter'], $current_quarter['year']);

        // Check if at quarter end (within 7 days)
        $days_to_quarter_end = floor((strtotime($quarter_dates['end']) - strtotime(date('Y-m-d'))) / 86400);
        $is_quarter_end = ($days_to_quarter_end <= 7 && $days_to_quarter_end >= -7);

        // Calculate penalty if NOT at quarter end
        $penalty_amount = 0;
        if (!$is_quarter_end && $current_accrued > 0) {
            $penalty_amount = calculate_penalty($current_accrued, $deposit->penalty_rate);
        }

        $net_interest = $current_accrued - $penalty_amount;

        // WHT (Withholding Tax) @ 15%
        $wht_rate = 15;
        $wht_amount = $net_interest * ($wht_rate / 100);
        $net_after_wht = $net_interest - $wht_amount;

        if ($payment_amount > $net_after_wht) {
            $this->session->set_flashdata('error', 'Payment amount exceeds available interest after penalty and WHT');
            redirect('Fixed_deposits/pay_interest/' . $deposit_id);
        }

        $notes = $is_quarter_end ? 'Quarterly interest payment (no penalty)' : 'Interest withdrawal with penalty';

        // If there's a schedule selected, update it
        if ($schedule_id) {
            $schedule = $this->Fd_schedules_model->get_by_id($schedule_id);
            if ($schedule) {
                $notes = 'Quarterly interest payment for Q' . $schedule->quarter . ' ' . $schedule->year;
                if (!$is_quarter_end) {
                    $notes .= ' (early withdrawal - penalty applied)';
                }

                // Update schedule
                $this->Fd_schedules_model->mark_paid($schedule_id, $payment_amount, $penalty_amount);
            }
        }

        // Record penalty transaction if applicable
        if ($penalty_amount > 0) {
            $penalty_tx = array(
                'deposit_id' => $deposit_id,
                'transaction_ref' => $this->Fd_transactions_model->generate_ref(),
                'transaction_type' => 'PENALTY',
                'amount' => $penalty_amount,
                'principal_before' => $deposit->current_principal,
                'principal_after' => $deposit->current_principal,
                'interest_before' => $current_accrued,
                'interest_after' => $net_interest,
                'penalty_amount' => $penalty_amount,
                'quarter' => $current_quarter['quarter'],
                'year' => $current_quarter['year'],
                'notes' => 'Early interest withdrawal penalty (' . $deposit->penalty_rate . '% on accrued interest)',
                'created_by' => $this->session->userdata('user_id')
            );
            $this->Fd_transactions_model->insert($penalty_tx);
        }

        // Record interest payment transaction (amount is net after WHT)
        $transaction_data = array(
            'deposit_id' => $deposit_id,
            'transaction_ref' => $this->Fd_transactions_model->generate_ref(),
            'transaction_type' => 'INTEREST_PAYMENT',
            'amount' => $payment_amount,
            'principal_before' => $deposit->current_principal,
            'principal_after' => $deposit->current_principal,
            'interest_before' => $net_interest,
            'interest_after' => $net_interest - $payment_amount - $wht_amount,
            'penalty_amount' => $penalty_amount,
            'wht_amount' => $wht_amount,
            'quarter' => $current_quarter['quarter'],
            'year' => $current_quarter['year'],
            'notes' => $notes . ' | WHT @' . $wht_rate . '% = K ' . number_format($wht_amount, 2),
            'created_by' => $this->session->userdata('user_id')
        );
        $this->Fd_transactions_model->insert($transaction_data);

        // Update deposit (paid_interest tracks gross interest consumed, not net payout)
        $this->Fd_deposits_model->update($deposit_id, array(
            'accrued_interest' => 0,
            'paid_interest' => $deposit->paid_interest + $payment_amount + $wht_amount,
            'last_accrual_date' => date('Y-m-d')
        ));

        $message = 'Interest payment of K ' . number_format($payment_amount, 2) . ' processed';
        $message .= ' (WHT @' . $wht_rate . '%: K ' . number_format($wht_amount, 2) . ')';
        if ($penalty_amount > 0) {
            $message .= ' (Penalty: K ' . number_format($penalty_amount, 2) . ')';
        }
        $this->session->set_flashdata('message', $message);
        redirect('Fixed_deposits/deposit_view/' . $deposit_id);
    }

    /**
     * Top-up deposit form
     */
    public function top_up($deposit_id)
    {
        $deposit = $this->Fd_deposits_model->get_deposit_with_customer($deposit_id);
        if (!$deposit || $deposit->status != 'ACTIVE') {
            $this->session->set_flashdata('error', 'Invalid deposit or not active');
            redirect('Fixed_deposits/deposits');
        }

        $data['deposit'] = $deposit;
        $data['page_title'] = 'Top-up Deposit - ' . $deposit->deposit_number;
        $data['action'] = site_url('Fixed_deposits/top_up_action');

        $this->load->view('admin/header');
        $this->load->view('fixed_deposits/top_up_form', $data);
        $this->load->view('admin/footer');
    }

    /**
     * Process top-up
     */
    public function top_up_action()
    {
        $deposit_id = $this->input->post('deposit_id', TRUE);
        $top_up_amount = floatval($this->input->post('top_up_amount', TRUE));

        $deposit = $this->Fd_deposits_model->get_by_id($deposit_id);
        if (!$deposit || $deposit->status != 'ACTIVE') {
            $this->session->set_flashdata('error', 'Invalid deposit');
            redirect('Fixed_deposits/deposits');
        }

        $new_principal = $deposit->current_principal + $top_up_amount;
        $current_quarter = get_current_quarter();

        // Record top-up transaction
        $transaction_data = array(
            'deposit_id' => $deposit_id,
            'transaction_ref' => $this->Fd_transactions_model->generate_ref(),
            'transaction_type' => 'TOP_UP',
            'amount' => $top_up_amount,
            'principal_before' => $deposit->current_principal,
            'principal_after' => $new_principal,
            'interest_before' => $deposit->accrued_interest,
            'interest_after' => $deposit->accrued_interest,
            'quarter' => $current_quarter['quarter'],
            'year' => $current_quarter['year'],
            'notes' => $this->input->post('notes', TRUE),
            'created_by' => $this->session->userdata('user_id')
        );
        $this->Fd_transactions_model->insert($transaction_data);

        // Update deposit
        $this->Fd_deposits_model->update($deposit_id, array(
            'current_principal' => $new_principal
        ));

        // Recalculate future schedules
        $this->Fd_schedules_model->recalculate_schedule($deposit_id, $new_principal, $deposit->interest_rate);

        $this->session->set_flashdata('message', 'Top-up of K ' . number_format($top_up_amount, 2) . ' added successfully');
        redirect('Fixed_deposits/deposit_view/' . $deposit_id);
    }

    /**
     * Merge deposits form
     */
    public function merge_deposits($customer_id)
    {
        $customer = $this->Fd_customers_model->get_by_id($customer_id);
        if (!$customer) {
            $this->session->set_flashdata('error', 'Customer not found');
            redirect('Fixed_deposits/customers');
        }

        $deposits = $this->Fd_deposits_model->get_active_by_customer($customer_id);
        if (count($deposits) < 2) {
            $this->session->set_flashdata('error', 'Customer must have at least 2 active deposits to merge');
            redirect('Fixed_deposits/customer_view/' . $customer_id);
        }

        $data['customer'] = $customer;
        $data['deposits'] = $deposits;
        $data['page_title'] = 'Merge Deposits';
        $data['action'] = site_url('Fixed_deposits/merge_action');

        $this->load->view('admin/header');
        $this->load->view('fixed_deposits/merge_form', $data);
        $this->load->view('admin/footer');
    }

    /**
     * Process deposit merge
     */
    public function merge_action()
    {
        $customer_id = $this->input->post('customer_id', TRUE);
        $deposit_ids = $this->input->post('deposit_ids', TRUE);
        $new_interest_rate = floatval($this->input->post('interest_rate', TRUE));
        $new_duration = intval($this->input->post('duration_months', TRUE));
        $new_penalty_rate = floatval($this->input->post('penalty_rate', TRUE));

        if (!is_array($deposit_ids) || count($deposit_ids) < 2) {
            $this->session->set_flashdata('error', 'Please select at least 2 deposits to merge');
            redirect('Fixed_deposits/merge_deposits/' . $customer_id);
        }

        $total_principal = 0;
        $total_interest = 0;
        $current_quarter = get_current_quarter();

        // Process each deposit being merged
        foreach ($deposit_ids as $dep_id) {
            $deposit = $this->Fd_deposits_model->get_by_id($dep_id);
            if ($deposit && $deposit->status == 'ACTIVE') {
                $accrued = calculate_accrued_interest($deposit);
                $total_principal += $deposit->current_principal;
                $total_interest += $accrued;

                // Record MERGE_OUT transaction
                $merge_out_tx = array(
                    'deposit_id' => $dep_id,
                    'transaction_ref' => $this->Fd_transactions_model->generate_ref(),
                    'transaction_type' => 'MERGE_OUT',
                    'amount' => $deposit->current_principal + $accrued,
                    'principal_before' => $deposit->current_principal,
                    'principal_after' => 0,
                    'interest_before' => $accrued,
                    'interest_after' => 0,
                    'quarter' => $current_quarter['quarter'],
                    'year' => $current_quarter['year'],
                    'notes' => 'Merged to new deposit',
                    'created_by' => $this->session->userdata('user_id')
                );
                $this->Fd_transactions_model->insert($merge_out_tx);
            }
        }

        // Create new merged deposit
        $new_principal = $total_principal + $total_interest;
        $start_date = date('Y-m-d');
        $maturity_date = calculate_maturity_date($start_date, $new_duration);

        $new_deposit_data = array(
            'deposit_number' => $this->Fd_deposits_model->generate_deposit_number(),
            'customer_id' => $customer_id,
            'principal_amount' => $new_principal,
            'current_principal' => $new_principal,
            'interest_rate' => $new_interest_rate,
            'penalty_rate' => $new_penalty_rate,
            'start_date' => $start_date,
            'maturity_date' => $maturity_date,
            'duration_months' => $new_duration,
            'payment_option' => 'QUARTERLY',
            'accrued_interest' => 0,
            'paid_interest' => 0,
            'status' => 'ACTIVE',
            'notes' => 'Merged from deposits: ' . implode(', ', $deposit_ids),
            'created_by' => $this->session->userdata('user_id')
        );

        $new_deposit_id = $this->Fd_deposits_model->insert($new_deposit_data);

        // Generate schedule for new deposit
        $this->Fd_schedules_model->generate_quarterly_schedule(
            $new_deposit_id,
            $start_date,
            $maturity_date,
            $new_principal,
            $new_interest_rate
        );

        // Record MERGE_IN transaction
        $merge_in_tx = array(
            'deposit_id' => $new_deposit_id,
            'transaction_ref' => $this->Fd_transactions_model->generate_ref(),
            'transaction_type' => 'MERGE_IN',
            'amount' => $new_principal,
            'principal_before' => 0,
            'principal_after' => $new_principal,
            'interest_before' => 0,
            'interest_after' => 0,
            'quarter' => $current_quarter['quarter'],
            'year' => $current_quarter['year'],
            'notes' => 'Merged from ' . count($deposit_ids) . ' deposits',
            'created_by' => $this->session->userdata('user_id')
        );
        $this->Fd_transactions_model->insert($merge_in_tx);

        // Mark old deposits as MERGED
        foreach ($deposit_ids as $dep_id) {
            $this->Fd_deposits_model->update($dep_id, array(
                'status' => 'MERGED',
                'merged_to_id' => $new_deposit_id,
                'current_principal' => 0,
                'accrued_interest' => 0
            ));
        }

        $this->session->set_flashdata('message', 'Deposits merged successfully. New deposit: ' . $new_deposit_data['deposit_number']);
        redirect('Fixed_deposits/deposit_view/' . $new_deposit_id);
    }

    /**
     * Close/mature deposit
     */
    public function close_deposit($id)
    {
        $deposit = $this->Fd_deposits_model->get_deposit_with_customer($id);
        if (!$deposit || $deposit->status != 'ACTIVE') {
            $this->session->set_flashdata('error', 'Invalid deposit or already closed');
            redirect('Fixed_deposits/deposits');
        }

        $data['deposit'] = $deposit;
        $data['current_accrued'] = calculate_accrued_interest($deposit);
        $data['page_title'] = 'Close Deposit - ' . $deposit->deposit_number;
        $data['action'] = site_url('Fixed_deposits/close_deposit_action');

        // Check if matured
        $data['is_matured'] = strtotime(date('Y-m-d')) >= strtotime($deposit->maturity_date);
        $data['penalty_preview'] = $data['is_matured'] ? 0 : calculate_penalty($data['current_accrued'], $deposit->penalty_rate);

        // WHT (Withholding Tax) @ 15%
        $interest_after_penalty = $data['current_accrued'] - $data['penalty_preview'];
        $data['wht_rate'] = 15;
        $data['wht_amount'] = $interest_after_penalty * ($data['wht_rate'] / 100);
        $data['net_interest'] = $interest_after_penalty - $data['wht_amount'];

        $this->load->view('admin/header');
        $this->load->view('fixed_deposits/close_form', $data);
        $this->load->view('admin/footer');
    }

    /**
     * Process deposit closure
     */
    public function close_deposit_action()
    {
        $deposit_id = $this->input->post('deposit_id', TRUE);
        $deposit = $this->Fd_deposits_model->get_by_id($deposit_id);

        if (!$deposit || $deposit->status != 'ACTIVE') {
            $this->session->set_flashdata('error', 'Invalid deposit');
            redirect('Fixed_deposits/deposits');
        }

        // Use withdraw action with full withdrawal
        $_POST['withdrawal_type'] = 'FULL';
        $_POST['withdraw_amount'] = $deposit->current_principal;
        $_POST['apply_penalty'] = strtotime(date('Y-m-d')) < strtotime($deposit->maturity_date) ? '1' : '0';

        $this->withdraw_action();
    }

    // ==================== REPORTS ====================

    /**
     * Reports page
     */
    public function report()
    {
        $data['page_title'] = 'Fixed Deposits Report';

        $from_date = $this->input->get('from_date', TRUE);
        $to_date = $this->input->get('to_date', TRUE);
        $status = $this->input->get('status', TRUE);

        // Default to current month
        if (!$from_date) {
            $from_date = date('Y-m-01');
        }
        if (!$to_date) {
            $to_date = date('Y-m-d');
        }

        $data['from_date'] = $from_date;
        $data['to_date'] = $to_date;
        $data['status'] = $status;

        // Get deposits
        $this->db->select('d.*, c.customer_number, c.first_name, c.last_name, c.phone_number');
        $this->db->from('fd_deposits d');
        $this->db->join('fd_customers c', 'c.id = d.customer_id', 'left');

        if ($status) {
            $this->db->where('d.status', $status);
        }
        if ($from_date) {
            $this->db->where('d.start_date >=', $from_date);
        }
        if ($to_date) {
            $this->db->where('d.start_date <=', $to_date);
        }

        $this->db->order_by('d.start_date', 'DESC');
        $data['deposits'] = $this->db->get()->result();

        // Calculate totals
        $data['total_principal'] = 0;
        $data['total_accrued'] = 0;
        foreach ($data['deposits'] as $deposit) {
            $data['total_principal'] += $deposit->current_principal;
            if ($deposit->status == 'ACTIVE') {
                $data['total_accrued'] += calculate_accrued_interest($deposit);
            }
        }

        $this->load->view('admin/header');
        $this->load->view('fixed_deposits/report', $data);
        $this->load->view('admin/footer');
    }

    // ==================== API ENDPOINTS ====================

    /**
     * Calculate interest API (AJAX)
     */
    public function calculate_interest_ajax()
    {
        $deposit_id = $this->input->post('deposit_id', TRUE);
        $deposit = $this->Fd_deposits_model->get_by_id($deposit_id);

        if (!$deposit) {
            echo json_encode(array('error' => 'Deposit not found'));
            return;
        }

        $accrued = calculate_accrued_interest($deposit);
        $penalty = calculate_penalty($accrued, $deposit->penalty_rate);

        echo json_encode(array(
            'accrued_interest' => $accrued,
            'penalty_amount' => $penalty,
            'net_interest' => $accrued - $penalty,
            'current_principal' => $deposit->current_principal
        ));
    }

    /**
     * Search customers API (AJAX)
     */
    public function search_customers_ajax()
    {
        $term = $this->input->get('term', TRUE);
        $customers = $this->Fd_customers_model->search($term);

        $results = array();
        foreach ($customers as $customer) {
            $results[] = array(
                'id' => $customer->id,
                'text' => $customer->customer_number . ' - ' . $customer->first_name . ' ' . $customer->last_name
            );
        }

        echo json_encode(array('results' => $results));
    }

    /**
     * Run daily accrual (for cron job)
     */
    public function run_daily_accrual()
    {
        $count = $this->Fd_accruals_model->run_daily_accrual();
        echo "Daily accrual completed. Processed {$count} deposits.";
    }

    /**
     * Export customer profile as PDF
     */
    public function customer_profile_pdf($id)
    {
        $customer = $this->Fd_customers_model->get_by_id($id);
        if (!$customer) {
            $this->session->set_flashdata('error', 'Customer not found');
            redirect('Fixed_deposits/customers');
        }

        $deposits = $this->Fd_deposits_model->get_by_customer($id);

        // Get company settings
        $settings = $this->db->get_where('settings', array('settings_id' => 1))->row();

        // Clean company address
        $company_address = $settings->address ?? '';
        $company_address = preg_replace('/<\/p>\s*<p>/i', "\n", $company_address);
        $company_address = preg_replace('/<br\s*\/?>/i', "\n", $company_address);
        $company_address = strip_tags($company_address);

        // Calculate totals
        $total_principal = 0;
        $total_accrued = 0;
        foreach ($deposits as $deposit) {
            if ($deposit->status == 'ACTIVE') {
                $total_principal += $deposit->current_principal;
                $total_accrued += calculate_accrued_interest($deposit);
            }
        }

        $data['customer'] = $customer;
        $data['deposits'] = $deposits;
        $data['settings'] = $settings;
        $data['company_address'] = trim($company_address);
        $data['total_principal'] = $total_principal;
        $data['total_accrued'] = $total_accrued;
        $data['current_date'] = date('d/m/Y');

        $this->load->view('fixed_deposits/customer_profile_pdf', $data);
    }

    /**
     * Generate Placement Certificate / Statement
     */
    public function placement_certificate($deposit_id)
    {
        $deposit = $this->Fd_deposits_model->get_deposit_with_customer($deposit_id);
        if (!$deposit) {
            $this->session->set_flashdata('error', 'Deposit not found');
            redirect('Fixed_deposits/deposits');
        }

        // Get company settings
        $settings = $this->db->get_where('settings', array('settings_id' => 1))->row();

        // Calculate interest details
        $principal = $deposit->principal_amount;
        $rate = $deposit->interest_rate;
        $duration_months = $deposit->duration_months;

        // Annual interest (before WHT)
        $annual_interest = $principal * ($rate / 100);

        // Prorated interest for actual duration
        $interest_before_tax = ($annual_interest / 12) * $duration_months;

        // WHT @ 15%
        $wht_rate = 15;
        $wht_amount = $interest_before_tax * ($wht_rate / 100);

        // Net interest after WHT
        $net_interest = $interest_before_tax - $wht_amount;

        // Quarterly payout (if quarterly option)
        $quarterly_payout = 0;
        if ($deposit->payment_option == 'QUARTERLY') {
            $quarterly_payout = $net_interest / ($duration_months / 3);
        }

        // Payout at maturity
        $payout_at_maturity = $principal + $net_interest;

        $data['deposit'] = $deposit;
        $data['settings'] = $settings;
        $data['interest_before_tax'] = $interest_before_tax;
        $data['wht_rate'] = $wht_rate;
        $data['wht_amount'] = $wht_amount;
        $data['net_interest'] = $net_interest;
        $data['quarterly_payout'] = $quarterly_payout;
        $data['payout_at_maturity'] = $payout_at_maturity;
        $data['current_date'] = date('d/m/Y');

        // Clean company address
        $company_address = $settings->address ?? '';
        $company_address = preg_replace('/<\/p>\s*<p>/i', "\n", $company_address);
        $company_address = preg_replace('/<br\s*\/?>/i', "\n", $company_address);
        $company_address = strip_tags($company_address);
        $data['company_address'] = trim($company_address);

        $this->load->view('fixed_deposits/placement_certificate', $data);
    }

    /**
     * Export Placement Certificate as Excel
     */
    public function placement_certificate_excel($deposit_id)
    {
        $this->load->helper('exportexcel');

        $deposit = $this->Fd_deposits_model->get_deposit_with_customer($deposit_id);
        if (!$deposit) {
            $this->session->set_flashdata('error', 'Deposit not found');
            redirect('Fixed_deposits/deposits');
        }

        // Get company settings
        $settings = $this->db->get_where('settings', array('settings_id' => 1))->row();

        // Calculate interest details
        $principal = $deposit->principal_amount;
        $rate = $deposit->interest_rate;
        $duration_months = $deposit->duration_months;

        $annual_interest = $principal * ($rate / 100);
        $interest_before_tax = ($annual_interest / 12) * $duration_months;
        $wht_rate = 15;
        $wht_amount = $interest_before_tax * ($wht_rate / 100);
        $net_interest = $interest_before_tax - $wht_amount;
        $quarterly_payout = ($deposit->payment_option == 'QUARTERLY') ? $net_interest / ($duration_months / 3) : 0;
        $payout_at_maturity = $principal + $net_interest;

        // Clean company address
        $company_address = $settings->address ?? '';
        $company_address = preg_replace('/<\/p>\s*<p>/i', "\n", $company_address);
        $company_address = preg_replace('/<br\s*\/?>/i', "\n", $company_address);
        $company_address = strip_tags($company_address);
        $address_lines = explode("\n", trim($company_address));

        $currency = $deposit->currency ?? 'ZMW';
        $filename = "Placement_Certificate_" . $deposit->deposit_number . "_" . date('Y-m-d') . ".xls";

        xlsHeaders($filename);
        xlsBOF();

        $row = 0;

        // Title
        xlsWriteLabel($row, 0, "PLACEMENT SHEET AS AT " . strtoupper(date('d/m/Y')));
        $row += 3;

        // Company Info
        xlsWriteLabel($row, 0, $settings->company_name ?? 'FUNDIT Capital Solutions Limited');
        $row++;
        foreach ($address_lines as $line) {
            if (trim($line)) {
                xlsWriteLabel($row, 0, trim($line));
                $row++;
            }
        }
        $row += 2;

        // Customer
        xlsWriteLabel($row, 0, "Attention: " . $deposit->first_name . " " . $deposit->last_name);
        $row += 2;

        xlsWriteLabel($row, 0, "Fixed Placement Certificate");
        $row += 2;

        // Headers
        xlsWriteLabel($row, 0, "Placement Date");
        xlsWriteLabel($row, 1, "Currency");
        xlsWriteLabel($row, 2, "Placement Amount");
        xlsWriteLabel($row, 3, "Duration");
        xlsWriteLabel($row, 4, "Interest Rate");
        xlsWriteLabel($row, 5, "Interest Before Tax");
        xlsWriteLabel($row, 6, "Net Interest (WHT @ " . $wht_rate . "%)");
        xlsWriteLabel($row, 7, "Quarterly Payout");
        xlsWriteLabel($row, 8, "Payout at Maturity");
        $row++;

        // Data
        xlsWriteLabel($row, 0, date('d-m-y', strtotime($deposit->start_date)));
        xlsWriteLabel($row, 1, $currency);
        xlsWriteNumber($row, 2, $principal);
        xlsWriteLabel($row, 3, $duration_months . " Months");
        xlsWriteLabel($row, 4, number_format($rate, 2) . "%");
        xlsWriteNumber($row, 5, $interest_before_tax);
        xlsWriteNumber($row, 6, $net_interest);
        xlsWriteNumber($row, 7, $quarterly_payout);
        xlsWriteNumber($row, 8, $payout_at_maturity);
        $row += 4;

        // Signature
        xlsWriteLabel($row, 0, "CHIEF OPERATIONS OFFICER");

        xlsEOF();
        exit;
    }

    /**
     * Search FD customers for linking (AJAX)
     */
    public function search_fd_customers()
    {
        header('Content-Type: application/json');
        $term = $this->input->get('term', TRUE);
        if (empty($term)) {
            echo json_encode(array('results' => array()));
            return;
        }

        $this->db->select('id, customer_number, first_name, last_name, email, phone_number, personal_linkage');
        $this->db->group_start();
        $this->db->like('customer_number', $term);
        $this->db->or_like('first_name', $term);
        $this->db->or_like('last_name', $term);
        $this->db->or_like('email', $term);
        $this->db->or_like('phone_number', $term);
        $this->db->group_end();
        $this->db->where('status', 'ACTIVE');
        $this->db->limit(10);
        $customers = $this->db->get('fd_customers')->result();

        $results = array();
        foreach ($customers as $c) {
            $results[] = array(
                'id' => $c->id,
                'customer_number' => $c->customer_number,
                'name' => $c->first_name . ' ' . $c->last_name,
                'email' => $c->email,
                'phone' => $c->phone_number,
                'already_linked' => !empty($c->personal_linkage)
            );
        }

        echo json_encode(array('results' => $results));
    }

    /**
     * Link an individual customer to an FD customer (AJAX)
     */
    public function link_individual()
    {
        header('Content-Type: application/json');
        $fd_customer_id = $this->input->post('fd_customer_id', TRUE);
        $individual_id = $this->input->post('individual_id', TRUE);

        if (empty($fd_customer_id) || empty($individual_id)) {
            echo json_encode(array('status' => 'error', 'message' => 'Missing required fields'));
            return;
        }

        // Check FD customer exists
        $fd_customer = $this->Fd_customers_model->get_by_id($fd_customer_id);
        if (!$fd_customer) {
            echo json_encode(array('status' => 'error', 'message' => 'FD customer not found'));
            return;
        }

        // Check individual customer exists
        $individual = get_by_id('individual_customers', 'id', $individual_id);
        if (!$individual) {
            echo json_encode(array('status' => 'error', 'message' => 'Individual customer not found'));
            return;
        }

        // Check if this FD customer is already linked to another individual
        if (!empty($fd_customer->personal_linkage) && $fd_customer->personal_linkage != $individual_id) {
            echo json_encode(array('status' => 'error', 'message' => 'This FD customer is already linked to another individual customer'));
            return;
        }

        // Update the linkage
        $this->Fd_customers_model->update($fd_customer_id, array('personal_linkage' => $individual_id));

        echo json_encode(array(
            'status' => 'success',
            'message' => 'FD customer linked successfully',
            'fd_customer' => array(
                'id' => $fd_customer->id,
                'customer_number' => $fd_customer->customer_number,
                'name' => $fd_customer->first_name . ' ' . $fd_customer->last_name
            )
        ));
    }

    /**
     * Unlink an individual customer from an FD customer (AJAX)
     */
    public function unlink_individual()
    {
        header('Content-Type: application/json');
        $fd_customer_id = $this->input->post('fd_customer_id', TRUE);

        if (empty($fd_customer_id)) {
            echo json_encode(array('status' => 'error', 'message' => 'Missing FD customer ID'));
            return;
        }

        $fd_customer = $this->Fd_customers_model->get_by_id($fd_customer_id);
        if (!$fd_customer) {
            echo json_encode(array('status' => 'error', 'message' => 'FD customer not found'));
            return;
        }

        $this->Fd_customers_model->update($fd_customer_id, array('personal_linkage' => NULL));

        echo json_encode(array('status' => 'success', 'message' => 'FD customer unlinked successfully'));
    }

    /**
     * Get FD customer linked to an individual (AJAX)
     */
    public function get_linked_fd($individual_id)
    {
        header('Content-Type: application/json');
        $fd_customer = $this->Fd_customers_model->get_by_personal_linkage($individual_id);

        if ($fd_customer) {
            echo json_encode(array(
                'status' => 'success',
                'linked' => true,
                'fd_customer' => array(
                    'id' => $fd_customer->id,
                    'customer_number' => $fd_customer->customer_number,
                    'name' => $fd_customer->first_name . ' ' . $fd_customer->last_name,
                    'email' => $fd_customer->email,
                    'phone' => $fd_customer->phone_number
                )
            ));
        } else {
            echo json_encode(array('status' => 'success', 'linked' => false));
        }
    }
}
