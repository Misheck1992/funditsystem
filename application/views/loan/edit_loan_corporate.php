<?php
$loan_types = $this->Loan_products_model->get_all();
$offtakercorporate = get_all_by_id('corporate_customers','category','off_taker');
$currencies  = get_all('currencies ');
$get_settings = get_by_id('settings','settings_id', '1');
?>

<style>
.loan-app-container { background: #f4f6f9; min-height: 100vh; padding-bottom: 2rem; }
.loan-app-header { background: #1e3a5f; color: #fff; padding: 1.5rem 2rem; border-radius: 0 0 20px 20px; margin-bottom: 2rem; }
.loan-app-header h2 { margin: 0; font-weight: 700; font-size: 1.5rem; color: #fff !important; }
.loan-app-header .breadcrumb { background: transparent; margin: 0; padding: 0.5rem 0 0 0; }
.loan-app-header .breadcrumb-item, .loan-app-header .breadcrumb-item a { color: rgba(255,255,255,0.8); font-size: 0.85rem; }
.loan-app-header .breadcrumb-item.active { color: #fff; }
.form-card { background: #fff; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); padding: 1.5rem; margin-bottom: 1.5rem; }
.form-card-title { color: #1e3a5f; font-weight: 700; font-size: 1.1rem; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 2px solid #e5e7eb; display: flex; align-items: center; gap: 0.5rem; }
.loan-form .form-group { margin-bottom: 1.25rem; }
.loan-form label { display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem; font-size: 0.9rem; }
.loan-form label i { margin-right: 0.5rem; color: #6b7280; width: 16px; }
.loan-form .form-control { border: 2px solid #e5e7eb; border-radius: 10px; padding: 0.75rem 1rem; font-size: 0.95rem; transition: all 0.3s ease; height: auto; min-height: 46px; }
.loan-form .form-control:focus { border-color: #1e3a5f; box-shadow: 0 0 0 3px rgba(30,58,95,0.1); outline: none; }
.loan-form select.form-control { cursor: pointer; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 1rem center; padding-right: 2.5rem; }
.loan-form textarea.form-control { min-height: 100px; resize: vertical; }
.form-row { display: flex; gap: 1rem; flex-wrap: wrap; }
.form-row .form-group { flex: 1; min-width: 200px; }
.btn-submit-loan { background: #1e3a5f; color: #fff; border: none; padding: 1rem 2rem; border-radius: 10px; font-weight: 600; font-size: 1rem; width: 100%; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 0.5rem; }
.btn-submit-loan:hover { background: #2d5a87; transform: translateY(-2px); box-shadow: 0 5px 20px rgba(30,58,95,0.3); }
.file-upload-section { background: #f8fafc; border: 2px dashed #e5e7eb; border-radius: 10px; padding: 1rem; margin-bottom: 1rem; }
.file-upload-row { display: flex; gap: 1rem; align-items: flex-end; margin-bottom: 0.75rem; flex-wrap: wrap; }
.file-upload-row .form-control { flex: 1; min-width: 150px; }
.btn-add-file { background: #1e3a5f; color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.85rem; cursor: pointer; transition: all 0.3s ease; }
.btn-add-file:hover { background: #2d5a87; }
.collateral-alert { background: #e8eef5; border: none; border-radius: 10px; color: #1e3a5f; padding: 1rem; }
.collateral-controls { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem; }
.collateral-controls select { flex: 1; min-width: 200px; }
.btn-add-collateral { background: #059669; color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.85rem; }
.btn-new-collateral { background: #1e3a5f; color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.85rem; }
.collateral-table { width: 100%; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
.collateral-table thead th { background: #1e3a5f; color: #fff; font-weight: 600; font-size: 0.8rem; padding: 0.75rem 0.5rem; text-align: left; }
.collateral-table tbody td { padding: 0.6rem 0.5rem; border-bottom: 1px solid #e5e7eb; font-size: 0.85rem; }
.collateral-table tbody tr:nth-child(even) { background: #f8fafc; }
.readonly-field { background: #f8fafc !important; color: #6b7280; cursor: not-allowed; }
.info-card { background: #fff; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); padding: 1.25rem; margin-bottom: 1.5rem; }
.info-card-title { color: #1e3a5f; font-weight: 700; font-size: 1rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
.validation-errors { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; border-radius: 10px; padding: 1rem; margin-bottom: 1rem; }
@media (max-width: 576px) { .form-row .form-group { min-width: 100%; } }
</style>

<div class="main-content loan-app-container">
    <div class="loan-app-header">
        <h2><i class="fa fa-edit"></i> Edit Corporate Loan</h2>
        <nav class="breadcrumb breadcrumb-dash">
            <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
            <a href="<?php echo base_url('loan/track')?>" class="breadcrumb-item">Loans</a>
            <span class="breadcrumb-item active">Edit <?php echo $loan_number; ?></span>
        </nav>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Form Column -->
            <div class="col-lg-8">
                <form action="<?php echo base_url('loan/edit_corporate_action')?>" method="POST" enctype="multipart/form-data" class="loan-form">
                    <input type="hidden" name="loan_id" value="<?php echo $loan_id; ?>">
                    <input type="hidden" name="original_loan_number" value="<?php echo $loan_number; ?>">
                    <input type="hidden" name="customer_type" value="institution">
                    <input type="hidden" name="customer" value="<?php echo $customer_id; ?>">

                    <!-- Corporate & Loan Details Card -->
                    <div class="form-card">
                        <h4 class="form-card-title"><i class="fa fa-building"></i> Corporate & Loan Details</h4>

                        <!-- Read-only: Loan Number -->
                        <div class="form-group">
                            <label><i class="fa fa-hashtag"></i> Loan Number</label>
                            <input type="text" class="form-control readonly-field" value="<?php echo $loan_number; ?>" readonly>
                        </div>

                        <!-- Read-only: Corporate Customer -->
                        <div class="form-group">
                            <label><i class="fa fa-building"></i> Corporate Customer</label>
                            <input type="text" class="form-control readonly-field" value="<?php echo $loan_customer; ?>" readonly>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label><i class="fa fa-money"></i> Loan Amount</label>
                                <input type="number" step="0.01" name="amount" class="form-control" value="<?php echo $loan_principal; ?>" placeholder="Enter amount" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-globe"></i> Currency</label>
                                <select name="currency" class="form-control select2" required>
                                    <option value="">-- Select Currency --</option>
                                    <?php foreach ($currencies as $cu): ?>
                                        <option value="<?php echo $cu->currency_id; ?>" <?php echo ($currency == $cu->currency_id) ? 'selected' : ''; ?>>
                                            <?php echo $cu->currency_code . ' (' . $cu->country_name . ')'; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-tags"></i> Loan Product</label>
                            <select name="loan_type" id="corp_edit_loan_type" class="form-control select2" required>
                                <option value="">-- Select Loan Product --</option>
                                <?php foreach ($loan_types as $lt): ?>
                                    <option value="<?php echo $lt->loan_product_id; ?>"
                                        data-calc-type="<?php echo $lt->calculation_type; ?>"
                                        <?php echo ($lt->loan_product_id == $loan_product_id) ? 'selected' : ''; ?>>
                                        <?php echo $lt->product_name . " (" . $lt->frequency . " - " . $lt->calculation_type . ")"; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label id="corp_edit_term_label"><i class="fa fa-calendar"></i> Term (Months)</label>
                                <input type="number" name="months" class="form-control" value="<?php echo $loan_period; ?>" placeholder="e.g. 12" required>
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-percent"></i> Interest Rate (%)</label>
                                <input type="number" step="0.01" name="interest" class="form-control" value="<?php echo $loan_interest; ?>" placeholder="e.g. 10" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label><i class="fa fa-calculator"></i> Processing Fee (%)</label>
                                <input type="number" step="0.01" name="processing_fee" class="form-control" value="<?php echo $processing_fee; ?>" placeholder="Optional">
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-calendar-check-o"></i> Start Date</label>
                                <input type="date" name="loan_date" class="form-control" value="<?php echo $loan_date; ?>" required>
                            </div>
                        </div>

                        <!-- Bullet Payment Extra Fields (shown only when bullet payment product selected) -->
                        <div id="corp_edit_bullet_fields" style="display:none;">
                            <div class="form-row">
                                <div class="form-group">
                                    <label><i class="fa fa-percent"></i> WHT (%)</label>
                                    <input type="number" step="0.01" name="wht" class="form-control" value="<?php echo isset($wht) ? $wht : ''; ?>" placeholder="Withholding Tax %">
                                </div>
                                <div class="form-group">
                                    <label><i class="fa fa-map-marker"></i> Chieftaincy / Area</label>
                                    <input type="text" name="chieftaincy" class="form-control" value="<?php echo isset($chieftaincy) ? $chieftaincy : ''; ?>" placeholder="Enter chieftaincy or area">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-handshake-o"></i> Off-Taker Corporate</label>
                            <select name="off_taker" id="corp_edit_off_taker" class="form-control select2">
                                <option value="">-- Select Off-Taker (Optional) --</option>
                                <?php foreach ($offtakercorporate as $c): ?>
                                    <option value="<?php echo $c->id; ?>" <?php echo ($off_taker == $c->id) ? 'selected' : ''; ?>>
                                        <?php echo $c->EntityName . " - " . $c->RegistrationNumber; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Bank Statement Card -->
                    <div class="form-card">
                        <h4 class="form-card-title"><i class="fa fa-bank"></i> Bank Statement Details</h4>
                        <p style="color:#6b7280; font-size:0.85rem; margin-bottom:1rem;">Upload new or updated bank statements. Existing statements are preserved.</p>

                        <div id="corp_edit_bank_statements_container">
                            <div class="bank-statement-row" style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; margin-bottom: 10px; background: #fafafa;">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label><i class="fa fa-arrow-down"></i> Credit</label>
                                        <input type="text" name="corporate_credit[]" class="form-control" placeholder="Total credits">
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fa fa-arrow-up"></i> Debit</label>
                                        <input type="text" name="corporate_debit[]" class="form-control" placeholder="Total debits">
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fa fa-calendar"></i> Statement Month</label>
                                        <select name="corporate_statement_month[]" class="form-control">
                                            <option value="">-- Select Month --</option>
                                            <?php foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m): ?>
                                                <option value="<?php echo $m; ?>"><?php echo $m; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row" style="align-items: flex-end;">
                                    <div class="form-group" style="flex: 2;">
                                        <label><i class="fa fa-upload"></i> Upload Bank Statement</label>
                                        <input type="file" name="corporate_statement_file[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display: block; padding: 8px;">
                                    </div>
                                    <div class="form-group" style="flex: 0 0 auto;">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="corpEditRemoveBankStatement(this)" style="border-radius: 8px; padding: 8px 12px;">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn-add-file" onclick="corpEditAddBankStatement()">
                            <i class="fa fa-plus"></i> Add Another Statement
                        </button>
                    </div>

                    <!-- Loan Documents Card -->
                    <div class="form-card">
                        <h4 class="form-card-title"><i class="fa fa-file-pdf-o"></i> Loan Documents</h4>
                        <p style="color:#6b7280; font-size:0.85rem; margin-bottom:1rem;">Upload new or replacement documents. Existing documents are preserved.</p>

                        <div class="file-upload-section" id="corp_edit_loan_forms">
                            <div class="file-upload-row">
                                <input type="text" name="corporate_file_name[]" class="form-control" placeholder="Document name">
                                <input type="file" name="corporate_loan_files[]" class="form-control" style="display: block">
                                <button type="button" class="btn btn-danger btn-sm" onclick="corpEditRemoveLoanFileRow(this)" style="border-radius:8px;">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>

                        <button type="button" class="btn-add-file" onclick="corpEditAddLoanFiles();">
                            <i class="fa fa-plus"></i> Add More Files
                        </button>
                    </div>

                    <!-- Narration Card -->
                    <div class="form-card">
                        <h4 class="form-card-title"><i class="fa fa-sticky-note-o"></i> Narration</h4>
                        <div class="form-group" style="margin-bottom: 0;">
                            <textarea name="narration" id="corp_edit_narration" class="form-control ckeditor-field" rows="4" placeholder="Enter loan purpose and additional notes..."><?php echo htmlspecialchars($narration); ?></textarea>
                        </div>
                    </div>

                    <!-- Loan Appraisal Card -->
                    <div class="form-card">
                        <h4 class="form-card-title"><i class="fa fa-clipboard-check"></i> Loan Appraisal</h4>

                        <div class="form-row">
                            <div class="form-group">
                                <label><i class="fa fa-search"></i> CRB Search</label>
                                <select name="crb_search" class="form-control">
                                    <option value="">-- Select --</option>
                                    <option value="Yes" <?php echo ($crb_search == 'Yes') ? 'selected' : ''; ?>>Yes</option>
                                    <option value="No" <?php echo ($crb_search == 'No') ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-building"></i> PACRA Search</label>
                                <select name="pacra_search" class="form-control">
                                    <option value="">-- Select --</option>
                                    <option value="Yes" <?php echo ($pacra_search == 'Yes') ? 'selected' : ''; ?>>Yes</option>
                                    <option value="No" <?php echo ($pacra_search == 'No') ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><i class="fa fa-history"></i> Previous Facilities</label>
                                <select name="previous_facilities" class="form-control">
                                    <option value="">-- Select --</option>
                                    <option value="Yes" <?php echo ($previous_facilities == 'Yes') ? 'selected' : ''; ?>>Yes</option>
                                    <option value="No" <?php echo ($previous_facilities == 'No') ? 'selected' : ''; ?>>No</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-comment"></i> Comments on Applicant's Past Loans</label>
                            <textarea name="past_loans_comment" id="corp_edit_past_loans" class="form-control ckeditor-field" rows="3" placeholder="Enter any comments about the applicant's previous loan history..."><?php echo htmlspecialchars($past_loans_comment); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-shield"></i> Security Notes</label>
                            <textarea name="security_notes" id="corp_edit_security_notes" class="form-control ckeditor-field" rows="3" placeholder="Enter details about security/collateral offered..."><?php echo htmlspecialchars($security_notes); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-bank"></i> Notes on Bank Statements</label>
                            <textarea name="bank_statement_notes" id="corp_edit_bank_notes" class="form-control ckeditor-field" rows="3" placeholder="Enter observations from bank statement analysis..."><?php echo htmlspecialchars($bank_statement_notes); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label><i class="fa fa-exchange"></i> About Transaction</label>
                            <textarea name="about_transaction" id="corp_edit_about_transaction" class="form-control ckeditor-field" rows="3" placeholder="Enter details about the transaction/loan purpose..."><?php echo htmlspecialchars($about_transaction); ?></textarea>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label><i class="fa fa-exclamation-triangle"></i> Risk Analysis</label>
                            <textarea name="risk_analysis" id="corp_edit_risk_analysis" class="form-control ckeditor-field" rows="4" placeholder="Enter risk assessment and mitigation measures..."><?php echo htmlspecialchars($risk_analysis); ?></textarea>
                        </div>
                    </div>

                    <!-- Collateral Section -->
                    <div class="form-card">
                        <h4 class="form-card-title"><i class="fa fa-shield"></i> Collateral</h4>

                        <div id="corp_edit_collateral_section">
                            <div id="corp_edit_collateral_list">
                                <div class="collateral-controls">
                                    <select id="corp_edit_collateral_select" class="form-control">
                                        <option value="">-- Select collateral to add --</option>
                                    </select>
                                    <button type="button" class="btn-add-collateral" onclick="corpEditAddCollateral()">
                                        <i class="fa fa-plus"></i> Add
                                    </button>
                                </div>

                                <table class="collateral-table" id="corp_edit_collaterals_table">
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
                                    <tbody id="corp_edit_collaterals_body"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <?php if (validation_errors()): ?>
                        <div class="validation-errors"><?php echo validation_errors(); ?></div>
                    <?php endif; ?>

                    <button type="submit" name="submit_loan_edit" class="btn-submit-loan" onclick="return confirm('Are you sure you want to update this loan?')">
                        <i class="fa fa-check-circle"></i> Update Loan
                    </button>
                </form>
            </div>

            <!-- Info Column -->
            <div class="col-lg-4">
                <div class="info-card">
                    <h5 class="info-card-title"><i class="fa fa-building"></i> Current Loan Details</h5>
                    <table class="table table-sm" style="font-size:0.9rem;">
                        <tr><td><strong>Loan Number</strong></td><td><?php echo $loan_number; ?></td></tr>
                        <tr><td><strong>Customer</strong></td><td><?php echo $loan_customer; ?></td></tr>
                        <tr><td><strong>Principal</strong></td><td><?php echo number_format($loan_principal, 2); ?></td></tr>
                        <tr><td><strong>Period</strong></td><td><?php echo $loan_period; ?> <?php echo $period_type; ?></td></tr>
                        <tr><td><strong>Interest</strong></td><td><?php echo $loan_interest; ?>%</td></tr>
                        <tr><td><strong>Product</strong></td><td><?php echo $loan_product; ?></td></tr>
                        <tr><td><strong>Status</strong></td><td><?php echo $loan_status; ?></td></tr>
                        <tr><td><strong>Calculation</strong></td><td><?php echo isset($calculation_type) ? $calculation_type : 'N/A'; ?></td></tr>
                    </table>
                </div>

                <?php if(isset($payments) && !empty($payments)): ?>
                <div class="info-card">
                    <h5 class="info-card-title"><i class="fa fa-list"></i> Payment Schedule</h5>
                    <div style="overflow-x:auto;">
                        <table class="table table-sm" style="font-size:0.85rem;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Due Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($payments as $payment): ?>
                                <tr>
                                    <td><?php echo $payment->payment_number; ?></td>
                                    <td><?php echo date('d M Y', strtotime($payment->payment_schedule)); ?></td>
                                    <td><?php echo number_format($payment->amount, 2); ?></td>
                                    <td><?php echo $payment->status; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// ===== Bullet Payment field toggle =====
var corpEditLoanTypeSelect = document.getElementById('corp_edit_loan_type');
var corpEditBulletFields   = document.getElementById('corp_edit_bullet_fields');
var corpEditTermLabel      = document.getElementById('corp_edit_term_label');

function corpEditCheckBullet() {
    var selected = corpEditLoanTypeSelect.options[corpEditLoanTypeSelect.selectedIndex];
    var calcType = selected ? selected.getAttribute('data-calc-type') : '';
    if (calcType === 'Bullet Payment') {
        corpEditBulletFields.style.display = 'block';
        corpEditTermLabel.innerHTML = '<i class="fa fa-calendar"></i> Term (Months)';
    } else {
        corpEditBulletFields.style.display = 'none';
        corpEditTermLabel.innerHTML = '<i class="fa fa-calendar"></i> Term (Months)';
    }
}

// Run on page load (pre-selected product)
<?php
$preselected_calc = '';
foreach ($loan_types as $lt) {
    if ($lt->loan_product_id == $loan_product_id) {
        $preselected_calc = $lt->calculation_type;
        break;
    }
}
?>
(function() {
    var preCalc = '<?php echo $preselected_calc; ?>';
    if (preCalc === 'Bullet Payment') {
        corpEditBulletFields.style.display = 'block';
    }
})();

corpEditLoanTypeSelect.addEventListener('change', corpEditCheckBullet);

// ===== Bank Statement =====
function corpEditAddBankStatement() {
    var container = document.getElementById('corp_edit_bank_statements_container');
    var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    var options = months.map(function(m){ return '<option value="'+m+'">'+m+'</option>'; }).join('');
    var div = document.createElement('div');
    div.className = 'bank-statement-row';
    div.style.cssText = 'border:1px solid #e0e0e0;border-radius:8px;padding:15px;margin-bottom:10px;background:#fafafa;';
    div.innerHTML = '<div class="form-row">'
        + '<div class="form-group"><label><i class="fa fa-arrow-down"></i> Credit</label><input type="text" name="corporate_credit[]" class="form-control" placeholder="Total credits"></div>'
        + '<div class="form-group"><label><i class="fa fa-arrow-up"></i> Debit</label><input type="text" name="corporate_debit[]" class="form-control" placeholder="Total debits"></div>'
        + '<div class="form-group"><label><i class="fa fa-calendar"></i> Statement Month</label><select name="corporate_statement_month[]" class="form-control"><option value="">-- Select Month --</option>' + options + '</select></div>'
        + '</div>'
        + '<div class="form-row" style="align-items:flex-end;">'
        + '<div class="form-group" style="flex:2;"><label><i class="fa fa-upload"></i> Upload Bank Statement</label><input type="file" name="corporate_statement_file[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display:block;padding:8px;"></div>'
        + '<div class="form-group" style="flex:0 0 auto;"><button type="button" class="btn btn-danger btn-sm" onclick="corpEditRemoveBankStatement(this)" style="border-radius:8px;padding:8px 12px;"><i class="fa fa-trash"></i></button></div>'
        + '</div>';
    container.appendChild(div);
}

function corpEditRemoveBankStatement(btn) {
    var row = btn.closest('.bank-statement-row');
    if (row) row.remove();
}

// ===== Loan Documents =====
function corpEditAddLoanFiles() {
    var section = document.getElementById('corp_edit_loan_forms');
    var div = document.createElement('div');
    div.className = 'file-upload-row';
    div.innerHTML = '<input type="text" name="corporate_file_name[]" class="form-control" placeholder="Document name">'
        + '<input type="file" name="corporate_loan_files[]" class="form-control" style="display:block;">'
        + '<button type="button" class="btn btn-danger btn-sm" onclick="corpEditRemoveLoanFileRow(this)" style="border-radius:8px;"><i class="fa fa-times"></i></button>';
    section.appendChild(div);
}

function corpEditRemoveLoanFileRow(btn) {
    var row = btn.closest('.file-upload-row');
    if (row) row.remove();
}

// ===== Collateral =====
var corpEditCollaterals = [];
var corpEditSelectedCollaterals = [];

(function loadCorpEditCollaterals() {
    var customerId = '<?php echo $customer_id; ?>';
    if (!customerId) return;
    fetch('<?php echo base_url('loan/get_corporate_collaterals/'); ?>' + customerId)
        .then(function(r){ return r.json(); })
        .then(function(data) {
            corpEditCollaterals = data.collaterals || [];
            updateCorpEditCollateralDropdown();
        })
        .catch(function(){});
})();

function updateCorpEditCollateralDropdown() {
    var sel = document.getElementById('corp_edit_collateral_select');
    sel.innerHTML = '<option value="">-- Select collateral to add --</option>';
    var linkedIds = corpEditSelectedCollaterals.map(function(c){ return c.id; });
    corpEditCollaterals.forEach(function(c) {
        var avail = parseFloat(c.available_balance || c.force_sale_value || 0);
        if (!linkedIds.includes(c.id) && avail > 0 && c.status === 'ACTIVE') {
            var opt = document.createElement('option');
            opt.value = c.id;
            opt.textContent = c.collateral_name + ' (' + c.collateral_type + ')';
            sel.appendChild(opt);
        }
    });
}

function corpEditAddCollateral() {
    var sel = document.getElementById('corp_edit_collateral_select');
    var id = sel.value;
    if (!id) return;
    var collateral = corpEditCollaterals.find(function(c){ return c.id == id; });
    if (!collateral) return;
    collateral.utilize = parseFloat(collateral.available_balance || collateral.force_sale_value || 0);
    corpEditSelectedCollaterals.push(collateral);
    renderCorpEditCollaterals();
    updateCorpEditCollateralDropdown();
}

function renderCorpEditCollaterals() {
    var tbody = document.getElementById('corp_edit_collaterals_body');
    tbody.innerHTML = '';
    corpEditSelectedCollaterals.forEach(function(c, idx) {
        var tr = document.createElement('tr');
        tr.innerHTML = '<td>' + c.collateral_name + '<input type="hidden" name="collateral_ids[]" value="' + c.id + '"></td>'
            + '<td>' + c.collateral_type + '</td>'
            + '<td>' + parseFloat(c.force_sale_value || 0).toFixed(2) + '</td>'
            + '<td>' + parseFloat(c.available_balance || c.force_sale_value || 0).toFixed(2) + '</td>'
            + '<td><input type="number" step="0.01" name="collateral_utilize[]" class="form-control form-control-sm" value="' + c.utilize.toFixed(2) + '" style="width:100px;" onchange="corpEditUpdateUtilize(' + idx + ', this.value)"></td>'
            + '<td><button type="button" class="btn btn-danger btn-sm" onclick="corpEditRemoveCollateral(' + idx + ')"><i class="fa fa-times"></i></button></td>';
        tbody.appendChild(tr);
    });
}

function corpEditUpdateUtilize(idx, val) {
    corpEditSelectedCollaterals[idx].utilize = parseFloat(val) || 0;
}

function corpEditRemoveCollateral(idx) {
    corpEditSelectedCollaterals.splice(idx, 1);
    renderCorpEditCollaterals();
    updateCorpEditCollateralDropdown();
}
</script>
