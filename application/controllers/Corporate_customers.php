<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Corporate_customers extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Corporate_customers_model');
        $this->load->model('Geo_countries_model');
        $this->load->model('Individual_customers_model');
        $this->load->model('Branches_model');
        $this->load->model('Authorized_signitories_model');
        $this->load->model('Currency_model');
        $this->load->model('Account_model');
        $this->load->model('Corporate_shareholders_model');
        $this->load->library('form_validation');
        $this->load->model('Shareholders_model');
    }

    public function index()
    {


        $data = array(
            'corporate_customers_data' => get_all('corporate_customers'),

        );
        $menu_toggle['toggles'] = 43;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('corporate_customers/corporate_customers_list',$data);
        $this->load->view('admin/footer');

    }
    public function approve()
    {


        $data = array(
            'corporate_customers_data' => $this->Corporate_customers_model->get_status('Not Approved')

        );
        $menu_toggle['toggles'] = 43;
        $this->load->view('admin/header',$menu_toggle);
        $this->load->view('corporate_customers/approve',$data);
        $this->load->view('admin/footer');
    }
    public function approved()
    {



        $data = array(
            'corporate_customers_data' => $this->Corporate_customers_model->get_status('Approved')

        );
        $menu_toggle['toggles'] = 43;
        $this->load->view('admin/header',$menu_toggle);
        $this->load->view('corporate_customers/approved',$data);
        $this->load->view('admin/footer');
    }
    public function read($id) 
    {
        $row = $this->Corporate_customers_model->get_by_id($id);
        if ($row) {
            $data = array(
		'id' => $row->id,
		'EntityName' => $row->EntityName,
		'DateOfIncorporation' => $row->DateOfIncorporation,
		'RegistrationNumber' => $row->RegistrationNumber,
                'entity_type' => $row->entity_type,
		'category' => $row->category,

		'ClientId' => $row->ClientId,
		'TaxIdentificationNumber' => $row->TaxIdentificationNumber,
		'Country' => $row->Country,
		'Branch' => $row->Branch,
		'Status' => $row->Status,
                'nature_of_business'=>$row->nature_of_business,
                'industry_sector'=>$row->industry_sector,
                'street'=>$row->street,
                'city_town'=>$row->city_town,
                'province'=>$row->province,
                'postal_code'=>$row->postal_code,
                'phone_number'=>$row->phone_number,
                'contact_email'=>$row->contact_email,
                'website'=>$row->website,
		'LastUpdatedOn' => $row->LastUpdatedOn,
		'CreatedOn' => $row->CreatedOn,
		'company_certificate' =>  $row->company_certificate,
		'tax_id_doc' =>  $row->tax_id_doc,
                'proof_physical_address' =>  $row->proof_physical_address,
                'financial_statement' =>  $row->financial_statement
	    );

            $menu_toggle['toggles'] = 43;
            $this->load->view('admin/header',$menu_toggle);
            $this->load->view('corporate_customers/corporate_customers_read',$data);
            $this->load->view('admin/footer');
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('corporate_customers'));
        }
    }

    public function create() 
    {
        $data = array(
            'button' => 'Create',
            'action' => site_url('corporate_customers/create_act'),
	    'id' => set_value('id'),
	    'EntityName' => set_value('EntityName'),
	    'DateOfIncorporation' => set_value('DateOfIncorporation'),
	    'RegistrationNumber' => set_value('RegistrationNumber'),
            'entity_type' =>set_value('entity_type'),
            'company_certificate' => set_value('company_certificate'),
            'tax_id_doc'=>set_value('tax_id_doc'),
            'proof_physical_address'=>set_value('proof_physical_address'),
            'financial_statement'=>set_value('financial_statement'),

	    'ClientId' => set_value('ClientId'),
	    'TaxIdentificationNumber' => set_value('TaxIdentificationNumber'),
	    'Country' => set_value('Country'),
	    'Branch' => set_value('Branch'),
	    'Status' => set_value('Status'),
            'nature_of_business'=>set_value('nature_of_business'),
            'industry_sector'=>set_value('industry_sector'),
            'street'=>set_value('street'),
            'city_town'=>set_value('city_town'),
            'province'=>set_value('province'),
            'postal_code'=>set_value('postal_code'),
            'category'=>set_value('category'),
            'phone_number'=>set_value('phone_number'),
            'contact_email'=>set_value('contact_email'),
            'website'=>set_value('website'),
	    //
            'title' => set_value('title'),
            'first_name' => set_value('first_name'),
            'last_name' => set_value('last_name'),
            'gender' => set_value('gender'),
            'approval_status' => set_value('approval_status', 'pending'), // Default to 'pending'
            'nationality' => set_value('nationality'),
            'email_address' => set_value('email_address'),
            'full_address' => set_value('full_address'),
            //
	    'LastUpdatedOn' => set_value('LastUpdatedOn'),
	    'CreatedOn' => set_value('CreatedOn'),

	);
        $menu_toggle['toggles'] = 43;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('corporate_customers/corporate_customers_form',$data);
        $this->load->view('admin/footer');
    }
    



        public function create_action()
    {
//        $dd = $this->Individual_customers_model->count_it();
//        $d1 = $this->Corporate_customers_model->count_it();
//        $clientid = (2010000)+($dd+$d1+1);
//        $this->load->library('upload');//loading the library
//        $imagePath = realpath(APPPATH . '../uploads/');//this is your real path APPPATH means you are at the application folder
//
//        $this->_rules();
//
//        if ($this->form_validation->run() == FALSE) {
//            $this->create();
//        } else {
//            // Corporate customer data
//
//                $_FILES['userfile']['name']     = $_FILES['company_certificate']['name'];
//                $_FILES['userfile']['type']     = $_FILES['company_certificate']['type'];
//                $_FILES['userfile']['tmp_name'] = $_FILES['company_certificate']['tmp_name'];
//                $_FILES['userfile']['error']    = $_FILES['company_certificate']['error'];
//                $_FILES['userfile']['size']     = $_FILES['company_certificate']['size'];
//                //configuration for upload your images
//                $config = array(
//                    'file_name'     => rand(100,1000).$_FILES['userfile']['name'],
//                    'allowed_types' => '*',
//                    'max_size'      => 200000,
//                    'overwrite'     => FALSE,
//                    'upload_path'
//                    =>$imagePath
//                );
//                $this->upload->initialize($config);
//
//                if (!$this->upload->do_upload())
//                {
//                    $error = array('error' => $this->upload->display_errors());
//                    $carImages[] = array(
//                        'errors'=> $error
//                    );
//                }
//                else
//                {
//
//                    $filename = $this->upload->data();
//
//                    $company_certificatefile=str_replace(' ', '_', $config['file_name']);;
//
//
//                }
//                //file 2
//            $_FILES['userfile']['name']     = $_FILES['proof_physical_address']['name'];
//            $_FILES['userfile']['type']     = $_FILES['proof_physical_address']['type'];
//            $_FILES['userfile']['tmp_name'] = $_FILES['proof_physical_address']['tmp_name'];
//            $_FILES['userfile']['error']    = $_FILES['proof_physical_address']['error'];
//            $_FILES['userfile']['size']     = $_FILES['proof_physical_address']['size'];
//            //configuration for upload your images
//            $config = array(
//                'file_name'     => rand(100,1000).$_FILES['userfile']['name'],
//                'allowed_types' => '*',
//                'max_size'      => 200000,
//                'overwrite'     => FALSE,
//                'upload_path'
//                =>$imagePath
//            );
//            $this->upload->initialize($config);
//
//            if (!$this->upload->do_upload())
//            {
//                $error = array('error' => $this->upload->display_errors());
//                $carImages[] = array(
//                    'errors'=> $error
//                );
//            }
//            else
//            {
//
//                $filename = $this->upload->data();
//
//                $proof_physical_addressfile=str_replace(' ', '_', $config['file_name']);;
//
//
//            }
//            // file 3
//
//            $_FILES['userfile']['name']     = $_FILES['financial_statement']['name'];
//            $_FILES['userfile']['type']     = $_FILES['financial_statement']['type'];
//            $_FILES['userfile']['tmp_name'] = $_FILES['financial_statement']['tmp_name'];
//            $_FILES['userfile']['error']    = $_FILES['financial_statement']['error'];
//            $_FILES['userfile']['size']     = $_FILES['financial_statement']['size'];
//            //configuration for upload your images
//            $config = array(
//                'file_name'     => rand(100,1000).$_FILES['userfile']['name'],
//                'allowed_types' => '*',
//                'max_size'      => 200000,
//                'overwrite'     => FALSE,
//                'upload_path'
//                =>$imagePath
//            );
//            $this->upload->initialize($config);
//
//            if (!$this->upload->do_upload())
//            {
//                $error = array('error' => $this->upload->display_errors());
//                $carImages[] = array(
//                    'errors'=> $error
//                );
//            }
//            else
//            {
//
//                $filename = $this->upload->data();
//
//                $financial_statementfile=str_replace(' ', '_', $config['file_name']);;
//
//
//            }
//
//            //file 4
//            $_FILES['userfile']['name']     = $_FILES['tax_id_doc']['name'];
//            $_FILES['userfile']['type']     = $_FILES['tax_id_doc']['type'];
//            $_FILES['userfile']['tmp_name'] = $_FILES['tax_id_doc']['tmp_name'];
//            $_FILES['userfile']['error']    = $_FILES['tax_id_doc']['error'];
//            $_FILES['userfile']['size']     = $_FILES['tax_id_doc']['size'];
//            //configuration for upload your files
//            $config = array(
//                'file_name'     => rand(100,1000).$_FILES['userfile']['name'],
//                'allowed_types' => '*',
//                'max_size'      => 200000,
//                'overwrite'     => FALSE,
//                'upload_path'
//                =>$imagePath
//            );
//            $this->upload->initialize($config);
//
//            if (!$this->upload->do_upload())
//            {
//                $error = array('error' => $this->upload->display_errors());
//                $carImages[] = array(
//                    'errors'=> $error
//                );
//            }
//            else
//            {
//
//                $filename = $this->upload->data();
//
//                $tax_id_docfile=str_replace(' ', '_', $config['file_name']);;
//
//
//            }
//
//
//            $data = array(
//                'EntityName' => $this->input->post('EntityName',TRUE),
//                'DateOfIncorporation' => $this->input->post('DateOfIncorporation',TRUE),
//                'RegistrationNumber' => $this->input->post('RegistrationNumber',TRUE),
//                'entity_type' => $this->input->post('entity_type', True),
//                'ClientId' => $clientid,
//                'TaxIdentificationNumber' => $this->input->post('TaxIdentificationNumber',TRUE),
//                'Country' => $this->input->post('Country',TRUE),
//                'Branch' => $this->input->post('Branch',TRUE),
//                'nature_of_business' => $this->input->post('nature_of_business',TRUE),
//                'industry_sector' => $this->input->post('industry_sector',TRUE),
//                'street' => $this->input->post('street',TRUE),
//                'category' => $this->input->post('category',TRUE),
//                'phone_number' => $this->input->post('corporate_phone',TRUE),
//                'city_town' => $this->input->post('city_town',TRUE),
//                'contact_email' => $this->input->post('contact_email',TRUE),
//                'website' => $this->input->post('website',TRUE),
//                'company_certificate' =>  $company_certificatefile,
//                'tax_id_doc'=>$tax_id_docfile,
//                'proof_physical_address'=>$proof_physical_addressfile,
//                'financial_statement'=> $financial_statementfile,
//                'added_by' => $this->session->userdata('user_id')
//            );
//
//            $insert_id = $this->Corporate_customers_model->insert($data);

            $number_of_collateral = count($_FILES['collateralfiles']['name']);
            print_r($number_of_collateral);
            exit();

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

                    $filename = $this->upload->data();




                    $data = array(
                        'collateral_loan_id' => $result,
                        'collateral_name' => $coname[$i],
                        'collateral_type' => $type[$i],
                        'collateral_serial' => $serial[$i],
                        'collateral_value' => $cvalue[$i],
                        'collateral_file' => $config['file_name'],
                        'collateral_desc' => $desc[$i],

                    );

                   // $this->Loan_files_model->insert_collateral($data);

                }//if file uploaded

            }
            $shareholders = $this->input->post('shareholders', TRUE);
            print_r($shareholders);
            exit();

            if (!empty($shareholders)) {
                foreach ($shareholders as $index => $shareholder) {
                    $data = array(
                        'title' => $shareholder['title'],
                        'first_name' => $shareholder['first_name'],
                        'last_name' => $shareholder['last_name'],
                        'gender' => $shareholder['gender'],
                        'nationality' => $shareholder['nationality'],
                        'phone_number' => $shareholder['phone_number'],
                        'email_address' => $shareholder['email_address'],
                        'full_address' => $shareholder['full_address'],
                        'ownership_percentage' => $shareholder['ownership_percentage'],
                        'IDType' => $shareholder['IDType']
                    );

                    // Handle file upload for each shareholder
                    if (!empty($_FILES['shareholders']['name'][$index]['Identificationfiles'])) {
                        $config['upload_path'] = './uploads/';
                        $config['allowed_types'] = 'gif|jpg|jpeg|png|pdf';
                        $config['max_size'] = 2048; // 2MB max-size

                        $this->load->library('upload', $config);
                        $_FILES['file']['name'] = $_FILES['shareholders']['name'][$index]['Identificationfiles'];
                        $_FILES['file']['type'] = $_FILES['shareholders']['type'][$index]['Identificationfiles'];
                        $_FILES['file']['tmp_name'] = $_FILES['shareholders']['tmp_name'][$index]['Identificationfiles'];
                        $_FILES['file']['error'] = $_FILES['shareholders']['error'][$index]['Identificationfiles'];
                        $_FILES['file']['size'] = $_FILES['shareholders']['size'][$index]['Identificationfiles'];

                        if ($this->upload->do_upload('file')) {
                            $uploadData = $this->upload->data();
                            $data['identification_document'] = $uploadData['file_name'];
                        }
                    }

                    // Insert or update shareholder data
                    // $this->shareholder_model->save($data);
                }
            }

            // Handle shareholders data
            $titles = $this->input->post('title', TRUE);
            $first_names = $this->input->post('first_name', TRUE);
            $last_names = $this->input->post('last_name', TRUE);
            $genders = $this->input->post('gender', TRUE);
            $nationalities = $this->input->post('nationality', TRUE);
            $phone_numbers = $this->input->post('phone_number', TRUE);
            $email_addresses = $this->input->post('email_address', TRUE);
            $full_addresses = $this->input->post('full_address', TRUE);
            $ownership_percentages = $this->input->post('ownership_percentage', TRUE);
            $IDType = $this->input->post('IDType', TRUE);
            //$Identificationfiles = $this->input->post('Identificationfiles', TRUE);

            // Process each shareholder
            print_r($titles);
            exit();

            if (!empty($titles)) {


                for ($i = 0; $i < count($titles); $i++) {
                    // Initialize ID file variable
                    $idfile = null;

                    // Handle file upload if a file was provided
                    if (isset($_FILES['Identificationfiles']['name'][$i]) && !empty($_FILES['Identificationfiles']['name'][$i])) {
                        // Prepare the file data for this iteration
                        $_FILES['userfile']['name']     = $_FILES['Identificationfiles']['name'][$i];
                        $_FILES['userfile']['type']     = $_FILES['Identificationfiles']['type'][$i];
                        $_FILES['userfile']['tmp_name'] = $_FILES['Identificationfiles']['tmp_name'][$i];
                        $_FILES['userfile']['error']    = $_FILES['Identificationfiles']['error'][$i];
                        $_FILES['userfile']['size']     = $_FILES['Identificationfiles']['size'][$i];

                        // Configuration for upload
                        $config = array(
                            'file_name'     => uniqid() . '_' . str_replace(' ', '_', $_FILES['userfile']['name']),
                            'allowed_types' => '*',
                            'max_size'      => 200000,
                            'overwrite'     => FALSE,
                            'upload_path'   => $imagePath
                        );

                        $this->upload->initialize($config);

                        if (!$this->upload->do_upload('userfile')) {
                            $error = $this->upload->display_errors();
                            log_message('error', 'File upload failed: ' . $error);
                        } else {
                            $upload_data = $this->upload->data();
                            $idfile = $config['file_name'];
                        }
                    }

                    // Prepare shareholder data
                    $shareholder_data = array(
                        'title'         => $titles[$i],
                        'first_name'    => $first_names[$i],
                        'last_name'     => $last_names[$i],
                        'gender'        => $genders[$i],
                        'nationality'   => $nationalities[$i],
                        'phone_number'  => $phone_numbers[$i],
                        'email_address' => $email_addresses[$i],
                        'full_address'  => $full_addresses[$i],
                        'added_by'      => $this->session->userdata('user_id'),
                        'idtype'        => $IDType[$i],
                        'idfile'        => $idfile
                    );

                    // Insert shareholder and get the ID
                    $shareholder_id = $this->Shareholders_model->insert($shareholder_data);

                    // Create corporate-shareholder relationship if shareholder was inserted successfully
                    if ($shareholder_id) {
                        $corporate_shareholder_data = array(
                            'corporate_id'      => $insert_id,
                            'shareholder_id'    => $shareholder_id,
                            'percentage_value'  => $ownership_percentages[$i]
                        );

                        $this->Corporate_shareholders_model->insert($corporate_shareholder_data);
                    }
                }
            }

            // Log the activity
            $logger = array(
                'user_id' => $this->session->userdata('user_id'),
                'activity' => 'Register corporate customer '.$data['EntityName'],
                'activity_cate' => 'corporate_customer_registration',
            );
            log_activity($logger);

            // Create account
            $at = get_all_by_id('account','account_type','1');
            $ct = 1;
            foreach ($at as $cc){
                $ct ++;
            }
            $account = 300000+$ct.rand(0,9999);
            $account_data = array(
                'client_id' => $insert_id,
                'account_number' => $account,
                'balance' => 0,
                'account_type' => 1,
                'account_type_product' => 2,
                'added_by' => $this->session->userdata('user_id'),
            );

            $this->Account_model->insert($account_data);
            $this->toaster->success('Success, Corporate customer was created pending approval');

            redirect(site_url('corporate_customers'));
        //}
    }
    
    public function update($id) 
    {
        $row = $this->Corporate_customers_model->get_by_id($id);

        if ($row) {
            $data = array(
                'button' => 'Update',
                'action' => site_url('corporate_customers/update_action'),
		'id' => set_value('id', $row->id),
		'EntityName' => set_value('EntityName', $row->EntityName),
		'DateOfIncorporation' => set_value('DateOfIncorporation', $row->DateOfIncorporation),
		'RegistrationNumber' => set_value('RegistrationNumber', $row->RegistrationNumber),
                'entity_type' =>set_value('entity_type',  $row->entity_type),
		'ClientId' => set_value('ClientId', $row->ClientId),
		'TaxIdentificationNumber' => set_value('TaxIdentificationNumber', $row->TaxIdentificationNumber),
		'Country' => set_value('Country', $row->Country),
		'Branch' => set_value('Branch', $row->Branch),
                'province' => set_value('province', $row->province),
                'nature_of_business' => set_value('nature_of_business',$row->nature_of_business),
                'industry_sector' => set_value('industry_sector',$row->industry_sector),
                'street' => set_value('street',$row->street),
                'postal_code' => set_value('postal_code',$row->postal_code),
                'phone_number' => set_value('phone_number',$row->phone_number),
                'city_town' => set_value('city_town',$row->city_town),
                'contact_email' =>set_value('contact_email', $row->contact_email),
                'website' => set_value('website',$row->website),
				'company_certificate' => set_value('company_certificate', $row->company_certificate),
                'tax_id_doc'=>set_value('tax_id_doc', $row->tax_id_doc),
                'proof_physical _address'=>set_value('proof_physical_address',  $row->financial_statement),
                'financial_statement'=>set_value('financial_statement',  $row->financial_statement),

	    );
            $menu_toggle['toggles'] = 43;
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('corporate_customers/corporate_edit',$data);
            $this->load->view('admin/footer');
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('corporate_customers'));
        }
    }

    public  function create_act()
    {


        $dd = $this->Individual_customers_model->count_it();
        $d1 = $this->Corporate_customers_model->count_it();
        $clientid = (2010000) + ($dd + $d1 + 1);
        $this->load->library('upload');//loading the library
        $imagePath = realpath(APPPATH . '../uploads/');//this is your real path APPPATH means you are at the application folder

        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->create();
        }
        else {
            // Corporate customer data

            $_FILES['userfile']['name'] = $_FILES['company_certificate']['name'];
            $_FILES['userfile']['type'] = $_FILES['company_certificate']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['company_certificate']['tmp_name'];
            $_FILES['userfile']['error'] = $_FILES['company_certificate']['error'];
            $_FILES['userfile']['size'] = $_FILES['company_certificate']['size'];
            //configuration for upload your images
            $config = array(
                'file_name' => rand(100, 1000) . $_FILES['userfile']['name'],
                'allowed_types' => '*',
                'max_size' => 200000,
                'overwrite' => FALSE,
                'upload_path'
                => $imagePath
            );
            $this->upload->initialize($config);

            if (!$this->upload->do_upload()) {
                $error = array('error' => $this->upload->display_errors());
                $carImages[] = array(
                    'errors' => $error
                );
            }
            else {

                $filename = $this->upload->data();

                $company_certificatefile = str_replace(' ', '_', $config['file_name']);;


            }
            //file 2
            $_FILES['userfile']['name'] = $_FILES['proof_physical_address']['name'];
            $_FILES['userfile']['type'] = $_FILES['proof_physical_address']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['proof_physical_address']['tmp_name'];
            $_FILES['userfile']['error'] = $_FILES['proof_physical_address']['error'];
            $_FILES['userfile']['size'] = $_FILES['proof_physical_address']['size'];
            //configuration for upload your images
            $config = array(
                'file_name' => rand(100, 1000) . $_FILES['userfile']['name'],
                'allowed_types' => '*',
                'max_size' => 200000,
                'overwrite' => FALSE,
                'upload_path'
                => $imagePath
            );
            $this->upload->initialize($config);

            if (!$this->upload->do_upload()) {
                $error = array('error' => $this->upload->display_errors());
                $carImages[] = array(
                    'errors' => $error
                );
            }
            else {

                $filename = $this->upload->data();

                $proof_physical_addressfile = str_replace(' ', '_', $config['file_name']);;


            }
            // file 3

            $_FILES['userfile']['name'] = $_FILES['financial_statement']['name'];
            $_FILES['userfile']['type'] = $_FILES['financial_statement']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['financial_statement']['tmp_name'];
            $_FILES['userfile']['error'] = $_FILES['financial_statement']['error'];
            $_FILES['userfile']['size'] = $_FILES['financial_statement']['size'];
            //configuration for upload your images
            $config = array(
                'file_name' => rand(100, 1000) . $_FILES['userfile']['name'],
                'allowed_types' => '*',
                'max_size' => 200000,
                'overwrite' => FALSE,
                'upload_path'
                => $imagePath
            );
            $this->upload->initialize($config);

            if (!$this->upload->do_upload()) {
                $error = array('error' => $this->upload->display_errors());
                $carImages[] = array(
                    'errors' => $error
                );
            }
            else {

                $filename = $this->upload->data();

                $financial_statementfile = str_replace(' ', '_', $config['file_name']);;


            }

            //file 4
            $_FILES['userfile']['name'] = $_FILES['tax_id_doc']['name'];
            $_FILES['userfile']['type'] = $_FILES['tax_id_doc']['type'];
            $_FILES['userfile']['tmp_name'] = $_FILES['tax_id_doc']['tmp_name'];
            $_FILES['userfile']['error'] = $_FILES['tax_id_doc']['error'];
            $_FILES['userfile']['size'] = $_FILES['tax_id_doc']['size'];
            //configuration for upload your files
            $config = array(
                'file_name' => rand(100, 1000) . $_FILES['userfile']['name'],
                'allowed_types' => '*',
                'max_size' => 200000,
                'overwrite' => FALSE,
                'upload_path'
                => $imagePath
            );
            $this->upload->initialize($config);

            if (!$this->upload->do_upload())
            {
                $error = array('error' => $this->upload->display_errors());
                $carImages[] = array(
                    'errors' => $error
                );
            }
            else {

                $filename = $this->upload->data();

                $tax_id_docfile = str_replace(' ', '_', $config['file_name']);;


            }


            $data = array(
                'EntityName' => $this->input->post('EntityName', TRUE),
                'DateOfIncorporation' => $this->input->post('DateOfIncorporation', TRUE),
                'RegistrationNumber' => $this->input->post('RegistrationNumber', TRUE),
                'entity_type' => $this->input->post('entity_type', True),
                'ClientId' => $clientid,
                'TaxIdentificationNumber' => $this->input->post('TaxIdentificationNumber', TRUE),
                'Country' => $this->input->post('Country', TRUE),
                'Branch' => $this->input->post('Branch', TRUE),
                'nature_of_business' => $this->input->post('nature_of_business', TRUE),
                'industry_sector' => $this->input->post('industry_sector', TRUE),
                'street' => $this->input->post('street', TRUE),
                'category' => $this->input->post('category', TRUE),
                'phone_number' => $this->input->post('corporate_phone', TRUE),
                'city_town' => $this->input->post('city_town', TRUE),
                'contact_email' => $this->input->post('contact_email', TRUE),
                'website' => $this->input->post('website', TRUE),
                'company_certificate' => $company_certificatefile,
                'tax_id_doc' => $tax_id_docfile,
                'proof_physical_address' => $proof_physical_addressfile,
                'financial_statement' => $financial_statementfile,
                'added_by' => $this->session->userdata('user_id')
            );

            $insert_id = $this->Corporate_customers_model->insert($data);


            $titles = $this->input->post('title');
            $first_names = $this->input->post('first_name');
            $last_names = $this->input->post('last_name');
            $genders = $this->input->post('gender');

            $nationalities = $this->input->post('nationality');
            $phone_numbers = $this->input->post('phone_number');
            $email_addresses = $this->input->post('email_address');
            $full_addresses = $this->input->post('full_address');
            $idtypes = $this->input->post('idtype');
            $percentage_values = $this->input->post('percentage_value'); // Capture percentage value

            // Handling file uploads
            $idfiles = $_FILES['idfile'];

            if (!empty($titles) && !empty($first_names)) {
                $data1 = [];
                for ($i = 0; $i < count($titles); $i++) {
                    $filename = null;

                    if (!empty($idfiles['name'][$i])) {
                        $_FILES['file']['name'] = $idfiles['name'][$i];
                        $_FILES['file']['type'] = $idfiles['type'][$i];
                        $_FILES['file']['tmp_name'] = $idfiles['tmp_name'][$i];
                        $_FILES['file']['error'] = $idfiles['error'][$i];
                        $_FILES['file']['size'] = $idfiles['size'][$i];

                        $config['upload_path'] = './uploads/';
                        $config['allowed_types'] = 'jpg|png|pdf';
                        $config['max_size'] = 2048;
                        $config['file_name'] = time() . '_' . $_FILES['file']['name'];

                        $this->load->library('upload', $config);

                        if ($this->upload->do_upload('file')) {
                            $filename = $this->upload->data('file_name');
                        }
                    }

                    $data1[] = [
                        'title' => $titles[$i],
                        'first_name' => $first_names[$i],
                        'last_name' => $last_names[$i],
                        'gender' => $genders[$i],

                        'nationality' => $nationalities[$i],
                        'phone_number' => $phone_numbers[$i],
                        'email_address' => $email_addresses[$i],
                        'full_address' => $full_addresses[$i],
                        'idtype' => $idtypes[$i],
                        'idfile' => $filename,
                        'added_by' => $this->session->userdata('user_id'),  // Change this to the logged-in user
                    ];
                }

                $shareholder_ids =  $this->Shareholders_model->insert_batch($data1);
                // Ensure the inserted ID count matches the percentage values count
                if (count($shareholder_ids) === count($percentage_values)) {
                    for ($i = 0; $i < count($shareholder_ids); $i++) {
                        $corporate_shareholder = array(
                            'corporate_id' => $insert_id,  // Ensure this is defined
                            'shareholder_id' => $shareholder_ids[$i], // Get ID properly
                            'percentage_value' => $percentage_values[$i], // Assign corresponding percentage value
                        );
                        $this->Corporate_shareholders_model->insert($corporate_shareholder);
                    }
                }
                $this->session->set_flashdata('success', 'Shareholders added successfully!');
                $this->index();
            }
            else {
                $this->session->set_flashdata('error', 'Please fill in all fields.');
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }
    public function update_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('id', TRUE));
        } else {
            $data = array(
		'EntityName' => $this->input->post('EntityName',TRUE),
		'DateOfIncorporation' => $this->input->post('DateOfIncorporation',TRUE),
		'RegistrationNumber' => $this->input->post('RegistrationNumber',TRUE),
                'entity_type' => $this->input->post('entity_type', True),
		'ClientId' => $this->input->post('ClientId',TRUE),
		'TaxIdentificationNumber' => $this->input->post('TaxIdentificationNumber',TRUE),
		'Country' => $this->input->post('Country',TRUE),
		'Branch' => $this->input->post('Branch',TRUE),
                'nature_of_business' => $this->input->post('nature_of_business',TRUE),
                'industry_sector' => $this->input->post('industry_sector',TRUE),
                'street' => $this->input->post('street',TRUE),
                'province' => $this->input->post('province',TRUE),
                'postal_code' => $this->input->post('postal_code',TRUE),
                'phone_number' => $this->input->post('phone_number',TRUE),
                'city_town' => $this->input->post('city_town',TRUE),
                'contact_email' => $this->input->post('contact_email',TRUE),
                'website' => $this->input->post('website',TRUE),

                'company_certificate' => $this->input->post('company_certificate', TRUE),
                'tax_id_doc'=>$this->input->post('tax_id_doc', TRUE),
                'proof_physical_address'=>$this->input->post('proof_physical_address', TRUE),
                'financial_statement'=>$this->input->post('financial_statement', TRUE),
	    );

            // Log the activity
            $logger = array(
                'user_id' => $this->session->userdata('user_id'),
                'activity' => 'Updated corporate customer '.$data['EntityName'],
                'activity_cate' => 'corporate_customer_registration',
            );
            log_activity($logger);

         $this->Corporate_customers_model->update($this->input->post('id', TRUE), $data);
            $this->toaster->success('Success, Corporate customer was updated');
            redirect(site_url('corporate_customers'));
        }
    }
    
    public function delete($id) 
    {
        $row = $this->Corporate_customers_model->get_by_id($id);

        if ($row) {
            $this->Corporate_customers_model->delete($id);
            $this->session->set_flashdata('message', 'Delete Record Success');
            redirect(site_url('corporate_customers'));
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('corporate_customers'));
        }
    }
    public function approval_action($id)
    {
        $row = $this->Corporate_customers_model->get_by_id($id);
        $notify = get_by_id('sms_settings','id','1');
        if ($row) {
            $this->Corporate_customers_model->update($id,array('approval_status'=>'Approved'));
            $this->Account_model->update_approval($id,array('account_status'=>'Active'));

            $logger = array(

                'user_id' => $this->session->userdata('user_id'),
                'activity' => 'corporate customer approved',
                'activity_cate' => 'customer_approval',

            );
            log_activity($logger);
            $this->toaster->success('Customer was approved successfully');
            redirect(site_url('corporate_customers/approve'));
        } else {

        }
    }
    public function reject_action($id)
    {
        $row = $this->Corporate_customers_model->get_by_id($id);

        if ($row) {
            $this->Corporate_customers_model->update($id,array('approval_status'=>'Rejected'));
            $this->toaster->success('Customer was approved successfully');
            redirect(site_url('corporate_customers/approve'));
        } else {

        }
    }
    public function _rules() 
    {
	$this->form_validation->set_rules('EntityName', 'entityname', 'trim|required');
	$this->form_validation->set_rules('DateOfIncorporation', 'dateofincorporation', 'trim|required');
	$this->form_validation->set_rules('RegistrationNumber', 'registrationnumber', 'trim');
	$this->form_validation->set_rules('TaxIdentificationNumber', 'taxidentificationnumber', 'trim|required');
	$this->form_validation->set_rules('Country', 'country', 'trim|required');
	$this->form_validation->set_rules('Branch', 'branch', 'trim|required');
	$this->form_validation->set_rules('id', 'id', 'trim');
	$this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

