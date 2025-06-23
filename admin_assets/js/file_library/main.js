/**
 * Finance Real - File Library Module
 * Main JavaScript file for file library functionality
 */

// Define FileLibrary in the global scope to make it accessible to other scripts
var FileLibrary = (function($) {
	// Private variables
	let currentFolderId = 0; // Root folder
	let selectedFiles = [];
	let clipboardFiles = [];
	let clipboardAction = null; // 'copy' or 'cut'
	let viewMode = 'grid'; // 'grid' or 'list'
	let sortField = 'date_added';
	let sortDirection = 'desc';
	let currentPage = 1;
	let isLoadingMore = false;
	let hasMoreFiles = true;
	let _folderTreeLoading = false;

	// Configuration
	const config = {
		baseUrl: apiURL,
		allowedFileTypes: ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'text/plain', 'text/csv'],
		maxFileSize: 5242880, // 5MB
		pageSize: 30, // Number of files to load per page
		previewableTypes: ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'text/plain', 'text/csv'],
		animations: true // Toggle animations
	};

	/**
	 * Create file element
	 * @param {Object} file - File data
	 * @returns {jQuery} - File element
	 */
	function createFileElement(file) {
		const isSelected = selectedFiles.includes(file.id);
		const fileTypeIcon = getFileTypeIcon(file.file_type);
		const canPreview = config.previewableTypes.includes(file.file_type);

		if (viewMode === 'grid') {
			const fileItem = $(`
            <div class="file-item ${isSelected ? 'selected' : ''}" 
                data-file-id="${file.id}" 
                data-file-name="${file.file_name}"
                data-file-type="${file.file_type}">
                <div class="file-select">
                    <input type="checkbox" ${isSelected ? 'checked' : ''}>
                </div>
                <div class="file-preview">
                    ${canPreview && file.file_type.startsWith('image/')
				? `<img src="${config.baseUrl}Files_controller/preview/${file.id}?size=thumbnail" alt="${escapeHtml(file.file_name)}" class="file-thumbnail">`
				: `<div class="file-icon"><i class="${fileTypeIcon}"></i></div>`}
                </div>
                <div class="file-details">
                    <div class="file-name" title="${escapeHtml(file.file_name)}">${escapeHtml(file.file_name)}</div>
                    <div class="file-meta">${formatFileSize(file.file_size)} • ${formatDate(file.date_modified)}</div>
                </div>
                <div class="file-actions">
                    ${canPreview ? `<button class="btn btn-sm btn-icon preview-file" data-toggle="tooltip" title="Preview">
                        <i class="fas fa-eye"></i>
                    </button>` : ''}
                    <button class="btn btn-sm btn-icon download-file" data-toggle="tooltip" title="Download">
                        <i class="fas fa-download"></i>
                    </button>
                    <button class="btn btn-sm btn-icon share-file" data-toggle="tooltip" title="Share">
                        <i class="fas fa-share-alt"></i>
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-icon" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item rename-file" href="#">
                                <i class="fas fa-edit"></i> Rename
                            </a>
                            <a class="dropdown-item delete-file" href="#">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                            ${file.is_public ?
				`<a class="dropdown-item make-private" href="#">
                                    <i class="fas fa-lock"></i> Make Private
                                </a>` :
				`<a class="dropdown-item make-public" href="#">
                                    <i class="fas fa-globe"></i> Make Public
                                </a>`}
                        </div>
                    </div>
                </div>
            </div>
        `);

			return fileItem;
		} else {
			const fileItem = $(`
            <div class="file-item ${isSelected ? 'selected' : ''}" 
                data-file-id="${file.id}" 
                data-file-name="${file.file_name}"
                data-file-type="${file.file_type}">
                <div class="file-select">
                    <input type="checkbox" ${isSelected ? 'checked' : ''}>
                </div>
                <div class="file-icon">
                    <i class="${fileTypeIcon}"></i>
                </div>
                <div class="file-details">
                    <div class="file-name">${escapeHtml(file.file_name)}</div>
                    <div class="file-category">${file.file_category}</div>
                </div>
                <div class="file-meta">
                    <span class="file-size">${formatFileSize(file.file_size)}</span>
                    <span class="file-owner">${file.owner_name || 'NA'}</span>
                    <span class="date-modified">${formatDate(file.date_modified)}</span>
                </div>
                <div class="file-actions">
                    ${canPreview ? `<button class="btn btn-sm btn-icon preview-file" data-toggle="tooltip" title="Preview">
                        <i class="fas fa-eye"></i>
                    </button>` : ''}
                    <button class="btn btn-sm btn-icon download-file" data-toggle="tooltip" title="Download">
                        <i class="fas fa-download"></i>
                    </button>
                    <button class="btn btn-sm btn-icon share-file" data-toggle="tooltip" title="Share">
                        <i class="fas fa-share-alt"></i>
                    </button>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-icon" data-toggle="dropdown">
                            <i class="fas fa-ellipsis-h"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item rename-file" href="#">
                                <i class="fas fa-edit"></i> Rename
                            </a>
                            <a class="dropdown-item delete-file" href="#">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                            ${file.is_public ?
				`<a class="dropdown-item make-private" href="#">
                                    <i class="fas fa-lock"></i> Make Private
                                </a>` :
				`<a class="dropdown-item make-public" href="#">
                                    <i class="fas fa-globe"></i> Make Public
                                </a>`}
                        </div>
                    </div>
                </div>
            </div>
        `);

			return fileItem;
		}
	}

	/**
	 * Get file type icon class based on MIME type
	 * @param {string} fileType - MIME type of the file
	 * @returns {string} - FontAwesome icon class
	 */
	function getFileTypeIcon(fileType) {
		if (!fileType) return 'fas fa-file';

		if (fileType.startsWith('image/')) {
			return 'fa fa-file-image';
		} else if (fileType === 'application/pdf') {
			return 'fa fa-file-pdf';
		} else if (fileType === 'application/msword' ||
			fileType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
			return 'fas fa-file-word';
		} else if (fileType === 'application/vnd.ms-excel' ||
			fileType === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
			return 'fas fa-file-excel';
		} else if (fileType === 'text/plain') {
			return 'fas fa-file-alt';
		} else if (fileType === 'text/csv') {
			return 'fas fa-file-csv';
		} else if (fileType.startsWith('video/')) {
			return 'fas fa-file-video';
		} else if (fileType.startsWith('audio/')) {
			return 'fas fa-file-audio';
		} else if (fileType === 'application/zip' ||
			fileType === 'application/x-rar-compressed') {
			return 'fas fa-file-archive';
		} else if (fileType === 'text/html' ||
			fileType === 'application/xhtml+xml') {
			return 'fas fa-file-code';
		}

		return 'fas fa-file';
	}

	/**
	 * Update the breadcrumb navigation
	 * @param {Array} breadcrumb - Breadcrumb data array
	 */
	function updateBreadcrumb(breadcrumb) {
		const breadcrumbContainer = $('#folder-breadcrumb');
		breadcrumbContainer.empty();

		// Add root folder
		breadcrumbContainer.append(`
            <li class="breadcrumb-item ${breadcrumb.length === 0 ? 'active' : ''}" 
                ${breadcrumb.length !== 0 ? 'data-folder-id="0"' : ''}>
                <i class="fas fa-home"></i> Home
            </li>
        `);

		// Add breadcrumb items
		breadcrumb.forEach((item, index) => {
			if (index === breadcrumb.length - 1) {
				// Last item (current folder)
				breadcrumbContainer.append(`
                    <li class="breadcrumb-item active">
                        ${escapeHtml(item.folder_name)}
                    </li>
                `);
			} else {
				breadcrumbContainer.append(`
                    <li class="breadcrumb-item" data-folder-id="${item.id}">
                        ${escapeHtml(item.folder_name)}
                    </li>
                `);
			}
		});
	}

	/**
	 * Update folder information display
	 * @param {Object} folder - Folder data
	 */
	function updateFolderInfo(folder) {
		// Update folder name in header
		$('#current-folder-name').text(folder ? folder.folder_name : 'Home');

		// Update folder metadata
		const metaContainer = $('#folder-meta');

		if (folder) {
			metaContainer.html(`
                <span><i class="far fa-clock"></i> Modified: ${formatDate(folder.date_modified)}</span>
                <span><i class="far fa-user"></i> Owner: ${folder.owner_name || 'System'}</span>
                ${folder.description ? `<span><i class="far fa-sticky-note"></i> ${escapeHtml(folder.description)}</span>` : ''}
            `);
		} else {
			metaContainer.html(`<span><i class="fas fa-home"></i> Root directory</span>`);
		}
	}

	/**
	 * Navigate to a specific folder
	 * @param {number} folderId - ID of the folder to navigate to
	 */
	function navigateToFolder(folderId) {
		// Clear selection
		//deselectAllFiles();
		updateClipboardButtons();
		// Add animation class before loading
		if (config.animations) {
			$('#file-container').addClass('folder-transition-out');

			setTimeout(() => {
				loadFolderContent(folderId);
			}, 200);
		} else {
			loadFolderContent(folderId);
		}
	}

	/**
	 * Show create folder modal
	 */
	function showCreateFolderModal() {
		// Reset form
		$('#create-folder-form')[0].reset();

		// Set current folder as parent
		$('#parent-folder-id').val(currentFolderId);

		// Set folder parent name
		const parentName = $('#current-folder-name').text();
		$('#parent-folder-name').text(parentName || 'Home');

		// Show modal
		$('#create-folder-modal').modal('show');

		// Focus on folder name input
		setTimeout(() => {
			$('#folder-name-input').focus();
		}, 500);
	}

	/**
	 * Create a new folder
	 */
	function createFolder() {
		const folderName = $('#folder-name-input').val().trim();
		const parentFolderId = $('#parent-folder-id').val();
		const description = $('#folder-description-input').val().trim();
		const isPublic = $('#folder-is-public').is(':checked') ? 1 : 0;

		// Validate folder name
		if (!folderName) {
			showFormError('folder-name-input', 'Please enter a folder name');
			return;
		}

		// Show loading state
		const submitBtn = $('#create-folder-submit');
		const originalBtnText = submitBtn.html();
		submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Creating...');
		submitBtn.prop('disabled', true);

		// AJAX request
		$.ajax({
			url: config.baseUrl + 'Folders_controller/create_folder',
			type: 'POST',
			data: {
				folder_name: folderName,
				parent_folder_id: parentFolderId,
				description: description,
				is_public: isPublic
			},
			success: function(response) {
				// Reset button
				submitBtn.html(originalBtnText);
				submitBtn.prop('disabled', false);

				if (response.success) {
					// Close modal
					$('#create-folder-modal').modal('hide');

					// Reload current folder
					loadFolderContent(currentFolderId);

					// Show notification
					showNotification('success', 'Folder created successfully: ' + response.folder.folder_name);

					// Refresh folder tree
					loadFolderTree();
				} else {
					showFormError('folder-name-input', response.message || 'Failed to create folder');
				}
			},
			error: function(xhr, status, error) {
				// Reset button
				submitBtn.html(originalBtnText);
				submitBtn.prop('disabled', false);

				showFormError('folder-name-input', 'Server error: ' + error);
			}
		});
	}

	/**
	 * Initialize the File Library
	 */
	function init() {
		// Initialize event listeners
		initEventListeners();

		// Initialize drag and drop
		//initDragAndDrop();

		// Load last visited folder or root folder
		const lastFolder = localStorage.getItem('fileLibrary_lastFolder');
		loadFolderContent(lastFolder ? parseInt(lastFolder) : 0);

		// Initialize folder tree
		const restored = restoreFolderTree();
		if (!restored) {
			loadFolderTree();
		}

		// Initialize tooltips and popovers
		$('[data-toggle="tooltip"]').tooltip();
		$('[data-toggle="popover"]').popover();

		// Set initial view mode from local storage if available
		const savedViewMode = localStorage.getItem('fileLibrary_viewMode');
		if (savedViewMode) {
			setViewMode(savedViewMode);
		}

		console.log('File Library initialized');
	}

	/**
	 * Initialize event listeners for various elements
	 */
	function initEventListeners() {
		// View mode toggle
		$('#view-mode-grid').on('click', function() {
			setViewMode('grid');
		});

		$('#view-mode-list').on('click', function() {
			setViewMode('list');
		});

		// File upload button
		$('#upload-file-btn').on('click', function() {
			$('#file-upload-input').click();
		});

		// File upload input change

// File upload input change
		$('#file-upload-input').on('change', function() {
			// Check if Uploader module is available
			if (FileLibrary.Uploader && FileLibrary.Uploader.handleFileSelect) {
				FileLibrary.Uploader.handleFileSelect(this.files);
			} else {
				console.error('Uploader module not available or handleFileSelect method not found');
			}
		});
		$('#upload-files-btn').on('click', function() {
			// Check if Uploader module is available
			if (FileLibrary.Uploader && FileLibrary.Uploader.uploadSelectedFiles) {
				FileLibrary.Uploader.uploadSelectedFiles(this.files);
			} else {
				console.error('Uploader module not available or uploadSelectedFiles method not found');
			}
		});
		// Create new folder button
		$('#create-folder-btn').on('click', function() {
			showCreateFolderModal();
		});
		$('#create-folder-submit').on('click', function() {
			createFolder();
		});


		// Folder navigation
		$(document).on('click', '.folder-item', function(e) {
			// Don't navigate if clicking on action buttons
			if ($(e.target).closest('.folder-actions').length) {
				return;
			}

			const folderId = $(this).data('folder-id');
			navigateToFolder(folderId);
		});

		// Breadcrumb navigation
		$(document).on('click', '.breadcrumb-item', function() {
			const folderId = $(this).data('folder-id');
			if (folderId !== undefined) {
				navigateToFolder(folderId);
			}
		});

		// File selection
		$(document).on('click', '.file-item', function(e) {
			// Don't select if clicking on action buttons
			if ($(e.target).closest('.file-actions').length) {
				return;
			}

			const fileId = $(this).data('file-id');

			// Handle multi-select with CTRL key
			if (e.ctrlKey || e.metaKey) {
				toggleFileSelection(fileId);
			} else {
				// Single selection
				selectFile(fileId);
			}
		});

		// File checkbox click
		$(document).on('click', '.file-select input[type="checkbox"]', function(e) {
			e.stopPropagation();
			const fileId = $(this).closest('.file-item').data('file-id');
			toggleFileSelection(fileId);
		});

		// File preview
		$(document).on('click', '.preview-file', function(e) {
			e.preventDefault();
			e.stopPropagation();
			const fileId = $(this).closest('.file-item').data('file-id');
			previewFile(fileId);
		});

		// File download
		$(document).on('click', '.download-file', function(e) {
			e.preventDefault();
			e.stopPropagation();
			const fileId = $(this).closest('.file-item').data('file-id');
			downloadFile(fileId);
		});

		// File delete
		$(document).on('click', '.delete-file', function(e) {
			e.preventDefault();
			e.stopPropagation();
			const fileId = $(this).closest('.file-item').data('file-id');
			confirmDeleteFile(fileId);
		});

		// File sharing
		$(document).on('click', '.share-file', function(e) {
			e.preventDefault();
			e.stopPropagation();
			const fileId = $(this).closest('.file-item').data('file-id');
			showShareFileModal(fileId);
		});

		// File rename
		$(document).on('click', '.rename-file', function(e) {
			e.preventDefault();
			e.stopPropagation();
			const fileId = $(this).closest('.file-item').data('file-id');
			showRenameFileModal(fileId);
		});

		// Folder rename
		$(document).on('click', '.rename-folder', function(e) {
			e.preventDefault();
			e.stopPropagation();
			const folderId = $(this).closest('.folder-item').data('folder-id');
			showRenameFolderModal(folderId);
		});

		// Folder delete
		$(document).on('click', '.delete-folder', function(e) {
			e.preventDefault();
			e.stopPropagation();
			const folderId = $(this).closest('.folder-item').data('folder-id');
			confirmDeleteFolder(folderId);
		});

		// Search input
		$('#file-search-input').on('keyup', debounce(function() {
			const searchTerm = $(this).val();
			if (searchTerm.length > 2 || searchTerm.length === 0) {
				searchFiles(searchTerm);
			}
		}, 500));

		// Advanced search toggle
		$('#toggle-advanced-search').on('click', function() {
			$('#advanced-search-form').toggleClass('d-none');
		});

		// Sort options
		$('.sort-option').on('click', function() {
			const field = $(this).data('sort-field');
			setSortOrder(field);
		});

		// Clipboard actions (Copy, Cut, Paste)
		$('#copy-selected-btn').on('click', function() {
			copySelectedFiles();
		});

		$('#cut-selected-btn').on('click', function() {
			cutSelectedFiles();
		});

		$('#paste-files-btn').on('click', function() {
			pasteFiles();
		});

		// Select all files
		$('#select-all-files').on('click', function() {
			selectAllFiles();
		});

		// Deselect all files
		$('#deselect-all-files').on('click', function() {
			deselectAllFiles();
		});

		// Refresh folder
		$('#refresh-folder-btn').on('click', function() {
			refreshCurrentFolder();
		});

		// Toggle folder tree items
		$(document).on('click', '.folder-toggle', function() {
			$(this).closest('.folder-tree-item').toggleClass('expanded');
			$(this).find('i').toggleClass('fa-caret-right fa-caret-down');
			$(this).siblings('.folder-tree-children').slideToggle();
		});

		// Folder tree navigation
		$(document).on('click', '.folder-tree-item-link', function(e) {
			e.preventDefault();
			const folderId = $(this).data('folder-id');
			navigateToFolder(folderId);

			// Update active state
			$('.folder-tree-item-link').removeClass('active');
			$(this).addClass('active');
		});

		// Toggle sidebar on mobile
		$('#toggle-sidebar-btn').on('click', function() {
			$('.file-library-sidebar').toggleClass('d-none d-md-flex');
		});

		// Infinite scroll
		$('#file-container').on('scroll', debounce(function() {
			const container = $(this);
			if (container.scrollTop() + container.innerHeight() >= container[0].scrollHeight - 200) {
				if (!isLoadingMore && hasMoreFiles) {
					loadMoreFiles();
				}
			}
		}, 200));

		// Create folder form submit
		$('#create-folder-form').on('submit', function(e) {
			e.preventDefault();
			createFolder();
		});

		// Context menu
		$(document).on('contextmenu', '.file-item, .folder-item, #file-container', function(e) {
			e.preventDefault();
			showContextMenu(e, this);
		});

		// Hide context menu on click outside
		$(document).on('click', function() {
			$('.context-menu').hide();
		});

		// Make file public/private
		$(document).on('click', '.make-public, .make-private', function(e) {
			e.preventDefault();
			e.stopPropagation();
			const fileId = $(this).closest('.file-item').data('file-id');
			toggleFilePublicStatus(fileId);
		});
	}

	/**
	 * Initialize drag and drop functionality
	 */
	function initDragAndDrop() {
		// Make file container a drop zone
		const fileContainer = document.getElementById('file-container');

		// Prevent default behavior
		['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
			fileContainer.addEventListener(eventName, preventDefaults, false);
		});

		// Highlight drop zone when dragging over
		['dragenter', 'dragover'].forEach(eventName => {
			fileContainer.addEventListener(eventName, highlight, false);
		});

		// Remove highlight when dragging out or dropping
		['dragleave', 'drop'].forEach(eventName => {
			fileContainer.addEventListener(eventName, unhighlight, false);
		});

		// Handle file drop
		fileContainer.addEventListener('drop', handleDrop, false);

		// Make file items draggable
		$(document).on('mousedown', '.file-item', function(e) {
			// Only start drag if not clicking on action buttons
			if ($(e.target).closest('.file-actions').length === 0) {
				$(this).attr('draggable', 'true');
			}
		});

		// Handle drag start
		$(document).on('dragstart', '.file-item', function(e) {
			const fileId = $(this).data('file-id');

			// If file is not in selection, select only this file
			if (!isFileSelected(fileId)) {
				selectFile(fileId);
			}

			// Set data for drag
			e.originalEvent.dataTransfer.setData('text/plain', JSON.stringify({
				type: 'file',
				fileIds: selectedFiles
			}));

			// Custom drag image for multiple files
			if (selectedFiles.length > 1) {
				const dragImage = createDragImage(selectedFiles.length);
				e.originalEvent.dataTransfer.setDragImage(dragImage, 15, 15);
			}
		});

		// Handle drag over folder
		$(document).on('dragover', '.folder-item', function(e) {
			e.preventDefault();
			$(this).addClass('folder-drag-over');
		});

		$(document).on('dragleave', '.folder-item', function() {
			$(this).removeClass('folder-drag-over');
		});

		// Handle drop on folder
		$(document).on('drop', '.folder-item', function(e) {
			e.preventDefault();
			$(this).removeClass('folder-drag-over');

			const targetFolderId = $(this).data('folder-id');

			try {
				const dragData = JSON.parse(e.originalEvent.dataTransfer.getData('text/plain'));

				if (dragData.type === 'file') {
					moveFilesToFolder(dragData.fileIds, targetFolderId);
				}
			} catch (err) {
				console.error('Error parsing drag data:', err);
			}
		});
	}

	/**
	 * Show a notification message
	 * @param {string} type - Notification type ('success', 'error', 'info', 'warning')
	 * @param {string} message - Notification message
	 * @param {number} [duration=3000] - How long to show the notification in ms
	 */
	function showNotification(type, message, duration = 3000) {
		// Create notification element if it doesn't exist
		if ($('#notification-container').length === 0) {
			$('body').append('<div id="notification-container"></div>');
		}

		// Create notification
		const notification = $(`
            <div class="notification notification-${type}">
                <div class="notification-icon">
                    ${type === 'success' ? '<i class="fas fa-check-circle"></i>' : ''}
                    ${type === 'error' ? '<i class="fas fa-times-circle"></i>' : ''}
                    ${type === 'info' ? '<i class="fas fa-info-circle"></i>' : ''}
                    ${type === 'warning' ? '<i class="fas fa-exclamation-triangle"></i>' : ''}
                </div>
                <div class="notification-content">${message}</div>
                <button class="notification-close"><i class="fas fa-times"></i></button>
            </div>
        `);

		// Add to container
		$('#notification-container').append(notification);

		// Add animation
		setTimeout(() => {
			notification.addClass('show');
		}, 10);

		// Close button event
		notification.find('.notification-close').on('click', function() {
			closeNotification(notification);
		});

		// Auto close after duration
		if (duration) {
			setTimeout(() => {
				closeNotification(notification);
			}, duration);
		}
	}

	/**
	 * Close a notification
	 * @param {jQuery} notification - The notification element
	 */
	function closeNotification(notification) {
		notification.removeClass('show');
		setTimeout(() => {
			notification.remove();
		}, 300);
	}

	/**
	 * Format file size to human-readable format
	 * @param {number} bytes - File size in bytes
	 * @returns {string} - Formatted file size
	 */
	function formatFileSize(bytes) {
		if (bytes === 0) return '0 Bytes';

		const k = 1024;
		const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
		const i = Math.floor(Math.log(bytes) / Math.log(k));

		return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
	}

	/**
	 * Format date to human-readable format
	 * @param {string} dateString - Date string
	 * @returns {string} - Formatted date
	 */
	function formatDate(dateString) {
		if (!dateString) return 'N/A';

		const date = new Date(dateString);
		const now = new Date();
		const diff = now - date;

		// Check if date is valid
		if (isNaN(date.getTime())) return 'N/A';

		// Today
		if (diff < 86400000 && date.getDate() === now.getDate()) {
			return 'Today ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
		}

		// Yesterday
		const yesterday = new Date(now);
		yesterday.setDate(now.getDate() - 1);
		if (date.getDate() === yesterday.getDate() &&
			date.getMonth() === yesterday.getMonth() &&
			date.getFullYear() === yesterday.getFullYear()) {
			return 'Yesterday ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
		}

		// This week (within last 7 days)
		if (diff < 604800000) {
			const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
			return days[date.getDay()] + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
		}

		// Default format
		return date.toLocaleDateString([], { month: 'short', day: 'numeric', year: 'numeric' });
	}

	/**
	 * Escape HTML to prevent XSS
	 * @param {string} text - Text to escape
	 * @returns {string} - Escaped text
	 */
	/**
	 * Escape HTML to prevent XSS
	 * @param {string} text - Text to escape
	 * @returns {string} - Escaped text
	 */
	function escapeHtml(text) {
		if (!text) return '';

		const map = {
			'&': '&amp;',
			'<': '&lt;',
			'>': '&gt;',
			'"': '&quot;',
			"'": '&#039;'
		};

		return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
	}

	/**
	 * Debounce function to limit how often a function can be called
	 * @param {Function} func - Function to debounce
	 * @param {number} wait - Time to wait in ms
	 * @returns {Function} - Debounced function
	 */
	function debounce(func, wait) {
		let timeout;
		return function() {
			const context = this;
			const args = arguments;
			clearTimeout(timeout);
			timeout = setTimeout(() => {
				func.apply(context, args);
			}, wait);
		};
	}

	/**
	 * Select a file
	 * @param {number} fileId - ID of the file to select
	 */
	function selectFile(fileId) {
		// Deselect any currently selected files
		deselectAllFiles();

		// Add fileId to selection
		selectedFiles.push(fileId);

		// Update UI
		$(`.file-item[data-file-id="${fileId}"]`).addClass('selected')
			.find('input[type="checkbox"]').prop('checked', true);

		// Update selection UI
		updateSelectionUI();
	}

	/**
	 * Toggle file selection
	 * @param {number} fileId - ID of the file to toggle
	 */
	function toggleFileSelection(fileId) {
		const index = selectedFiles.indexOf(fileId);

		if (index === -1) {
			// Add to selection
			selectedFiles.push(fileId);
			$(`.file-item[data-file-id="${fileId}"]`).addClass('selected')
				.find('input[type="checkbox"]').prop('checked', true);
		} else {
			// Remove from selection
			selectedFiles.splice(index, 1);
			$(`.file-item[data-file-id="${fileId}"]`).removeClass('selected')
				.find('input[type="checkbox"]').prop('checked', false);
		}

		// Update selection UI
		updateSelectionUI();
	}

	/**
	 * Check if a file is selected
	 * @param {number} fileId - ID of the file to check
	 * @returns {boolean} - Whether the file is selected
	 */
	function isFileSelected(fileId) {
		return selectedFiles.indexOf(fileId) !== -1;
	}
	function getCurrentFolderId() {
		return currentFolderId;
	}

	/**
	 * Select all files
	 */
	function selectAllFiles() {
		// Clear current selection
		selectedFiles = [];

		// Get all file IDs and add to selection
		$('.file-item').each(function() {
			const fileId = $(this).data('file-id');
			selectedFiles.push(fileId);
		});

		// Update UI
		$('.file-item').addClass('selected')
			.find('input[type="checkbox"]').prop('checked', true);

		// Update selection UI
		updateSelectionUI();
	}

	/**
	 * Deselect all files
	 */
	function deselectAllFiles() {
		// Clear selection
		selectedFiles = [];

		// Update UI
		$('.file-item').removeClass('selected')
			.find('input[type="checkbox"]').prop('checked', false);

		// Update selection UI
		updateSelectionUI();
	}

	/**
	 * Update selection UI based on selected files
	 */
	function updateSelectionUI() {
		if (selectedFiles.length > 0) {
			// Show selection actions
			$('#selection-actions').removeClass('d-none');
			$('#selected-count').text(selectedFiles.length);

			// Enable/disable buttons based on selection count
			$('.selection-action').prop('disabled', false);
		} else {
			// Hide selection actions
			$('#selection-actions').addClass('d-none');
		}
	}

	/**
	 * Update clipboard buttons based on clipboard content
	 */
	function updateClipboardButtons() {
		console.log("UPDATING CLIPBOARD BUTTONS:", {
			hasFiles: clipboardFiles.length > 0,
			numFiles: clipboardFiles.length,
			action: clipboardAction
		});

		if (clipboardFiles.length > 0 && clipboardAction) {
			// Enable paste button and update text
			$('#paste-files-btn').prop('disabled', false);

			if (clipboardAction === 'cut') {
				$('#paste-files-btn').html('<i class="fas fa-paste"></i> Move Here (' + clipboardFiles.length + ')');
			} else {
				$('#paste-files-btn').html('<i class="fas fa-paste"></i> Copy Here (' + clipboardFiles.length + ')');
			}
		} else {
			// Disable paste button
			$('#paste-files-btn').prop('disabled', true);
			$('#paste-files-btn').html('<i class="fas fa-paste"></i> Paste');
		}
	}

	/**
	 * Copy selected files to clipboard
	 */
	function copySelectedFiles() {
		if (selectedFiles.length === 0) {
			showNotification('info', 'No files selected');
			return;
		}

		// Set clipboard data
		clipboardFiles = [...selectedFiles];
		clipboardAction = 'copy';

		// Update UI
		updateClipboardButtons();

		// Show notification
		showNotification('success', `${selectedFiles.length} ${selectedFiles.length === 1 ? 'file' : 'files'} copied to clipboard`);
	}

	/**
	 * Cut selected files to clipboard
	 */
	function cutSelectedFiles() {
		if (selectedFiles.length === 0) {
			showNotification('info', 'No files selected');
			return;
		}

		// Set clipboard data
		clipboardFiles = [...selectedFiles];
		clipboardAction = 'cut';

		// Add visual indication for cut files
		selectedFiles.forEach(fileId => {
			$(`.file-item[data-file-id="${fileId}"]`).addClass('file-cut');
		});

		// Update UI
		updateClipboardButtons();

		// Show notification
		showNotification('success', `${selectedFiles.length} ${selectedFiles.length === 1 ? 'file' : 'files'} cut to clipboard`);
	}

	/**
	 * Paste files from clipboard to current folder
	 */
	function pasteFiles() {
		if (clipboardFiles.length === 0) {
			return;
		}

		// Show loading notification
		showNotification('info', 'Processing files...', false);

		// AJAX request
		$.ajax({
			url: config.baseUrl + (clipboardAction === 'cut' ? 'Files_controller/move_files' : 'Files_controller/copy_files'),
			type: 'POST',
			data: {
				file_ids: clipboardFiles,
				target_folder_id: currentFolderId
			},
			success: function(response) {
				if (response.success) {
					// Close notification
					$('.notification').remove();

					// Show success notification
					showNotification('success', response.message);

					// Clear cut visual indication if was a cut operation
					if (clipboardAction === 'cut') {
						clipboardFiles.forEach(fileId => {
							$(`.file-item[data-file-id="${fileId}"]`).removeClass('file-cut');
						});

						// Clear clipboard after cut (not after copy)
						clipboardFiles = [];
						clipboardAction = null;
					}

					// Reload current folder to show changes
					loadFolderContent(currentFolderId);
				} else {
					// Close notification
					$('.notification').remove();

					// Show error notification
					showNotification('error', response.message || 'Failed to paste files');
				}

				// Update clipboard buttons
				updateClipboardButtons();
			},
			error: function(xhr, status, error) {
				// Close notification
				$('.notification').remove();

				// Show error notification
				showNotification('error', 'Server error: ' + error);
			}
		});
	}

	/**
	 * Move files to a folder via drag and drop
	 * @param {Array} fileIds - Array of file IDs to move
	 * @param {number} targetFolderId - ID of the target folder
	 */
	function moveFilesToFolder(fileIds, targetFolderId) {
		// Don't do anything if target is same as current folder
		if (targetFolderId === currentFolderId) {
			return;
		}

		// Show loading notification
		showNotification('info', 'Moving files...', false);

		// AJAX request
		$.ajax({
			url: config.baseUrl + 'move_files',
			type: 'POST',
			data: {
				file_ids: fileIds,
				target_folder_id: targetFolderId
			},
			success: function(response) {
				// Close notification
				$('.notification').remove();

				if (response.success) {
					// Show success notification
					showNotification('success', response.message);

					// Remove moved files from view with animation
					fileIds.forEach(fileId => {
						$(`.file-item[data-file-id="${fileId}"]`).fadeOut(function() {
							$(this).remove();
						});
					});

					// Clear selection
					deselectAllFiles();
				} else {
					// Show error notification
					showNotification('error', response.message || 'Failed to move files');
				}
			},
			error: function(xhr, status, error) {
				// Close notification
				$('.notification').remove();

				// Show error notification
				showNotification('error', 'Server error: ' + error);
			}
		});
	}

	/**
	 * Set view mode (grid or list)
	 * @param {string} mode - View mode ('grid' or 'list')
	 */
	function setViewMode(mode) {
		if (mode !== 'grid' && mode !== 'list') {
			return;
		}

		viewMode = mode;

		// Update UI
		$('.view-mode-btn').removeClass('active');
		$(`#view-mode-${mode}`).addClass('active');

		// Update container class
		const container = $('#file-container').children();
		if (mode === 'grid') {
			container.removeClass('file-list').addClass('file-grid');
		} else {
			container.removeClass('file-grid').addClass('file-list');
		}

		// Save preference to local storage
		localStorage.setItem('fileLibrary_viewMode', mode);
	}

	/**
	 * Set sort order
	 * @param {string} field - Field to sort by
	 */
	function setSortOrder(field) {
		// Toggle direction if same field
		if (field === sortField) {
			sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
		} else {
			sortField = field;
			sortDirection = 'asc';
		}

		// Update UI
		$('.sort-option').removeClass('active asc desc');
		$(`.sort-option[data-sort-field="${field}"]`).addClass('active ' + sortDirection);

		// Reload folder content
		loadFolderContent(currentFolderId);
	}

	/**
	 * Search files
	 * @param {string} searchTerm - Search term
	 */
	function searchFiles(searchTerm) {
		// Show loading indicator
		$('#file-container').html('<div class="loading-indicator"><i class="fas fa-spinner fa-spin"></i> Searching...</div>');

		// AJAX request
		$.ajax({
			url: config.baseUrl + 'Files_controller/search',
			type: 'GET',
			data: {
				q: searchTerm,
				folder_id: currentFolderId,
				file_category: $('#search-category').val(),
				owner_type: $('#search-owner-type').val(),
				date_from: $('#search-date-from').val(),
				date_to: $('#search-date-to').val()
			},
			success: function(response) {
				if (response.success) {
					// Use the modified renderFolderContent function
					if (FileLibrary.Explorer && typeof FileLibrary.Explorer.renderFolderContent === 'function') {
						FileLibrary.Explorer.renderFolderContent(response.content, true, response.search_term);
					} else {
						console.error('Explorer module not available');
						// Fallback rendering if needed
					}
				} else {
					showNotification('error', response.message || 'Search failed');
					$('#file-container').html('<div class="error-message"><i class="fas fa-exclamation-circle"></i> ' +
						(response.message || 'Search failed') + '</div>');
				}
			},
			error: function(xhr, status, error) {
				showNotification('error', 'Server error: ' + error);
				$('#file-container').html('<div class="error-message"><i class="fas fa-exclamation-circle"></i> Server error</div>');
			}
		});
	}

	/**
	 * Render search results
	 * @param {Object} results - Search results
	 * @param {string} searchTerm - Search term
	 */
	function renderSearchResults(results, searchTerm) {
		// Clear container
		$('#file-container').empty();

		// Create container based on view mode
		const containerClass = viewMode === 'grid' ? 'file-grid' : 'file-list';
		const container = $(`<div class="${containerClass}"></div>`);

		// Add search info
		const searchInfo = $(`
            <div class="search-info">
                <div class="search-summary">
                    <i class="fas fa-search"></i> 
                    ${results.total} ${results.total === 1 ? 'result' : 'results'} found for 
                    <span class="search-term">"${escapeHtml(searchTerm)}"</span>
                </div>
                <button class="btn btn-sm btn-outline-secondary clear-search">
                    <i class="fas fa-times"></i> Clear Search
                </button>
            </div>
        `);

		$('#file-container').append(searchInfo);

		// Add event listener for clear search
		$('.clear-search').on('click', function() {
			$('#file-search-input').val('');
			loadFolderContent(currentFolderId);
		});

		// Add files
		if (results.files && results.files.length > 0) {
			results.files.forEach(file => {
				container.append(createFileElement(file));
			});
		}

		// Add empty state if no results
		if (results.total === 0) {
			container.append(`
                <div class="empty-search">
                    <i class="fas fa-search"></i>
                    <p>No results found for "${escapeHtml(searchTerm)}"</p>
                    <p class="empty-search-tips">Try different keywords or filters</p>
                </div>
            `);
		}

		// Add to main container
		$('#file-container').append(container);

		// Initialize tooltips
		$('[data-toggle="tooltip"]').tooltip();
	}

	/**
	 * Load folder tree
	 */
	function loadFolderTree() {
		$.ajax({
			url: config.baseUrl + 'Folders_controller/get_folder_tree',
			type: 'GET',
			success: function(response) {
				if (response.success) {
					renderFolderTree(response.folders);
				}
			}
		});
	}

	/**
	 * Render folder tree
	 * @param {Array} folders - Folders data
	 */
	function renderFolderTree(folders) {
		const treeContainer = $('#folder-tree');
		treeContainer.empty();

		// Add root folder
		treeContainer.append(`
            <div class="folder-tree-item ${currentFolderId === 0 ? 'active' : ''}">
                <a href="#" class="folder-tree-item-link ${currentFolderId === 0 ? 'active' : ''}" data-folder-id="0">
                    <i class="fas fa-home"></i> Home
                </a>
            </div>
        `);

		// Add folders
		if (folders && folders.length > 0) {
			renderFolderTreeItems(folders, treeContainer);
		}
	}

	/**
	 * Render folder tree items recursively
	 * @param {Array} folders - Folders data
	 * @param {jQuery} container - Container element
	 */
	function renderFolderTreeItems(folders, container) {
		folders.forEach(folder => {
			const hasChildren = folder.children && folder.children.length > 0;

			const folderItem = $(`
                <div class="folder-tree-item ${currentFolderId === folder.id ? 'active' : ''}">
                    ${hasChildren ?
				`<div class="folder-toggle">
                            <i class="fas fa-caret-right"></i>
                        </div>` :
				`<div class="folder-no-toggle"></div>`
			}
                    <a href="#" class="folder-tree-item-link ${currentFolderId === folder.id ? 'active' : ''}" data-folder-id="${folder.id}">
                        <i class="fas fa-folder"></i> ${escapeHtml(folder.folder_name)}
                    </a>
                </div>
            `);

			container.append(folderItem);

			// Add children if any
			if (hasChildren) {
				const childrenContainer = $('<div class="folder-tree-children"></div>');
				folderItem.append(childrenContainer);

				renderFolderTreeItems(folder.children, childrenContainer);
			}
		});
	}

	/**
	 * Show context menu
	 * @param {Event} e - Mouse event
	 * @param {HTMLElement} target - Target element
	 */
	function showContextMenu(e, target) {
		// Hide any existing context menu
		$('.context-menu').hide();

		// Determine menu type based on target
		let menuType = 'container';
		let fileId, folderId;

		if ($(target).hasClass('file-item')) {
			menuType = 'file';
			fileId = $(target).data('file-id');

			// If file is not in selection, select only this file
			if (!isFileSelected(fileId)) {
				selectFile(fileId);
			}
		} else if ($(target).hasClass('folder-item')) {
			menuType = 'folder';
			folderId = $(target).data('folder-id');
		}

		// Create menu based on type
		let menuHtml = '';

		if (menuType === 'file') {
			menuHtml = `
                <div class="context-menu-item preview-file">
                    <i class="fas fa-eye"></i> Preview
                </div>
                <div class="context-menu-item download-file">
                    <i class="fas fa-download"></i> Download
                </div>
                <div class="context-menu-item rename-file">
                    <i class="fas fa-edit"></i> Rename
                </div>
                <div class="context-menu-item share-file">
                    <i class="fas fa-share-alt"></i> Share
                </div>
                <div class="context-menu-divider"></div>
                <div class="context-menu-item copy-file">
                    <i class="fas fa-copy"></i> Copy
                </div>
                <div class="context-menu-item cut-file">
                    <i class="fas fa-cut"></i> Cut
                </div>
                <div class="context-menu-divider"></div>
                <div class="context-menu-item delete-file">
                    <i class="fas fa-trash"></i> Delete
                </div>
            `;
		} else if (menuType === 'folder') {
			menuHtml = `
                <div class="context-menu-item open-folder">
                    <i class="fas fa-folder-open"></i> Open
                </div>
                <div class="context-menu-item rename-folder">
                    <i class="fas fa-edit"></i> Rename
                </div>
                <div class="context-menu-divider"></div>
                <div class="context-menu-item delete-folder">
                    <i class="fas fa-trash"></i> Delete
                </div>
            `;
		} else {
			// Container menu
			menuHtml = `
                <div class="context-menu-item refresh-folder">
                    <i class="fas fa-sync-alt"></i> Refresh
                </div>
                <div class="context-menu-divider"></div>
                <div class="context-menu-item create-folder">
                    <i class="fas fa-folder-plus"></i> New Folder
                </div>
                <div class="context-menu-item upload-file">
                    <i class="fas fa-upload"></i> Upload Files
                </div>
                ${clipboardFiles.length > 0 ? `
                    <div class="context-menu-divider"></div>
                    <div class="context-menu-item paste-files">
                        <i class="fas fa-paste"></i> Paste ${clipboardAction === 'cut' ? '(Move)' : '(Copy)'}
                    </div>
                ` : ''}
                <div class="context-menu-divider"></div>
                <div class="context-menu-item select-all">
                    <i class="fas fa-check-square"></i> Select All
                </div>
            `;
		}

		// Create menu element
		const contextMenu = $(`<div class="context-menu">${menuHtml}</div>`);
		$('body').append(contextMenu);

		// Position menu
		const menuWidth = contextMenu.outerWidth();
		const menuHeight = contextMenu.outerHeight();
		const windowWidth = $(window).width();
		const windowHeight = $(window).height();

		// Adjust position to ensure menu is fully visible
		let posX = e.clientX;
		let posY = e.clientY;

		if (posX + menuWidth > windowWidth) {
			posX = windowWidth - menuWidth;
		}

		if (posY + menuHeight > windowHeight) {
			posY = windowHeight - menuHeight;
		}

		contextMenu.css({
			top: posY + 'px',
			left: posX + 'px'
		});

		// Show menu with animation
		contextMenu.addClass('show');

		// Add event listeners for menu items
		if (menuType === 'file') {
			contextMenu.find('.preview-file').on('click', function() {
				previewFile(fileId);
				contextMenu.remove();
			});

			contextMenu.find('.download-file').on('click', function() {
				downloadFile(fileId);
				contextMenu.remove();
			});

			contextMenu.find('.rename-file').on('click', function() {
				showRenameFileModal(fileId);
				contextMenu.remove();
			});

			contextMenu.find('.share-file').on('click', function() {
				showShareFileModal(fileId);
				contextMenu.remove();
			});

			contextMenu.find('.copy-file').on('click', function() {
				copySelectedFiles();
				contextMenu.remove();
			});

			contextMenu.find('.cut-file').on('click', function() {
				cutSelectedFiles();
				contextMenu.remove();
			});

			contextMenu.find('.delete-file').on('click', function() {
				confirmDeleteFile(fileId);
				contextMenu.remove();
			});
		} else if (menuType === 'folder') {
			contextMenu.find('.open-folder').on('click', function() {
				navigateToFolder(folderId);
				contextMenu.remove();
			});

			contextMenu.find('.rename-folder').on('click', function() {
				showRenameFolderModal(folderId);
				contextMenu.remove();
			});

			contextMenu.find('.delete-folder').on('click', function() {
				confirmDeleteFolder(folderId);
				contextMenu.remove();
			});
		} else {
			// Container menu
			contextMenu.find('.refresh-folder').on('click', function() {
				refreshCurrentFolder();
				contextMenu.remove();
			});

			contextMenu.find('.create-folder').on('click', function() {
				showCreateFolderModal();
				contextMenu.remove();
			});

			contextMenu.find('.upload-file').on('click', function() {
				$('#file-upload-input').click();
				contextMenu.remove();
			});

			if (clipboardFiles.length > 0) {
				contextMenu.find('.paste-files').on('click', function() {
					pasteFiles();
					contextMenu.remove();
				});
			}

			contextMenu.find('.select-all').on('click', function() {
				selectAllFiles();
				contextMenu.remove();
			});
		}
	}

	/**
	 * Refresh current folder
	 */
	function refreshCurrentFolder() {
		loadFolderContent(currentFolderId);
	}

	/**
	 * Create drag image for multiple files
	 * @param {number} count - Number of files being dragged
	 * @returns {HTMLElement} - The drag image element
	 */
	function createDragImage(count) {
		const dragImage = document.createElement('div');
		dragImage.className = 'file-drag-image';
		dragImage.innerHTML = `
            <div class="file-icon">
                <i class="fas fa-file"></i>
            </div>
            <span class="badge badge-pill badge-primary">${count}</span>
        `;
		document.body.appendChild(dragImage);

		// Position off-screen
		dragImage.style.position = 'absolute';
		dragImage.style.top = '-1000px';

		// Remove after drag
		setTimeout(() => {
			document.body.removeChild(dragImage);
		}, 0);

		return dragImage;
	}

	/**
	 * Prevent default behavior for events
	 * @param {Event} e - The event
	 */
	function preventDefaults(e) {
		e.preventDefault();
		e.stopPropagation();
	}

	/**
	 * Highlight drop zone
	 */
	function highlight() {
		$('#file-container').addClass('drag-over');
		$('#drop-overlay').show();
	}

	/**
	 * Remove highlight from drop zone
	 */
	function unhighlight() {
		$('#file-container').removeClass('drag-over');
		$('#drop-overlay').hide();
	}

	/**
	 * Handle file drop event
	 * @param {Event} e - The drop event
	 */
	function handleDrop(e) {
		const dt = e.dataTransfer;
		const files = dt.files;

		if (files && files.length > 0) {
			handleFileUpload(files);
		} else {
			// Check if this is a file being moved
			try {
				const dragData = JSON.parse(dt.getData('text/plain'));
				if (dragData.type === 'file') {
					// Move files to current folder
					moveFilesToFolder(dragData.fileIds, currentFolderId);
				}
			} catch (err) {
				console.error('Error parsing drag data:', err);
			}
		}
	}
	/**
	 * Create folder element for display in file explorer
	 * @param {Object} folder - Folder data object
	 * @param {string} [viewMode='grid'] - Current view mode ('grid' or 'list')
	 * @returns {jQuery} - jQuery element representing the folder
	 */
	function createFolderElement(folder, viewMode = 'grid') {
		// Normalize view mode
		viewMode = viewMode === 'grid' ? 'grid' : 'list';

		// Escape HTML to prevent XSS
		const escapeHtml = FileLibrary.escapeHtml || function(text) {
			const map = {
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;',
				"'": '&#039;'
			};
			return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
		};

		// Format date
		const formatDate = FileLibrary.formatDate || function(dateString) {
			if (!dateString) return 'N/A';
			return new Date(dateString).toLocaleDateString();
		};

		if (viewMode === 'grid') {
			// Grid view folder element
			const folderItem = $(`
                <div class="folder-item" data-folder-id="${folder.id}">
                    <div class="folder-select">
                        <input type="checkbox">
                    </div>
                    <div class="folder-preview">
                        <i class="fas fa-folder"></i>
                    </div>
                    <div class="folder-details">
                        <div class="folder-name" title="${escapeHtml(folder.folder_name)}">
                            ${escapeHtml(folder.folder_name)}
                        </div>
                        <div class="folder-meta">
                            ${folder.file_count || 0} files • 
                            ${formatDate(folder.date_modified)}
                        </div>
                    </div>
                    <div class="folder-actions">
                        <button class="btn btn-sm btn-icon rename-folder" data-toggle="tooltip" title="Rename">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-icon delete-folder" data-toggle="tooltip" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `);

			return folderItem;
		} else {
			// List view folder element
			const folderItem = $(`
                <div class="folder-item" data-folder-id="${folder.id}">
                    <div class="folder-select">
                        <input type="checkbox">
                    </div>
                    <div class="folder-icon">
                        <i class="fas fa-folder"></i>
                    </div>
                    <div class="folder-details">
                        <div class="folder-name">${escapeHtml(folder.folder_name)}</div>
                        <div class="folder-path">${escapeHtml(folder.folder_path || '')}</div>
                    </div>
                    <div class="folder-meta">
                        <span class="folder-files">${folder.file_count || 0} files</span>
                        <span class="folder-modified">${formatDate(folder.date_modified)}</span>
                    </div>
                    <div class="folder-actions">
                        <button class="btn btn-sm btn-icon rename-folder" data-toggle="tooltip" title="Rename">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-icon delete-folder" data-toggle="tooltip" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `);

			return folderItem;
		}
	}
	/**
	 * Load folder content
	 * @param {number} folderId - ID of the folder to load
	 */
	function loadFolderContent(folderId) {
		// Show loading indicator
		$('#file-container').html('<div class="loading-indicator"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');

		// Update current folder ID
		currentFolderId = folderId;

		// Reset pagination
		currentPage = 1;
		hasMoreFiles = true;

		// Ensure folder tree is visible and loaded if not already
		ensureFolderTreeLoaded();

		// AJAX request to get folder content
		$.ajax({
			url: config.baseUrl + 'Files_controller/get_folder_content',
			type: 'GET',
			data: {
				folder_id: folderId,
				sort_field: sortField,
				sort_direction: sortDirection,
				page: currentPage,
				limit: config.pageSize
			},
			success: function(response) {
				if (response.success) {
					updateClipboardButtons();

					console.log("CLIPBOARD STATE AFTER FOLDER LOAD:", {
						files: clipboardFiles,
						action: clipboardAction
					});
					// Clear selected files
					deselectAllFiles();

					// Update breadcrumb
					updateBreadcrumb(response.breadcrumb);

					// Only update the file container, not the sidebar
					if (FileLibrary.Explorer && typeof FileLibrary.Explorer.renderFolderContent === 'function') {
						FileLibrary.Explorer.renderFolderContent(response.content);
					}

					// Update folder info
					updateFolderInfo(response.folder);

					// Update pagination status
					hasMoreFiles = response.content.total_pages > 1;

					// Enable/disable paste button based on clipboard content
					updateClipboardButtons();

					// Save current folder to session/local storage
					localStorage.setItem('fileLibrary_lastFolder', folderId);

					// Add folder transition animation
					if (config.animations) {
						$('#file-container').children().addClass('folder-transition-in');
					}

					// Highlight current folder in the tree
					highlightCurrentFolder(folderId);
				} else {
					showNotification('error', response.message || 'Failed to load folder content');
					$('#file-container').html('<div class="error-message"><i class="fas fa-exclamation-circle"></i> ' +
						(response.message || 'Failed to load folder content') + '</div>');
				}
			},
			error: function(xhr, status, error) {
				showNotification('error', 'Error loading folder: ' + error);
				$('#file-container').html('<div class="error-message"><i class="fas fa-exclamation-circle"></i> Error loading folder</div>');
			}
		});
	}

