/**
 * Finance Real - File Library Module
 * Uploader.js - Handles file uploads
 */

// Extend the FileLibrary namespace
(function(FileLibrary) {
	/**
	 * File Uploader
	 * Handles file uploads with drag and drop support
	 */
	const Uploader = {
		/**
		 * Initialize the uploader
		 */
		init: function() {
			// Initialize Dropzone for drag and drop uploads
			this.initDropzone();

			// Initialize upload events
			this.initEvents();

			console.log('File Uploader initialized');
		},

		/**
		 * Initialize dropzone
		 */
		initDropzone: function() {
			// Make file container a drop zone
			const fileContainer = document.getElementById('file-container');
			if (!fileContainer) return;

			// Prevent default behavior
			['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
				fileContainer.addEventListener(eventName, this.preventDefaults, false);
			});

			// Highlight drop zone when dragging over
			['dragenter', 'dragover'].forEach(eventName => {
				fileContainer.addEventListener(eventName, this.highlight, false);
			});

			// Remove highlight when dragging out or dropping
			['dragleave', 'drop'].forEach(eventName => {
				fileContainer.addEventListener(eventName, this.unhighlight, false);
			});

			// Handle file drop
			fileContainer.addEventListener('drop', this.handleFileDrop, false);
		},

		/**
		 * Initialize upload events
		 */
		// Here's the specific part of the code you need to fix:

// Find this section in your Uploader.js file and replace it with this code:

		/**
		 * Initialize upload events
		 */
		initEvents: function() {
			// Flag to prevent double-opening of file picker
			let isFilePickerOpen = false;

			// Upload button click handler
			$('#upload-file-btn').off('click').on('click', function(e) {
				e.preventDefault();

				// Only trigger if not already open
				if (!isFilePickerOpen) {
					isFilePickerOpen = true;
					$('#file-upload-input').click();

					// Reset flag after a short delay
					setTimeout(function() {
						isFilePickerOpen = false;
					}, 500);
				}
			});

			// File input change - improved handler
			$('#file-upload-input').off('change').on('change', function() {
				// Check if files are selected and create a local copy
				if (this.files && this.files.length > 0) {
					// Create a copy of the files before clearing
					const filesArray = Array.from(this.files);

					// Handle file selection with the copy
					Uploader.handleFileSelect(filesArray);

					// Only reset after a delay to prevent event confusion
					setTimeout(() => {
						this.value = '';
					}, 300);
				}
			});

			// Multiple upload button
			$('#multiple-upload-btn').on('click', function() {
				$('#multiple-file-upload-input').click();
			});

			// Multiple file input change
			$('#multiple-file-upload-input').on('change', function() {
				Uploader.handleFileSelect(this.files);
			});

			// Upload form submit
			$('#upload-form').on('submit', function(e) {
				e.preventDefault();
				Uploader.uploadSelectedFiles();
			});

			// Category change
			$('#file-category').on('change', function() {
				Uploader.updateCategoryFields($(this).val());
			});
		},

		/**
		 * Prevent default behavior for events
		 * @param {Event} e - The event
		 */
		preventDefaults: function(e) {
			e.preventDefault();
			e.stopPropagation();
		},

		/**
		 * Highlight drop zone
		 */
		highlight: function() {
			$('#file-container').addClass('drag-over');
			$('#drop-overlay').show();
		},

		/**
		 * Remove highlight from drop zone
		 */
		unhighlight: function() {
			$('#file-container').removeClass('drag-over');
			$('#drop-overlay').hide();
		},

		/**
		 * Handle file drop event
		 * @param {DragEvent} e - The drop event
		 */
		handleFileDrop: function(e) {
			const dt = e.dataTransfer;
			const files = dt.files;

			if (files && files.length > 0) {
				Uploader.handleFileSelect(files);
			}
		},

		/**
		 * Handle file selection from input or drop
		 * @param {FileList} files - List of selected files
		 */
		/**
		 * Handle file selection from input or drop
		 * @param {FileList} files - List of selected files
		 */
		handleFileSelect: function(files) {
			if (!files || files.length === 0) {
				return;
			}

			// Debug log - verify if we're getting the files more than once
			console.log('Files selected:', files.length);
			Array.from(files).forEach(file => {
				console.log(' - File:', file.name, file.size);
			});

			// Create a clean copy of files to avoid duplicate references
			const uniqueFiles = Array.from(files).filter((file, index, self) =>
				index === self.findIndex(f => f.name === file.name && f.size === file.size)
			);

			// Debug log - show if we found duplicates
			if (uniqueFiles.length < files.length) {
				console.log('Removed', files.length - uniqueFiles.length, 'duplicate files');
			}

			// Show upload modal with unique files
			this.showUploadModal(uniqueFiles);
		},

		/**
		 * Show upload modal with file list
		 * @param {FileList} files - List of selected files
		 */
		showUploadModal: function(files) {
			// Clear previous files
			$('#upload-file-list').empty();

			// Add each file to the list
			Array.from(files).forEach((file, index) => {
				const isValid = this.validateFile(file);
				const fileItem = `
                    <div class="upload-file-item ${isValid ? '' : 'invalid'}" data-index="${index}">
                        <div class="upload-file-icon">
                            <i class="${this.getFileTypeIcon(file.type)}"></i>
                        </div>
                        <div class="upload-file-details">
                            <div class="upload-file-name">${file.name}</div>
                            <div class="upload-file-size">${this.formatFileSize(file.size)}</div>
                            ${!isValid ? '<div class="upload-file-error">Invalid file type or size</div>' : ''}
                        </div>
                        <div class="upload-file-actions">
                            <button type="button" class="btn btn-sm btn-icon remove-file" data-index="${index}">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;

				$('#upload-file-list').append(fileItem);
			});

			// Store files
			this.selectedFiles = files;

			// Update button state based on valid files
			this.updateUploadButtonState();

			// Show modal
			$('#upload-modal').modal('show');

			// Add event listener for remove buttons
			$('.remove-file').on('click', function() {
				const index = $(this).data('index');
				$(`#upload-file-list .upload-file-item[data-index="${index}"]`).remove();

				// Update button state
				Uploader.updateUploadButtonState();
			});
		},

		/**
		 * Update upload button state based on valid files
		 */
		updateUploadButtonState: function() {
			const hasValidFiles = $('#upload-file-list .upload-file-item:not(.invalid)').length > 0;
			$('#upload-files-btn').prop('disabled', !hasValidFiles);
		},

		/**
		 * Upload selected files
		 */
		uploadSelectedFiles: function() {
			if (!this.selectedFiles || this.selectedFiles.length === 0) {
				return;
			}

			// Get form data
			const category = $('#file-category').val();
			const isPublic = $('#file-is-public').is(':checked');
			const ownerType = $('#file-owner-type').val();
			const ownerId = $('#file-owner-id').val();

			// Close upload modal
			$('#upload-modal').modal('hide');

			// Show progress modal
			this.showProgressModal(this.selectedFiles.length);

			// Upload each file
			Array.from(this.selectedFiles).forEach((file, index) => {
				// Skip invalid files
				if (!this.validateFile(file)) {
					this.updateProgressItem(index, 100, 'error', 'Invalid file type or size');
					return;
				}

				setTimeout(() => {
					this.uploadFile(file, index, {
						category: category,
						isPublic: isPublic,
						ownerType: ownerType,
						ownerId: ownerId
					});
				}, index * 200);
			});
		},

		// Replace the uploadFile function in your Uploader.js

		/**
		 * Upload a single file
		 * @param {File} file - The file to upload
		 * @param {number} index - The index of the file
		 * @param {Object} options - Upload options
		 */
		uploadFile: function(file, index, options) {
			// Add a flag to the file object to prevent duplicate uploads
			if (file._uploading) {
				console.log('File already uploading, skipping:', file.name);
				return;
			}

			// Mark file as uploading
			file._uploading = true;

			// Create FormData
			const formData = new FormData();
			formData.append('file', file);
			formData.append('folder_id', FileLibrary.getCurrentFolderId());
			formData.append('file_category', options.category || 'general_files');
			formData.append('is_public', options.isPublic ? 1 : 0);
			formData.append('owner_type', options.ownerType || 'system_user');
			formData.append('owner_id', options.ownerId || 0);

			// Update progress
			this.updateProgressItem(index, 0, 'pending', file.name);

			// AJAX upload - Add a variable to reference the current upload
			const currentUpload = $.ajax({
				url:  FileLibrary.getConfig().baseUrl+'Files_controller/upload',
				type: 'POST',
				data: formData,
				contentType: false,
				processData: false,
				xhr: function() {
					const xhr = new window.XMLHttpRequest();
					xhr.upload.addEventListener('progress', function(e) {
						if (e.lengthComputable) {
							const percent = Math.round((e.loaded / e.total) * 100);
							Uploader.updateProgressItem(index, percent);
						}
					});
					return xhr;
				},
				success: function(response) {
					if (response.success) {
						Uploader.updateProgressItem(index, 100, 'success', 'Uploaded successfully');

						// Add file to current view if in the same folder
						if (FileLibrary.getCurrentFolderId() == response.file.folder_id) {
							FileLibrary.addFileToView(response.file);
						}
					} else {
						Uploader.updateProgressItem(index, 100, 'error', response.message);
					}

					// Check if all uploads are done
					Uploader.checkAllUploadsComplete();
				},
				error: function(xhr, status, error) {
					Uploader.updateProgressItem(index, 100, 'error', 'Upload failed: ' + error);
					Uploader.checkAllUploadsComplete();
				}
			});

			// Store the AJAX request reference to prevent duplicate uploads
			file._uploadRequest = currentUpload;
		},

		/**
		 * Show upload progress modal
		 * @param {number} fileCount - Number of files being uploaded
		 */
		showProgressModal: function(fileCount) {
			// Clear previous progress items
			$('#upload-progress-container').empty();

			// Create progress items
			for (let i = 0; i < fileCount; i++) {
				const progressItem = `
                    <div class="upload-progress-item" data-index="${i}">
                        <div class="progress-info">
                            <span class="progress-filename">Preparing...</span>
                            <span class="progress-percentage">0%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: 0%" 
                                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="progress-status"></div>
                    </div>
                `;

				$('#upload-progress-container').append(progressItem);
			}

			// Disable close button
			$('#upload-progress-modal .modal-footer button').prop('disabled', true);

			// Show modal
			$('#upload-progress-modal').modal({
				backdrop: 'static',
				keyboard: false
			});
		},

		/**
		 * Update progress item
		 * @param {number} index - File index
		 * @param {number} percent - Progress percentage
		 * @param {string} status - Status (pending, success, error)
		 * @param {string} filename - File name
		 */
		updateProgressItem: function(index, percent, status, filename) {
			const item = $(`#upload-progress-container .upload-progress-item[data-index="${index}"]`);

			if (item.length === 0) return;

			// Update filename if provided
			if (filename) {
				item.find('.progress-filename').text(filename);
			}

			// Update progress bar
			item.find('.progress-bar').css('width', percent + '%');
			item.find('.progress-bar').attr('aria-valuenow', percent);
			item.find('.progress-percentage').text(percent + '%');

			// Update status
			if (status) {
				item.attr('data-status', status);

				switch (status) {
					case 'success':
						item.find('.progress-bar').addClass('bg-success');
						item.find('.progress-status').html('<i class="fas fa-check-circle text-success"></i>');
						break;
					case 'error':
						item.find('.progress-bar').addClass('bg-danger');
						item.find('.progress-status').html('<i class="fas fa-times-circle text-danger"></i>');

						// Add tooltip with error message
						if (filename && filename !== item.find('.progress-filename').text()) {
							item.find('.progress-status i').attr('data-toggle', 'tooltip');
							item.find('.progress-status i').attr('title', filename);
							item.find('.progress-status i').tooltip();
						}
						break;
				}
			}
		},

		/**
		 * Check if all uploads are complete
		 */
		checkAllUploadsComplete: function() {
			const items = $('#upload-progress-container .upload-progress-item');
			let allComplete = true;

			items.each(function() {
				const percent = parseInt($(this).find('.progress-bar').attr('aria-valuenow'));
				if (percent < 100) {
					allComplete = false;
					return false; // Break loop
				}
			});

			if (allComplete) {
				// Enable close button
				$('#upload-progress-modal .modal-footer button').prop('disabled', false);

				// Auto close if all successful
				let allSuccess = true;
				items.each(function() {
					if ($(this).attr('data-status') === 'error') {
						allSuccess = false;
						return false; // Break loop
					}
				});

				if (allSuccess) {
					setTimeout(function() {
						$('#upload-progress-modal').modal('hide');
					}, 2000);
				}

				// Refresh folder if needed
				FileLibrary.refreshCurrentFolder();
			}
		},

		/**
		 * Validate file before upload
		 * @param {File} file - The file to validate
		 * @returns {boolean} - Whether file is valid
		 */
		validateFile: function(file) {
			const config = FileLibrary.getConfig();

			// Check file type
			const fileType = file.type;
			if (!fileType || config.allowedFileTypes.indexOf(fileType) === -1) {
				return false;
			}

			// Check file size
			if (file.size > config.maxFileSize) {
				return false;
			}

			return true;
		},

		/**
		 * Get file type icon
		 * @param {string} fileType - MIME type
		 * @returns {string} - Icon class
		 */
		getFileTypeIcon: function(fileType) {
			return FileLibrary.getFileTypeIcon(fileType);
		},

		/**
		 * Format file size
		 * @param {number} bytes - Size in bytes
		 * @returns {string} - Formatted size
		 */
		formatFileSize: function(bytes) {
			return FileLibrary.formatFileSize(bytes);
		},

		/**
		 * Update category fields
		 * @param {string} category - Selected category
		 */
		updateCategoryFields: function(category) {
			// Show/hide category specific fields
			$('.category-fields').hide();
			$(`.category-fields[data-category="${category}"]`).show();
		}
	};

	// Add to FileLibrary namespace
	FileLibrary.Uploader = Uploader;

})(FileLibrary || {});
