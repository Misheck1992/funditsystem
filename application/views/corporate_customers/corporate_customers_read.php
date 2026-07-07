<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Corporate Customers</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">Corporate Customer Details</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
            <div class="row">
                <div class="col-lg-4 border-right">
                    <h2>Basic Information</h2>
                    <hr>
                    <table class="table">
                        <tr>
                            <th style="text-align: right; width: 50%;">Entity Name</th>
                            <td><?php echo $EntityName; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Entity Type</th>
                            <td><?php echo $entity_type; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Category</th>
                            <td><?php echo $category; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Registration Number</th>
                            <td><?php echo $RegistrationNumber; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Tax ID Number</th>
                            <td><?php echo $TaxIdentificationNumber; ?></td>
                        </tr>
                    </table>
                    <!-- Show Key Management Info -->
                    <div class="mt-3">
                        <strong>Key Management Info:</strong>
                        <div style="white-space: pre-line;"><?php echo isset($key_management_info) ? $key_management_info : ''; ?></div>
                    </div>
                </div>

                <div class="col-lg-4 border-right">
                    <h2>Business Details</h2>
                    <hr>
                    <table class="table">
                        <tr>
                            <th style="text-align: right; width: 50%;">Nature of Business</th>
                            <td><?php echo $nature_of_business; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Industry Sector</th>
                            <td><?php echo $industry_sector; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Country</th>
                            <td><?php echo $Country; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Branch</th>
                            <td><?php echo $Branch; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Financial Year End</th>
                            <td><?php echo isset($financial_year_end) && $financial_year_end ? date('d M Y', strtotime($financial_year_end)) : 'Not specified'; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Casual Employees</th>
                            <td><?php echo isset($casual_employees) ? $casual_employees : '0'; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Permanent Employees</th>
                            <td><?php echo isset($permanent_employees) ? $permanent_employees : '0'; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Last Update</th>
                            <td>
                                <?php
                                if (isset($LastUpdatedOn) && $LastUpdatedOn) {
                                    echo date('M Y', strtotime($LastUpdatedOn));
                                } else {
                                    echo 'N/A';
                                }
                                ?>
                            </td>
                        </tr>
                    </table>
                    <!-- Show Business Info -->
                    <div class="mt-3">
                        <strong>Business Info:</strong>
                        <div style="white-space: pre-line;"><?php echo isset($business_info) ? $business_info : ''; ?></div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <h2>Contact Information</h2>
                    <hr>
                    <table class="table">
                        <tr>
                            <th style="text-align: right; width: 50%;">Street</th>
                            <td><?php echo $street; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">City/Town</th>
                            <td><?php echo $city_town; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Phone Number</th>
                            <td><?php echo $phone_number; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Email</th>
                            <td><?php echo $contact_email; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Website</th>
                            <td><?php echo $website; ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    <h2>Linked Individual Customer</h2>
                    <hr>
                    <?php if (!empty($linked_individual)): ?>
                        <table class="table">
                            <tr>
                                <th style="text-align: right; width: 25%;">Name</th>
                                <td><?php echo $linked_individual->Firstname . ' ' . $linked_individual->Lastname; ?></td>
                            </tr>
                            <tr>
                                <th style="text-align: right;">Client ID</th>
                                <td><?php echo $linked_individual->ClientId; ?></td>
                            </tr>
                            <tr>
                                <th style="text-align: right;">Phone</th>
                                <td><?php echo $linked_individual->PhoneNumber; ?></td>
                            </tr>
                            <tr>
                                <th style="text-align: right;">Email</th>
                                <td><?php echo $linked_individual->EmailAddress; ?></td>
                            </tr>
                        </table>
                        <a href="<?php echo site_url('individual_customers/view/' . $linked_individual->id); ?>" class="btn btn-sm btn-primary mb-3">
                            <i class="fa fa-external-link-alt"></i> View Customer
                        </a>
                    <?php else: ?>
                        <p class="text-muted">Not linked to any individual customer</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    <h2>Required Documents</h2>
                    <hr>
                    <table class="table">
                        <tr>
                            <th style="text-align: right; width: 25%;">Company Certificate</th>

                            <td><a href="<?php echo base_url('uploads/').$company_certificate ?>">Download attachment</a></td>


                        </tr>
                        <tr>
                            <th style="text-align: right;">Proof of Address</th>

                            <td><a href="<?php echo base_url('uploads/'). $proof_physical_address ?>">Download attachment</a></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Financial Statements</th>

                            <td><a href="<?php echo base_url('uploads/'). $financial_statement ?>">Download attachment</a></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Tax Clearance</th>
                           
                            <td><a href="<?php echo base_url('uploads/'). $tax_id_doc ?>">Download attachment</a></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-lg-12">
                    <h2>Shareholder Details </h2>
                    <hr>
                    <div class="double-scroll">
                    <table class="table table-bordered" style="margin-bottom: 10px">
                        <tr>
                            <th>No</th>
                            <th>Title</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Gender</th>
                            <th>Approval Status</th>
                            <th>Nationality</th>
                            <th>Phone Number</th>
                            <th>Email Address</th>
                            <th>Full Address</th>
                            <th>ID Type</th>
                            <th>Identity Number</th>
                            <th>% Ownership </th>
                            <th>KYC file </th>

                        </tr>
                        <?php
                        $shareholders_data=get_all_shareholders($id);
                        $start = 0;
                        foreach ($shareholders_data as $shareholder) {




                            ?>
                            <tr>
                                <td width="80px"><?php echo ++$start ?></td>
                                <td><?php echo $shareholder->title ?></td>
                                <td><?php echo $shareholder->first_name ?></td>
                                <td><?php echo $shareholder->last_name ?></td>
                                <td><?php echo $shareholder->gender ?></td>
                                <td><?php echo $shareholder->approval_status ?></td>
                                <td><?php echo $shareholder->nationality ?></td>
                                <td><?php echo $shareholder->phone_number ?></td>
                                <td><?php echo $shareholder->email_address ?></td>
                                <td><?php echo $shareholder->full_address ?></td>
                                <td><?php echo isset($shareholder->idtype) ? $shareholder->idtype : 'N/A' ?></td>
                                <td><?php echo isset($shareholder->idnumber) ? $shareholder->idnumber : 'N/A' ?></td>
                                <td><?php echo $shareholder->percentage_value ?></td>
                                <td><a href="<?php echo base_url('uploads/').'147detailed_approach.pdf' ?>">Download KYC</a></td>

                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    </div>
                </div>
            </div>

            <!-- Collateral Register Section -->
            <div class="row mt-4">
                <div class="col-lg-12">
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

            <div class="row mt-4">
                <div class="col-12">
                    <a href="<?php echo site_url('corporate_customers') ?>" class="btn btn-default">Back</a>
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
                    <input type="hidden" name="customer_type" value="institution">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Collateral Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="collateral_name" required placeholder="e.g., Commercial Property">
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
                                    <option value="Debenture">Debenture</option>
                                    <option value="Corporate Guarantee">Corporate Guarantee</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Serial/Registration Number</label>
                                <input type="text" class="form-control" name="collateral_serial" placeholder="e.g., Title Deed #">
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
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" name="collateral_desc" rows="2"></textarea>
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
                        <input type="file" class="form-control" name="collateral_file">
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
                                    <option value="Debenture">Debenture</option>
                                    <option value="Corporate Guarantee">Corporate Guarantee</option>
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
                        <input type="file" class="form-control" name="collateral_file">
                    </div>

                    <button type="submit" class="btn btn-warning btn-block"><i class="fa fa-save"></i> Update Collateral</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.collateral-status { display: inline-block; padding: 0.15rem 0.5rem; border-radius: 50px; font-size: 0.7rem; font-weight: 600; }
.collateral-status.active { background: #dcfce7; color: #166534; }
.collateral-status.released { background: #dbeafe; color: #1e40af; }
</style>

<script>
var customerId = <?php echo $id; ?>;
var customerType = 'institution';

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
                document.getElementById('collateral_list_container').innerHTML = '<div style="text-align: center; padding: 2rem; color: #6b7280;"><i class="fa fa-shield-alt fa-3x" style="opacity: 0.3;"></i><p>No collaterals registered</p></div>';
            }
        },
        error: function(xhr, status, error) {
            console.log('Error loading collaterals:', error);
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
        html += '<button onclick="editCollateral(' + c.id + ')" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></button> ';
        if(c.utilized_amount == 0) html += '<button onclick="deleteCollateral(' + c.id + ')" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>';
        html += '</td></tr>';
    });
    html += '</tbody></table>';
    document.getElementById('collateral_list_container').innerHTML = html;
}

