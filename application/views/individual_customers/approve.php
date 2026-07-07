<style>
.approve-list-container {
    max-width: 1600px;
    margin: 0 auto;
}
.list-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    overflow: hidden;
}
.list-card-header {
    background: #1e3a5f;
    color: #fff;
    padding: 1rem 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}
.list-card-header h5 {
    margin: 0;
    font-weight: 600;
    font-size: 1.1rem;
    color: #fff;
}
.list-card-body {
    padding: 1.5rem;
}

/* Info Banner */
.info-banner {
    background: #fef3c7;
    border: 1px solid #f59e0b;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.info-banner i {
    color: #d97706;
    font-size: 1.25rem;
}
.info-banner p {
    margin: 0;
    color: #92400e;
    font-size: 0.9rem;
}

/* Table Styles */
.table-container {
    overflow-x: auto;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
}
.approve-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.85rem;
}
.approve-table thead {
    background: #f1f5f9;
}
.approve-table th {
    padding: 0.75rem 1rem;
    text-align: left;
    font-weight: 600;
    color: #1e3a5f;
    border-bottom: 2px solid #e5e7eb;
    white-space: nowrap;
}
.approve-table td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    color: #374151;
}
.approve-table tbody tr:hover {
    background: #f8fafc;
}
.approve-table tbody tr:last-child td {
    border-bottom: none;
}

/* Checkbox Styling */
.custom-checkbox {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #1e3a5f;
}
.check-all-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    font-weight: 600;
    color: #1e3a5f;
    font-size: 0.8rem;
}

/* Status Badge */
.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}
.status-pending {
    background: #fef3c7;
    color: #92400e;
}

/* Customer Info */
.customer-name {
    font-weight: 600;
    color: #1e3a5f;
}
.customer-id {
    font-size: 0.75rem;
    color: #6b7280;
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
}
.btn-approve {
    background: #059669;
    color: #fff;
}
.btn-approve:hover {
    background: #047857;
    color: #fff;
    text-decoration: none;
}
.btn-reject {
    background: #dc2626;
    color: #fff;
}
.btn-reject:hover {
    background: #b91c1c;
    color: #fff;
    text-decoration: none;
}

/* Bulk Action Buttons */
.bulk-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
    flex-wrap: wrap;
}
.btn-bulk {
    border: none;
    padding: 0.6rem 1.25rem;
    border-radius: 6px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}
