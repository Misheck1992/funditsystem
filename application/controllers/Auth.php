<?php


class Auth extends CI_Controller
{
public function __construct()
{
	parent::__construct();
	$this->load->library('form_validation');
	$this->load->model('User_access_model');
	$this->load->model('Sytem_date_model');
	$this->load->model('Access_model');
	$this->load->model('Loan_model');
    $this->load->model('Districts_model');
    $this->load->model('Activity_logger_model');
}
//function load login page
public function index(){
    $this->output->set_header('X-Content-Type-Options: nosniff');
	$this->load->view('login');
}
    public function forget_password(){
        $this->output->set_header('X-Content-Type-Options: nosniff');

        $this->load->view('forget');
    }
public  function list_auth(){
    $menu_toggle['toggles'] = 23;
    $this->load->view('admin/header', $menu_toggle);
    $this->load->view('admin/auth_list');
    $this->load->view('admin/footer');
}
    public function auth_action(){
        $id = $this->input->post('id');
        $row = get_by_id('approval_edits','approval_edits',$id);
        if ($row) {
if($this->input->post('approval')=='recommend'){
    $state = "recommended";
    $state_date = "recommended_date";
    $state_by = "recommended_by";
    $state_comment = "recommend_comment";
}else{
    $state = "Approved";
    $state_date = "approved_date";
    $state_by = "approved_by";
    $state_comment = "approval_comment";
}


            $data = array(

                'state' =>$state ,
                $state_date => date('Y-m-d'),
                $state_by => $this->session->userdata('user_id'),
                $state_comment => $this->input->post('comment')

            );
            $data_reject = array(

                'state' => 'rejected',
                'rejected_date' => date('Y-m-d'),
                'rejected_by' => $this->session->userdata('user_id'),
                'rejected_comment' => $this->input->post('comment')

            );
            $this->Access_model->update_auth($row->id,$data);
            if($row->type=='Loan edit' && $this->input->post('approval') =="approve"){
//                $this->Employees_model->insert(json_decode($row->new_data));
                $this->toaster->success('Success, approval succeeded');
                $this->list_auth();
            }else{
                $this->toaster->success('Success, action succeeded');
                $this->list_auth();
            }
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            $this->list_auth();
        }

    }
public  function auth_data($id){

$row = get_by_id('approval_edits','approval_edits',$id);
    if ($row) {
        $data = array(
            'id' => $id,
            'type' => $row->type,
            'old_info' => json_decode($row->old_info),
            'new_info' => json_decode($row->new_info),
            'state' => $row->state,

            'stamp' => $row->stamp,
            'Initiated_by' => $row->Initiated_by,

        );
        $menu_toggle['toggles'] = 23;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('admin/auth_action',$data);
        $this->load->view('admin/footer');
    } else {
       echo "Not found";
    }

}
    public function reset_password_get_info(){
        $this->output->set_header('X-Content-Type-Options: nosniff');

            $result = $this->User_access_model->check_user_email($this->input->post('useremail'));


            if(!empty($result)){
                $randomValue =str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

                $data = array(

                    'reset_code' =>   $randomValue,

                );
                $updateresult = $this->User_access_model->update_auth($result['Employee'], $data);



                    $to=$result['EmailAddress'];
                   
                    $body= "Your password reset code is ".$randomValue;
                    


                    $sendemail =  send_email($to, $body);
                    if($sendemail){

                        $data = array(

                            'employeeid' => set_value('employeeid', $result['Employee']),
                            'acccesscode' => set_value('acccesscode', $result['AccessCode']),
                        );

                        $this->load->view('forget_code', $data);


                    }




            }
            else{
                $this->session->set_flashdata('error','Sorry the email you is not available on our database you contact the admin');
                $this->forget_password();
            }


    }
    public function reset_password_get_code()
    {


        $result = $this->User_access_model->check_user_code($this->input->post('resetcode'),$this->input->post('employeeid'),$this->input->post('acccesscode'));

        if(!empty($result)){

            $data = array(

                'employeeid' => set_value('employeeid', $result['Employee']),
                'acccesscode' => set_value('acccesscode', $result['AccessCode']),
            );

            $this->load->view('forget_reset_password', $data);


        }
        else{
            $this->load->view('forget');
        }


    }

