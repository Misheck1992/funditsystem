<?php
// Default to ZMW (Zambian Kwacha)
$currency_code = isset($currency) && $currency && $currency->currency_code ? $currency->currency_code : 'ZMW';
?>
<style>
/* Summary Card */
.summary-card {
    background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%);
    color: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1rem;
}
.summary-card .summary-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 1rem;
}
.summary-card .summary-item {
    text-align: center;
}
.summary-card .summary-value {
    font-size: 1.25rem;
    font-weight: 700;
    word-break: break-word;
}
.summary-card .summary-label {
    font-size: 0.75rem;
    opacity: 0.8;
    margin-top: 0.25rem;
}

/* Badges */
.type-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 50px;
    font-size: 0.7rem;
    font-weight: 600;
    white-space: nowrap;
}
.type-badge.vehicle { background: #dbeafe; color: #1e40af; }
.type-badge.property { background: #dcfce7; color: #166534; }
.type-badge.equipment { background: #fef3c7; color: #92400e; }
.type-badge.securities { background: #e0e7ff; color: #3730a3; }
.type-badge.cash { background: #d1fae5; color: #065f46; }
.type-badge.guarantee { background: #fce7f3; color: #9d174d; }
.type-badge.other { background: #f3f4f6; color: #374151; }
.status-active { background: #dcfce7; color: #166534; }
.status-pledged { background: #fef3c7; color: #92400e; }
.status-released { background: #dbeafe; color: #1e40af; }
.status-sold { background: #fee2e2; color: #991b1b; }

/* Utilization Bar */
.utilization-bar {
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
    min-width: 60px;
}
.utilization-bar .fill {
    height: 100%;
    border-radius: 4px;
    transition: width 0.3s ease;
}
.utilization-low { background: #22c55e; }
.utilization-medium { background: #f59e0b; }
.utilization-high { background: #ef4444; }

/* Chart Container */
.chart-container {
    background: #fff;
    border-radius: 12px;
    padding: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 1rem;
}

/* Responsive Table Wrapper */
.table-responsive-wrapper {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* Collateral Table */
#collateral-table {
    min-width: 1200px;
    font-size: 0.85rem;
}
#collateral-table th,
#collateral-table td {
    white-space: nowrap;
    padding: 0.5rem 0.4rem;
    vertical-align: middle;
}
#collateral-table .col-name {
    min-width: 150px;
    max-width: 200px;
    white-space: normal;
    word-break: break-word;
}

/* Filter Form */
.filter-row {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    align-items: flex-end;
}
.filter-item {
    flex: 1;
    min-width: 140px;
}
.filter-item label {
    font-size: 0.8rem;
    margin-bottom: 0.25rem;
    display: block;
}
.filter-buttons {
    display: flex;
    gap: 0.25rem;
    flex-wrap: wrap;
}

/* Responsive Breakpoints */
@media (max-width: 1200px) {
    .summary-card .summary-grid {
        grid-template-columns: repeat(3, 1fr);
    }
    .summary-card .summary-value {
        font-size: 1.1rem;
    }
}

@media (max-width: 768px) {
    .summary-card {
        padding: 1rem;
    }
    .summary-card .summary-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
    .summary-card .summary-value {
        font-size: 1rem;
    }
    .summary-card .summary-label {
        font-size: 0.65rem;
    }
    .filter-item {
        min-width: 100%;
    }
    .chart-container {
        padding: 0.75rem;
    }
    #collateral-table {
        font-size: 0.75rem;
    }
    #collateral-table th,
    #collateral-table td {
        padding: 0.4rem 0.3rem;
    }
}

@media (max-width: 480px) {
    .summary-card .summary-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    .summary-card .summary-value {
        font-size: 0.9rem;
    }
    .filter-buttons {
        width: 100%;
        justify-content: space-between;
    }
    .filter-buttons .btn {
        flex: 1;
    }
}
</style>

<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Collateral Report</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">Reports</a>
                <span class="breadcrumb-item active">Collateral Report</span>
            </nav>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-card">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-value"><?php echo number_format($summary['total_count']); ?></div>
                <div class="summary-label">Total Collaterals</div>
            </div>
            <div class="summary-item">
                <div class="summary-value"><?php echo $currency_code; ?> <?php echo number_format($summary['total_market_value'], 2); ?></div>
                <div class="summary-label">Market Value</div>
            </div>
            <div class="summary-item">
                <div class="summary-value"><?php echo $currency_code; ?> <?php echo number_format($summary['total_force_sale_value'], 2); ?></div>
                <div class="summary-label">Force Sale Value</div>
            </div>
            <div class="summary-item">
                <div class="summary-value" style="color: #fca5a5;"><?php echo $currency_code; ?> <?php echo number_format($summary['total_utilized'], 2); ?></div>
                <div class="summary-label">Utilized</div>
            </div>
            <div class="summary-item">
                <div class="summary-value" style="color: #86efac;"><?php echo $currency_code; ?> <?php echo number_format($summary['total_available'], 2); ?></div>
                <div class="summary-label">Available</div>
            </div>
            <div class="summary-item">
                <div class="summary-value"><?php echo $summary['utilization_rate']; ?>%</div>
                <div class="summary-label">Utilization Rate</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body" style="border: 2px #153505 solid; border-radius: 14px;">
            <!-- Filter Form -->
            <form action="<?php echo base_url('reports/collateral_report'); ?>" method="get">
                <fieldset>
                    <legend>Report Filters</legend>
                    <div class="filter-row">
                        <div class="filter-item">
                            <label>Customer Type</label>
                            <select name="customer_type" class="form-control form-control-sm">
                                <option value="">All Types</option>
                                <option value="individual" <?php echo ($filters['customer_type'] == 'individual') ? 'selected' : ''; ?>>Individual</option>
                                <option value="institution" <?php echo ($filters['customer_type'] == 'institution') ? 'selected' : ''; ?>>Corporate</option>
                            </select>
                        </div>
                        <div class="filter-item">
                            <label>Collateral Type</label>
                            <select name="collateral_type" class="form-control form-control-sm">
                                <option value="">All Types</option>
                                <?php foreach ($collateral_types as $type): ?>
                                    <option value="<?php echo $type->collateral_type; ?>" <?php echo ($filters['collateral_type'] == $type->collateral_type) ? 'selected' : ''; ?>><?php echo $type->collateral_type; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-item">
                            <label>Status</label>
                            <select name="status" class="form-control form-control-sm">
                                <option value="">All Status</option>
                                <option value="ACTIVE" <?php echo ($filters['status'] == 'ACTIVE') ? 'selected' : ''; ?>>Active</option>
                                <option value="PLEDGED" <?php echo ($filters['status'] == 'PLEDGED') ? 'selected' : ''; ?>>Pledged</option>
                                <option value="RELEASED" <?php echo ($filters['status'] == 'RELEASED') ? 'selected' : ''; ?>>Released</option>
                                <option value="SOLD" <?php echo ($filters['status'] == 'SOLD') ? 'selected' : ''; ?>>Sold</option>
                            </select>
                        </div>
                        <div class="filter-item">
                            <label>From Date</label>
                            <input type="text" class="form-control form-control-sm dpicker" name="from" value="<?php echo $filters['from_date']; ?>" placeholder="From">
                        </div>
                        <div class="filter-item">
                            <label>To Date</label>
                            <input type="text" class="form-control form-control-sm dpicker" name="to" value="<?php echo $filters['to_date']; ?>" placeholder="To">
                        </div>
                        <div class="filter-item">
                            <label>&nbsp;</label>
                            <div class="filter-buttons">
                                <button type="submit" name="search" value="filter" class="btn btn-primary btn-sm"><i class="fa fa-filter"></i> Filter</button>
                                <button type="submit" name="search" value="pdf" class="btn btn-danger btn-sm"><i class="fa fa-file-pdf"></i> PDF</button>
                                <a href="<?php echo base_url('reports/collateral_report'); ?>" class="btn btn-secondary btn-sm"><i class="fa fa-refresh"></i></a>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </form>

            <hr>

            <!-- Breakdown by Type -->
            <div class="row mb-3">
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="chart-container">
                        <h6><i class="fa fa-pie-chart"></i> By Collateral Type</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th class="text-center">Count</th>
                                        <th class="text-right">Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($summary['by_type'] as $type): ?>
                                    <tr>
                                        <td>
                                            <span class="type-badge <?php echo strtolower(str_replace(' ', '', $type->collateral_type ?? 'other')); ?>">
                                                <?php echo $type->collateral_type ?? 'Unknown'; ?>
                                            </span>
                                        </td>
                                        <td class="text-center"><?php echo $type->count; ?></td>
                                        <td class="text-right"><?php echo $currency_code; ?> <?php echo number_format($type->total_value, 2); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12">
                    <div class="chart-container">
                        <h6><i class="fa fa-chart-bar"></i> By Status</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th class="text-center">Count</th>
                                        <th class="text-right">Percentage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($summary['by_status'] as $status): ?>
                                    <tr>
                                        <td>
                                            <span class="type-badge status-<?php echo strtolower($status->collateral_status ?? 'active'); ?>">
                                                <?php echo $status->collateral_status ?? 'Unknown'; ?>
                                            </span>
                                        </td>
                                        <td class="text-center"><?php echo $status->count; ?></td>
                                        <td class="text-right"><?php echo $summary['total_count'] > 0 ? round(($status->count / $summary['total_count']) * 100, 1) : 0; ?>%</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Collateral List -->
            <h5><i class="fa fa-list"></i> Collateral Details (<?php echo count($collaterals); ?> records)</h5>
            <div class="table-responsive-wrapper">
                <table class="table table-bordered table-striped table-hover" id="collateral-table">
                    <thead style="background: #1e3a5f; color: #fff;">
                        <tr>
                            <th>#</th>
                            <th class="col-name">Collateral Name</th>
                            <th>Type</th>
                            <th>Serial No.</th>
                            <th>Customer</th>
                            <th>Cust. Type</th>
                            <th class="text-right">Market Value</th>
                            <th class="text-right">Force Sale</th>
                            <th class="text-right">Utilized</th>
                            <th class="text-right">Available</th>
                            <th>Util %</th>
                            <th class="text-center">Loans</th>
                            <th>Status</th>
                            <th>Date Added</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $n = 1; foreach ($collaterals as $c): ?>
                        <tr>
                            <td><?php echo $n++; ?></td>
                            <td class="col-name">
                                <strong><?php echo htmlspecialchars($c->collateral_name); ?></strong>
                                <?php if (!empty($c->collateral_desc)): ?>
                                    <br><small class="text-muted"><?php echo htmlspecialchars(substr($c->collateral_desc, 0, 40)); ?><?php echo strlen($c->collateral_desc) > 40 ? '...' : ''; ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="type-badge <?php echo strtolower(str_replace(' ', '', $c->collateral_type ?? 'other')); ?>">
                                    <?php echo $c->collateral_type; ?>
                                </span>
                            </td>
                            <td><?php echo $c->collateral_serial ?: '-'; ?></td>
                            <td>
                                <?php
                                $customer_url = $c->customer_type == 'individual'
                                    ? base_url('individual_customers/view/' . $c->customer_id)
                                    : base_url('Corporate_customers/read/' . $c->customer_id);
                                ?>
                                <a href="<?php echo $customer_url; ?>"><?php echo htmlspecialchars(substr($c->customer_name, 0, 20)); ?><?php echo strlen($c->customer_name) > 20 ? '...' : ''; ?></a>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo $c->customer_type == 'individual' ? 'info' : 'warning'; ?>">
                                    <?php echo $c->customer_type == 'individual' ? 'IND' : 'CORP'; ?>
                                </span>
                            </td>
                            <td class="text-right"><?php echo $currency_code; ?> <?php echo number_format($c->market_value, 2); ?></td>
                            <td class="text-right"><?php echo $currency_code; ?> <?php echo number_format($c->force_sale_value, 2); ?></td>
                            <td class="text-right" style="color: #dc2626;"><?php echo $currency_code; ?> <?php echo number_format($c->total_utilized, 2); ?></td>
                            <td class="text-right" style="color: #059669; font-weight: 600;"><?php echo $currency_code; ?> <?php echo number_format($c->available_balance, 2); ?></td>
                            <td>
                                <?php
                                $util_class = 'utilization-low';
                                if ($c->utilization_percent > 75) $util_class = 'utilization-high';
                                elseif ($c->utilization_percent > 50) $util_class = 'utilization-medium';
                                ?>
                                <div class="utilization-bar">
                                    <div class="fill <?php echo $util_class; ?>" style="width: <?php echo min($c->utilization_percent, 100); ?>%;"></div>
                                </div>
                                <small><?php echo $c->utilization_percent; ?>%</small>
                            </td>
                            <td class="text-center">
                                <?php if ($c->active_loans_count > 0): ?>
                                    <span class="badge badge-primary"><?php echo $c->active_loans_count; ?></span>
                                <?php else: ?>
                                    <span class="text-muted">0</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="type-badge status-<?php echo strtolower($c->collateral_status ?? 'active'); ?>">
                                    <?php echo $c->collateral_status ?? 'ACTIVE'; ?>
                                </span>
                            </td>
                            <td><?php echo !empty($c->added_at) ? date('d M Y', strtotime($c->added_at)) : '-'; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot style="background: #f8fafc; font-weight: 600;">
                        <tr>
                            <td colspan="6" class="text-right">TOTALS:</td>
                            <td class="text-right"><?php echo $currency_code; ?> <?php echo number_format($summary['total_market_value'], 2); ?></td>
                            <td class="text-right"><?php echo $currency_code; ?> <?php echo number_format($summary['total_force_sale_value'], 2); ?></td>
                            <td class="text-right" style="color: #dc2626;"><?php echo $currency_code; ?> <?php echo number_format($summary['total_utilized'], 2); ?></td>
                            <td class="text-right" style="color: #059669;"><?php echo $currency_code; ?> <?php echo number_format($summary['total_available'], 2); ?></td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