.btn-bulk-approve {
    background: #059669;
    color: #fff;
}
.btn-bulk-approve:hover {
    background: #047857;
}
.btn-bulk-reject {
    background: #dc2626;
    color: #fff;
}
.btn-bulk-reject:hover {
    background: #b91c1c;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem;
    color: #6b7280;
}
.empty-state i {
    font-size: 3rem;
    opacity: 0.3;
    margin-bottom: 1rem;
}
.empty-state p {
    margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .list-card-header {
        flex-direction: column;
        align-items: flex-start;
    }
    .bulk-actions {
        flex-direction: column;
    }
    .btn-bulk {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Customer Approvals</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="<?php echo base_url('individual_customers')?>">Customers</a>
                <span class="breadcrumb-item active">Pending Approvals</span>
            </nav>
        </div>
    </div>

    <div class="approve-list-container">
        <div class="list-card">
            <div class="list-card-header">
                <h5><i class="fa fa-user-check mr-2"></i>Pending Customer Approvals</h5>
                <div>
                    <span style="background: rgba(255,255,255,0.2); padding: 0.4rem 0.8rem; border-radius: 50px; font-size: 0.85rem;">
                        <i class="fa fa-clock mr-1"></i> <?php echo count($individual_customers_data); ?> Pending
                    </span>
                </div>
            </div>
            <div class="list-card-body">
                <?php if(!empty($individual_customers_data)): ?>
                <!-- Info Banner -->
                <div class="info-banner">
                    <i class="fa fa-info-circle"></i>
                    <p>Review customer details carefully before approving or rejecting. You can select multiple customers using checkboxes for bulk actions.</p>
                </div>

                <form name="frmUser" method="post" action="" id="approvalForm">
                    <!-- Table -->
                    <div class="table-container">
                        <table class="approve-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">
                                        <label class="check-all-label">
                                            <input type="checkbox" id="checkAll" class="custom-checkbox" onclick="toggleCheckAll(this)">
                                            All
                                        </label>
                                    </th>
                                    <th>Customer</th>
                                    <th>Gender</th>
                                    <th>Date of Birth</th>
                                    <th>Status</th>
                                    <th>Registered</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($individual_customers_data as $individual_customers): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="users[]" value="<?php echo $individual_customers->id ?>" class="custom-checkbox user-checkbox">
                                    </td>
                                    <td>
                                        <div class="customer-name"><?php echo $individual_customers->Title.' '.$individual_customers->Firstname.' '.$individual_customers->Middlename.' '.$individual_customers->Lastname; ?></div>
                                        <div class="customer-id"><?php echo $individual_customers->ClientId; ?></div>
                                    </td>
                                    <td><?php echo $individual_customers->Gender; ?></td>
                                    <td><?php echo date('d M Y', strtotime($individual_customers->DateOfBirth)); ?></td>
                                    <td>
                                        <span class="status-badge status-pending"><?php echo $individual_customers->approval_status; ?></span>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($individual_customers->CreatedOn)); ?></td>
                                    <td style="text-align: center;">
                                        <a href="<?php echo base_url('individual_customers/view/'.$individual_customers->id)?>" class="btn-action" style="background: #3b82f6; color: #fff;">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                        <a href="<?php echo base_url('individual_customers/approval_action/'.$individual_customers->id)?>" class="btn-action btn-approve" onclick="return confirm('Are you sure you want to approve this customer?')">
                                            <i class="fa fa-check"></i> Approve
                                        </a>
                                        <a href="<?php echo base_url('individual_customers/reject_action/'.$individual_customers->id)?>" class="btn-action btn-reject" onclick="return confirm('Are you sure you want to reject this customer?')">
                                            <i class="fa fa-times"></i> Reject
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Bulk Actions -->
                    <div class="bulk-actions">
                        <button type="button" class="btn-bulk btn-bulk-approve" onclick="approveSelected()">
                            <i class="fa fa-check-double"></i> Approve Selected
                        </button>
                        <button type="button" class="btn-bulk btn-bulk-reject" onclick="rejectSelected()">
                            <i class="fa fa-times-circle"></i> Reject Selected
                        </button>
                        <span style="color: #6b7280; font-size: 0.85rem; display: flex; align-items: center; margin-left: auto;">
                            <i class="fa fa-info-circle mr-1"></i> <span id="selectedCount">0</span> customer(s) selected
                        </span>
                    </div>
                </form>
                <?php else: ?>
                <!-- Empty State -->
                <div class="empty-state">
                    <i class="fa fa-check-circle"></i>
                    <p>No customers pending approval</p>
                    <p style="font-size: 0.85rem; margin-top: 0.5rem;">All customers have been reviewed.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle all checkboxes
function toggleCheckAll(checkbox) {
    var checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(function(cb) {
        cb.checked = checkbox.checked;
    });
    updateSelectedCount();
}

// Update selected count
function updateSelectedCount() {
    var count = document.querySelectorAll('.user-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = count;
}

// Add event listeners to individual checkboxes
document.addEventListener('DOMContentLoaded', function() {
    var checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(function(cb) {
        cb.addEventListener('change', function() {
            updateSelectedCount();
            // Update "Check All" state
            var allChecked = document.querySelectorAll('.user-checkbox:checked').length === checkboxes.length;
            document.getElementById('checkAll').checked = allChecked;
        });
    });
});

// Approve selected customers
function approveSelected() {
    var selected = document.querySelectorAll('.user-checkbox:checked');
    if (selected.length === 0) {
        alert('Please select at least one customer to approve.');
        return;
    }
    if (confirm('Are you sure you want to approve ' + selected.length + ' customer(s)?')) {
        var form = document.getElementById('approvalForm');
        form.action = '<?php echo base_url('individual_customers/bulk_approve')?>';
        form.submit();
    }
}

// Reject selected customers
function rejectSelected() {
    var selected = document.querySelectorAll('.user-checkbox:checked');
    if (selected.length === 0) {
        alert('Please select at least one customer to reject.');
        return;
    }
    if (confirm('Are you sure you want to reject ' + selected.length + ' customer(s)?')) {
        var form = document.getElementById('approvalForm');
        form.action = '<?php echo base_url('individual_customers/bulk_reject')?>';
        form.submit();
    }
}

// Legacy function support
function checkAll(ele) {
    toggleCheckAll(ele);
}
function approve_all() {
    approveSelected();
}
function reject() {
    rejectSelected();
}
</script>
