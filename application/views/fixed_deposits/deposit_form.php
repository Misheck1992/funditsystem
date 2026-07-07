<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="fd-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title"><?php echo $page_title; ?></h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo site_url('Fixed_deposits'); ?>">Fixed Deposits</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo site_url('Fixed_deposits/deposits'); ?>">Deposits</a></li>
                    <li class="breadcrumb-item active">New</li>
                </ol>
            </nav>
        </div>
    </div>

    <form method="post" action="<?php echo $action; ?>" id="depositForm">
        <div class="row">
            <div class="col-lg-8">
                <!-- Customer & Deposit Number -->
                <div class="fd-card">
                    <div class="fd-card-header">
                        <span class="fd-card-title">Customer & Reference</span>
                    </div>
                    <div class="fd-card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Deposit Number</label>
                                    <input type="text" name="deposit_number" class="form-control" value="<?php echo $deposit_number; ?>" readonly style="background: #f8f9fa;">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Customer <span style="color: #d93025;">*</span></label>
                                    <select name="customer_id" class="form-control select2" required>
                                        <option value="">-- Select Customer --</option>
                                        <?php foreach ($customers as $customer): ?>
                                            <option value="<?php echo $customer->id; ?>" <?php echo ($selected_customer == $customer->id || $customer_id == $customer->id) ? 'selected' : ''; ?>>
                                                <?php echo $customer->customer_number . ' - ' . $customer->first_name . ' ' . $customer->last_name; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php echo form_error('customer_id'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Details -->
                <div class="fd-card">
                    <div class="fd-card-header">
                        <span class="fd-card-title">Financial Details</span>
                    </div>
                    <div class="fd-card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Principal Amount (K) <span style="color: #d93025;">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">K</span>
                                        </div>
                                        <input type="number" name="principal_amount" id="principal_amount" class="form-control form-control-lg" value="<?php echo $principal_amount; ?>" step="0.01" min="0" placeholder="0.00" required>
                                    </div>
                                    <?php echo form_error('principal_amount'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Interest Rate (%) <span style="color: #d93025;">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="interest_rate" id="interest_rate" class="form-control form-control-lg" value="<?php echo $interest_rate; ?>" step="0.01" min="0" max="100" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <?php echo form_error('interest_rate'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Penalty Rate (% on interest) <span style="color: #d93025;">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="penalty_rate" class="form-control" value="<?php echo $penalty_rate; ?>" step="0.01" min="0" max="100" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <small style="color: #5f6368;">Applied to interest when withdrawing principal early</small>
                                    <?php echo form_error('penalty_rate'); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Interest Payment Schedule <span style="color: #d93025;">*</span></label>
                                    <select name="payment_option" class="form-control" required>
                                        <option value="QUARTERLY" <?php echo $payment_option == 'QUARTERLY' ? 'selected' : ''; ?>>Quarterly Schedule</option>
                                        <option value="AT_MATURITY" <?php echo $payment_option == 'AT_MATURITY' ? 'selected' : ''; ?>>At Maturity</option>
                                    </select>
                                    <small style="color: #5f6368;">Interest can be withdrawn at quarter end without penalty</small>
                                    <?php echo form_error('payment_option'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Duration & Dates -->
                <div class="fd-card">
                    <div class="fd-card-header">
                        <span class="fd-card-title">Duration & Dates</span>
                    </div>
                    <div class="fd-card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Start Date <span style="color: #d93025;">*</span></label>
                                    <input type="date" name="start_date" id="start_date" class="form-control form-control-lg" value="<?php echo $start_date; ?>" required>
                                    <?php echo form_error('start_date'); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Duration <span style="color: #d93025;">*</span></label>
                                    <select name="duration_months" id="duration_months" class="form-control form-control-lg" required>
                                        <option value="3" <?php echo $duration_months == '3' ? 'selected' : ''; ?>>3 Months</option>
                                        <option value="6" <?php echo $duration_months == '6' ? 'selected' : ''; ?>>6 Months</option>
                                        <option value="9" <?php echo $duration_months == '9' ? 'selected' : ''; ?>>9 Months</option>
                                        <option value="12" <?php echo $duration_months == '12' ? 'selected' : ''; ?>>12 Months</option>
                                        <option value="18" <?php echo $duration_months == '18' ? 'selected' : ''; ?>>18 Months</option>
                                        <option value="24" <?php echo $duration_months == '24' ? 'selected' : ''; ?>>24 Months</option>
                                        <option value="36" <?php echo $duration_months == '36' ? 'selected' : ''; ?>>36 Months</option>
                                    </select>
                                    <?php echo form_error('duration_months'); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Maturity Date</label>
                                    <input type="text" id="maturity_date" class="form-control form-control-lg" readonly placeholder="Auto-calculated" style="background: #f8f9fa;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="fd-card">
                    <div class="fd-card-body">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes about this deposit..."><?php echo $notes; ?></textarea>
                        </div>
                    </div>
                    <div class="fd-card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> <?php echo $button; ?>
                        </button>
                        <a href="<?php echo site_url('Fixed_deposits/deposits'); ?>" class="btn btn-default ml-2">Cancel</a>
                    </div>
                </div>
            </div>

            <!-- Preview Panel -->
            <div class="col-lg-4">
                <!-- Live Preview -->
                <div class="fd-card">
                    <div class="fd-card-header">
                        <span class="fd-card-title">Live Preview</span>
                    </div>
                    <div class="fd-card-body compact">
                        <table class="table table-borderless fd-kv-table">
                            <tr><td>Principal</td><td><strong id="preview_principal" style="color: #1a73e8;">K 0.00</strong></td></tr>
                            <tr><td>Interest Rate</td><td><span id="preview_rate">0</span>%</td></tr>
                            <tr><td>Duration</td><td><span id="preview_duration">0</span> months</td></tr>
                            <tr><td>Total Interest</td><td><strong id="preview_total_interest" style="color: #1e8e3e;">K 0.00</strong></td></tr>
                            <tr><td>Quarterly Interest</td><td><span id="preview_quarterly_interest" style="color: #1a73e8;">K 0.00</span></td></tr>
                        </table>
                    </div>
                    <div class="fd-card-footer" style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #5f6368; font-size: 13px;">Total at Maturity</span>
                        <strong id="preview_total" style="font-size: 18px; color: #202124;">K 0.00</strong>
                    </div>
                </div>

                <!-- Info Cards -->
                <div class="fd-card">
                    <div class="fd-card-header">
                        <span class="fd-card-title">Penalty Rules</span>
                    </div>
                    <div class="fd-card-body">
                        <p style="font-weight: 500; color: #1e8e3e; margin-bottom: 6px; font-size: 13px;">Interest Withdrawal</p>
                        <ul style="font-size: 13px; color: #5f6368; padding-left: 20px; margin-bottom: 12px;">
                            <li><span class="badge fd-badge-active">No Penalty</span> At quarter end</li>
                            <li><span class="badge fd-badge-pending">Penalty</span> Before quarter end</li>
                        </ul>
                        <p style="font-weight: 500; color: #d93025; margin-bottom: 6px; font-size: 13px;">Principal Withdrawal</p>
                        <ul style="font-size: 13px; color: #5f6368; padding-left: 20px; margin-bottom: 0;">
                            <li>Penalty applied on accrued interest</li>
                            <li><strong>Full:</strong> Deposit closed</li>
                            <li><strong>Partial:</strong> New facility created</li>
                        </ul>
                    </div>
                </div>

                <div class="fd-card">
                    <div class="fd-card-header">
                        <span class="fd-card-title">Quick Info</span>
                    </div>
                    <div class="fd-card-body">
                        <ul style="font-size: 13px; color: #5f6368; padding-left: 20px; margin: 0;">
                            <li>Minimum duration: <strong>3 months</strong></li>
                            <li>Interest accrues <strong>daily</strong></li>
                            <li style="margin-top: 8px;"><strong>Quarters:</strong>
                                <br>Q1: Jan-Mar | Q2: Apr-Jun
                                <br>Q3: Jul-Sep | Q4: Oct-Dec
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize select2 if jQuery is available
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.select2').select2({ theme: 'bootstrap4' });
    }

    var principalEl = document.getElementById('principal_amount');
    var rateEl = document.getElementById('interest_rate');
    var durationEl = document.getElementById('duration_months');
    var startDateEl = document.getElementById('start_date');

    function calculateMaturity() {
        var startDate = startDateEl.value;
        var months = parseInt(durationEl.value);

        if (startDate && months) {
            var date = new Date(startDate);
            date.setMonth(date.getMonth() + months);
            document.getElementById('maturity_date').value = date.toISOString().split('T')[0];
        }
    }

    function updatePreview() {
        var principal = parseFloat(principalEl.value) || 0;
        var rate = parseFloat(rateEl.value) || 0;
        var months = parseInt(durationEl.value) || 0;

        var totalInterest = (principal * rate / 100) * (months / 12);
        var quarterlyInterest = (principal * rate / 100) / 4;
        var total = principal + totalInterest;

        var fmt = function(n) { return n.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}); };

        document.getElementById('preview_principal').textContent = 'K ' + fmt(principal);
        document.getElementById('preview_rate').textContent = rate;
        document.getElementById('preview_duration').textContent = months;
        document.getElementById('preview_total_interest').textContent = 'K ' + fmt(totalInterest);
        document.getElementById('preview_quarterly_interest').textContent = 'K ' + fmt(quarterlyInterest);
        document.getElementById('preview_total').textContent = 'K ' + fmt(total);

        calculateMaturity();
    }

    [principalEl, rateEl, durationEl, startDateEl].forEach(function(el) {
        el.addEventListener('change', updatePreview);
        el.addEventListener('keyup', updatePreview);
    });

    updatePreview();
});
</script>