// STEP 3: Add these new helper functions to FileLibrary.js

	/**
	 * Ensure folder tree is loaded
	 */
	function ensureFolderTreeLoaded() {
		// Check if folder-tree is empty
		if ($('#folder-tree').children().length === 0 && !_folderTreeLoading) {
			console.log('Folder tree empty, loading it now');
			_folderTreeLoading = true;

			// Try to restore from session storage first
			const restored = restoreFolderTree();

			// If not restored, load from server
			if (!restored) {
				loadFolderTree();
			}
		}
	}

	/**
	 * Restore folder tree from session storage
	 * @returns {boolean} Whether restoration was successful
	 */
	function restoreFolderTree() {
		try {
			// Try to get folder tree data from session storage
			const treeData = sessionStorage.getItem('folderTreeData');
			if (treeData) {
				const parsedData = JSON.parse(treeData);
				renderFolderTree(parsedData);

				// Expand current folder path in tree if we have a folder ID
				if (currentFolderId > 0) {
					expandFolderPath(currentFolderId);
				}

				return true;
			}
		} catch (e) {
			console.warn('Failed to restore folder tree from session storage:', e);
		}

		return false;
	}

	/**
	 * Highlight the current folder in the tree
	 * @param {number} folderId - Current folder ID
	 */
	function highlightCurrentFolder(folderId) {
		// Remove active class from all folder links
		$('.folder-tree-item-link').removeClass('active');

		// Add active class to current folder
		const currentFolderLink = $(`.folder-tree-item-link[data-folder-id="${folderId}"]`);
		currentFolderLink.addClass('active');

		// Make sure the folder path is expanded
		if (folderId > 0 && currentFolderLink.length === 0) {
			// If we can't find the folder in the tree, try to expand its path
			expandFolderPath(folderId);
		}
	}

	/**
	 * Expand folder path in tree to show current folder
	 * @param {number} folderId - Current folder ID
	 */
	function expandFolderPath(folderId) {
		// Find the folder path
		$.ajax({
			url: config.baseUrl + 'Folders_controller/get_folder_path',
			type: 'GET',
			data: { folder_id: folderId },
			success: function(response) {
				if (response.success && response.path) {
					// For each folder in path, expand its parent
					response.path.forEach(folder => {
						if (folder.parent_id) {
							expandFolderInTree(folder.parent_id);
						}
					});

					// Highlight the current folder
					$(`.folder-tree-item-link[data-folder-id="${folderId}"]`).addClass('active');
				}
			}
		});
	}

	/**
	 * Expand a specific folder in the tree
	 * @param {number} folderId - Folder ID to expand
	 */
	function expandFolderInTree(folderId) {
		const folderItem = $(`.folder-tree-item-link[data-folder-id="${folderId}"]`).closest('.folder-tree-item');

		if (folderItem.length) {
			// Expand this folder
			folderItem.addClass('expanded');
			folderItem.find('.folder-toggle > i').removeClass('fa-caret-right').addClass('fa-caret-down');
			folderItem.find('.folder-tree-children').slideDown();

			// Also expand all parent folders
			let parent = folderItem.parent().closest('.folder-tree-item');
			while (parent.length) {
				parent.addClass('expanded');
				parent.find('.folder-toggle > i').first().removeClass('fa-caret-right').addClass('fa-caret-down');
				parent.find('.folder-tree-children').first().slideDown();

				parent = parent.parent().closest('.folder-tree-item');
			}
		}
	}

