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
        $this->load->model('Loan_model');
        $this->load->model('Groups_model');
        $this->load->model('charges_model');
        $this->load->model('Account_model');
        $this->load->model('Loan_files_model');
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
          $number_of_files_uploaded = count($_FILES['loan_files']['name']);
        $name = $this->input->post('file_name');
        $coname = $this->input->post('coname');
        $type = $this->input->post('type');
        $currency = $this->input->post('currency');
        $serial = $this->input->post('serial');
        $cvalue = $this->input->post('cvalue');
       
        $desc = $this->input->post('desc');


        $loan_number = str_replace(' ', '', $this->input->post('loan_number'));
        $result = $this->Loan_model->add_loan( $loan_number,$this->input->post('amount'), $this->input->post('months'),$this->input->post('interest'), $this->input->post('loan_type'), $this->input->post('loan_date'),$this->input->post('customer'),$this->input->post('customer_type'),$this->input->post('worthness_file'),$this->input->post('narration'),$this->session->userdata('user_id'), $this->input->post('payment_method'),$this->input->post('fee_amount'),$currency,$this->input->post('off_taker'),$this->input->post('processing_fee'));
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
                    'real_file' => $config['file_name'],

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


        $number_of_collateral = count($_FILES['collateralfiles']['name']);

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


        $this->toaster->success('Success, loan  was created  pending authorisation');

        redirect('loan/individual_track');


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
        redirect('loan/individual_track');






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
        redirect('loan/individual_track');






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
                    redirect('loan/individual_track');
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
            $this->load->view('loan/track', $data);
            $this->load->view('admin/footer');
        }
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
        $data['loan_data'] = $this->Loan_model->get_all('APPROVED');
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


						$result = $this->Payement_schedules_model->new_pay($loan_number, $pay_number, $amount, $paid_date, $acrued_amount);

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
					$r = $this->Payement_schedules_model->new_pay($loan_number, $pay_number, $topay_amount, $paid_date);

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
						$this->Payement_schedules_model->new_pay($loan_number, $pay_number, $amount, $paid_date, $acrued_amount);
						// $this->Rescheduled_payments_model->new__late_pay($loan_number, $pay_number, $amount);
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
                        'ref' => "GF." . date('Y') . date('m') . date('d') . '.' . rand(100, 999),
                        'loan_id' => $this->input->post('loan_id', TRUE),
                        'amount' => $amount,
                        'transaction_type' => 2,
                        'payment_number' => $this->input->post('payment_number'),

                        'method' => $this->input->post('payment_method'),
                        'payment_proof' => $proof,
                        'reference' => $this->input->post('reference'),
                        'date_stamp' => $paid_date,
                        'added_by' => $this->session->userdata('user_id')

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
                        'ref' => "GF." . date('Y') . date('m') . date('d') . '.' . rand(100, 999),
                        'loan_id' => $this->input->post('loan_id', TRUE),
                        'amount' => $topay_amount,
                        'transaction_type' => 2,
                        'payment_number' => $this->input->post('payment_number'),
                        'date_stamp' => $paid_date,
                        'method' => $this->input->post('payment_method'),
                        'payment_proof' => $proof,
                        'reference' => $this->input->post('reference'),

                        'added_by' => $this->session->userdata('user_id')

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
                                'ref' => "GF." . date('Y') . date('m') . date('d') . '.' . rand(100, 999),
                                'loan_id' => $this->input->post('loan_id', TRUE),
                                'amount' => $amount,
                                'transaction_type' => 2,
                                'payment_number' => $this->input->post('payment_number'),
                                'date_stamp' => $paid_date,
                                'method' => $this->input->post('payment_method'),
                                'payment_proof' => $proof,
                                'reference' => $this->input->post('reference'),

                                'added_by' => $this->session->userdata('user_id')

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
                        'ref' => "CF." . date('Y') . date('m') . date('d') . '.' . rand(100, 999),
                        'loan_id' => $loan_number,
                        'amount' => $amount,
                        'transaction_type' => 1,
                        'payment_number' => 0,
                        'method' => $this->input->post('payment_method'),
                        'payment_proof' => $proof,
                        'reference' => $this->input->post('reference'),
                        'date_stamp' => $paid_date,
                        'added_by' => $this->session->userdata('user_id')

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

    function view($id){
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
			'acrued' => $acrued

        );
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
        $payments = $this->Payement_schedules_model->get_all_by_id($row->loan_id);
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
            $view = "Edit_loan";
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
        $this->Loan_model->add_loan_edit($row->id,$data_new->loan_number,$data_new->loan_principal, $data_new->loan_period, $data_new->sy_loan_product, $data_new->loan_date,$data_new->sy_loan_customer,$data_new->customer_type,$data_new->loan_worthness_file,$data_new->narration,$data_new->sy_added_by);
        $this->toaster->success('Success, loan edit was authorised  pending authorisation');
        redirect('loan/track');


    }
    public function edit_action(){

        $row = $this->Loan_model->get_by_id($this->input->post('loan_id'));


        if($row->customer_type=='group'){
            $group = $this->Groups_model->get_by_id($row->loan_customer);

            $customer_name = $group->group_name.'('.$group->group_code.')';
            $preview_url = "Customer_groups/members/";
        }elseif($row->customer_type=='individual'){
            $indi = $this->Individual_customers_model->get_by_id($row->loan_customer);
            $customer_name = $indi->Firstname.' '.$indi->Lastname;
            $preview_url = "Individual_customers/view/";
        }

        if($this->input->post('customer_type')=='group'){
            $group1 = $this->Groups_model->get_by_id($this->input->post('customer'));

            $customer_name1 = $group1->group_name.'('.$group1->group_code.')';
            $preview_url1 = "Customer_groups/members/";
        }elseif($this->input->post('customer_type')=='individual'){
            $indi1 = $this->Individual_customers_model->get_by_id($this->input->post('customer'));
            $customer_name1 = $indi1->Firstname.' '.$indi1->Lastname;
            $preview_url1 = "Individual_customers/view/";
        }
        $loan_number = str_replace(' ', '', $this->input->post('loan_number'));
        $product_n = get_by_id('loan_products','loan_product_id',$this->input->post('loan_type'));
        $added_by1 = get_by_id('employees','id',$this->session->userdata('user_id'));
        $result = array(
            'loan_id' => $row->loan_id,
            'loan_number'=> $loan_number,
            'sy_loan_product'=>$this->input->post('loan_type'),
            'loan_product'=>$product_n->product_name,
            'sy_loan_customer'=>$this->input->post('customer'),
            'loan_customer'=>$customer_name1,
            'customer_type'=> $this->input->post('customer_type'),
            'preview_url' => $preview_url1,
            'customer_id' => $row->loan_customer,
            'loan_date'=>$this->input->post('loan_date'),
            'loan_principal'=>$this->input->post('amount'),
            'loan_period'=>$this->input->post('months'),
            'loan_worthness_file'=>$this->input->post('worthness_file'),
            'narration'=>$this->input->post('narration'),
            'sy_added_by'=>$this->session->userdata('user_id'),
            'added_by'=>$added_by1->Firstname." ".$added_by1->Lastname,

        );
        $added_by = get_by_id('employees','id',$row->loan_added_by);
        $data = array(
            'loan_id' => $row->loan_id,
            'loan_number' => $row->loan_number,
            'loan_product' => $row->product_name,
            'loan_customer' => $customer_name,
            'customer_type' => $row->customer_type,
            'preview_url' => $preview_url,
            'customer_id' => $row->loan_customer,
            'loan_date' => $row->loan_date,
            'loan_principal' => $row->loan_principal,
            'loan_period' => $row->loan_period,
            'loan_worthness_file'=>$row->worthness_file,
            'narration'=>$row->narration,
            'loan_added_by' => $added_by->Firstname." ".$added_by->Lastname,


        );




        $logger = array(
            'type' => 'Loan edit',
            'old_info' => json_encode($data),
            'new_info' => json_encode($result),
            'id'=> $this->input->post('loan_id'),
            'summary'=> $this->input->post('loan_number'),

            'Initiated_by' => $this->session->userdata('user_id')

        );
        auth_logger($logger);
        $this->toaster->success('You successfully, initiated loan edit, wait for approval');
        redirect('Loan/initiate_edit_loan');
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
        );
        $this->load->library('Pdf');
        $html = $this->load->view('loan/report', $data,true);
        $this->pdf->createPDF($html, $data['loan_customer']." loan report as on".date('Y-m-d'), true);
     }

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
            $this->Loan_model->update($id,array('loan_status'=>$action,'disbursed'=>'Yes',$by=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s')));
        }else{
            $this->Loan_model->update($id,array('loan_status'=>$action,$by=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s')));
        }
        if($notify->loan_disbursement=='Yes' && $action =="ACTIVE"){
            send_sms($customer->PhoneNumber,'Dear customer, loan has been approved, you can call numbers below for more');
        }
        $this->toaster->success('Success, your action successful');
        redirect($_SERVER['HTTP_REFERER']);
    }
    function disburse_loan(){
        $id = $this->input->post('loan_id');
        $previous_date= $this->input->post('pdate');
        $current_date = $this->input->post('cdate');
        $comment= $this->input->post('comment');
        $customer = $this->Loan_model->loan_user($id);
        $notify = get_by_id('sms_settings','id','1');

        $by = 'disbursed_by';

        $by_date = 'disbursed_date';

        if($current_date !=""){
            $r  = $this->Loan_model->restructure($id,$current_date);
            $this->Loan_model->update($id,array('loan_status'=>'ACTIVE','disbursed'=>'Yes',$by=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s')));

        }else{

            $this->Loan_model->update($id,array('loan_status'=>'ACTIVE','disbursed'=>'Yes',$by=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s')));

        }
        $logger = array(
            'user_id' => $this->session->userdata('user_id'),
            'activity' => 'Disbursed a loan',
            'activity_cate' => 'loan_disbursement'

        );
        log_activity($logger);

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
        $customer = $this->Loan_model->loan_user($id);
        $notify = get_by_id('sms_settings','id','1');

        $by = 'disbursed_by';

        $by_date = 'disbursed_date';

        if($current_date !=""){

            $r  = $this->Loan_model->restructure($id,$current_date);

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


            $this->Loan_model->update($id,array('loan_status'=>'ACTIVE','disbursed_amount'=> $disbursedamount,'disbursed'=> 'Yes',$by=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s')));
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

            $this->Loan_model->update($id,array('loan_status'=>'ACTIVE', 'disbursed_amount'=> $disbursedamount,'disbursed'=>'Yes',$by=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s')));
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


            $this->Loan_model->update($users[$i],array('loan_status'=>'APPROVED','loan_approved_by'=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s'), 'minutes'=>$this->input->post('minutes')));


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

        $this->toaster->success('Loan were rejection was successful');
        redirect(site_url('loan/recommend'));

    }
    function bulkactions_recommend(){
        $by_date = 'loan_recommended_date';
        $users = $this->input->post('loans');
        $rowCount = count($users);
        for ($i = 0; $i < $rowCount; $i ++) {


            $this->Loan_model->update($users[$i],array('loan_status'=>'RECOMMENDED','loan_recommended_by'=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s')));


        }
        $this->toaster->success('loans were approved successfully');
        redirect(site_url('Loan/recommend'));
    }
    function single_recommend()
    {
        $by_date = 'loan_recommended_date';
        $loan_id = $this->input->post('loan_id');
        $reasons = $this->input->post('recommend_reasons');
        $this->Loan_model->update($loan_id,array('loan_status'=>'RECOMMENDED','recommend_reasons'=>$reasons,'loan_recommended_by'=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s'), 'minutes'=>$this->input->post('minutes')));
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
            $this->Loan_model->update($id,array('loan_status'=>$action, $by=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s')));
        }else{
            $this->Loan_model->update($id,array('loan_status'=>$action,$by=>$this->session->userdata('user_id'),$by_date =>date('Y-m-d H:i:s')));
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
        $this->Loan_model->update($id,array('loan_status'=>'RECOMMENDED', 'loan_recommended_by'=>$this->session->userdata('user_id'),'loan_recommended_date' =>date('Y-m-d H:i:s')));



        $this->toaster->success('Success, your recommending action was successful');
        redirect($_SERVER['HTTP_REFERER']);
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
        $data['loan_data'] = array();
        $this->load->view('admin/header');
        $this->load->view('loan/loan_report',$data);
        $this->load->view('admin/footer');
    }
    function loan_report_search(){
        $user = $this->input->get('user');
        $product = $this->input->get('product');
        $status = $this->input->get('status');
        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $search = $this->input->get('search');
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
		// Check if this is an AJAX request
		if (!$this->input->is_ajax_request()) {
			show_error('No direct script access allowed', 403);
			return;
		}

		$loan_id = $this->input->post('loan_id');
		$payoff_date = $this->input->post('payoff_date');

		if (!$loan_id || !$payoff_date) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Missing required parameters'
			]);
			return;
		}


		$loan = $this->Loan_model->get_by_id($loan_id);


		// Get all unpaid schedules
		$unpaid_schedules = $this->Payement_schedules_model->get_unpaid_schedules($loan_id);

		// Calculate remaining principal
		$remaining_principal = 0;
		$amount_paid = 0;
		foreach ($unpaid_schedules as $schedule) {

			//remaining principal is total after deducting current accrued interest and that current accrued interest is fully paid and the remainder or remaining is what is deducted to $loan->loan_principal to get the principal balance
			$amount_paid += $schedule->paid_amount;
			// Check if partial paid
//			if ($schedule->partial_paid == 'YES') {
//				// Calculate remaining amount
//
//				$remaining_principal += $schedule->principal * (1 - ($schedule->paid_amount / $schedule->amount));
//			} else {
//				$remaining_principal += $schedule->principal;
//			}
		}

		// Convert loan date and payoff date to DateTime objects
		$loan_date = new DateTime($loan->loan_date);
		$payoff_date_obj = new DateTime($payoff_date);

		// Calculate monthly interest amount
		$monthly_interest_rate = $loan->loan_interest / 100; // Convert percentage to decimal
		$full_monthly_interest = $loan->loan_principal * $monthly_interest_rate;
		$daily_interest = $full_monthly_interest / 30; // Daily interest based on 30-day month

		// Initialize interest amount
		$accrued_interest = 0;

		// Get the next month date (one month after loan date)
		$next_month_date = clone $loan_date;
		$next_month_date->modify('+1 month');

		// Simple algorithm:
		// 1. If payoff date is before or equal to loan date + 1 month: full interest
		// 2. Otherwise: full interest + daily accruals for elapsed days beyond first month
		$calculation_explanation = "";
		$days_elapsed1 = 0;
		$real_interest_balance = 0;

		if ($payoff_date_obj <= $next_month_date) {
			// Case 1: Payoff within first month - charge full interest
			$accrued_interest = $full_monthly_interest;
			$interest_balance = abs($amount_paid - $accrued_interest);
			if($accrued_interest < $amount_paid){
				$real_interest_balance = 0;
				$remaining_principal = $loan->loan_principal - $interest_balance;
			}else{
				$real_interest_balance = $interest_balance;
				$remaining_principal = $loan->loan_principal;
			}


			$calculation_explanation = "Payoff within first month - full interest applied: " .
				"$remaining_principal × $monthly_interest_rate = $accrued_interest";
			$days_elapsed1 = 0;
		} else {
			// Case 2: Payoff after first month
			// Start with full interest for the first month
			$accrued_interest = $full_monthly_interest;

			// Calculate days elapsed after first month
			$days_elapsed = $payoff_date_obj->diff($next_month_date)->days;
			$days_elapsed1 = $days_elapsed;
			// Add daily accruals for elapsed days
			$daily_accrual = $daily_interest * $days_elapsed;
			$accrued_interest += $daily_accrual;
			$interest_balance = abs($amount_paid - $accrued_interest);
			if($accrued_interest < $amount_paid){
				$real_interest_balance = 0;
				$remaining_principal = $loan->loan_principal - $interest_balance;
			}else{
				$real_interest_balance = $interest_balance;
				$remaining_principal = $loan->loan_principal;
			}
			$calculation_explanation = "Payoff after first month:\n" .
				"Full interest for first month: $loan->loan_principal × $monthly_interest_rate = $full_monthly_interest\n" .
				"Days elapsed after first month: $days_elapsed\n" .
				"Daily interest: $full_monthly_interest ÷ 30 = $daily_interest\n" .
				"Daily accrual: $daily_interest × $days_elapsed = $daily_accrual\n" .
				"Total interest: $full_monthly_interest + $daily_accrual = $accrued_interest\n".
				"Total interest balance after payment of $amount_paid is : $amount_paid - $accrued_interest = $real_interest_balance \n";
		}

		// Total payoff amount
		$total_payoff = $remaining_principal + $real_interest_balance;

		echo json_encode( [
			'status' => 'success',
			'current_balance' => number_format($remaining_principal, 2, '.', ''),
			'accrued_interest' => number_format($accrued_interest, 2, '.', ''),
			'accrued_interest_balance' => number_format($real_interest_balance, 2, '.', ''),
			'total_payoff' => number_format($total_payoff, 2, '.', ''),
			'debug' => [
				'loan_id' => $loan_id,
				'payoff_date' => $payoff_date,
				'loan_date' => $loan->loan_date,
				'next_month_date' => $next_month_date->format('Y-m-d'),
				'loan_principal' => $loan->loan_principal,
				'monthly_interest_rate' => $monthly_interest_rate,
				'full_monthly_interest' => $full_monthly_interest,
				'daily_interest' => $daily_interest,
				'remaining_principal' => $remaining_principal,
				'calculation_explanation' => $calculation_explanation,
				'elapsed_days' => $days_elapsed1
			]
		] );

		
	}
	public function calculate_payoff_inline($lid) {


		$loan_id = $lid;
		$payoff_date = date('Y-m-d');
//		$payoff_date = date('2025-8-5');



		$loan = $this->Loan_model->get_by_id($loan_id);


		// Get all unpaid schedules
		$unpaid_schedules = $this->Payement_schedules_model->get_unpaid_schedules($loan_id);

		// Calculate remaining principal
		$remaining_principal = 0;
		$amount_paid = 0;
		foreach ($unpaid_schedules as $schedule) {

			//remaining principal is total after deducting current accrued interest and that current accrued interest is fully paid and the remainder or remaining is what is deducted to $loan->loan_principal to get the principal balance
           $amount_paid += $schedule->paid_amount;
			// Check if partial paid
//			if ($schedule->partial_paid == 'YES') {
//				// Calculate remaining amount
//
//				$remaining_principal += $schedule->principal * (1 - ($schedule->paid_amount / $schedule->amount));
//			} else {
//				$remaining_principal += $schedule->principal;
//			}
		}

		// Convert loan date and payoff date to DateTime objects
		$loan_date = new DateTime($loan->loan_date);
		$payoff_date_obj = new DateTime($payoff_date);

		// Calculate monthly interest amount
		$monthly_interest_rate = $loan->loan_interest / 100; // Convert percentage to decimal
		$full_monthly_interest = $loan->loan_principal * $monthly_interest_rate;
		$daily_interest = $full_monthly_interest / 30; // Daily interest based on 30-day month

		// Initialize interest amount
		$accrued_interest = 0;

		// Get the next month date (one month after loan date)
		$next_month_date = clone $loan_date;
		$next_month_date->modify('+1 month');

		// Simple algorithm:
		// 1. If payoff date is before or equal to loan date + 1 month: full interest
		// 2. Otherwise: full interest + daily accruals for elapsed days beyond first month
		$calculation_explanation = "";
		$days_elapsed1 = 0;
		$real_interest_balance = 0;

		if ($payoff_date_obj <= $next_month_date) {
			// Case 1: Payoff within first month - charge full interest
			$accrued_interest = $full_monthly_interest;
			$interest_balance = abs($amount_paid - $accrued_interest);
			if($accrued_interest < $amount_paid){
				$real_interest_balance = 0;
				$remaining_principal = $loan->loan_principal - $interest_balance;
			}else{
				$real_interest_balance = $interest_balance;
				$remaining_principal = $loan->loan_principal;
			}


			$calculation_explanation = "Payoff within first month - full interest applied: " .
				"$remaining_principal × $monthly_interest_rate = $accrued_interest";
			$days_elapsed1 = 0;
		} else {
			// Case 2: Payoff after first month
			// Start with full interest for the first month
			$accrued_interest = $full_monthly_interest;

			// Calculate days elapsed after first month
			$days_elapsed = $payoff_date_obj->diff($next_month_date)->days;
			$days_elapsed1 = $days_elapsed;
			// Add daily accruals for elapsed days
			$daily_accrual = $daily_interest * $days_elapsed;
			$accrued_interest += $daily_accrual;
			$interest_balance = abs($amount_paid - $accrued_interest);
			if($accrued_interest < $amount_paid){
				$real_interest_balance = 0;
				$remaining_principal = $loan->loan_principal - $interest_balance;
			}else{
				$real_interest_balance = $interest_balance;
				$remaining_principal = $loan->loan_principal;
			}
			$calculation_explanation = "Payoff after first month:\n" .
				"Full interest for first month: $loan->loan_principal × $monthly_interest_rate = $full_monthly_interest\n" .
				"Days elapsed after first month: $days_elapsed\n" .
				"Daily interest: $full_monthly_interest ÷ 30 = $daily_interest\n" .
				"Daily accrual: $daily_interest × $days_elapsed = $daily_accrual\n" .
				"Total interest: $full_monthly_interest + $daily_accrual = $accrued_interest\n".
				"Total interest balance after payment of $amount_paid is : $amount_paid - $accrued_interest = $real_interest_balance \n";
		}

		// Total payoff amount
		$total_payoff = $remaining_principal + $real_interest_balance;

		return [
			'status' => 'success',
			'current_balance' => number_format($remaining_principal, 2, '.', ''),
			'accrued_interest' => number_format($accrued_interest, 2, '.', ''),
			'accrued_interest_balance' => number_format($real_interest_balance, 2, '.', ''),
			'total_payoff' => number_format($total_payoff, 2, '.', ''),
			'debug' => [
				'loan_id' => $loan_id,
				'payoff_date' => $payoff_date,
				'loan_date' => $loan->loan_date,
				'next_month_date' => $next_month_date->format('Y-m-d'),
				'loan_principal' => $loan->loan_principal,
				'monthly_interest_rate' => $monthly_interest_rate,
				'full_monthly_interest' => $full_monthly_interest,
				'daily_interest' => $daily_interest,
				'remaining_principal' => $remaining_principal,
				'calculation_explanation' => $calculation_explanation,
				'elapsed_days' => $days_elapsed1
			]
		 ] ;
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
}
