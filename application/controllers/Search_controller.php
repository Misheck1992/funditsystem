<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Search_controller
 *
 * Controller for file search operations
 */
class Search_controller extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		// Load models
		$this->load->model('file_library_model');
		$this->load->model('file_folders_model');
		$this->load->model('file_folder_mapping_model');
		$this->load->model('file_shares_model');

		// Check if user is logged in
		if (!$this->auth->is_logged_in()) {
			// If AJAX request, send JSON response
			if ($this->input->is_ajax_request()) {
				$this->output
					->set_content_type('application/json')
					->set_output(json_encode([
						'success' => false,
						'message' => 'Authentication required'
					]));
				return;
			}

			// Redirect to login page
			redirect('auth/login');
		}
	}

	/**
	 * Main search endpoint
	 */
	public function search()
	{
		$keyword = $this->input->get('q');
		$owner_type = $this->input->get('owner_type');
		$file_category = $this->input->get('file_category');
		$file_type = $this->input->get('file_type');
		$date_from = $this->input->get('date_from');
		$date_to = $this->input->get('date_to');
		$my_files = $this->input->get('my_files') ? true : false;
		$shared_with_me = $this->input->get('shared_with_me') ? true : false;

		$sort_field = $this->input->get('sort_field') ?: 'date_added';
		$sort_direction = $this->input->get('sort_direction') ?: 'desc';
		$page = $this->input->get('page') ?: 1;
		$limit = $this->input->get('limit') ?: 50;
		$offset = ($page - 1) * $limit;

		// Build filters
		$filters = [];

		// Filter by owner type
		if ($owner_type) {
			$filters['owner_type'] = $owner_type;
		}

		// Filter by file category
		if ($file_category) {
			$filters['file_category'] = $file_category;
		}

		// Filter by owner (current user)
		if ($my_files) {
			$filters['added_by'] = $this->auth->get_user_id();
		}

		// Filter by file type
		if ($file_type) {
			if ($file_type === 'image') {
				$filters['file_type LIKE'] = 'image/%';
			} elseif ($file_type === 'document') {
				$filters['file_type IN'] = [
					'application/pdf',
					'application/msword',
					'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
					'text/plain'
				];
			} elseif ($file_type === 'spreadsheet') {
				$filters['file_type IN'] = [
					'application/vnd.ms-excel',
					'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
					'text/csv'
				];
			} else {
				$filters['file_type'] = $file_type;
			}
		}

		// Filter by date range
		if ($date_from) {
			$filters['date_added >='] = $date_from . ' 00:00:00';
		}

		if ($date_to) {
			$filters['date_added <='] = $date_to . ' 23:59:59';
		}

		// Search files
		if ($shared_with_me) {
			// Get files shared with current user
			$files = $this->_search_shared_files($keyword, $filters, $sort_field, $sort_direction, $limit, $offset);
			$total_files = $this->_count_shared_files($keyword, $filters);
		} else {
			// Normal search
			$files = $this->file_library_model->search_files($keyword, $filters, $sort_field, $sort_direction, $limit, $offset);
			$total_files = $this->file_library_model->count_search_files($keyword, $filters);
		}

		// Add folder information for each file
		foreach ($files as &$file) {
			$folder_ids = $this->file_folder_mapping_model->get_folders_for_file($file->id);
			$file->folders = [];

			foreach ($folder_ids as $folder_id) {
				$folder = $this->file_folders_model->get($folder_id);
				if ($folder) {
					$file->folders[] = [
						'id' => $folder->id,
						'name' => $folder->folder_name
					];
				}
			}
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success' => true,
				'files' => $files,
				'total_files' => $total_files,
				'page' => $page,
				'limit' => $limit,
				'total_pages' => ceil($total_files / $limit)
			]));
	}

	/**
	 * Get search suggestions
	 */

}
	?>
