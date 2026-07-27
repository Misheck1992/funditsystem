<?php

/**
 * Check if user has access to a specific controller/method
 * @param string $method The controller/method path (e.g., 'Individual_customers/edit')
 * @return bool True if user has access, false otherwise
 */
function has_access($method) {
    $ci =& get_instance();
    $ci->load->library('session');
    $ci->load->database();

    $access = $ci->session->userdata('access');

    if (empty($access)) {
        return false;
    }

    // Get the menu item ID for this method (case-insensitive)
    $menu_item = $ci->db->query("SELECT id FROM menuitems WHERE LOWER(method) = LOWER(?) LIMIT 1", array($method))->row();

    if (empty($menu_item)) {
        return false;
    }

    // Check if user has access to this menu item
    foreach ($access as $r) {
        if ($r->controllerid == $menu_item->id) {
            return true;
        }
    }

    return false;
}

function get_all($id){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM $id ";
	return $query = $ci->db->query($sql)->result();


}function get_all_order($id, $order_field){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM $id ORDER BY $order_field DESC";
	return $query = $ci->db->query($sql)->result();


}


//get all shareholders
function get_all_shareholders($id){


    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM corporate_shareholders INNER JOIN shareholders ON shareholders.id = corporate_shareholders.shareholder_id WHERE corporate_shareholders.corporate_id = '$id'";
    return $query = $ci->db->query($sql)->result();


}


function get_all_where($table, $where, $order_by = null, $order_direction = 'ASC') {
    $ci =& get_instance();
    $ci->load->database();

    $sql = "SELECT * FROM $table WHERE $where";

    // If order by parameters are provided, add them to the SQL query
    if ($order_by !== null) {
        $sql .= " ORDER BY $order_by $order_direction";
    }

    // Execute the SQL query and return the results
    return $ci->db->query($sql)->result();
}
function get_active_loan_products(){


    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM  loan_products ";
    return $query = $ci->db->query($sql)->result();


}

function delete_payments(){


    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM  loan_products ";
    return $query = $ci->db->query($sql)->result();


}


function  get_user_loan_individual($id){


    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *
FROM loan
WHERE loan_customer = '$id'
AND customer_type = 'individual' ";
    return $query = $ci->db->query($sql)->result();


}
function  get_all_un_paid_loans(){


    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT loan.*, payement_schedules.* FROM loan INNER JOIN ( SELECT loan_id, MIN(payment_number) AS min_payment_number FROM payement_schedules WHERE status = 'Not paid' GROUP BY loan_id ) AS first_payment_schedule ON loan.loan_id = first_payment_schedule.loan_id AND loan.loan_status = 'active' INNER JOIN payement_schedules ON loan.loan_id = payement_schedules.loan_id AND first_payment_schedule.min_payment_number = payement_schedules.payment_number";
    return $query = $ci->db->query($sql)->result();


}

function  get_all_full_un_paid_loans(){


    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT loan.* FROM loan JOIN payement_schedules ON loan.loan_id = payement_schedules.loan_id WHERE loan.loan_status = 'active' AND payement_schedules.status = 'NOT PAID' AND payement_schedules.payment_number = 1";
    return $query = $ci->db->query($sql)->result();


}



function get_all_data_imported_payments_cofi()
{
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM `massrepayments` JOIN loan ON loan.loan_number = massrepayments.loan_number  WHERE `massrepayment_status`='imported'  ";
    return $query = $ci->db->query($sql)->result();
}


/**
 * Ensure notification_logs table exists
 */
