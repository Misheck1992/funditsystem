<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="fd-page">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h1 class="page-title"><?php echo $page_title; ?></h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo site_url('Fixed_deposits'); ?>">Fixed Deposits</a></li>
                    <li class="breadcrumb-item active">Report</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Filters -->
    <div class="fd-card no-print">
        <div class="fd-card-body" style="padding: 16px 24px;">
            <form method="get" action="<?php echo current_url(); ?>" class="d-flex align-items-center flex-wrap" style="gap: 12px;">
                <div class="d-flex align-items-center" style="gap: 8px;">
                    <label style="white-space: nowrap; margin: 0;">From</label>
                    <input type="date" name="from_date" class="form-control" value="<?php echo $from_date; ?>" style="width: auto;">
                </div>
                <div class="d-flex align-items-center" style="gap: 8px;">
                    <label style="white-space: nowrap; margin: 0;">To</label>
                    <input type="date" name="to_date" class="form-control" value="<?php echo $to_date; ?>" style="width: auto;">
                </div>
                <select name="status" class="form-control" style="width: auto;">
                    <option value="">All Status</option>
                    <option value="ACTIVE" <?php echo $status == 'ACTIVE' ? 'selected' : ''; ?>>Active</option>
                    <option value="MATURED" <?php echo $status == 'MATURED' ? 'selected' : ''; ?>>Matured</option>
                    <option value="CLOSED" <?php echo $status == 'CLOSED' ? 'selected' : ''; ?>>Closed</option>
                    <option value="MERGED" <?php echo $status == 'MERGED' ? 'selected' : ''; ?>>Merged</option>
                </select>
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter mr-1"></i> Filter</button>
                <a href="<?php echo current_url(); ?>" class="btn btn-default">Reset</a>
            </form>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="fd-stats no-print">
        <div class="fd-stat">
            <div class="fd-stat-icon green"><i class="fas fa-file-invoice-dollar"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Total Deposits</div>
                <div class="fd-stat-value"><?php echo count($deposits); ?></div>
            </div>
        </div>
        <div class="fd-stat">
            <div class="fd-stat-icon blue"><i class="fas fa-money-bill-wave"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Total Principal</div>
                <div class="fd-stat-value sm">K <?php echo number_format($total_principal, 2); ?></div>
            </div>
        </div>
        <div class="fd-stat">
            <div class="fd-stat-icon yellow"><i class="fas fa-coins"></i></div>
            <div class="fd-stat-content">
                <div class="fd-stat-label">Total Accrued Interest</div>
                <div class="fd-stat-value sm">K <?php echo number_format($total_accrued, 2); ?></div>
            </div>
        </div>
    </div>

    <!-- Report Table -->
    <div class="fd-card">
        <div class="fd-card-header no-print">
            <span class="fd-card-title">Deposits Report</span>
            <div style="display: flex; gap: 8px;">
                <button onclick="exportTableToExcel('reportTable', 'fd_report')" class="btn btn-default btn-sm">
                    <i class="fas fa-file-excel mr-1"></i> Excel
                </button>
                <button onclick="window.print()" class="btn btn-default btn-sm">
                    <i class="fas fa-print mr-1"></i> Print
                </button>
            </div>
        </div>
        <div class="fd-card-body compact">
            <table class="table" id="reportTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Deposit #</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th style="text-align: right;">Principal</th>
                        <th style="text-align: center;">Rate</th>
                        <th style="text-align: right;">Accrued Interest</th>
                        <th>Start Date</th>
                        <th>Maturity</th>
                        <th style="text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($deposits)): ?>
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 32px; color: #5f6368;">No deposits found for the selected criteria</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($deposits as $deposit): ?>
                            <?php
                            $accrued = 0;
                            if ($deposit->status == 'ACTIVE') {
                                $accrued = calculate_accrued_interest($deposit);
                            }
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <a href="<?php echo site_url('Fixed_deposits/deposit_view/' . $deposit->id); ?>" style="font-family: monospace;">
                                        <?php echo $deposit->deposit_number; ?>
                                    </a>
                                </td>
                                <td><?php echo $deposit->first_name . ' ' . $deposit->last_name; ?></td>
                                <td><?php echo $deposit->phone_number; ?></td>
                                <td style="text-align: right;">K <?php echo number_format($deposit->current_principal, 2); ?></td>
                                <td style="text-align: center;"><?php echo $deposit->interest_rate; ?>%</td>
                                <td style="text-align: right;">K <?php echo number_format($accrued, 2); ?></td>
                                <td><?php echo date('d M Y', strtotime($deposit->start_date)); ?></td>
                                <td><?php echo date('d M Y', strtotime($deposit->maturity_date)); ?></td>
                                <td style="text-align: center;">
                                    <?php
                                    $status_badges = array(
                                        'ACTIVE' => 'fd-badge-active',
                                        'MATURED' => 'fd-badge-matured',
                                        'CLOSED' => 'fd-badge-closed',
                                        'MERGED' => 'fd-badge-pending'
                                    );
                                    $badge = isset($status_badges[$deposit->status]) ? $status_badges[$deposit->status] : 'fd-badge-closed';
                                    ?>
                                    <span class="badge <?php echo $badge; ?>"><?php echo $deposit->status; ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr style="background: #f8f9fa; font-weight: 500;">
                        <td colspan="4" style="padding: 12px 24px;"><strong>TOTALS</strong></td>
                        <td style="text-align: right; padding: 12px 24px;"><strong>K <?php echo number_format($total_principal, 2); ?></strong></td>
                        <td></td>
                        <td style="text-align: right; padding: 12px 24px;"><strong>K <?php echo number_format($total_accrued, 2); ?></strong></td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
function exportTableToExcel(tableID, filename) {
    var downloadLink;
    var dataType = 'application/vnd.ms-excel';
    var tableSelect = document.getElementById(tableID);
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

    filename = filename ? filename + '.xls' : 'excel_data.xls';

    downloadLink = document.createElement("a");
    document.body.appendChild(downloadLink);

    if (navigator.msSaveOrOpenBlob) {
        var blob = new Blob(['\ufeff', tableHTML], {
            type: dataType
        });
        navigator.msSaveOrOpenBlob(blob, filename);
    } else {
        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
        downloadLink.download = filename;
        downloadLink.click();
    }
}
</script>
