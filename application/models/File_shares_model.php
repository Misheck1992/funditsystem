<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * File_shares_model
 *
 * Handles database operations for file sharing
 */
class File_shares_model extends CI_Model {

	private $table = 'file_shares';

	public function __construct() {
		parent::__construct();

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
	public function something_else($file_id) {
		$this->db->select('u.id, u.Firstname, u.EmailAddress, fs.permission_level, fs.date_shared, fs.id as share_id');
		$this->db->from($this->table . ' as fs');
		$this->db->join('Employees as u', 'fs.shared_with = u.id', 'inner');
		$this->db->where('fs.file_id', $file_id);
		$this->db->where('fs.is_active', 1);
		$this->db->order_by('u.Firstname', 'asc');

		$query = $this->db->get();
		return $query->result();
	}
	/**
	 * Get shares by user ID (shared with)
	 *
	 * @param int $user_id
	 * @param string $sort_field
	 * @param string $sort_direction
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function get_shares_for_user($user_id, $sort_field = 'date_shared', $sort_direction = 'desc', $limit = 0, $offset = 0) {
		$this->db->where('shared_with', $user_id);
		$this->db->where('is_active', 1);

		// Apply sorting
		$this->db->order_by($sort_field, $sort_direction);

		// Apply limit if specified
		if ($limit > 0) {
			$this->db->limit($limit, $offset);
		}

		$query = $this->db->get($this->table);
		return $query->result();
	}

	/**
	 * Count shares for a user
	 *
	 * @param int $user_id
	 * @return int
	 */
	public function count_shares_for_user($user_id) {
		$this->db->where('shared_with', $user_id);
		$this->db->where('is_active', 1);
		return $this->db->count_all_results($this->table);
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

	/**
	 * Get files shared with a user with details
	 *
	 * @param int $user_id
	 * @param string $sort_field
	 * @param string $sort_direction
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function get_shared_files_with_details($user_id, $sort_field = 'date_shared', $sort_direction = 'desc', $limit = 0, $offset = 0) {
		$this->db->select('fl.*, fs.date_shared, fs.permission_level, fs.shared_by, u.fullname as shared_by_name');
		$this->db->from($this->table . ' as fs');
		$this->db->join('file_library as fl', 'fs.file_id = fl.id', 'inner');
		$this->db->join('users as u', 'fs.shared_by = u.id', 'left');
		$this->db->where('fs.shared_with', $user_id);
		$this->db->where('fs.is_active', 1);

		// Apply sorting
		if ($sort_field === 'date_shared') {
			$this->db->order_by('fs.' . $sort_field, $sort_direction);
		} else {
			$this->db->order_by('fl.' . $sort_field, $sort_direction);
		}

		// Apply limit if specified
		if ($limit > 0) {
			$this->db->limit($limit, $offset);
		}

		$query = $this->db->get();
		return $query->result();
	}

	/**
	 * Get users a file is shared with
	 *
	 * @param int $file_id
	 * @return array
	 */
	public function get_users_for_shared_file1($file_id) {
//		$this->db->select('u.id, u.Firstname, u.EmailAddress, fs.permission_level, fs.date_shared, fs.id as share_id');
//		$this->db->from($this->table . ' as fs');
//		$this->db->join('employees as u', 'fs.shared_with = u.id');
//		$this->db->where('fs.file_id', $file_id);
//		$this->db->where('fs.is_active', 1);
//
//
//		$query = $this->db->get();
		return array();
	}
public function fuck()
{
	return array();
}
}
