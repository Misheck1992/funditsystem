<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="fd-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Merge Deposits</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo site_url('Fixed_deposits'); ?>">Fixed Deposits</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo site_url('Fixed_deposits/customer_view/' . $customer->id); ?>"><?php echo $customer->customer_number; ?></a></li>
                    <li class="breadcrumb-item active">Merge</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="fd-stats">
        <div class="fd-stat">
            <div class="fd-stat-icon blue"><i class="fas fa-check-square"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Selected</div>
                <div class="fd-stat-value" id="summary_count">0</div>
            </div>
        </div>
        <div class="fd-stat">
            <div class="fd-stat-icon purple"><i class="fas fa-money-bill-wave"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Total Principal</div>
                <div class="fd-stat-value sm" id="summary_principal">K 0.00</div>
            </div>
        </div>
        <div class="fd-stat">
            <div class="fd-stat-icon green"><i class="fas fa-coins"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Total Accrued</div>
                <div class="fd-stat-value sm" id="summary_accrued">K 0.00</div>
            </div>
        </div>
        <div class="fd-stat">
            <div class="fd-stat-icon yellow"><i class="fas fa-wallet"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">New Deposit</div>
                <div class="fd-stat-value sm" id="summary_total">K 0.00</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form method="post" action="<?php echo $action; ?>" id="mergeForm">
                <input type="hidden" name="customer_id" value="<?php echo $customer->id; ?>">

                <!-- Select Deposits -->
                <div class="fd-card">
                    <div class="fd-card-header">
                        <span class="fd-card-title">Select Deposits to Merge</span>
                        <label style="display: flex; align-items: center; gap: 6px; margin: 0; cursor: pointer; font-size: 13px; color: #5f6368;">
                            <input type="checkbox" id="selectAll"> Select All
                        </label>
                    </div>
                    <div class="fd-card-body compact">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;"></th>
                                    <th>Deposit #</th>
                                    <th style="text-align: right;">Principal</th>
                                    <th style="text-align: right;">Accrued</th>
                                    <th style="text-align: right;">Total</th>
                                    <th style="text-align: center;">Rate</th>
                                    <th style="text-align: right;">Maturity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $this->load->helper('fd');
                                foreach ($deposits as $dep):
                                    $accrued = calculate_accrued_interest($dep);
                                ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="deposit_ids[]" value="<?php echo $dep->id; ?>"
                                                   class="deposit-checkbox"
                                                   data-principal="<?php echo $dep->current_principal; ?>"
                                                   data-accrued="<?php echo $accrued; ?>">
                                        </td>
                                        <td style="font-family: monospace; font-size: 13px;"><?php echo $dep->deposit_number; ?></td>
                                        <td style="text-align: right; color: #1a73e8;"><strong>K <?php echo number_format($dep->current_principal, 2); ?></strong></td>
                                        <td style="text-align: right; color: #1e8e3e;">K <?php echo number_format($accrued, 2); ?></td>
                                        <td style="text-align: right;"><strong>K <?php echo number_format($dep->current_principal + $accrued, 2); ?></strong></td>
                                        <td style="text-align: center;"><?php echo $dep->interest_rate; ?>%</td>
                                        <td style="text-align: right;"><?php echo date('d M Y', strtotime($dep->maturity_date)); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr style="background: #f8f9fa; font-weight: 500;">
                                    <td colspan="2" style="padding: 12px 24px;"><strong>Selected Total</strong></td>
                                    <td style="text-align: right; padding: 12px 24px;"><strong id="total_principal">K 0.00</strong></td>
                                    <td style="text-align: right; padding: 12px 24px;"><strong id="total_accrued">K 0.00</strong></td>
                                    <td style="text-align: right; padding: 12px 24px;"><strong id="total_combined">K 0.00</strong></td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- New Deposit Settings -->
                <div class="fd-card">
                    <div class="fd-card-header">
                        <span class="fd-card-title">New Deposit Settings</span>
                    </div>
                    <div class="fd-card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Interest Rate (%) <span style="color: #d93025;">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="interest_rate" class="form-control" step="0.01" min="0" max="100" value="<?php echo $deposits[0]->interest_rate; ?>" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Duration <span style="color: #d93025;">*</span></label>
                                    <select name="duration_months" class="form-control" required>
                                        <option value="3">3 Months</option>
                                        <option value="6">6 Months</option>
                                        <option value="9">9 Months</option>
                                        <option value="12" selected>12 Months</option>
                                        <option value="18">18 Months</option>
                                        <option value="24">24 Months</option>
                                        <option value="36">36 Months</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Penalty Rate (%) <span style="color: #d93025;">*</span></label>
                                    <div class="input-group">
                                        <input type="number" name="penalty_rate" class="form-control" step="0.01" min="0" max="100" value="<?php echo $deposits[0]->penalty_rate; ?>" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="fd-card-footer">
                        <button type="submit" class="btn btn-primary" onclick="return validateMerge()">
                            <i class="fas fa-compress-arrows-alt mr-1"></i> Merge Selected Deposits
                        </button>
                        <a href="<?php echo site_url('Fixed_deposits/customer_view/' . $customer->id); ?>" class="btn btn-default ml-2">Cancel</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <!-- Merge Preview -->
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">Merge Preview</span>
                </div>
                <div class="fd-card-body compact">
                    <table class="table table-borderless fd-kv-table">
                        <tr><td>Deposits Selected</td><td><strong id="preview_count">0</strong></td></tr>
                        <tr><td>Total Principal</td><td><strong id="preview_principal" style="color: #1a73e8;">K 0.00</strong></td></tr>
                        <tr><td>Total Accrued Interest</td><td><strong id="preview_accrued" style="color: #1e8e3e;">K 0.00</strong></td></tr>
                    </table>
                </div>
                <div class="fd-card-footer" style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #5f6368; font-size: 13px;">New Deposit Amount</span>
                    <strong id="preview_total" style="font-size: 18px; color: #202124;">K 0.00</strong>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">Customer</span>
                </div>
                <div class="fd-card-body compact">
                    <table class="table table-borderless fd-kv-table">
                        <tr><td>Customer #</td><td style="font-family: monospace;"><?php echo $customer->customer_number; ?></td></tr>
                        <tr><td>Name</td><td><strong><?php echo $customer->first_name . ' ' . $customer->last_name; ?></strong></td></tr>
                        <tr><td>Phone</td><td><?php echo $customer->phone_number; ?></td></tr>
                    </table>
                </div>
            </div>

            <!-- How It Works -->
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">How It Works</span>
                </div>
                <div class="fd-card-body">
                    <ol style="font-size: 13px; color: #5f6368; padding-left: 20px; margin: 0;">
                        <li style="margin-bottom: 8px;">Select 2+ deposits to merge</li>
                        <li style="margin-bottom: 8px;">Principal + accrued interest combined</li>
                        <li style="margin-bottom: 8px;">Original deposits marked as "MERGED"</li>
                        <li>New deposit created with combined amount</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var checkboxes = document.querySelectorAll('.deposit-checkbox');
    var selectAll = document.getElementById('selectAll');

    function fmt(n) {
        return n.toLocaleString('en-US', {minimumFractionDigits: 2});
    }

    function updateTotals() {
        var totalPrincipal = 0;
        var totalAccrued = 0;
        var count = 0;

        checkboxes.forEach(function(cb) {
            if (cb.checked) {
                totalPrincipal += parseFloat(cb.getAttribute('data-principal')) || 0;
                totalAccrued += parseFloat(cb.getAttribute('data-accrued')) || 0;
                count++;
            }
        });

        var combined = totalPrincipal + totalAccrued;

        // Table totals
        document.getElementById('total_principal').textContent = 'K ' + fmt(totalPrincipal);
        document.getElementById('total_accrued').textContent = 'K ' + fmt(totalAccrued);
        document.getElementById('total_combined').textContent = 'K ' + fmt(combined);

        // Preview panel
        document.getElementById('preview_count').textContent = count;
        document.getElementById('preview_principal').textContent = 'K ' + fmt(totalPrincipal);
        document.getElementById('preview_accrued').textContent = 'K ' + fmt(totalAccrued);
        document.getElementById('preview_total').textContent = 'K ' + fmt(combined);

        // Summary stat cards
        document.getElementById('summary_count').textContent = count;
        document.getElementById('summary_principal').textContent = 'K ' + fmt(totalPrincipal);
        document.getElementById('summary_accrued').textContent = 'K ' + fmt(totalAccrued);
        document.getElementById('summary_total').textContent = 'K ' + fmt(combined);
    }

    checkboxes.forEach(function(cb) {
        cb.addEventListener('change', updateTotals);
    });

    selectAll.addEventListener('change', function() {
        checkboxes.forEach(function(cb) {
            cb.checked = selectAll.checked;
        });
        updateTotals();
    });
});

function validateMerge() {
    var checked = document.querySelectorAll('.deposit-checkbox:checked').length;
    if (checked < 2) {
        alert('Please select at least 2 deposits to merge.');
        return false;
    }
    return confirm('Merge ' + checked + ' deposits into one new facility?\n\nThis action cannot be undone. Original deposits will be marked as MERGED.');
}
</script>
