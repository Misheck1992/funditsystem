<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Email extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('common_queries');

        // Check if user is logged in
        if (!$this->session->userdata('user_id')) {
            redirect('Login');
        }
    }

    /**
     * Main email management page
     */
    public function index()
    {
        $data = array(
            'page_title' => 'Email Management'
        );

        // Get settings for display
        $data['settings'] = $this->db->get_where('settings', array('settings_id' => 1))->row();

        // Get email templates if any
        $data['templates'] = $this->get_email_templates();

        // Get customer groups for bulk email
        $data['customer_count'] = $this->get_customer_counts();

        $this->load->view('admin/header');
        $this->load->view('email/index', $data);
        $this->load->view('admin/footer');
    }

    /**
     * Send test email
     */
    public function send_test()
    {
        $to = $this->input->post('to');
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        $use_template = $this->input->post('use_template') == '1';

        if (empty($to) || empty($subject) || empty($message)) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Please fill in all required fields'
            ));
            return;
        }

        // Validate email
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Please enter a valid email address'
            ));
            return;
        }

        // Handle file attachments
        $attachments = array();
        if (!empty($_FILES['attachments']['name'][0])) {
            $attachments = $this->_upload_attachments('attachments');
            log_message('debug', 'Uploaded attachments: ' . print_r($attachments, true));
        }

        // Send email with attachments
        $options = array('attachments' => $attachments);
        if ($use_template) {
            $result = send_templated_email($to, $subject, $message, $options);
        } else {
            $result = send_smtp_email($to, $subject, $message, $options);
        }

        // Add attachment info to result for debugging
        if (!empty($attachments)) {
            $result['attachments_count'] = count($attachments);
        }

        // Clean up uploaded attachments AFTER sending
        $this->_cleanup_attachments($attachments);

        // Log the email
        $this->log_email($to, $subject, $result['success'] ? 'sent' : 'failed', 'test');

        echo json_encode($result);
    }

    /**
     * Upload attachments to temp directory
     */
    private function _upload_attachments($field_name)
    {
        $attachments = array();
        $upload_path = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;

        // Create temp directory if it doesn't exist
        if (!is_dir($upload_path)) {
            @mkdir($upload_path, 0755, true);
        }

        // Log for debugging
        log_message('debug', 'Upload path: ' . $upload_path);
        log_message('debug', 'FILES data: ' . print_r($_FILES, true));

        $allowed_ext = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'zip');
        $max_size = 10240 * 1024; // 10MB in bytes

        if (isset($_FILES[$field_name]) && is_array($_FILES[$field_name]['name'])) {
            foreach ($_FILES[$field_name]['name'] as $key => $name) {
                if (empty($name)) continue;

                // Check for upload errors
                if ($_FILES[$field_name]['error'][$key] !== UPLOAD_ERR_OK) {
                    log_message('error', 'Upload error for ' . $name . ': ' . $_FILES[$field_name]['error'][$key]);
                    continue;
                }

                $file = array(
                    'name'     => $_FILES[$field_name]['name'][$key],
                    'type'     => $_FILES[$field_name]['type'][$key],
                    'tmp_name' => $_FILES[$field_name]['tmp_name'][$key],
                    'error'    => $_FILES[$field_name]['error'][$key],
                    'size'     => $_FILES[$field_name]['size'][$key]
                );

                // Check file size
                if ($file['size'] > $max_size) {
                    log_message('error', 'File too large: ' . $name . ' (' . $file['size'] . ' bytes)');
                    continue;
                }

                // Check file extension
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed_ext)) {
                    log_message('error', 'Invalid file type: ' . $name . ' (' . $ext . ')');
                    continue;
                }

                // Generate unique filename
                $new_name = uniqid('attach_') . '.' . $ext;
                $destination = $upload_path . $new_name;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $attachments[] = array(
                        'path' => $destination,
                        'name' => $file['name']
                    );
                    log_message('debug', 'File uploaded: ' . $destination);
                } else {
                    log_message('error', 'Failed to move file: ' . $file['tmp_name'] . ' to ' . $destination);
                }
            }
        }

        return $attachments;
    }

    /**
     * Clean up temporary attachment files
     */
    private function _cleanup_attachments($attachments)
    {
        foreach ($attachments as $attachment) {
            if (isset($attachment['path']) && file_exists($attachment['path'])) {
                @unlink($attachment['path']);
            }
        }
    }

    /**
     * Send bulk email
     */
    public function send_bulk()
    {
        $recipient_type = $this->input->post('recipient_type');
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        $use_template = $this->input->post('use_template') == '1';

        if (empty($subject) || empty($message)) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Please fill in subject and message'
            ));
            return;
        }

        // Get recipients based on type
        $recipients = $this->get_recipients($recipient_type);

        if (empty($recipients)) {
            echo json_encode(array(
                'success' => false,
                'message' => 'No recipients found for the selected group'
            ));
            return;
        }

        $sent = 0;
        $failed = 0;
        $errors = array();

        foreach ($recipients as $recipient) {
            $email = $recipient->email;
            $name = $recipient->name;

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $failed++;
                continue;
            }

            // Personalize message
            $personalized_message = str_replace(
                array('{name}', '{NAME}', '{email}', '{EMAIL}'),
                array($name, $name, $email, $email),
                $message
            );

            // Send email
            if ($use_template) {
                $result = send_templated_email($email, $subject, $personalized_message);
            } else {
                $result = send_smtp_email($email, $subject, $personalized_message);
            }

            if ($result['success']) {
                $sent++;
                $this->log_email($email, $subject, 'sent', 'bulk');
            } else {
                $failed++;
                $errors[] = $email . ': ' . $result['message'];
                $this->log_email($email, $subject, 'failed', 'bulk');
            }

            // Small delay to prevent overwhelming the SMTP server
            usleep(100000); // 0.1 second
        }

        echo json_encode(array(
            'success' => true,
            'message' => "Bulk email completed. Sent: $sent, Failed: $failed",
            'sent' => $sent,
            'failed' => $failed,
            'errors' => array_slice($errors, 0, 5) // Only return first 5 errors
        ));
    }

    /**
     * Get recipients based on type
     */
    private function get_recipients($type)
    {
        $recipients = array();

        switch ($type) {
            case 'all_customers':
                // Individual customers
                $individual = $this->db->select('EmailAddress as email, CONCAT(Firstname, " ", Lastname) as name')
                    ->from('individual_customers')
                    ->where('EmailAddress IS NOT NULL')
                    ->where('EmailAddress !=', '')
                    ->get()->result();

                // Corporate customers
                $corporate = $this->db->select('contact_email as email, EntityName as name')
                    ->from('corporate_customers')
                    ->where('contact_email IS NOT NULL')
                    ->where('contact_email !=', '')
                    ->get()->result();

                $recipients = array_merge($individual, $corporate);
                break;

            case 'individual_customers':
                $recipients = $this->db->select('EmailAddress as email, CONCAT(Firstname, " ", Lastname) as name')
                    ->from('individual_customers')
                    ->where('EmailAddress IS NOT NULL')
                    ->where('EmailAddress !=', '')
                    ->get()->result();
                break;

            case 'corporate_customers':
                $recipients = $this->db->select('contact_email as email, EntityName as name')
                    ->from('corporate_customers')
                    ->where('contact_email IS NOT NULL')
                    ->where('contact_email !=', '')
                    ->get()->result();
                break;

            case 'active_borrowers':
                // Customers with active loans
                $individual = $this->db->select('DISTINCT ic.EmailAddress as email, CONCAT(ic.Firstname, " ", ic.Lastname) as name')
                    ->from('loan l')
                    ->join('individual_customers ic', 'ic.id = l.loan_customer AND l.customer_type = "individual"', 'inner')
                    ->where('l.loan_status', 'active')
                    ->where('ic.EmailAddress IS NOT NULL')
                    ->where('ic.EmailAddress !=', '')
                    ->get()->result();

                $corporate = $this->db->select('DISTINCT cc.contact_email as email, cc.EntityName as name')
                    ->from('loan l')
                    ->join('corporate_customers cc', 'cc.id = l.loan_customer AND l.customer_type = "corporate"', 'inner')
                    ->where('l.loan_status', 'active')
                    ->where('cc.contact_email IS NOT NULL')
                    ->where('cc.contact_email !=', '')
                    ->get()->result();

                $recipients = array_merge($individual, $corporate);
                break;

            case 'arrears_customers':
                // Customers in arrears
                $individual = $this->db->select('DISTINCT ic.EmailAddress as email, CONCAT(ic.Firstname, " ", ic.Lastname) as name')
                    ->from('loan l')
                    ->join('individual_customers ic', 'ic.id = l.loan_customer AND l.customer_type = "individual"', 'inner')
                    ->join('payement_schedules ps', 'ps.loan_id = l.loan_id', 'inner')
                    ->where('l.loan_status', 'active')
                    ->where('ps.status !=', 'paid')
                    ->where('ps.payment_schedule <', date('Y-m-d'))
                    ->where('ic.EmailAddress IS NOT NULL')
                    ->where('ic.EmailAddress !=', '')
                    ->get()->result();

                $corporate = $this->db->select('DISTINCT cc.contact_email as email, cc.EntityName as name')
                    ->from('loan l')
                    ->join('corporate_customers cc', 'cc.id = l.loan_customer AND l.customer_type = "corporate"', 'inner')
                    ->join('payement_schedules ps', 'ps.loan_id = l.loan_id', 'inner')
                    ->where('l.loan_status', 'active')
                    ->where('ps.status !=', 'paid')
                    ->where('ps.payment_schedule <', date('Y-m-d'))
                    ->where('cc.contact_email IS NOT NULL')
                    ->where('cc.contact_email !=', '')
                    ->get()->result();

                $recipients = array_merge($individual, $corporate);
                break;

            case 'employees':
                $recipients = $this->db->select('EmailAddress as email, CONCAT(Firstname, " ", Lastname) as name')
                    ->from('employees')
                    ->where('EmailAddress IS NOT NULL')
                    ->where('EmailAddress !=', '')
                    ->get()->result();
                break;

            case 'custom':
                // Custom emails from textarea
                $custom_emails = $this->input->post('custom_emails');
                if (!empty($custom_emails)) {
                    $emails = preg_split('/[\r\n,;]+/', $custom_emails);
                    foreach ($emails as $email) {
                        $email = trim($email);
                        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $recipients[] = (object) array('email' => $email, 'name' => $email);
                        }
                    }
                }
                break;
        }

        return $recipients;
    }

    /**
     * Get customer counts for display
     */
    private function get_customer_counts()
    {
        $counts = array();

        // All customers
        $individual_count = $this->db->from('individual_customers')
            ->where('EmailAddress IS NOT NULL')
            ->where('EmailAddress !=', '')
            ->count_all_results();

        $corporate_count = $this->db->from('corporate_customers')
            ->where('contact_email IS NOT NULL')
            ->where('contact_email !=', '')
            ->count_all_results();

        $counts['all_customers'] = $individual_count + $corporate_count;
        $counts['individual_customers'] = $individual_count;
        $counts['corporate_customers'] = $corporate_count;

        // Active borrowers
        $counts['active_borrowers'] = $this->db->select('COUNT(DISTINCT l.loan_customer) as count')
            ->from('loan l')
            ->where('l.loan_status', 'active')
            ->get()->row()->count;

        // Arrears customers
        $counts['arrears_customers'] = $this->db->select('COUNT(DISTINCT l.loan_customer) as count')
            ->from('loan l')
            ->join('payement_schedules ps', 'ps.loan_id = l.loan_id', 'inner')
            ->where('l.loan_status', 'active')
            ->where('ps.status !=', 'paid')
            ->where('ps.payment_schedule <', date('Y-m-d'))
            ->get()->row()->count;

        // Employees
        $counts['employees'] = $this->db->from('employees')
            ->where('EmailAddress IS NOT NULL')
            ->where('EmailAddress !=', '')
            ->count_all_results();

        return $counts;
    }

    /**
     * Get email templates
     */
    private function get_email_templates()
    {
        // Predefined templates
        return array(
            array(
                'name' => 'Payment Reminder',
                'subject' => 'Payment Reminder - Your loan payment is due',
                'body' => '<h3>Payment Reminder</h3>
<p>Dear {name},</p>
<p>This is a friendly reminder that your loan payment is due. Please ensure timely payment to avoid any penalties.</p>
<p>If you have already made the payment, please disregard this reminder.</p>
<p>Thank you for your continued partnership.</p>'
            ),
            array(
                'name' => 'General Announcement',
                'subject' => 'Important Announcement',
                'body' => '<h3>Important Announcement</h3>
<p>Dear {name},</p>
<p>[Your announcement message here]</p>
<p>If you have any questions, please do not hesitate to contact us.</p>'
            ),
            array(
                'name' => 'Holiday Greeting',
                'subject' => 'Season\'s Greetings',
                'body' => '<h3>Season\'s Greetings!</h3>
<p>Dear {name},</p>
<p>Wishing you and your loved ones a wonderful holiday season filled with joy and prosperity.</p>
<p>Thank you for being a valued customer. We look forward to serving you in the coming year.</p>'
            ),
            array(
                'name' => 'New Product Announcement',
                'subject' => 'Introducing Our New Loan Product',
                'body' => '<h3>Exciting News!</h3>
<p>Dear {name},</p>
<p>We are pleased to announce our new loan product designed to meet your financial needs.</p>
<p>[Product details here]</p>
<p>Contact us today to learn more about how this product can benefit you.</p>'
            )
        );
    }

    /**
     * Log email for tracking
     */
    private function log_email($to, $subject, $status, $type)
    {
        // Check if email_logs table exists, if not skip logging
        if ($this->db->table_exists('email_logs')) {
            $this->db->insert('email_logs', array(
                'recipient' => $to,
                'subject' => $subject,
                'status' => $status,
                'type' => $type,
                'sent_by' => $this->session->userdata('user_id'),
                'sent_at' => date('Y-m-d H:i:s')
            ));
        }
    }

    /**
     * Get email logs
     */
    public function get_logs()
    {
        if (!$this->db->table_exists('email_logs')) {
            echo json_encode(array('data' => array()));
            return;
        }

        $logs = $this->db->select('email_logs.*, CONCAT(employees.Firstname, " ", employees.Lastname) as sent_by_name')
            ->from('email_logs')
            ->join('employees', 'employees.id = email_logs.sent_by', 'left')
            ->order_by('sent_at', 'DESC')
            ->limit(100)
            ->get()->result();

        echo json_encode(array('data' => $logs));
    }

    /**
     * Get notification logs for DataTables
     */
    public function notification_logs()
    {
        $filters = array(
            'notification_type' => $this->input->get('type'),
            'status' => $this->input->get('status'),
            'reference_type' => $this->input->get('ref_type'),
            'reference_id' => $this->input->get('ref_id'),
            'recipient_email' => $this->input->get('email'),
            'date_from' => $this->input->get('from'),
            'date_to' => $this->input->get('to'),
            'limit' => 500
        );

        $logs = get_notification_logs($filters);

        $data = array();
        foreach ($logs as $log) {
            $status_badge = '';
            switch ($log->status) {
                case 'sent':
                    $status_badge = '<span class="badge" style="background:#059669;color:#fff;">Sent</span>';
                    break;
                case 'failed':
                    $status_badge = '<span class="badge" style="background:#dc2626;color:#fff;">Failed</span>';
                    break;
                default:
                    $status_badge = '<span class="badge" style="background:#f59e0b;color:#fff;">Pending</span>';
            }

            $type_badge = '';
            switch ($log->notification_type) {
                case 'loan_created':
                    $type_badge = '<span class="badge" style="background:#3b82f6;color:#fff;">Loan Created</span>';
                    break;
                case 'loan_recommended':
                    $type_badge = '<span class="badge" style="background:#f59e0b;color:#fff;">Loan Recommended</span>';
                    break;
                case 'loan_creator_update':
                    $type_badge = '<span class="badge" style="background:#8b5cf6;color:#fff;">Creator Update</span>';
                    break;
                case 'test':
                    $type_badge = '<span class="badge" style="background:#6b7280;color:#fff;">Test</span>';
                    break;
                case 'bulk':
                    $type_badge = '<span class="badge" style="background:#7c3aed;color:#fff;">Bulk</span>';
                    break;
                default:
                    $type_badge = '<span class="badge" style="background:#1e3a5f;color:#fff;">' . ucfirst($log->notification_type) . '</span>';
            }

            $triggered_by = $log->triggered_by_firstname ? $log->triggered_by_firstname . ' ' . $log->triggered_by_lastname : 'System';

            $reference_link = '';
            if ($log->reference_type == 'loan' && $log->reference_id) {
                $reference_link = '<a href="' . base_url('loan/view/' . $log->reference_id) . '" target="_blank">' . htmlspecialchars($log->reference_number) . '</a>';
            } else {
                $reference_link = htmlspecialchars($log->reference_number ?: '-');
            }

            $data[] = array(
                'log_id' => $log->log_id,
                'type' => $type_badge,
                'reference' => $reference_link,
                'recipient' => '<strong>' . htmlspecialchars($log->recipient_name ?: $log->recipient_email) . '</strong><br><small style="color:#6b7280;">' . htmlspecialchars($log->recipient_email) . '</small>',
                'subject' => htmlspecialchars(substr($log->subject, 0, 50)) . (strlen($log->subject) > 50 ? '...' : ''),
                'status' => $status_badge,
                'triggered_by' => $triggered_by,
                'created_at' => date('d M Y H:i', strtotime($log->created_at)),
                'error' => $log->error_message ? '<span title="' . htmlspecialchars($log->error_message) . '" style="color:#dc2626;cursor:help;"><i class="fa fa-exclamation-circle"></i></span>' : ''
            );
        }

        echo json_encode(array('data' => $data));
    }

    /**
     * Get notification logs for a specific loan
     */
    public function loan_notifications($loan_id)
    {
        $filters = array(
            'reference_type' => 'loan',
            'reference_id' => $loan_id,
            'limit' => 100
        );

        $logs = get_notification_logs($filters);
        echo json_encode(array('data' => $logs));
    }

    /**
     * Upload Excel file for bulk email
     */
    public function upload_excel()
    {
        header('Content-Type: application/json');

        if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Please select a valid Excel file to upload'
            ));
            return;
        }

        $file = $_FILES['excel_file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, array('xls', 'xlsx', 'csv'))) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Invalid file format. Please upload XLS, XLSX, or CSV file'
            ));
            return;
        }

        // Read the Excel file
        $recipients = $this->read_excel_file($file['tmp_name'], $ext);

        if (empty($recipients)) {
            echo json_encode(array(
                'success' => false,
                'message' => 'No valid email addresses found in the file. Make sure the file has columns for Name and Email.'
            ));
            return;
        }

        // Store in session for later use
        $this->session->set_userdata('excel_recipients', $recipients);

        echo json_encode(array(
            'success' => true,
            'message' => 'File uploaded successfully',
            'count' => count($recipients),
            'preview' => array_slice($recipients, 0, 5) // First 5 for preview
        ));
    }

    /**
     * Read Excel/CSV file and extract name and email
     */
    private function read_excel_file($filepath, $ext)
    {
        $recipients = array();
        $data = array();

        // Try to read the file based on extension
        if ($ext === 'csv') {
            $data = $this->read_csv_file($filepath);
        } elseif ($ext === 'xlsx') {
            $data = $this->read_xlsx_file($filepath);
        } elseif ($ext === 'xls') {
            // Try reading as CSV first (some XLS are actually CSV)
            $data = $this->read_csv_file($filepath);

            // If that didn't work, try XLSX format (some files are mislabeled)
            if (empty($data)) {
                $data = $this->read_xlsx_file($filepath);
            }

            // Try reading as tab-delimited
            if (empty($data)) {
                $data = $this->read_csv_file($filepath, "\t");
            }
        }

        if (empty($data)) {
            return array();
        }

        // Process the data to extract recipients
        $header = array_shift($data);

        if (empty($header)) {
            return array();
        }

        // Find name and email column indices
        $name_col = -1;
        $email_col = -1;

        foreach ($header as $i => $col) {
            $col_lower = strtolower(trim($col ?? ''));

            // Check for name columns
            if ($name_col === -1 && in_array($col_lower, array('name', 'full name', 'fullname', 'customer name', 'recipient name', 'firstname', 'first name', 'customer', 'recipient'))) {
                $name_col = $i;
            }

            // Check for email columns
            if ($email_col === -1 && in_array($col_lower, array('email', 'email address', 'emailaddress', 'e-mail', 'mail', 'email_address'))) {
                $email_col = $i;
            }
        }

        // If no email column found by exact match, try partial match
        if ($email_col === -1) {
            foreach ($header as $i => $col) {
                $col_lower = strtolower($col ?? '');
                if (strpos($col_lower, 'email') !== false || strpos($col_lower, 'mail') !== false) {
                    $email_col = $i;
                    break;
                }
            }
        }

        // If still no email column, scan data for email patterns
        if ($email_col === -1 && !empty($data)) {
            foreach ($data[0] as $i => $val) {
                if (!empty($val) && filter_var(trim($val), FILTER_VALIDATE_EMAIL)) {
                    $email_col = $i;
                    // Assume name is in the previous column if exists
                    if ($name_col === -1 && $i > 0) {
                        $name_col = $i - 1;
                    }
                    break;
                }
            }
        }

        if ($email_col === -1) {
            return array();
        }

        // Extract recipients from data
        foreach ($data as $row) {
            if (!is_array($row)) continue;

            $email = isset($row[$email_col]) ? trim($row[$email_col] ?? '') : '';
            $name = ($name_col >= 0 && isset($row[$name_col])) ? trim($row[$name_col] ?? '') : '';

            if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $recipients[] = array(
                    'name' => $name ?: $email,
                    'email' => $email
                );
            }
        }

        // Remove duplicates by email
        $unique = array();
        $seen_emails = array();
        foreach ($recipients as $r) {
            $email_lower = strtolower($r['email']);
            if (!in_array($email_lower, $seen_emails)) {
                $seen_emails[] = $email_lower;
                $unique[] = $r;
            }
        }

        return $unique;
    }

    /**
     * Read CSV file
     */
    private function read_csv_file($filepath, $delimiter = ',')
    {
        $data = array();

        // Try to detect delimiter if comma doesn't work
        $content = file_get_contents($filepath);
        if ($delimiter === ',' && substr_count($content, ';') > substr_count($content, ',')) {
            $delimiter = ';';
        }

        if (($handle = fopen($filepath, 'r')) !== false) {
            // Skip BOM if present
            $bom = fread($handle, 3);
            if ($bom !== chr(0xEF).chr(0xBB).chr(0xBF)) {
                rewind($handle);
            }

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                // Skip completely empty rows
                if (!empty(array_filter($row, function($v) { return $v !== '' && $v !== null; }))) {
                    $data[] = $row;
                }
            }
            fclose($handle);
        }

        return $data;
    }

    /**
     * Read XLSX file (ZIP-based format)
     */
    private function read_xlsx_file($filepath)
    {
        $data = array();
        $content = file_get_contents($filepath);

        // Check if it's a ZIP file (XLSX)
        if (substr($content, 0, 2) !== 'PK') {
            return array();
        }

        $zip = new ZipArchive();
        if ($zip->open($filepath) !== true) {
            return array();
        }

        // Get shared strings
        $strings = array();
        $strings_xml = $zip->getFromName('xl/sharedStrings.xml');
        if ($strings_xml) {
            $xml_strings = @simplexml_load_string($strings_xml);
            if ($xml_strings) {
                foreach ($xml_strings->si as $si) {
                    // Handle both simple and rich text
                    if (isset($si->t)) {
                        $strings[] = (string) $si->t;
                    } elseif (isset($si->r)) {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string) $r->t;
                        }
                        $strings[] = $text;
                    } else {
                        $strings[] = '';
                    }
                }
            }
        }

        // Get sheet data
        $sheet_xml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if (!$sheet_xml) {
            return array();
        }

        $xml = @simplexml_load_string($sheet_xml);
        if (!$xml || !isset($xml->sheetData)) {
            return array();
        }

        foreach ($xml->sheetData->row as $row) {
            $row_data = array();
            $max_col = 0;

            foreach ($row->c as $cell) {
                // Get column index from cell reference (e.g., "A1" -> 0, "B1" -> 1)
                $cell_ref = (string) $cell['r'];
                preg_match('/([A-Z]+)/', $cell_ref, $matches);
                $col_letter = $matches[1] ?? 'A';
                $col_index = $this->column_letter_to_index($col_letter);

                $value = '';
                if (isset($cell->v)) {
                    $value = (string) $cell->v;
                    // Check if it's a shared string reference
                    if (isset($cell['t']) && (string) $cell['t'] === 's') {
                        $value = isset($strings[(int) $value]) ? $strings[(int) $value] : $value;
                    }
                }

                // Fill gaps with empty strings
                while (count($row_data) < $col_index) {
                    $row_data[] = '';
                }
                $row_data[$col_index] = $value;
                $max_col = max($max_col, $col_index);
            }

            if (!empty(array_filter($row_data, function($v) { return $v !== '' && $v !== null; }))) {
                $data[] = $row_data;
            }
        }

        return $data;
    }

    /**
     * Convert Excel column letter to index (A=0, B=1, AA=26, etc.)
     */
    private function column_letter_to_index($letter)
    {
        $letter = strtoupper($letter);
        $index = 0;
        $length = strlen($letter);

        for ($i = 0; $i < $length; $i++) {
            $index = $index * 26 + (ord($letter[$i]) - ord('A') + 1);
        }

        return $index - 1;
    }

    /**
     * Send bulk email from uploaded Excel
     */
    public function send_excel_bulk()
    {
        header('Content-Type: application/json');

        $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        $use_template = $this->input->post('use_template') == '1';
        $use_name = $this->input->post('use_name') == '1';

        if (empty($subject) || empty($message)) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Please fill in subject and message'
            ));
            return;
        }

        // Get recipients from session
        $recipients = $this->session->userdata('excel_recipients');

        if (empty($recipients)) {
            echo json_encode(array(
                'success' => false,
                'message' => 'No recipients found. Please upload an Excel file first.'
            ));
            return;
        }

        // Handle file attachments (upload once, reuse for all recipients)
        $attachments = array();
        if (!empty($_FILES['attachments']['name'][0])) {
            $attachments = $this->_upload_attachments('attachments');
        }

        $sent = 0;
        $failed = 0;
        $errors = array();

        foreach ($recipients as $recipient) {
            $email = $recipient['email'];
            $name = $recipient['name'];

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $failed++;
                continue;
            }

            // Personalize message
            $personalized_message = $message;
            if ($use_name) {
                $personalized_message = str_replace(
                    array('{name}', '{NAME}', '{{name}}', '{{NAME}}'),
                    array($name, $name, $name, $name),
                    $personalized_message
                );
            }

            // Also replace email placeholders
            $personalized_message = str_replace(
                array('{email}', '{EMAIL}', '{{email}}', '{{EMAIL}}'),
                array($email, $email, $email, $email),
                $personalized_message
            );

            // Send email with attachments
            $options = array('attachments' => $attachments);
            if ($use_template) {
                $result = send_templated_email($email, $subject, $personalized_message, $options);
            } else {
                $result = send_smtp_email($email, $subject, $personalized_message, $options);
            }

            if ($result['success']) {
                $sent++;
                $this->log_email($email, $subject, 'sent', 'excel_bulk');
            } else {
                $failed++;
                $errors[] = $email . ': ' . $result['message'];
                $this->log_email($email, $subject, 'failed', 'excel_bulk');
            }

            // Small delay to prevent overwhelming the SMTP server
            usleep(100000); // 0.1 second
        }

        // Clean up attachments after all emails are sent
        $this->_cleanup_attachments($attachments);

        // Clear session data
        $this->session->unset_userdata('excel_recipients');

        echo json_encode(array(
            'success' => true,
            'message' => "Bulk email completed. Sent: $sent, Failed: $failed",
            'sent' => $sent,
            'failed' => $failed,
            'errors' => array_slice($errors, 0, 5)
        ));
    }

    /**
     * Clear Excel session data
     */
    public function clear_excel_session()
    {
        $this->session->unset_userdata('excel_recipients');
        echo json_encode(array('success' => true));
    }

    /**
     * Download sample Excel template for bulk email (CSV format for compatibility)
     */
    public function download_sample()
    {
        $filename = "bulk_email_template.csv";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // Add BOM for Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Header row
        fputcsv($output, array('Name', 'Email'));

        // Sample data rows
        fputcsv($output, array('John Doe', 'john.doe@example.com'));
        fputcsv($output, array('Jane Smith', 'jane.smith@example.com'));
        fputcsv($output, array('Company ABC', 'contact@companyabc.com'));
        fputcsv($output, array('Michael Johnson', 'michael.j@example.com'));
        fputcsv($output, array('Sarah Williams', 'sarah.w@example.com'));

        fclose($output);
        exit;
    }

    /**
     * Preview email with template
     */
    public function preview()
    {
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');
        $use_template = $this->input->post('use_template') == '1';

        if ($use_template) {
            $settings = $this->db->get_where('settings', array('settings_id' => 1))->row();
            $company_name = $settings->company_name ?? 'FundIt';
            $company_email = $settings->company_email ?? '';
            $company_phone = $settings->phone_number ?? '';
            $company_address_raw = $settings->address ?? '';

            // Clean address - strip HTML tags and format properly
            $company_address = $company_address_raw;
            $company_address = preg_replace('/<\/p>\s*<p>/i', "\n", $company_address);
            $company_address = preg_replace('/<br\s*\/?>/i', "\n", $company_address);
            $company_address = strip_tags($company_address);
            $company_address = trim($company_address);
            $company_address_html = nl2br(htmlspecialchars($company_address));

            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>' . htmlspecialchars($subject) . '</title>
            </head>
            <body style="margin: 0; padding: 20px; font-family: Arial, sans-serif; background-color: #f4f4f4;">
                <table width="600" cellspacing="0" cellpadding="0" border="0" style="margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%); padding: 30px 40px; border-radius: 8px 8px 0 0;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px;">' . htmlspecialchars($company_name) . '</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px;">
                            ' . $message . '
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 25px 40px; border-radius: 0 0 8px 8px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 5px 0; color: #6b7280; font-size: 12px;"><strong>' . htmlspecialchars($company_name) . '</strong></p>
                            ' . (!empty($company_address) ? '<p style="margin: 0 0 5px 0; color: #6b7280; font-size: 12px;">' . $company_address_html . '</p>' : '') . '
                            ' . (!empty($company_phone) ? '<p style="margin: 0 0 5px 0; color: #6b7280; font-size: 12px;">Phone: ' . htmlspecialchars($company_phone) . '</p>' : '') . '
                            ' . (!empty($company_email) ? '<p style="margin: 0; color: #6b7280; font-size: 12px;">Email: ' . htmlspecialchars($company_email) . '</p>' : '') . '
                        </td>
                    </tr>
                </table>
            </body>
            </html>';
        } else {
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>' . htmlspecialchars($subject) . '</title>
            </head>
            <body style="margin: 0; padding: 20px; font-family: Arial, sans-serif;">
                ' . $message . '
            </body>
            </html>';
        }

        echo $html;
    }
}
