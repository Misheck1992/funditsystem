<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Files_controller
 *
 * Main controller for file operations
 */
class Files_controller extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		// Load models
		$this->load->model('File_library_model');
		$this->load->model('File_shares_model');
		$this->load->model('file_folders_model');
		$this->load->model('Folders_model');
		$this->load->model('Employees_model');
		$this->load->model('file_folder_mapping_model');

		// Load libraries
		$this->load->library('FileUploader');
		$this->load->library('FilePreview');

		// Check if user is logged in

	}
public function testmodel()
{
	$this->load->model('Folders_model');
	$this->Folders_model->get_all();
}
	/**
	 * Main view for file library
	 */
	public function index()
	{
		$data = [
			'title' => 'File Library',
			// Add any other data needed for the view
		];

		$this->load->view('admin/header', $data);
		$this->load->view('file_library/index', $data);
		$this->load->view('admin/footer');
	}

	/**
	 * Get folder content (files and subfolders)
	 */
	public function get_folder_content()
	{
		$folder_id = ($this->input->get('folder_id') === '0' || $this->input->get('folder_id') === 0) ? NULL : $this->input->get('folder_id');
		$sort_field = $this->input->get('sort_field') ?: 'date_added';
		$sort_direction = $this->input->get('sort_direction') ?: 'desc';
		$page = $this->input->get('page') ?: 1;
		$limit = $this->input->get('limit') ?: 50;
		$offset = ($page - 1) * $limit;

		// Get folder info
		$folder = null;
		$breadcrumb = [];
		if ($folder_id > 0) {
			$this->load->model('file_folders_model');
			$folder = $this->file_folders_model->get($folder_id);

			if (!$folder) {
				$this->output
					->set_content_type('application/json')
					->set_output(json_encode([
						'success' => false,
						'message' => 'Folder not found'
					]));
				return;
			}

			// Get breadcrumb data
			$breadcrumb = $this->file_folders_model->get_folder_path($folder_id);
		}

		// Get subfolders
		$this->load->model('file_folders_model');
		$folders = $this->file_folders_model->get_folders_by_parent($folder_id);

		// Get file count for each subfolder
		foreach ($folders as &$subfolder) {
			$subfolder->file_count = $this->File_library_model->count_files_by_folder($subfolder->id);
		}

		// Get files in current folder
		$files = $this->File_library_model->get_files_by_folder($folder_id, $sort_field, $sort_direction, $limit, $offset);
		$total_files = $this->File_library_model->count_files_by_folder($folder_id);

		// Format data for response
		$response = [
			'success' => true,
			'folder' => $folder,
			'breadcrumb' => $breadcrumb,
			'content' => [
				'folders' => $folders,
				'files' => $files,
				'total_files' => $total_files,
				'page' => $page,
				'limit' => $limit,
				'total_pages' => ceil($total_files / $limit)
			]
		];

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($response));
	}

	/**
	 * Upload a file
	 */
	public function upload()
	{
		// Check for AJAX request
		if (!$this->input->is_ajax_request()) {
			show_error('Direct access not allowed');
			return;
		}

		// Process the upload
		$upload_path = './uploads/files/' . date('Y/m/d') . '/';

		// Create directory if it doesn't exist
		if (!is_dir($upload_path)) {
			mkdir($upload_path, 0777, true);
		}

		$config = [
			'upload_path' => $upload_path,
			'allowed_types' => 'gif|jpg|jpeg|png|pdf|doc|docx|xls|xlsx|csv|txt|zip|rar',
			'max_size' => 5120, // 5MB
			'encrypt_name' => TRUE
		];

		$this->fileuploader->initialize($config);

		if ($this->fileuploader->do_upload('file')) {
			$file_data = $this->fileuploader->data();

			// Insert into database
			$insert_data = [
				'owner_type' => $this->input->post('owner_type'),
				'owner_id' => $this->input->post('owner_id'),
				'file_category' => $this->input->post('file_category') ?: 'general_files',
				'file_type' => $file_data['file_type'],
				'file_name' => $this->input->post('file_name') ?: $file_data['client_name'],
				'file_path' => $file_data['full_path'],
				'file_size' => $file_data['file_size'],
				'is_public' => $this->input->post('is_public') ? 1 : 0,
				'date_added' => date('Y-m-d H:i:s'),
				'date_modified' => date('Y-m-d H:i:s'),
				'added_by' => $this->session->userdata('user_id'),
				'description' => $this->input->post('description'),
				'tags' => $this->input->post('tags')
			];

			$file_id = $this->File_library_model->insert($insert_data);

			// If folder specified, add to that folder
			$folder_id = ($this->input->post('folder_id') === '0' || $this->input->post('folder_id') === 0) ? NULL : $this->input->post('folder_id');
			if ($folder_id) {
				$this->file_folder_mapping_model->insert([
					'file_id' => $file_id,
					'folder_id' => $folder_id,
					'date_added' => date('Y-m-d H:i:s')
				]);
			}

			// Get the complete file record
			$file = $this->File_library_model->get($file_id);

			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => true,
					'message' => 'File uploaded successfully',
					'file' => $file
				]));
		} else {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => $this->fileuploader->display_errors('', '')
				]));
		}
	}

	/**
	 * Download a file
	 *
	 * @param int $file_id
	 */
	public function download($file_id)
	{
		$file = $this->File_library_model->get($file_id);

		if (!$file) {
			show_error('File not found');
			return;
		}

		// Check permissions
		if (!$this->_check_file_access($file)) {
			show_error('You do not have permission to access this file');
			return;
		}

		// Force download
		$this->load->helper('download');
		force_download($file->file_name, file_get_contents($file->file_path));
	}

	/**
	 * Preview a file
	 *
	 * @param int $file_id
	 */
	public function preview($file_id)
	{
		$file = $this->File_library_model->get($file_id);

		if (!$file) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'File not found'
				]));
			return;
		}

		// Check permissions
		if (!$this->_check_file_access($file)) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'You do not have permission to access this file'
				]));
			return;
		}

		// Check if thumbnail requested
		$size = $this->input->get('size');

		// Get preview based on file type
		if (strpos($file->file_type, 'image/') === 0) {
			// Image preview
			if ($size === 'thumbnail') {
				// Generate thumbnail
				$this->load->library('FilePreview');
				$thumbnail = $this->filepreview->create_thumbnail($file->file_path, 150, 150);

				// Output the thumbnail
				header('Content-Type: ' . $file->file_type);
				readfile($thumbnail);
				exit;
			} else {
				// Full image
				header('Content-Type: ' . $file->file_type);
				readfile($file->file_path);
				exit;
			}
		} else if ($file->file_type === 'application/pdf') {
			// PDF preview - let the browser handle it
			header('Content-Type: application/pdf');
			header('Content-Disposition: inline; filename="' . $file->file_name . '"');
			readfile($file->file_path);
			exit;
		} else if ($file->file_type === 'text/plain' || $file->file_type === 'text/csv') {
			// Text preview
			$content = file_get_contents($file->file_path);

			// Limit content length for large files
			if (strlen($content) > 100000) {
				$content = substr($content, 0, 100000) . "\n\n[Content truncated, file too large to display completely]";
			}

			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => true,
					'content' => $content
				]));
		} else {
			// No preview available for this file type
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'No preview available for this file type'
				]));
		}
	}

	/**
	 * Delete a file
	 */
	public function delete()
	{
		// Check for AJAX request
		if (!$this->input->is_ajax_request()) {
			show_error('Direct access not allowed');
			return;
		}

		$file_id = $this->input->post('file_id');
		$file = $this->File_library_model->get($file_id);

		if (!$file) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'File not found'
				]));
			return;
		}

		// Check ownership
		if ($file->added_by != $this->session->userdata('user_id') && !$this->auth->has_permission('file_library_manage_all')) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'You do not have permission to delete this file'
				]));
			return;
		}

		// Delete the physical file
		if (file_exists($file->file_path)) {
			unlink($file->file_path);
		}

		// Delete folder mappings
		$this->File_folder_mapping_model->delete_file_mappings($file_id);

		// Delete file shares
		$this->load->model('file_shares_model');
		$this->file_shares_model->remove_file_shares($file_id);

		// Delete database record
		$this->File_library_model->delete($file_id);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success' => true,
				'message' => 'File deleted successfully'
			]));
	}

	/**
	 * Update file metadata
	 */
	/**
	 * Update file metadata
	 */
	public function update() {
		// Check for AJAX request
		if (!$this->input->is_ajax_request()) {
			show_error('Direct access not allowed');
			return;
		}

		$file_id = $this->input->post('file_id');
		$file = $this->File_library_model->get($file_id);

		if (!$file) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'File not found'
				]));
			return;
		}

		// Check ownership
		if ($file->added_by != $this->auth->get_user_id() && !$this->auth->has_permission('file_library_manage_all')) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'You do not have permission to update this file'
				]));
			return;
		}

		// Update data
		$update_data = [
			'file_name' => $this->input->post('file_name') ?: $file->file_name,
			'description' => $this->input->post('description'),
			'tags' => $this->input->post('tags'),
			'is_public' => $this->input->post('is_public') ? 1 : 0,
			'date_modified' => date('Y-m-d H:i:s')
		];

		// Update only non-empty fields
		foreach ($update_data as $key => $value) {
			if ($value === null) {
				unset($update_data[$key]);
			}
		}

		$this->File_library_model->update($file_id, $update_data);

		// Get updated file
		$updated_file = $this->File_library_model->get($file_id);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success' => true,
				'message' => 'File updated successfully',
				'file' => $updated_file
			]));
	}

	/**
	 * Move file to a different folder
	 */
	public function move() {
		// Check for AJAX request
		if (!$this->input->is_ajax_request()) {
			show_error('Direct access not allowed');
			return;
		}

		$file_id = $this->input->post('file_id');
		$source_folder_id = $this->input->post('source_folder_id');
		$target_folder_id = $this->input->post('target_folder_id');

		$file = $this->File_library_model->get($file_id);

		if (!$file) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'File not found'
				]));
			return;
		}

		// Check ownership
		if ($file->added_by != $this->auth->get_user_id() && !$this->auth->has_permission('file_library_manage_all')) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'You do not have permission to move this file'
				]));
			return;
		}

		// Validate target folder
		if ($target_folder_id > 0) {
			$this->load->model('file_folders_model');
			$target_folder = $this->File_library_model->get($target_folder_id);

			if (!$target_folder) {
				$this->output
					->set_content_type('application/json')
					->set_output(json_encode([
						'success' => false,
						'message' => 'Target folder not found'
					]));
				return;
			}
		}

		// Remove from source folder
		if ($source_folder_id) {
			$this->file_folder_mapping_model->delete($file_id, $source_folder_id);
		}

		// Add to target folder
		if ($target_folder_id) {
			// Check if already in target folder
			$folder_ids = $this->file_folder_mapping_model->get_folders_for_file($file_id);

			if (!in_array($target_folder_id, $folder_ids)) {
				$this->file_folder_mapping_model->insert([
					'file_id' => $file_id,
					'folder_id' => $target_folder_id,
					'date_added' => date('Y-m-d H:i:s')
				]);
			}
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success' => true,
				'message' => 'File moved successfully'
			]));
	}

	/**
	 * Copy file to a different folder
	 */
	public function copy() {
		// Check for AJAX request
		if (!$this->input->is_ajax_request()) {
			show_error('Direct access not allowed');
			return;
		}

		$file_id = $this->input->post('file_id');
		$target_folder_id = $this->input->post('target_folder_id');

		$file = $this->File_library_model->get($file_id);

		if (!$file) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'File not found'
				]));
			return;
		}

		// Check ownership or permission
		if (!$this->_check_file_access($file)) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'You do not have permission to copy this file'
				]));
			return;
		}

		// Validate target folder
		if ($target_folder_id > 0) {
			$this->load->model('file_folders_model');
			$target_folder = $this->file_folders_model->get($target_folder_id);

			if (!$target_folder) {
				$this->output
					->set_content_type('application/json')
					->set_output(json_encode([
						'success' => false,
						'message' => 'Target folder not found'
					]));
				return;
			}
		}

		// Check if already in target folder
		$folder_ids = $this->file_folder_mapping_model->get_folders_for_file($file_id);

		if (in_array($target_folder_id, $folder_ids)) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'File already exists in target folder'
				]));
			return;
		}

		// Add to target folder
		$this->file_folder_mapping_model->insert([
			'file_id' => $file_id,
			'folder_id' => $target_folder_id,
			'date_added' => date('Y-m-d H:i:s')
		]);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success' => true,
				'message' => 'File copied successfully'
			]));
	}
