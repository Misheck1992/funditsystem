<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">File Management</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">-</a>
				<span class="breadcrumb-item active">Files</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" style="border: thick darkblue solid;border-radius: 14px;">

<!-- File Library Main View -->
<div class="file-library-container">
	<div class="file-library-header">
		<h1 class="file-library-title">File Library</h1>
		<div class="file-library-actions">
			<div class="btn-group">
				<button id="upload-file-btn" class="btn btn-primary">
					<i class="fas fa-upload"></i> Upload
				</button>
				<button id="create-folder-btn" class="btn btn-outline-secondary">
					<i class="fas fa-folder-plus"></i> New Folder
				</button>
			</div>
		</div>
	</div>

	<div class="file-library-main">
		<!-- Sidebar -->
		<div class="file-library-sidebar d-none d-md-flex">
			<div class="sidebar-section">
				<div class="sidebar-section-title">
					<span>Quick Access</span>
				</div>
				<ul class="sidebar-menu">
					<li class="sidebar-menu-item active">
						<i class="fas fa-home"></i> Home
					</li>
<!--					<li class="sidebar-menu-item">-->
<!--						<i class="fas fa-clock"></i> Recent-->
<!--					</li>-->
<!--					<li class="sidebar-menu-item">-->
<!--						<i class="fas fa-star"></i> Favorites-->
<!--					</li>-->
<!--					<li class="sidebar-menu-item">-->
<!--						<i class="fas fa-share-alt"></i> Shared with me-->
<!--					</li>-->
				</ul>
			</div>

			<div class="sidebar-section">
				<div class="sidebar-section-title">
					<span>System Folders</span>
				</div>
				<ul class="sidebar-menu">
					<li class="sidebar-menu-item">
						<i class="fas fa-file"></i>
						<a href="#" class="folder-tree-item-link" data-folder-id="10">Loan Files</a>
					</li>

					<li class="sidebar-menu-item">
						<i class="fas fa-file"></i>
						<a href="#" class="folder-tree-item-link" data-folder-id="11">Person KYC Files</a>
					</li>

					<li class="sidebar-menu-item">
						<i class="fas fa-file"></i>
						<a href="#" class="folder-tree-item-link" data-folder-id="12">Corporate Files</a>
					</li>
				</ul>
			</div>

			<div class="sidebar-section">
				<div class="sidebar-section-title">
					<span>Folders</span>
					<span class="toggle-icon"><i class="fas fa-chevron-down"></i></span>
				</div>
				<div class="sidebar-section-content">
					<div id="folder-tree">
						<!-- Folder tree will be loaded here via JavaScript -->
						<div class="loading-indicator">
							<i class="fas fa-spinner fa-spin"></i> Loading folders...
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Main Content -->
		<div class="file-library-content">
			<!-- Toolbar -->
			<div class="file-library-toolbar">
				<div class="file-library-toolbar-left">
					<!-- Mobile sidebar toggle -->
					<button id="toggle-sidebar-btn" class="btn btn-sm btn-icon d-md-none mr-2">
						<i class="fas fa-bars"></i>
					</button>

					<!-- Breadcrumb -->
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb mb-0 py-0 bg-transparent" id="folder-breadcrumb">
							<li class="breadcrumb-item active">
								<i class="fas fa-home"></i> Home
							</li>
						</ol>
					</nav>
				</div>

				<div class="file-library-toolbar-right">
					<!-- Search -->
					<div class="search-container mr-3">
						<input type="text" class="form-control form-control-sm search-input" id="file-search-input" placeholder="Search files...">
						<i class="fas fa-search search-icon"></i>
						<i class="fas fa-spinner fa-spin search-loading"></i>
					</div>

					<!-- View options -->
					<div class="btn-group btn-group-sm mr-2">
						<button id="view-mode-grid" class="btn btn-outline-secondary active">
							<i class="fas fa-th"></i>
						</button>
						<button id="view-mode-list" class="btn btn-outline-secondary">
							<i class="fas fa-list"></i>
						</button>
					</div>

					<!-- Sort options -->
					<div class="dropdown mr-2">
						<button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fas fa-sort"></i> Sort
						</button>
						<div class="dropdown-menu dropdown-menu-right" aria-labelledby="sortDropdown">
							<a class="dropdown-item sort-option active" href="#" data-sort-field="date_added">
								Date Added <i class="fas fa-sort-down ml-2"></i>
							</a>
							<a class="dropdown-item sort-option" href="#" data-sort-field="date_modified">
								Date Modified
							</a>
							<a class="dropdown-item sort-option" href="#" data-sort-field="file_name">
								Name
							</a>
							<a class="dropdown-item sort-option" href="#" data-sort-field="file_size">
								Size
							</a>
							<a class="dropdown-item sort-option" href="#" data-sort-field="file_type">
								Type
							</a>
						</div>
					</div>

					<!-- More actions -->
					<div class="dropdown">
						<button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="moreActionsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fas fa-ellipsis-v"></i>
						</button>
						<div class="dropdown-menu dropdown-menu-right" aria-labelledby="moreActionsDropdown">
							<a class="dropdown-item" href="#" id="refresh-folder-btn">
								<i class="fas fa-sync-alt"></i> Refresh
							</a>
							<a class="dropdown-item" href="#" id="select-all-files">
								<i class="fas fa-check-square"></i> Select All
							</a>
							<a class="dropdown-item" href="#" id="toggle-advanced-search">
								<i class="fas fa-search-plus"></i> Advanced Search
							</a>
						</div>
					</div>
				</div>
			</div>

			<!-- Selection Toolbar (Hidden by default) -->
			<div id="selection-toolbar" class="file-library-toolbar" style="display: none;">
				<div class="file-library-toolbar-left">
					<button id="deselect-all-files" class="btn btn-sm btn-outline-secondary">
						<i class="fas fa-times"></i> Cancel
					</button>
					<span class="ml-3">
                        <span id="selected-count">0</span> item(s) selected
                    </span>
				</div>

				<div class="file-library-toolbar-right">
					<button id="download-selected-btn" class="btn btn-sm btn-outline-secondary">
						<i class="fas fa-download"></i> Download
					</button>
					<button id="share-selected-btn" class="btn btn-sm btn-outline-secondary">
						<i class="fas fa-share-alt"></i> Share
					</button>
					<button id="copy-selected-btn" class="btn btn-sm btn-outline-secondary">
						<i class="fas fa-copy"></i> Copy
					</button>
					<button id="cut-selected-btn" class="btn btn-sm btn-outline-secondary">
						<i class="fas fa-cut"></i> Cut
					</button>
					<button id="paste-files-btn" class="btn btn-sm btn-outline-secondary" disabled>
						<i class="fas fa-paste"></i> Paste
					</button>
					<button id="delete-selected-btn" class="btn btn-sm btn-outline-danger">
						<i class="fas fa-trash"></i> Delete
					</button>
				</div>
			</div>

			<!-- Advanced Search Form (Hidden by default) -->
			<div id="advanced-search-form" class="d-none">
				<div class="card mb-0">
					<div class="card-body">
						<h5 class="card-title">Advanced Search</h5>
						<form>
							<div class="form-row">
								<div class="form-group col-md-6">
									<label for="search-query">Keywords</label>
									<input type="text" class="form-control" id="search-query" placeholder="Search...">
								</div>
								<div class="form-group col-md-3">
									<label for="search-file-type">File Type</label>
									<select class="form-control" id="search-file-type">
										<option value="">All Types</option>
										<option value="image">Images</option>
										<option value="document">Documents</option>
										<option value="spreadsheet">Spreadsheets</option>
									</select>
								</div>
								<div class="form-group col-md-3">
									<label for="search-file-category">Category</label>
									<select class="form-control" id="search-file-category">
										<option value="">All Categories</option>
										<option value="general_files">General</option>
										<option value="loan_collateral">Loan Collateral</option>
										<option value="loan_files">Loan Files</option>
										<option value="kyc_data">KYC Data</option>
									</select>
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-3">
									<label for="search-date-from">Date From</label>
									<input type="date" class="form-control" id="search-date-from">
								</div>
								<div class="form-group col-md-3">
									<label for="search-date-to">Date To</label>
									<input type="date" class="form-control" id="search-date-to">
								</div>
								<div class="form-group col-md-3">
									<label>File Source</label>
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" id="search-my-files">
										<label class="custom-control-label" for="search-my-files">My Files</label>
									</div>
									<div class="custom-control custom-checkbox">
										<input type="checkbox" class="custom-control-input" id="search-shared-with-me">
										<label class="custom-control-label" for="search-shared-with-me">Shared with me</label>
									</div>
								</div>
								<div class="form-group col-md-3 d-flex align-items-end">
									<button type="button" class="btn btn-primary mr-2" id="search-btn">
										<i class="fas fa-search"></i> Search
									</button>
									<button type="button" class="btn btn-outline-secondary" id="search-reset-btn">
										<i class="fas fa-undo"></i> Reset
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>

			<!-- Current Folder Info -->
			<div class="file-folder-info p-2 bg-light border-top border-bottom">
				<h5 id="current-folder-name" class="m-0 d-inline-block">Home</h5>
				<div id="folder-meta" class="text-muted small d-inline-block ml-3">
					<span><i class="fas fa-home"></i> Root directory</span>
				</div>
			</div>

			<!-- File Container -->
			<div id="file-container" class="file-container">
				<!-- Content will be loaded via JavaScript -->
				<div class="loading-indicator">
					<i class="fas fa-spinner fa-spin"></i> Loading...
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Drop Overlay -->
<div id="drop-overlay" style="display: none;">
	<i class="fas fa-cloud-upload-alt"></i>
	<p>Drop files here to upload</p>
