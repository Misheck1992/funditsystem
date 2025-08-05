<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * File_library_model
 *
 * Handles database operations for file library
 */
class File_library_model extends CI_Model {

	private $table = 'file_library';

	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	/**
	 * Get file by ID
	 *
	 * @param int $id
	 * @return object|null
	 */
	public function get($id) {
		$query = $this->db->get_where($this->table, ['id' => $id]);
		return $query->row();
	}

	/**
	 * Get files with optional filters
	 *
	 * @param array $filters
	 * @param string $sort_field
	 * @param string $sort_direction
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function get_files($filters = [], $sort_field = 'date_added', $sort_direction = 'desc', $limit = 50, $offset = 0) {
		// Apply filters
		if (!empty($filters)) {
			foreach ($filters as $key => $value) {
				if (is_array($value)) {
					$this->db->where_in($key, $value);
				} else {
					$this->db->where($key, $value);
				}
			}
		}

		// Apply sorting
		$this->db->order_by($sort_field, $sort_direction);

		// Apply pagination
		if ($limit > 0) {
			$this->db->limit($limit, $offset);
		}

		$query = $this->db->get($this->table);
		return $query->result();
	}

	/**
	 * Count files with optional filters
	 *
	 * @param array $filters
	 * @return int
	 */
	public function count_files($filters = []) {
		// Apply filters
		if (!empty($filters)) {
			foreach ($filters as $key => $value) {
				if (is_array($value)) {
					$this->db->where_in($key, $value);
				} else {
					$this->db->where($key, $value);
				}
			}
		}

		return $this->db->count_all_results($this->table);
	}

	/**
	 * Insert a new file record
	 *
	 * @param array $data
	 * @return int
	 */
	public function insert($data) {
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	/**
	 * Update a file record
	 *
	 * @param int $id
	 * @param array $data
	 * @return bool
	 */
	public function update($id, $data) {
		$this->db->where('id', $id);
		return $this->db->update($this->table, $data);
	}

	/**
	 * Delete a file record
	 *
	 * @param int $id
	 * @return bool
	 */
	public function delete($id) {
		$this->db->where('id', $id);
		return $this->db->delete($this->table);
	}

	/**
	 * Search files by keyword
	 *
	 * @param string $keyword
	 * @param array $additional_filters
	 * @param string $sort_field
	 * @param string $sort_direction
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function search_files($keyword, $additional_filters = [], $sort_field = 'date_added', $sort_direction = 'desc', $limit = 50, $offset = 0) {
		// Apply keyword search
		if (!empty($keyword)) {
			$this->db->group_start();
			$this->db->like('file_name', $keyword);
			$this->db->or_like('description', $keyword);
			$this->db->or_like('tags', $keyword);
			$this->db->group_end();
		}

		// Apply additional filters
		if (!empty($additional_filters)) {
			foreach ($additional_filters as $key => $value) {
				if (is_array($value)) {
					$this->db->where_in($key, $value);
				} else {
					$this->db->where($key, $value);
				}
			}
		}

		// Apply sorting
		$this->db->order_by($sort_field, $sort_direction);

		// Apply pagination
		if ($limit > 0) {
			$this->db->limit($limit, $offset);
		}

		$query = $this->db->get($this->table);
		return $query->result();
	}
	public function search_files_in_folder($folder, $keyword, $additional_filters = [], $sort_field = 'file_library.date_added', $sort_direction = 'desc', $limit = 50, $offset = 0) {
		// Select specific fields with proper aliases to avoid ambiguity
		$this->db->select('file_library.id as id, file_library.file_name, file_library.file_path, file_library.file_type, file_library.file_size, file_library.owner_type, file_library.owner_id, file_library.is_public, file_library.description, file_library.tags, file_library.date_added, file_library.date_modified, file_library.added_by');

		// Apply keyword search
		if (!empty($keyword)) {
			$this->db->group_start();
			$this->db->like('file_library.file_name', $keyword);
			$this->db->or_like('file_library.description', $keyword);
			$this->db->or_like('file_library.tags', $keyword);
			$this->db->group_end();
		}

		// Apply additional filters
		if (!empty($additional_filters)) {
			foreach ($additional_filters as $key => $value) {
				if (is_array($value)) {
					$this->db->where_in($key, $value);
				} else {
					$this->db->where($key, $value);
				}
			}
		}

		// Apply sorting
		$this->db->order_by($sort_field, $sort_direction);

		// Apply pagination
		if ($limit > 0) {
			$this->db->limit($limit, $offset);
		}

		// Join tables
		$this->db->join('file_folder_mapping', 'file_folder_mapping.file_id = file_library.id');
		$this->db->join('file_folders', 'file_folder_mapping.folder_id = file_folders.id');
		$this->db->where('file_folders.id', $folder);

		$query = $this->db->get($this->table);
		return $query->result();
	}
	public function count_search_files_in_folder($folder,$keyword, $additional_filters = []) {
		// Apply keyword search
		if (!empty($keyword)) {
			$this->db->group_start();
			$this->db->like('file_library.file_name', $keyword);
			$this->db->or_like('file_library.description', $keyword);
			$this->db->or_like('file_library.tags', $keyword);
			$this->db->group_end();
		}

		// Apply additional filters
		if (!empty($additional_filters)) {
			foreach ($additional_filters as $key => $value) {
				if (is_array($value)) {
					$this->db->where_in($key, $value);
				} else {
					$this->db->where($key, $value);
				}
			}
		}

		// Apply sorting
		$this->db->join('file_folder_mapping','file_folder_mapping.file_id = file_library.id');
		$this->db->join('file_folders','file_folder_mapping.folder_id = file_folders.id');
		$this->db->where('file_folders.id',$folder);
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}

	/**
	 * Get files by folder ID
	 *
	 * @param int $folder_id
	 * @param string $sort_field
	 * @param string $sort_direction
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function get_files_by_folder($folder_id, $sort_field = 'date_added', $sort_direction = 'desc', $limit = 50, $offset = 0) {
		$this->db->select('fl.*');
		$this->db->from($this->table . ' as fl');
		$this->db->join('file_folder_mapping as ffm', 'fl.id = ffm.file_id', 'inner');
		$this->db->where('ffm.folder_id', $folder_id);

		// Apply sorting
		$this->db->order_by($sort_field, $sort_direction);

		// Apply pagination
		if ($limit > 0) {
			$this->db->limit($limit, $offset);
		}

		$query = $this->db->get();
		return $query->result();
	}

	/**
	 * Get count of files by folder ID
	 *
	 * @param int $folder_id
	 * @return int
	 */
	public function count_files_by_folder($folder_id) {
		$this->db->select('COUNT(*) as count');
		$this->db->from($this->table . ' as fl');
		$this->db->join('file_folder_mapping as ffm', 'fl.id = ffm.file_id', 'inner');
		$this->db->where('ffm.folder_id', $folder_id);

		$query = $this->db->get();
		return $query->row()->count;
	}
}

/**
 * File_folders_model
 *
 * Handles database operations for file folders
 */
class File_folders_model extends CI_Model {

