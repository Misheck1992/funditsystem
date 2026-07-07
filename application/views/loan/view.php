<?php
$next_payment_details = $this->Payement_schedules_model->get_next($next_payment_id,$loan_id);
$currency = get_by_id('currencies','currency_id',$currency);
?>

<style>
/* Main Layout */
.loan-view-container {
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

/* Info Grid */
.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}
.info-item {
    padding: 0.75rem;
    background: #f8fafc;
    border-radius: 8px;
    border-left: 3px solid #1e3a5f;
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
.status-approved { background: #dbeafe; color: #1e40af; }
.status-rejected { background: #fee2e2; color: #991b1b; }
.status-paid { background: #dcfce7; color: #166534; }
.status-not-paid { background: #fee2e2; color: #991b1b; }
.status-overdue { background: #fecaca; color: #7f1d1d; }
.status-due-today { background: #fed7aa; color: #9a3412; }
.status-partial { background: #e0e7ff; color: #3730a3; }
.status-disbursed { background: #d1fae5; color: #065f46; }
.status-client_signed { background: #fae8ff; color: #86198f; }

/* Summary Cards Row */
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

/* Active Loan Alert */
.loan-status-alert {
    padding: 1rem 1.25rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.loan-status-alert.active {
    background: #d1fae5;
    border: 1px solid #10b981;
    color: #065f46;
}
.loan-status-alert i {
    font-size: 1.5rem;
}
.loan-status-alert .alert-content h6 {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 600;
}
.loan-status-alert .alert-content p {
    margin: 0.25rem 0 0 0;
    font-size: 0.85rem;
    opacity: 0.9;
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
.next-payment-card .payment-status.overdue {
    background: #dc2626;
}
.next-payment-card .payment-status.due-today {
    background: #f59e0b;
}
.next-payment-card .payment-status.upcoming {
    background: #059669;
}

/* Upload Section */
.upload-section {
    border: 2px dashed #d1d5db;
    border-radius: 10px;
    padding: 2rem;
    text-align: center;
    background: #f9fafb;
    transition: all 0.3s;
    cursor: pointer;
}
.upload-section:hover {
    border-color: #3b82f6;
    background: #eff6ff;
}
.upload-section i {
    font-size: 2.5rem;
    color: #9ca3af;
    margin-bottom: 0.75rem;
}
.upload-section p {
    margin: 0;
    color: #6b7280;
    font-size: 0.9rem;
}
.upload-section .browse-btn {
    display: inline-block;
    margin-top: 0.75rem;
    padding: 0.5rem 1.25rem;
    background: #1e3a5f;
    color: #fff;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
}

/* File List */
.file-list {
    margin-top: 1rem;
}
.file-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-bottom: 0.5rem;
}
.file-item .file-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;
    min-width: 0;
    overflow: hidden;
}
.file-item .file-icon {
    width: 40px;
    height: 40px;
    min-width: 40px;
    background: #e5e7eb;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
}
.file-item .file-name {
    font-weight: 500;
    color: #374151;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 250px;
}
.file-item .file-actions {
    display: flex;
    gap: 0.25rem;
    flex-shrink: 0;
}
.file-item .file-actions a,
.file-item .file-actions button {
    padding: 0.4rem 0.75rem;
    font-size: 0.8rem;
}

/* Progress Bar */
.upload-progress {
    display: none;
    margin-top: 1rem;
}
.upload-progress .progress-bar-container {
    background: #e5e7eb;
    border-radius: 10px;
    height: 8px;
    overflow: hidden;
}
.upload-progress .progress-bar-fill {
    height: 100%;
    background: #059669;
    border-radius: 10px;
    width: 0%;
    transition: width 0.3s;
}
.upload-progress .progress-text {
    font-size: 0.8rem;
    color: #6b7280;
    margin-top: 0.5rem;
    text-align: center;
}

/* Collapsible Section */
.collapsible-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    padding: 0.5rem 0;
}
.collapsible-header i.toggle-icon {
    transition: transform 0.3s;
}
.collapsible-header.collapsed i.toggle-icon {
    transform: rotate(-90deg);
}

/* Narration Box */
.narration-box {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1.25rem;
    font-size: 0.9rem;
    color: #374151;
    line-height: 1.6;
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
.modern-modal .modal-footer {
    border-top: 1px solid #e5e7eb;
    padding: 1rem 1.5rem;
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

/* Timeline */
.timeline-item {
    position: relative;
    padding-left: 30px;
    margin-bottom: 1.5rem;
}
.timeline-item::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 25px;
    bottom: -25px;
    width: 2px;
    background: #e5e7eb;
}
.timeline-item:last-child::before {
    display: none;
}
.timeline-icon {
    position: absolute;
    left: 0;
    top: 0;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.6rem;
    color: #fff;
}
.timeline-content {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
}
.timeline-content h6 {
    margin: 0 0 0.5rem 0;
    font-size: 0.9rem;
    font-weight: 600;
}
.timeline-content p {
    margin: 0;
    font-size: 0.85rem;
    color: #6b7280;
}
.timeline-content .timeline-meta {
    font-size: 0.75rem;
    color: #9ca3af;
    margin-top: 0.5rem;
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
        <h2 class="header-title">Loan Details</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="<?php echo base_url('loan/track')?>">Loans</a>
                <span class="breadcrumb-item active"><?php echo $loan_number; ?></span>
            </nav>
        </div>
    </div>

    <div class="loan-view-container">
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

        // Determine which interest and total-amount figures to show on the cards
        if ($loan_status == 'CLOSED') {
            // Use actual interest from paid rows (computed in controller)
            $display_interest = $total_paid_interest;
            $display_total    = $loan_principal + $total_paid_interest;
        } elseif ($calculation_type == 'Bullet Payment' && !empty($acrued['accrued_interest'])) {
            // Active bullet loan – show live accrued figure
            $display_interest = $acrued['accrued_interest'];
            $display_total    = $loan_principal + $acrued['accrued_interest'];
        } else {
            $display_interest = $loan_interest_amount;
            $display_total    = $loan_amount_total;
        }

        $remaining_balance = $display_total - $total_paid;
        ?>

        <!-- Summary Cards -->
        <div class="summary-row">
            <div class="summary-card">
                <div class="sc-label">Principal Amount</div>
                <div class="sc-value"><?php echo $currency->currency_code; ?> <?php echo number_format($loan_principal, 2); ?></div>
            </div>
            <div class="summary-card info">
                <div class="sc-label">Total Interest<?php echo ($loan_status == 'CLOSED') ? ' (Accrued)' : ''; ?></div>
                <div class="sc-value" style="color: #3b82f6;"><?php echo $currency->currency_code; ?> <?php echo number_format($display_interest, 2); ?></div>
            </div>
            <div class="summary-card warning">
                <div class="sc-label">Total Loan Amount<?php echo ($loan_status == 'CLOSED') ? ' (at Maturity)' : ''; ?></div>
                <div class="sc-value"><?php echo $currency->currency_code; ?> <?php echo number_format($display_total, 2); ?></div>
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

        <!-- Quick Action Buttons Row -->
        <div style="margin-bottom: 1.5rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <button onclick="openNotesModal()" class="btn-action btn-info" style="flex: 1; min-width: 180px; justify-content: center; padding: 0.75rem; font-size: 0.95rem; border-radius: 10px;">
                <i class="fa fa-comments"></i> Internal Notes <span id="notes_count_badge" style="background: #dc2626; color: #fff; padding: 0.2rem 0.5rem; border-radius: 50px; font-size: 0.75rem; margin-left: 0.5rem; display: none;">0</span>
            </button>
            <a href="<?php echo base_url('loan/report/').$loan_id ?>" class="btn-action btn-danger" style="flex: 1; min-width: 180px; justify-content: center; padding: 0.75rem; font-size: 0.95rem; border-radius: 10px; text-decoration: none;">
                <i class="fa fa-file-pdf"></i> Download Report
            </a>
            <button onclick="viewApprovalTrail('<?php echo $loan_id; ?>')" class="btn-action btn-secondary" style="flex: 1; min-width: 180px; justify-content: center; padding: 0.75rem; font-size: 0.95rem; border-radius: 10px; background: #6b7280;">
                <i class="fa fa-history"></i> View Approval Trail
            </button>
            <a href="<?php echo base_url('loan/appraisal_report/').$loan_id; ?>" target="_blank" class="btn-action" style="flex: 1; min-width: 180px; justify-content: center; padding: 0.75rem; font-size: 0.95rem; border-radius: 10px; text-decoration: none; background: linear-gradient(135deg, #c97b3d, #d4a853); color: #fff;">
                <i class="fa fa-clipboard"></i> Loan Appraisal
            </a>
        </div>

        <!-- Sent Back Alert -->
        <?php if(isset($sent_back) && $sent_back == 1): ?>
        <div style="background: linear-gradient(135deg, #fef3c7, #fde68a); border: 2px solid #f59e0b; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: flex-start; gap: 1rem;">
            <div style="background: #f59e0b; color: #fff; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <i class="fa fa-exclamation-triangle" style="font-size: 1.25rem;"></i>
            </div>
            <div style="flex: 1;">
                <h5 style="margin: 0 0 0.5rem 0; color: #92400e; font-size: 1rem; font-weight: 600;">
                    <i class="fa fa-undo"></i> Loan Sent Back for Corrections
                </h5>
                <p style="margin: 0 0 0.5rem 0; color: #78350f; font-size: 0.9rem;">
                    This loan was sent back and requires attention before it can proceed to the next approval stage.
                </p>
                <?php if(isset($sent_back_comment) && !empty($sent_back_comment)): ?>
                <div style="background: #fff; border-radius: 8px; padding: 0.75rem; margin-top: 0.5rem;">
                    <strong style="color: #92400e; font-size: 0.85rem;">Reason:</strong>
                    <p style="margin: 0.25rem 0 0 0; color: #78350f; font-size: 0.9rem;"><?php echo htmlspecialchars($sent_back_comment); ?></p>
                </div>
                <?php endif; ?>
                <?php if(isset($sent_back_by_name) && !empty($sent_back_by_name)): ?>
                <p style="margin: 0.5rem 0 0 0; color: #a16207; font-size: 0.8rem;">
                    <i class="fa fa-user"></i> Sent back by: <strong><?php echo $sent_back_by_name; ?></strong>
                    <?php if(isset($sent_back_date) && !empty($sent_back_date)): ?>
                    on <?php echo date('d M Y H:i', strtotime($sent_back_date)); ?>
                    <?php endif; ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Left Column - Loan Info & Actions -->
            <div class="col-lg-3">
                <!-- Loan Info Card -->
                <div class="view-card">
                    <div class="view-card-header">
                        <h5><i class="fa fa-info-circle"></i> Loan Information</h5>
                    </div>
                    <div class="view-card-body">
                        <div class="info-item" style="margin-bottom: 0.75rem;">
                            <div class="label">Loan Number</div>
                            <div class="value"><?php echo $loan_number; ?></div>
                        </div>
                        <div class="info-item" style="margin-bottom: 0.75rem;">
                            <div class="label">Loan Product</div>
                            <div class="value"><?php echo $loan_product; ?></div>
                        </div>
                        <div class="info-item" style="margin-bottom: 0.75rem;">
                            <div class="label">Customer</div>
                            <div class="value"><a href="<?php echo base_url($preview_url).$customer_id?>"><?php echo $loan_customer; ?></a></div>
                        </div>
                        <div class="info-item" style="margin-bottom: 0.75rem;">
                            <div class="label">Status</div>
                            <div class="value">
                                <?php
                                $status_class = 'status-pending';
                                $status_lower = strtolower($loan_status);
                                if($status_lower == 'active') $status_class = 'status-active';
                                elseif(strpos($status_lower, 'approved') !== false) $status_class = 'status-approved';
                                elseif(strpos($status_lower, 'reject') !== false) $status_class = 'status-rejected';
                                elseif($status_lower == 'disbursed') $status_class = 'status-disbursed';
                                ?>
                                <span class="status-badge <?php echo $status_class; ?>"><?php echo $loan_status; ?></span>
                            </div>
                        </div>
                        <div class="info-item" style="margin-bottom: 0.75rem;">
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
                <?php else: ?>
                <div class="loan-status-alert active">
                    <i class="fa fa-check-circle"></i>
                    <div class="alert-content">
                        <h6>Fully Paid</h6>
                        <p>No more payments to make</p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Actions Panel -->
                <div class="actions-panel">
                    <h6><i class="fa fa-cog"></i> Actions</h6>

                    <?php if (isset($action) && !empty($action) && $loan_status != 'ACTIVE'): ?>
                        <?php if ($action == 'multi_approve'): ?>
                            <!-- Multi-Level Approval Progress -->
                            <?php
                            $approval_count = isset($approvers) ? count($approvers) : 0;
                            ?>
                            <div style="background: #f8fafc; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                                <div style="font-size: 0.85rem; font-weight: 600; color: #374151; margin-bottom: 0.75rem;">
                                    <i class="fa fa-check-double"></i> Approval Progress
                                </div>
                                <!-- Progress circles -->
                                <div style="display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.75rem;">
                                    <?php for($i = 1; $i <= 3; $i++): ?>
                                        <?php if($i <= $approval_count): ?>
                                            <div style="width: 28px; height: 28px; border-radius: 50%; background: #059669; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.7rem;">
                                                <i class="fa fa-check"></i>
                                            </div>
                                        <?php elseif($i == $approval_count + 1): ?>
                                            <div style="width: 28px; height: 28px; border-radius: 50%; background: #f59e0b; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700;">
                                                <?php echo $i; ?>
                                            </div>
                                        <?php else: ?>
                                            <div style="width: 28px; height: 28px; border-radius: 50%; background: #e5e7eb; color: #6b7280; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700;">
                                                <?php echo $i; ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if($i < 3): ?>
                                            <div style="width: 15px; height: 3px; background: <?php echo $i < $approval_count ? '#059669' : '#e5e7eb'; ?>;"></div>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    <span style="margin-left: 0.5rem; font-size: 0.85rem; color: #6b7280; font-weight: 500;"><?php echo $approval_count; ?> of 3</span>
                                </div>
                                <!-- Approvers list -->
                                <?php if(!empty($approvers)): ?>
                                <div style="font-size: 0.75rem; color: #6b7280;">
                                    <?php foreach($approvers as $idx => $approver): ?>
                                        <div style="display: inline-block; background: #ecfdf5; color: #059669; padding: 0.15rem 0.5rem; border-radius: 20px; margin: 0.15rem;">
                                            <i class="fa fa-user-check"></i> <?php echo ($idx + 1); ?>. <?php echo $approver['user_name']; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>

                            <?php if(isset($can_approve) && $can_approve): ?>
                                <button onclick="openMultiApprovalModal('MULTI_APPROVE', '<?php echo $loan_id; ?>', <?php echo $approval_count + 1; ?>)" class="btn-action btn-success" style="width: 100%; justify-content: center;">
                                    <i class="fa fa-check"></i> Approve (#<?php echo $approval_count + 1; ?>)
                                </button>
                                <button onclick="openMultiApprovalModal('REJECT', '<?php echo $loan_id; ?>', 0)" class="btn-action btn-danger" style="width: 100%; justify-content: center;">
                                    <i class="fa fa-times"></i> Reject
                                </button>
                                <button onclick="openSendBackModal('<?php echo $loan_id; ?>')" class="btn-action btn-warning" style="width: 100%; justify-content: center;">
                                    <i class="fa fa-undo"></i> Return for Edit
                                </button>
                                <a href="<?php echo base_url('loan/edit_single_loan_request/').$loan_id; ?>" class="btn-action btn-primary" style="width: 100%; justify-content: center;">
                                    <i class="fa fa-edit"></i> Edit Loan
                                </a>
                            <?php else: ?>
                                <div style="background: #fef3c7; border: 1px solid #fbbf24; border-radius: 8px; padding: 0.75rem; margin-bottom: 0.5rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; color: #92400e; font-size: 0.85rem;">
                                        <i class="fa fa-ban"></i>
                                        <span><?php echo isset($approval_reason) ? $approval_reason : 'Cannot approve'; ?></span>
                                    </div>
                                </div>
                                <button onclick="openSendBackModal('<?php echo $loan_id; ?>')" class="btn-action btn-warning" style="width: 100%; justify-content: center;">
                                    <i class="fa fa-undo"></i> Return for Edit
                                </button>
                                <a href="<?php echo base_url('loan/edit_single_loan_request/').$loan_id; ?>" class="btn-action btn-primary" style="width: 100%; justify-content: center;">
                                    <i class="fa fa-edit"></i> Edit Loan
                                </a>
                            <?php endif; ?>
                        <?php elseif ($action == 'approve_first'): ?>
                            <button onclick="openApprovalModal('APPROVED_FIRST', '<?php echo $loan_id; ?>')" class="btn-action btn-warning" style="width: 100%; justify-content: center;">
                                <i class="fa fa-check"></i> Approve (1st Level)
                            </button>
                            <button onclick="openApprovalModal('REJECT', '<?php echo $loan_id; ?>')" class="btn-action btn-danger" style="width: 100%; justify-content: center;">
                                <i class="fa fa-times"></i> Reject
                            </button>
                            <button onclick="openSendBackModal('<?php echo $loan_id; ?>')" class="btn-action btn-secondary" style="width: 100%; justify-content: center; background: #6b7280;">
                                <i class="fa fa-undo"></i> Return for Edit
                            </button>
                            <a href="<?php echo base_url('loan/edit_single_loan_request/').$loan_id; ?>" class="btn-action btn-primary" style="width: 100%; justify-content: center;">
                                <i class="fa fa-edit"></i> Edit Loan
                            </a>
                        <?php elseif ($action == 'recommend'): ?>
                            <button onclick="openApprovalModal('RECOMMENDED', '<?php echo $loan_id; ?>')" class="btn-action btn-warning" style="width: 100%; justify-content: center;">
                                <i class="fa fa-thumbs-up"></i> Recommend
                            </button>
                            <button onclick="openApprovalModal('REJECT', '<?php echo $loan_id; ?>')" class="btn-action btn-danger" style="width: 100%; justify-content: center;">
                                <i class="fa fa-times"></i> Reject
                            </button>
                            <button onclick="openSendBackModal('<?php echo $loan_id; ?>')" class="btn-action btn-secondary" style="width: 100%; justify-content: center; background: #6b7280;">
                                <i class="fa fa-undo"></i> Return for Edit
                            </button>
                            <a href="<?php echo base_url('loan/edit_single_loan_request/').$loan_id; ?>" class="btn-action btn-primary" style="width: 100%; justify-content: center;">
                                <i class="fa fa-edit"></i> Edit Loan
                            </a>
                        <?php elseif ($action == 'approve_second'): ?>
                            <button onclick="openApprovalModal('APPROVED_SECOND', '<?php echo $loan_id; ?>')" class="btn-action btn-success" style="width: 100%; justify-content: center;">
                                <i class="fa fa-check-double"></i> Second Approve
                            </button>
                            <button onclick="openApprovalModal('REJECT', '<?php echo $loan_id; ?>')" class="btn-action btn-danger" style="width: 100%; justify-content: center;">
                                <i class="fa fa-times"></i> Reject
                            </button>
                            <button onclick="openSendBackModal('<?php echo $loan_id; ?>')" class="btn-action btn-secondary" style="width: 100%; justify-content: center; background: #6b7280;">
                                <i class="fa fa-undo"></i> Return for Edit
                            </button>
                            <a href="<?php echo base_url('loan/edit_single_loan_request/').$loan_id; ?>" class="btn-action btn-primary" style="width: 100%; justify-content: center;">
                                <i class="fa fa-edit"></i> Edit Loan
                            </a>
                        <?php elseif ($action == 'approve_third'): ?>
                            <button onclick="openApprovalModal('APPROVED', '<?php echo $loan_id; ?>')" class="btn-action btn-success" style="width: 100%; justify-content: center;">
                                <i class="fa fa-check-circle"></i> Final Approve
                            </button>
                            <button onclick="openApprovalModal('REJECT', '<?php echo $loan_id; ?>')" class="btn-action btn-danger" style="width: 100%; justify-content: center;">
                                <i class="fa fa-times"></i> Reject
                            </button>
                            <button onclick="openSendBackModal('<?php echo $loan_id; ?>')" class="btn-action btn-secondary" style="width: 100%; justify-content: center; background: #6b7280;">
                                <i class="fa fa-undo"></i> Return for Edit
                            </button>
                            <a href="<?php echo base_url('loan/edit_single_loan_request/').$loan_id; ?>" class="btn-action btn-primary" style="width: 100%; justify-content: center;">
                                <i class="fa fa-edit"></i> Edit Loan
                            </a>
                        <?php elseif ($action == 'disburse'): ?>
                            <button onclick="disburse_loan_charge_pre_paid('<?php echo $loan_id; ?>','<?php echo $loan_date ?>')" class="btn-action btn-success" style="width: 100%; justify-content: center;">
                                <i class="fa fa-money-bill"></i> Disburse Loan
                            </button>
                            <button onclick="openApprovalModal('REJECT', '<?php echo $loan_id; ?>')" class="btn-action btn-danger" style="width: 100%; justify-content: center;">
                                <i class="fa fa-times"></i> Reject
                            </button>
                            <button onclick="openSendBackModal('<?php echo $loan_id; ?>')" class="btn-action btn-secondary" style="width: 100%; justify-content: center; background: #6b7280;">
                                <i class="fa fa-undo"></i> Return for Edit
                            </button>
                            <a href="<?php echo base_url('loan/edit_single_loan_request/').$loan_id; ?>" class="btn-action btn-primary" style="width: 100%; justify-content: center;">
                                <i class="fa fa-edit"></i> Edit Loan
                            </a>
                        <?php elseif ($action == 'delete_recommend'): ?>
                            <a href="<?php echo base_url('Loan/delete_recommend/').$loan_id ?>" class="btn-action btn-warning" style="width: 100%; justify-content: center;">
                                <i class="fa fa-trash"></i> Recommend Delete
                            </a>
                            <a href="<?php echo base_url('Loan/delete_reject/').$loan_id ?>" class="btn-action btn-primary" style="width: 100%; justify-content: center;">
                                <i class="fa fa-undo"></i> Reject Delete
                            </a>
                        <?php elseif ($action == 'delete_approve'): ?>
                            <a href="<?php echo base_url('Loan/delete_approve/').$loan_id ?>" class="btn-action btn-warning" style="width: 100%; justify-content: center;">
                                <i class="fa fa-trash"></i> Approve Delete
                            </a>
                            <a href="<?php echo base_url('Loan/delete_reject/').$loan_id ?>" class="btn-action btn-primary" style="width: 100%; justify-content: center;">
                                <i class="fa fa-undo"></i> Reject Delete
                            </a>
                        <?php endif; ?>
                    <?php elseif ($loan_status == 'APPROVED'): ?>
                        <div class="loan-status-alert" style="padding: 0.75rem; margin-bottom: 0.5rem; background: #dbeafe; border: 1px solid #3b82f6; border-radius: 8px;">
                            <i class="fa fa-file-signature" style="font-size: 1rem; color: #2563eb;"></i>
                            <div class="alert-content">
                                <h6 style="font-size: 0.85rem; color: #1e40af;">Upload Signed Client Copy</h6>
                                <p style="font-size: 0.75rem; color: #3b82f6; margin: 0;">Upload signed documents below, then send for disbursement</p>
                            </div>
                        </div>
                        <a href="<?php echo base_url('loan/send_for_disburse/' . $loan_id); ?>" class="btn-action btn-success" style="width: 100%; justify-content: center;" onclick="return confirm('Are you sure you want to send this loan for disbursement? Please ensure all signed documents are uploaded.')">
                            <i class="fa fa-paper-plane"></i> Send for Disburse
                        </a>
                        <button onclick="openApprovalModal('REJECT', '<?php echo $loan_id; ?>')" class="btn-action btn-danger" style="width: 100%; justify-content: center;">
                            <i class="fa fa-times"></i> Reject
                        </button>
                        <button onclick="openSendBackModal('<?php echo $loan_id; ?>')" class="btn-action btn-secondary" style="width: 100%; justify-content: center; background: #6b7280;">
                            <i class="fa fa-undo"></i> Return for Edit
                        </button>
                        <a href="<?php echo base_url('loan/edit_single_loan_request/').$loan_id; ?>" class="btn-action btn-primary" style="width: 100%; justify-content: center;">
                            <i class="fa fa-edit"></i> Edit Loan
                        </a>
                    <?php elseif ($loan_status == 'CLIENT_SIGNED'): ?>
                        <div class="loan-status-alert" style="padding: 0.75rem; margin-bottom: 0.5rem; background: #d1fae5; border: 1px solid #059669; border-radius: 8px;">
                            <i class="fa fa-clock" style="font-size: 1rem; color: #059669;"></i>
                            <div class="alert-content">
                                <h6 style="font-size: 0.85rem; color: #065f46;">Ready for Disbursement</h6>
                                <p style="font-size: 0.75rem; color: #059669; margin: 0;">Client has signed. Ready to disburse.</p>
                            </div>
                        </div>
                        <?php
                        // Check if user has access to loan/approved (disburse rights)
                        $can_disburse = false;
                        $disburse_menuitem = $this->db->get_where('menuitems', array('method' => 'loan/approved'))->row();
                        if (!$disburse_menuitem) {
                            $disburse_menuitem = $this->db->get_where('menuitems', array('method' => 'Loan/approved'))->row();
                        }
                        if ($disburse_menuitem) {
                            foreach ($this->session->userdata('access') as $access) {
                                if ($access->controllerid == $disburse_menuitem->id) {
                                    $can_disburse = true;
                                    break;
                                }
                            }
                        }
                        ?>
                        <?php if ($can_disburse): ?>
                            <button type="button" class="btn-action btn-success" style="width: 100%; justify-content: center;" data-toggle="modal" data-target="#disburseModal">
                                <i class="fa fa-money-bill-wave"></i> Disburse Loan
                            </button>
                        <?php else: ?>
                            <div style="background: #fef3c7; border: 1px solid #fbbf24; border-radius: 8px; padding: 0.75rem; margin-top: 0.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; color: #92400e; font-size: 0.8rem;">
                                    <i class="fa fa-info-circle"></i>
                                    <span>Awaiting disbursement by authorized personnel.</span>
                                </div>
                            </div>
                        <?php endif; ?>
                        <button onclick="openApprovalModal('REJECT', '<?php echo $loan_id; ?>')" class="btn-action btn-danger" style="width: 100%; justify-content: center;">
                            <i class="fa fa-times"></i> Reject
                        </button>
                        <button onclick="openSendBackModal('<?php echo $loan_id; ?>')" class="btn-action btn-secondary" style="width: 100%; justify-content: center; background: #6b7280;">
                            <i class="fa fa-undo"></i> Return for Edit
                        </button>
                        <a href="<?php echo base_url('loan/edit_single_loan_request/').$loan_id; ?>" class="btn-action btn-primary" style="width: 100%; justify-content: center;">
                            <i class="fa fa-edit"></i> Edit Loan
                        </a>
                    <?php elseif ($loan_status == 'ACTIVE'): ?>
                        <div class="loan-status-alert active" style="padding: 0.75rem; margin-bottom: 0.5rem;">
                            <i class="fa fa-check-circle" style="font-size: 1rem;"></i>
                            <div class="alert-content">
                                <h6 style="font-size: 0.85rem;">Loan Active</h6>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if ($loan_status == 'INITIATED' || (isset($sent_back) && $sent_back == 1)): ?>
                        <a href="<?php echo base_url('loan/edit_single_loan_request/').$loan_id; ?>" class="btn-action btn-warning" style="width: 100%; justify-content: center; margin-bottom: 0.5rem;">
                            <i class="fa fa-edit"></i> Edit Loan
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Middle Column - Payment Schedule & History -->
            <div class="col-lg-9">
                <?php if($calculation_type == 'Bullet Payment'): ?>
                    <?php if (!empty($acrued) && $acrued['status'] == 'success' && isset($acrued['debug']) && $loan_status != 'CLOSED'): ?>
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
                                        <div class="label">Maturity Date</div>
                                        <div class="value"><?= $acrued['debug']['maturity_date'] ?></div>
                                    </div>
                                    <div class="info-item">
                                        <div class="label">Current Date</div>
                                        <div class="value"><?= $acrued['debug']['payoff_date'] ?></div>
                                    </div>
                                    <div class="info-item">
                                        <div class="label">Days Past Maturity</div>
                                        <div class="value"><?= $acrued['debug']['days_past_maturity'] ?> Days</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="label">Monthly Interest Rate</div>
                                        <div class="value"><?= number_format($acrued['debug']['monthly_interest_rate'] * 100, 2) ?>%</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="label">Original Interest (<?= $acrued['debug']['term_months'] ?> mo)</div>
                                        <div class="value"><?= $currency->currency_code ?> <?= number_format($acrued['debug']['original_interest'], 2) ?></div>
                                    </div>
                                    <div class="info-item">
                                        <div class="label">Total at Maturity</div>
                                        <div class="value"><?= $currency->currency_code ?> <?= number_format($acrued['debug']['maturity_total'], 2) ?></div>
                                    </div>
                                    <?php if($acrued['debug']['days_past_maturity'] > 0): ?>
                                    <div class="info-item" style="border-left-color: #dc2626;">
                                        <div class="label">Arrears Interest (compounded)</div>
                                        <div class="value" style="color: #dc2626;"><?= $currency->currency_code ?> <?= number_format($acrued['accrued_interest'] - $acrued['debug']['original_interest'], 2) ?></div>
                                    </div>
                                    <?php endif; ?>
                                    <div class="info-item">
                                        <div class="label">Total Accrued Interest</div>
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
                                <div class="narration-box mt-3">
                                    <pre style="white-space: pre-wrap; margin: 0;"><?= $acrued['debug']['calculation_explanation'] ?></pre>
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
                        <h5><i class="fa fa-calendar-alt"></i> Payment Schedule</h5>
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
                                        <?php if($calculation_type == 'Bullet Payment'): ?>
                                        <th>Interest on Maturity</th>
                                        <th>Accrued Interest</th>
                                        <th>Amount Due on Maturity</th>
                                        <th>Amount Due</th>
                                        <th>Amount Paid</th>
                                        <th>Current Balance</th>
                                        <?php else: ?>
                                        <th>Interest</th>
                                        <th>Amount Due</th>
                                        <th>Amount Paid</th>
                                        <th>Balance</th>
                                        <?php endif; ?>
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
                                        <td><?php echo $p->payment_number; ?></td>
                                        <td><?php echo date('d M Y', strtotime($p->payment_schedule)); ?></td>
                                        <td><?php echo $currency->currency_code; ?> <?php echo number_format($p->principal, 2); ?></td>
                                        <?php if($calculation_type == 'Bullet Payment'): ?>
                                        <td><?php echo $currency->currency_code; ?> <?php echo number_format($p->interest, 2); ?></td>
                                        <td><?php echo $currency->currency_code; ?> <?php echo ($p->status == 'PAID') ? number_format($total_paid_interest, 2) : (isset($acrued['accrued_interest']) ? number_format($acrued['accrued_interest'], 2) : number_format($p->interest, 2)); ?></td>
                                        <td><strong><?php echo $currency->currency_code; ?> <?php echo number_format($p->amount, 2); ?></strong></td>
                                        <td><strong style="color: #059669;"><?php echo $currency->currency_code; ?> <?php echo ($p->status == 'PAID') ? number_format(0, 2) : (isset($acrued['total_payoff']) ? number_format($acrued['total_payoff'], 2) : number_format($p->amount, 2)); ?></strong></td>
                                        <td><?php echo $currency->currency_code; ?> <?php echo number_format($p->paid_amount, 2); ?></td>
                                        <td><strong style="color: #dc2626;"><?php echo $currency->currency_code; ?> <?php echo ($p->status == 'PAID') ? number_format(0, 2) : (isset($acrued['total_payoff']) ? number_format($acrued['total_payoff'] - $p->paid_amount, 2) : number_format($p->loan_balance, 2)); ?></strong></td>
                                        <?php else: ?>
                                        <td><?php echo $currency->currency_code; ?> <?php echo number_format($p->interest, 2); ?></td>
                                        <td><strong><?php echo $currency->currency_code; ?> <?php echo number_format($p->amount, 2); ?></strong></td>
                                        <td><?php echo $currency->currency_code; ?> <?php echo number_format($p->paid_amount, 2); ?></td>
                                        <td><?php echo $currency->currency_code; ?> <?php echo number_format($p->loan_balance, 2); ?></td>
                                        <?php endif; ?>
                                        <td><span class="status-badge <?php echo $status_badge_class; ?>"><?php echo $status_text; ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <?php if($calculation_type != 'Bullet Payment'): ?>
                                <tfoot>
                                    <tr style="background:#f1f5f9; font-weight:700; border-top:2px solid #cbd5e1;">
                                        <td colspan="2" style="padding:0.6rem 0.75rem;">Totals</td>
                                        <td style="padding:0.6rem 0.75rem;"><?php echo $currency->currency_code; ?> <?php echo number_format($total_schedule_principal, 2); ?></td>
                                        <td style="padding:0.6rem 0.75rem; color:#3b82f6;"><?php echo $currency->currency_code; ?> <?php echo number_format($total_schedule_interest, 2); ?></td>
                                        <td style="padding:0.6rem 0.75rem; color:#f59e0b;"><?php echo $currency->currency_code; ?> <?php echo number_format($total_schedule_amount, 2); ?></td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                                <?php endif; ?>
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
                                        <th>Reason</th>
                                        <th>Date</th>
                                        <th>Cashier Account</th>
                                        <th>Proof</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($trans as $history): ?>
                                    <tr>
                                        <td><strong><?php echo $currency->currency_code; ?> <?php echo number_format($history->credit, 2); ?></strong></td>
                                        <td><?php echo $history->transaction_id; ?></td>
                                        <td><?php echo $history->reason; ?></td>
                                        <td><?php echo date('d M Y H:i', strtotime($history->system_time)); ?></td>
                                        <td><?php echo $history->coresponding_account; ?></td>
                                        <td>
                                            <?php if($history->proof):
                                                $proof_ext = strtolower(pathinfo($history->proof, PATHINFO_EXTENSION));
                                                $proof_previewable = in_array($proof_ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
                                                $proof_type = ($proof_ext == 'pdf') ? 'pdf' : 'image';
                                            ?>
                                            <?php if($proof_previewable): ?>
                                            <button onclick="previewFile('<?php echo base_url('uploads/').$history->proof; ?>', 'Payment Proof', '<?php echo $proof_type; ?>')" class="btn-action btn-info" title="Preview">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <?php endif; ?>
                                            <a href="<?php echo base_url('uploads/').$history->proof; ?>" download class="btn-action btn-primary" title="Download">
                                                <i class="fa fa-download"></i>
                                            </a>
                                            <?php else: ?>
                                            <span style="color: #9ca3af;">No file</span>
                                            <?php endif; ?>
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

                <!-- Narration -->
                <?php if(!empty($narration)): ?>
                <div class="view-card">
                    <div class="view-card-header">
                        <h5><i class="fa fa-comment-alt"></i> Narration</h5>
                    </div>
                    <div class="view-card-body">
                        <div class="narration-box">
                            <?php echo $narration; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Bank Statements -->
                <?php if(!empty($bank_statements)): ?>
                <div class="view-card">
                    <div class="view-card-header">
                        <h5><i class="fa fa-university"></i> Bank Statements</h5>
                        <span class="header-badge"><?php echo count($bank_statements); ?> Statement(s)</span>
                    </div>
                    <div class="view-card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Credit</th>
                                    <th>Debit</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($bank_statements as $bs): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($bs->month); ?></td>
                                    <td><?php echo number_format($bs->credit, 2); ?></td>
                                    <td><?php echo number_format($bs->debit, 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <div class="row">
                    <!-- Loan Files -->
                    <div class="col-lg-6">
                        <div class="view-card">
                            <div class="view-card-header">
                                <h5><i class="fa fa-folder-open"></i> Loan Files</h5>
                                <span class="header-badge"><?php echo count($files); ?> Files</span>
                            </div>
                            <div class="view-card-body">
                                <!-- Upload Section -->
                                <div class="upload-section" onclick="document.getElementById('loan_file_input').click();">
                                    <i class="fa fa-cloud-upload-alt"></i>
                                    <p>Drag files here or click to upload</p>
                                    <span class="browse-btn">Browse Files</span>
                                </div>
                                <input type="file" id="loan_file_input" style="display: none;" onchange="uploadLoanFile(this)" multiple>

                                <div class="upload-progress" id="upload_progress">
                                    <div class="progress-bar-container">
                                        <div class="progress-bar-fill" id="progress_bar"></div>
                                    </div>
                                    <div class="progress-text" id="progress_text">0%</div>
                                </div>

                                <!-- File List -->
                                <div class="file-list" id="file_list">
                                    <?php if(!empty($files)): ?>
                                    <?php foreach ($files as $file):
                                        $file_ext = strtolower(pathinfo($file->real_file, PATHINFO_EXTENSION));
                                        $is_previewable = in_array($file_ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
                                        $is_pdf = ($file_ext == 'pdf');
                                        $is_image = in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);

                                        // Set appropriate icon
                                        $file_icon = 'fa-file';
                                        if ($is_pdf) $file_icon = 'fa-file-pdf-o';
                                        elseif ($is_image) $file_icon = 'fa-file-image-o';
                                        elseif (in_array($file_ext, ['doc', 'docx'])) $file_icon = 'fa-file-word-o';
                                        elseif (in_array($file_ext, ['xls', 'xlsx'])) $file_icon = 'fa-file-excel-o';
                                    ?>
                                    <div class="file-item">
                                        <div class="file-info">
                                            <div class="file-icon">
                                                <i class="fa <?php echo $file_icon; ?>"></i>
                                            </div>
                                            <div class="file-name"><?php echo $file->file_name; ?></div>
                                        </div>
                                        <div class="file-actions">
                                            <?php if($is_previewable): ?>
                                            <button onclick="previewFile('<?php echo base_url('uploads/').$file->real_file; ?>', '<?php echo $file->file_name; ?>', '<?php echo $is_pdf ? 'pdf' : 'image'; ?>')" class="btn-action btn-info" title="Preview">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <?php endif; ?>
                                            <a href="<?php echo base_url('uploads/').$file->real_file; ?>" download class="btn-action btn-primary" title="Download">
                                                <i class="fa fa-download"></i>
                                            </a>
                                            <button onclick="deleteFile(<?php echo $file->file_id; ?>, this)" class="btn-action btn-danger" title="Delete">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                    <div id="no_files_msg" style="text-align: center; padding: 1rem; color: #6b7280;">
                                        <p>No files uploaded yet</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Collateral -->
                    <div class="col-lg-6">
                        <div class="view-card">
                            <div class="view-card-header">
                                <h5><i class="fa fa-shield-alt"></i> Linked Collaterals</h5>
                                <button onclick="openLinkCollateralModal()" class="btn-action btn-success" style="padding: 0.4rem 1rem;">
                                    <i class="fa fa-link"></i> Link Collateral
                                </button>
                            </div>
                            <div class="view-card-body">
                                <div id="collateral_summary">
                                    <?php if (!empty($linked_collaterals)): ?>
                                        <div style="display: flex; justify-content: space-around; text-align: center;">
                                            <div>
                                                <div style="font-size: 1.5rem; font-weight: 700; color: #1e3a5f;"><?php echo count($linked_collaterals); ?></div>
                                                <div style="font-size: 0.75rem; color: #6b7280;">Linked Items</div>
                                            </div>
                                            <div>
                                                <div style="font-size: 1.1rem; font-weight: 700; color: #059669;"><?php echo $currency->currency_code; ?> <?php echo number_format($total_force_sale, 2); ?></div>
                                                <div style="font-size: 0.75rem; color: #6b7280;">Force Sale Total</div>
                                            </div>
                                            <div>
                                                <div style="font-size: 1.1rem; font-weight: 700; color: #dc2626;"><?php echo $currency->currency_code; ?> <?php echo number_format($total_utilized, 2); ?></div>
                                                <div style="font-size: 0.75rem; color: #6b7280;">Utilized</div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <p style="color: #6b7280; text-align: center; margin: 0;">
                                            <i class="fa fa-shield-alt" style="opacity: 0.3;"></i> No collateral linked yet
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($linked_collaterals)): ?>
                                <div style="margin-top: 1rem; padding: 0.75rem; background: #f0f9ff; border-radius: 8px; font-size: 0.85rem;">
                                    <div class="d-flex justify-content-between">
                                        <span>Total Force Sale Value:</span>
                                        <strong style="color: #059669;"><?php echo $currency->currency_code; ?> <?php echo number_format($total_force_sale, 2); ?></strong>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Amount Utilized (This Loan):</span>
                                        <strong style="color: #dc2626;"><?php echo $currency->currency_code; ?> <?php echo number_format($total_utilized, 2); ?></strong>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <button onclick="openCollateralListModal()" class="btn-action btn-info" style="width: 100%; justify-content: center; margin-top: 1rem;">
                                    <i class="fa fa-list"></i> View All Linked Collaterals
                                    <?php if (!empty($linked_collaterals)): ?>
                                    <span style="background: #dc2626; color: #fff; padding: 0.2rem 0.5rem; border-radius: 50px; font-size: 0.75rem; margin-left: 0.5rem;"><?php echo count($linked_collaterals); ?></span>
                                    <?php endif; ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank Statements (for institutions) -->
                <?php if($customer_type == 'institution'): ?>
                <div class="view-card">
                    <div class="view-card-header">
                        <h5><i class="fa fa-university"></i> Bank Statements</h5>
                    </div>
                    <div class="view-card-body">
                        <?php
                        $bank_statements = $this->db->where('loan_id', $loan_id)
                                                   ->order_by('year DESC, month DESC')
                                                   ->get('bank_statements')->result();
                        ?>
                        <?php if(!empty($bank_statements)): ?>
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Credit Amount</th>
                                        <th>Debit Amount</th>
                                        <th>Month</th>
                                        <th>Year</th>
                                        <th>Statement File</th>
                                        <th>Date Added</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $months = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                                    $statement_no = 1;
                                    foreach ($bank_statements as $statement):
                                    ?>
                                    <tr>
                                        <td><?php echo $statement_no; ?></td>
                                        <td><?php echo $currency->currency_code; ?> <?php echo number_format($statement->credit, 2); ?></td>
                                        <td><?php echo $currency->currency_code; ?> <?php echo number_format($statement->debit, 2); ?></td>
                                        <td><?php echo $months[$statement->month]; ?></td>
                                        <td><?php echo $statement->year; ?></td>
                                        <td>
                                            <?php if($statement->file):
                                                $stmt_ext = strtolower(pathinfo($statement->file, PATHINFO_EXTENSION));
                                                $stmt_is_pdf = ($stmt_ext === 'pdf');
                                                $stmt_is_image = in_array($stmt_ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
                                                $stmt_is_previewable = $stmt_is_pdf || $stmt_is_image;
                                            ?>
                                            <div style="display: flex; gap: 0.5rem;">
                                                <?php if($stmt_is_previewable): ?>
                                                <button onclick="previewFile('<?php echo base_url('uploads/'.$loan_number.'/'.$statement->file); ?>', '<?php echo htmlspecialchars($statement->file); ?>', '<?php echo $stmt_is_pdf ? 'pdf' : 'image'; ?>')" class="btn-action btn-info" title="Preview">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <?php endif; ?>
                                                <a href="<?php echo base_url('uploads/'.$loan_number.'/'.$statement->file); ?>" target="_blank" class="btn-action btn-primary" title="Download">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                            </div>
                                            <?php else: ?>
                                            <span style="color: #9ca3af;">No file</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d M Y H:i', strtotime($statement->date_added)); ?></td>
                                    </tr>
                                    <?php $statement_no++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <div style="text-align: center; padding: 2rem; color: #6b7280;">
                            <i class="fa fa-university fa-3x" style="opacity: 0.3; margin-bottom: 1rem;"></i>
                            <p>No bank statements found for this loan</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<!-- Disburse Modal -->
<div class="modal fade" id="disburseModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border: none; border-radius: 12px; overflow: hidden;">
            <div class="modal-header" style="background: #059669; border: none; padding: 1rem 1.5rem;">
                <h5 class="modal-title" style="font-weight: 600; color: #fff;">
                    <i class="fa fa-money-bill-wave mr-2" style="color: #fff;"></i>Disburse Loan
                </h5>
                <button type="button" class="close" data-dismiss="modal" style="color: #fff; opacity: 0.8;">
                    <span>&times;</span>
                </button>
            </div>
            <form action="<?php echo base_url('loan/disburse_loan'); ?>" method="post">
                <div class="modal-body" style="padding: 1.5rem;">
                    <input type="hidden" name="loan_id" value="<?php echo $loan_id; ?>">

                    <div style="background: #ecfdf5; border: 1px solid #059669; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="color: #065f46;">Loan Number:</span>
                            <strong style="color: #065f46;"><?php echo $loan_number; ?></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span style="color: #065f46;">Customer:</span>
                            <strong style="color: #065f46;"><?php echo $customer_name ?? 'N/A'; ?></strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #065f46;">Amount:</span>
                            <strong style="color: #065f46; font-size: 1.1rem;"><?php echo $currency->currency_code ?? 'ZMW'; ?> <?php echo number_format($loan_principal, 2); ?></strong>
                        </div>
                    </div>

                    <div class="form-group">
                        <label style="font-weight: 600; color: #374151;">Disbursement Date</label>
                        <input type="date" name="cdate" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                        <small class="text-muted">Leave as today's date for standard disbursement, or change to backdate if needed.</small>
                    </div>

                    <div class="form-group">
                        <label style="font-weight: 600; color: #374151;">Comment (Optional)</label>
                        <textarea name="comment" class="form-control" rows="2" placeholder="Add any disbursement notes..."></textarea>
                    </div>

                    <div style="background: #fef3c7; border: 1px solid #fbbf24; border-radius: 8px; padding: 0.75rem; margin-top: 1rem;">
                        <div style="display: flex; align-items: flex-start; gap: 0.5rem; color: #92400e; font-size: 0.85rem;">
                            <i class="fa fa-exclamation-triangle" style="margin-top: 2px;"></i>
                            <span>This action will activate the loan and generate the repayment schedule. Please ensure all documents are verified.</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border: none; padding: 1rem 1.5rem; background: #f9fafb;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to disburse this loan? This action cannot be undone.')">
                        <i class="fa fa-check"></i> Confirm Disbursement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Notes Chat Modal -->
<div class="modal fade" id="notes_chat_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border: none; border-radius: 12px; overflow: hidden;">
            <div class="modal-header" style="background: #1e3a5f; border: none; padding: 1rem 1.5rem;">
                <h5 class="modal-title" style="font-weight: 600; color: #fff;">
                    <i class="fa fa-comments mr-2" style="color: #fff;"></i>Internal Notes - <?php echo $loan_number; ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" style="color: #fff; opacity: 0.8;">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 0; height: 450px; display: flex; flex-direction: column;">
                <!-- Chat Messages Container -->
                <div id="chat_messages_container" style="flex: 1; overflow-y: auto; padding: 1rem; background: #f3f4f6;">
                    <div style="text-align: center; padding: 2rem; color: #6b7280;">
                        <i class="fa fa-spinner fa-spin fa-2x"></i>
                        <p style="margin-top: 0.5rem;">Loading notes...</p>
                    </div>
                </div>

                <!-- Chat Input -->
                <div style="border-top: 1px solid #e5e7eb; padding: 1rem; background: #fff;">
                    <form id="chat_note_form" style="display: flex; gap: 0.75rem;">
                        <input type="hidden" name="loan_id" value="<?php echo $loan_id; ?>">
                        <textarea name="notes" id="chat_note_input" rows="1" placeholder="Type your note here..."
                            style="flex: 1; border: 1px solid #d1d5db; border-radius: 20px; padding: 0.6rem 1rem; font-size: 0.9rem; resize: none; outline: none; max-height: 100px;"
                            onkeydown="handleNoteKeydown(event)" required></textarea>
                        <button type="submit" id="send_note_btn" style="background: #1e3a5f; color: #fff; border: none; border-radius: 50%; width: 42px; height: 42px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: background 0.2s;">
                            <i class="fa fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Chat Bubble Styles */
.chat-bubble {
    max-width: 80%;
    margin-bottom: 1rem;
    animation: fadeIn 0.3s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.chat-bubble.own {
    margin-left: auto;
}
.chat-bubble.other {
    margin-right: auto;
}
.chat-bubble .bubble-content {
    padding: 0.75rem 1rem;
    border-radius: 18px;
    position: relative;
    word-wrap: break-word;
}
.chat-bubble.own .bubble-content {
    background: #1e3a5f;
    color: #fff;
    border-bottom-right-radius: 4px;
}
.chat-bubble.other .bubble-content {
    background: #fff;
    color: #374151;
    border: 1px solid #e5e7eb;
    border-bottom-left-radius: 4px;
}
.chat-bubble .bubble-meta {
    font-size: 0.7rem;
    color: #9ca3af;
    margin-top: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.chat-bubble.own .bubble-meta {
    justify-content: flex-end;
}
.chat-bubble .bubble-author {
    font-weight: 600;
    font-size: 0.75rem;
    margin-bottom: 0.25rem;
}
.chat-bubble.own .bubble-author {
    color: rgba(255,255,255,0.8);
}
.chat-bubble.other .bubble-author {
    color: #1e3a5f;
}
.chat-bubble .delete-note-btn {
    opacity: 0;
    transition: opacity 0.2s;
    background: none;
    border: none;
    color: #dc2626;
    cursor: pointer;
    padding: 0.2rem;
    font-size: 0.75rem;
}
.chat-bubble:hover .delete-note-btn {
    opacity: 1;
}
.chat-date-divider {
    text-align: center;
    margin: 1rem 0;
    position: relative;
}
.chat-date-divider span {
    background: #f3f4f6;
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
    color: #6b7280;
    border-radius: 10px;
}
#chat_note_input:focus {
    border-color: #1e3a5f;
    box-shadow: 0 0 0 2px rgba(30, 58, 95, 0.1);
}
#send_note_btn:hover {
    background: #153050;
}
#send_note_btn:disabled {
    background: #9ca3af;
    cursor: not-allowed;
}

/* Collateral Styles */
.collateral-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    transition: all 0.2s;
}
.collateral-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.collateral-status {
    display: inline-block;
    padding: 0.2rem 0.6rem;
    border-radius: 50px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
}
.collateral-status.active { background: #dcfce7; color: #166534; }
.collateral-status.returned { background: #dbeafe; color: #1e40af; }
.collateral-status.recovered { background: #fef3c7; color: #92400e; }
.collateral-status.released { background: #e0e7ff; color: #3730a3; }
.collateral-status.sold { background: #fee2e2; color: #991b1b; }
.collateral-status.damaged { background: #fecaca; color: #7f1d1d; }
.collateral-status.lost { background: #f3f4f6; color: #374151; }
</style>

<!-- Collateral List Modal -->
<div class="modal fade" id="collateral_list_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" style="border: none; border-radius: 12px; overflow: hidden;">
            <div class="modal-header" style="background: #1e3a5f; border: none; padding: 1rem 1.5rem;">
                <h5 class="modal-title" style="font-weight: 600; color: #fff;">
                    <i class="fa fa-shield-alt mr-2" style="color: #fff;"></i>Linked Collaterals - <?php echo $loan_number; ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" style="color: #fff; opacity: 0.8;">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 1.5rem; max-height: 70vh; overflow-y: auto;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div id="collateral_stats" style="font-size: 0.85rem; color: #6b7280;"></div>
                    <button onclick="openLinkCollateralModal()" class="btn-action btn-success">
                        <i class="fa fa-link"></i> Link Collateral
                    </button>
                </div>
                <div id="collateral_list_container">
                    <div style="text-align: center; padding: 2rem; color: #6b7280;">
                        <i class="fa fa-spinner fa-spin fa-2x"></i>
                        <p style="margin-top: 0.5rem;">Loading collaterals...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Link Collateral Modal -->
<div class="modal fade modern-modal" id="link_collateral_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #059669;">
                <h5 class="modal-title" style="color: #fff;"><i class="fa fa-link mr-2"></i>Link Collateral to Loan</h5>
                <button type="button" class="close" data-dismiss="modal" style="color: #fff;"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> Select a collateral from this customer's registered collaterals and specify the amount to utilize.
                </div>

                <div id="available_collaterals_container">
                    <div style="text-align: center; padding: 2rem; color: #6b7280;">
                        <i class="fa fa-spinner fa-spin fa-2x"></i>
                        <p style="margin-top: 0.5rem;">Loading customer collaterals...</p>
                    </div>
                </div>

                <form id="link_collateral_form" style="display: none;">
                    <input type="hidden" name="loan_id" value="<?php echo $loan_id; ?>">
                    <input type="hidden" name="collateral_id" id="link_collateral_id">

                    <div class="selected-collateral-info" style="background: #f0f9ff; padding: 1rem; border-radius: 8px; margin-bottom: 1rem;">
                        <h6 style="margin: 0 0 0.5rem 0; color: #1e3a5f;"><i class="fa fa-shield-alt"></i> Selected Collateral</h6>
                        <div id="selected_collateral_details"></div>
                    </div>

                    <div class="form-group">
                        <label>Amount to Utilize (<?php echo $currency->currency_code; ?>) <span style="color: #dc2626;">*</span></label>
                        <input type="number" step="0.01" class="form-control" name="amount_utilized" id="amount_to_utilize" required placeholder="0.00">
                        <small class="text-muted">Max available: <span id="max_available_display">0.00</span></small>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <button type="submit" class="btn-action btn-success" style="width: 100%; justify-content: center; padding: 0.75rem;">
                                <i class="fa fa-link"></i> Link Collateral
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn-action btn-secondary" onclick="cancelLinkCollateral()" style="width: 100%; justify-content: center; padding: 0.75rem;">
                                <i class="fa fa-times"></i> Cancel
                            </button>
                        </div>
                    </div>
                </form>

                <div id="no_collaterals_message" style="display: none; text-align: center; padding: 2rem;">
                    <i class="fa fa-exclamation-circle fa-3x" style="color: #f59e0b; margin-bottom: 1rem;"></i>
                    <p>No collaterals registered for this customer.</p>
                    <p class="text-muted">Please add collaterals from the customer's profile page first.</p>
                    <a href="<?php echo base_url($customer_type == 'institution' ? 'corporate_customers/read/' : 'individual_customers/view/') . $cust_id; ?>" class="btn-action btn-primary">
                        <i class="fa fa-user"></i> Go to Customer Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Collateral Modal (Creates collateral for customer) -->
<div class="modal fade modern-modal" id="add_collateral_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #059669;">
                <h5 class="modal-title" style="color: #fff;"><i class="fa fa-plus-circle mr-2"></i>Add New Collateral for Customer</h5>
                <button type="button" class="close" data-dismiss="modal" style="color: #fff;"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3">
                    <i class="fa fa-info-circle"></i> This will add a new collateral to the customer's profile. After saving, you can link it to this loan.
                </div>
                <form id="add_collateral_form" enctype="multipart/form-data">
                    <input type="hidden" name="customer_id" value="<?php echo $cust_id; ?>">
                    <input type="hidden" name="customer_type" value="<?php echo $customer_type == 'institution' ? 'institution' : 'individual'; ?>">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Collateral Name <span style="color: #dc2626;">*</span></label>
                                <input type="text" class="form-control" name="collateral_name" required placeholder="e.g., Toyota Hilux 2020">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Collateral Type <span style="color: #dc2626;">*</span></label>
                                <select class="form-control" name="collateral_type" required>
                                    <option value="">-- Select Type --</option>
                                    <option value="Real Estate">Real Estate</option>
                                    <option value="Vehicle">Vehicle</option>
                                    <option value="Equipment/Machinery">Equipment/Machinery</option>
                                    <option value="Inventory/Stock">Inventory/Stock</option>
                                    <option value="Cash Deposit">Cash Deposit</option>
                                    <option value="Securities/Bonds">Securities/Bonds</option>
                                    <option value="Accounts Receivable">Accounts Receivable</option>
                                    <option value="Personal Guarantee">Personal Guarantee</option>
                                    <option value="Corporate Guarantee">Corporate Guarantee</option>
                                    <option value="Life Insurance Policy">Life Insurance Policy</option>
                                    <option value="Fixed Deposit">Fixed Deposit</option>
                                    <option value="Gold/Precious Metals">Gold/Precious Metals</option>
                                    <option value="Intellectual Property">Intellectual Property</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Serial/Registration Number</label>
                                <input type="text" class="form-control" name="collateral_serial" placeholder="e.g., ABC 1234 ZM">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Market Value (<?php echo $currency->currency_code; ?>) <span style="color: #dc2626;">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="market_value" required placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Force Sale Value (<?php echo $currency->currency_code; ?>) <span style="color: #dc2626;">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="force_sale_value" required placeholder="0.00">
                                <small class="text-muted">Quick sale/distressed value (usually 70-80% of market)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" name="description" rows="2" placeholder="Additional details..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <button type="submit" class="btn-action btn-success" style="width: 100%; justify-content: center; padding: 0.75rem;">
                                <i class="fa fa-save"></i> Save Collateral
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn-action btn-secondary" data-dismiss="modal" style="width: 100%; justify-content: center; padding: 0.75rem;">
                                <i class="fa fa-times"></i> Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Update Collateral Status Modal -->
<div class="modal fade modern-modal" id="update_collateral_status_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #f59e0b;">
                <h5 class="modal-title" style="color: #fff;"><i class="fa fa-exchange-alt mr-2"></i>Update Collateral Status</h5>
                <button type="button" class="close" data-dismiss="modal" style="color: #fff;"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="update_status_form">
                    <input type="hidden" name="collateral_id" id="status_collateral_id">

                    <div class="form-group">
                        <label>Current Status</label>
                        <input type="text" class="form-control" id="current_status_display" readonly>
                    </div>

                    <div class="form-group">
                        <label>New Status <span style="color: #dc2626;">*</span></label>
                        <select class="form-control" name="status" id="new_status_select" required>
                            <option value="">-- Select Status --</option>
                            <option value="ACTIVE">Active - Currently held as security</option>
                            <option value="RETURNED">Returned - Released back to borrower</option>
                            <option value="RECOVERED">Recovered - Repossessed due to default</option>
                            <option value="RELEASED">Released - Loan fully paid</option>
                            <option value="SOLD">Sold - Disposed to recover debt</option>
                            <option value="DAMAGED">Damaged - Asset damaged/depreciated</option>
                            <option value="LOST">Lost - Asset lost/missing</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Remarks</label>
                        <textarea class="form-control" name="remarks" rows="3" placeholder="Reason for status change..."></textarea>
                    </div>

                    <button type="submit" class="btn-action btn-warning" style="width: 100%; justify-content: center; padding: 0.75rem;">
                        <i class="fa fa-check"></i> Update Status
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Collateral Details Modal -->
<div class="modal fade" id="view_collateral_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border: none; border-radius: 12px; overflow: hidden;">
            <div class="modal-header" style="background: #1e3a5f; border: none; padding: 1rem 1.5rem;">
                <h5 class="modal-title" style="font-weight: 600; color: #fff;">
                    <i class="fa fa-eye mr-2"></i>Collateral Details
                </h5>
                <button type="button" class="close" data-dismiss="modal" style="color: #fff; opacity: 0.8;">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 1.5rem;">
                <div id="collateral_details_content">
                    <!-- Content loaded dynamically -->
                </div>
                <hr>
                <h6 style="color: #1e3a5f; font-weight: 600;"><i class="fa fa-history"></i> Status History</h6>
                <div id="collateral_history_content">
                    <!-- History loaded dynamically -->
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
                <h5 class="modal-title"><i class="fa fa-money-bill-wave mr-2"></i>Make Payment</h5>
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
                        <div class="value" id="payment_modal_amount_display" style="color: #059669;"><?php echo $currency->currency_code; ?> <?php echo !empty($next_payment_details) ? number_format($next_payment_details->amount, 2) : '0.00'; ?></div>
                    </div>
                </div>

                <?php if($calculation_type == 'Bullet Payment'): ?>
                <!-- Bullet Loan Calculation Breakdown -->
                <div id="bullet_calculation_breakdown" style="display: none; background: #fefce8; border: 1px solid #fbbf24; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                    <div style="font-weight: 600; color: #92400e; margin-bottom: 0.5rem;">
                        <i class="fa fa-calculator"></i> Bullet Loan Interest Calculation
                    </div>
                    <div id="bullet_breakdown_content" style="font-size: 0.85rem; color: #78350f;"></div>
                </div>
                <?php endif; ?>

                <form action="<?php echo base_url('loan/pay_loan')?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="loan_id" value="<?php echo $loan_id?>">
                    <input type="hidden" name="payment_number" value="<?php echo $next_payment_id ?>">

                    <div class="form-group">
                        <label>Payment Date <span style="color: #dc2626;">*</span></label>
                        <input type="date" class="form-control" id="payment_date_input" name="paid_date" required>
                        <?php if($calculation_type == 'Bullet Payment'): ?>
                        <small style="color: #6b7280;">Amount will be calculated based on this date (daily accrual)</small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Payment Amount (<?php echo $currency->currency_code; ?>)</label>
                        <input type="text" class="form-control" id="payment_amount_input" name="amount" value="<?php echo !empty($next_payment_details) ? $next_payment_details->amount : '0'; ?>" <?php echo ($calculation_type == 'Bullet Payment') ? '' : 'readonly'; ?> required>
                        <div id="payment_calculating" style="display: none; color: #3b82f6; font-size: 0.85rem; margin-top: 0.25rem;">
                            <i class="fa fa-spinner fa-spin"></i> Calculating amount...
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Proof of Payment</label>
                        <input type="file" class="form-control" name="proof" onchange="uploadcommon('id_front')">
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

                <form action="<?php echo base_url('loan/pay_advance')?>" method="POST">
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
                        <div class="label">Late Payment Charge</div>
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
                        <input type="file" class="form-control" name="file">
                    </div>

                    <button type="submit" class="btn-action btn-danger" style="width: 100%; justify-content: center; padding: 0.75rem;">
                        <i class="fa fa-check"></i> Submit Late Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Approval Comment Modal -->
<div class="modal fade modern-modal" id="approval_comment_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approval_modal_title"><i class="fa fa-comment-alt mr-2"></i>Loan Action</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p id="approval_modal_message" style="color: #374151; margin-bottom: 1.5rem;">Please provide a comment for this action:</p>

                <form action="<?php echo base_url('loan/approval_action_with_comment')?>" method="POST">
                    <input type="hidden" name="loan_id" id="approval_loan_id" value="">
                    <input type="hidden" name="action" id="approval_action" value="">

                    <div class="form-group">
                        <label>Comment <span style="color: #dc2626;">*</span></label>
                        <textarea class="form-control" name="comment" id="approval_comment" rows="5" placeholder="Enter your comment here..." required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <button type="submit" class="btn-action btn-success" style="width: 100%; justify-content: center; padding: 0.75rem;">
                                <i class="fa fa-check"></i> Submit
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn-action btn-secondary" data-dismiss="modal" style="width: 100%; justify-content: center; padding: 0.75rem;">
                                <i class="fa fa-times"></i> Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Approval Trail Modal -->
<div class="modal fade modern-modal" id="approval_trail_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background: #0891b2;">
                <h5 class="modal-title"><i class="fa fa-history mr-2"></i>Approval Trail History</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
                <div id="approval_trail_content">
                    <!-- Content loaded via AJAX -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-action btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Multi-Level Approval Modal -->
<div class="modal fade modern-modal" id="multi_approval_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header" id="multi_approval_modal_header" style="background: #059669; color: #fff;">
                <h5 class="modal-title" id="multi_approval_modal_title"><i class="fa fa-check-circle mr-2"></i>Approve Loan</h5>
                <button type="button" class="close" data-dismiss="modal" style="color: #fff; opacity: 1;">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="multi_approval_level_badge" style="text-align: center; margin-bottom: 1rem;">
                    <span style="background: #dbeafe; color: #1e40af; padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600;">
                        Approval Level: <span id="multi_approval_level_number">1</span> of 3
                    </span>
                </div>

                <form action="<?php echo base_url('loan/multi_approval_action'); ?>" method="POST" id="multi_approval_form">
                    <input type="hidden" name="loan_id" id="multi_modal_loan_id" value="">
                    <input type="hidden" name="action" id="multi_modal_action" value="">
                    <input type="hidden" name="approval_level" id="multi_modal_approval_level" value="">

                    <div class="form-group">
                        <label><strong>Comment</strong> <span style="color: #dc2626;">*</span></label>
                        <textarea class="form-control" name="comment" id="multi_modal_comment" rows="4"
                            placeholder="Enter your comment or reason for this action..." required
                            style="border-radius: 8px;"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <button type="submit" class="btn btn-success" style="width: 100%; border-radius: 8px;">
                                <i class="fa fa-check"></i> Confirm
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" style="width: 100%; border-radius: 8px;">
                                <i class="fa fa-times"></i> Cancel
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Send Back Modal -->
<div class="modal fade modern-modal" id="send_back_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff;">
                <h5 class="modal-title"><i class="fa fa-undo mr-2"></i>Send Back Loan</h5>
                <button type="button" class="close" data-dismiss="modal" style="color: #fff; opacity: 1;">
                    <span>&times;</span>
                </button>
            </div>
            <form action="<?php echo base_url('loan/send_back'); ?>" method="post">
                <div class="modal-body" style="padding: 1.5rem;">
                    <input type="hidden" name="loan_id" id="send_back_loan_id" value="">

                    <div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <i class="fa fa-exclamation-triangle" style="color: #92400e; font-size: 1.25rem;"></i>
                            <div>
                                <strong style="color: #92400e;">Send Back for Corrections</strong>
                                <p style="margin: 0.25rem 0 0 0; color: #78350f; font-size: 0.85rem;">
                                    This will return the loan to the previous stage for corrections. The responsible person will be notified.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="send_back_comment" style="font-weight: 600; color: #374151;">
                            <i class="fa fa-comment"></i> Reason for Sending Back <span style="color: #dc2626;">*</span>
                        </label>
                        <textarea name="comment" id="send_back_comment" class="form-control" rows="4"
                            placeholder="Please explain why this loan is being sent back and what corrections are needed..."
                            style="border-radius: 8px; border: 1px solid #d1d5db;" required></textarea>
                        <small class="text-muted">This comment will be visible to the person who needs to make corrections.</small>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e5e7eb; padding: 1rem 1.5rem; background: #f9fafb;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 6px;">
                        <i class="fa fa-times"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-warning" style="border-radius: 6px; background: #f59e0b; border-color: #f59e0b;">
                        <i class="fa fa-undo"></i> Return for Edit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- File Preview Modal -->
<div class="modal fade" id="file_preview_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" style="border: none; border-radius: 12px; overflow: hidden;">
            <div class="modal-header" style="background: #1e3a5f; border: none; padding: 1rem 1.5rem;">
                <h5 class="modal-title" style="font-weight: 600; color: #fff;">
                    <i class="fa fa-eye mr-2"></i><span id="preview_file_name">File Preview</span>
                </h5>
                <div>
                    <a id="preview_download_btn" href="#" download class="btn btn-sm btn-success mr-2" style="color: #fff;">
                        <i class="fa fa-download"></i> Download
                    </a>
                    <button type="button" class="close" data-dismiss="modal" style="color: #fff; opacity: 0.8; font-size: 1.5rem;">
                        <span>&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body" style="padding: 0; background: #f3f4f6; min-height: 70vh; display: flex; align-items: center; justify-content: center;">
                <!-- PDF Preview -->
                <div id="pdf_preview_container" style="width: 100%; height: 80vh; display: none;">
                    <iframe id="pdf_preview_frame" src="" style="width: 100%; height: 100%; border: none;"></iframe>
                </div>
                <!-- Image Preview -->
                <div id="image_preview_container" style="padding: 1rem; text-align: center; display: none; max-height: 80vh; overflow: auto;">
                    <img id="image_preview" src="" style="max-width: 100%; max-height: 75vh; border-radius: 8px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
                </div>
                <!-- Loading -->
                <div id="preview_loading" style="text-align: center; padding: 3rem;">
                    <i class="fa fa-spinner fa-spin fa-3x" style="color: #1e3a5f;"></i>
                    <p style="margin-top: 1rem; color: #6b7280;">Loading preview...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<script>
console.log('Loan view script started');

// File Preview Function
function previewFile(fileUrl, fileName, fileType) {
    // Show modal
    $('#file_preview_modal').modal('show');

    // Set file name and download link
    document.getElementById('preview_file_name').textContent = fileName;
    document.getElementById('preview_download_btn').href = fileUrl;

    // Hide all containers, show loading
    document.getElementById('pdf_preview_container').style.display = 'none';
    document.getElementById('image_preview_container').style.display = 'none';
    document.getElementById('preview_loading').style.display = 'block';

    // Clear previous content
    document.getElementById('pdf_preview_frame').src = '';
    document.getElementById('image_preview').src = '';

    setTimeout(function() {
        document.getElementById('preview_loading').style.display = 'none';

        if (fileType === 'pdf') {
            document.getElementById('pdf_preview_container').style.display = 'block';
            document.getElementById('pdf_preview_frame').src = fileUrl;
        } else if (fileType === 'image') {
            document.getElementById('image_preview_container').style.display = 'block';
            document.getElementById('image_preview').src = fileUrl;
        }
    }, 300);
}

// Clear preview when modal is closed
$('#file_preview_modal').on('hidden.bs.modal', function () {
    document.getElementById('pdf_preview_frame').src = '';
    document.getElementById('image_preview').src = '';
});

// Send Back Modal Function
function openSendBackModal(loanId) {
    document.getElementById('send_back_loan_id').value = loanId;
    document.getElementById('send_back_comment').value = '';
    $('#send_back_modal').modal('show');
}

// Currency code for JavaScript use
var currencyCode = '<?php echo isset($currency) && $currency ? $currency->currency_code : "ZMW"; ?>';
var loanId = <?php echo $loan_id; ?>;

// ==================== MULTI-LEVEL APPROVAL ====================
function openMultiApprovalModal(action, loanId, level) {
    document.getElementById('multi_modal_loan_id').value = loanId;
    document.getElementById('multi_modal_action').value = action;
    document.getElementById('multi_modal_approval_level').value = level;
    document.getElementById('multi_modal_comment').value = '';

    var header = document.getElementById('multi_approval_modal_header');
    var title = document.getElementById('multi_approval_modal_title');
    var levelBadge = document.getElementById('multi_approval_level_badge');

    if(action === 'REJECT') {
        header.style.background = '#dc2626';
        title.innerHTML = '<i class="fa fa-times-circle mr-2"></i>Reject Loan';
        levelBadge.style.display = 'none';
    } else {
        header.style.background = '#059669';
        title.innerHTML = '<i class="fa fa-check-circle mr-2"></i>Approve Loan';
        levelBadge.style.display = 'block';
        document.getElementById('multi_approval_level_number').textContent = level;
    }

    $('#multi_approval_modal').modal('show');
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

// File upload with progress
// Delete file function
function deleteFile(fileId, btn) {
    if (!confirm('Are you sure you want to delete this file?')) {
        return;
    }

    var fileItem = btn.closest('.file-item');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '<?php echo base_url("loan/delete_file"); ?>', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    fileItem.style.transition = 'opacity 0.3s';
                    fileItem.style.opacity = '0';
                    setTimeout(function() {
                        fileItem.remove();
                        // Check if no files left
                        var fileList = document.getElementById('file_list');
                        if (fileList.querySelectorAll('.file-item').length === 0) {
                            fileList.innerHTML = '<div id="no_files_msg" style="text-align: center; padding: 1rem; color: #6b7280;"><p>No files uploaded yet</p></div>';
                        }
                    }, 300);
                } else {
                    alert('Error: ' + response.message);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa fa-trash"></i>';
                }
            } catch (e) {
                alert('Error deleting file');
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-trash"></i>';
            }
        } else {
            alert('Error deleting file');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-trash"></i>';
        }
    };

    xhr.onerror = function() {
        alert('Error deleting file');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-trash"></i>';
    };

    xhr.send('file_id=' + fileId);
}

// File upload with progress
function uploadLoanFile(input) {
    if (input.files.length === 0) return;

    var formData = new FormData();
    for (var i = 0; i < input.files.length; i++) {
        formData.append('files[]', input.files[i]);
    }
    formData.append('loan_id', '<?php echo $loan_id; ?>');
    formData.append('loan_number', '<?php echo $loan_number; ?>');

    var progressDiv = document.getElementById('upload_progress');
    var progressBar = document.getElementById('progress_bar');
    var progressText = document.getElementById('progress_text');

    progressDiv.style.display = 'block';

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '<?php echo base_url("loan/upload_files"); ?>', true);

    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            var percent = Math.round((e.loaded / e.total) * 100);
            progressBar.style.width = percent + '%';
            progressText.textContent = percent + '%';
        }
    });

    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    progressText.textContent = 'Upload complete!';
                    progressBar.style.background = '#059669';

                    // Add uploaded files to list
                    var fileList = document.getElementById('file_list');
                    var noFilesMsg = document.getElementById('no_files_msg');
                    if (noFilesMsg) noFilesMsg.remove();

                    response.files.forEach(function(file) {
                        var fileItem = document.createElement('div');
                        fileItem.className = 'file-item';
                        fileItem.setAttribute('data-file-id', file.id);

                        // Check if file is previewable
                        var ext = file.name.split('.').pop().toLowerCase();
                        var isPdf = (ext === 'pdf');
                        var isImage = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].indexOf(ext) !== -1;
                        var isPreviewable = isPdf || isImage;
                        var fileType = isPdf ? 'pdf' : 'image';
                        var fileUrl = '<?php echo base_url('uploads/'); ?>' + file.path;

                        // Set file icon
                        var fileIcon = 'fa-file';
                        if (isPdf) fileIcon = 'fa-file-pdf-o';
                        else if (isImage) fileIcon = 'fa-file-image-o';
                        else if (['doc', 'docx'].indexOf(ext) !== -1) fileIcon = 'fa-file-word-o';
                        else if (['xls', 'xlsx'].indexOf(ext) !== -1) fileIcon = 'fa-file-excel-o';

                        var previewBtn = isPreviewable ?
                            `<button onclick="previewFile('${fileUrl}', '${file.name}', '${fileType}')" class="btn-action btn-info" title="Preview">
                                <i class="fa fa-eye"></i>
                            </button>` : '';

                        fileItem.innerHTML = `
                            <div class="file-info">
                                <div class="file-icon"><i class="fa ${fileIcon}"></i></div>
                                <div class="file-name">${file.name}</div>
                            </div>
                            <div class="file-actions">
                                ${previewBtn}
                                <a href="${fileUrl}" download class="btn-action btn-primary" title="Download">
                                    <i class="fa fa-download"></i>
                                </a>
                                <button onclick="deleteFile(${file.id}, this)" class="btn-action btn-danger" title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        `;
                        fileList.appendChild(fileItem);
                    });

                    setTimeout(function() {
                        progressDiv.style.display = 'none';
                        progressBar.style.width = '0%';
                        progressBar.style.background = '#059669';
                    }, 2000);
                } else {
                    progressText.textContent = 'Upload failed: ' + response.message;
                    progressBar.style.background = '#dc2626';
                }
            } catch (e) {
                progressText.textContent = 'Upload failed';
                progressBar.style.background = '#dc2626';
            }
        } else {
            progressText.textContent = 'Upload failed';
            progressBar.style.background = '#dc2626';
        }
    };

    xhr.onerror = function() {
        progressText.textContent = 'Upload failed';
        progressBar.style.background = '#dc2626';
    };

    xhr.send(formData);
}

// Open approval modal
function openApprovalModal(action, loanId) {
    document.getElementById('approval_loan_id').value = loanId;
    document.getElementById('approval_action').value = action;
    document.getElementById('approval_comment').value = '';

    var title = '';
    var message = '';

    switch(action) {
        case 'APPROVED_FIRST':
            title = 'Approve Loan (First Level)';
            message = 'Please provide your comments for first level approval:';
            break;
        case 'APPROVED_SECOND':
            title = 'Approve Loan (Second Level)';
            message = 'Please provide your comments for second level approval:';
            break;
        case 'APPROVED':
            title = 'Final Approve Loan (Third Level)';
            message = 'Please provide your comments for final approval of this loan:';
            break;
        case 'RECOMMENDED':
            title = 'Recommend Loan';
            message = 'Please provide your comments for recommending this loan:';
            break;
        case 'REJECT':
            title = 'Reject Loan';
            message = 'Please provide the reason for rejecting this loan:';
            break;
        default:
            title = 'Loan Action';
            message = 'Please provide a comment for this action:';
    }

    document.getElementById('approval_modal_title').innerHTML = '<i class="fa fa-comment-alt mr-2"></i>' + title;
    document.getElementById('approval_modal_message').innerText = message;

    $('#approval_comment_modal').modal('show');
}

// View approval trail
function viewApprovalTrail(loanId) {
    document.getElementById('approval_trail_content').innerHTML = '<div style="text-align: center; padding: 2rem;"><i class="fa fa-spinner fa-spin fa-3x" style="color: #0891b2;"></i><p style="margin-top: 1rem; color: #6b7280;">Loading approval trail...</p></div>';

    $('#approval_trail_modal').modal('show');

    $.ajax({
        url: '<?php echo base_url("loan/get_approval_trail/"); ?>' + loanId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.status == 'success') {
                displayApprovalTrail(response.data);
            } else {
                document.getElementById('approval_trail_content').innerHTML = '<div style="text-align: center; padding: 2rem; background: #fef3c7; border-radius: 8px; color: #92400e;"><i class="fa fa-exclamation-triangle fa-2x"></i><p style="margin-top: 0.5rem;">No approval trail found for this loan.</p></div>';
            }
        },
        error: function() {
            document.getElementById('approval_trail_content').innerHTML = '<div style="text-align: center; padding: 2rem; background: #fee2e2; border-radius: 8px; color: #991b1b;"><i class="fa fa-times-circle fa-2x"></i><p style="margin-top: 0.5rem;">Error loading approval trail. Please try again.</p></div>';
        }
    });
}

function displayApprovalTrail(trailData) {
    var html = '<div class="approval-timeline">';

    if(trailData.length === 0) {
        html = '<div style="text-align: center; padding: 2rem; background: #eff6ff; border-radius: 8px; color: #1e40af;"><i class="fa fa-info-circle fa-2x"></i><p style="margin-top: 0.5rem;">No approval actions recorded yet.</p></div>';
    } else {
        trailData.forEach(function(item) {
            var iconClass = 'fa-circle';
            var colorClass = '#3b82f6';

            switch(item.action) {
                case 'INITIATED':
                    iconClass = 'fa-plus-circle';
                    colorClass = '#0891b2';
                    break;
                case 'RECOMMENDED':
                    iconClass = 'fa-thumbs-up';
                    colorClass = '#f59e0b';
                    break;
                case 'APPROVED_FIRST':
                case 'APPROVED_SECOND':
                    iconClass = 'fa-check-circle';
                    colorClass = '#3b82f6';
                    break;
                case 'APPROVED':
                    iconClass = 'fa-check-circle';
                    colorClass = '#059669';
                    break;
                case 'REJECT':
                case 'REJECTED':
                    iconClass = 'fa-times-circle';
                    colorClass = '#dc2626';
                    break;
                case 'DISBURSED':
                    iconClass = 'fa-money-bill-wave';
                    colorClass = '#059669';
                    break;
            }

            html += `
                <div class="timeline-item">
                    <div class="timeline-icon" style="background: ${colorClass};">
                        <i class="fa ${iconClass}"></i>
                    </div>
                    <div class="timeline-content">
                        <h6><span class="status-badge" style="background: ${colorClass}20; color: ${colorClass};">${item.action}</span></h6>
                        <p><strong>User:</strong> ${item.user_name || 'N/A'}</p>
                        <p><strong>Comment:</strong> ${item.comment || 'No comment provided'}</p>
                        <div class="timeline-meta"><i class="fa fa-clock"></i> ${item.date_stamp}</div>
                    </div>
                </div>
            `;
        });
    }

    html += '</div>';
    document.getElementById('approval_trail_content').innerHTML = html;
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

// Payment modal functions (legacy support)
function pay_current() {
    $('#payment_modal').modal('show');
}

function advance_payment() {
    $('#advance_payment_modal').modal('show');
}

// Notes functionality - Chat Style
var currentUserId = '<?php echo $this->session->userdata('user_id'); ?>';
var currentUserName = '<?php $user = get_by_id("employees", "id", $this->session->userdata("user_id")); echo $user ? addslashes($user->Firstname . " " . $user->Lastname) : "Unknown"; ?>';

function loadNotesCount() {
    $.ajax({
        url: '<?php echo base_url("loan/get_notes/"); ?><?php echo $loan_id; ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.status == 'success' && response.count > 0) {
                var badge = document.getElementById('notes_count_badge');
                badge.textContent = response.count;
                badge.style.display = 'inline';
            }
        }
    });
}

function openNotesModal() {
    $('#notes_chat_modal').modal('show');
    loadChatNotes();
}

function loadChatNotes() {
    var container = document.getElementById('chat_messages_container');

    $.ajax({
        url: '<?php echo base_url("loan/get_notes/"); ?><?php echo $loan_id; ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.status == 'success') {
                displayChatNotes(response.data);
                updateNotesCount(response.count);
            } else {
                container.innerHTML = '<div style="text-align: center; padding: 3rem; color: #6b7280;"><i class="fa fa-comments fa-3x" style="opacity: 0.3; margin-bottom: 1rem;"></i><p>No notes yet</p><p style="font-size: 0.85rem;">Start the conversation by typing a note below</p></div>';
            }
        },
        error: function() {
            container.innerHTML = '<div style="text-align: center; padding: 2rem; color: #dc2626;"><i class="fa fa-exclamation-circle fa-2x"></i><p style="margin-top: 0.5rem;">Error loading notes</p></div>';
        }
    });
}

function displayChatNotes(notes) {
    var container = document.getElementById('chat_messages_container');

    if(notes.length === 0) {
        container.innerHTML = '<div style="text-align: center; padding: 3rem; color: #6b7280;"><i class="fa fa-comments fa-3x" style="opacity: 0.3; margin-bottom: 1rem;"></i><p>No notes yet</p><p style="font-size: 0.85rem;">Start the conversation by typing a note below</p></div>';
        return;
    }

    // Reverse to show oldest first (chat style)
    var sortedNotes = notes.slice().reverse();
    var html = '';
    var lastDate = '';

    sortedNotes.forEach(function(note) {
        var noteDate = note.datetime.split(' ').slice(0, 3).join(' ');

        // Add date divider if new date
        if(noteDate !== lastDate) {
            html += '<div class="chat-date-divider"><span>' + noteDate + '</span></div>';
            lastDate = noteDate;
        }

        var isOwn = (note.user_id == currentUserId);
        var bubbleClass = isOwn ? 'own' : 'other';
        var time = note.datetime.split(' ').slice(3).join(' ');

        html += `
            <div class="chat-bubble ${bubbleClass}" data-note-id="${note.note_id}">
                <div class="bubble-content">
                    ${!isOwn ? '<div class="bubble-author">' + escapeHtml(note.notes_by) + '</div>' : ''}
                    <div style="white-space: pre-wrap;">${escapeHtml(note.notes)}</div>
                </div>
                <div class="bubble-meta">
                    ${isOwn ? '<button class="delete-note-btn" onclick="deleteChatNote(' + note.note_id + ', this)" title="Delete"><i class="fa fa-trash"></i></button>' : ''}
                    <span>${time}</span>
                    ${!isOwn ? '' : '<span style="color: #1e3a5f; font-weight: 500;">You</span>'}
                </div>
            </div>
        `;
    });

    container.innerHTML = html;

    // Scroll to bottom
    setTimeout(function() {
        container.scrollTop = container.scrollHeight;
    }, 100);
}

function updateNotesCount(count) {
    var badge = document.getElementById('notes_count_badge');
    if(count > 0) {
        badge.textContent = count;
        badge.style.display = 'inline';
    } else {
        badge.style.display = 'none';
    }
}

function escapeHtml(text) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}

function handleNoteKeydown(e) {
    if(e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        document.getElementById('chat_note_form').dispatchEvent(new Event('submit'));
    }
}

// Handle chat note form submission
var chatNoteForm = document.getElementById('chat_note_form');
if (chatNoteForm) chatNoteForm.addEventListener('submit', function(e) {
    e.preventDefault();

    var input = document.getElementById('chat_note_input');
    var submitBtn = document.getElementById('send_note_btn');
    var noteText = input.value.trim();

    if(!noteText) return;

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

    $.ajax({
        url: '<?php echo base_url("loan/add_note"); ?>',
        type: 'POST',
        data: {
            loan_id: '<?php echo $loan_id; ?>',
            notes: noteText
        },
        dataType: 'json',
        success: function(response) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa fa-paper-plane"></i>';

            if(response.status == 'success') {
                input.value = '';
                input.style.height = 'auto';

                // Add the new note to chat immediately
                var container = document.getElementById('chat_messages_container');
                var emptyMsg = container.querySelector('div[style*="text-align: center"]');
                if(emptyMsg) {
                    container.innerHTML = '';
                }

                var newBubble = document.createElement('div');
                newBubble.className = 'chat-bubble own';
                newBubble.setAttribute('data-note-id', response.note.note_id);
                newBubble.innerHTML = `
                    <div class="bubble-content">
                        <div style="white-space: pre-wrap;">${escapeHtml(response.note.notes)}</div>
                    </div>
                    <div class="bubble-meta">
                        <button class="delete-note-btn" onclick="deleteChatNote(${response.note.note_id}, this)" title="Delete"><i class="fa fa-trash"></i></button>
                        <span>${response.note.datetime.split(' ').slice(3).join(' ')}</span>
                        <span style="color: #1e3a5f; font-weight: 500;">You</span>
                    </div>
                `;
                container.appendChild(newBubble);
                container.scrollTop = container.scrollHeight;

                // Update badge count
                var badge = document.getElementById('notes_count_badge');
                var currentCount = parseInt(badge.textContent) || 0;
                badge.textContent = currentCount + 1;
                badge.style.display = 'inline';
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fa fa-paper-plane"></i>';
            alert('Error adding note. Please try again.');
        }
    });
});

function deleteChatNote(noteId, btn) {
    if (!confirm('Delete this note?')) {
        return;
    }

    var bubble = btn.closest('.chat-bubble');

    $.ajax({
        url: '<?php echo base_url("loan/delete_note"); ?>',
        type: 'POST',
        data: { note_id: noteId },
        dataType: 'json',
        success: function(response) {
            if(response.status == 'success') {
                bubble.style.transition = 'all 0.3s';
                bubble.style.opacity = '0';
                bubble.style.transform = 'scale(0.8)';
                setTimeout(function() {
                    bubble.remove();

                    // Update badge count
                    var badge = document.getElementById('notes_count_badge');
                    var currentCount = parseInt(badge.textContent) || 0;
                    if(currentCount > 1) {
                        badge.textContent = currentCount - 1;
                    } else {
                        badge.style.display = 'none';
                        // Show empty message
                        var container = document.getElementById('chat_messages_container');
                        if(container.querySelectorAll('.chat-bubble').length === 0) {
                            container.innerHTML = '<div style="text-align: center; padding: 3rem; color: #6b7280;"><i class="fa fa-comments fa-3x" style="opacity: 0.3; margin-bottom: 1rem;"></i><p>No notes yet</p><p style="font-size: 0.85rem;">Start the conversation by typing a note below</p></div>';
                        }
                    }
                }, 300);
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('Error deleting note');
        }
    });
}

// Auto-resize textarea (with null check)
var chatNoteInput = document.getElementById('chat_note_input');
if (chatNoteInput) {
    chatNoteInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 100) + 'px';
    });
}

