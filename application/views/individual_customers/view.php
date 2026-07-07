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
						
						<tr><td style="text-align: right;padding-right: 10px;" >Profession</td><td><?php echo $Profession; ?></td></tr>
						<tr><td style="text-align: right;padding-right: 10px;" >SourceOfIncome</td><td><?php echo $SourceOfIncome; ?></td></tr>
						<tr><td style="text-align: right;padding-right: 10px;" >GrossMonthlyIncome</td><td><?php echo $GrossMonthlyIncome; ?></td></tr>

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
						<input type="text" value="<?php echo $ClientId?>" id="cid" hidden > <button id="add_kyc">Add KYC</button>
							<?php
					}else{

						?>
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
					<h5>Actions</h5>
					<hr>

					<?php if(isset($approval_status) && $approval_status == 'CREATED'): ?>
					<a href="<?php echo base_url('individual_customers/review/').$id?>" class="btn btn-sm btn-success mb-2 d-block"><i class="fa fa-check-circle"></i> Review</a>
					<?php endif; ?>

					<a href="<?php echo base_url('individual_customers/report/').$id?>" class="btn btn-sm btn-danger mb-2 d-block"><i class="fa fa-file-pdf"></i> Report</a>

					<?php if(has_access('Individual_customers/edit')): ?>
					<a href="<?php echo base_url('individual_customers/update/').$id?>" class="btn btn-sm btn-primary mb-2 d-block"><i class="fa fa-edit"></i> Edit</a>
					<?php endif; ?>

					<?php if(has_access('Individual_customers/to_delete')): ?>
					<a href="<?php echo base_url('individual_customers/delete/').$id?>" class="btn btn-sm btn-warning mb-2 d-block" onclick="return confirm('Are you sure you want to delete this customer?')"><i class="fa fa-trash"></i> Delete</a>
					<?php endif; ?>

					<?php if(has_access('Individual_customers/kyc_edit')): ?>
					<a href="<?php echo base_url('individual_customers/view_kyc/').$id?>" class="btn btn-sm btn-info mb-2 d-block"><i class="fa fa-id-card"></i> Edit KYC</a>
					<?php endif; ?>

					<hr>
					<h6>FD Linkage</h6>
					<div id="fd-linkage-status"><small class="text-muted">Loading...</small></div>
					<a class="btn btn-sm btn-success mb-2 d-block" data-toggle="modal" data-target="#linkFdModal"><i class="fa fa-university"></i> Link FD</a>

					<hr>
					<h6>Business Linkage</h6>
					<div id="biz-linkage-status"><small class="text-muted">Loading...</small></div>
					<a class="btn btn-sm btn-info mb-2 d-block" data-toggle="modal" data-target="#linkBizModal"><i class="fa fa-building"></i> Link Business</a>

				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-6">
                    <h1>Bank Details</h1>

                    <table  class="table">
                        <thead>
                        <tr>

                            <th>#</th>
                            <th> Name</th>
                            <th>account number
                            </th>
                            <th>Bank</th>
                            <th>Branch</th>
                            



                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $bankdetails=get_all_by_id('bank_details','customer_id',$id);
                        $n = 1;
						if(!empty( $bankdetails)){
							foreach ($bankdetails as $l){
								?>
						
                        
                            <tr>
                                <td><?php echo $n;?></td>
                                <td><?php echo $l->account_name;?></td>

                                <td><?php echo $l->account_number;?></td>
                                <td><?php echo $l->bank_name;?></td>
                                <td><?php echo $l->bank_branch;?></td>
                               

                            </tr>

                            <?php
                            $n ++;
                        }
					}
					else {
						echo "No bank details for this particular customers";
					} 
                        ?>
                        </tbody>
                    </table>
                 
                </div>

	<div class="col-lg-12">
		<h1>Customer Loans</h1>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>#</th>
					<th>Loan Number</th>
					<th>Product</th>
					<th>Principal</th>
					<th>Total Amount</th>
					<th>Status</th>
					<th>Loan Date</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
			<?php
			$n = 1;
			if(!empty($loans)){
				foreach ($loans as $loan){
					$product = get_by_id('loan_products','loan_product_id',$loan->loan_product);
					?>
					<tr>
						<td><?php echo $n;?></td>
						<td><?php echo $loan->loan_number;?></td>
						<td><?php echo !empty($product) ? $product->product_name : 'N/A';?></td>
						<td><?php echo number_format($loan->loan_principal,2);?></td>
						<td><?php echo number_format($loan->loan_amount_total,2);?></td>
						<td>
							<?php
							if($loan->loan_status == 'Disbursed'){
								echo '<span class="badge badge-success">'.$loan->loan_status.'</span>';
							}elseif($loan->loan_status == 'Pending'){
								echo '<span class="badge badge-warning">'.$loan->loan_status.'</span>';
							}elseif($loan->loan_status == 'Approved'){
								echo '<span class="badge badge-info">'.$loan->loan_status.'</span>';
							}elseif($loan->loan_status == 'Rejected'){
								echo '<span class="badge badge-danger">'.$loan->loan_status.'</span>';
							}else{
								echo '<span class="badge badge-secondary">'.$loan->loan_status.'</span>';
							}
							?>
						</td>
						<td><?php echo $loan->loan_date;?></td>
						<td>
							<a href="<?php echo base_url('loan/view/').$loan->loan_id;?>" class="btn btn-sm btn-primary">View</a>
						</td>
					</tr>
					<?php
					$n++;
				}
			} else {
				echo '<tr><td colspan="8" class="text-center">No loans found for this customer</td></tr>';
			}
			?>
			</tbody>
		</table>
	</div>

	<!-- Collateral Register Section -->
	<div class="col-lg-12" style="margin-top: 2rem;">
		<div class="card">
			<div class="card-header" style="background: #1e3a5f; color: #fff; display: flex; justify-content: space-between; align-items: center;">
				<h5 style="margin: 0;"><i class="fa fa-shield-alt"></i> Collateral Register</h5>
				<button onclick="openAddCollateralModal()" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Add Collateral</button>
			</div>
			<div class="card-body">
				<div id="collateral_list_container">
					<div style="text-align: center; padding: 2rem; color: #6b7280;">
						<i class="fa fa-spinner fa-spin fa-2x"></i>
						<p>Loading collaterals...</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Add Collateral Modal -->
<div class="modal fade" id="add_collateral_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header" style="background: #059669; color: #fff;">
				<h5 class="modal-title"><i class="fa fa-plus-circle"></i> Add New Collateral</h5>
				<button type="button" class="close" data-dismiss="modal" style="color: #fff;"><span>&times;</span></button>
			</div>
			<div class="modal-body">
				<form id="add_collateral_form" enctype="multipart/form-data">
					<input type="hidden" name="customer_id" value="<?php echo $id; ?>">
					<input type="hidden" name="customer_type" value="individual">

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Collateral Name <span class="text-danger">*</span></label>
								<input type="text" class="form-control" name="collateral_name" required placeholder="e.g., Toyota Hilux 2020">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Collateral Type <span class="text-danger">*</span></label>
								<select class="form-control" name="collateral_type" required>
									<option value="">-- Select Type --</option>
									<option value="Vehicle">Vehicle</option>
									<option value="Property">Property/Real Estate</option>
									<option value="Equipment">Equipment/Machinery</option>
									<option value="Inventory">Inventory/Stock</option>
									<option value="Securities">Securities/Shares</option>
									<option value="Cash Deposit">Cash Deposit</option>
									<option value="Guarantee">Personal Guarantee</option>
									<option value="Other">Other</option>
								</select>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label>Serial/Registration Number</label>
								<input type="text" class="form-control" name="collateral_serial" placeholder="e.g., ABC 1234 ZM">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Market Value <span class="text-danger">*</span></label>
								<input type="number" step="0.01" class="form-control" name="market_value" required placeholder="0.00">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Force Sale Value <span class="text-danger">*</span></label>
								<input type="number" step="0.01" class="form-control" name="force_sale_value" required placeholder="0.00">
								<small class="text-muted">Amount recoverable on forced sale</small>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Description</label>
								<textarea class="form-control" name="collateral_desc" rows="2" placeholder="Additional details..."></textarea>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Location Status <span class="text-danger">*</span></label>
								<select class="form-control" name="location_status" required>
									<option value="In Our Possession" selected>In Our Possession</option>
									<option value="Perfected">Perfected</option>
									<option value="Released">Released</option>
								</select>
								<small class="text-muted">Where is the collateral document/item currently?</small>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label>Supporting Document</label>
						<input type="file" class="form-control" name="collateral_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
					</div>

					<button type="submit" class="btn btn-success btn-block"><i class="fa fa-save"></i> Save Collateral</button>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Edit Collateral Modal -->
<div class="modal fade" id="edit_collateral_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header" style="background: #f59e0b; color: #fff;">
				<h5 class="modal-title"><i class="fa fa-edit"></i> Edit Collateral</h5>
				<button type="button" class="close" data-dismiss="modal" style="color: #fff;"><span>&times;</span></button>
			</div>
			<div class="modal-body">
				<form id="edit_collateral_form" enctype="multipart/form-data">
					<input type="hidden" name="collateral_id" id="edit_collateral_id">

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Collateral Name <span class="text-danger">*</span></label>
								<input type="text" class="form-control" name="collateral_name" id="edit_collateral_name" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Collateral Type <span class="text-danger">*</span></label>
								<select class="form-control" name="collateral_type" id="edit_collateral_type" required>
									<option value="">-- Select Type --</option>
									<option value="Vehicle">Vehicle</option>
									<option value="Property">Property/Real Estate</option>
									<option value="Equipment">Equipment/Machinery</option>
									<option value="Inventory">Inventory/Stock</option>
									<option value="Securities">Securities/Shares</option>
									<option value="Cash Deposit">Cash Deposit</option>
									<option value="Guarantee">Personal Guarantee</option>
									<option value="Other">Other</option>
								</select>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label>Serial/Registration Number</label>
								<input type="text" class="form-control" name="collateral_serial" id="edit_collateral_serial">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Market Value <span class="text-danger">*</span></label>
								<input type="number" step="0.01" class="form-control" name="market_value" id="edit_market_value" required>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>Force Sale Value <span class="text-danger">*</span></label>
								<input type="number" step="0.01" class="form-control" name="force_sale_value" id="edit_force_sale_value" required>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>Description</label>
								<textarea class="form-control" name="collateral_desc" id="edit_collateral_desc" rows="2"></textarea>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label>Location Status <span class="text-danger">*</span></label>
								<select class="form-control" name="location_status" id="edit_location_status" required>
									<option value="In Our Possession">In Our Possession</option>
									<option value="Perfected">Perfected</option>
									<option value="Released">Released</option>
								</select>
								<small class="text-muted">Where is the collateral document/item currently?</small>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label>Update Document (optional)</label>
						<input type="file" class="form-control" name="collateral_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
					</div>

					<button type="submit" class="btn btn-warning btn-block"><i class="fa fa-save"></i> Update Collateral</button>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- Collateral Loans Modal -->
<div class="modal fade" id="collateral_loans_modal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header" style="background: #1e3a5f; color: #fff;">
				<h5 class="modal-title"><i class="fa fa-link"></i> Loans Using This Collateral</h5>
				<button type="button" class="close" data-dismiss="modal" style="color: #fff;"><span>&times;</span></button>
			</div>
			<div class="modal-body">
				<div id="collateral_loans_content">
					Loading...
				</div>
			</div>
		</div>
	</div>
</div>

<style>
.collateral-card {
	background: #fff;
	border: 1px solid #e5e7eb;
	border-radius: 8px;
	padding: 1rem;
	margin-bottom: 0.75rem;
}
.collateral-card:hover {
	box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.collateral-status {
	display: inline-block;
	padding: 0.15rem 0.5rem;
	border-radius: 50px;
	font-size: 0.7rem;
	font-weight: 600;
}
.collateral-status.active { background: #dcfce7; color: #166534; }
.collateral-status.released { background: #dbeafe; color: #1e40af; }
.collateral-status.sold { background: #fee2e2; color: #991b1b; }
.value-box {
	background: #f8fafc;
	border-radius: 6px;
	padding: 0.5rem;
	text-align: center;
	border: 1px solid #e5e7eb;
}
.value-box .label { font-size: 0.7rem; color: #6b7280; }
.value-box .value { font-weight: 700; font-size: 0.9rem; }
</style>

<script>
var customerId = <?php echo $id; ?>;
var customerType = 'individual';

function loadCollaterals() {
	if (typeof jQuery === 'undefined') {
		setTimeout(loadCollaterals, 100);
		return;
	}
	$.ajax({
		url: '<?php echo base_url("loan/get_customer_collaterals/"); ?>' + customerId + '/' + customerType,
		type: 'GET',
		dataType: 'json',
		success: function(response) {
			console.log('Collaterals response:', response);
			if(response.success && response.collaterals && response.collaterals.length > 0) {
				displayCollaterals(response.collaterals);
			} else {
				document.getElementById('collateral_list_container').innerHTML = '<div style="text-align: center; padding: 2rem; color: #6b7280;"><i class="fa fa-shield-alt fa-3x" style="opacity: 0.3;"></i><p>No collaterals registered for this customer</p></div>';
			}
		},
		error: function(xhr, status, error) {
			console.log('Error loading collaterals:', error, xhr.responseText);
			document.getElementById('collateral_list_container').innerHTML = '<div style="text-align: center; padding: 2rem; color: #dc2626;">Error loading collaterals</div>';
		}
	});
}

function displayCollaterals(collaterals) {
	var html = '<table class="table table-bordered"><thead><tr><th>Collateral</th><th>Type</th><th>Market Value</th><th>Force Sale</th><th>Utilized</th><th>Available</th><th>Location Status</th><th>Actions</th></tr></thead><tbody>';

	collaterals.forEach(function(c) {
		var locationStatus = c.location_status || 'In Our Possession';
		var locationClass = '';
		if(locationStatus == 'Perfected') locationClass = 'background: #dcfce7; color: #166534;';
		else if(locationStatus == 'In Our Possession') locationClass = 'background: #dbeafe; color: #1e40af;';
		else if(locationStatus == 'Released') locationClass = 'background: #fee2e2; color: #991b1b;';

		html += '<tr>';
		html += '<td><strong>' + c.collateral_name + '</strong>' + (c.collateral_serial ? '<br><small class="text-muted">' + c.collateral_serial + '</small>' : '') + '</td>';
		html += '<td>' + c.collateral_type + '</td>';
		html += '<td>ZMW ' + numberFormat(c.market_value) + '</td>';
		html += '<td>ZMW ' + numberFormat(c.force_sale_value) + '</td>';
		html += '<td style="color: #dc2626;">ZMW ' + numberFormat(c.utilized_amount) + '</td>';
		html += '<td style="color: #059669; font-weight: 700;">ZMW ' + numberFormat(c.available_balance) + '</td>';
		html += '<td><span style="display: inline-block; padding: 0.2rem 0.6rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600; ' + locationClass + '">' + locationStatus + '</span></td>';
		html += '<td>';
		html += '<button onclick="viewCollateralLoans(' + c.id + ')" class="btn btn-info btn-sm" title="View Loans"><i class="fa fa-link"></i></button> ';
		html += '<button onclick="editCollateral(' + c.id + ')" class="btn btn-warning btn-sm" title="Edit"><i class="fa fa-edit"></i></button> ';
		if(c.utilized_amount == 0) {
			html += '<button onclick="deleteCollateral(' + c.id + ')" class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></button>';
		}
		html += '</td>';
		html += '</tr>';
	});

	html += '</tbody></table>';
	document.getElementById('collateral_list_container').innerHTML = html;
}

function numberFormat(num) {
	return parseFloat(num || 0).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

function openAddCollateralModal() {
	document.getElementById('add_collateral_form').reset();
	$('#add_collateral_modal').modal('show');
}

document.getElementById('add_collateral_form').addEventListener('submit', function(e) {
	e.preventDefault();
	var formData = new FormData(this);
	var btn = this.querySelector('button[type="submit"]');
	btn.disabled = true;
	btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';

	$.ajax({
		url: '<?php echo base_url("loan/add_customer_collateral"); ?>',
		type: 'POST',
		data: formData,
		processData: false,
		contentType: false,
		dataType: 'json',
		success: function(response) {
			btn.disabled = false;
			btn.innerHTML = '<i class="fa fa-save"></i> Save Collateral';
			if(response.success) {
				$('#add_collateral_modal').modal('hide');
				loadCollaterals();
				alert('Collateral added successfully');
			} else {
				alert('Error: ' + response.message);
			}
		},
		error: function() {
			btn.disabled = false;
			btn.innerHTML = '<i class="fa fa-save"></i> Save Collateral';
			alert('Error adding collateral');
		}
	});
});

function editCollateral(id) {
	$.ajax({
		url: '<?php echo base_url("loan/get_customer_collaterals/"); ?>' + customerId + '/' + customerType,
		type: 'GET',
		dataType: 'json',
		success: function(response) {
			if(response.success && response.collaterals) {
				var c = response.collaterals.find(x => x.id == id);
				if(c) {
					document.getElementById('edit_collateral_id').value = c.id;
					document.getElementById('edit_collateral_name').value = c.collateral_name;
					document.getElementById('edit_collateral_type').value = c.collateral_type;
					document.getElementById('edit_collateral_serial').value = c.collateral_serial || '';
					document.getElementById('edit_market_value').value = c.market_value;
					document.getElementById('edit_force_sale_value').value = c.force_sale_value;
					document.getElementById('edit_collateral_desc').value = c.description || '';
					document.getElementById('edit_location_status').value = c.location_status || 'In Our Possession';
					$('#edit_collateral_modal').modal('show');
				}
			}
		}
	});
}

document.getElementById('edit_collateral_form').addEventListener('submit', function(e) {
	e.preventDefault();
	var formData = new FormData(this);
	var btn = this.querySelector('button[type="submit"]');
	btn.disabled = true;
	btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Updating...';

	$.ajax({
		url: '<?php echo base_url("loan/update_customer_collateral"); ?>',
		type: 'POST',
		data: formData,
		processData: false,
		contentType: false,
		dataType: 'json',
		success: function(response) {
			btn.disabled = false;
			btn.innerHTML = '<i class="fa fa-save"></i> Update Collateral';
			if(response.success) {
				$('#edit_collateral_modal').modal('hide');
				loadCollaterals();
				alert('Collateral updated successfully');
			} else {
				alert('Error: ' + response.message);
			}
		},
		error: function() {
			btn.disabled = false;
			btn.innerHTML = '<i class="fa fa-save"></i> Update Collateral';
			alert('Error updating collateral');
		}
	});
});

function deleteCollateral(id) {
	if(!confirm('Are you sure you want to delete this collateral?')) return;

	$.ajax({
		url: '<?php echo base_url("loan/delete_collateral"); ?>',
		type: 'POST',
		data: { collateral_id: id },
		dataType: 'json',
		success: function(response) {
			if(response.success) {
				loadCollaterals();
				alert('Collateral deleted');
			} else {
				alert('Error: ' + response.message);
			}
		}
	});
}

function viewCollateralLoans(collateralId) {
	$('#collateral_loans_modal').modal('show');
	document.getElementById('collateral_loans_content').innerHTML = '<div style="text-align: center; padding: 2rem;"><i class="fa fa-spinner fa-spin fa-2x"></i></div>';

	$.ajax({
		url: '<?php echo base_url("loan/get_collateral_loans/"); ?>' + collateralId,
		type: 'GET',
		dataType: 'json',
		success: function(response) {
			if(response.success && response.loans && response.loans.length > 0) {
				var html = '<table class="table table-bordered"><thead><tr><th>Loan Number</th><th>Principal</th><th>Amount Utilized</th><th>Loan Status</th><th>Link Status</th></tr></thead><tbody>';
				response.loans.forEach(function(l) {
					html += '<tr>';
					html += '<td><a href="<?php echo base_url("loan/view/"); ?>' + l.loan_id + '">' + l.loan_number + '</a></td>';
					html += '<td>ZMW ' + numberFormat(l.loan_principal) + '</td>';
					html += '<td>ZMW ' + numberFormat(l.amount_utilized) + '</td>';
					html += '<td>' + l.loan_status + '</td>';
					html += '<td><span class="collateral-status ' + (l.link_status || 'active').toLowerCase() + '">' + (l.link_status || 'ACTIVE') + '</span></td>';
					html += '</tr>';
				});
				html += '</tbody></table>';
				document.getElementById('collateral_loans_content').innerHTML = html;
			} else {
				document.getElementById('collateral_loans_content').innerHTML = '<div style="text-align: center; padding: 2rem; color: #6b7280;">This collateral is not linked to any loans yet</div>';
			}
		}
	});
}

// Load collaterals on page load - wait for DOM and jQuery
function initCollaterals() {
	if (typeof jQuery === 'undefined') {
		setTimeout(initCollaterals, 50);
		return;
	}
	console.log('jQuery loaded, calling loadCollaterals');
	loadCollaterals();
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initCollaterals);
} else {
	initCollaterals();
}
</script>

<!-- Link FD Modal -->
<div class="modal fade" id="linkFdModal" tabindex="-1" role="dialog" aria-labelledby="linkFdModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="linkFdModalLabel"><i class="fa fa-university"></i> Link to FD Customer</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label>Search FD Customer</label>
					<div class="input-group">
						<input type="text" class="form-control" id="fd-search-input" placeholder="Search by name, number, email, or phone...">
						<div class="input-group-append">
							<button class="btn btn-primary" type="button" id="fd-search-btn"><i class="fa fa-search"></i> Search</button>
						</div>
					</div>
				</div>
				<div id="fd-search-results" style="max-height: 300px; overflow-y: auto;"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script>
var individualId = <?php echo json_encode($id); ?>;
var baseUrl = <?php echo json_encode(base_url()); ?>;

function loadFdLinkageStatus() {
	$.ajax({
		url: baseUrl + 'Fixed_deposits/get_linked_fd/' + individualId,
		type: 'GET',
		dataType: 'json',
		success: function(response) {
			var container = $('#fd-linkage-status');
			if (response.linked) {
				var fc = response.fd_customer;
				container.html(
					'<span class="badge badge-success mb-1">Linked</span><br>' +
					'<small><strong>' + fc.customer_number + '</strong><br>' +
					fc.name + '</small><br>' +
					'<button class="btn btn-xs btn-outline-danger mt-1" onclick="unlinkFd(' + fc.id + ')"><i class="fa fa-unlink"></i> Unlink</button>'
				);
			} else {
				container.html('<span class="badge badge-secondary mb-1">Not linked</span>');
			}
		},
		error: function() {
			$('#fd-linkage-status').html('<span class="badge badge-secondary mb-1">Not linked</span>');
		}
	});
}

function searchFdCustomers() {
	var term = document.getElementById('fd-search-input').value.trim();
	if (term.length < 2) {
		$('#fd-search-results').html('<p class="text-muted text-center">Type at least 2 characters to search</p>');
		return;
	}
	$('#fd-search-btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Searching...');
	$('#fd-search-results').html('');
	$.ajax({
		url: baseUrl + 'Fixed_deposits/search_fd_customers',
		type: 'GET',
		data: { term: term },
		dataType: 'json',
		success: function(response) {
			var html = '';
			if (!response.results || response.results.length === 0) {
				html = '<p class="text-muted text-center">No FD customers found</p>';
			} else {
				html = '<table class="table table-sm table-hover"><thead><tr><th>Number</th><th>Name</th><th>Email</th><th>Phone</th><th>Action</th></tr></thead><tbody>';
				$.each(response.results, function(i, c) {
					var btn = '';
					if (c.already_linked) {
						btn = '<span class="badge badge-warning">Already Linked</span>';
					} else {
						btn = '<button class="btn btn-sm btn-success" onclick="linkFd(' + c.id + ')"><i class="fa fa-link"></i> Select</button>';
					}
					html += '<tr><td>' + c.customer_number + '</td><td>' + c.name + '</td><td>' + (c.email || '-') + '</td><td>' + (c.phone || '-') + '</td><td>' + btn + '</td></tr>';
				});
				html += '</tbody></table>';
			}
			$('#fd-search-results').html(html);
		},
		error: function(xhr) {
			console.log('FD search error:', xhr.status, xhr.responseText);
			$('#fd-search-results').html('<p class="text-danger text-center">Error searching. Check browser console for details.</p>');
		},
		complete: function() {
			$('#fd-search-btn').prop('disabled', false).html('<i class="fa fa-search"></i> Search');
		}
	});
}

function linkFd(fdCustomerId) {
	if (!confirm('Link this FD customer to this individual customer?')) return;
	$.ajax({
		url: baseUrl + 'Fixed_deposits/link_individual',
		type: 'POST',
		data: { fd_customer_id: fdCustomerId, individual_id: individualId },
		dataType: 'json',
		success: function(response) {
			if (response.status === 'success') {
				alert('FD customer linked successfully!');
				$('#linkFdModal').modal('hide');
				$('#fd-search-input').val('');
				$('#fd-search-results').html('');
				loadFdLinkageStatus();
			} else {
				alert('Error: ' + response.message);
			}
		},
		error: function() {
			alert('An error occurred. Please try again.');
		}
	});
}

function unlinkFd(fdCustomerId) {
	if (!confirm('Are you sure you want to unlink this FD customer?')) return;
	$.ajax({
		url: baseUrl + 'Fixed_deposits/unlink_individual',
		type: 'POST',
		data: { fd_customer_id: fdCustomerId },
		dataType: 'json',
		success: function(response) {
			if (response.status === 'success') {
				alert('FD customer unlinked successfully!');
				loadFdLinkageStatus();
			} else {
				alert('Error: ' + response.message);
			}
		},
		error: function() {
			alert('An error occurred. Please try again.');
		}
	});
}

// Wait for jQuery then bind events and init
function initFdLinkage() {
	if (typeof jQuery === 'undefined') {
		setTimeout(initFdLinkage, 50);
		return;
	}

	document.getElementById('fd-search-btn').addEventListener('click', function() {
		searchFdCustomers();
	});

	document.getElementById('fd-search-input').addEventListener('keypress', function(e) {
		if (e.which === 13 || e.keyCode === 13) {
			e.preventDefault();
			searchFdCustomers();
		}
	});

	loadFdLinkageStatus();
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initFdLinkage);
} else {
	initFdLinkage();
}
</script>

<!-- Link Business Modal -->
<div class="modal fade" id="linkBizModal" tabindex="-1" role="dialog" aria-labelledby="linkBizModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="linkBizModalLabel"><i class="fa fa-building"></i> Link to Business Customer</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label>Search Business Customer</label>
					<div class="input-group">
						<input type="text" class="form-control" id="biz-search-input" placeholder="Search by name, reg number, email, or phone...">
						<div class="input-group-append">
							<button class="btn btn-primary" type="button" id="biz-search-btn"><i class="fa fa-search"></i> Search</button>
						</div>
					</div>
				</div>
				<div id="biz-search-results" style="max-height: 300px; overflow-y: auto;"></div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script>
function loadBizLinkageStatus() {
	$.ajax({
		url: baseUrl + 'Corporate_customers/get_linked_corporates/' + individualId,
		type: 'GET',
		dataType: 'json',
		success: function(response) {
			var container = $('#biz-linkage-status');
			if (response.linked) {
				var html = '';
				$.each(response.corporates, function(i, c) {
					html += '<span class="badge badge-info mb-1">Linked</span><br>' +
						'<small><strong>' + c.name + '</strong><br>' +
						(c.reg_number || '') + '</small><br>' +
						'<button class="btn btn-xs btn-outline-danger mt-1 mb-2" onclick="unlinkBiz(' + c.id + ')"><i class="fa fa-unlink"></i> Unlink</button><br>';
				});
				container.html(html);
			} else {
				container.html('<span class="badge badge-secondary mb-1">Not linked</span>');
			}
		},
		error: function() {
			$('#biz-linkage-status').html('<span class="badge badge-secondary mb-1">Not linked</span>');
		}
	});
}

function searchBizCustomers() {
	var term = document.getElementById('biz-search-input').value.trim();
	if (term.length < 2) {
		$('#biz-search-results').html('<p class="text-muted text-center">Type at least 2 characters to search</p>');
		return;
	}
	$('#biz-search-btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Searching...');
	$('#biz-search-results').html('');
	$.ajax({
		url: baseUrl + 'Corporate_customers/search_corporate_ajax',
		type: 'GET',
		data: { term: term },
		dataType: 'json',
		success: function(response) {
			var html = '';
			if (!response.results || response.results.length === 0) {
				html = '<p class="text-muted text-center">No business customers found</p>';
			} else {
				html = '<table class="table table-sm table-hover"><thead><tr><th>Name</th><th>Reg #</th><th>Email</th><th>Phone</th><th>Action</th></tr></thead><tbody>';
				$.each(response.results, function(i, c) {
					var btn = '';
					if (c.already_linked) {
						btn = '<span class="badge badge-warning">Already Linked</span>';
					} else {
						btn = '<button class="btn btn-sm btn-success" onclick="linkBiz(' + c.id + ')"><i class="fa fa-link"></i> Select</button>';
					}
					html += '<tr><td>' + c.name + '</td><td>' + (c.reg_number || '-') + '</td><td>' + (c.email || '-') + '</td><td>' + (c.phone || '-') + '</td><td>' + btn + '</td></tr>';
				});
				html += '</tbody></table>';
			}
			$('#biz-search-results').html(html);
		},
		error: function(xhr) {
			console.log('Biz search error:', xhr.status, xhr.responseText);
			$('#biz-search-results').html('<p class="text-danger text-center">Error searching. Check browser console.</p>');
		},
		complete: function() {
			$('#biz-search-btn').prop('disabled', false).html('<i class="fa fa-search"></i> Search');
		}
	});
}

function linkBiz(corporateId) {
	if (!confirm('Link this business to this individual customer?')) return;
	$.ajax({
		url: baseUrl + 'Corporate_customers/link_individual',
		type: 'POST',
		data: { corporate_id: corporateId, individual_id: individualId },
		dataType: 'json',
		success: function(response) {
			if (response.status === 'success') {
				alert('Business linked successfully!');
				$('#linkBizModal').modal('hide');
				$('#biz-search-input').val('');
				$('#biz-search-results').html('');
				loadBizLinkageStatus();
			} else {
				alert('Error: ' + response.message);
			}
		},
		error: function() { alert('An error occurred. Please try again.'); }
	});
}

function unlinkBiz(corporateId) {
	if (!confirm('Are you sure you want to unlink this business?')) return;
	$.ajax({
		url: baseUrl + 'Corporate_customers/unlink_individual',
		type: 'POST',
		data: { corporate_id: corporateId },
		dataType: 'json',
		success: function(response) {
			if (response.status === 'success') {
				alert('Business unlinked successfully!');
				loadBizLinkageStatus();
			} else {
				alert('Error: ' + response.message);
			}
		},
		error: function() { alert('An error occurred. Please try again.'); }
	});
}

function initBizLinkage() {
	if (typeof jQuery === 'undefined') {
		setTimeout(initBizLinkage, 50);
		return;
	}

	document.getElementById('biz-search-btn').addEventListener('click', function() {
		searchBizCustomers();
	});

	document.getElementById('biz-search-input').addEventListener('keypress', function(e) {
		if (e.which === 13 || e.keyCode === 13) {
			e.preventDefault();
			searchBizCustomers();
		}
	});

	loadBizLinkageStatus();
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initBizLinkage);
} else {
	initBizLinkage();
}
</script>
