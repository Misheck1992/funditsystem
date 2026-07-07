<?php
$products = get_all('geo_countries');
$officer = get_all('employees');
?>

<style>
.customer-list-container {
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
.list-card-header .header-actions {
    display: flex;
    gap: 0.5rem;
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
.filter-section .filter-title {
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
.btn-export {
    background: #059669;
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
.btn-export:hover {
    background: #047857;
}
.btn-export-pdf {
    background: #dc2626;
}
.btn-export-pdf:hover {
    background: #b91c1c;
}

/* Table Styles */
.table-container {
    overflow-x: auto;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
}
.customer-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.85rem;
}
.customer-table thead {
    background: #f1f5f9;
}
.customer-table th {
    padding: 0.75rem 1rem;
    text-align: left;
    font-weight: 600;
    color: #1e3a5f;
    border-bottom: 2px solid #e5e7eb;
    white-space: nowrap;
}
.customer-table td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f1f5f9;
    color: #374151;
}
.customer-table tbody tr:hover {
    background: #f8fafc;
}
.customer-table tbody tr:last-child td {
    border-bottom: none;
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
.status-approved {
    background: #dcfce7;
    color: #166534;
}
.status-not-approved {
    background: #fef3c7;
    color: #92400e;
}
.status-rejected {
    background: #fee2e2;
    color: #991b1b;
}
.status-archived {
    background: #f3f4f6;
    color: #6b7280;
}

/* Action Buttons */
.btn-view {
    background: #3b82f6;
    color: #fff;
    border: none;
    padding: 0.4rem 0.75rem;
    border-radius: 5px;
    font-size: 0.8rem;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    transition: all 0.2s;
}
.btn-view:hover {
    background: #2563eb;
    color: #fff;
    text-decoration: none;
}

/* Footer Section */
.list-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid #e5e7eb;
}
.record-count {
    background: #1e3a5f;
    color: #fff;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.85rem;
    font-weight: 500;
}
.pagination-wrapper {
    display: flex;
    gap: 0.25rem;
}
.pagination-wrapper a,
.pagination-wrapper span {
    padding: 0.4rem 0.75rem;
    border: 1px solid #e5e7eb;
    border-radius: 5px;
    font-size: 0.85rem;
    color: #374151;
    text-decoration: none;
    transition: all 0.2s;
}
.pagination-wrapper a:hover {
    background: #f1f5f9;
    border-color: #d1d5db;
}
.pagination-wrapper .active,
.pagination-wrapper strong {
    background: #1e3a5f;
    color: #fff;
    border-color: #1e3a5f;
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
    .filter-buttons {
        width: 100%;
        justify-content: flex-start;
    }
    .list-footer {
        flex-direction: column;
        align-items: flex-start;
    }
}

/* Customer Info Cell */
.customer-name {
    font-weight: 600;
    color: #1e3a5f;
}
.customer-id {
    font-size: 0.75rem;
    color: #6b7280;
}
</style>

<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Individual Customers</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <span class="breadcrumb-item active">Customers</span>
            </nav>
        </div>
    </div>

    <div class="customer-list-container">
        <div class="list-card">
            <div class="list-card-header">
                <h5><i class="fa fa-users mr-2"></i>Customer Directory</h5>
                <div class="header-actions">
                    <?php if(has_access('Individual_customers/create')): ?>
                    <a href="<?php echo base_url('individual_customers/create')?>" class="btn btn-sm" style="background: #059669; color: #fff; border-radius: 6px; padding: 0.5rem 1rem; font-weight: 500;">
                        <i class="fa fa-plus mr-1"></i> New Customer
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="list-card-body">
                <!-- Filter Section -->
                <div class="filter-section">
                    <div class="filter-title">
                        <i class="fa fa-filter"></i> Filter Customers
                    </div>
                    <form action="<?php echo base_url('Individual_customers/index') ?>" method="get">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label>Search Keyword</label>
                                <input type="text" name="q" placeholder="Name, ID, Phone..." value="<?php echo $this->input->get('q'); ?>">
                            </div>
                            <div class="filter-group">
                                <label>Country</label>
                                <select name="country">
                                    <option value="">All Countries</option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?php echo $product->code; ?>" <?php if($this->input->get('country')==$product->code){ echo "selected"; }?>><?php echo $product->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label>Gender</label>
                                <select name="gender">
                                    <option value="">All Genders</option>
                                    <option value="MALE" <?php if($this->input->get('gender')=='MALE'){echo "selected";} ?>>Male</option>
                                    <option value="FEMALE" <?php if($this->input->get('gender')=='FEMALE'){echo "selected";} ?>>Female</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label>Status</label>
                                <select name="status">
                                    <option value="">All Statuses</option>
                                    <option value="Approved" <?php if($this->input->get('status')=='Approved'){echo "selected";} ?>>Approved</option>
                                    <option value="Not Approved" <?php if($this->input->get('status')=='Not Approved'){echo "selected";} ?>>Not Approved</option>
                                    <option value="Rejected" <?php if($this->input->get('status')=='Rejected'){echo "selected";} ?>>Rejected</option>
                                    <option value="Archived" <?php if($this->input->get('status')=='Archived'){echo "selected";} ?>>Archived</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <label>Officer</label>
                                <select name="user">
                                    <option value="">All Officers</option>
                                    <?php foreach ($officer as $item): ?>
                                        <option value="<?php echo $item->id; ?>" <?php if($this->input->get('user')==$item->id){echo "selected";} ?>><?php echo $item->Firstname." ".$item->Lastname ?></option>
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
                            <button type="submit" name="search" value="export excel" class="btn-export">
                                <i class="fa fa-file-excel"></i> Excel
                            </button>
                            <button type="submit" name="search" value="export pdf" class="btn-export btn-export-pdf">
                                <i class="fa fa-file-pdf"></i> PDF
                            </button>
                            <a href="<?php echo base_url('Individual_customers/index') ?>" class="btn-filter" style="background: #6b7280; text-decoration: none;">
                                <i class="fa fa-times"></i> Clear
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Table -->
                <div class="table-container">
                    <table class="customer-table" id="individual-customers-table">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Gender</th>
                                <th>Date of Birth</th>
                                <th>Contact</th>
                                <th>Marital Status</th>
                                <th>Officer</th>
                                <th>Status</th>
                                <th>Registered</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($individual_customers_data)): ?>
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 3rem; color: #6b7280;">
                                    <i class="fa fa-users fa-3x mb-3" style="opacity: 0.3;"></i>
                                    <p style="margin: 0;">No customers found</p>
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($individual_customers_data as $individual_customers): ?>
                            <tr>
                                <td>
                                    <div class="customer-name"><?php echo $individual_customers->Title.' '.$individual_customers->cfname.' '.$individual_customers->cmname.' '.$individual_customers->clname; ?></div>
                                    <div class="customer-id"><?php echo $individual_customers->ClientId; ?></div>
                                </td>
                                <td><?php echo $individual_customers->cgender; ?></td>
                                <td><?php echo $individual_customers->cdob; ?></td>
                                <td>
                                    <div><?php echo $individual_customers->cphonee; ?></div>
                                    <div style="font-size: 0.75rem; color: #6b7280;"><?php echo $individual_customers->cemail; ?></div>
                                </td>
                                <td><?php echo $individual_customers->cmarital; ?></td>
                                <td><?php echo $individual_customers->efname.' '.$individual_customers->elname; ?></td>
                                <td>
                                    <?php
                                    $status = $individual_customers->approval_status;
                                    $status_class = 'status-archived';
                                    if($status == 'Approved') $status_class = 'status-approved';
                                    elseif($status == 'Not Approved') $status_class = 'status-not-approved';
                                    elseif($status == 'Rejected') $status_class = 'status-rejected';
                                    ?>
                                    <span class="status-badge <?php echo $status_class; ?>"><?php echo $status; ?></span>
                                </td>
                                <td><?php echo date('d M Y', strtotime($individual_customers->CreatedOn)); ?></td>
                                <td style="text-align: center;">
                                    <a href="<?php echo base_url('individual_customers/view/'.$individual_customers->id)?>" class="btn-view">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Footer -->
                <div class="list-footer">
                    <div>
                        <span class="record-count">
                            <i class="fa fa-database mr-1"></i> Total Records: <?php echo $total_rows; ?>
                        </span>
                    </div>
                    <div class="pagination-wrapper">
                        <?php echo $pagination; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
