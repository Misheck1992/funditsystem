<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Shareholders extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Shareholders_model');
        $this->load->model('Geo_countries_model');
        $this->load->model('Individual_customers_model');
        $this->load->model('Branches_model');
        $this->load->model('Authorized_signitories_model');
        $this->load->model('Currency_model');
        $this->load->model('Account_model');
        $this->load->library('form_validation');
    }

    public function index()
    {


        $data = array(
            'shareholders_data' => $this->Shareholders_model->get_all('shareholders'),

        );
        $menu_toggle['toggles'] = 56;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('shareholders/shareholder_list',$data);
        $this->load->view('admin/footer');

    }
    public function approve()
    {


        $data = array(
            'shareholders_data' => $this->Shareholders_model->get_status('initiated')

        );
        $menu_toggle['toggles'] = 56;
        $this->load->view('admin/header',$menu_toggle);
        $this->load->view('shareholders/approve',$data);
        $this->load->view('admin/footer');
    }
    public function approved()
    {



        $data = array(
            'corporate_customers_data' => $this->Shareholders_model->get_status('Approved')

        );
        $menu_toggle['toggles'] = 56;
        $this->load->view('admin/header',$menu_toggle);
        $this->load->view('shareholders/approved',$data);
        $this->load->view('admin/footer');
    }
    public function read($id)
    {
        $row = $this->Shareholders_model->get_by_id($id); // Update the model name
        if ($row) {
            $data = array(
                'id' => $row->id,
                'title' => $row->title,
                'first_name' => $row->first_name,
                'last_name' => $row->last_name,
                'gender' => $row->gender,
                'approval_status' => $row->approval_status,
                'nationality' => $row->nationality,
                'phone_number' => $row->phone_number,
                'email_address' => $row->email_address,
                'full_address' => $row->full_address,
            );

            $menu_toggle['toggles'] = 56;
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('shareholders/shareholder_read', $data); // Update view path
            $this->load->view('admin/footer');
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('shareholders')); // Update redirect path
        }
    }

    public function create()
    {
        $data = array(
            'button' => 'Create',
            'action' => site_url('shareholders/create_action'),
            'id' => set_value('id'),
            'title' => set_value('title'),
            'first_name' => set_value('first_name'),
            'last_name' => set_value('last_name'),
            'gender' => set_value('gender'),
            'approval_status' => set_value('approval_status', 'pending'), // Default to 'pending'
            'nationality' => set_value('nationality'),
            'phone_number' => set_value('phone_number'),
            'email_address' => set_value('email_address'),
            'full_address' => set_value('full_address'),
        );

        $menu_toggle['toggles'] = 56;
        $this->load->view('admin/header', $menu_toggle);
        $this->load->view('shareholders/shareholder_form', $data); // Update view path
        $this->load->view('admin/footer');
    }


    public function create_action()
    {
        // Generate a unique account number based on current count and random number

        $data = array(

            'title' => $this->input->post('title'), // Assuming title comes from user input
            'first_name' => $this->input->post('first_name'),
            'last_name' => $this->input->post('last_name'),
            'gender' => $this->input->post('gender'),

            'nationality' => $this->input->post('nationality'),
            'phone_number' => $this->input->post('phone_number'),
            'email_address' => $this->input->post('email_address'),
            'full_address' => $this->input->post('full_address'),
            'added_by' => $this->session->userdata('user_id'), // Assuming `added_by` is relevant
        );

        // Insert into the `shareholders` table
        $this->Shareholders_model->insert($data);

        // Success message
        $this->toaster->success('Success, Shareholder was created and is pending approval.');

        // Redirect to a relevant page
        redirect(site_url('shareholders'));
    }

    public function update($id)
    {
        $row = $this->Shareholders_model->get_by_id($id);

        if ($row) {
            $data = array(
                'button' => 'Update',
                'action' => site_url('shareholders/update_action'),

                'id' => set_value('id',$row->id),
                'title' => set_value('title', $row->title),
                'first_name' => set_value('first_name',$row->first_name),
                'last_name' => set_value('last_name',$row->last_name),
                'gender' => set_value('gender', $row->gender),
                'approval_status' => set_value('approval_status', $row->approval_status), // Default to 'pending'
                'nationality' => set_value('nationality',$row->nationality),
                'phone_number' => set_value('phone_number',$row->phone_number),
                'email_address' => set_value('email_address',$row->email_address),
                'full_address' => set_value('full_address',$row->full_address),

            );
            $menu_toggle['toggles'] = 56;
            $this->load->view('admin/header', $menu_toggle);
            $this->load->view('shareholders/shareholder_edit', $data); // Update view path
            $this->load->view('admin/footer');
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('shareholders'));
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
                'EntityType' => $this->input->post('EntityType',TRUE),
                'ClientId' => $this->input->post('ClientId',TRUE),
                'TaxIdentificationNumber' => $this->input->post('TaxIdentificationNumber',TRUE),
                'Country' => $this->input->post('Country',TRUE),
                'Branch' => $this->input->post('Branch',TRUE),
                'nature_of_business' => $this->input->post('nature_of_business',TRUE),
                'industry_sector' => $this->input->post('industry_sector',TRUE),
                'street' => $this->input->post('street',TRUE),
                'phone_number' => $this->input->post('phone_number',TRUE),
                'city_town' => $this->input->post('city_town',TRUE),
                'contact_email' => $this->input->post('contact_email',TRUE),
                'website' => $this->input->post('website',TRUE),

                'company_certificate' => $this->input->post('company_certificate', TRUE),
                'tax_id_doc'=>$this->input->post('tax_id_doc', TRUE),
                'proof_physical _address'=>$this->input->post('proof_physical_address', TRUE),
                'financial_statement'=>$this->input->post('financial_statement', TRUE),
            );
            $row = $this->Corporate_customers_model->get_by_id($this->input->post('id', TRUE));

            $data2 = array(

                'EntityName' => $row->EntityName,
                'DateOfIncorporation' => $row->DateOfIncorporation,
                'RegistrationNumber' => $row->RegistrationNumber,
                'EntityType' => $row->EntityType,
                'ClientId' => $row->ClientId,
                'TaxIdentificationNumber' => $row->TaxIdentificationNumber,
                'Country' => $row->Country,
                'Branch' => $row->Branch,
                'nature_of_business' =>  $row->nature_of_business,
                'industry_sector' => $row->industry_sector,
                'street' => $row->street,
                'phone_number' => $row->phone_number,
                'city_town' => $row->city_town,
                'contact_email' => $row->contact_email,
                'website' => $row->website,
                'company_certificate' =>  $row->company_certificate,
                'tax_id_doc' =>  $row->tax_id_doc,
                'memo_doc' =>  $row->memo_doc,
            );
            $logger2 = array(
                'identity'=>$this->input->post('id', TRUE),
                'auth_type' => 'corporate_customer_update',
                'old_data' => json_encode($data2),
                'new_data' => json_encode($data),
                'system_date' => $this->session->userdata('system_date'),
                'initiator' => $this->session->userdata('user_id')

            );
