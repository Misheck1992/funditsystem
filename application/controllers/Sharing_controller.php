<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Sharing_controller
 *
 * Controller for file sharing operations
 */
class Sharing_controller extends CI_Controller {

	public function __construct() {
		parent::__construct();
		// Load models
		$this->load->model('file_library_model');
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
	 * Get shares for a file
	 */
	public function get_file_shares() {
		$file_id = $this->input->get('file_id');

		// Get file
		$file = $this->file_library_model->get($file_id);

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
					'message' => 'You do not have permission to view shares for this file'
				]));
			return;
		}

		// Get shares
		$shares = $this->file_shares_model->get_shares_by_file($file_id);

		// Get user details for each share
		$this->load->model('users_model');
		foreach ($shares as &$share) {
			$share->user = $this->users_model->get_user_by_id($share->shared_with);
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success' => true,
				'shares' => $shares
			]));
	}

	/**
	 * Share a file with another user
	 */
	public function share_file() {
		// Check for AJAX request
		if (!$this->input->is_ajax_request()) {
			show_error('Direct access not allowed');
			return;
		}

		$file_id = $this->input->post('file_id');
		$user_id = $this->input->post('user_id');
		$permission_level = $this->input->post('permission_level') ?: 'view';

		// Validate permission level
		$valid_permissions = ['view', 'edit'];
		if (!in_array($permission_level, $valid_permissions)) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'Invalid permission level'
				]));
			return;
		}

		// Get file
		$file = $this->file_library_model->get($file_id);

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
					'message' => 'You do not have permission to share this file'
				]));
			return;
		}

		// Validate user to share with
		$this->load->model('users_model');
		$user = $this->users_model->get_user_by_id($user_id);

		if (!$user) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'User not found'
				]));
			return;
		}

		// Check if already shared with this user
		if ($this->file_shares_model->is_file_shared_with_user($file_id, $user_id)) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'File is already shared with this user'
				]));
			return;
		}

		// Create share
		$share_data = [
			'file_id' => $file_id,
			'shared_by' => $this->auth->get_user_id(),
			'shared_with' => $user_id,
			'date_shared' => date('Y-m-d H:i:s'),
			'permission_level' => $permission_level,
			'is_active' => 1
		];

		$share_id = $this->file_shares_model->insert($share_data);

		// Get share details
		$share = $this->file_shares_model->get($share_id);
		$share->user = $user;

		// Send notification to user
		$this->_send_share_notification($file, $user, $permission_level);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success' => true,
				'message' => 'File shared successfully',
				'share' => $share
			]));
	}

	/**
	 * Update sharing permissions
	 */
	public function update_share() {
		// Check for AJAX request
		if (!$this->input->is_ajax_request()) {
			show_error('Direct access not allowed');
			return;
		}

		$share_id = $this->input->post('share_id');
		$permission_level = $this->input->post('permission_level');

		// Validate permission level
		$valid_permissions = ['view', 'edit'];
		if (!in_array($permission_level, $valid_permissions)) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'Invalid permission level'
				]));
			return;
		}

		// Get share
		$share = $this->file_shares_model->get($share_id);

		if (!$share) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'Share not found'
				]));
			return;
		}

		// Get file
		$file = $this->file_library_model->get($share->file_id);

		// Check ownership
		if ($file->added_by != $this->auth->get_user_id() && !$this->auth->has_permission('file_library_manage_all')) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'You do not have permission to update this share'
				]));
			return;
		}

		// Update share
		$this->file_shares_model->update($share_id, [
			'permission_level' => $permission_level
		]);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success' => true,
				'message' => 'Share updated successfully'
			]));
	}

	/**
	 * Remove sharing
	 */
	public function remove_share() {
		// Check for AJAX request
		if (!$this->input->is_ajax_request()) {
			show_error('Direct access not allowed');
			return;
		}

		$share_id = $this->input->post('share_id');

		// Get share
		$share = $this->file_shares_model->get($share_id);

		if (!$share) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'Share not found'
				]));
			return;
		}

		// Get file
		$file = $this->file_library_model->get($share->file_id);

		// Check ownership
		if ($file->added_by != $this->auth->get_user_id() &&
			$share->shared_by != $this->auth->get_user_id() &&
			!$this->auth->has_permission('file_library_manage_all')) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'You do not have permission to remove this share'
				]));
			return;
		}

		// Remove share
		$this->file_shares_model->delete($share_id);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success' => true,
				'message' => 'Share removed successfully'
			]));
	}

	/**
	 * Get files shared with me
	 */
	public function get_shared_files() {
		$sort_field = $this->input->get('sort_field') ?: 'date_shared';
		$sort_direction = $this->input->get('sort_direction') ?: 'desc';
		$page = $this->input->get('page') ?: 1;
		$limit = $this->input->get('limit') ?: 50;
		$offset = ($page - 1) * $limit;

		// Get shares for current user
		$shares = $this->file_shares_model->get_shares_for_user($this->auth->get_user_id(), $sort_field, $sort_direction, $limit, $offset);

		// Get file details for each share
		$files = [];
		foreach ($shares as $share) {
			$file = $this->file_library_model->get($share->file_id);

			if ($file) {
				// Add sharing information to file
				$file->shared_by_user_id = $share->shared_by;
				$file->shared_date = $share->date_shared;
				$file->permission_level = $share->permission_level;
				$file->share_id = $share->id;

				// Get user who shared
				$this->load->model('users_model');
				$shared_by_user = $this->users_model->get_user_by_id($share->shared_by);
				$file->shared_by_name = $shared_by_user ? $shared_by_user->fullname : 'Unknown';

				$files[] = $file;
			}
		}

		// Get total count
		$total_files = $this->file_shares_model->count_shares_for_user($this->auth->get_user_id());

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
	 * Send notification to user about file share
	 *
	 * @param object $file
	 * @param object $user
	 * @param string $permission_level
	 */
	private function _send_share_notification($file, $user, $permission_level) {
		// This is a placeholder for notification functionality
		// In a real implementation, this would send an email or in-app notification

		// Example notification code:
		/*
		$this->load->library('email');

		$this->email->from('noreply@financereal.com', 'Finance Real');
		$this->email->to($user->email);
		$this->email->subject('File shared with you');

		$message = "Hello " . $user->fullname . ",\n\n";
		$message .= "A file has been shared with you on Finance Real.\n\n";
		$message .= "File: " . $file->file_name . "\n";
		$message .= "Shared by: " . $this->auth->get_user_name() . "\n";
		$message .= "Permission: " . ucfirst($permission_level) . "\n\n";
		$message .= "You can access this file by logging into the system.\n\n";
		$message .= "Best regards,\nFinance Real Team";

		$this->email->message($message);
		$this->email->send();
		*/
	}
}
