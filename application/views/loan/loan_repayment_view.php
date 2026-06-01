<?php
$fover = false;
$next_payment_details = $this->Payement_schedules_model->get_next($next_payment_id,$loan_id);
$currency = get_by_id('currencies','currency_id',$currency);
?>

<style>
/* Main Layout */
.repayment-view-container {
    max-width: 1600px;
    margin: 0 auto;
}

/* Cards */
.view-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    overflow: hidden;
    margin-bottom: 1.5rem;
}
.view-card-header {
    background: #1e3a5f;
    color: #fff;
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.view-card-header h5 {
    margin: 0;
    font-weight: 600;
    font-size: 1rem;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.view-card-header .header-badge {
    background: rgba(255,255,255,0.2);
    padding: 0.3rem 0.75rem;
    border-radius: 50px;
    font-size: 0.8rem;
}
.view-card-body {
    padding: 1.5rem;
}

/* Info Items */
.info-item {
    padding: 0.75rem;
    background: #f8fafc;
    border-radius: 8px;
    border-left: 3px solid #1e3a5f;
    margin-bottom: 0.75rem;
}
.info-item .label {
    font-size: 0.75rem;
    color: #6b7280;
    text-transform: uppercase;
    font-weight: 600;
    margin-bottom: 0.25rem;
}
.info-item .value {
    font-size: 1rem;
    color: #1e3a5f;
    font-weight: 600;
}
.info-item .value a {
    color: #3b82f6;
    text-decoration: none;
}
.info-item .value a:hover {
    text-decoration: underline;
}

/* Status Badges */
.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}
.status-active { background: #dcfce7; color: #166534; }
.status-pending { background: #fef3c7; color: #92400e; }
.status-paid { background: #dcfce7; color: #166534; }
.status-not-paid { background: #fee2e2; color: #991b1b; }
.status-overdue { background: #fecaca; color: #7f1d1d; }
.status-due-today { background: #fed7aa; color: #9a3412; }
.status-partial { background: #e0e7ff; color: #3730a3; }

/* Summary Cards */
.summary-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.summary-card {
    background: #fff;
    border-radius: 10px;
    padding: 1.25rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    border-left: 4px solid #1e3a5f;
}
.summary-card.success { border-left-color: #059669; }
.summary-card.warning { border-left-color: #f59e0b; }
.summary-card.danger { border-left-color: #dc2626; }
.summary-card.info { border-left-color: #3b82f6; }
.summary-card .sc-label {
    font-size: 0.75rem;
    color: #6b7280;
    text-transform: uppercase;
    font-weight: 600;
}
.summary-card .sc-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e3a5f;
    margin-top: 0.25rem;
}
.summary-card.success .sc-value { color: #059669; }
.summary-card.warning .sc-value { color: #f59e0b; }
.summary-card.danger .sc-value { color: #dc2626; }

/* Table Styles */
.table-container {
    overflow-x: auto;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
}
.data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.85rem;
}
.data-table thead {
    background: #f1f5f9;
}
.data-table th {
    padding: 0.75rem 1rem;
    text-align: left;
    font-weight: 600;
    color: #1e3a5f;
    border-bottom: 2px solid #e5e7eb;
    white-space: nowrap;
}
.data-table td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    color: #374151;
}
.data-table tbody tr:hover {
    background: #f8fafc;
}
.data-table tbody tr:last-child td {
    border-bottom: none;
}
.data-table tbody tr.row-overdue {
    background: #fef2f2;
}
.data-table tbody tr.row-due-today {
    background: #fffbeb;
}
.data-table tbody tr.row-paid {
    background: #f0fdf4;
}

/* Action Buttons */
.btn-action {
    border: none;
    padding: 0.4rem 0.75rem;
    border-radius: 5px;
    font-size: 0.8rem;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    cursor: pointer;
    transition: all 0.2s;
    margin-right: 0.25rem;
    margin-bottom: 0.25rem;
}
.btn-primary { background: #3b82f6; color: #fff; }
.btn-primary:hover { background: #2563eb; color: #fff; text-decoration: none; }
.btn-success { background: #059669; color: #fff; }
.btn-success:hover { background: #047857; color: #fff; text-decoration: none; }
.btn-warning { background: #f59e0b; color: #fff; }
.btn-warning:hover { background: #d97706; color: #fff; text-decoration: none; }
.btn-danger { background: #dc2626; color: #fff; }
.btn-danger:hover { background: #b91c1c; color: #fff; text-decoration: none; }
.btn-info { background: #0891b2; color: #fff; }
.btn-info:hover { background: #0e7490; color: #fff; text-decoration: none; }
.btn-secondary { background: #6b7280; color: #fff; }
.btn-secondary:hover { background: #4b5563; color: #fff; text-decoration: none; }

/* Actions Panel */
.actions-panel {
    background: #f8fafc;
    border-radius: 10px;
    padding: 1.25rem;
    border: 1px solid #e5e7eb;
}
.actions-panel h6 {
    font-size: 0.9rem;
    font-weight: 600;
    color: #1e3a5f;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e5e7eb;
}
.actions-panel .btn-action {
    width: 100%;
    justify-content: center;
    padding: 0.6rem 1rem;
    margin-bottom: 0.5rem;
}

/* Next Payment Card */
.next-payment-card {
    background: linear-gradient(135deg, #1e3a5f 0%, #2d4a6f 100%);
    border-radius: 10px;
    padding: 1.25rem;
    color: #fff;
    margin-bottom: 1rem;
}
.next-payment-card h6 {
    font-size: 0.85rem;
    font-weight: 600;
    opacity: 0.9;
    margin-bottom: 1rem;
    text-transform: uppercase;
}
.next-payment-card .payment-amount {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}
.next-payment-card .payment-detail {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-top: 1px solid rgba(255,255,255,0.2);
    font-size: 0.85rem;
}
.next-payment-card .payment-status {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-top: 0.5rem;
}
.next-payment-card .payment-status.overdue { background: #dc2626; }
.next-payment-card .payment-status.due-today { background: #f59e0b; }
.next-payment-card .payment-status.upcoming { background: #059669; }

/* Info Grid */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

/* Modal Styles */
.modern-modal .modal-content {
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
}
.modern-modal .modal-header {
    background: #1e3a5f;
    color: #fff;
    border-radius: 12px 12px 0 0;
    padding: 1.25rem 1.5rem;
    border-bottom: none;
}
.modern-modal .modal-header .modal-title {
    font-weight: 600;
    font-size: 1.1rem;
}
.modern-modal .modal-header .close {
    color: #fff;
    opacity: 0.8;
    text-shadow: none;
}
.modern-modal .modal-header .close:hover {
    opacity: 1;
}
.modern-modal .modal-body {
    padding: 1.5rem;
}
.modern-modal .form-group {
    margin-bottom: 1rem;
}
.modern-modal .form-group label {
    font-weight: 600;
    color: #374151;
    font-size: 0.85rem;
    margin-bottom: 0.4rem;
    display: block;
}
.modern-modal .form-control {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 0.6rem 0.75rem;
    font-size: 0.9rem;
}
.modern-modal .form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
}

/* Responsive */
@media (max-width: 992px) {
    .summary-row {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 768px) {
    .view-card-header {
        flex-direction: column;
        align-items: flex-start;
    }
    .info-grid {
        grid-template-columns: 1fr;
    }
    .summary-row {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Loan Repayment</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="<?php echo base_url('loan/track')?>">Loans</a>
                <span class="breadcrumb-item active"><?php echo $loan_number; ?></span>
            </nav>
        </div>
    </div>

    <div class="repayment-view-container">
        <?php
        // Calculate totals
        $total_paid = 0;
        foreach ($payments as $pp) {
            if($pp->status == "PAID") {
                $total_paid += $pp->amount;
            }
            if($pp->partial_paid == "YES") {
                $total_paid += $pp->paid_amount;
            }
        }
        $remaining_balance = $loan_amount_total - $total_paid;
        ?>

        <!-- Summary Cards -->
        <div class="summary-row">
            <div class="summary-card">
                <div class="sc-label">Principal Amount</div>
                <div class="sc-value"><?php echo $currency->currency_code; ?> <?php echo number_format($loan_principal, 2); ?></div>
            </div>
            <div class="summary-card info">
                <div class="sc-label">Total Interest</div>
                <div class="sc-value" style="color: #3b82f6;"><?php echo $currency->currency_code; ?> <?php echo number_format($loan_interest_amount, 2); ?></div>
            </div>
            <div class="summary-card warning">
                <div class="sc-label">Total Loan Amount</div>
                <div class="sc-value"><?php echo $currency->currency_code; ?> <?php echo number_format($loan_amount_total, 2); ?></div>
            </div>
            <div class="summary-card success">
                <div class="sc-label">Payments Made</div>
                <div class="sc-value"><?php echo $currency->currency_code; ?> <?php echo number_format($total_paid, 2); ?></div>
            </div>
            <div class="summary-card danger">
                <div class="sc-label">Remaining Balance</div>
                <div class="sc-value"><?php echo $currency->currency_code; ?> <?php echo number_format($remaining_balance, 2); ?></div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - Loan Info -->
            <div class="col-lg-3">
                <!-- Loan Info Card -->
                <div class="view-card">
                    <div class="view-card-header">
                        <h5><i class="fa fa-info-circle"></i> Loan Information</h5>
                    </div>
                    <div class="view-card-body">
                        <div class="info-item">
                            <div class="label">Loan Number</div>
                            <div class="value"><?php echo $loan_number; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="label">Loan Product</div>
                            <div class="value"><?php echo $loan_product; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="label">Customer</div>
                            <div class="value"><a href="<?php echo base_url($preview_url).$customer_id?>"><?php echo $loan_customer; ?></a></div>
                        </div>
                        <div class="info-item">
                            <div class="label">Status</div>
                            <div class="value">
                                <?php
                                $status_class = 'status-pending';
                                if(strtolower($loan_status) == 'active') $status_class = 'status-active';
                                ?>
                                <span class="status-badge <?php echo $status_class; ?>"><?php echo $loan_status; ?></span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="label">Processing Fee</div>
                            <div class="value"><?php echo $processing_fee; ?>%</div>
                        </div>
                    </div>
                </div>

                <!-- Next Payment Card -->
                <?php if(!empty($next_payment_details)): ?>
                <div class="next-payment-card">
                    <h6><i class="fa fa-calendar"></i> Next Payment Due</h6>
                    <div class="payment-amount"><?php echo $currency->currency_code; ?> <?php echo number_format($next_payment_details->amount, 2); ?></div>
                    <div class="payment-detail">
                        <span>Payment #</span>
                        <span><?php echo $next_payment_id; ?></span>
                    </div>
                    <div class="payment-detail">
                        <span>Principal</span>
                        <span><?php echo $currency->currency_code; ?> <?php echo number_format($next_payment_details->principal, 2); ?></span>
                    </div>
                    <div class="payment-detail">
                        <span>Interest</span>
                        <span><?php echo $currency->currency_code; ?> <?php echo number_format($next_payment_details->interest, 2); ?></span>
                    </div>
                    <div class="payment-detail">
                        <span>Due Date</span>
                        <span><?php echo date('d M Y', strtotime($next_payment_details->payment_schedule)); ?></span>
                    </div>
                    <?php
                    $payment_status_class = 'upcoming';
                    $payment_status_text = 'UPCOMING';
                    if($loan_status == 'ACTIVE' && $next_payment_details->payment_schedule < date('Y-m-d') && $next_payment_details->status == 'NOT PAID') {
                        $payment_status_class = 'overdue';
                        $payment_status_text = 'OVERDUE';
                        $fover = true;
                    } elseif($loan_status == 'ACTIVE' && $next_payment_details->payment_schedule == date('Y-m-d') && $next_payment_details->status == 'NOT PAID') {
                        $payment_status_class = 'due-today';
                        $payment_status_text = 'DUE TODAY';
                    } elseif($next_payment_details->status == 'PAID') {
                        $payment_status_class = 'upcoming';
                        $payment_status_text = 'PAID';
                    }
                    ?>
                    <span class="payment-status <?php echo $payment_status_class; ?>"><?php echo $payment_status_text; ?></span>
                </div>

                <?php
                // Check for rescheduled payments
                $resc = get_all_by_id('rescheduled_payments', 'loan_id', $loan_id);
                if(!empty($resc)):
                    $rd = get_by_id('rescheduled_payments', 'payment_number', $next_payment_id_rescheduled);
                ?>
                <div class="view-card">
                    <div class="view-card-header" style="background: #f59e0b;">
                        <h5><i class="fa fa-calendar-alt"></i> Rescheduled Payment</h5>
                    </div>
                    <div class="view-card-body">
                        <div class="info-item">
                            <div class="label">Rescheduled Payment #</div>
                            <div class="value"><?php echo $next_payment_id_rescheduled; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="label">Payment Amount</div>
                            <div class="value"><?php echo $currency->currency_code; ?> <?php echo number_format($rd->payment_amount, 2); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="label">Due Date</div>
                            <div class="value"><?php echo date('d M Y', strtotime($rd->payment_date)); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="label">Status</div>
                            <div class="value">
                                <?php
                                $rs_status = $rd->pay_status;
                                $rs_class = 'status-not-paid';
                                if($loan_status == 'ACTIVE' && $rd->payment_date < date('Y-m-d') && $rd->pay_status == 'UNPAID') {
                                    $rs_status = 'OVERDUE';
                                    $rs_class = 'status-overdue';
                                } elseif($rd->pay_status == 'PAID') {
                                    $rs_class = 'status-paid';
                                }
                                ?>
                                <span class="status-badge <?php echo $rs_class; ?>"><?php echo $rs_status; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php else: ?>
                <div class="view-card">
                    <div class="view-card-body" style="text-align: center; padding: 2rem;">
                        <i class="fa fa-check-circle fa-3x" style="color: #059669; margin-bottom: 1rem;"></i>
                        <h6 style="color: #059669;">Fully Paid</h6>
                        <p style="color: #6b7280; margin: 0;">No more payments to make</p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Actions Panel -->
                <div class="actions-panel">
                    <h6><i class="fa fa-cog"></i> Actions</h6>

                    <a href="<?php echo base_url('loan/report/').$loan_id ?>" class="btn-action btn-danger" style="width: 100%; justify-content: center;">
                        <i class="fa fa-file-pdf"></i> Download Report
                    </a>

                    <?php
                    $repp = get_all_by_id('rescheduled_payments', 'loan_id', $loan_id);
                    // Only show payment button if loan is not closed or written off
                    if($loan_status != 'CLOSED' && $loan_status != 'WRITTEN_OFF'):
                        // For Bullet loans, always show payment button regardless of arrears status
                        // For other loan types, only show if not overdue ($fover = false)
                        if($calculation_type == "Bullet Payment") {
                            // Bullet loans - show payment button always (arrears interest is compounded on outstanding balance)
                            if (!empty($next_payment_details)) {
                    ?>
                                <?php if($fover): ?>
                                <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 0.75rem; margin-bottom: 0.75rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; color: #991b1b; font-size: 0.85rem;">
                                        <i class="fa fa-exclamation-triangle"></i>
                                        <span><strong>Loan in Arrears</strong> - Interest accruing on outstanding balance</span>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <button onclick="calculate_payoff(<?php echo $loan_id; ?>)" class="btn-action btn-success" style="width: 100%; justify-content: center;">
                                    <i class="fa fa-money-bill-wave"></i> Make Payment
                                </button>
                    <?php
                            }
                        } else {
                            // Non-bullet loans - same payoff modal as bullet
                            if (!empty($next_payment_details)) {
                    ?>
                                <button onclick="calculate_payoff(<?php echo $loan_id; ?>)" class="btn-action btn-success" style="width: 100%; justify-content: center;">
                                    <i class="fa fa-money-bill-wave"></i> Make Payment
                                </button>
                    <?php
                            }
                        }

                        // Forced Close — available whenever loan is active (regardless of next payment state)
                        ?>
                        <button onclick="open_force_close_modal()" class="btn-action btn-danger" style="width: 100%; justify-content: center;">
                            <i class="fa fa-lock"></i> Forced Close Loan
                        </button>
                    <?php
                    endif;
                    ?>
                </div>
            </div>

            <!-- Middle Column - Payment Schedule & History -->
            <div class="col-lg-9">
                <?php if($calculation_type == 'Bullet Payment'): ?>
                    <?php if (!empty($acrued) && $acrued['status'] == 'success' && isset($acrued['debug'])): ?>
                        <div class="view-card mb-3" id="payoffDetails" style="display: none;">
                            <div class="view-card-header" style="background: #0891b2;">
                                <h5><i class="fa fa-calculator"></i> Payoff Calculation Details</h5>
                            </div>
                            <div class="view-card-body">
                                <div class="info-grid">
                                    <div class="info-item">
                                        <div class="label">Loan Start Date</div>
                                        <div class="value"><?= $acrued['debug']['loan_date'] ?></div>
                                    </div>
                                    <div class="info-item">
                                        <div class="label">Current Date</div>
                                        <div class="value"><?= $acrued['debug']['payoff_date'] ?></div>
                                    </div>
                                    <div class="info-item">
                                        <div class="label">Days Elapsed</div>
                                        <div class="value"><?= $acrued['debug']['elapsed_days'] ?> Days</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="label">Monthly Interest Rate</div>
                                        <div class="value"><?= number_format($acrued['debug']['monthly_interest_rate'] * 100, 2) ?>%</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="label">Daily Interest</div>
                                        <div class="value"><?= $currency->currency_code ?> <?= number_format($acrued['debug']['daily_interest'], 2) ?></div>
                                    </div>
                                    <div class="info-item">
                                        <div class="label">Accrued Interest</div>
                                        <div class="value"><?= $currency->currency_code ?> <?= number_format($acrued['accrued_interest'], 2) ?></div>
                                    </div>
                                    <div class="info-item">
                                        <div class="label">Principal Balance</div>
                                        <div class="value"><?= $currency->currency_code ?> <?= number_format($acrued['current_balance'], 2) ?></div>
                                    </div>
                                    <div class="info-item" style="border-left-color: #059669;">
                                        <div class="label">Total Payoff Amount</div>
                                        <div class="value" style="color: #059669; font-size: 1.2rem;"><?= $currency->currency_code ?> <?= number_format($acrued['total_payoff'], 2) ?></div>
                                    </div>
                                </div>
                                <div style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1rem; margin-top: 1rem;">
                                    <pre style="white-space: pre-wrap; margin: 0; font-size: 0.85rem;"><?= $acrued['debug']['calculation_explanation'] ?></pre>
                                </div>
                            </div>
                        </div>
                        <button onclick="togglePayoffDetails()" class="btn-action btn-info mb-3">
                            <i class="fa fa-calculator"></i> Show/Hide Payoff Details
                        </button>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Payment Schedule -->
                <div class="view-card">
                    <div class="view-card-header">
                        <h5><i class="fa fa-calendar-alt"></i> Contract Summary</h5>
                        <span class="header-badge"><?php echo count($payments); ?> Payments</span>
                    </div>
                    <div class="view-card-body">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Due Date</th>
                                        <th>Principal</th>
                                        <th>Interest</th>
                                        <th>Amount Due</th>
                                        <th>Amount Paid</th>
                                        <th>Balance</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $p):
                                        $row_class = '';
                                        $status_text = $p->status;
                                        $status_badge_class = 'status-not-paid';
                                        $show_pay_btn = false;

                                        if($loan_status == 'ACTIVE' && $p->payment_schedule < date('Y-m-d') && $p->status == 'NOT PAID') {
                                            $row_class = 'row-overdue';
                                            $status_text = 'OVERDUE';
                                            $status_badge_class = 'status-overdue';
                                            $show_pay_btn = true;
                                        } elseif($p->status == 'PAID') {
                                            $row_class = 'row-paid';
                                            $status_badge_class = 'status-paid';
                                        } elseif($loan_status == 'ACTIVE' && $p->payment_schedule == date('Y-m-d') && $p->status == 'NOT PAID') {
                                            $row_class = 'row-due-today';
                                            $status_text = 'DUE TODAY';
                                            $status_badge_class = 'status-due-today';
                                            $show_pay_btn = true;
                                        }

                                        if($p->partial_paid == "YES") {
                                            $status_badge_class = 'status-partial';
                                            $status_text = 'PARTIAL';
                                        }
                                    ?>
                                    <tr class="<?php echo $row_class; ?>">
                                        <td><?php echo $p->payment_number; ?></td>
                                        <td><?php echo date('d M Y', strtotime($p->payment_schedule)); ?></td>
                                        <td><?php echo $currency->currency_code; ?> <?php echo number_format($p->principal, 2); ?></td>
                                        <td><?php echo $currency->currency_code; ?> <?php echo number_format($p->interest, 2); ?></td>
                                        <td><strong><?php echo $currency->currency_code; ?> <?php echo number_format($p->amount, 2); ?></strong></td>
                                        <td><?php echo $currency->currency_code; ?> <?php echo number_format($p->paid_amount, 2); ?></td>
                                        <td><?php echo $currency->currency_code; ?> <?php echo number_format($p->amount - $p->paid_amount, 2); ?></td>
                                        <td><span class="status-badge <?php echo $status_badge_class; ?>"><?php echo $status_text; ?></span></td>
                                        <td>
                                            <?php if($show_pay_btn): ?>

                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                <div class="view-card">
                    <div class="view-card-header">
                        <h5><i class="fa fa-money-bill-wave"></i> Deposit/Payment History</h5>
                    </div>
                    <div class="view-card-body">
                        <?php $trans = get_all_where('transaction','account_number = "'.$loan_number.'" AND credit !=0'); ?>
                        <?php if(!empty($trans)): ?>
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Amount</th>
                                        <th>Transaction Ref</th>
                                        <th>Proof</th>
                                        <th>Date</th>
                                        <th>Cashier</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($trans as $history): ?>
                                    <tr>
                                        <td><strong><?php echo $currency->currency_code; ?> <?php echo number_format($history->credit, 2); ?></strong></td>
                                        <td><?php echo $history->transaction_id; ?></td>
                                        <td>
                                            <?php if($history->proof): ?>
                                            <a href="<?php echo base_url('uploads/').$history->proof; ?>" target="_blank" class="btn-action btn-primary">
                                                <i class="fa fa-download"></i>
                                            </a>
                                            <?php else: ?>
                                            <span style="color: #9ca3af;">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d M Y H:i', strtotime($history->system_time)); ?></td>
                                        <td><?php echo $history->coresponding_account; ?></td>
                                        <td>
                                            <?php if($calculation_type != "Bullet Payment"): ?>
                                            <button onclick="get_transaction_usage('<?php echo $history->transaction_id; ?>')" class="btn-action btn-primary">
                                                <i class="fa fa-list"></i> Breakdown
                                            </button>
                                            <?php endif; ?>
                                            <button class="btn-action btn-warning">
                                                <i class="fa fa-undo"></i> Reverse
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div style="text-align: center; padding: 2rem; color: #6b7280;">
                            <i class="fa fa-receipt fa-3x" style="opacity: 0.3; margin-bottom: 1rem;"></i>
                            <p>No payment transactions recorded yet</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade modern-modal" id="payment_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-money-bill-wave mr-2"></i>Payment Deposit</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="info-grid mb-4">
                    <div class="info-item">
                        <div class="label">Loan Number</div>
                        <div class="value"><?php echo $loan_number; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="label">Payment Number</div>
                        <div class="value"><?php echo $next_payment_id; ?></div>
                    </div>
                    <div class="info-item" style="border-left-color: #059669;">
                        <div class="label">Amount Due</div>
                        <div class="value" style="color: #059669;"><?php echo $currency->currency_code; ?> <?php echo !empty($next_payment_details) ? number_format($next_payment_details->amount, 2) : '0.00'; ?></div>
                    </div>
                </div>

                <form action="<?php echo base_url('loan/pay_loan_r')?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="loan_id" value="<?php echo $loan_id?>">
                    <input type="hidden" name="payment_number" value="<?php echo $next_payment_id ?>">

                    <div class="form-group">
                        <label>Deposit Amount (<?php echo $currency->currency_code; ?>)</label>
                        <input type="text" class="form-control" name="amount" value="<?php echo !empty($next_payment_details) ? $next_payment_details->amount : '0'; ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Payment Method</label>
                                <?php $methods = get_all('payment_method'); ?>
                                <select name="payment_method" class="form-control" required>
                                    <option value="">-- Select --</option>
                                    <option value="0">Institution's Bank Savings</option>
                                    <?php foreach ($methods as $method): ?>
                                    <option value="<?php echo $method->payment_method; ?>"><?php echo $method->payment_method_name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Reference Number</label>
                                <input type="text" class="form-control" name="reference" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Payment Date</label>
                        <input type="date" class="form-control" name="paid_date" required>
                    </div>

                    <div class="form-group">
                        <label>Proof of Payment</label>
                        <input type="file" class="form-control-file" name="pay_proof" style="border: 1px solid #ced4da; padding: 8px; border-radius: 4px; width: 100%; background: #fff;">
                    </div>

                    <button type="submit" class="btn-action btn-success" style="width: 100%; justify-content: center; padding: 0.75rem;">
                        <i class="fa fa-check"></i> Submit Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Advance Payment Modal -->
<div class="modal fade modern-modal" id="advance_payment_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-forward mr-2"></i>Advance Payments</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p style="color: #dc2626; font-size: 0.9rem; margin-bottom: 1rem;">
                    <i class="fa fa-exclamation-triangle"></i> Select multiple payments to pay in advance
                </p>

                <form action="<?php echo base_url('loan/pay_advance')?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="loan_id" value="<?php echo $loan_id?>">
                    <input type="hidden" name="amount" value="<?php echo !empty($next_payment_details) ? $next_payment_details->amount : '0'; ?>">

                    <div class="table-container mb-3">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">Select</th>
                                    <th>Payment #</th>
                                    <th>Due Date</th>
                                    <th>Amount</th>
                                    <th>Paid</th>
                                    <th>Balance</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $p):
                                    $row_class = '';
                                    $status_text = $p->status;
                                    $status_badge_class = 'status-not-paid';

                                    if($loan_status == 'ACTIVE' && $p->payment_schedule < date('Y-m-d') && $p->status == 'NOT PAID') {
                                        $row_class = 'row-overdue';
                                        $status_text = 'OVERDUE';
                                        $status_badge_class = 'status-overdue';
                                    } elseif($p->status == 'PAID') {
                                        $row_class = 'row-paid';
                                        $status_badge_class = 'status-paid';
                                    } elseif($loan_status == 'ACTIVE' && $p->payment_schedule == date('Y-m-d') && $p->status == 'NOT PAID') {
                                        $row_class = 'row-due-today';
                                        $status_text = 'DUE TODAY';
                                        $status_badge_class = 'status-due-today';
                                    }

                                    if($p->partial_paid == "YES") {
                                        $status_badge_class = 'status-partial';
                                        $status_text = 'PARTIAL';
                                    }
                                ?>
                                <tr class="<?php echo $row_class; ?>">
                                    <td>
                                        <?php if($p->status == 'NOT PAID'): ?>
                                        <input type="checkbox" name="payment_number[]" value="<?php echo $p->payment_number; ?>" class="check-cls" style="width: 18px; height: 18px;">
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $p->payment_number; ?></td>
                                    <td><?php echo date('d M Y', strtotime($p->payment_schedule)); ?></td>
                                    <td><?php echo $currency->currency_code; ?> <?php echo number_format($p->amount, 2); ?></td>
                                    <td><?php echo $currency->currency_code; ?> <?php echo number_format($p->paid_amount, 2); ?></td>
                                    <td><?php echo $currency->currency_code; ?> <?php echo number_format($p->loan_balance, 2); ?></td>
                                    <td><span class="status-badge <?php echo $status_badge_class; ?>"><?php echo $status_text; ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group">
                        <label>Payment Date</label>
                        <input type="date" class="form-control" name="paid_date" required>
                    </div>

                    <button type="submit" class="btn-action btn-success submit-btn" style="width: 100%; justify-content: center; padding: 0.75rem;" disabled>
                        <i class="fa fa-check"></i> Submit Payments
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Late Payment Modal -->
<div class="modal fade modern-modal" id="late_payment_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #dc2626;">
                <h5 class="modal-title"><i class="fa fa-exclamation-circle mr-2"></i>Late Payment</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="info-grid mb-4">
                    <div class="info-item">
                        <div class="label">Loan Number</div>
                        <div class="value"><?php echo $loan_number; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="label">Payment Number</div>
                        <div class="value" id="spn"></div>
                    </div>
                    <div class="info-item">
                        <div class="label">Payment Amount</div>
                        <div class="value" id="slm"></div>
                    </div>
                    <div class="info-item" style="border-left-color: #dc2626;">
                        <div class="label">Late Charge</div>
                        <div class="value" style="color: #dc2626;" id="spc"></div>
                    </div>
                </div>

                <form action="<?php echo base_url('loan/pay_late_loan')?>" method="POST" id="latepaymentform" enctype="multipart/form-data">
                    <input type="hidden" name="loan_id" value="<?php echo $loan_id?>">
                    <input type="hidden" name="payment_number" id="pn">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Payment Amount (<?php echo $currency->currency_code; ?>)</label>
                                <input type="text" class="form-control" id="lm_late" name="amount" readonly required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Late Charge Amount</label>
                                <input type="text" class="form-control" id="late_charge_amount" name="lamount" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Payment Date</label>
                                <input type="date" class="form-control" name="paid_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Payment Method</label>
                                <?php $methods = get_all('payment_method'); ?>
                                <select name="payment_method" class="form-control">
                                    <option value="">-- Select --</option>
                                    <option value="0">Institution's Bank Savings</option>
                                    <?php foreach ($methods as $method): ?>
                                    <option value="<?php echo $method->payment_method; ?>"><?php echo $method->payment_method_name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Payment Reference Number</label>
                        <input type="text" class="form-control" name="reference" required>
                    </div>

                    <div class="form-group">
                        <label>Proof of Payment</label>
                        <input type="file" class="form-control-file" name="pay_proof" style="border: 1px solid #ced4da; padding: 8px; border-radius: 4px; width: 100%; background: #fff;">
                    </div>

                    <button type="submit" class="btn-action btn-danger" style="width: 100%; justify-content: center; padding: 0.75rem;">
                        <i class="fa fa-check"></i> Submit Late Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Breakdown Usage Modal -->
<div class="modal fade modern-modal" id="breakdown_usage" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-list mr-2"></i>Deposit Breakdown</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="breakdown_content"></div>
            </div>
        </div>
    </div>
</div>

<!-- Payoff Modal -->
<div class="modal fade modern-modal" id="payoff_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #059669;">
                <h5 class="modal-title"><i class="fa fa-check-circle mr-2"></i>Loan Pay-off</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p style="color: #dc2626; font-size: 0.9rem; margin-bottom: 1.5rem;">
                    <i class="fa fa-exclamation-triangle"></i> Please confirm you want to pay off this loan completely
                </p>

                <div class="info-grid mb-4">
                    <div class="info-item">
                        <div class="label">Loan Number</div>
                        <div class="value"><?php echo $loan_number; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="label">Current Balance</div>
                        <div class="value"><?php echo $currency->currency_code; ?> <span id="current_balance"></span></div>
                    </div>
                    <div class="info-item" id="accrued_interest_row" style="display: none;">
                        <div class="label">Accrued Interest</div>
                        <div class="value"><?php echo $currency->currency_code; ?> <span id="accrued_interest"></span></div>
                    </div>
                    <div class="info-item" id="total_payoff_row" style="display: none; border-left-color: #059669;">
                        <div class="label">Total Pay-off Amount</div>
                        <div class="value" style="color: #059669; font-size: 1.2rem;"><?php echo $currency->currency_code; ?> <span id="total_payoff"></span></div>
                    </div>
                </div>

                <!-- Non-bullet breakdown: shown only for installment loans -->
                <div id="non_bullet_breakdown" style="display:none; margin-bottom: 1rem;">
                    <table class="table table-sm table-bordered" style="margin-bottom:0.5rem;">
                        <thead style="background:#f3f4f6;">
                            <tr>
                                <th>Payment Option</th>
                                <th>Amount (<?php echo $currency->currency_code; ?>)</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Amount Due</strong><br><small class="text-muted">Outstanding started period(s)</small></td>
                                <td><span id="amount_due_display">0.00</span></td>
                                <td><button type="button" class="btn btn-sm btn-warning" onclick="setPayAmount('amount_due')">Pay This</button></td>
                            </tr>
                            <tr>
                                <td><strong>Full Pay-off</strong><br><small class="text-muted">Settle all remaining balance</small></td>
                                <td><span id="full_payoff_display">0.00</span></td>
                                <td><button type="button" class="btn btn-sm btn-success" onclick="setPayAmount('full_payoff')">Pay This</button></td>
                            </tr>
                        </tbody>
                    </table>
                    <input type="hidden" id="amount_due_value" value="0">
                    <input type="hidden" id="full_payoff_value" value="0">
                </div>

                <form action="<?php echo base_url('loan/pay_loan')?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="loan_id" value="<?php echo $loan_id ?>">
                    <input type="hidden" name="payment_number" value="<?php echo $next_payment_id ?>">
                    <input type="hidden" name="acrued_amount" id="total_amount1">

                    <div class="form-group">
                        <label>Pay-off Amount (<?php echo $currency->currency_code; ?>)</label>
                        <input type="text" class="form-control" name="amount" id="total_amount" placeholder="Pay off amount or enter custom">
                    </div>

                    <div class="form-group">
                        <label>Pay-off Date</label>
                        <input type="date" class="form-control" id="payoff_date" name="paid_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <button type="button" class="btn-action btn-info mb-3" id="calculate_btn" onclick="fetchPayoffAmount()">
                        <i class="fa fa-calculator"></i> Calculate Pay-off Amount
                    </button>

                    <div id="payment_options">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Payment Method</label>
                                    <?php $methods = get_all('payment_method'); ?>
                                    <select name="payment_method" class="form-control" required>
                                        <option value="">-- Select --</option>
                                        <option value="0">Institution's Bank Savings</option>
                                        <?php foreach ($methods as $method): ?>
                                        <option value="<?php echo $method->payment_method; ?>"><?php echo $method->payment_method_name; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Reference Number</label>
                                    <input type="text" class="form-control" name="reference" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Proof of Payment</label>
                            <input type="file" class="form-control-file" name="pay_proof" style="border: 1px solid #ced4da; padding: 8px; border-radius: 4px; width: 100%; background: #fff;">
                        </div>

                        <input type="hidden" name="payoff_amount" id="payoff_amount" value="0">
                        <button type="submit" class="btn-action btn-success" id="submit_payoff" style="width: 100%; justify-content: center; padding: 0.75rem;">
                            <i class="fa fa-check"></i> Complete Loan Pay-off
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
function setPayAmount(type) {
    var amount = type === 'amount_due'
        ? document.getElementById('amount_due_value').value
        : document.getElementById('full_payoff_value').value;
    document.getElementById('total_amount').value = amount;
    document.getElementById('total_amount1').value = amount;
    document.getElementById('payoff_amount').value = amount;
}

// Toggle payoff details
function togglePayoffDetails() {
    var details = document.getElementById('payoffDetails');
    if (details.style.display === 'none') {
        details.style.display = 'block';
    } else {
        details.style.display = 'none';
    }
}

// Payment modal functions
function pay_current() {
    $('#payment_modal').modal('show');
}

function pay_current_r() {
    $('#payment_modal').modal('show');
}

function advance_payment() {
    $('#advance_payment_modal').modal('show');
}

function advance_payment_r() {
    $('#advance_payment_modal').modal('show');
}

// Enable/disable submit button for advance payments
document.addEventListener('DOMContentLoaded', function() {
    var checkboxes = document.querySelectorAll('.check-cls');
    var submitBtn = document.querySelector('.submit-btn');

    checkboxes.forEach(function(cb) {
        cb.addEventListener('change', function() {
            var anyChecked = document.querySelectorAll('.check-cls:checked').length > 0;
            if (submitBtn) {
                submitBtn.disabled = !anyChecked;
            }
        });
    });
});

// Pay due function
function pay_due(loan_id, payment_number, amount, paid_amount) {
    document.getElementById('pn').value = payment_number;
    document.getElementById('spn').innerText = payment_number;
    document.getElementById('slm').innerText = '<?php echo $currency->currency_code; ?> ' + parseFloat(amount).toFixed(2);
    document.getElementById('lm_late').value = amount;
    document.getElementById('spc').innerText = 'Calculate below';
    $('#late_payment_modal').modal('show');
}

// Get transaction usage
function get_transaction_usage(transaction_id) {
    document.getElementById('breakdown_content').innerHTML = '<div style="text-align: center; padding: 2rem;"><i class="fa fa-spinner fa-spin fa-3x" style="color: #1e3a5f;"></i><p style="margin-top: 1rem;">Loading...</p></div>';
    $('#breakdown_usage').modal('show');

    $.ajax({
        url: '<?php echo base_url("loan/get_transaction_usage/"); ?>' + transaction_id,
        type: 'GET',
        success: function(response) {
            document.getElementById('breakdown_content').innerHTML = response;
        },
        error: function() {
            document.getElementById('breakdown_content').innerHTML = '<div style="text-align: center; padding: 2rem; color: #dc2626;"><i class="fa fa-times-circle fa-3x"></i><p style="margin-top: 1rem;">Error loading data</p></div>';
        }
    });
}


</script>
