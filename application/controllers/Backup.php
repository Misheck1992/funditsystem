<?php


class Backup extends CI_Controller
{
	public function __construct(){

		parent::__construct();
		$this->load->helper('url');

		// Load zip library
		$this->load->library('zip');


	}
    function backupdb_and_email() {
        // Load the necessary libraries and helpers
        $this->load->dbutil();
        $this->load->helper('file');
        $this->load->library('email');

        // Database backup preferences
        $prefs = array(
            'format'   => 'zip',
            'filename' => 'system_db_backup.sql',
        );

        // Perform database backup
        $backup = $this->dbutil->backup($prefs);

        // Save the backup file locally
        $db_name = 'backup-on-' . date("Y-m-d-H-i-s") . '.zip';
        $save_path = FCPATH . '/' . $db_name;
        write_file($save_path, $backup);

        // Attach the backup file to the email
        $this->email->attach($save_path);

        // Email configuration
        $this->email->from('info@infocustech-mw.com', 'Finrealm');
        $this->email->to('kamulonim@gmail.com');
        $this->email->subject('Database Backup on '.date("Y-m-d-H-i-s"));
        $this->email->message('Please find the attached database backup.');

        // Send the email
        if ($this->email->send()) {
            // If the email is sent successfully, you may delete the local backup file
            unlink($save_path);
            echo 'Email sent with database backup successfully.';
        } else {
            echo 'Error sending email: ' . $this->email->print_debugger();
        }
    }

    function backupdb(){

		// Load the DB utility class
		$this->load->dbutil();

		$prefs = array(
			'format'      => 'zip',
			'filename'    => 'system_db_backup.sql'
		);

		$backup = & $this->dbutil->backup($prefs);

		$db_name = 'backup-on-'. date("Y-m-d-H-i-s") .'.zip';
		$save = FCPATH.'/'.$db_name;

		$this->load->helper('file');
		write_file($save, $backup);
		$this->load->helper('download');
		force_download($db_name, $backup);
	}
	public function files(){


			// File name
			$filename = "backup.zip";

			// Directory path (uploads directory stored in project root)
			$path = 'uploads';

			// Add directory to zip
			$this->zip->read_dir($path);

			// Save the zip file to archivefiles directory
			$this->zip->archive(FCPATH.'/archivefiles/'.$filename);

			// Download
			$this->zip->download($filename);

	}
	public function index()
	{
		$this->load->view('admin/header');
		$this->load->view('backup/index');
		$this->load->view('admin/footer');

	}

	public function read($id)
	{
		$row = $this->Logger_model->get_by_id($id);
		if ($row) {
			$data = array(
				'id' => $row->id,
				'userID' => $row->userID,
				'Action' => $row->Action,
				'Date' => $row->Date,
			);
			$this->load->view('logger/logger_read', $data);
		} else {
			$this->session->set_flashdata('message', 'Record Not Found');
			redirect(site_url('logger'));
		}
	}


}
