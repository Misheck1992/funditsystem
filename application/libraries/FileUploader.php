<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * FileUploader Library
 *
 * Custom library to handle file uploads
 */
class FileUploader {
	private $CI;
	private $config = [
		'upload_path'   => './uploads/files/',
		'allowed_types' => 'gif|jpg|jpeg|png|pdf|doc|docx|xls|xlsx|csv|txt|zip|rar',
		'max_size'      => 5120, // 5MB
		'encrypt_name'  => TRUE
	];

	/**
	 * Constructor
	 */
	public function __construct() {
		// Get CodeIgniter instance
		$this->CI =& get_instance();

		// Load required libraries
		$this->CI->load->library('upload');
	}

	/**
	 * Initialize uploader with custom config
	 *
	 * @param array $config
	 */
	public function initialize($config = []) {
		$this->config = array_merge($this->config, $config);
		$this->CI->upload->initialize($this->config);
	}

	/**
	 * Do upload
	 *
	 * @param string $field_name
	 * @return bool
	 */
	public function do_upload($field_name = 'file') {
		if (!$this->CI->upload->do_upload($field_name)) {
			return false;
		}

		return true;
	}

	/**
	 * Get upload data
	 *
	 * @return array
	 */
	public function data() {
		return $this->CI->upload->data();
	}

	/**
	 * Display errors
	 *
	 * @param string $prefix
	 * @param string $suffix
	 * @return string
	 */
	public function display_errors($prefix = '', $suffix = '') {
		return $this->CI->upload->display_errors($prefix, $suffix);
	}

	/**
	 * Handle multiple file uploads
	 *
	 * @param string $field_name
	 * @return array
	 */
	public function do_multiple_upload($field_name = 'files') {
		$files = $_FILES;
		$file_count = count($_FILES[$field_name]['name']);
		$uploaded_files = [];

		// Loop through each file
		for ($i = 0; $i < $file_count; $i++) {
			// Prepare for upload
			$_FILES['file']['name'] = $files[$field_name]['name'][$i];
			$_FILES['file']['type'] = $files[$field_name]['type'][$i];
			$_FILES['file']['tmp_name'] = $files[$field_name]['tmp_name'][$i];
			$_FILES['file']['error'] = $files[$field_name]['error'][$i];
			$_FILES['file']['size'] = $files[$field_name]['size'][$i];

			// Upload file
			if ($this->do_upload('file')) {
				$uploaded_files[] = $this->data();
			}
		}

		return $uploaded_files;
	}

	/**
	 * Validate file before upload
	 *
	 * @param string $field_name
	 * @return bool|string
	 */
	public function validate($field_name = 'file') {
		if (!isset($_FILES[$field_name])) {
			return 'No file selected';
		}

		$file = $_FILES[$field_name];

		// Check file size
		if ($file['size'] > $this->config['max_size'] * 1024) {
			return 'File too large. Maximum size is ' . $this->config['max_size'] . 'KB';
		}

		// Check file type
		$file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
		$allowed_types = explode('|', $this->config['allowed_types']);

		if (!in_array(strtolower($file_ext), $allowed_types)) {
			return 'File type not allowed. Allowed types are: ' . $this->config['allowed_types'];
		}

		return true;
	}
}

/**
 * FilePreview Library
 *
 * Custom library to generate file previews
 */
class FilePreview {
	private $CI;
	private $image_lib_config = [
		'create_thumb' => TRUE,
		'maintain_ratio' => TRUE,
		'width' => 200,
		'height' => 200
	];

	/**
	 * Constructor
	 */
	public function __construct() {
		// Get CodeIgniter instance
		$this->CI =& get_instance();

		// Load required libraries
		$this->CI->load->library('image_lib');
	}

	/**
	 * Create thumbnail for an image
	 *
	 * @param string $source_path
	 * @param int $width
	 * @param int $height
	 * @return string
	 */
	public function create_thumbnail($source_path, $width = 200, $height = 200) {
		// Check if source file exists
		if (!file_exists($source_path)) {
			return false;
		}

		// Get file info
		$path_info = pathinfo($source_path);

		// Generate thumbnail path
		$thumb_path = $path_info['dirname'] . '/' . $path_info['filename'] . '_thumb.' . $path_info['extension'];

		// Check if thumbnail already exists
		if (file_exists($thumb_path)) {
			return $thumb_path;
		}

		// Configure image library
		$config = $this->image_lib_config;
		$config['source_image'] = $source_path;
		$config['width'] = $width;
		$config['height'] = $height;

		$this->CI->image_lib->initialize($config);

		// Create thumbnail
		if (!$this->CI->image_lib->resize()) {
			return false;
		}

		// Return thumbnail path
		return $path_info['dirname'] . '/' . $path_info['filename'] . '_thumb.' . $path_info['extension'];
	}

	/**
	 * Generate preview for a file
	 *
	 * @param string $file_path
	 * @param string $file_type
	 * @return mixed
	 */
	public function generate_preview($file_path, $file_type) {
		// Check if source file exists
		if (!file_exists($file_path)) {
			return false;
		}

		// Handle different file types
		if (strpos($file_type, 'image/') === 0) {
			// Image preview
			return $this->create_thumbnail($file_path);
		} elseif ($file_type === 'application/pdf') {
			// PDF preview - return path for browser to handle
			return $file_path;
		} elseif ($file_type === 'text/plain' || $file_type === 'text/csv') {
			// Text file preview
			return file_get_contents($file_path);
		} else {
			// No preview available
			return false;
		}
	}

	/**
	 * Get file content type
	 *
	 * @param string $file_path
	 * @return string
	 */
	public function get_content_type($file_path) {
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime_type = finfo_file($finfo, $file_path);
		finfo_close($finfo);

		return $mime_type;
	}

	/**
	 * Check if file type is previewable
	 *
	 * @param string $file_type
	 * @return bool
	 */
	public function is_previewable($file_type) {
		$previewable_types = [
			'image/jpeg', 'image/png', 'image/gif',
			'application/pdf',
			'text/plain', 'text/csv'
		];

		return in_array($file_type, $previewable_types);
	}
}
