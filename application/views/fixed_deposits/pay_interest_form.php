<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="fd-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title">Interest Withdrawal</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo site_url('Fixed_deposits'); ?>">Fixed Deposits</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo site_url('Fixed_deposits/deposit_view/' . $deposit->id); ?>"><?php echo $deposit->deposit_number; ?></a></li>
                    <li class="breadcrumb-item active">Withdraw Interest</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="fd-stats">
        <div class="fd-stat">
            <div class="fd-stat-icon blue"><i class="fas fa-coins"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Accrued Interest</div>
                <div class="fd-stat-value">K <?php echo number_format($current_accrued, 2); ?></div>
            </div>
        </div>
        <div class="fd-stat">
            <div class="fd-stat-icon <?php echo $is_quarter_end ? 'green' : 'red'; ?>">
                <i class="fas fa-<?php echo $is_quarter_end ? 'check-circle' : 'minus-circle'; ?>"></i>
            </div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Penalty (<?php echo $deposit->penalty_rate; ?>%)</div>
                <div class="fd-stat-value"><?php echo $is_quarter_end ? 'K 0.00' : 'K ' . number_format($penalty_preview, 2); ?></div>
            </div>
        </div>
        <div class="fd-stat">
            <div class="fd-stat-icon yellow"><i class="fas fa-landmark"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">WHT (<?php echo $wht_rate; ?>%)</div>
                <div class="fd-stat-value">K <?php echo number_format($wht_amount, 2); ?></div>
            </div>
        </div>
        <div class="fd-stat">
            <div class="fd-stat-icon green"><i class="fas fa-hand-holding-usd"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Net Available</div>
                <div class="fd-stat-value">K <?php echo number_format($net_interest, 2); ?></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Quarter Status Notice -->
            <?php if ($is_quarter_end): ?>
                <div class="fd-notice success">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>Quarter End Window - No Penalty</strong><br>
                        Current quarter ends on <strong><?php echo date('d M Y', strtotime($quarter_end)); ?></strong>.
                        You can withdraw interest <strong>without penalty</strong>.
                    </div>
                </div>
            <?php else: ?>
                <div class="fd-notice warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Before Quarter End - Penalty Applies</strong><br>
                        Quarter ends <strong><?php echo date('d M Y', strtotime($quarter_end)); ?></strong>
                        (<?php echo abs($days_to_quarter_end); ?> days <?php echo $days_to_quarter_end > 0 ? 'remaining' : 'ago'; ?>).
                        Early withdrawal incurs <strong><?php echo $deposit->penalty_rate; ?>%</strong> penalty.
                    </div>
                </div>
            <?php endif; ?>

            <!-- Payment Form -->
            <form method="post" action="<?php echo $action; ?>">
                <input type="hidden" name="deposit_id" value="<?php echo $deposit->id; ?>">

                <?php if ($current_accrued <= 0): ?>
                    <div class="fd-card">
                        <div class="fd-card-body" style="text-align: center; padding: 48px 24px;">
                            <i class="fas fa-info-circle" style="font-size: 40px; color: #dadce0; margin-bottom: 16px;"></i>
                            <h4 style="font-weight: 400; color: #202124; margin-bottom: 8px;">No Interest Available</h4>
                            <p style="color: #5f6368; margin-bottom: 24px;">There is no accrued interest available to withdraw at this time.</p>
                            <a href="<?php echo site_url('Fixed_deposits/deposit_view/' . $deposit->id); ?>" class="btn btn-default">
                                <i class="fas fa-arrow-left mr-1"></i> Back to Deposit
                            </a>
                        </div>
                    </div>
                <?php else: ?>

                    <div class="fd-card">
                        <div class="fd-card-header">
                            <span class="fd-card-title">Withdrawal Amount</span>
                        </div>
                        <div class="fd-card-body">
                            <?php if ($deposit->payment_option == 'QUARTERLY' && !empty($pending_schedules)): ?>
                                <div class="form-group">
                                    <label>Quarter Schedule (Optional)</label>
                                    <select name="schedule_id" id="schedule_id" class="form-control">
                                        <option value="">-- Withdraw All Accrued Interest --</option>
                                        <?php foreach ($pending_schedules as $schedule): ?>
                                            <option value="<?php echo $schedule->id; ?>"
                                                    data-expected="<?php echo $schedule->expected_interest; ?>"
                                                    data-paid="<?php echo $schedule->paid_interest; ?>">
                                                Q<?php echo $schedule->quarter . ' ' . $schedule->year; ?>
                                                - Expected: K <?php echo number_format($schedule->expected_interest, 2); ?>
                                                <?php if ($schedule->paid_interest > 0): ?>
                                                    (Paid: K <?php echo number_format($schedule->paid_interest, 2); ?>)
                                                <?php endif; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small style="color: #5f6368;">Select a quarter or leave blank to withdraw all available interest</small>
                                </div>
                            <?php else: ?>
                                <input type="hidden" name="schedule_id" value="">
                            <?php endif; ?>

                            <div class="form-group">
                                <label>Amount to Withdraw (K) <span style="color: #d93025;">*</span></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">K</span>
                                    </div>
                                    <input type="number" name="payment_amount" id="payment_amount" class="form-control form-control-lg"
                                           step="0.01" min="0.01" max="<?php echo $net_interest; ?>"
                                           value="<?php echo number_format($net_interest, 2, '.', ''); ?>" required>
                                </div>
                                <small style="color: #5f6368;">Maximum: K <?php echo number_format($net_interest, 2); ?> (after penalty & WHT deduction)</small>
                            </div>

                            <!-- Preview -->
                            <hr class="fd-divider">
                            <h6 style="font-weight: 500; color: #5f6368; text-transform: uppercase; font-size: 11px; letter-spacing: 0.8px; margin-bottom: 16px;">Payment Preview</h6>
                            <table class="fd-summary-table">
                                <tr>
                                    <td>Accrued Interest</td>
                                    <td>K <?php echo number_format($current_accrued, 2); ?></td>
                                </tr>
                                <?php if (!$is_quarter_end && $penalty_preview > 0): ?>
                                <tr class="deduction">
                                    <td>Less: Penalty (<?php echo $deposit->penalty_rate; ?>%)</td>
                                    <td>(K <?php echo number_format($penalty_preview, 2); ?>)</td>
                                </tr>
                                <tr>
                                    <td>Interest After Penalty</td>
                                    <td>K <?php echo number_format($interest_after_penalty, 2); ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr class="deduction">
                                    <td>Less: WHT (<?php echo $wht_rate; ?>%)</td>
                                    <td>(K <?php echo number_format($wht_amount, 2); ?>)</td>
                                </tr>
                                <tr class="total">
                                    <td>Net Payable to Customer</td>
                                    <td>K <?php echo number_format($net_interest, 2); ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="fd-card-footer">
                            <button type="submit" class="btn btn-success" onclick="return confirm('Process interest withdrawal of K <?php echo number_format($net_interest, 2); ?>?\n\nWHT (<?php echo $wht_rate; ?>%): K <?php echo number_format($wht_amount, 2); ?><?php if (!$is_quarter_end): ?>\nPenalty: K <?php echo number_format($penalty_preview, 2); ?><?php endif; ?>')">
                                <i class="fas fa-coins mr-1"></i> Process Withdrawal
                            </button>
                            <a href="<?php echo site_url('Fixed_deposits/deposit_view/' . $deposit->id); ?>" class="btn btn-default ml-2">Cancel</a>
                        </div>
                    </div>

                <?php endif; ?>
            </form>

            <!-- Pending Schedules Table -->
            <?php if ($deposit->payment_option == 'QUARTERLY' && !empty($pending_schedules)): ?>
                <div class="fd-card">
                    <div class="fd-card-header">
                        <span class="fd-card-title">Quarterly Schedule</span>
                    </div>
                    <div class="fd-card-body compact">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Quarter</th>
                                    <th>Period</th>
                                    <th style="text-align: right;">Expected</th>
                                    <th style="text-align: right;">Paid</th>
                                    <th style="text-align: right;">Balance</th>
                                    <th style="text-align: center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pending_schedules as $schedule): ?>
                                    <tr>
                                        <td><strong>Q<?php echo $schedule->quarter; ?></strong> <?php echo $schedule->year; ?></td>
                                        <td>
                                            <?php echo date('d M', strtotime($schedule->deposit_start_in_quarter)); ?> -
                                            <?php echo date('d M', strtotime($schedule->quarter_end)); ?>
                                        </td>
                                        <td style="text-align: right;">K <?php echo number_format($schedule->expected_interest, 2); ?></td>
                                        <td style="text-align: right; color: #1e8e3e;">K <?php echo number_format($schedule->paid_interest, 2); ?></td>
                                        <td style="text-align: right; color: #d93025;">
                                            <strong>K <?php echo number_format($schedule->expected_interest - $schedule->paid_interest, 2); ?></strong>
                                        </td>
                                        <td style="text-align: center;">
                                            <?php
                                            $today = date('Y-m-d');
                                            if (strtotime($schedule->quarter_end) < strtotime($today)):
                                            ?>
                                                <span class="badge fd-badge-overdue">Overdue</span>
                                            <?php else: ?>
                                                <span class="badge fd-badge-pending">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
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
                        <tr><td>Principal</td><td>K <?php echo number_format($deposit->current_principal, 2); ?></td></tr>
                        <tr><td>Interest Rate</td><td><?php echo $deposit->interest_rate; ?>%</td></tr>
                        <tr><td>Penalty Rate</td><td><?php echo $deposit->penalty_rate; ?>%</td></tr>
                        <tr><td>Maturity</td><td><?php echo date('d M Y', strtotime($deposit->maturity_date)); ?></td></tr>
                    </table>
                </div>
            </div>

            <!-- Penalty Rules -->
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">Penalty Rules</span>
                </div>
                <div class="fd-card-body">
                    <div style="margin-bottom: 16px;">
                        <span class="badge fd-badge-active">No Penalty</span>
                        <p style="font-size: 13px; color: #5f6368; margin: 6px 0 0 0;">At quarter end (within 7 days)</p>
                    </div>
                    <div>
                        <span class="badge fd-badge-overdue"><?php echo $deposit->penalty_rate; ?>% Penalty</span>
                        <p style="font-size: 13px; color: #5f6368; margin: 6px 0 0 0;">Before quarter end</p>
                    </div>
                    <hr class="fd-divider">
                    <p style="font-size: 12px; color: #5f6368; margin: 0;">
                        <i class="fas fa-info-circle" style="color: #1a73e8;"></i>
                        Quarter end window: 7 days before/after
                    </p>
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
                        <li><a href="<?php echo site_url('Fixed_deposits/withdraw/' . $deposit->id); ?>"><i class="fas fa-hand-holding-usd"></i> Withdraw Principal</a></li>
                        <li><a href="<?php echo site_url('Fixed_deposits/deposit_statement/' . $deposit->id); ?>"><i class="fas fa-file-alt"></i> Statement</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var maxAmount = <?php echo $net_interest; ?>;
    var scheduleSelect = document.getElementById('schedule_id');
    var paymentAmount = document.getElementById('payment_amount');

    if (scheduleSelect) {
        scheduleSelect.addEventListener('change', function() {
            var selected = this.options[this.selectedIndex];
            if (this.value) {
                var expected = parseFloat(selected.getAttribute('data-expected')) || 0;
                var paid = parseFloat(selected.getAttribute('data-paid')) || 0;
                var balance = Math.min(expected - paid, maxAmount);
                paymentAmount.value = balance.toFixed(2);
            } else {
                paymentAmount.value = maxAmount.toFixed(2);
            }
        });
    }
});
</script>
