<?php
$users = get_all('employees');
$products = get_all('loan_products');
$settings = get_by_id('settings', 'settings_id', '1');
$currency = $settings ? $settings->currency : 'ZMW';
?>

<style>
/* Stats Strip */
.report-stats {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 20px;
}
.stat-box {
    flex: 1;
    min-width: 200px;
    background: #fff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border-left: 4px solid;
}
.stat-box.active { border-left-color: #059669; }
.stat-box.closed { border-left-color: #6b7280; }
.stat-box.written-off { border-left-color: #dc2626; }
.stat-box .stat-title {
    font-size: 0.85rem;
    color: #6b7280;
    font-weight: 500;
    margin-bottom: 8px;
}
.stat-box .stat-amount {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e3a5f;
}
.stat-box .stat-count {
    font-size: 0.8rem;
    color: #9ca3af;
    margin-top: 5px;
}

/* Filter Section */
.filter-card {
    background: #fff;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.filter-card h5 {
    margin: 0 0 15px 0;
    color: #1e3a5f;
    font-weight: 600;
}
.filter-row {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
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
    margin-bottom: 5px;
}
.filter-group select,
.filter-group input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.9rem;
}
.filter-buttons {
    display: flex;
    gap: 10px;
}
.btn-filter {
    background: #1e3a5f;
    color: #fff;
    border: none;
    padding: 8px 20px;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
}
.btn-filter:hover {
    background: #153050;
}
.btn-export {
    background: #fff;
    border: 1px solid #d1d5db;
    padding: 8px 15px;
    border-radius: 6px;
    cursor: pointer;
}
.btn-export:hover {
    background: #f3f4f6;
}
.btn-export.pdf { color: #dc2626; }
.btn-export.excel { color: #059669; }

/* Table Card */
.table-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    overflow: hidden;
}
.table-card-header {
    background: #1e3a5f;
    color: #fff;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.table-card-header h5 {
    margin: 0;
    font-weight: 600;
}
.table-card-body {
    padding: 20px;
}

/* Status badges */
.status-badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}
.status-badge.active { background: #d1fae5; color: #065f46; }
.status-badge.closed { background: #e5e7eb; color: #374151; }
.status-badge.written-off { background: #fee2e2; color: #991b1b; }

@media (max-width: 768px) {
    .stat-box { min-width: 100%; }
    .filter-group { min-width: 100%; }
}
</style>

<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Portfolio Report</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">Reports</a>
                <span class="breadcrumb-item active">Portfolio Report</span>
            </nav>
        </div>
    </div>

    <!-- Stats Section -->
    <?php if(isset($stats)): ?>
    <div class="report-stats">
        <div class="stat-box active">
            <div class="stat-title"><i class="fa fa-check-circle"></i> Active Loans (Disbursed)</div>
            <div class="stat-amount"><?php echo $currency; ?> <?php echo number_format($stats['active']['total'], 2); ?></div>
            <div class="stat-count"><?php echo $stats['active']['count']; ?> Loans</div>
        </div>
        <div class="stat-box closed">
            <div class="stat-title"><i class="fa fa-lock"></i> Closed Loans</div>
            <div class="stat-amount"><?php echo $currency; ?> <?php echo number_format($stats['closed']['total'], 2); ?></div>
            <div class="stat-count"><?php echo $stats['closed']['count']; ?> Loans</div>
        </div>
        <div class="stat-box written-off">
            <div class="stat-title"><i class="fa fa-times-circle"></i> Written Off</div>
            <div class="stat-amount"><?php echo $currency; ?> <?php echo number_format($stats['written_off']['total'], 2); ?></div>
            <div class="stat-count"><?php echo $stats['written_off']['count']; ?> Loans</div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Filter Section -->
    <div class="filter-card">
        <h5><i class="fa fa-filter"></i> Filter & Export</h5>
        <form action="<?php echo base_url('loan/loan_report_search') ?>" method="get">
            <div class="filter-row">
                <div class="filter-group">
                    <label>Officer</label>
                    <select name="user">
                        <option value="All">All Officers</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user->id;?>" <?php if($user->id==$this->input->get('user')){echo 'selected';}?>><?php echo $user->Firstname." ".$user->Lastname;?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Product</label>
                    <select name="product">
                        <option value="All">All Products</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?php echo $product->loan_product_id;?>" <?php if($product->loan_product_id==$this->input->get('product')){echo 'selected';}?>><?php echo $product->product_name;?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="All">All Status</option>
                        <option value="ACTIVE" <?php if($this->input->get('status')=="ACTIVE"){echo 'selected';}?>>Active</option>
                        <option value="CLOSED" <?php if($this->input->get('status')=="CLOSED"){echo 'selected';}?>>Closed</option>
                        <option value="WRITTEN_OFF" <?php if($this->input->get('status')=="WRITTEN_OFF"){echo 'selected';}?>>Written Off</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Date From</label>
                    <input type="date" name="from" value="<?php echo $this->input->get('from')?>">
                </div>
                <div class="filter-group">
                    <label>Date To</label>
                    <input type="date" name="to" value="<?php echo $this->input->get('to')?>">
                </div>
                <div class="filter-buttons">
                    <button type="submit" name="search" value="filter" class="btn-filter">
                        <i class="fa fa-search"></i> Filter
                    </button>
                    <button type="submit" name="search" value="pdf" class="btn-export pdf">
                        <i class="fa fa-file-pdf"></i> PDF
                    </button>
                    <a href="<?php echo base_url('loan/loan_report'); ?>" class="btn-export">
                        <i class="fa fa-refresh"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="table-card">
        <div class="table-card-header">
            <h5><i class="fa fa-list"></i> Loan Portfolio</h5>
            <span><?php echo count($loan_data); ?> Records</span>
        </div>
        <div class="table-card-body">
            <table id="portfolio_table" class="table table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Loan Number</th>
                        <th>Product</th>
                        <th>Customer</th>
                        <th>Loan Date</th>
                        <th>Principal</th>
                        <th>Period</th>
                        <th>Interest</th>
                        <th>Total Amount</th>
                        <th>Officer</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $n = 1;
                foreach ($loan_data as $loan) {
                    // Get customer info
                    $customer_name = '';
                    $customer_link = '#';

                    if (isset($loan->customer_type) && $loan->customer_type == 'group') {
                        $custgroup = get_by_id('groups', 'group_id', $loan->loan_customer);
                        if ($custgroup) {
                            $customer_name = $custgroup->group_name;
                            $customer_link = base_url('groups/view/' . $custgroup->group_id);
                        }
                    } elseif (isset($loan->customer_type) && ($loan->customer_type == 'corporate' || $loan->customer_type == 'institution')) {
                        $corp = get_by_id('corporate_customers', 'id', $loan->loan_customer);
                        if ($corp) {
                            $customer_name = $corp->EntityName;
                            $customer_link = base_url('corporate_customers/read/' . $corp->id);
                        }
                    } else {
                        $indiv = get_by_id('individual_customers', 'id', $loan->loan_customer);
                        if ($indiv) {
                            $customer_name = $indiv->Firstname . ' ' . $indiv->Lastname;
                            $customer_link = base_url('individual_customers/view/' . $indiv->id);
                        }
                    }

                    // Status badge class
                    $status_class = 'active';
                    if ($loan->loan_status == 'CLOSED') $status_class = 'closed';
                    if ($loan->loan_status == 'WRITTEN_OFF') $status_class = 'written-off';
                ?>
                    <tr>
                        <td><?php echo $n; ?></td>
                        <td><strong><?php echo $loan->loan_number; ?></strong></td>
                        <td><?php echo isset($loan->product_name) ? $loan->product_name : ''; ?></td>
                        <td><a href="<?php echo $customer_link; ?>"><?php echo $customer_name; ?></a></td>
                        <td><?php echo date('d M Y', strtotime($loan->loan_date)); ?></td>
                        <td><?php echo $currency; ?> <?php echo number_format($loan->loan_principal, 2); ?></td>
                        <td><?php echo $loan->loan_period; ?> <?php echo isset($loan->period_type) ? $loan->period_type : ''; ?></td>
                        <td><?php echo $loan->loan_interest; ?>%</td>
                        <td><?php echo $currency; ?> <?php echo number_format($loan->loan_amount_total, 2); ?></td>
                        <td><?php echo (isset($loan->efname) ? $loan->efname : '') . ' ' . (isset($loan->elname) ? $loan->elname : ''); ?></td>
                        <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $loan->loan_status; ?></span></td>
                        <td>
                            <a href="<?php echo base_url('loan/view/' . $loan->loan_id); ?>" class="btn btn-sm btn-primary">
                                <i class="fa fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php
                    $n++;
                }
                ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" style="text-align: right;"><strong>Totals:</strong></th>
                        <th><?php echo $currency; ?> <?php echo number_format(array_sum(array_column($loan_data, 'loan_principal')), 2); ?></th>
                        <th></th>
                        <th></th>
                        <th><?php echo $currency; ?> <?php echo number_format(array_sum(array_column($loan_data, 'loan_amount_total')), 2); ?></th>
                        <th colspan="3"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Check if DataTable is already initialized
    if ($.fn.DataTable.isDataTable('#portfolio_table')) {
        $('#portfolio_table').DataTable().destroy();
    }

    var table = $('#portfolio_table').DataTable({
        "processing": false,
        "serverSide": false, // Client-side processing
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "order": [[4, "desc"]], // Order by loan date descending
        "dom": 'Blfrtip',
        "buttons": [
            {
                extend: 'copy',
                text: '<i class="fa fa-copy"></i> Copy',
                className: 'btn btn-secondary btn-sm'
            },
            {
                extend: 'excel',
                text: '<i class="fa fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                exportOptions: { columns: [0,1,2,3,4,5,6,7,8,9,10] }
            },
            {
                extend: 'pdf',
                text: '<i class="fa fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                exportOptions: { columns: [0,1,2,3,4,5,6,7,8,9,10] },
                orientation: 'landscape',
                pageSize: 'A4'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Print',
                className: 'btn btn-info btn-sm',
                exportOptions: { columns: [0,1,2,3,4,5,6,7,8,9,10] }
            }
        ],
        "language": {
            "search": "Quick Search:",
            "lengthMenu": "Show _MENU_ entries per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ loans",
            "infoEmpty": "No loans available",
            "infoFiltered": "(filtered from _MAX_ total loans)",
            "zeroRecords": "No matching loans found",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        },
        "columnDefs": [
            { "orderable": false, "targets": [11] },
            { "className": "text-center", "targets": [0, 6, 7, 10, 11] }
        ],
        "initComplete": function() {
            // Add status filter dropdown to DataTable
            var statusColumn = this.api().column(10);
            var statusSelect = $('<select class="form-control form-control-sm ml-2" style="width: auto; display: inline-block;"><option value="">All Status</option></select>')
                .appendTo($('#portfolio_table_filter'))
                .on('change', function() {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());
                    statusColumn.search(val ? '^' + val + '$' : '', true, false).draw();
                });

            statusColumn.data().unique().sort().each(function(d) {
                // Extract text from HTML if needed
                var text = $('<div>').html(d).text().trim();
                if (text) {
                    statusSelect.append('<option value="' + text + '">' + text + '</option>');
                }
            });
        }
    });
});
</script>

<style>
/* DataTable overrides */
#portfolio_table_wrapper {
    padding: 10px 0;
}
#portfolio_table_wrapper .dt-buttons {
    margin-bottom: 15px;
    float: left;
}
#portfolio_table_wrapper .dt-buttons .btn {
    margin-right: 5px;
    border-radius: 4px;
    font-size: 12px;
    padding: 6px 12px;
}
#portfolio_table_wrapper .dataTables_length {
    float: left;
    margin-right: 20px;
}
#portfolio_table_wrapper .dataTables_filter {
    float: right;
    margin-bottom: 15px;
}
#portfolio_table_wrapper .dataTables_filter input {
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 6px 12px;
    margin-left: 8px;
    width: 200px;
}
#portfolio_table_wrapper .dataTables_length select {
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 4px 8px;
    margin: 0 8px;
}
#portfolio_table_wrapper .dataTables_info {
    padding-top: 15px;
    color: #6b7280;
    clear: both;
}
#portfolio_table_wrapper .dataTables_paginate {
    padding-top: 15px;
    float: right;
}
#portfolio_table_wrapper .dataTables_paginate .paginate_button {
    padding: 6px 12px;
    margin: 0 2px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    background: #fff;
    color: #374151 !important;
    cursor: pointer;
}
#portfolio_table_wrapper .dataTables_paginate .paginate_button:hover {
    background: #f3f4f6 !important;
    border-color: #9ca3af;
    color: #1e3a5f !important;
}
#portfolio_table_wrapper .dataTables_paginate .paginate_button.current {
    background: #1e3a5f !important;
    border-color: #1e3a5f !important;
    color: #fff !important;
}
#portfolio_table_wrapper .dataTables_paginate .paginate_button.disabled {
    color: #9ca3af !important;
    cursor: not-allowed;
    background: #f9fafb;
}
#portfolio_table_wrapper .dataTables_paginate .paginate_button.disabled:hover {
    background: #f9fafb !important;
    border-color: #d1d5db;
}
/* Clear floats */
#portfolio_table_wrapper::after {
    content: "";
    display: table;
    clear: both;
}
/* Responsive pagination */
@media (max-width: 768px) {
    #portfolio_table_wrapper .dataTables_length,
    #portfolio_table_wrapper .dataTables_filter {
        float: none;
        text-align: center;
        margin-bottom: 10px;
    }
    #portfolio_table_wrapper .dt-buttons {
        float: none;
        text-align: center;
    }
    #portfolio_table_wrapper .dataTables_info,
    #portfolio_table_wrapper .dataTables_paginate {
        float: none;
        text-align: center;
    }
}
</style>