public function testsearch()
{
	$folder_id = 24;
	$keyword = "Welcome";
	$sort_field = $this->input->get('sort_field') ?: 'file_library.date_added';
	$sort_direction = $this->input->get('sort_direction') ?: 'desc';
	$page = $this->input->get('page') ?: 1;
	$limit = $this->input->get('limit') ?: 50;
	$offset = ($page - 1) * $limit;

	// Build filters
	$filters = [];
	$files = $this->File_library_model->search_files_in_folder($folder_id, $keyword, $filters, $sort_field, $sort_direction, $limit, $offset);
print_r($files);
}
	/**
	 * Search files
	 */
	public function search() {
		$keyword = $this->input->get('q');
		$folder_id = $this->input->get('folder_id');
		$file_type = $this->input->get('file_type');
		$file_category = $this->input->get('file_category');
		$owner_type = $this->input->get('owner_type');
		$date_from = $this->input->get('date_from');
		$date_to = $this->input->get('date_to');

		$sort_field = $this->input->get('sort_field') ?: 'file_library.date_added';
		$sort_direction = $this->input->get('sort_direction') ?: 'desc';
		$page = $this->input->get('page') ?: 1;
		$limit = $this->input->get('limit') ?: 50;
		$offset = ($page - 1) * $limit;

		// Build filters for files
		$file_filters = [];

		// Filter by owner (current user)
		if ($this->input->get('my_files')) {
			$file_filters['added_by'] = $this->session->userdata('user_id');
		}

		// Filter by owner type
		if ($owner_type) {
			$file_filters['owner_type'] = $owner_type;
		}

		// Filter by file category
		if ($file_category) {
			$file_filters['file_category'] = $file_category;
		}

		// Filter by file type
		if ($file_type) {
			if ($file_type === 'image') {
				$this->db->like('file_type', 'image/');
			} elseif ($file_type === 'document') {
				$file_filters['file_type'] = [
					'application/pdf',
					'application/msword',
					'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
					'text/plain'
				];
			} elseif ($file_type === 'spreadsheet') {
				$file_filters['file_type'] = [
					'application/vnd.ms-excel',
					'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
					'text/csv'
				];
			} else {
				$file_filters['file_type'] = $file_type;
			}
		}

		// Filter by date range
		if ($date_from) {
			$file_filters['date_added >='] = $date_from . ' 00:00:00';
		}

		if ($date_to) {
			$file_filters['date_added <='] = $date_to . ' 23:59:59';
		}

		// Get folder info for breadcrumb
		$folder = null;
		$breadcrumb = [];
		if ($folder_id && $folder_id != '0') {

			$folder = $this->Folders_model->get($folder_id);

			if ($folder) {
				// Get breadcrumb data
				$breadcrumb = $this->Folders_model->get_folder_path($folder_id);
			}
		}

		// Search for folders matching the keyword

		$folders = [];

		if (!empty($keyword)) {
			// Only search folders if we have a keyword
			$folders = $this->Folders_model->search_infolders($keyword, $folder_id, 0);

			// Get file count for each found folder
			foreach ($folders as &$subfolder) {
				$subfolder->file_count = $this->File_library_model->count_files_by_folder($subfolder->id);
			}
		}

		// Perform search for files
		if ($folder_id !== null && $folder_id !== '') {
			// Get files in the specified folder
			$files = $this->File_library_model->search_files_in_folder($folder_id, $keyword, $file_filters, $sort_field, $sort_direction, $limit, $offset);
			$total_files = $this->File_library_model->count_search_files_in_folder($folder_id, $keyword, $file_filters);
		} else {
			// Search all accessible files
			$files = $this->File_library_model->search_files($keyword, $file_filters, $sort_field, $sort_direction, $limit, $offset);
			$total_files = $this->File_library_model->count_search_files($keyword, $file_filters);
		}

		// Format response to match get_folder_content format
		$response = [
			'success' => true,
			'folder' => $folder,
			'breadcrumb' => $breadcrumb,
			'content' => [
				'folders' => $folders,  // Now includes matching folders
				'files' => $files,
				'total_files' => $total_files,
				'total_folders' => count($folders),
				'page' => (int)$page,
				'limit' => (int)$limit,
				'total_pages' => ceil($total_files / $limit)
			],
			'search_term' => $keyword // Add search term for UI display
		];

		// Add debugging info if in development environment
		if (ENVIRONMENT === 'development') {
			$response['debug'] = [
				'query' => $this->db->last_query(),
				'filters' => $file_filters
			];
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($response));
	}

	/**
	 * Toggle file public status
	 */
	public function toggle_public() {
		// Check for AJAX request
		if (!$this->input->is_ajax_request()) {
			show_error('Direct access not allowed');
			return;
		}

		$file_id = $this->input->post('file_id');
		$file = $this->File_library_model->get($file_id);

		if (!$file) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'File not found'
				]));
			return;
		}

		// Check ownership
		if ($file->added_by != $this->auth->get_user_id() && !$this->auth->has_permission('file_library_manage_all')) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'You do not have permission to update this file'
				]));
			return;
		}

		// Toggle public status
		$is_public = $file->is_public ? 0 : 1;

		$this->File_library_model->update($file_id, [
			'is_public' => $is_public,
			'date_modified' => date('Y-m-d H:i:s')
		]);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success' => true,
				'message' => 'File ' . ($is_public ? 'made public' : 'made private') . ' successfully',
				'is_public' => $is_public
			]));
	}

	/**
	 * Check if user has access to a file
	 *
	 * @param object $file
	 * @return bool
	 */
	private function _check_file_access($file) {
		$user_id = $this->session->userdata();

		// Check if user is file owner
		if ($file->added_by == $user_id) {
			return true;
		}

		// Check if file is public
		if ($file->is_public) {
			return true;
		}

		// Check if admin
		if ($this->auth->has_permission('file_library_manage_all')) {
			return true;
		}

		// Check if file is shared with user
		$this->load->model('file_shares_model');
		if ($this->file_shares_model->is_file_shared_with_user($file->id, $user_id)) {
			return true;
		}

		return false;
	}


	/**
	 * Get file info including shares
	 */
	public function get_file_info()
	{
		// Check for AJAX request
		if (!$this->input->is_ajax_request()) {
			show_error('Direct access not allowed');
			return;
		}

		$file_id = $this->input->get('file_id');
		$include_shares = $this->input->get('include_shares') === 'true';

		if (!$file_id) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'File ID is required'
				]));
			return;
		}

		// Get file info
		$file = $this->File_library_model->get($file_id);

		if (!$file) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'File not found'
				]));
			return;
		}

		$response = [
			'success' => true,
			'file' => $file
		];

		// Include shares if requested
