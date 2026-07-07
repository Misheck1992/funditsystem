<?php
$current_user_id = $this->session->userdata('user_id');
?>
<style>
.approval-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    margin-bottom: 1.5rem;
    overflow: hidden;
}
.approval-header {
    background: #1e3a5f;
    color: #fff;
    padding: 1.25rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.approval-header h4 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #fff !important;
}
.approval-stats {
    display: flex;
    gap: 1.5rem;
}
.approval-stat {
    text-align: center;
    background: rgba(255,255,255,0.1);
    padding: 0.5rem 1rem;
    border-radius: 8px;
}
.approval-stat .number {
    font-size: 1.5rem;
    font-weight: 700;
}
.approval-stat .label {
    font-size: 0.75rem;
    opacity: 0.9;
}
.approval-body {
    padding: 1.5rem;
}

/* Progress indicator */
.approval-progress {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.progress-circle {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
}
.progress-circle.completed {
    background: #059669;
    color: #fff;
}
.progress-circle.pending {
    background: #e5e7eb;
    color: #6b7280;
}
.progress-circle.current {
    background: #f59e0b;
    color: #fff;
    animation: pulse 1.5s infinite;
}
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}
.progress-line {
    width: 20px;
    height: 3px;
    background: #e5e7eb;
}
.progress-line.completed {
    background: #059669;
}
.progress-text {
    margin-left: 0.75rem;
    font-size: 0.85rem;
    color: #6b7280;
    font-weight: 500;
}

/* Approvers list */
.approvers-list {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-top: 0.5rem;
}
.approver-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    background: #ecfdf5;
    color: #059669;
    padding: 0.25rem 0.5rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
}
.approver-badge i {
    font-size: 0.65rem;
}
.approver-badge.you {
    background: #dbeafe;
    color: #1e40af;
}

/* Action buttons */
.btn-approve {
    background: #059669;
    color: #fff;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
}
.btn-approve:hover {
    background: #047857;
    color: #fff;
}
.btn-approve:disabled {
    background: #9ca3af;
    cursor: not-allowed;
}
.btn-reject {
    background: #dc2626;
    color: #fff;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
}
.btn-reject:hover {
    background: #b91c1c;
    color: #fff;
}

/* Status message */
.approval-status-msg {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
}
.approval-status-msg.warning {
    background: #fef3c7;
    color: #92400e;
}
.approval-status-msg.info {
    background: #dbeafe;
    color: #1e40af;
}

/* Table styles */
.approval-table {
    width: 100%;
    border-collapse: collapse;
}
.approval-table th {
    background: #f8fafc;
    padding: 0.75rem 1rem;
    text-align: left;
    font-weight: 600;
    color: #374151;
    font-size: 0.85rem;
    border-bottom: 2px solid #e5e7eb;
}
.approval-table td {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
    vertical-align: top;
}
.approval-table tr:hover {
    background: #f8fafc;
}
.loan-number-link {
    color: #1e3a5f;
    font-weight: 600;
    text-decoration: none;
}
.loan-number-link:hover {
    color: #3b82f6;
    text-decoration: underline;
}
.customer-link {
    color: #3b82f6;
    text-decoration: none;
}
.customer-link:hover {
    text-decoration: underline;
}
.amount-cell {
    font-weight: 600;
    color: #1e3a5f;
}

/* Empty state */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #6b7280;
}
.empty-state i {
    font-size: 4rem;
    opacity: 0.3;
    margin-bottom: 1rem;
}
.empty-state h5 {
    color: #374151;
    margin-bottom: 0.5rem;
}
</style>

