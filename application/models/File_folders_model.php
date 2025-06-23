<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * File_folders_model
 *
 * Handles database operations for file folders
 */
class File_folders_model extends CI_Model {

	public $table = 'file_folders';

	public function __construct() {
		parent::__construct();

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
	public function get_all() {
		$query = $this->db->get($this->table);
		return $query->results();
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
//		$this->db->where('is_system_folder','No');
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

	/**
	 * Search folders by name
	 *
	 * @param string $keyword
	 * @param int $limit
	 * @return array
	 */
	public function search_infolders($keyword, $folder_id, $limit = 10) {
		$this->db->like('folder_name', $keyword);

		if ($limit > 0) {
			$this->db->limit($limit);
		}

		$this->db->order_by('folder_name', 'asc');
		$this->db->where('parent_folder_id',$folder_id);
		$query = $this->db->get($this->table);
		return $query->result();
	}
	public function search_folders($keyword, $limit = 10) {
		$this->db->like('folder_name', $keyword);

		if ($limit > 0) {
			$this->db->limit($limit);
		}

		$this->db->order_by('folder_name', 'asc');

		$query = $this->db->get($this->table);
		return $query->result();
	}

	/**
	 * Count folders with optional filters
	 *
	 * @param array $filters
	 * @return int
	 */
	public function count_folders($filters = []) {
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
}