// ==================== COLLATERAL MANAGEMENT (Customer-Centric) ====================

var customerCollaterals = [];
var linkedCollaterals = [];

function loadCollateralSummary() {
    console.log('Loading collateral summary for loan <?php echo $loan_id; ?>');
    $.ajax({
        url: '<?php echo base_url("loan/get_loan_collaterals/"); ?><?php echo $loan_id; ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Collateral summary response:', response);
            if(response.success && response.collaterals && response.collaterals.length > 0) {
                linkedCollaterals = response.collaterals;
                displayCollateralSummary(response.collaterals, response.summary);
            } else {
                linkedCollaterals = [];
                document.getElementById('collateral_summary').innerHTML = '<p style="color: #6b7280; text-align: center;">No collateral linked to this loan</p>';
                document.getElementById('collateral_totals').style.display = 'none';
                document.getElementById('collateral_count_badge').style.display = 'none';
            }
        },
        error: function(xhr, status, error) {
            console.log('Error loading collateral summary:', error, xhr.responseText);
            document.getElementById('collateral_summary').innerHTML = '<p style="color: #dc2626; text-align: center;">Error loading collaterals</p>';
        }
    });
}

function displayCollateralSummary(collaterals, summary) {
    var badge = document.getElementById('collateral_count_badge');
    var count = collaterals.length;

    if(count > 0) {
        badge.textContent = count;
        badge.style.display = 'inline';

        var totalForceSale = 0;
        var totalUtilizedThis = 0;

        collaterals.forEach(function(c) {
            totalForceSale += parseFloat(c.force_sale_value) || 0;
            totalUtilizedThis += parseFloat(c.amount_utilized) || 0;
        });

        var html = '<div style="display: flex; justify-content: space-around; text-align: center;">';
        html += '<div><div style="font-size: 1.5rem; font-weight: 700; color: #1e3a5f;">' + count + '</div><div style="font-size: 0.75rem; color: #6b7280;">Linked Items</div></div>';
        html += '<div><div style="font-size: 1.1rem; font-weight: 700; color: #059669;">' + currencyCode + ' ' + numberFormat(totalForceSale) + '</div><div style="font-size: 0.75rem; color: #6b7280;">Force Sale Total</div></div>';
        html += '<div><div style="font-size: 1.1rem; font-weight: 700; color: #dc2626;">' + currencyCode + ' ' + numberFormat(totalUtilizedThis) + '</div><div style="font-size: 0.75rem; color: #6b7280;">Utilized</div></div>';
        html += '</div>';

        document.getElementById('collateral_summary').innerHTML = html;

        // Show totals section
        document.getElementById('collateral_totals').style.display = 'block';
        document.getElementById('total_force_sale').textContent = currencyCode + ' ' + numberFormat(totalForceSale);
        document.getElementById('total_utilized_this').textContent = currencyCode + ' ' + numberFormat(totalUtilizedThis);
        document.getElementById('total_utilized_other').textContent = currencyCode + ' ' + numberFormat(summary ? summary.other_loans_utilization : 0);
    } else {
        badge.style.display = 'none';
        document.getElementById('collateral_totals').style.display = 'none';
        document.getElementById('collateral_summary').innerHTML = '<p style="color: #6b7280; text-align: center; margin: 0;"><i class="fa fa-shield-alt" style="opacity: 0.3;"></i> No collateral linked yet</p>';
    }
}

