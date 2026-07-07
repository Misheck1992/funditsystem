<?php
// Default to ZMW (Zambian Kwacha)
$currency_code = isset($currency) && $currency && $currency->currency_code ? $currency->currency_code : 'ZMW';
$settings = get_by_id('settings', 'settings_id', 1);
$company_name = $settings->company_name ?? 'Company Name';
$company_address = $settings->company_address ?? '';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Collateral Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1e3a5f;
            padding-bottom: 10px;
        }
        .header h1 {
            color: #1e3a5f;
            margin: 0;
            font-size: 18px;
        }
        .header h2 {
            color: #666;
            margin: 5px 0;
            font-size: 14px;
            font-weight: normal;
        }
        .header .date {
            font-size: 10px;
            color: #888;
        }
        .summary-box {
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 15px;
        }
        .summary-grid {
            display: table;
            width: 100%;
        }
        .summary-item {
            display: table-cell;
            text-align: center;
            padding: 5px;
            border-right: 1px solid #e5e7eb;
        }
        .summary-item:last-child {
            border-right: none;
        }
        .summary-value {
            font-size: 14px;
            font-weight: bold;
            color: #1e3a5f;
        }
        .summary-label {
            font-size: 8px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background: #1e3a5f;
            color: #fff;
            padding: 8px 4px;
            text-align: left;
            font-size: 9px;
        }
        td {
            padding: 6px 4px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9px;
        }
        tr:nth-child(even) {
            background: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-success {
            color: #059669;
        }
        .text-danger {
            color: #dc2626;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
        }
        .badge-active {
            background: #dcfce7;
            color: #166534;
        }
        .badge-pledged {
            background: #fef3c7;
            color: #92400e;
        }
        .badge-released {
            background: #dbeafe;
            color: #1e40af;
        }
        .badge-individual {
            background: #dbeafe;
            color: #1e40af;
        }
        .badge-institution {
            background: #fef3c7;
            color: #92400e;
        }
        .totals-row {
            background: #1e3a5f !important;
            color: #fff;
            font-weight: bold;
        }
        .totals-row td {
            border-bottom: none;
        }
        .section-title {
            background: #e5e7eb;
            padding: 8px;
            margin: 15px 0 10px 0;
            font-weight: bold;
            color: #1e3a5f;
        }
        .breakdown-table {
            width: 48%;
            display: inline-table;
            vertical-align: top;
            margin-right: 2%;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8px;
            color: #888;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><?php echo $company_name; ?></h1>
        <h2>Collateral Report</h2>
        <div class="date">Generated on: <?php echo date('d M Y H:i'); ?></div>
        <?php if (!empty($filters['from_date']) || !empty($filters['to_date'])): ?>
        <div class="date">
            Period: <?php echo !empty($filters['from_date']) ? $filters['from_date'] : 'All'; ?> to <?php echo !empty($filters['to_date']) ? $filters['to_date'] : 'Present'; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Summary -->
    <div class="summary-box">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="text-align: center; border: none; width: 16.66%;">
                    <div class="summary-value"><?php echo number_format($summary['total_count']); ?></div>
                    <div class="summary-label">Total Collaterals</div>
                </td>
                <td style="text-align: center; border: none; width: 16.66%;">
                    <div class="summary-value"><?php echo $currency_code; ?> <?php echo number_format($summary['total_market_value'], 2); ?></div>
                    <div class="summary-label">Market Value</div>
                </td>
                <td style="text-align: center; border: none; width: 16.66%;">
                    <div class="summary-value"><?php echo $currency_code; ?> <?php echo number_format($summary['total_force_sale_value'], 2); ?></div>
                    <div class="summary-label">Force Sale Value</div>
                </td>
                <td style="text-align: center; border: none; width: 16.66%;">
                    <div class="summary-value text-danger"><?php echo $currency_code; ?> <?php echo number_format($summary['total_utilized'], 2); ?></div>
                    <div class="summary-label">Utilized</div>
                </td>
                <td style="text-align: center; border: none; width: 16.66%;">
                    <div class="summary-value text-success"><?php echo $currency_code; ?> <?php echo number_format($summary['total_available'], 2); ?></div>
                    <div class="summary-label">Available</div>
                </td>
                <td style="text-align: center; border: none; width: 16.66%;">
                    <div class="summary-value"><?php echo $summary['utilization_rate']; ?>%</div>
                    <div class="summary-label">Utilization Rate</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Breakdown Tables -->
    <table class="breakdown-table">
        <tr><th colspan="3" style="background: #374151;">By Collateral Type</th></tr>
        <tr style="background: #6b7280; color: #fff;">
            <th>Type</th>
            <th class="text-center">Count</th>
            <th class="text-right">Value</th>
        </tr>
        <?php foreach ($summary['by_type'] as $type): ?>
        <tr>
            <td><?php echo $type->collateral_type ?? 'Unknown'; ?></td>
            <td class="text-center"><?php echo $type->count; ?></td>
            <td class="text-right"><?php echo $currency_code; ?> <?php echo number_format($type->total_value, 2); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <table class="breakdown-table">
        <tr><th colspan="3" style="background: #374151;">By Status</th></tr>
        <tr style="background: #6b7280; color: #fff;">
            <th>Status</th>
            <th class="text-center">Count</th>
            <th class="text-right">Percentage</th>
        </tr>
        <?php foreach ($summary['by_status'] as $status): ?>
        <tr>
            <td><?php echo $status->collateral_status ?? 'Unknown'; ?></td>
            <td class="text-center"><?php echo $status->count; ?></td>
            <td class="text-right"><?php echo $summary['total_count'] > 0 ? round(($status->count / $summary['total_count']) * 100, 1) : 0; ?>%</td>
        </tr>
        <?php endforeach; ?>
    </table>

    <div style="clear: both;"></div>

    <!-- Collateral Details -->
    <div class="section-title">Collateral Details (<?php echo count($collaterals); ?> records)</div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Collateral Name</th>
                <th>Type</th>
                <th>Serial No.</th>
                <th>Customer</th>
                <th>Cust. Type</th>
                <th class="text-right">Market Value</th>
                <th class="text-right">Force Sale</th>
                <th class="text-right">Utilized</th>
                <th class="text-right">Available</th>
                <th class="text-center">Util %</th>
                <th class="text-center">Loans</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $n = 1; foreach ($collaterals as $c): ?>
            <tr>
                <td><?php echo $n++; ?></td>
                <td><?php echo htmlspecialchars(substr($c->collateral_name, 0, 25)); ?><?php echo strlen($c->collateral_name) > 25 ? '...' : ''; ?></td>
                <td><?php echo $c->collateral_type; ?></td>
                <td><?php echo $c->collateral_serial ?: '-'; ?></td>
                <td><?php echo htmlspecialchars(substr($c->customer_name, 0, 20)); ?><?php echo strlen($c->customer_name) > 20 ? '...' : ''; ?></td>
                <td>
                    <span class="badge badge-<?php echo $c->customer_type; ?>">
                        <?php echo $c->customer_type == 'individual' ? 'IND' : 'CORP'; ?>
                    </span>
                </td>
                <td class="text-right"><?php echo number_format($c->market_value, 2); ?></td>
                <td class="text-right"><?php echo number_format($c->force_sale_value, 2); ?></td>
                <td class="text-right text-danger"><?php echo number_format($c->total_utilized, 2); ?></td>
                <td class="text-right text-success"><?php echo number_format($c->available_balance, 2); ?></td>
                <td class="text-center"><?php echo $c->utilization_percent; ?>%</td>
                <td class="text-center"><?php echo $c->active_loans_count; ?></td>
                <td>
                    <span class="badge badge-<?php echo strtolower($c->collateral_status ?? 'active'); ?>">
                        <?php echo $c->collateral_status ?? 'ACTIVE'; ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="totals-row">
                <td colspan="6" class="text-right">TOTALS:</td>
                <td class="text-right"><?php echo $currency_code; ?> <?php echo number_format($summary['total_market_value'], 2); ?></td>
                <td class="text-right"><?php echo $currency_code; ?> <?php echo number_format($summary['total_force_sale_value'], 2); ?></td>
                <td class="text-right"><?php echo $currency_code; ?> <?php echo number_format($summary['total_utilized'], 2); ?></td>
                <td class="text-right"><?php echo $currency_code; ?> <?php echo number_format($summary['total_available'], 2); ?></td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <?php echo $company_name; ?> - Collateral Report - Page {PAGENO} of {nbpg} - Generated on <?php echo date('d M Y H:i'); ?>
    </div>
</body>
</html>