function numberFormat(num) { return parseFloat(num || 0).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'); }

function openAddCollateralModal() { document.getElementById('add_collateral_form').reset(); $('#add_collateral_modal').modal('show'); }

document.getElementById('add_collateral_form').addEventListener('submit', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    var btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    $.ajax({
        url: '<?php echo base_url("loan/add_customer_collateral"); ?>',
        type: 'POST', data: formData, processData: false, contentType: false, dataType: 'json',
        success: function(response) {
            btn.disabled = false;
            if(response.success) { $('#add_collateral_modal').modal('hide'); loadCollaterals(); alert('Collateral added'); }
            else alert('Error: ' + response.message);
        }
    });
});

function editCollateral(id) {
    $.ajax({
        url: '<?php echo base_url("loan/get_customer_collaterals/"); ?>' + customerId + '/' + customerType,
        type: 'GET', dataType: 'json',
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
    $.ajax({
        url: '<?php echo base_url("loan/update_customer_collateral"); ?>',
        type: 'POST', data: formData, processData: false, contentType: false, dataType: 'json',
        success: function(response) {
            btn.disabled = false;
            if(response.success) { $('#edit_collateral_modal').modal('hide'); loadCollaterals(); alert('Collateral updated'); }
            else alert('Error: ' + response.message);
        }
    });
});

function deleteCollateral(id) {
    if(!confirm('Delete this collateral?')) return;
    $.ajax({
        url: '<?php echo base_url("loan/delete_collateral"); ?>',
        type: 'POST', data: { collateral_id: id }, dataType: 'json',
        success: function(response) {
            if(response.success) { loadCollaterals(); alert('Deleted'); }
            else alert('Error: ' + response.message);
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