function numberFormat(num) {
    return parseFloat(num).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

function openCollateralModal() {
    openAddCollateralModal();
}

function openAddCollateralModal() {
    document.getElementById('add_collateral_form').reset();
    $('#add_collateral_modal').modal('show');
}

function openCollateralListModal() {
    $('#collateral_list_modal').modal('show');
    loadCollateralList();
}

function openLinkCollateralModal() {
    $('#link_collateral_modal').modal('show');
    loadAvailableCollaterals();
}

function loadAvailableCollaterals() {
    var container = document.getElementById('available_collaterals_container');
    container.innerHTML = '<div style="text-align: center; padding: 2rem; color: #6b7280;"><i class="fa fa-spinner fa-spin fa-2x"></i><p style="margin-top: 0.5rem;">Loading customer collaterals...</p></div>';

    document.getElementById('link_collateral_form').style.display = 'none';
    document.getElementById('no_collaterals_message').style.display = 'none';

    $.ajax({
        url: '<?php echo base_url("loan/get_customer_collaterals/"); ?><?php echo $cust_id; ?>/<?php echo $customer_type == "institution" ? "institution" : "individual"; ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            customerCollaterals = response.collaterals || [];

            // Filter out already linked collaterals and those with no available balance
            var linkedIds = linkedCollaterals.map(function(c) { return c.collateral_id || c.id; });
            var available = customerCollaterals.filter(function(c) {
                var availableBalance = parseFloat(c.available_balance || c.force_sale_value);
                return !linkedIds.includes(c.id) && availableBalance > 0 && c.status === 'ACTIVE';
            });

            if(available.length > 0) {
                var html = '<div class="list-group">';
                available.forEach(function(c) {
                    var availableBalance = parseFloat(c.available_balance || c.force_sale_value);
                    html += `
                        <a href="javascript:void(0)" class="list-group-item list-group-item-action" onclick="selectCollateralToLink(${c.id})">
                            <div class="d-flex w-100 justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1" style="font-weight: 600; color: #1e3a5f;">${escapeHtml(c.collateral_name)}</h6>
                                    <small class="text-muted">${escapeHtml(c.collateral_type)} ${c.collateral_serial ? '| ' + c.collateral_serial : ''}</small>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 0.85rem;"><strong>Force Sale:</strong> ${currencyCode} ${numberFormat(c.force_sale_value)}</div>
                                    <div style="font-size: 0.85rem; color: #059669;"><strong>Available:</strong> ${currencyCode} ${numberFormat(availableBalance)}</div>
                                </div>
                            </div>
                        </a>
                    `;
                });
                html += '</div>';
                html += '<div class="mt-3 text-center"><button type="button" class="btn-action btn-primary" onclick="openAddCollateralModal()"><i class="fa fa-plus"></i> Add New Collateral for Customer</button></div>';
                container.innerHTML = html;
            } else if(customerCollaterals.length > 0) {
                container.innerHTML = '<div style="text-align: center; padding: 2rem;"><i class="fa fa-exclamation-circle fa-3x" style="color: #f59e0b; margin-bottom: 1rem;"></i><p>All customer collaterals are already linked or have no available balance.</p><button type="button" class="btn-action btn-primary" onclick="openAddCollateralModal()"><i class="fa fa-plus"></i> Add New Collateral</button></div>';
            } else {
                container.innerHTML = '';
                document.getElementById('no_collaterals_message').style.display = 'block';
            }
        },
        error: function() {
            container.innerHTML = '<div style="text-align: center; padding: 2rem; color: #dc2626;"><i class="fa fa-exclamation-circle fa-2x"></i><p style="margin-top: 0.5rem;">Error loading collaterals</p></div>';
        }
    });
}

