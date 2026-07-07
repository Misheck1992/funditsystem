<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Placement Certificate - <?php echo $deposit->deposit_number; ?></title>
    <style>
        @media print {
            body { margin: 0; padding: 20px; }
            .no-print { display: none !important; }
            .print-only { display: block !important; }
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .certificate-container {
            background: #fff;
            padding: 40px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header-section {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #1e3a5f;
        }
        .header-title {
            font-size: 16px;
            font-weight: bold;
            color: #1e3a5f;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .company-info {
            text-align: left;
            margin-bottom: 30px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a5f;
            margin-bottom: 5px;
        }
        .company-address {
            color: #666;
            line-height: 1.6;
        }
        .attention-line {
            margin: 25px 0;
            font-size: 13px;
        }
        .attention-line strong {
            color: #1e3a5f;
        }
        .certificate-title {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a5f;
            text-align: center;
            margin: 30px 0 20px 0;
            padding: 10px;
            background: #f8f9fa;
            border-left: 4px solid #1e3a5f;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 11px;
        }
        .details-table th {
            background: #1e3a5f;
            color: #fff;
            padding: 10px 8px;
            text-align: center;
            font-weight: 600;
            border: 1px solid #1e3a5f;
            font-size: 10px;
        }
        .details-table td {
            padding: 12px 8px;
            text-align: center;
            border: 1px solid #ddd;
            background: #fff;
        }
        .details-table tr:nth-child(even) td {
            background: #f9f9f9;
        }
        .amount {
            font-family: 'Consolas', monospace;
            font-weight: bold;
        }
        .highlight-amount {
            color: #28a745;
            font-size: 12px;
        }
        .summary-section {
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #ddd;
        }
        .summary-row:last-child {
            border-bottom: none;
            font-weight: bold;
            color: #1e3a5f;
        }
        .signature-section {
            margin-top: 60px;
            padding-top: 30px;
        }
        .signature-line {
            width: 250px;
            border-bottom: 1px solid #333;
            margin-bottom: 5px;
        }
        .signature-title {
            font-weight: bold;
            color: #1e3a5f;
            text-transform: uppercase;
            font-size: 11px;
        }
        .footer-note {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        .btn-actions {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background: #fff;
            border-radius: 8px;
        }
        .btn {
            display: inline-block;
            padding: 10px 25px;
            margin: 0 5px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-primary {
            background: #1e3a5f;
            color: #fff;
        }
        .btn-success {
            background: #28a745;
            color: #fff;
        }
        .btn-secondary {
            background: #6c757d;
            color: #fff;
        }
        .print-only { display: none; }
    </style>
</head>
<body>
    <div class="btn-actions no-print">
        <button class="btn btn-primary" onclick="window.print();">
            <i class="fas fa-print"></i> Print Certificate
        </button>
        <a class="btn btn-success" href="<?php echo site_url('Fixed_deposits/placement_certificate_excel/' . $deposit->id); ?>">
            <i class="fas fa-file-excel"></i> Export to Excel
        </a>
        <a class="btn btn-secondary" href="<?php echo site_url('Fixed_deposits/deposit_view/' . $deposit->id); ?>">
            <i class="fas fa-arrow-left"></i> Back to Deposit
        </a>
    </div>

    <div class="certificate-container">
        <div class="header-section">
            <div class="header-title">PLACEMENT SHEET AS AT <?php echo strtoupper($current_date); ?></div>
        </div>

        <div class="company-info">
            <div class="company-name"><?php echo $settings->company_name ?? 'FUNDIT Capital Solutions Limited'; ?></div>
            <div class="company-address">
                <?php echo nl2br(htmlspecialchars($company_address)); ?>
            </div>
        </div>

        <div class="attention-line">
            <strong>Attention:</strong> <?php echo $deposit->first_name . ' ' . $deposit->last_name; ?>
            <br>
            <strong>Customer No:</strong> <?php echo $deposit->customer_number; ?>
            <br>
            <strong>Phone:</strong> <?php echo $deposit->phone_number ?? 'N/A'; ?>
        </div>

        <div class="certificate-title">Fixed Placement Certificate</div>

        <table class="details-table">
            <thead>
                <tr>
                    <th>Placement Date</th>
                    <th>Currency</th>
                    <th>Placement Amount</th>
                    <th>Duration</th>
                    <th>Interest Rate</th>
                    <th>Interest Before Tax</th>
                    <th>Net Interest<br>(WHT @ <?php echo $wht_rate; ?>%)</th>
                    <th>Quarterly Payout</th>
                    <th>Payout at Maturity</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo date('d-m-y', strtotime($deposit->start_date)); ?></td>
                    <td><strong><?php echo $deposit->currency ?? 'ZMW'; ?></strong></td>
                    <td class="amount"><?php echo number_format($deposit->principal_amount, 2); ?></td>
                    <td><?php echo $deposit->duration_months; ?> Months</td>
                    <td><strong><?php echo number_format($deposit->interest_rate, 2); ?>%</strong></td>
                    <td class="amount"><?php echo number_format($interest_before_tax, 2); ?></td>
                    <td class="amount highlight-amount"><?php echo number_format($net_interest, 2); ?></td>
                    <td class="amount"><?php echo $deposit->payment_option == 'QUARTERLY' ? number_format($quarterly_payout, 2) : 'N/A'; ?></td>
                    <td class="amount highlight-amount"><?php echo number_format($payout_at_maturity, 2); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="summary-section">
            <h4 style="margin: 0 0 15px 0; color: #1e3a5f;">Deposit Summary</h4>
            <div class="summary-row">
                <span>Principal Amount:</span>
                <span class="amount"><?php echo ($deposit->currency ?? 'ZMW') . ' ' . number_format($deposit->principal_amount, 2); ?></span>
            </div>
            <div class="summary-row">
                <span>Interest Rate (Annual):</span>
                <span><?php echo number_format($deposit->interest_rate, 2); ?>%</span>
            </div>
            <div class="summary-row">
                <span>Gross Interest (<?php echo $deposit->duration_months; ?> months):</span>
                <span class="amount"><?php echo ($deposit->currency ?? 'ZMW') . ' ' . number_format($interest_before_tax, 2); ?></span>
            </div>
            <div class="summary-row">
                <span>Withholding Tax (<?php echo $wht_rate; ?>%):</span>
                <span class="amount" style="color: #dc3545;">- <?php echo ($deposit->currency ?? 'ZMW') . ' ' . number_format($wht_amount, 2); ?></span>
            </div>
            <div class="summary-row">
                <span>Net Interest After Tax:</span>
                <span class="amount" style="color: #28a745;"><?php echo ($deposit->currency ?? 'ZMW') . ' ' . number_format($net_interest, 2); ?></span>
            </div>
            <div class="summary-row">
                <span>Start Date:</span>
                <span><?php echo date('d F Y', strtotime($deposit->start_date)); ?></span>
            </div>
            <div class="summary-row">
                <span>Maturity Date:</span>
                <span><?php echo date('d F Y', strtotime($deposit->maturity_date)); ?></span>
            </div>
            <div class="summary-row">
                <span>Payment Option:</span>
                <span><?php echo $deposit->payment_option == 'QUARTERLY' ? 'Quarterly Interest Payments' : 'At Maturity'; ?></span>
            </div>
            <div class="summary-row" style="font-size: 14px; padding-top: 15px;">
                <span>TOTAL PAYOUT AT MATURITY:</span>
                <span class="amount" style="color: #1e3a5f; font-size: 16px;"><?php echo ($deposit->currency ?? 'ZMW') . ' ' . number_format($payout_at_maturity, 2); ?></span>
            </div>
        </div>

        <div class="signature-section">
            <div class="signature-line"></div>
            <div class="signature-title">CHIEF OPERATIONS OFFICER</div>
        </div>

        <div class="footer-note">
            <p>This certificate is issued by <?php echo $settings->company_name ?? 'FUNDIT Capital Solutions Limited'; ?></p>
            <p>Deposit Reference: <strong><?php echo $deposit->deposit_number; ?></strong> | Generated on: <?php echo date('d F Y H:i'); ?></p>
            <p style="margin-top: 10px;">
                <?php if ($settings->phone_number): ?>Phone: <?php echo $settings->phone_number; ?> | <?php endif; ?>
                <?php if ($settings->company_email): ?>Email: <?php echo $settings->company_email; ?><?php endif; ?>
            </p>
        </div>
    </div>
</body>
</html>
