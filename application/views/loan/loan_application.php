<?php
$loan_types = $this->Loan_products_model->get_all();
$corporate = get_all_by_id('corporate_customers','category','client');
$offtakercorporate = get_all_by_id('corporate_customers','category','off_taker');
$currencies  = get_all('currencies ');
$get_settings = get_by_id('settings','settings_id', '1');

// Check which tab should be active (from URL parameter)
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'individual';
$personal_active = ($active_tab == 'individual' || $active_tab == 'personal') ? 'active' : '';
$corporate_active = ($active_tab == 'corporate') ? 'active' : '';
$personal_show = ($active_tab == 'individual' || $active_tab == 'personal') ? 'show active' : '';
$corporate_show = ($active_tab == 'corporate') ? 'show active' : '';
?>

<style>
/* Page Container */
.loan-app-container {
    background: #f4f6f9;
    min-height: 100vh;
    padding-bottom: 2rem;
}

/* Header Section */
.loan-app-header {
    background: #1e3a5f;
    color: #fff;
    padding: 1.5rem 2rem;
    border-radius: 0 0 20px 20px;
    margin-bottom: 2rem;
}

.loan-app-header h2 {
    margin: 0;
    font-weight: 700;
    font-size: 1.5rem;
    color: #fff !important;
}

.loan-app-header .breadcrumb {
    background: transparent;
    margin: 0;
    padding: 0.5rem 0 0 0;
}

.loan-app-header .breadcrumb-item,
.loan-app-header .breadcrumb-item a {
    color: rgba(255,255,255,0.8);
    font-size: 0.85rem;
}

.loan-app-header .breadcrumb-item.active {
    color: #fff;
}

/* Tab Styling */
.loan-tabs-wrapper {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 1.5rem;
    overflow: hidden;
}

.loan-tabs {
    display: flex;
    border-bottom: 2px solid #e5e7eb;
    margin: 0;
    padding: 0;
    list-style: none;
}

.loan-tabs .nav-item {
    flex: 1;
}

.loan-tabs .nav-link {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1.25rem 1.5rem;
    font-weight: 600;
    font-size: 1rem;
    color: #6b7280;
    background: #f8fafc;
    border: none;
    border-radius: 0;
    transition: all 0.3s ease;
    width: 100%;
    text-align: center;
}

.loan-tabs .nav-link:hover {
    background: #e8eef5;
    color: #1e3a5f;
}

.loan-tabs .nav-link.active {
    background: #1e3a5f;
    color: #fff;
}

.loan-tabs .nav-link i {
    font-size: 1.1rem;
}

/* Tab Content */
.tab-content {
    padding: 0;
    background: transparent;
    box-shadow: none;
}

.tab-pane {
    padding: 0;
}

