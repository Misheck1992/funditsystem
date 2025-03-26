<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
require_once APPPATH . "/third_party/Spout/Autoloader/autoload.php";
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;


class Groups extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Group_categories_model');
        $this->load->model('Groups_model');
        $this->load->model('Customer_groups_model');
        $this->load->model('Account_model');
        $this->load->model('Access_model');
        $this->load->model('Branches_model');
        $this->load->library('form_validation');
        $this->load->model('Individual_customers_model');
        $this->load->model('Proofofidentity_model');
    }
    public function index()
    {


        $data = array(
            'groups_data' => $this->Groups_model->get_all(),
        );
        $menu_toggle['toggles'] = 49;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('groups/groups_list',$data);
        $this->load->view('admin/footer');

    }
    public function assign_members()
    {


        $data = array(
            'groups_data' => $this->Groups_model->get_all(),
        );
        $menu_toggle['toggles'] = 49;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('groups/groups_list_assign',$data);
        $this->load->view('admin/footer');

    }
    public function approve()
    {


        $data = array(
            'groups_data' => $this->Groups_model->get_all(),
        );
        $menu_toggle['toggles'] = 49;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('groups/groups_list_approve',$data);
        $this->load->view('admin/footer');

    }

    public function read($id)
    {
        $row = $this->Groups_model->get_by_id($id);
        if ($row) {
            $data = array(
                'group_id' => $row->group_id,
                'group_code' => $row->group_code,
                'group_name' => $row->group_name,
                'group_category' => $row->group_category,
                'branch' => $row->branch,
                'group_description' => $row->group_description,
                'group_added_by' => $row->group_added_by,
                'group_registered_date' => $row->group_registered_date,
            );
            $this->load->view('groups/groups_read', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('groups'));
        }
    }


    public function view_group($id)
    {
        $row = $this->Groups_model->get_by_id($id);
        if ($row) {
            $data = array(
                'group_id' => $row->group_id,
                'group_code' => $row->group_code,
                'group_name' => $row->group_name,
                'group_category' => $row->group_category,
                'branch' => $row->branch,
                'group_description' => $row->group_description,
                'group_village' => $row->group_village,
                'group_ta' => $row->group_ta,
                'group_district' => $row->group_district,
                'group_address' => $row->group_address,
                'group_contact' => $row->group_contact,
                'file' => $row->file,
                'group_email' => $row->group_email,
                'group_established_date' => $row->group_established_date,

                'group_added_by' => $row->group_added_by,
                'group_registered_date' => $row->group_registered_date,
            );

            $menu_toggle['toggles'] = 49;
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('groups/view_group',$data);
            $this->load->view('admin/footer');
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('groups'));
        }
    }

    public function create()
    {
        $data = array(
            'button' => 'Create',
            'action' => site_url('groups/create_action'),
            'group_id' => set_value('group_id'),
            'group_code' => set_value('group_code'),
            'group_name' => set_value('group_name'),
            'group_category' => set_value('group_category'),
            'branch' => set_value('branch'),
            'group_description' => set_value('group_description'),
            'group_village' => set_value('group_village'),
            'group_ta' => set_value('group_ta'),
            'group_district' => set_value('group_district'),
            'group_address' => set_value('group_address'),
            'group_contact' => set_value('group_contact'),
            'group_email' => set_value('group_email'),
            'account_name' => set_value('account_name'),
            'account_number' => set_value('account_number'),
            'bank_name' => set_value('bank_name'),
            'bank_branch' => set_value('bank_branch'),

            'group_added_by' => set_value('group_added_by'),
            'group_registered_date' => set_value('group_registered_date'),
        );
        $menu_toggle['toggles'] = 49;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('groups/groups_form',$data);
        $this->load->view('admin/footer');

    }
    function validateIdNumber($idNumber) {
        return !empty($idNumber) && strlen($idNumber) == 8;
    }



    public function create_action()
    {
        $countercheck=0;
        $dd = $this->Individual_customers_model->count_it();


        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {

            $insertdata                   = array();
            set_time_limit(1000);

            if(!empty($_FILES['excelfile']['name']) && empty($this->input->post('customer')))
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

                                $idnumber=$row[4];
                                if(strlen($idnumber)!= 8){
                                    $countercheck= $countercheck++;

                                }
                                if(empty($idnumber)!= 8){
                                    $countercheck= $countercheck++;

                                }

                            }

                            $count++;

                        }


                        // Close excel file

                    }



                    if($countercheck==0) {




                        $data = array(
                            'group_code' =>rand(100,9999),
                            'group_name' => $this->input->post('group_name',TRUE),

                            'branch' => $this->input->post('branch',TRUE),
                            'group_description' => $this->input->post('group_description',TRUE),
                            'group_village' => $this->input->post('group_village',TRUE),
                            'group_ta' => $this->input->post('group_ta',TRUE),
                            'group_district' => $this->input->post('group_district',TRUE),
                            'group_address' => $this->input->post('group_address',TRUE),
                            'group_contact' => $this->input->post('group_contact',TRUE),
                            'group_email' => $this->input->post('group_email',TRUE),
                            'account_name' => $this->input->post('account_name',TRUE),
                            'account_number' =>$this->input->post('account_number',TRUE),
                            'bank_name' => $this->input->post('bank_name',TRUE),
                            'bank_branch' => $this->input->post('bank_branch',TRUE),

                            'file' => $this->input->post('file',TRUE),
                            'group_added_by' => $this->session->userdata('user_id')

                        );

                        $id= $this->Groups_model->insert($data);



                        $reader->open($file);
                        $count = 1;
                        foreach ($reader->getSheetIterator() as $sheet) {


                            // Number of Rows in Excel sheet
                            foreach ($sheet->getRowIterator() as $row) {
                                if ($count > 1 && !empty($row[1])) {



                                    $clientid = rand(100,1000).rand(100,9999);
                                    $insertdata['ClientId']=$clientid;
                                    $insertdata['Title']=$row[0];
                                    $insertdata['Firstname']=$row[1];
                                    $insertdata['Lastname']=$row[2];
                                    $insertdata['Gender']=$row[3];
                                    $insertdata['village']=$row[5];
                                    $insertdata['City']=$row[6];
                                    $insertdata['Country'] = 'Malawi';
                                    $insertdata['Branch' ]= 'Lilongwe';
                                    $insertdata['customer_type'] = 'group';
                                    $insertdata['added_by'] = $this->session->userdata('user_id');

                                    $insert_id =  $this->Individual_customers_model->insert($insertdata);

                                    $menu=array(
                                        'group_id'=>$id,
                                        'customer'=>$insert_id,

                                    );

                                    $this->Customer_groups_model->insert($menu);


                                    $logger = array(

                                        'user_id' => $this->session->userdata('user_id'),
                                        'activity' => 'Register a group  customer'.$row[1].' '.$row[2],

                                        'activity_cate' => 'customer_group_registration',


                                    );


                                    log_activity($logger);


                                    $at = get_all_by_id('account','account_type','1');
                                    $ct = 1;
                                    foreach ($at as $cc){
                                        $ct ++;
                                    }
                                    $account = 300000+$ct.rand(0,9999);
                                    $data = array(
                                        'client_id' => $insert_id,
                                        'account_number' => $account,
                                        'balance' => 0,
                                        'account_type' => 1,
                                        'account_type_product' => 2,

                                        'added_by' => $this->session->userdata('user_id'),

                                    );

                                    $this->Account_model->insert($data);

                                    $data = array(
                                        'IDType' => 'NATIONAL_IDENTITY_CARD',
                                        'IDNumber' => $insertdata['IDNumber']=$row[4],
                                        'ClientId' => $clientid,

                                    );
                                    $this->Proofofidentity_model->insert($data);

                                }


                                $count++;

                            }
                        }





                        $this->toaster->success('Success, Create of group was successful');
                        redirect(site_url('groups'));


                    }
                    else {

                        $this->toaster->error('Error, Sorry group failed to be added ');
                        redirect($_SERVER['HTTP_REFERER']);

                    }

                }
                else
                {
                    echo "Please Choose only Excel file";
                }
            }
            else
            {


                $data = array(
                    'group_code' =>rand(100,9999),
                    'group_name' => $this->input->post('group_name',TRUE),

                    'branch' => $this->input->post('branch',TRUE),
                    'group_description' => $this->input->post('group_description',TRUE),
                    'group_village' => $this->input->post('group_village',TRUE),
                    'group_ta' => $this->input->post('group_ta',TRUE),
                    'group_district' => $this->input->post('group_district',TRUE),
                    'group_address' => $this->input->post('group_address',TRUE),
                    'group_contact' => $this->input->post('group_contact',TRUE),
                    'group_email' => $this->input->post('group_email',TRUE),
                    'file' => $this->input->post('file',TRUE),
                    'group_added_by' => $this->session->userdata('user_id')

                );

                $id= $this->Groups_model->insert($data);
                $this->Customer_groups_model->add_members($this->input->post('customer'),$id);

                $logger = array(

                    'user_id' => $this->session->userdata('user_id'),
                    'activity' => 'updated role access',
                    'activity_cate' => 'group_registration',

                );
                log_activity($logger);
                $this->toaster->success('Success, Create of group was successful');
                redirect(site_url('groups'));

            }


        }
    }

    public function group_customers()
    {


        $data = array(
            'individual_customers_data' => $this->Individual_customers_model->get_group_customers(),

        );
        $menu_toggle['toggles'] = 43;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('groups/group_customers_list',$data);
        $this->load->view('admin/footer');
    }


    public function create_action_mass_insert()
    {
        $sampleDataCofi =  get_all_groups_filter();
        $addescofi=7;
        $cofibranch=6;
        $country='MW';

        foreach ( $sampleDataCofi as $rowcofi){
            $groupcode=rand(100,9999);
            if(!empty($rowcofi->LOCATION)){


                $dis=$rowcofi->LOCATION;

            }
            else{
                $dis='LILONGWE';
            }
            $data = array(
                'group_code' =>$groupcode,
                'group_name' => $rowcofi->CLIENTNAME,

                'branch' =>$cofibranch,
                'group_description' => $rowcofi->CLIENTNAME,
                'group_district' => $dis,

                'group_contact' => $rowcofi->PHONENUMBER,
                'group_status' => 'Active',


                'group_added_by' => $this->session->userdata('user_id')

            );

            $id= $this->Groups_model->insert($data);
            $data=array(
                'group_id' =>$id,
                'group_code' =>$groupcode
            );

            $this->Groups_model->updateother($rowcofi->id,'completedlimbenew' ,$data);
            $at = get_all_by_id('account','account_type','1');
            $ct = 1;
            foreach ($at as $cc){
                $ct ++;
            }
            $account = 3060000+$ct;
            $data = array(
                'client_id' => $id,
                'account_number' => $account,
                'balance' => 0,
                'account_type' => 1,
                'account_type_product' => 2,

                'added_by' => $this->session->userdata('user_id')

            );

            $this->Account_model->insert($data);



            $logger = array(

                'user_id' => $this->session->userdata('user_id'),
                'activity' => 'added group',
                'activity_cate' => 'group_registration',

            );

        }
        log_activity($logger);
        $this->toaster->success('Success, Create of group was successful');
        redirect(site_url('groups'));

    }

    public function update($id)
    {
        $row = $this->Groups_model->get_by_id($id);

        if ($row) {
            $data = array(
                'button' => 'Update',
                'action' => site_url('groups/update_action'),
                'group_id' => set_value('group_id', $row->group_id),
                'group_code' => set_value('group_code', $row->group_code),
                'group_name' => set_value('group_name', $row->group_name),
                'group_category' => set_value('group_category', $row->group_category),
                'branch' => set_value('branch', $row->branch),
                'group_description' => set_value('group_description', $row->group_description),
                'group_village' => set_value('group_village', $row->group_village),
                'group_ta' => set_value('group_ta', $row->group_ta),
                'group_district' => set_value('group_district', $row->group_district),
                'group_address' => set_value('group_address', $row->group_address),
                'group_contact' => set_value('group_contact', $row->group_contact),
                'group_email' => set_value('group_email', $row->group_email),

                'group_added_by' => set_value('group_added_by', $row->group_added_by),
                'file' => set_value('file', $row->file),
            );
            $menu_toggle['toggles'] = 49;
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('groups/groups_form_update',$data);
            $this->load->view('admin/footer');
        } else {
            $this->session->set_flashdata('error', 'Record Not Found');
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function update_action()
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('group_id', TRUE));
        } else {
            $data = array(

                'group_name' => $this->input->post('group_name',TRUE),

                'branch' => $this->input->post('branch',TRUE),
                'group_description' => $this->input->post('group_description',TRUE),
                'group_village' => $this->input->post('group_village',TRUE),
                'group_ta' => $this->input->post('group_ta',TRUE),
                'group_district' => $this->input->post('group_district',TRUE),
                'group_address' => $this->input->post('group_address',TRUE),
                'group_contact' => $this->input->post('group_contact',TRUE),
                'group_email' => $this->input->post('group_email',TRUE),
                'file' => $this->input->post('file',TRUE),

            );

            $this->Groups_model->update($this->input->post('group_id', TRUE), $data);
            $this->session->set_flashdata('success', 'Update Record Success');
            redirect(site_url('groups'));
        }
    }
    public function approval()
    {


        $comment = $this->input->post('comment',TRUE);
        $act = $this->input->post('action',TRUE);


        $data = array(

            'group_status' => $this->input->post('action',TRUE),
            'reject_comment' => $act ==="Rejected" ? $comment : 'Null',
            'approval_comment' => $act ==="Active" ? $comment : 'Null',

        );

        $this->Groups_model->update($this->input->post('group_id', TRUE), $data);
        $this->toaster->success('Success, your action was successful');
        redirect(site_url('groups/approve'));

    }

    public function delete($id)
    {
        $row = $this->Groups_model->get_by_id($id);

        if ($row) {
            $this->Groups_model->delete($id);
            $this->session->set_flashdata('message', 'Delete Record Success');
            redirect(site_url('groups'));
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('groups'));
        }
    }

    public function _rules()
    {

        $this->form_validation->set_rules('group_name', 'group name', 'trim|required');

        $this->form_validation->set_rules('branch', 'branch', 'trim|required');
        $this->form_validation->set_rules('group_description', 'group description', 'trim|required');


        $this->form_validation->set_rules('group_id', 'group_id', 'trim');
        $this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

