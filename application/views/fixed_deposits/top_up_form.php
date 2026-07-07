<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="fd-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Top-up Deposit</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo site_url('Fixed_deposits'); ?>">Fixed Deposits</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo site_url('Fixed_deposits/deposit_view/' . $deposit->id); ?>"><?php echo $deposit->deposit_number; ?></a></li>
                    <li class="breadcrumb-item active">Top-up</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="fd-stats">
        <div class="fd-stat">
            <div class="fd-stat-icon blue"><i class="fas fa-money-bill-wave"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Current Principal</div>
                <div class="fd-stat-value sm">K <?php echo number_format($deposit->current_principal, 2); ?></div>
            </div>
        </div>
        <div class="fd-stat">
            <div class="fd-stat-icon teal"><i class="fas fa-percentage"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Interest Rate</div>
                <div class="fd-stat-value"><?php echo $deposit->interest_rate; ?>%</div>
            </div>
        </div>
        <div class="fd-stat">
            <div class="fd-stat-icon green"><i class="fas fa-plus-circle"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Adding</div>
                <div class="fd-stat-value sm" id="summary_topup">K 0.00</div>
            </div>
        </div>
        <div class="fd-stat">
            <div class="fd-stat-icon yellow"><i class="fas fa-wallet"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">New Total</div>
                <div class="fd-stat-value sm" id="summary_new">K <?php echo number_format($deposit->current_principal, 2); ?></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form method="post" action="<?php echo $action; ?>">
                <input type="hidden" name="deposit_id" value="<?php echo $deposit->id; ?>">

                <div class="fd-card">
                    <div class="fd-card-header">
                        <span class="fd-card-title">Add Funds</span>
                    </div>
                    <div class="fd-card-body">
                        <div class="form-group">
                            <label>Top-up Amount (K) <span style="color: #d93025;">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">K</span>
                                </div>
                                <input type="number" name="top_up_amount" id="top_up_amount" class="form-control form-control-lg" step="0.01" min="1" placeholder="0.00" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Reason for top-up..."></textarea>
                        </div>

                        <!-- Preview -->
                        <hr class="fd-divider">
                        <h6 style="font-weight: 500; color: #5f6368; text-transform: uppercase; font-size: 11px; letter-spacing: 0.8px; margin-bottom: 16px;">Balance Preview</h6>
                        <div class="row" style="text-align: center;">
                            <div class="col-md-4">
                                <small style="color: #5f6368; display: block; margin-bottom: 4px;">Current</small>
                                <strong style="color: #1a73e8;">K <?php echo number_format($deposit->current_principal, 2); ?></strong>
                            </div>
                            <div class="col-md-4">
                                <small style="color: #5f6368; display: block; margin-bottom: 4px;">Adding</small>
                                <strong style="color: #1e8e3e;" id="preview_topup">+ K 0.00</strong>
                            </div>
                            <div class="col-md-4">
                                <small style="color: #5f6368; display: block; margin-bottom: 4px;">New Principal</small>
                                <strong style="font-size: 18px; color: #202124;" id="preview_new">K <?php echo number_format($deposit->current_principal, 2); ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="fd-card-footer">
                        <button type="submit" class="btn btn-success" onclick="return confirm('Add funds to this deposit?')">
                            <i class="fas fa-plus-circle mr-1"></i> Add Funds
                        </button>
                        <a href="<?php echo site_url('Fixed_deposits/deposit_view/' . $deposit->id); ?>" class="btn btn-default ml-2">Cancel</a>
                    </div>
                </div>
            </form>

            <!-- Info Notice -->
            <div class="fd-notice info">
                <i class="fas fa-info-circle"></i>
                <div>
                    <strong>What happens when you top-up?</strong>
                    <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                        <li>Principal balance increases immediately</li>
                        <li>Future quarterly interest recalculated</li>
                        <li>Maturity date remains unchanged</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Deposit Info -->
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">Deposit Details</span>
                </div>
                <div class="fd-card-body compact">
                    <table class="table table-borderless fd-kv-table">
                        <tr><td>Deposit #</td><td><strong><?php echo $deposit->deposit_number; ?></strong></td></tr>
                        <tr><td>Customer</td><td><?php echo $deposit->first_name . ' ' . $deposit->last_name; ?></td></tr>
                        <tr><td>Phone</td><td><?php echo $deposit->phone_number; ?></td></tr>
                        <tr><td>Start Date</td><td><?php echo date('d M Y', strtotime($deposit->start_date)); ?></td></tr>
                        <tr><td>Maturity</td><td><?php echo date('d M Y', strtotime($deposit->maturity_date)); ?></td></tr>
                        <tr><td>Interest Rate</td><td><?php echo $deposit->interest_rate; ?>%</td></tr>
                    </table>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">Quick Actions</span>
                </div>
                <div class="fd-card-body compact">
                    <ul class="fd-action-list">
                        <li><a href="<?php echo site_url('Fixed_deposits/deposit_view/' . $deposit->id); ?>"><i class="fas fa-eye"></i> View Deposit</a></li>
                        <li><a href="<?php echo site_url('Fixed_deposits/pay_interest/' . $deposit->id); ?>"><i class="fas fa-coins"></i> Withdraw Interest</a></li>
                        <li><a href="<?php echo site_url('Fixed_deposits/deposit_statement/' . $deposit->id); ?>"><i class="fas fa-file-alt"></i> Statement</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var currentPrincipal = <?php echo $deposit->current_principal; ?>;
    var topUpEl = document.getElementById('top_up_amount');

    topUpEl.addEventListener('keyup', updatePreview);
    topUpEl.addEventListener('change', updatePreview);

    function updatePreview() {
        var topUp = parseFloat(topUpEl.value) || 0;
        var newPrincipal = currentPrincipal + topUp;
        var fmt = function(n) { return n.toLocaleString('en-US', {minimumFractionDigits: 2}); };

        document.getElementById('preview_topup').textContent = '+ K ' + fmt(topUp);
        document.getElementById('preview_new').textContent = 'K ' + fmt(newPrincipal);
        document.getElementById('summary_topup').textContent = 'K ' + fmt(topUp);
        document.getElementById('summary_new').textContent = 'K ' + fmt(newPrincipal);
    }
});
</script>