/* Form Card */
.form-card {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-card-title {
    color: #1e3a5f;
    font-weight: 700;
    font-size: 1.1rem;
    margin-bottom: 1.25rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-card-title i {
    color: #1e3a5f;
}

/* Form Styling */
.loan-form .form-group {
    margin-bottom: 1.25rem;
}

.loan-form label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.loan-form label i {
    margin-right: 0.5rem;
    color: #6b7280;
    width: 16px;
}

.loan-form .form-control,
.loan-form .select2-container--default .select2-selection--single {
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    height: auto;
    min-height: 46px;
}

.loan-form .form-control:focus {
    border-color: #1e3a5f;
    box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.1);
    outline: none;
}

.loan-form select.form-control {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M8 11L3 6h10l-5 5z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    padding-right: 2.5rem;
}

.loan-form textarea.form-control {
    min-height: 100px;
    resize: vertical;
}

/* Row Layout for Form */
.form-row {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.form-row .form-group {
    flex: 1;
    min-width: 200px;
}

/* Submit Button */
.btn-submit-loan {
    background: #1e3a5f;
    color: #fff;
    border: none;
    padding: 1rem 2rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 1rem;
    width: 100%;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-submit-loan:hover {
    background: #2d5a87;
    transform: translateY(-2px);
    box-shadow: 0 5px 20px rgba(30, 58, 95, 0.3);
}

/* Info Cards */
.info-card {
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}

.info-card-title {
    color: #1e3a5f;
    font-weight: 700;
    font-size: 1rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.info-card-title i {
    color: #1e3a5f;
}

.info-card-content {
    color: #6b7280;
    font-size: 0.9rem;
}

/* KYC Card */
.kyc-card {
    background: linear-gradient(180deg, #1e3a5f 0%, #2d5a87 100%);
    color: #fff;
    border-radius: 15px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
}

.kyc-card-title {
    font-weight: 700;
    font-size: 1rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.kyc-card #kyc_data {
    background: rgba(255,255,255,0.1);
    border-radius: 10px;
}

.kyc-card #kyc_data td {
    color: #fff;
    padding: 0.5rem;
    border-color: rgba(255,255,255,0.1);
    font-size: 0.85rem;
}

/* File Upload Section */
.file-upload-section {
    background: #f8fafc;
    border: 2px dashed #e5e7eb;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.file-upload-row {
    display: flex;
    gap: 1rem;
    align-items: flex-end;
    margin-bottom: 0.75rem;
    flex-wrap: wrap;
}

.file-upload-row .form-control {
    flex: 1;
    min-width: 150px;
}

.btn-add-file {
    background: #1e3a5f;
    color: #fff;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-add-file:hover {
    background: #2d5a87;
}

/* Collateral Section */
.collateral-section {
    background: #f8fafc;
    border-radius: 10px;
    padding: 1.25rem;
    margin-top: 1rem;
}

.collateral-section h5 {
    color: #1e3a5f;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.collateral-section .text-muted {
    font-size: 0.85rem;
    margin-bottom: 1rem;
}

.collateral-alert {
    background: #e8eef5;
    border: none;
    border-radius: 10px;
    color: #1e3a5f;
    padding: 1rem;
}

.collateral-controls {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-bottom: 1rem;
}

.collateral-controls select {
    flex: 1;
    min-width: 200px;
}

.collateral-controls .btn {
    white-space: nowrap;
}

.btn-add-collateral {
    background: #059669;
    color: #fff;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.85rem;
}

.btn-new-collateral {
    background: #1e3a5f;
    color: #fff;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.85rem;
}

/* Collateral Table */
.collateral-table {
    width: 100%;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.collateral-table thead th {
    background: #1e3a5f;
    color: #fff;
    font-weight: 600;
    font-size: 0.8rem;
    padding: 0.75rem 0.5rem;
    text-align: left;
}

.collateral-table tbody td {
    padding: 0.6rem 0.5rem;
    border-bottom: 1px solid #e5e7eb;
    font-size: 0.85rem;
}

.collateral-table tbody tr:nth-child(even) {
    background: #f8fafc;
}

/* Results Section */
.results-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.results-list li {
    padding: 0.5rem 0;
    border-bottom: 1px solid #e5e7eb;
    font-size: 0.9rem;
    color: #374151;
}

.results-list li:last-child {
    border-bottom: none;
}

/* Validation Errors */
.validation-errors {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #dc2626;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
}

/* Responsive */
@media (max-width: 992px) {
    .loan-tabs .nav-link {
        padding: 1rem;
        font-size: 0.9rem;
    }

    .loan-tabs .nav-link span {
        display: none;
    }

    .form-row .form-group {
        min-width: 100%;
    }
}

@media (max-width: 576px) {
    .loan-app-header {
        padding: 1rem;
        border-radius: 0 0 15px 15px;
    }

    .form-card {
        padding: 1rem;
    }

    .collateral-controls {
        flex-direction: column;
    }

    .collateral-controls select,
    .collateral-controls .btn {
        width: 100%;
    }
}

/* Modal Styling */
.modal-content {
    border-radius: 15px;
    border: none;
}

.modal-header {
    background: #1e3a5f;
    color: #fff;
    border-radius: 15px 15px 0 0;
    padding: 1rem 1.5rem;
}

.modal-header .close {
    color: #fff;
    opacity: 1;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    border-top: 1px solid #e5e7eb;
    padding: 1rem 1.5rem;
}
</style>

<div class="main-content loan-app-container">
    <!-- Header -->
    <div class="loan-app-header">
        <h2><i class="fa fa-file-text-o"></i> Loan Application</h2>
        <nav class="breadcrumb breadcrumb-dash">
            <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
            <span class="breadcrumb-item">Loans</span>
            <span class="breadcrumb-item active">Create Loan</span>
        </nav>
    </div>

    <div class="container-fluid">
        <!-- Tabs -->
        <div class="loan-tabs-wrapper">
            <ul class="nav loan-tabs" id="loanTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?php echo $personal_active; ?>" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal-loan" type="button" role="tab">
                        <i class="fa fa-user"></i> <span>Personal Loan</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link <?php echo $corporate_active; ?>" id="corporate-tab" data-bs-toggle="tab" data-bs-target="#corporate-loan" type="button" role="tab">
                        <i class="fa fa-building"></i> <span>Corporate Loan</span>
                    </button>
                </li>
            </ul>
        </div>

        <!-- Tab Content -->
        <div class="tab-content" id="loanTabsContent">

            <!-- ==================== PERSONAL LOAN TAB ==================== -->
            <div class="tab-pane fade <?php echo $personal_show; ?>" id="personal-loan" role="tabpanel">
                <div class="row">
                    <!-- Form Column -->
                    <div class="col-lg-6">
                        <form action="<?php echo base_url('loan/create_act')?>" method="POST" enctype="multipart/form-data" class="loan-form">
                            <input type="hidden" name="customer_type" value="individual">

                            <!-- Customer & Loan Details Card -->
                            <div class="form-card">
                                <h4 class="form-card-title"><i class="fa fa-user-circle"></i> Customer & Loan Details</h4>

                                <div class="form-group">
                                    <label><i class="fa fa-user"></i> Select Customer</label>
                                    <select name="customer" id="customer_loan" class="form-control select2" required>
                                        <option value="">-- Select Customer --</option>
                                        <?php foreach ($customers as $c): ?>
                                            <option value="<?php echo $c->id; ?>"><?php echo $c->Firstname . " " . $c->Lastname; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label><i class="fa fa-money"></i> Loan Amount</label>
                                        <input type="number" step="0.01" name="amount" class="form-control" value="<?php echo set_value('amount'); ?>" placeholder="Enter amount" required>
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fa fa-globe"></i> Currency</label>
                                        <select name="currency" class="form-control select2" required>
                                            <option value="">-- Select Currency --</option>
                                            <?php foreach ($currencies as $cu): ?>
                                                <option value="<?php echo $cu->currency_id; ?>"><?php echo $cu->currency_code . ' (' . $cu->country_name . ')'; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label><i class="fa fa-tags"></i> Loan Product</label>
                                    <select name="loan_type" id="personal_loan_type" class="form-control select2" required>
                                        <option value="">-- Select Loan Product --</option>
                                        <?php foreach ($loan_types as $lt): ?>
                                            <option value="<?php echo $lt->loan_product_id; ?>"><?php echo $lt->product_name . " (" . $lt->frequency . " - " . $lt->calculation_type . ")"; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label><i class="fa fa-calendar"></i> Term (Months)</label>
                                        <input type="number" name="months" class="form-control" value="<?php echo set_value('months'); ?>" placeholder="e.g. 12" required>
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fa fa-percent"></i> Interest Rate (%)</label>
                                        <input type="number" step="0.01" name="interest" id="interest" class="form-control" placeholder="e.g. 10" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label><i class="fa fa-calculator"></i> Processing Fee (%)</label>
                                        <input type="number" step="0.01" name="processing_fee" class="form-control" value="<?php echo set_value('processing_fee'); ?>" placeholder="Optional">
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fa fa-calendar-check-o"></i> Start Date</label>
                                        <input type="date" name="loan_date" class="form-control" value="<?php echo set_value('loan_date'); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Documents Card -->
                            <div class="form-card">
                                <h4 class="form-card-title"><i class="fa fa-file-pdf-o"></i> Supporting Documents</h4>

                                <div class="file-upload-section" id="loan_forms">
                                    <div class="file-upload-row">
                                        <input type="text" name="file_name[]" class="form-control" placeholder="Document name">
                                        <input type="file" name="loan_files[]" class="form-control" style="display: block">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeLoanFileRow(this)" style="border-radius:8px;">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                <button type="button" class="btn-add-file" onclick="addloan_files();">
                                    <i class="fa fa-plus"></i> Add More Files
                                </button>
                            </div>

                            <!-- Bank Statement Details Card -->
                            <div class="form-card">
                                <h4 class="form-card-title"><i class="fa fa-bank"></i> Bank Statement Details</h4>

                                <div id="personal_bank_statements_container">
                                    <div class="bank-statement-row" style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; margin-bottom: 10px; background: #fafafa;">
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label><i class="fa fa-arrow-down"></i> Credit</label>
                                                <input type="text" name="personal_credit[]" class="form-control" placeholder="Total credits">
                                            </div>
                                            <div class="form-group">
                                                <label><i class="fa fa-arrow-up"></i> Debit</label>
                                                <input type="text" name="personal_debit[]" class="form-control" placeholder="Total debits">
                                            </div>
                                            <div class="form-group">
                                                <label><i class="fa fa-calendar"></i> Statement Month</label>
                                                <select name="personal_statement_month[]" class="form-control">
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
                                                <input type="file" name="personal_statement_file[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display: block; padding: 8px;">
                                            </div>
                                            <div class="form-group" style="flex: 0 0 auto;">
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removePersonalBankStatement(this)" style="border-radius: 8px; padding: 8px 12px;">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="btn-add-file" onclick="addPersonalBankStatement()">
                                    <i class="fa fa-plus"></i> Add Another Statement
                                </button>
                            </div>

                            <!-- Narration Card -->
                            <div class="form-card">
                                <h4 class="form-card-title"><i class="fa fa-sticky-note-o"></i> Narration</h4>
                                <div class="form-group" style="margin-bottom: 0;">
                                    <textarea name="narration" id="narration" class="form-control ckeditor-field" rows="4" placeholder="Enter loan purpose and additional notes..."></textarea>
                                </div>
                            </div>

                            <!-- Loan Appraisal Card -->
                            <div class="form-card">
                                <h4 class="form-card-title"><i class="fa fa-clipboard-check"></i> Loan Appraisal</h4>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label><i class="fa fa-search"></i> CRB Search Done?</label>
                                        <select name="crb_search" class="form-control">
                                            <option value="">-- Select --</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fa fa-building"></i> PACRA Search Done?</label>
                                        <select name="pacra_search" class="form-control">
                                            <option value="">-- Select --</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fa fa-history"></i> Previous Facilities?</label>
                                        <select name="previous_facilities" class="form-control">
                                            <option value="">-- Select --</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label><i class="fa fa-comment"></i> Comments on Applicant's Past Loans</label>
                                    <textarea name="past_loans_comment" id="past_loans_comment" class="form-control ckeditor-field" rows="3" placeholder="Enter any comments about the applicant's previous loan history..."></textarea>
                                </div>

                                <div class="form-group">
                                    <label><i class="fa fa-shield"></i> Security Notes</label>
                                    <textarea name="security_notes" id="security_notes" class="form-control ckeditor-field" rows="3" placeholder="Enter details about security/collateral offered..."></textarea>
                                </div>

                                <div class="form-group">
                                    <label><i class="fa fa-bank"></i> Notes on Bank Statements</label>
                                    <textarea name="bank_statement_notes" id="bank_statement_notes" class="form-control ckeditor-field" rows="3" placeholder="Enter observations from bank statement analysis..."></textarea>
                                </div>

                                <div class="form-group">
                                    <label><i class="fa fa-exchange"></i> About Transaction</label>
                                    <textarea name="about_transaction" id="about_transaction" class="form-control ckeditor-field" rows="3" placeholder="Enter details about the transaction/loan purpose..."></textarea>
                                </div>

                                <div class="form-group" style="margin-bottom: 0;">
                                    <label><i class="fa fa-exclamation-triangle"></i> Risk Analysis</label>
                                    <textarea name="risk_analysis" id="risk_analysis" class="form-control ckeditor-field" rows="4" placeholder="Enter risk assessment and mitigation measures..."></textarea>
                                </div>
                            </div>

                            <!-- Collateral Section -->
                            <div class="form-card">
                                <h4 class="form-card-title"><i class="fa fa-shield"></i> Collateral</h4>

                                <div id="personal_collateral_section">
                                    <div class="collateral-alert" id="personal_no_customer_selected">
                                        <i class="fa fa-info-circle"></i> Please select a customer first to view their collaterals
                                    </div>

                                    <div id="personal_collateral_list" style="display:none;">
                                        <div class="collateral-controls">
                                            <select id="personal_collateral_select" class="form-control">
                                                <option value="">-- Select collateral to add --</option>
                                            </select>
                                            <button type="button" class="btn-add-collateral" onclick="addPersonalCollateralToLoan()">
                                                <i class="fa fa-plus"></i> Add
                                            </button>
                                            <button type="button" class="btn-new-collateral" onclick="openAddPersonalCollateralModal()">
                                                <i class="fa fa-plus-circle"></i> New
                                            </button>
                                        </div>

                                        <table class="collateral-table" id="personal_selected_collaterals_table">
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
                                            <tbody id="personal_selected_collaterals"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Validation Errors -->
                            <?php if (validation_errors()): ?>
                                <div class="validation-errors">
                                    <?php echo validation_errors(); ?>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($error)): ?>
                                <div class="validation-errors">
                                    <?php echo $error; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Submit Button -->
                            <button type="submit" name="submit_loan" class="btn-submit-loan" onclick="return confirm('Are you sure you want to create this loan?')">
                                <i class="fa fa-check-circle"></i> Create Loan
                            </button>
                        </form>
                    </div>

                    <!-- Info Column -->
                    <div class="col-lg-3">
                        <div class="info-card">
                            <h5 class="info-card-title"><i class="fa fa-search"></i> Customer Info</h5>
                            <div class="info-card-content" id="customer-results">
                                <p class="text-muted">Select a customer to view details</p>
                            </div>
                        </div>

                        <div class="info-card">
                            <h5 class="info-card-title"><i class="fa fa-list"></i> Active Loans</h5>
                            <ul class="results-list" id="loandd">
                                <li class="text-muted">No customer selected</li>
                            </ul>
                        </div>
                    </div>

                    <!-- KYC Column -->
                    <div class="col-lg-3">
                        <div class="kyc-card">
                            <h5 class="kyc-card-title"><i class="fa fa-id-card"></i> KYC Details</h5>
                            <table class="table table-sm" id="kyc_data">
                                <tr><td colspan="2" class="text-center" style="opacity:0.7;">Select customer to view KYC</td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ==================== CORPORATE LOAN TAB ==================== -->
            <div class="tab-pane fade <?php echo $corporate_show; ?>" id="corporate-loan" role="tabpanel">
                <div class="row">
                    <!-- Form Column -->
                    <div class="col-lg-7">
                        <form action="<?php echo base_url('loan/create_act')?>" method="POST" enctype="multipart/form-data" class="loan-form">
                            <input type="hidden" name="customer_type" value="institution">

                            <!-- Customer & Loan Details Card -->
                            <div class="form-card">
                                <h4 class="form-card-title"><i class="fa fa-building"></i> Corporate & Loan Details</h4>

                                <div class="form-group">
                                    <label><i class="fa fa-building"></i> Select Corporate</label>
                                    <select name="customer" id="group_c" class="form-control select2" required>
                                        <option value="">-- Select Corporate Customer --</option>
                                        <?php foreach ($corporate as $c): ?>
                                            <option value="<?php echo $c->id; ?>"><?php echo $c->EntityName . " - " . $c->RegistrationNumber; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label><i class="fa fa-money"></i> Loan Amount</label>
                                        <input type="number" step="0.01" name="amount" class="form-control" value="<?php echo set_value('amount'); ?>" placeholder="Enter amount" required>
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fa fa-globe"></i> Currency</label>
                                        <select name="currency" class="form-control select2" required>
                                            <option value="">-- Select Currency --</option>
                                            <?php foreach ($currencies as $cu): ?>
                                                <option value="<?php echo $cu->currency_id; ?>"><?php echo $cu->currency_code . ' (' . $cu->country_name . ')'; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label><i class="fa fa-tags"></i> Loan Product</label>
                                    <select name="loan_type" id="loan_type" class="form-control select2" required>
                                        <option value="">-- Select Loan Product --</option>
                                        <?php foreach ($loan_types as $lt): ?>
                                            <option value="<?php echo $lt->loan_product_id; ?>" <?php echo ($lt->loan_product_id == $this->input->get('loan_type')) ? 'selected' : ''; ?>>
                                                <?php echo $lt->product_name . " (" . $lt->frequency . " - " . $lt->calculation_type . ")"; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label><i class="fa fa-calendar"></i> Term (Months)</label>
                                        <input type="number" name="months" class="form-control" value="<?php echo set_value('months'); ?>" placeholder="e.g. 12" required>
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fa fa-percent"></i> Interest Rate (%)</label>
                                        <input type="number" step="0.01" name="interest" class="form-control" value="<?php echo set_value('interest'); ?>" placeholder="e.g. 10" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label><i class="fa fa-calculator"></i> Processing Fee (%)</label>
                                        <input type="number" step="0.01" name="processing_fee" class="form-control" value="<?php echo set_value('processing_fee'); ?>" placeholder="Optional">
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fa fa-calendar-check-o"></i> Start Date</label>
                                        <input type="date" name="loan_date" class="form-control" value="<?php echo set_value('loan_date'); ?>" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label><i class="fa fa-handshake-o"></i> Off-Taker Corporate</label>
                                    <select name="off_taker" id="off_taker" class="form-control select2">
                                        <option value="">-- Select Off-Taker (Optional) --</option>
                                        <?php foreach ($offtakercorporate as $c): ?>
                                            <option value="<?php echo $c->id; ?>"><?php echo $c->EntityName . " - " . $c->RegistrationNumber; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Bank Statement Card -->
                            <div class="form-card">
                                <h4 class="form-card-title"><i class="fa fa-bank"></i> Bank Statement Details</h4>

                                <div id="corporate_bank_statements_container">
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
                                                <input type="file" name="corporate_statement_file[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display: block; padding: 8px;">
                                            </div>
                                            <div class="form-group" style="flex: 0 0 auto;">
                                                <button type="button" class="btn btn-danger btn-sm" onclick="removeCorporateBankStatement(this)" style="border-radius: 8px; padding: 8px 12px;">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" class="btn-add-file" onclick="addCorporateBankStatement()">
                                    <i class="fa fa-plus"></i> Add Another Statement
                                </button>
                            </div>

                            <!-- Loan Files Card -->
                            <div class="form-card">
                                <h4 class="form-card-title"><i class="fa fa-file-pdf-o"></i> Loan Documents</h4>

                                <div class="file-upload-section" id="loan_forms1">
                                    <div class="file-upload-row">
                                        <input type="text" name="corporate_file_name[]" class="form-control" placeholder="Document name">
                                        <input type="file" name="corporate_loan_files[]" class="form-control" style="display: block">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeCorporateLoanFileRow(this)" style="border-radius:8px;">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                <button type="button" class="btn-add-file" onclick="addloan_corporate_files();">
                                    <i class="fa fa-plus"></i> Add More Files
                                </button>
                            </div>

                            <!-- Narration Card -->
                            <div class="form-card">
                                <h4 class="form-card-title"><i class="fa fa-sticky-note-o"></i> Narration</h4>
                                <div class="form-group" style="margin-bottom: 0;">
                                    <textarea name="narration" id="narration_corp" class="form-control ckeditor-field" rows="4" placeholder="Enter loan purpose and additional notes..."></textarea>
                                </div>
                            </div>

                            <!-- Loan Appraisal Card -->
                            <div class="form-card">
                                <h4 class="form-card-title"><i class="fa fa-clipboard-check"></i> Loan Appraisal</h4>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label><i class="fa fa-search"></i> CRB Search Done?</label>
                                        <select name="crb_search" class="form-control">
                                            <option value="">-- Select --</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fa fa-building"></i> PACRA Search Done?</label>
                                        <select name="pacra_search" class="form-control">
                                            <option value="">-- Select --</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label><i class="fa fa-history"></i> Previous Facilities?</label>
                                        <select name="previous_facilities" class="form-control">
                                            <option value="">-- Select --</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label><i class="fa fa-comment"></i> Comments on Applicant's Past Loans</label>
                                    <textarea name="past_loans_comment" id="past_loans_comment_corp" class="form-control ckeditor-field" rows="3" placeholder="Enter any comments about the applicant's previous loan history..."></textarea>
                                </div>

                                <div class="form-group">
                                    <label><i class="fa fa-shield"></i> Security Notes</label>
                                    <textarea name="security_notes" id="security_notes_corp" class="form-control ckeditor-field" rows="3" placeholder="Enter details about security/collateral offered..."></textarea>
                                </div>

                                <div class="form-group">
                                    <label><i class="fa fa-bank"></i> Notes on Bank Statements</label>
                                    <textarea name="bank_statement_notes" id="bank_statement_notes_corp" class="form-control ckeditor-field" rows="3" placeholder="Enter observations from bank statement analysis..."></textarea>
                                </div>

                                <div class="form-group">
                                    <label><i class="fa fa-exchange"></i> About Transaction</label>
                                    <textarea name="about_transaction" id="about_transaction_corp" class="form-control ckeditor-field" rows="3" placeholder="Enter details about the transaction/loan purpose..."></textarea>
                                </div>

                                <div class="form-group" style="margin-bottom: 0;">
                                    <label><i class="fa fa-exclamation-triangle"></i> Risk Analysis</label>
                                    <textarea name="risk_analysis" id="risk_analysis_corp" class="form-control ckeditor-field" rows="4" placeholder="Enter risk assessment and mitigation measures..."></textarea>
                                </div>
                            </div>

                            <!-- Collateral Section -->
                            <div class="form-card">
                                <h4 class="form-card-title"><i class="fa fa-shield"></i> Collateral</h4>

                                <div id="corporate_collateral_section">
                                    <div class="collateral-alert" id="corporate_no_customer_selected">
                                        <i class="fa fa-info-circle"></i> Please select a corporate customer first to view their collaterals
                                    </div>

                                    <div id="corporate_collateral_list" style="display:none;">
                                        <div class="collateral-controls">
                                            <select id="corporate_collateral_select" class="form-control">
                                                <option value="">-- Select collateral to add --</option>
                                            </select>
                                            <button type="button" class="btn-add-collateral" onclick="addCorporateCollateralToLoan()">
                                                <i class="fa fa-plus"></i> Add
                                            </button>
                                            <button type="button" class="btn-new-collateral" onclick="openAddCorporateCollateralModal()">
                                                <i class="fa fa-plus-circle"></i> New
                                            </button>
                                        </div>

                                        <table class="collateral-table" id="corporate_selected_collaterals_table">
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
                                            <tbody id="corporate_selected_collaterals"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Validation Errors -->
                            <?php if (validation_errors()): ?>
                                <div class="validation-errors">
                                    <?php echo validation_errors(); ?>
                                </div>
                            <?php endif; ?>
                            <?php if (isset($error)): ?>
                                <div class="validation-errors">
                                    <?php echo $error; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Submit Button -->
                            <button type="submit" name="submit_loan" class="btn-submit-loan" onclick="return confirm('Are you sure you want to create this loan?')">
                                <i class="fa fa-check-circle"></i> Create Loan
                            </button>
                        </form>
                    </div>

                    <!-- Info Column -->
                    <div class="col-lg-5">
                        <div class="info-card">
                            <h5 class="info-card-title"><i class="fa fa-building"></i> Corporate Info</h5>
                            <div class="info-card-content">
                                <ul class="results-list" id="customer_loan1">
                                    <li class="text-muted">Select a corporate to view details</li>
                                </ul>
                            </div>
                        </div>

                        <div class="info-card">
                            <h5 class="info-card-title"><i class="fa fa-list"></i> Active Loans</h5>
                            <ul class="results-list" id="loandd_corporate">
                                <li class="text-muted">No corporate selected</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Adding New Collateral (Personal) -->
<div class="modal fade" id="addPersonalCollateralModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-plus-circle"></i> Add New Collateral</h5>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;">&times;</button>
            </div>
            <div class="modal-body">
                <form id="personal_collateral_form" class="loan-form">
                    <input type="hidden" id="personal_coll_customer_id" name="customer_id">
                    <input type="hidden" name="customer_type" value="individual">
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
                <button type="button" class="btn btn-primary" onclick="savePersonalCollateral()" style="background:#1e3a5f;border:none;border-radius:8px;">Save Collateral</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Adding New Collateral (Corporate) -->
<div class="modal fade" id="addCorporateCollateralModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-plus-circle"></i> Add New Collateral</h5>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;opacity:1;">&times;</button>
            </div>
            <div class="modal-body">
                <form id="corporate_collateral_form" class="loan-form">
                    <input type="hidden" id="corporate_coll_customer_id" name="customer_id">
                    <input type="hidden" name="customer_type" value="institution">
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
                <button type="button" class="btn btn-primary" onclick="saveCorporateCollateral()" style="background:#1e3a5f;border:none;border-radius:8px;">Save Collateral</button>
            </div>
        </div>
    </div>
</div>

<script>
// Store collateral data
var personalCollaterals = [];
var corporateCollaterals = [];
var selectedPersonalCollaterals = [];
var selectedCorporateCollaterals = [];

// Wait for jQuery to be loaded before attaching event handlers
function initCollateralHandlers() {
    if (typeof jQuery === 'undefined') {
        setTimeout(initCollateralHandlers, 50);
        return;
    }

    // Load collaterals when customer is selected (Personal)
    $(document).on('change select2:select', '#customer_loan', function() {
        var customerId = $(this).val();
        if (customerId) {
            $('#personal_no_customer_selected').hide();
            $('#personal_collateral_list').show();
            $('#personal_coll_customer_id').val(customerId);
            loadPersonalCollaterals(customerId);
        } else {
            $('#personal_no_customer_selected').show();
            $('#personal_collateral_list').hide();
        }
    });

    // Load collaterals when customer is selected (Corporate)
    $(document).on('change select2:select', '#group_c', function() {
        var customerId = $(this).val();
        if (customerId) {
            $('#corporate_no_customer_selected').hide();
            $('#corporate_collateral_list').show();
            $('#corporate_coll_customer_id').val(customerId);
            loadCorporateCollaterals(customerId);
        } else {
            $('#corporate_no_customer_selected').show();
            $('#corporate_collateral_list').hide();
        }
    });
}

// Initialize when ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCollateralHandlers);
} else {
    initCollateralHandlers();
}

// Load personal customer collaterals
function loadPersonalCollaterals(customerId) {
    $.ajax({
        url: '<?php echo base_url("loan/get_customer_collaterals"); ?>/' + customerId + '/individual',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            personalCollaterals = response.collaterals || [];
            updatePersonalCollateralDropdown();
        },
        error: function() {
            alert('Error loading collaterals');
        }
    });
}

// Load corporate customer collaterals
function loadCorporateCollaterals(customerId) {
    $.ajax({
        url: '<?php echo base_url("loan/get_customer_collaterals"); ?>/' + customerId + '/institution',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            corporateCollaterals = response.collaterals || [];
            updateCorporateCollateralDropdown();
        },
        error: function() {
            alert('Error loading collaterals');
        }
    });
}

// Update personal collateral dropdown
function updatePersonalCollateralDropdown() {
    var select = $('#personal_collateral_select');
    select.empty().append('<option value="">-- Select collateral to add --</option>');

    personalCollaterals.forEach(function(c) {
        if (selectedPersonalCollaterals.find(s => s.id == c.id)) return;
        var available = parseFloat(c.available_balance || c.force_sale_value || 0);
        if (available > 0) {
            select.append('<option value="' + c.id + '">' +
                c.collateral_name + ' (' + c.collateral_type + ') - Available: ' +
                formatCurrency(available) + '</option>');
        }
    });
}

// Update corporate collateral dropdown
function updateCorporateCollateralDropdown() {
    var select = $('#corporate_collateral_select');
    select.empty().append('<option value="">-- Select collateral to add --</option>');

    corporateCollaterals.forEach(function(c) {
        if (selectedCorporateCollaterals.find(s => s.id == c.id)) return;
        var available = parseFloat(c.available_balance || c.force_sale_value || 0);
        if (available > 0) {
            select.append('<option value="' + c.id + '">' +
                c.collateral_name + ' (' + c.collateral_type + ') - Available: ' +
                formatCurrency(available) + '</option>');
        }
    });
}

// Add personal collateral to loan
function addPersonalCollateralToLoan() {
    var collateralId = $('#personal_collateral_select').val();
    if (!collateralId) {
        alert('Please select a collateral');
        return;
    }

    var collateral = personalCollaterals.find(c => c.id == collateralId);
    if (!collateral) return;

    var available = parseFloat(collateral.available_balance || collateral.force_sale_value);

    selectedPersonalCollaterals.push({
        id: collateral.id,
        name: collateral.collateral_name,
        type: collateral.collateral_type,
        force_sale_value: collateral.force_sale_value,
        available: available,
        amount_to_utilize: available
    });

    renderPersonalSelectedCollaterals();
    updatePersonalCollateralDropdown();
}

// Add corporate collateral to loan
function addCorporateCollateralToLoan() {
    var collateralId = $('#corporate_collateral_select').val();
    if (!collateralId) {
        alert('Please select a collateral');
        return;
    }

    var collateral = corporateCollaterals.find(c => c.id == collateralId);
    if (!collateral) return;

    var available = parseFloat(collateral.available_balance || collateral.force_sale_value);

    selectedCorporateCollaterals.push({
        id: collateral.id,
        name: collateral.collateral_name,
        type: collateral.collateral_type,
        force_sale_value: collateral.force_sale_value,
        available: available,
        amount_to_utilize: available
    });

    renderCorporateSelectedCollaterals();
    updateCorporateCollateralDropdown();
}

// Render personal selected collaterals table
function renderPersonalSelectedCollaterals() {
    var tbody = $('#personal_selected_collaterals');
    tbody.empty();

    selectedPersonalCollaterals.forEach(function(c, index) {
        tbody.append(
            '<tr>' +
            '<td>' + c.name + '<input type="hidden" name="collateral_ids[]" value="' + c.id + '"></td>' +
            '<td>' + c.type + '</td>' +
            '<td>' + formatCurrency(c.force_sale_value) + '</td>' +
            '<td>' + formatCurrency(c.available) + '</td>' +
            '<td><input type="number" step="0.01" class="form-control form-control-sm" ' +
                'name="collateral_amounts[]" value="' + c.amount_to_utilize + '" ' +
                'max="' + c.available + '" min="0" style="width:100px;" ' +
                'onchange="updatePersonalCollateralAmount(' + index + ', this.value)"></td>' +
            '<td><button type="button" class="btn btn-danger btn-sm" onclick="removePersonalCollateral(' + index + ')" style="border-radius:6px;">'+
                '<i class="fa fa-times"></i></button></td>' +
            '</tr>'
        );
    });
}

// Render corporate selected collaterals table
function renderCorporateSelectedCollaterals() {
    var tbody = $('#corporate_selected_collaterals');
    tbody.empty();

    selectedCorporateCollaterals.forEach(function(c, index) {
        tbody.append(
            '<tr>' +
            '<td>' + c.name + '<input type="hidden" name="collateral_ids[]" value="' + c.id + '"></td>' +
            '<td>' + c.type + '</td>' +
            '<td>' + formatCurrency(c.force_sale_value) + '</td>' +
            '<td>' + formatCurrency(c.available) + '</td>' +
            '<td><input type="number" step="0.01" class="form-control form-control-sm" ' +
                'name="collateral_amounts[]" value="' + c.amount_to_utilize + '" ' +
                'max="' + c.available + '" min="0" style="width:100px;" ' +
                'onchange="updateCorporateCollateralAmount(' + index + ', this.value)"></td>' +
            '<td><button type="button" class="btn btn-danger btn-sm" onclick="removeCorporateCollateral(' + index + ')" style="border-radius:6px;">'+
                '<i class="fa fa-times"></i></button></td>' +
            '</tr>'
        );
    });
}

// Update collateral amounts
function updatePersonalCollateralAmount(index, value) {
    var max = selectedPersonalCollaterals[index].available;
    if (parseFloat(value) > max) {
        alert('Amount cannot exceed available balance of ' + formatCurrency(max));
        value = max;
    }
    selectedPersonalCollaterals[index].amount_to_utilize = parseFloat(value);
    renderPersonalSelectedCollaterals();
}

function updateCorporateCollateralAmount(index, value) {
    var max = selectedCorporateCollaterals[index].available;
    if (parseFloat(value) > max) {
        alert('Amount cannot exceed available balance of ' + formatCurrency(max));
        value = max;
    }
    selectedCorporateCollaterals[index].amount_to_utilize = parseFloat(value);
    renderCorporateSelectedCollaterals();
}

// Remove collaterals
function removePersonalCollateral(index) {
    selectedPersonalCollaterals.splice(index, 1);
    renderPersonalSelectedCollaterals();
    updatePersonalCollateralDropdown();
}

function removeCorporateCollateral(index) {
    selectedCorporateCollaterals.splice(index, 1);
    renderCorporateSelectedCollaterals();
    updateCorporateCollateralDropdown();
}

// Modal functions
function openAddPersonalCollateralModal() {
    var customerId = $('#customer_loan').val();
    if (!customerId) {
        alert('Please select a customer first');
        return;
    }
    $('#personal_collateral_form')[0].reset();
    $('#personal_coll_customer_id').val(customerId);
    $('#addPersonalCollateralModal').modal('show');
}

function openAddCorporateCollateralModal() {
    var customerId = $('#group_c').val();
    if (!customerId) {
        alert('Please select a corporate customer first');
        return;
    }
    $('#corporate_collateral_form')[0].reset();
    $('#corporate_coll_customer_id').val(customerId);
    $('#addCorporateCollateralModal').modal('show');
}

// Save collaterals
function savePersonalCollateral() {
    var formData = new FormData($('#personal_collateral_form')[0]);

    $.ajax({
        url: '<?php echo base_url("loan/add_customer_collateral"); ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#addPersonalCollateralModal').modal('hide');
                loadPersonalCollaterals($('#customer_loan').val());
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

function saveCorporateCollateral() {
    var formData = new FormData($('#corporate_collateral_form')[0]);

    $.ajax({
        url: '<?php echo base_url("loan/add_customer_collateral"); ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#addCorporateCollateralModal').modal('hide');
                loadCorporateCollaterals($('#group_c').val());
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

// Format currency
function formatCurrency(amount) {
    return parseFloat(amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

// ==================== LOAN FILE MANAGEMENT ====================
// Add more files for Personal Loan
function addloan_files() {
    var container = document.getElementById('loan_forms');
    var newRow = document.createElement('div');
    newRow.className = 'file-upload-row';
    newRow.innerHTML = '<input type="text" name="file_name[]" class="form-control" placeholder="Document name" required>' +
        '<input type="file" name="loan_files[]" class="form-control">' +
        '<button type="button" class="btn btn-danger btn-sm" onclick="removeLoanFileRow(this)" style="border-radius:8px;">' +
        '<i class="fa fa-times"></i></button>';
    container.appendChild(newRow);
}

// Remove file row for Personal Loan
function removeLoanFileRow(btn) {
    var row = btn.closest('.file-upload-row');
    var container = document.getElementById('loan_forms');
    // Don't remove if it's the only row
    if (container.getElementsByClassName('file-upload-row').length > 1) {
        row.remove();
    }
}

// Add more files for Corporate Loan
function addloan_corporate_files() {
    var container = document.getElementById('loan_forms1');
    var newRow = document.createElement('div');
    newRow.className = 'file-upload-row';
    newRow.innerHTML = '<input type="text" name="corporate_file_name[]" class="form-control" placeholder="Document name">' +
        '<input type="file" name="corporate_loan_files[]" class="form-control">' +
        '<button type="button" class="btn btn-danger btn-sm" onclick="removeCorporateLoanFileRow(this)" style="border-radius:8px;">' +
        '<i class="fa fa-times"></i></button>';
    container.appendChild(newRow);
}

// Remove file row for Corporate Loan
function removeCorporateLoanFileRow(btn) {
    var row = btn.closest('.file-upload-row');
    var container = document.getElementById('loan_forms1');
    // Don't remove if it's the only row
    if (container.getElementsByClassName('file-upload-row').length > 1) {
        row.remove();
    }
}

// ==================== BANK STATEMENT MANAGEMENT ====================
// Add bank statement for Personal Loan
function addPersonalBankStatement() {
    var container = document.getElementById('personal_bank_statements_container');
    var newRow = document.createElement('div');
    newRow.className = 'bank-statement-row';
    newRow.style.cssText = 'border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; margin-bottom: 10px; background: #fafafa;';
    newRow.innerHTML = `
        <div class="form-row">
            <div class="form-group">
                <label><i class="fa fa-arrow-down"></i> Credit</label>
                <input type="text" name="personal_credit[]" class="form-control" placeholder="Total credits">
            </div>
            <div class="form-group">
                <label><i class="fa fa-arrow-up"></i> Debit</label>
                <input type="text" name="personal_debit[]" class="form-control" placeholder="Total debits">
            </div>
            <div class="form-group">
                <label><i class="fa fa-calendar"></i> Statement Month</label>
                <select name="personal_statement_month[]" class="form-control">
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
                <input type="file" name="personal_statement_file[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display: block; padding: 8px;">
            </div>
            <div class="form-group" style="flex: 0 0 auto;">
                <button type="button" class="btn btn-danger btn-sm" onclick="removePersonalBankStatement(this)" style="border-radius: 8px; padding: 8px 12px;">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(newRow);
}

// Remove bank statement for Personal Loan
function removePersonalBankStatement(btn) {
    var row = btn.closest('.bank-statement-row');
    var container = document.getElementById('personal_bank_statements_container');
    // Don't remove if it's the only row
    if (container.getElementsByClassName('bank-statement-row').length > 1) {
        row.remove();
    } else {
        alert('At least one bank statement entry is required.');
    }
}

// Add bank statement for Corporate Loan
function addCorporateBankStatement() {
    var container = document.getElementById('corporate_bank_statements_container');
    var newRow = document.createElement('div');
    newRow.className = 'bank-statement-row';
    newRow.style.cssText = 'border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; margin-bottom: 10px; background: #fafafa;';
    newRow.innerHTML = `
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
                <input type="file" name="corporate_statement_file[]" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display: block; padding: 8px;">
            </div>
            <div class="form-group" style="flex: 0 0 auto;">
                <button type="button" class="btn btn-danger btn-sm" onclick="removeCorporateBankStatement(this)" style="border-radius: 8px; padding: 8px 12px;">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(newRow);
}

// Remove bank statement for Corporate Loan
function removeCorporateBankStatement(btn) {
    var row = btn.closest('.bank-statement-row');
    var container = document.getElementById('corporate_bank_statements_container');
    // Don't remove if it's the only row
    if (container.getElementsByClassName('bank-statement-row').length > 1) {
        row.remove();
    } else {
        alert('At least one bank statement entry is required.');
    }
}
</script>
