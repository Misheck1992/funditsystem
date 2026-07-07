<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">Customer view</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">-</a>
				<span class="breadcrumb-item active">Customer Details</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
			<div class="row">
				<div class="col-lg-4 border-right">
					<h2>Personal Info</h2>
					<hr>
					<table>
						<tr><td style="text-align: right;padding-right: 10px;" >ClientId</td><td><?php echo $ClientId; ?></td></tr>
						<tr><td style="text-align: right;padding-right: 10px;" >Title</td><td><?php echo $Title; ?></td></tr>
						<tr><td style="text-align: right;padding-right: 10px;" >Firstname</td><td><?php echo $Firstname; ?></td></tr>
						<tr><td style="text-align: right;padding-right: 10px;" >Middle name</td><td><?php echo $Middlename; ?></td></tr>
						<tr><td style="text-align: right;padding-right: 10px;" >Lastname</td><td><?php echo $Lastname; ?></td></tr>
						<tr><td style="text-align: right;padding-right: 10px;" >Gender</td><td><?php echo $Gender; ?></td></tr>
						<tr><td style="text-align: right;padding-right: 10px;" >DateOfBirth</td><td><?php echo $DateOfBirth; ?></td></tr>
						<tr><td style="text-align: right;padding-right: 10px;" >EmailAddress</td><td><?php echo $EmailAddress; ?></td></tr>
						<tr><td style="text-align: right;padding-right: 10px;" >PhoneNumber</td><td><?php echo $PhoneNumber; ?></td></tr>
						<tr><td style="text-align: right;padding-right: 10px;" >AddressLine1</td><td><?php echo $AddressLine1; ?></td></tr>
						<tr><td style="text-align: right;padding-right: 10px;" >AddressLine2</td><td><?php echo $AddressLine2; ?></td></tr>
						<tr><td style="text-align: right;padding-right: 10px;" >AddressLine3</td><td><?php echo $AddressLine3; ?></td></tr>
						<tr><td style="text-align: right;padding-right: 10px;" >Province</td><td><?php echo $Province; ?></td></tr>
						<tr><td style="text-align: right;padding-right: 10px;" >City</td><td><?php echo $City; ?></td></tr>
						<tr><td style="text-align: right;padding-right: 10px;" >Country</td><td><?php echo $Country; ?></td></tr>
						<tr><td style="text-align: right;padding-right: 10px;" >ResidentialStatus</td><td><?php echo $ResidentialStatus; ?></td></tr>
						<tr><td style="text-align: right;padding-right: 10px;" >Profession</td><td><?php echo $Profession; ?></td></tr>
						<tr><td style="text-align: right;padding-right: 10px;" >SourceOfIncome</td><td><?php echo $SourceOfIncome; ?></td></tr>
						<tr><td style="text-align: right;padding-right: 10px;" >GrossMonthlyIncome</td><td><?php echo $GrossMonthlyIncome; ?></td></tr>
						<tr><td style="text-align: right;padding-right: 10px;" >Branch</td><td><?php echo $Branch; ?></td></tr>
						<tr><td style="text-align: right;padding-right: 10px;" >LastUpdatedOn</td><td><?php echo $LastUpdatedOn; ?></td></tr>
						<tr><td style="text-align: right;padding-right: 10px;" >CreatedOn</td><td><?php echo $CreatedOn; ?></td></tr>

					</table>



				</div>
				<div class="col-lg-7 border-right">
					<h2>KYC</h2>
					<hr>

					<?php

					$row  = $this->Proofofidentity_model->check($ClientId);
					if(empty($row)){
						echo "<font color='red'>Sorry no kyc</font>";

						?>
						<input type="text" value="<?php echo $ClientId?>" id="cid" hidden >
						<button id="add_kyc" class="btn btn-success" style="background: #059669; border: none; padding: 0.6rem 1.25rem; border-radius: 6px; font-weight: 600;">
							<i class="fa fa-plus mr-2"></i>Add KYC
						</button>
							<?php
					}else{

						?>
						<input type="text" value="<?php echo $ClientId?>" id="cid" hidden >
						<button id="edit_kyc" class="btn btn-primary" style="background: #1e3a5f; border: none; padding: 0.6rem 1.25rem; border-radius: 6px; font-weight: 600;">
							<i class="fa fa-edit mr-2"></i>Edit KYC
						</button>

						<br>
						<table class="table">
							<tr><td>IDType</td><td><?php echo $row->IDType; ?></td></tr>
							<tr><td>IDNumber</td><td><?php echo $row->IDNumber; ?></td></tr>
							<tr><td>IssueDate</td><td><?php echo $row->IssueDate; ?></td></tr>
							<tr><td>Date Added </td><td><?php echo $row->Stamp; ?></td></tr>
							<tr><td>ClientId</td><td><?php echo $row->ClientId; ?></td></tr>


						</table>
						<table>
							<thead>
							<tr>
								<th>Photograph</th>
								<th>Signature</th>
								<th>Id Back</th>
								<th>Id Front</th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td>
									<img src="<?php echo base_url('uploads/').$row->photograph?>" alt="" height="75" width="150" onclick="image_preview('<?php echo $row->photograph?>')">
								</td>
								<td>
									<img src="<?php echo base_url('uploads/').$row->signature?>" alt="" height="75" width="150" onclick="image_preview('<?php echo $row->signature?>')">
								</td>
								<td>
									<img src="<?php echo base_url('uploads/').$row->Id_back?>" alt="" height="75" width="150" onclick="image_preview('<?php echo $row->Id_back?>')">
								</td>
								<td>
									<img src="<?php echo base_url('uploads/').$row->id_front?>" alt="" height="75" width="150" onclick="image_preview('<?php echo $row->id_front?>')">
								</td>
							</tr>
							</tbody>
						</table>
						<?php
					}

					?>
				</div>
				<div class="col-lg-1">
					<a href="<?php echo base_url('individual_customers/report/').$id?>"><i class="fa fa-file-pdf fa-2x text-danger"></i>Report</a>
					<hr>

				</div>
			</div>
		</div>
	</div>