	private $table = 'file_folders';

	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	/**
	 * Get folder by ID
	 *
	 * @param int $id
	 * @return object|null
	 */
	public function get($id) {
		$query = $this->db->get_where($this->table, ['id' => $id]);
		return $query->row();
	}

	/**
	 * Get folders with optional filters
	 *
	 * @param array $filters
	 * @param string $sort_field
	 * @param string $sort_direction
	 * @return array
	 */
	public function get_folders($filters = [], $sort_field = 'folder_name', $sort_direction = 'asc') {
		// Apply filters
		if (!empty($filters)) {
			foreach ($filters as $key => $value) {
				if (is_array($value)) {
					$this->db->where_in($key, $value);
				} else {
					$this->db->where($key, $value);
				}
			}
		}

		// Apply sorting
		$this->db->order_by($sort_field, $sort_direction);

		$query = $this->db->get($this->table);
		return $query->result();
	}

	/**
	 * Insert a new folder
	 *
	 * @param array $data
	 * @return int
	 */
	public function insert($data) {
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	/**
	 * Update a folder
	 *
	 * @param int $id
	 * @param array $data
	 * @return bool
	 */
	public function update($id, $data) {
		$this->db->where('id', $id);
		return $this->db->update($this->table, $data);
	}

	/**
	 * Delete a folder
	 *
	 * @param int $id
	 * @return bool
	 */
	public function delete($id) {
		$this->db->where('id', $id);
		return $this->db->delete($this->table);
	}

	/**
	 * Get folders by parent ID
	 *
	 * @param int|null $parent_id
	 * @param string $sort_field
	 * @param string $sort_direction
	 * @return array
	 */
	public function get_folders_by_parent($parent_id = null, $sort_field = 'folder_name', $sort_direction = 'asc') {
		if ($parent_id === null) {
			$this->db->where('parent_folder_id IS NULL', null, false);
		} else {
			$this->db->where('parent_folder_id', $parent_id);
		}

		// Apply sorting
		$this->db->order_by($sort_field, $sort_direction);

		$query = $this->db->get($this->table);
		return $query->result();
	}

	/**
	 * Get folder path (breadcrumb)
	 *
	 * @param int $folder_id
	 * @return array
	 */
	public function get_folder_path($folder_id) {
		$path = [];
		$current_folder = $this->get($folder_id);

		// If folder not found, return empty path
		if (!$current_folder) {
			return $path;
		}

		// Add current folder to path
		$path[] = $current_folder;

		// Recursively add parent folders
		while ($current_folder && $current_folder->parent_folder_id) {
			$current_folder = $this->get($current_folder->parent_folder_id);
			if ($current_folder) {
				array_unshift($path, $current_folder);
			}
		}

		return $path;
	}
}

/**
 * File_folder_mapping_model
 *
 * Handles database operations for file to folder mappings
 */
class File_folder_mapping_model extends CI_Model {