// STEP 4: Replace the loadFolderTree function in FileLibrary.js

	/**
	 * Load folder tree
	 */
	function loadFolderTree() {
		// Set loading flag
		_folderTreeLoading = true;

		// Show loading indicator
		$('#folder-tree').html('<div class="loading-indicator"><i class="fas fa-spinner fa-spin"></i> Loading folders...</div>');

		$.ajax({
			url: config.baseUrl + 'Folders_controller/get_folder_tree',
			type: 'GET',
			success: function(response) {
				// Reset loading flag
				_folderTreeLoading = false;

				if (response.success) {
					// Store the folder tree data in session storage for persistence
					try {
						sessionStorage.setItem('folderTreeData', JSON.stringify(response.tree));
					} catch (e) {
						console.warn('Failed to save folder tree to session storage:', e);
					}

					// Only render if the tree container still exists and is empty or if we need to refresh
					if ($('#folder-tree').length && ($('#folder-tree').children('.folder-tree').length === 0 || $('#folder-tree').data('needs-refresh'))) {
						renderFolderTree(response.tree);

						// Expand current folder path in tree if we have a folder ID
						if (currentFolderId > 0) {
							expandFolderPath(currentFolderId);
						}

						// Remove refresh flag
						$('#folder-tree').removeData('needs-refresh');
					}
				} else {
					console.error('Failed to load folder tree:', response.message);
					$('#folder-tree').html('<div class="error-message"><i class="fas fa-exclamation-circle"></i> Failed to load folders</div>');
				}
			},
			error: function(xhr, status, error) {
				// Reset loading flag
				_folderTreeLoading = false;

				console.error('Error loading folder tree:', error);
				$('#folder-tree').html('<div class="error-message"><i class="fas fa-exclamation-circle"></i> Error loading folders</div>');
			}
		});
	}
	/**
	 * Preview a file
	 * @param {number} fileId - ID of the file to preview
	 */
	function previewFile(fileId) {
		// Show loading state
		$('#file-preview-modal').modal('show');
		$('#preview-container').html('<div class="preview-loading"><i class="fas fa-spinner fa-spin"></i> Loading preview...</div>');

		// Clear previous file name
		$('#preview-file-name').text('Loading...');

		// Get file info and preview content
		$.ajax({
			url: config.baseUrl + 'Files_controller/get_file_info',
			type: 'GET',
			data: { file_id: fileId },
			success: function(response) {
				if (response.success) {
					const file = response.file;

					// Update modal title with file name
					$('#preview-file-name').text(file.file_name);

					// Enable/disable navigation buttons based on available files
					updatePreviewNavigation(fileId);

					// Set download link
					$('#preview-download-btn').off('click').on('click', function() {
						downloadFile(fileId);
					});

					// Generate preview based on file type
					generatePreview(file);
				} else {
					$('#preview-container').html('<div class="preview-error"><i class="fas fa-exclamation-circle"></i> ' +
						(response.message || 'Failed to load file information') + '</div>');
				}
			},
			error: function(xhr, status, error) {
				$('#preview-container').html('<div class="preview-error"><i class="fas fa-exclamation-circle"></i> Error loading file: ' + error + '</div>');
			}
		});
	}

	/**
	 * Generate preview content based on file type
	 * @param {Object} file - File information object
	 */
	function generatePreview(file) {
		const previewContainer = $('#preview-container');
		const fileType = file.file_type;
		const previewUrl = config.baseUrl + 'Files_controller/preview/' + file.id;

		// Clear previous content
		previewContainer.empty();

		// Check if file type is previewable
		if (!config.previewableTypes.includes(fileType)) {
			// Not previewable - show file info
			previewContainer.html(`
            <div class="no-preview-available">
                <div class="file-icon"><i class="${getFileTypeIcon(fileType)}"></i></div>
                <h4>Preview not available</h4>
                <p>File type ${fileType} cannot be previewed directly.</p>
                <p class="file-details">
                    <strong>File Name:</strong> ${escapeHtml(file.file_name)}<br>
                    <strong>File Size:</strong> ${formatFileSize(file.file_size)}<br>
                    <strong>Uploaded on:</strong> ${formatDate(file.date_added)}<br>
                </p>
                <button class="btn btn-primary" id="direct-download-btn">
                    <i class="fas fa-download"></i> Download File
                </button>
            </div>
        `);

			// Add event listener for download button
			$('#direct-download-btn').on('click', function() {
				downloadFile(file.id);
			});

			return;
		}

		// Generate preview based on file type
		if (fileType.startsWith('image/')) {
			// Image preview
			previewContainer.html(`
            <div class="image-preview">
                <img src="${previewUrl}" alt="${escapeHtml(file.file_name)}" class="img-fluid">
            </div>
        `);
		} else if (fileType === 'application/pdf') {
			// PDF preview - using browser's built-in PDF viewer if available
			previewContainer.html(`
            <div class="pdf-preview">
                <iframe src="${previewUrl}?inline=true" class="pdf-frame" 
                    title="${escapeHtml(file.file_name)}" frameborder="0"></iframe>
            </div>
        `);
		} else if (fileType === 'text/plain' || fileType === 'text/csv') {
			// Text file preview
			$.ajax({
				url: config.baseUrl + 'Files_controller/get_text_content',
				type: 'GET',
				data: { file_id: file.id },
				success: function(response) {
					if (response.success) {
						previewContainer.html(`
                        <div class="text-preview">
                            <pre class="text-content">${escapeHtml(response.content)}</pre>
                        </div>
                    `);
					} else {
						previewContainer.html('<div class="preview-error"><i class="fas fa-exclamation-circle"></i> ' +
							(response.message || 'Failed to load text content') + '</div>');
					}
				},
				error: function(xhr, status, error) {
					previewContainer.html('<div class="preview-error"><i class="fas fa-exclamation-circle"></i> Error loading text: ' + error + '</div>');
				}
			});
		} else {
			// Generic file preview (fallback)
			previewContainer.html(`
            <div class="generic-preview">
                <div class="file-icon"><i class="${getFileTypeIcon(fileType)}"></i></div>
                <h4>Preview limited</h4>
                <p class="file-details">
                    <strong>File Name:</strong> ${escapeHtml(file.file_name)}<br>
                    <strong>File Type:</strong> ${fileType}<br>
                    <strong>File Size:</strong> ${formatFileSize(file.file_size)}<br>
                    <strong>Uploaded on:</strong> ${formatDate(file.date_added)}<br>
                </p>
                <button class="btn btn-primary" id="direct-download-btn">
                    <i class="fas fa-download"></i> Download File
                </button>
            </div>
        `);

			// Add event listener for download button
			$('#direct-download-btn').on('click', function() {
				downloadFile(file.id);
			});
		}

		// Add file info section below preview
		previewContainer.append(`
        <div class="file-info-section">
            <div class="file-info-header">File Information</div>
            <table class="file-info-table">
                <tr>
                    <td>Name:</td>
                    <td>${escapeHtml(file.file_name)}</td>
                </tr>
                <tr>
                    <td>Type:</td>
                    <td>${fileType}</td>
                </tr>
                <tr>
                    <td>Size:</td>
                    <td>${formatFileSize(file.file_size)}</td>
                </tr>
                <tr>
                    <td>Uploaded:</td>
                    <td>${formatDate(file.date_added)}</td>
                </tr>
                <tr>
                    <td>Modified:</td>
                    <td>${formatDate(file.date_modified)}</td>
                </tr>
                <tr>
                    <td>Category:</td>
                    <td>${escapeHtml(file.file_category || 'Unknown')}</td>
                </tr>
                <tr>
                    <td>Visibility:</td>
                    <td>${file.is_public ? '<span class="badge badge-success">Public</span>' : '<span class="badge badge-secondary">Private</span>'}</td>
                </tr>
            </table>
        </div>
    `);
	}

	/**
	 * Update preview navigation buttons (previous/next)
	 * @param {number} currentFileId - ID of the current file being previewed
	 */
	function updatePreviewNavigation(currentFileId) {
		// Get all files in current view
		const fileItems = $('.file-item');
		const fileIds = [];

		// Collect all file IDs
		fileItems.each(function() {
			fileIds.push($(this).data('file-id'));
		});

		// Find current index
		const currentIndex = fileIds.indexOf(currentFileId);

		if (currentIndex === -1) {
			// File not found in current view
			$('#preview-prev-btn, #preview-next-btn').prop('disabled', true);
			return;
		}

		// Update previous button
		if (currentIndex > 0) {
			$('#preview-prev-btn').prop('disabled', false).off('click').on('click', function() {
				previewFile(fileIds[currentIndex - 1]);
			});
		} else {
			$('#preview-prev-btn').prop('disabled', true);
		}

		// Update next button
		if (currentIndex < fileIds.length - 1) {
			$('#preview-next-btn').prop('disabled', false).off('click').on('click', function() {
				previewFile(fileIds[currentIndex + 1]);
			});
		} else {
			$('#preview-next-btn').prop('disabled', true);
		}
	}

	/**
	 * Download a file
	 * @param {number} fileId - ID of the file to download
	 */
	function downloadFile(fileId) {
		// Create a temporary link for download
		const link = document.createElement('a');
		link.href = config.baseUrl + 'Files_controller/download/' + fileId;
		link.target = '_blank';
		link.download = '';  // Let server set the filename

		// Append to body, click, and remove
		document.body.appendChild(link);
		link.click();
		document.body.removeChild(link);

		// Show notification
		showNotification('info', 'Download started');
	}
	/**
	 * Show modal to rename a file
	 * @param {number} fileId - ID of the file to rename
	 */
	function showRenameFileModal(fileId) {
		// Get file info
		$.ajax({
			url: config.baseUrl + 'Files_controller/get_file_info',
			type: 'GET',
			data: { file_id: fileId },
			success: function(response) {
				if (response.success) {
					const file = response.file;

					// Populate modal
					$('#rename-file-input').val(file.file_name);
					$('#rename-file-id').val(file.id);
					$('#rename-file-description').val(file.description || '');

					// Show modal
					$('#rename-file-modal').modal('show');

					// Focus on input
					setTimeout(function() {
						$('#rename-file-input').focus().select();
					}, 500);
				} else {
					showNotification('error', response.message || 'Failed to get file information');
				}
			},
			error: function(xhr, status, error) {
				showNotification('error', 'Error: ' + error);
			}
		});
	}

	/**
	 * Show confirmation modal for file deletion
	 * @param {number} fileId - ID of the file to delete
	 */
	function confirmDeleteFile(fileId) {
		// Get file name
		const fileName = $(`.file-item[data-file-id="${fileId}"]`).data('file-name') || 'this file';

		// Set delete info
		$('#delete-item-id').val(fileId);
		$('#delete-item-type').val('file');
		$('#confirm-delete-message').text(`Are you sure you want to delete "${fileName}"?`);

		// Show modal
		$('#confirm-delete-modal').modal('show');

		// Set up delete button
		$('#confirm-delete-btn').off('click').on('click', function() {
			deleteFile(fileId);
		});
	}

	/**
	 * Delete a file
	 * @param {number} fileId - ID of the file to delete
	 */
	function deleteFile(fileId) {
		// Close modal
		$('#confirm-delete-modal').modal('hide');

		// Show loading notification
		showNotification('info', 'Deleting file...', false);

		// AJAX request
		$.ajax({
			url: config.baseUrl + 'Files_controller/delete_file',
			type: 'POST',
			data: { file_id: fileId },
			success: function(response) {
				// Close loading notification
				$('.notification').remove();

				if (response.success) {
					// Remove file from view with animation
					const fileItem = $(`.file-item[data-file-id="${fileId}"]`);
					fileItem.fadeOut(300, function() {
						$(this).remove();

						// Refresh empty state if needed
						if ($('.file-item, .folder-item').length === 0) {
							refreshCurrentFolder();
						}
					});

					// Show success notification
					showNotification('success', response.message || 'File deleted successfully');

					// If file was in selection, remove it
					if (isFileSelected(fileId)) {
						toggleFileSelection(fileId);
					}
				} else {
					// Show error notification
					showNotification('error', response.message || 'Failed to delete file');
				}
			},
			error: function(xhr, status, error) {
				// Close loading notification
				$('.notification').remove();

				// Show error notification
				showNotification('error', 'Server error: ' + error);
			}
		});
	}

	/**
	 * Toggle public/private status of a file
	 * @param {number} fileId - ID of the file to toggle status
	 */
	function toggleFilePublicStatus(fileId) {
		// Show loading notification
		showNotification('info', 'Updating file status...', false);

		// AJAX request
		$.ajax({
			url: config.baseUrl + 'Files_controller/toggle_file_status',
			type: 'POST',
			data: { file_id: fileId },
			success: function(response) {
				// Close loading notification
				$('.notification').remove();

				if (response.success) {
					// Update file item - replace menu option
					const fileItem = $(`.file-item[data-file-id="${fileId}"]`);
					const isPublic = response.is_public;

					if (isPublic) {
						fileItem.find('.make-public').replaceWith(`
                        <a class="dropdown-item make-private" href="#">
                            <i class="fas fa-lock"></i> Make Private
                        </a>
                    `);

						// Add public indicator if not present
						if (fileItem.find('.file-public-indicator').length === 0) {
							fileItem.find('.file-name').append('<span class="file-public-indicator ml-1" title="Public file"><i class="fas fa-globe"></i></span>');
						}
					} else {
						fileItem.find('.make-private').replaceWith(`
                        <a class="dropdown-item make-public" href="#">
                            <i class="fas fa-globe"></i> Make Public
                        </a>
                    `);

						// Remove public indicator
						fileItem.find('.file-public-indicator').remove();
					}

					// Show success notification
					showNotification('success', response.message || 'File status updated');
				} else {
					// Show error notification
					showNotification('error', response.message || 'Failed to update file status');
				}
			},
			error: function(xhr, status, error) {
				// Close loading notification
				$('.notification').remove();

				// Show error notification
				showNotification('error', 'Server error: ' + error);
			}
		});
	}

	/**
	 * Show file sharing modal
	 * @param {number} fileId - ID of the file to share
	 */
	function showShareFileModal(fileId) {
		// Reset form
		$('#file-share-form')[0].reset();
		$('#share-file-id').val(fileId);

		// Get file info and current shares
		$.ajax({
			url: config.baseUrl + 'Files_controller/get_file_info',
			type: 'GET',
			data: {
				file_id: fileId,
				include_shares: true
			},
			success: function(response) {
				if (response.success) {
					const file = response.file;

					// Set file name
					$('#share-file-name').text(file.file_name);

					// Load users for sharing
					loadUsersForSharing();

					// Update current shares list
					updateSharesList(response.shares || []);

					// Show modal
					$('#file-share-modal').modal('show');
				} else {
					showNotification('error', response.message || 'Failed to get file information');
				}
			},
			error: function(xhr, status, error) {
				showNotification('error', 'Error: ' + error);
			}
		});

		// Set up share button
		$('#share-submit-btn').off('click').on('click', function() {
			shareFile(fileId);
		});
	}

	/**
	 * Load users for sharing
	 */
	function loadUsersForSharing() {
		// AJAX request to get users
		$.ajax({
			url: config.baseUrl + 'Employees/get_users_for_sharing',
			type: 'GET',
			success: function(response) {
				// Check if the response is a string (might be JSON string)
				if (typeof response === 'string') {
					try {
						response = JSON.parse(response);
					} catch (e) {
						console.error('Failed to parse response:', e);
					}
				}

				// Now process the response
				if (response && response.status === "success" && Array.isArray(response.data)) {
					const userSelect = $('#share-user-select');
					userSelect.empty();

					// Add default option
					userSelect.append('<option value="">Select user...</option>');

					// Add user options
					response.data.forEach((user) => {
						// Create full name by combining parts
						const fullName = `${user.Firstname || ''} ${user.Lastname || ''}`.trim();

						// Use empid instead of id since all ids are "1"
						const userId = user.empid || user.id;

						userSelect.append(`<option value="${userId}">${fullName} (${user.EmailAddress})</option>`);
					});
				} else {
					console.error('Invalid user data format', response);
					$('#share-user-select').empty().append('<option value="">Invalid user data format</option>');
				}
			},
			error: function(xhr, status, error) {
				console.error('Error loading users:', error);
				$('#share-user-select').empty().append('<option value="">Error loading users</option>');
			}
		});
	}

	/**
	 * Update shares list in share modal
	 * @param {Array} shares - Array of share objects
	 */
	function updateSharesList(shares) {
		const shareList = $('#share-user-list');

		if (!shares || shares.length === 0) {
			shareList.html('<div class="text-center text-muted py-3"><i class="fas fa-info-circle"></i> Not shared with anyone</div>');
			return;
		}

		// Clear list and add shares
		shareList.empty();

		shares.forEach(share => {
			const shareItem = $(`
            <div class="share-item" data-share-id="${share.id}">
                <div class="share-user-info">
                    <span class="share-user-name">${share.user_name}</span>
                    <span class="share-user-email">${share.user_email}</span>
                </div>
                <div class="share-permission">
                    ${share.permission === 'edit'
				? '<span class="badge badge-primary">Edit</span>'
				: '<span class="badge badge-secondary">View</span>'}
                </div>
                <div class="share-actions">
                    <button class="btn btn-sm btn-icon remove-share" data-share-id="${share.id}" title="Remove share">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        `);

			shareList.append(shareItem);
		});

		// Add event listener for remove buttons
		$('.remove-share').on('click', function() {
			const shareId = $(this).data('share-id');
			removeFileShare(shareId);
		});
	}

	/**
	 * Share a file with a user
	 * @param {number} fileId - ID of the file to share
	 */
	function shareFile(fileId) {
		const userId = $('#share-user-select').val();
		const permission = $('input[name="permission-level"]:checked').val();

		if (!userId) {
			showNotification('error', 'Please select a user to share with');
			return;
		}

		// Show loading state
		const submitBtn = $('#share-submit-btn');
		const originalBtnText = submitBtn.html();
		submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Sharing...');
		submitBtn.prop('disabled', true);

		// AJAX request
		$.ajax({
			url: config.baseUrl + 'Files_controller/share_file',
			type: 'POST',
			data: {
				file_id: fileId,
				user_id: userId,
				permission: permission
			},
			success: function(response) {
				// Reset button
				submitBtn.html(originalBtnText);
				submitBtn.prop('disabled', false);

				if (response.success) {
					// Reset select
					$('#share-user-select').val('');

					// Update shares list
					updateSharesList(response.shares);

					// Show notification
					showNotification('success', response.message || 'File shared successfully');
				} else {
					showNotification('error', response.message || 'Failed to share file');
				}
			},
			error: function(xhr, status, error) {
				// Reset button
				submitBtn.html(originalBtnText);
				submitBtn.prop('disabled', false);

				showNotification('error', 'Server error: ' + error);
			}
		});
	}

	/**
	 * Remove file share
	 * @param {number} shareId - ID of the share to remove
	 */
	function removeFileShare(shareId) {
		// Show loading notification
		showNotification('info', 'Removing share...', false);

		// AJAX request
		$.ajax({
			url: config.baseUrl + 'Files_controller/remove_share',
			type: 'POST',
			data: { share_id: shareId },
			success: function(response) {
				// Close loading notification
				$('.notification').remove();

				if (response.success) {
					// Remove share item with animation
					$(`.share-item[data-share-id="${shareId}"]`).slideUp(300, function() {
						$(this).remove();

						// Check if list is empty
						if ($('.share-item').length === 0) {
							$('#share-user-list').html('<div class="text-center text-muted py-3"><i class="fas fa-info-circle"></i> Not shared with anyone</div>');
						}
					});

					// Show success notification
					showNotification('success', response.message || 'Share removed successfully');
				} else {
					// Show error notification
					showNotification('error', response.message || 'Failed to remove share');
				}
			},
			error: function(xhr, status, error) {
				// Close loading notification
				$('.notification').remove();

				// Show error notification
				showNotification('error', 'Server error: ' + error);
			}
		});
	}
	/**
	 * Show a notification message
	 * @param {string} type - Notification type ('success', 'error', 'info', 'warning')
	 * @param {string} message - Notification message
	 * @param {number} [duration=3000] - How long to show the notification in ms
	 */
	function showNotification(type, message, duration = 3000) {
		// Create notification container if it doesn't exist
		if ($('#notification-container').length === 0) {
			$('body').append('<div id="notification-container"></div>');
		}

		// Generate a unique ID for this notification
		const notificationId = 'notification-' + new Date().getTime();

		// Create notification HTML with appropriate icon based on type
		let iconHtml = '';
		switch(type) {
			case 'success':
				iconHtml = '<i class="fas fa-check-circle"></i>';
				break;
			case 'error':
				iconHtml = '<i class="fas fa-times-circle"></i>';
				break;
			case 'info':
				iconHtml = '<i class="fas fa-info-circle"></i>';
				break;
			case 'warning':
				iconHtml = '<i class="fas fa-exclamation-triangle"></i>';
				break;
			default:
				iconHtml = '<i class="fas fa-bell"></i>';
		}

		// Create notification element
		const notification = $(`
        <div id="${notificationId}" class="notification notification-${type}">
            <div class="notification-icon">
                ${iconHtml}
            </div>
            <div class="notification-content">${message}</div>
            <button class="notification-close"><i class="fas fa-times"></i></button>
        </div>
    `);

		// Add to container
		$('#notification-container').append(notification);

		// Add animation
		setTimeout(() => {
			notification.addClass('show');
		}, 10);

		// Close button event
		notification.find('.notification-close').on('click', function() {
			closeNotification(notification);
		});

		// Auto close after duration (if provided)
		if (duration !== false) {
			setTimeout(() => {
				closeNotification(notification);
			}, duration);
		}

		// Return the notification element for potential further manipulation
		return notification;
	}

	/**
	 * Close a notification
	 * @param {jQuery} notification - The notification element
	 */
	function closeNotification(notification) {
		// Don't close if already closing
		if (notification.hasClass('closing')) {
			return;
		}

		notification.addClass('closing');
		notification.removeClass('show');

		// Remove from DOM after animation
		setTimeout(() => {
			notification.remove();
		}, 300);
	}

	/**
	 * Show a loading overlay for long operations
	 * @param {string} [message='Loading...'] - Message to display
	 */
	function showLoadingOverlay(message = 'Loading...') {
		// Remove any existing overlay first
		hideLoadingOverlay();

		// Create overlay element
		const overlay = $(`
        <div id="loading-overlay">
            <div class="loading-content">
                <div class="loading-spinner">
                    <i class="fas fa-spinner fa-spin"></i>
                </div>
                <div class="loading-message">${message}</div>
            </div>
        </div>
    `);

		// Add to body
		$('body').append(overlay);

		// Add show class for animation
		setTimeout(() => {
			overlay.addClass('show');
		}, 10);

		return overlay;
	}

	/**
	 * Hide the loading overlay
	 */
	function hideLoadingOverlay() {
		const overlay = $('#loading-overlay');

		if (overlay.length > 0) {
			overlay.removeClass('show');

			// Remove after animation
			setTimeout(() => {
				overlay.remove();
			}, 300);
		}
	}

	// Public API
	return {
		init: init,
		 // previewFile: previewFile,
		// downloadFile: downloadFile,
		showCreateFolderModal: showCreateFolderModal,
		selectFile: selectFile,
		deselectAllFiles: deselectAllFiles,
		setViewMode: setViewMode,
		refreshCurrentFolder: refreshCurrentFolder,
		// Add these methods needed by Preview.js
		getConfig: function() {
			return config;
		},
		escapeHtml: escapeHtml,
		getFileTypeIcon: getFileTypeIcon,
		formatFileSize: formatFileSize,
		getCurrentFolderId : getCurrentFolderId,
		createFileElement : createFileElement,
		formatDate : formatDate,
		createFolderElement : createFolderElement,
		restoreFolderTree: restoreFolderTree,
		expandFolderPath: expandFolderPath,
		ensureFolderTreeLoaded: ensureFolderTreeLoaded,
		highlightCurrentFolder: highlightCurrentFolder,
		previewFile: previewFile,
		downloadFile: downloadFile,
		showRenameFileModal: showRenameFileModal,
		confirmDeleteFile: confirmDeleteFile,
		toggleFilePublicStatus: toggleFilePublicStatus,
		showShareFileModal: showShareFileModal,
		deleteFile: deleteFile,
		shareFile: shareFile,
		showNotification: showNotification,
		showLoadingOverlay: showLoadingOverlay,
		hideLoadingOverlay: hideLoadingOverlay,
	};

})(jQuery);

// Initialize the File Library when document is ready
// In main.js, update the initialization code at the end:

// Initialize the File Library when document is ready
$(document).ready(function() {
	FileLibrary.init();

	// Trigger event to notify modules that FileLibrary is ready
	$(document).trigger('fileLibraryReady');
});