</div>

<!-- File Upload Input (Hidden) -->
<input type="file" id="file-upload-input" style="display: none;" multiple>

<!-- Create Folder Modal -->
<div class="modal fade" id="create-folder-modal" tabindex="-1" role="dialog" aria-labelledby="create-folder-title" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="create-folder-title">Create New Folder</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="create-folder-form">
					<div class="form-group">
						<label for="folder-name-input">Folder Name</label>
						<input type="text" class="form-control" id="folder-name-input" placeholder="Enter folder name" required>
					</div>
					<div class="form-group">
						<label>Parent Folder</label>
						<p class="form-control-static" id="parent-folder-name">Home</p>
						<input type="hidden" id="parent-folder-id" value="0">
					</div>
					<div class="form-group">
						<label for="folder-description-input">Description (Optional)</label>
						<textarea class="form-control" id="folder-description-input" rows="3"></textarea>
					</div>
					<div class="form-group">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" id="folder-is-public">
							<label class="custom-control-label" for="folder-is-public">Make this folder public</label>
							<small class="form-text text-muted">Public folders can be viewed by all users</small>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary" id="create-folder-submit">Create Folder</button>
			</div>
		</div>
	</div>
</div>

<!-- File Preview Modal -->
<div class="modal fade" id="file-preview-modal" tabindex="-1" role="dialog" aria-labelledby="file-preview-title" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="file-preview-title">
					<span id="preview-file-name">File Preview</span>
				</h5>
				<div class="btn-group ml-2">
					<button type="button" class="btn btn-sm btn-outline-secondary" id="preview-prev-btn">
						<i class="fas fa-chevron-left"></i>
					</button>
					<button type="button" class="btn btn-sm btn-outline-secondary" id="preview-next-btn">
						<i class="fas fa-chevron-right"></i>
					</button>
				</div>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div id="preview-container">
					<div class="preview-loading">
						<i class="fas fa-spinner fa-spin"></i> Loading preview...
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="preview-download-btn">
					<i class="fas fa-download"></i> Download
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Upload Progress Modal -->
<div class="modal fade" id="upload-progress-modal" tabindex="-1" role="dialog" aria-labelledby="upload-progress-title" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="upload-progress-title">Uploading Files</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div id="upload-progress-container">
					<!-- Upload progress items will be added here -->
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal" disabled>Close</button>
			</div>
		</div>
	</div>