<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Loan Approvals</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">Loans</a>
                <span class="breadcrumb-item active">Pending Approvals</span>
            </nav>
        </div>
    </div>

    <div class="approval-card">
        <div class="approval-header">
            <h4><i class="fa fa-check-double mr-2"></i>Loans Pending Approval</h4>
            <div class="approval-stats">
                <div class="approval-stat">
                    <div class="number"><?php echo count($loan_data); ?></div>
                    <div class="label">Total Pending</div>
                </div>
                <div class="approval-stat">
                    <div class="number"><?php
                        $can_approve_count = 0;
                        foreach($loan_data as $loan) {
                            $approvers = get_loan_approvers($loan->loan_id);
                            $last_approver = !empty($approvers) ? end($approvers)['user_id'] : null;
                            // Only check consecutive - same user CAN approve 1st and 3rd
                            if($last_approver != $current_user_id) {
                                $can_approve_count++;
                            }
                        }
                        echo $can_approve_count;
                    ?></div>
                    <div class="label">You Can Approve</div>
                </div>
            </div>
        </div>

        <div class="approval-body">
            <!-- Instructions -->
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <i class="fa fa-info-circle" style="color: #3b82f6; font-size: 1.25rem; margin-top: 0.1rem;"></i>
                    <div>
                        <strong style="color: #1e40af;">Multi-Level Approval System</strong>
                        <p style="margin: 0.5rem 0 0 0; color: #1e40af; font-size: 0.9rem;">
                            Each loan requires <strong>3 approvals</strong> from different users before disbursement.
                            You cannot approve a loan if you were the last person to approve it (no consecutive approvals).
                        </p>
                    </div>
                </div>
            </div>

            <?php if(empty($loan_data)): ?>
            <div class="empty-state">
                <i class="fa fa-check-circle"></i>
                <h5>No Loans Pending Approval</h5>
                <p>All recommended loans have been processed.</p>
            </div>
            <?php else: ?>

            <div style="overflow-x: auto;">
                <table class="approval-table" id="unified_approval_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Loan Details</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Approval Progress</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $n = 1;
                        foreach ($loan_data as $loan):
                            // Get customer info
                            if($loan->customer_type == 'group'){
                                $group = $this->Groups_model->get_by_id($loan->loan_customer);
                                $customer_name = $group->group_name.' ('.$group->group_code.')';
                                $preview_url = "Customer_groups/members/";
                            } elseif($loan->customer_type == 'individual'){
                                $indi = $this->Individual_customers_model->get_by_id($loan->loan_customer);
                                $customer_name = $indi->Firstname.' '.$indi->Lastname;
                                $preview_url = "Individual_customers/view/";
                            } elseif($loan->customer_type == 'institution'){
                                $inst = get_by_id('corporate_customers','id',$loan->loan_customer);
                                $customer_name = $inst->EntityName.' ('.$inst->entity_type.')';
                                $preview_url = "Corporate_customers/read/";
                            } else {
                                $customer_name = 'Unknown';
                                $preview_url = "#";
                            }

                            $currency = get_by_id('currencies','currency_id',$loan->currency);

                            // Get approval info
                            $approvers = get_loan_approvers($loan->loan_id);
                            $approval_count = count($approvers);
                            $last_approver_id = !empty($approvers) ? end($approvers)['user_id'] : null;

                            // Only check for consecutive approvals - same user CAN approve 1st and 3rd
                            // But same user CANNOT approve consecutively (1st and 2nd, or 2nd and 3rd)
                            $can_approve = ($last_approver_id != $current_user_id);
                            $reason_cannot_approve = '';
                            if($last_approver_id == $current_user_id) {
                                $reason_cannot_approve = 'You were the last approver (no consecutive approvals)';
                            }
                        ?>
                        <tr>
                            <td><?php echo $n; ?></td>
                            <td>
                                <a href="<?php echo base_url('loan/view/').$loan->loan_id; ?>" class="loan-number-link">
                                    <?php echo $loan->loan_number; ?>
                                </a>
                                <div style="font-size: 0.8rem; color: #6b7280; margin-top: 0.25rem;">
                                    <?php echo $loan->product_name; ?>
                                </div>
                                <div style="font-size: 0.75rem; color: #9ca3af;">
                                    <?php echo date('d M Y', strtotime($loan->loan_date)); ?> |
                                    <?php echo $loan->loan_period; ?> <?php echo $loan->period_type; ?> @
                                    <?php echo $loan->loan_interest; ?>%
                                </div>
                            </td>
                            <td>
                                <a href="<?php echo base_url($preview_url).$loan->loan_customer; ?>" class="customer-link">
                                    <?php echo $customer_name; ?>
                                </a>
                            </td>
                            <td class="amount-cell">
                                <?php echo $currency->currency_code; ?> <?php echo number_format($loan->loan_principal, 2); ?>
                                <div style="font-size: 0.75rem; color: #6b7280; font-weight: normal;">
                                    Total: <?php echo $currency->currency_code; ?> <?php echo number_format($loan->loan_amount_total, 2); ?>
                                </div>
                            </td>
                            <td>
                                <!-- Progress circles -->
                                <div class="approval-progress">
                                    <?php for($i = 1; $i <= 3; $i++): ?>
                                        <?php if($i <= $approval_count): ?>
                                            <div class="progress-circle completed"><i class="fa fa-check"></i></div>
                                        <?php elseif($i == $approval_count + 1): ?>
                                            <div class="progress-circle current"><?php echo $i; ?></div>
                                        <?php else: ?>
                                            <div class="progress-circle pending"><?php echo $i; ?></div>
                                        <?php endif; ?>
                                        <?php if($i < 3): ?>
                                            <div class="progress-line <?php echo $i < $approval_count ? 'completed' : ''; ?>"></div>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    <span class="progress-text"><?php echo $approval_count; ?> of 3</span>
                                </div>

                                <!-- Approvers list -->
                                <?php if(!empty($approvers)): ?>
                                <div class="approvers-list">
                                    <?php foreach($approvers as $idx => $approver): ?>
                                        <span class="approver-badge <?php echo $approver['user_id'] == $current_user_id ? 'you' : ''; ?>">
                                            <i class="fa fa-user-check"></i>
                                            <?php echo ($idx + 1); ?>. <?php echo $approver['user_name']; ?>
                                            <?php if($approver['user_id'] == $current_user_id): ?>(You)<?php endif; ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($can_approve): ?>
                                    <button type="button" class="btn-approve" onclick="openApprovalModal('MULTI_APPROVE', <?php echo $loan->loan_id; ?>, <?php echo $approval_count + 1; ?>)">
                                        <i class="fa fa-check"></i> Approve (#<?php echo $approval_count + 1; ?>)
                                    </button>
                                    <button type="button" class="btn-reject" onclick="openApprovalModal('REJECT', <?php echo $loan->loan_id; ?>, 0)" style="margin-left: 0.5rem;">
                                        <i class="fa fa-times"></i>
                                    </button>
                                <?php else: ?>
                                    <span class="approval-status-msg warning">
                                        <i class="fa fa-ban"></i>
                                        <?php echo $reason_cannot_approve; ?>
                                    </span>
                                <?php endif; ?>
                                <div style="margin-top: 0.5rem;">
                                    <a href="<?php echo base_url('loan/view/').$loan->loan_id.'?action=multi_approve'; ?>" class="btn btn-sm btn-outline-primary" style="font-size: 0.75rem;">
                                        <i class="fa fa-eye"></i> View Details
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php
                        $n++;
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="multi_approval_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header" id="approval_modal_header" style="background: #059669; color: #fff;">
                <h5 class="modal-title" id="approval_modal_title"><i class="fa fa-check-circle mr-2"></i>Approve Loan</h5>
                <button type="button" class="close" data-dismiss="modal" style="color: #fff; opacity: 1;">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="approval_level_badge" style="text-align: center; margin-bottom: 1rem;">
                    <span style="background: #dbeafe; color: #1e40af; padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600;">
                        Approval Level: <span id="approval_level_number">1</span> of 3
                    </span>
                </div>

                <form action="<?php echo base_url('loan/multi_approval_action'); ?>" method="POST" id="multi_approval_form">
                    <input type="hidden" name="loan_id" id="modal_loan_id" value="">
                    <input type="hidden" name="action" id="modal_action" value="">
                    <input type="hidden" name="approval_level" id="modal_approval_level" value="">

                    <div class="form-group">
                        <label><strong>Comment</strong> <span style="color: #dc2626;">*</span></label>
                        <textarea class="form-control" name="comment" id="modal_comment" rows="4"
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

<script>
function openApprovalModal(action, loanId, level) {
    document.getElementById('modal_loan_id').value = loanId;
    document.getElementById('modal_action').value = action;
    document.getElementById('modal_approval_level').value = level;
    document.getElementById('modal_comment').value = '';

    var header = document.getElementById('approval_modal_header');
    var title = document.getElementById('approval_modal_title');
    var levelBadge = document.getElementById('approval_level_badge');

    if(action === 'REJECT') {
        header.style.background = '#dc2626';
        title.innerHTML = '<i class="fa fa-times-circle mr-2"></i>Reject Loan';
        levelBadge.style.display = 'none';
    } else {
        header.style.background = '#059669';
        title.innerHTML = '<i class="fa fa-check-circle mr-2"></i>Approve Loan';
        levelBadge.style.display = 'block';
        document.getElementById('approval_level_number').textContent = level;
    }

    $('#multi_approval_modal').modal('show');
}

// Initialize DataTable
$(document).ready(function() {
    $('#unified_approval_table').DataTable({
        "pageLength": 10,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "order": [[0, "asc"]],
        "responsive": true,
        "dom": '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>B',
        "buttons": [
            {
                extend: 'excel',
                text: '<i class="fa fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fa fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Print',
                className: 'btn btn-info btn-sm',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            }
        ],
        "language": {
            "search": "<i class='fa fa-search'></i> Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ loans",
            "infoEmpty": "No loans available",
            "infoFiltered": "(filtered from _MAX_ total loans)",
            "paginate": {
                "first": "<i class='fa fa-angle-double-left'></i>",
                "last": "<i class='fa fa-angle-double-right'></i>",
                "next": "<i class='fa fa-angle-right'></i>",
                "previous": "<i class='fa fa-angle-left'></i>"
            }
        },
        "columnDefs": [
            { "orderable": false, "targets": [5] }
        ]
    });
});
</script>

<style>
/* DataTable styling overrides */
#unified_approval_table_wrapper .dataTables_filter input {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    margin-left: 0.5rem;
}
#unified_approval_table_wrapper .dataTables_length select {
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 0.35rem 0.75rem;
    margin: 0 0.5rem;
}
#unified_approval_table_wrapper .dataTables_paginate .paginate_button {
    border-radius: 6px !important;
    margin: 0 2px;
}
#unified_approval_table_wrapper .dataTables_paginate .paginate_button.current {
    background: #1e3a5f !important;
    border-color: #1e3a5f !important;
    color: #fff !important;
}
#unified_approval_table_wrapper .dataTables_paginate .paginate_button:hover {
    background: #3b82f6 !important;
    border-color: #3b82f6 !important;
    color: #fff !important;
}
#unified_approval_table_wrapper .dt-buttons {
    margin-bottom: 1rem;
}
#unified_approval_table_wrapper .dt-buttons .btn {
    margin-right: 0.5rem;
    border-radius: 6px;
}
#unified_approval_table_wrapper .dataTables_info {
    padding-top: 1rem;
    color: #6b7280;
}
</style>
