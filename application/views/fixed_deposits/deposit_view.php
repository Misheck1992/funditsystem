<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php
// Pre-calculate values
$daily_rate = $deposit->interest_rate / 100 / 365;
$daily_interest = $deposit->current_principal * $daily_rate;
$start_from = !empty($deposit->last_accrual_date) ? $deposit->last_accrual_date : $deposit->start_date;
$days_accrued = max(0, floor((strtotime(date('Y-m-d')) - strtotime($start_from)) / 86400));

// Quarter end check
$current_quarter = get_current_quarter();
$quarter_dates = get_quarter_dates($current_quarter['quarter'], $current_quarter['year']);
$days_to_quarter_end = floor((strtotime($quarter_dates['end']) - strtotime(date('Y-m-d'))) / 86400);
$is_quarter_end = ($days_to_quarter_end <= 7 && $days_to_quarter_end >= -7);
$penalty_on_interest = calculate_penalty($current_accrued, $deposit->penalty_rate);
$net_interest = $current_accrued - ($is_quarter_end ? 0 : $penalty_on_interest);

$status_map = array('ACTIVE' => 'fd-badge-active', 'MATURED' => 'fd-badge-matured', 'CLOSED' => 'fd-badge-closed', 'MERGED' => 'fd-badge-pending');
$status_class = isset($status_map[$deposit->status]) ? $status_map[$deposit->status] : 'fd-badge-closed';
?>