</div>

<!-- File Share Modal -->
<div class="modal fade" id="file-share-modal" tabindex="-1" role="dialog" aria-labelledby="file-share-title" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="file-share-title">Share File</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="file-share-form">
					<div class="form-group">
						<label for="share-file-name">File</label>
						<p class="form-control-static" id="share-file-name">file.pdf</p>
						<input type="hidden" id="share-file-id" value="0">
					</div>
					<div class="form-group">
						<label for="share-user-select">Share with</label>
						<select class="form-control" id="share-user-select">
							<option value="">Select user...</option>
							<!-- User options will be loaded dynamically -->
						</select>
					</div>
					<div class="form-group">
						<label>Permission</label>
						<div class="custom-control custom-radio">
							<input type="radio" id="permission-view" name="permission-level" class="custom-control-input" value="view" checked>
							<label class="custom-control-label" for="permission-view">View only</label>
						</div>
						<div class="custom-control custom-radio">
							<input type="radio" id="permission-edit" name="permission-level" class="custom-control-input" value="edit">
							<label class="custom-control-label" for="permission-edit">Can edit</label>
						</div>
					</div>
				</form>

				<hr>

				<h6>Currently shared with</h6>
				<div class="user-list">
					<!-- Shared user items will be added here -->
					<div id="share-user-list">
						<div class="text-center text-muted py-3">
							<i class="fas fa-info-circle"></i> Not shared with anyone
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="share-submit-btn">Share</button>
			</div>
		</div>
	</div>