</div>
<!-- Modern KYC Edit Modal -->
<style>
.kyc-modal .modal-content {
	border: none;
	border-radius: 12px;
	box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}
.kyc-modal .modal-header {
	background: #1e3a5f;
	border-radius: 12px 12px 0 0;
	padding: 1.25rem 1.5rem;
	border: none;
}
.kyc-modal .modal-title {
	color: #fff;
	font-weight: 600;
	font-size: 1.1rem;
}
.kyc-modal .modal-header .close {
	color: #fff;
	opacity: 0.8;
	text-shadow: none;
	font-size: 1.5rem;
}
.kyc-modal .modal-header .close:hover {
	opacity: 1;
}
.kyc-modal .modal-body {
	padding: 1.5rem;
}
.kyc-modal .form-group label {
	font-weight: 600;
	color: #374151;
	margin-bottom: 0.5rem;
	font-size: 0.85rem;
}
.kyc-modal .form-control {
	border: 1px solid #d1d5db;
	border-radius: 6px;
	padding: 0.6rem 0.875rem;
	font-size: 0.9rem;
	transition: all 0.2s ease;
}
.kyc-modal .form-control:focus {
	border-color: #3b82f6;
	box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
.kyc-modal .upload-card {
	border: 2px dashed #d1d5db;
	border-radius: 8px;
	padding: 1rem;
	text-align: center;
	transition: all 0.2s ease;
	background: #f9fafb;
	cursor: pointer;
	position: relative;
	overflow: visible;
	min-height: 80px;
}
.kyc-modal .upload-card input[type="file"] {
	display: none;
}
.kyc-modal .upload-card:hover {
	border-color: #3b82f6;
	background: #eff6ff;
}
.kyc-modal .upload-card.uploading {
	border-color: #3b82f6;
	background: #eff6ff;
}
.kyc-modal .upload-card .upload-icon {
	font-size: 1.75rem;
	color: #9ca3af;
	margin-bottom: 0.5rem;
}
.kyc-modal .upload-card:hover .upload-icon {
	color: #3b82f6;
}
.kyc-modal .upload-card .upload-text {
	font-size: 0.8rem;
	color: #6b7280;
	margin-bottom: 0;
}
.kyc-modal .upload-preview {
	margin-top: 0.75rem;
	position: relative;
}
.kyc-modal .upload-preview img {
	max-width: 100%;
	max-height: 100px;
	border-radius: 6px;
	object-fit: cover;
	border: 1px solid #e5e7eb;
}
.kyc-modal .progress-container {
	margin-top: 0.75rem;
	display: none;
}
.kyc-modal .progress {
	height: 6px;
	border-radius: 3px;
	background: #e5e7eb;
	overflow: hidden;
}
.kyc-modal .progress-bar {
	background: #3b82f6;
	transition: width 0.3s ease;
}
.kyc-modal .progress-text {
	font-size: 0.75rem;
	color: #6b7280;
	margin-top: 0.25rem;
	text-align: center;
}
.kyc-modal .upload-success {
	color: #059669;
	font-size: 0.8rem;
	margin-top: 0.5rem;
	display: none;
}
.kyc-modal .upload-success i {
	margin-right: 0.25rem;
}
.kyc-modal .modal-footer {
	border: none;
	padding: 1rem 1.5rem 1.5rem;
	gap: 0.75rem;
}
.kyc-modal .btn-save {
	background: #1e3a5f;
	border: none;
	padding: 0.65rem 1.5rem;
	border-radius: 6px;
	font-weight: 600;
	transition: all 0.2s ease;
}
.kyc-modal .btn-save:hover {
	background: #153050;
}
.kyc-modal .btn-cancel {
	background: #f3f4f6;
	color: #374151;
	border: 1px solid #d1d5db;
	padding: 0.65rem 1.5rem;
	border-radius: 6px;
	font-weight: 600;
	transition: all 0.2s ease;
}
.kyc-modal .btn-cancel:hover {
	background: #e5e7eb;
}
.kyc-modal .section-title {
	font-size: 0.85rem;
	font-weight: 700;
	color: #1e3a5f;
	text-transform: uppercase;
	letter-spacing: 0.05em;
	margin-bottom: 1rem;
	padding-bottom: 0.5rem;
	border-bottom: 2px solid #e5e7eb;
}
</style>

<div class="modal fade kyc-modal" id="kyc_modal_edit" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><i class="fa fa-id-card mr-2"></i>Update KYC Information</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form action="<?php echo base_url('Proofofidentity/update_action')?>" method="post" id="kycForm">
				<div class="modal-body">
					<input type="hidden" name="ClientId" id="Client">
					<input type="hidden" name="id" value="<?php echo $row->id ?>">

					<!-- ID Information Section -->
					<div class="section-title"><i class="fa fa-info-circle mr-2"></i>Identification Details</div>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label><i class="fa fa-id-badge mr-1"></i>ID Type</label>
								<select class="form-control" name="IDType" id="IDType" required>
									<option value="">-- Select ID Type --</option>
									<option value="NATIONAL_IDENTITY_CARD" <?php if($row->IDType=="NATIONAL_IDENTITY_CARD"){ echo "selected";}?>>National Identity Card</option>
									<option value="DRIVING_LISENCE" <?php if($row->IDType=="DRIVING_LISENCE"){ echo "selected";}?>>Driving Licence</option>
									<option value="PASSPORT" <?php if($row->IDType=="PASSPORT"){ echo "selected";}?>>Passport</option>
									<option value="WORK_PERMIT" <?php if($row->IDType=="WORK_PERMIT"){ echo "selected";}?>>Work Permit</option>
									<option value="VOTER_REGISTRATION" <?php if($row->IDType=="VOTER_REGISTRATION"){ echo "selected";}?>>Voter Registration</option>
									<option value="PUBLIC_STATE_OFFICIAL_LETTER" <?php if($row->IDType=="PUBLIC_STATE_OFFICIAL_LETTER"){ echo "selected";}?>>Official Letter</option>
								</select>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label><i class="fa fa-hashtag mr-1"></i>ID Number</label>
								<input type="text" class="form-control" name="IDNumber" id="IDNumber" placeholder="Enter ID Number" value="<?php echo $row->IDNumber ?>" required>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label><i class="fa fa-calendar mr-1"></i>Issue Date</label>
								<input type="date" class="form-control" name="IssueDate" id="IssueDate" value="<?php echo $row->IssueDate?>" required>
							</div>
						</div>
					</div>

					<!-- Document Uploads Section -->
					<div class="section-title mt-4"><i class="fa fa-cloud-upload-alt mr-2"></i>Document Uploads</div>
					<div class="row">
						<!-- ID Front -->
						<div class="col-md-6 mb-3">
							<div class="form-group">
								<label><i class="fa fa-image mr-1"></i>ID Front</label>
								<div class="upload-card" id="id_front_card" onclick="document.getElementById('id_front').click()">
									<input type="file" id="id_front" accept="image/*" onchange="uploadWithProgress('id_front')">
									<div class="upload-icon"><i class="fa fa-cloud-upload-alt"></i></div>
									<p class="upload-text">Click to upload front of ID</p>
								</div>
								<input type="hidden" id="id_front1" name="id_front" value="<?php echo $row->id_front ?>" required>
								<div class="progress-container" id="id_front_progress">
									<div class="progress">
										<div class="progress-bar" role="progressbar" style="width: 0%"></div>
									</div>
									<div class="progress-text">Uploading... 0%</div>
								</div>
								<div class="upload-success" id="id_front_success"><i class="fa fa-check-circle"></i>Upload complete!</div>
								<div class="upload-preview" id="id_front2">
									<?php if(!empty($row->id_front)): ?>
									<img src="<?php echo base_url('uploads/').$row->id_front?>" alt="ID Front">
									<?php endif; ?>
								</div>
							</div>
						</div>

						<!-- ID Back -->
						<div class="col-md-6 mb-3">
							<div class="form-group">
								<label><i class="fa fa-image mr-1"></i>ID Back</label>
								<div class="upload-card" id="Id_back_card" onclick="document.getElementById('Id_back').click()">
									<input type="file" id="Id_back" accept="image/*" onchange="uploadWithProgress('Id_back')">
									<div class="upload-icon"><i class="fa fa-cloud-upload-alt"></i></div>
									<p class="upload-text">Click to upload back of ID</p>
								</div>
								<input type="hidden" id="Id_back1" name="Id_back" value="<?php echo $row->Id_back ?>" required>
								<div class="progress-container" id="Id_back_progress">
									<div class="progress">
										<div class="progress-bar" role="progressbar" style="width: 0%"></div>
									</div>
									<div class="progress-text">Uploading... 0%</div>
								</div>
								<div class="upload-success" id="Id_back_success"><i class="fa fa-check-circle"></i>Upload complete!</div>
								<div class="upload-preview" id="Id_back2">
									<?php if(!empty($row->Id_back)): ?>
									<img src="<?php echo base_url('uploads/').$row->Id_back?>" alt="ID Back">
									<?php endif; ?>
								</div>
							</div>
						</div>

						<!-- Photograph -->
						<div class="col-md-6 mb-3">
							<div class="form-group">
								<label><i class="fa fa-user-circle mr-1"></i>Photograph</label>
								<div class="upload-card" id="photograph_card" onclick="document.getElementById('photograph').click()">
									<input type="file" id="photograph" accept="image/*" onchange="uploadWithProgress('photograph')">
									<div class="upload-icon"><i class="fa fa-camera"></i></div>
									<p class="upload-text">Click to upload photograph</p>
								</div>
								<input type="hidden" id="photograph1" name="photograph" value="<?php echo $row->photograph ?>" required>
								<div class="progress-container" id="photograph_progress">
									<div class="progress">
										<div class="progress-bar" role="progressbar" style="width: 0%"></div>
									</div>
									<div class="progress-text">Uploading... 0%</div>
								</div>
								<div class="upload-success" id="photograph_success"><i class="fa fa-check-circle"></i>Upload complete!</div>
								<div class="upload-preview" id="photograph2">
									<?php if(!empty($row->photograph)): ?>
									<img src="<?php echo base_url('uploads/').$row->photograph?>" alt="Photograph">
									<?php endif; ?>
								</div>
							</div>
						</div>

						<!-- Signature -->
						<div class="col-md-6 mb-3">
							<div class="form-group">
								<label><i class="fa fa-signature mr-1"></i>Signature</label>
								<div class="upload-card" id="signature_card" onclick="document.getElementById('signature').click()">
									<input type="file" id="signature" accept="image/*" onchange="uploadWithProgress('signature')">
									<div class="upload-icon"><i class="fa fa-pen-fancy"></i></div>
									<p class="upload-text">Click to upload signature</p>
								</div>
								<input type="hidden" id="signature1" name="signature" value="<?php echo $row->signature ?>" required>
								<div class="progress-container" id="signature_progress">
									<div class="progress">
										<div class="progress-bar" role="progressbar" style="width: 0%"></div>
									</div>
									<div class="progress-text">Uploading... 0%</div>
								</div>
								<div class="upload-success" id="signature_success"><i class="fa fa-check-circle"></i>Upload complete!</div>
								<div class="upload-preview" id="signature2">
									<?php if(!empty($row->signature)): ?>
									<img src="<?php echo base_url('uploads/').$row->signature?>" alt="Signature">
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-cancel" data-dismiss="modal"><i class="fa fa-times mr-1"></i>Cancel</button>
					<button type="submit" class="btn btn-primary btn-save"><i class="fa fa-save mr-1"></i>Save Changes</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
function uploadWithProgress(fieldId) {
	var fileInput = document.getElementById(fieldId);
	var file = fileInput.files[0];

	if (!file) return;

	var card = document.getElementById(fieldId + '_card');
	var progressContainer = document.getElementById(fieldId + '_progress');
	var progressBar = progressContainer.querySelector('.progress-bar');
	var progressText = progressContainer.querySelector('.progress-text');
	var successMsg = document.getElementById(fieldId + '_success');
	var preview = document.getElementById(fieldId + '2');
	var hiddenInput = document.getElementById(fieldId + '1');

	// Show progress, hide success
	card.classList.add('uploading');
	progressContainer.style.display = 'block';
	successMsg.style.display = 'none';
	progressBar.style.width = '0%';
	progressText.textContent = 'Uploading... 0%';

	var formData = new FormData();
	formData.append('file', file);

	var xhr = new XMLHttpRequest();

	xhr.upload.addEventListener('progress', function(e) {
		if (e.lengthComputable) {
			var percent = Math.round((e.loaded / e.total) * 100);
			progressBar.style.width = percent + '%';
			progressText.textContent = 'Uploading... ' + percent + '%';
		}
	});

	xhr.addEventListener('load', function() {
		if (xhr.status === 200) {
			try {
				var response = JSON.parse(xhr.responseText);
				if (response.status === 'success') {
					progressBar.style.width = '100%';
					progressText.textContent = 'Upload complete!';

					setTimeout(function() {
						progressContainer.style.display = 'none';
						successMsg.style.display = 'block';
						card.classList.remove('uploading');

						// Update hidden input
						hiddenInput.value = response.data.file_name;

						// Update preview
						preview.innerHTML = '<img src="<?php echo base_url('uploads/')?>' + response.data.file_name + '" alt="Preview">';

						// Hide success after 3 seconds
						setTimeout(function() {
							successMsg.style.display = 'none';
						}, 3000);
					}, 500);
				} else {
					alert('Upload failed: ' + response.message);
					progressContainer.style.display = 'none';
					card.classList.remove('uploading');
				}
			} catch (e) {
				alert('Upload failed. Please try again.');
				progressContainer.style.display = 'none';
				card.classList.remove('uploading');
			}
		}
	});

	xhr.addEventListener('error', function() {
		alert('Upload failed. Please check your connection.');
		progressContainer.style.display = 'none';
		card.classList.remove('uploading');
	});

	xhr.open('POST', '<?php echo base_url('Proofofidentity/upload')?>');
	xhr.send(formData);
}
</script>