<div class="fd-page">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <div class="d-flex align-items-center" style="gap: 12px;">
                <h1 class="page-title"><?php echo $deposit->deposit_number; ?></h1>
                <span class="badge <?php echo $status_class; ?>"><?php echo $deposit->status; ?></span>
            </div>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo site_url('Fixed_deposits'); ?>">Fixed Deposits</a></li>
                    <li class="breadcrumb-item active"><?php echo $deposit->deposit_number; ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if ($this->session->flashdata('message')): ?>
        <div class="fd-notice success">
            <i class="fas fa-check-circle"></i>
            <div><?php echo $this->session->flashdata('message'); ?></div>
        </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
        <div class="fd-notice danger">
            <i class="fas fa-exclamation-circle"></i>
            <div><?php echo $this->session->flashdata('error'); ?></div>
        </div>
    <?php endif; ?>

    <!-- Stat Cards -->
    <div class="fd-stats">
        <div class="fd-stat">
            <div class="fd-stat-icon blue"><i class="fas fa-money-bill-wave"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Current Principal</div>
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
            <div class="fd-stat-icon purple"><i class="fas fa-percentage"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Interest Rate</div>
                <div class="fd-stat-value"><?php echo $deposit->interest_rate; ?>%</div>
            </div>
        </div>
        <div class="fd-stat">
            <div class="fd-stat-icon <?php echo $days_to_maturity <= 30 ? 'yellow' : 'gray'; ?>"><i class="fas fa-calendar-check"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Days to Maturity</div>
                <div class="fd-stat-value"><?php echo $days_to_maturity; ?></div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-4">
            <!-- Deposit Details -->
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">Deposit Details</span>
                </div>
                <div class="fd-card-body compact">
                    <table class="table table-sm fd-kv-table mb-0">
                        <tr>
                            <td>Customer</td>
                            <td><a href="<?php echo site_url('Fixed_deposits/customer_view/' . $deposit->customer_id); ?>"><?php echo $deposit->first_name . ' ' . $deposit->last_name; ?></a></td>
                        </tr>
                        <tr><td>Phone</td><td><?php echo $deposit->phone_number; ?></td></tr>
                        <tr><td>Start Date</td><td><?php echo date('d M Y', strtotime($deposit->start_date)); ?></td></tr>
                        <tr><td>Maturity</td><td><?php echo date('d M Y', strtotime($deposit->maturity_date)); ?></td></tr>
                        <tr><td>Duration</td><td><?php echo $deposit->duration_months; ?> months</td></tr>
                        <tr>
                            <td>Payment</td>
                            <td>
                                <?php if ($deposit->payment_option == 'QUARTERLY'): ?>
                                    <span class="badge fd-badge-matured">Quarterly</span>
                                <?php else: ?>
                                    <span class="badge fd-badge-closed">At Maturity</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr><td>Penalty Rate</td><td><?php echo $deposit->penalty_rate; ?>%</td></tr>
                    </table>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">Financial Summary</span>
                </div>
                <div class="fd-card-body">
                    <table class="fd-summary-table">
                        <tr><td>Original Principal</td><td>K <?php echo number_format($deposit->principal_amount, 2); ?></td></tr>
                        <tr><td><strong>Current Principal</strong></td><td><strong>K <?php echo number_format($deposit->current_principal, 2); ?></strong></td></tr>
                        <tr><td style="color: #1e8e3e;"><strong>Accrued Interest</strong></td><td style="color: #1e8e3e;"><strong>K <?php echo number_format($current_accrued, 2); ?></strong></td></tr>
                        <tr><td>Interest Paid</td><td>K <?php echo number_format($deposit->paid_interest, 2); ?></td></tr>
                        <tr class="total"><td>Total Value</td><td>K <?php echo number_format($deposit->current_principal + $current_accrued, 2); ?></td></tr>
                    </table>
                </div>
            </div>

            <!-- Quick Actions -->
            <?php if ($deposit->status == 'ACTIVE'): ?>
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">Quick Actions</span>
                    <?php if ($is_quarter_end): ?>
                        <small style="color: #1e8e3e;"><i class="fas fa-check-circle"></i> No penalty</small>
                    <?php else: ?>
                        <small style="color: #e37400;"><i class="fas fa-info-circle"></i> <?php echo $deposit->penalty_rate; ?>% penalty</small>
                    <?php endif; ?>
                </div>
                <div class="fd-card-body compact">
                    <ul class="fd-action-list">
                        <?php if ($current_accrued > 0): ?>
                        <li>
                            <a href="<?php echo site_url('Fixed_deposits/pay_interest/' . $deposit->id); ?>">
                                <i class="fas fa-coins" style="color: #1e8e3e;"></i>
                                Withdraw Interest
                                <span class="fd-action-value">K <?php echo number_format($is_quarter_end ? $current_accrued : $net_interest, 2); ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <li><a href="<?php echo site_url('Fixed_deposits/top_up/' . $deposit->id); ?>"><i class="fas fa-plus-circle" style="color: #1a73e8;"></i> Top-up Deposit</a></li>
                        <li><a href="<?php echo site_url('Fixed_deposits/withdraw/' . $deposit->id); ?>"><i class="fas fa-hand-holding-usd" style="color: #d93025;"></i> Withdraw Principal</a></li>
                        <li><a href="<?php echo site_url('Fixed_deposits/deposit_statement/' . $deposit->id); ?>"><i class="fas fa-file-alt"></i> View Statement</a></li>
                        <li><a href="<?php echo site_url('Fixed_deposits/placement_certificate/' . $deposit->id); ?>" target="_blank"><i class="fas fa-certificate"></i> Placement Certificate</a></li>
                    </ul>
                </div>
            </div>
            <?php else: ?>
            <div class="fd-card">
                <div class="fd-card-body compact">
                    <ul class="fd-action-list">
                        <li><a href="<?php echo site_url('Fixed_deposits/deposit_statement/' . $deposit->id); ?>"><i class="fas fa-file-alt"></i> View Statement</a></li>
                        <li><a href="<?php echo site_url('Fixed_deposits/placement_certificate/' . $deposit->id); ?>" target="_blank"><i class="fas fa-certificate"></i> Placement Certificate</a></li>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Right Column -->
        <div class="col-lg-8">
            <!-- Daily Interest Calculation -->
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">Daily Interest Calculation</span>
                </div>
                <div class="fd-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div style="background: #f8f9fa; border-radius: 8px; padding: 16px; margin-bottom: 16px; border-left: 3px solid #1a73e8;">
                                <p style="margin: 0 0 4px; font-size: 13px; color: #5f6368;">Formula: <code>Principal x (Rate / 365)</code></p>
                                <p style="margin: 0; font-size: 14px;">
                                    K <?php echo number_format($deposit->current_principal, 2); ?> x <?php echo number_format($daily_rate, 8); ?>
                                    = <strong style="color: #1e8e3e;">K <?php echo number_format($daily_interest, 2); ?>/day</strong>
                                </p>
                            </div>
                            <table class="fd-summary-table">
                                <tr><td>Daily Interest</td><td style="color: #1e8e3e;"><strong>K <?php echo number_format($daily_interest, 2); ?></strong></td></tr>
                                <tr><td>Weekly (7 days)</td><td>K <?php echo number_format($daily_interest * 7, 2); ?></td></tr>
                                <tr><td>Monthly (30 days)</td><td>K <?php echo number_format($daily_interest * 30, 2); ?></td></tr>
                                <tr><td>Quarterly (91 days)</td><td>K <?php echo number_format($daily_interest * 91, 2); ?></td></tr>
                                <tr><td>Yearly (365 days)</td><td>K <?php echo number_format($daily_interest * 365, 2); ?></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div style="background: #f8f9fa; border-radius: 8px; padding: 16px; margin-bottom: 16px; border-left: 3px solid #1e8e3e;">
                                <p style="margin: 0 0 4px; font-size: 13px; color: #5f6368;">Current Accrual Period</p>
                                <p style="margin: 0; font-size: 14px;">Since <?php echo date('d M Y', strtotime($start_from)); ?> = <strong><?php echo $days_accrued; ?> days</strong></p>
                            </div>
                            <div style="background: #e6f4ea; border-radius: 8px; padding: 20px; text-align: center;">
                                <div style="font-size: 12px; color: #1e8e3e; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Total Accrued Interest</div>
                                <div style="font-size: 28px; font-weight: 500; color: #1e8e3e;">K <?php echo number_format($current_accrued, 2); ?></div>
                                <div style="font-size: 12px; color: #5f6368; margin-top: 4px;">K <?php echo number_format($daily_interest, 2); ?> x <?php echo $days_accrued; ?> days</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Accrual Ledger -->
            <?php if ($deposit->status == 'ACTIVE'): ?>
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">Daily Accrual Ledger</span>
                    <span class="badge fd-badge-matured">Last 14 Days</span>
                </div>
                <div class="fd-card-body compact" style="max-height: 350px; overflow-y: auto;">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th class="text-right">Principal</th>
                                <th class="text-right">Daily Rate</th>
                                <th class="text-right">Interest</th>
                                <th class="text-right">Running Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $accrual_start = !empty($deposit->last_accrual_date) ? $deposit->last_accrual_date : $deposit->start_date;
                            $daily_rate_decimal = $deposit->interest_rate / 100 / 365;
                            $daily_interest_amount = $deposit->current_principal * $daily_rate_decimal;
                            $today = new DateTime();
                            $start_dt = new DateTime($accrual_start);
                            $deposit_start_dt = new DateTime($deposit->start_date);
                            if ($start_dt < $deposit_start_dt) $start_dt = $deposit_start_dt;

                            $running_total = 0;
                            $ledger_entries = array();
                            $current_date = clone $start_dt;

                            while ($current_date <= $today) {
                                $running_total += $daily_interest_amount;
                                $ledger_entries[] = array(
                                    'date' => $current_date->format('Y-m-d'),
                                    'interest' => $daily_interest_amount,
                                    'running' => $running_total
                                );
                                $current_date->modify('+1 day');
                            }

                            $display_entries = array_slice($ledger_entries, -14);

                            if (empty($display_entries)):
                            ?>
                            <tr><td colspan="5" class="text-center" style="padding: 24px; color: #5f6368;">No accruals yet</td></tr>
                            <?php else: ?>
                                <?php foreach ($display_entries as $entry): ?>
                                <tr<?php echo ($entry['date'] == date('Y-m-d')) ? ' style="background: #e8f0fe;"' : ''; ?>>
                                    <td>
                                        <?php echo date('D, d M', strtotime($entry['date'])); ?>
                                        <?php if ($entry['date'] == date('Y-m-d')): ?>
                                        <span class="badge fd-badge-active" style="font-size: 10px;">Today</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right">K <?php echo number_format($deposit->current_principal, 2); ?></td>
                                    <td class="text-right"><?php echo number_format($daily_rate_decimal * 100, 4); ?>%</td>
                                    <td class="text-right" style="color: #1e8e3e;">+K <?php echo number_format($entry['interest'], 2); ?></td>
                                    <td class="text-right"><strong>K <?php echo number_format($entry['running'], 2); ?></strong></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (count($ledger_entries) > 14): ?>
                <div class="fd-card-footer">
                    <small style="color: #5f6368;">Showing last 14 of <?php echo count($ledger_entries); ?> days</small>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Quarterly Schedule -->
            <?php if ($deposit->payment_option == 'QUARTERLY' && !empty($schedules)): ?>
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">Quarterly Payment Schedule</span>
                </div>
                <div class="fd-card-body compact">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Quarter</th>
                                <th>Period</th>
                                <th>Days</th>
                                <th class="text-right">Expected</th>
                                <th class="text-right">Paid</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedules as $schedule): ?>
                            <?php
                            $sched_badge_map = array('PENDING' => 'fd-badge-pending', 'PAID' => 'fd-badge-paid', 'PARTIAL' => 'fd-badge-matured', 'SKIPPED' => 'fd-badge-closed');
                            $s_badge = isset($sched_badge_map[$schedule->status]) ? $sched_badge_map[$schedule->status] : 'fd-badge-closed';
                            ?>
                            <tr>
                                <td><strong>Q<?php echo $schedule->quarter; ?> <?php echo $schedule->year; ?></strong></td>
                                <td><?php echo date('d M', strtotime($schedule->deposit_start_in_quarter)); ?> - <?php echo date('d M', strtotime($schedule->quarter_end)); ?></td>
                                <td><?php echo $schedule->deposit_days; ?></td>
                                <td class="text-right">K <?php echo number_format($schedule->expected_interest, 2); ?></td>
                                <td class="text-right">K <?php echo number_format($schedule->paid_interest, 2); ?></td>
                                <td><span class="badge <?php echo $s_badge; ?>"><?php echo $schedule->status; ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Transaction History -->
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">Transaction History</span>
                </div>
                <div class="fd-card-body compact">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Ref</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th class="text-right">Amount</th>
                                <th class="text-right">Balance</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($transactions)): ?>
                            <tr><td colspan="6" class="text-center" style="padding: 24px; color: #5f6368;">No transactions yet</td></tr>
                            <?php else: ?>
                                <?php
                                $type_badge_map = array(
                                    'DEPOSIT' => 'fd-badge-active', 'INTEREST_PAYMENT' => 'fd-badge-matured', 'PRINCIPAL_WITHDRAWAL' => 'fd-badge-pending',
                                    'PENALTY' => 'fd-badge-overdue', 'CLOSURE' => 'fd-badge-closed', 'MERGE_OUT' => 'fd-badge-closed',
                                    'MERGE_IN' => 'fd-badge-matured', 'TOP_UP' => 'fd-badge-active'
                                );
                                ?>
                                <?php foreach ($transactions as $tx): ?>
                                <tr>
                                    <td><span style="font-family: monospace; font-size: 12px; color: #5f6368;"><?php echo $tx->transaction_ref; ?></span></td>
                                    <td><?php echo date('d M Y', strtotime($tx->created_at)); ?></td>
                                    <td>
                                        <?php $tb = isset($type_badge_map[$tx->transaction_type]) ? $type_badge_map[$tx->transaction_type] : 'fd-badge-closed'; ?>
                                        <span class="badge <?php echo $tb; ?>"><?php echo str_replace('_', ' ', $tx->transaction_type); ?></span>
                                    </td>
                                    <td class="text-right">
                                        <?php if (in_array($tx->transaction_type, array('DEPOSIT', 'MERGE_IN', 'TOP_UP'))): ?>
                                        <span style="color: #1e8e3e;">+K <?php echo number_format($tx->amount, 2); ?></span>
                                        <?php else: ?>
                                        <span style="color: #d93025;">-K <?php echo number_format($tx->amount, 2); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right" style="font-weight: 500;">K <?php echo number_format($tx->principal_after, 2); ?></td>
                                    <td>
                                        <small style="color: #5f6368;"><?php echo $tx->notes; ?></small>
                                        <?php if (!empty($tx->wht_amount) && $tx->wht_amount > 0): ?>
                                        <br><small style="color: #d93025;"><i class="fas fa-landmark"></i> WHT: K <?php echo number_format($tx->wht_amount, 2); ?></small>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notes -->
            <?php if ($deposit->notes): ?>
            <div class="fd-card">
                <div class="fd-card-header"><span class="fd-card-title">Notes</span></div>
                <div class="fd-card-body">
                    <p style="color: #202124; margin: 0;"><?php echo nl2br(htmlspecialchars($deposit->notes)); ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
