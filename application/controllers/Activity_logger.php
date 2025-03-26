<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Activity_logger extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Activity_logger_model');
        $this->load->library('form_validation');
    }

    public function index()
    {


        $data = array(
            'activity_logger_data' => $this->Activity_logger_model->get_all(),

        );

        $this->load->view('admin/header');
        $this->load->view('activity_logger/activity_logger_list',$data);
        $this->load->view('admin/footer');

    }



 public function filteractivity()
    {
$status = $this->input->get('status');
    $from = $this->input->get('from');
    $to = $this->input->get('to');
    $search = $this->input->get('search');
    if($search=="filter"){

        $data = array(
            'activity_logger_data' => $this->Activity_logger_model->get_all_filter($status,$from,$to),

        );

        $this->load->view('admin/header');
        $this->load->view('activity_logger/activity_logger_list',$data);
        $this->load->view('admin/footer');
}
    }

    public function read($id) 
    {
        $row = $this->Activity_logger_model->get_by_id($id);
        if ($row) {
            $data = array(
		'id' => $row->id,
		'user_id' => $row->user_id,
		'activity' => $row->activity,
		'system_time' => $row->system_time,
		'server_time' => $row->server_time,
	    );
            $this->load->view('activity_logger/activity_logger_read', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('activity_logger'));
        }
    }

    public function create() 
    {
        $data = array(
            'button' => 'Create',
            'action' => site_url('activity_logger/create_action'),
	    'id' => set_value('id'),
	    'user_id' => set_value('user_id'),
	    'activity' => set_value('activity'),
	    'system_time' => set_value('system_time'),
	    'server_time' => set_value('server_time'),
	);
        $this->load->view('activity_logger/activity_logger_form', $data);
    }
    
    public function create_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->create();
        } else {
            $data = array(
		'user_id' => $this->input->post('user_id',TRUE),
		'activity' => $this->input->post('activity',TRUE),
		'system_time' => $this->input->post('system_time',TRUE),
		'server_time' => $this->input->post('server_time',TRUE),
	    );

            $this->Activity_logger_model->insert($data);
            $this->session->set_flashdata('message', 'Create Record Success');
            redirect(site_url('activity_logger'));
        }
    }
    
    public function update($id) 
    {
        $row = $this->Activity_logger_model->get_by_id($id);

        if ($row) {
            $data = array(
                'button' => 'Update',
                'action' => site_url('activity_logger/update_action'),
		'id' => set_value('id', $row->id),
		'user_id' => set_value('user_id', $row->user_id),
		'activity' => set_value('activity', $row->activity),
		'system_time' => set_value('system_time', $row->system_time),
		'server_time' => set_value('server_time', $row->server_time),
	    );
            $this->load->view('activity_logger/activity_logger_form', $data);
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('activity_logger'));
        }
    }
    
    public function update_action() 
    {
        $this->_rules();

        if ($this->form_validation->run() == FALSE) {
            $this->update($this->input->post('id', TRUE));
        } else {
            $data = array(
		'user_id' => $this->input->post('user_id',TRUE),
		'activity' => $this->input->post('activity',TRUE),
		'system_time' => $this->input->post('system_time',TRUE),
		'server_time' => $this->input->post('server_time',TRUE),
	    );

            $this->Activity_logger_model->update($this->input->post('id', TRUE), $data);
            $this->session->set_flashdata('message', 'Update Record Success');
            redirect(site_url('activity_logger'));
        }
    }
    
    public function delete($id) 
    {
        $row = $this->Activity_logger_model->get_by_id($id);

        if ($row) {
            $this->Activity_logger_model->delete($id);
            $this->session->set_flashdata('message', 'Delete Record Success');
            redirect(site_url('activity_logger'));
        } else {
            $this->session->set_flashdata('message', 'Record Not Found');
            redirect(site_url('activity_logger'));
        }
    }

    public function _rules() 
    {
	$this->form_validation->set_rules('user_id', 'user id', 'trim|required');
	$this->form_validation->set_rules('activity', 'activity', 'trim|required');
	$this->form_validation->set_rules('system_time', 'system time', 'trim|required');
	$this->form_validation->set_rules('server_time', 'server time', 'trim|required');

	$this->form_validation->set_rules('id', 'id', 'trim');
	$this->form_validation->set_error_delimiters('<span class="text-danger">', '</span>');
    }

}

/* End of file Activity_logger.php */
/* Location: ./application/controllers/Activity_logger.php */
/* Please DO NOT modify this information : */
/* Generated by Harviacode Codeigniter CRUD Generator 2021-05-05 22:08:21 */
/* http://harviacode.com */