//            $this->Employees_model->insert($data);
            auth_logger($logger2);
            $this->toaster->success('Success, Corporate customer was edited  pending approval');
//            $this->Corporate_customers_model->update($this->input->post('id', TRUE), $data);
//            $this->session->set_flashdata('message', 'Update Record Success');
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
        $row = $this->Shareholders_model->get_by_id($id);
        $notify = get_by_id('sms_settings','id','1');
        if ($row) {
            $this->Shareholders_model->update($id,array('approval_status'=>'Approved'));

            $logger = array(

                'user_id' => $this->session->userdata('user_id'),
                'activity' => 'Shareholder approved',
                'activity_cate' => 'shareholder_approval',

            );
            log_activity($logger);
            $this->toaster->success('Shareholder was approved successfully');
            redirect(site_url('shareholders/approve'));
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
        $this->form_validation->set_rules('first_name', 'First Name', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('nationality', 'Nationality', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('phone_number', 'Phone Number', 'trim|required|max_length[20]');
        $this->form_validation->set_rules('email_address', 'Email Address', 'trim|required|valid_email|max_length[100]');
        $this->form_validation->set_rules('full_address', 'Full Address', 'trim|required');
        $this->form_validation->set_rules('id', 'ID', 'trim|is_natural_no_zero'); // Optional, used for updates.
        $this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }


}

