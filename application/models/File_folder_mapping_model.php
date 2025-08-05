<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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

	/**
	 * Count files in a folder
	 *
	 * @param int $folder_id
	 * @return int
	 */
	public function count_files_in_folder($folder_id) {
		$this->db->where('folder_id', $folder_id);
		return $this->db->count_all_results($this->table);
	}

	/**
	 * Check if file exists in folder
	 *
	 * @param int $file_id
	 * @param int $folder_id
	 * @return bool
	 */
	public function file_exists_in_folder($file_id, $folder_id) {
		$this->db->where('file_id', $file_id);
		$this->db->where('folder_id', $folder_id);
		$count = $this->db->count_all_results($this->table);

		return $count > 0;
	}

	/**
	 * Get all mappings
	 *
	 * @return array
	 */
	public function get_all_mappings() {
		$query = $this->db->get($this->table);
		return $query->result();
	}

	/**
	 * Get mappings by filter
	 *
	 * @param array $filters
	 * @return array
	 */
	public function get_mappings($filters = []) {
		if (!empty($filters)) {
			foreach ($filters as $key => $value) {
				if (is_array($value)) {
					$this->db->where_in($key, $value);
				} else {
					$this->db->where($key, $value);
				}
			}
		}

		$query = $this->db->get($this->table);
		return $query->result();
	}

	/**
	 * Get files with folder details
	 *
	 * @param int $folder_id
	 * @return array
	 */
	public function get_files_with_details($folder_id) {
		$this->db->select('f.*, ffm.date_added as added_to_folder');
		$this->db->from($this->table . ' as ffm');
		$this->db->join('file_library as f', 'ffm.file_id = f.id', 'inner');
		$this->db->where('ffm.folder_id', $folder_id);

		$query = $this->db->get();
		return $query->result();
	}
}