function selectCollateralToLink(collateralId) {
    var collateral = customerCollaterals.find(function(c) { return c.id == collateralId; });
    if(!collateral) return;

    var availableBalance = parseFloat(collateral.available_balance || collateral.force_sale_value);

    document.getElementById('link_collateral_id').value = collateralId;
    document.getElementById('amount_to_utilize').value = availableBalance;
    document.getElementById('amount_to_utilize').max = availableBalance;
    document.getElementById('max_available_display').textContent = numberFormat(availableBalance);

    document.getElementById('selected_collateral_details').innerHTML = `
        <div><strong>${escapeHtml(collateral.collateral_name)}</strong> (${escapeHtml(collateral.collateral_type)})</div>
        <div class="mt-1">
            <span class="mr-3">Market Value: ${currencyCode} ${numberFormat(collateral.market_value)}</span>
            <span class="mr-3">Force Sale: ${currencyCode} ${numberFormat(collateral.force_sale_value)}</span>
            <span style="color: #059669;">Available: ${currencyCode} ${numberFormat(availableBalance)}</span>
        </div>
    `;

    document.getElementById('available_collaterals_container').style.display = 'none';
    document.getElementById('link_collateral_form').style.display = 'block';
}

function cancelLinkCollateral() {
    document.getElementById('link_collateral_form').style.display = 'none';
    document.getElementById('available_collaterals_container').style.display = 'block';
}

