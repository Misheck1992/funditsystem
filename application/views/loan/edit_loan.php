<?php
$loan_types = $this->Loan_products_model->get_all();
$currencies = get_all('currencies');
$get_settings = get_by_id('settings','settings_id', '1');
?>

<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Edit Loan - <?php echo $loan_number; ?></h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="<?php echo base_url('loan/track'); ?>">Loans</a>
                <span class="breadcrumb-item active">Edit <?php echo $loan_number; ?></span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-8">
                    <form action="<?php echo base_url('loan/edit_action')?>" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="customer_type" value="individual">
                        <input type="hidden" name="loan_id" value="<?php echo $loan_id; ?>">

                        <h5 style="font-weight: 600; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #e5e7eb;"><i class="fas fa-file-invoice-dollar"></i> Loan Details</h5>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>Customer <span class="text-danger">*</span></label>
                                <?php
                                $selected_customer = null;
                                foreach ($customers as $c) {
                                    if ($c->id == $customer) { $selected_customer = $c; break; }
                                }
                                ?>
                                <input type="hidden" name="customer" value="<?php echo $customer; ?>">
                                <input type="text" class="form-control" value="<?php echo $selected_customer ? $selected_customer->Firstname . ' ' . $selected_customer->Lastname : $customer; ?>" readonly>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Loan Number <span class="text-danger">*</span></label>
                                <input type="hidden" name="loan_number" value="<?php echo $loan_number; ?>">
                                <input type="text" class="form-control" value="<?php echo $loan_number; ?>" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Loan Amount <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="amount" class="form-control" value="<?php echo $loan_principal; ?>" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Currency <span class="text-danger">*</span></label>
                                <select name="currency" class="form-control select2" required>
                                    <option value="">--select--</option>
                                    <?php foreach ($currencies as $cur): ?>
                                        <option value="<?php echo $cur->currency_id; ?>" <?php if($currency == $cur->currency_id) echo "selected"; ?>><?php echo $cur->currency_code . ' - ' . $cur->currency_name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Select Loan Type <span class="text-danger">*</span></label>
                                <select name="loan_type" id="personal_loan_type" class="form-control select2" required>
                                    <option value="">--select--</option>
                                    <?php foreach ($loan_types as $lt): ?>
                                        <option value="<?php echo $lt->loan_product_id; ?>" <?php if($loan_product_id == $lt->loan_product_id) echo "selected"; ?>><?php echo $lt->product_name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-3">
                                <label>Loan Term (Months) <span class="text-danger">*</span></label>
                                <input type="number" name="months" class="form-control" value="<?php echo $loan_period; ?>" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Interest Rate (%) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="interest" class="form-control" value="<?php echo $loan_interest; ?>" required>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Processing Fee</label>
                                <input type="number" step="0.01" name="processing_fee" class="form-control" value="<?php echo $processing_fee; ?>">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Loan Date <span class="text-danger">*</span></label>
                                <input type="date" name="loan_date" class="form-control" value="<?php echo $loan_date; ?>" required>
                            </div>
                        </div>

                        <!-- Bank Statement Details -->
                        <h5 style="font-weight: 600; margin: 20px 0 15px; padding-bottom: 10px; border-bottom: 2px solid #e5e7eb;"><i class="fa fa-university"></i> Bank Statement Details</h5>
                        <div id="edit_bank_statements_container">
                        <?php if(!empty($bank_statements)): ?>
                            <?php foreach($bank_statements as $bs): ?>
                            <div class="bank-statement-row" style="border:1px solid #e0e0e0;border-radius:8px;padding:15px;margin-bottom:10px;background:#fafafa;">
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label>Credit</label>
                                        <input type="text" name="personal_credit[]" class="form-control" value="<?php echo $bs->credit; ?>" placeholder="Total credits">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Debit</label>
                                        <input type="text" name="personal_debit[]" class="form-control" value="<?php echo $bs->debit; ?>" placeholder="Total debits">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Statement Month</label>
                                        <select name="personal_statement_month[]" class="form-control">
                                            <option value="">-- Select Month --</option>
                                            <?php foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m): ?>
                                            <option value="<?php echo $m; ?>" <?php if($bs->month==$m) echo 'selected'; ?>><?php echo $m; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2" style="display:flex;align-items:flex-end;">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeEditBankStatement(this)"><i class="fa fa-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="bank-statement-row" style="border:1px solid #e0e0e0;border-radius:8px;padding:15px;margin-bottom:10px;background:#fafafa;">
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label>Credit</label>
                                        <input type="text" name="personal_credit[]" class="form-control" placeholder="Total credits">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label>Debit</label>
                                        <input type="text" name="personal_debit[]" class="form-control" placeholder="Total debits">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Statement Month</label>
                                        <select name="personal_statement_month[]" class="form-control">
                                            <option value="">-- Select Month --</option>
                                            <?php foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m): ?>
                                            <option value="<?php echo $m; ?>"><?php echo $m; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2" style="display:flex;align-items:flex-end;">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeEditBankStatement(this)"><i class="fa fa-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="addEditBankStatement()" style="margin-bottom:15px;"><i class="fa fa-plus"></i> Add Another Statement</button>

                        <!-- Narration -->
                        <h5 style="font-weight: 600; margin: 20px 0 15px; padding-bottom: 10px; border-bottom: 2px solid #e5e7eb;"><i class="fas fa-pen-alt"></i> Loan Purpose & Narration</h5>
                        <div class="form-group">
                            <label>Narration / Loan Purpose</label>
                            <textarea name="narration" id="narration" class="form-control" rows="3"><?php echo $narration; ?></textarea>
                        </div>

                        <!-- Due Diligence -->
                        <h5 style="font-weight: 600; margin: 20px 0 15px; padding-bottom: 10px; border-bottom: 2px solid #e5e7eb;"><i class="fas fa-search"></i> Due Diligence</h5>
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>CRB Search</label>
                                <select name="crb_search" class="form-control">
                                    <option value="">--select--</option>
                                    <option value="Yes" <?php if($crb_search == 'Yes') echo 'selected'; ?>>Yes</option>
                                    <option value="No" <?php if($crb_search == 'No') echo 'selected'; ?>>No</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>PACRA Search</label>
                                <select name="pacra_search" class="form-control">
                                    <option value="">--select--</option>
                                    <option value="Yes" <?php if($pacra_search == 'Yes') echo 'selected'; ?>>Yes</option>
                                    <option value="No" <?php if($pacra_search == 'No') echo 'selected'; ?>>No</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Previous Facilities</label>
                                <select name="previous_facilities" class="form-control">
                                    <option value="">--select--</option>
                                    <option value="Yes" <?php if($previous_facilities == 'Yes') echo 'selected'; ?>>Yes</option>
                                    <option value="No" <?php if($previous_facilities == 'No') echo 'selected'; ?>>No</option>
                                </select>
                            </div>
                        </div>

                        <!-- Analysis Notes -->
                        <h5 style="font-weight: 600; margin: 20px 0 15px; padding-bottom: 10px; border-bottom: 2px solid #e5e7eb;"><i class="fas fa-clipboard-list"></i> Analysis & Notes</h5>
                        <div class="form-group">
                            <label>Past Loans Comment</label>
                            <textarea name="past_loans_comment" id="past_loans_comment" class="form-control" rows="3"><?php echo $past_loans_comment; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Security Notes</label>
                            <textarea name="security_notes" id="security_notes" class="form-control" rows="3"><?php echo $security_notes; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Bank Statement Notes</label>
                            <textarea name="bank_statement_notes" id="bank_statement_notes" class="form-control" rows="3"><?php echo $bank_statement_notes; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>About Transaction</label>
                            <textarea name="about_transaction" id="about_transaction" class="form-control" rows="3"><?php echo $about_transaction; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Risk Analysis</label>
                            <textarea name="risk_analysis" id="risk_analysis" class="form-control" rows="3"><?php echo $risk_analysis; ?></textarea>
                        </div>

                        <?php if (validation_errors()) : ?>
                            <div class="alert alert-danger"><?php echo validation_errors(); ?></div>
                        <?php endif; ?>
                        <?php if (isset($error)) : ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <br>
                        <input type="submit" name="submit_loan" value="Update Loan" class="btn btn-danger btn-block" onclick="return confirm('Are you sure you want to update this loan?')"/>
                    </form>
                </div>
                <div class="col-lg-4">
                    <div id="customer-results" style="margin-bottom: 15px;">
                        <p>Loading customer details...</p>
                    </div>
                    <div>
                        <h5 style="font-weight: 600;">Booked Loan Products</h5>
                        <ul id="loandd"></ul>
                    </div>
                    <hr>
                    <h5 style="font-weight: 600;">KYC</h5>
                    <table class="table" id="kyc_data"></table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var id = "<?php echo $customer; ?>";
    document.addEventListener('DOMContentLoaded', function() {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "<?php echo base_url()?>Individual_customers/view_customer/" + id, true);
        xhr.setRequestHeader("Content-Type", "application/json");

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var res = JSON.parse(xhr.responseText);

                var det = '<table class="table">' +
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
                    if (value.loan_status === 'ACTIVE') {
                        color = "green";
                    } else if (value.loan_status === 'CLOSED') {
                        color = "red";
                    }
                    dd += '<li><a href="<?php echo base_url('loan/view/')?>' + value.loan_id + '">#' + value.loan_number + '-</a><span style="color: ' + color + '">' + value.loan_status + '</span></li>';
                });

                document.getElementById("loandd").innerHTML = dd;

                var kyc = '';
                if (!isEmpty(res.data.kyc)) {
                    kyc += '<tr><td>Photo</td><td><img src="' + baseURL + 'uploads/' + res.data.kyc.photograph + '" alt="" width="100" height="50"></td></tr>' +
                        '<tr><td>ID type</td><td>' + res.data.kyc.IDType + '</td></tr>' +
                        '<tr><td>ID Number</td><td>' + res.data.kyc.IDNumber + '</td></tr>' +
                        '<tr><td>ID issue date</td><td>' + res.data.kyc.IssueDate + '</td></tr>' +
                        '<tr><td>ID Expiry date</td><td>' + res.data.kyc.ExpiryDate + '</td></tr>' +
                        '<tr><td>ID front</td><td><img src="' + baseURL + 'uploads/' + res.data.kyc.id_front + '" alt="" width="100" height="50"></td></tr>' +
                        '<tr><td>ID back</td><td><img src="' + baseURL + 'uploads/' + res.data.kyc.Id_back + '" alt="" width="100" height="50"></td></tr>' +
                        '<tr><td>Sig/fingerprint</td><td><img src="' + baseURL + 'uploads/' + res.data.kyc.signature + '" alt="" width="100" height="50"></td></tr>';
                }

                document.getElementById("kyc_data").innerHTML = kyc;
            } else if (xhr.readyState === 4 && xhr.status !== 200) {
                alert('Failed to interact with server. Please check internet connection.');
            }
        };

        xhr.send();
    });