	private $table = 'file_folder_mapping';

	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	/**
	 * Insert a new mapping
	 *
	 * @param array $data
	 * @return int
	 */
	public function insert($data) {
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	/**
	 * Delete a mapping
	 *
	 * @param int $file_id
	 * @param int $folder_id
	 * @return bool
	 */
	public function delete($file_id, $folder_id) {
		$this->db->where('file_id', $file_id);
		$this->db->where('folder_id', $folder_id);
		return $this->db->delete($this->table);
	}

	/**
	 * Delete all mappings for a file
	 *
	 * @param int $file_id
	 * @return bool
	 */
	public function delete_file_mappings($file_id) {
		$this->db->where('file_id', $file_id);
		return $this->db->delete($this->table);
	}

	/**
	 * Delete all mappings for a folder
	 *
	 * @param int $folder_id
	 * @return bool
	 */
	public function delete_folder_mappings($folder_id) {
		$this->db->where('folder_id', $folder_id);
		return $this->db->delete($this->table);
	}

	/**
	 * Get folder IDs for a file
	 *
	 * @param int $file_id
	 * @return array
	 */
	public function get_folders_for_file($file_id) {
		$this->db->select('folder_id');
		$this->db->where('file_id', $file_id);
		$query = $this->db->get($this->table);

		$folder_ids = [];
		foreach ($query->result() as $row) {
			$folder_ids[] = $row->folder_id;
		}

		return $folder_ids;
	}

	/**
	 * Get file IDs for a folder
	 *
	 * @param int $folder_id
	 * @return array
	 */
	public function get_files_for_folder($folder_id) {
		$this->db->select('file_id');
		$this->db->where('folder_id', $folder_id);
		$query = $this->db->get($this->table);

		$file_ids = [];
		foreach ($query->result() as $row) {
			$file_ids[] = $row->file_id;
		}

		return $file_ids;
	}
}

/**
 * File_shares_model
 *
 * Handles database operations for file sharing
 */
class File_shares_model extends CI_Model {

	private $table = 'file_shares';

	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	/**
	 * Get file share by ID
	 *
	 * @param int $id
	 * @return object|null
	 */
	public function get($id) {
		$query = $this->db->get_where($this->table, ['id' => $id]);
		return $query->row();
	}

	/**
	 * Get shares by file ID
	 *
	 * @param int $file_id
	 * @return array
	 */
	public function get_shares_by_file($file_id) {
		$this->db->where('file_id', $file_id);
		$this->db->where('is_active', 1);
		$query = $this->db->get($this->table);
		return $query->result();
	}

	/**
	 * Get shares by user ID (shared with)
	 *
	 * @param int $user_id
	 * @return array
	 */
	public function get_shares_for_user($user_id) {
		$this->db->where('shared_with', $user_id);
		$this->db->where('is_active', 1);
		$query = $this->db->get($this->table);
		return $query->result();
	}

	/**
	 * Get shares by user ID (shared by)
	 *
	 * @param int $user_id
	 * @return array
	 */
	public function get_shares_by_user($user_id) {
		$this->db->where('shared_by', $user_id);
		$query = $this->db->get($this->table);
		return $query->result();
	}

	/**
	 * Insert a new share
	 *
	 * @param array $data
	 * @return int
	 */
	public function insert($data) {
		$this->db->insert($this->table, $data);
		return $this->db->insert_id();
	}

	/**
	 * Update a share
	 *
	 * @param int $id
	 * @param array $data
	 * @return bool
	 */
	public function update($id, $data) {
		$this->db->where('id', $id);
		return $this->db->update($this->table, $data);
	}

	/**
	 * Delete a share
	 *
	 * @param int $id
	 * @return bool
	 */
	public function delete($id) {
		$this->db->where('id', $id);
		return $this->db->delete($this->table);
	}

	/**
	 * Check if a file is shared with a user
	 *
	 * @param int $file_id
	 * @param int $user_id
	 * @return bool
	 */
	public function is_file_shared_with_user($file_id, $user_id) {
		$this->db->where('file_id', $file_id);
		$this->db->where('shared_with', $user_id);
		$this->db->where('is_active', 1);
		return $this->db->count_all_results($this->table) > 0;
	}

	/**
	 * Remove all shares for a file
	 *
	 * @param int $file_id
	 * @return bool
	 */
	public function remove_file_shares($file_id) {
		$this->db->where('file_id', $file_id);
		return $this->db->delete($this->table);
	}
}
