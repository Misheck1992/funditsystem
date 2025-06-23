/**
 * Finance Real - File Library Module
 * Preview.js - Handles file preview functionality
 */

// Wait for FileLibrary to be fully defined before extending it
$(document).ready(function() {
	// Make sure FileLibrary exists
	if (typeof FileLibrary === 'undefined') {
		console.error('Error: FileLibrary is not defined. Make sure main.js is loaded before Preview.js');
		return;
	}

	// Extend the FileLibrary namespace
	(function(FileLibrary) {
		/**
		 * File Preview
		 * Handles file preview functionality
		 */
		const Preview = {
			/**
			 * Initialize the preview functionality
			 */
			init: function() {
				// Initialize preview event listeners
				this.initEvents();

				console.log('File Preview initialized');
			},

			/**
			 * Initialize preview events
			 */
			initEvents: function() {
				// File preview button click
				$(document).on('click', '.preview-file', (e) => {
					e.preventDefault();
					e.stopPropagation();

					const fileId = $(e.currentTarget).closest('.file-item').data('file-id');
					this.showPreview(fileId);
				});

				// Preview modal keyboard navigation
				$('#file-preview-modal').on('keydown', (e) => {
					// Left arrow
					if (e.keyCode === 37) {
						this.navigatePrevious();
					}
					// Right arrow
					else if (e.keyCode === 39) {
						this.navigateNext();
					}
				});

				// Preview navigation buttons
				$('#preview-prev-btn').on('click', () => {
					this.navigatePrevious();
				});

				$('#preview-next-btn').on('click', () => {
					this.navigateNext();
				});

				// Download button in preview
				$(document).on('click', '.download-preview-file', (e) => {
					e.preventDefault();

					const fileId = $(e.currentTarget).data('file-id');
					this.downloadFile(fileId);
				});
			},

			/**
			 * Show file preview
			 * @param {number} fileId - The file ID to preview
			 */
			showPreview: function(fileId) {
				// Get file data
				const fileItem = $(`.file-item[data-file-id="${fileId}"]`);
				if (fileItem.length === 0) return;

				const fileName = fileItem.data('file-name');
				const fileType = fileItem.data('file-type');

				// Reset preview container
				$('#preview-container').empty();
				$('#preview-file-name').text(fileName);

				// Show loading indicator
				$('#preview-container').html('<div class="preview-loading"><i class="fas fa-spinner fa-spin"></i> Loading preview...</div>');

				// Show modal
				$('#file-preview-modal').modal('show');

				// Store current file ID for navigation
				$('#file-preview-modal').data('current-file-id', fileId);

				// Update navigation buttons
				this.updatePreviewNavigation(fileId);

				// Load preview based on file type
				this.loadPreviewContent(fileId, fileType);
			},

			/**
			 * Load preview content based on file type
			 * @param {number} fileId - The file ID
			 * @param {string} fileType - The file MIME type
			 */
			loadPreviewContent: function(fileId, fileType) {
				// Check if file type is previewable
				const config = FileLibrary.getConfig();
				const isPreviewable = config.previewableTypes.includes(fileType);

				if (!isPreviewable) {
					this.showNoPreviewAvailable(fileId, fileType);
					return;
				}

				// Handle different file types
				if (fileType.startsWith('image/')) {
					this.loadImagePreview(fileId);
				} else if (fileType === 'application/pdf') {
					this.loadPdfPreview(fileId);
				} else if (fileType === 'text/plain' || fileType === 'text/csv') {
					this.loadTextPreview(fileId);
				} else {
					this.showNoPreviewAvailable(fileId, fileType);
				}
			},

			/**
			 * Load image preview
			 * @param {number} fileId - The file ID
			 */
			loadImagePreview: function(fileId) {
				const img = new Image();

				img.onload = function() {
					$('#preview-container').empty().append(img);
					$(img).addClass('img-fluid preview-image');
				};

				img.onerror = function() {
					$('#preview-container').html('<div class="preview-error"><i class="fas fa-exclamation-circle"></i> Failed to load image</div>');
				};

				img.src = FileLibrary.getConfig().baseUrl+'Files_controller/preview/' + fileId;
			},

			/**
			 * Load PDF preview
			 * @param {number} fileId - The file ID
			 */
			loadPdfPreview: function(fileId) {
				$('#preview-container').html(`
                    <div class="pdf-container">
                        <iframe src="${FileLibrary.getConfig().baseUrl}Files_controller/preview/${fileId}" frameborder="0"></iframe>
                    </div>
                `);
			},

			/**
			 * Load text preview
			 * @param {number} fileId - The file ID
			 */
			loadTextPreview: function(fileId) {
				$.ajax({
					url: FileLibrary.getConfig().baseUrl+'Files_controller/preview/' + fileId,
					type: 'GET',
					success: function(response) {
						if (response.success) {
							$('#preview-container').html(`
                                <pre class="text-preview">${FileLibrary.escapeHtml(response.content)}</pre>
                            `);
						} else {
							$('#preview-container').html('<div class="preview-error"><i class="fas fa-exclamation-circle"></i> ' +
								(response.message || 'Failed to load preview') + '</div>');
						}
					},
					error: function() {
						$('#preview-container').html('<div class="preview-error"><i class="fas fa-exclamation-circle"></i> Failed to load preview</div>');
					}
				});
			},

			/**
			 * Show no preview available message
			 * @param {number} fileId - The file ID
			 * @param {string} fileType - The file MIME type
			 */
			showNoPreviewAvailable: function(fileId, fileType) {
				$('#preview-container').html(`
                    <div class="no-preview">
                        <div class="file-icon large">
                            <i class="${FileLibrary.getFileTypeIcon(fileType)}"></i>
                        </div>
                        <p>No preview available for this file type</p>
                        <button class="btn btn-primary download-preview-file" data-file-id="${fileId}">
                            <i class="fas fa-download"></i> Download File
                        </button>
                    </div>
                `);
			},

			/**
			 * Update preview navigation buttons
			 * @param {number} currentFileId - Current file ID
			 */
			updatePreviewNavigation: function(currentFileId) {
				// Get all visible file items
				const fileItems = $('.file-item:visible');

				// Find current file index
				let currentIndex = -1;
				fileItems.each(function(index) {
					if ($(this).data('file-id') === currentFileId) {
						currentIndex = index;
						return false; // Break loop
					}
				});

				// If file not found, disable navigation
				if (currentIndex === -1) {
					$('#preview-prev-btn, #preview-next-btn').prop('disabled', true);
					return;
				}

				// Update prev button
				$('#preview-prev-btn').prop('disabled', currentIndex === 0);

				// Update next button
				$('#preview-next-btn').prop('disabled', currentIndex === fileItems.length - 1);
			},

			/**
			 * Navigate to previous file in preview
			 */
			navigatePrevious: function() {
				const currentFileId = $('#file-preview-modal').data('current-file-id');
				if (!currentFileId) return;

				// Get all visible file items
				const fileItems = $('.file-item:visible');

				// Find current file index
				let currentIndex = -1;
				fileItems.each(function(index) {
					if ($(this).data('file-id') === currentFileId) {
						currentIndex = index;
						return false; // Break loop
					}
				});

				// If found and not first, go to previous
				if (currentIndex > 0) {
					const prevFileId = $(fileItems[currentIndex - 1]).data('file-id');
					const prevFileType = $(fileItems[currentIndex - 1]).data('file-type');

					// Update modal data
					$('#file-preview-modal').data('current-file-id', prevFileId);
					$('#preview-file-name').text($(fileItems[currentIndex - 1]).data('file-name'));

					// Update navigation buttons
					this.updatePreviewNavigation(prevFileId);

					// Load preview content
					this.loadPreviewContent(prevFileId, prevFileType);
				}
			},

			/**
			 * Navigate to next file in preview
			 */
			navigateNext: function() {
				const currentFileId = $('#file-preview-modal').data('current-file-id');
				if (!currentFileId) return;

				// Get all visible file items
				const fileItems = $('.file-item:visible');

				// Find current file index
				let currentIndex = -1;
				fileItems.each(function(index) {
					if ($(this).data('file-id') === currentFileId) {
						currentIndex = index;
						return false; // Break loop
					}
				});

				// If found and not last, go to next
				if (currentIndex !== -1 && currentIndex < fileItems.length - 1) {
					const nextFileId = $(fileItems[currentIndex + 1]).data('file-id');
					const nextFileType = $(fileItems[currentIndex + 1]).data('file-type');

					// Update modal data
					$('#file-preview-modal').data('current-file-id', nextFileId);
					$('#preview-file-name').text($(fileItems[currentIndex + 1]).data('file-name'));

					// Update navigation buttons
					this.updatePreviewNavigation(nextFileId);

					// Load preview content
					this.loadPreviewContent(nextFileId, nextFileType);
				}
			},

			/**
			 * Download a file
			 * @param {number} fileId - The file ID
			 */
			downloadFile: function(fileId) {
				window.location.href =  FileLibrary.getConfig().baseUrl+'Files_controller/download/' + fileId;
			}
		};

		// Add to FileLibrary namespace
		FileLibrary.Preview = Preview;
// Create a bridge function to connect FileLibrary.previewFile API to Preview.showPreview
FileLibrary.previewFile = function(fileId) {
    if (FileLibrary.Preview) {
        FileLibrary.Preview.showPreview(fileId);
    } else {
        console.error('Preview module not initialized');
    }
};
		// Initialize Preview module when FileLibrary is ready
		$(document).on('fileLibraryReady', function() {
			FileLibrary.Preview.init();
		});

	})(FileLibrary);
});
