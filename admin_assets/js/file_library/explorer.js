/**
 * Finance Real - File Library Module
 * Explorer.js - Handles file explorer UI functionality
 */
FileLibrary._folderTreeLoading = false;
// Extend the FileLibrary namespace
(function(FileLibrary) {
	/**
	 * File Explorer
	 * Handles file navigation, selection, and UI interactions
	 */
	const Explorer = {
		// Private properties
		_currentFolderId: 0,
		_selectedFiles: [],
		_viewMode: 'grid',
		_sortField: 'date_added',
		_sortDirection: 'desc',
		_clipboard: {
			files: [],
			action: null // 'copy' or 'cut'
		},
		_currentPage: 1,
		_isLoadingMore: false,
		_hasMoreFiles: true,

		/**
		 * Initialize the explorer
		 */
		/**
		 * Initialize the explorer
		 */
		init: function() {
			// First, try to restore last folder ID from local storage
			const lastFolderId = localStorage.getItem('fileLibrary_lastFolder');
			if (lastFolderId && !isNaN(parseInt(lastFolderId))) {
				this._currentFolderId = parseInt(lastFolderId);
			}

			// Set initial view mode from local storage
			const savedViewMode = localStorage.getItem('fileLibrary_viewMode');
			if (savedViewMode) {
				this.setViewMode(savedViewMode);
			}

			// Initialize event listeners
			this.initEvents();

			// Try to restore folder tree from session storage first
			const restored = this.restoreFolderTree();

			// If not restored, load it fresh
			if (!restored) {
				this.loadFolderTree();
			}

			// Load current folder content
			this.loadFolderContent(this._currentFolderId);

			console.log('File Explorer initialized with folder ID:', this._currentFolderId);
		},

		/**
		 * Initialize explorer events
		 */
		initEvents: function() {
			// View mode toggle
			$('#view-mode-grid').on('click', () => {
				this.setViewMode('grid');
			});

			$('#view-mode-list').on('click', () => {
				this.setViewMode('list');
			});

			// Folder navigation events
			$(document).on('click', '.folder-item', (e) => {
				// Don't navigate if clicking on actions
				if ($(e.target).closest('.folder-actions').length) {
					return;
				}

				const folderId = $(e.currentTarget).data('folder-id');
				this.navigateToFolder(folderId);
			});

			// Breadcrumb navigation
			$(document).on('click', '.breadcrumb-item', (e) => {
				const folderId = $(e.currentTarget).data('folder-id');
				if (folderId !== undefined) {
					this.navigateToFolder(folderId);
				}
			});

			// File selection
			$(document).on('click', '.file-item', (e) => {
				// Don't select if clicking on actions
				if ($(e.target).closest('.file-actions').length) {
					return;
				}

				const fileId = $(e.currentTarget).data('file-id');

				// Multi-select with modifier key
				if (e.ctrlKey || e.metaKey) {
					this.toggleFileSelection(fileId);
				} else {
					this.selectFile(fileId);
				}
			});

			// File checkbox click
			$(document).on('click', '.file-select input[type="checkbox"]', (e) => {
				e.stopPropagation();
				const fileId = $(e.currentTarget).closest('.file-item').data('file-id');
				this.toggleFileSelection(fileId);
			});

			// Select all files
			$('#select-all-files').on('click', () => {
				this.selectAllFiles();
			});

			// Deselect all files
			$('#deselect-all-files').on('click', () => {
				this.deselectAllFiles();
			});

			// Sort options
			$('.sort-option').on('click', (e) => {
				const field = $(e.currentTarget).data('sort-field');
				this.setSortOrder(field);
			});

			// Clipboard actions
			$('#copy-selected-btn').on('click', () => {
				this.copySelectedFiles();
			});

			$('#cut-selected-btn').on('click', () => {
				this.cutSelectedFiles();
			});

			$('#paste-files-btn').on('click', () => {
				this.pasteFiles();
			});

			// Refresh folder
			$('#refresh-folder-btn').on('click', () => {
				this.refreshCurrentFolder();
			});

			// Folder tree toggle
			$(document).on('click', '.folder-toggle', function() {
				$(this).closest('.folder-tree-item').toggleClass('expanded');
				$(this).find('i').toggleClass('fa-caret-right fa-caret-down');
				$(this).siblings('.folder-tree-children').slideToggle();
			});

			// Folder tree navigation
			$(document).on('click', '.folder-tree-item-link', (e) => {
				e.preventDefault();
				const folderId = $(e.currentTarget).data('folder-id');
				this.navigateToFolder(folderId);

				// Update active state
				$('.folder-tree-item-link').removeClass('active');
				$(e.currentTarget).addClass('active');
			});

			// Infinite scroll for loading more files
			$('#file-container').on('scroll', $.debounce(200, () => {
				const container = $('#file-container');
				if (container.scrollTop() + container.innerHeight() >= container[0].scrollHeight - 200) {
					if (!this._isLoadingMore && this._hasMoreFiles) {
						this.loadMoreFiles();
					}
				}
			}));
		},

		/**
		 * Set view mode (grid or list)
		 * @param {string} mode - The view mode
		 */
		setViewMode: function(mode) {
			if (mode !== 'grid' && mode !== 'list') {
				return;
			}

			// Update view mode
			this._viewMode = mode;

			// Update UI
			if (mode === 'grid') {
				$('#view-mode-grid').addClass('active');
				$('#view-mode-list').removeClass('active');
				$('.file-list').removeClass('file-list').addClass('file-grid');
			} else {
				$('#view-mode-list').addClass('active');
				$('#view-mode-grid').removeClass('active');
				$('.file-grid').removeClass('file-grid').addClass('file-list');
			}

			// Save preference
			localStorage.setItem('fileLibrary_viewMode', mode);
		},

		/**
		 * Set sort order
		 * @param {string} field - The field to sort by
		 */
		setSortOrder: function(field) {
			// Toggle direction if same field
			if (field === this._sortField) {
				this._sortDirection = this._sortDirection === 'asc' ? 'desc' : 'asc';
			} else {
				this._sortField = field;
				this._sortDirection = 'desc';
			}

			// Update UI
			$('.sort-option').removeClass('active');
			$(`.sort-option[data-sort-field="${field}"]`).addClass('active');

			// Add direction indicator
			$('.sort-direction').remove();
			$(`.sort-option[data-sort-field="${field}"]`).append(
				`<span class="sort-direction ml-1">
                    <i class="fas fa-sort-${this._sortDirection === 'asc' ? 'up' : 'down'}"></i>
                </span>`
			);

			// Reload current folder with new sort
			this.refreshCurrentFolder();
		},

		/**
		 * Navigate to folder
		 * @param {number} folderId - The folder ID
		 */
		navigateToFolder: function(folderId) {
			// Reset selection
			this.deselectAllFiles();

			// Update current folder ID
			this._currentFolderId = folderId;

			// Reset pagination
			this._currentPage = 1;
			this._hasMoreFiles = true;

			// Add animation if enabled
			if (FileLibrary.getConfig().animations) {
				$('#file-container').addClass('folder-transition-out');

				setTimeout(() => {
					this.loadFolderContent(folderId);
				}, 200);
			} else {
				this.loadFolderContent(folderId);
			}
		},

		/**
		 * Load folder content
		 * @param {number} folderId - The folder ID
		 */
		loadFolderContent: function(folderId) {
			// Show loading indicator
			$('#file-container').html('<div class="loading-indicator"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');

			// AJAX request
			$.ajax({
				url: FileLibrary.getConfig().baseUrl+'Files_controller/get_folder_content',
				type: 'GET',
				data: {
					folder_id: folderId,
					sort_field: this._sortField,
					sort_direction: this._sortDirection,
					page: this._currentPage,
					limit: FileLibrary.getConfig().pageSize
				},
				success: (response) => {
					if (response.success) {
						// Render content
						this.renderFolderContent(response.content);

						// Update breadcrumb
						this.updateBreadcrumb(response.breadcrumb);

						// Update folder info
						this.updateFolderInfo(response.folder);

						// Update pagination status
						this._hasMoreFiles = response.content.total_pages > 1;

						// Update clipboard buttons
						this.updateClipboardButtons();

						// Save current folder ID
						localStorage.setItem('fileLibrary_lastFolder', folderId);

						// Apply transition animation
						if (FileLibrary.getConfig().animations) {
							$('#file-container').children().addClass('folder-transition-in');
						}
					} else {
						FileLibrary.showNotification('error', response.message || 'Failed to load folder content');
						$('#file-container').html('<div class="error-message"><i class="fas fa-exclamation-circle"></i> ' +
							(response.message || 'Failed to load folder content') + '</div>');
					}
				},
				error: (xhr, status, error) => {
					FileLibrary.showNotification('error', 'Error loading folder: ' + error);
					$('#file-container').html('<div class="error-message"><i class="fas fa-exclamation-circle"></i> Error loading folder</div>');
				}
			});
		},

		/**
		 * Load more files when scrolling
		 */
		loadMoreFiles: function() {
			if (this._isLoadingMore || !this._hasMoreFiles) {
				return;
			}

			this._isLoadingMore = true;
			this._currentPage++;

			// Add loading indicator
			$(`.file-${this._viewMode}`).append('<div class="loading-more"><i class="fas fa-spinner fa-spin"></i> Loading more...</div>');

			// AJAX request
			$.ajax({
				url: FileLibrary.getConfig().baseUrl+'Files_controller/get_folder_content',
				type: 'GET',
				data: {
					folder_id: this._currentFolderId,
					sort_field: this._sortField,
					sort_direction: this._sortDirection,
					page: this._currentPage,
					limit: FileLibrary.getConfig().pageSize
				},
				success: (response) => {
					// Remove loading indicator
					$('.loading-more').remove();

					if (response.success) {
						// Append files to current view
						response.content.files.forEach(file => {
							$(`.file-${this._viewMode}`).append(FileLibrary.createFileElement(file, this._viewMode));
						});

						// Update pagination status
						this._hasMoreFiles = this._currentPage < response.content.total_pages;

						// Initialize tooltips for new elements
						$('[data-toggle="tooltip"]').tooltip();
					} else {
						FileLibrary.showNotification('error', response.message || 'Failed to load more files');
					}

					this._isLoadingMore = false;
				},
				error: (xhr, status, error) => {
					// Remove loading indicator
					$('.loading-more').remove();

					FileLibrary.showNotification('error', 'Error loading more files: ' + error);
					this._isLoadingMore = false;
				}
			});
		},

		/**
		 * Render folder content
		 * @param {Object} content - The folder content data
		 */
		renderFolderContent: function(content, isSearch = false, searchTerm = '') {
			// Clear container
			$('#file-container').empty();

			// If this is a search result, add search info header
			if (isSearch) {
				const searchInfo = $(`
            <div class="search-info">
                <div class="search-summary">
                    <i class="fas fa-search"></i> 
                    ${content.total_files} ${content.total_files === 1 ? 'result' : 'results'} found for 
                    <span class="search-term">"${FileLibrary.escapeHtml(searchTerm)}"</span>
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
					FileLibrary.refreshCurrentFolder();
				});
			}

			// Create container based on view mode
			const containerClass = this._viewMode === 'grid' ? 'file-grid' : 'file-list';
			const container = $(`<div class="${containerClass}"></div>`);

			// Add folders first
			if (content.folders && content.folders.length > 0) {
				content.folders.forEach(folder => {
					container.append(FileLibrary.createFolderElement(folder, this._viewMode));
				});
			}

			// Then add files
			if (content.files && content.files.length > 0) {
				content.files.forEach(file => {
					container.append(FileLibrary.createFileElement(file, this._viewMode));
				});
			}

			// Add empty state if no content
			if ((!content.folders || content.folders.length === 0) &&
				(!content.files || content.files.length === 0)) {

				if (isSearch) {
					// Empty search state
					container.append(`
                <div class="empty-search">
                    <i class="fas fa-search"></i>
                    <p>No results found for "${FileLibrary.escapeHtml(searchTerm)}"</p>
                    <p class="empty-search-tips">Try different keywords or filters</p>
                </div>
            `);
				} else {
					// Empty folder state
					container.append(`
                <div class="empty-folder">
                    <i class="fas fa-folder-open"></i>
                    <p>This folder is empty</p>
                    <div class="empty-folder-actions">
                        <button class="btn btn-sm btn-primary" id="empty-upload-btn">
                            <i class="fas fa-upload"></i> Upload Files
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" id="empty-create-folder-btn">
                            <i class="fas fa-folder-plus"></i> Create Folder
                        </button>
                    </div>
                </div>
            `);

					// Add event listeners for empty state buttons
					$('#empty-upload-btn').on('click', function() {
						$('#file-upload-input').click();
					});

					$('#empty-create-folder-btn').on('click', function() {
						FileLibrary.showCreateFolderModal();
					});
				}
			}

			// Add to main container
			$('#file-container').append(container);

			// Initialize tooltips
			$('[data-toggle="tooltip"]').tooltip();
		},

		/**
		 * Update breadcrumb navigation
		 * @param {Array} breadcrumb - The breadcrumb data
		 */
		updateBreadcrumb: function(breadcrumb) {
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
                            ${FileLibrary.escapeHtml(item.folder_name)}
                        </li>
                    `);
				} else {
					breadcrumbContainer.append(`
                        <li class="breadcrumb-item" data-folder-id="${item.id}">
                            ${FileLibrary.escapeHtml(item.folder_name)}
                        </li>
                    `);
				}
			});
		},

		/**
		 * Update folder information
		 * @param {Object} folder - The folder data
		 */
		updateFolderInfo: function(folder) {
			// Update folder name in header
			$('#current-folder-name').text(folder ? folder.folder_name : 'Home');

			// Update folder metadata
			const metaContainer = $('#folder-meta');

			if (folder) {
				metaContainer.html(`
                    <span><i class="far fa-clock"></i> Modified: ${FileLibrary.formatDate(folder.date_modified)}</span>
                    <span><i class="far fa-user"></i> Owner: ${folder.owner_name || 'System'}</span>
                    ${folder.description ? `<span><i class="far fa-sticky-note"></i> ${FileLibrary.escapeHtml(folder.description)}</span>` : ''}
                `);
			} else {
				metaContainer.html(`<span><i class="fas fa-home"></i> Root directory</span>`);
			}
		},

		/**
		 * Load folder tree for sidebar navigation
		 */
		/**
		 * Load folder tree for sidebar navigation
		 */

		/**
		 * Filter out system folders recursively
		 * @param {Array} folders - Array of folder data
		 * @returns {Array} Filtered folders without system folders
		 */
		 filterSystemFolders: function(folders) {
		if (!folders || !Array.isArray(folders)) {
			return [];
		}

		return folders.filter(folder => {
			// Filter out folders with is_system_folder = "Yes"
			if (folder.is_system_folder === "Yes") {
				return false;
			}

			// For folders with children, recursively filter their children too
			if (folder.children && Array.isArray(folder.children)) {
				folder.children = this.filterSystemFolders(folder.children);
			}

			return true;
		});
	},
		loadFolderTree: function() {
			// Show loading indicator
			$('#folder-tree').html('<div class="loading-indicator"><i class="fas fa-spinner fa-spin"></i> Loading folders...</div>');

			$.ajax({
				url: FileLibrary.getConfig().baseUrl+'fundit/Folders_controller/get_folder_tree',
				type: 'GET',
				success: (response) => {
					if (response.success) {
						// Store the folder tree data in session storage for persistence
						const filteredTree = this.filterSystemFolders(response.tree);
						try {
							sessionStorage.setItem('folderTreeData', JSON.stringify(filteredTree));
						} catch (e) {
							console.warn('Failed to save folder tree to session storage:', e);
						}

						this.renderFolderTree(filteredTree);

						// Expand current folder path in tree if we have a folder ID
						if (this._currentFolderId > 0) {
							this.expandFolderPath(this._currentFolderId);
						}
					} else {
						console.error('Failed to load folder tree:', response.message);
						$('#folder-tree').html('<div class="error-message"><i class="fas fa-exclamation-circle"></i> Failed to load folders</div>');
					}
				},
				error: (xhr, status, error) => {
					console.error('Error loading folder tree:', error);
					$('#folder-tree').html('<div class="error-message"><i class="fas fa-exclamation-circle"></i> Error loading folders</div>');
				}
			});
		},


		/**
		 * Try to restore folder tree from session storage
		 * This should be called after initialization
		 */
		restoreFolderTree: function() {
			try {
				// Try to get folder tree data from session storage
				const treeData = sessionStorage.getItem('folderTreeData');
				if (treeData) {
					const parsedData = JSON.parse(treeData);
					this.renderFolderTree(parsedData);

					// Expand current folder path in tree if we have a folder ID
					if (this._currentFolderId > 0) {
						this.expandFolderPath(this._currentFolderId);
					}

					return true;
				}
			} catch (e) {
				console.warn('Failed to restore folder tree from session storage:', e);
			}

			return false;
		},

		/**
		 * Expand folder path in tree to show current folder
		 * @param {number} folderId - Current folder ID
		 */
		expandFolderPath: function(folderId) {
			// Find the folder path
			$.ajax({
				url: FileLibrary.getConfig().baseUrl+'Folders_controller/get_folder_path',
				type: 'GET',
				data: { folder_id: folderId },
				success: (response) => {
					if (response.success && response.path) {
						// For each folder in path, expand its parent
						response.path.forEach(folder => {
							if (folder.parent_id) {
								this.expandFolderInTree(folder.parent_id);
							}
						});

						// Highlight the current folder
						$(`.folder-tree-item-link[data-folder-id="${folderId}"]`).addClass('active');
					}
				}
			});
		},

		/**
		 * Expand a specific folder in the tree
		 * @param {number} folderId - Folder ID to expand
		 */
		expandFolderInTree: function(folderId) {
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
		},

		/**
		 * Render folder tree
		 * @param {Array} folders - Array of folder data
		 * @param {jQuery} container - Container element (optional)
		 */
		renderFolderTree: function(folders, container) {
			if (!container) {
				// Root container
				container = $('#folder-tree');
				container.empty();
			}

			if (!folders || folders.length === 0) {
				return;
			}

			const list = $('<ul class="folder-tree"></ul>');

			folders.forEach(folder => {
				const hasChildren = folder.children && folder.children.length > 0;
				const item = $(`
                    <li class="folder-tree-item">
                        <div class="folder-toggle ${hasChildren ? '' : 'invisible'}">
                            <i class="fas fa-caret-right"></i>
                        </div>
                        <a href="#" class="folder-tree-item-link" data-folder-id="${folder.id}">
                            <i class="fas fa-folder"></i>
                            ${FileLibrary.escapeHtml(folder.name)}
                            ${folder.file_count > 0 ? `<span class="badge badge-light badge-pill ml-1">${folder.file_count}</span>` : ''}
                        </a>
                        ${hasChildren ? '<ul class="folder-tree-children" style="display: none;"></ul>' : ''}
                    </li>
                `);

				list.append(item);

				// Recursively add children
				if (hasChildren) {
					this.renderFolderTree(folder.children, item.find('.folder-tree-children'));
				}
			});

			container.append(list);
		},

		/**
		 * Select a single file
		 * @param {number} fileId - The file ID
		 */
		selectFile: function(fileId) {
			// Deselect all files first
			this.deselectAllFiles();

			// Then select the file
			this.toggleFileSelection(fileId, true);
		},

		/**
		 * Toggle file selection
		 * @param {number} fileId - The file ID
		 * @param {boolean} forceSelect - Force selection state
		 */
		toggleFileSelection: function(fileId, forceSelect) {
			const fileItem = $(`.file-item[data-file-id="${fileId}"]`);
			const isSelected = typeof forceSelect !== 'undefined' ? forceSelect : !fileItem.hasClass('selected');

			if (isSelected) {
				// Add to selection if not already selected
				if (!this.isFileSelected(fileId)) {
					this._selectedFiles.push(fileId);
				}

				fileItem.addClass('selected');
				fileItem.find('.file-select input[type="checkbox"]').prop('checked', true);
			} else {
				// Remove from selection
				this._selectedFiles = this._selectedFiles.filter(id => id !== fileId);

				fileItem.removeClass('selected');
				fileItem.find('.file-select input[type="checkbox"]').prop('checked', false);
			}

			// Update toolbar based on selection
			this.updateSelectionUI();
		},

		/**
		 * Check if file is selected
		 * @param {number} fileId - The file ID
		 * @returns {boolean} Whether file is selected
		 */
		isFileSelected: function(fileId) {
			return this._selectedFiles.includes(fileId);
		},

		/**
		 * Select all files in current view
		 */
		selectAllFiles: function() {
			const fileItems = $('.file-item');

			// Reset selection
			this._selectedFiles = [];

			// Add all file IDs to selection
			fileItems.each((index, item) => {
				const fileId = $(item).data('file-id');
				this._selectedFiles.push(fileId);

				$(item).addClass('selected');
				$(item).find('.file-select input[type="checkbox"]').prop('checked', true);
			});

			// Update toolbar
			this.updateSelectionUI();
		},

		/**
		 * Deselect all files
		 */
		deselectAllFiles: function() {
			// Clear selection array
			this._selectedFiles = [];

			// Update UI
			$('.file-item').removeClass('selected');
			$('.file-item .file-select input[type="checkbox"]').prop('checked', false);

			// Update toolbar
			this.updateSelectionUI();
		},

		/**
		 * Update UI based on selection
		 */
		updateSelectionUI: function() {
			const count = this._selectedFiles.length;

			// Update selection counter
			$('#selected-count').text(count);

			// Show/hide selection toolbar
			if (count > 0) {
				$('#selection-toolbar').show();
			} else {
				$('#selection-toolbar').hide();
			}

			// Enable/disable buttons based on selection
			$('#copy-selected-btn, #cut-selected-btn, #delete-selected-btn').prop('disabled', count === 0);
		},

		/**
		 * Copy selected files to clipboard
		 */
		copySelectedFiles: function() {
			if (this._selectedFiles.length === 0) {
				return;
			}

			this._clipboard.files = [...this._selectedFiles];
			this._clipboard.action = 'copy';

			// Update UI
			this.updateClipboardButtons();

			// Show notification
			FileLibrary.showNotification('info', `${this._selectedFiles.length} files copied to clipboard`);
		},

		/**
		 * Cut selected files to clipboard
		 */
		cutSelectedFiles: function() {
			if (this._selectedFiles.length === 0) {
				return;
			}

			this._clipboard.files = [...this._selectedFiles];
			this._clipboard.action = 'cut';

			// Update UI - add 'cut' style to files
			this._selectedFiles.forEach(fileId => {
				$(`.file-item[data-file-id="${fileId}"]`).addClass('file-cut');
			});

			// Update clipboard buttons
			this.updateClipboardButtons();

			// Show notification
			FileLibrary.showNotification('info', `${this._selectedFiles.length} files cut to clipboard`);
		},

		/**
		 * Paste files from clipboard to current folder
		 */
		pasteFiles: function() {
			if (this._clipboard.files.length === 0 || !this._clipboard.action) {
				return;
			}

			// Action depending on clipboard type
			if (this._clipboard.action === 'copy') {
				this.copyFilesToFolder(this._clipboard.files, this._currentFolderId);
			} else if (this._clipboard.action === 'cut') {
				this.moveFilesToFolder(this._clipboard.files, this._currentFolderId);
			}

			// Clear clipboard after paste
			this._clipboard.files = [];
			this._clipboard.action = null;

			// Update UI
			$('.file-cut').removeClass('file-cut');
			this.updateClipboardButtons();
		},

		/**
		 * Update clipboard buttons based on content
		 */
		updateClipboardButtons: function() {
			const hasClipboardContent = this._clipboard.files.length > 0 && this._clipboard.action !== null;

			// Enable/disable paste button
			$('#paste-files-btn').prop('disabled', !hasClipboardContent);

			// Add clipboard count badge if has content
			if (hasClipboardContent) {
				$('#paste-files-btn').html(`<i class="fas fa-paste"></i> Paste (${this._clipboard.files.length})`);
			} else {
				$('#paste-files-btn').html('<i class="fas fa-paste"></i> Paste');
			}
		},

		/**
		 * Move files to a folder
		 * @param {Array} fileIds - Array of file IDs
		 * @param {number} targetFolderId - Target folder ID
		 */
		moveFilesToFolder: function(fileIds, targetFolderId) {
			if (!fileIds || fileIds.length === 0) {
				return;
			}

			// Show loading spinner
			FileLibrary.showLoadingOverlay();

			// AJAX request for each file
			const promises = fileIds.map(fileId => {
				return $.ajax({
					url: FileLibrary.getConfig().baseUrl+'Files_controller/move',
					type: 'POST',
					data: {
						file_id: fileId,
						source_folder_id: this._currentFolderId,
						target_folder_id: targetFolderId
					}
				});
			});

			// Process all promises
			Promise.all(promises)
				.then(results => {
					// Hide loading spinner
					FileLibrary.hideLoadingOverlay();

					// Count successes
					const successCount = results.filter(r => r.success).length;

					if (successCount > 0) {
						FileLibrary.showNotification('success', `${successCount} ${successCount === 1 ? 'file' : 'files'} moved successfully`);

						// Refresh current folder
						this.refreshCurrentFolder();
					}

					// Show errors if any
					const errors = results.filter(r => !r.success);
					if (errors.length > 0) {
						FileLibrary.showNotification('error', `Failed to move ${errors.length} ${errors.length === 1 ? 'file' : 'files'}`);
					}

					// Clear cut styling
					$('.file-cut').removeClass('file-cut');
				})
				.catch(error => {
					// Hide loading spinner
					FileLibrary.hideLoadingOverlay();

					// Show error
					FileLibrary.showNotification('error', 'Error moving files: ' + error);

					// Clear cut styling
					$('.file-cut').removeClass('file-cut');
				});
		},

		/**
		 * Copy files to a folder
		 * @param {Array} fileIds - Array of file IDs
		 * @param {number} targetFolderId - Target folder ID
		 */
		copyFilesToFolder: function(fileIds, targetFolderId) {
			if (!fileIds || fileIds.length === 0) {
				return;
			}

			// Show loading spinner
			FileLibrary.showLoadingOverlay();

			// AJAX request for each file
			const promises = fileIds.map(fileId => {
				return $.ajax({
					url: FileLibrary.getConfig().baseUrl+'Files_controller/copy',
					type: 'POST',
					data: {
						file_id: fileId,
						target_folder_id: targetFolderId
					}
				});
			});

			// Process all promises
			Promise.all(promises)
				.then(results => {
					// Hide loading spinner
					FileLibrary.hideLoadingOverlay();

					// Count successes
					const successCount = results.filter(r => r.success).length;

					if (successCount > 0) {
						FileLibrary.showNotification('success', `${successCount} ${successCount === 1 ? 'file' : 'files'} copied successfully`);

						// Refresh current folder
						this.refreshCurrentFolder();
					}

					// Show errors if any
					const errors = results.filter(r => !r.success);
					if (errors.length > 0) {
						FileLibrary.showNotification('error', `Failed to copy ${errors.length} ${errors.length === 1 ? 'file' : 'files'}`);
					}
				})
				.catch(error => {
					// Hide loading spinner
					FileLibrary.hideLoadingOverlay();

					// Show error
					FileLibrary.showNotification('error', 'Error copying files: ' + error);
				});
		},

		/**
		 * Refresh current folder
		 */
		refreshCurrentFolder: function() {
			this.loadFolderContent(this._currentFolderId);
		},

		/**
		 * Get current folder ID
		 * @returns {number} Current folder ID
		 */
		getCurrentFolderId: function() {
			return this._currentFolderId;
		},

		/**
		 * Get selected files
		 * @returns {Array} Selected file IDs
		 */
		getSelectedFiles: function() {
			return this._selectedFiles;
		}
	};

	// Add to FileLibrary namespace
	FileLibrary.Explorer = Explorer;

})(FileLibrary || {});