//		if ($include_shares) {
//
//			$shared_users = $this->File_shares_model->something_else($file_id);
//
//			// Format the user data
//			$formatted_users = [];
//			foreach ($shared_users as $shared_user) {
//				$formatted_users[] = [
//					'id' => $shared_user->id,
//					'user_name' => $shared_user->fullname,
//					'user_email' => $shared_user->email,
//					'permission' => $shared_user->permission_level,
//					'share_id' => $shared_user->share_id,
//					'date_shared' => $shared_user->date_shared
//				];
//			}
//
//			$response['shares'] = $formatted_users;
//		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($response));
	}
	public function get_text_content() {
		$file_id = $this->input->get('file_id');

		if (!$file_id) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'File ID is required'
				]));
			return;
		}

		$file = $this->File_library_model->get($file_id);

		if (!$file) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'File not found'
				]));
			return;
		}

		// Only allow text files
		$allowed_types = ['text/plain', 'text/csv'];
		if (!in_array($file->file_type, $allowed_types)) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'File type not supported for text preview'
				]));
			return;
		}

		// Get file content
		$content = file_get_contents($file->file_path);

		if ($content === false) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'Failed to read file content'
				]));
			return;
		}

		// For large files, limit content
		if (strlen($content) > 100000) {
			$content = substr($content, 0, 100000) . "\n\n[File truncated as it's too large to display completely]";
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success' => true,
				'content' => $content
			]));
	}

	/**
	 * Share a file with another user
	 */
	public function share_file()
	{
		// Check for AJAX request
		if (!$this->input->is_ajax_request()) {
			show_error('Direct access not allowed');
			return;
		}

		$file_id = $this->input->post('file_id');
		$user_id = $this->input->post('user_id');
		$permission = $this->input->post('permission', TRUE) ?: 'view'; // Default to view permission

		// Validate inputs
		if (!$file_id || !$user_id) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'File ID and User ID are required'
				]));
			return;
		}

		// Get file info
		$file = $this->File_library_model->get($file_id);

		if (!$file) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'File not found'
				]));
			return;
		}

		// Check if user has permission to share the file (owner or admin)
		if ($file->owner_id != $this->session->userdata('user_id') ) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'You do not have permission to share this file'
				]));
			return;
		}

		// Check if user exists

		$user = $this->Employees_model->get_by_id($user_id);

		if (!$user) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'User not found'
				]));
			return;
		}

		// Load file shares model
		$this->load->model('file_shares_model');

		// Check if the file is already shared with this user
		if ($this->File_shares_model->is_file_shared_with_user($file_id, $user_id)) {
			// Update the existing share
			$existing_shares = $this->File_shares_model->get_shares_by_file($file_id);

			foreach ($existing_shares as $share) {
				if ($share->shared_with == $user_id) {
					$this->File_shares_model->update($share->id, [
						'permission_level' => $permission,
						'date_shared' => date('Y-m-d H:i:s'),
						'is_active' => 1
					]);
					break;
				}
			}

			$action = 'updated';
		} else {
			// Create a new share
			$this->File_shares_model->insert([
				'file_id' => $file_id,
				'shared_by' => $this->session->userdata('user_id'),
				'shared_with' => $user_id,
				'date_shared' => date('Y-m-d H:i:s'),
				'permission_level' => $permission,
				'is_active' => 1
			]);

			$action = 'created';
		}

		// Get updated list of users the file is shared with
