<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require APPPATH . '/libraries/FPDF.php';
require_once APPPATH . "/third_party/Spout/Autoloader/autoload.php";
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;

class Loan extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Corporate_customers_model');
        $this->load->model('Loan_model');
        $this->load->model('Groups_model');
        $this->load->model('charges_model');
        $this->load->model('Account_model');
        $this->load->model('Loan_files_model');
        $this->load->model('Loan_approval_trail_model');
        $this->load->model('Loan_recommendation_model');
        $this->load->model('Rescheduled_payments_model');
        $this->load->model('Individual_customers_model');
        $this->load->model('Loan_products_model');
        $this->load->model('Transactions_model');
        $this->load->model('Group_loan_tracker_model');
        $this->load->model('Loan_products_model');
        $this->load->model('Payement_schedules_model');
        $this->load->model('Tellering_model');
        $this->load->model('Edit_loan_model');
       $this->load->model('Close_loan_model');
        $this->load->model('Masspayments_model');
        $this->load->model('Loan_customer_first_drafr_model');
        $this->load->library('form_validation');
		$this->load->model('File_library_model');
		$this->load->model('File_shares_model');
		$this->load->model('File_folders_model');
		$this->load->model('File_folder_mapping_model');
		$this->load->model('Loan_notes_model');
		$this->load->model('Collateral_model');

    }
    public function file_add(){
        $this->load->view('import');
    }

    function test_hhtp(){


        $a = false;
        if ($a){
            http_response_code(200);
        }else{
            http_response_code(401);

        }
        echo json_encode(array('message'=>"zathela","data"=>array("name"=>"misheck")));
    }
    public function get_transaction_usage($transaction_id)
    {
        $txn = $this->db->where('transaction_id', $transaction_id)
                        ->where('credit >', 0)
                        ->get('transaction')->row();

        if (!$txn) {
            echo '<p class="text-danger"><i class="fa fa-exclamation-circle"></i> Transaction not found: ' . htmlspecialchars($transaction_id) . '</p>';
            return;
        }

        $loan = $this->db->get_where('loan', array('loan_number' => $txn->account_number))->row();
        $currency_code = '';
        if ($loan) {
            $currency = $this->db->get_where('currencies', array('currency_id' => $loan->currency))->row();
            if ($currency) $currency_code = $currency->currency_code;
        }

        $deposited = (float) $txn->credit;
        $paid_date = date('Y-m-d', strtotime($txn->system_time));

        // Read pre-stored breakdown from transactions table
        $breakdown = $this->db->where('ref', $transaction_id)
                              ->order_by('payment_number', 'ASC')
                              ->get('transactions')->result();

        $html  = '<table class="table table-sm mb-3" style="font-size:0.9em;">';
        $html .= '<tr><td><strong>Transaction Ref</strong></td><td>' . htmlspecialchars($transaction_id) . '</td></tr>';
        $html .= '<tr><td><strong>Loan Account</strong></td><td>' . htmlspecialchars($txn->account_number) . '</td></tr>';
        $html .= '<tr><td><strong>Amount Deposited</strong></td><td><strong>' . $currency_code . ' ' . number_format($deposited, 2) . '</strong></td></tr>';
        $html .= '<tr><td><strong>Payment Date</strong></td><td>' . $paid_date . '</td></tr>';
        $html .= '</table>';

        if (!empty($breakdown)) {
            $total_applied = 0;
            $html .= '<h6><i class="fa fa-list-ul"></i> How payment was applied</h6>';
            $html .= '<table class="table table-bordered table-sm" style="font-size:0.85em;">';
            $html .= '<thead class="thead-dark"><tr><th>Schedule #</th><th>Amount Applied</th></tr></thead><tbody>';

            foreach ($breakdown as $row) {
                $applied = (float) $row->amount;
                $total_applied += $applied;
                $html .= '<tr>';
                $html .= '<td>Schedule ' . htmlspecialchars($row->payment_number) . '</td>';
                $html .= '<td><strong>' . $currency_code . ' ' . number_format($applied, 2) . '</strong></td>';
                $html .= '</tr>';
            }

            $html .= '</tbody><tfoot>';
            $html .= '<tr style="background:#f3f4f6;"><td><strong>Total Applied</strong></td>';
            $html .= '<td><strong>' . $currency_code . ' ' . number_format($total_applied, 2) . '</strong></td></tr>';
            if ($deposited - $total_applied > 0.01) {
                $html .= '<tr style="background:#fef3c7;"><td><strong>Remaining in Loan Account</strong></td>';
                $html .= '<td><strong>' . $currency_code . ' ' . number_format($deposited - $total_applied, 2) . '</strong></td></tr>';
            }
            $html .= '</tfoot></table>';
        } else {
            $html .= '<p class="text-muted mt-2"><i class="fa fa-info-circle"></i> No payment breakdown recorded for this transaction.</p>';
        }

        echo $html;
    }

    public function reverse_payment($transaction_id)
    {
        // Get the credit leg (collection account received money)
        $credit_leg = $this->db->where('transaction_id', $transaction_id)
                               ->where('credit >', 0)
                               ->get('transaction')->row();

        if (!$credit_leg) {
            $this->toaster->error('Reversal failed: transaction not found.');
            redirect($_SERVER['HTTP_REFERER']);
            return;
        }

        // Get the debit leg (loan account was debited)
        $debit_leg = $this->db->where('transaction_id', $transaction_id)
                              ->where('debit >', 0)
                              ->get('transaction')->row();

        if (!$debit_leg) {
            $this->toaster->error('Reversal failed: original debit entry not found.');
            redirect($_SERVER['HTTP_REFERER']);
            return;
        }

        $amount             = (float) $credit_leg->credit;
        $loan_account_num   = $debit_leg->account_number;
        $collection_acc_num = $credit_leg->account_number;

        // Check 1: Only allow reversals for transactions recorded by the server on or after 2026-06-17
        // Use server_time (actual DB insert time) not system_time (user-entered payment date, can be backdated)
        $txn_date = date('Y-m-d', strtotime($credit_leg->server_time));
        if ($txn_date < '2026-06-17') {
            $this->toaster->error('Transaction ' . $transaction_id . ' was recorded before 17 June 2026 and cannot be reversed.');
            redirect($_SERVER['HTTP_REFERER']);
            return;
        }

        // Check 2: Must reverse in latest-first order — compare by server_time, not system_time
        // Exclude REV- correction entries; only real payments count
        $newer = $this->db
            ->where('account_number', $loan_account_num)
            ->where('credit >', 0)
            ->where('server_time >', $credit_leg->server_time)
            ->not_like('transaction_id', 'REV-', 'none')
            ->get('transaction')->result();

        foreach ($newer as $n) {
            $already_reversed = $this->db
                ->where('transaction_id', 'REV-' . $n->transaction_id)
                ->get('transaction')->row();
            if (!$already_reversed) {
                $this->toaster->error('Please reverse the latest transaction first: ' . $n->transaction_id);
                redirect($_SERVER['HTTP_REFERER']);
                return;
            }
        }

        // Get the breakdown records from transactions table
        $breakdown = $this->db->where('ref', $transaction_id)->get('transactions')->result();

        if (empty($breakdown)) {
            $this->toaster->error('Reversal failed: no payment breakdown found. Only payments recorded after the latest update can be reversed.');
            redirect($_SERVER['HTTP_REFERER']);
            return;
        }

        $loan_id = $breakdown[0]->loan_id;

        // Revert each schedule allocation
        foreach ($breakdown as $row) {
            $sched = $this->db->where('loan_id', $loan_id)
                              ->where('payment_number', $row->payment_number)
                              ->get('payement_schedules')->row();
            if (!$sched) continue;

            $new_paid = round(max(0, (float)$sched->paid_amount - (float)$row->amount), 2);

            if ($new_paid <= 0) {
                $this->db->where('loan_id', $loan_id)
                         ->where('payment_number', $row->payment_number)
                         ->update('payement_schedules', [
                             'status'       => 'NOT PAID',
                             'partial_paid' => 'NO',
                             'paid_amount'  => 0,
                             'paid_date'    => NULL,
                         ]);
            } else {
                $this->db->where('loan_id', $loan_id)
                         ->where('payment_number', $row->payment_number)
                         ->update('payement_schedules', [
                             'status'       => 'PARTIAL PAID',
                             'partial_paid' => 'YES',
                             'paid_amount'  => $new_paid,
                         ]);
            }
        }

        // Reset next_payment_id to the earliest schedule that is now unpaid/partial
        $first_unpaid = $this->db
            ->where('loan_id', $loan_id)
            ->where_in('status', ['NOT PAID', 'PARTIAL PAID'])
            ->order_by('payment_number', 'ASC')
            ->get('payement_schedules')->row();

        if ($first_unpaid) {
            $this->db->where('loan_id', $loan_id)->update('loan', [
                'next_payment_id' => $first_unpaid->payment_number,
            ]);
        }

        // Reverse the ledger: credit the loan account, debit the collection account
        $rev_tid = 'REV-' . $transaction_id;
        $rev_date = date('Y-m-d H:i:s');

        $loan_acc = $this->Account_model->get_account($loan_account_num);
        $coll_acc = $this->Account_model->get_account($collection_acc_num);

        $new_loan_bal = (float)$loan_acc->balance + $amount;
        $new_coll_bal = (float)$coll_acc->balance - $amount;

        $this->db->where('account_number', $loan_account_num)->update('account', ['balance' => $new_loan_bal]);
        $this->db->where('account_number', $collection_acc_num)->update('account', ['balance' => $new_coll_bal]);

        $this->db->insert('transaction', [
            'account_number' => $loan_account_num,
            'transaction_id' => $rev_tid,
            'credit'         => $amount,
            'debit'          => 0,
            'balance'        => $new_loan_bal,
            'system_time'    => $rev_date,
        ]);
        $this->db->insert('transaction', [
            'account_number' => $collection_acc_num,
            'transaction_id' => $rev_tid,
            'credit'         => 0,
            'debit'          => $amount,
            'balance'        => $new_coll_bal,
            'system_time'    => $rev_date,
        ]);

        // Remove breakdown records for this transaction
        $this->db->where('ref', $transaction_id)->delete('transactions');

        // Reopen loan if it was closed by this payment
        $loan = $this->db->get_where('loan', ['loan_id' => $loan_id])->row();
        if ($loan && $loan->loan_status === 'CLOSED') {
            $this->db->where('loan_id', $loan_id)->update('loan', [
                'loan_status' => 'ACTIVE',
                'paid_off'    => 'No',
            ]);
        }

        log_activity([
            'user_id'       => $this->session->userdata('user_id'),
            'activity'      => 'Reversed transaction ' . $transaction_id . ' (amount: ' . $amount . ') on loan ' . $loan_id,
            'activity_cate' => 'loan_reversal',
        ]);

        $this->toaster->success('Transaction ' . $transaction_id . ' has been reversed successfully.');
        redirect($_SERVER['HTTP_REFERER']);
    }

    function get_loan_files($id){
        $result = $this->Loan_files_model->get_by_loans($id);
        $outpur = '';

        foreach ($result as $files){
            $outpur .='
            <tr>
            <td>'.$files->file_name.'</td>
            <td><a href="'.base_url('uploads/').''.$files->real_file.'" download>Download</a></td>
            </tr>
            ';
        }
        echo $outpur;

    }
    public function start_long_task() {
        // Send immediate response to acknowledge receipt of the request
        echo "Long task started.";



        if (ob_get_level() > 0) {
            ob_end_flush();
        }
        flush();

        // Execute the long task in a separate process
        // This will allow the main script to continue execution immediately


       $this->background_task();
    }
    private function background_task() {


        for ($i = 0; $i < 1000005; $i++) {
            $this->db->where('mass_repayment_request_id', 1)->update('mass_repayment_requests', array('progress' => 'Processing loan number '.$i));

        }
        // Exit child process
        exit();
    }
    public function initiate_mass_payment() {
        // Load the view with the upload form
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/initiate_mass');
        $this->load->view('admin/footer');
    }
    public function view_mass_payment_requests() {
        // Load the view with the upload form
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/view_mass_payment_request');
        $this->load->view('admin/footer');
    }
    public function repayment_request_details($id) {
        // Load the view with the upload form
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/view_mass_payment_details');
        $this->load->view('admin/footer');
    }

    public function upload_excel() {
        // Set up configuration for file upload
        $config['upload_path'] = './uploads';
        $config['allowed_types'] = 'xls|xlsx|csv';
        $config['max_size'] = 1024 * 10; // 10MB max

        // Load the upload library with the configuration
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('excel_file')) {
            // If upload fails, show error
            $error = $this->upload->display_errors();
            echo $error;
        } else {
            // Upload successful, read the uploaded file
            $upload_data = $this->upload->data();
            $file_path = $upload_data['full_path'];

            $records = $this->read_csv($file_path);
$this->db->insert('mass_repayment_requests', array('file'=>$upload_data['file_name'],'user'=>$this->session->userdata('user_id')));
$request_id = $this->db->insert_id();
            // Echo out the records
            foreach ($records as $record) {
                $loan_id = get_by_id('loan','loan_number',$record['loan_number']);
                if(!empty($loan_id)){
                    $lid = $loan_id->loan_id;
                }else{
                    $lid = 'error';
                    $this->db->where('mass_repayment_request_id',$request_id)
                        ->update('mass_repayment_requests',array('status'=>'Has Errors'));
                }
                $date = DateTime::createFromFormat('m/d/Y', $record['date']);
                $formatted_date = $date->format('Y-m-d');
                $this->db->insert('mass_repayment_requests_details',

                array(
                    'mass_repayment_request'=>$request_id,
                    'loan_id'=>$lid,
                    'loan_number'=>$record['loan_number'],
                    'amount'=>$record['amount'],
                    'payment_date'=>$formatted_date
                )
                );

            }
            $this->toaster->success('Mass repayment has been initiated please check status');
            $this->view_mass_payment_requests() ;
        }
    }


    private function read_csv($file_path) {
        $rows = [];

        // Open the CSV file
        if (($handle = fopen($file_path, "r")) !== FALSE) {
            // Read each row
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Check if row is not empty
                if (!empty(array_filter($data))) {
                    // Create an associative array with specific keys
                    $row = [
                        'loan_number' => $data[0],
                        'amount' => $data[1],
                        'date' => $data[2]
                    ];
                    // Add the row to the rows array
                    $rows[] = $row;
                }
            }
            fclose($handle);
        }

        // Remove the first row (header)
        array_shift($rows);

        return $rows;
    }



    function get_co_loan_files($id){
        $result = get_all_by_id('loan_collaterals','collateral_loan_id',$id);
        $outpur = '';

        foreach ($result as $files){
            $outpur .='
            <tr>
            <td>'.$files->collateral_name.'</td>
            <td><a href="'.base_url('uploads/').''.$files->collateral_file.'" download>Download</a></td>
            </tr>
            ';
        }
        echo $outpur;

    }


    function import_preview()
    {
        if(isset($_FILES["file"]["name"])) {
            $path = $_FILES["file"]["tmp_name"];
            $object = PHPExcel_IOFactory::load($path);
            foreach ($object->getWorksheetIterator() as $worksheet) {
                $highestRow = $worksheet->getHighestRow();
                $highestColumn = $worksheet->getHighestColumn();
                for ($row = 2; $row <= $highestRow; $row++) {


                    $title = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
                    $fname = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
                    $mdame = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                    $lastname = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                    $gender = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                    $dob = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                    $phone = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                    $village = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
                    $ta = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                    $group_name = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                    $city = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
                    $marital = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
                    $country = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
                    $mresidential_status = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
                    $profession = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
                    $source_of_income = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
                    $gross = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
                    $customer_created_on = $worksheet->getCellByColumnAndRow(17, $row)->getValue();
                    $loan_number = $worksheet->getCellByColumnAndRow(18, $row)->getValue();
                    $loan_product = $worksheet->getCellByColumnAndRow(19, $row)->getValue();
                    $loan_effective_date = $worksheet->getCellByColumnAndRow(20, $row)->getValue();
                    $pricipal = $worksheet->getCellByColumnAndRow(21, $row)->getValue();
                    $loan_period = $worksheet->getCellByColumnAndRow(22, $row)->getValue();
                    $period_type = $worksheet->getCellByColumnAndRow(23, $row)->getValue();
                    $interest = $worksheet->getCellByColumnAndRow(24, $row)->getValue();
                    $next_payment_number = $worksheet->getCellByColumnAndRow(25, $row)->getValue();
                    $loan_added_by = $worksheet->getCellByColumnAndRow(26, $row)->getValue();
                    $loan_status = $worksheet->getCellByColumnAndRow(27, $row)->getValue();
                    $loan_added_date = $worksheet->getCellByColumnAndRow(28, $row)->getValue();
                    $total_repaid = $worksheet->getCellByColumnAndRow(29, $row)->getValue();
                    $pricipal_paid = $worksheet->getCellByColumnAndRow(30, $row)->getValue();
                    $interest_paid = $worksheet->getCellByColumnAndRow(31, $row)->getValue();


                    //$added_by = $this->session->userdata('istitution_code');
                    $data = array(
                        'Title' => $title,
                        'Firstname' => $fname,
                        'Middlename' => $mdame,
                        'Lastname' => $lastname,
                        'Gender' => $gender,
                        'DateOfBirth' => $dob,
                        'PhoneNumber' => $phone,
                        'Village' => $village,
                        'TA' => $ta,
                        'ClubName' => $group_name,
                        'City' => $city,
                        'MarritalStatus' => $marital,
                        'Country' => $country,
                        'ResidentialStatus' => $mresidential_status,
                        'Profession' => $profession,
                        'SourceOfIncome' => $source_of_income,
                        'GrossMonthlyIncome' => $gross,
                        'CreatedOnCustomer' => $customer_created_on,
                        'loan_number' => $loan_number,
                        'loan_product' => $loan_product,
                        'loan_effectve_date' => $loan_effective_date,
                        'loan_principal' => $pricipal,
                        'loan_period' => $loan_period,
                        'period_type' => $period_type,
                        'loan_interest' => $interest,
                        'next_payment_number' => $next_payment_number,
                        'loan_added_by' => $loan_added_by,
                        'loan_status' => $loan_status,
                        'loan_added_date' => $loan_added_date,
                        'Totalrepaid' => $total_repaid,
                        'PrincipalPaid' => $pricipal_paid,
                        'InteresrPaid' => $interest_paid,
                    );

                    $this->Loan_customer_first_drafr_model->insert($data);
                }



            }
        }
    }

    function update_loan_numbers(){

        $r=get_all('account');


        foreach ($r as $l){
            $cleaned_string = str_replace(' ', '', $l->account_number);

      echo $cleaned_string."<br/>";
            $data = array(
                'account_number'=>$cleaned_string,
            );
            $result=$this->Account_model->update($l->account_id, $data);

           if($result){
               echo 'updated';

           }
        }





    }
    function add_loan_products(){
        $this->Loan_customer_first_drafr_model->add_loan_products();
    }	function migrate_customer(){
    $this->Loan_customer_first_drafr_model->insert_c();
}
    function convert_date(){
        $r =	$this->Loan_customer_first_drafr_model->get_all();

        foreach ($r as $l){
            $my_date = date('Y-m-d', strtotime($l->CreatedOnCustomer));

            $this->Loan_customer_first_drafr_model->update($l->id,array('CreatedOnCustomer'=>$my_date));
        }


    }
    function convert_date1(){
        $r =	$this->Loan_customer_first_drafr_model->get_all_active();
        $c = 0;
        foreach ($r as $l){
            $this->Loan_model->update1($l->customer_id, array('loan_status'=>'Active'));
        }
        echo $c;

    }
    function update2(){
        $r =	$this->Loan_model->get_all2();
        $c = 0;
        foreach ($r as $l){
            $this->Payement_schedules_model->update1($l->loan_id, array('status'=>'PAID'));
        }
        echo $c;

    }
    function update_loan_payment(){
        $r =	$this->Loan_customer_first_drafr_model->get_all_active();
        $c = 0;
        foreach ($r as $l){
            $this->Loan_model->update1($l->customer_id, array('loan_status'=>'Active'));
        }
        echo $c;

    }
    function add_groups(){
        $this->Loan_customer_first_drafr_model->add_groups();
    }
    function add_customer_to_group(){
        $this->Loan_customer_first_drafr_model->add_customer_to_group();
    }
    function csv_loan_create(){
        $r =	$this->Loan_customer_first_drafr_model->get_all();

        foreach ($r as $l){
            $this->Loan_model->add_loan($l->loan_principal, $l->loan_period, $l->loan_product_id, $l->loan_effectve_date,$l->customer_id,'N/A','N/A',7);

        }


    }
    function get_by_customer($id){
        $res = '<option>-select loan number-</option>';
        $data = $this->Loan_model->get_user_loan($id);

        foreach ($data as $dd){
            $res .='<option value="'.$dd->loan_id.'">'.$dd->loan_number.'</option>';
        }
        echo  $res;

    }

    function get_charges_fundit($id){

        $charge_value = 0;
        $loan =	$this->Loan_model->get_by_id($this->uri->segment(3));


        if($loan->processing_fee == 0.00){

                $charge_value = 0;

        }else{

                $charge_value =  ($loan->processing_fee/100) *  ($loan->loan_principal);

        }

        echo $charge_value;


    }
    function get_charges($id){

        $charge_value = 0;
        $loan =	$this->Loan_model->get_by_id($this->uri->segment(3));
        $charge = get_by_id('loan_products','loan_product_id', $this->uri->segment(4));

        if($loan->loan_principal > $charge->loan_processing_fee_threshold){
            if($charge->processing_charge_type_above == "Fixed"){
                $charge_value = $charge->processing_fixed_charge_above;
            }else{
                $charge_value =  ($charge->processing_variable_charge_above/100) *  ($loan->loan_principal);
            }
        }else{
            if($charge->processing_charge_type_below == "Fixed"){
                $charge_value = $charge->processing_fixed_charge_below;
            }else{
                $charge_value =  ($charge->processing_variable_charge_below/100) *  ($loan->loan_principal);
            }
        }

        echo $charge_value;


    }
    function get_late_charg($id){
        $re = array();
        $charge_value = 0;
        $loan =	$this->Loan_model->get_by_id($id);
        $charge = get_by_id('charges','charge_id','2');
        if($charge->charge_type=="Fixed"){
            $charge_value = $charge->fixed_amount;
        }elseif($charge->charge_type=="Variable"){
            $charge_value =  ($charge->variable_value/100) *  ($loan->loan_amount_term);

        }
        echo $charge_value;


    }

    public function add(){
        $data['customers'] =$this->Individual_customers_model->get_all_active();
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/add_loan',$data);
        $this->load->view('admin/footer');
    }
    public function add_group(){
        $data['customers'] =$this->Groups_model->get_all_active();
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/add_loan_group',$data);
        $this->load->view('admin/footer');
    }
    public function calculator(){
        $data['result'] = '';
        $menu_toggle['toggles'] = 41;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/calculator',$data);
        $this->load->view('admin/footer');
    }
    function calculate(){
        $id = $this->input->get('loan_type');
        $exist = $this->Loan_products_model->get_by_id($id);

        if ($exist) {
            $result = $this->Loan_model->calculate($this->input->get('amount'), $this->input->get('months'), $this->input->get('loan_type'), $this->input->get('loan_date'),$this->input->get('interest'));
            $data['result'] = $result;
            $menu_toggle['toggles'] = 41;
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('loan/calculator',$data);
            $this->load->view('admin/footer');
        } else {

        }
    }
    function create_act(){

        $this->load->library('upload');//loading the library
          $number_of_files_uploaded = isset($_FILES['loan_files']['name']) ? count($_FILES['loan_files']['name']) : 0;
        $name = $this->input->post('file_name');
        $coname = $this->input->post('coname');
        $type = $this->input->post('type');
        $currency = $this->input->post('currency');
        $serial = $this->input->post('serial');
        $cvalue = $this->input->post('cvalue');

        $desc = $this->input->post('desc');

        // Determine which tab to redirect to based on customer_type
        $customer_type = $this->input->post('customer_type');
        $tab = ($customer_type == 'corporate' || $customer_type == 'institution') ? 'corporate' : 'individual';
        $redirect_url = site_url('loan/loan_application?tab=' . $tab);

        // Validate required fields
        if(empty($this->input->post('customer'))){
            $this->toaster->error('Error: Please select a customer');
            redirect($redirect_url);
            return;
        }
        if(empty($this->input->post('amount'))){
            $this->toaster->error('Error: Please enter loan amount');
            redirect($redirect_url);
            return;
        }
        if(empty($currency)){
            $this->toaster->error('Error: Please select currency');
            redirect($redirect_url);
            return;
        }
        if(empty($this->input->post('months'))){
            $this->toaster->error('Error: Please enter loan term');
            redirect($redirect_url);
            return;
        }
        if(empty($this->input->post('interest'))){
            $this->toaster->error('Error: Please enter loan interest');
            redirect($redirect_url);
            return;
        }
        if(empty($this->input->post('loan_type'))){
            $this->toaster->error('Error: Please select loan type');
            redirect($redirect_url);
            return;
        }
        if(empty($this->input->post('loan_date'))){
            $this->toaster->error('Error: Please select loan date');
            redirect($redirect_url);
            return;
        }

        $loan_number = str_replace(' ', '', $this->input->post('loan_number'));

        // Gather appraisal data
        // Note: Bank statement fields (personal_credit, personal_debit, etc.) are now stored
        // in the separate bank_statements table with multiple row support
        $appraisal_data = array(
            'crb_search' => $this->input->post('crb_search'),
            'pacra_search' => $this->input->post('pacra_search'),
            'previous_facilities' => $this->input->post('previous_facilities'),
            'past_loans_comment' => $this->input->post('past_loans_comment'),
            'security_notes' => $this->input->post('security_notes'),
            'bank_statement_notes' => $this->input->post('bank_statement_notes'),
            'about_transaction' => $this->input->post('about_transaction'),
            'risk_analysis' => $this->input->post('risk_analysis')
        );

        try {
            $result = $this->Loan_model->add_loan( $loan_number,$this->input->post('amount'), $this->input->post('months'),$this->input->post('interest'), $this->input->post('loan_type'), $this->input->post('loan_date'),$this->input->post('customer'),$this->input->post('customer_type'),$this->input->post('worthness_file'),$this->input->post('narration'),$this->session->userdata('user_id'), $this->input->post('payment_method'),$this->input->post('fee_amount'),$currency,$this->input->post('off_taker'),$this->input->post('processing_fee'), $appraisal_data);
        } catch (Exception $e) {
            $this->toaster->error('Error: ' . $e->getMessage());
            redirect($redirect_url);
            return;
        }

        // Check if add_loan returned an error message (string) instead of result array
        if(!is_array($result)){
            $this->toaster->error('Error: ' . $result);
            redirect($redirect_url);
            return;
        }

        $data['result'] = $result;
		$folder_data = [
			'folder_name' => $result['loan_number'],
			'parent_folder_id' => 10,
			'owner_id' => $result['loan_id'],
			'is_public' => 1,
			'date_created' => date('Y-m-d H:i:s'),
			'date_modified' => date('Y-m-d H:i:s'),
			'description' => 'Loan folder'
		];

		$folder_id = $this->File_folders_model->insert($folder_data);
//loan files folder
		$folder_data_loan_files = [
			'folder_name' => $result['loan_number']." loan files",
			'parent_folder_id' => $folder_id,
			'owner_id' => $result['loan_id'],
			'is_public' => 1,
			'date_created' => date('Y-m-d H:i:s'),
			'date_modified' => date('Y-m-d H:i:s'),
			'description' => 'Loan files folder'
		];

		$folder_id_loan_files = $this->File_folders_model->insert($folder_data_loan_files);
//collateral files folder
		$folder_data_loan_collateral_files = [
			'folder_name' => $result['loan_number']." loan collateral files",
			'parent_folder_id' => $folder_id,
			'owner_id' => $result['loan_id'],
			'is_public' => 1,
			'date_created' => date('Y-m-d H:i:s'),
			'date_modified' => date('Y-m-d H:i:s'),
			'description' => 'Loan collateral files folder'
		];

		$folder_id_loan_collateral_files = $this->File_folders_model->insert($folder_data_loan_collateral_files);

		$imagePath = APPPATH . '../uploads/'.$result['loan_number'];
// Create directory if it doesn't exist
		if (!is_dir($imagePath)) {
			mkdir($imagePath, 0777, true);
		}
		//this is your real path APPPATH means you are at the application folder

		for ($i = 0; $i <  $number_of_files_uploaded; $i++) {
            $_FILES['userfile']['name']     = $_FILES['loan_files']['name'][$i];
            $_FILES['userfile']['type']     = $_FILES['loan_files']['type'][$i];
            $_FILES['userfile']['tmp_name'] = $_FILES['loan_files']['tmp_name'][$i];
            $_FILES['userfile']['error']    = $_FILES['loan_files']['error'][$i];
            $_FILES['userfile']['size']     = $_FILES['loan_files']['size'][$i];
            //configuration for upload your images
            $config = array(
                'file_name'     => $_FILES['userfile']['name'],
                'allowed_types' => '*',
                'max_size'      => 200000,
                'overwrite'     => FALSE,
                'upload_path' => $imagePath
            );
            $this->upload->initialize($config);
            $errCount = 0;//counting errrs
            if (!$this->upload->do_upload())
            {
                $error = array('error' => $this->upload->display_errors());
                $carImages[] = array(
                    'errors'=> $error
                );//saving arrors in the array
            }
            else
            {

				$uploaded_data = $this->upload->data();

                $data = array(
                    'loan_id' => $result['loan_id'],
                    'file_name' => $uploaded_data['file_name'],
                    'real_file' => $result['loan_number'] . '/' . $uploaded_data['file_name'],

                );

                $this->Loan_files_model->insert($data);

				$insert_data = [
					'owner_type' => 'loan',
					'owner_id' => $result['loan_id'],
					'file_category' => $this->input->post('file_category') ?: 'loan_files',
					'file_type' => $_FILES['userfile']['type'] ,
					'file_name' => $uploaded_data['file_name'],
					'file_path' => "uploads/".$result['loan_number']."/".$uploaded_data['file_name'],
					'file_size' => $_FILES['userfile']['size'],
					'is_public' => 1,
					'date_added' => date('Y-m-d H:i:s'),
					'date_modified' => date('Y-m-d H:i:s'),
					'added_by' => $this->session->userdata('user_id'),
					'description' => "loan file for loan",
					'tags' => ""
				];

				$file_id = $this->File_library_model->insert($insert_data);

				if ($folder_id) {
					$this->File_folder_mapping_model->insert([
						'file_id' => $file_id,
						'folder_id' => $folder_id_loan_files,
						'date_added' => date('Y-m-d H:i:s')
					]);
				}




            }//if file uploaded

        }//for loop ends here

        // Handle corporate loan files upload
        if (isset($_FILES['corporate_loan_files']) && !empty($_FILES['corporate_loan_files']['name'][0])) {
            $number_of_corporate_files = count($_FILES['corporate_loan_files']['name']);
            
            for ($i = 0; $i < $number_of_corporate_files; $i++) {
                if (!empty($_FILES['corporate_loan_files']['name'][$i])) {
                    $_FILES['userfile']['name']     = $_FILES['corporate_loan_files']['name'][$i];
                    $_FILES['userfile']['type']     = $_FILES['corporate_loan_files']['type'][$i];
                    $_FILES['userfile']['tmp_name'] = $_FILES['corporate_loan_files']['tmp_name'][$i];
                    $_FILES['userfile']['error']    = $_FILES['corporate_loan_files']['error'][$i];
                    $_FILES['userfile']['size']     = $_FILES['corporate_loan_files']['size'][$i];
                    
                    $config = array(
                        'file_name'     => $_FILES['userfile']['name'],
                        'allowed_types' => '*',
                        'max_size'      => 200000,
                        'overwrite'     => FALSE,
                        'upload_path'   => $imagePath
                    );
                    
                    $this->upload->initialize($config);
                    
                    if (!$this->upload->do_upload()) {
                        $error = array('error' => $this->upload->display_errors());
                        error_log('Corporate loan file upload error: ' . $this->upload->display_errors());
                    } else {
                        $uploaded_data = $this->upload->data();
                        
                        $data = array(
                            'loan_id' => $result['loan_id'],
                            'file_name' => $uploaded_data['file_name'],
                            'real_file' => $result['loan_number'] . '/' . $uploaded_data['file_name'],
                        );

                        $this->Loan_files_model->insert($data);

                        $insert_data = [
                            'owner_type' => 'loan',
                            'owner_id' => $result['loan_id'],
                            'file_category' => 'corporate_loan_files',
                            'file_type' => $_FILES['userfile']['type'],
                            'file_name' => $uploaded_data['file_name'],
                            'file_path' => "uploads/".$result['loan_number']."/".$uploaded_data['file_name'],
                            'file_size' => $_FILES['userfile']['size'],
                            'is_public' => 1,
                            'date_added' => date('Y-m-d H:i:s'),
                            'date_modified' => date('Y-m-d H:i:s'),
                            'added_by' => $this->session->userdata('user_id'),
                            'description' => "Corporate loan file",
                            'tags' => ""
                        ];
                        
                        $file_id = $this->File_library_model->insert($insert_data);
                        
                        if ($folder_id) {
                            $this->File_folder_mapping_model->insert([
                                'file_id' => $file_id,
                                'folder_id' => $folder_id_loan_files,
                                'date_added' => date('Y-m-d H:i:s')
                            ]);
                        }
                    }
                }
            }
        }

        $number_of_collateral = isset($_FILES['collateralfiles']['name']) ? count($_FILES['collateralfiles']['name']) : 0;

        for ($i = 0; $i <  $number_of_collateral; $i++) {
            $_FILES['userfile']['name']     = $_FILES['collateralfiles']['name'][$i];
            $_FILES['userfile']['type']     = $_FILES['collateralfiles']['type'][$i];
            $_FILES['userfile']['tmp_name'] = $_FILES['collateralfiles']['tmp_name'][$i];
            $_FILES['userfile']['error']    = $_FILES['collateralfiles']['error'][$i];
            $_FILES['userfile']['size']     = $_FILES['collateralfiles']['size'][$i];
            //configuration for upload your images
            $config = array(
                'file_name'     => rand(100,1000).$_FILES['userfile']['name'],
                'allowed_types' => '*',
                'max_size'      => 200000,
                'overwrite'     => FALSE,
                'upload_path'
                =>$imagePath
            );
            $this->upload->initialize($config);
            $errCount = 0;//counting errrs
            if (!$this->upload->do_upload())
            {
                $error = array('error' => $this->upload->display_errors());
                $carImages[] = array(
                    'errors'=> $error
                );//saving arrors in the array
            }
            else
            {

                $fileData = $this->upload->data();




                $data = array(
                    'collateral_loan_id' => $result['loan_id'],
                    'collateral_name' => $coname[$i],
                    'collateral_type' => $type[$i],
                    'collateral_serial' => $serial[$i],
                    'collateral_value' => $cvalue[$i],
                    'collateral_file' => $config['file_name'],
                    'collateral_desc' => $desc[$i],

                );

                $this->Loan_files_model->insert_collateral($data);


				$insert_data_collateral = [
					'owner_type' => 'loan',
					'owner_id' => $result['loan_id'],
					'file_category' =>  'loan_files',
					'file_type' => $_FILES['userfile']['type'] ,
					'file_name' => $fileData['file_name'],
					'file_path' => "uploads/".$result['loan_number']."/".$fileData['file_name'],
					'file_size' => $_FILES['userfile']['size'],
					'is_public' => 1,
					'date_added' => date('Y-m-d H:i:s'),
					'date_modified' => date('Y-m-d H:i:s'),
					'added_by' => $this->session->userdata('user_id'),
					'description' => "loan collateral file for loan",
					'tags' => ""
				];

				$file_id_co = $this->File_library_model->insert($insert_data_collateral);

				if ($folder_id) {
					$this->File_folder_mapping_model->insert([
						'file_id' => $file_id_co,
						'folder_id' => $folder_id_loan_collateral_files,
						'date_added' => date('Y-m-d H:i:s')
					]);
				}



			}//if file uploaded

        }

        // Ensure bank_statements table exists with correct structure
        if (!$this->db->table_exists('bank_statements')) {
            $sql = "CREATE TABLE IF NOT EXISTS `bank_statements` (
                `statement_id` int NOT NULL AUTO_INCREMENT,
                `loan_id` int NOT NULL,
                `statement_type` varchar(20) DEFAULT 'corporate',
                `credit` decimal(18,2) NOT NULL,
                `debit` decimal(18,2) NOT NULL,
                `month` varchar(20) NOT NULL,
                `year` int NOT NULL,
                `file` varchar(200) DEFAULT NULL,
                `added_by` int NOT NULL,
                `date_added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`statement_id`),
                KEY `loan_id` (`loan_id`)
            )";
            $this->db->query($sql);
        } else {
            // Check if statement_type column exists, add if missing
            $fields = $this->db->field_data('bank_statements');
            $statement_type_exists = false;
            foreach ($fields as $field) {
                if ($field->name == 'statement_type') {
                    $statement_type_exists = true;
                    break;
                }
            }
            if (!$statement_type_exists) {
                $this->db->query("ALTER TABLE bank_statements ADD COLUMN `statement_type` varchar(20) DEFAULT 'corporate' AFTER `loan_id`");
            }

            // Also check if month column type needs updating (from int to varchar)
            foreach ($fields as $field) {
                if ($field->name == 'month' && $field->type == 'int') {
                    $this->db->query("ALTER TABLE bank_statements MODIFY COLUMN `month` varchar(20) NOT NULL");
                    break;
                }
            }
        }

        // Handle bank statements for corporate loans (multiple)
        if ($this->input->post('customer_type') == 'institution') {
            $corporate_credits = $this->input->post('corporate_credit');
            $corporate_debits = $this->input->post('corporate_debit');
            $corporate_months = $this->input->post('corporate_statement_month');

            if (is_array($corporate_credits) && is_array($corporate_debits) && is_array($corporate_months)) {
                $num_statements = count($corporate_credits);

                for ($i = 0; $i < $num_statements; $i++) {
                    $credit = isset($corporate_credits[$i]) ? $corporate_credits[$i] : null;
                    $debit = isset($corporate_debits[$i]) ? $corporate_debits[$i] : null;
                    $month = isset($corporate_months[$i]) ? $corporate_months[$i] : null;

                    // Skip empty entries
                    if (empty($credit) && empty($debit) && empty($month)) {
                        continue;
                    }

                    $statement_filename = null;

                    // Handle file upload for this statement
                    if (isset($_FILES['corporate_statement_file']) &&
                        isset($_FILES['corporate_statement_file']['name'][$i]) &&
                        $_FILES['corporate_statement_file']['name'][$i] != '') {

                        $_FILES['userfile']['name']     = $_FILES['corporate_statement_file']['name'][$i];
                        $_FILES['userfile']['type']     = $_FILES['corporate_statement_file']['type'][$i];
                        $_FILES['userfile']['tmp_name'] = $_FILES['corporate_statement_file']['tmp_name'][$i];
                        $_FILES['userfile']['error']    = $_FILES['corporate_statement_file']['error'][$i];
                        $_FILES['userfile']['size']     = $_FILES['corporate_statement_file']['size'][$i];

                        $config = array(
                            'file_name'     => 'statement_' . time() . '_' . $i . '_' . $_FILES['userfile']['name'],
                            'allowed_types' => '*',
                            'max_size'      => 200000,
                            'overwrite'     => FALSE,
                            'upload_path'   => $imagePath
                        );

                        $this->upload->initialize($config);

                        if ($this->upload->do_upload()) {
                            $uploaded_data = $this->upload->data();
                            $statement_filename = $uploaded_data['file_name'];

                            // Add to file library
                            $insert_data_statement = [
                                'owner_type' => 'loan',
                                'owner_id' => $result['loan_id'],
                                'file_category' => 'bank_statement',
                                'file_type' => $_FILES['userfile']['type'],
                                'file_name' => $uploaded_data['file_name'],
                                'file_path' => "uploads/".$result['loan_number']."/".$uploaded_data['file_name'],
                                'file_size' => $_FILES['userfile']['size'],
                                'is_public' => 1,
                                'date_added' => date('Y-m-d H:i:s'),
                                'date_modified' => date('Y-m-d H:i:s'),
                                'added_by' => $this->session->userdata('user_id'),
                                'description' => "Bank statement for loan - " . $month,
                                'tags' => ""
                            ];

                            $file_id_statement = $this->File_library_model->insert($insert_data_statement);

                            if ($folder_id) {
                                $this->File_folder_mapping_model->insert([
                                    'file_id' => $file_id_statement,
                                    'folder_id' => $folder_id_loan_files,
                                    'date_added' => date('Y-m-d H:i:s')
                                ]);
                            }
                        }
                    }

                    // Insert bank statement data
                    $bank_statement_data = [
                        'loan_id' => $result['loan_id'],
                        'statement_type' => 'corporate',
                        'credit' => $credit ? str_replace(',', '', $credit) : 0,
                        'debit' => $debit ? str_replace(',', '', $debit) : 0,
                        'month' => $month,
                        'year' => date('Y'),
                        'file' => $statement_filename,
                        'added_by' => $this->session->userdata('user_id'),
                        'date_added' => date('Y-m-d H:i:s')
                    ];

                    $this->db->insert('bank_statements', $bank_statement_data);
                }
            }
        }

        // Handle bank statements for personal/individual loans (multiple)
        if ($this->input->post('customer_type') == 'individual') {
            $personal_credits = $this->input->post('personal_credit');
            $personal_debits = $this->input->post('personal_debit');
            $personal_months = $this->input->post('personal_statement_month');

            if (is_array($personal_credits) && is_array($personal_debits) && is_array($personal_months)) {
                $num_statements = count($personal_credits);

                for ($i = 0; $i < $num_statements; $i++) {
                    $credit = isset($personal_credits[$i]) ? $personal_credits[$i] : null;
                    $debit = isset($personal_debits[$i]) ? $personal_debits[$i] : null;
                    $month = isset($personal_months[$i]) ? $personal_months[$i] : null;

                    // Skip empty entries
                    if (empty($credit) && empty($debit) && empty($month)) {
                        continue;
                    }

                    $statement_filename = null;

                    // Handle file upload for this statement
                    if (isset($_FILES['personal_statement_file']) &&
                        isset($_FILES['personal_statement_file']['name'][$i]) &&
                        $_FILES['personal_statement_file']['name'][$i] != '') {

                        $_FILES['userfile']['name']     = $_FILES['personal_statement_file']['name'][$i];
                        $_FILES['userfile']['type']     = $_FILES['personal_statement_file']['type'][$i];
                        $_FILES['userfile']['tmp_name'] = $_FILES['personal_statement_file']['tmp_name'][$i];
                        $_FILES['userfile']['error']    = $_FILES['personal_statement_file']['error'][$i];
                        $_FILES['userfile']['size']     = $_FILES['personal_statement_file']['size'][$i];

                        $config = array(
                            'file_name'     => 'statement_' . time() . '_' . $i . '_' . $_FILES['userfile']['name'],
                            'allowed_types' => '*',
                            'max_size'      => 200000,
                            'overwrite'     => FALSE,
                            'upload_path'   => $imagePath
                        );

                        $this->upload->initialize($config);

                        if ($this->upload->do_upload()) {
                            $uploaded_data = $this->upload->data();
                            $statement_filename = $uploaded_data['file_name'];

                            // Add to file library
                            $insert_data_statement = [
                                'owner_type' => 'loan',
                                'owner_id' => $result['loan_id'],
                                'file_category' => 'bank_statement',
                                'file_type' => $_FILES['userfile']['type'],
                                'file_name' => $uploaded_data['file_name'],
                                'file_path' => "uploads/".$result['loan_number']."/".$uploaded_data['file_name'],
                                'file_size' => $_FILES['userfile']['size'],
                                'is_public' => 1,
                                'date_added' => date('Y-m-d H:i:s'),
                                'date_modified' => date('Y-m-d H:i:s'),
                                'added_by' => $this->session->userdata('user_id'),
                                'description' => "Bank statement for loan - " . $month,
                                'tags' => ""
                            ];

                            $file_id_statement = $this->File_library_model->insert($insert_data_statement);

                            if ($folder_id) {
                                $this->File_folder_mapping_model->insert([
                                    'file_id' => $file_id_statement,
                                    'folder_id' => $folder_id_loan_files,
                                    'date_added' => date('Y-m-d H:i:s')
                                ]);
                            }
                        }
                    }

                    // Insert bank statement data
                    $bank_statement_data = [
                        'loan_id' => $result['loan_id'],
                        'statement_type' => 'personal',
                        'credit' => $credit ? str_replace(',', '', $credit) : 0,
                        'debit' => $debit ? str_replace(',', '', $debit) : 0,
                        'month' => $month,
                        'year' => date('Y'),
                        'file' => $statement_filename,
                        'added_by' => $this->session->userdata('user_id'),
                        'date_added' => date('Y-m-d H:i:s')
                    ];

                    $this->db->insert('bank_statements', $bank_statement_data);
                }
            }
        }

        // Link selected collaterals to the loan
        $collateral_ids = $this->input->post('collateral_ids');
        $collateral_amounts = $this->input->post('collateral_amounts');

        if (!empty($collateral_ids) && is_array($collateral_ids)) {
            $user_id = $this->session->userdata('user_id');

            for ($i = 0; $i < count($collateral_ids); $i++) {
                $collateral_id = $collateral_ids[$i];
                $amount_utilized = isset($collateral_amounts[$i]) ? floatval($collateral_amounts[$i]) : 0;

                if ($collateral_id && $amount_utilized > 0) {
                    // Check available balance
                    $available = $this->Collateral_model->get_available_balance($collateral_id);

                    if ($amount_utilized <= $available) {
                        $link_data = array(
                            'loan_id' => $result['loan_id'],
                            'collateral_id' => $collateral_id,
                            'amount_utilized' => $amount_utilized,
                            'linked_by' => $user_id,
                            'linked_at' => date('Y-m-d H:i:s'),
                            'status' => 'ACTIVE'
                        );

                        $this->Collateral_model->link_to_loan($link_data);
                    }
                }
            }
        }

        // Send email notification to users who can recommend loans
        $customer_type = $this->input->post('customer_type');
        $customer_id = $this->input->post('customer');
        $customer_name = 'N/A';

        // Get customer name based on type
        if ($customer_type == 'individual') {
            $customer = $this->db->get_where('individual_customers', array('id' => $customer_id))->row();
            if ($customer) {
                $customer_name = $customer->Firstname . ' ' . $customer->Lastname;
            }
        } else {
            $customer = $this->db->get_where('corporate_customers', array('id' => $customer_id))->row();
            if ($customer) {
                $customer_name = $customer->EntityName;
            }
        }

        // Get currency code
        $currency_data = $this->db->get_where('currency', array('id' => $currency))->row();
        $currency_code = $currency_data ? $currency_data->code : 'ZMW';

        // Prepare loan data for notification
        $loan_notification_data = array(
            'loan_id' => $result['loan_id'],
            'loan_number' => $result['loan_number'],
            'customer_name' => $customer_name,
            'amount' => $this->input->post('amount'),
            'currency' => $currency_code
        );

        // Notify users with access to Loan/recommend
        notify_loan_recommenders($loan_notification_data, $this->session->userdata('user_id'));

        $this->toaster->success('Success, loan  was created  pending authorisation');

        redirect('loan/track');


    }


    function create_act_loan_period(){

        $sampleDataCofi =  get_all_distinctLoan_cofi();
        foreach ( $sampleDataCofi as $rowdistinctcofi){

            $rowcofi =  get_all_cust_cofi($rowdistinctcofi->LoanReferenceNo);
            $date_str = 	$rowcofi-> DisbursementDate;
            $timestamp = strtotime($date_str);
            $disbursedDateTime = new DateTime(date("Y-m-d", $timestamp));


            $date_strm = 	$rowcofi-> MaturityDate;
            $timestampm = strtotime($date_strm);
            $maturityDateTime = new DateTime(date("Y-m-d", $timestampm));


            $interval = $disbursedDateTime->diff($maturityDateTime);
            $numMonths = $interval->m + $interval->y * 12;

            echo "Number of months: " . $numMonths ."<br>";


        }





    }


    function create_act_migration(){



        $sampleDataCofi =  get_all_distinctLoan_cofi();
        $addescofi=$this->session->userdata('user_id');
        $cofibranch=6;
        $country='MW';

        $loantype=4;
        $paymentmethods=0;
        $cash=10000;
        $worthness="";
        $narration="";
        $amount=0;
        $patialpaymentnumber=0;
        $paymentnumber=0;
        $nextpayment=0;
        $mAmount=0;
        $patialpaidamount=0;
        foreach ( $sampleDataCofi as $rowdistinctcofi){

            $rowcofi =  get_all_cust_cofi($rowdistinctcofi->LoanReferenceNo);

            $checkifexist=get_all_loanCheck($rowcofi->LoanReferenceNo);
            if(sizeof($checkifexist) == 0){


                if($rowcofi->ScheduledRepaymentAmountMWK==0){
                    $paymentnumber=0;
                    $nextpayment=$paymentnumber+1;
                }
                else {

                    $num_strtotal = floatval(str_replace(',', '', $rowcofi->TotalAmountPaidToDateMWK));
                    $num_strtorp=	floatval(str_replace(',', '', $rowcofi->ScheduledRepaymentAmountMWK));


                    $paymentnumber=($num_strtotal/$num_strtorp);
                    $modulus=fmod($num_strtotal,$num_strtorp);

                    if($modulus>5000){

                        $patialpaymentnumber=$paymentnumber+1;
                        $patialpaidamount=$modulus;
                    }
                    if( $paymentnumber==$rowcofi->PaymentPeriod){
                        $paymentnumber=$rowcofi->PaymentPeriod;
                        $patialpaidamount=0;
                    }else{
                        $nextpayment=$paymentnumber+1;
                    }

                }


                $lamount=floatval(str_replace(',', '', $rowcofi->DisbursedAmount));

                $firstpaiddate=	$rowcofi-> FirstPaymentDate;
                $timestampf = strtotime($firstpaiddate);
                $fdate = date("Y-m-d", $timestampf);

                $date_str = 	$rowcofi-> DisbursementDate;
                $timestamp = strtotime($date_str);
                $date = date("Y-m-d", $timestamp);

                $paymentnumber=	intval($paymentnumber);



                $result = $this->Loan_model->add_loan_migration($rowcofi->LoanReferenceNo,$lamount,
                    $rowcofi->PaymentPeriod,
                    $rowcofi->loan_product_id,
                    $date	,
                    $rowcofi->customer_id,
                    'individual',
                    $worthness,
                    $narration,
                    $this->session->userdata('user_id'), $paymentmethods,$amount,$paymentnumber,  $patialpaidamount,$patialpaymentnumber,$nextpayment,$fdate );
                $data['result'] = $result;
            }
            else{
                continue;
            }


        }
        $this->toaster->success('Success, Loans uploaded successfully');
        redirect('loan/track');






    }



    function create_act_migration_groups(){





        $sampleDataCofi =  get_all_distinctLoan_cofi();
        $addescofi=$this->session->userdata('user_id');
        $cofibranch=6;
        $country='MW';

        $loantype=5;
        $paymentmethods=0;
        $cash=10000;
        $worthness="";
        $narration="";
        $amount=0;
        $patialpaymentnumber=0;
        $paymentnumber=0;
        $nextpayment=0;
        $mAmount=0;
        $patialpaidamount=0;
        foreach ( $sampleDataCofi as $rowdistinctcofi){

            $rowcofi =  get_all_cust_cofi($rowdistinctcofi->LOANNO);

            $checkifexist=get_all_loanCheck($rowcofi->LOANNO);
            if(sizeof($checkifexist) == 0){


                if($rowcofi->AMOUNTPAID==0){
                    $paymentnumber=0;
                    $nextpayment=$paymentnumber+1;
                }
                else {

                    $num_strtotal = floatval(str_replace(',', '', $rowcofi->AMOUNTPAID));
                    $num_strtorp=	floatval(str_replace(',', '', $rowcofi->MREPAYMENT));


                    $paymentnumber=($num_strtotal/$num_strtorp);
                    $modulus=fmod($num_strtotal,$num_strtorp);

                    if($modulus>5000){

                        $patialpaymentnumber=$paymentnumber+1;
                        $patialpaidamount=$modulus;
                    }
                    if( $paymentnumber==$rowcofi->loan_period){
                        $paymentnumber=$rowcofi->loan_period;
                        $patialpaidamount=0;
                    }else{
                        $nextpayment=$paymentnumber+1;
                    }

                }


                $lamount=floatval(str_replace(',', '', $rowcofi->LAMOUNT));



                $firstpaiddate=$rowcofi->disbursed_date;

                $dateParts = explode('/', $firstpaiddate);
                $day = $dateParts[0];
                $month = $dateParts[1];
                $year = $dateParts[2];
                $datep= $month.'/'.$day.'/'.$year;
                $fdate =date("Y-m-d",strtotime($datep));;



                $datep=$rowcofi->disbursed_date;
                $dateParts = explode('/', $datep);
                $day = $dateParts[0];
                $month = $dateParts[1];
                $year = $dateParts[2];
                $datep= $month.'/'.$day.'/'.$year;
                $date=date("Y-m-d",strtotime($datep));
echo $date;
exit();


                $paymentnumber=	intval($paymentnumber);

                $loannumber=trim($rowcofi->LOANNO);

                $result = $this->Loan_model->add_loan_migration($loannumber,$lamount,
                    $rowcofi->loan_period,
                    $rowcofi->loan_product_id,
                    $date,
                    $rowcofi->group_id,
                    'group',
                    $worthness,
                    $narration,
                    $this->session->userdata('user_id'), $paymentmethods,$amount,$paymentnumber,  $patialpaidamount,$patialpaymentnumber,$nextpayment,$fdate );
                $data['result'] = $result;
            }
            else{
                continue;
            }


        }
        $this->toaster->success('Success, Loans uploaded successfully');
        redirect('loan/track');






    }
    function create_acti(){

        $group = $this->Groups_model->check($this->input->post('group_id'));


        if(!empty($group)){
            $user_gotten = $this->Group_loan_tracker_model->validate($this->input->post('group_id'),$this->input->post('customer'),$group->id);
            if(!empty($user_gotten)){
                $this->toaster->error('Error, Sorry this member has received his shares already from this group');
                redirect($_SERVER['HTTP_REFERER']);
            }else{
                $validate_trans = $this->Group_loan_tracker_model->validate_trans($group->id);
                if(($validate_trans->amount+$this->input->post('amount')) > $group->amount){
                    $this->toaster->error('Error, Sorry this group has no enough amount to create this loan contract, please add smaller amount');
                    redirect($_SERVER['HTTP_REFERER']);
                }else{
                    $result = $this->Loan_model->add_loan($this->input->post('amount'), $this->input->post('months'), $this->input->post('loan_type'), $this->input->post('loan_date'),$this->input->post('customer'),$this->input->post('worthness_file'),$this->input->post('narration'));
                    $data['result'] = $result;
                    $this->toaster->success('Success, customer was created  pending authorisation');
                    $data = array(
                        'disbursement_id' => $group->id,
                        'group_id' => $this->input->post('group_id',TRUE),
                        'customer_id' => $this->input->post('customer',TRUE),
                        'amount' => $this->input->post('amount',TRUE),

                    );

                    $this->Group_loan_tracker_model->insert($data);
                    redirect('loan/track');
                }
            }
        }else{
            $this->toaster->error('Error, Sorry this group has no amount assigned yet , Please assign group amount first');
            redirect($_SERVER['HTTP_REFERER']);
        }





    }
    function scores($app_form){
        if($app_form >=0 && $app_form <= 60){
            return "FAILED";
        }elseif ($app_form >60 && $app_form <= 70){
            return "AVERAGE";
        }elseif ($app_form >70 && $app_form <= 80){
            return "GOOD";
        }elseif ($app_form >80 && $app_form <= 90){
            return "VERY GOOD";
        }elseif ($app_form >90 && $app_form <= 100){
            return "EXCELLENT";
        }
    }
    function bg($app_form){
        if($app_form >=0 && $app_form <= 60){
            return "bg-danger";
        }elseif ($app_form >60 && $app_form <= 70){
            return "bg-warning";
        }elseif ($app_form >70 && $app_form <= 80){
            return "bg-primary";
        }elseif ($app_form >80 && $app_form <= 90){
            return "bg-info";
        }elseif ($app_form >90 && $app_form <= 100){
            return "bg-success";
        }
    }
    function score_data($id){
        $output = "";
        $scores = $this->Loan_recommendation_model->get_by_loan($id);


        $output .= '<table class="table">
                <tr>
                    <th>Application requirement</th>
                    <th>Score (Fail, Average, good, Very Good, excellent)</th>
                    <th>Comments</th>
                </tr>  <tr>
                    <td>Application Form  (score out of 15)</td>
                    <td>'.$scores->application_form.'/15 ='.round((($scores->application_form/15)*100),2).' %   '.$this->scores((($scores->application_form/15)*100)).' <div class="progress">
            <div class="progress-bar '.$this->bg((($scores->application_form/15)*100)).' progress-bar-stripped"
                style="width:'.(($scores->application_form/15)*100).'%;">
                  '.round((($scores->application_form/15)*100),2).'
              </div>
        </div>
                </td>
                    <td><p>'.$scores->application_form_comment.'</p></td>
                </tr>
                
                <tr>
                    <td>Letter from the Local Authority (score out of 10)</td>
                    <td>'.$scores->letter_from_auth.'/10 = '.round((($scores->letter_from_auth/10)*100), 2).'%   '.$this->scores((($scores->letter_from_auth/10)*100)).'
                     <div class="progress">
            <div class="progress-bar '.$this->bg((($scores->letter_from_auth/10)*100)).' progress-bar-stripped"
                style="width:'.(($scores->letter_from_auth/10)*100).'%;">
                  '.round((($scores->letter_from_auth/10)*100),2).'
              </div>
        </div>
                    </td>
                    <td>'.$scores->letter_from_auth_comment.'</td>
                </tr>
                <tr>
                    <td>Commitment Fee  (10)</td>
                    <td>'.$scores->commitment_fee.'/10 ='.round((($scores->commitment_fee/10)*100),2).'%'.$this->scores((($scores->commitment_fee/10)*100)).'
                   <div class="progress">
            <div class="progress-bar '.$this->bg((($scores->commitment_fee/10)*100)).' progress-bar-stripped"
                style="width:'.(($scores->commitment_fee/10)*100).'%;">
                  '.round((($scores->commitment_fee/10)*100),2).'
              </div>
        </div>
                    </td>
                    <td>'.$scores->commitment_fee_comment.'</td>
                </tr>
                <tr>
                    <td>Evidence of Access to Land / Existence of the business (score out of 15))</td>
                    <td>'.$scores->land_evidence.'/15 = '.round((($scores->land_evidence/15)*100),2).'%'.$this->scores((($scores->land_evidence/15)*100)).'
                    <div class="progress">
            <div class="progress-bar '.$this->bg((($scores->land_evidence/15)*100)).' progress-bar-stripped"
                style="width:'.(($scores->land_evidence/15)*100).'%;">
                  '.round((($scores->land_evidence/15)*100),2).'
              </div>
        </div>
                    </td>
                    <td>'.$scores->land_evidence_comment.'</td>
                </tr>
                <tr>
                    <td>
                        Off taker Agreement (score out of 10)
                    </td>
                    <td>'.$scores->offtaker_evidence.'/10 = '.round((($scores->offtaker_evidence/10)*100),2).'%'.$this->scores((($scores->offtaker_evidence/10)*100)).' 
                     <div class="progress">
            <div class="progress-bar '.$this->bg((($scores->offtaker_evidence/10)*100)).' progress-bar-stripped"
                style="width:'.(($scores->offtaker_evidence/10)*100).'%;">
                  '.round((($scores->offtaker_evidence/10)*100),2).'
              </div>
        </div>
                    </td>
                    <td>'.$scores->offtaker_evidence_comment.'</td>
                </tr>
                <tr>
                    <td>
                        Training Received (score out of 10)
                    </td>
                    <td>'.$scores->training_recieved.'/10 ='.round((($scores->training_recieved/10)*100),2).'%'.$this->scores((($scores->training_recieved/10)*100)).'
                     <div class="progress">
            <div class="progress-bar '.$this->bg((($scores->training_recieved/10)*100)).' progress-bar-stripped"
                style="width:'.(($scores->training_recieved/10)*100).'%;">
                  '.round((($scores->training_recieved/10)*100),2).'
              </div>
        </div>
                
                    </td>
                    <td>'.$scores->training_recieved_comment.'</td>
                </tr>
                <tr>
                    <td>
                        Loans owed (score out of 15)
                    </td>
                    <td>'.$scores->loans_owed.'/15 ='.round((($scores->loans_owed/15)*100),2).'%'.$this->scores((($scores->loans_owed/15)*100)).'
                    <div class="progress">
            <div class="progress-bar '.$this->bg((($scores->loans_owed/15)*100)).' progress-bar-stripped"
                style="width:'.(($scores->loans_owed/15)*100).'%;">
                  '.round((($scores->loans_owed/15)*100),2).'
              </div>
        </div>
                    </td>
                    <td>'.$scores->loans_owed_comment.'</td>
                </tr>
                <tr>
                    <td>
                        Character In the community (score out of 15)
                    </td>
                    <td>'.$scores->community_character.'/15 ='.round((($scores->community_character/15)*100),2).'%'.$this->scores((($scores->community_character/15)*100)).' <div class="progress">
            <div class="progress-bar '.$this->bg((($scores->community_character/15)*100)).' progress-bar-stripped"
                style="width:'.round((($scores->community_character/15)*100),2).'%;">
                '.round((($scores->community_character/15)*100),2).'
            </div>
        </div></td>
                    <td>'.$scores->community_character_comment.'</td>
                </tr>
                <tr style="background-color: bisque;">
                <td style="font-weight: bolder;" >TOTAL AVERAGE SCORE  '.((($scores->community_character + $scores->loans_owed + $scores->training_recieved + $scores->offtaker_evidence + $scores->land_evidence + $scores->commitment_fee + $scores->letter_from_auth + $scores->application_form)/100)*100).' %    '.$this->scores(((($scores->community_character + $scores->loans_owed + $scores->training_recieved + $scores->offtaker_evidence + $scores->land_evidence + $scores->commitment_fee + $scores->letter_from_auth + $scores->application_form)/100)*100)).' 
                
                </td>
                <td>
                <div class="progress">
                 <div class="progress-bar '.$this->bg(((($scores->community_character + $scores->loans_owed + $scores->training_recieved + $scores->offtaker_evidence + $scores->land_evidence + $scores->commitment_fee + $scores->letter_from_auth + $scores->application_form)/100)*100)).' progress-bar-stripped"
                style="width:'.((($scores->community_character + $scores->loans_owed + $scores->training_recieved + $scores->offtaker_evidence + $scores->land_evidence + $scores->commitment_fee + $scores->letter_from_auth + $scores->application_form)/100)*100).'%;">
                '.round(((($scores->community_character + $scores->loans_owed + $scores->training_recieved + $scores->offtaker_evidence + $scores->land_evidence + $scores->commitment_fee + $scores->letter_from_auth + $scores->application_form)/100)*100),2).' %
            </div>
        </div>
</td>
</tr>
            </table>';
        echo $output;
    }
    function initiated(){
        $data['loan_data'] = $this->Loan_model->get_all('RECOMMENDED');
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/loan_list', $data);
        $this->load->view('admin/footer');
    }
	function get_approved_first(){
		$data['loan_data'] = $this->Loan_model->get_all('APPROVED_FIRST');
		$menu_toggle['toggles'] = 23;
		$this->load->view('admin/header', $menu_toggle);
		$this->load->view('loan/to_approve_second', $data);
		$this->load->view('admin/footer');
	}
	function get_approved_second(){
		$data['loan_data'] = $this->Loan_model->get_all('APPROVED_SECOND');
		$menu_toggle['toggles'] = 23;
		$this->load->view('admin/header', $menu_toggle);
		$this->load->view('loan/to_approve_third', $data);
		$this->load->view('admin/footer');
	}

	/**
	 * Unified approval page - shows all loans with RECOMMENDED status
	 * Multi-level approval system: 3 different users must approve
	 */
	function unified_approval(){
		$data['loan_data'] = $this->Loan_model->get_all('RECOMMENDED');
		$menu_toggle['toggles'] = 23;
		$this->load->view('admin/header', $menu_toggle);
		$this->load->view('loan/unified_approval', $data);
		$this->load->view('admin/footer');
	}

	/**
	 * Handle multi-level approval action
	 * Tracks individual approvals and updates loan status when 3 approvals are reached
	 */
	function multi_approval_action(){
		$action = $this->input->post('action');
		$loan_id = $this->input->post('loan_id');
		$comment = $this->input->post('comment');
		$approval_level = $this->input->post('approval_level');

		// Enforce role-based permission server-side (approve/reject require approval rights)
		if(!has_access('loan/unified_approval')){
			$this->toaster->error('You do not have permission to perform this action.');
			redirect($_SERVER['HTTP_REFERER']);
			return;
		}

		$current_user_id = $this->session->userdata('user_id');

		// Get the loan
		$loan = $this->Loan_model->get_by_id($loan_id);
		if(!$loan || $loan->loan_status != 'RECOMMENDED') {
			$this->toaster->error('Error: Loan not found or not in RECOMMENDED status.');
			redirect($_SERVER['HTTP_REFERER']);
			return;
		}

		// Handle rejection
		if($action == 'REJECT') {
			// Insert rejection into approval trail
			$trail_data = array(
				'user_id' => $current_user_id,
				'action' => 'REJECTED',
				'comment' => $comment,
				'loan_id' => $loan_id
			);
			$this->Loan_approval_trail_model->insert($trail_data);

			// Update loan status to REJECTED
			$this->Loan_model->update($loan_id, array(
				'loan_status' => 'REJECTED',
				'rejected_by' => $current_user_id,
				'rejected_date' => date('Y-m-d H:i:s'),
				'rejection_reasons' => $comment
			));

			// Log activity
			$logger = array(
				'user_id' => $current_user_id,
				'activity' => 'REJECTED a loan during multi-level approval',
				'activity_cate' => 'updating'
			);
			log_activity($logger);

			$this->toaster->success('Loan has been rejected.');
			redirect('loan/unified_approval');
			return;
		}

		// Handle approval
		if($action == 'MULTI_APPROVE') {
			// Get existing approvers for this loan
			$approvers = get_loan_approvers($loan_id);
			$approval_count = count($approvers);
			$last_approver_id = !empty($approvers) ? end($approvers)['user_id'] : null;

			// Only check for consecutive approvals - same user CAN approve 1st and 3rd
			// But same user CANNOT approve consecutively (1st and 2nd, or 2nd and 3rd)
			if($last_approver_id == $current_user_id) {
				$this->toaster->error('Error: You cannot approve consecutively. Another user must approve first.');
				redirect($_SERVER['HTTP_REFERER']);
				return;
			}

			// Insert approval into trail
			$trail_data = array(
				'user_id' => $current_user_id,
				'action' => 'MULTI_APPROVE',
				'comment' => $comment . ' (Approval #' . ($approval_count + 1) . ' of 3)',
				'loan_id' => $loan_id
			);
			$this->Loan_approval_trail_model->insert($trail_data);

			// Log activity
			$logger = array(
				'user_id' => $current_user_id,
				'activity' => 'Performed approval #' . ($approval_count + 1) . ' of 3 on loan ' . $loan->loan_number,
				'activity_cate' => 'updating'
			);
			log_activity($logger);

			// Check if this is the 3rd approval (final approval)
			if($approval_count + 1 >= 3) {
				// Update loan status to APPROVED and clear sent_back flag
				$this->Loan_model->update($loan_id, array(
					'loan_status' => 'APPROVED',
					'loan_approved_by' => $current_user_id,
					'approved_date' => date('Y-m-d H:i:s'),
					'sent_back' => 0,
					'sent_back_comment' => null
				));

				// Add final approval to trail
				$trail_data = array(
					'user_id' => $current_user_id,
					'action' => 'APPROVED',
					'comment' => 'Final approval - Loan has received 3 approvals and is ready for disbursement.',
					'loan_id' => $loan_id
				);
				$this->Loan_approval_trail_model->insert($trail_data);

				// Notify users with access to loan/approved about the fully approved loan
				$customer_name = 'Customer';
				if ($loan->customer_type == 'individual') {
					$customer = $this->db->get_where('individual_customers', array('id' => $loan->loan_customer))->row();
					if ($customer) {
						$customer_name = $customer->Firstname . ' ' . $customer->Lastname;
					}
				} else {
					$customer = $this->db->get_where('corporate_customers', array('id' => $loan->loan_customer))->row();
					if ($customer) {
						$customer_name = $customer->EntityName;
					}
				}

				// Get currency code
				$currency_data = $this->db->get_where('currency', array('id' => $loan->currency))->row();
				$currency_code = $currency_data ? $currency_data->code : 'ZMW';

				// Prepare loan data for notification
				$loan_notification_data = array(
					'loan_id' => $loan_id,
					'loan_number' => $loan->loan_number,
					'customer_name' => $customer_name,
					'amount' => $loan->loan_principal,
					'currency' => $currency_code
				);

				// Notify users with access to loan/approved (disbursement team)
				notify_loan_approved($loan_notification_data, 'loan/approved', $current_user_id);

				// Notify the loan creator that the loan has been fully approved
				notify_loan_creator($loan_notification_data, 'APPROVED', $current_user_id, $loan->loan_added_by);

				$this->toaster->success('Final approval granted! Loan is now ready for disbursement.');
			} else {
				// Notify creator of intermediate approval
				$customer_name = 'Customer';
				if ($loan->customer_type == 'individual') {
					$cust = $this->db->get_where('individual_customers', array('id' => $loan->loan_customer))->row();
					if ($cust) $customer_name = $cust->Firstname . ' ' . $cust->Lastname;
				} else {
					$cust = $this->db->get_where('corporate_customers', array('id' => $loan->loan_customer))->row();
					if ($cust) $customer_name = $cust->EntityName;
				}
				$currency_data2 = $this->db->get_where('currency', array('id' => $loan->currency))->row();
				$currency_code2 = $currency_data2 ? $currency_data2->code : 'ZMW';
				$intermediate_data = array(
					'loan_id' => $loan_id,
					'loan_number' => $loan->loan_number,
					'customer_name' => $customer_name,
					'amount' => $loan->loan_principal,
					'currency' => $currency_code2
				);
				$approval_status = ($approval_count + 1 == 1) ? 'APPROVED_FIRST' : 'APPROVED_SECOND';
				notify_loan_creator($intermediate_data, $approval_status, $current_user_id, $loan->loan_added_by);

				$this->toaster->success('Approval #' . ($approval_count + 1) . ' recorded. ' . (3 - $approval_count - 1) . ' more approval(s) needed.');
			}

			redirect('loan/unified_approval');
			return;
		}

		$this->toaster->error('Error: Invalid action.');
		redirect($_SERVER['HTTP_REFERER']);
	}

    function recommend(){
        $data['loan_data'] = $this->Loan_model->get_all('INITIATED');
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/recommend', $data);
        $this->load->view('admin/footer');
    }
    function delete_request(){
        $data['loan_data'] = $this->Loan_model->get_all_delete();
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/delete_requests', $data);
        $this->load->view('admin/footer');
    }
    function deleted_loans(){
        $data['loan_data'] = $this->Loan_model->get_all_deleted();
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/deleted_loans', $data);
        $this->load->view('admin/footer');
    }
    function delete_request_approve(){
        $data['loan_data'] = $this->Loan_model->get_all_delete_approve();
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/delete_approve', $data);
        $this->load->view('admin/footer');
    }


    function initiate_edit_loan()
    {
        $data['loan_data'] = $this->Loan_model->get_all_not_disbursed();
        $menu_toggle['toggles'] = 23;

            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('loan/initiate_edit_loan', $data);
            $this->load->view('admin/footer');
        
    }

    function import_loan_mass_repayments()
    {
        //$data['loan_data'] = $this->Masspayments_model->get_all('');
        $menu_toggle['toggles'] = 23;

        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/import_loan_mass_repayments');
        $this->load->view('admin/footer');

    }

    function process_imported_loan_mass_repayments()
    {
        $data['loan_data'] = $this->Masspayments_model->get_all_imported();
        $menu_toggle['toggles'] = 23;

        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/process_imported_loan_mass_repayments', $data);
        $this->load->view('admin/footer');

    }
    function make_mass_mass_deposit()
    {
        $data['loan_data'] = $this->Masspayments_model->get_all_processed_list();
        $menu_toggle['toggles'] = 23;

        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/make_mass_mass_deposit', $data);
        $this->load->view('admin/footer');

    }

    function make_mass_mass_repayments()
    {
        $data['loan_data'] = $this->Masspayments_model->get_all_deposited_list();
        $menu_toggle['toggles'] = 23;

        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/make_mass_mass_repayments', $data);
        $this->load->view('admin/footer');

    }


    function initiate_close_loan(){
    

        $data['loan_data'] = $this->Loan_model->get_all_initiate();
        $menu_toggle['toggles'] = 23;


        $user = $this->input->get('user');
        $product = $this->input->get('product');
        $status = $this->input->get('status');
        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $search = $this->input->get('search');
        if($search=="filter"){
            $data['loan_data'] = $this->Loan_model->get_filter($user,$product,$status,$from,$to);
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('loan/track', $data);
            $this->load->view('admin/footer');
        }elseif($search=='pdf'){
            $data['loan_data'] = $this->Loan_model->get_filter($user,$product,$status,$from,$to);
            $data['officer'] = ($user=="All") ? "All Officers" : get_by_id('employees','id',$user)->Firstname;
            $data['product'] =($product=="All") ? "All Products" : get_by_id('loan_products','loan_product_id',$product)->product_name;
            $data['from'] = $from;
            $data['to'] = $to;
            $this->load->library('Pdf');
            $html = $this->load->view('loan/loan_report_pdf', $data,true);
            $this->pdf->createPDF($html, "loan report as on".date('Y-m-d'), true,'A4','landscape');
        }else{
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('loan/initiate_close_loan', $data);
            $this->load->view('admin/footer');
        }
        }
    
        function  edit_loan(){
            $data['loan_data'] = $this->Loan_model->get_all_approved_edit_loan();
            $menu_toggle['toggles'] = 23;
    
    
            $user = $this->input->get('user');
            $product = $this->input->get('product');
            $status = $this->input->get('status');
            $from = $this->input->get('from');
            $to = $this->input->get('to');
            $search = $this->input->get('search');
            if($search=="filter"){
                $data['loan_data'] = $this->Loan_model->get_filter($user,$product,$status,$from,$to);
                $this->load->view('admin/header', $menu_toggle);
                $this->load->view('loan/track', $data);
                $this->load->view('admin/footer');
            }elseif($search=='pdf'){
                $data['loan_data'] = $this->Loan_model->get_filter($user,$product,$status,$from,$to);
                $data['officer'] = ($user=="All") ? "All Officers" : get_by_id('employees','id',$user)->Firstname;
                $data['product'] =($product=="All") ? "All Products" : get_by_id('loan_products','loan_product_id',$product)->product_name;
                $data['from'] = $from;
                $data['to'] = $to;
                $this->load->library('Pdf');
                $html = $this->load->view('loan/loan_report_pdf', $data,true);
                $this->pdf->createPDF($html, "loan report as on".date('Y-m-d'), true,'A4','landscape');
            }else{
                $this->load->view('admin/header', $menu_toggle);
                $this->load->view('loan/approved_edit_loan', $data);
                $this->load->view('admin/footer');
            }
        }
    

        function  close_loan(){
            $data['loan_data'] = $this->Loan_model->get_all_approved_close_loan();
            $menu_toggle['toggles'] = 23;
    
    
            $user = $this->input->get('user');
            $product = $this->input->get('product');
            $status = $this->input->get('status');
            $from = $this->input->get('from');
            $to = $this->input->get('to');
            $search = $this->input->get('search');
            if($search=="filter"){
                $data['loan_data'] = $this->Loan_model->get_filter($user,$product,$status,$from,$to);
                $this->load->view('admin/header', $menu_toggle);
                $this->load->view('loan/track', $data);
                $this->load->view('admin/footer');
            }elseif($search=='pdf'){
                $data['loan_data'] = $this->Loan_model->get_filter($user,$product,$status,$from,$to);
                $data['officer'] = ($user=="All") ? "All Officers" : get_by_id('employees','id',$user)->Firstname;
                $data['product'] =($product=="All") ? "All Products" : get_by_id('loan_products','loan_product_id',$product)->product_name;
                $data['from'] = $from;
                $data['to'] = $to;
                $this->load->library('Pdf');
                $html = $this->load->view('loan/loan_report_pdf', $data,true);
                $this->pdf->createPDF($html, "loan report as on".date('Y-m-d'), true,'A4','landscape');
            }else{
                $this->load->view('admin/header', $menu_toggle);
                $this->load->view('loan/approved_close_loan', $data);
                $this->load->view('admin/footer');
            }
        }
    
    function  recomend_close_loan(){
        $data['loan_data'] = $this->Loan_model->get_all_recomended_close_loan();
        $menu_toggle['toggles'] = 23;


        $user = $this->input->get('user');
        $product = $this->input->get('product');
        $status = $this->input->get('status');
        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $search = $this->input->get('search');
        if($search=="filter"){
            $data['loan_data'] = $this->Loan_model->get_filter($user,$product,$status,$from,$to);
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('loan/track', $data);
            $this->load->view('admin/footer');
        }elseif($search=='pdf'){
            $data['loan_data'] = $this->Loan_model->get_filter($user,$product,$status,$from,$to);
            $data['officer'] = ($user=="All") ? "All Officers" : get_by_id('employees','id',$user)->Firstname;
            $data['product'] =($product=="All") ? "All Products" : get_by_id('loan_products','loan_product_id',$product)->product_name;
            $data['from'] = $from;
            $data['to'] = $to;
            $this->load->library('Pdf');
            $html = $this->load->view('loan/loan_report_pdf', $data,true);
            $this->pdf->createPDF($html, "loan report as on".date('Y-m-d'), true,'A4','landscape');
        }else{
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('loan/recomend_close_loan', $data);
            $this->load->view('admin/footer');
        }
    }

 

    function  loan_product_summary($id){


        $menu_toggle['toggles'] = 23;

        $row = $this->Loan_products_model->get_by_id($id);
        if ($row) {
            $data = array(
                'product_id' => $row->loan_product_id,
                'product_name' => $row->product_name,
            );


            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('admin/summary', $data);
            $this->load->view('admin/footer');
        }
        else{
            redirect($_SERVER['HTTP_REFERER']);
        }
        
    }


    function disbursed_loans(){
        $data['loan_data'] = $this->Loan_model->get_all_disbursed();
        $menu_toggle['toggles'] = 23;


        $user = $this->input->get('user');
        $product = $this->input->get('product');
        $status = 'All';
        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $search = $this->input->get('search');
        if($search=="filter"){
            $data['loan_data'] = $this->Loan_model->get_filter($user,$product,$status,$from,$to);
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('loan/disbursed_track', $data);
            $this->load->view('admin/footer');
        }elseif($search=='pdf'){
            $data['loan_data'] = $this->Loan_model->get_filter($user,$product,$status,$from,$to);
            $data['officer'] = ($user=="All") ? "All Officers" : get_by_id('employees','id',$user)->Firstname;
            $data['product'] =($product=="All") ? "All Products" : get_by_id('loan_products','loan_product_id',$product)->product_name;
            $data['from'] = $from;
            $data['to'] = $to;
            $this->load->library('Pdf');
            $html = $this->load->view('loan/loan_report_pdf', $data,true);
            $this->pdf->createPDF($html, "loan report as on".date('Y-m-d'), true,'A4','landscape');
        }else{
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('loan/disbursed_track', $data);
            $this->load->view('admin/footer');
        }
    }
    function track(){
        $menu_toggle['toggles'] = 23;

        $user = $this->input->get('user');
        $product = $this->input->get('product');
        $status = $this->input->get('status');
        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $search = $this->input->get('search');

        // If status is passed directly via URL (e.g., from dashboard), apply filter
        if ($status && $status != 'All' && !$search) {
            $data['loan_data'] = $this->Loan_model->get_filter($user ?? 'All', $product ?? 'All', $status, $from, $to);
        } elseif ($search == "filter") {
            $data['loan_data'] = $this->Loan_model->get_filter($user, $product, $status, $from, $to);
        } elseif ($search == 'pdf') {
            $data['loan_data'] = $this->Loan_model->get_filter($user, $product, $status, $from, $to);
            $data['officer'] = ($user == "All") ? "All Officers" : get_by_id('employees', 'id', $user)->Firstname;
            $data['product'] = ($product == "All") ? "All Products" : get_by_id('loan_products', 'loan_product_id', $product)->product_name;
            $data['from'] = $from;
            $data['to'] = $to;
            $this->load->library('Pdf');
            $html = $this->load->view('loan/loan_report_pdf', $data, true);
            $this->pdf->createPDF($html, "loan report as on" . date('Y-m-d'), true, 'A4', 'landscape');
            return;
        } else {
            $data['loan_data'] = $this->Loan_model->get_all('');
        }

        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/track', $data);
        $this->load->view('admin/footer');
    }


    function deleteloan_view(){
        $data['loan_data'] = $this->Loan_model->get_all('');
        $menu_toggle['toggles'] = 23;


        $user = $this->input->get('user');
        $product = $this->input->get('product');
        $status = $this->input->get('status');
        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $search = $this->input->get('search');
        if($search=="filter"){
            $data['loan_data'] = $this->Loan_model->get_filter($user,$product,$status,$from,$to);
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('loan/deleteloans', $data);
            $this->load->view('admin/footer');
        }elseif($search=='pdf'){
            $data['loan_data'] = $this->Loan_model->get_filter($user,$product,$status,$from,$to);
            $data['officer'] = ($user=="All") ? "All Officers" : get_by_id('employees','id',$user)->Firstname;
            $data['product'] =($product=="All") ? "All Products" : get_by_id('loan_products','loan_product_id',$product)->product_name;
            $data['from'] = $from;
            $data['to'] = $to;
            $this->load->library('Pdf');
            $html = $this->load->view('loan/loan_report_pdf', $data,true);
            $this->pdf->createPDF($html, "loan report as on".date('Y-m-d'), true,'A4','landscape');
        }else{
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('loan/deleteloans', $data);
            $this->load->view('admin/footer');
        }
    }


    function filterLoan(){

        $menu_toggle['toggles'] = 23;
        $status = $this->input->get('status');
        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $search = $this->input->get('search');
        if($search=="filter"){
            $data['loanreports'] = $this->Loan_model->rbm_reportFilter($from,$to);
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('loan/searchresult_rbm', $data);
            $this->load->view('admin/footer');

        }
    }
    function individual_track(){
        $idd=$this->session->userdata('user_id');
        $data['loan_data'] = $this->Loan_model->track_individual($idd);
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/track', $data);
        $this->load->view('admin/footer');
    }
    function loan_repayment(){
        $data['loan_data'] = $this->Loan_model->get_all('ACTIVE');
        $menu_toggle['toggles'] = 52;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/loan_repayment', $data);
        $this->load->view('admin/footer');
    }
    function active(){
        $data['loan_data'] = $this->Loan_model->get_all('ACTIVE');
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/active', $data);
        $this->load->view('admin/footer');
    }
    function closed(){
        $data['loan_data'] = $this->Loan_model->get_all('CLOSED');
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/closed', $data);
        $this->load->view('admin/footer');
    }

    function approved(){
        // Default to CLIENT_SIGNED (ready for disbursement)
        $data['loan_data'] = $this->Loan_model->get_all('CLIENT_SIGNED');
        $menu_toggle['toggles'] = 23;
        $user = $this->input->get('user');
        $product = $this->input->get('product');
        $status = $this->input->get('status');
        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $search = $this->input->get('search');
        if($search=="filter"){
            $data['loan_data'] = $this->Loan_model->get_filter($user,$product,$status,$from,$to);
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('loan/approved', $data);
            $this->load->view('admin/footer');
        }elseif($search=='pdf'){
            $data['loan_data'] = $this->Loan_model->get_filter($user,$product,$status,$from,$to);
            $data['officer'] = ($user=="All") ? "All Officers" : get_by_id('employees','id',$user)->Firstname;
            $data['product'] =($product=="All") ? "All Products" : get_by_id('loan_products','loan_product_id',$product)->product_name;
            $data['from'] = $from;
            $data['to'] = $to;
            $this->load->library('Pdf');
            $html = $this->load->view('loan/loan_report_pdf', $data,true);
            $this->pdf->createPDF($html, "loan report as on".date('Y-m-d'), true,'A4','landscape');
        }else{
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('loan/approved', $data);
            $this->load->view('admin/footer');
        }

    }
    function disbursed(){
        $data['loan_data'] = $this->Loan_model->get_disbursed();
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/active_loans', $data);
        $this->load->view('admin/footer');
    }
    function write_off(){
        $data['loan_data'] = $this->Loan_model->get_all('ACTIVE');
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/write_off', $data);
        $this->load->view('admin/footer');
    }
    function rejected(){
        $data['loan_data'] = $this->Loan_model->get_all('REJECTED');
        $this->load->view('admin/header');
        $this->load->view('loan/approved', $data);
        $this->load->view('admin/footer');
    }
    function written_off(){
        $data['loan_data'] = $this->Loan_model->get_all('WRITTEN_OFF');
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/written_off', $data);
        $this->load->view('admin/footer');
    }
    function write_off_approve(){
        $data['loan_data'] = $this->Loan_model->get_all_mod('ACTIVE');
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/write_off_approval', $data);
        $this->load->view('admin/footer');
    }

    function delete_permanent(){
        $data['loan_data'] = $this->Loan_model->get_all('');
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/delete_p', $data);
        $this->load->view('admin/footer');
    }
    
    public function pay_advance(){
        $loan_number = $this->input->post('loan_id');
        $pay_number = $this->input->post('payment_number');
        $paid_date= $this->input->post('paid_date');

        $amount = $this->input->post('amount');
        $amount_total = 0;
        for($i=0;$i <count($pay_number);$i++){
            $amount_total += $amount;
        }
        $loan_account = get_by_id('loan','loan_id',$loan_number);
        $recepientt = get_by_id('account','collection_account','Yes');
        $check = $this->Account_model->get_account($loan_account->loan_number);
        if($check->balance >= $amount_total) {

            $result = $this->Payement_schedules_model->pay_advance($loan_number, $amount, $pay_number,$paid_date);
            if ($result) {
                $logger = array(

                    'user_id' => $this->session->userdata('user_id'),
                    'activity' => 'Paid advance loan,  loan ID:' . ' ' . $loan_number . ' ' . ' payment number' . ' ' . $pay_number .
                        ' ' . 'amount' . ' ' . $amount,
                    'activity_cate' => 'loan_repayment'

                );
                log_activity($logger);

                $this->toaster->success('Success, advance payment was successful');
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                $this->toaster->error('Ops!, Sorry advance payment failed P7');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }else{
            $this->toaster->error('Ops!, Sorry advance payment failed, You dont have enough funds to perform this transactions');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
    function finish_loan(){
        $tid="TR-S".rand(100,9999).date('Y').date('m').date('d');
        $loan_number = $this->input->post('loan_id');
        $pay_number = $this->input->post('payment_number');
        $amount = $this->input->post('amount');
        $paid_date =  $this->input->post('paid_date');

        $proof = $this->input->post('pay_proof');
        $loan_account = get_by_id('loan','loan_id',$loan_number);
        $recepientt = get_by_id('account','collection_account','Yes');
        $check = $this->Account_model->get_account($loan_account->loan_number);
        if($check->balance >= $amount){
            $do_transactions = $this->Account_model->transfer_funds($loan_account->loan_number, $recepientt->account_number, $amount, $tid,$paid_date);
            if($do_transactions=='success'){
                $result = $this->Payement_schedules_model->finish_pay($loan_number,$pay_number,$amount);

                if($result== true){

                    $logger = array(

                        'user_id' => $this->session->userdata('user_id'),
                        'activity' => 'Paid a loan, loan ID:'.' '.$loan_number.' '.' payment number'.' '.$pay_number.
                            ' '.'amount'.' '.$amount,
                        'activity_cate' => 'loan_repayment'

                    );
                    log_activity($logger);
                    $this->toaster->success('Success, payment was successful');
                    redirect($_SERVER['HTTP_REFERER']);
                }else{
                    $this->toaster->error('Ops!, Sorry payment failed P2');
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }else{
                $this->toaster->error('Ops!, Sorry payment failed, Error P2');
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else{
            $this->toaster->error('Ops!, Sorry payment failed loan account savings does not have enough funds');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
    public function pay_loan_backup(){
        $loan_number = $this->input->post('loan_id');
        $pay_number = $this->input->post('payment_number');
        $amount = $this->input->post('amount');
        $proof = $this->input->post('pay_proof');
        $paid_date = $this->input->post('paid_date');





        if($this->input->post('payment_method')=="0") {
            $tid = "TR-S" . rand(100, 9999) . date('Y') . date('m') . date('d');

            $loan_account = get_by_id('loan', 'loan_id', $loan_number);
            $recepientt = get_by_id('account', 'collection_account', 'Yes');
            $check = $this->Account_model->get_account($loan_account->loan_number);
            if ($check->balance >= $amount) {

                $do_transactions = $this->Account_model->transfer_funds($loan_account->loan_number, $recepientt->account_number, $amount, $tid,  $paid_date );
                if ($do_transactions == 'success') {
                    $result = $this->Payement_schedules_model->new_pay($loan_number, $pay_number, $amount,  $paid_date);

                    if ($result == true) {

                        $logger = array(

                            'user_id' => $this->session->userdata('user_id'),
                            'activity' => 'Paid a loan, loan ID:' . ' ' . $loan_number . ' ' . ' payment number' . ' ' . $pay_number .
                                ' ' . 'amount' . ' ' . $amount,
                            'activity_cate' => 'loan_repayment'

                        );
                        log_activity($logger);
                        $this->toaster->success('Success, payment was successful');
                        redirect($_SERVER['HTTP_REFERER']);
                    } else {
                        $this->toaster->error('Ops!, Sorry payment failed P2');
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                } else {
                    $this->toaster->error('Ops!, Sorry payment failed, Error P2');
                    redirect($_SERVER['HTTP_REFERER']);
                }
            } elseif ($check->balance > 0 && $check->balance < $amount) {
                $topay_amount = $check->balance;
                $this->Account_model->transfer_funds($loan_account->loan_number, $recepientt->account_number, $topay_amount, $tid, $paid_date);
                $r = $this->Payement_schedules_model->new_pay($loan_number, $pay_number, $topay_amount,$paid_date);

                $logger = array(

                    'user_id' => $this->session->userdata('user_id'),
                    'activity' => 'Paid a loan, loan ID:' . ' ' . $loan_number . ' ' . ' payment number' . ' ' . $pay_number .
                        ' ' . 'amount' . ' ' . $topay_amount,
                    'activity_cate' => 'loan_repayment'

                );
                log_activity($logger);
                $data = array(
                    'ref' => "GF." . date('Y') . date('m') . date('d') . '.' . rand(100, 999),
                    'loan_id' => $this->input->post('loan_id', TRUE),
                    'amount' => $topay_amount,
                    'transaction_type' => 2,
                    'payment_number' => $this->input->post('payment_number'),
                    'date_stamp' => $paid_date,
                    'added_by' => $this->session->userdata('user_id')

                );

                $this->Transactions_model->insert($data);
                $this->toaster->success('Success, payment was successful');
                redirect($_SERVER['HTTP_REFERER']);

            } else {
                $this->toaster->error('Ops!, Sorry payment failed loan account savings does not have enough funds');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }else{
            $result = $this->Payement_schedules_model->new_pay($loan_number, $pay_number, $amount, $paid_date);
            $logger = array(

                'user_id' => $this->session->userdata('user_id'),
                'activity' => 'Paid a loan, loan ID:'.$loan_number,
                'activity_cate' => 'loan_repayment'

            );
            log_activity($logger);

            $data = array(
                'ref' => "CF." . date('Y') . date('m') . date('d') . '.' . rand(100, 999),
                'loan_id' => $loan_number,
                'amount' => $amount,
                'transaction_type' => 1,
                'payment_number' => 0,
                'method' => $this->input->post('payment_method'),
                'payment_proof' => $proof,
                'reference' => $this->input->post('reference'),
                'added_by' => $this->session->userdata('user_id')

            );

            $this->Transactions_model->insert($data);
            $this->toaster->success('Success, payment was successful');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
    public function pay_loan_latest(){
        $loan_number = $this->input->post('loan_id');
        $pay_number = $this->input->post('payment_number');
        $pay_number_r = $this->input->post('payment_numberr');
        $amount = $this->input->post('amount');
        $proof = $this->input->post('pay_proof');
        $paid_date = $this->input->post('paid_date');
        $this->Payement_schedules_model->new_pay($loan_number,$pay_number,$amount,$paid_date);
        $logger = array(

            'user_id' => $this->session->userdata('user_id'),
            'activity' => 'Paid a loan, loan ID:' . ' ' . $loan_number . ' ' . ' payment number' . ' ' . $pay_number .
                ' ' . 'amount' . ' ' . $amount,
            'activity_cate' => 'loan_repayment'

        );
        log_activity($logger);
        $this->toaster->success('Success, payment was successful');
        redirect($_SERVER['HTTP_REFERER']);
    }
	public function pay_loan_l()
	{
		$loan_id = $this->input->post('loan_id');
		$payment_number = $this->input->post('payment_number');
		$amount = $this->input->post('amount');
		$acrued_amount = $this->input->post('acrued_amount');
		$paid_date = $this->input->post('paid_date');


		// Process regular payment if not a bullet payment

			$this->Payement_schedules_model->new_pay($loan_id, $payment_number, $amount, $paid_date, $acrued_amount);
			$logger = array(
				'user_id' => $this->session->userdata('user_id'),
				'activity' => 'Paid a loan, loan ID: ' . $loan_id . ' payment number: ' . $payment_number .
					' amount: ' . $amount,
				'activity_cate' => 'loan_repayment'
			);
			log_activity($logger);


		$this->toaster->success('Success, payment was successful');
		redirect($_SERVER['HTTP_REFERER']);
	}
	public function pay_loan(){
		$loan_number = $this->input->post('loan_id');
		$pay_number = $this->input->post('payment_number');
		$amount = $this->input->post('amount');
		$acrued_amount = $this->input->post('acrued_amount');
		$paid_date = $this->input->post('paid_date');

		$unique_name = "";

		$config['upload_path']   = './uploads/';

		$config['allowed_types'] = 'jpg|png|jpeg|gif|pdf|docx|txt|zip';
		$config['max_size']      = 2048; // 2MB
		$config['remove_spaces'] = TRUE;

		// Load the upload library
		$this->load->library('upload', $config);

		if (!empty($_FILES['pay_proof']['name'])) {
			$file_name = pathinfo($_FILES['pay_proof']['name'], PATHINFO_FILENAME);
			$file_ext = pathinfo($_FILES['pay_proof']['name'], PATHINFO_EXTENSION);

			// Generate a unique file name
			$unique_name =  'file_' . time() . '_' . uniqid() . '.' . $file_ext;
			$config['file_name'] = $unique_name;

			// Reinitialize with new config
			$this->upload->initialize($config);

			if (!$this->upload->do_upload('pay_proof')) {
//                $error = array('error' => $this->upload->display_errors());
//                $this->load->view('upload_form', $error);
			} else {

			}
		}
		$tid = "TR-S" . rand(100, 9999) . date('Y') . date('m') . date('d');

		$loan_account = get_by_id('loan', 'loan_id', $loan_number);
		$recepientt = get_by_id('account', 'collection_account', 'Yes');
		$loan_full    = $this->Loan_model->get_by_id($loan_number);
		$is_non_bullet = ($loan_full && $loan_full->calculation_type !== 'Bullet Payment');

		if($this->input->post('payment_method')=="0") {
			$get_account = $this->Tellering_model->get_teller_account($this->session->userdata('user_id'));
			if(empty($get_account)){


				$this->toaster->error('You are not authorized to do this transaction, only cashiers');
				redirect($_SERVER['HTTP_REFERER']);
			}else {
				$check = $this->Account_model->get_account($loan_account->loan_number);
				if ($check->balance >= $amount) {
					$do_transactions = $this->Account_model->transfer_funds($loan_account->loan_number, $recepientt->account_number, $amount, $tid, $paid_date, $unique_name);
					if ($do_transactions == 'success') {


						if ($is_non_bullet) {
							$result = $this->_process_non_bullet_payment($loan_number, $amount, $paid_date, $tid);
						} else {
							$result = $this->Payement_schedules_model->new_pay($loan_number, $pay_number, $amount, $paid_date, $acrued_amount);
							if ($result) {
								$this->Transactions_model->insert(array(
									'ref'              => $tid,
									'loan_id'          => $loan_number,
									'amount'           => $amount,
									'transaction_type' => 3,
									'payment_number'   => $pay_number,
									'method'           => $this->input->post('payment_method'),
									'payment_proof'    => $unique_name,
									'reference'        => $this->input->post('reference'),
									'date_stamp'       => $paid_date,
									'added_by'         => $this->session->userdata('user_id'),
								));
							}
						}

						if ($result) {

							$logger = array(

								'user_id' => $this->session->userdata('user_id'),
								'activity' => 'Paid a loan, loan ID:' . ' ' . $loan_number . ' ' . ' payment number' . ' ' . $pay_number .
									' ' . 'amount' . ' ' . $amount,
								'activity_cate' => 'loan_repayment'

							);
							log_activity($logger);

							// Check if loan was closed after payment
							$loan_after = get_by_id('loan', 'loan_id', $loan_number);
							if ($loan_after && $loan_after->loan_status == 'CLOSED') {
								$this->toaster->success('Success! Loan has been fully paid and closed.');
							} else {
								$this->toaster->success('Success, payment was successful');
							}
							redirect($_SERVER['HTTP_REFERER']);
						} else {
							$this->toaster->error('Ops!, Sorry payment failed P2');
							redirect($_SERVER['HTTP_REFERER']);
						}
					} else {
						$this->toaster->error('Ops!, Sorry payment failed, Error P2');
						redirect($_SERVER['HTTP_REFERER']);
					}
				} elseif ($check->balance > 0 && $check->balance < $amount) {
					$topay_amount = $check->balance;
					$this->Account_model->transfer_funds($loan_account->loan_number, $recepientt->account_number, $topay_amount, $tid, $paid_date);
					$r = $this->Payement_schedules_model->new_pay($loan_number, $pay_number, $topay_amount, $paid_date);

					$logger = array(

						'user_id' => $this->session->userdata('user_id'),
						'activity' => 'Paid a loan, loan ID:' . ' ' . $loan_number . ' ' . ' payment number' . ' ' . $pay_number .
							' ' . 'amount' . ' ' . $topay_amount,
						'activity_cate' => 'loan_repayment'

					);
					log_activity($logger);
					$data = array(
						'ref'              => $tid,
						'loan_id'          => $this->input->post('loan_id', TRUE),
						'amount'           => $topay_amount,
						'transaction_type' => 3,
						'payment_number'   => $this->input->post('payment_number'),
						'date_stamp'       => $paid_date,
						'added_by'         => $this->session->userdata('user_id'),
					);

					$this->Transactions_model->insert($data);
					$this->toaster->success('Success, payment was successful');
					redirect($_SERVER['HTTP_REFERER']);

				} else {
					$this->toaster->error('Ops!, Sorry payment failed loan account savings does not have enough funds');
					redirect($_SERVER['HTTP_REFERER']);
				}
			}
		}
		else{
			$get_account = $this->Tellering_model->get_teller_account($this->session->userdata('user_id'));
			if(empty($get_account)){


				$this->toaster->error('You are not authorized to do this transaction, only cashiers');
				redirect($_SERVER['HTTP_REFERER']);
			}

			else {
				$teller_account = $get_account->account;
				$mode='deposit';
				$deposit_money = $this->Account_model->cash_transaction($teller_account, $loan_account->loan_number, $amount, $mode, $tid, $paid_date,$unique_name);


				//
				if($deposit_money){

					$pay_late_first = $this->Account_model->transfer_funds($loan_account->loan_number, $recepientt->account_number, $amount, $tid, $paid_date,$unique_name);


					if ($pay_late_first == 'success') {
						if ($is_non_bullet) {
							$this->_process_non_bullet_payment($loan_number, $amount, $paid_date, $tid);
						} else {
							$this->Payement_schedules_model->new_pay($loan_number, $pay_number, $amount, $paid_date, $acrued_amount);
							$this->Transactions_model->insert(array(
								'ref'              => $tid,
								'loan_id'          => $loan_number,
								'amount'           => $amount,
								'transaction_type' => 3,
								'payment_number'   => $pay_number,
								'method'           => $this->input->post('payment_method'),
								'payment_proof'    => $unique_name,
								'reference'        => $this->input->post('reference'),
								'date_stamp'       => $paid_date,
								'added_by'         => $this->session->userdata('user_id'),
							));
						}
						$logger = array(

							'user_id' => $this->session->userdata('user_id'),
							'activity' => 'Paid a loan, loan ID:' . $loan_number,
							'activity_cate' => 'loan_repayment'

						);
						log_activity($logger);

						// Check if loan was closed after payment
						$loan_after = get_by_id('loan', 'loan_id', $loan_number);
						if ($loan_after && $loan_after->loan_status == 'CLOSED') {
							$this->toaster->success('Success! Loan has been fully paid and closed.');
						} else {
							$this->toaster->success('Success, payment was successful');
						}
						redirect($_SERVER['HTTP_REFERER']);
					}
				}
				else{
					echo "deposit failed";
					exit();
				}
			}
		}
	}
	/**
	 * Process bullet payment
	 */
	private function processBulletPayment($loan_id, $payoff, $paid_date)
	{
		// Get loan details
		$loan = $this->Loan_model->get_by_id($loan_id);

		// Get payment schedule
		$payment = $this->Payement_schedules_model->get_by_loan_payment($loan_id, 1);

		if (!$payment) {
			$this->toaster->error('Error: Payment schedule not found');
			redirect($_SERVER['HTTP_REFERER']);
			return;
		}

		// Update payment record
		$data = array(
			'status' => 'PAID',
			'paid_amount' => $payoff['total_payoff'],
			'interest' => $payoff['interest'], // Actual calculated interest
			'paid_date' => $paid_date
		);

		$this->Payement_schedules_model->update($payment->id, $data);

		// Update loan status if fully paid
		$this->Loan_model->update($loan_id, array(
			'loan_status' => 'CLOSED',
			'next_payment_id' => 1
		));

		// Log the transaction
		$logger = array(
			'user_id' => $this->session->userdata('user_id'),
			'activity' => 'Paid a bullet loan, loan ID: ' . $loan_id .
				' Principal: ' . $payoff['principal'] .
				' Interest: ' . $payoff['interest'] .
				' Total: ' . $payoff['total_payoff'],
			'activity_cate' => 'loan_repayment'
		);
		log_activity($logger);

		// Record transaction details
		$data = array(
			'ref' => "BLT." . date('Y-m-d', strtotime($paid_date)) . '.' . rand(100, 999),
			'loan_id' => $loan_id,
			'amount' => $payoff['total_payoff'],
			'principal' => $payoff['principal'],
			'interest' => $payoff['interest'],
			'transaction_type' => 1,
			'payment_number' => 1,
			'date_stamp' => $paid_date,
			'added_by' => $this->session->userdata('user_id')
		);

		$this->Transactions_model->insert($data);
	}
	public function calculate_bullet_payoff()
	{
		// Check if this is an AJAX request
		if (!$this->input->is_ajax_request()) {
			exit('No direct script access allowed');
		}

		$loan_id = $this->input->post('loan_id');
		$payment_date = $this->input->post('payment_date');

		if (!$loan_id) {
			echo json_encode([
				'status' => 'error',
				'message' => 'No loan ID provided'
			]);
			return;
		}

		// Calculate payoff
		$payoff = $this->Loan_model->calculateBulletPayoff($loan_id, $payment_date);

		if ($payoff) {
			echo json_encode([
				'status' => 'success',
				'data' => $payoff
			]);
		} else {
			echo json_encode([
				'status' => 'error',
				'message' => 'Could not calculate payoff amount'
			]);
		}
	}
    public function pay_loan_r(){
        $loan_number = $this->input->post('loan_id');
        $pay_number = $this->input->post('payment_number');
        $pay_number_r = $this->input->post('payment_numberr');
        $amount = $this->input->post('amount');

        $paid_date = $this->input->post('paid_date');
        $unique_name = "";

        $config['upload_path']   = './uploads/';

        $config['allowed_types'] = 'jpg|png|jpeg|gif|pdf|docx|txt|zip';
        $config['max_size']      = 2048; // 2MB
        $config['remove_spaces'] = TRUE;

        // Load the upload library
        $this->load->library('upload', $config);

        if (!empty($_FILES['pay_proof']['name'])) {
            $file_name = pathinfo($_FILES['pay_proof']['name'], PATHINFO_FILENAME);
            $file_ext = pathinfo($_FILES['pay_proof']['name'], PATHINFO_EXTENSION);

            // Generate a unique file name
            $unique_name =  'file_' . time() . '_' . uniqid() . '.' . $file_ext;
            $config['file_name'] = $unique_name;

            // Reinitialize with new config
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('pay_proof')) {
//                $error = array('error' => $this->upload->display_errors());
//                $this->load->view('upload_form', $error);
            } else {

            }
        }
        $tid = "TR-S" . rand(100, 9999) . date('Y') . date('m') . date('d');

        $loan_account = get_by_id('loan', 'loan_id', $loan_number);
        $recepientt = get_by_id('account', 'collection_account', 'Yes');

        if($this->input->post('payment_method')=="0") {
            $check = $this->Account_model->get_account($loan_account->loan_number);
            if ($check->balance >= $amount) {
                $do_transactions = $this->Account_model->transfer_funds($loan_account->loan_number, $recepientt->account_number, $amount, $tid,  $paid_date, $unique_name);
                if ($do_transactions == 'success') {


                    $result = $this->Payement_schedules_model->new_pay($loan_number, $pay_number, $amount,  $paid_date);

                    if ($result == true) {

                        $this->Transactions_model->insert(array(
                            'ref'              => $tid,
                            'loan_id'          => $loan_number,
                            'amount'           => $amount,
                            'transaction_type' => 3,
                            'payment_number'   => $pay_number,
                            'payment_proof'    => $unique_name,
                            'date_stamp'       => $paid_date,
                            'added_by'         => $this->session->userdata('user_id'),
                        ));

                        $logger = array(

                            'user_id' => $this->session->userdata('user_id'),
                            'activity' => 'Paid a loan, loan ID:' . ' ' . $loan_number . ' ' . ' payment number' . ' ' . $pay_number .
                                ' ' . 'amount' . ' ' . $amount,
                            'activity_cate' => 'loan_repayment'

                        );
                        log_activity($logger);
                        $this->toaster->success('Success, payment was successful');
                        redirect($_SERVER['HTTP_REFERER']);
                    } else {
                        $this->toaster->error('Ops!, Sorry payment failed P2');
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                } else {
                    $this->toaster->error('Ops!, Sorry payment failed, Error P2');
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }
            elseif ($check->balance > 0 && $check->balance < $amount) {
                $topay_amount = $check->balance;
                $this->Account_model->transfer_funds($loan_account->loan_number, $recepientt->account_number, $topay_amount, $tid,  $paid_date);
                $r = $this->Payement_schedules_model->new_pay($loan_number, $pay_number, $topay_amount,  $paid_date);

                $logger = array(

                    'user_id' => $this->session->userdata('user_id'),
                    'activity' => 'Paid a loan, loan ID:' . ' ' . $loan_number . ' ' . ' payment number' . ' ' . $pay_number .
                        ' ' . 'amount' . ' ' . $topay_amount,
                    'activity_cate' => 'loan_repayment'

                );
                log_activity($logger);
                $data = array(
                    'ref'              => $tid,
                    'loan_id'          => $this->input->post('loan_id', TRUE),
                    'amount'           => $topay_amount,
                    'transaction_type' => 3,
                    'payment_number'   => $this->input->post('payment_number'),
                    'payment_proof'    => $unique_name,
                    'date_stamp'       => $paid_date,
                    'added_by'         => $this->session->userdata('user_id'),
                );

                $this->Transactions_model->insert($data);
                $this->toaster->success('Success, payment was successful');
                redirect($_SERVER['HTTP_REFERER']);

            }
            else {
                $this->toaster->error('Ops!, Sorry payment failed loan account savings does not have enough funds');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
        else{
            $get_account = $this->Tellering_model->get_teller_account($this->session->userdata('user_id'));
            if(empty($get_account)){


                $this->toaster->error('You are not authorized to do this transaction, only cashiers');
                redirect($_SERVER['HTTP_REFERER']);
            }

            else {
                $teller_account = $get_account->account;
                $mode='deposit';
                $deposit_money = $this->Account_model->cash_transaction($teller_account, $loan_account->loan_number, $amount, $mode, $tid, $paid_date,$unique_name);


                //
                if($deposit_money){

                    $pay_late_first = $this->Account_model->transfer_funds($loan_account->loan_number, $recepientt->account_number, $amount, $tid, $paid_date,$unique_name);


                    if ($pay_late_first == 'success') {
                        $this->Payement_schedules_model->new_pay($loan_number, $pay_number, $amount, $paid_date);
                        // $this->Rescheduled_payments_model->new__late_pay($loan_number, $pay_number, $amount);
                        $this->Transactions_model->insert(array(
                            'ref'              => $tid,
                            'loan_id'          => $loan_number,
                            'amount'           => $amount,
                            'transaction_type' => 3,
                            'payment_number'   => $pay_number,
                            'payment_proof'    => $unique_name,
                            'date_stamp'       => $paid_date,
                            'added_by'         => $this->session->userdata('user_id'),
                        ));
                        $logger = array(

                            'user_id' => $this->session->userdata('user_id'),
                            'activity' => 'Paid a loan, loan ID:' . $loan_number,
                            'activity_cate' => 'loan_repayment'

                        );
                        log_activity($logger);


                        $this->toaster->success('Success, payment was successful');
                        redirect($_SERVER['HTTP_REFERER']);
                    }
                }
                else{
                    echo "deposit failed";
                    exit();
                }
            }
        }
    }
    public function pay_late_loan(){
        $transid = "TXN.".date('Y').date('m').date('d').'.'.rand(100,999);
        $loan_number = $this->input->post('loan_id');

        $pay_number = $this->input->post('payment_number');

        $lamount = $this->input->post('lamount');
        $amount = $this->input->post('amount');
        $recepientt = get_by_id('account','collection_account','Yes');
        $sender = get_by_id('loan','loan_id',$loan_number);

        $proof = $this->input->post('pay_proof');
        $paid_date = $this->input->post('paid_date');

        if($this->input->post('payment_method')=="0")
        {

//     make deductions first
            $get_sender_balance = get_by_id('account', 'account_number', $sender->loan_number);

            $check_if_paid = $this->Transactions_model->get_by_loan($loan_number);
            if (!empty($check_if_paid)) {
                $get_sender_balance2 = get_by_id('account', 'account_number', $sender->loan_number);

                if ($get_sender_balance2->balance >= $amount) {

                    $pay_late_first = $this->Account_model->transfer_funds($sender->loan_number, $recepientt->account_number, $lamount, $transid, $paid_date);


                    if ($pay_late_first == 'success') {

                    $this->Account_model->transfer_funds($sender->loan_number, $recepientt->account_number, $amount, $transid, $paid_date);
                    $result = $this->Payement_schedules_model->new_late_pay($loan_number, $pay_number, $amount, $paid_date);
                    $logger = array(

                        'user_id' => $this->session->userdata('user_id'),
                        'activity' => 'Paid a loan, loan ID:' . ' ' . $loan_number . ' ' . ' payment number' . ' ' . $pay_number .
                            ' ' . 'amount' . ' ' . $amount,
                        'activity_cate' => 'loan_repayment'

                    );
                    log_activity($logger);
                    $data = array(
                        'ref'              => $transid,
                        'loan_id'          => $this->input->post('loan_id', TRUE),
                        'amount'           => $amount,
                        'transaction_type' => 3,
                        'payment_number'   => $this->input->post('payment_number'),
                        'method'           => $this->input->post('payment_method'),
                        'payment_proof'    => $proof,
                        'reference'        => $this->input->post('reference'),
                        'date_stamp'       => $paid_date,
                        'added_by'         => $this->session->userdata('user_id'),
                    );

                    $this->Transactions_model->insert($data);
                    $this->toaster->success('Success, payment was successful');
                    redirect($_SERVER['HTTP_REFERER']);

                }
                } elseif ($get_sender_balance2->balance > 0 && $get_sender_balance2->balance < $amount) {
                    $topay_amount = $get_sender_balance->balance;
                    $this->Account_model->transfer_funds($sender->loan_number, $recepientt->account_number, $topay_amount, $transid, $paid_date);
                    $pay_late_first = $this->Account_model->transfer_funds($sender->loan_number, $recepientt->account_number, $lamount, $transid, $paid_date);


                    if ($pay_late_first == 'success') {

                    $result = $this->Payement_schedules_model->new_late_pay($loan_number, $pay_number, $topay_amount, $paid_date);
                    $logger = array(

                        'user_id' => $this->session->userdata('user_id'),
                        'activity' => 'Paid a loan, loan ID:' . ' ' . $loan_number . ' ' . ' payment number' . ' ' . $pay_number .
                            ' ' . 'amount' . ' ' . $topay_amount,
                        'activity_cate' => 'loan_repayment'

                    );
                    log_activity($logger);
                    $data = array(
                        'ref'              => $transid,
                        'loan_id'          => $this->input->post('loan_id', TRUE),
                        'amount'           => $topay_amount,
                        'transaction_type' => 3,
                        'payment_number'   => $this->input->post('payment_number'),
                        'date_stamp'       => $paid_date,
                        'method'           => $this->input->post('payment_method'),
                        'payment_proof'    => $proof,
                        'reference'        => $this->input->post('reference'),
                        'added_by'         => $this->session->userdata('user_id'),
                    );

                    $this->Transactions_model->insert($data);
                    $this->toaster->success('Success, payment was successful');
                    redirect($_SERVER['HTTP_REFERER']);
                  }
                } else {
                    $this->toaster->error('Ops!, Sorry payment failed loan account  does not have enough funds');
                    redirect($_SERVER['HTTP_REFERER']);
                }
            } else {
                if ($get_sender_balance->balance > $lamount) {

                    //

                    //


                    $pay_late_first = $this->Account_model->transfer_funds($sender->loan_number, $recepientt->account_number, $lamount, $transid, $paid_date);


                    if ($pay_late_first == 'success') {

                        $get_sender_balance2 = get_by_id('account', 'account_number', $sender->loan_number);


                        if ($get_sender_balance2->balance > $amount) {

                            $this->Account_model->transfer_funds($sender->loan_number, $recepientt->account_number, $amount, $transid, $paid_date);
                            $result = $this->Payement_schedules_model->new_late_pay($loan_number, $pay_number, $amount, $paid_date);
                            $logger = array(

                                'user_id' => $this->session->userdata('user_id'),
                                'activity' => 'Paid a loan, loan ID:' . ' ' . $loan_number . ' ' . ' payment number' . ' ' . $pay_number .
                                    ' ' . 'amount' . ' ' . $amount,
                                'activity_cate' => 'loan_repayment'

                            );
                            log_activity($logger);
                            $data = array(
                                'ref'              => $transid,
                                'loan_id'          => $this->input->post('loan_id', TRUE),
                                'amount'           => $amount,
                                'transaction_type' => 3,
                                'payment_number'   => $this->input->post('payment_number'),
                                'date_stamp'       => $paid_date,
                                'method'           => $this->input->post('payment_method'),
                                'payment_proof'    => $proof,
                                'reference'        => $this->input->post('reference'),
                                'added_by'         => $this->session->userdata('user_id'),
                            );

                            $this->Transactions_model->insert($data);
                            $this->toaster->success('Success, payment was successful');
                            redirect($_SERVER['HTTP_REFERER']);
                        } elseif ($get_sender_balance2->balance > 0 && $get_sender_balance2->balance < $amount) {

                            $topay_amount = $get_sender_balance2->balance;


                            $this->Account_model->transfer_funds($sender->loan_number, $recepientt->account_number, $topay_amount, $transid, $paid_date);
                            $result = $this->Payement_schedules_model->new_late_pay($loan_number, $pay_number, $topay_amount, $paid_date);
                            $logger = array(

                                'user_id' => $this->session->userdata('user_id'),
                                'activity' => 'Paid a loan, loan ID:' . ' ' . $loan_number . ' ' . ' payment number' . ' ' . $pay_number .
                                    ' ' . 'amount' . ' ' . $topay_amount,
                                'activity_cate' => 'loan_repayment'

                            );
                            log_activity($logger);
                            $data = array(
                                'ref'              => $transid,
                                'loan_id'          => $this->input->post('loan_id', TRUE),
                                'amount'           => $topay_amount,
                                'transaction_type' => 3,
                                'payment_number'   => $this->input->post('payment_number'),
                                'date_stamp'       => $paid_date,
                                'added_by'         => $this->session->userdata('user_id'),
                            );

                            $this->Transactions_model->insert($data);
                            $this->toaster->success('Success, payment was successful');
                            redirect($_SERVER['HTTP_REFERER']);

                        } else {
                            $this->toaster->error('Ops!, Sorry payment failed loan account  does not have enough funds');
                            redirect($_SERVER['HTTP_REFERER']);
                        }


                    } else {
                        $this->toaster->error('Ops!, Sorry payment failed');
                        redirect($_SERVER['HTTP_REFERER']);
                    }

                } else {
                    $this->toaster->error('Ops!, Sorry late fee payment failed loan account  does not have enough funds');
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }

        }
        else {


            //
            //cash_transaction($teller_account,$account,$amount,$mode,$transid)
            $get_account = $this->Tellering_model->get_teller_account($this->session->userdata('user_id'));
            if(empty($get_account)){
                $result['status']= 'error';

                $this->toaster->error('You are not authorized to do this transaction, only cashiers');
                redirect($_SERVER['HTTP_REFERER']);
            }

            else {
                $teller_account = $get_account->account;
                $mode='deposit';
                $deposit_money = $this->Account_model->cash_transaction($teller_account, $sender->loan_number, $lamount, $mode, $transid, $paid_date);


                //
                if( $deposit_money){

                $pay_late_first = $this->Account_model->transfer_funds($sender->loan_number, $recepientt->account_number, $lamount, $transid, $paid_date);


                if ($pay_late_first == 'success') {
                    $this->Payement_schedules_model->new_late_pay($loan_number, $pay_number, $amount, $paid_date);
                   // $this->Rescheduled_payments_model->new__late_pay($loan_number, $pay_number, $amount);
                    $logger = array(

                        'user_id' => $this->session->userdata('user_id'),
                        'activity' => 'Paid a loan, loan ID:' . $loan_number,
                        'activity_cate' => 'loan_repayment'

                    );
                    log_activity($logger);

                    $data = array(
                        'ref'              => $transid,
                        'loan_id'          => $loan_number,
                        'amount'           => $amount,
                        'transaction_type' => 3,
                        'payment_number'   => $pay_number,
                        'method'           => $this->input->post('payment_method'),
                        'payment_proof'    => $proof,
                        'reference'        => $this->input->post('reference'),
                        'date_stamp'       => $paid_date,
                        'added_by'         => $this->session->userdata('user_id'),
                    );

                    $this->Transactions_model->insert($data);
                    $this->toaster->success('Success, payment was successful');
                    redirect($_SERVER['HTTP_REFERER']);
                }
                }
                else{
                    echo "deposit failed";
                    exit();
                }
            }
        }

        }





    function delete_loan($id)
    {
        $row = $this->Loan_model->get_by_id($id);


        if ($row) {
            $data = array(
                'delete_requested'=>"Yes",
                'delete_by'=> $this->session->userdata("user_id")
            );

            $this->Loan_model->update($id,$data);



            $this->toaster->success('Success, your action successful');
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect($_SERVER['HTTP_REFERER']);
        }





    }
    function delete_approve($id)
    {
        $row = $this->Loan_model->get_by_id($id);

//        $scores = $this->Loan_recommendation_model->get_by_loan($row->loan_id);

        if ($row) {
            $data = array(
                'delete_requested'=>"Yes",
                'loan_status'=>"DELETED",
                'delete_athourise_by'=> $this->session->userdata("user_id")
            );

            $this->Loan_model->update($id,$data);



            $this->toaster->success('Success, your action successful');
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect($_SERVER['HTTP_REFERER']);
        }





    } function delete_reject($id)
    {
        $row = $this->Loan_model->get_by_id($id);

//        $scores = $this->Loan_recommendation_model->get_by_loan($row->loan_id);

        if ($row) {
            $data = array(
                'delete_requested'=>"Yes",
                'delete_rejected_by'=> $this->session->userdata("user_id")
            );

            $this->Loan_model->update($id,$data);



            $this->toaster->success('Success, your action successful');
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect($_SERVER['HTTP_REFERER']);
        }





    }
    function delete_recommend($id)
    {
        $row = $this->Loan_model->get_by_id($id);

//        $scores = $this->Loan_recommendation_model->get_by_loan($row->loan_id);

        if ($row) {
            $data = array(
                'delete_requested'=>"Yes",
                'delete_approve_by'=> $this->session->userdata("user_id")
            );

            $this->Loan_model->update($id,$data);



            $this->toaster->success('Success, your action successful');
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect($_SERVER['HTTP_REFERER']);
        }





    }

    /**
     * Unified approval page - automatically determines the appropriate approval level
     * based on the loan's current status
     */
    function approve($id){
        $row = $this->Loan_model->get_by_id($id);

        if(!$row){
            $this->toaster->error('Error: Loan not found');
            redirect('loan/track');
            return;
        }

        // Determine the appropriate action based on loan status
        $action = null;
        switch($row->loan_status){
            case 'INITIATED':
                $action = 'recommend';
                break;
            case 'RECOMMENDED':
                // Use multi-level approval system
                $action = 'multi_approve';
                break;
            case 'APPROVED_FIRST':
                $action = 'approve_second';
                break;
            case 'APPROVED_SECOND':
                $action = 'approve_third';
                break;
            case 'APPROVED':
                $action = 'disburse';
                break;
            case 'ACTIVE':
            case 'DISBURSED':
            case 'CLOSED':
                // Already fully processed, just view
                $action = null;
                break;
            case 'REJECTED':
                $action = null;
                break;
            default:
                $action = null;
        }

        // Redirect to view with the determined action
        $this->view($id, $action);
    }

    function view($id, $action = null){
        // Get action from URL parameter if not passed directly
        if ($action === null) {
            $action = $this->input->get('action');
        }

        $row = $this->Loan_model->get_by_id($id);
        $payments = $this->Payement_schedules_model->get_all_by_id($row->loan_id);
        $files = $this->Loan_files_model->get_by_loans($row->loan_id);
        $bank_statements = $this->db->where('loan_id', $row->loan_id)->get('bank_statements')->result();
//        $scores = $this->Loan_recommendation_model->get_by_loan($row->loan_id);

        if($row->customer_type=='group'){
            $group = $this->Groups_model->get_by_id($row->loan_customer);

            $customer_name = $group->group_name.'('.$group->group_code.')';
            $preview_url = "Customer_groups/members/";
        }elseif($row->customer_type=='individual'){
            $indi = $this->Individual_customers_model->get_by_id($row->loan_customer);
            $customer_name = $indi->Firstname.' '.$indi->Lastname;
            $preview_url = "Individual_customers/view/";
        }
        elseif($row->customer_type=='institution'){
            $inst = get_by_id('corporate_customers','id',$row->loan_customer);
            $customer_name = $inst->EntityName.' - '.$inst->RegistrationNumber.' ('.$inst->	entity_type.')';
            $preview_url = "Corporate_customers/read/";
        }
		$acrued = array();
		if($row->calculation_type=='Bullet Payment'){
			$acrued = $this->calculate_payoff_inline($row->loan_id);
		}

        // Compute schedule totals for amortisation footer and closed-loan summary cards
        $total_schedule_interest  = 0;
        $total_schedule_principal = 0;
        $total_schedule_amount    = 0;
        $total_paid_interest      = 0;
        foreach ($payments as $p) {
            $total_schedule_interest  += floatval($p->interest  ?? 0);
            $total_schedule_principal += floatval($p->principal ?? 0);
            $total_schedule_amount    += floatval($p->amount    ?? 0);
        }
        // For closed loans, actual interest = what was recorded in PAID rows
        if ($row->loan_status == 'CLOSED') {
            foreach ($payments as $p) {
                if ($p->status == 'PAID') {
                    if ($row->calculation_type == 'Bullet Payment') {
                        $total_paid_interest += max(0, floatval($p->paid_amount ?? 0) - floatval($p->principal ?? 0));
                    } else {
                        $total_paid_interest += floatval($p->interest ?? 0);
                    }
                }
            }
            if ($total_paid_interest == 0) {
                $total_paid_interest = $total_schedule_interest;
            }
        }

        // Get linked collaterals for this loan
        $linked_collaterals = $this->Collateral_model->get_loan_collaterals($row->loan_id);
        $total_force_sale = 0;
        $total_utilized = 0;
        foreach ($linked_collaterals as &$lc) {
            $total_force_sale += floatval($lc->force_sale_value ?? 0);
            $total_utilized += floatval($lc->amount_utilized ?? 0);
        }

        // Get customer collaterals for linking (available ones)
        $cust_type = ($row->customer_type == 'institution') ? 'institution' : 'individual';
        $customer_collaterals = $this->Collateral_model->get_by_customer($row->loan_customer, $cust_type);

        // Get multi-level approval data for RECOMMENDED loans
        $approvers = array();
        $can_approve = false;
        $approval_reason = '';
        if($row->loan_status == 'RECOMMENDED') {
            $approvers = get_loan_approvers($row->loan_id);
            $approval_count = count($approvers);
            $last_approver_id = !empty($approvers) ? end($approvers)['user_id'] : null;
            $current_user_id = $this->session->userdata('user_id');

            // Only check for consecutive approvals - same user CAN approve 1st and 3rd
            // But same user CANNOT approve consecutively (1st and 2nd, or 2nd and 3rd)
            if($last_approver_id == $current_user_id) {
                $can_approve = false;
                $approval_reason = 'You cannot approve consecutively. Another user must approve next.';
            } else {
                $can_approve = true;
            }
        }

        $data = array(
            'loan_id' => $row->loan_id,
            'loan_number' => $row->loan_number,
            'loan_product' => $row->product_name,
            'customer_type' => $row->customer_type,
            'loan_customer' => $customer_name,
            'preview_url' => $preview_url,
            'customer_id' => $row->loan_customer,
            'loan_date' => $row->loan_date,
            'loan_principal' => $row->loan_principal,
            'loan_period' => $row->loan_period,
            'period_type' => $row->period_type,
            'loan_interest' => $row->loan_interest,
            'loan_interest_amount' => $row->loan_interest_amount,
            'loan_amount_total' => $row->loan_amount_total,
            'narration' => $row->narration,
            'loan_amount_term' => $row->loan_amount_term,
            'next_payment_id' => $row->next_payment_id,
            'loan_added_by' => $row->loan_added_by,
            'loan_approved_by' => $row->loan_approved_by,
            'loan_status' => $row->loan_status,
            'loan_added_date' => $row->loan_added_date,
            'payments'=>$payments,
            'files'=>$files,
            'currency'=>$row->currency,
            'processing_fee'=>$row->processing_fee,
            'calculation_type'=>$row->calculation_type,
			'acrued' => $acrued,
            'action' => $action,
            'linked_collaterals' => $linked_collaterals,
            'total_force_sale' => $total_force_sale,
            'total_utilized' => $total_utilized,
            'customer_collaterals' => $customer_collaterals,
            'cust_id' => $row->loan_customer,
            // Multi-level approval data
            'approvers' => $approvers,
            'can_approve' => $can_approve,
            'approval_reason' => $approval_reason,
            'total_schedule_interest'  => $total_schedule_interest,
            'total_schedule_principal' => $total_schedule_principal,
            'total_schedule_amount'    => $total_schedule_amount,
            'total_paid_interest'      => $total_paid_interest,
            // Sent back data
            'sent_back' => isset($row->sent_back) ? $row->sent_back : 0,
            'sent_back_comment' => isset($row->sent_back_comment) ? $row->sent_back_comment : '',
            'sent_back_by_name' => '',
            'sent_back_date' => isset($row->sent_back_date) ? $row->sent_back_date : '',
            'bank_statements' => $bank_statements,

        );

        // Get sent back by user name if loan was sent back
        if (isset($row->sent_back) && $row->sent_back == 1 && isset($row->sent_back_by)) {
            $sent_back_user = $this->db->get_where('employees', array('id' => $row->sent_back_by))->row();
            if ($sent_back_user) {
                $data['sent_back_by_name'] = $sent_back_user->Firstname . ' ' . $sent_back_user->Lastname;
            }
        }
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/view',$data);
        $this->load->view('admin/footer');
    } 
    function client_summary($id){
        $row = $this->Loan_model->get_by_id($id);
        $payments = $this->Payement_schedules_model->get_all_by_id($row->loan_id);
        $files = $this->Loan_files_model->get_by_loans($row->loan_id);
//        $scores = $this->Loan_recommendation_model->get_by_loan($row->loan_id);

        if($row->customer_type=='group'){
            $group = $this->Groups_model->get_by_id($row->loan_customer);

            $customer_name = $group->group_name.'('.$group->group_code.')';
            $preview_url = "Customer_groups/members/";
        }elseif($row->customer_type=='individual'){
            $indi = $this->Individual_customers_model->get_by_id($row->loan_customer);
            $customer_name = $indi->Firstname.' '.$indi->Lastname;
            $preview_url = "Individual_customers/view/";
        }
        elseif($row->customer_type=='institution'){
            $inst = get_by_id('corporate_customers','id',$row->loan_customer);
            $customer_name = $inst->EntityName.' - '.$inst->RegistrationNumber.' ('.$inst->	entity_type.')';
            $preview_url = "Corporate_customers/read/";
        }

        $data = array(
            'loan_id' => $row->loan_id,
            'loan_number' => $row->loan_number,
            'loan_product' => $row->product_name,
            'customer_type' => $row->customer_type,
            'loan_customer' => $customer_name,
            'preview_url' => $preview_url,
            'customer_id' => $row->loan_customer,
            'loan_date' => $row->loan_date,
            'loan_principal' => $row->loan_principal,
            'loan_period' => $row->loan_period,
            'period_type' => $row->period_type,
            'loan_interest' => $row->loan_interest,
            'loan_interest_amount' => $row->loan_interest_amount,
            'loan_amount_total' => $row->loan_amount_total,
            'loan_amount_term' => $row->loan_amount_term,
            'next_payment_id' => $row->next_payment_id,
            'loan_added_by' => $row->loan_added_by,
            'loan_approved_by' => $row->loan_approved_by,
            'loan_status' => $row->loan_status,
            'loan_added_date' => $row->loan_added_date,
            'payments'=>$payments,
            'files'=>$files,
            'off_taker'=>$row->off_taker,
            'processing_fee'=>$row->processing_fee,
            'currency'=>$row->currency

        );
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/client_summary',$data);
        $this->load->view('admin/footer');
    }

    function view_recomend_loan($id){
        $row = $this->Loan_model->get_by_id($id);
        $payments = $this->Payement_schedules_model->get_all_by_id($row->loan_id);
        $files = $this->Loan_files_model->get_by_loans($row->loan_id);
//        $scores = $this->Loan_recommendation_model->get_by_loan($row->loan_id);

        if($row->customer_type=='group'){
            $group = $this->Groups_model->get_by_id($row->loan_customer);

            $customer_name = $group->group_name.'('.$group->group_code.')';
            $preview_url = "Customer_groups/members/";
        }elseif($row->customer_type=='individual'){
            $indi = $this->Individual_customers_model->get_by_id($row->loan_customer);
            $customer_name = $indi->Firstname.' '.$indi->Lastname;
            $preview_url = "Individual_customers/view/";
        }

        $data = array(
            'loan_id' => $row->loan_id,
            'loan_number' => $row->loan_number,
            'loan_product' => $row->product_name,
            'customer_type' => $row->customer_type,
            'loan_customer' => $customer_name,
            'preview_url' => $preview_url,
            'customer_id' => $row->loan_customer,
            'loan_date' => $row->loan_date,
            'loan_principal' => $row->loan_principal,
            'loan_period' => $row->loan_period,
            'period_type' => $row->period_type,
            'loan_interest' => $row->loan_interest,
            'loan_interest_amount' => $row->loan_interest_amount,
            'loan_amount_total' => $row->loan_amount_total,
            'loan_amount_term' => $row->loan_amount_term,
            'next_payment_id' => $row->next_payment_id,
            'loan_added_by' => $row->loan_added_by,
            'loan_approved_by' => $row->loan_approved_by,
            'loan_status' => $row->loan_status,
            'loan_added_date' => $row->loan_added_date,
            'payments'=>$payments,
            'files'=>$files,
            'currency'=>$row->currency,
            'processing_fee'=>$row->processing_fee,

        );
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/view_recomend_close',$data);
        $this->load->view('admin/footer');
    }







    function edit_single_loan_request($id){
        $row = $this->Loan_model->get_by_id($id);

        // Check if loan exists
        if(!$row) {
            $this->toaster->error('Loan not found');
            redirect('loan/track');
            return;
        }

        // Prevent editing of CLOSED or WRITTEN_OFF loans
        if($row->loan_status == 'CLOSED' || $row->loan_status == 'WRITTEN_OFF') {
            $this->toaster->error('Cannot edit a ' . strtolower(str_replace('_', ' ', $row->loan_status)) . ' loan');
            redirect('loan/track');
            return;
        }

        $payments = $this->Payement_schedules_model->get_all_by_id($row->loan_id);
        $bank_statements = $this->db->where('loan_id', $row->loan_id)->get('bank_statements')->result();
//        $files = $this->Loan_files_model->get_by_loans($row->loan_id);
//  $scores = $this->Loan_recommendation_model->get_by_loan($row->loan_id);

        if($row->customer_type=='group'){
            $group = $this->Groups_model->get_by_id($row->loan_customer);

            $customer_name = $group->group_name.'('.$group->group_code.')';
            $preview_url = "Customer_groups/members/";
            $view = "edit_loan_group";
        }elseif($row->customer_type=='individual'){
            $indi = $this->Individual_customers_model->get_by_id($row->loan_customer);
            $customer_name = $indi->Firstname.' '.$indi->Lastname;
            $preview_url = "Individual_customers/view/";
            $view = "edit_loan";
        }else{
            $institution = $this->Corporate_customers_model->get_by_id($row->loan_customer);

            $customer_name = $institution->EntityName.'('.$institution->RegistrationNumber.')';
            $preview_url = "Corporate_customers/view/";
            $view = "edit_loan_corporate";
        }
        $customers =$this->Individual_customers_model->get_all_active();
        $data = array(
            'loan_id' => $row->loan_id,
            'loan_number' => $row->loan_number,
            'loan_product' => $row->product_name,
            'loan_product_id' => $row->loan_product,
            'customer_type' => $row->customer_type,
            'loan_customer' => $customer_name,
            'preview_url' => $preview_url,
            'customer_id' => $row->loan_customer,
            'loan_date' => $row->loan_date,
            'loan_principal' => $row->loan_principal,
            'loan_period' => $row->loan_period,
            'period_type' => $row->period_type,
            'loan_interest' => $row->loan_interest,
            'loan_interest_amount' => $row->loan_interest_amount,
            'loan_amount_total' => $row->loan_amount_total,
            'loan_amount_term' => $row->loan_amount_term,
            'next_payment_id' => $row->next_payment_id,
            'loan_added_by' => $row->loan_added_by,
            'loan_approved_by' => $row->loan_approved_by,
            'loan_status' => $row->loan_status,
            'loan_added_date' => $row->loan_added_date,
            'payments'=>$payments,
            'customers'=>$customers,
            'customer'=>$row->loan_customer,
            'currency'=>$row->currency,
            'processing_fee'=>$row->processing_fee,
            'off_taker'=>$row->off_taker,
            'narration'=>$row->narration,
            'calculation_type'=>isset($row->calculation_type) ? $row->calculation_type : '',
            'wht'=>isset($row->wht) ? $row->wht : '',
            'chieftaincy'=>isset($row->chieftaincy) ? $row->chieftaincy : '',
            'crb_search'=>isset($row->crb_search) ? $row->crb_search : '',
            'pacra_search'=>isset($row->pacra_search) ? $row->pacra_search : '',
            'previous_facilities'=>isset($row->previous_facilities) ? $row->previous_facilities : '',
            'past_loans_comment'=>isset($row->past_loans_comment) ? $row->past_loans_comment : '',
            'security_notes'=>isset($row->security_notes) ? $row->security_notes : '',
            'bank_statement_notes'=>isset($row->bank_statement_notes) ? $row->bank_statement_notes : '',
            'about_transaction'=>isset($row->about_transaction) ? $row->about_transaction : '',
            'risk_analysis'=>isset($row->risk_analysis) ? $row->risk_analysis : '',
            'bank_statements' => $bank_statements,

        );
        $menu_toggle['toggles'] = 23;

        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/'.$view,$data);
        $this->load->view('admin/footer');
    }

    function edit_recommend(){
        $menu_toggle['toggles'] = 23;

        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/edit_recommend');
        $this->load->view('admin/footer');
    }
    function edit_approve(){
        $menu_toggle['toggles'] = 23;

        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/edit_approve');
        $this->load->view('admin/footer');
    }

	function loan_application(){
		$menu_toggle['toggles'] = 23;
		$data['customers'] =$this->Individual_customers_model->get_all_active();
		$this->load->view('admin/header', $menu_toggle);
		$this->load->view('loan/loan_application', $data);
		$this->load->view('admin/footer');
	}
    function create_act_edit(){
        $row = get_by_id('approval_edits','approval_edits_id',$this->session->userdata('loan_data'));
        $data_new = json_decode($row->new_info);
        $this->Loan_model->add_loan_edit($row->id,$data_new->loan_number,$data_new->loan_principal, $data_new->loan_period, $data_new->loan_interest, $data_new->sy_loan_product, $data_new->loan_date,$data_new->sy_loan_customer,$data_new->customer_type,$data_new->loan_worthness_file,$data_new->narration,$data_new->sy_added_by);
        $this->toaster->success('Success, loan edit was authorised  pending authorisation');
        redirect('loan/track');


    }
    public function edit_action(){
        $this->load->database();
        $this->db->trans_start();

        try {
            $loan_id = $this->input->post('loan_id');
            $row = $this->Loan_model->get_by_id($loan_id);

            if (!$row) {
                throw new Exception('Loan not found');
            }

            // Always use the DB loan number so the user always sees the same number
            $original_loan_number = $row->loan_number;

            // For fields only present in the individual form, fall back to the
            // original loan values so the group form (which omits them) is safe.
            $amount          = $this->input->post('amount')          !== FALSE ? $this->input->post('amount')          : $row->loan_principal;
            $months          = $this->input->post('months')          !== FALSE ? $this->input->post('months')          : $row->loan_period;
            $interest        = $this->input->post('interest')        !== FALSE ? $this->input->post('interest')        : $row->loan_interest;
            $loan_type       = $this->input->post('loan_type')       !== FALSE ? $this->input->post('loan_type')       : $row->loan_product;
            $loan_date       = $this->input->post('loan_date')       !== FALSE ? $this->input->post('loan_date')       : $row->loan_date;
            $customer        = $this->input->post('customer')        !== FALSE ? $this->input->post('customer')        : $row->loan_customer;
            $customer_type   = $this->input->post('customer_type')   !== FALSE ? $this->input->post('customer_type')   : $row->customer_type;
            $narration       = $this->input->post('narration')       !== FALSE ? $this->input->post('narration')       : $row->narration;
            $currency        = $this->input->post('currency')        !== FALSE ? $this->input->post('currency')        : $row->currency;
            $off_taker       = $this->input->post('off_taker')       !== FALSE ? $this->input->post('off_taker')       : $row->off_taker;
            $processing_fee  = $this->input->post('processing_fee')  !== FALSE ? $this->input->post('processing_fee')  : $row->processing_fee;
            $appraisal_data  = array(
                'crb_search'           => $this->input->post('crb_search')           !== FALSE ? $this->input->post('crb_search')           : $row->crb_search,
                'pacra_search'         => $this->input->post('pacra_search')         !== FALSE ? $this->input->post('pacra_search')         : $row->pacra_search,
                'previous_facilities'  => $this->input->post('previous_facilities')  !== FALSE ? $this->input->post('previous_facilities')  : $row->previous_facilities,
                'past_loans_comment'   => $this->input->post('past_loans_comment')   !== FALSE ? $this->input->post('past_loans_comment')   : $row->past_loans_comment,
                'security_notes'       => $this->input->post('security_notes')       !== FALSE ? $this->input->post('security_notes')       : $row->security_notes,
                'bank_statement_notes' => $this->input->post('bank_statement_notes') !== FALSE ? $this->input->post('bank_statement_notes') : $row->bank_statement_notes,
                'about_transaction'    => $this->input->post('about_transaction')    !== FALSE ? $this->input->post('about_transaction')    : $row->about_transaction,
                'risk_analysis'        => $this->input->post('risk_analysis')        !== FALSE ? $this->input->post('risk_analysis')        : $row->risk_analysis,
            );

            // Remove old account and schedules before recreating
            $this->db->where('account_number', $original_loan_number)->delete('account');
            $this->db->where('loan_id', $loan_id)->delete('payement_schedules');

            // Create the new loan — this recalculates all schedules
            $result = $this->Loan_model->add_loan(
                $original_loan_number,
                $amount,
                $months,
                $interest,
                $loan_type,
                $loan_date,
                $customer,
                $customer_type,
                $row->worthness_file,
                $narration,
                $this->session->userdata('user_id'),
                '',
                '',
                $currency,
                $off_taker,
                $processing_fee,
                $appraisal_data
            );

            if (!$result || !isset($result['loan_id'])) {
                throw new Exception('Failed to create updated loan');
            }

            // add_loan always auto-generates a new loan number — restore the original
            $this->db->where('loan_id', $result['loan_id'])->update('loan', array('loan_number' => $original_loan_number));
            $this->db->where('account_number', $result['loan_number'])->update('account', array('account_number' => $original_loan_number));

            // Preserve the original loan status (e.g. INITIATED, SENT_BACK)
            $this->db->where('loan_id', $result['loan_id'])->update('loan', array('loan_status' => $row->loan_status));

            // Migrate existing files and folders to the new loan record
            $this->db->where('loan_id', $loan_id)->update('loan_files', array('loan_id' => $result['loan_id']));
            $this->db->where('owner_id', $loan_id)->update('file_folders', array('owner_id' => $result['loan_id']));

            // Replace bank statements: delete old ones, then insert from form
            $this->db->where('loan_id', $loan_id)->delete('bank_statements');
            $credits = $this->input->post('personal_credit');
            $debits  = $this->input->post('personal_debit');
            $months  = $this->input->post('personal_statement_month');
            if (is_array($credits) && is_array($debits) && is_array($months)) {
                for ($i = 0; $i < count($credits); $i++) {
                    $credit = isset($credits[$i]) ? $credits[$i] : null;
                    $debit  = isset($debits[$i])  ? $debits[$i]  : null;
                    $month  = isset($months[$i])  ? $months[$i]  : null;
                    if (empty($credit) && empty($debit) && empty($month)) continue;
                    $this->db->insert('bank_statements', array(
                        'loan_id'        => $result['loan_id'],
                        'statement_type' => 'personal',
                        'credit'         => $credit ? str_replace(',', '', $credit) : 0,
                        'debit'          => $debit  ? str_replace(',', '', $debit)  : 0,
                        'month'          => $month,
                        'year'           => date('Y'),
                        'added_by'       => $this->session->userdata('user_id'),
                        'date_added'     => date('Y-m-d H:i:s'),
                    ));
                }
            }

            // Delete the old loan record
            $this->Loan_model->delete($loan_id);

            $logger = array(
                'type'         => 'Loan Edit',
                'old_info'     => json_encode($row),
                'new_info'     => json_encode($result),
                'id'           => $result['loan_id'],
                'summary'      => 'Edited loan ' . $original_loan_number,
                'Initiated_by' => $this->session->userdata('user_id'),
            );
            auth_logger($logger);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Transaction failed');
            }

            $this->toaster->success('Loan updated successfully. Payment schedule recalculated.');
            redirect('loan/track');

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->toaster->error('Failed to update loan: ' . $e->getMessage());
            redirect('loan/edit_single_loan_request/' . $this->input->post('loan_id'));
        }
    }

    public function  edit_corporate_action(){
        // Load database library for transaction support
        $this->load->database();
        
        // Start database transaction
        $this->db->trans_start();
        
        try {
            // Get the original loan data
            $loan_id = $this->input->post('loan_id');
            $original_loan_number = $this->input->post('original_loan_number');
            $row = $this->Loan_model->get_by_id($loan_id);
            
            if (!$row) {
                throw new Exception('Loan not found');
            }
            
            // Prepare data for new loan (based on create_act implementation)
            $loan_number = str_replace(' ', '', $original_loan_number); // Keep same loan number
            $amount = $this->input->post('amount');
            $months = $this->input->post('months');
            $interest = $this->input->post('interest');
            $loan_type = $this->input->post('loan_type');
            $loan_date = $this->input->post('loan_date');
            $customer = $this->input->post('customer');
            $customer_type = $this->input->post('customer_type');
            $narration = $this->input->post('narration');
            $currency = $this->input->post('currency');
            $off_taker = $this->input->post('off_taker');
            $processing_fee = $this->input->post('processing_fee');
            $appraisal_data = array(
                'crb_search'          => $this->input->post('crb_search'),
                'pacra_search'        => $this->input->post('pacra_search'),
                'previous_facilities' => $this->input->post('previous_facilities'),
                'past_loans_comment'  => $this->input->post('past_loans_comment'),
                'security_notes'      => $this->input->post('security_notes'),
                'bank_statement_notes'=> $this->input->post('bank_statement_notes'),
                'about_transaction'   => $this->input->post('about_transaction'),
                'risk_analysis'       => $this->input->post('risk_analysis'),
            );
            
            // Delete associated records before deleting the loan to avoid duplicate key errors
            // Delete account record if it exists
            $this->db->where('account_number', $loan_number);
            $this->db->delete('account');
            
            // Delete payment schedules
            $this->db->where('loan_id', $loan_id);
            $this->db->delete('payement_schedules');
            
            // Create new loan with same loan number BEFORE deleting old loan
            $result = $this->Loan_model->add_loan(
                $loan_number,
                $amount,
                $months,
                $interest,
                $loan_type,
                $loan_date,
                $customer,
                $customer_type,
                '', // worthness_file
                $narration,
                $this->session->userdata('user_id'),
                '', // payment_method
                '', // fee_amount
                $currency,
                $off_taker,
                $processing_fee,
                $appraisal_data
            );
            
            if (!$result) {
                throw new Exception('Failed to create new loan');
            }

            // Restore the original loan number (add_loan always generates a new one)
            $this->db->where('loan_id', $result['loan_id'])->update('loan', array('loan_number' => $loan_number));
            $this->db->where('account_number', $result['loan_number'])->update('account', array('account_number' => $loan_number));
            $result['loan_number'] = $loan_number;

            // Update existing loan files to reference the new loan_id
            $this->db->where('loan_id', $loan_id);
            $this->db->update('loan_files', array('loan_id' => $result['loan_id']));

            // Update existing loan folders to reference the new loan_id
            $this->db->where('owner_id', $loan_id);
            $this->db->update('file_folders', array('owner_id' => $result['loan_id']));

            // Finally delete the old loan
            $this->Loan_model->delete($loan_id);
            
            // Handle file uploads if any
//            $number_of_files_uploaded = count($_FILES['corporate_loan_files']['name']);
//            if ($number_of_files_uploaded > 0 && $_FILES['corporate_loan_files']['name'][0] != '') {
//                $this->load->library('upload');
//
//                // Create directory if it doesn't exist
//                $imagePath = APPPATH . '../uploads/' . $result['loan_number'];
//                if (!is_dir($imagePath)) {
//                    mkdir($imagePath, 0777, true);
//                }
//
//                for ($i = 0; $i < $number_of_files_uploaded; $i++) {
//                    if ($_FILES['corporate_loan_files']['name'][$i] != '') {
//                        $_FILES['userfile']['name'] = $_FILES['corporate_loan_files']['name'][$i];
//                        $_FILES['userfile']['type'] = $_FILES['corporate_loan_files']['type'][$i];
//                        $_FILES['userfile']['tmp_name'] = $_FILES['corporate_loan_files']['tmp_name'][$i];
//                        $_FILES['userfile']['error'] = $_FILES['corporate_loan_files']['error'][$i];
//                        $_FILES['userfile']['size'] = $_FILES['corporate_loan_files']['size'][$i];
//
//                        $config = array(
//                            'file_name' => $_FILES['userfile']['name'],
//                            'allowed_types' => '*',
//                            'max_size' => 200000,
//                            'overwrite' => FALSE,
//                            'upload_path' => $imagePath
//                        );
//
//                        $this->upload->initialize($config);
//
//                        if ($this->upload->do_upload()) {
//                            $uploaded_data = $this->upload->data();
//                            $file_data = array(
//                                'loan_id' => $result['loan_id'],
//                                'file_name' => $uploaded_data['file_name'],
//                                'real_file' => $config['file_name'],
//                            );
//                            $this->Loan_files_model->insert($file_data);
//                        }
//                    }
//                }
//            }
//
//            // Handle collateral files if any
//            $conames = $this->input->post('coname');
//            $types = $this->input->post('type');
//            $serials = $this->input->post('serial');
//            $cvalues = $this->input->post('cvalue');
//            $descs = $this->input->post('desc');
//
//            if (!empty($conames)) {
//                for ($i = 0; $i < count($conames); $i++) {
//                    if (!empty($conames[$i])) {
//                        // Handle collateral data insertion here if you have a collateral model
//                        // This would need to be implemented based on your collateral table structure
//                    }
//                }
//            }
//
            // Add reference to original loan
            $edit_reference = array(
                'original_loan_id' => $loan_id,
                'new_loan_id' => $result['loan_id'],
                'edit_date' => date('Y-m-d H:i:s'),
                'edited_by' => $this->session->userdata('user_id'),
                'edit_reason' => 'Corporate loan update'
            );
            
            // Log the edit operation
            $logger = array(
                'type' => 'Corporate Loan Edit',
                'old_info' => json_encode($row),
                'new_info' => json_encode($result),
                'id' => $result['loan_id'],
                'summary' => 'Edited loan ' . $loan_number,
                'Initiated_by' => $this->session->userdata('user_id')
            );
            auth_logger($logger);
            
            // Complete transaction
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Transaction failed');
            }
            
            $this->toaster->success('Loan updated successfully. Previous loan archived and new loan created.');
            redirect('loan/track');
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->trans_rollback();
            $this->toaster->error('Failed to update loan: ' . $e->getMessage());
            redirect('loan/edit_single_loan_request/' . $loan_id);
        }
    }
    
    public function delete_loan_action($loan_id){
        // Load database library for transaction support
        $this->load->database();
        
        // Start database transaction
        $this->db->trans_start();
        
        try {
            // Get the loan data for logging before deletion
            $row = $this->Loan_model->get_by_id($loan_id);
            
            if (!$row) {
                throw new Exception('Loan not found');
            }
            
            // Check if loan can be deleted (only INITIATED or REJECTED)
            if ($row->loan_status != 'INITIATED' && $row->loan_status != 'REJECTED') {
                throw new Exception('Only loans with status INITIATED or REJECTED can be deleted');
            }
            
            // Get customer information for logging
            if($row->customer_type=='group'){
                $group = $this->Groups_model->get_by_id($row->loan_customer);
                $customer_name = $group->group_name.'('.$group->group_code.')';
            }elseif($row->customer_type=='individual'){
                $indi = $this->Individual_customers_model->get_by_id($row->loan_customer);
                $customer_name = $indi->Firstname.' '.$indi->Lastname;
            }else{
                $institution = $this->Corporate_customers_model->get_by_id($row->loan_customer);
                $customer_name = $institution->EntityName.'('.$institution->RegistrationNumber.')';
            }
            
            // Prepare data for logging
            $loan_data_for_log = array(
                'loan_id' => $row->loan_id,
                'loan_number' => $row->loan_number,
                'loan_product' => $row->product_name,
                'loan_customer' => $customer_name,
                'customer_type' => $row->customer_type,
                'customer_id' => $row->loan_customer,
                'loan_date' => $row->loan_date,
                'loan_principal' => $row->loan_principal,
                'loan_period' => $row->loan_period,
                'period_type' => $row->period_type,
                'loan_interest' => $row->loan_interest,
                'loan_interest_amount' => $row->loan_interest_amount,
                'loan_amount_total' => $row->loan_amount_total,
                'loan_status' => $row->loan_status,
                'loan_added_date' => $row->loan_added_date,
                'currency' => $row->currency,
                'processing_fee' => $row->processing_fee
            );
            
            // Log the deletion operation BEFORE deleting
            $logger = array(
                'type' => 'Loan Deletion',
                'old_info' => json_encode($loan_data_for_log),
                'new_info' => 'DELETED',
                'id' => $loan_id,
                'summary' => 'Deleted loan ' . $row->loan_number . ' for customer ' . $customer_name,
                'Initiated_by' => $this->session->userdata('user_id')
            );
            auth_logger($logger);
            
            // Delete associated records in order
            // 1. Delete account record if it exists
            $this->db->where('account_number', $row->loan_number);
            $this->db->delete('account');
            
            // 2. Delete payment schedules
            $this->db->where('loan_id', $loan_id);
            $this->db->delete('payement_schedules');
            
            // 3. Delete loan files
            $this->db->where('loan_id', $loan_id);
            $this->db->delete('loan_files');
            
            // 4. Delete loan folders
            $this->db->where('owner_id', $loan_id);
            $this->db->delete('file_folders');
            
            // Delete physical files from server if folder exists
            $upload_path = APPPATH . '../uploads/' . $row->loan_number;
            if (is_dir($upload_path)) {
                // Delete all files in the directory
                $files = glob($upload_path . '/*');
                foreach($files as $file) {
                    if(is_file($file)) {
                        unlink($file);
                    }
                }
                // Remove the directory
                rmdir($upload_path);
            }
            
            // 5. Finally delete the loan
            $this->Loan_model->delete($loan_id);
            
            // Complete transaction
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Transaction failed');
            }
            
            $this->toaster->success('Loan deleted successfully. All associated records and files have been removed.');
            redirect('loan/track');
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->trans_rollback();
            $this->toaster->error('Failed to delete loan: ' . $e->getMessage());
            redirect('loan/track');
        }
    }
    
    function edit_single_loan_recommend($id){
        $row = $this->Loan_model->get_by_id_recommend($id);
        $payments = $this->Payement_schedules_model->get_all_by_id($row->loan_id);
        $files = $this->Loan_files_model->get_by_loans($row->loan_id);
//  $scores = $this->Loan_recommendation_model->get_by_loan($row->loan_id);

        if($row->customer_type=='group'){
            $group = $this->Groups_model->get_by_id($row->loan_customer);

            $customer_name = $group->group_name.'('.$group->group_code.')';
            $preview_url = "Customer_groups/members/";
        }elseif($row->customer_type=='individual'){
            $indi = $this->Individual_customers_model->get_by_id($row->loan_customer);
            $customer_name = $indi->Firstname.' '.$indi->Lastname;
            $preview_url = "Individual_customers/view/";
        }

        $data = array(
            'loan_id' => $row->loan_id,
            'loan_number' => $row->loan_number,
            'loan_product' => $row->product_name,
            'customer_type' => $row->customer_type,
            'loan_customer' => $customer_name,
            'preview_url' => $preview_url,
            'customer_id' => $row->loan_customer,
            'loan_date' => $row->loan_date,
            'loan_principal' => $row->loan_principal,
            'loan_period' => $row->loan_period,
            'period_type' => $row->period_type,
            'loan_interest' => $row->loan_interest,
            'loan_interest_amount' => $row->loan_interest_amount,
            'loan_amount_total' => $row->loan_amount_total,
            'loan_amount_term' => $row->loan_amount_term,
            'next_payment_id' => $row->next_payment_id,
            'loan_added_by' => $row->loan_added_by,
            'loan_approved_by' => $row->loan_approved_by,
            'loan_status' => $row->loan_status,
            'loan_added_date' => $row->loan_added_date,
            'new_loan_number' => $row->new_loan_number,
            'reason_for_editing' => $row->reason_for_editing,
            'payments'=>$payments,
            'files'=>$files,
            'currency'=>$row->currency

        );
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/edit_single_loan_recommend',$data);
        $this->load->view('admin/footer');
    }





    function view_initiate_loan($id){
        $row = $this->Loan_model->get_by_id($id);
        $payments = $this->Payement_schedules_model->get_all_by_id($row->loan_id);
        $files = $this->Loan_files_model->get_by_loans($row->loan_id);
//        $scores = $this->Loan_recommendation_model->get_by_loan($row->loan_id);

        if($row->customer_type=='group'){
            $group = $this->Groups_model->get_by_id($row->loan_customer);

            $customer_name = $group->group_name.'('.$group->group_code.')';
            $preview_url = "Customer_groups/members/";
        }elseif($row->customer_type=='individual'){
            $indi = $this->Individual_customers_model->get_by_id($row->loan_customer);
            $customer_name = $indi->Firstname.' '.$indi->Lastname;
            $preview_url = "Individual_customers/view/";
        }

        $data = array(
            'loan_id' => $row->loan_id,
            'loan_number' => $row->loan_number,
            'loan_product' => $row->product_name,
            'customer_type' => $row->customer_type,
            'loan_customer' => $customer_name,
            'preview_url' => $preview_url,
            'customer_id' => $row->loan_customer,
            'loan_date' => $row->loan_date,
            'loan_principal' => $row->loan_principal,
            'loan_period' => $row->loan_period,
            'period_type' => $row->period_type,
            'loan_interest' => $row->loan_interest,
            'loan_interest_amount' => $row->loan_interest_amount,
            'loan_amount_total' => $row->loan_amount_total,
            'loan_amount_term' => $row->loan_amount_term,
            'next_payment_id' => $row->next_payment_id,
            'loan_added_by' => $row->loan_added_by,
            'loan_approved_by' => $row->loan_approved_by,
            'loan_status' => $row->loan_status,
            'loan_added_date' => $row->loan_added_date,
            'payments'=>$payments,
            'files'=>$files,
            'currency'=>$row->currency,
            'processing_fee'=>$row->processing_fee,

        );
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/view_initiate_close',$data);
        $this->load->view('admin/footer');
    }


    function view_approved_loan($id){
        $row = $this->Loan_model->get_by_id($id);
        $payments = $this->Payement_schedules_model->get_all_by_id($row->loan_id);
        $files = $this->Loan_files_model->get_by_loans($row->loan_id);
//        $scores = $this->Loan_recommendation_model->get_by_loan($row->loan_id);

        if($row->customer_type=='group'){
            $group = $this->Groups_model->get_by_id($row->loan_customer);

            $customer_name = $group->group_name.'('.$group->group_code.')';
            $preview_url = "Customer_groups/members/";
        }elseif($row->customer_type=='individual'){
            $indi = $this->Individual_customers_model->get_by_id($row->loan_customer);
            $customer_name = $indi->Firstname.' '.$indi->Lastname;
            $preview_url = "Individual_customers/view/";
        }

        $data = array(
            'loan_id' => $row->loan_id,
            'loan_number' => $row->loan_number,
            'loan_product' => $row->product_name,
            'customer_type' => $row->customer_type,
            'loan_customer' => $customer_name,
            'preview_url' => $preview_url,
            'customer_id' => $row->loan_customer,
            'loan_date' => $row->loan_date,
            'loan_principal' => $row->loan_principal,
            'loan_period' => $row->loan_period,
            'period_type' => $row->period_type,
            'loan_interest' => $row->loan_interest,
            'loan_interest_amount' => $row->loan_interest_amount,
            'loan_amount_total' => $row->loan_amount_total,
            'loan_amount_term' => $row->loan_amount_term,
            'next_payment_id' => $row->next_payment_id,
            'loan_added_by' => $row->loan_added_by,
            'loan_approved_by' => $row->loan_approved_by,
            'loan_status' => $row->loan_status,
            'loan_added_date' => $row->loan_added_date,
            'payments'=>$payments,
            'files'=>$files,
            'currency'=>$row->currency,

        );
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/view_approved_close',$data);
        $this->load->view('admin/footer');
    }

    function repayment_view($id){
        $row = $this->Loan_model->get_by_id($id);
        $payments = $this->Payement_schedules_model->get_all_by_id($row->loan_id);

        if($row->customer_type=='group'){
            $group = $this->Groups_model->get_by_id($row->loan_customer);

            $customer_name = $group->group_name.'('.$group->group_code.')';
            $preview_url = "Customer_groups/members/";
        }elseif($row->customer_type=='individual'){
            $indi = $this->Individual_customers_model->get_by_id($row->loan_customer);
            $customer_name = $indi->Firstname.' '.$indi->Lastname;
            $preview_url = "Individual_customers/view/";
        }elseif($row->customer_type=='institution'){
            $inst = get_by_id('corporate_customers','id',$row->loan_customer);
            $customer_name = $inst->EntityName.' - '.$inst->RegistrationNumber.' ('.$inst->	entity_type.')';
            $preview_url = "Corporate_customers/read/";
        }
		$acrued = array();
		if($row->calculation_type=='Bullet Payment'){
			$acrued = $this->calculate_payoff_inline($row->loan_id);
		}
        $data = array(
            'loan_id' => $row->loan_id,
            'loan_number' => $row->loan_number,
            'loan_product' => $row->product_name,
            'customer_type' => $row->customer_type,
            'loan_customer' => $customer_name,
            'preview_url' => $preview_url,
            'customer_id' => $row->loan_customer,
            'loan_date' => $row->loan_date,
            'loan_principal' => $row->loan_principal,
            'loan_period' => $row->loan_period,
            'period_type' => $row->period_type,
            'loan_interest' => $row->loan_interest,
            'loan_interest_amount' => $row->loan_interest_amount,

            'loan_amount_total' => $row->loan_amount_total,
            'loan_amount_term' => $row->loan_amount_term,
            'next_payment_id' => $row->next_payment_id,
            'next_payment_id_rescheduled' => $row->next_payment_id_rescheduled,
            'loan_added_by' => $row->loan_added_by,
            'loan_approved_by' => $row->loan_approved_by,
            'loan_status' => $row->loan_status,
            'loan_added_date' => $row->loan_added_date,
            'payments'=>$payments,
            'currency'=>$row->currency,
            'processing_fee'=>$row->processing_fee,
            'calculation_type'=>$row->calculation_type,
            'acrued'=>$acrued,
        );
        $menu_toggle['toggles'] = 52;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/loan_repayment_view',$data);
        $this->load->view('admin/footer');
    }



    function report_client_summary($id){
        $row = $this->Loan_model->get_by_id($id);
        $payments = $this->Payement_schedules_model->get_all_by_id($row->loan_id);
        $maturity_date = $this->Payement_schedules_model->get_last_payment($row->loan_id);
        $first_payment = $this->Payement_schedules_model->get_first_payment($row->loan_id);

        if( $row->customer_type=='individual'){
            $row = $this->Loan_model->get_by_id_report($id);

            $data = array(
                'loan_id' => $row->loan_id,
                'maturity_date' => $maturity_date->payment_schedule,
                'maturity_pay' => $maturity_date->amount,
                'first_payment' => $first_payment->amount,
                'first_payment_date' => $first_payment->payment_schedule,
                'loan_number' => $row->loan_number,
                'loan_product' => $row->product_name,
                'loan_customer' => $row->Firstname." ".$row->Lastname,
                'customer_id' => $row->id,
                'loan_date' => $row->loan_date,
                'loan_principal' => $row->loan_principal,
                'loan_period' => $row->loan_period,
                'period_type' => $row->period_type,
                'loan_interest' => $row->loan_interest,
                'loan_amount_total' => $row->loan_amount_total,
                'loan_amount_term' => $row->loan_amount_term,
                'next_payment_id' => $row->next_payment_id,
                'loan_added_by' => $row->loan_added_by,
                'loan_approved_by' => $row->loan_approved_by,
                'loan_status' => $row->loan_status,
                'loan_added_date' => $row->loan_added_date,
                'payments'=>$payments,
                'currency'=>$row->currency,
            );
            $this->load->library('Pdf');
            $html = $this->load->view('loan/loan_report_client_summary_pdf', $data,true);
            $this->pdf->createPDF($html, $data['loan_customer']." Client summary as on".date('Y-m-d'), true);


        }
        else{

            $row = $this->Loan_model->get_by_id_group($id);

            $data = array(
                'loan_id' => $row->loan_id,
                'maturity_date' => $maturity_date->payment_schedule,
                'maturity_pay' => $maturity_date->amount,
                'first_payment' => $first_payment->amount,
                'first_payment_date' => $first_payment->payment_schedule,
                'loan_number' => $row->loan_number,
                'loan_product' => $row->product_name,
                'loan_customer' => $row->EntityName,
                'customer_id' => $row->id,
                'loan_date' => $row->loan_date,
                'loan_principal' => $row->loan_principal,
                'loan_period' => $row->loan_period,
                'period_type' => $row->period_type,
                'loan_interest' => $row->loan_interest,
                'loan_amount_total' => $row->loan_amount_total,
                'loan_amount_term' => $row->loan_amount_term,
                'next_payment_id' => $row->next_payment_id,
                'loan_added_by' => $row->loan_added_by,
                'loan_approved_by' => $row->loan_approved_by,
                'loan_status' => $row->loan_status,
                'loan_added_date' => $row->loan_added_date,
                'payments'=>$payments,
                'currency'=>$row->currency,
                'processing_fee'=>$row->processing_fee,
            );
            $this->load->library('Pdf');
            $html = $this->load->view('loan/report', $data,true);
            $this->pdf->createPDF($html, $data['loan_customer']." loan report as on".date('Y-m-d'), true);
        }

    }

    function report($id){
        $row = $this->Loan_model->get_by_id($id);
        $payments = $this->Payement_schedules_model->get_all_by_id($row->loan_id);
        $maturity_date = $this->Payement_schedules_model->get_last_payment($row->loan_id);
        $first_payment = $this->Payement_schedules_model->get_first_payment($row->loan_id);

        // Calculate accrued amount if it's a Bullet Payment
        $acrued = array();
        if($row->calculation_type == 'Bullet Payment'){
            $acrued = $this->calculate_payoff_inline($row->loan_id);
        }

        // Schedule totals for amortisation tfoot and closed-loan header summary
        $total_schedule_interest  = 0;
        $total_schedule_principal = 0;
        $total_schedule_amount    = 0;
        $total_paid_interest      = 0;
        foreach ($payments as $p) {
            $total_schedule_interest  += floatval($p->interest  ?? 0);
            $total_schedule_principal += floatval($p->principal ?? 0);
            $total_schedule_amount    += floatval($p->amount    ?? 0);
        }
        if ($row->loan_status == 'CLOSED') {
            foreach ($payments as $p) {
                if ($p->status == 'PAID') {
                    if ($row->calculation_type == 'Bullet Payment') {
                        $total_paid_interest += max(0, floatval($p->paid_amount ?? 0) - floatval($p->principal ?? 0));
                    } else {
                        $total_paid_interest += floatval($p->interest ?? 0);
                    }
                }
            }
            if ($total_paid_interest == 0) {
                $total_paid_interest = $total_schedule_interest;
            }
        }

        // --- Force-close detection and settlement calculations for report ---
        $is_force_closed       = false;
        $fcl_amount_paid       = 0;
        $fcl_payment_date      = null;
        $accrued_at_settlement = 0;
        if ($row->loan_status == 'CLOSED') {
            foreach ($payments as $p) {
                if ($p->status == 'PAID' && (float)($p->paid_amount ?? 0) == 0) {
                    $is_force_closed = true;
                    break;
                }
            }
        }
        if ($is_force_closed) {
            $fcl_rows = $this->db->where('loan_id', $row->loan_id)->where('transaction_type', 5)->get('transactions')->result();
            foreach ($fcl_rows as $ft) {
                $fcl_amount_paid += (float)$ft->amount;
                if (!$fcl_payment_date) $fcl_payment_date = $ft->date_stamp;
            }
            if ($row->calculation_type == 'Bullet Payment' && $fcl_payment_date) {
                // Bullet: daily/monthly accrual formula matching _compute_bullet_payoff
                $mr   = (float)$row->loan_interest / 100;
                $term = (int)$row->loan_period;
                $pr   = (float)$row->loan_principal;
                $ld   = new DateTime($row->loan_date);
                $pd   = new DateTime(date('Y-m-d', strtotime($fcl_payment_date)));
                $md   = clone $ld; $md->modify("+{$term} months");
                $orig_int  = $pr * $mr * $term;
                $mat_total = $pr + $orig_int;
                if ($pd <= $md) {
                    $days   = max(1, $ld->diff($pd)->days);
                    $fm     = max(1, (int)floor($days / 30)); if ($fm > $term) $fm = $term;
                    $ed     = max(0, $days - $fm * 30);
                    $accrued_at_settlement = round($pr * $mr * $fm + $pr * ($mr/30) * $ed, 2);
                    $cap = round($pr * $mr * $term, 2);
                    if ($accrued_at_settlement > $cap) $accrued_at_settlement = $cap;
                } else {
                    $dp  = $md->diff($pd)->days;
                    $mpa = (int)floor($dp / 30); $rd = $dp % 30;
                    $rb  = $mat_total;
                    for ($m = 1; $m <= $mpa; $m++) $rb += round($rb * $mr, 2);
                    if ($rd > 0) $rb += round(($rb * $mr / 30) * $rd, 2);
                    $accrued_at_settlement = round($rb - $pr, 2);
                }
            } elseif ($row->calculation_type != 'Bullet Payment' && $fcl_payment_date) {
                // Non-bullet: accrued = sum of interest from instalments due on/before settlement date
                $fcl_date_only = date('Y-m-d', strtotime($fcl_payment_date));
                foreach ($payments as $p) {
                    if ($p->payment_schedule <= $fcl_date_only) {
                        $accrued_at_settlement += (float)$p->interest;
                    }
                }
            }
        }

      if( $row->customer_type=='individual'){
      $row = $this->Loan_model->get_by_id_report($id);

        $data = array(
            'loan_id' => $row->loan_id,
            'maturity_date' => $maturity_date->payment_schedule,
            'maturity_pay' => $maturity_date->amount,
            'first_payment' => $first_payment->amount,
            'first_payment_date' => $first_payment->payment_schedule,
            'loan_number' => $row->loan_number,
            'loan_product' => $row->product_name,
            'loan_customer' => $row->Firstname." ".$row->Lastname,
            'customer_id' => $row->id,
            'loan_date' => $row->loan_date,
            'loan_principal' => $row->loan_principal,
            'loan_period' => $row->loan_period,
            'period_type' => $row->period_type,
            'loan_interest' => $row->loan_interest,
            'loan_amount_total' => $row->loan_amount_total,
            'loan_amount_term' => $row->loan_amount_term,
            'next_payment_id' => $row->next_payment_id,
            'loan_added_by' => $row->loan_added_by,
            'loan_approved_by' => $row->loan_approved_by,
            'loan_status' => $row->loan_status,
            'loan_added_date' => $row->loan_added_date,
            'payments'=>$payments,
            'currency'=>$row->currency,
            'processing_fee'=>$row->processing_fee,
            'calculation_type'=>$row->calculation_type,
            'acrued' => $acrued,
            'total_schedule_interest'  => $total_schedule_interest,
            'total_schedule_principal' => $total_schedule_principal,
            'total_schedule_amount'    => $total_schedule_amount,
            'total_paid_interest'      => $total_paid_interest,
            'is_force_closed'          => $is_force_closed,
            'fcl_amount_paid'          => $fcl_amount_paid,
            'fcl_payment_date'         => $fcl_payment_date,
            'accrued_at_settlement'    => $accrued_at_settlement,
        );
        $this->load->library('Pdf');
        $html = $this->load->view('loan/report', $data,true);
        $this->pdf->createPDF($html, $data['loan_customer']." loan report as on".date('Y-m-d'), true);


    }
     else{

$row = $this->Loan_model->get_by_id_group($id);
        $data = array(
            'loan_id' => $row->loan_id,
            'maturity_date' => $maturity_date->payment_schedule,
            'maturity_pay' => $maturity_date->amount,
            'first_payment' => $first_payment->amount,
            'first_payment_date' => $first_payment->payment_schedule,
            'loan_number' => $row->loan_number,
            'loan_product' => $row->product_name,
            'loan_customer' => $row->EntityName. "(".$row->	RegistrationNumber.")",
            'customer_id' => $row->id,
            'loan_date' => $row->loan_date,
            'loan_principal' => $row->loan_principal,
            'loan_period' => $row->loan_period,
            'period_type' => $row->period_type,
            'loan_interest' => $row->loan_interest,
            'loan_amount_total' => $row->loan_amount_total,
            'loan_amount_term' => $row->loan_amount_term,
            'next_payment_id' => $row->next_payment_id,
            'loan_added_by' => $row->loan_added_by,
            'loan_approved_by' => $row->loan_approved_by,
            'loan_status' => $row->loan_status,
            'loan_added_date' => $row->loan_added_date,
            'payments'=>$payments,
            'currency'=>$row->currency,
            'calculation_type'=>$row->calculation_type,
            'acrued' => $acrued,
            'total_schedule_interest'  => $total_schedule_interest,
            'total_schedule_principal' => $total_schedule_principal,
            'total_schedule_amount'    => $total_schedule_amount,
            'total_paid_interest'      => $total_paid_interest,
            'is_force_closed'          => $is_force_closed,
            'fcl_amount_paid'          => $fcl_amount_paid,
            'fcl_payment_date'         => $fcl_payment_date,
            'accrued_at_settlement'    => $accrued_at_settlement,
        );
        $this->load->library('Pdf');
        $html = $this->load->view('loan/report', $data,true);
        $this->pdf->createPDF($html, $data['loan_customer']." loan report as on".date('Y-m-d'), true);
     }

    }

    /**
     * Loan Appraisal Report
     * Displays a comprehensive appraisal report for a loan
     */
    function appraisal_report($id) {
        $loan = $this->Loan_model->get_by_id($id);
        if (!$loan) {
            $this->toaster->error('Loan not found');
            redirect('loan');
        }

        // Get loan product
        $loan_product = get_by_id('loan_products', 'loan_product_id', $loan->loan_product);

        // Get currency
        $currency = get_by_id('currencies', 'currency_id', $loan->currency);

        // Get customer based on type
        $is_corporate = false;
        if ($loan->customer_type == 'individual') {
            $customer = $this->Individual_customers_model->get_by_id($loan->loan_customer);
        } elseif ($loan->customer_type == 'institution') {
            $customer = get_by_id('corporate_customers', 'id', $loan->loan_customer);
            $is_corporate = true;
        } elseif ($loan->customer_type == 'group') {
            $customer = $this->Groups_model->get_by_id($loan->loan_customer);
        }

        // Get previous loans for this customer
        $this->db->where('loan_customer', $loan->loan_customer);
        $this->db->where('customer_type', $loan->customer_type);
        $this->db->where('loan_id !=', $loan->loan_id);
        $this->db->order_by('loan_date', 'DESC');
        $previous_loans = $this->db->get('loan')->result();

        // Get approvers
        $approvers = array();
        $this->db->select('lat.*, e.Firstname as first_name, e.Lastname as last_name');
        $this->db->from('loan_approval_trail lat');
        $this->db->join('employees e', 'e.id = lat.user_id', 'left');
        $this->db->where('lat.loan_id', $loan->loan_id);
        $this->db->where_in('lat.action', array('APPROVED', 'MULTI_APPROVE'));
        $this->db->order_by('lat.date_stamp', 'ASC');
        $approvers = $this->db->get()->result();

        // Get loan creator
        $created_by = get_by_id('employees', 'id', $loan->loan_added_by);

        // Calculate total interest and repayment
        $payments = $this->Payement_schedules_model->get_all_by_id($loan->loan_id);
        $total_interest = 0;
        $total_repayment = 0;
        if (!empty($payments)) {
            foreach ($payments as $payment) {
                $total_interest += floatval($payment->interest ?? 0);
                $total_repayment += floatval($payment->amount ?? 0);
            }
        }

        // Get company details from settings
        $settings = get_by_id('settings', 'settings_id', '1');
        $company_name = $settings ? $settings->company_name : 'Fundit Capital Solutions';
        $company_logo = $settings && !empty($settings->logo) ? 'uploads/' . $settings->logo : '';
        $company_address = $settings ? $settings->address : '';
        $company_phone = $settings ? $settings->phone_number : '';
        $company_email = $settings ? $settings->company_email : '';

        // Get off-taker for bullet loans
        $off_taker = null;
        if (!empty($loan->off_taker)) {
            $off_taker = get_by_id('corporate_customers', 'id', $loan->off_taker);
        }

        // Get shareholders for client (if corporate)
        $client_shareholders = array();
        if ($is_corporate && !empty($customer->id)) {
            $this->db->where('corporate_id', $customer->id);
            $client_shareholders = $this->db->get('shareholders')->result();
        }

        // Get shareholders for off-taker (if exists)
        $offtaker_shareholders = array();
        if (!empty($off_taker) && !empty($off_taker->id)) {
            $this->db->where('corporate_id', $off_taker->id);
            $offtaker_shareholders = $this->db->get('shareholders')->result();
        }

        $bank_statements = $this->db->where('loan_id', $loan->loan_id)->get('bank_statements')->result();

        // For individual customers, fetch KYC (ID number lives in proofofidentity, not individual_customers)
        $customer_kyc = null;
        if (!$is_corporate && $loan->customer_type == 'individual') {
            $customer_kyc = $this->db->where('ClientId', $loan->loan_customer)->get('proofofidentity')->row();
        }

        $data = array(
            'loan' => $loan,
            'loan_product' => $loan_product,
            'currency' => $currency,
            'customer' => $customer,
            'customer_kyc' => $customer_kyc,
            'is_corporate' => $is_corporate,
            'previous_loans' => $previous_loans,
            'approvers' => $approvers,
            'created_by' => $created_by,
            'total_interest' => $total_interest,
            'total_repayment' => $total_repayment,
            'company_name' => $company_name,
            'company_logo' => $company_logo,
            'company_address' => $company_address,
            'company_phone' => $company_phone,
            'company_email' => $company_email,
            'off_taker' => $off_taker,
            'client_shareholders' => $client_shareholders,
            'offtaker_shareholders' => $offtaker_shareholders,
            'bank_statements' => $bank_statements,
        );

        $this->load->view('loan/appraisal_report', $data);
    }

    function pv(){
        $this->load->view('testv');
    }
    function approval_action(){
        $action = $this->input->get('action');
        $id= $this->input->get('id');
        $customer = $this->Loan_model->loan_user($id);
        $by = 'loan_approved_by';
        $by_date = 'approved_date';
        if($action =="REJECTED"){
            $by = 'rejected_by';
            $by_date = 'rejected_date';
        }
        if($action =="WRITTEN_OFF"){
            $by = 'written_off_by';
            $by_date = 'written_off_date';
        }
        if($action =="WRITE_OFF"){
            $by = 'written_off_by';
            $by_date = 'written_off_date';
        }
        $logger = array(
            'user_id' => $this->session->userdata('user_id'),
            'activity' => $action.' '.' a loan',
            'activity_cate' => 'updating'

        );
        log_activity($logger);
        $notify = get_by_id('sms_settings','id','1');
        if($action =="ACTIVE"){
            $by = 'disbursed_by';

            $by_date = 'disbursed_date';
            $this->Loan_model->update($id,array('loan_status'=>$action,'disbursed'=>'Yes',$by=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s'), 'sent_back'=>0, 'sent_back_comment'=>null));
        }else{
            $this->Loan_model->update($id,array('loan_status'=>$action,$by=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s'), 'sent_back'=>0, 'sent_back_comment'=>null));
        }
        if($notify->loan_disbursement=='Yes' && $action =="ACTIVE"){
            send_sms($customer->PhoneNumber,'Dear customer, loan has been approved, you can call numbers below for more');
        }

        // Notify the loan creator of the status change
        $loan_row = $this->db->get_where('loan', array('loan_id' => $id))->row();
        if ($loan_row) {
            $cust_name = 'N/A';
            if ($loan_row->customer_type == 'individual') {
                $c = $this->db->get_where('individual_customers', array('id' => $loan_row->loan_customer))->row();
                if ($c) $cust_name = $c->Firstname . ' ' . $c->Lastname;
            } else {
                $c = $this->db->get_where('corporate_customers', array('id' => $loan_row->loan_customer))->row();
                if ($c) $cust_name = $c->EntityName;
            }
            $cur = $this->db->get_where('currency', array('id' => $loan_row->currency))->row();
            $cur_code = $cur ? $cur->code : 'ZMW';

            $creator_data = array(
                'loan_id' => $id,
                'loan_number' => $loan_row->loan_number,
                'customer_name' => $cust_name,
                'amount' => $loan_row->loan_principal,
                'currency' => $cur_code
            );
            notify_loan_creator($creator_data, $action, $this->session->userdata('user_id'), $loan_row->loan_added_by);
        }

        $this->toaster->success('Success, your action successful');
        redirect($_SERVER['HTTP_REFERER']);
    }

    function approval_action_with_comment(){
        $action = $this->input->post('action');
        $id = $this->input->post('loan_id');
        $comment = $this->input->post('comment');

        // Enforce role-based permission server-side.
        // Recommend requires the recommend permission; all other decisions
        // (reject / first / second / final approve) require approval rights.
        $required_perm = ($action == 'RECOMMENDED') ? 'Loan/recommend' : 'loan/unified_approval';
        if(!has_access($required_perm)){
            $this->toaster->error('You do not have permission to perform this action.');
            redirect($_SERVER['HTTP_REFERER']);
            return;
        }

        // Check for adjacent approval actions by same user
        $current_user_id = $this->session->userdata('user_id');

        // Get the last approval action for this loan
        $this->db->select('action, user_id');
        $this->db->from('loan_approval_trail');
        $this->db->where('loan_id', $id);
        $this->db->order_by('date_stamp', 'DESC');
        $this->db->limit(1);
        $last_action = $this->db->get()->row();

        // Define adjacent actions that cannot be done by same user
        // Note: INITIATED → RECOMMENDED is allowed by same user
        $adjacent_actions = array(
            'RECOMMENDED' => 'APPROVED_FIRST',
            'APPROVED_FIRST' => 'APPROVED_SECOND',
            'APPROVED_SECOND' => 'APPROVED',
            'APPROVED' => 'DISBURSED'
        );

        // Check if current user did the previous action
        if($last_action && $last_action->user_id == $current_user_id){
            // Check if current action is adjacent to previous action
            if(isset($adjacent_actions[$last_action->action]) && $adjacent_actions[$last_action->action] == $action){
                $this->toaster->error('Error: You cannot perform consecutive approval actions. The previous action was done by you.');
                redirect($_SERVER['HTTP_REFERER']);
                return;
            }
        }

        $customer = $this->Loan_model->loan_user($id);
        $by = 'loan_approved_by';
        $by_date = 'approved_date';

        // Store original action for trail
        $original_action = $action;

        if($action == "REJECT"){
            $by = 'rejected_by';
            $by_date = 'rejected_date';
            $action = "REJECTED"; // Set status to REJECTED
        }
        if($action == "RECOMMENDED"){
            $by = 'loan_recommended_by';
            $by_date = 'loan_recommended_date';
            // Status remains RECOMMENDED
        }
        if($action == "APPROVED_FIRST"){
            $by = 'loan_approved_by';
            $by_date = 'approved_date';
            // Status remains APPROVED_FIRST
        }
        if($action == "APPROVED_SECOND"){
            $by = 'loan_approved_by';
            $by_date = 'approved_date';
            // Status remains APPROVED_SECOND
        }
        if($action == "APPROVED_THIRD" || $action == "APPROVED"){
            $by = 'loan_approved_by';
            $by_date = 'approved_date';
            $action = "APPROVED"; // Final approval status
        }
        if($action == "WRITTEN_OFF"){
            $by = 'written_off_by';
            $by_date = 'written_off_date';
        }
        if($action == "WRITE_OFF"){
            $by = 'written_off_by';
            $by_date = 'written_off_date';
        }

        // Insert into loan_approval_trail (keep original action)
        $trail_data = array(
            'user_id' => $this->session->userdata('user_id'),
            'action' => $original_action,
            'comment' => $comment,
            'loan_id' => $id
        );
        $this->Loan_approval_trail_model->insert($trail_data);

        // Log activity
        $logger = array(
            'user_id' => $this->session->userdata('user_id'),
            'activity' => $original_action.' a loan with comment',
            'activity_cate' => 'updating'
        );
        log_activity($logger);

        $notify = get_by_id('sms_settings','id','1');

        // Update loan status and clear sent_back flag
        if($action == "ACTIVE"){
            $by = 'disbursed_by';
            $by_date = 'disbursed_date';
            $this->Loan_model->update($id, array(
                'loan_status' => $action,
                'disbursed' => 'Yes',
                $by => $this->session->userdata('user_id'),
                $by_date => date('Y-m-d H:i:s'),
                'sent_back' => 0,
                'sent_back_comment' => null
            ));
        } else {
            $this->Loan_model->update($id, array(
                'loan_status' => $action,
                $by => $this->session->userdata('user_id'),
                $by_date => date('Y-m-d H:i:s'),
                'sent_back' => 0,
                'sent_back_comment' => null
            ));
        }

        // Send SMS notification if applicable
        if($notify->loan_disbursement == 'Yes' && $action == "ACTIVE"){
            send_sms($customer->PhoneNumber, 'Dear customer, loan has been approved, you can call numbers below for more');
        }

        // Send email notifications based on action
        $loan = $this->db->get_where('loan', array('loan_id' => $id))->row();
        if ($loan) {
            // Get customer name
            $customer_name = 'N/A';
            if ($loan->customer_type == 'individual') {
                $cust = $this->db->get_where('individual_customers', array('id' => $loan->loan_customer))->row();
                if ($cust) {
                    $customer_name = $cust->Firstname . ' ' . $cust->Lastname;
                }
            } else {
                $cust = $this->db->get_where('corporate_customers', array('id' => $loan->loan_customer))->row();
                if ($cust) {
                    $customer_name = $cust->EntityName;
                }
            }

            // Get currency code
            $currency_data = $this->db->get_where('currency', array('id' => $loan->currency))->row();
            $currency_code = $currency_data ? $currency_data->code : 'ZMW';

            // Prepare loan data for notification
            $loan_notification_data = array(
                'loan_id' => $id,
                'loan_number' => $loan->loan_number,
                'customer_name' => $customer_name,
                'amount' => $loan->loan_principal,
                'currency' => $currency_code
            );

            // Notify based on action
            if ($original_action == 'RECOMMENDED') {
                // Notify users with access to loan/unified_approval (approvers)
                notify_loan_approvers($loan_notification_data, 'loan/unified_approval', $current_user_id);
            } elseif ($original_action == 'APPROVED' || $original_action == 'APPROVED_THIRD') {
                // Notify users with loan creation rights to upload signed client copy
                notify_loan_upload_signed($loan_notification_data, $current_user_id);
            }

            // Always notify the loan creator of the status change
            notify_loan_creator($loan_notification_data, $action, $current_user_id, $loan->loan_added_by);
        }

        $this->toaster->success('Success, your action was successful');
        redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * Send loan for disbursement after client has signed documents
     */
    function send_for_disburse($id) {
        // Enforce role-based permission server-side (disbursement rights)
        if(!has_access('loan/approved')){
            $this->toaster->error('You do not have permission to perform this action.');
            redirect($_SERVER['HTTP_REFERER']);
            return;
        }

        $loan = $this->db->get_where('loan', array('loan_id' => $id))->row();

        if (!$loan) {
            $this->toaster->error('Loan not found');
            redirect($_SERVER['HTTP_REFERER']);
            return;
        }

        // Check loan is in APPROVED status
        if ($loan->loan_status != 'APPROVED') {
            $this->toaster->error('Loan must be in APPROVED status to send for disbursement');
            redirect($_SERVER['HTTP_REFERER']);
            return;
        }

        $current_user_id = $this->session->userdata('user_id');

        // Update loan status to CLIENT_SIGNED and clear sent_back flag
        $this->Loan_model->update($id, array(
            'loan_status' => 'CLIENT_SIGNED',
            'client_signed_by' => $current_user_id,
            'client_signed_date' => date('Y-m-d H:i:s'),
            'sent_back' => 0,
            'sent_back_comment' => null
        ));

        // Insert into loan_approval_trail
        $trail_data = array(
            'user_id' => $current_user_id,
            'action' => 'CLIENT_SIGNED',
            'comment' => 'Client signed documents uploaded. Sent for disbursement.',
            'loan_id' => $id
        );
        $this->Loan_approval_trail_model->insert($trail_data);

        // Log activity
        $logger = array(
            'user_id' => $current_user_id,
            'activity' => 'Sent loan for disbursement - client signed',
            'activity_cate' => 'loan_disburse'
        );
        log_activity($logger);

        // Get customer name for notification
        $customer_name = 'N/A';
        if ($loan->customer_type == 'individual') {
            $cust = $this->db->get_where('individual_customers', array('id' => $loan->loan_customer))->row();
            if ($cust) {
                $customer_name = $cust->Firstname . ' ' . $cust->Lastname;
            }
        } else {
            $cust = $this->db->get_where('corporate_customers', array('id' => $loan->loan_customer))->row();
            if ($cust) {
                $customer_name = $cust->EntityName;
            }
        }

        // Get currency code
        $currency_data = $this->db->get_where('currency', array('id' => $loan->currency))->row();
        $currency_code = $currency_data ? $currency_data->code : 'ZMW';

        // Prepare loan data for notification
        $loan_notification_data = array(
            'loan_id' => $id,
            'loan_number' => $loan->loan_number,
            'customer_name' => $customer_name,
            'amount' => $loan->loan_principal,
            'currency' => $currency_code
        );

        // Notify users with disburse rights (loan/approved page)
        notify_loan_ready_disburse($loan_notification_data, 'loan/approved', $current_user_id);

        // Notify the loan creator that the loan has been sent for disbursement
        notify_loan_creator($loan_notification_data, 'CLIENT_SIGNED', $current_user_id, $loan->loan_added_by);

        $this->toaster->success('Loan sent for disbursement successfully');
        redirect('loan/view/' . $id);
    }

    function get_approval_trail($loan_id){
        // Fetch approval trail records for the loan
        $this->db->select('loan_approval_trail.*, employees.Firstname, employees.Lastname');
        $this->db->from('loan_approval_trail');
        $this->db->join('employees', 'employees.id = loan_approval_trail.user_id', 'left');
        $this->db->where('loan_approval_trail.loan_id', $loan_id);
        $this->db->order_by('loan_approval_trail.date_stamp', 'ASC');
        $trail = $this->db->get()->result();

        if(!empty($trail)){
            $data = array();
            foreach($trail as $record){
                $data[] = array(
                    'action' => $record->action,
                    'comment' => $record->comment,
                    'user_name' => $record->Firstname . ' ' . $record->Lastname,
                    'date_stamp' => date('d M Y, h:i A', strtotime($record->date_stamp))
                );
            }

            echo json_encode(array('status' => 'success', 'data' => $data));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'No approval trail found'));
        }
    }
    function disburse_loan(){
        $id = $this->input->post('loan_id');
        $previous_date= $this->input->post('pdate');
        $current_date = $this->input->post('cdate');
        $comment= $this->input->post('comment');

        // Enforce role-based permission server-side (disbursement rights)
        if(!has_access('loan/approved')){
            $this->toaster->error('You do not have permission to perform this action.');
            redirect($_SERVER['HTTP_REFERER']);
            return;
        }

        // Check if current user did the final approval
        $current_user_id = $this->session->userdata('user_id');
        $this->db->select('action, user_id');
        $this->db->from('loan_approval_trail');
        $this->db->where('loan_id', $id);
        $this->db->where_in('action', array('APPROVED', 'CLIENT_SIGNED'));
        $this->db->order_by('date_stamp', 'DESC');
        $this->db->limit(1);
        $final_approval = $this->db->get()->row();

        if($final_approval && $final_approval->user_id == $current_user_id){
            $this->toaster->error('Error: You cannot disburse a loan that you approved or sent for disbursement. Another user must perform the disbursement.');
            redirect($_SERVER['HTTP_REFERER']);
            return;
        }

        $customer = $this->Loan_model->loan_user($id);
        $notify = get_by_id('sms_settings','id','1');

        $by = 'disbursed_by';

        $by_date = 'disbursed_date';

        if($current_date !=""){
            $r  = $this->Loan_model->restructure($id,$current_date);
            $this->Loan_model->update($id,array('loan_status'=>'ACTIVE','disbursed'=>'Yes',$by=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s'), 'sent_back'=>0, 'sent_back_comment'=>null));

        }else{

            $this->Loan_model->update($id,array('loan_status'=>'ACTIVE','disbursed'=>'Yes',$by=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s'), 'sent_back'=>0, 'sent_back_comment'=>null));

        }
        $logger = array(
            'user_id' => $this->session->userdata('user_id'),
            'activity' => 'Disbursed a loan',
            'activity_cate' => 'loan_disbursement'

        );
        log_activity($logger);

        // Insert into loan_approval_trail to track disbursement
        $trail_data = array(
            'user_id' => $this->session->userdata('user_id'),
            'action' => 'DISBURSED',
            'comment' => !empty($comment) ? $comment : 'Loan disbursed to customer',
            'loan_id' => $id
        );
        $this->Loan_approval_trail_model->insert($trail_data);

        if($notify->loan_disbursement=='Yes'){
            send_sms($customer->PhoneNumber,'Dear customer, loan has been Disbursed, you can call numbers below for more');
        }
        $this->toaster->success('Success, your action successful');
        redirect($_SERVER['HTTP_REFERER']);
    }


    function disburse_loan_pre_paid(){
        $id = $this->input->post('loan_id');
        $previous_date= $this->input->post('pdate');
        $current_date = $this->input->post('cdate');
        $comment= $this->input->post('comment');

        // Enforce role-based permission server-side (disbursement rights)
        if(!has_access('loan/approved')){
            $this->toaster->error('You do not have permission to perform this action.');
            redirect($_SERVER['HTTP_REFERER']);
            return;
        }

        // Check if current user did the final approval
        $current_user_id = $this->session->userdata('user_id');
        $this->db->select('action, user_id');
        $this->db->from('loan_approval_trail');
        $this->db->where('loan_id', $id);
        $this->db->where_in('action', array('APPROVED', 'CLIENT_SIGNED'));
        $this->db->order_by('date_stamp', 'DESC');
        $this->db->limit(1);
        $final_approval = $this->db->get()->row();

        if($final_approval && $final_approval->user_id == $current_user_id){
            $this->toaster->error('Error: You cannot disburse a loan that you approved or sent for disbursement. Another user must perform the disbursement.');
            redirect($_SERVER['HTTP_REFERER']);
            return;
        }

        $customer = $this->Loan_model->loan_user($id);
        $notify = get_by_id('sms_settings','id','1');

        $by = 'disbursed_by';

        $by_date = 'disbursed_date';

        if($current_date !=""){
            // Restructure payment schedules with new date
            $r  = $this->Loan_model->restructure($id,$current_date);
            
            // Update loan date if it's different from the previous date
            if($current_date != $previous_date) {
                $this->Loan_model->update($id, array('loan_date' => $current_date));
            }

            $charge_value = 0;
            $loan =	$this->Loan_model->get_by_id($id);
            $charge = get_by_id('loan_products','loan_product_id',	$loan->loan_product);

            if($loan->loan_principal > $charge->loan_processing_fee_threshold){
                if($charge->processing_charge_type_above == "Fixed"){
                    $charge_value = $charge->processing_fixed_charge_above;
                    if($loan->disbursed_amount<1){
                        $disbursedamount=$loan->loan_principal-$charge_value;
                    }
                    else{
                        $disbursedamount=$loan->disbursed_amount-$charge_value;

                    }

                }else{
                    $charge_value =  ($charge->processing_variable_charge_above/100) *  ($loan->loan_principal);
                    if($loan->disbursed_amount<1){
                        $disbursedamount=$loan->loan_principal-$charge_value;
                    }
                    else{
                        $disbursedamount=$loan->disbursed_amount-$charge_value;

                    }

                }
            }else{
                if($charge->processing_charge_type_below == "Fixed"){
                    $charge_value = $charge->processing_fixed_charge_below;
                    if($loan->disbursed_amount<1){
                        $disbursedamount=$loan->loan_principal-$charge_value;
                    }
                    else{
                        $disbursedamount=$loan->disbursed_amount-$charge_value;

                    }

                }else{
                    $charge_value =  ($charge->processing_variable_charge_below/100) *  ($loan->loan_principal);
                    if($loan->disbursed_amount<1){
                        $disbursedamount=$loan->loan_principal-$charge_value;
                    }
                    else{
                        $disbursedamount=$loan->disbursed_amount-$charge_value;

                    }

                }
            }


            $this->Loan_model->update($id,array('loan_status'=>'ACTIVE','disbursed_amount'=> $disbursedamount,'disbursed'=> 'Yes',$by=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s'), 'sent_back'=>0, 'sent_back_comment'=>null));
            $data = array(
                'ref' => "CF." . date('Y') . date('m') . date('d') . '.' . rand(100, 999),
                'loan_id' => $id,
                'amount' => $charge_value,
                'transaction_type' => 1,
                'payment_number' => 0,
                'method' => 0,
                'reference' => "CF." . date('Y') . date('m') . date('d') . '.' . rand(100, 999),
                'added_by' => $this->session->userdata('user_id')

            );

            $this->Transactions_model->insert($data);
        }else{
            // If no current date provided, use the previous date
            $current_date = $previous_date;
            
            $charge_value = 0;
            $loan =	$this->Loan_model->get_by_id($id);

            $charge = get_by_id('loan_products','loan_product_id',	$loan->loan_product);

            if($loan->loan_principal > $charge->loan_processing_fee_threshold){
                if($charge->processing_charge_type_above == "Fixed"){
                    $charge_value = $charge->processing_fixed_charge_above;
                    if($loan->disbursed_amount<1){
                        $disbursedamount=$loan->loan_principal-$charge_value;
                    }
                    else{
                        $disbursedamount=$loan->disbursed_amount-$charge_value;

                    }
                }else{
                    $charge_value =  ($charge->processing_variable_charge_above/100) *  ($loan->loan_principal);
                    if($loan->disbursed_amount<1){
                        $disbursedamount=$loan->loan_principal-$charge_value;
                    }
                    else{
                        $disbursedamount=$loan->disbursed_amount-$charge_value;

                    }
                }
            }else{
                if($charge->processing_charge_type_below == "Fixed"){
                    $charge_value = $charge->processing_fixed_charge_below;
                    if($loan->disbursed_amount<1){
                        $disbursedamount=$loan->loan_principal-$charge_value;
                    }
                    else{
                        $disbursedamount=$loan->disbursed_amount-$charge_value;

                    }
                }else{
                    $charge_value =  ($charge->processing_variable_charge_below/100) *  ($loan->loan_principal);
                    if($loan->disbursed_amount<1){
                        $disbursedamount=$loan->loan_principal-$charge_value;
                    }
                    else{
                        $disbursedamount=$loan->disbursed_amount-$charge_value;

                    }
                }
            }

            $this->Loan_model->update($id,array('loan_status'=>'ACTIVE', 'disbursed_amount'=> $disbursedamount,'disbursed'=>'Yes',$by=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s'), 'sent_back'=>0, 'sent_back_comment'=>null));
            $data = array(
                'ref' => "CF." . date('Y') . date('m') . date('d') . '.' . rand(100, 999),
                'loan_id' => $id,
                'amount' => $charge_value,
                'transaction_type' => 1,
                'payment_number' => 0,
                'method' => 0,
                'reference' => "CF." . date('Y') . date('m') . date('d') . '.' . rand(100, 999),
                'added_by' => $this->session->userdata('user_id')

            );

            $this->Transactions_model->insert($data);
        }
        $logger = array(
            'user_id' => $this->session->userdata('user_id'),
            'activity' => 'Disbursed a loan',
            'activity_cate' => 'loan_disbursement'

        );
        log_activity($logger);

        // Insert into loan_approval_trail to track disbursement
        $trail_data = array(
            'user_id' => $this->session->userdata('user_id'),
            'action' => 'DISBURSED',
            'comment' => !empty($comment) ? $comment : 'Loan disbursed to customer with processing fee deducted',
            'loan_id' => $id
        );
        $this->Loan_approval_trail_model->insert($trail_data);

        if($notify->loan_disbursement=='Yes'){
            send_sms($customer->PhoneNumber,'Dear customer, loan has been Disbursed, you can call numbers below for more');
        }
        $this->toaster->success('Success, your action successful');
        redirect($_SERVER['HTTP_REFERER']);
    }


    function bulkactions(){
        $by_date = 'approved_date';
        $users = $this->input->post('loans');
        $rowCount = count($users);
        for ($i = 0; $i < $rowCount; $i ++) {


            $this->Loan_model->update($users[$i],array('loan_status'=>'APPROVED','loan_approved_by'=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s'), 'minutes'=>$this->input->post('minutes'), 'sent_back'=>0, 'sent_back_comment'=>null));


        }
        $this->toaster->success('loans were approved successfully');
        redirect(site_url('Loan/initiated'));
    }


    function un_paid_loans()
    {

        $status = $this->input->get('status');
        if ($status == "fully_unpaid") {
            $data['loan_data'] =  get_all_full_un_paid_loans();
            $menu_toggle['toggles'] = 23;
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('loan/un_paid_loans', $data);
            $this->load->view('admin/footer');
        } else {
            $data['loan_data'] = get_all_un_paid_loans();
            $menu_toggle['toggles'] = 23;
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('loan/un_paid_loans', $data);
            $this->load->view('admin/footer');

        }
    }


    function bulkreject()
    {
        $by_date = 'approved_date';
        $users = $this->input->post('loans');
        $reasons = $this->input->post('rejectedReasons');
        $rowCount = count($users);
        for ($i = 0; $i < $rowCount; $i++) {

            $this->Loan_model->update($users[$i],array('loan_status'=>'REJECTED','rejection_reasons'=>$reasons,'rejected_by'=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s'), 'minutes'=>$this->input->post('minutes')));

            // Notify the loan creator of rejection
            $rej_loan = $this->db->get_where('loan', array('loan_id' => $users[$i]))->row();
            if ($rej_loan) {
                $cust_name = 'N/A';
                if ($rej_loan->customer_type == 'individual') {
                    $c = $this->db->get_where('individual_customers', array('id' => $rej_loan->loan_customer))->row();
                    if ($c) $cust_name = $c->Firstname . ' ' . $c->Lastname;
                } else {
                    $c = $this->db->get_where('corporate_customers', array('id' => $rej_loan->loan_customer))->row();
                    if ($c) $cust_name = $c->EntityName;
                }
                $cur = $this->db->get_where('currency', array('id' => $rej_loan->currency))->row();
                $cur_code = $cur ? $cur->code : 'ZMW';

                $creator_data = array(
                    'loan_id' => $rej_loan->loan_id,
                    'loan_number' => $rej_loan->loan_number,
                    'customer_name' => $cust_name,
                    'amount' => $rej_loan->loan_principal,
                    'currency' => $cur_code
                );
                notify_loan_creator($creator_data, 'REJECTED', $this->session->userdata('user_id'), $rej_loan->loan_added_by);
            }

        }
        $this->toaster->success('loan were rejected successfully');
        redirect(site_url('Loan/initiated'));
    }
    function single_reject()
    {
        $by_date = 'rejected_date';
        $loan_id = $this->input->post('loan_id');
        $reasons = $this->input->post('rejectedReasons');



        $this->Loan_model->update($loan_id,array('loan_status'=>'REJECTED','rejection_reasons'=>$reasons,'rejected_by'=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s')));

        // Notify the loan creator of rejection
        $rej_loan = $this->db->get_where('loan', array('loan_id' => $loan_id))->row();
        if ($rej_loan) {
            $cust_name = 'N/A';
            if ($rej_loan->customer_type == 'individual') {
                $c = $this->db->get_where('individual_customers', array('id' => $rej_loan->loan_customer))->row();
                if ($c) $cust_name = $c->Firstname . ' ' . $c->Lastname;
            } else {
                $c = $this->db->get_where('corporate_customers', array('id' => $rej_loan->loan_customer))->row();
                if ($c) $cust_name = $c->EntityName;
            }
            $cur = $this->db->get_where('currency', array('id' => $rej_loan->currency))->row();
            $cur_code = $cur ? $cur->code : 'ZMW';

            $creator_data = array(
                'loan_id' => $loan_id,
                'loan_number' => $rej_loan->loan_number,
                'customer_name' => $cust_name,
                'amount' => $rej_loan->loan_principal,
                'currency' => $cur_code
            );
            notify_loan_creator($creator_data, 'REJECTED', $this->session->userdata('user_id'), $rej_loan->loan_added_by);
        }

        $this->toaster->success('Loan were rejection was successful');
        redirect(site_url('loan/recommend'));

    }
    function bulkactions_recommend(){
        $by_date = 'loan_recommended_date';
        $users = $this->input->post('loans');
        $rowCount = count($users);
        $notified_approvers = false;

        for ($i = 0; $i < $rowCount; $i ++) {
            $this->Loan_model->update($users[$i],array('loan_status'=>'RECOMMENDED','loan_recommended_by'=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s'), 'sent_back'=>0, 'sent_back_comment'=>null));
        }

        // Send a single notification for bulk recommendations (to avoid spamming)
        if ($rowCount > 0) {
            // Get the first loan for basic notification info
            $first_loan = $this->db->get_where('loan', array('loan_id' => $users[0]))->row();
            if ($first_loan) {
                $currency_data = $this->db->get_where('currency', array('id' => $first_loan->currency))->row();
                $currency_code = $currency_data ? $currency_data->code : 'ZMW';

                // Send bulk notification
                $loan_notification_data = array(
                    'loan_id' => $users[0],
                    'loan_number' => $rowCount . ' loans',
                    'customer_name' => 'Multiple customers',
                    'amount' => 0,
                    'currency' => $currency_code
                );
                notify_loan_approvers($loan_notification_data, 'loan/unified_approval', $this->session->userdata('user_id'));
            }

            // Notify each loan's creator individually
            $notified_creators = array();
            for ($j = 0; $j < $rowCount; $j++) {
                $bulk_loan = $this->db->get_where('loan', array('loan_id' => $users[$j]))->row();
                if ($bulk_loan && !in_array($bulk_loan->loan_added_by, $notified_creators)) {
                    $cust_name = 'N/A';
                    if ($bulk_loan->customer_type == 'individual') {
                        $cust = $this->db->get_where('individual_customers', array('id' => $bulk_loan->loan_customer))->row();
                        if ($cust) $cust_name = $cust->Firstname . ' ' . $cust->Lastname;
                    } else {
                        $cust = $this->db->get_where('corporate_customers', array('id' => $bulk_loan->loan_customer))->row();
                        if ($cust) $cust_name = $cust->EntityName;
                    }
                    $cur = $this->db->get_where('currency', array('id' => $bulk_loan->currency))->row();
                    $cur_code = $cur ? $cur->code : 'ZMW';

                    $creator_notif_data = array(
                        'loan_id' => $bulk_loan->loan_id,
                        'loan_number' => $bulk_loan->loan_number,
                        'customer_name' => $cust_name,
                        'amount' => $bulk_loan->loan_principal,
                        'currency' => $cur_code
                    );
                    notify_loan_creator($creator_notif_data, 'RECOMMENDED', $this->session->userdata('user_id'), $bulk_loan->loan_added_by);
                    $notified_creators[] = $bulk_loan->loan_added_by;
                }
            }
        }

        $this->toaster->success('loans were approved successfully');
        redirect(site_url('Loan/recommend'));
    }
    function single_recommend()
    {
        $by_date = 'loan_recommended_date';
        $loan_id = $this->input->post('loan_id');
        $reasons = $this->input->post('recommend_reasons');
        $this->Loan_model->update($loan_id,array('loan_status'=>'RECOMMENDED','recommend_reasons'=>$reasons,'loan_recommended_by'=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s'), 'minutes'=>$this->input->post('minutes'), 'sent_back'=>0, 'sent_back_comment'=>null));

        // Send email notification to users who can approve loans
        $loan = $this->db->get_where('loan', array('loan_id' => $loan_id))->row();
        if ($loan) {
            // Get customer name
            $customer_name = 'N/A';
            if ($loan->customer_type == 'individual') {
                $customer = $this->db->get_where('individual_customers', array('id' => $loan->loan_customer))->row();
                if ($customer) {
                    $customer_name = $customer->Firstname . ' ' . $customer->Lastname;
                }
            } else {
                $customer = $this->db->get_where('corporate_customers', array('id' => $loan->loan_customer))->row();
                if ($customer) {
                    $customer_name = $customer->EntityName;
                }
            }

            // Get currency code
            $currency_data = $this->db->get_where('currency', array('id' => $loan->currency))->row();
            $currency_code = $currency_data ? $currency_data->code : 'ZMW';

            // Prepare loan data for notification
            $loan_notification_data = array(
                'loan_id' => $loan_id,
                'loan_number' => $loan->loan_number,
                'customer_name' => $customer_name,
                'amount' => $loan->loan_principal,
                'currency' => $currency_code
            );

            // Notify users with access to loan/unified_approval (approvers)
            notify_loan_approvers($loan_notification_data, 'loan/unified_approval', $this->session->userdata('user_id'));

            // Notify the loan creator that the loan has been recommended
            notify_loan_creator($loan_notification_data, 'RECOMMENDED', $this->session->userdata('user_id'), $loan->loan_added_by);
        }

        $this->toaster->success('Loan were recommended successfully');
        redirect(site_url('loan/recommend'));


    }
    function bulkreject_recommend()
    {
        $by_date = 'approved_date';
        $users = $this->input->post('loans');
        $reasons = $this->input->post('rejectedReasons');
        $rowCount = count($users);
        for ($i = 0; $i < $rowCount; $i++) {

            $this->Loan_model->update($users[$i],array('loan_status'=>'REJECTED','rejection_reasons'=>$reasons,'rejected_by'=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s')));

            // Notify the loan creator of rejection
            $rej_loan = $this->db->get_where('loan', array('loan_id' => $users[$i]))->row();
            if ($rej_loan) {
                $cust_name = 'N/A';
                if ($rej_loan->customer_type == 'individual') {
                    $c = $this->db->get_where('individual_customers', array('id' => $rej_loan->loan_customer))->row();
                    if ($c) $cust_name = $c->Firstname . ' ' . $c->Lastname;
                } else {
                    $c = $this->db->get_where('corporate_customers', array('id' => $rej_loan->loan_customer))->row();
                    if ($c) $cust_name = $c->EntityName;
                }
                $cur = $this->db->get_where('currency', array('id' => $rej_loan->currency))->row();
                $cur_code = $cur ? $cur->code : 'ZMW';

                $creator_data = array(
                    'loan_id' => $rej_loan->loan_id,
                    'loan_number' => $rej_loan->loan_number,
                    'customer_name' => $cust_name,
                    'amount' => $rej_loan->loan_principal,
                    'currency' => $cur_code
                );
                notify_loan_creator($creator_data, 'REJECTED', $this->session->userdata('user_id'), $rej_loan->loan_added_by);
            }

        }
        $this->toaster->success('loan were rejected successfully');
        redirect(site_url('Loan/recommend'));
    }
    function write_action(){
        $action = $this->input->get('action');
        $id= $this->input->get('id');
        $by = 'loan_approved_by';
        $by_date = 'approved_date';
        if($action =="REJECTED"){
            $by = 'rejected_by';
            $by_date = 'rejected_date';
        }
        if($action =="WRITTEN_OFF"){
            $by = 'write_off_approved_by';
            $by_date = 'write_off_approval_date';
        }
        if($action =="WRITE_OFF"){
            $by = 'written_off_by';
            $by_date = 'written_off_date';
        }
        $logger = array(
            'user_id' => $this->session->userdata('user_id'),
            'activity' => $action.' '.' a loan',
            'activity_cate' => 'updating'

        );
        log_activity($logger);
        if($action =="ACTIVE"){
            $by = 'written_off_by';

            $by_date = 'written_off_date';
            $this->Loan_model->update($id,array('loan_status'=>$action, $by=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s'), 'sent_back'=>0, 'sent_back_comment'=>null));
        }else{
            $this->Loan_model->update($id,array('loan_status'=>$action,$by=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s'), 'sent_back'=>0, 'sent_back_comment'=>null));
        }

        // Notify the loan creator of the status change
        $write_loan = $this->db->get_where('loan', array('loan_id' => $id))->row();
        if ($write_loan) {
            $cust_name = 'N/A';
            if ($write_loan->customer_type == 'individual') {
                $c = $this->db->get_where('individual_customers', array('id' => $write_loan->loan_customer))->row();
                if ($c) $cust_name = $c->Firstname . ' ' . $c->Lastname;
            } else {
                $c = $this->db->get_where('corporate_customers', array('id' => $write_loan->loan_customer))->row();
                if ($c) $cust_name = $c->EntityName;
            }
            $cur = $this->db->get_where('currency', array('id' => $write_loan->currency))->row();
            $cur_code = $cur ? $cur->code : 'ZMW';

            $creator_data = array(
                'loan_id' => $id,
                'loan_number' => $write_loan->loan_number,
                'customer_name' => $cust_name,
                'amount' => $write_loan->loan_principal,
                'currency' => $cur_code
            );
            notify_loan_creator($creator_data, $action, $this->session->userdata('user_id'), $write_loan->loan_added_by);
        }

        $this->toaster->success('Success, your action successful');
        redirect($_SERVER['HTTP_REFERER']);
    }
    function recommend_action(){
        $id = $this->input->post('loan_id');

        $logger = array(
            'user_id' => $this->session->userdata('user_id'),
            'activity' => 'Recommended a loan',
            'activity_cate' => 'loan_recomendation'

        );
        log_activity($logger);
        $this->Loan_model->update($id,array('loan_status'=>'RECOMMENDED', 'loan_recommended_by'=>$this->session->userdata('user_id'),'loan_recommended_date' =>date('Y-m-d H:i:s'), 'sent_back'=>0, 'sent_back_comment'=>null));

        // Send email notification to users who can approve loans
        $loan = $this->db->get_where('loan', array('loan_id' => $id))->row();
        if ($loan) {
            // Get customer name
            $customer_name = 'N/A';
            if ($loan->customer_type == 'individual') {
                $customer = $this->db->get_where('individual_customers', array('id' => $loan->loan_customer))->row();
                if ($customer) {
                    $customer_name = $customer->Firstname . ' ' . $customer->Lastname;
                }
            } else {
                $customer = $this->db->get_where('corporate_customers', array('id' => $loan->loan_customer))->row();
                if ($customer) {
                    $customer_name = $customer->EntityName;
                }
            }

            // Get currency code
            $currency_data = $this->db->get_where('currency', array('id' => $loan->currency))->row();
            $currency_code = $currency_data ? $currency_data->code : 'ZMW';

            // Prepare loan data for notification
            $loan_notification_data = array(
                'loan_id' => $id,
                'loan_number' => $loan->loan_number,
                'customer_name' => $customer_name,
                'amount' => $loan->loan_principal,
                'currency' => $currency_code
            );

            // Notify users with access to loan/unified_approval (approvers)
            notify_loan_approvers($loan_notification_data, 'loan/unified_approval', $this->session->userdata('user_id'));

            // Notify the loan creator that the loan has been recommended
            notify_loan_creator($loan_notification_data, 'RECOMMENDED', $this->session->userdata('user_id'), $loan->loan_added_by);
        }

        $this->toaster->success('Success, your recommending action was successful');
        redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * Send back loan to previous stage
     * This allows approvers to send loans back for corrections
     */
    function send_back() {
        $loan_id = $this->input->post('loan_id');
        $comment = $this->input->post('comment');
        $current_user_id = $this->session->userdata('user_id');

        // Enforce role-based permission server-side (return-for-edit is an approval decision)
        if(!has_access('loan/unified_approval')){
            $this->toaster->error('You do not have permission to perform this action.');
            redirect($_SERVER['HTTP_REFERER']);
            return;
        }

        if (!$loan_id || !$comment) {
            $this->toaster->error('Loan ID and comment are required');
            redirect($_SERVER['HTTP_REFERER']);
            return;
        }

        $loan = $this->Loan_model->get_by_id($loan_id);
        if (!$loan) {
            $this->toaster->error('Loan not found');
            redirect('loan/track');
            return;
        }

        // Determine previous status based on current status
        $current_status = $loan->loan_status;
        $previous_status = '';
        $send_back_to = '';

        switch ($current_status) {
            case 'INITIATED':
                // Send back from recommend stage to initiator
                $previous_status = 'INITIATED';
                $send_back_to = 'Initiator';
                break;
            case 'RECOMMENDED':
                $previous_status = 'INITIATED';
                $send_back_to = 'Initiator';
                break;
            case 'APPROVED_FIRST':
                $previous_status = 'RECOMMENDED';
                $send_back_to = 'Recommender';
                break;
            case 'APPROVED_SECOND':
                $previous_status = 'APPROVED_FIRST';
                $send_back_to = 'First Approver';
                break;
            case 'APPROVED':
                // Check if we have multi-level approvals
                $approvers = get_loan_approvers($loan_id);
                $approval_count = count($approvers);
                if ($approval_count >= 3) {
                    $previous_status = 'RECOMMENDED';
                    $send_back_to = 'Recommender';
                } elseif ($approval_count == 2) {
                    $previous_status = 'RECOMMENDED';
                    $send_back_to = 'Recommender';
                } else {
                    $previous_status = 'RECOMMENDED';
                    $send_back_to = 'Recommender';
                }
                break;
            case 'CLIENT_SIGNED':
                $previous_status = 'APPROVED';
                $send_back_to = 'Final Approver';
                break;
            default:
                $this->toaster->error('Cannot send back loan in current status: ' . $current_status);
                redirect($_SERVER['HTTP_REFERER']);
                return;
        }

        // Update loan status and set sent_back flag
        $update_data = array(
            'loan_status' => $previous_status,
            'sent_back' => 1,
            'sent_back_comment' => $comment,
            'sent_back_by' => $current_user_id,
            'sent_back_date' => date('Y-m-d H:i:s')
        );
        $this->Loan_model->update($loan_id, $update_data);

        // If sending back from RECOMMENDED, clear the multi-level approvals
        if ($current_status == 'RECOMMENDED' || $current_status == 'APPROVED') {
            // Remove approval trail entries for this loan (only MULTI_APPROVE type)
            $this->db->where('loan_id', $loan_id);
            $this->db->where('action', 'MULTI_APPROVE');
            $this->db->delete('loan_approval_trail');
        }

        // Log the send back action in approval trail
        $trail_data = array(
            'loan_id' => $loan_id,
            'user_id' => $current_user_id,
            'action' => 'SENT_BACK',
            'comment' => $comment,
            'from_status' => $current_status,
            'to_status' => $previous_status,
            'date_stamp' => date('Y-m-d H:i:s')
        );
        $this->Loan_approval_trail_model->insert($trail_data);

        // Log activity
        $logger = array(
            'user_id' => $current_user_id,
            'activity' => 'Sent back loan ' . $loan->loan_number . ' from ' . $current_status . ' to ' . $previous_status . '. Reason: ' . $comment,
            'activity_cate' => 'loan_send_back'
        );
        log_activity($logger);

        // Send email notification to the person who needs to correct the loan
        $this->_notify_send_back($loan, $comment, $previous_status);

        // Also notify the loan creator if they are not the one being sent back to
        if ($previous_status != 'INITIATED') {
            $cust_name = 'N/A';
            if ($loan->customer_type == 'individual') {
                $c = $this->db->get_where('individual_customers', array('id' => $loan->loan_customer))->row();
                if ($c) $cust_name = $c->Firstname . ' ' . $c->Lastname;
            } else {
                $c = $this->db->get_where('corporate_customers', array('id' => $loan->loan_customer))->row();
                if ($c) $cust_name = $c->EntityName;
            }
            $cur = $this->db->get_where('currency', array('id' => $loan->currency))->row();
            $cur_code = $cur ? $cur->code : 'ZMW';

            $creator_notif = array(
                'loan_id' => $loan_id,
                'loan_number' => $loan->loan_number,
                'customer_name' => $cust_name,
                'amount' => $loan->loan_principal,
                'currency' => $cur_code
            );
            notify_loan_creator($creator_notif, $previous_status, $current_user_id, $loan->loan_added_by);
        }

        $this->toaster->success('Loan has been sent back to ' . $send_back_to . ' for corrections');
        redirect('loan/view/' . $loan_id);
    }

    /**
     * Notify relevant users when a loan is sent back
     */
    private function _notify_send_back($loan, $comment, $previous_status) {
        // Get customer name
        $customer_name = 'N/A';
        if ($loan->customer_type == 'individual') {
            $customer = $this->db->get_where('individual_customers', array('id' => $loan->loan_customer))->row();
            if ($customer) {
                $customer_name = $customer->Firstname . ' ' . $customer->Lastname;
            }
        } else {
            $customer = $this->db->get_where('corporate_customers', array('id' => $loan->loan_customer))->row();
            if ($customer) {
                $customer_name = $customer->EntityName;
            }
        }

        // Determine who to notify based on previous status
        $notify_user_id = null;
        switch ($previous_status) {
            case 'INITIATED':
                $notify_user_id = $loan->loan_added_by;
                break;
            case 'RECOMMENDED':
                $notify_user_id = $loan->loan_recommended_by;
                break;
            case 'APPROVED_FIRST':
                // Get the first approver from trail
                $first_approver = $this->db->select('user_id')
                    ->from('loan_approval_trail')
                    ->where('loan_id', $loan->loan_id)
                    ->where('action', 'APPROVED_FIRST')
                    ->order_by('date_stamp', 'ASC')
                    ->limit(1)
                    ->get()->row();
                if ($first_approver) {
                    $notify_user_id = $first_approver->user_id;
                }
                break;
        }

        if ($notify_user_id) {
            $user = $this->db->get_where('employees', array('id' => $notify_user_id))->row();
            if ($user && !empty($user->EmailAddress)) {
                $currency_data = $this->db->get_where('currency', array('id' => $loan->currency))->row();
                $currency_code = $currency_data ? $currency_data->code : 'ZMW';

                $sender_user = $this->db->get_where('employees', array('id' => $this->session->userdata('user_id')))->row();
                $sender_name = $sender_user ? $sender_user->Firstname . ' ' . $sender_user->Lastname : 'System';

                $subject = 'Loan Sent Back for Corrections - ' . $loan->loan_number;
                $message = '
                    <p>Dear ' . $user->Firstname . ',</p>
                    <p>A loan has been sent back to you for corrections.</p>
                    <table style="border-collapse: collapse; width: 100%; margin: 15px 0;">
                        <tr><td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5; width: 30%;"><strong>Loan Number</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . $loan->loan_number . '</td></tr>
                        <tr><td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>Customer</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . $customer_name . '</td></tr>
                        <tr><td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>Amount</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . $currency_code . ' ' . number_format($loan->loan_principal, 2) . '</td></tr>
                        <tr><td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>Sent Back By</strong></td><td style="padding: 8px; border: 1px solid #ddd;">' . $sender_name . '</td></tr>
                        <tr><td style="padding: 8px; border: 1px solid #ddd; background: #f5f5f5;"><strong>Reason</strong></td><td style="padding: 8px; border: 1px solid #ddd; color: #dc3545;">' . htmlspecialchars($comment) . '</td></tr>
                    </table>
                    <p>Please review and make the necessary corrections, then resubmit the loan for approval.</p>
                    <p><a href="' . base_url('loan/view/' . $loan->loan_id) . '" style="display: inline-block; padding: 10px 20px; background: #007bff; color: #fff; text-decoration: none; border-radius: 5px;">View Loan</a></p>
                ';

                send_templated_email($user->EmailAddress, $subject, $message);
            }
        }
    }

    public function index()
    {
        $q = urldecode($this->input->get('q', TRUE));
        $start = intval($this->input->get('start'));

        if ($q <> '') {
            $config['base_url'] = base_url() . 'loan/index.html?q=' . urlencode($q);
            $config['first_url'] = base_url() . 'loan/index.html?q=' . urlencode($q);
        } else {
            $config['base_url'] = base_url() . 'loan/index.html';
            $config['first_url'] = base_url() . 'loan/index.html';
        }

        $config['per_page'] = 10;
        $config['page_query_string'] = TRUE;
        $config['total_rows'] = $this->Loan_model->total_rows($q);
        $loan = $this->Loan_model->get_limit_data($config['per_page'], $start, $q);

        $this->load->library('pagination');
        $this->pagination->initialize($config);

        $data = array(
            'loan_data' => $loan,
            'q' => $q,
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'start' => $start,
        );
        $this->load->view('admin/header');
        $this->load->view('loan/loan_list', $data);
        $this->load->view('admin/footer');

    }

    public function read($id)
    {
        $row = $this->Loan_model->get_by_id($id);
        if ($row) {
            $data = array(
                'loan_id' => $row->loan_id,
                'loan_number' => $row->loan_number,
                'loan_product' => $row->loan_product,
                'loan_customer' => $row->loan_customer,
                'loan_date' => $row->loan_date,
                'loan_principal' => $row->loan_principal,
                'loan_period' => $row->loan_period,
                'period_type' => $row->period_type,
                'loan_interest' => $row->loan_interest,
                'loan_amount_total' => $row->loan_amount_total,
                'next_payment_id' => $row->next_payment_id,
                'loan_added_by' => $row->loan_added_by,
                'loan_approved_by' => $row->loan_approved_by,
                'loan_status' => $row->loan_status,
                'loan_added_date' => $row->loan_added_date,
                'currency'=>$row->currency,
            );
            $this->load->view('loan/loan_read', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('loan'));
        }
    }

    public function create()
    {
        $data = array(
            'button' => 'Create',
            'action' => site_url('loan/create_action'),
            'loan_id' => set_value('loan_id'),
            'loan_number' => set_value('loan_number'),
            'loan_product' => set_value('loan_product'),
            'loan_customer' => set_value('loan_customer'),
            'loan_date' => set_value('loan_date'),
            'loan_principal' => set_value('loan_principal'),
            'loan_period' => set_value('loan_period'),
            'period_type' => set_value('period_type'),
            'loan_interest' => set_value('loan_interest'),
            'loan_amount_total' => set_value('loan_amount_total'),
            'next_payment_id' => set_value('next_payment_id'),
            'loan_added_by' => set_value('loan_added_by'),
            'loan_approved_by' => set_value('loan_approved_by'),
            'loan_status' => set_value('loan_status'),
            'loan_added_date' => set_value('loan_added_date'),
        );
        $this->load->view('loan/loan_form', $data);
    }

    public function create_action()
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            $data = array(
                'loan_number' => $this->input->post('loan_number',TRUE),
                'loan_product' => $this->input->post('loan_product',TRUE),
                'loan_customer' => $this->input->post('loan_customer',TRUE),
                'loan_date' => $this->input->post('loan_date',TRUE),
                'loan_principal' => $this->input->post('loan_principal',TRUE),
                'loan_period' => $this->input->post('loan_period',TRUE),
                'period_type' => $this->input->post('period_type',TRUE),
                'loan_interest' => $this->input->post('loan_interest',TRUE),
                'loan_amount_total' => $this->input->post('loan_amount_total',TRUE),
                'next_payment_id' => $this->input->post('next_payment_id',TRUE),
                'loan_added_by' => $this->input->post('loan_added_by',TRUE),
                'loan_approved_by' => $this->input->post('loan_approved_by',TRUE),
                'loan_status' => $this->input->post('loan_status',TRUE),
                'loan_added_date' => $this->input->post('loan_added_date',TRUE),
            );

            $this->Loan_model->insert($data);
            $this->session->set_flashdata('message', 'Create Record Success');
            redirect(site_url('loan'));
        }
    }

//edit initiate loan number


                    public function edit_initiate_loan()
                    {
                        date_default_timezone_set("Africa/Blantyre");
                        $date = new DateTime("now");

                        $curr_date = $date->format('Y-m-d');
                        $newloannumber=str_replace(' ', '', $this->input->post('new_loan_number',TRUE));
                            $data = array(
                                'loan_id' => $this->input->post('loan_id',TRUE),
                                'is_initiated' => 'yes',
                                'initiated_date' =>  $curr_date,
                                'initiated_by'=>$this->session->userdata('user_id'),
                                'old_loan_number' => $this->input->post('old_loan_number',TRUE),
                                'new_loan_number' =>  $newloannumber,
                                'reason_for_editing' => $this->input->post('reason_for_editing',TRUE),

                            );

                            $this->Edit_loan_model->insert( $data);
                            $this->session->set_flashdata('message', 'Inserted Record Success');
                            redirect(site_url('loan/recomend_edit_loan'));
                        }


    function  recomend_edit_loan(){
        $data['loan_data'] = $this->Loan_model->get_all_recomended_edit_loan();
        $menu_toggle['toggles'] = 23;


        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('loan/recomend_edit_loan', $data);
        $this->load->view('admin/footer');

    }


    public function update_initiate_close($id)
    {
        date_default_timezone_set("Africa/Blantyre");
        $date = new DateTime("now");

        $curr_date = $date->format('Y-m-d');
            $data = array(
                'loan_id' => $id,
                'is_initiated' => 'yes',
                'initiated_date' =>  $curr_date,
                'initiated_by'=>$this->session->userdata('user_id'),

            );

            $this->Close_loan_model->insert( $data);
            $this->session->set_flashdata('message', 'Inserted Record Success');
            redirect(site_url('loan/recomend_close_loan'));

        }
        //import payments data


    public function masspaymentscreate()
    {

        $insertdata                   = array();
        set_time_limit(1000);

        if(!empty($_FILES['excelfile']['name']))
        {
            // Get File extension eg. 'xlsx' to check file is excel sheet
            $pathinfo = pathinfo($_FILES['excelfile']['name']);

            // check file has extension xlsx, xls and also check
            // file is not empty
            if (($pathinfo['extension'] == 'xlsx' || $pathinfo['extension'] == 'xls')
                && $_FILES['excelfile']['size'] > 0 )
            {
                $config['upload_path'] = 'uploads/';

                $config['max_size'] = '10240';
                $config['overwrite'] = TRUE;
                $config['file_name'] = time().'_'.$_FILES['excelfile']['name'];
                $file = $_FILES['excelfile']['tmp_name'];
                $this->load->library('upload', $config);

                // Read excel file by using ReadFactory object.
                $reader = ReaderFactory::create(Type::XLSX);

                // Open file
                $reader->open($file);
                $count = 1;
                foreach ($reader->getSheetIterator() as $sheet)
                {



                    // Number of Rows in Excel sheet
                    foreach ($sheet->getRowIterator() as $row)
                    {

                        // It reads data after header. In the my excel sheet,
                        // header is in the first row.
                        if ($count > 1 && !empty($row[1])) {

                            // Data of excel sheet


                            $insertdata['loan_number'] = str_replace(' ', '', $row[0]);

                           // $monthlypaid = trim($row[1]); // Replace with the appropriate index from your Excel data

                            // Remove thousands separators (e.g., commas) and any non-numeric characters
//                            $cleanedpaid = str_replace(',', '', $monthlypaid);
//                            $decimalValue = floatval($cleanedpaid);
//
//                            // Now you can use $decimalValue in your $insertdata array or store it in the database
//                            $insertdata['monthly_repayment'] = $decimalValue;
                           // $insertdata['starting_pay_num'] = trim($row[]);
                            $insertdata['amount_paid'] = trim($row[1]);

                            $timestampf = strtotime($row[2]);
                            $fdate = date("Y-m-d", $timestampf);

                            $insertdata['paid_date'] =    $fdate;

                            $insertdata['massrepayment_status'] = 'imported';



                            $result = $this->Masspayments_model->insert($insertdata);
                        }

                        $count++;

                    }




                        $reader->close();

                        $this->toaster->success('Success, Imported successfully');

                        redirect('loan/process_imported_loan_mass_repayments');





                    // Close excel file
                }

            }
            else
            {

                $this->toaster->warning('Warning, Please Choose only Excel file');

                redirect('loan/import_loan_mass_repayments');
            }
        }
        else
        {
            $this->toaster->warning('Warning, Please Choose  Excel file');

            redirect('loan/import_loan_mass_repayments');
        }
    }


    //process imported loans

    function mass_repayments_process_data(){



        $sampleDataCofi =  get_all_data_imported_payments_cofi();



        foreach ( $sampleDataCofi as $rowdistinctcofi){


           $starting_pay_num=0;
           $getPaidrows=$this->Payement_schedules_model->count_full_paid_payments($rowdistinctcofi->loan_id);
            $getpartislPaidrows=$this->Payement_schedules_model->count_partial_paid_payments($rowdistinctcofi->loan_id);
            if( $getpartislPaidrows==0)
            {
                $starting_pay_num= $getPaidrows+  1;
            }
            else{

                $getpartialpaidrow=get_partial_paid_last($rowdistinctcofi->loan_id);


                $starting_pay_num=$getpartialpaidrow->payment_number;
            }

//            if($rowdistinctcofi->starting_pay_num==NULL ){
//                $starting_pay_num=1;
//
//            }
//            else{
//                $starting_pay_num=$rowdistinctcofi->starting_pay_num;
//            }

            $indi = $this->Loan_model->get_by_id($rowdistinctcofi->loan_id);
            $mothlypayments=get_paid_last($rowdistinctcofi->loan_id);

            $loan_product_id=$indi->loan_product_id;
            $data = array(
                'mass_loan_id' => $rowdistinctcofi->loan_id,
                'loan_period' => $rowdistinctcofi->loan_period,
                'starting_pay_num' => $starting_pay_num,
                'monthly_repayment' => $mothlypayments->amount,
                'mass_loan_product_id' => $loan_product_id,
                'massrepayment_status' => 'processed',

            );

            $this->Masspayments_model->update($rowdistinctcofi->massrepayment_id , $data);






        }
        $this->toaster->success('Success, All payments processed successfully');
        redirect('loan/make_mass_mass_repayments');






    }

//mass  repayments
    function mass_repayments_make_deposits_data()
    {


        $sampleDataCofi = $this->Masspayments_model->get_all_processed();
        $tid = "TR-S" . rand(100, 9999) . date('Y') . date('m') . date('d');
        $result = array();
        $get_account = $this->Tellering_model->get_teller_account($this->session->userdata('user_id'));
        if (empty($get_account)) {
            $result['status'] = 'error';
            $result['message'] = "You are not authorized to do this transaction";
        } else {
            foreach ($sampleDataCofi as $rowdistinctcofi) {
                $patialpaymentnumber = 0;
                $paymentnumber = 0;
                $nextpayment = 0;
                $mAmount = 0;
                $patialpaidamount = 0;
                $specialpartialpaid=0;
                $start_pay_num=0;

                $num_strtotal = floatval(str_replace(',', '', $rowdistinctcofi->amount_paid));
                $num_strtorp = floatval(str_replace(',', '', $rowdistinctcofi->monthly_repayment));

                if($num_strtotal>=$num_strtorp){
                    $paymentnumberm = ($num_strtotal / $num_strtorp);
                    $modulus = fmod($num_strtotal, $num_strtorp);
                }

                else {
                    $paymentnumberm=$rowdistinctcofi->starting_pay_num;
                    $patialpaidamount =$num_strtotal;
                    $specialpartialpaid=$patialpaidamount;
                    $patialpaymentnumber=$rowdistinctcofi->starting_pay_num;
                }


                if ($modulus > 5000) {

                    $patialpaymentnumber = $paymentnumberm + 1;
                    $patialpaidamount = $modulus;
                }
                if ($paymentnumberm == $rowdistinctcofi->loan_period) {
                    $paymentnumberm = $rowdistinctcofi->loan_period;
                    $patialpaidamount = 0;
                } else {
                    $nextpayment = $paymentnumberm + 1;
                }


                $teller_account = $get_account->account;


                $start_pay_num=$rowdistinctcofi->starting_pay_num;

                $account = $rowdistinctcofi->loan_number;
                $amount = $rowdistinctcofi->amount_paid;
                $mode = 'deposit';
                $paid_date = $rowdistinctcofi->paid_date;
                $res = $this->Account_model->cash_transaction($teller_account, $account, $amount, $mode, $tid, $paid_date);
                $data = array(
                    'massrepayment_status' => 'deposited',

                );

                $this->Masspayments_model->update($rowdistinctcofi->massrepayment_id , $data);

            }
            if ($res == 'success') {
                $this->toaster->success('Success, payment deposits was successful');
                redirect($_SERVER['HTTP_REFERER']);

            } else {
                $this->toaster->success('Danger,Check mass  payment ');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }




//mass  repayments
    function mass_repayments_make_payments_data()
    {


        $sampleDataCofi = $this->Masspayments_model->get_all_deposited();
        $tid = "TR-S" . rand(100, 9999) . date('Y') . date('m') . date('d');
        $result = array();
        $get_account = $this->Tellering_model->get_teller_account($this->session->userdata('user_id'));
        if (empty($get_account)) {
            $result['status'] = 'error';
            $result['message'] = "You are not authorized to do this transaction";
        } else {
            foreach ($sampleDataCofi as $rowloan) {



                $rowdistinctcofi=getlastrow($rowloan->loan_number );
                $paid_date=$rowdistinctcofi->paid_date;


                   // for ($pay_number = $start_pay_num; $pay_number <= $rowdistinctcofi->loan_period; $pay_number++) {
                        $loan_account = get_by_id('loan', 'loan_id', $rowdistinctcofi->mass_loan_id);
                        $recepientt = get_by_id('account', 'collection_account', 'Yes');
                        $check = $this->Account_model->get_account($loan_account->loan_number);
                        if ($check->balance >= $rowdistinctcofi->monthly_repayment) {


                                $resultpayment=$this->Loan_model->mass_full_payments($loan_account->loan_number, $recepientt->account_number,$rowdistinctcofi->monthly_repayment, $rowdistinctcofi->mass_loan_id, $paid_date,$rowdistinctcofi->loan_period);

                                    if($resultpayment) {
                                        $data = array(
                                            'massrepayment_status' => 'payment_made',
                                        );

                                        $this->Masspayments_model->update($rowdistinctcofi->massrepayment_id, $data);



                                $logger = array(

                                    'user_id' => $this->session->userdata('user_id'),
                                    'activity' => 'Mass payments,  loan ID:' . ' ' . $loan_account->loan_number . ' ' . ' payment number' . ' ' ,
                                    'activity_cate' => 'Mass_loan_repayment'

                                );
                                log_activity($logger);

                                    }

                            }
                        elseif ($check->balance > 0 && $check->balance < $rowdistinctcofi->monthly_repayment) {



                                $resultpayment=$this->Loan_model->mass_full_payments($loan_account->loan_number, $recepientt->account_number,$rowdistinctcofi->monthly_repayment, $rowdistinctcofi->mass_loan_id, $paid_date,$rowdistinctcofi->loan_period);


                                $data = array(
                                    'massrepayment_status' => 'payment_made',
                                );

                                $this->Masspayments_model->update($rowdistinctcofi->massrepayment_id , $data);

                                $logger = array(

                                    'user_id' => $this->session->userdata('user_id'),
                                    'activity' => 'Mass payments,  loan ID:' . ' ' . $loan_account->loan_number . ' ' . ' payment number',
                                    'activity_cate' => 'Mass_loan_repayment'

                                );
                                log_activity($logger);



                        }





            }
            if ($resultpayment) {
                $this->toaster->success('Success, payment was successful');
                redirect($_SERVER['HTTP_REFERER']);

            } else {
                $this->toaster->success('Danger,Check mass  payment ');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

        public function update_recomend_edit()
                    {
                $id=$this->input->post('loan_id',TRUE);
                        $loandetails=get_by_id('edit_loan','loan_id',$id);
                        date_default_timezone_set("Africa/Blantyre");
                        $date = new DateTime("now");

                        $curr_date = $date->format('Y-m-d');
                            $data = array(

                                'is_recommended' => 'yes',
                                'recomended_date ' =>  $curr_date,
                                'recomended_by '=>$this->session->userdata('user_id'),

                            );

                            $this->Edit_loan_model->update($loandetails->edit_loan_id, $data);
                            $this->session->set_flashdata('message', 'Inserted Record Success');
                            redirect(site_url('loan/edit_loan'));
                        }


        public function update_recomend_close($id)
    {

        $loandetails=get_by_id('close_loan','loan_id',$id);
        date_default_timezone_set("Africa/Blantyre");
        $date = new DateTime("now");

        $curr_date = $date->format('Y-m-d');
            $data = array(

                'is_recommended' => 'yes',
                'recomended_date ' =>  $curr_date,
                'recomended_by '=>$this->session->userdata('user_id'),

            );

            $this->Close_loan_model->update($loandetails->close_loan_id, $data);
            $this->session->set_flashdata('message', 'Inserted Record Success');
            redirect(site_url('loan/close_loan'));
        }


        public function update_close_loan($id)
    {

        $loandetails=get_by_id('close_loan','loan_id',$id);
        date_default_timezone_set("Africa/Blantyre");
        $date = new DateTime("now");

        $curr_date = $date->format('Y-m-d');
            $data = array(

                'close_loan_status' => 'yes',
                'closed_loan_date' =>  $curr_date,
                'close_by'=>$this->session->userdata('user_id'),

            );

            $this->Close_loan_model->update($loandetails->close_loan_id, $data);

            $data = array(

                'loan_status' => 'CLOSED',
            );

            $this->Loan_model->update($id, $data);

            $paymentschedule =  get_all_loan_partial_paid($id);

            foreach ( $paymentschedule as $rowdistinctcofi){

                $data = array(
                    'partial_paid' => 'NO',
                    'status' => 'PAID',
                    );
                    $result = $this->Payement_schedules_model->update($rowdistinctcofi->id, $data);

        }
        if ($result) {
            $logger = array(

                'user_id' => $this->session->userdata('user_id'),
                'activity' => 'Closed loan' ,
                'activity_cate' => 'manual_loan_closing',

            );
            log_activity($logger);
        }

            $this->session->set_flashdata('message', 'Inserted Record Success');
            redirect(site_url('loan/close_loan'));

    }



     public function update_edit_loan($id)
    {
       $row = $this->Loan_model->get_by_id($id);
        $loandetails=get_by_id('edit_loan','loan_id',$id);
        date_default_timezone_set("Africa/Blantyre");
        $date = new DateTime("now");

        $curr_date = $date->format('Y-m-d');
            $data = array(

                'edit_loan_status' => 'yes',
                'edit_loan_date' =>  $curr_date,
                'edit_by'=>$this->session->userdata('user_id'),

            );

            $this->Edit_loan_model->update($loandetails->edit_loan_id, $data);
             //$newloannumber=str_replace(' ', '', $this->input->post('new_loan_number',TRUE));
            $data = array(

                'loan_number' => $loandetails->new_loan_number,
            );
              $this->Loan_model->update($id, $data);

            $accountdetails=get_by_id('account','account_number',$loandetails->old_loan_number);
            if($accountdetails)
            {

                        $data = array(


                                'account_number'=>$loandetails->new_loan_number,

                                );

                      $this->Account_model->update($accountdetails->account_id, $data);

                            $logger = array(

                                        'user_id' => $this->session->userdata('user_id'),
                                        'activity' => 'loan edited ' ,
                                        'activity_cate' => 'manual_loan_editing',

                                    );
                                    log_activity($logger);
                                     $this->session->set_flashdata('message', 'Inserted Record Success');
                                    redirect(site_url('loan/edit_loan'));






            }
            else {
                 $data = array(
                            		'client_id' => $row->loan_customer,
                            		'account_number' => $loandetails->new_loan_number,
                            		'balance' => 0,
                            		'account_type' => 1,
                            		'account_type_product' => $row->loan_product,
                            		'added_by' => $this->session->userdata('user_id'),

                            	    );


                                        $result= $this->Account_model->insert($data);

                            $logger = array(

                                        'user_id' => $this->session->userdata('user_id'),
                                        'activity' => 'loan edited ' ,
                                        'activity_cate' => 'manual_loan_editing',

                                    );
                                    log_activity($logger);
                                     $this->session->set_flashdata('message', 'Inserted Record Success');
                                    redirect(site_url('loan/edit_loan'));





            }



    }



    public function update($id)
    {
        $row = $this->Loan_model->get_by_id($id);

        if ($row) {
            $data = array(
                'button' => 'Update',
                'action' => site_url('loan/update_action'),
                'loan_id' => set_value('loan_id', $row->loan_id),
                'loan_number' => set_value('loan_number', $row->loan_number),
                'loan_product' => set_value('loan_product', $row->loan_product),
                'loan_customer' => set_value('loan_customer', $row->loan_customer),
                'loan_date' => set_value('loan_date', $row->loan_date),
                'loan_principal' => set_value('loan_principal', $row->loan_principal),
                'loan_period' => set_value('loan_period', $row->loan_period),
                'period_type' => set_value('period_type', $row->period_type),
                'loan_interest' => set_value('loan_interest', $row->loan_interest),
                'loan_amount_total' => set_value('loan_amount_total', $row->loan_amount_total),
                'next_payment_id' => set_value('next_payment_id', $row->next_payment_id),
                'loan_added_by' => set_value('loan_added_by', $row->loan_added_by),
                'loan_approved_by' => set_value('loan_approved_by', $row->loan_approved_by),
                'loan_status' => set_value('loan_status', $row->loan_status),
                'loan_added_date' => set_value('loan_added_date', $row->loan_added_date),
            );
            $this->load->view('loan/loan_form', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('loan'));
        }
    }

    public function update_action()
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('loan_id', TRUE));
        } else {
            $data = array(
                'loan_number' => $this->input->post('loan_number', TRUE),
                'loan_product' => $this->input->post('loan_product', TRUE),
                'loan_customer' => $this->input->post('loan_customer', TRUE),
                'loan_date' => $this->input->post('loan_date', TRUE),
                'loan_principal' => $this->input->post('loan_principal', TRUE),
                'loan_period' => $this->input->post('loan_period', TRUE),
                'period_type' => $this->input->post('period_type', TRUE),
                'loan_interest' => $this->input->post('loan_interest', TRUE),
                'loan_amount_total' => $this->input->post('loan_amount_total', TRUE),
                'next_payment_id' => $this->input->post('next_payment_id', TRUE),
                'loan_added_by' => $this->input->post('loan_added_by', TRUE),
                'loan_approved_by' => $this->input->post('loan_approved_by', TRUE),
                'loan_status' => $this->input->post('loan_status', TRUE),
                'loan_added_date' => $this->input->post('loan_added_date', TRUE)
            );

            $this->Loan_model->update($this->input->post('loan_id', TRUE), $data);
            $this->session->set_flashdata('message', 'Update Record Success');
            redirect(site_url('loan'));
        }
    }

    public function delete($id)
    {
        $row = $this->Loan_model->get_by_id($id);

        if ($row) {
            $this->Loan_model->delete($id);
            $this->toaster->success('Success, your action successful');
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function delete_data($id)
    {
        $row = $this->Loan_model->get_by_id($id);

        if ($row) {
            $this->Loan_model->delete_data($id);
            $this->toaster->success('Success, your action successful');
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
    function loan_report(){
        // Load all loans by default (Active and Closed)
        $data['loan_data'] = $this->Loan_model->get_loans_for_report();

        // Get stats for the report
        $data['stats'] = array(
            'active' => get_loan_stats_by_status('ACTIVE'),
            'closed' => get_loan_stats_by_status('CLOSED'),
            'written_off' => get_loan_stats_by_status('WRITTEN_OFF')
        );

        $this->load->view('admin/header');
        $this->load->view('loan/loan_report', $data);
        $this->load->view('admin/footer');
    }
    function loan_report_search(){
        $user = $this->input->get('user');
        $product = $this->input->get('product');
        $status = $this->input->get('status');
        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $search = $this->input->get('search');

        // Get stats for the report
        $data['stats'] = array(
            'active' => get_loan_stats_by_status('ACTIVE'),
            'closed' => get_loan_stats_by_status('CLOSED'),
            'written_off' => get_loan_stats_by_status('WRITTEN_OFF')
        );

        if($search=="filter"){
            $data['loan_data'] = $this->Loan_model->get_filter($user,$product,$status,$from,$to);
            $this->load->view('admin/header');
            $this->load->view('loan/loan_report',$data);
            $this->load->view('admin/footer');
        }elseif($search=='pdf'){
            $data['loan_data'] = $this->Loan_model->get_filter($user,$product,$status,$from,$to);
            $data['officer'] = ($user=="All") ? "All Officers" : get_by_id('employees','id',$user)->Firstname;
            $data['product'] =($product=="All") ? "All Products" : get_by_id('loan_products','loan_product_id',$product)->product_name;
            $data['from'] = $from;
            $data['to'] = $to;
            $this->load->library('Pdf');
            $html = $this->load->view('loan/loan_report_pdf', $data,true);
            $this->pdf->createPDF($html, "loan report as on".date('Y-m-d'), true,'A4','landscape');
        } else {
            // No search parameter - redirect to main report
            redirect('loan/loan_report');
        }
    }
    function exportExcel()
    {
        $export_type ='CSV';
        // file name
        $filename = 'RBM_loanReport' . date('Ymd') . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        // get data

        $usersData = rbm_report();
        // file creation
        $file = fopen('php://output', 'w');
        $header = array(
            "Salutation ",
            "Surname ",
            "First Name ",
            "Middle Name" ,
            "	Maiden Name" ,
            "Gender" ,
            "	Marital Status"	,
            "No. of Dependents" ,
            "Date of Birth"	,
            "National ID No.",
            "ID Type",
            "ID No.",
            "	Nationality",
            "	Village" ,
            " T/A" ,
            " Home District",
            "Resident Permit No.",
            "Phone No."	,
            "Postal Address" ,
            "Email Address" ,
            "Residential Address" ,
            "Residential District",
            "Plot No.	",
            "Profession/Occupation",
            "Employer Name ",
            "Employer Address",
            "	Employer Phone No.",
            "Employment Date" ,
            "	Branch Code/Name",
            "	Loan Reference No.",
            "Old Loan Reference No.",
            "Currency  "	,
            "Approved Amount",
            "	Approved Amount(MWK)",
            "Disbursed" ,
            "Amount"	,
            "Disbursed Amount (MWK)"	,
            " Disbursement Date",
            "Maturity Date",
            " Borrower Type",
            "Group Name",
            " Group No.",
            "Product Type",
            "Payment Terms",
            "Collateral Status",
            "Reserve Bank Classification",
            "	Account Status",
            "	Account Status Change Date"	,
            " Scheduled Repayment Amount",
            "Scheduled Repayment Amount(MWK)",
            "Total Amount Paid To Date",
            "Total Amount Paid To Date(MWK)"	,
            "Current Balance	Current Balance(MWK)",
            "	Available Credit",
            "	Available Credit(MWK)",
            "Amount In Arrears",
            "Amount In Arrears(MWK)",
            "	Days In Arrears	",
            "No. of Installments In Arrears ",
            "	Default Date",
            " Pay Off/Termination" ,
            "Date	Reason For Closure"	,
            "First Payment Date",
            "	Last Payment Date",
            "Last Payment Amount" ,
            "Last Payment Amount (MWK)"

        );
        fputcsv($file, $header);
        foreach ($usersData as $key => $line) {
            fputcsv($file, $line);
        }
        fclose($file);
        exit();
    }

    function exportExceView()
    {
        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $search = $this->input->get('search');


        $this->load->view('admin/header');
        $this->load->view('loan/rbm_view_report');
        $this->load->view('admin/footer');
    }

    function loan_report_projection(){

        $this->load->view('admin/header');
        $this->load->view('loan/loan_report_projectn');
        $this->load->view('admin/footer');
    }
    function loan_report_search_projection(){
//		$user = $this->input->get('user');
//		$product = $this->input->get('product');
//		$status = $this->input->get('status');
        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $search = $this->input->get('search');
        if($search=="filter"){


            $result = $this->Payement_schedules_model->get_filter_projection($from,$to);
            $amount= $this->Payement_schedules_model->get_filter_projections($from,$to);
            $pri= $this->Payement_schedules_model->get_filter_projection_principal($from,$to);
            $inter= $this->Payement_schedules_model->get_filter_projection_interest($from,$to);
            $data = array(
                'amount'=>$amount['amount'],
                'interest'=>$inter['interest'],
                'principal'=>$pri['principal'],
                'paid_amount'=>$result['paid_amount']

            );

            $this->load->view('admin/header');
            $this->load->view('loan/loan_report_projections',$data);
            $this->load->view('admin/footer');
        }elseif($search=='pdf'){
            $data['loan_data'] = $this->Loan_model->get_filter($from,$to);
//			$data['officer'] = ($user=="All") ? "All Officers" : get_by_id('employees','id',$user)->Firstname;
//			$data['product'] =($product=="All") ? "All Products" : get_by_id('loan_products','loan_product_id',$product)->product_name;
            $data['from'] = $from;
            $data['to'] = $to;
            $this->load->library('Pdf');
            $html = $this->load->view('loan/loan_report_pdf', $data,true);
            $this->pdf->createPDF($html, "loan report as on".date('Y-m-d'), true,'A4','landscape');
        }

    }

    public function _rules()
    {
        $this->form_validation->set_rules('loan_number', 'loan number', 'trim|required');
        $this->form_validation->set_rules('loan_product', 'loan product', 'trim|required');
        $this->form_validation->set_rules('loan_customer', 'loan customer', 'trim|required');
        $this->form_validation->set_rules('loan_date', 'loan date', 'trim|required');
        $this->form_validation->set_rules('loan_principal', 'loan principal', 'trim|required|numeric');
        $this->form_validation->set_rules('loan_period', 'loan period', 'trim|required');
        $this->form_validation->set_rules('period_type', 'period type', 'trim|required');
        $this->form_validation->set_rules('loan_interest', 'loan interest', 'trim|required');
        $this->form_validation->set_rules('loan_amount_total', 'loan amount total', 'trim|required|numeric');
        $this->form_validation->set_rules('next_payment_id', 'next payment id', 'trim|required');
        $this->form_validation->set_rules('loan_added_by', 'loan added by', 'trim|required');
        $this->form_validation->set_rules('loan_approved_by', 'loan approved by', 'trim|required');


    }


// Add this method to your Loan controller
public function get_loan_product_details() {
    // Check if this is an AJAX request
    if (!$this->input->is_ajax_request()) {
        exit('No direct script access allowed');
    }
    
    $loan_product_id = $this->input->post('loan_product_id');
    
    if (!$loan_product_id) {
        echo json_encode([
            'status' => 'error',
            'message' => 'No loan product ID provided'
        ]);
        return;
    }
    
    // Load the model if not already loaded
    if (!isset($this->Loan_products_model)) {
        $this->load->model('Loan_products_model');
    }
    
    // Get loan product details
    $product = $this->Loan_products_model->get_by_id($loan_product_id);
    
    if ($product) {
        echo json_encode([
            'status' => 'success',
            'data' => [
                'interest_min' => $product->interest_min,
                'interest_max' => $product->interest_max,
                'product_description' => $product->product_description ?? '',
                // Add any other fields you want to return
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Loan product not found'
        ]);
    }
}


// Add these methods to the Loan controller

	/**calculate_payoff
	 * Calculate payoff amount for a loan
	 * This method handles AJAX requests to calculate the total payoff amount
	 */
	/**
	 * Calculate payoff amount for a loan with special first month interest handling
	 * This method handles AJAX requests to calculate the total payoff amount
	 */
	/**
	 * Calculate payoff amount for a loan with special first month interest handling
	 * This method handles AJAX requests to calculate the total payoff amount
	 */
	/**
	 * Calculate payoff amount for a loan with special first month interest handling
	 * This method handles AJAX requests to calculate the total payoff amount
	 */
	/**
	 * Calculate payoff amount for a loan with special first month interest handling
	 * This method handles AJAX requests to calculate the total payoff amount
	 */
	/**
	 * Calculate payoff amount for a loan with special first month interest handling
	 * This method handles AJAX requests to calculate the total payoff amount
	 */
	/**
	 * Calculate payoff amount for a loan with special first month interest handling
	 * This method handles AJAX requests to calculate the total payoff amount
	 */
	/**
	 * Calculate payoff amount for a loan with special first month interest handling
	 * This method handles AJAX requests to calculate the total payoff amount
	 */
	/**
	 * Calculate payoff amount for a loan with special first month interest handling
	 * This method handles AJAX requests to calculate the total payoff amount
	 */
	/**
	 * Calculate payoff amount for a loan with special first month interest handling
	 * This method handles AJAX requests to calculate the total payoff amount
	 */
	/**
	 * Calculate payoff amount for a loan with special first month interest handling
	 * This method handles AJAX requests to calculate the total payoff amount
	 */
	/**
	 * Calculate payoff amount for a loan with special first month interest handling
	 * This method handles AJAX requests to calculate the total payoff amount
	 */
	public function calculate_payoff() {
		if (!$this->input->is_ajax_request()) {
			show_error('No direct script access allowed', 403);
			return;
		}

		$loan_id     = $this->input->post('loan_id');
		$payoff_date = $this->input->post('payoff_date');

		if (!$loan_id || !$payoff_date) {
			echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
			return;
		}

		$loan = $this->Loan_model->get_by_id($loan_id);
		if (!$loan) {
			echo json_encode(['status' => 'error', 'message' => 'Loan not found']);
			return;
		}

		if ($loan->calculation_type == 'Bullet Payment') {
			$result = $this->_compute_bullet_payoff($loan_id, $payoff_date);
		} else {
			$result = $this->_compute_non_bullet_payoff($loan_id, $payoff_date);
		}

		echo json_encode($result);
	}

	public function calculate_payoff_inline($lid) {
		$loan = $this->Loan_model->get_by_id($lid);
		if (!$loan) return ['status' => 'error', 'message' => 'Loan not found'];
		if ($loan->calculation_type == 'Bullet Payment') {
			return $this->_compute_bullet_payoff($lid, date('Y-m-d'));
		}
		return $this->_compute_non_bullet_payoff($lid, date('Y-m-d'));
	}

	public function get_early_settlement_amount($loan_id)
	{
		$loan_id = (int) $loan_id;
		$date    = $this->input->get('date');

		if (!$loan_id || !$date) {
			echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
			return;
		}

		$d = DateTime::createFromFormat('Y-m-d', $date);
		if (!$d || $d->format('Y-m-d') !== $date) {
			echo json_encode(['status' => 'error', 'message' => 'Invalid date format. Use Y-m-d.']);
			return;
		}

		$loan = $this->Loan_model->get_by_id($loan_id);
		if (!$loan || $loan->loan_status !== 'ACTIVE') {
			echo json_encode(['status' => 'error', 'message' => 'Loan is not available for early settlement']);
			return;
		}

		if ($loan->calculation_type == 'Bullet Payment') {
			echo json_encode(['status' => 'error', 'message' => 'Early settlement is not available for bullet payment loans']);
			return;
		}

		$breakdown = $this->Payement_schedules_model->calculate_payoff_amount($loan_id, $date);
		if (!is_array($breakdown)) {
			echo json_encode(['status' => 'error', 'message' => 'Failed to calculate payoff amount']);
			return;
		}

		$currency      = get_by_id('currencies', 'currency_id', $loan->currency);
		$currency_code = $currency ? $currency->currency_code : '';

		echo json_encode([
			'status'           => 'success',
			'overdue_amount'   => $breakdown['overdue_amount'],
			'future_principal' => $breakdown['future_principal'],
			'interest_waived'  => $breakdown['interest_waived'],
			'total_payoff'     => $breakdown['total_payoff'],
			'currency_code'    => $currency_code,
		]);
	}

	public function process_early_settlement()
	{
		$loan_id    = (int)$this->input->post('loan_id');
		$paid_date  = $this->input->post('paid_date');
		$amount     = (float)$this->input->post('amount');
		$pay_method = $this->input->post('payment_method');

		// Validate $paid_date format
		$dt = DateTime::createFromFormat('Y-m-d', $paid_date);
		if (!$dt || $dt->format('Y-m-d') !== $paid_date) {
			$this->toaster->error('Invalid settlement date.');
			redirect($_SERVER['HTTP_REFERER']);
			return;
		}

		// Guard: loan must be active
		$loan = $this->Loan_model->get_by_id($loan_id);
		if (!$loan || $loan->loan_status !== 'ACTIVE') {
			$this->toaster->error('This loan is not active.');
			redirect($_SERVER['HTTP_REFERER']);
			return;
		}

		if ($loan->calculation_type == 'Bullet Payment') {
			$this->toaster->error('Early settlement is not available for bullet payment loans.');
			redirect($_SERVER['HTTP_REFERER']);
			return;
		}

		// Guard: amount must match the calculated payoff (±0.01 tolerance)
		$breakdown = $this->Payement_schedules_model->calculate_payoff_amount($loan_id, $paid_date);
		if (abs($amount - $breakdown['total_payoff']) > 0.01) {
			$this->toaster->error('Settlement amount does not match the calculated payoff of ' .
				number_format($breakdown['total_payoff'], 2) . '. Please recalculate.');
			redirect($_SERVER['HTTP_REFERER']);
			return;
		}

		// Handle proof of payment upload
		$unique_name = '';
		$config = [
			'upload_path'   => './uploads/',
			'allowed_types' => 'jpg|png|jpeg|gif|pdf|docx|txt|zip',
			'max_size'      => 2048,
			'remove_spaces' => TRUE,
		];
		$this->load->library('upload', $config);
		if (!empty($_FILES['pay_proof']['name'])) {
			$file_ext    = pathinfo($_FILES['pay_proof']['name'], PATHINFO_EXTENSION);
			$unique_name = 'file_' . time() . '_' . uniqid() . '.' . $file_ext;
			$config['file_name'] = $unique_name;
			$this->upload->initialize($config);
			if (!$this->upload->do_upload('pay_proof')) {
				$unique_name = ''; // upload failed, proceed without proof
			}
		}

		$tid          = 'ES-' . rand(1000, 9999) . date('Ymd');
		$loan_account = get_by_id('loan', 'loan_id', $loan_id);
		$recepientt   = get_by_id('account', 'collection_account', 'Yes');

		if (!$loan_account || !$recepientt) {
			$this->toaster->error('Account configuration error. Please contact support.');
			redirect($_SERVER['HTTP_REFERER']);
			return;
		}

		if ($pay_method == '0') {
			// Institution's Bank Savings path
			$check = $this->Account_model->get_account($loan_account->loan_number);
			if ($check->balance < $amount) {
				$this->toaster->error('Insufficient funds in loan savings account.');
				redirect($_SERVER['HTTP_REFERER']);
				return;
			}
			$txn = $this->Account_model->transfer_funds(
				$loan_account->loan_number, $recepientt->account_number, $amount, $tid, $paid_date, $unique_name
			);
			if ($txn !== 'success') {
				$this->toaster->error('Account transfer failed.');
				redirect($_SERVER['HTTP_REFERER']);
				return;
			}
		} else {
			// Cash deposit via teller
			$get_account = $this->Tellering_model->get_teller_account($this->session->userdata('user_id'));
			if (empty($get_account)) {
				$this->toaster->error('You are not authorized to do this transaction, only cashiers.');
				redirect($_SERVER['HTTP_REFERER']);
				return;
			}
			$deposit = $this->Account_model->cash_transaction(
				$get_account->account, $loan_account->loan_number, $amount, 'deposit', $tid, $paid_date, $unique_name
			);
			if ($deposit !== 'success') {
				$this->toaster->error('Cash deposit failed.');
				redirect($_SERVER['HTTP_REFERER']);
				return;
			}
			$txn = $this->Account_model->transfer_funds(
				$loan_account->loan_number, $recepientt->account_number, $amount, $tid, $paid_date, $unique_name
			);
			if ($txn !== 'success') {
				$this->toaster->error('Transfer to collection account failed.');
				redirect($_SERVER['HTTP_REFERER']);
				return;
			}
		}

		// Execute the payoff
		$settled = $this->Payement_schedules_model->payoff_loan($loan_id, $amount, $paid_date);
		if (!$settled) {
			log_message('error', 'Early settlement DB failure: loan_id=' . $loan_id . ', tid=' . $tid);
			$this->toaster->error('Payment recorded but loan closure failed. Contact support with reference: ' . $tid);
			redirect($_SERVER['HTTP_REFERER']);
			return;
		}

		$this->toaster->success('Loan settled successfully. All remaining schedules have been closed and interest waived.');
		redirect($_SERVER['HTTP_REFERER']);
	}

	/**
	 * Compute bullet loan payoff with compound interest on arrears.
	 *
	 * Before maturity: simple interest on principal (principal × rate × months elapsed).
	 * After maturity:  interest compounds monthly on OUTSTANDING BALANCE (after payments).
	 *   - At maturity the total owed = principal + (principal × rate × term)
	 *   - Payments reduce the outstanding balance BEFORE compounding
	 *   - Each full month past maturity: balance = balance × (1 + rate)
	 *   - Remaining days: daily pro-rata on the current compounded balance
	 */
	private function _compute_bullet_payoff($loan_id, $payoff_date)
	{
		$loan = $this->Loan_model->get_by_id($loan_id);

		// Get all unpaid schedules to find total payments already made
		$unpaid_schedules = $this->Payement_schedules_model->get_unpaid_schedules($loan_id);
		$amount_paid = 0;
		foreach ($unpaid_schedules as $schedule) {
			$amount_paid += $schedule->paid_amount;
		}

		// Basic loan parameters
		$principal         = floatval($loan->loan_principal);
		$monthly_rate      = $loan->loan_interest / 100; // e.g. 10% = 0.10
		$term              = intval($loan->loan_period);  // months

		// Dates
		$loan_date_obj     = new DateTime($loan->loan_date);
		$payoff_date_obj   = new DateTime($payoff_date);
		$maturity_date_obj = clone $loan_date_obj;
		$maturity_date_obj->modify("+{$term} months");

		// Interest at maturity (simple interest for the original term)
		$original_interest = $principal * $monthly_rate * $term;
		$maturity_total    = $principal + $original_interest;

		$accrued_interest        = 0;
		$calculation_explanation = '';
		$days_past_maturity      = 0;
		$full_months_past        = 0;
		$remaining_days          = 0;
		$running_balance         = 0;
		$outstanding_at_maturity = 0;
		$total_days_elapsed      = 0;
		$daily_rate              = 0;

		if ($payoff_date_obj <= $maturity_date_obj) {
			// ── BEFORE OR AT MATURITY ──
			// Month 1 (days 1–30): always 1 full month flat.
			// Month 2+ (day 31+): whole months at monthly rate + daily accrual for remaining days.
			$total_days_elapsed = max(1, $loan_date_obj->diff($payoff_date_obj)->days);
			$daily_rate         = $monthly_rate / 30;
			$full_months        = max(1, (int) floor($total_days_elapsed / 30));
			if ($full_months > $term) $full_months = $term;
			$extra_days         = max(0, $total_days_elapsed - ($full_months * 30));

			$accrued_interest = round(
				$principal * $monthly_rate * $full_months + $principal * $daily_rate * $extra_days,
				2
			);

			// Cap at full-term interest
			$max_interest = round($principal * $monthly_rate * $term, 2);
			if ($accrued_interest > $max_interest) $accrued_interest = $max_interest;

			$calculation_explanation = "Before maturity (term = {$term} months):\n" .
				"Days elapsed: {$total_days_elapsed}\n" .
				"Full months: {$full_months}, Extra days: {$extra_days}\n" .
				"Interest = ({$principal} x {$monthly_rate} x {$full_months}) + ({$principal} x {$daily_rate} x {$extra_days}) = {$accrued_interest}\n";

			// Determine remaining balances after any payments made
			$remaining_principal    = $principal;
			$real_interest_balance  = $accrued_interest;

			if ($amount_paid > 0) {
				if ($amount_paid >= $accrued_interest) {
					$real_interest_balance  = 0;
					$remaining_principal    = $principal - ($amount_paid - $accrued_interest);
					if ($remaining_principal < 0) $remaining_principal = 0;
				} else {
					$real_interest_balance = $accrued_interest - $amount_paid;
				}
				$calculation_explanation .= "\nPayments made: " . number_format($amount_paid, 2) . "\n" .
					"Interest balance: " . number_format($real_interest_balance, 2) . "\n" .
					"Principal balance: " . number_format($remaining_principal, 2) . "\n";
			}

			$total_payoff = round($remaining_principal + $real_interest_balance, 2);

		} else {
			// ── AFTER MATURITY – COMPOUND INTEREST ON OUTSTANDING BALANCE ──
			$days_past_maturity = $maturity_date_obj->diff($payoff_date_obj)->days;
			$full_months_past   = floor($days_past_maturity / 30);
			$remaining_days     = $days_past_maturity % 30;

			// Calculate outstanding balance at maturity (after payments)
			// Payments reduce the balance BEFORE interest compounds
			$outstanding_at_maturity = $maturity_total - $amount_paid;
			if ($outstanding_at_maturity < 0) $outstanding_at_maturity = 0;

			$calculation_explanation = "After maturity – compound interest on OUTSTANDING balance:\n" .
				"Principal: " . number_format($principal, 2) . "\n" .
				"Original interest ({$term} months): " . number_format($original_interest, 2) . "\n" .
				"Total at maturity: " . number_format($maturity_total, 2) . "\n" .
				"Payments made: " . number_format($amount_paid, 2) . "\n" .
				"Outstanding balance at maturity: " . number_format($outstanding_at_maturity, 2) . "\n" .
				"Days past maturity: {$days_past_maturity} ({$full_months_past} months + {$remaining_days} days)\n\n";

			// Start compounding from outstanding balance
			$running_balance = $outstanding_at_maturity;

			// Compound for each full month past maturity
			for ($m = 1; $m <= $full_months_past; $m++) {
				$month_interest = round($running_balance * $monthly_rate, 2);
				$balance_before = $running_balance;
				$running_balance += $month_interest;
				$calculation_explanation .= "Month {$m} arrears: " . number_format($balance_before, 2) .
					" x " . ($monthly_rate * 100) . "% = " . number_format($month_interest, 2) .
					" → Balance: " . number_format($running_balance, 2) . "\n";
			}

			// Pro-rate remaining days
			if ($remaining_days > 0) {
				$daily_interest = round(($running_balance * $monthly_rate) / 30, 2);
				$partial_interest = round($daily_interest * $remaining_days, 2);
				$running_balance += $partial_interest;
				$calculation_explanation .= "Remaining {$remaining_days} days: " . number_format($daily_interest, 2) .
					"/day x {$remaining_days} = " . number_format($partial_interest, 2) .
					" → Balance: " . number_format($running_balance, 2) . "\n";
			}

			$running_balance = round($running_balance, 2);

			// Calculate how much of the outstanding is principal vs interest
			// After payments, we track remaining principal separately
			$principal_paid = max(0, $amount_paid - $original_interest);
			$remaining_principal = $principal - $principal_paid;
			if ($remaining_principal < 0) $remaining_principal = 0;

			// Total interest accrued = current balance - remaining principal
			$accrued_interest = $running_balance - $remaining_principal;
			if ($accrued_interest < 0) $accrued_interest = 0;

			$real_interest_balance = $accrued_interest;
			$total_payoff = $running_balance;
		}

		// Calculate arrears interest (compound interest portion past maturity)
		$arrears_interest = ($days_past_maturity > 0 && $outstanding_at_maturity > 0)
			? round($running_balance - $outstanding_at_maturity, 2)
			: 0;

		return [
			'status'                   => 'success',
			'payoff_amount'            => number_format($total_payoff, 2, '.', ''),
			'current_balance'          => number_format($remaining_principal, 2, '.', ''),
			'accrued_interest'         => number_format($accrued_interest, 2, '.', ''),
			'accrued_interest_balance' => number_format($real_interest_balance, 2, '.', ''),
			'total_payoff'             => number_format($total_payoff, 2, '.', ''),
			// Additional fields for JavaScript breakdown display
			'principal'                => number_format($principal, 2, '.', ''),
			'maturity_total'           => number_format($maturity_total, 2, '.', ''),
			'outstanding_at_maturity'  => number_format($outstanding_at_maturity, 2, '.', ''),
			'arrears_interest'         => number_format($arrears_interest, 2, '.', ''),
			'amount_paid'              => number_format($amount_paid, 2, '.', ''),
			'days_past_maturity'       => $days_past_maturity,
			'full_months_past'         => $full_months_past,
			'remaining_days'           => $remaining_days,
			'monthly_rate'             => $monthly_rate,
			'maturity_date'            => $maturity_date_obj->format('Y-m-d'),
			'debug' => [
				'loan_id'                => $loan_id,
				'payoff_date'            => $payoff_date,
				'loan_date'              => $loan->loan_date,
				'maturity_date'          => $maturity_date_obj->format('Y-m-d'),
				'loan_principal'         => $principal,
				'monthly_interest_rate'  => $monthly_rate,
				'term_months'            => $term,
				'original_interest'      => $original_interest,
				'maturity_total'         => $maturity_total,
				'amount_paid'            => $amount_paid,
				'days_past_maturity'     => $days_past_maturity,
				'full_months_arrears'    => $full_months_past,
				'remaining_days'         => $remaining_days,
				'daily_interest'         => $days_past_maturity > 0
					? round(($running_balance * $monthly_rate) / 30, 2)
					: round($principal * $daily_rate, 2),
				'remaining_principal'    => $remaining_principal,
				'calculation_explanation'=> $calculation_explanation,
				'elapsed_days'           => $days_past_maturity > 0 ? $days_past_maturity : $total_days_elapsed
			]
		];
	}

	private function _compute_non_bullet_payoff($loan_id, $payoff_date)
	{
		$loan      = $this->Loan_model->get_by_id($loan_id);
		$schedules = $this->Payement_schedules_model->get_all_by_id($loan_id);

		$payoff_date_obj = new DateTime($payoff_date);

		// Sort schedules by payment_number ascending
		usort($schedules, function($a, $b) {
			return intval($a->payment_number) - intval($b->payment_number);
		});

		$total_payoff            = 0;
		$amount_due_total        = 0;  // started periods only
		$total_accrued_interest  = 0;
		$remaining_principal     = 0;
		$interest_waived         = 0;
		$daily_interest          = 0;
		$days_elapsed            = 0;
		$explanation             = '';

		foreach ($schedules as $s) {
			if ($s->status === 'PAID') continue;

			$remaining_principal += floatval($s->principal);

			// Period start = previous schedule's due date, or loan_date for period 1
			$period_start_obj = null;
			foreach ($schedules as $prev) {
				if (intval($prev->payment_number) === intval($s->payment_number) - 1) {
					$period_start_obj = new DateTime($prev->payment_schedule);
					break;
				}
			}
			if (!$period_start_obj) {
				$period_start_obj = new DateTime($loan->loan_date);
			}

			$due_date_obj = new DateTime($s->payment_schedule);

			if ($payoff_date_obj <= $period_start_obj) {
				// Future period — principal only, interest waived (early settlement benefit)
				$total_payoff    += floatval($s->principal);
				$interest_waived += floatval($s->interest);
				continue;
			}

			// Period has started
			$period_interest = floatval($s->interest);
			$daily_rate      = $period_interest / 30;

			if ($payoff_date_obj <= $due_date_obj) {
				// On or before the due date — charge the full scheduled interest (first-month flat rule)
				// This ensures the modal matches the amortization schedule exactly on the due date
				$accrued      = $period_interest;
				$days_elapsed = $period_start_obj->diff($payoff_date_obj)->days;
				$explanation .= "Period {$s->payment_number} (due {$due_date_obj->format('Y-m-d')}): within period, flat interest=" . number_format($accrued, 2) . "\n";
			} else {
				// Past the due date — scheduled interest + daily penalty for overdue days
				$days_overdue = $due_date_obj->diff($payoff_date_obj)->days;
				$accrued      = round($period_interest + $daily_rate * $days_overdue, 2);
				$days_elapsed = $days_overdue;
				$explanation .= "Period {$s->payment_number} (due {$due_date_obj->format('Y-m-d')}): {$days_overdue} days overdue, " .
					"interest=" . number_format($accrued, 2) . " (sched=" . number_format($period_interest, 2) . " + daily=" . number_format($daily_rate * $days_overdue, 2) . ")\n";
			}

			$amount_due = max(0, floatval($s->principal) + $accrued - floatval($s->paid_amount));
			$total_payoff           += $amount_due;
			$amount_due_total       += $amount_due;
			$total_accrued_interest += $accrued;
			$daily_interest          = $daily_rate;
		}

		$total_payoff     = round($total_payoff, 2);
		$amount_due_total = round($amount_due_total, 2);

		return [
			'status'           => 'success',
			'current_balance'  => number_format($remaining_principal,    2, '.', ''),
			'accrued_interest' => number_format($total_accrued_interest, 2, '.', ''),
			'amount_due'       => number_format($amount_due_total,       2, '.', ''),
			'total_payoff'     => number_format($total_payoff,           2, '.', ''),
			'payoff_amount'    => number_format($total_payoff,           2, '.', ''),
			'debug' => [
				'loan_date'               => $loan->loan_date,
				'payoff_date'             => $payoff_date,
				'calculation_type'        => $loan->calculation_type,
				'total_accrued_interest'  => $total_accrued_interest,
				'interest_waived'         => $interest_waived,
				'amount_due'              => $amount_due_total,
				'elapsed_days'            => $days_elapsed,
				'daily_interest'          => round($daily_interest, 2),
				'monthly_interest_rate'   => floatval($loan->loan_interest) / 100,
				'calculation_explanation' => $explanation,
			]
		];
	}

	private function _process_non_bullet_payment($loan_id, $amount_paid, $paid_date, $tid = '')
	{
		$payoff = $this->_compute_non_bullet_payoff($loan_id, $paid_date);
		if ($payoff['status'] !== 'success') return false;

		$total_payoff    = floatval($payoff['total_payoff']);
		$amount_paid     = floatval($amount_paid);
		$tolerance       = 0.01;
		$is_full_payoff  = ($amount_paid >= $total_payoff - $tolerance);

		$loan      = $this->Loan_model->get_by_id($loan_id);
		$schedules = $this->Payement_schedules_model->get_all_by_id($loan_id);
		usort($schedules, function($a, $b) {
			return intval($a->payment_number) - intval($b->payment_number);
		});

		$payoff_date_obj = new DateTime($paid_date);
		$remaining       = $amount_paid;
		$last_pay_num    = 1;
		$allocations     = array(); // payment_number => amount_applied

		// Returns the period-start DateTime for a given schedule row
		$get_period_start = function($s) use ($schedules, $loan) {
			foreach ($schedules as $prev) {
				if (intval($prev->payment_number) === intval($s->payment_number) - 1) {
					return new DateTime($prev->payment_schedule);
				}
			}
			return new DateTime($loan->loan_date);
		};

		// Returns what is owed for one schedule row as of payoff_date, minus already-paid
		$get_period_due = function($s) use ($payoff_date_obj, $get_period_start) {
			$period_start_obj = $get_period_start($s);
			if ($payoff_date_obj <= $period_start_obj) {
				// Future period: principal only (interest waived)
				return max(0, floatval($s->principal) - floatval($s->paid_amount));
			}
			$due_date_obj    = new DateTime($s->payment_schedule);
			$period_interest = floatval($s->interest);
			if ($payoff_date_obj <= $due_date_obj) {
				$accrued = $period_interest;  // flat scheduled interest within period
			} else {
				$days_overdue = $due_date_obj->diff($payoff_date_obj)->days;
				$accrued      = round($period_interest + ($period_interest / 30) * $days_overdue, 2);
			}
			return max(0, floatval($s->principal) + $accrued - floatval($s->paid_amount));
		};

		// Returns the interest accrued for one schedule as of payoff_date (0 for a period
		// that has not started — interest not yet earned). Used to attribute a payment
		// between interest (charged per contract) and principal (reduced by surplus).
		$get_period_accrued = function($s) use ($payoff_date_obj, $get_period_start) {
			$period_start_obj = $get_period_start($s);
			if ($payoff_date_obj <= $period_start_obj) {
				return 0.0; // future period — interest not yet accrued
			}
			$due_date_obj    = new DateTime($s->payment_schedule);
			$period_interest = floatval($s->interest);
			if ($payoff_date_obj <= $due_date_obj) {
				return $period_interest; // within period — flat scheduled interest
			}
			$days_overdue = $due_date_obj->diff($payoff_date_obj)->days;
			return round($period_interest + ($period_interest / 30) * $days_overdue, 2);
		};

		foreach ($schedules as $s) {
			if ($s->status === 'PAID') continue;

			$period_due = $get_period_due($s);

			if ($is_full_payoff) {
				// Mark every unpaid schedule as PAID with its calculated due amount.
				// Full settlement => the whole principal portion is paid.
				$this->db->where('loan_id', $loan_id)
					->where('payment_number', $s->payment_number)
					->update('payement_schedules', [
						'status'         => 'PAID',
						'partial_paid'   => 'NO',
						'paid_amount'    => floatval($s->paid_amount) + $period_due,
						'principal_paid' => floatval($s->principal),
						'paid_date'      => $paid_date,
					]);
				$allocations[$s->payment_number] = $period_due;
				$last_pay_num = $s->payment_number;
				continue;
			}

			// Partial / cascade path
			if ($remaining <= 0) break;

			if ($remaining >= $period_due - $tolerance) {
				// Covers this period's due amount => the whole principal portion is paid.
				// A schedule is only fully PAID when its contract interest is also covered.
				// When surplus prepays a FUTURE schedule's principal (interest not yet
				// accrued), the schedule stays PARTIAL PAID so its interest is still
				// collected per contract when it later falls due.
				$new_paid      = floatval($s->paid_amount) + $period_due;
				$fully_settled = ($new_paid >= floatval($s->amount) - $tolerance);
				$this->db->where('loan_id', $loan_id)
					->where('payment_number', $s->payment_number)
					->update('payement_schedules', [
						'status'         => $fully_settled ? 'PAID' : 'PARTIAL PAID',
						'partial_paid'   => $fully_settled ? 'NO' : 'YES',
						'paid_amount'    => $new_paid,
						'principal_paid' => floatval($s->principal),
						'paid_date'      => $paid_date,
					]);
				if ($fully_settled) {
					$this->db->where('loan_id', $loan_id)
						->update('loan', ['next_payment_id' => intval($s->payment_number) + 1]);
				}
				$allocations[$s->payment_number] = $period_due;
				$remaining   -= $period_due;
				$last_pay_num = $s->payment_number;
			} else {
				// Partial payment on this period.
				// Attribution rule: settle outstanding (accrued) interest first, the
				// remainder reduces principal. For a future schedule the accrued
				// interest is 0, so the whole surplus reduces principal.
				$accrued              = $get_period_accrued($s);
				$interest_outstanding = max(0, $accrued - (floatval($s->paid_amount) - floatval($s->principal_paid)));
				$principal_portion    = max(0, $remaining - $interest_outstanding);
				$new_principal_paid   = min(floatval($s->principal), floatval($s->principal_paid) + $principal_portion);

				$this->db->where('loan_id', $loan_id)
					->where('payment_number', $s->payment_number)
					->update('payement_schedules', [
						'status'         => 'PARTIAL PAID',
						'partial_paid'   => 'YES',
						'paid_amount'    => floatval($s->paid_amount) + $remaining,
						'principal_paid' => $new_principal_paid,
						'paid_date'      => $paid_date,
					]);
				$allocations[$s->payment_number] = $remaining;
				$last_pay_num = $s->payment_number;
				$remaining    = 0;
				break;
			}
		}

		if ($is_full_payoff) {
			$this->db->where('loan_id', $loan_id)->update('loan', [
				'loan_status'     => 'CLOSED',
				'paid_off'        => 'Yes',
				'next_payment_id' => $last_pay_num + 1,
			]);
		}

		// Record one row per schedule allocation so get_transaction_usage can read it back
		foreach ($allocations as $pay_num => $applied_amount) {
			$this->db->insert('transactions', [
				'ref'              => $tid,
				'loan_id'          => $loan_id,
				'amount'           => $applied_amount,
				'payment_number'   => $pay_num,
				'transaction_type' => 3,
				'payment_proof'    => 'null',
				'added_by'         => $this->session->userdata('user_id'),
				'date_stamp'       => $paid_date,
			]);
		}

		return $is_full_payoff ? 'closed' : 'paid';
	}

	/**
	 * Process loan pay-off
	 */
	public function pay_off_loan() {
		$loan_id = $this->input->post('loan_id');
		$payoff_amount = $this->input->post('payoff_amount');
		$payoff_date = $this->input->post('payoff_date');
		$payment_method = $this->input->post('payment_method');
		$payment_number = $this->input->post('payment_number');
		$reference = $this->input->post('reference');

		// Initialize upload for payment proof if any
		$unique_name = "";
		if (!empty($_FILES['pay_proof']['name'])) {
			$config['upload_path'] = './uploads/';
			$config['allowed_types'] = 'jpg|png|jpeg|gif|pdf|docx|txt|zip';
			$config['max_size'] = 2048; // 2MB
			$config['remove_spaces'] = TRUE;

			$this->load->library('upload', $config);

			$file_name = pathinfo($_FILES['pay_proof']['name'], PATHINFO_FILENAME);
			$file_ext = pathinfo($_FILES['pay_proof']['name'], PATHINFO_EXTENSION);

			// Generate a unique file name
			$unique_name = 'file_' . time() . '_' . uniqid() . '.' . $file_ext;
			$config['file_name'] = $unique_name;

			// Reinitialize with new config
			$this->upload->initialize($config);

			if (!$this->upload->do_upload('pay_proof')) {
				// Upload failed, but continue processing
			}
		}

		// Get loan details
		$loan = $this->Loan_model->get_by_id($loan_id);
		if (!$loan) {
			$this->toaster->error('Error: Loan not found.');
			redirect($_SERVER['HTTP_REFERER']);
			return;
		}

		// Transaction reference
		$tid = "PAYOFF-" . rand(1000, 9999) . date('Ymd');

		// Get collection account
		$recepientt = get_by_id('account', 'collection_account', 'Yes');
		if (!$recepientt) {
			$this->toaster->error('Error: Collection account not set up.');
			redirect($_SERVER['HTTP_REFERER']);
			return;
		}

		// Process payment based on method
		if ($payment_method == "0") {
			// Payment from loan account savings
			$check = $this->Account_model->get_account($loan->loan_number);
			if (!$check) {
				$this->toaster->error('Error: Loan account not found.');
				redirect($_SERVER['HTTP_REFERER']);
				return;
			}

			if ($check->balance < $payoff_amount) {
				$this->toaster->error('Error: Insufficient funds in loan account.');
				redirect($_SERVER['HTTP_REFERER']);
				return;
			}

			// Transfer funds from loan account to collection account
			$transfer_result = $this->Account_model->transfer_funds(
				$loan->loan_number,
				$recepientt->account_number,
				$payoff_amount,
				$tid,
				$payoff_date,
				$unique_name
			);

			if ($transfer_result != 'success') {
				$this->toaster->error('Error: Fund transfer failed.');
				redirect($_SERVER['HTTP_REFERER']);
				return;
			}
		} else {
			// Payment via teller/cashier
			$get_account = $this->Tellering_model->get_teller_account($this->session->userdata('user_id'));
			if (empty($get_account)) {
				$this->toaster->error('Error: You are not authorized to process this payment. Only cashiers can do this.');
				redirect($_SERVER['HTTP_REFERER']);
				return;
			}

			// Make cash deposit and then transfer
			$teller_account = $get_account->account;
			$mode = 'deposit';

			// First deposit to loan account
			$deposit_result = $this->Account_model->cash_transaction(
				$teller_account,
				$loan->loan_number,
				$payoff_amount,
				$mode,
				$tid,
				$payoff_date,
				$unique_name
			);

			if (!$deposit_result) {
				$this->toaster->error('Error: Deposit to loan account failed.');
				redirect($_SERVER['HTTP_REFERER']);
				return;
			}

			// Then transfer to collection account
			$transfer_result = $this->Account_model->transfer_funds(
				$loan->loan_number,
				$recepientt->account_number,
				$payoff_amount,
				$tid,
				$payoff_date,
				$unique_name
			);

			if ($transfer_result != 'success') {
				$this->toaster->error('Error: Fund transfer failed.');
				redirect($_SERVER['HTTP_REFERER']);
				return;
			}
		}

		// Mark all unpaid schedules as paid
		$unpaid_schedules = $this->Payement_schedules_model->get_unpaid_schedules($loan_id);
		foreach ($unpaid_schedules as $schedule) {
			$data = array(
				'status' => 'PAID',
				'paid_amount' => $schedule->amount,
				'paid_date' => $payoff_date
			);

			$this->Payement_schedules_model->update($schedule->id, $data);
		}

		// Update loan status to CLOSED
		$this->Loan_model->update($loan_id, array(
			'loan_status' => 'CLOSED',
			'closed_date' => $payoff_date,
			'closed_by' => $this->session->userdata('user_id'),
			'closing_notes' => 'Loan paid off in full on ' . $payoff_date
		));

		// Record transaction
		$transaction_data = array(
			'ref' => $tid,
			'loan_id' => $loan_id,
			'amount' => $payoff_amount,
			'transaction_type' => 4, // Payoff transaction type
			'payment_number' => 0,
			'date_stamp' => $payoff_date,
			'method' => $payment_method,
			'payment_proof' => $unique_name,
			'reference' => $reference,
			'added_by' => $this->session->userdata('user_id')
		);
		$this->Transactions_model->insert($transaction_data);

		// Log activity
		$logger = array(
			'user_id' => $this->session->userdata('user_id'),
			'activity' => 'Completed loan payoff for loan ID: ' . $loan_id . ' (Loan #: ' . $loan->loan_number . ') with amount ' . $payoff_amount,
			'activity_cate' => 'loan_payoff'
		);
		log_activity($logger);

		$this->toaster->success('Success: Loan has been paid off and closed.');
		redirect('loan/repayment_view/' . $loan_id);
	}

	public function force_close_loan()
	{
		$loan_id        = (int) $this->input->post('loan_id');
		$amount         = (float) $this->input->post('amount');
		$reason         = trim($this->input->post('reason'));
		$paid_date      = $this->input->post('paid_date');
		$payment_method = $this->input->post('payment_method');
		$reference      = $this->input->post('reference');

		if (empty($loan_id)) {
			$this->toaster->error('Error: Invalid loan ID.');
			redirect($_SERVER['HTTP_REFERER']);
			return;
		}

		// Load and validate loan
		$loan = $this->Loan_model->get_by_id($loan_id);
		if (!$loan) {
			$this->toaster->error('Error: Loan not found.');
			redirect($_SERVER['HTTP_REFERER']);
			return;
		}
		if ($loan->loan_status === 'CLOSED' || $loan->loan_status === 'WRITTEN_OFF') {
			$this->toaster->error('Error: This loan is already ' . $loan->loan_status . '.');
			redirect($_SERVER['HTTP_REFERER']);
			return;
		}

		// Amount must be at least the remaining (unpaid) principal balance.
		// For partial rows we cap the principal at the row's outstanding balance to avoid overstating it.
		$unpaid_for_min = $this->Payement_schedules_model->get_unpaid_schedules($loan_id);
		$remaining_principal_min = 0;
		foreach ($unpaid_for_min as $s) {
			if ($s->partial_paid == 'YES') {
				$row_outstanding = $s->amount - $s->paid_amount;
				$remaining_principal_min += min((float) $s->principal, (float) $row_outstanding);
			} else {
				$remaining_principal_min += (float) $s->principal;
			}
		}
		if ($amount < $remaining_principal_min) {
			$this->toaster->error('Error: Amount must be at least the remaining principal balance (' . number_format($remaining_principal_min, 2) . ').');
			redirect($_SERVER['HTTP_REFERER']);
			return;
		}

		// Reason is mandatory
		if (empty($reason)) {
			$this->toaster->error('Error: A reason for manual closure is required.');
			redirect($_SERVER['HTTP_REFERER']);
			return;
		}

		// Handle optional proof-of-payment upload
		$unique_name = "";
		if (!empty($_FILES['pay_proof']['name'])) {
			$config['upload_path']   = './uploads/';
			$config['allowed_types'] = 'jpg|png|jpeg|gif|pdf|docx|txt|zip';
			$config['max_size']      = 2048;
			$config['remove_spaces'] = TRUE;
			$this->load->library('upload', $config);
			$file_ext    = pathinfo($_FILES['pay_proof']['name'], PATHINFO_EXTENSION);
			$unique_name = 'file_' . time() . '_' . uniqid() . '.' . $file_ext;
			$config['file_name'] = $unique_name;
			$this->upload->initialize($config);
			$this->upload->do_upload('pay_proof'); // Upload failure is non-fatal; continue without proof
		}

		// Get collection account
		$recepientt = get_by_id('account', 'collection_account', 'Yes');
		if (!$recepientt) {
			$this->toaster->error('Error: Collection account not configured.');
			redirect($_SERVER['HTTP_REFERER']);
			return;
		}

		$tid = "FCL-" . rand(1000, 9999) . date('Ymd');

		// Process fund movement
		if ($payment_method == "0") {
			// From loan savings account
			$check = $this->Account_model->get_account($loan->loan_number);
			if (!$check || $check->balance < $amount) {
				$this->toaster->error('Error: Insufficient funds in loan account.');
				redirect($_SERVER['HTTP_REFERER']);
				return;
			}
			$transfer = $this->Account_model->transfer_funds(
				$loan->loan_number, $recepientt->account_number,
				$amount, $tid, $paid_date, $unique_name
			);
			if ($transfer != 'success') {
				$this->toaster->error('Error: Fund transfer failed.');
				redirect($_SERVER['HTTP_REFERER']);
				return;
			}
		} else {
			// Cash via teller
			$get_account = $this->Tellering_model->get_teller_account($this->session->userdata('user_id'));
			if (empty($get_account)) {
				$this->toaster->error('Error: Only cashiers can process this payment.');
				redirect($_SERVER['HTTP_REFERER']);
				return;
			}
			$deposit = $this->Account_model->cash_transaction(
				$get_account->account, $loan->loan_number,
				$amount, 'deposit', $tid, $paid_date, $unique_name
			);
			if (!$deposit) {
				$this->toaster->error('Error: Deposit to loan account failed.');
				redirect($_SERVER['HTTP_REFERER']);
				return;
			}
			$transfer = $this->Account_model->transfer_funds(
				$loan->loan_number, $recepientt->account_number,
				$amount, $tid, $paid_date, $unique_name
			);
			if ($transfer != 'success') {
				$this->toaster->error('Error: Fund transfer failed.');
				redirect($_SERVER['HTTP_REFERER']);
				return;
			}
		}

		// Force-settle all outstanding schedules in one bulk query.
		// Avoids Payement_schedules_model::update() which is broken ($this->id = '').
		// Condition mirrors get_unpaid_schedules(): NOT PAID or partial_paid = YES.
		// paid_amount=0: view uses $pp->amount for PAID rows so balance displays as 0 correctly.
		$this->db->where('loan_id', $loan_id)
			->group_start()
			->where('status', 'NOT PAID')
			->or_where('partial_paid', 'YES')
			->group_end()
			->update('payement_schedules', array(
				'status'      => 'PAID',
				'paid_amount' => 0,
				'paid_date'   => $paid_date,
			));

		// Close the loan (only columns confirmed in schema; reason is captured in activity log above)
		$this->Loan_model->update($loan_id, array(
			'loan_status' => 'CLOSED',
		));

		// Record transaction (type 5 = forced close, distinct from type 4 = normal payoff)
		$this->Transactions_model->insert(array(
			'ref'              => $tid,
			'loan_id'          => $loan_id,
			'amount'           => $amount,
			'transaction_type' => 5,
			'payment_number'   => 0,
			'date_stamp'       => $paid_date,
			'method'           => $payment_method,
			'payment_proof'    => $unique_name,
			'reference'        => $reference,
			'added_by'         => $this->session->userdata('user_id'),
		));

		// Audit log with full detail including the reason
		log_activity(array(
			'user_id'       => $this->session->userdata('user_id'),
			'activity'      => 'Forced close loan ID: ' . $loan_id . ' (Loan #: ' . $loan->loan_number . '), amount: ' . $amount . ', reason: ' . $reason,
			'activity_cate' => 'force_close_loan',
		));

		$this->toaster->success('Loan has been manually closed.');
		redirect('loan/repayment_view/' . $loan_id);
	}

	/**
	 * Upload files for a loan via AJAX
	 */
	public function upload_files() {
		header('Content-Type: application/json');

		$loan_id = $this->input->post('loan_id');
		$loan_number = $this->input->post('loan_number');

		if (empty($loan_id) || empty($loan_number)) {
			echo json_encode(array('status' => 'error', 'message' => 'Missing loan information'));
			return;
		}

		// Create upload directory if it doesn't exist
		$upload_path = FCPATH . 'uploads/' . $loan_number . '/';
		if (!is_dir($upload_path)) {
			mkdir($upload_path, 0755, true);
		}

		$uploaded_files = array();

		// Check if files were uploaded
		if (!empty($_FILES['files']['name'][0])) {
			$file_count = count($_FILES['files']['name']);

			for ($i = 0; $i < $file_count; $i++) {
				$file_name = $_FILES['files']['name'][$i];
				$file_tmp = $_FILES['files']['tmp_name'][$i];
				$file_error = $_FILES['files']['error'][$i];

				if ($file_error === UPLOAD_ERR_OK) {
					// Generate unique filename
					$file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
					$new_file_name = time() . '_' . $i . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $file_name);
					$file_path = $upload_path . $new_file_name;

					if (move_uploaded_file($file_tmp, $file_path)) {
						// Save file record to database
						$file_data = array(
							'file_name' => $file_name,
							'real_file' => $loan_number . '/' . $new_file_name,
							'loan_id' => $loan_id,
							'file_stamp' => date('Y-m-d H:i:s')
						);

						$this->Loan_files_model->insert($file_data);
						$insert_id = $this->db->insert_id();

						$uploaded_files[] = array(
							'id' => $insert_id,
							'name' => $file_name,
							'path' => $loan_number . '/' . $new_file_name
						);
					}
				}
			}

			if (!empty($uploaded_files)) {
				// Log activity
				$logger = array(
					'user_id' => $this->session->userdata('user_id'),
					'activity' => 'Uploaded ' . count($uploaded_files) . ' file(s) to loan #' . $loan_number,
					'activity_cate' => 'loan_file_upload'
				);
				log_activity($logger);

				echo json_encode(array(
					'status' => 'success',
					'message' => 'Files uploaded successfully',
					'files' => $uploaded_files
				));
			} else {
				echo json_encode(array('status' => 'error', 'message' => 'Failed to upload files'));
			}
		} else {
			echo json_encode(array('status' => 'error', 'message' => 'No files selected'));
		}
	}

	/**
	 * Delete a loan file via AJAX
	 */
	public function delete_file() {
		header('Content-Type: application/json');

		$file_id = $this->input->post('file_id');

		if (empty($file_id)) {
			echo json_encode(array('status' => 'error', 'message' => 'Missing file ID'));
			return;
		}

		// Get file info before deleting
		$file = $this->Loan_files_model->get_by_id($file_id);

		if (empty($file)) {
			echo json_encode(array('status' => 'error', 'message' => 'File not found'));
			return;
		}

		// Delete physical file
		$file_path = FCPATH . 'uploads/' . $file->real_file;
		if (file_exists($file_path)) {
			unlink($file_path);
		}

		// Delete from database
		$this->Loan_files_model->delete($file_id);

		// Log activity
		$logger = array(
			'user_id' => $this->session->userdata('user_id'),
			'activity' => 'Deleted file: ' . $file->file_name . ' from loan',
			'activity_cate' => 'loan_file_delete'
		);
		log_activity($logger);

		echo json_encode(array(
			'status' => 'success',
			'message' => 'File deleted successfully'
		));
	}

	/**
	 * Add a note to a loan
	 */
	public function add_note() {
		header('Content-Type: application/json');

		$loan_id = $this->input->post('loan_id');
		$notes = $this->input->post('notes');
		$user_id = $this->session->userdata('user_id');

		if (empty($loan_id) || empty($notes)) {
			echo json_encode(array(
				'status' => 'error',
				'message' => 'Loan ID and notes are required'
			));
			return;
		}

		$data = array(
			'loan_id' => $loan_id,
			'notes' => $notes,
			'notes_by' => $user_id,
			'datetime' => date('Y-m-d H:i:s')
		);

		$note_id = $this->Loan_notes_model->insert($data);

		if ($note_id) {
			// Get user info for response
			$user = get_by_id('employees', 'id', $user_id);
			$user_name = $user ? $user->Firstname . ' ' . $user->Lastname : 'Unknown';

			// Log activity
			$logger = array(
				'user_id' => $user_id,
				'activity' => 'Added note to loan ID: ' . $loan_id,
				'activity_cate' => 'loan_note_add'
			);
			log_activity($logger);

			// Get loan details for notification
			$loan = $this->Loan_model->get_by_id($loan_id);
			if ($loan) {
				// Get customer name
				$customer_name = 'Unknown';
				if ($loan->customer_type == 'individual') {
					$customer = $this->Individual_customers_model->get_by_id($loan->loan_customer);
					if ($customer) {
						$customer_name = $customer->Firstname . ' ' . $customer->Lastname;
					}
				} elseif ($loan->customer_type == 'group') {
					$group = $this->Groups_model->get_by_id($loan->loan_customer);
					if ($group) {
						$customer_name = $group->group_name;
					}
				} elseif ($loan->customer_type == 'institution') {
					$inst = get_by_id('corporate_customers', 'id', $loan->loan_customer);
					if ($inst) {
						$customer_name = $inst->EntityName;
					}
				}

				// Send email notification to stakeholders
				$loan_data = array(
					'loan_id' => $loan_id,
					'loan_number' => $loan->loan_number,
					'customer_name' => $customer_name
				);
				notify_loan_note_added($loan_data, $notes, $user_id);
			}

			echo json_encode(array(
				'status' => 'success',
				'message' => 'Note added successfully',
				'note' => array(
					'note_id' => $note_id,
					'notes' => $notes,
					'notes_by' => $user_name,
					'datetime' => date('d M Y H:i')
				)
			));
		} else {
			echo json_encode(array(
				'status' => 'error',
				'message' => 'Failed to add note'
			));
		}
	}

	/**
	 * Get all notes for a loan
	 */
	public function get_notes($loan_id) {
		header('Content-Type: application/json');

		if (empty($loan_id)) {
			echo json_encode(array(
				'status' => 'error',
				'message' => 'Loan ID is required'
			));
			return;
		}

		$notes = $this->Loan_notes_model->get_by_loan($loan_id);

		$formatted_notes = array();
		foreach ($notes as $note) {
			$formatted_notes[] = array(
				'note_id' => $note->note_id,
				'notes' => $note->notes,
				'notes_by' => $note->Firstname . ' ' . $note->Lastname,
				'user_id' => $note->notes_by,
				'datetime' => date('d M Y H:i', strtotime($note->datetime))
			);
		}

		echo json_encode(array(
			'status' => 'success',
			'data' => $formatted_notes,
			'count' => count($formatted_notes)
		));
	}

	/**
	 * Delete a note
	 */
	public function delete_note() {
		header('Content-Type: application/json');

		$note_id = $this->input->post('note_id');
		$user_id = $this->session->userdata('user_id');

		if (empty($note_id)) {
			echo json_encode(array(
				'status' => 'error',
				'message' => 'Note ID is required'
			));
			return;
		}

		// Get note to check ownership
		$note = $this->Loan_notes_model->get_by_id($note_id);

		if (!$note) {
			echo json_encode(array(
				'status' => 'error',
				'message' => 'Note not found'
			));
			return;
		}

		// Only allow deletion by the note creator or admin
		if ($note->notes_by != $user_id && $this->session->userdata('user_type') != 'admin') {
			echo json_encode(array(
				'status' => 'error',
				'message' => 'You can only delete your own notes'
			));
			return;
		}

		if ($this->Loan_notes_model->delete($note_id)) {
			// Log activity
			$logger = array(
				'user_id' => $user_id,
				'activity' => 'Deleted note ID: ' . $note_id,
				'activity_cate' => 'loan_note_delete'
			);
			log_activity($logger);

			echo json_encode(array(
				'status' => 'success',
				'message' => 'Note deleted successfully'
			));
		} else {
			echo json_encode(array(
				'status' => 'error',
				'message' => 'Failed to delete note'
			));
		}
	}

	// ==================== NEW COLLATERAL MANAGEMENT SYSTEM ====================

	/**
	 * Get customer collaterals (for individual customer page and loan application)
	 */
	public function get_customer_collaterals($customer_id, $customer_type = 'individual') {
		header('Content-Type: application/json');

		if (empty($customer_id)) {
			echo json_encode(array('success' => false, 'message' => 'Customer ID is required'));
			return;
		}

		$collaterals = $this->Collateral_model->get_by_customer($customer_id, $customer_type);

		$formatted = array();
		foreach ($collaterals as $c) {
			$utilized = $c->utilized_amount ?? $this->Collateral_model->get_utilized_amount($c->id);
			$available = $c->available_balance ?? max(0, ($c->force_sale_value ?? 0) - $utilized);

			$added_by_name = 'N/A';
			if (!empty($c->added_by)) {
				$added_by = get_by_id('employees', 'id', $c->added_by);
				$added_by_name = $added_by ? $added_by->Firstname . ' ' . $added_by->Lastname : 'N/A';
			}

			$formatted[] = array(
				'id' => $c->id,
				'collateral_name' => $c->collateral_name,
				'collateral_type' => $c->collateral_type,
				'collateral_serial' => $c->collateral_serial ?? '',
				'market_value' => floatval($c->market_value ?? 0),
				'force_sale_value' => floatval($c->force_sale_value ?? 0),
				'utilized_amount' => floatval($utilized),
				'available_balance' => floatval($available),
				'description' => $c->collateral_desc ?? '',
				'status' => $c->collateral_status ?? 'ACTIVE',
				'added_by' => $added_by_name,
				'added_at' => !empty($c->added_at) ? date('d M Y', strtotime($c->added_at)) : 'N/A'
			);
		}

		echo json_encode(array(
			'success' => true,
			'collaterals' => $formatted,
			'count' => count($formatted)
		));
	}

	/**
	 * Add collateral to customer
	 */
	public function add_customer_collateral() {
		header('Content-Type: application/json');

		$customer_id = $this->input->post('customer_id');
		$customer_type = $this->input->post('customer_type') ?? 'individual';
		$user_id = $this->session->userdata('user_id');

		if (empty($customer_id)) {
			echo json_encode(array('status' => 'error', 'message' => 'Customer ID is required'));
			return;
		}

		// Handle file upload
		$file_name = '';
		if (!empty($_FILES['collateral_file']['name'])) {
			$upload_path = FCPATH . 'uploads/collaterals/' . $customer_type . '_' . $customer_id . '/';
			if (!is_dir($upload_path)) {
				mkdir($upload_path, 0777, true);
			}

			$config['upload_path'] = $upload_path;
			$config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|doc|docx';
			$config['max_size'] = 10240;
			$config['file_name'] = time() . '_' . $_FILES['collateral_file']['name'];

			$this->load->library('upload', $config);

			if ($this->upload->do_upload('collateral_file')) {
				$upload_data = $this->upload->data();
				$file_name = 'collaterals/' . $customer_type . '_' . $customer_id . '/' . $upload_data['file_name'];
			}
		}

		$data = array(
			'customer_id' => $customer_id,
			'customer_type' => $customer_type,
			'collateral_name' => $this->input->post('collateral_name'),
			'collateral_type' => $this->input->post('collateral_type'),
			'collateral_serial' => $this->input->post('collateral_serial'),
			'market_value' => $this->input->post('market_value'),
			'force_sale_value' => $this->input->post('force_sale_value'),
			'collateral_desc' => $this->input->post('description'),
			'collateral_status' => 'ACTIVE',
			'location_status' => $this->input->post('location_status') ?? 'In Our Possession',
			'added_by' => $user_id,
			'added_at' => date('Y-m-d H:i:s')
		);

		$collateral_id = $this->Collateral_model->insert($data);

		if ($collateral_id) {
			$logger = array(
				'user_id' => $user_id,
				'activity' => 'Added collateral "' . $data['collateral_name'] . '" for customer',
				'activity_cate' => 'collateral_add'
			);
			log_activity($logger);

			echo json_encode(array(
				'success' => true,
				'message' => 'Collateral added successfully',
				'collateral_id' => $collateral_id
			));
		} else {
			echo json_encode(array('success' => false, 'message' => 'Failed to add collateral'));
		}
	}

	/**
	 * Update customer collateral
	 */
	public function update_customer_collateral() {
		header('Content-Type: application/json');

		$collateral_id = $this->input->post('collateral_id');
		$user_id = $this->session->userdata('user_id');

		if (empty($collateral_id)) {
			echo json_encode(array('status' => 'error', 'message' => 'Collateral ID is required'));
			return;
		}

		$collateral = $this->Collateral_model->get_by_id($collateral_id);
		if (!$collateral) {
			echo json_encode(array('status' => 'error', 'message' => 'Collateral not found'));
			return;
		}

		$data = array(
			'collateral_name' => $this->input->post('collateral_name'),
			'collateral_type' => $this->input->post('collateral_type'),
			'collateral_serial' => $this->input->post('collateral_serial'),
			'market_value' => $this->input->post('market_value'),
			'force_sale_value' => $this->input->post('force_sale_value'),
			'collateral_desc' => $this->input->post('collateral_desc'),
			'location_status' => $this->input->post('location_status'),
			'updated_by' => $user_id,
			'updated_at' => date('Y-m-d H:i:s')
		);

		// Handle file upload
		if (!empty($_FILES['collateral_file']['name'])) {
			$upload_path = FCPATH . 'uploads/collaterals/' . $collateral->customer_type . '_' . $collateral->customer_id . '/';
			if (!is_dir($upload_path)) {
				mkdir($upload_path, 0777, true);
			}

			$config['upload_path'] = $upload_path;
			$config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|doc|docx';
			$config['max_size'] = 10240;
			$config['file_name'] = time() . '_' . $_FILES['collateral_file']['name'];

			$this->load->library('upload', $config);

			if ($this->upload->do_upload('collateral_file')) {
				$upload_data = $this->upload->data();
				$data['collateral_file'] = 'collaterals/' . $collateral->customer_type . '_' . $collateral->customer_id . '/' . $upload_data['file_name'];
			}
		}

		$result = $this->Collateral_model->update($collateral_id, $data);

		if ($result) {
			echo json_encode(array('success' => true, 'message' => 'Collateral updated successfully'));
		} else {
			echo json_encode(array('success' => false, 'message' => 'Failed to update collateral'));
		}
	}

	/**
	 * Delete customer collateral
	 */
	public function delete_collateral() {
		header('Content-Type: application/json');

		$collateral_id = $this->input->post('collateral_id');
		$user_id = $this->session->userdata('user_id');

		if (empty($collateral_id)) {
			echo json_encode(array('success' => false, 'message' => 'Collateral ID is required'));
			return;
		}

		$collateral = $this->Collateral_model->get_by_id($collateral_id);
		if (!$collateral) {
			echo json_encode(array('success' => false, 'message' => 'Collateral not found'));
			return;
		}

		// Check if collateral is linked to any active loans
		$loans = $this->Collateral_model->get_collateral_loans($collateral_id);
		$active_loans = array_filter($loans, function($l) { return $l->status == 'ACTIVE'; });
		if (count($active_loans) > 0) {
			echo json_encode(array('success' => false, 'message' => 'Cannot delete collateral linked to active loans'));
			return;
		}

		$result = $this->Collateral_model->delete($collateral_id);

		if ($result) {
			$logger = array(
				'user_id' => $user_id,
				'activity' => 'Deleted collateral "' . $collateral->collateral_name . '"',
				'activity_cate' => 'collateral_delete'
			);
			log_activity($logger);

			echo json_encode(array('success' => true, 'message' => 'Collateral deleted successfully'));
		} else {
			echo json_encode(array('success' => false, 'message' => 'Failed to delete collateral'));
		}
	}

	/**
	 * Link collateral to a loan
	 */
	public function link_collateral_to_loan() {
		header('Content-Type: application/json');

		$loan_id = $this->input->post('loan_id');
		$collateral_id = $this->input->post('collateral_id');
		$amount_utilized = $this->input->post('amount_utilized');
		$user_id = $this->session->userdata('user_id');

		if (empty($loan_id) || empty($collateral_id) || empty($amount_utilized)) {
			echo json_encode(array('success' => false, 'message' => 'Loan ID, Collateral ID and Amount are required'));
			return;
		}

		// Check available balance
		$available = $this->Collateral_model->get_available_balance($collateral_id);
		if ($amount_utilized > $available) {
			echo json_encode(array('success' => false, 'message' => 'Amount exceeds available balance. Available: ' . number_format($available, 2)));
			return;
		}

		$data = array(
			'loan_id' => $loan_id,
			'collateral_id' => $collateral_id,
			'amount_utilized' => $amount_utilized,
			'status' => 'ACTIVE',
			'linked_by' => $user_id,
			'linked_at' => date('Y-m-d H:i:s')
		);

		$link_id = $this->Collateral_model->link_to_loan($data);

		if ($link_id) {
			$collateral = $this->Collateral_model->get_by_id($collateral_id);
			$loan = $this->Loan_model->get_by_id($loan_id);

			$logger = array(
				'user_id' => $user_id,
				'activity' => 'Linked collateral "' . $collateral->collateral_name . '" to loan ' . $loan->loan_number . ' (Amount: ' . number_format($amount_utilized, 2) . ')',
				'activity_cate' => 'collateral_link'
			);
			log_activity($logger);

			echo json_encode(array('success' => true, 'message' => 'Collateral linked successfully', 'link_id' => $link_id));
		} else {
			echo json_encode(array('success' => false, 'message' => 'Failed to link collateral'));
		}
	}

	/**
	 * Get collaterals linked to a loan
	 */
	public function get_loan_collaterals($loan_id) {
		header('Content-Type: application/json');

		if (empty($loan_id)) {
			echo json_encode(array('success' => false, 'message' => 'Loan ID is required'));
			return;
		}

		$collaterals = $this->Collateral_model->get_loan_collaterals($loan_id);

		$formatted = array();
		$total_utilized_this_loan = 0;
		$total_utilized_other_loans = 0;

		foreach ($collaterals as $c) {
			$linked_by_name = 'N/A';
			if (!empty($c->linked_by)) {
				$linked_by = get_by_id('employees', 'id', $c->linked_by);
				$linked_by_name = $linked_by ? $linked_by->Firstname . ' ' . $linked_by->Lastname : 'N/A';
			}

			// Get total utilized for this collateral across all loans
			$total_collateral_utilized = $this->Collateral_model->get_utilized_amount($c->collateral_id);
			$other_loans_utilized = floatval($total_collateral_utilized) - floatval($c->amount_utilized ?? 0);
			$available_balance = max(0, ($c->force_sale_value ?? 0) - $total_collateral_utilized);

			$formatted[] = array(
				'link_id' => $c->id,
				'collateral_id' => $c->collateral_id,
				'collateral_name' => $c->collateral_name,
				'collateral_type' => $c->collateral_type,
				'collateral_serial' => $c->collateral_serial ?? '',
				'market_value' => floatval($c->market_value ?? 0),
				'force_sale_value' => floatval($c->force_sale_value ?? 0),
				'amount_utilized' => floatval($c->amount_utilized ?? 0),
				'total_utilized' => floatval($total_collateral_utilized),
				'other_loans_utilized' => floatval($other_loans_utilized),
				'available_balance' => floatval($available_balance),
				'link_status' => $c->status ?? 'ACTIVE',
				'status' => $c->status ?? 'ACTIVE',
				'collateral_status' => $c->collateral_status ?? 'ACTIVE',
				'description' => $c->collateral_desc ?? '',
				'linked_by' => $linked_by_name,
				'linked_at' => !empty($c->linked_at) ? date('d M Y H:i', strtotime($c->linked_at)) : 'N/A'
			);

			if ($c->status == 'ACTIVE') {
				$total_utilized_this_loan += floatval($c->amount_utilized ?? 0);
				$total_utilized_other_loans += floatval($other_loans_utilized);
			}
		}

		// Calculate summary
		$summary = array(
			'total_force_sale' => array_sum(array_column($formatted, 'force_sale_value')),
			'this_loan_utilization' => $total_utilized_this_loan,
			'other_loans_utilization' => $total_utilized_other_loans
		);

		echo json_encode(array(
			'success' => true,
			'collaterals' => $formatted,
			'count' => count($formatted),
			'summary' => $summary
		));
	}

	/**
	 * Release collateral from loan (when loan is paid/closed)
	 */
	public function release_loan_collateral() {
		header('Content-Type: application/json');

		$link_id = $this->input->post('link_id');
		$user_id = $this->session->userdata('user_id');

		if (empty($link_id)) {
			echo json_encode(array('status' => 'error', 'message' => 'Link ID is required'));
			return;
		}

		$link = $this->Collateral_model->get_link_by_id($link_id);
		if (!$link) {
			echo json_encode(array('status' => 'error', 'message' => 'Link not found'));
			return;
		}

		$result = $this->Collateral_model->update_link_status($link_id, 'RELEASED', $user_id);

		if ($result) {
			$collateral = $this->Collateral_model->get_by_id($link->collateral_id);

			$logger = array(
				'user_id' => $user_id,
				'activity' => 'Released collateral "' . $collateral->collateral_name . '" from loan',
				'activity_cate' => 'collateral_release'
			);
			log_activity($logger);

			echo json_encode(array('status' => 'success', 'message' => 'Collateral released successfully'));
		} else {
			echo json_encode(array('status' => 'error', 'message' => 'Failed to release collateral'));
		}
	}

	/**
	 * Remove collateral link from loan
	 */
	public function unlink_collateral() {
		header('Content-Type: application/json');

		$link_id = $this->input->post('link_id');
		$user_id = $this->session->userdata('user_id');

		if (empty($link_id)) {
			echo json_encode(array('success' => false, 'message' => 'Link ID is required'));
			return;
		}

		$result = $this->Collateral_model->delete_link($link_id);

		if ($result) {
			echo json_encode(array('success' => true, 'message' => 'Collateral unlinked successfully'));
		} else {
			echo json_encode(array('success' => false, 'message' => 'Failed to unlink collateral'));
		}
	}

	/**
	 * Update collateral status
	 */
	public function update_collateral_status() {
		header('Content-Type: application/json');

		$collateral_id = $this->input->post('collateral_id');
		$new_status = $this->input->post('status');
		$remarks = $this->input->post('remarks');
		$user_id = $this->session->userdata('user_id');

		if (empty($collateral_id) || empty($new_status)) {
			echo json_encode(array('success' => false, 'message' => 'Collateral ID and status are required'));
			return;
		}

		$valid_statuses = array('ACTIVE', 'RELEASED', 'SOLD', 'DAMAGED', 'LOST', 'RETURNED', 'RECOVERED');
		if (!in_array($new_status, $valid_statuses)) {
			echo json_encode(array('success' => false, 'message' => 'Invalid status'));
			return;
		}

		$collateral = $this->Collateral_model->get_by_id($collateral_id);
		if (!$collateral) {
			echo json_encode(array('success' => false, 'message' => 'Collateral not found'));
			return;
		}

		$old_status = $collateral->collateral_status ?? 'ACTIVE';

		$data = array(
			'collateral_status' => $new_status
		);

		$result = $this->Collateral_model->update($collateral_id, $data);

		if ($result) {
			$this->Collateral_model->log_history(array(
				'collateral_id' => $collateral_id,
				'old_status' => $old_status,
				'new_status' => $new_status,
				'remarks' => $remarks,
				'changed_by' => $user_id,
				'changed_at' => date('Y-m-d H:i:s')
			));

			echo json_encode(array('success' => true, 'message' => 'Collateral status updated'));
		} else {
			echo json_encode(array('success' => false, 'message' => 'Failed to update status'));
		}
	}

	/**
	 * Get collateral history
	 */
	public function get_collateral_history($collateral_id) {
		header('Content-Type: application/json');

		if (empty($collateral_id)) {
			echo json_encode(array('success' => false, 'message' => 'Collateral ID is required'));
			return;
		}

		$history = $this->Collateral_model->get_history($collateral_id);

		$formatted = array();
		foreach ($history as $h) {
			$changed_by = get_by_id('employees', 'id', $h->changed_by);
			$formatted[] = array(
				'old_status' => $h->old_status,
				'new_status' => $h->new_status,
				'remarks' => $h->remarks,
				'changed_by' => $changed_by ? $changed_by->Firstname . ' ' . $changed_by->Lastname : 'N/A',
				'changed_at' => date('d M Y H:i', strtotime($h->changed_at))
			);
		}

		echo json_encode(array('success' => true, 'history' => $formatted));
	}

	/**
	 * Get loans using a specific collateral
	 */
	public function get_collateral_loans($collateral_id) {
		header('Content-Type: application/json');

		if (empty($collateral_id)) {
			echo json_encode(array('success' => false, 'message' => 'Collateral ID is required'));
			return;
		}

		$loans = $this->Collateral_model->get_collateral_loans($collateral_id);

		$formatted = array();
		foreach ($loans as $l) {
			$formatted[] = array(
				'link_id' => $l->id,
				'loan_id' => $l->loan_id,
				'loan_number' => $l->loan_number,
				'loan_principal' => floatval($l->loan_principal),
				'loan_status' => $l->loan_status,
				'amount_utilized' => floatval($l->amount_utilized),
				'link_status' => $l->status
			);
		}

		echo json_encode(array('success' => true, 'loans' => $formatted));
	}

	// ==================== CREATED LOANS (API-created, awaiting completion) ====================

	/**
	 * List all loans with status CREATED
	 */
	function created_loans()
	{
		$data['loan_data'] = $this->Loan_model->get_all('CREATED');
		$menu_toggle['toggles'] = 23;
		$this->load->view('admin/header', $menu_toggle);
		$this->load->view('loan/created_loans_list', $data);
		$this->load->view('admin/footer');
	}

	/**
	 * Show form to complete a CREATED loan
	 */
	function complete_loan($id)
	{
		$row = $this->Loan_model->get_by_id($id);

		if (!$row) {
			$this->toaster->error('Loan not found');
			redirect('Loan/created_loans');
			return;
		}

		if ($row->loan_status != 'CREATED') {
			$this->toaster->error('This loan is not in CREATED status');
			redirect('Loan/created_loans');
			return;
		}

		// Determine customer info based on type
		if ($row->customer_type == 'individual') {
			$indi = $this->Individual_customers_model->get_by_id($row->loan_customer);
			$customer_name = $indi->Firstname . ' ' . $indi->Lastname;
		} elseif ($row->customer_type == 'institution') {
			$institution = $this->Corporate_customers_model->get_by_id($row->loan_customer);
			$customer_name = $institution->EntityName . ' (' . $institution->RegistrationNumber . ')';
		} else {
			$group = $this->Groups_model->get_by_id($row->loan_customer);
			$customer_name = $group->group_name . ' (' . $group->group_code . ')';
		}

		$data = array(
			'loan_id' => $row->loan_id,
			'loan_number' => $row->loan_number,
			'loan_product' => $row->product_name,
			'loan_product_id' => $row->loan_product,
			'customer_type' => $row->customer_type,
			'loan_customer' => $customer_name,
			'customer_id' => $row->loan_customer,
			'loan_date' => $row->loan_date,
			'loan_principal' => $row->loan_principal,
			'loan_period' => $row->loan_period,
			'period_type' => $row->period_type,
			'loan_interest' => $row->loan_interest,
			'currency' => $row->currency,
			'processing_fee' => $row->processing_fee,
			'off_taker' => $row->off_taker,
			'narration' => $row->narration,
			'crb_search' => isset($row->crb_search) ? $row->crb_search : '',
			'pacra_search' => isset($row->pacra_search) ? $row->pacra_search : '',
			'previous_facilities' => isset($row->previous_facilities) ? $row->previous_facilities : '',
			'past_loans_comment' => isset($row->past_loans_comment) ? $row->past_loans_comment : '',
			'security_notes' => isset($row->security_notes) ? $row->security_notes : '',
			'bank_statement_notes' => isset($row->bank_statement_notes) ? $row->bank_statement_notes : '',
			'about_transaction' => isset($row->about_transaction) ? $row->about_transaction : '',
			'risk_analysis' => isset($row->risk_analysis) ? $row->risk_analysis : '',
		);

		$menu_toggle['toggles'] = 23;
		$this->load->view('admin/header', $menu_toggle);
		$this->load->view('loan/complete_loan', $data);
		$this->load->view('admin/footer');
	}

	/**
	 * Process the complete loan form submission
	 * Updates loan details and changes status from CREATED to INITIATED
	 */
	function complete_loan_action()
	{
		$loan_id = $this->input->post('loan_id');
		$row = $this->Loan_model->get_by_id($loan_id);

		if (!$row) {
			$this->toaster->error('Loan not found');
			redirect('Loan/created_loans');
			return;
		}

		if ($row->loan_status != 'CREATED') {
			$this->toaster->error('This loan is not in CREATED status');
			redirect('Loan/created_loans');
			return;
		}

		// Gather appraisal data
		$update_data = array(
			'processing_fee' => $this->input->post('processing_fee'),
			'off_taker' => $this->input->post('off_taker'),
			'narration' => $this->input->post('narration'),
			'crb_search' => $this->input->post('crb_search'),
			'pacra_search' => $this->input->post('pacra_search'),
			'previous_facilities' => $this->input->post('previous_facilities'),
			'past_loans_comment' => $this->input->post('past_loans_comment'),
			'security_notes' => $this->input->post('security_notes'),
			'bank_statement_notes' => $this->input->post('bank_statement_notes'),
			'about_transaction' => $this->input->post('about_transaction'),
			'risk_analysis' => $this->input->post('risk_analysis'),
			'loan_status' => 'INITIATED',
			'loan_added_by' => $this->session->userdata('user_id'),
		);

		// Update the loan record
		$this->Loan_model->update($loan_id, $update_data);

		// Create upload directory
		$imagePath = APPPATH . '../uploads/' . $row->loan_number;
		if (!is_dir($imagePath)) {
			mkdir($imagePath, 0777, true);
		}

		// Create file library folders if they don't exist
		$existing_folder = $this->db->where('folder_name', $row->loan_number)->where('parent_folder_id', 10)->get('file_folders')->row();
		if ($existing_folder) {
			$folder_id = $existing_folder->id;
			$existing_loan_files_folder = $this->db->where('folder_name', $row->loan_number . " loan files")->where('parent_folder_id', $folder_id)->get('file_folders')->row();
			$folder_id_loan_files = $existing_loan_files_folder ? $existing_loan_files_folder->id : 0;
		} else {
			$folder_data = [
				'folder_name' => $row->loan_number,
				'parent_folder_id' => 10,
				'owner_id' => $loan_id,
				'is_public' => 1,
				'date_created' => date('Y-m-d H:i:s'),
				'date_modified' => date('Y-m-d H:i:s'),
				'description' => 'Loan folder'
			];
			$folder_id = $this->File_folders_model->insert($folder_data);

			$folder_data_loan_files = [
				'folder_name' => $row->loan_number . " loan files",
				'parent_folder_id' => $folder_id,
				'owner_id' => $loan_id,
				'is_public' => 1,
				'date_created' => date('Y-m-d H:i:s'),
				'date_modified' => date('Y-m-d H:i:s'),
				'description' => 'Loan files folder'
			];
			$folder_id_loan_files = $this->File_folders_model->insert($folder_data_loan_files);
		}

		// Handle file uploads
		$this->load->library('upload');
		$number_of_files_uploaded = isset($_FILES['loan_files']['name']) ? count($_FILES['loan_files']['name']) : 0;

		for ($i = 0; $i < $number_of_files_uploaded; $i++) {
			if (empty($_FILES['loan_files']['name'][$i])) continue;

			$_FILES['userfile']['name']     = $_FILES['loan_files']['name'][$i];
			$_FILES['userfile']['type']     = $_FILES['loan_files']['type'][$i];
			$_FILES['userfile']['tmp_name'] = $_FILES['loan_files']['tmp_name'][$i];
			$_FILES['userfile']['error']    = $_FILES['loan_files']['error'][$i];
			$_FILES['userfile']['size']     = $_FILES['loan_files']['size'][$i];

			$config = array(
				'file_name'     => $_FILES['userfile']['name'],
				'allowed_types' => '*',
				'max_size'      => 200000,
				'overwrite'     => FALSE,
				'upload_path'   => $imagePath
			);

			$this->upload->initialize($config);

			if ($this->upload->do_upload()) {
				$uploaded_data = $this->upload->data();

				$data = array(
					'loan_id' => $loan_id,
					'file_name' => $uploaded_data['file_name'],
					'real_file' => $row->loan_number . '/' . $uploaded_data['file_name'],
				);
				$this->Loan_files_model->insert($data);

				$insert_data = [
					'owner_type' => 'loan',
					'owner_id' => $loan_id,
					'file_category' => 'loan_files',
					'file_type' => $_FILES['userfile']['type'],
					'file_name' => $uploaded_data['file_name'],
					'file_path' => "uploads/" . $row->loan_number . "/" . $uploaded_data['file_name'],
					'file_size' => $_FILES['userfile']['size'],
					'is_public' => 1,
					'date_added' => date('Y-m-d H:i:s'),
					'date_modified' => date('Y-m-d H:i:s'),
					'added_by' => $this->session->userdata('user_id'),
					'description' => "loan file for loan",
					'tags' => ""
				];

				$file_id = $this->File_library_model->insert($insert_data);

				if ($folder_id_loan_files) {
					$this->File_folder_mapping_model->insert([
						'file_id' => $file_id,
						'folder_id' => $folder_id_loan_files,
						'date_added' => date('Y-m-d H:i:s')
					]);
				}
			}
		}

		// Handle bank statements
		$customer_type = $this->input->post('customer_type');
		$statement_type = ($customer_type == 'institution') ? 'corporate' : 'personal';
		$credit_field = ($customer_type == 'institution') ? 'corporate_credit' : 'personal_credit';
		$debit_field = ($customer_type == 'institution') ? 'corporate_debit' : 'personal_debit';
		$month_field = ($customer_type == 'institution') ? 'corporate_statement_month' : 'personal_statement_month';
		$file_field = ($customer_type == 'institution') ? 'corporate_statement_file' : 'personal_statement_file';

		$credits = $this->input->post($credit_field);
		$debits = $this->input->post($debit_field);
		$months = $this->input->post($month_field);

		if (is_array($credits) && is_array($debits) && is_array($months)) {
			$num_statements = count($credits);

			for ($i = 0; $i < $num_statements; $i++) {
				$credit = isset($credits[$i]) ? $credits[$i] : null;
				$debit = isset($debits[$i]) ? $debits[$i] : null;
				$month = isset($months[$i]) ? $months[$i] : null;

				if (empty($credit) && empty($debit) && empty($month)) {
					continue;
				}

				$statement_filename = null;

				// Handle file upload for this statement
				if (isset($_FILES[$file_field]) &&
					isset($_FILES[$file_field]['name'][$i]) &&
					$_FILES[$file_field]['name'][$i] != '') {

					$_FILES['userfile']['name']     = $_FILES[$file_field]['name'][$i];
					$_FILES['userfile']['type']     = $_FILES[$file_field]['type'][$i];
					$_FILES['userfile']['tmp_name'] = $_FILES[$file_field]['tmp_name'][$i];
					$_FILES['userfile']['error']    = $_FILES[$file_field]['error'][$i];
					$_FILES['userfile']['size']     = $_FILES[$file_field]['size'][$i];

					$config = array(
						'file_name'     => 'statement_' . time() . '_' . $i . '_' . $_FILES['userfile']['name'],
						'allowed_types' => '*',
						'max_size'      => 200000,
						'overwrite'     => FALSE,
						'upload_path'   => $imagePath
					);

					$this->upload->initialize($config);

					if ($this->upload->do_upload()) {
						$uploaded_data = $this->upload->data();
						$statement_filename = $uploaded_data['file_name'];

						$insert_data_statement = [
							'owner_type' => 'loan',
							'owner_id' => $loan_id,
							'file_category' => 'bank_statement',
							'file_type' => $_FILES['userfile']['type'],
							'file_name' => $uploaded_data['file_name'],
							'file_path' => "uploads/" . $row->loan_number . "/" . $uploaded_data['file_name'],
							'file_size' => $_FILES['userfile']['size'],
							'is_public' => 1,
							'date_added' => date('Y-m-d H:i:s'),
							'date_modified' => date('Y-m-d H:i:s'),
							'added_by' => $this->session->userdata('user_id'),
							'description' => "Bank statement for loan - " . $month,
							'tags' => ""
						];

						$file_id_statement = $this->File_library_model->insert($insert_data_statement);

						if ($folder_id_loan_files) {
							$this->File_folder_mapping_model->insert([
								'file_id' => $file_id_statement,
								'folder_id' => $folder_id_loan_files,
								'date_added' => date('Y-m-d H:i:s')
							]);
						}
					}
				}

				$bank_statement_data = [
					'loan_id' => $loan_id,
					'statement_type' => $statement_type,
					'credit' => $credit ? str_replace(',', '', $credit) : 0,
					'debit' => $debit ? str_replace(',', '', $debit) : 0,
					'month' => $month,
					'year' => date('Y'),
					'file' => $statement_filename,
					'added_by' => $this->session->userdata('user_id'),
					'date_added' => date('Y-m-d H:i:s')
				];

				$this->db->insert('bank_statements', $bank_statement_data);
			}
		}

		// Link selected collaterals to the loan
		$collateral_ids = $this->input->post('collateral_ids');
		$collateral_amounts = $this->input->post('collateral_amounts');

		if (!empty($collateral_ids) && is_array($collateral_ids)) {
			$user_id = $this->session->userdata('user_id');

			for ($i = 0; $i < count($collateral_ids); $i++) {
				$collateral_id = $collateral_ids[$i];
				$amount_utilized = isset($collateral_amounts[$i]) ? floatval($collateral_amounts[$i]) : 0;

				if ($collateral_id && $amount_utilized > 0) {
					$available = $this->Collateral_model->get_available_balance($collateral_id);

					if ($amount_utilized <= $available) {
						$link_data = array(
							'loan_id' => $loan_id,
							'collateral_id' => $collateral_id,
							'amount_utilized' => $amount_utilized,
							'linked_by' => $user_id,
							'linked_at' => date('Y-m-d H:i:s'),
							'status' => 'ACTIVE'
						);

						$this->Collateral_model->link_to_loan($link_data);
					}
				}
			}
		}

		// Insert approval trail record
		$this->Loan_approval_trail_model->insert(array(
			'loan_id' => $loan_id,
			'user_id' => $this->session->userdata('user_id'),
			'action' => 'COMPLETED',
			'comment' => 'Loan completed from API-created state and submitted for approval'
		));

		// Send email notification to users who can recommend loans (same as INITIATE flow)
		$customer_name = 'N/A';
		if ($row->customer_type == 'individual') {
			$cust = $this->db->get_where('individual_customers', array('id' => $row->loan_customer))->row();
			if ($cust) {
				$customer_name = $cust->Firstname . ' ' . $cust->Lastname;
			}
		} else {
			$cust = $this->db->get_where('corporate_customers', array('id' => $row->loan_customer))->row();
			if ($cust) {
				$customer_name = $cust->EntityName;
			}
		}

		$currency_data = $this->db->get_where('currency', array('id' => $row->currency))->row();
		$currency_code = $currency_data ? $currency_data->code : 'ZMW';

		$loan_notification_data = array(
			'loan_id' => $loan_id,
			'loan_number' => $row->loan_number,
			'customer_name' => $customer_name,
			'amount' => $row->loan_principal,
			'currency' => $currency_code
		);

		notify_loan_recommenders($loan_notification_data, $this->session->userdata('user_id'));

		$this->toaster->success('Loan completed successfully and submitted for approval');
		redirect('Loan/created_loans');
	}
}