</script>
<script>
function addEditBankStatement() {
    var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    var opts = '<option value="">-- Select Month --</option>' + months.map(function(m){ return '<option value="'+m+'">'+m+'</option>'; }).join('');
    var row = document.createElement('div');
    row.className = 'bank-statement-row';
    row.style.cssText = 'border:1px solid #e0e0e0;border-radius:8px;padding:15px;margin-bottom:10px;background:#fafafa;';
    row.innerHTML = '<div class="row">'
        + '<div class="form-group col-md-3"><label>Credit</label><input type="text" name="personal_credit[]" class="form-control" placeholder="Total credits"></div>'
        + '<div class="form-group col-md-3"><label>Debit</label><input type="text" name="personal_debit[]" class="form-control" placeholder="Total debits"></div>'
        + '<div class="form-group col-md-4"><label>Statement Month</label><select name="personal_statement_month[]" class="form-control">' + opts + '</select></div>'
        + '<div class="form-group col-md-2" style="display:flex;align-items:flex-end;"><button type="button" class="btn btn-danger btn-sm" onclick="removeEditBankStatement(this)"><i class="fa fa-trash"></i></button></div>'
        + '</div>';
    document.getElementById('edit_bank_statements_container').appendChild(row);
}
function removeEditBankStatement(btn) {
    var container = document.getElementById('edit_bank_statements_container');
    if (container.getElementsByClassName('bank-statement-row').length > 1) {
        btn.closest('.bank-statement-row').remove();
    } else {
        alert('At least one bank statement entry is required.');
    }
}
</script>
