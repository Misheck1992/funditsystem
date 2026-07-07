<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="fd-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title"><?php echo $page_title; ?></h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                    <li class="breadcrumb-item active">Fixed Deposits</li>
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
            <div class="fd-stat-icon blue"><i class="fas fa-users"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Active Customers</div>
                <div class="fd-stat-value"><?php echo $customer_count; ?></div>
            </div>
        </div>
        <div class="fd-stat">
            <div class="fd-stat-icon green"><i class="fas fa-piggy-bank"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Active Deposits</div>
                <div class="fd-stat-value"><?php echo $stats['active_count']; ?></div>
            </div>
        </div>
        <div class="fd-stat">
            <div class="fd-stat-icon purple"><i class="fas fa-money-bill-wave"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Total Principal</div>
                <div class="fd-stat-value sm">K <?php echo number_format($stats['total_principal'], 0); ?></div>
            </div>
        </div>
        <div class="fd-stat">
            <div class="fd-stat-icon yellow"><i class="fas fa-clock"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Maturing Soon</div>
                <div class="fd-stat-value"><?php echo $stats['maturing_soon']; ?></div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="fd-card" style="margin-bottom: 24px;">
        <div class="fd-card-body" style="padding: 16px 24px;">
            <div class="d-flex flex-wrap" style="gap: 8px;">
                <a href="<?php echo site_url('Fixed_deposits/customer_create'); ?>" class="btn btn-primary">
                    <i class="fas fa-user-plus mr-1"></i> New Customer
                </a>
                <a href="<?php echo site_url('Fixed_deposits/deposit_create'); ?>" class="btn btn-success">
                    <i class="fas fa-plus-circle mr-1"></i> New Deposit
                </a>
                <a href="<?php echo site_url('Fixed_deposits/customers'); ?>" class="btn btn-default">
                    <i class="fas fa-users mr-1"></i> Customers
                </a>
                <a href="<?php echo site_url('Fixed_deposits/deposits'); ?>" class="btn btn-default">
                    <i class="fas fa-list mr-1"></i> All Deposits
                </a>
                <a href="<?php echo site_url('Fixed_deposits/report'); ?>" class="btn btn-default">
                    <i class="fas fa-chart-bar mr-1"></i> Reports
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Maturing Deposits -->
        <div class="col-md-6" id="maturing-section">
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">Maturing Soon (30 days)</span>
                    <span class="badge fd-badge-pending"><?php echo count($maturing_deposits); ?></span>
                </div>
                <div class="fd-card-body compact">
                    <div style="max-height: 300px; overflow-y: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Deposit #</th>
                                    <th>Customer</th>
                                    <th style="text-align: right;">Principal</th>
                                    <th style="text-align: right;">Maturity</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($maturing_deposits)): ?>
                                    <tr><td colspan="4" style="text-align: center; padding: 32px; color: #5f6368;">
                                        <i class="fas fa-check-circle" style="color: #1e8e3e; display: block; font-size: 24px; margin-bottom: 8px;"></i>
                                        No deposits maturing soon
                                    </td></tr>
                                <?php else: ?>
                                    <?php foreach ($maturing_deposits as $dep): ?>
                                        <?php $days = floor((strtotime($dep->maturity_date) - time()) / 86400); ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo site_url('Fixed_deposits/deposit_view/' . $dep->id); ?>" style="font-family: monospace; font-size: 13px;">
                                                    <?php echo $dep->deposit_number; ?>
                                                </a>
                                            </td>
                                            <td><?php echo $dep->first_name . ' ' . $dep->last_name; ?></td>
                                            <td style="text-align: right;">K <?php echo number_format($dep->current_principal, 2); ?></td>
                                            <td style="text-align: right;">
                                                <?php echo date('d M Y', strtotime($dep->maturity_date)); ?>
                                                <br><small style="color: #e37400;"><?php echo $days; ?> days</small>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overdue Payments -->
        <div class="col-md-6">
            <div class="fd-card">
                <div class="fd-card-header">
                    <span class="fd-card-title">Overdue Interest Payments</span>
                    <span class="badge fd-badge-overdue"><?php echo count($overdue_payments); ?></span>
                </div>
                <div class="fd-card-body compact">
                    <div style="max-height: 300px; overflow-y: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Deposit #</th>
                                    <th>Customer</th>
                                    <th style="text-align: center;">Quarter</th>
                                    <th style="text-align: right;">Expected</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($overdue_payments)): ?>
                                    <tr><td colspan="4" style="text-align: center; padding: 32px; color: #5f6368;">
                                        <i class="fas fa-check-circle" style="color: #1e8e3e; display: block; font-size: 24px; margin-bottom: 8px;"></i>
                                        No overdue payments
                                    </td></tr>
                                <?php else: ?>
                                    <?php foreach ($overdue_payments as $payment): ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo site_url('Fixed_deposits/pay_interest/' . $payment->deposit_id); ?>" style="font-family: monospace; font-size: 13px;">
                                                    <?php echo $payment->deposit_number; ?>
                                                </a>
                                            </td>
                                            <td><?php echo $payment->first_name . ' ' . $payment->last_name; ?></td>
                                            <td style="text-align: center;"><span class="badge fd-badge-pending">Q<?php echo $payment->quarter . ' ' . $payment->year; ?></span></td>
                                            <td style="text-align: right; color: #d93025;"><strong>K <?php echo number_format($payment->expected_interest, 2); ?></strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="fd-card">
        <div class="fd-card-header">
            <span class="fd-card-title">Recent Transactions</span>
        </div>
        <div class="fd-card-body compact">
            <table class="table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Deposit</th>
                        <th>Customer</th>
                        <th style="text-align: center;">Type</th>
                        <th style="text-align: right;">Amount</th>
                        <th style="text-align: right;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent_transactions)): ?>
                        <tr><td colspan="6" style="text-align: center; padding: 32px; color: #5f6368;">
                            No transactions yet
                        </td></tr>
                    <?php else: ?>
                        <?php foreach ($recent_transactions as $tx): ?>
                            <tr>
                                <td style="font-family: monospace; font-size: 12px;"><?php echo $tx->transaction_ref; ?></td>
                                <td>
                                    <a href="<?php echo site_url('Fixed_deposits/deposit_view/' . $tx->deposit_id); ?>">
                                        <?php echo $tx->deposit_number; ?>
                                    </a>
                                </td>
                                <td><?php echo $tx->first_name . ' ' . $tx->last_name; ?></td>
                                <td style="text-align: center;">
                                    <?php
                                    $type_badges = array(
                                        'DEPOSIT' => 'fd-badge-active',
                                        'INTEREST_PAYMENT' => 'fd-badge-matured',
                                        'PRINCIPAL_WITHDRAWAL' => 'fd-badge-pending',
                                        'PENALTY' => 'fd-badge-overdue',
                                        'CLOSURE' => 'fd-badge-closed',
                                        'MERGE_OUT' => 'fd-badge-closed',
                                        'MERGE_IN' => 'fd-badge-active',
                                        'TOP_UP' => 'fd-badge-active'
                                    );
                                    $badge_class = isset($type_badges[$tx->transaction_type]) ? $type_badges[$tx->transaction_type] : 'fd-badge-closed';
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo str_replace('_', ' ', $tx->transaction_type); ?></span>
                                </td>
                                <td style="text-align: right;"><strong>K <?php echo number_format($tx->amount, 2); ?></strong></td>
                                <td style="text-align: right;">
                                    <?php echo date('d M Y', strtotime($tx->created_at)); ?>
                                    <br><small style="color: #5f6368;"><?php echo date('H:i', strtotime($tx->created_at)); ?></small>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