    public function reset_password_user()
    {


        $this->output->set_header('X-Content-Type-Options: nosniff');
        $dd = get_by_id('employees', 'id', $this->input->post('employeeid'));

        $hashedPassword = password_hash($this->input->post('password', TRUE), PASSWORD_BCRYPT);
        $result = $this->User_access_model->check_user_email($dd->EmailAddress);

        $data = array(
            'Password' => $hashedPassword,
        );


        $this->User_access_model->update_auth($this->input->post('employeeid'), $data);



            $rand_id = rand(100, 9999);
//				$sdate =$this->Sytem_date_model->get_active();
            $this->session->set_userdata('rand_id', $rand_id);
            $this->session->set_userdata('username', $result['AccessCode']);
            $this->session->set_userdata('user_id', $result['Employee']);
            $this->session->set_userdata('Firstname', $result['Firstname']);
            $this->session->set_userdata('Lastname', $result['Lastname']);
            $this->session->set_userdata('RoleName', $result['RoleName']);
            $this->session->set_userdata('role', $result['Role']);
            $this->session->set_userdata('profile_photo', $result['profile_photo']);
            $this->session->set_userdata('stamp', $result['server_date']);
//				$this->session->set_userdata('system_date',$sdate->s_date);

            $data = $this->Access_model->get_all_acces($this->session->userdata('role'));
            $this->session->set_userdata('access', $data);
            $this->User_access_model->update($result['Employee'], array('is_logged_in' => $rand_id));
            $logger = array(

                'user_id' => $this->session->userdata('user_id'),
                'activity' => 'logged in the system',
                'activity_cate' => 'logging',

            );
            log_activity($logger);
            $this->toaster->success('Success you have logged in successfully');
            redirect('admin/index');
//			}



    }

function update_state(){
	$this->Loan_model->update_defaulters();
}
	public function logout(){
		$this->User_access_model->update($this->session->userdata('user_id'),array('is_logged_in'=>'No'));
		$this->session->sess_destroy();
		$this->index();
	}
     public function authenticate()
    {
//        echo $this->input->post('username');
//        exit();
        //validate user logins
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');


        $this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
        if ($this->form_validation->run() == FALSE) {
            $this->index();

        } else {
            $result = $this->User_access_model->login_user($this->input->post('username'), sha1($this->input->post('password')));
      
            if (!empty($result)) {
//			if($result['is_logged_in']=="Yes"){
//				$this->session->set_flashdata('active','Sorry another session with your credentials is on,Please log out or ask admin');
//
//				$this->toaster->error('error','Sorry another session with your credentials is on,Please log out or ask admin');
//				$this->index();
//			}else{
                $sdate = $this->Sytem_date_model->get_active();
                $rand_id = rand(100, 9999);
                $this->session->set_userdata('rand_id', $rand_id);
                $this->session->set_userdata('username', $result['AccessCode']);
                $this->session->set_userdata('user_id', $result['Employee']);
                $this->session->set_userdata('Firstname', $result['Firstname']);
                $this->session->set_userdata('Lastname', $result['Lastname']);
                $this->session->set_userdata('RoleName', $result['RoleName']);
                $this->session->set_userdata('role', $result['Role']);
                $this->session->set_userdata('profile_photo', $result['profile_photo']);
                $this->session->set_userdata('stamp', $result['server_date']);
                $this->session->set_userdata('system_date', $sdate->s_date);

                $data = $this->Access_model->get_all_acces($this->session->userdata('role'));
                $this->session->set_userdata('access', $data);
                $this->User_access_model->update($result['Employee'], array('is_logged_in' => $rand_id));
                $logger = array(

                    'user_id' => $this->session->userdata('user_id'),
                    'activity' => 'logged in the system'

                );
                log_activity($logger);
                $this->toaster->success('Success you have logged in successfully');
            print_r('Success you have logged in successfully');
            redirect('admin/index');
            exit();
                redirect('admin/index');
//			}

            } else {
                $this->toaster->error('error', 'Sorry username or password is not correct');
                $this->session->set_flashdata('error', 'Sorry username or password is not correct');
                $this->index();
            }
        }
    }
    public function excel()
    {
        $this->load->helper('exportexcel');
        $namaFile = "districts.xls";
        $judul = "districts";
        $tablehead = 0;
        $tablebody = 1;
        $nourut = 1;
        //penulisan header
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment;filename=" . $namaFile . "");
        header("Content-Transfer-Encoding: binary ");

        xlsBOF();

        $kolomhead = 0;
        xlsWriteLabel($tablehead, $kolomhead++, "No");
        xlsWriteLabel($tablehead, $kolomhead++, "District Name");
        xlsWriteLabel($tablehead, $kolomhead++, "District Code");
        xlsWriteLabel($tablehead, $kolomhead++, "Parent");
        xlsWriteLabel($tablehead, $kolomhead++, "Rcode");

        foreach ($this->Districts_model->get_all() as $data) {
            $kolombody = 0;

            //ubah xlsWriteLabel menjadi xlsWriteNumber untuk kolom numeric
            xlsWriteNumber($tablebody, $kolombody++, $nourut);
            xlsWriteLabel($tablebody, $kolombody++, $data->district_name);
            xlsWriteLabel($tablebody, $kolombody++, $data->district_code);
            xlsWriteNumber($tablebody, $kolombody++, $data->parent);
            xlsWriteLabel($tablebody, $kolombody++, $data->rcode);

            $tablebody++;
            $nourut++;
        }

        xlsEOF();
        exit();
    }
}