function loadCollateralList() {
    var container = document.getElementById('collateral_list_container');
    container.innerHTML = '<div style="text-align: center; padding: 2rem; color: #6b7280;"><i class="fa fa-spinner fa-spin fa-2x"></i><p style="margin-top: 0.5rem;">Loading collaterals...</p></div>';

    $.ajax({
        url: '<?php echo base_url("loan/get_loan_collaterals/"); ?><?php echo $loan_id; ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.success && response.collaterals && response.collaterals.length > 0) {
                displayCollateralList(response.collaterals);
            } else {
                container.innerHTML = '<div style="text-align: center; padding: 3rem; color: #6b7280;"><i class="fa fa-shield-alt fa-3x" style="opacity: 0.3; margin-bottom: 1rem;"></i><p>No collateral linked to this loan</p><p style="font-size: 0.85rem;">Click "Link Collateral" to add one</p></div>';
            }
        },
        error: function() {
            container.innerHTML = '<div style="text-align: center; padding: 2rem; color: #dc2626;"><i class="fa fa-exclamation-circle fa-2x"></i><p style="margin-top: 0.5rem;">Error loading collaterals</p></div>';
        }
    });
}

function displayCollateralList(collaterals) {
    var container = document.getElementById('collateral_list_container');
    var html = '';

    collaterals.forEach(function(c) {
        var statusClass = (c.link_status || c.status || 'ACTIVE').toLowerCase();
        var collateralStatus = (c.collateral_status || 'ACTIVE').toLowerCase();
        var availableBalance = parseFloat(c.force_sale_value) - parseFloat(c.total_utilized || c.amount_utilized || 0);

        html += `
            <div class="collateral-card" data-id="${c.link_id || c.id}">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
                    <div style="flex: 1; min-width: 200px;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <h6 style="margin: 0; font-weight: 600; color: #1e3a5f;">${escapeHtml(c.collateral_name)}</h6>
                            <span class="collateral-status ${statusClass}">${c.link_status || c.status || 'ACTIVE'}</span>
                            <span class="collateral-status ${collateralStatus}" style="font-size: 0.7rem;">Asset: ${c.collateral_status || 'ACTIVE'}</span>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.5rem; font-size: 0.85rem; color: #6b7280;">
                            <div><strong>Type:</strong> ${escapeHtml(c.collateral_type)}</div>
                            <div><strong>Serial:</strong> ${c.collateral_serial || 'N/A'}</div>
                            <div><strong>Market Value:</strong> <span style="color: #6b7280;">${currencyCode} ${numberFormat(c.market_value)}</span></div>
                            <div><strong>Force Sale:</strong> <span style="color: #059669;">${currencyCode} ${numberFormat(c.force_sale_value)}</span></div>
                        </div>
                        <div style="margin-top: 0.5rem; padding: 0.5rem; background: #f0f9ff; border-radius: 6px; font-size: 0.85rem;">
                            <strong>This Loan Utilization:</strong> <span style="color: #dc2626; font-weight: 600;">${currencyCode} ${numberFormat(c.amount_utilized)}</span>
                            <span class="ml-3"><strong>Available Balance:</strong> <span style="color: #059669;">${currencyCode} ${numberFormat(availableBalance > 0 ? availableBalance : 0)}</span></span>
                        </div>
                        ${c.description ? '<p style="margin: 0.5rem 0 0 0; font-size: 0.85rem; color: #6b7280;"><em>' + escapeHtml(c.description) + '</em></p>' : ''}
                    </div>
                    <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
                        <button onclick="viewCollateralLoans(${c.collateral_id || c.id})" class="btn-action btn-info" title="View All Loans Using This Collateral"><i class="fa fa-list"></i></button>
                        <button onclick="unlinkCollateral(${c.link_id}, this)" class="btn-action btn-danger" title="Unlink from Loan"><i class="fa fa-unlink"></i></button>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

function viewCollateralLoans(collateralId) {
    $.ajax({
        url: '<?php echo base_url("loan/get_collateral_loans/"); ?>' + collateralId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.success && response.loans && response.loans.length > 0) {
                var html = '<h6>Loans using this collateral:</h6><table class="table table-sm"><thead><tr><th>Loan #</th><th>Principal</th><th>Utilized</th><th>Status</th></tr></thead><tbody>';
                response.loans.forEach(function(loan) {
                    html += '<tr><td>' + loan.loan_number + '</td><td>' + currencyCode + ' ' + numberFormat(loan.loan_principal) + '</td><td>' + currencyCode + ' ' + numberFormat(loan.amount_utilized) + '</td><td>' + loan.loan_status + '</td></tr>';
                });
                html += '</tbody></table>';

                document.getElementById('collateral_details_content').innerHTML = html;
                document.getElementById('collateral_history_content').innerHTML = '';
                $('#view_collateral_modal').modal('show');
            } else {
                alert('No loans found using this collateral');
            }
        },
        error: function() {
            alert('Error loading loan information');
        }
    });
}

function unlinkCollateral(linkId, btn) {
    if (!confirm('Are you sure you want to unlink this collateral from the loan?')) {
        return;
    }

    var card = btn.closest('.collateral-card');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

    $.ajax({
        url: '<?php echo base_url("loan/unlink_collateral"); ?>',
        type: 'POST',
        data: { link_id: linkId },
        dataType: 'json',
        success: function(response) {
            if(response.success) {
                card.style.transition = 'all 0.3s';
                card.style.opacity = '0';
                card.style.transform = 'translateX(20px)';
                setTimeout(function() {
                    card.remove();
                    loadCollateralSummary();
                    if(document.querySelectorAll('.collateral-card').length === 0) {
                        document.getElementById('collateral_list_container').innerHTML = '<div style="text-align: center; padding: 3rem; color: #6b7280;"><i class="fa fa-shield-alt fa-3x" style="opacity: 0.3; margin-bottom: 1rem;"></i><p>No collateral linked to this loan</p></div>';
                    }
                }, 300);
            } else {
                alert('Error: ' + response.message);
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-unlink"></i>';
            }
        },
        error: function() {
            alert('Error unlinking collateral');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-unlink"></i>';
        }
    });
}

function openUpdateStatusModal(collateralId, currentStatus) {
    document.getElementById('status_collateral_id').value = collateralId;
    document.getElementById('current_status_display').value = currentStatus;
    document.getElementById('new_status_select').value = '';
    document.querySelector('#update_status_form textarea[name="remarks"]').value = '';
    $('#update_collateral_status_modal').modal('show');
}

function viewCollateralDetails(collateralId) {
    // For linked collaterals
    var collateral = linkedCollaterals.find(function(c) { return (c.collateral_id || c.id) == collateralId; });
    if(collateral) {
        var availableBalance = parseFloat(collateral.force_sale_value) - parseFloat(collateral.total_utilized || 0);
        var html = `
            <div class="info-grid">
                <div class="info-item"><div class="label">Name</div><div class="value">${escapeHtml(collateral.collateral_name)}</div></div>
                <div class="info-item"><div class="label">Type</div><div class="value">${escapeHtml(collateral.collateral_type)}</div></div>
                <div class="info-item"><div class="label">Serial/Reg No.</div><div class="value">${collateral.collateral_serial || 'N/A'}</div></div>
                <div class="info-item"><div class="label">Market Value</div><div class="value">${currencyCode} ${numberFormat(collateral.market_value)}</div></div>
                <div class="info-item"><div class="label">Force Sale Value</div><div class="value" style="color: #059669;">${currencyCode} ${numberFormat(collateral.force_sale_value)}</div></div>
                <div class="info-item"><div class="label">This Loan Usage</div><div class="value" style="color: #dc2626;">${currencyCode} ${numberFormat(collateral.amount_utilized)}</div></div>
                <div class="info-item"><div class="label">Available Balance</div><div class="value" style="color: #059669;">${currencyCode} ${numberFormat(availableBalance > 0 ? availableBalance : 0)}</div></div>
            </div>
            ${collateral.description ? '<div style="margin-top: 1rem; padding: 1rem; background: #f8fafc; border-radius: 8px;"><strong>Description:</strong><br>' + escapeHtml(collateral.description) + '</div>' : ''}
        `;
        document.getElementById('collateral_details_content').innerHTML = html;
        loadCollateralHistory(collateralId);
        $('#view_collateral_modal').modal('show');
    }
}

function loadCollateralHistory(collateralId) {
    var container = document.getElementById('collateral_history_content');
    container.innerHTML = '<div style="text-align: center; padding: 1rem;"><i class="fa fa-spinner fa-spin"></i> Loading history...</div>';

    $.ajax({
        url: '<?php echo base_url("loan/get_collateral_history/"); ?>' + collateralId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.success && response.history && response.history.length > 0) {
                var html = '<div style="max-height: 200px; overflow-y: auto;">';
                response.history.forEach(function(h) {
                    html += `
                        <div style="padding: 0.5rem; border-left: 3px solid #f59e0b; margin-bottom: 0.5rem; background: #f8fafc;">
                            <div style="font-size: 0.85rem;"><span class="collateral-status ${h.old_status.toLowerCase()}">${h.old_status}</span> <i class="fa fa-arrow-right" style="color: #6b7280;"></i> <span class="collateral-status ${h.new_status.toLowerCase()}">${h.new_status}</span></div>
                            ${h.remarks ? '<div style="font-size: 0.8rem; color: #6b7280; margin-top: 0.25rem;">' + escapeHtml(h.remarks) + '</div>' : ''}
                            <div style="font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">By ${h.changed_by || 'System'} on ${h.changed_at}</div>
                        </div>
                    `;
                });
                html += '</div>';
                container.innerHTML = html;
            } else {
                container.innerHTML = '<p style="color: #6b7280; font-size: 0.85rem; text-align: center;">No status changes recorded</p>';
            }
        },
        error: function() {
            container.innerHTML = '<p style="color: #dc2626; font-size: 0.85rem;">Error loading history</p>';
        }
    });
}

// Handle add collateral form (creates new collateral for customer)
var addCollateralForm = document.getElementById('add_collateral_form');
if (addCollateralForm) addCollateralForm.addEventListener('submit', function(e) {
    e.preventDefault();

    var formData = new FormData(this);
    var submitBtn = this.querySelector('button[type="submit"]');
    var originalText = submitBtn.innerHTML;

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';

    $.ajax({
        url: '<?php echo base_url("loan/add_customer_collateral"); ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;

            if(response.success) {
                $('#add_collateral_modal').modal('hide');
                alert('Collateral added to customer profile. You can now link it to the loan.');
                // Refresh the link modal if open
                if($('#link_collateral_modal').hasClass('show')) {
                    loadAvailableCollaterals();
                }
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            alert('Error adding collateral. Please try again.');
        }
    });
});

// Handle link collateral form
var linkCollateralForm = document.getElementById('link_collateral_form');
if (linkCollateralForm) linkCollateralForm.addEventListener('submit', function(e) {
    e.preventDefault();

    var formData = new FormData(this);
    var submitBtn = this.querySelector('button[type="submit"]');
    var originalText = submitBtn.innerHTML;

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Linking...';

    $.ajax({
        url: '<?php echo base_url("loan/link_collateral_to_loan"); ?>',
        type: 'POST',
        data: {
            loan_id: formData.get('loan_id'),
            collateral_id: formData.get('collateral_id'),
            amount_utilized: formData.get('amount_utilized')
        },
        dataType: 'json',
        success: function(response) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;

            if(response.success) {
                $('#link_collateral_modal').modal('hide');
                loadCollateralSummary();
                if($('#collateral_list_modal').hasClass('show')) {
                    loadCollateralList();
                }
                alert('Collateral linked successfully');
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            alert('Error linking collateral. Please try again.');
        }
    });
});

// Handle update status form
var updateStatusForm = document.getElementById('update_status_form');
if (updateStatusForm) updateStatusForm.addEventListener('submit', function(e) {
    e.preventDefault();

    var formData = new FormData(this);
    var submitBtn = this.querySelector('button[type="submit"]');
    var originalText = submitBtn.innerHTML;

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Updating...';

    $.ajax({
        url: '<?php echo base_url("loan/update_collateral_status"); ?>',
        type: 'POST',
        data: {
            collateral_id: formData.get('collateral_id'),
            status: formData.get('status'),
            remarks: formData.get('remarks')
        },
        dataType: 'json',
        success: function(response) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;

            if(response.success) {
                $('#update_collateral_status_modal').modal('hide');
                loadCollateralSummary();
                loadCollateralList();
                alert('Status updated successfully');
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            alert('Error updating status. Please try again.');
        }
    });
});

// ==================== BULLET LOAN PAYMENT DATE CALCULATION ====================
<?php if($calculation_type == 'Bullet Payment'): ?>
var isBulletLoan = true;
var originalPaymentAmount = <?php echo !empty($next_payment_details) ? $next_payment_details->amount : '0'; ?>;

// Handle payment date change for bullet loans
var paymentDateInput = document.getElementById('payment_date_input');
if (paymentDateInput) {
    paymentDateInput.addEventListener('change', function() {
        var selectedDate = this.value;
        if (!selectedDate) return;

        calculateBulletPayoff(selectedDate);
    });
}

function calculateBulletPayoff(paymentDate) {
    var amountInput = document.getElementById('payment_amount_input');
    var amountDisplay = document.getElementById('payment_modal_amount_display');
    var calculatingDiv = document.getElementById('payment_calculating');
    var breakdownDiv = document.getElementById('bullet_calculation_breakdown');
    var breakdownContent = document.getElementById('bullet_breakdown_content');

    // Show calculating indicator
    calculatingDiv.style.display = 'block';
    amountInput.disabled = true;

    $.ajax({
        url: '<?php echo base_url("loan/calculate_payoff"); ?>',
        type: 'POST',
        data: {
            loan_id: loanId,
            payoff_date: paymentDate
        },
        dataType: 'json',
        success: function(response) {
            calculatingDiv.style.display = 'none';
            amountInput.disabled = false;

            if (response.status === 'success') {
                var payoffAmount = parseFloat(response.payoff_amount);

                // Update the amount input and display
                amountInput.value = payoffAmount.toFixed(2);
                amountDisplay.innerHTML = currencyCode + ' ' + numberFormat(payoffAmount);

                // Show breakdown for arrears (compound interest)
                if (response.days_past_maturity > 0) {
                    amountDisplay.style.color = '#dc2626';
                    amountDisplay.style.fontWeight = '700';

                    var breakdownHtml = '<div style="margin-bottom: 0.5rem;"><strong>⚠️ Loan is ' + response.days_past_maturity + ' days past maturity</strong></div>';
                    breakdownHtml += '<table style="width: 100%; font-size: 0.85rem;">';
                    breakdownHtml += '<tr><td>Original Principal:</td><td style="text-align: right;">' + currencyCode + ' ' + numberFormat(response.principal) + '</td></tr>';
                    breakdownHtml += '<tr><td>Amount at Maturity:</td><td style="text-align: right;">' + currencyCode + ' ' + numberFormat(response.maturity_total) + '</td></tr>';
                    breakdownHtml += '<tr><td>Payments Made:</td><td style="text-align: right; color: #059669;">-' + currencyCode + ' ' + numberFormat(response.amount_paid) + '</td></tr>';
                    breakdownHtml += '<tr><td>Outstanding at Maturity:</td><td style="text-align: right;">' + currencyCode + ' ' + numberFormat(response.outstanding_at_maturity) + '</td></tr>';
                    breakdownHtml += '<tr style="border-top: 1px solid #fbbf24;"><td><strong>Arrears Interest (Compounded):</strong></td><td style="text-align: right; color: #dc2626;"><strong>+' + currencyCode + ' ' + numberFormat(response.arrears_interest) + '</strong></td></tr>';
                    breakdownHtml += '<tr style="background: #fef3c7;"><td><strong>Total Payoff Amount:</strong></td><td style="text-align: right; font-weight: 700; color: #dc2626;">' + currencyCode + ' ' + numberFormat(payoffAmount) + '</td></tr>';
                    breakdownHtml += '</table>';

                    if (response.full_months_past > 0 || response.remaining_days > 0) {
                        breakdownHtml += '<div style="margin-top: 0.5rem; font-size: 0.8rem; color: #78350f;">';
                        breakdownHtml += '<i class="fa fa-info-circle"></i> Interest compounded for ' + response.full_months_past + ' month(s) + ' + response.remaining_days + ' day(s) at ' + (parseFloat(response.monthly_rate) * 100).toFixed(2) + '% per month';
                        breakdownHtml += '</div>';
                    }

                    breakdownContent.innerHTML = breakdownHtml;
                    breakdownDiv.style.display = 'block';
                } else {
                    // Before maturity
                    amountDisplay.style.color = '#059669';
                    amountDisplay.style.fontWeight = '600';

                    var breakdownHtml = '<table style="width: 100%; font-size: 0.85rem;">';
                    breakdownHtml += '<tr><td>Principal:</td><td style="text-align: right;">' + currencyCode + ' ' + numberFormat(response.principal) + '</td></tr>';
                    breakdownHtml += '<tr><td>Accrued Interest:</td><td style="text-align: right;">' + currencyCode + ' ' + numberFormat(response.accrued_interest) + '</td></tr>';
                    breakdownHtml += '<tr><td>Payments Made:</td><td style="text-align: right; color: #059669;">-' + currencyCode + ' ' + numberFormat(response.amount_paid) + '</td></tr>';
                    breakdownHtml += '<tr style="background: #ecfdf5;"><td><strong>Total Payoff:</strong></td><td style="text-align: right; font-weight: 700; color: #059669;">' + currencyCode + ' ' + numberFormat(payoffAmount) + '</td></tr>';
                    breakdownHtml += '</table>';

                    breakdownContent.innerHTML = breakdownHtml;
                    breakdownDiv.style.display = 'block';
                }
            } else {
                alert('Error calculating payoff: ' + response.message);
                amountInput.value = originalPaymentAmount;
            }
        },
        error: function() {
            calculatingDiv.style.display = 'none';
            amountInput.disabled = false;
            alert('Error calculating payoff amount. Please try again.');
            amountInput.value = originalPaymentAmount;
        }
    });
}
<?php else: ?>
var isBulletLoan = false;
<?php endif; ?>

// Load notes count on page load (collaterals are rendered in PHP)
$(document).ready(function() {
    console.log('Document ready');
    loadNotesCount();

    // Check if we should auto-open the notes modal (from email link)
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('open_notes') === '1') {
        // Small delay to ensure modal is ready
        setTimeout(function() {
            openNotesModal();
        }, 500);
    }
});
console.log('Loan view script finished');
</script>
<?php $GLOBALS['page_scripts'] = ob_get_clean(); ?>