function ensure_notification_logs_table() {
    $ci =& get_instance();
    $ci->load->database();

    if (!$ci->db->table_exists('notification_logs')) {
        $sql = "CREATE TABLE IF NOT EXISTS `notification_logs` (
            `log_id` int NOT NULL AUTO_INCREMENT,
            `notification_type` varchar(50) NOT NULL COMMENT 'Type: loan_created, loan_recommended, payment_reminder, etc.',
            `reference_type` varchar(50) DEFAULT NULL COMMENT 'Reference type: loan, customer, payment, etc.',
            `reference_id` int DEFAULT NULL COMMENT 'ID of the referenced record',
            `reference_number` varchar(100) DEFAULT NULL COMMENT 'Reference number (e.g., loan number)',
            `recipient_email` varchar(200) NOT NULL,
            `recipient_name` varchar(200) DEFAULT NULL,
            `recipient_user_id` int DEFAULT NULL COMMENT 'Employee ID if recipient is a system user',
            `subject` varchar(500) NOT NULL,
            `status` enum('sent','failed','pending') NOT NULL DEFAULT 'pending',
            `error_message` text DEFAULT NULL,
            `triggered_by` int DEFAULT NULL COMMENT 'User ID who triggered this notification',
            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `sent_at` datetime DEFAULT NULL,
            PRIMARY KEY (`log_id`),
            KEY `idx_notification_type` (`notification_type`),
            KEY `idx_reference` (`reference_type`, `reference_id`),
            KEY `idx_recipient` (`recipient_email`),
            KEY `idx_status` (`status`),
            KEY `idx_created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $ci->db->query($sql);
    }
}

/**
 * Log a notification
 * @param array $data Notification data
 * @return int|bool Insert ID or false on failure
 */
function log_notification($data) {
    $ci =& get_instance();
    $ci->load->database();

    // Ensure table exists
    ensure_notification_logs_table();

    $log_data = array(
        'notification_type' => $data['notification_type'] ?? 'general',
        'reference_type' => $data['reference_type'] ?? null,
        'reference_id' => $data['reference_id'] ?? null,
        'reference_number' => $data['reference_number'] ?? null,
        'recipient_email' => $data['recipient_email'],
        'recipient_name' => $data['recipient_name'] ?? null,
        'recipient_user_id' => $data['recipient_user_id'] ?? null,
        'subject' => $data['subject'],
        'status' => $data['status'] ?? 'pending',
        'error_message' => $data['error_message'] ?? null,
        'triggered_by' => $data['triggered_by'] ?? ($ci->session->userdata('user_id') ?: null),
        'created_at' => date('Y-m-d H:i:s'),
        'sent_at' => ($data['status'] == 'sent') ? date('Y-m-d H:i:s') : null
    );

    $ci->db->insert('notification_logs', $log_data);
    return $ci->db->insert_id();
}

/**
 * Get notification logs with optional filters
 * @param array $filters Optional filters: reference_type, reference_id, notification_type, status, limit, offset
 * @return array
 */
function get_notification_logs($filters = array()) {
    $ci =& get_instance();
    $ci->load->database();

    // Ensure table exists
    ensure_notification_logs_table();

    $ci->db->select('notification_logs.*, employees.Firstname as triggered_by_firstname, employees.Lastname as triggered_by_lastname');
    $ci->db->from('notification_logs');
    $ci->db->join('employees', 'employees.id = notification_logs.triggered_by', 'left');

    if (!empty($filters['reference_type'])) {
        $ci->db->where('notification_logs.reference_type', $filters['reference_type']);
    }
    if (!empty($filters['reference_id'])) {
        $ci->db->where('notification_logs.reference_id', $filters['reference_id']);
    }
    if (!empty($filters['notification_type'])) {
        $ci->db->where('notification_logs.notification_type', $filters['notification_type']);
    }
    if (!empty($filters['status'])) {
        $ci->db->where('notification_logs.status', $filters['status']);
    }
    if (!empty($filters['recipient_email'])) {
        $ci->db->like('notification_logs.recipient_email', $filters['recipient_email']);
    }
    if (!empty($filters['date_from'])) {
        $ci->db->where('notification_logs.created_at >=', $filters['date_from'] . ' 00:00:00');
    }
    if (!empty($filters['date_to'])) {
        $ci->db->where('notification_logs.created_at <=', $filters['date_to'] . ' 23:59:59');
    }

    $ci->db->order_by('notification_logs.created_at', 'DESC');

    $limit = $filters['limit'] ?? 100;
    $offset = $filters['offset'] ?? 0;
    $ci->db->limit($limit, $offset);

    return $ci->db->get()->result();
}

/**
 * Send email using SMTP settings from database
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $body Email body (can be HTML)
 * @param array $options Optional settings:
 *        - 'cc' => string|array CC recipients
 *        - 'bcc' => string|array BCC recipients
 *        - 'attachment' => string|array File path(s) to attach
 *        - 'from_name' => string Custom sender name (defaults to company name)
 *        - 'reply_to' => string Reply-to email address
 *        - 'log' => array Logging options (notification_type, reference_type, reference_id, etc.)
 * @return array ['success' => bool, 'message' => string]
 */
function send_smtp_email($to, $subject, $body, $options = array()) {
    $ci =& get_instance();
    $ci->load->database();
    $ci->load->library('email');

    // Get SMTP settings from database
    $settings = $ci->db->get_where('settings', array('settings_id' => 1))->row();

    if (!$settings) {
        return array('success' => false, 'message' => 'Email settings not found in database');
    }

    // Check if SMTP settings are configured
    if (empty($settings->email_host) || empty($settings->email_user) || empty($settings->email_pass)) {
        return array('success' => false, 'message' => 'SMTP settings are not configured. Please configure email settings.');
    }

    $host       = $settings->email_host;
    $user       = $settings->email_user;
    $pass       = $settings->email_pass;
    $from_email = $user;
    $from_name  = isset($options['from_name']) ? $options['from_name'] : ($settings->company_name ?? 'FundIt');

    // Allow self-signed/untrusted SSL certs (common on cPanel shared hosting)
    stream_context_set_default(array(
        'ssl' => array(
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true,
        )
    ));

    // Attempt chain: configured port first, then 587 TLS, then 25 plain
    $configured_port   = !empty($settings->email_port) ? intval($settings->email_port) : 465;
    $configured_crypto = ($configured_port == 465) ? 'ssl' : (($configured_port == 587) ? 'tls' : '');

    $attempts = array(
        array('label' => $host . ':' . $configured_port, 'host' => $host, 'port' => $configured_port, 'crypto' => $configured_crypto),
    );
    if ($configured_port != 587) {
        $attempts[] = array('label' => $host . ':587 (TLS)', 'host' => $host, 'port' => 587, 'crypto' => 'tls');
    }
    if ($configured_port != 25) {
        $attempts[] = array('label' => $host . ':25 (plain)', 'host' => $host, 'port' => 25, 'crypto' => '');
    }

    // Load the CI3 Email class file so we can instantiate it fresh each attempt.
    // CI3's email singleton shares _smtp_connect across calls — a stale/half-open
    // socket from a previous attempt causes is_resource() to return TRUE and the
    // library skips reconnecting, silently failing on every subsequent attempt.
    if (!class_exists('CI_Email')) {
        require_once BASEPATH . 'libraries/Email.php';
    }

    $last_error = '';

    foreach ($attempts as $attempt) {
        // Fresh instance per attempt — no shared socket state
        $emailLib = new CI_Email();
        $emailLib->initialize(array(
            'protocol'    => 'smtp',
            'smtp_host'   => $attempt['host'],
            'smtp_port'   => $attempt['port'],
            'smtp_crypto' => $attempt['crypto'],
            'smtp_user'   => $user,
            'smtp_pass'   => $pass,
            'smtp_timeout'=> 30,
            'mailtype'    => 'html',
            'charset'     => 'utf-8',
            'wordwrap'    => TRUE,
            'newline'     => "\r\n",
            'crlf'        => "\r\n"
        ));

        $emailLib->from($from_email, $from_name);
        $emailLib->to($to);
        if (!empty($options['cc']))       $emailLib->cc($options['cc']);
        if (!empty($options['bcc']))      $emailLib->bcc($options['bcc']);
        if (!empty($options['reply_to'])) $emailLib->reply_to($options['reply_to']);
        $emailLib->subject($subject);
        $emailLib->message($body);

        if (!empty($options['attachment'])) {
            foreach ((array)$options['attachment'] as $att) {
                if (file_exists($att)) $emailLib->attach($att);
            }
        }
        if (!empty($options['attachments'])) {
            foreach ($options['attachments'] as $att) {
                if (isset($att['path']) && file_exists($att['path'])) {
                    $emailLib->attach($att['path'], '', isset($att['name']) ? $att['name'] : basename($att['path']));
                }
            }
        }

        if ($emailLib->send()) {
            log_message('info', 'Email sent via ' . $attempt['label']);
            return array('success' => true, 'message' => 'Email sent successfully');
        }

        $last_error = strip_tags($emailLib->print_debugger(array('headers')));
        log_message('error', 'SMTP attempt failed (' . $attempt['label'] . '): ' . $last_error);
    }

    log_message('error', 'All SMTP attempts failed for ' . $to);
    return array(
        'success' => false,
        'message' => 'SMTP failed (' . $host . '): ' . substr($last_error, 0, 300),
        'debug'   => $last_error
    );
}

/**
 * Send email using a template
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $template_body The main content to insert into template
 * @param array $options Same as send_smtp_email
 * @return array ['success' => bool, 'message' => string]
 */
function send_templated_email($to, $subject, $template_body, $options = array()) {
    $ci =& get_instance();
    $ci->load->database();

    // Get company settings for template
    $settings = $ci->db->get_where('settings', array('settings_id' => 1))->row();
    $company_name = $settings->company_name ?? 'FundIt';
    $company_email = $settings->company_email ?? '';
    $company_phone = $settings->phone_number ?? '';
    $company_address_raw = $settings->address ?? '';

    // Clean address - strip HTML tags and format properly
    // Replace </p><p>, <br>, <br/>, <br /> with newlines, then strip remaining tags
    $company_address = $company_address_raw;
    $company_address = preg_replace('/<\/p>\s*<p>/i', "\n", $company_address);
    $company_address = preg_replace('/<br\s*\/?>/i', "\n", $company_address);
    $company_address = strip_tags($company_address);
    $company_address = trim($company_address);

    // Convert newlines to <br> for HTML email
    $company_address_html = nl2br(htmlspecialchars($company_address));

    // Build HTML email template
    $html_body = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . htmlspecialchars($subject) . '</title>
    </head>
    <body style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 1.6; color: #333333; background-color: #f4f4f4;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f4f4f4;">
            <tr>
                <td align="center" style="padding: 40px 20px;">
                    <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <!-- Header -->
                        <tr>
                            <td style="background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%); padding: 30px 40px; border-radius: 8px 8px 0 0;">
                                <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 600;">' . htmlspecialchars($company_name) . '</h1>
                            </td>
                        </tr>

                        <!-- Content -->
                        <tr>
                            <td style="padding: 40px;">
                                ' . $template_body . '
                            </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                            <td style="background-color: #f8f9fa; padding: 25px 40px; border-radius: 0 0 8px 8px; border-top: 1px solid #e5e7eb;">
                                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tr>
                                        <td style="color: #6b7280; font-size: 12px;">
                                            <p style="margin: 0 0 5px 0;"><strong>' . htmlspecialchars($company_name) . '</strong></p>
                                            ' . (!empty($company_address) ? '<p style="margin: 0 0 5px 0;">' . $company_address_html . '</p>' : '') . '
                                            ' . (!empty($company_phone) ? '<p style="margin: 0 0 5px 0;">Phone: ' . htmlspecialchars($company_phone) . '</p>' : '') . '
                                            ' . (!empty($company_email) ? '<p style="margin: 0;">Email: ' . htmlspecialchars($company_email) . '</p>' : '') . '
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding-top: 15px; color: #9ca3af; font-size: 11px;">
                                            <p style="margin: 0;">This is an automated message. Please do not reply directly to this email.</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>';

    return send_smtp_email($to, $subject, $html_body, $options);
}

/**
 * Legacy send_email function - kept for backward compatibility
 * Now uses SMTP from database settings
 * @param string $to Recipient email address
 * @param string $body Email body
 * @return void
 */
function send_email($to, $body) {
    $result = send_smtp_email($to, 'Password reset', $body);
    if (!$result['success']) {
        echo json_encode($result);
    } else {
        echo json_encode(array('status' => 'success', 'message' => 'Email sent'));
    }
}
function login_user($username){


    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM user_access inner join employees on employees.id=user_access.Employee 
     inner join roles on roles.id=employees.Role 
      WHERE AccessCode = '$username'";
    return $query = $ci->db->query($sql)->row();


}
function get_by_id($table,$key,$value){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM $table  WHERE $key = '$value'";
	return $query = $ci->db->query($sql)->row();


}
function get_partial_paid($key ,$value){


    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM `payement_schedules` WHERE `loan_id`= '$key ' AND `payment_number`= '$value' AND  `partial_paid`= 'YES'";
    return $query = $ci->db->query($sql)->row();


}

function get_partial_paid_last($key){


    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM `payement_schedules` WHERE `loan_id`= '$key ' AND partial_paid = 'YES' ORDER BY id DESC
LIMIT 1";
    return $query = $ci->db->query($sql)->row();


}
function get_paid_last($key){


    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM `payement_schedules` WHERE `loan_id`= '$key ' ORDER BY id DESC LIMIT 1";
    return $query = $ci->db->query($sql)->row();


}


function getlastrow($loannumber){

    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *
            FROM massrepayments
            WHERE loan_number = '$loannumber'
            ORDER BY massrepayment_id DESC
            LIMIT 1;
";
    return $query = $ci->db->query($sql)->row();


}
function get_all_by_id($table,$key,$value){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM $table  WHERE $key = '$value'";
	return $query = $ci->db->query($sql)->result();


}


function get_allLoanPayRBM_by_id($loanid,$paymentnumber){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM payement_schedules WHERE loan_id=$loanid AND payment_number=$paymentnumber ";
	return $query = $ci->db->query($sql)->row();


}

function get_previous_loan($customerID){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM loan WHERE loan_customer = $customerID AND loan_id < (SELECT MAX(loan_id) FROM loan WHERE loan_customer = $customerID) ORDER BY loan_id DESC LIMIT 1 ";
	return $query = $ci->db->query($sql)->row();


}
function get_all_loan_balances_by_product($product){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id JOIN loan_products ON loan_products.loan_product_id = loan.loan_product WHERE payement_schedules.status = 'NOT PAID' AND loan.loan_product= '$product' AND loan.loan_status = 'ACTIVE'";
    return $query = $ci->db->query($sql)->result();
}
function institutional_portfolio(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id JOIN loan_products ON loan_products.loan_product_id = loan.loan_product WHERE payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE'";
    return $query = $ci->db->query($sql)->result();
}



   function get_number_of_arreas($loadID){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT COUNT(*) AS num_arrears
                FROM loan l
                JOIN payement_schedules ps ON l.loan_id = ps.loan_id
                WHERE l.loan_id = '$loadID'
                  AND l.loan_status = 'active'
                  AND ps.payment_schedule < CURDATE()
                  AND ps.status <> 'paid' ";
    return $query = $ci->db->query($sql)->row();
}



function get_days_of_arreas($loadID){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT DATEDIFF(CURRENT_DATE(), MAX(ps.payment_schedule)) AS days_in_arrears
            FROM loan l
            JOIN payement_schedules ps ON l.loan_id = ps.loan_id
            WHERE l.loan_id = '$loadID'
            AND payment_schedule <  CURDATE()
          AND l.loan_status = 'active'
                  
                  AND ps.status <> 'paid' ";
    return $query = $ci->db->query($sql)->row();
}

   function get_amount_of_arreas($loadID){
    $ci =& get_instance();
    $ci->load->database();

    // Get basic arrears amount
    $sql="SELECT SUM(ps.amount) AS amount_arrears
                FROM loan l
                JOIN payement_schedules ps ON l.loan_id = ps.loan_id
                WHERE l.loan_id = ?
                  AND l.loan_status = 'active'
                  AND ps.payment_schedule < CURDATE()
                  AND ps.status <> 'paid' ";
    $result = $ci->db->query($sql, array($loadID))->row();

    // For bullet loans: recalculate with compound interest on OUTSTANDING BALANCE
    $loan = $ci->db->get_where('loan', array('loan_id' => $loadID))->row();
    if ($loan && $loan->calculation_type == 'Bullet Payment') {
        $principal = floatval($loan->loan_principal);
        $monthly_rate = floatval($loan->loan_interest) / 100;
        $term = intval($loan->loan_period);
        $maturity_total = $principal + ($principal * $monthly_rate * $term);

        // Get total payments made
        $paid_sql = "SELECT SUM(ps.paid_amount) AS total_paid
                     FROM payement_schedules ps
                     WHERE ps.loan_id = ? AND ps.paid_amount > 0";
        $paid_result = $ci->db->query($paid_sql, array($loadID))->row();
        $total_paid = ($paid_result && $paid_result->total_paid) ? floatval($paid_result->total_paid) : 0;

        $maturity_date = date('Y-m-d', strtotime("+{$term} months", strtotime($loan->loan_date)));
        $days_past_maturity = max(0, floor((strtotime(date('Y-m-d')) - strtotime($maturity_date)) / 86400));

        if ($days_past_maturity > 0) {
            // Payments reduce the balance BEFORE compounding
            $outstanding_at_maturity = $maturity_total - $total_paid;
            if ($outstanding_at_maturity < 0) $outstanding_at_maturity = 0;

            $full_months = floor($days_past_maturity / 30);
            $remaining_days = $days_past_maturity % 30;

            // Compound interest on the outstanding balance
            $running_balance = $outstanding_at_maturity;
            for ($m = 0; $m < $full_months; $m++) {
                $running_balance *= (1 + $monthly_rate);
            }
            if ($remaining_days > 0) {
                $daily_int = ($running_balance * $monthly_rate) / 30;
                $running_balance += $daily_int * $remaining_days;
            }

            $result->amount_arrears = round($running_balance, 2);
        }
    }

    return $result;
}
//get distinct loan
function get_all_distinctLoan_cofi(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT DISTINCT(`LOANNO`) FROM `completedlimbenew` ";
    return $query = $ci->db->query($sql)->result();
}

//get_all_from_cofi_salarybacked()


function get_all_sme_cofi(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT DISTINCT(`IDNumber`) FROM `smeloans` WHERE 1";
    return $query = $ci->db->query($sql)->result();
}


function get_all_sme_cofi_by_id($idloan){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

     $sql="SELECT * FROM `smeloans` WHERE `IDNumber`='$idloan'";
    return $query = $ci->db->query($sql)->row();
}

function get_all_sme_cofi_by_all($idloan){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

     $sql="SELECT * FROM `smeloans` WHERE `IDNumber`='$idloan'";
    return $query = $ci->db->query($sql)->result();
}

function check_individual($idloan){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM `proofofidentity` WHERE `IDNumber`='$idloan'";
    return $query = $ci->db->query($sql)->result();
}

//get_all_from_cofi_salarybacked()


function get_all_cust_cofi($idloan){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM `completedlimbenew` WHERE `LOANNO`='$idloan'";
    return $query = $ci->db->query($sql)->row();
}

function get_total_loan_amount($status){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT SUM(loan_amount_total) AS total_amount
            FROM loan
            WHERE loan_status = '$status'";

    return $query = $ci->db->query($sql)->row();
}

function get_total_loan_amount_product($product){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT SUM(loan_amount_total) AS total_amount_product
            FROM loan
            WHERE loan_product = '$product'";

    return $query = $ci->db->query($sql)->row();
}

function get_total_loan_amount_product_by_id($status,$product){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT SUM(loan_amount_total) AS total_amount_product
            FROM loan
            WHERE loan_status = '$status' AND loan_product = '$product'";

    return $query = $ci->db->query($sql)->row();
}


function get_all_loanCheck($loadID){
    $ci =& get_instance();
    $ci->load->database();


    $sql="SELECT * FROM loan  WHERE `Loan_number`='$loadID'";
    return $query = $ci->db->query($sql)->result();
}
function get_all_accountCheck($loadID){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM `account` WHERE `account_number`='$loadID'";
    return $query = $ci->db->query($sql)->result();
}

function get_all_cust_cofi_group(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM completedlimbenew GROUP BY`LOANNO`";
    return $query = $ci->db->query($sql)->result();
}

function get_all_groups_filter(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT DISTINCT(`CLIENTNAME` ),id,LOCATION,PHONENUMBER FROM completedlimbenew";
    return $query = $ci->db->query($sql)->result();
}
function institutional_arrears(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id JOIN loan_products ON loan_products.loan_product_id = loan.loan_product WHERE payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payement_schedules.payment_schedule < CURDATE()";
    return $query = $ci->db->query($sql)->result();
}
function institutional_arrears_today(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id JOIN loan_products ON loan_products.loan_product_id = loan.loan_product WHERE payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payement_schedules.payment_schedule = SUBDATE(CURDATE(),1)";
    return $query = $ci->db->query($sql)->result();
}


function institutional_arrears_today_by_id($id){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id  WHERE  loan.loan_product='$id' AND payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payement_schedules.payment_schedule = SUBDATE(CURDATE(),1)";
    return $query = $ci->db->query($sql)->result();
}
function institutional_arrears_threedays(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id JOIN loan_products ON loan_products.loan_product_id = loan.loan_product WHERE payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payement_schedules.payment_schedule BETWEEN SUBDATE(CURDATE(),3) AND SUBDATE(CURDATE(),1)";
    return $query = $ci->db->query($sql)->result();
}


function institutional_arrears_threedays_by_id($id){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id  WHERE loan.loan_product='$id' AND payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payement_schedules.payment_schedule BETWEEN SUBDATE(CURDATE(),3) AND SUBDATE(CURDATE(),1)";
    return $query = $ci->db->query($sql)->result();
}
function institutional_arrears_week(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id JOIN loan_products ON loan_products.loan_product_id = loan.loan_product WHERE payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payment_schedule < CURDATE() AND  payement_schedules.payment_schedule BETWEEN SUBDATE(CURDATE(),7) AND SUBDATE(CURDATE(),1)";
    return $query = $ci->db->query($sql)->result();
}

function institutional_arrears_week_by_id($id){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id WHERE loan.loan_product='$id' AND payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payment_schedule < CURDATE() AND  payement_schedules.payment_schedule BETWEEN SUBDATE(CURDATE(),7) AND SUBDATE(CURDATE(),1)";
    return $query = $ci->db->query($sql)->result();
}


function institutional_arrears_month(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id JOIN loan_products ON loan_products.loan_product_id = loan.loan_product WHERE payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payment_schedule < CURDATE() AND  payement_schedules.payment_schedule BETWEEN SUBDATE(CURDATE(),30) AND SUBDATE(CURDATE(),1)";
    return $query = $ci->db->query($sql)->result();
}

function institutional_arrears_month_by_id($id){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id  WHERE loan.loan_product='$id' AND payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payment_schedule < CURDATE() AND  payement_schedules.payment_schedule BETWEEN SUBDATE(CURDATE(),30) AND SUBDATE(CURDATE(),1)";
    return $query = $ci->db->query($sql)->result();
}
function institutional_arrears_2month(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id JOIN loan_products ON loan_products.loan_product_id = loan.loan_product WHERE payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payment_schedule < CURDATE() AND  payement_schedules.payment_schedule BETWEEN SUBDATE(CURDATE(),60) AND SUBDATE(CURDATE(),1)";
    return $query = $ci->db->query($sql)->result();
}


function institutional_arrears_2month_by_id($id){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id  WHERE loan.loan_product='$id' AND payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payment_schedule < CURDATE() AND  payement_schedules.payment_schedule BETWEEN SUBDATE(CURDATE(),60) AND SUBDATE(CURDATE(),1)";
    return $query = $ci->db->query($sql)->result();
}
function institutional_arrears_3month(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id JOIN loan_products ON loan_products.loan_product_id = loan.loan_product WHERE payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payment_schedule < CURDATE() AND  payement_schedules.payment_schedule BETWEEN SUBDATE(CURDATE(),90) AND SUBDATE(CURDATE(),1)";
    return $query = $ci->db->query($sql)->result();
}
function institutional_arrears_3month_by_id($id){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id  WHERE loan.loan_product='$id' AND payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payment_schedule < CURDATE() AND  payement_schedules.payment_schedule BETWEEN SUBDATE(CURDATE(),90) AND SUBDATE(CURDATE(),1)";
    return $query = $ci->db->query($sql)->result();
}

function payments_today(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id JOIN loan_products ON loan_products.loan_product_id = loan.loan_product WHERE payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payment_schedule = CURDATE()";
    return $query = $ci->db->query($sql)->result();
}


function payments_today_by_id($id){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id WHERE loan.loan_product='$id' AND payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payment_schedule = CURDATE()";
    return $query = $ci->db->query($sql)->result();
}
function payments_week(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id JOIN loan_products ON loan_products.loan_product_id = loan.loan_product WHERE payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payement_schedules.payment_schedule BETWEEN adddate(curdate(), 7) AND adddate(curdate(), 1)";
    return $query = $ci->db->query($sql)->result();
}

function payments_week_by_id($id){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id WHERE loan.loan_product='$id'  AND  payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payement_schedules.payment_schedule BETWEEN adddate(curdate(), 7) AND adddate(curdate(), 1)";
    return $query = $ci->db->query($sql)->result();
}
function payments_month(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id JOIN loan_products ON loan_products.loan_product_id = loan.loan_product WHERE payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payement_schedules.payment_schedule BETWEEN subdate(curdate(), 30) AND subdate(curdate(), 1)";
    return $query = $ci->db->query($sql)->result();
}

function payments_month_by_id($id){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT *, payement_schedules.id as psid FROM payement_schedules  JOIN loan ON loan.loan_id = payement_schedules.loan_id WHERE loan.loan_product='$id' AND payement_schedules.status = 'NOT PAID'  AND loan.loan_status = 'ACTIVE' AND payement_schedules.payment_schedule BETWEEN subdate(curdate(), 30) AND subdate(curdate(), 1)";
    return $query = $ci->db->query($sql)->result();
}

function rbm_report(){
    $ci =& get_instance();
    $ci->load->database();
//	$ci->load->model('Dbc_users_model');

    $sql="SELECT * from individual_customers 
    inner join  proofofidentity on proofofidentity.ClientID=individual_customers.ClientID 
    INNER JOIN loan ON loan.loan_customer=individual_customers.id ORDER by loan.loan_id DESC limit 0,300";
    return $query = $ci->db->query($sql)->result();
}




function check_paid_fees($loan_id){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM transactions WHERE loan_id = $loan_id AND transaction_type= '1'";
	return $query = $ci->db->query($sql)->result();


}
function get_active_loan(){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM loan WHERE loan_status = 'ACTIVE'";
	return $query = $ci->db->query($sql)->result();


}
function get_logs($table,$key,$value){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM $table  WHERE $key = $value LIMIT 5";
	return $query = $ci->db->query($sql)->result();


}
function get_all_features($value){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM listing_features JOIN features ON features.feature_id = listing_features.feature_id WHERE listing_features.listing_id = '$value'";
	return $query = $ci->db->query($sql)->result();


}

function get_all_customersGroup($value){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * from individual_customers INNER JOIN customer_groups on customer_groups.customer=individual_customers.id 
	WHERE customer_groups.group_id
 = '$value'";
	return $query = $ci->db->query($sql)->result();


}
function get_features(){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM features ";
	return $query = $ci->db->query($sql)->result();


}
function get_listing_images($id){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM listing_images WHERE listing_images.listing_id = '$id'";

	return $query = $ci->db->query($sql)->result();


}
function get_recent(){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM  listings  JOIN districts ON districts.district_id = listings.listing_location  JOIN release_type ON release_type.rtype_id = listings.rtype JOIN categories ON categories.category_id = listings.listing_category LIMIT 5";

	return $query = $ci->db->query($sql)->result();


}
function get_similar(){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM  listings  JOIN districts ON districts.district_id = listings.listing_location  JOIN release_type ON release_type.rtype_id = listings.rtype JOIN categories ON categories.category_id = listings.listing_category LIMIT 5";
	return $query = $ci->db->query($sql)->result();


}
function get_featured(){


	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM  listings  JOIN districts ON districts.district_id = listings.listing_location  JOIN categories ON categories.category_id= listings.listing_category  JOIN release_type ON release_type.rtype_id = listings .rtype WHERE  is_featured= 'YES' LIMIT 5";
	return $query = $ci->db->query($sql)->result();


}
function check_exist_in_table($table,$field,$key){
	$ci =& get_instance();
	$ci->load->database();
//	$ci->load->model('Dbc_users_model');

	$sql="SELECT * FROM $table WHERE $field='$key'";
	return $query = $ci->db->query($sql)->row();

}
function getDaysDifference($startDate, $endDate) {
    $startTimestamp = strtotime($startDate);
    $endTimestamp = strtotime($endDate);

    $difference = $endTimestamp - $startTimestamp;

    return floor($difference / (60 * 60 * 24));
}

 function calculateLateFeeAmount($paymentAmount, $daysOverdue) {
    $lateFeePercentage = 5;
    $daysPerCycle = 5;

    // Calculate the number of cycles (every 5 days)
    $cycles = intval($daysOverdue / $daysPerCycle);
if($cycles > 4){
    $real_cylce = 4;
}else{
    $real_cylce = $cycles;
}
    // Calculate the late fee
    $lateFee = ($lateFeePercentage / 100) * $paymentAmount * $real_cylce;

    return $lateFee;
}
function findFifthDayOfNextMonth($dateString) {
    // Create a DateTime object from the input date string
    $date = new DateTime($dateString);

    // Add one month to the current date
    $date->modify('+1 month');

    // Set the day of the month to 5
    $date->setDate($date->format('Y'), $date->format('m'), 5);

    // Format the result as 'Y-m-d'
    $result = $date->format('Y-m-d');

    return $result;
}

/**
 * Get loan stats (count and total principal) by status
 * @param string $status
 * @return array ['count' => int, 'total' => float]
 */
function get_loan_stats_by_status($status) {
    $ci =& get_instance();
    $ci->load->database();
    $sql = "SELECT COUNT(*) as count, COALESCE(SUM(loan_principal),0) as total FROM loan WHERE loan_status = ?";
    $query = $ci->db->query($sql, array($status));
    return $query->row_array();
}

/**
 * Get total disbursed loan stats (principal disbursed to date).
 * Counts every loan that has been disbursed regardless of current status —
 * i.e. active, closed, written-off and defaulted loans — so the figure
 * reflects all principal ever released, not just the current book.
 * @return array ['count' => int, 'total' => float]
 */
function get_total_disbursed_stats() {
    $ci =& get_instance();
    $ci->load->database();
    $sql = "SELECT COUNT(*) as count, COALESCE(SUM(loan_principal),0) as total
            FROM loan
            WHERE UPPER(loan_status) IN ('ACTIVE','CLOSED','WRITTEN_OFF','DEFAULTED')";
    $query = $ci->db->query($sql);
    return $query->row_array();
}

/**
 * Get loan count by status for dashboard strip
 * @param string $status
 * @return int
 */
function get_loan_count_by_status($status) {
    $ci =& get_instance();
    $ci->load->database();
    $sql = "SELECT COUNT(*) as count FROM loan WHERE UPPER(loan_status) = UPPER(?)";
    $query = $ci->db->query($sql, array($status));
    $result = $query->row();
    return $result ? $result->count : 0;
}

/**
 * Get all loan status counts for dashboard strip
 * @return array
 */
function get_all_loan_status_counts() {
    $ci =& get_instance();
    $ci->load->database();

    $statuses = array(
        'initiated' => 'INITIATED',
        'recommended' => 'RECOMMENDED',
        'approved' => 'APPROVED',
        'client_signed' => 'CLIENT_SIGNED',
        'active' => 'ACTIVE',
        'closed' => 'CLOSED',
        'written_off' => 'WRITTEN_OFF',
        'rejected' => 'REJECTED'
    );

    $counts = array();
    foreach ($statuses as $key => $status) {
        $sql = "SELECT COUNT(*) as count FROM loan WHERE UPPER(loan_status) = ?";
        $query = $ci->db->query($sql, array($status));
        $result = $query->row();
        $counts[$key] = $result ? $result->count : 0;
    }

    return $counts;
}

/**
 * Get active loans principal by product
 * @param int $product_id
 * @return object
 */
function get_active_loans_principal_by_product($product_id) {
    $ci =& get_instance();
    $ci->load->database();
    $sql = "SELECT 
                COUNT(*) as count,
                COALESCE(SUM(loan_principal),0) as total_principal,
                COALESCE(SUM(loan_amount_total),0) as total_disbursed
            FROM loan 
            WHERE loan_status = 'active' AND loan_product = ?";
    $query = $ci->db->query($sql, array($product_id));
    return $query->row();
}

/**
 * Get total active loans principal (all products)
 * @return object
 */
function get_total_active_loans_principal() {
    $ci =& get_instance();
    $ci->load->database();
    $sql = "SELECT 
                COUNT(*) as count,
                COALESCE(SUM(loan_principal),0) as total_principal,
                COALESCE(SUM(loan_amount_total),0) as total_disbursed
            FROM loan 
            WHERE loan_status = 'active'";
    $query = $ci->db->query($sql);
    return $query->row();
}

/**
 * Get outstanding balance for active loans by product
 * @param int $product_id
 * @return object
 */
function get_outstanding_balance_by_product($product_id) {
    $ci =& get_instance();
    $ci->load->database();
    // Outstanding = remaining principal + interest that has already ACCRUED
    // (installments due on or before today), net of what has been paid.
    // principal_paid tracks the principal portion already settled (incl. surplus
    // prepaid onto future schedules); interest not yet accrued is excluded so the
    // figure doesn't overstate expected income.
    $sql = "SELECT
                COALESCE(SUM(
                    GREATEST(ps.principal - ps.principal_paid, 0)
                    + CASE WHEN DATE(ps.payment_schedule) <= CURDATE()
                           THEN GREATEST(ps.interest - GREATEST(ps.paid_amount - ps.principal_paid, 0), 0)
                           ELSE 0 END
                ), 0) as outstanding_balance
            FROM payement_schedules ps
            INNER JOIN loan l ON l.loan_id = ps.loan_id
            WHERE l.loan_status = 'active'
            AND l.loan_product = ?
            AND ps.amount > ps.paid_amount";
    $query = $ci->db->query($sql, array($product_id));
    return $query->row();
}

/**
 * Get total outstanding balance for all active loans
 * @return object
 */
function get_total_outstanding_balance() {
    $ci =& get_instance();
    $ci->load->database();
    // Outstanding = remaining principal + interest that has already ACCRUED
    // (installments due on or before today), net of what has been paid.
    // principal_paid tracks the principal portion already settled (incl. surplus
    // prepaid onto future schedules); interest not yet accrued is excluded so the
    // figure doesn't overstate expected income.
    $sql = "SELECT
                COALESCE(SUM(
                    GREATEST(ps.principal - ps.principal_paid, 0)
                    + CASE WHEN DATE(ps.payment_schedule) <= CURDATE()
                           THEN GREATEST(ps.interest - GREATEST(ps.paid_amount - ps.principal_paid, 0), 0)
                           ELSE 0 END
                ), 0) as outstanding_balance
            FROM payement_schedules ps
            INNER JOIN loan l ON l.loan_id = ps.loan_id
            WHERE l.loan_status = 'active'
            AND ps.amount > ps.paid_amount";
    $query = $ci->db->query($sql);
    return $query->row();
}

/**
 * Get total number of active microfinance borrowers
 * @return int
 */
function get_total_microfinance_borrowers() {
    $ci =& get_instance();
    $ci->load->database();
    $sql = "SELECT COUNT(DISTINCT loan_customer) as total_borrowers
            FROM loan 
            WHERE loan_status = 'active'";
    $query = $ci->db->query($sql);
    $result = $query->row();
    return $result ? $result->total_borrowers : 0;
}

/**
 * Get microfinance overdue amount (PAR > 30 days)
 * @return float
 */
function get_microfinance_overdue_amount() {
    $ci =& get_instance();
    $ci->load->database();
    $sql = "SELECT COALESCE(SUM(ps.amount - ps.paid_amount), 0) as overdue_amount
            FROM payement_schedules ps
            INNER JOIN loan l ON l.loan_id = ps.loan_id
            WHERE l.loan_status = 'active' 
            AND ps.payment_schedule < DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            AND ps.amount > ps.paid_amount";
    $query = $ci->db->query($sql);
    $result = $query->row();
    return $result ? $result->overdue_amount : 0;
}

/**
 * Get microfinance Portfolio at Risk (PAR) ratio
 * @return float
 */
function get_microfinance_par_ratio() {
    $ci =& get_instance();
    $ci->load->database();
    
    // Get total outstanding balance
    $total_sql = "SELECT COALESCE(SUM(ps.amount - ps.paid_amount), 0) as total_outstanding
                  FROM payement_schedules ps
                  INNER JOIN loan l ON l.loan_id = ps.loan_id
                  WHERE l.loan_status = 'active' 
                  AND ps.amount > ps.paid_amount";
    $total_result = $ci->db->query($total_sql)->row();
    $total_outstanding = $total_result ? $total_result->total_outstanding : 0;
    
    if($total_outstanding == 0) {
        return 0;
    }
    
    // Get overdue amount
    $overdue_amount = get_microfinance_overdue_amount();
    
    return ($overdue_amount / $total_outstanding) * 100;
}

/**
 * Get microfinance collections for today
 * @return float
 */
function get_microfinance_collection_today() {
    $ci =& get_instance();
    $ci->load->database();
    $sql = "SELECT COALESCE(SUM(ps.paid_amount), 0) as collections_today
            FROM payement_schedules ps
            INNER JOIN loan l ON l.loan_id = ps.loan_id
            WHERE l.loan_status = 'active' 
            AND DATE(ps.payment_schedule) = CURDATE()
            AND ps.paid_amount > 0";
    $query = $ci->db->query($sql);
    $result = $query->row();
    return $result ? $result->collections_today : 0;
}

/**
 * Get average microfinance loan size
 * @return float
 */
function get_microfinance_avg_loan_size() {
    $ci =& get_instance();
    $ci->load->database();
    $sql = "SELECT COALESCE(AVG(loan_principal), 0) as avg_loan_size
            FROM loan 
            WHERE loan_status = 'active'";
    $query = $ci->db->query($sql);
    $result = $query->row();
    return $result ? $result->avg_loan_size : 0;
}

/**
 * Get microfinance repayment rate
 * @return float
 */
function get_microfinance_repayment_rate() {
    $ci =& get_instance();
    $ci->load->database();
    
    $sql = "SELECT 
                COALESCE(SUM(ps.amount), 0) as total_expected,
                COALESCE(SUM(ps.paid_amount), 0) as total_paid
            FROM payement_schedules ps
            INNER JOIN loan l ON l.loan_id = ps.loan_id
            WHERE l.loan_status = 'active'
            AND ps.payment_schedule <= CURDATE()";
    
    $query = $ci->db->query($sql);
    $result = $query->row();
    
    if(!$result || $result->total_expected == 0) {
        return 0;
    }
    
    return ($result->total_paid / $result->total_expected) * 100;
}

/**
 * Get count of active group loans
 * @return int
 */
function get_microfinance_group_loans_count() {
    $ci =& get_instance();
    $ci->load->database();
    $sql = "SELECT COUNT(*) as group_loans_count
            FROM loan 
            WHERE loan_status = 'active' 
            AND customer_type = 'group'";
    $query = $ci->db->query($sql);
    $result = $query->row();
    return $result ? $result->group_loans_count : 0;
}

/**
 * Get microfinance cash at hand (placeholder - needs actual cash account logic)
 * @return float
 */
function get_microfinance_cash_at_hand() {
    // This would typically come from a cash/bank accounts table
    // For now, returning a placeholder value
    return 0;
}

/**
 * Get loan approvers from the approval trail for multi-level approval system
 * Returns array of users who approved this loan (MULTI_APPROVE actions)
 * @param int $loan_id
 * @return array Array of approvers with user_id, user_name, date
 */
function get_loan_approvers($loan_id) {
    $ci =& get_instance();
    $ci->load->database();

    $ci->db->select('loan_approval_trail.user_id, loan_approval_trail.date_stamp, loan_approval_trail.comment, employees.Firstname, employees.Lastname');
    $ci->db->from('loan_approval_trail');
    $ci->db->join('employees', 'employees.id = loan_approval_trail.user_id', 'left');
    $ci->db->where('loan_approval_trail.loan_id', $loan_id);
    $ci->db->where('loan_approval_trail.action', 'MULTI_APPROVE');
    $ci->db->order_by('loan_approval_trail.date_stamp', 'ASC');
    $result = $ci->db->get()->result();

    $approvers = array();
    foreach($result as $row) {
        $approvers[] = array(
            'user_id' => $row->user_id,
            'user_name' => $row->Firstname . ' ' . $row->Lastname,
            'date' => $row->date_stamp,
            'comment' => $row->comment
        );
    }
    return $approvers;
}

/**
 * Get count of loans pending multi-level approval (RECOMMENDED status)
 * @return int
 */
function get_pending_approval_count() {
    $ci =& get_instance();
    $ci->load->database();

    $ci->db->from('loan');
    $ci->db->where('loan_status', 'RECOMMENDED');
    return $ci->db->count_all_results();
}

/**
 * Get count of loans that current user can approve
 * (Not already approved by user, and user is not the last approver)
 * @return int
 */
function get_user_approvable_count() {
    $ci =& get_instance();
    $ci->load->database();
    $ci->load->library('session');

    $current_user_id = $ci->session->userdata('user_id');

    // Get all RECOMMENDED loans
    $ci->db->from('loan');
    $ci->db->where('loan_status', 'RECOMMENDED');
    $loans = $ci->db->get()->result();

    $count = 0;
    foreach($loans as $loan) {
        $approvers = get_loan_approvers($loan->loan_id);
        $last_approver_id = !empty($approvers) ? end($approvers)['user_id'] : null;

        // Check if current user already approved
        $user_already_approved = false;
        foreach($approvers as $a) {
            if($a['user_id'] == $current_user_id) {
                $user_already_approved = true;
                break;
            }
        }

        // User can approve if: not already approved AND not the last approver
        if(!$user_already_approved && $last_approver_id != $current_user_id) {
            $count++;
        }
    }

    return $count;
}

// ============================================================================
// EMAIL NOTIFICATION HELPER FUNCTIONS
// ============================================================================

/**
 * Send password reset email
 * @param string $to Recipient email
 * @param string $name Recipient name
 * @param string $new_password The new password
 * @return array
 */
function send_password_reset_email($to, $name, $new_password) {
    $body = '
        <h2 style="color: #1e3a5f;">Password Reset</h2>
        <p>Hello ' . htmlspecialchars($name) . ',</p>
        <p>Your password has been reset successfully. Here are your new login credentials:</p>
        <div style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <p style="margin: 0;"><strong>New Password:</strong> <code style="background: #1e3a5f; color: #fff; padding: 4px 8px; border-radius: 4px;">' . htmlspecialchars($new_password) . '</code></p>
        </div>
        <p style="color: #dc2626;"><strong>Important:</strong> Please change your password immediately after logging in for security purposes.</p>
    ';

    return send_templated_email($to, 'Password Reset - FundIt', $body);
}

/**
 * Send loan application notification to customer
 * @param string $to Customer email
 * @param string $name Customer name
 * @param string $loan_number Loan reference number
 * @param float $amount Loan amount
 * @return array
 */
function send_loan_application_email($to, $name, $loan_number, $amount) {
    $ci =& get_instance();
    $settings = $ci->db->get_where('settings', array('settings_id' => 1))->row();
    $currency = $settings->currency ?? 'ZMW';

    $body = '
        <h2 style="color: #1e3a5f;">Loan Application Received</h2>
        <p>Dear ' . htmlspecialchars($name) . ',</p>
        <p>We have received your loan application and it is currently being processed.</p>
        <div style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;"><strong>Reference Number:</strong></td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">' . htmlspecialchars($loan_number) . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Applied Amount:</strong></td>
                    <td style="padding: 8px 0;">' . $currency . ' ' . number_format($amount, 2) . '</td>
                </tr>
            </table>
        </div>
        <p>You will be notified once your application has been reviewed.</p>
    ';

    return send_templated_email($to, 'Loan Application Received - ' . $loan_number, $body);
}

/**
 * Send loan approval notification to customer
 * @param string $to Customer email
 * @param string $name Customer name
 * @param string $loan_number Loan reference number
 * @param float $approved_amount Approved loan amount
 * @return array
 */
function send_loan_approval_email($to, $name, $loan_number, $approved_amount) {
    $ci =& get_instance();
    $settings = $ci->db->get_where('settings', array('settings_id' => 1))->row();
    $currency = $settings->currency ?? 'ZMW';

    $body = '
        <h2 style="color: #059669;">Loan Application Approved!</h2>
        <p>Dear ' . htmlspecialchars($name) . ',</p>
        <p>Congratulations! Your loan application has been <strong style="color: #059669;">APPROVED</strong>.</p>
        <div style="background: #dcfce7; border: 1px solid #059669; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #86efac;"><strong>Reference Number:</strong></td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #86efac;">' . htmlspecialchars($loan_number) . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Approved Amount:</strong></td>
                    <td style="padding: 8px 0; font-size: 18px; font-weight: bold; color: #059669;">' . $currency . ' ' . number_format($approved_amount, 2) . '</td>
                </tr>
            </table>
        </div>
        <p>Please contact our office to complete the disbursement process.</p>
    ';

    return send_templated_email($to, 'Loan Approved - ' . $loan_number, $body);
}

/**
 * Send loan rejection notification to customer
 * @param string $to Customer email
 * @param string $name Customer name
 * @param string $loan_number Loan reference number
 * @param string $reason Optional rejection reason
 * @return array
 */
function send_loan_rejection_email($to, $name, $loan_number, $reason = '') {
    $reason_html = !empty($reason) ?
        '<div style="background: #fef2f2; border: 1px solid #fca5a5; border-radius: 8px; padding: 15px; margin: 20px 0;">
            <strong>Reason:</strong> ' . htmlspecialchars($reason) . '
        </div>' : '';

    $body = '
        <h2 style="color: #dc2626;">Loan Application Update</h2>
        <p>Dear ' . htmlspecialchars($name) . ',</p>
        <p>We regret to inform you that your loan application (<strong>' . htmlspecialchars($loan_number) . '</strong>) could not be approved at this time.</p>
        ' . $reason_html . '
        <p>If you have any questions or would like to discuss alternative options, please do not hesitate to contact us.</p>
    ';

    return send_templated_email($to, 'Loan Application Update - ' . $loan_number, $body);
}

/**
 * Send payment reminder email
 * @param string $to Customer email
 * @param string $name Customer name
 * @param string $loan_number Loan reference number
 * @param float $amount Payment amount due
 * @param string $due_date Payment due date
 * @return array
 */
function send_payment_reminder_email($to, $name, $loan_number, $amount, $due_date) {
    $ci =& get_instance();
    $settings = $ci->db->get_where('settings', array('settings_id' => 1))->row();
    $currency = $settings->currency ?? 'ZMW';

    $body = '
        <h2 style="color: #f59e0b;">Payment Reminder</h2>
        <p>Dear ' . htmlspecialchars($name) . ',</p>
        <p>This is a friendly reminder that your loan payment is due soon.</p>
        <div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #fcd34d;"><strong>Loan Reference:</strong></td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #fcd34d;">' . htmlspecialchars($loan_number) . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #fcd34d;"><strong>Amount Due:</strong></td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #fcd34d; font-weight: bold;">' . $currency . ' ' . number_format($amount, 2) . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Due Date:</strong></td>
                    <td style="padding: 8px 0; color: #dc2626; font-weight: bold;">' . date('d M Y', strtotime($due_date)) . '</td>
                </tr>
            </table>
        </div>
        <p>Please ensure timely payment to avoid any penalties. If you have already made the payment, please disregard this reminder.</p>
    ';

    return send_templated_email($to, 'Payment Reminder - ' . $loan_number, $body);
}

/**
 * Send payment received confirmation email
 * @param string $to Customer email
 * @param string $name Customer name
 * @param string $loan_number Loan reference number
 * @param float $amount Payment amount received
 * @param float $balance Remaining balance
 * @return array
 */
function send_payment_received_email($to, $name, $loan_number, $amount, $balance) {
    $ci =& get_instance();
    $settings = $ci->db->get_where('settings', array('settings_id' => 1))->row();
    $currency = $settings->currency ?? 'ZMW';

    $body = '
        <h2 style="color: #059669;">Payment Received</h2>
        <p>Dear ' . htmlspecialchars($name) . ',</p>
        <p>We have received your payment. Thank you!</p>
        <div style="background: #dcfce7; border: 1px solid #059669; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #86efac;"><strong>Loan Reference:</strong></td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #86efac;">' . htmlspecialchars($loan_number) . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #86efac;"><strong>Amount Received:</strong></td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #86efac; font-weight: bold; color: #059669;">' . $currency . ' ' . number_format($amount, 2) . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #86efac;"><strong>Payment Date:</strong></td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #86efac;">' . date('d M Y H:i') . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Outstanding Balance:</strong></td>
                    <td style="padding: 8px 0; font-weight: bold;">' . $currency . ' ' . number_format($balance, 2) . '</td>
                </tr>
            </table>
        </div>
        <p>Thank you for your timely payment.</p>
    ';

    return send_templated_email($to, 'Payment Confirmation - ' . $loan_number, $body);
}

/**
 * Send loan disbursement notification
 * @param string $to Customer email
 * @param string $name Customer name
 * @param string $loan_number Loan reference number
 * @param float $amount Disbursed amount
 * @param string $account_number Account number where funds were sent
 * @return array
 */
function send_loan_disbursement_email($to, $name, $loan_number, $amount, $account_number = '') {
    $ci =& get_instance();
    $settings = $ci->db->get_where('settings', array('settings_id' => 1))->row();
    $currency = $settings->currency ?? 'ZMW';

    $account_html = !empty($account_number) ?
        '<tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #86efac;"><strong>Account:</strong></td>
            <td style="padding: 8px 0; border-bottom: 1px solid #86efac;">' . htmlspecialchars($account_number) . '</td>
        </tr>' : '';

    $body = '
        <h2 style="color: #059669;">Loan Disbursed</h2>
        <p>Dear ' . htmlspecialchars($name) . ',</p>
        <p>Great news! Your loan has been disbursed successfully.</p>
        <div style="background: #dcfce7; border: 1px solid #059669; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #86efac;"><strong>Loan Reference:</strong></td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #86efac;">' . htmlspecialchars($loan_number) . '</td>
                </tr>
                ' . $account_html . '
                <tr>
                    <td style="padding: 8px 0; border-bottom: 1px solid #86efac;"><strong>Amount Disbursed:</strong></td>
                    <td style="padding: 8px 0; border-bottom: 1px solid #86efac; font-size: 18px; font-weight: bold; color: #059669;">' . $currency . ' ' . number_format($amount, 2) . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0;"><strong>Date:</strong></td>
                    <td style="padding: 8px 0;">' . date('d M Y') . '</td>
                </tr>
            </table>
        </div>
        <p>Please remember to make your repayments on time according to your payment schedule.</p>
    ';

    return send_templated_email($to, 'Loan Disbursed - ' . $loan_number, $body);
}

/**
 * Send customer registration welcome email
 * @param string $to Customer email
 * @param string $name Customer name
 * @param string $client_id Customer ID
 * @return array
 */
function send_welcome_email($to, $name, $client_id) {
    $ci =& get_instance();
    $settings = $ci->db->get_where('settings', array('settings_id' => 1))->row();
    $company_name = $settings->company_name ?? 'FundIt';

    $body = '
        <h2 style="color: #1e3a5f;">Welcome to ' . htmlspecialchars($company_name) . '!</h2>
        <p>Dear ' . htmlspecialchars($name) . ',</p>
        <p>Thank you for registering with us. Your account has been created successfully.</p>
        <div style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <p style="margin: 0;"><strong>Your Customer ID:</strong> <code style="background: #1e3a5f; color: #fff; padding: 4px 8px; border-radius: 4px;">' . htmlspecialchars($client_id) . '</code></p>
        </div>
        <p>Please keep this ID safe as you will need it for future reference.</p>
        <p>If you have any questions, feel free to contact us.</p>
    ';

    return send_templated_email($to, 'Welcome to ' . $company_name, $body);
}

// ============================================================================
// MENU ACCESS & NOTIFICATION HELPER FUNCTIONS
// ============================================================================

/**
 * Get all employees who have access to a specific menu method
 * @param string $method The menu method (e.g., 'Loan/recommend')
 * @return array Array of employee objects with id, Firstname, Lastname, EmailAddress
 */
function get_employees_with_menu_access($method) {
    $ci =& get_instance();
    $ci->load->database();

    // Get the menuitem id for this method
    $menuitem = $ci->db->select('id')
        ->from('menuitems')
        ->where('LOWER(method)', strtolower($method))
        ->get()->row();

    if (!$menuitem) {
        return array();
    }

    // Get all role ids that have access to this menuitem
    $roles = $ci->db->select('roleid')
        ->from('access')
        ->where('controllerid', $menuitem->id)
        ->get()->result();

    if (empty($roles)) {
        return array();
    }

    $role_ids = array();
    foreach ($roles as $role) {
        $role_ids[] = $role->roleid;
    }

    // Get all employees with these roles who have email addresses
    $employees = $ci->db->select('id, Firstname, Lastname, EmailAddress')
        ->from('employees')
        ->where_in('Role', $role_ids)
        ->where('EmailAddress IS NOT NULL')
        ->where('EmailAddress !=', '')
        ->get()->result();

    return $employees;
}

/**
 * Send notification to all employees with access to a specific menu method
 * @param string $method The menu method (e.g., 'Loan/recommend')
 * @param string $subject Email subject
 * @param string $body Email body (HTML)
 * @param int $exclude_user_id Optional user ID to exclude (e.g., the creator)
 * @param array $log_options Optional logging options (notification_type, reference_type, reference_id, reference_number)
 * @return array ['sent' => int, 'failed' => int, 'logs' => array]
 */
function notify_users_with_access($method, $subject, $body, $exclude_user_id = null, $log_options = array()) {
    // Prevent PHP execution timeout when sending to many recipients
    set_time_limit(0);

    $employees = get_employees_with_menu_access($method);

    $sent = 0;
    $failed = 0;
    $logs = array();

    foreach ($employees as $employee) {
        // Skip excluded user (e.g., the person who created the loan)
        if ($exclude_user_id && $employee->id == $exclude_user_id) {
            continue;
        }

        $result = send_templated_email(
            $employee->EmailAddress,
            $subject,
            $body
        );

        // Log the notification — include real SMTP error so it's visible in the UI
        $error_msg = null;
        if (!$result['success']) {
            $error_msg = $result['message'] ?? 'Unknown error';
        }

        $log_data = array_merge($log_options, array(
            'recipient_email'  => $employee->EmailAddress,
            'recipient_name'   => $employee->Firstname . ' ' . $employee->Lastname,
            'recipient_user_id'=> $employee->id,
            'subject'          => $subject,
            'status'           => $result['success'] ? 'sent' : 'failed',
            'error_message'    => $error_msg
        ));
        $log_id = log_notification($log_data);
        $logs[] = $log_id;

        if ($result['success']) {
            $sent++;
        } else {
            $failed++;
        }
    }

    return array('sent' => $sent, 'failed' => $failed, 'logs' => $logs);
}

/**
 * Send loan creation notification to users who can recommend loans
 * @param array $loan_data Array with loan_id, loan_number, customer_name, amount, currency
 * @param int $created_by User ID who created the loan
 * @return array ['sent' => int, 'failed' => int]
 */
function notify_loan_recommenders($loan_data, $created_by = null) {
    $ci =& get_instance();
    $ci->load->database();

    $loan_number = $loan_data['loan_number'] ?? 'N/A';
    $customer_name = $loan_data['customer_name'] ?? 'N/A';
    $amount = $loan_data['amount'] ?? 0;
    $currency = $loan_data['currency'] ?? 'ZMW';
    $loan_id = $loan_data['loan_id'] ?? '';

    // Get creator name
    $creator_name = 'System';
    if ($created_by) {
        $creator = $ci->db->get_where('employees', array('id' => $created_by))->row();
        if ($creator) {
            $creator_name = $creator->Firstname . ' ' . $creator->Lastname;
        }
    }

    $view_url = base_url('loan/view/' . $loan_id);

    $body = '
        <h2 style="color: #1e3a5f;">New Loan Application Requires Your Attention</h2>
        <p>A new loan application has been created and requires your review.</p>
        <div style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb; width: 40%;"><strong>Loan Reference:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #1e3a5f;">' . htmlspecialchars($loan_number) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;"><strong>Customer:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">' . htmlspecialchars($customer_name) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;"><strong>Amount Requested:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb; font-size: 18px; font-weight: bold; color: #059669;">' . htmlspecialchars($currency) . ' ' . number_format($amount, 2) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;"><strong>Created By:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">' . htmlspecialchars($creator_name) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0;"><strong>Date:</strong></td>
                    <td style="padding: 10px 0;">' . date('d M Y, H:i') . '</td>
                </tr>
            </table>
        </div>
        <p style="text-align: center; margin: 25px 0;">
            <a href="' . $view_url . '" style="display: inline-block; background: #1e3a5f; color: #fff; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                View Loan Application
            </a>
        </p>
        <p style="color: #6b7280; font-size: 13px;">Please log in to the system to review and process this application.</p>
    ';

    // Logging options
    $log_options = array(
        'notification_type' => 'loan_created',
        'reference_type' => 'loan',
        'reference_id' => $loan_id,
        'reference_number' => $loan_number,
        'triggered_by' => $created_by
    );

    return notify_users_with_access('Loan/recommend', 'New Loan Application - ' . $loan_number, $body, $created_by, $log_options);
}

/**
 * Send loan approval notification to users who can approve loans (next level)
 * @param array $loan_data Array with loan_id, loan_number, customer_name, amount, currency
 * @param string $approval_method The menu method for approvers (e.g., 'loan/initiated')
 * @param int $recommended_by User ID who recommended the loan
 * @return array ['sent' => int, 'failed' => int]
 */
function notify_loan_approvers($loan_data, $approval_method, $recommended_by = null) {
    $ci =& get_instance();
    $ci->load->database();

    $loan_number = $loan_data['loan_number'] ?? 'N/A';
    $customer_name = $loan_data['customer_name'] ?? 'N/A';
    $amount = $loan_data['amount'] ?? 0;
    $currency = $loan_data['currency'] ?? 'ZMW';
    $loan_id = $loan_data['loan_id'] ?? '';

    // Get recommender name
    $recommender_name = 'System';
    if ($recommended_by) {
        $recommender = $ci->db->get_where('employees', array('id' => $recommended_by))->row();
        if ($recommender) {
            $recommender_name = $recommender->Firstname . ' ' . $recommender->Lastname;
        }
    }

    $view_url = base_url('loan/view/' . $loan_id);

    $body = '
        <h2 style="color: #f59e0b;">Loan Application Recommended for Approval</h2>
        <p>A loan application has been recommended and requires your approval.</p>
        <div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #fcd34d; width: 40%;"><strong>Loan Reference:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #fcd34d; font-weight: 600; color: #1e3a5f;">' . htmlspecialchars($loan_number) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #fcd34d;"><strong>Customer:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #fcd34d;">' . htmlspecialchars($customer_name) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #fcd34d;"><strong>Amount:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #fcd34d; font-size: 18px; font-weight: bold; color: #059669;">' . htmlspecialchars($currency) . ' ' . number_format($amount, 2) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #fcd34d;"><strong>Recommended By:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #fcd34d;">' . htmlspecialchars($recommender_name) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0;"><strong>Date:</strong></td>
                    <td style="padding: 10px 0;">' . date('d M Y, H:i') . '</td>
                </tr>
            </table>
        </div>
        <p style="text-align: center; margin: 25px 0;">
            <a href="' . $view_url . '" style="display: inline-block; background: #f59e0b; color: #fff; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                Review & Approve
            </a>
        </p>
        <p style="color: #6b7280; font-size: 13px;">Please log in to the system to review and approve this loan application.</p>
    ';

    // Logging options
    $log_options = array(
        'notification_type' => 'loan_recommended',
        'reference_type' => 'loan',
        'reference_id' => $loan_id,
        'reference_number' => $loan_number,
        'triggered_by' => $recommended_by
    );

    return notify_users_with_access($approval_method, 'Loan Pending Approval - ' . $loan_number, $body, $recommended_by, $log_options);
}

/**
 * Notify users when a loan has been fully approved (3rd approval)
 * @param array $loan_data Loan details (loan_id, loan_number, customer_name, amount, currency)
 * @param string $approval_method The menu method to check access (e.g., 'loan/approved')
 * @param int $approved_by User ID who gave final approval
 * @return array ['sent' => int, 'failed' => int]
 */
function notify_loan_approved($loan_data, $approval_method, $approved_by = null) {
    $ci =& get_instance();
    $ci->load->database();

    $loan_number = $loan_data['loan_number'] ?? 'N/A';
    $customer_name = $loan_data['customer_name'] ?? 'N/A';
    $amount = $loan_data['amount'] ?? 0;
    $currency = $loan_data['currency'] ?? 'ZMW';
    $loan_id = $loan_data['loan_id'] ?? '';

    // Get approver name
    $approver_name = 'System';
    if ($approved_by) {
        $approver = $ci->db->get_where('employees', array('id' => $approved_by))->row();
        if ($approver) {
            $approver_name = $approver->Firstname . ' ' . $approver->Lastname;
        }
    }

    $view_url = base_url('loan/view/' . $loan_id);

    $body = '
        <h2 style="color: #059669;">Loan Fully Approved - Ready for Disbursement</h2>
        <p>A loan application has received all required approvals and is now ready for disbursement.</p>
        <div style="background: #d1fae5; border: 1px solid #059669; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #6ee7b7; width: 40%;"><strong>Loan Reference:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #6ee7b7; font-weight: 600; color: #1e3a5f;">' . htmlspecialchars($loan_number) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #6ee7b7;"><strong>Customer:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #6ee7b7;">' . htmlspecialchars($customer_name) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #6ee7b7;"><strong>Amount:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #6ee7b7; font-size: 18px; font-weight: bold; color: #059669;">' . htmlspecialchars($currency) . ' ' . number_format($amount, 2) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #6ee7b7;"><strong>Final Approval By:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #6ee7b7;">' . htmlspecialchars($approver_name) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0;"><strong>Approved Date:</strong></td>
                    <td style="padding: 10px 0;">' . date('d M Y, H:i') . '</td>
                </tr>
            </table>
        </div>
        <p style="text-align: center; margin: 25px 0;">
            <a href="' . $view_url . '" style="display: inline-block; background: #059669; color: #fff; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                View Loan Details
            </a>
        </p>
        <p style="color: #6b7280; font-size: 13px;">This loan has been fully approved and is now ready for disbursement processing.</p>
    ';

    // Logging options
    $log_options = array(
        'notification_type' => 'loan_approved',
        'reference_type' => 'loan',
        'reference_id' => $loan_id,
        'reference_number' => $loan_number,
        'triggered_by' => $approved_by
    );

    return notify_users_with_access($approval_method, 'Loan Approved - ' . $loan_number, $body, $approved_by, $log_options);
}

/**
 * Notify loan creators to upload signed client copy after final approval
 */
function notify_loan_upload_signed($loan_data, $approved_by = null) {
    $ci =& get_instance();
    $ci->load->database();

    $loan_number = $loan_data['loan_number'] ?? 'N/A';
    $customer_name = $loan_data['customer_name'] ?? 'N/A';
    $amount = $loan_data['amount'] ?? 0;
    $currency = $loan_data['currency'] ?? 'ZMW';
    $loan_id = $loan_data['loan_id'] ?? '';

    // Get approver name
    $approver_name = 'System';
    if ($approved_by) {
        $approver = $ci->db->get_where('employees', array('id' => $approved_by))->row();
        if ($approver) {
            $approver_name = $approver->Firstname . ' ' . $approver->Lastname;
        }
    }

    $view_url = base_url('loan/view/' . $loan_id);

    $body = '
        <h2 style="color: #2563eb;">Loan Approved - Upload Signed Client Copy Required</h2>
        <p>A loan application has been fully approved. Please upload the signed client copy to proceed with disbursement.</p>
        <div style="background: #dbeafe; border: 1px solid #2563eb; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #93c5fd; width: 40%;"><strong>Loan Reference:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #93c5fd; font-weight: 600; color: #1e3a5f;">' . htmlspecialchars($loan_number) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #93c5fd;"><strong>Customer:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #93c5fd;">' . htmlspecialchars($customer_name) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #93c5fd;"><strong>Amount:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #93c5fd; font-size: 18px; font-weight: bold; color: #2563eb;">' . htmlspecialchars($currency) . ' ' . number_format($amount, 2) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #93c5fd;"><strong>Final Approval By:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #93c5fd;">' . htmlspecialchars($approver_name) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0;"><strong>Approved Date:</strong></td>
                    <td style="padding: 10px 0;">' . date('d M Y, H:i') . '</td>
                </tr>
            </table>
        </div>
        <div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; padding: 15px; margin: 20px 0;">
            <p style="margin: 0; color: #92400e;"><strong>Action Required:</strong> Please upload the signed client copy document, then click "Send for Disburse" to proceed.</p>
        </div>
        <p style="text-align: center; margin: 25px 0;">
            <a href="' . $view_url . '" style="display: inline-block; background: #2563eb; color: #fff; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                View Loan & Upload Document
            </a>
        </p>
    ';

    // Logging options
    $log_options = array(
        'notification_type' => 'loan_upload_signed',
        'reference_type' => 'loan',
        'reference_id' => $loan_id,
        'reference_number' => $loan_number,
        'triggered_by' => $approved_by
    );

    return notify_users_with_access('loan/loan_application', 'Upload Signed Copy - ' . $loan_number, $body, $approved_by, $log_options);
}

/**
 * Notify disbursers when loan is ready for disbursement (client signed)
 */
function notify_loan_ready_disburse($loan_data, $disburse_method, $sent_by = null) {
    $ci =& get_instance();
    $ci->load->database();

    $loan_number = $loan_data['loan_number'] ?? 'N/A';
    $customer_name = $loan_data['customer_name'] ?? 'N/A';
    $amount = $loan_data['amount'] ?? 0;
    $currency = $loan_data['currency'] ?? 'ZMW';
    $loan_id = $loan_data['loan_id'] ?? '';

    // Get sender name
    $sender_name = 'System';
    if ($sent_by) {
        $sender = $ci->db->get_where('employees', array('id' => $sent_by))->row();
        if ($sender) {
            $sender_name = $sender->Firstname . ' ' . $sender->Lastname;
        }
    }

    $view_url = base_url('loan/view/' . $loan_id);

    $body = '
        <h2 style="color: #059669;">Loan Ready for Disbursement - Client Copy Signed</h2>
        <p>A loan has been signed by the client and is now ready for disbursement.</p>
        <div style="background: #d1fae5; border: 1px solid #059669; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #6ee7b7; width: 40%;"><strong>Loan Reference:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #6ee7b7; font-weight: 600; color: #1e3a5f;">' . htmlspecialchars($loan_number) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #6ee7b7;"><strong>Customer:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #6ee7b7;">' . htmlspecialchars($customer_name) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #6ee7b7;"><strong>Amount:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #6ee7b7; font-size: 18px; font-weight: bold; color: #059669;">' . htmlspecialchars($currency) . ' ' . number_format($amount, 2) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #6ee7b7;"><strong>Sent for Disburse By:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #6ee7b7;">' . htmlspecialchars($sender_name) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0;"><strong>Date:</strong></td>
                    <td style="padding: 10px 0;">' . date('d M Y, H:i') . '</td>
                </tr>
            </table>
        </div>
        <p style="text-align: center; margin: 25px 0;">
            <a href="' . $view_url . '" style="display: inline-block; background: #059669; color: #fff; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                View & Disburse Loan
            </a>
        </p>
        <p style="color: #6b7280; font-size: 13px;">The client has signed all required documents. Please proceed with the disbursement process.</p>
    ';

    // Logging options
    $log_options = array(
        'notification_type' => 'loan_ready_disburse',
        'reference_type' => 'loan',
        'reference_id' => $loan_id,
        'reference_number' => $loan_number,
        'triggered_by' => $sent_by
    );

    return notify_users_with_access($disburse_method, 'Ready for Disbursement - ' . $loan_number, $body, $sent_by, $log_options);
}

/**
 * Notify loan stakeholders when a new internal note is added
 * Sends to users with: loan/loan_application (create), loan/recommend, loan/unified_approval (approve), loan/approved (disburse)
 * @param array $loan_data Array with loan_id, loan_number, customer_name
 * @param string $note_text The note content
 * @param int $note_by User ID who added the note
 * @return array ['sent' => int, 'failed' => int]
 */
function notify_loan_note_added($loan_data, $note_text, $note_by = null) {
    $ci =& get_instance();
    $ci->load->database();

    $loan_number = $loan_data['loan_number'] ?? 'N/A';
    $customer_name = $loan_data['customer_name'] ?? 'N/A';
    $loan_id = $loan_data['loan_id'] ?? '';

    // Get note author name
    $author_name = 'System';
    if ($note_by) {
        $author = $ci->db->get_where('employees', array('id' => $note_by))->row();
        if ($author) {
            $author_name = $author->Firstname . ' ' . $author->Lastname;
        }
    }

    // URL with parameter to open notes modal
    $view_url = base_url('loan/view/' . $loan_id . '?open_notes=1');

    // Truncate note for preview (max 200 chars)
    $note_preview = strlen($note_text) > 200 ? substr($note_text, 0, 200) . '...' : $note_text;

    $body = '
        <h2 style="color: #1e3a5f;">New Internal Note Added</h2>
        <p>A new internal note has been added to a loan application.</p>
        <div style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb; width: 30%;"><strong>Loan Reference:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #1e3a5f;">' . htmlspecialchars($loan_number) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;"><strong>Customer:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">' . htmlspecialchars($customer_name) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;"><strong>Note By:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">' . htmlspecialchars($author_name) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0;"><strong>Date:</strong></td>
                    <td style="padding: 10px 0;">' . date('d M Y, H:i') . '</td>
                </tr>
            </table>
        </div>
        <div style="background: #eff6ff; border-left: 4px solid #3b82f6; border-radius: 4px; padding: 15px; margin: 20px 0;">
            <p style="margin: 0 0 5px 0; font-weight: 600; color: #1e40af;">Note Preview:</p>
            <p style="margin: 0; color: #374151; font-style: italic;">"' . htmlspecialchars($note_preview) . '"</p>
        </div>
        <p style="text-align: center; margin: 25px 0;">
            <a href="' . $view_url . '" style="display: inline-block; background: #1e3a5f; color: #fff; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                <span style="margin-right: 8px;">&#128172;</span> View Notes
            </a>
        </p>
        <p style="color: #6b7280; font-size: 13px;">Click the button above to view all notes for this loan.</p>
    ';

    // Logging options
    $log_options = array(
        'notification_type' => 'loan_note_added',
        'reference_type' => 'loan',
        'reference_id' => $loan_id,
        'reference_number' => $loan_number,
        'triggered_by' => $note_by
    );

    // Collect all recipients from different access levels
    $total_sent = 0;
    $total_failed = 0;
    $all_logs = array();
    $notified_emails = array(); // Track already notified to avoid duplicates

    // Methods to notify
    $methods = array(
        'loan/loan_application',  // Creators
        'loan/recommend',         // Recommenders
        'loan/unified_approval',  // Approvers
        'loan/approved'           // Disbursers
    );

    foreach ($methods as $method) {
        $employees = get_employees_with_menu_access($method);

        foreach ($employees as $employee) {
            // Skip the note author
            if ($note_by && $employee->id == $note_by) {
                continue;
            }

            // Skip if already notified (user has multiple access levels)
            if (in_array($employee->EmailAddress, $notified_emails)) {
                continue;
            }

            $result = send_templated_email(
                $employee->EmailAddress,
                'New Loan Note - ' . $loan_number,
                $body
            );

            // Log the notification
            $log_data = array_merge($log_options, array(
                'recipient_email' => $employee->EmailAddress,
                'recipient_name' => $employee->Firstname . ' ' . $employee->Lastname,
                'recipient_user_id' => $employee->id,
                'subject' => 'New Loan Note - ' . $loan_number,
                'status' => $result['success'] ? 'sent' : 'failed',
                'error_message' => $result['success'] ? null : ($result['message'] ?? 'Unknown error')
            ));
            $log_id = log_notification($log_data);
            $all_logs[] = $log_id;

            if ($result['success']) {
                $total_sent++;
            } else {
                $total_failed++;
            }

            $notified_emails[] = $employee->EmailAddress;
        }
    }

    return array('sent' => $total_sent, 'failed' => $total_failed, 'logs' => $all_logs);
}

/**
 * Notify the loan creator of any status change on their loan.
 * This is a purely informational notification - no action required language.
 *
 * @param array  $loan_data   Array with loan_id, loan_number, customer_name, amount, currency
 * @param string $new_status  The new loan status (e.g., RECOMMENDED, APPROVED_FIRST, APPROVED, ACTIVE, REJECTED, CLIENT_SIGNED)
 * @param int    $action_by   User ID who performed the action
 * @param int    $creator_id  User ID of the loan creator (loan_added_by)
 * @return array ['sent' => int, 'failed' => int]
 */
function notify_loan_creator($loan_data, $new_status, $action_by = null, $creator_id = null) {
    $ci =& get_instance();
    $ci->load->database();

    if (!$creator_id) {
        return array('sent' => 0, 'failed' => 0);
    }

    // Don't notify creator if they are the one who performed the action
    if ($action_by && $creator_id == $action_by) {
        return array('sent' => 0, 'failed' => 0);
    }

    // Get creator details
    $creator = $ci->db->get_where('employees', array('id' => $creator_id))->row();
    if (!$creator || empty($creator->EmailAddress)) {
        return array('sent' => 0, 'failed' => 0);
    }

    $loan_number = $loan_data['loan_number'] ?? 'N/A';
    $customer_name = $loan_data['customer_name'] ?? 'N/A';
    $amount = $loan_data['amount'] ?? 0;
    $currency = $loan_data['currency'] ?? 'ZMW';
    $loan_id = $loan_data['loan_id'] ?? '';

    // Get action performer name
    $action_by_name = 'System';
    if ($action_by) {
        $performer = $ci->db->get_where('employees', array('id' => $action_by))->row();
        if ($performer) {
            $action_by_name = $performer->Firstname . ' ' . $performer->Lastname;
        }
    }

    $view_url = base_url('loan/view/' . $loan_id);

    // Status-specific messaging
    $status_config = array(
        'RECOMMENDED' => array(
            'color' => '#f59e0b',
            'bg' => '#fef3c7',
            'border' => '#fcd34d',
            'title' => 'Loan Has Been Recommended',
            'description' => 'Your loan application has been recommended and is now pending approval.',
            'status_label' => 'Recommended',
            'action_label' => 'Recommended By'
        ),
        'APPROVED_FIRST' => array(
            'color' => '#3b82f6',
            'bg' => '#dbeafe',
            'border' => '#93c5fd',
            'title' => 'Loan Received First Approval',
            'description' => 'Your loan application has received its first approval and is progressing through the approval process.',
            'status_label' => 'First Approval',
            'action_label' => 'Approved By'
        ),
        'APPROVED_SECOND' => array(
            'color' => '#3b82f6',
            'bg' => '#dbeafe',
            'border' => '#93c5fd',
            'title' => 'Loan Received Second Approval',
            'description' => 'Your loan application has received its second approval and is progressing through the approval process.',
            'status_label' => 'Second Approval',
            'action_label' => 'Approved By'
        ),
        'APPROVED' => array(
            'color' => '#059669',
            'bg' => '#d1fae5',
            'border' => '#6ee7b7',
            'title' => 'Loan Fully Approved',
            'description' => 'Your loan application has received all required approvals. Please upload the signed client copy to proceed.',
            'status_label' => 'Fully Approved',
            'action_label' => 'Final Approval By'
        ),
        'CLIENT_SIGNED' => array(
            'color' => '#059669',
            'bg' => '#d1fae5',
            'border' => '#6ee7b7',
            'title' => 'Loan Sent for Disbursement',
            'description' => 'The loan has been sent for disbursement after client documents were signed.',
            'status_label' => 'Client Signed',
            'action_label' => 'Sent By'
        ),
        'ACTIVE' => array(
            'color' => '#059669',
            'bg' => '#d1fae5',
            'border' => '#6ee7b7',
            'title' => 'Loan Disbursed',
            'description' => 'The loan has been disbursed and is now active.',
            'status_label' => 'Disbursed / Active',
            'action_label' => 'Disbursed By'
        ),
        'REJECTED' => array(
            'color' => '#dc2626',
            'bg' => '#fee2e2',
            'border' => '#fca5a5',
            'title' => 'Loan Rejected',
            'description' => 'The loan application has been rejected.',
            'status_label' => 'Rejected',
            'action_label' => 'Rejected By'
        ),
        'WRITTEN_OFF' => array(
            'color' => '#6b7280',
            'bg' => '#f3f4f6',
            'border' => '#d1d5db',
            'title' => 'Loan Written Off',
            'description' => 'The loan has been written off.',
            'status_label' => 'Written Off',
            'action_label' => 'Written Off By'
        )
    );

    // Default config for unknown statuses
    $config = isset($status_config[$new_status]) ? $status_config[$new_status] : array(
        'color' => '#1e3a5f',
        'bg' => '#f8fafc',
        'border' => '#e5e7eb',
        'title' => 'Loan Status Updated to ' . $new_status,
        'description' => 'The status of your loan application has been updated.',
        'status_label' => $new_status,
        'action_label' => 'Updated By'
    );

    $subject = 'Loan Update: ' . $config['status_label'] . ' - ' . $loan_number;

    $body = '
        <h2 style="color: ' . $config['color'] . ';">' . $config['title'] . '</h2>
        <p>Dear ' . htmlspecialchars($creator->Firstname) . ',</p>
        <p>' . $config['description'] . '</p>
        <div style="background: ' . $config['bg'] . '; border: 1px solid ' . $config['color'] . '; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid ' . $config['border'] . '; width: 40%;"><strong>Loan Reference:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid ' . $config['border'] . '; font-weight: 600; color: #1e3a5f;">' . htmlspecialchars($loan_number) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid ' . $config['border'] . ';"><strong>Customer:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid ' . $config['border'] . ';">' . htmlspecialchars($customer_name) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid ' . $config['border'] . ';"><strong>Amount:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid ' . $config['border'] . '; font-size: 18px; font-weight: bold; color: ' . $config['color'] . ';">' . htmlspecialchars($currency) . ' ' . number_format($amount, 2) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid ' . $config['border'] . ';"><strong>New Status:</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid ' . $config['border'] . ';"><span style="display:inline-block; padding: 4px 12px; border-radius: 4px; background: ' . $config['color'] . '; color: #fff; font-weight: 600; font-size: 13px;">' . $config['status_label'] . '</span></td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; border-bottom: 1px solid ' . $config['border'] . ';"><strong>' . $config['action_label'] . ':</strong></td>
                    <td style="padding: 10px 0; border-bottom: 1px solid ' . $config['border'] . ';">' . htmlspecialchars($action_by_name) . '</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0;"><strong>Date:</strong></td>
                    <td style="padding: 10px 0;">' . date('d M Y, H:i') . '</td>
                </tr>
            </table>
        </div>
        <p style="text-align: center; margin: 25px 0;">
            <a href="' . $view_url . '" style="display: inline-block; background: ' . $config['color'] . '; color: #fff; padding: 12px 30px; border-radius: 6px; text-decoration: none; font-weight: 600;">
                View Loan Details
            </a>
        </p>
        <p style="color: #6b7280; font-size: 13px;">This is an informational notification about a loan you created.</p>
    ';

    $result = send_templated_email($creator->EmailAddress, $subject, $body);

    // Log the notification
    $log_data = array(
        'notification_type' => 'loan_creator_update',
        'reference_type' => 'loan',
        'reference_id' => $loan_id,
        'reference_number' => $loan_number,
        'triggered_by' => $action_by,
        'recipient_email' => $creator->EmailAddress,
        'recipient_name' => $creator->Firstname . ' ' . $creator->Lastname,
        'recipient_user_id' => $creator_id,
        'subject' => $subject,
        'status' => $result['success'] ? 'sent' : 'failed',
        'error_message' => $result['success'] ? null : ($result['message'] ?? 'Unknown error')
    );
    log_notification($log_data);

    return array('sent' => $result['success'] ? 1 : 0, 'failed' => $result['success'] ? 0 : 1);
}
