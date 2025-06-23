<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Folders_controller
 *
 * Controller for folder operations
 */
class Folders_controller extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		// Load models
		$this->load->model('file_folders_model');
		$this->load->model('file_folder_mapping_model');

		// Check if user is logged in

	}

	/**
	 * Get folder tree for sidebar navigation
	 */
	public function get_folder_tree()
	{
		$user_id = $this->session->userdata('user_id');

		// Get root folders
		$root_folders = $this->file_folders_model->get_folders([
			'parent_folder_id' => null,
			'owner_id' => $user_id
		]);

		// Build tree recursively
		$tree = [];
		foreach ($root_folders as $folder) {
			$tree[] = $this->_build_folder_tree($folder);
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success' => true,
				'tree' => $tree
			]));
	}

	/**
	 * Create a new folder
	 */
	public function create_folder()
	{
		// Check for AJAX request
		if (!$this->input->is_ajax_request()) {
			show_error('Direct access not allowed');
			return;
		}

		$folder_name = $this->input->post('folder_name');
		$parent_folder_id = $this->input->post('parent_folder_id') ? (int)$this->input->post('parent_folder_id') : null;
		$description = $this->input->post('description');
		$is_public = $this->input->post('is_public') ? 1 : 0;

		// Validate folder name
		if (empty($folder_name)) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'Folder name is required'
				]));
			return;
		}

		// Validate parent folder if specified
		if ($parent_folder_id) {
			$parent_folder = $this->file_folders_model->get($parent_folder_id);

			if (!$parent_folder) {
				$this->output
					->set_content_type('application/json')
					->set_output(json_encode([
						'success' => false,
						'message' => 'Parent folder not found'
					]));
				return;
			}

			// Check ownership of parent folder
			//if ($parent_folder->owner_id != $this->session->userdata('user_id') && !$this->auth->has_permission('file_library_manage_all')) {
			if ($parent_folder->owner_id != $this->session->userdata('user_id')) {
				$this->output
					->set_content_type('application/json')
					->set_output(json_encode([
						'success' => false,
						'message' => 'You do not have permission to create subfolders in this folder'
					]));
				return;
			}
		}

		// Check for duplicate folder name in the same parent
		$existing_folders = $this->file_folders_model->get_folders([
			'folder_name' => $folder_name,
			'parent_folder_id' => $parent_folder_id,
			'owner_id' => $this->session->userdata('user_id')
		]);

		if (!empty($existing_folders)) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'A folder with this name already exists in the selected location'
				]));
			return;
		}

		// Create folder
		$folder_data = [
			'folder_name' => $folder_name,
			'parent_folder_id' => $parent_folder_id,
			'owner_id' => $this->session->userdata('user_id'),
			'is_public' => $is_public,
			'date_created' => date('Y-m-d H:i:s'),
			'date_modified' => date('Y-m-d H:i:s'),
			'description' => $description
		];

		$folder_id = $this->file_folders_model->insert($folder_data);

		// Get the new folder
		$folder = $this->file_folders_model->get($folder_id);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success' => true,
				'message' => 'Folder created successfully',
				'folder' => $folder
			]));
	}

	/**
	 * Rename a folder
	 */
	public function rename_folder()
	{
		// Check for AJAX request
		if (!$this->input->is_ajax_request()) {
			show_error('Direct access not allowed');
			return;
		}

		$folder_id = $this->input->post('folder_id');
		$new_name = $this->input->post('folder_name');
		$description = $this->input->post('description');

		// Validate folder name
		if (empty($new_name)) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'Folder name is required'
				]));
			return;
		}

		// Get folder
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

		// Check ownership
		if ($folder->owner_id != $this->auth->get_user_id() && !$this->auth->has_permission('file_library_manage_all')) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'You do not have permission to rename this folder'
				]));
			return;
		}

		// Check for duplicate folder name
		$existing_folders = $this->file_folders_model->get_folders([
			'folder_name' => $new_name,
			'parent_folder_id' => $folder->parent_folder_id,
			'owner_id' => $this->auth->get_user_id(),
			'id !=' => $folder_id
		]);

		if (!empty($existing_folders)) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'A folder with this name already exists in the same location'
				]));
			return;
		}

		// Update folder
		$update_data = [
			'folder_name' => $new_name,
			'date_modified' => date('Y-m-d H:i:s')
		];

		if ($description !== null) {
			$update_data['description'] = $description;
		}

		$this->file_folders_model->update($folder_id, $update_data);

		// Get updated folder
		$updated_folder = $this->file_folders_model->get($folder_id);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success' => true,
				'message' => 'Folder renamed successfully',
				'folder' => $updated_folder
			]));
	}

	/**
	 * Delete a folder
	 */
	public function delete_folder()
	{
		// Check for AJAX request
		if (!$this->input->is_ajax_request()) {
			show_error('Direct access not allowed');
			return;
		}

		$folder_id = $this->input->post('folder_id');
		$delete_files = $this->input->post('delete_files') ? true : false;

		// Get folder
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

		// Check ownership
		if ($folder->owner_id != $this->auth->get_user_id() && !$this->auth->has_permission('file_library_manage_all')) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'You do not have permission to delete this folder'
				]));
			return;
		}

		// Check for subfolders
		$subfolders = $this->file_folders_model->get_folders_by_parent($folder_id);

		if (!empty($subfolders)) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'Cannot delete folder with subfolders. Please delete subfolders first.'
				]));
			return;
		}

		// Get files in folder
		$file_ids = $this->file_folder_mapping_model->get_files_for_folder($folder_id);

		if ($delete_files) {
			// Delete files
			$this->load->model('file_library_model');

			foreach ($file_ids as $file_id) {
				$file = $this->file_library_model->get($file_id);

				if ($file) {
					// Delete physical file
					if (file_exists($file->file_path)) {
						unlink($file->file_path);
					}

					// Delete file record
					$this->file_library_model->delete($file_id);
				}
			}
		} else {
			// Remove mappings but keep files
			$this->file_folder_mapping_model->delete_folder_mappings($folder_id);
		}

		// Delete folder
		$this->file_folders_model->delete($folder_id);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success' => true,
				'message' => 'Folder deleted successfully'
			]));
	}

	/**
	 * Move folder to a different parent
	 */
	public function move_folder()
	{
		// Check for AJAX request
		if (!$this->input->is_ajax_request()) {
			show_error('Direct access not allowed');
			return;
		}

		$folder_id = $this->input->post('folder_id');
		$target_parent_id = $this->input->post('target_parent_id') ?: null;

		// Get folder
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

		// Check ownership
		if ($folder->owner_id != $this->auth->get_user_id() && !$this->auth->has_permission('file_library_manage_all')) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'You do not have permission to move this folder'
				]));
			return;
		}

		// Validate target parent
		if ($target_parent_id) {
			// Cannot move to itself
			if ($target_parent_id == $folder_id) {
				$this->output
					->set_content_type('application/json')
					->set_output(json_encode([
						'success' => false,
						'message' => 'Cannot move folder to itself'
					]));
				return;
			}

			$target_parent = $this->file_folders_model->get($target_parent_id);

			if (!$target_parent) {
				$this->output
					->set_content_type('application/json')
					->set_output(json_encode([
						'success' => false,
						'message' => 'Target folder not found'
					]));
				return;
			}

			// Check ownership of target folder
			if ($target_parent->owner_id != $this->auth->get_user_id() && !$this->auth->has_permission('file_library_manage_all')) {
				$this->output
					->set_content_type('application/json')
					->set_output(json_encode([
						'success' => false,
						'message' => 'You do not have permission to move to the target folder'
					]));
				return;
			}

			// Check if target is a subfolder of the folder being moved
			$check_parent = $target_parent;
			while ($check_parent && $check_parent->parent_folder_id) {
				if ($check_parent->parent_folder_id == $folder_id) {
					$this->output
						->set_content_type('application/json')
						->set_output(json_encode([
							'success' => false,
							'message' => 'Cannot move a folder to its own subfolder'
						]));
					return;
				}
				$check_parent = $this->file_folders_model->get($check_parent->parent_folder_id);
			}

			// Check for duplicate folder name in target
			$existing_folders = $this->file_folders_model->get_folders([
				'folder_name' => $folder->folder_name,
				'parent_folder_id' => $target_parent_id,
				'id !=' => $folder_id
			]);

			if (!empty($existing_folders)) {
				$this->output
					->set_content_type('application/json')
					->set_output(json_encode([
						'success' => false,
						'message' => 'A folder with this name already exists in the target location'
					]));
				return;
			}
		}

		// Update folder
		$this->file_folders_model->update($folder_id, [
			'parent_folder_id' => $target_parent_id,
			'date_modified' => date('Y-m-d H:i:s')
		]);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success' => true,
				'message' => 'Folder moved successfully'
			]));
	}

	/**
	 * Toggle folder public status
	 */
	/**
	 * Get folder tree for sidebar navigation
	 */


	/**
	 * Create a new folder
	 */


	/**
	 * Rename a folder
	 */


	/**
	 * Delete a folder
	 */


	/**
	 * Recursively build folder tree
	 *
	 * @param object $folder
	 * @return array
	 */
	private function _build_folder_tree($folder)
	{
		// Get subfolders
		$subfolders = $this->file_folders_model->get_folders_by_parent($folder->id);

		// Count files in folder
		$file_count = $this->file_folder_mapping_model->count_files_in_folder($folder->id);

		// Build node
		$node = [
			'id' => $folder->id,
			'name' => $folder->folder_name,
			'file_count' => $file_count,
			'is_public' => $folder->is_public ? true : false,
			'children' => []
		];

		// Recursively add children
		foreach ($subfolders as $subfolder) {
			$node['children'][] = $this->_build_folder_tree($subfolder);
		}

		return $node;
	}

	/**
	 * Get folder path from root to the specified folder
	 *
	 * Returns an array of folder objects representing the path
	 * from root to the specified folder
	 */
	public function get_folder_path()
	{
		// Check for AJAX request
		if (!$this->input->is_ajax_request()) {
			show_error('Direct access not allowed');
			return;
		}

		$folder_id = $this->input->get('folder_id');

		if (!$folder_id) {
			$this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'success' => false,
					'message' => 'Folder ID is required'
				]));
			return;
		}

		// Get the folder
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

		// Check ownership - only check if not a public folder
		if (!$folder->is_public) {
			if ($folder->owner_id != $this->session->userdata('user_id')) {
				$this->output
					->set_content_type('application/json')
					->set_output(json_encode([
						'success' => false,
						'message' => 'You do not have permission to access this folder'
					]));
				return;
			}
		}

		// Build path array
		$path = $this->_build_folder_path($folder_id);

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success' => true,
				'path' => $path
			]));
	}

	/**
	 * Build folder path from root to specified folder
	 *
	 * @param int $folder_id
	 * @return array
	 */
	private function _build_folder_path($folder_id)
	{
		$path = [];
		$current_folder = $this->file_folders_model->get($folder_id);

		// Build path from folder to root (will be in reverse order)
		while ($current_folder) {
			// Add current folder to path
			$path[] = [
				'id' => $current_folder->id,
				'folder_name' => $current_folder->folder_name,
				'parent_id' => $current_folder->parent_folder_id
			];

			// Move up to parent folder
			if ($current_folder->parent_folder_id) {
				$current_folder = $this->file_folders_model->get($current_folder->parent_folder_id);
			} else {
				// Reached root folder
				break;
			}
		}

		// Reverse array to get path from root to folder
		return array_reverse($path);
	}
}
 ?>
