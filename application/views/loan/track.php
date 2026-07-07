<?php
$products = get_all('loan_products');
$officer = get_all('employees');
?>

<style>
.loan-track-container {
    max-width: 1800px;
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

/* Filter Section */
.filter-section {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1.5rem;
}
.filter-title {
    font-weight: 600;
    color: #1e3a5f;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.filter-row {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: flex-end;
}
.filter-group {
    flex: 1;
    min-width: 150px;
}
.filter-group label {
    display: block;
    font-size: 0.8rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.3rem;
}
.filter-group input,
.filter-group select {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.85rem;
    transition: all 0.2s;
}
.filter-group input:focus,
.filter-group select:focus {
    border-color: #3b82f6;
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
.filter-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}
.btn-filter {
    background: #1e3a5f;
    color: #fff;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}
.btn-filter:hover {
    background: #153050;
}
.btn-clear {
    background: #6b7280;
    color: #fff;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}
.btn-clear:hover {
    background: #4b5563;
    color: #fff;
    text-decoration: none;
}

/* Table Styles */
.table-container {
    overflow-x: auto;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
}
.loan-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.85rem;
}
.loan-table thead {
    background: #f1f5f9;
}
.loan-table th {
    padding: 0.75rem 0.75rem;
    text-align: left;
    font-weight: 600;
    color: #1e3a5f;
    border-bottom: 2px solid #e5e7eb;
    white-space: nowrap;
    font-size: 0.8rem;
}
.loan-table td {
    padding: 0.65rem 0.75rem;
    border-bottom: 1px solid #f1f5f9;
    color: #374151;
    font-size: 0.85rem;
}
.loan-table tbody tr:hover {
    background: #f8fafc;
}
.loan-table tbody tr:last-child td {
    border-bottom: none;
}

/* Status Badges */
.status-badge {
    display: inline-block;
    padding: 0.2rem 0.6rem;
    border-radius: 50px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
}
.status-active {
    background: #dcfce7;
    color: #166534;
}
.status-initiated {
    background: #e0e7ff;
    color: #3730a3;
}
.status-recommended {
    background: #fef3c7;
    color: #92400e;
}
.status-approved {
    background: #dbeafe;
    color: #1e40af;
}
.status-rejected {
    background: #fee2e2;
    color: #991b1b;
}
.status-closed {
    background: #f3f4f6;
    color: #374151;
}
.status-written-off {
    background: #fce7f3;
    color: #9d174d;
}
.status-deleted, .status-archived {
    background: #e5e7eb;
    color: #6b7280;
}

/* Loan Info */
.loan-number {
    font-weight: 600;
    color: #1e3a5f;
}
.loan-product {
    font-size: 0.75rem;
    color: #6b7280;
}
.customer-link {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
}
.customer-link:hover {
    text-decoration: underline;
}
.amount-cell {
    font-weight: 600;
    color: #059669;
    white-space: nowrap;
}
.currency-code {
    color: #6b7280;
    font-size: 0.75rem;
}

/* Action Buttons */
.action-btns {
    display: flex;
    gap: 0.25rem;
    flex-wrap: wrap;
}
.btn-action {
    border: none;
    padding: 0.3rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.2rem;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}
.btn-view {
    background: #3b82f6;
    color: #fff;
}
.btn-view:hover {
    background: #2563eb;
    color: #fff;
    text-decoration: none;
}
.btn-summary {
    background: #8b5cf6;
    color: #fff;
}
.btn-summary:hover {
    background: #7c3aed;
    color: #fff;
    text-decoration: none;
}
.btn-edit {
    background: #f59e0b;
    color: #fff;
}
.btn-edit:hover {
    background: #d97706;
    color: #fff;
    text-decoration: none;
}
.btn-delete {
    background: #dc2626;
    color: #fff;
}
.btn-delete:hover {
    background: #b91c1c;
    color: #fff;
    text-decoration: none;
}
.btn-download {
    background: #059669;
    color: #fff;
}
.btn-download:hover {
    background: #047857;
    color: #fff;
    text-decoration: none;
}

/* Stats Bar */
.stats-bar {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}
.stat-item {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
}
.stat-icon-total {
    background: #dbeafe;
    color: #1e40af;
}
.stat-info h4 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 700;
    color: #1e3a5f;
}
.stat-info p {
    margin: 0;
    font-size: 0.75rem;
    color: #6b7280;
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

/* Responsive */
@media (max-width: 992px) {
    .filter-group {
        min-width: 120px;
    }
}
@media (max-width: 768px) {
    .list-card-header {
        flex-direction: column;
        align-items: flex-start;
    }
    .filter-row {
        flex-direction: column;
    }
    .filter-group {
        width: 100%;
    }
    .stats-bar {
        flex-direction: column;
    }
    .stat-item {
        width: 100%;
    }
}
</style>

<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Loan Tracking</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="<?php echo base_url('loan')?>">Loans</a>
                <span class="breadcrumb-item active">Track Loans</span>
            </nav>
        </div>
    </div>

    <div class="loan-track-container">
        <div class="list-card">
            <div class="list-card-header">
                <h5><i class="fa fa-chart-line mr-2"></i>Loan Applications Tracker</h5>
                <div>
                    <span style="background: rgba(255,255,255,0.2); padding: 0.4rem 0.8rem; border-radius: 50px; font-size: 0.85rem;">
                        <i class="fa fa-file-invoice-dollar mr-1"></i> <?php echo count($loan_data); ?> Loans
                    </span>
                </div>
            </div>
            <div class="list-card-body">
                <!-- Stats Bar -->
                <div class="stats-bar">
                    <div class="stat-item">
                        <div class="stat-icon stat-icon-total">
                            <i class="fa fa-file-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h4><?php echo count($loan_data); ?></h4>
                            <p>Total Loans</p>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="filter-section">
                    <div class="filter-title">
                        <i class="fa fa-filter"></i> Filter Loans
                    </div>
                    <form action="<?php echo base_url('loan/track') ?>" method="get">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label>Loan Product</label>
                                <select name="product">
                                    <option value="All">All Products</option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?php echo $product->loan_product_id; ?>" <?php if($this->input->get('product')==$product->loan_product_id){echo "selected";} ?>><?php echo $product->product_name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label>Status</label>
                                <select name="status">
                                    <option value="All">All Statuses</option>
                                    <option value="INITIATED" <?php if($this->input->get('status')=='INITIATED'){echo "selected";} ?>>Initiated</option>
                                    <option value="RECOMMENDED" <?php if($this->input->get('status')=='RECOMMENDED'){echo "selected";} ?>>Recommended</option>
                                    <option value="APPROVED" <?php if($this->input->get('status')=='APPROVED'){echo "selected";} ?>>Pending Client Signing</option>
                                    <option value="CLIENT_SIGNED" <?php if($this->input->get('status')=='CLIENT_SIGNED'){echo "selected";} ?>>Client Signed</option>
                                    <option value="ACTIVE" <?php if($this->input->get('status')=='ACTIVE'){echo "selected";} ?>>Active (Disbursed)</option>
                                    <option value="CLOSED" <?php if($this->input->get('status')=='CLOSED'){echo "selected";} ?>>Closed</option>
                                    <option value="WRITTEN_OFF" <?php if($this->input->get('status')=='WRITTEN_OFF'){echo "selected";} ?>>Written Off</option>
                                    <option value="REJECTED" <?php if($this->input->get('status')=='REJECTED'){echo "selected";} ?>>Rejected</option>
                                    <option value="DELETED" <?php if($this->input->get('status')=='DELETED'){echo "selected";} ?>>Archived</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label>Officer</label>
                                <select name="user">
                                    <option value="All">All Officers</option>
                                    <?php foreach ($officer as $item): ?>
                                        <option value="<?php echo $item->id; ?>" <?php if($this->input->get('user')==$item->id){echo "selected";} ?>><?php echo $item->Firstname." ".$item->Lastname; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label>From Date</label>
                                <input type="date" name="from" value="<?php echo $this->input->get('from'); ?>">
                            </div>
                            <div class="filter-group">
                                <label>To Date</label>
                                <input type="date" name="to" value="<?php echo $this->input->get('to'); ?>">
                            </div>
                        </div>
                        <div class="filter-buttons mt-3">
                            <button type="submit" name="search" value="filter" class="btn-filter">
                                <i class="fa fa-search"></i> Search
                            </button>
                            <a href="<?php echo base_url('loan/track') ?>" class="btn-clear">
                                <i class="fa fa-times"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Table -->
                <div class="table-container">
                    <table class="loan-table" id="loan-track-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Loan Details</th>
                                <th>Customer</th>
                                <th>Principal</th>
                                <th>Period</th>
                                <th>Interest</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Files</th>
                                <th style="text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($loan_data)): ?>
                            <tr>
                                <td colspan="11" class="empty-state">
                                    <i class="fa fa-file-invoice-dollar"></i>
                                    <p>No loans found</p>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php
                            $n = 1;
                            foreach ($loan_data as $loan):
                                if($loan->customer_type=='group'){
                                    $group = $this->Groups_model->get_by_id($loan->loan_customer);
                                    $customer_name = $group->group_name.' ('.$group->group_code.')';
                                    $preview_url = "Customer_groups/members/";
                                }elseif($loan->customer_type=='individual'){
                                    $indi = $this->Individual_customers_model->get_by_id($loan->loan_customer);
                                    $customer_name = $indi->Firstname.' '.$indi->Lastname.' ('.$indi->ClientId.')';
                                    $preview_url = "Individual_customers/view/";
                                }elseif($loan->customer_type=='institution'){
                                    $inst = get_by_id('corporate_customers','id',$loan->loan_customer);
                                    $customer_name = $inst->EntityName.' - '.$inst->RegistrationNumber.' ('.$inst->entity_type.')';
                                    $preview_url = "Corporate_customers/read/";
                                }
                                $currency = get_by_id('currencies','currency_id',$loan->currency);

                                // Status class
                                $status_class = 'status-initiated';
                                $status = strtoupper($loan->loan_status);
                                if($status == 'ACTIVE') $status_class = 'status-active';
                                elseif($status == 'INITIATED') $status_class = 'status-initiated';
                                elseif($status == 'RECOMMENDED') $status_class = 'status-recommended';
                                elseif($status == 'APPROVED') $status_class = 'status-approved';
                                elseif($status == 'REJECTED') $status_class = 'status-rejected';
                                elseif($status == 'CLOSED') $status_class = 'status-closed';
                                elseif($status == 'WRITTEN_OFF') $status_class = 'status-written-off';
                                elseif($status == 'DELETED' || $status == 'ARCHIVED') $status_class = 'status-archived';
                            ?>
                            <tr>
                                <td><?php echo $n; ?></td>
                                <td>
                                    <div class="loan-number"><?php echo $loan->loan_number; ?></div>
                                    <div class="loan-product"><?php echo $loan->product_name; ?></div>
                                </td>
                                <td>
                                    <a href="<?php echo base_url($preview_url).$loan->loan_customer?>" class="customer-link"><?php echo $customer_name; ?></a>
                                </td>
                                <td class="amount-cell">
                                    <span class="currency-code"><?php echo $currency->currency_code; ?></span>
                                    <?php echo number_format($loan->loan_principal, 2); ?>
                                </td>
                                <td><?php echo $loan->loan_period; ?> <?php echo $loan->period_type; ?></td>
                                <td><?php echo $loan->loan_interest; ?>%</td>
                                <td class="amount-cell">
                                    <span class="currency-code"><?php echo $currency->currency_code; ?></span>
                                    <?php echo number_format($loan->loan_amount_total, 2); ?>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo $status_class; ?>"><?php echo $loan->loan_status; ?></span>
                                </td>
                                <td><?php echo date('d M Y', strtotime($loan->loan_date)); ?></td>
                                <td>
                                    <a href="#" onclick="get_loan_files('<?php echo $loan->loan_id; ?>')" class="btn-action btn-download" title="Download Files">
                                        <i class="fa fa-download"></i>
                                    </a>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <a href="<?php echo base_url('loan/view/').urlencode($loan->loan_id); ?>" class="btn-action btn-view" title="View Summary">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                        <a href="<?php echo base_url('loan/client_summary/').urlencode($loan->loan_id); ?>" class="btn-action btn-summary" title="Client Summary">
                                            <i class="fa fa-user"></i>
                                        </a>
                                        <?php if($loan->loan_status == 'INITIATED' || (isset($loan->sent_back) && $loan->sent_back == 1)): ?>
                                        <a href="<?php echo base_url('loan/edit_single_loan_request/').urlencode($loan->loan_id); ?>" class="btn-action btn-edit" title="Edit Loan">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if($loan->loan_status == 'INITIATED' || $loan->loan_status == 'REJECTED'): ?>
                                        <a href="<?php echo base_url('loan/delete_loan_action/').urlencode($loan->loan_id); ?>" class="btn-action btn-delete" title="Delete Loan" onclick="return confirm('Are you sure you want to permanently delete this loan? This action cannot be undone.')">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php
                            $n++;
                            endforeach;
                            ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
