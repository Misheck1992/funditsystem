<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $settings = get_by_id('settings', 'settings_id', '1'); ?>

<div class="fd-page">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h1 class="page-title"><?php echo $page_title; ?></h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo site_url('Fixed_deposits'); ?>">Fixed Deposits</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo site_url('Fixed_deposits/deposit_view/' . $deposit->id); ?>"><?php echo $deposit->deposit_number; ?></a></li>
                    <li class="breadcrumb-item active">Statement</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="fd-card no-print">
        <div class="fd-card-body" style="padding: 16px 24px;">
            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 12px;">
                <form method="get" action="<?php echo current_url(); ?>" class="d-flex align-items-center flex-wrap" style="gap: 12px;">
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        <label class="mb-0" style="white-space: nowrap;">From</label>
                        <input type="date" name="from_date" class="form-control" value="<?php echo $from_date; ?>" style="width: auto;">
                    </div>
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        <label class="mb-0" style="white-space: nowrap;">To</label>
                        <input type="date" name="to_date" class="form-control" value="<?php echo $to_date; ?>" style="width: auto;">
                    </div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
                <button onclick="window.print()" class="btn btn-default">
                    <i class="fas fa-print mr-1"></i> Print Statement
                </button>
            </div>
        </div>
    </div>

    <!-- Printable Statement -->
    <div class="fd-card" id="printArea">
        <div class="fd-card-body">
            <!-- Letterhead -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h3 style="font-weight: 500; color: #202124; margin-bottom: 4px;"><?php echo $settings->company_name; ?></h3>
                    <p style="color: #5f6368; font-size: 13px; line-height: 1.6; margin: 0;">
                        <?php echo strip_tags($settings->address); ?><br>
                        Phone: <?php echo $settings->phone_number; ?>
                    </p>
                </div>
                <div class="col-md-6 text-right">
                    <h4 style="font-weight: 500; color: #202124; letter-spacing: 1px; margin-bottom: 8px;">FIXED DEPOSIT STATEMENT</h4>
                    <p style="color: #5f6368; font-size: 13px; margin: 0;">
                        <strong>Statement Date:</strong> <?php echo date('d M Y'); ?>
                        <?php if ($from_date || $to_date): ?><br>
                            <strong>Period:</strong>
                            <?php echo $from_date ? date('d M Y', strtotime($from_date)) : 'Start'; ?> -
                            <?php echo $to_date ? date('d M Y', strtotime($to_date)) : 'Present'; ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>

            <hr class="fd-divider">

            <!-- Account Details -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 style="font-weight: 500; color: #5f6368; text-transform: uppercase; font-size: 11px; letter-spacing: 0.8px; margin-bottom: 12px;">Customer Details</h6>
                    <table class="table table-sm table-borderless fd-kv-table mb-0">
                        <tr><td>Customer Name:</td><td><strong><?php echo $deposit->first_name . ' ' . $deposit->last_name; ?></strong></td></tr>
                        <tr><td>Customer Number:</td><td><?php echo $deposit->customer_number; ?></td></tr>
                        <tr><td>Phone:</td><td><?php echo $deposit->phone_number; ?></td></tr>
                        <tr><td>Email:</td><td><?php echo $deposit->email ?: 'N/A'; ?></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 style="font-weight: 500; color: #5f6368; text-transform: uppercase; font-size: 11px; letter-spacing: 0.8px; margin-bottom: 12px;">Deposit Details</h6>
                    <table class="table table-sm table-borderless fd-kv-table mb-0">
                        <tr><td>Deposit Number:</td><td><strong><?php echo $deposit->deposit_number; ?></strong></td></tr>
                        <tr><td>Original Principal:</td><td>K <?php echo number_format($deposit->principal_amount, 2); ?></td></tr>
                        <tr><td>Interest Rate:</td><td><?php echo $deposit->interest_rate; ?>%</td></tr>
                        <tr><td>Term:</td><td><?php echo $deposit->duration_months; ?> months</td></tr>
                        <tr><td>Start Date:</td><td><?php echo date('d M Y', strtotime($deposit->start_date)); ?></td></tr>
                        <tr><td>Maturity Date:</td><td><?php echo date('d M Y', strtotime($deposit->maturity_date)); ?></td></tr>
                        <tr><td>Status:</td><td><strong><?php echo $deposit->status; ?></strong></td></tr>
                    </table>
                </div>
            </div>

            <hr class="fd-divider">

            <!-- Transaction History -->
            <h6 style="font-weight: 500; color: #5f6368; text-transform: uppercase; font-size: 11px; letter-spacing: 0.8px; margin-bottom: 12px;">Transaction History</h6>
            <table class="table table-sm" style="font-size: 13px;">
                <thead>
                    <tr style="background: #f8f9fa;">
                        <th style="padding: 10px 12px; font-size: 11px; text-transform: uppercase; color: #5f6368; letter-spacing: 0.5px; border-top: none; border-bottom: 1px solid #dadce0;">Date</th>
                        <th style="padding: 10px 12px; font-size: 11px; text-transform: uppercase; color: #5f6368; letter-spacing: 0.5px; border-top: none; border-bottom: 1px solid #dadce0;">Reference</th>
                        <th style="padding: 10px 12px; font-size: 11px; text-transform: uppercase; color: #5f6368; letter-spacing: 0.5px; border-top: none; border-bottom: 1px solid #dadce0;">Description</th>
                        <th style="padding: 10px 12px; font-size: 11px; text-transform: uppercase; color: #5f6368; letter-spacing: 0.5px; border-top: none; border-bottom: 1px solid #dadce0; text-align: right;">Debit</th>
                        <th style="padding: 10px 12px; font-size: 11px; text-transform: uppercase; color: #5f6368; letter-spacing: 0.5px; border-top: none; border-bottom: 1px solid #dadce0; text-align: right;">Credit</th>
                        <th style="padding: 10px 12px; font-size: 11px; text-transform: uppercase; color: #5f6368; letter-spacing: 0.5px; border-top: none; border-bottom: 1px solid #dadce0; text-align: right;">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr><td colspan="6" class="text-center" style="padding: 24px; color: #5f6368;">No transactions found</td></tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $tx): ?>
                            <tr>
                                <td style="padding: 10px 12px; border-bottom: 1px solid #e8eaed;"><?php echo date('d M Y', strtotime($tx->created_at)); ?></td>
                                <td style="padding: 10px 12px; border-bottom: 1px solid #e8eaed; font-family: monospace; font-size: 12px;"><?php echo $tx->transaction_ref; ?></td>
                                <td style="padding: 10px 12px; border-bottom: 1px solid #e8eaed;">
                                    <?php echo str_replace('_', ' ', $tx->transaction_type); ?>
                                    <?php if ($tx->notes): ?><br><small style="color: #5f6368;"><?php echo $tx->notes; ?></small><?php endif; ?>
                                </td>
                                <td style="padding: 10px 12px; border-bottom: 1px solid #e8eaed; text-align: right;">
                                    <?php if (in_array($tx->transaction_type, array('PRINCIPAL_WITHDRAWAL', 'INTEREST_PAYMENT', 'CLOSURE', 'PENALTY', 'MERGE_OUT'))): ?>
                                        K <?php echo number_format($tx->amount, 2); ?>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 10px 12px; border-bottom: 1px solid #e8eaed; text-align: right;">
                                    <?php if (in_array($tx->transaction_type, array('DEPOSIT', 'TOP_UP', 'MERGE_IN'))): ?>
                                        K <?php echo number_format($tx->amount, 2); ?>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 10px 12px; border-bottom: 1px solid #e8eaed; text-align: right; font-weight: 500;">K <?php echo number_format($tx->principal_after, 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <hr class="fd-divider">

            <!-- Summary -->
            <?php
            $wht_rate = 15;
            $currency = $deposit->currency ?? 'ZMW';
            $currency_symbol = ($currency == 'USD') ? '$' : 'K';
            $annual_interest = $deposit->principal_amount * ($deposit->interest_rate / 100);
            $total_expected_interest = ($annual_interest / 12) * $deposit->duration_months;
            $wht_on_expected = $total_expected_interest * ($wht_rate / 100);
            $net_expected_interest = $total_expected_interest - $wht_on_expected;
            $accrued_interest = $deposit->accrued_interest;
            $wht_on_accrued = $accrued_interest * ($wht_rate / 100);
            $net_accrued_interest = $accrued_interest - $wht_on_accrued;
            $paid_interest = $deposit->paid_interest;
            ?>
            <div class="row">
                <div class="col-md-6">
                    <h6 style="font-weight: 500; color: #5f6368; text-transform: uppercase; font-size: 11px; letter-spacing: 0.8px; margin-bottom: 12px;">Expected Returns at Maturity</h6>
                    <table class="fd-summary-table">
                        <tr><td>Principal Amount</td><td><?php echo $currency_symbol; ?> <?php echo number_format($deposit->principal_amount, 2); ?></td></tr>
                        <tr><td>Interest Rate</td><td><?php echo $deposit->interest_rate; ?>%</td></tr>
                        <tr><td>Term</td><td><?php echo $deposit->duration_months; ?> months</td></tr>
                        <tr><td><strong>Gross Interest (Before Tax)</strong></td><td><strong><?php echo $currency_symbol; ?> <?php echo number_format($total_expected_interest, 2); ?></strong></td></tr>
                        <tr class="deduction"><td>Less: WHT @ <?php echo $wht_rate; ?>%</td><td>(<?php echo $currency_symbol; ?> <?php echo number_format($wht_on_expected, 2); ?>)</td></tr>
                        <tr class="total"><td>Net Interest (After Tax)</td><td><?php echo $currency_symbol; ?> <?php echo number_format($net_expected_interest, 2); ?></td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6 style="font-weight: 500; color: #5f6368; text-transform: uppercase; font-size: 11px; letter-spacing: 0.8px; margin-bottom: 12px;">Current Position</h6>
                    <table class="fd-summary-table">
                        <tr><td>Current Principal</td><td><strong><?php echo $currency_symbol; ?> <?php echo number_format($deposit->current_principal, 2); ?></strong></td></tr>
                        <tr><td>Accrued Interest (Gross)</td><td><?php echo $currency_symbol; ?> <?php echo number_format($accrued_interest, 2); ?></td></tr>
                        <tr class="deduction"><td>Less: WHT @ <?php echo $wht_rate; ?>%</td><td>(<?php echo $currency_symbol; ?> <?php echo number_format($wht_on_accrued, 2); ?>)</td></tr>
                        <tr><td>Net Accrued Interest</td><td><?php echo $currency_symbol; ?> <?php echo number_format($net_accrued_interest, 2); ?></td></tr>
                        <tr><td>Total Interest Paid to Date</td><td><?php echo $currency_symbol; ?> <?php echo number_format($paid_interest, 2); ?></td></tr>
                        <tr class="total"><td>Current Value (Net)</td><td><?php echo $currency_symbol; ?> <?php echo number_format($deposit->current_principal + $net_accrued_interest, 2); ?></td></tr>
                    </table>
                    <p style="color: #5f6368; font-size: 12px; margin-top: 12px;">
                        <i class="fas fa-info-circle"></i> WHT (Withholding Tax) at <?php echo $wht_rate; ?>% is deducted from interest earnings as per tax regulations.
                    </p>
                </div>
            </div>

            <hr class="fd-divider">

            <div class="text-center">
                <p style="color: #5f6368; font-size: 12px; margin: 0;">
                    This statement was generated on <?php echo date('d M Y H:i:s'); ?>.<br>
                    For any queries, please contact us at <?php echo $settings->phone_number; ?>
                </p>
            </div>
        </div>
    </div>
</div>
