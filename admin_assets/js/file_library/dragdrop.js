/**
 * Finance Real - File Library Module
 * DragDrop.js - Handles drag and drop functionality
 */

// Extend the FileLibrary namespace
(function(FileLibrary) {
	/**
	 * Drag and Drop
	 * Handles drag and drop functionality for files and folders
	 */
	const DragDrop = {
		// Store drag data
		_draggedItems: [],
		_dragType: null, // 'file' or 'folder'
		_dragAction: null, // 'move' or 'copy'

		/**
		 * Initialize drag and drop functionality
		 */
		init: function() {
			// Make file items draggable
			this.initDraggableItems();

			// Make folders droppable
			this.initDroppableTargets();

			// Make file container a drop zone for file uploads
			this.initFileDropZone();

			console.log('Drag and Drop initialized');
		},

		/**
		 * Initialize draggable items
		 */
		initDraggableItems: function() {
			// Make file items draggable
			$(document).on('mousedown', '.file-item', (e) => {
				// Only enable drag if not clicking on actions or checkbox
				if ($(e.target).closest('.file-actions, .file-select').length === 0) {
					$(e.currentTarget).attr('draggable', 'true');
				}
			});

			// Handle dragstart for files
			$(document).on('dragstart', '.file-item', (e) => {
				this.handleFileDragStart(e);
			});

			// Handle dragend for files
			$(document).on('dragend', '.file-item', (e) => {
				this.handleDragEnd(e);
			});

			// Make folder items draggable (excluding the current folder's parent)
			$(document).on('mousedown', '.folder-item:not(.parent-folder)', (e) => {
				// Only enable drag if not clicking on actions
				if ($(e.target).closest('.folder-actions').length === 0) {
					$(e.currentTarget).attr('draggable', 'true');
				}
			});

			// Handle dragstart for folders
			$(document).on('dragstart', '.folder-item', (e) => {
				this.handleFolderDragStart(e);
			});

			// Handle dragend for folders
			$(document).on('dragend', '.folder-item', (e) => {
				this.handleDragEnd(e);
			});
		},

		/**
		 * Initialize droppable targets
		 */
		initDroppableTargets: function() {
			// Folder items as drop targets
			$(document).on('dragover', '.folder-item', (e) => {
				e.preventDefault();
				e.stopPropagation();

				// Add highlight class
				$(e.currentTarget).addClass('folder-drag-over');
			});

			$(document).on('dragleave', '.folder-item', (e) => {
				e.preventDefault();
				e.stopPropagation();

				// Remove highlight class
				$(e.currentTarget).removeClass('folder-drag-over');
			});

			$(document).on('drop', '.folder-item', (e) => {
				e.preventDefault();
				e.stopPropagation();

				// Remove highlight class
				$(e.currentTarget).removeClass('folder-drag-over');

				// Get target folder ID
				const targetFolderId = $(e.currentTarget).data('folder-id');

				// Handle drop
				this.handleDropOnFolder(e, targetFolderId);
			});

			// Folder tree items as drop targets
			$(document).on('dragover', '.folder-tree-item-link', (e) => {
				e.preventDefault();
				e.stopPropagation();

				// Add highlight class
				$(e.currentTarget).addClass('folder-tree-drag-over');
			});

			$(document).on('dragleave', '.folder-tree-item-link', (e) => {
				e.preventDefault();
				e.stopPropagation();

				// Remove highlight class
				$(e.currentTarget).removeClass('folder-tree-drag-over');
			});

			$(document).on('drop', '.folder-tree-item-link', (e) => {
				e.preventDefault();
				e.stopPropagation();

				// Remove highlight class
				$(e.currentTarget).removeClass('folder-tree-drag-over');

				// Get target folder ID
				const targetFolderId = $(e.currentTarget).data('folder-id');

				// Handle drop
				this.handleDropOnFolder(e, targetFolderId);
			});

			// Root container as drop target
			$(document).on('dragover', '#file-container', (e) => {
				// Don't interfere with other dragover handlers
				if ($(e.target).closest('.folder-item, .file-item').length > 0) {
					return;
				}

				e.preventDefault();
				e.stopPropagation();

				// Check if the drag contains files from desktop
				if (e.originalEvent.dataTransfer.types &&
					e.originalEvent.dataTransfer.types.includes('Files')) {
					$('#file-container').addClass('drag-over');
					$('#drop-overlay').show();
				}
			});

			$(document).on('dragleave', '#file-container', (e) => {
				// Don't interfere with other dragleave handlers
				if ($(e.target).closest('.folder-item, .file-item').length > 0) {
					return;
				}

				e.preventDefault();
				e.stopPropagation();

				// Check if leaving the container (not entering a child element)
				if ($(e.target).is('#file-container')) {
					$('#file-container').removeClass('drag-over');
					$('#drop-overlay').hide();
				}
			});

			$(document).on('drop', '#file-container', (e) => {
				// Don't interfere with other drop handlers
				if ($(e.target).closest('.folder-item, .file-item').length > 0) {
					return;
				}

				e.preventDefault();
				e.stopPropagation();

				$('#file-container').removeClass('drag-over');
				$('#drop-overlay').hide();

				// Handle drop of internal files to root folder
				if (this._draggedItems.length > 0 && this._dragType) {
					this.handleDropOnRoot(e);
				}
				// Handle drop of files from desktop
				else if (e.originalEvent.dataTransfer.files &&
					e.originalEvent.dataTransfer.files.length > 0) {
					this.handleExternalFileDrop(e);
				}
			});
		},

		/**
		 * Initialize file drop zone for uploads
		 */
		initFileDropZone: function() {
			const fileContainer = document.getElementById('file-container');
			if (!fileContainer) return;

			// Create drop overlay if it doesn't exist
			if ($('#drop-overlay').length === 0) {
				$('body').append(`
                    <div id="drop-overlay">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Drop files here to upload</p>
                    </div>
                `);
			}
		},

		/**
		 * Handle file item drag start
		 * @param {Event} e - The dragstart event
		 */
		handleFileDragStart: function(e) {
			const fileId = $(e.currentTarget).data('file-id');

			// Set drag action based on modifier key
			this._dragAction = e.ctrlKey || e.metaKey ? 'copy' : 'move';

			// If item is not selected, select only this item
			if (!FileLibrary.Explorer.isFileSelected(fileId)) {
				FileLibrary.Explorer.selectFile(fileId);
			}

			// Get all selected file IDs
			this._draggedItems = FileLibrary.Explorer.getSelectedFiles();
			this._dragType = 'file';

			// Set drag image
			if (this._draggedItems.length > 1) {
				const dragImage = this.createDragImage(this._draggedItems.length);
				e.originalEvent.dataTransfer.setDragImage(dragImage, 15, 15);
			}

			// Set data for drag (needed for Firefox)
			e.originalEvent.dataTransfer.setData('text/plain', JSON.stringify({
				type: 'file',
				items: this._draggedItems,
				action: this._dragAction
			}));

			// Add drag effect
			e.originalEvent.dataTransfer.effectAllowed = this._dragAction === 'copy' ? 'copy' : 'move';

			// Add visual feedback
			if (this._dragAction === 'move') {
				$('.file-item.selected').addClass('dragging');
			}
		},

		/**
		 * Handle folder item drag start
		 * @param {Event} e - The dragstart event
		 */
		handleFolderDragStart: function(e) {
			const folderId = $(e.currentTarget).data('folder-id');

			this._draggedItems = [folderId];
			this._dragType = 'folder';
			this._dragAction = 'move'; // Always move folders (no copy)

			// Set data for drag
			e.originalEvent.dataTransfer.setData('text/plain', JSON.stringify({
				type: 'folder',
				items: this._draggedItems,
				action: 'move'
			}));

			// Add drag effect
			e.originalEvent.dataTransfer.effectAllowed = 'move';

			// Add visual feedback
			$(e.currentTarget).addClass('dragging');
		},

		/**
		 * Handle drag end
		 * @param {Event} e - The dragend event
		 */
		handleDragEnd: function(e) {
			// Remove visual feedback
			$('.file-item.dragging, .folder-item.dragging').removeClass('dragging');
			$('.folder-drag-over, .folder-tree-drag-over').removeClass('folder-drag-over folder-tree-drag-over');

			// Clear drag data
			this._draggedItems = [];
			this._dragType = null;
			this._dragAction = null;
		},

		/**
		 * Handle drop on folder
		 * @param {Event} e - The drop event
		 * @param {number} targetFolderId - Target folder ID
		 */
		handleDropOnFolder: function(e, targetFolderId) {
			// Check if we have valid drag data
			if (this._draggedItems.length === 0 || !this._dragType) {
				// Check for external files
				if (e.originalEvent.dataTransfer.files &&
					e.originalEvent.dataTransfer.files.length > 0) {
					this.handleExternalFileDrop(e, targetFolderId);
				}
				return;
			}

			// Handle based on drag type
			if (this._dragType === 'file') {
				if (this._dragAction === 'move') {
					FileLibrary.Explorer.moveFilesToFolder(this._draggedItems, targetFolderId);
				} else if (this._dragAction === 'copy') {
					FileLibrary.Explorer.copyFilesToFolder(this._draggedItems, targetFolderId);
				}
			} else if (this._dragType === 'folder') {
				this.moveFolderToFolder(this._draggedItems[0], targetFolderId);
			}
		},

		/**
		 * Handle drop on root container
		 * @param {Event} e - The drop event
		 */
		handleDropOnRoot: function(e) {
			// Only proceed if we have valid drag data
			if (this._draggedItems.length === 0 || !this._dragType) {
				return;
			}

			const rootFolderId = 0; // Root folder ID

			// Handle based on drag type
			if (this._dragType === 'file') {
				if (this._dragAction === 'move') {
					FileLibrary.Explorer.moveFilesToFolder(this._draggedItems, rootFolderId);
				} else if (this._dragAction === 'copy') {
					FileLibrary.Explorer.copyFilesToFolder(this._draggedItems, rootFolderId);
				}
			} else if (this._dragType === 'folder') {
				this.moveFolderToFolder(this._draggedItems[0], rootFolderId);
			}
		},

		/**
		 * Handle external file drop (from desktop)
		 * @param {Event} e - The drop event
		 * @param {number} targetFolderId - Target folder ID (optional)
		 */
		handleExternalFileDrop: function(e, targetFolderId = null) {
			const files = e.originalEvent.dataTransfer.files;

			if (files && files.length > 0) {
				// Set target folder
				if (targetFolderId !== null) {
					// Store target folder ID temporarily
					sessionStorage.setItem('fileLibrary_uploadFolderId', targetFolderId);
				} else {
					// Use current folder
					sessionStorage.setItem('fileLibrary_uploadFolderId', FileLibrary.Explorer.getCurrentFolderId());
				}

				// Pass to uploader
				FileLibrary.Uploader.handleFileSelect(files);
			}
		},

		/**
		 * Move folder to another folder
		 * @param {number} folderId - Source folder ID
		 * @param {number} targetFolderId - Target folder ID
		 */
		moveFolderToFolder: function(folderId, targetFolderId) {
			// Don't move to itself
			if (folderId === targetFolderId) {
				FileLibrary.showNotification('error', 'Cannot move folder to itself');
				return;
			}

			// Show loading spinner
			FileLibrary.showLoadingOverlay();

			// AJAX request
			$.ajax({
				url: FileLibrary.getConfig().baseUrl + 'move_folder',
				type: 'POST',
				data: {
					folder_id: folderId,
					target_parent_id: targetFolderId
				},
				success: function(response) {
					// Hide loading spinner
					FileLibrary.hideLoadingOverlay();

					if (response.success) {
						FileLibrary.showNotification('success', 'Folder moved successfully');

						// Refresh folder tree
						FileLibrary.Explorer.loadFolderTree();

						// Refresh current folder if needed
						const currentFolderId = FileLibrary.Explorer.getCurrentFolderId();
						if (currentFolderId === folderId || currentFolderId === targetFolderId) {
							FileLibrary.Explorer.refreshCurrentFolder();
						}
					} else {
						FileLibrary.showNotification('error', response.message || 'Failed to move folder');
					}
				},
				error: function(xhr, status, error) {
					// Hide loading spinner
					FileLibrary.hideLoadingOverlay();

					FileLibrary.showNotification('error', 'Error moving folder: ' + error);
				}
			});
		},

		/**
		 * Create a drag image for multiple items
		 * @param {number} count - Number of items
		 * @returns {HTMLElement} - The drag image element
		 */
		createDragImage: function(count) {
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
	};

	// Add to FileLibrary namespace
	FileLibrary.DragDrop = DragDrop;

})(FileLibrary || {});