</div>

<!-- Rename File Modal -->
<div class="modal fade" id="rename-file-modal" tabindex="-1" role="dialog" aria-labelledby="rename-file-title" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="rename-file-title">Rename File</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="rename-file-form">
					<div class="form-group">
						<label for="rename-file-input">New Name</label>
						<input type="text" class="form-control" id="rename-file-input" required>
						<input type="hidden" id="rename-file-id" value="0">
					</div>
					<div class="form-group">
						<label for="rename-file-description">Description (Optional)</label>
						<textarea class="form-control" id="rename-file-description" rows="3"></textarea>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary" id="rename-file-submit">Save Changes</button>
			</div>
		</div>
	</div>
</div>

<!-- Confirm Delete Modal -->
<div class="modal fade" id="confirm-delete-modal" tabindex="-1" role="dialog" aria-labelledby="confirm-delete-title" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="confirm-delete-title">Confirm Delete</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p id="confirm-delete-message">Are you sure you want to delete this item?</p>
				<p class="text-danger">This action cannot be undone.</p>
				<input type="hidden" id="delete-item-id" value="0">
				<input type="hidden" id="delete-item-type" value="">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-danger" id="confirm-delete-btn">Delete</button>
			</div>
		</div>
	</div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="upload-modal" tabindex="-1" role="dialog" aria-labelledby="upload-modal-title" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="upload-modal-title">Upload Files</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="upload-form">
					<div class="form-group">
						<label>Selected Files</label>
						<div id="upload-file-list" class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
							<!-- Selected files will be shown here -->
						</div>
					</div>
					<div class="form-group">
						<label for="file-category">File Category</label>
						<select class="form-control" id="file-category">
							<option value="general_files">General Files</option>
							<option value="loan_collateral">Loan Collateral</option>
							<option value="loan_files">Loan Files</option>
							<option value="kyc_data">KYC Data</option>
						</select>
					</div>
					<div class="form-group">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" id="file-is-public">
							<label class="custom-control-label" for="file-is-public">Make files public</label>
							<small class="form-text text-muted">Public files can be viewed by all users</small>
						</div>
					</div>

					<!-- Hidden inputs for owner data -->
					<input type="hidden" id="file-owner-type" value="system_user">
					<input type="hidden" id="file-owner-id" value="<?php echo $this->session->userdata('user_id'); ?>">
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
				<button type="button" class="btn btn-primary" id="upload-files-btn">Upload</button>
			</div>
		</div>
	</div>
</div>

<!-- Notifications Container -->
<div class="toast-container"></div>

<!-- Context Menu (Positioned by JavaScript) -->
<div class="context-menu" id="context-menu">
	<!-- Context menu items will be dynamically added based on context -->
</div>


</div>
	</div>
</div>