//		$shared_users = $this->File_shares_model->fuck($file_id);
$shared_users = array();
		// Format the user data for the response
		$formatted_users = [];
		foreach ($shared_users as $shared_user) {
			$formatted_users[] = [
				'id' => $shared_user->id,
				'user_name' => $shared_user->fullname,
				'user_email' => $shared_user->email,
				'permission' => $shared_user->permission_level,
				'share_id' => $shared_user->share_id,
				'date_shared' => $shared_user->date_shared
			];
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success' => true,
				'message' => 'File share ' . $action . ' successfully',
				'shares' => $formatted_users
			]));
	}
	/**
	 * Remove a file share
	 */
	public function remove_share()
	{
		// Check for AJAX request
		if (!$this->input->is_ajax_request()) {
			show_error('Direct access not allowed');
			return;
		}

		$share_id = $this->input->post('share_id');

		if (!$share_id) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'Share ID is required'
				]));
			return;
		}

		// Load models


		// Get share
		$share = $this->File_shares_model->get($share_id);

		if (!$share) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'Share not found'
				]));
			return;
		}

		// Get file info
		$file = $this->File_library_model->get($share->file_id);

		// Check if user has permission to remove the share (owner of file, the sharer, or admin)
		if ($file->owner_id != $this->session->userdata('user_id') &&
			$share->shared_by != $this->session->userdata('user_id')
			) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'You do not have permission to remove this share'
				]));
			return;
		}

		// Delete share (or mark inactive)
		// Option 1: Delete the share record
		$this->File_shares_model->delete($share_id);

		// Option 2: Mark as inactive instead of deleting
		// $this->file_shares_model->update($share_id, ['is_active' => 0]);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success' => true,
				'message' => 'File share removed successfully'
			]));
	}
}



	?>
