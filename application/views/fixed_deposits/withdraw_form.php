<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="fd-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Principal Withdrawal</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo site_url('Fixed_deposits'); ?>">Fixed Deposits</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo site_url('Fixed_deposits/deposit_view/' . $deposit->id); ?>"><?php echo $deposit->deposit_number; ?></a></li>
                    <li class="breadcrumb-item active">Withdraw</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="fd-stats">
        <div class="fd-stat">
            <div class="fd-stat-icon blue"><i class="fas fa-money-bill-wave"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Principal</div>
                <div class="fd-stat-value">K <?php echo number_format($deposit->current_principal, 2); ?></div>
            </div>
        </div>
        <div class="fd-stat">
            <div class="fd-stat-icon green"><i class="fas fa-coins"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Accrued Interest</div>
                <div class="fd-stat-value">K <?php echo number_format($current_accrued, 2); ?></div>
            </div>
        </div>
        <div class="fd-stat">
            <div class="fd-stat-icon red"><i class="fas fa-minus-circle"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Penalty (<?php echo $deposit->penalty_rate; ?>%)</div>
                <div class="fd-stat-value">K <?php echo number_format($penalty_preview, 2); ?></div>
            </div>
        </div>
        <div class="fd-stat">
            <div class="fd-stat-icon yellow"><i class="fas fa-landmark"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">WHT (<?php echo $wht_rate; ?>%)</div>
                <div class="fd-stat-value">K <?php echo number_format($wht_amount, 2); ?></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Notice -->
            <div class="fd-notice warning">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <strong>Principal Withdrawal - Penalty & WHT Apply</strong><br>
                    A penalty of <strong><?php echo $deposit->penalty_rate; ?>%</strong> and WHT of <strong><?php echo $wht_rate; ?>%</strong> will be deducted from accrued interest.
                </div>
            </div>

            <!-- Withdrawal Form -->
            <form method="post" action="<?php echo $action; ?>" id="withdrawForm">
                <input type="hidden" name="deposit_id" value="<?php echo $deposit->id; ?>">

                <div class="row">
                    <!-- Full Withdrawal Option -->
                    <div class="col-md-6 mb-3">
                        <div class="fd-radio-card selected" id="cardFull">
                            <div class="fd-radio-header">
                                <input type="radio" name="withdrawal_type" value="FULL" id="typeFull" checked style="margin-right: 8px;">
                                <label for="typeFull" style="margin: 0; font-weight: 500; color: #202124; cursor: pointer;">Full Withdrawal</label>
                            </div>
                            <div class="fd-radio-body">
                                <p style="color: #5f6368; font-size: 13px; margin-bottom: 16px;">Close this deposit completely.</p>
                                <table class="fd-summary-table">
                                    <tr>
                                        <td>Principal</td>
                                        <td>K <?php echo number_format($deposit->current_principal, 2); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Interest (after penalty)</td>
                                        <td>K <?php echo number_format($interest_after_penalty, 2); ?></td>
                                    </tr>
                                    <tr class="deduction">
                                        <td>Less: WHT (<?php echo $wht_rate; ?>%)</td>
                                        <td>(K <?php echo number_format($wht_amount, 2); ?>)</td>
                                    </tr>
                                    <tr>
                                        <td>Net Interest</td>
                                        <td>K <?php echo number_format($net_interest, 2); ?></td>
                                    </tr>
                                    <tr class="total">
                                        <td>Total Payout</td>
                                        <td>K <?php echo number_format($deposit->current_principal + $net_interest, 2); ?></td>
                                    </tr>
                                </table>
                                <div style="margin-top: 12px;">
                                    <span class="badge fd-badge-closed">CLOSED</span>
                                    <span style="font-size: 12px; color: #5f6368; margin-left: 4px;">Deposit will be closed</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Partial Withdrawal Option -->
                    <div class="col-md-6 mb-3">
                        <div class="fd-radio-card" id="cardPartial">
                            <div class="fd-radio-header">
                                <input type="radio" name="withdrawal_type" value="PARTIAL" id="typePartial" style="margin-right: 8px;">
                                <label for="typePartial" style="margin: 0; font-weight: 500; color: #202124; cursor: pointer;">Partial Withdrawal</label>
                            </div>
                            <div class="fd-radio-body">
                                <p style="color: #5f6368; font-size: 13px; margin-bottom: 16px;">Withdraw part, create new facility with remainder.</p>
                                <div class="form-group" id="partialAmountGroup" style="display: none;">
                                    <label>Amount to Withdraw (K)</label>
                                    <input type="number" name="withdraw_amount" id="withdraw_amount" class="form-control form-control-lg"
                                           step="0.01" min="1" max="<?php echo $deposit->current_principal - 1; ?>"
                                           placeholder="Enter amount">
                                    <small style="color: #5f6368;">Max: K <?php echo number_format($deposit->current_principal - 1, 2); ?></small>
                                </div>
                                <div id="partialPreview" style="display: none;">
                                    <table class="fd-summary-table">
                                        <tr>
                                            <td>Withdraw</td>
                                            <td id="preview_withdraw">K 0.00</td>
                                        </tr>
                                        <tr>
                                            <td>Interest (after penalty)</td>
                                            <td>K <?php echo number_format($interest_after_penalty, 2); ?></td>
                                        </tr>
                                        <tr class="deduction">
                                            <td>Less: WHT (<?php echo $wht_rate; ?>%)</td>
                                            <td>(K <?php echo number_format($wht_amount, 2); ?>)</td>
                                        </tr>
                                        <tr>
                                            <td>Net Interest</td>
                                            <td>K <?php echo number_format($net_interest, 2); ?></td>
                                        </tr>
                                        <tr class="total">
                                            <td>Your Payout</td>
                                            <td id="preview_payout">K 0.00</td>
                                        </tr>
                                    </table>
                                    <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #e8eaed;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <span style="font-size: 13px; color: #5f6368;">New Facility</span>
                                            <strong id="preview_new" style="color: #1a73e8;">K 0.00</strong>
                                        </div>
                                    </div>
                                </div>
                                <div style="margin-top: 12px;">
                                    <span class="badge fd-badge-closed">CLOSED</span>
                                    <span style="font-size: 12px; color: #5f6368; margin-left: 2px;">Old deposit</span>
                                    <span class="badge fd-badge-active" style="margin-left: 8px;">NEW</span>
                                    <span style="font-size: 12px; color: #5f6368; margin-left: 2px;">New facility</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fd-card">
                    <div class="fd-card-body">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Notes (Optional)</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Reason for withdrawal..."></textarea>
                        </div>
                    </div>
                    <div class="fd-card-footer">
                        <button type="submit" class="btn btn-warning" onclick="return confirmWithdrawal()">
                            <i class="fas fa-hand-holding-usd mr-1"></i> Process Withdrawal
                        </button>
                        <a href="<?php echo site_url('Fixed_deposits/deposit_view/' . $deposit->id); ?>" class="btn btn-default ml-2">Cancel</a>
                    </div>
                </div>
            </form>
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
                        <tr><td>Maturity</td><td><?php echo date('d M Y', strtotime($deposit->maturity_date)); ?></td></tr>
                        <tr><td>Interest Rate</td><td><?php echo $deposit->interest_rate; ?>%</td></tr>
                        <tr><td>Penalty Rate</td><td><?php echo $deposit->penalty_rate; ?>%</td></tr>
                    </table>
                </div>
            </div>

            <!-- How It Works -->
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">How It Works</span>
                </div>
                <div class="fd-card-body">
                    <p style="font-weight: 500; color: #d93025; margin-bottom: 8px; font-size: 13px;">Full Withdrawal</p>
                    <ul style="font-size: 13px; color: #5f6368; padding-left: 20px; margin-bottom: 16px;">
                        <li>Get entire principal + net interest</li>
                        <li>WHT (<?php echo $wht_rate; ?>%) deducted from interest</li>
                        <li>Deposit is closed permanently</li>
                    </ul>
                    <p style="font-weight: 500; color: #1a73e8; margin-bottom: 8px; font-size: 13px;">Partial Withdrawal</p>
                    <ul style="font-size: 13px; color: #5f6368; padding-left: 20px; margin-bottom: 0;">
                        <li>Get requested amount + net interest</li>
                        <li>WHT (<?php echo $wht_rate; ?>%) deducted from interest</li>
                        <li>Current deposit is closed</li>
                        <li>New facility created with remaining balance</li>
                        <li>New maturity date starts from today</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var principal = <?php echo $deposit->current_principal; ?>;
    var netInterest = <?php echo $net_interest; ?>;

    var radios = document.querySelectorAll('input[name="withdrawal_type"]');
    var partialAmountGroup = document.getElementById('partialAmountGroup');
    var partialPreview = document.getElementById('partialPreview');
    var withdrawAmount = document.getElementById('withdraw_amount');
    var cardFull = document.getElementById('cardFull');
    var cardPartial = document.getElementById('cardPartial');

    function handleWithdrawalTypeChange(value) {
        if (value === 'PARTIAL') {
            partialAmountGroup.style.display = '';
            partialPreview.style.display = '';
            withdrawAmount.required = true;
            withdrawAmount.focus();
            cardFull.classList.remove('selected');
            cardPartial.classList.add('selected');
        } else {
            partialAmountGroup.style.display = 'none';
            partialPreview.style.display = 'none';
            withdrawAmount.required = false;
            cardFull.classList.add('selected');
            cardPartial.classList.remove('selected');
        }
    }

    radios.forEach(function(radio) {
        radio.addEventListener('change', function() {
            handleWithdrawalTypeChange(this.value);
        });
    });

    // Make entire card clickable to select radio button
    [cardFull, cardPartial].forEach(function(card) {
        card.addEventListener('click', function(e) {
            if (e.target.tagName === 'INPUT') return;
            var radio = this.querySelector('input[type="radio"]');
            if (radio && !radio.checked) {
                radio.checked = true;
                handleWithdrawalTypeChange(radio.value);
            }
        });
    });

    withdrawAmount.addEventListener('input', function() {
        var amount = parseFloat(this.value) || 0;
        var remaining = principal - amount;
        var payout = amount + netInterest;

        document.getElementById('preview_withdraw').textContent = 'K ' + amount.toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('preview_payout').textContent = 'K ' + payout.toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('preview_new').textContent = 'K ' + remaining.toLocaleString('en-US', {minimumFractionDigits: 2});
    });
});

function confirmWithdrawal() {
    var type = document.querySelector('input[name="withdrawal_type"]:checked').value;
    if (type === 'FULL') {
        return confirm('Withdraw FULL principal?\n\nThis will CLOSE the deposit permanently.');
    } else {
        var amount = parseFloat(document.getElementById('withdraw_amount').value) || 0;
        if (amount <= 0) { alert('Enter a valid amount.'); return false; }
        var remaining = <?php echo $deposit->current_principal; ?> - amount;
        return confirm('Partial withdrawal of K ' + amount.toLocaleString() + '?\n\n' +
            'Current deposit will be CLOSED\n' +
            'New facility created with K ' + remaining.toLocaleString());
    }
}
</script>
