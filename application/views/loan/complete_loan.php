<?php
$loan_types = $this->Loan_products_model->get_all();
$offtakercorporate = get_all_by_id('corporate_customers','category','off_taker');
$currencies = get_all('currencies');
$get_settings = get_by_id('settings','settings_id', '1');
?>

<style>
.complete-loan-container { background: #f4f6f9; min-height: 100vh; padding-bottom: 2rem; }
.complete-loan-header { background: #1e3a5f; color: #fff; padding: 1.5rem 2rem; border-radius: 0 0 20px 20px; margin-bottom: 2rem; }
.complete-loan-header h2 { margin: 0; font-weight: 700; font-size: 1.5rem; color: #fff !important; }
.complete-loan-header .breadcrumb { background: transparent; margin: 0; padding: 0.5rem 0 0 0; }
.complete-loan-header .breadcrumb-item, .complete-loan-header .breadcrumb-item a { color: rgba(255,255,255,0.8); font-size: 0.85rem; }
.complete-loan-header .breadcrumb-item.active { color: #fff; }
.form-card { background: #fff; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); padding: 1.5rem; margin-bottom: 1.5rem; }
.form-card-title { color: #1e3a5f; font-weight: 700; font-size: 1.1rem; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 2px solid #e5e7eb; display: flex; align-items: center; gap: 0.5rem; }
.form-row { display: flex; gap: 1rem; align-items: flex-end; margin-bottom: 0.75rem; flex-wrap: wrap; }
.form-row .form-group { flex: 1; min-width: 150px; }
.readonly-field { background-color: #e9ecef; cursor: not-allowed; }
.info-card { background: #fff; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); padding: 1.25rem; margin-bottom: 1.25rem; }
.info-card-title { color: #1e3a5f; font-weight: 700; font-size: 1rem; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #e5e7eb; }
.btn-submit-loan { display: block; width: 100%; padding: 1rem; background: #059669; color: #fff; border: none; border-radius: 12px; font-size: 1.1rem; font-weight: 700; cursor: pointer; transition: all 0.3s ease; margin-top: 1rem; }
.btn-submit-loan:hover { background: #047857; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(5,150,105,0.4); }
.file-upload-row { display: flex; gap: 0.5rem; align-items: flex-end; margin-bottom: 0.75rem; flex-wrap: wrap; }
.file-upload-row .form-control { flex: 1; min-width: 150px; }
.btn-add-file { background: #1e3a5f; color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.85rem; cursor: pointer; transition: all 0.3s ease; }
.btn-add-file:hover { background: #2d5a87; }
.bank-statement-row { border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; margin-bottom: 10px; background: #fafafa; }
.collateral-section { background: #f8fafc; border-radius: 10px; padding: 1.25rem; margin-top: 1rem; }
.collateral-alert { background: #e8eef5; border: none; border-radius: 10px; color: #1e3a5f; padding: 1rem; }
.collateral-controls { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem; }
.collateral-controls select { flex: 1; min-width: 200px; }
.btn-add-collateral { background: #059669; color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.85rem; }
.btn-new-collateral { background: #1e3a5f; color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.85rem; }
.collateral-table { width: 100%; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
.collateral-table thead th { background: #1e3a5f; color: #fff; font-weight: 600; font-size: 0.8rem; padding: 0.75rem 0.5rem; text-align: left; }
.collateral-table tbody td { padding: 0.6rem 0.5rem; border-bottom: 1px solid #e5e7eb; font-size: 0.85rem; }
.results-list { list-style: none; padding: 0; margin: 0; }
.results-list li { padding: 0.5rem 0; border-bottom: 1px solid #e5e7eb; font-size: 0.9rem; color: #374151; }
.results-list li:last-child { border-bottom: none; }
.modal-content { border-radius: 15px; border: none; }
.modal-header { background: #1e3a5f; color: #fff; border-radius: 15px 15px 0 0; padding: 1rem 1.5rem; }
.modal-header .close { color: #fff; opacity: 1; }
</style>

<div class="main-content complete-loan-container">
    <!-- Header -->
    <div class="complete-loan-header">
        <h2><i class="fa fa-check-circle"></i> Complete Loan - <?php echo $loan_number; ?></h2>
        <nav class="breadcrumb breadcrumb-dash">
            <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
            <a href="<?php echo base_url('Loan/created_loans')?>" class="breadcrumb-item">Created Loans</a>
            <span class="breadcrumb-item active">Complete <?php echo $loan_number; ?></span>
        </nav>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Form Column -->
            <div class="col-lg-<?php echo ($customer_type == 'individual') ? '6' : '8'; ?>">
                <form action="<?php echo base_url('Loan/complete_loan_action')?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="loan_id" value="<?php echo $loan_id; ?>">
                    <input type="hidden" name="customer_type" value="<?php echo $customer_type; ?>">
                    <input type="hidden" name="customer" value="<?php echo $customer_id; ?>">

                    <!-- Loan Details (Read-Only) -->
                    <div class="form-card">
                        <h4 class="form-card-title"><i class="fa fa-file-invoice-dollar"></i> Loan Details (Pre-filled from API)</h4>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Customer</label>
                                <input type="text" class="form-control readonly-field" value="<?php echo $loan_customer; ?>" readonly>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Loan Number</label>
                                <input type="text" class="form-control readonly-field" value="<?php echo $loan_number; ?>" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Loan Amount</label>
                                <input type="text" class="form-control readonly-field" value="<?php echo number_format($loan_principal, 2); ?>" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Currency</label>
                                <?php $cur = get_by_id('currencies','currency_id',$currency); ?>
                                <input type="text" class="form-control readonly-field" value="<?php echo $cur->currency_code . ' - ' . $cur->currency_name; ?>" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Loan Product</label>
                                <input type="text" class="form-control readonly-field" value="<?php echo $loan_product; ?>" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Loan Term</label>
                                <input type="text" class="form-control readonly-field" value="<?php echo $loan_period; ?> <?php echo $period_type; ?>" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Interest Rate</label>
                                <input type="text" class="form-control readonly-field" value="<?php echo $loan_interest; ?>%" readonly>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Loan Date</label>
                                <input type="text" class="form-control readonly-field" value="<?php echo $loan_date; ?>" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Editable Fields -->
                    <div class="form-card">
                        <h4 class="form-card-title"><i class="fa fa-edit"></i> Additional Details</h4>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label><i class="fa fa-calculator"></i> Processing Fee (%)</label>
                                <input type="number" step="0.01" name="processing_fee" class="form-control" value="<?php echo $processing_fee; ?>" placeholder="Optional">
                            </div>
                            <?php if ($customer_type == 'institution'): ?>
                            <div class="form-group col-md-6">
                                <label><i class="fa fa-handshake-o"></i> Off-Taker Corporate</label>
                                <select name="off_taker" class="form-control select2">
                                    <option value="">-- Select Off-Taker (Optional) --</option>
                                    <?php foreach ($offtakercorporate as $c): ?>
                                        <option value="<?php echo $c->id; ?>" <?php if($off_taker == $c->id) echo 'selected'; ?>><?php echo $c->EntityName . " - " . $c->RegistrationNumber; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Narration -->
                    <div class="form-card">
                        <h4 class="form-card-title"><i class="fa fa-sticky-note-o"></i> Narration</h4>
                        <div class="form-group" style="margin-bottom: 0;">
                            <textarea name="narration" class="form-control ckeditor-field" rows="4" placeholder="Enter loan purpose and additional notes..."><?php echo $narration; ?></textarea>
                        </div>
                    </div>

                    <!-- Supporting Documents -->
                    <div class="form-card">
                        <h4 class="form-card-title"><i class="fa fa-file-pdf-o"></i> Supporting Documents</h4>

                        <div id="loan_forms_complete">
                            <div class="file-upload-row">
                                <input type="text" name="file_name[]" class="form-control" placeholder="Document name">
                                <input type="file" name="loan_files[]" class="form-control" style="display: block">
                                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.file-upload-row').remove()" style="border-radius:8px;">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <button type="button" class="btn-add-file" onclick="addCompleteLoanFile()">
                            <i class="fa fa-plus"></i> Add More Files
                        </button>
                    </div>

                    <!-- Bank Statement Details -->
                    <div class="form-card">
                        <h4 class="form-card-title"><i class="fa fa-bank"></i> Bank Statement Details</h4>

                        <div id="complete_bank_statements_container">
                            <div class="bank-statement-row">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label><i class="fa fa-arrow-down"></i> Credit</label>
                                        <input type="text" name="<?php echo ($customer_type == 'institution') ? 'corporate_credit' : 'personal_credit'; ?>[]" class="form-control" placeholder="Total credits">
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fa fa-arrow-up"></i> Debit</label>
                                        <input type="text" name="<?php echo ($customer_type == 'institution') ? 'corporate_debit' : 'personal_debit'; ?>[]" class="form-control" placeholder="Total debits">
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fa fa-calendar"></i> Statement Month</label>
                                        <select name="<?php echo ($customer_type == 'institution') ? 'corporate_statement_month' : 'personal_statement_month'; ?>[]" class="form-control">
                                            <option value="">-- Select Month --</option>
                                            <option value="January">January</option>
                                            <option value="February">February</option>
                                            <option value="March">March</option>
                                            <option value="April">April</option>
                                            <option value="May">May</option>
                                            <option value="June">June</option>
                                            <option value="July">July</option>
                                            <option value="August">August</option>
                                            <option value="September">September</option>
                                            <option value="October">October</option>
                                            <option value="November">November</option>
                                            <option value="December">December</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row" style="align-items: flex-end;">
                                    <div class="form-group" style="flex: 2;">
                                        <label><i class="fa fa-upload"></i> Upload Bank Statement</label>
                                        <input type="file" name="<?php echo ($customer_type == 'institution') ? 'corporate_statement_file' : 'personal_statement_file'; ?>[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display: block; padding: 8px;">
                                    </div>
                                    <div class="form-group" style="flex: 0 0 auto;">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.bank-statement-row').remove()" style="border-radius: 8px; padding: 8px 12px;">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn-add-file" onclick="addCompleteBankStatement()">
                            <i class="fa fa-plus"></i> Add Another Statement
                        </button>
                    </div>

                    <!-- Loan Appraisal -->
                    <div class="form-card">
                        <h4 class="form-card-title"><i class="fa fa-clipboard"></i> Loan Appraisal</h4>

                        <div class="form-row">
                            <div class="form-group">
                                <label><i class="fa fa-search"></i> CRB Search Done?</label>
                                <select name="crb_search" class="form-control">
                                    <option value="">-- Select --</option>
                                    <option value="Yes" <?php if($crb_search == 'Yes') echo 'selected'; ?>>Yes</option>
                                    <option value="No" <?php if($crb_search == 'No') echo 'selected'; ?>>No</option>
                                    <option value="Clean" <?php if($crb_search == 'Clean') echo 'selected'; ?>>Clean</option>
                                    <option value="Has Issues" <?php if($crb_search == 'Has Issues') echo 'selected'; ?>>Has Issues</option>
                                    <option value="Not Done" <?php if($crb_search == 'Not Done') echo 'selected'; ?>>Not Done</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-building"></i> PACRA Search Done?</label>
                                <select name="pacra_search" class="form-control">
                                    <option value="">-- Select --</option>
                                    <option value="Yes" <?php if($pacra_search == 'Yes') echo 'selected'; ?>>Yes</option>
                                    <option value="No" <?php if($pacra_search == 'No') echo 'selected'; ?>>No</option>
                                    <option value="Clean" <?php if($pacra_search == 'Clean') echo 'selected'; ?>>Clean</option>
                                    <option value="Has Issues" <?php if($pacra_search == 'Has Issues') echo 'selected'; ?>>Has Issues</option>
                                    <option value="Not Done" <?php if($pacra_search == 'Not Done') echo 'selected'; ?>>Not Done</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-history"></i> Previous Facilities?</label>
                                <select name="previous_facilities" class="form-control">
                                    <option value="">-- Select --</option>
                                    <option value="Yes" <?php if($previous_facilities == 'Yes') echo 'selected'; ?>>Yes</option>
                                    <option value="No" <?php if($previous_facilities == 'No') echo 'selected'; ?>>No</option>
                                    <option value="None" <?php if($previous_facilities == 'None') echo 'selected'; ?>>None</option>
                                    <option value="Performing" <?php if($previous_facilities == 'Performing') echo 'selected'; ?>>Performing</option>
                                    <option value="Non-Performing" <?php if($previous_facilities == 'Non-Performing') echo 'selected'; ?>>Non-Performing</option>
                                    <option value="Closed" <?php if($previous_facilities == 'Closed') echo 'selected'; ?>>Closed</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-comment"></i> Comments on Applicant's Past Loans</label>
                            <textarea name="past_loans_comment" class="form-control ckeditor-field" rows="3" placeholder="Enter any comments about the applicant's previous loan history..."><?php echo $past_loans_comment; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-shield"></i> Security Notes</label>
                            <textarea name="security_notes" class="form-control ckeditor-field" rows="3" placeholder="Enter details about security/collateral offered..."><?php echo $security_notes; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-bank"></i> Notes on Bank Statements</label>
                            <textarea name="bank_statement_notes" class="form-control ckeditor-field" rows="3" placeholder="Enter observations from bank statement analysis..."><?php echo $bank_statement_notes; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-exchange"></i> About Transaction</label>
                            <textarea name="about_transaction" class="form-control ckeditor-field" rows="3" placeholder="Enter details about the transaction/loan purpose..."><?php echo $about_transaction; ?></textarea>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label><i class="fa fa-exclamation-triangle"></i> Risk Analysis</label>
                            <textarea name="risk_analysis" class="form-control ckeditor-field" rows="4" placeholder="Enter risk assessment and mitigation measures..."><?php echo $risk_analysis; ?></textarea>
                        </div>
                    </div>

                    <!-- Collateral Section -->
                    <div class="form-card">
                        <h4 class="form-card-title"><i class="fa fa-shield"></i> Collateral</h4>

                        <div id="complete_collateral_section">
                            <div id="complete_collateral_list">
                                <div class="collateral-controls">
                                    <select id="complete_collateral_select" class="form-control">
                                        <option value="">-- Select collateral to add --</option>
                                    </select>
                                    <button type="button" class="btn-add-collateral" onclick="addCompleteCollateralToLoan()">
                                        <i class="fa fa-plus"></i> Add
                                    </button>
                                    <button type="button" class="btn-new-collateral" onclick="openAddCompleteCollateralModal()">
                                        <i class="fa fa-plus-circle"></i> New
                                    </button>
                                </div>

                                <table class="collateral-table" id="complete_selected_collaterals_table">
                                    <thead>
                                        <tr>
                                            <th>Collateral</th>
                                            <th>Type</th>
                                            <th>Force Sale</th>
                                            <th>Available</th>
                                            <th>Utilize</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="complete_selected_collaterals"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Validation Errors -->
                    <?php if (validation_errors()): ?>
                        <div class="alert alert-danger" style="border-radius: 10px;">
                            <?php echo validation_errors(); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Submit Button -->
                    <button type="submit" name="submit_loan" class="btn-submit-loan" onclick="return confirm('Are you sure you want to complete this loan and submit it for approval?')">
                        <i class="fa fa-check-circle"></i> Complete Loan & Submit for Approval
                    </button>
                </form>
            </div>

            <!-- Info Column (for individual customers) -->
            <?php if ($customer_type == 'individual'): ?>
            <div class="col-lg-3">
                <div class="info-card">
                    <h5 class="info-card-title"><i class="fa fa-search"></i> Customer Info</h5>
                    <div id="customer-results">
                        <p>Loading customer details...</p>
                    </div>
                </div>

                <div class="info-card">
                    <h5 class="info-card-title"><i class="fa fa-list"></i> Active Loans</h5>
                    <ul class="results-list" id="loandd">
                        <li>Loading...</li>
                    </ul>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="info-card">
                    <h5 class="info-card-title"><i class="fa fa-id-card"></i> KYC Details</h5>
                    <table class="table table-sm" id="kyc_data">
                        <tr><td colspan="2" class="text-center" style="opacity:0.7;">Loading KYC...</td></tr>
                    </table>
                </div>
            </div>
            <?php else: ?>
            <div class="col-lg-4">
                <div class="info-card">
                    <h5 class="info-card-title"><i class="fa fa-building"></i> Customer Info</h5>
                    <div id="customer-results">
                        <p><strong><?php echo $loan_customer; ?></strong></p>
                        <p class="text-muted">Type: <?php echo ucfirst($customer_type); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal for Adding New Collateral -->
<div class="modal fade" id="addCompleteCollateralModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-plus-circle"></i> Add New Collateral</h5>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;">&times;</button>
            </div>
            <div class="modal-body">
                <form id="complete_collateral_form">
                    <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
                    <input type="hidden" name="customer_type" value="<?php echo $customer_type; ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Collateral Name *</label>
                            <input type="text" class="form-control" name="collateral_name" required>
                        </div>
                        <div class="form-group">
                            <label>Collateral Type *</label>
                            <select name="collateral_type" class="form-control" required>
                                <option value="">-- Select Type --</option>
                                <option value="Real Estate">Real Estate</option>
                                <option value="Vehicle">Vehicle</option>
                                <option value="Equipment/Machinery">Equipment/Machinery</option>
                                <option value="Inventory/Stock">Inventory/Stock</option>
                                <option value="Cash Deposit">Cash Deposit</option>
                                <option value="Securities/Bonds">Securities/Bonds</option>
                                <option value="Accounts Receivable">Accounts Receivable</option>
                                <option value="Personal Guarantee">Personal Guarantee</option>
                                <option value="Corporate Guarantee">Corporate Guarantee</option>
                                <option value="Life Insurance Policy">Life Insurance Policy</option>
                                <option value="Fixed Deposit">Fixed Deposit</option>
                                <option value="Gold/Precious Metals">Gold/Precious Metals</option>
                                <option value="Intellectual Property">Intellectual Property</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Serial/Registration Number</label>
                            <input type="text" class="form-control" name="collateral_serial">
                        </div>
                        <div class="form-group">
                            <label>Market Value *</label>
                            <input type="number" step="0.01" class="form-control" name="market_value" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Force Sale Value *</label>
                            <input type="number" step="0.01" class="form-control" name="force_sale_value" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius:8px;">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveCompleteCollateral()" style="background:#1e3a5f;border:none;border-radius:8px;">Save Collateral</button>
            </div>
        </div>
    </div>
</div>

<script>
var baseURL = '<?php echo base_url(); ?>';
var customerId = '<?php echo $customer_id; ?>';
var customerType = '<?php echo $customer_type; ?>';

// ==================== COLLATERAL MANAGEMENT ====================
var completeCollaterals = [];
var selectedCompleteCollaterals = [];

// Load collaterals on page load
document.addEventListener('DOMContentLoaded', function() {
    loadCompleteCollaterals();

    <?php if ($customer_type == 'individual'): ?>
    // Load customer info via AJAX
    var xhr = new XMLHttpRequest();
    xhr.open("GET", baseURL + "Individual_customers/view_customer/" + customerId, true);
    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var res = JSON.parse(xhr.responseText);

            var det = '<table class="table table-sm">' +
                '<tr><td>Firstname</td><td>' + res.data.Firstname + '</td></tr>' +
                '<tr><td>Lastname</td><td>' + res.data.Lastname + '</td></tr>' +
                '<tr><td>Gender</td><td>' + res.data.Gender + '</td></tr>' +
                '<tr><td>Date of Birth</td><td>' + res.data.DateOfBirth + '</td></tr>' +
                '<tr><td>Contact No</td><td>' + res.data.PhoneNumber + '</td></tr>' +
                '<tr><td>Profession</td><td>' + res.data.Profession + '</td></tr>' +
                '<tr><td>Source of Income</td><td>' + res.data.SourceOfIncome + '</td></tr>' +
                '</table>';

            document.getElementById("customer-results").innerHTML = det;

            var dd = '';
            res.data.loan.forEach(function (value) {
                var color = 'orange';
                if (value.loan_status === 'ACTIVE') { color = "green"; }
                else if (value.loan_status === 'CLOSED') { color = "red"; }
                dd += '<li><a href="' + baseURL + 'loan/view/' + value.loan_id + '">#' + value.loan_number + '</a> - <span style="color: ' + color + '">' + value.loan_status + '</span></li>';
            });
            document.getElementById("loandd").innerHTML = dd || '<li>No loans found</li>';

            var kyc = '';
            if (res.data.kyc && Object.keys(res.data.kyc).length > 0) {
                kyc += '<tr><td>Photo</td><td><img src="' + baseURL + 'uploads/' + res.data.kyc.photograph + '" alt="" width="100" height="50"></td></tr>' +
                    '<tr><td>ID type</td><td>' + res.data.kyc.IDType + '</td></tr>' +
                    '<tr><td>ID Number</td><td>' + res.data.kyc.IDNumber + '</td></tr>' +
                    '<tr><td>ID issue date</td><td>' + res.data.kyc.IssueDate + '</td></tr>' +
                    '<tr><td>ID Expiry date</td><td>' + res.data.kyc.ExpiryDate + '</td></tr>' +
                    '<tr><td>ID front</td><td><img src="' + baseURL + 'uploads/' + res.data.kyc.id_front + '" alt="" width="100" height="50"></td></tr>' +
                    '<tr><td>ID back</td><td><img src="' + baseURL + 'uploads/' + res.data.kyc.Id_back + '" alt="" width="100" height="50"></td></tr>' +
                    '<tr><td>Sig/fingerprint</td><td><img src="' + baseURL + 'uploads/' + res.data.kyc.signature + '" alt="" width="100" height="50"></td></tr>';
            } else {
                kyc = '<tr><td colspan="2" class="text-center">No KYC data</td></tr>';
            }
            document.getElementById("kyc_data").innerHTML = kyc;
        }
    };
    xhr.send();
    <?php endif; ?>
});

function loadCompleteCollaterals() {
    if (typeof jQuery === 'undefined') {
        setTimeout(loadCompleteCollaterals, 50);
        return;
    }
    $.ajax({
        url: baseURL + 'loan/get_customer_collaterals/' + customerId + '/' + customerType,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            completeCollaterals = response.collaterals || [];
            updateCompleteCollateralDropdown();
        },
        error: function() {
            console.log('No collaterals found or error loading');
        }
    });
}

function updateCompleteCollateralDropdown() {
    var select = $('#complete_collateral_select');
    select.empty().append('<option value="">-- Select collateral to add --</option>');

    completeCollaterals.forEach(function(c) {
        if (selectedCompleteCollaterals.find(function(s) { return s.id == c.id; })) return;
        var available = parseFloat(c.available_balance || c.force_sale_value || 0);
        if (available > 0) {
            select.append('<option value="' + c.id + '">' +
                c.collateral_name + ' (' + c.collateral_type + ') - Available: ' +
                formatCurrency(available) + '</option>');
        }
    });
}

function addCompleteCollateralToLoan() {
    var collateralId = $('#complete_collateral_select').val();
    if (!collateralId) {
        alert('Please select a collateral');
        return;
    }

    var collateral = completeCollaterals.find(function(c) { return c.id == collateralId; });
    if (!collateral) return;

    var available = parseFloat(collateral.available_balance || collateral.force_sale_value);

    selectedCompleteCollaterals.push({
        id: collateral.id,
        name: collateral.collateral_name,
        type: collateral.collateral_type,
        force_sale_value: collateral.force_sale_value,
        available: available,
        amount_to_utilize: available
    });

    renderCompleteSelectedCollaterals();
    updateCompleteCollateralDropdown();
}

function renderCompleteSelectedCollaterals() {
    var tbody = $('#complete_selected_collaterals');
    tbody.empty();

    selectedCompleteCollaterals.forEach(function(c, index) {
        tbody.append(
            '<tr>' +
            '<td>' + c.name + '<input type="hidden" name="collateral_ids[]" value="' + c.id + '"></td>' +
            '<td>' + c.type + '</td>' +
            '<td>' + formatCurrency(c.force_sale_value) + '</td>' +
            '<td>' + formatCurrency(c.available) + '</td>' +
            '<td><input type="number" step="0.01" class="form-control form-control-sm" ' +
                'name="collateral_amounts[]" value="' + c.amount_to_utilize + '" ' +
                'max="' + c.available + '" min="0" style="width:100px;" ' +
                'onchange="updateCompleteCollateralAmount(' + index + ', this.value)"></td>' +
            '<td><button type="button" class="btn btn-danger btn-sm" onclick="removeCompleteCollateral(' + index + ')" style="border-radius:6px;">' +
                '<i class="fa fa-times"></i></button></td>' +
            '</tr>'
        );
    });
}

function updateCompleteCollateralAmount(index, value) {
    var max = selectedCompleteCollaterals[index].available;
    if (parseFloat(value) > max) {
        alert('Amount cannot exceed available balance of ' + formatCurrency(max));
        value = max;
    }
    selectedCompleteCollaterals[index].amount_to_utilize = parseFloat(value);
    renderCompleteSelectedCollaterals();
}

function removeCompleteCollateral(index) {
    selectedCompleteCollaterals.splice(index, 1);
    renderCompleteSelectedCollaterals();
    updateCompleteCollateralDropdown();
}

function openAddCompleteCollateralModal() {
    $('#complete_collateral_form')[0].reset();
    $('#addCompleteCollateralModal').modal('show');
}

function saveCompleteCollateral() {
    var formData = new FormData($('#complete_collateral_form')[0]);

    $.ajax({
        url: baseURL + 'loan/add_customer_collateral',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#addCompleteCollateralModal').modal('hide');
                loadCompleteCollaterals();
                alert('Collateral added successfully');
            } else {
                alert(response.message || 'Error adding collateral');
            }
        },
        error: function() {
            alert('Error saving collateral');
        }
    });
}

function formatCurrency(amount) {
    return parseFloat(amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

// ==================== FILE MANAGEMENT ====================
function addCompleteLoanFile() {
    var container = document.getElementById('loan_forms_complete');
    var newRow = document.createElement('div');
    newRow.className = 'file-upload-row';
    newRow.innerHTML = '<input type="text" name="file_name[]" class="form-control" placeholder="Document name">' +
        '<input type="file" name="loan_files[]" class="form-control" style="display: block">' +
        '<button type="button" class="btn btn-danger btn-sm" onclick="this.closest(\'.file-upload-row\').remove()" style="border-radius:8px;">' +
        '<i class="fa fa-times"></i></button>';
    container.appendChild(newRow);
}

// ==================== BANK STATEMENT MANAGEMENT ====================
function addCompleteBankStatement() {
    var container = document.getElementById('complete_bank_statements_container');
    var creditName = customerType === 'institution' ? 'corporate_credit' : 'personal_credit';
    var debitName = customerType === 'institution' ? 'corporate_debit' : 'personal_debit';
    var monthName = customerType === 'institution' ? 'corporate_statement_month' : 'personal_statement_month';
    var fileName = customerType === 'institution' ? 'corporate_statement_file' : 'personal_statement_file';

    var newRow = document.createElement('div');
    newRow.className = 'bank-statement-row';
    newRow.innerHTML = '<div class="form-row">' +
        '<div class="form-group"><label><i class="fa fa-arrow-down"></i> Credit</label>' +
        '<input type="text" name="' + creditName + '[]" class="form-control" placeholder="Total credits"></div>' +
        '<div class="form-group"><label><i class="fa fa-arrow-up"></i> Debit</label>' +
        '<input type="text" name="' + debitName + '[]" class="form-control" placeholder="Total debits"></div>' +
        '<div class="form-group"><label><i class="fa fa-calendar"></i> Statement Month</label>' +
        '<select name="' + monthName + '[]" class="form-control">' +
        '<option value="">-- Select Month --</option>' +
        '<option value="January">January</option><option value="February">February</option>' +
        '<option value="March">March</option><option value="April">April</option>' +
        '<option value="May">May</option><option value="June">June</option>' +
        '<option value="July">July</option><option value="August">August</option>' +
        '<option value="September">September</option><option value="October">October</option>' +
        '<option value="November">November</option><option value="December">December</option>' +
        '</select></div></div>' +
        '<div class="form-row" style="align-items: flex-end;">' +
        '<div class="form-group" style="flex: 2;"><label><i class="fa fa-upload"></i> Upload Bank Statement</label>' +
        '<input type="file" name="' + fileName + '[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display: block; padding: 8px;"></div>' +
        '<div class="form-group" style="flex: 0 0 auto;">' +
        '<button type="button" class="btn btn-danger btn-sm" onclick="this.closest(\'.bank-statement-row\').remove()" style="border-radius: 8px; padding: 8px 12px;">' +
        '<i class="fa fa-trash"></i></button></div></div>';
    container.appendChild(newRow);
}
</script>
