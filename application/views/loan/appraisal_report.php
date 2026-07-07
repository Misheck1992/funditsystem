<?php
// Helper variables for consistent field access
$customer_name = '';
if ($is_corporate) {
    $customer_name = $customer->EntityName ?? '';
} else {
    $customer_name = ($customer->Firstname ?? '') . ' ' . ($customer->Lastname ?? '');
}

// Loan field mappings (handle both old and new field names)
$loan_amount = $loan->loan_principal ?? $loan->approved_amount ?? 0;
$loan_rate = $loan->loan_interest ?? $loan->interest_rate ?? 0;
$loan_term = $loan->loan_period ?? $loan->loan_duration ?? 0;
$loan_term_unit = $loan->period_type ?? $loan->loan_duration_unit ?? 'Months';

// Convert frequency labels: Monthly→Months, Weekly→Weeks, Daily→Days
$term_unit_display = $loan_term_unit;
$frequency_map = [
    'Monthly' => 'Months',
    'Weekly' => 'Weeks',
    'Daily' => 'Days',
    'Yearly' => 'Years',
    'Annually' => 'Years'
];
if (isset($frequency_map[$loan_term_unit])) {
    $term_unit_display = $frequency_map[$loan_term_unit];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appraisal Report - <?php echo $loan->loan_number; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Source+Sans+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        :root {
            --primary-dark: #1a365d;
            --primary-blue: #2563eb;
            --surface-light: #f8fafc;
            --surface-cream: #ffffff;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border-subtle: #e2e8f0;
            --success-green: #16a34a;
            --warning-amber: #d97706;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Source Sans Pro', -apple-system, sans-serif;
            background: var(--surface-light);
            color: var(--text-dark);
            line-height: 1.6;
            font-size: 15px;
        }

        /* Print Button */
        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
        }

        .print-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .print-btn.primary {
            background: var(--primary-blue);
            color: #fff;
        }

        .print-btn.secondary {
            background: var(--primary-blue);
            color: #fff;
        }

        .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        /* Letterhead */
        .letterhead {
            background: #fff;
            padding: 1.5rem 2rem;
            border-bottom: 3px solid var(--primary-dark);
        }

        .letterhead-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .letterhead-logo {
            max-height: 80px;
            width: auto;
        }

        .letterhead-info {
            text-align: right;
        }

        .letterhead-company {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 0.25rem;
        }

        .letterhead-details {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        .letterhead-details i {
            color: var(--primary-blue);
            margin-right: 0.25rem;
            width: 14px;
        }

        /* Header */
        .report-header {
            background: var(--primary-dark);
            padding: 2rem;
            position: relative;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .report-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 2rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .report-subtitle {
            font-size: 1.1rem;
            color: rgba(255,255,255,0.7);
            font-weight: 300;
        }

        .header-meta {
            display: flex;
            gap: 2rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .meta-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(255,255,255,0.5);
        }

        .meta-value {
            font-size: 1rem;
            color: #fff;
            font-weight: 600;
        }

        /* Main Content */
        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Summary Cards */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin: -3rem 0 2rem 0;
            position: relative;
            z-index: 10;
        }

        .summary-card {
            background: var(--surface-cream);
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid var(--border-subtle);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .summary-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        }

        .summary-card .label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .summary-card .value {
            font-family: 'Playfair Display', serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-blue);
        }

        .summary-card .subtext {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 0.25rem;
        }

        .summary-card.highlight {
            background: var(--primary-blue);
            border: none;
        }

        .summary-card.highlight .label,
        .summary-card.highlight .subtext {
            color: rgba(255,255,255,0.8);
        }

        .summary-card.highlight .value {
            color: #fff;
        }

        /* Sections */
        .section {
            background: var(--surface-cream);
            border-radius: 12px;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border-subtle);
            overflow: hidden;
        }

        .section-header {
            background: var(--primary-dark);
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-number {
            width: 28px;
            height: 28px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--primary-dark);
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            font-weight: 600;
            color: #fff;
            letter-spacing: 0.02em;
        }

        .section-body {
            padding: 1.5rem;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .info-item {
            display: flex;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-subtle);
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            flex: 0 0 40%;
            font-size: 0.85rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .info-value {
            flex: 1;
            font-weight: 600;
            color: var(--text-dark);
        }

        .info-value.highlight {
            color: var(--primary-blue);
        }

        /* Full Width Info */
        .info-full {
            grid-column: 1 / -1;
            background: var(--surface-light);
            padding: 1rem;
            border-radius: 8px;
            margin-top: 0.5rem;
        }

        .info-full .info-label {
            flex: 0 0 20%;
        }

        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
        }

        .data-table thead {
            background: var(--primary-dark);
        }

        .data-table th {
            padding: 0.875rem 1rem;
            text-align: left;
            font-weight: 600;
            color: #fff;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .data-table tbody tr {
            border-bottom: 1px solid var(--border-subtle);
            transition: background 0.15s ease;
        }

        .data-table tbody tr:hover {
            background: var(--surface-light);
        }

        .data-table tbody tr:last-child {
            border-bottom: none;
        }

        .data-table td {
            padding: 0.875rem 1rem;
            color: var(--text-dark);
        }

        .data-table .currency {
            font-weight: 600;
            font-family: 'Source Sans Pro', monospace;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-badge.paid {
            background: rgba(45, 106, 79, 0.1);
            color: var(--success-green);
        }

        .status-badge.pending {
            background: rgba(180, 83, 9, 0.1);
            color: var(--warning-amber);
        }

        .status-badge.active {
            background: rgba(59, 130, 246, 0.1);
            color: #1e40af;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
        }

        /* Due Diligence */
        .dd-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .dd-card {
            background: var(--surface-light);
            border-radius: 8px;
            padding: 1.25rem;
            border-left: 4px solid var(--primary-blue);
        }

        .dd-card.full-width {
            grid-column: 1 / -1;
        }

        .dd-card-title {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            font-weight: 600;
            color: var(--primary-blue);
            margin-bottom: 0.75rem;
        }

        .dd-card-content {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.7;
        }

        .dd-card-content strong {
            color: var(--text-dark);
        }

        /* Bank Statement */
        .bank-summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .bank-stat {
            text-align: center;
            padding: 1.25rem;
            background: var(--surface-light);
            border-radius: 8px;
        }

        .bank-stat .stat-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .bank-stat .stat-value {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-blue);
        }

        .bank-stat.debit .stat-value {
            color: #b91c1c;
        }

        .bank-stat.credit .stat-value {
            color: var(--success-green);
        }

        .bank-notes {
            background: var(--surface-light);
            border-left: 4px solid var(--primary-blue);
            padding: 1rem 1.25rem;
            border-radius: 0 8px 8px 0;
        }

        .bank-notes-content {
            font-size: 0.9rem;
            color: var(--text-muted);
            line-height: 1.7;
        }

        /* Risk Analysis */
        .risk-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .risk-item {
            background: var(--surface-light);
            border-radius: 8px;
            padding: 1.25rem;
            position: relative;
            overflow: hidden;
        }

        .risk-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--success-green);
        }

        .risk-type {
            font-weight: 700;
            color: var(--primary-blue);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .risk-mitigation {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.6;
        }

        .risk-content {
            grid-column: 1 / -1;
            background: var(--surface-light);
            border-radius: 8px;
            padding: 1.25rem;
            border-left: 4px solid var(--primary-blue);
        }

        /* Pricing */
        .pricing-display {
            text-align: center;
            padding: 2rem;
            background: var(--primary-dark);
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .pricing-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: rgba(255,255,255,0.6);
            margin-bottom: 0.5rem;
        }

        .pricing-rate {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 700;
            color: #fff;
        }

        .pricing-rate sup {
            font-size: 1.25rem;
            vertical-align: super;
        }

        .pricing-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .pricing-detail {
            background: var(--surface-light);
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
        }

        .pricing-detail .detail-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }

        .pricing-detail .detail-value {
            font-weight: 700;
            color: var(--primary-blue);
        }

        /* Regulatory Table */
        .reg-table {
            width: 100%;
            border-collapse: collapse;
        }

        .reg-table th {
            background: var(--primary-dark);
            color: #fff;
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .reg-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-subtle);
            font-size: 0.9rem;
        }

        .reg-table .category {
            font-weight: 600;
            color: var(--primary-blue);
            background: var(--surface-light);
        }

        .compliance-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            font-weight: 700;
            font-size: 0.8rem;
        }

        .compliance-badge.yes {
            background: rgba(45, 106, 79, 0.1);
            color: var(--success-green);
        }

        .compliance-badge.no {
            background: var(--surface-light);
            color: var(--text-muted);
        }

        /* Security Section */
        .security-list {
            list-style: none;
        }

        .security-list li {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid var(--border-subtle);
        }

        .security-list li:last-child {
            border-bottom: none;
        }

        .security-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-blue);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .security-content {
            flex: 1;
        }

        .security-title {
            font-weight: 600;
            color: var(--primary-blue);
            margin-bottom: 0.25rem;
        }

        .security-desc {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        /* Signatures */
        .signatures-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            margin-top: 1rem;
        }

        .signature-block {
            background: var(--surface-light);
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
        }

        .signature-role {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
        }

        .signature-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary-blue);
            margin-bottom: 1rem;
        }

        .signature-line {
            width: 80%;
            height: 1px;
            background: var(--border-subtle);
            margin: 0 auto;
        }

        /* Footer */
        .report-footer {
            background: var(--primary-dark);
            padding: 2rem;
            text-align: center;
            margin-top: 2rem;
        }

        .footer-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: #fff;
            margin-bottom: 0.5rem;
        }

        .footer-text {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.5);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .info-grid, .dd-grid, .risk-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }
            .report-title {
                font-size: 1.75rem;
            }
            .header-meta {
                flex-wrap: wrap;
            }
            .bank-summary {
                grid-template-columns: 1fr;
            }
            .pricing-details, .signatures-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Print Styles */
        @media print {
            body {
                background: #fff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .print-controls {
                display: none !important;
            }
            .letterhead {
                border-bottom: 2px solid var(--primary-dark) !important;
            }
            .section {
                break-inside: avoid;
            }
            .summary-card:hover {
                transform: none;
                box-shadow: none;
            }
            .report-header {
                background: var(--primary-dark) !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .section-header {
                background: var(--primary-dark) !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .report-footer {
                background: var(--primary-dark) !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <!-- Print Controls -->
    <div class="print-controls">
        <button class="print-btn primary" onclick="window.print();">
            <i class="fa fa-print"></i> Print / Save PDF
        </button>
        <a href="<?php echo base_url('loan/view/').$loan->loan_id; ?>" class="print-btn secondary">
            <i class="fa fa-arrow-left"></i> Back to Loan
        </a>
    </div>

    <!-- Letterhead -->
    <div class="letterhead">
        <div class="letterhead-content">
            <div>
                <?php if (!empty($company_logo)): ?>
                <img src="<?php echo base_url($company_logo); ?>" alt="<?php echo $company_name; ?>" class="letterhead-logo">
                <?php else: ?>
                <div class="letterhead-company"><?php echo $company_name ?? 'Fundit Capital Solutions'; ?></div>
                <?php endif; ?>
            </div>
            <div class="letterhead-info">
                <div class="letterhead-company"><?php echo $company_name ?? 'Fundit Capital Solutions'; ?></div>
                <div class="letterhead-details">
                    <?php if (!empty($company_address)): ?>
                    <div><i class="fa fa-map-marker"></i> <?php echo nl2br($company_address); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($company_phone)): ?>
                    <div><i class="fa fa-phone"></i> <?php echo $company_phone; ?></div>
                    <?php endif; ?>
                    <?php if (!empty($company_email)): ?>
                    <div><i class="fa fa-envelope"></i> <?php echo $company_email; ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="report-header">
        <div class="header-content">
            <h1 class="report-title">Credit Appraisal Report</h1>
            <p class="report-subtitle"><?php echo $customer_name; ?> &mdash; <?php echo $loan_product->product_name; ?></p>
            <div class="header-meta">
                <div class="meta-item">
                    <span class="meta-label">Application Date</span>
                    <span class="meta-value"><?php echo date('d F Y', strtotime($loan->loan_date)); ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Facility Type</span>
                    <span class="meta-value"><?php echo $loan_product->product_name; ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Loan Number</span>
                    <span class="meta-value"><?php echo $loan->loan_number; ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">Maturity</span>
                    <span class="meta-value"><?php echo isset($loan->maturity_date) && $loan->maturity_date ? date('d F Y', strtotime($loan->maturity_date)) : 'N/A'; ?></span>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <!-- Summary Cards -->
        <div class="summary-grid">
            <div class="summary-card highlight">
                <div class="label">Amount Requested</div>
                <div class="value"><?php echo $currency->currency_code; ?> <?php echo number_format($loan_amount, 0); ?></div>
                <div class="subtext"><?php echo $loan_product->product_name; ?></div>
            </div>
            <div class="summary-card">
                <div class="label">Interest Rate</div>
                <div class="value"><?php echo $loan_rate; ?>%</div>
                <div class="subtext"><?php echo ucfirst($loan->calculation_type ?? 'Flat'); ?> Rate</div>
            </div>
            <div class="summary-card">
                <div class="label">Loan Term</div>
                <div class="value"><?php echo $loan_term; ?></div>
                <div class="subtext"><?php echo ucfirst($term_unit_display); ?></div>
            </div>
            <div class="summary-card">
                <div class="label">Status</div>
                <div class="value"><?php echo $loan->loan_status; ?></div>
                <div class="subtext">Current Status</div>
            </div>
        </div>

        <!-- Section 1: Client Information -->
        <section class="section">
            <div class="section-header">
                <span class="section-number">1</span>
                <h2 class="section-title">Client Information</h2>
            </div>
            <div class="section-body">
                <div class="info-grid">
                    <?php if ($is_corporate): ?>
                    <div class="info-item">
                        <span class="info-label">Company Name</span>
                        <span class="info-value"><?php echo $customer->EntityName ?? 'N/A'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Registration Number</span>
                        <span class="info-value"><?php echo $customer->RegistrationNumber ?? 'N/A'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Industry / Sector</span>
                        <span class="info-value"><?php echo $customer->industry_sector ?? 'N/A'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Nature of Business</span>
                        <span class="info-value"><?php echo $customer->nature_of_business ?? 'N/A'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Entity Type</span>
                        <span class="info-value"><?php echo $customer->entity_type ?? 'N/A'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone</span>
                        <span class="info-value"><?php echo $customer->phone_number ?? 'N/A'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value"><?php echo $customer->contact_email ?? 'N/A'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Address</span>
                        <span class="info-value"><?php echo trim(($customer->street ?? '') . ', ' . ($customer->city_town ?? '') . ', ' . ($customer->province ?? ''), ', ') ?: 'N/A'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Financial Year End</span>
                        <span class="info-value"><?php echo isset($customer->financial_year_end) && $customer->financial_year_end ? date('d M Y', strtotime($customer->financial_year_end)) : 'N/A'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Number of Employees</span>
                        <span class="info-value">
                            Casual: <?php echo $customer->casual_employees ?? 0; ?> |
                            Permanent: <?php echo $customer->permanent_employees ?? 0; ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Last Update</span>
                        <span class="info-value"><?php echo isset($customer->LastUpdatedOn) && $customer->LastUpdatedOn ? date('M Y', strtotime($customer->LastUpdatedOn)) : 'N/A'; ?></span>
                    </div>
                    <?php else: ?>
                    <div class="info-item">
                        <span class="info-label">Customer Name</span>
                        <span class="info-value"><?php echo $customer_name; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">ID Number</span>
                        <span class="info-value"><?php echo isset($customer_kyc->IDNumber) ? $customer_kyc->IDNumber : 'N/A'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone</span>
                        <span class="info-value"><?php echo $customer->PhoneNumber ?? 'N/A'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value"><?php echo $customer->EmailAddress ?? 'N/A'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Profession</span>
                        <span class="info-value"><?php echo $customer->Profession ?? 'N/A'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Source of Income</span>
                        <span class="info-value"><?php echo $customer->SourceOfIncome ?? 'N/A'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Monthly Income</span>
                        <span class="info-value"><?php echo isset($customer->GrossMonthlyIncome) ? $currency->currency_code.' '.number_format($customer->GrossMonthlyIncome, 2) : 'N/A'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Address</span>
                        <span class="info-value"><?php echo trim(($customer->AddressLine1 ?? '').' '.($customer->AddressLine2 ?? '')) ?: 'N/A'; ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if ($is_corporate && !empty($client_shareholders) && count($client_shareholders) > 0): ?>
                <!-- Client Shareholders/Directors -->
                <div style="margin-top: 1.5rem;">
                    <h4 style="font-family: 'Playfair Display', serif; color: var(--primary-blue); margin-bottom: 1rem; font-size: 1rem;">
                        <i class="fa fa-users" style="margin-right: 0.5rem;"></i>Directors / Shareholders
                    </h4>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>ID Number</th>
                                <th>Nationality</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Shares %</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($client_shareholders as $sh): ?>
                            <tr>
                                <td><?php echo ($sh->title ?? '') . ' ' . ($sh->first_name ?? '') . ' ' . ($sh->last_name ?? ''); ?></td>
                                <td><?php echo $sh->idnumber ?? 'N/A'; ?></td>
                                <td><?php echo $sh->nationality ?? 'N/A'; ?></td>
                                <td><?php echo $sh->phone_number ?? 'N/A'; ?></td>
                                <td><?php echo $sh->email_address ?? 'N/A'; ?></td>
                                <td><?php echo ($sh->percentage_value ?? 0) . '%'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <?php if (!empty($off_taker)): ?>
        <!-- Off-Taker Information -->
        <section class="section">
            <div class="section-header">
                <span class="section-number"><i class="fa fa-building"></i></span>
                <h2 class="section-title">Off-Taker Information</h2>
            </div>
            <div class="section-body">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Company Name</span>
                        <span class="info-value"><?php echo $off_taker->EntityName ?? 'N/A'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Registration Number</span>
                        <span class="info-value"><?php echo $off_taker->RegistrationNumber ?? 'N/A'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Industry / Sector</span>
                        <span class="info-value"><?php echo $off_taker->industry_sector ?? 'N/A'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Nature of Business</span>
                        <span class="info-value"><?php echo $off_taker->nature_of_business ?? 'N/A'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Phone</span>
                        <span class="info-value"><?php echo $off_taker->phone_number ?? 'N/A'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Email</span>
                        <span class="info-value"><?php echo $off_taker->contact_email ?? 'N/A'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Address</span>
                        <span class="info-value"><?php echo trim(($off_taker->street ?? '') . ', ' . ($off_taker->city_town ?? '') . ', ' . ($off_taker->province ?? ''), ', ') ?: 'N/A'; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Website</span>
                        <span class="info-value"><?php echo $off_taker->website ?? 'N/A'; ?></span>
                    </div>
                </div>

                <?php if (!empty($offtaker_shareholders) && count($offtaker_shareholders) > 0): ?>
                <!-- Off-Taker Shareholders/Directors -->
                <div style="margin-top: 1.5rem;">
                    <h4 style="font-family: 'Playfair Display', serif; color: var(--primary-blue); margin-bottom: 1rem; font-size: 1rem;">
                        <i class="fa fa-users" style="margin-right: 0.5rem;"></i>Directors / Shareholders
                    </h4>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>ID Number</th>
                                <th>Nationality</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Shares %</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($offtaker_shareholders as $sh): ?>
                            <tr>
                                <td><?php echo ($sh->title ?? '') . ' ' . ($sh->first_name ?? '') . ' ' . ($sh->last_name ?? ''); ?></td>
                                <td><?php echo $sh->idnumber ?? 'N/A'; ?></td>
                                <td><?php echo $sh->nationality ?? 'N/A'; ?></td>
                                <td><?php echo $sh->phone_number ?? 'N/A'; ?></td>
                                <td><?php echo $sh->email_address ?? 'N/A'; ?></td>
                                <td><?php echo ($sh->percentage_value ?? 0) . '%'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Section 2: Due Diligence -->
        <section class="section">
            <div class="section-header">
                <span class="section-number">2</span>
                <h2 class="section-title">Due Diligence</h2>
            </div>
            <div class="section-body">
                <div class="dd-grid">
                    <div class="dd-card">
                        <h3 class="dd-card-title">CRB Search</h3>
                        <div class="dd-card-content">
                            <p><strong>Status:</strong> <?php echo $loan->crb_search ?? 'Not Specified'; ?></p>
                        </div>
                    </div>
                    <div class="dd-card">
                        <h3 class="dd-card-title">PACRA Search</h3>
                        <div class="dd-card-content">
                            <p><strong>Status:</strong> <?php echo $loan->pacra_search ?? 'Not Specified'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 3: Previous Facilities -->
        <section class="section">
            <div class="section-header">
                <span class="section-number">3</span>
                <h2 class="section-title">Previous Facilities</h2>
            </div>
            <div class="section-body">
                <p style="margin-bottom: 1rem;"><strong>Previous Facilities with this Institution:</strong> <?php echo $loan->previous_facilities ?? 'Not Specified'; ?></p>

                <?php if (!empty($previous_loans) && count($previous_loans) > 0): ?>
                <div style="padding: 0; margin-top: 1rem;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Loan Number</th>
                                <th>Facility Type</th>
                                <th>Currency</th>
                                <th>Approved Amount</th>
                                <th>Disbursed</th>
                                <th>Created Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; foreach ($previous_loans as $prev_loan): ?>
                            <?php
                                $prev_currency = get_by_id('currencies', 'currency_id', $prev_loan->currency);
                                $prev_product = get_by_id('loan_products', 'loan_product_id', $prev_loan->loan_product);
                                $prev_amount = $prev_loan->loan_principal ?? $prev_loan->approved_amount ?? 0;
                            ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo $prev_loan->loan_number; ?></td>
                                <td><?php echo $prev_product ? $prev_product->product_name : 'N/A'; ?></td>
                                <td><?php echo $prev_currency ? $prev_currency->currency_code : 'N/A'; ?></td>
                                <td class="currency"><?php echo number_format($prev_amount, 2); ?></td>
                                <td class="currency"><?php echo number_format($prev_loan->disbursed_amount ?? $prev_amount, 2); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($prev_loan->loan_date)); ?></td>
                                <td>
                                    <?php
                                    $status_lower = strtolower($prev_loan->loan_status);
                                    $badge_class = 'pending';
                                    if (strpos($status_lower, 'paid') !== false || $status_lower == 'closed') $badge_class = 'paid';
                                    elseif ($status_lower == 'active' || $status_lower == 'disbursed') $badge_class = 'active';
                                    ?>
                                    <span class="status-badge <?php echo $badge_class; ?>">
                                        <span class="status-dot"></span><?php echo $prev_loan->loan_status; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="dd-card full-width" style="margin-top: 1rem;">
                    <div class="dd-card-content">
                        <p style="text-align: center; color: var(--text-muted);">No previous facilities found for this customer.</p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($loan->past_loans_comment)): ?>
                <div class="dd-card full-width" style="margin-top: 1.5rem;">
                    <h3 class="dd-card-title">Comments on Past Loans</h3>
                    <div class="dd-card-content">
                        <?php echo $loan->past_loans_comment; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Section 4: Bank Account Details -->
        <section class="section">
            <div class="section-header">
                <span class="section-number">4</span>
                <h2 class="section-title">Bank Account Details & Statistics</h2>
            </div>
            <div class="section-body">
                <?php if (!empty($bank_statements)): ?>
                <?php
                    $total_credits = array_sum(array_column((array)$bank_statements, 'credit'));
                    $total_debits  = array_sum(array_column((array)$bank_statements, 'debit'));
                ?>
                <div class="bank-summary">
                    <div class="bank-stat debit">
                        <div class="stat-label">Total Debits</div>
                        <div class="stat-value"><?php echo $currency->currency_code; ?> <?php echo number_format($total_debits, 2); ?></div>
                    </div>
                    <div class="bank-stat credit">
                        <div class="stat-label">Total Credits</div>
                        <div class="stat-value"><?php echo $currency->currency_code; ?> <?php echo number_format($total_credits, 2); ?></div>
                    </div>
                    <div class="bank-stat">
                        <div class="stat-label">Statements</div>
                        <div class="stat-value"><?php echo count($bank_statements); ?> month(s)</div>
                    </div>
                </div>
                <table style="width:100%;border-collapse:collapse;margin-top:1rem;font-size:0.9rem;">
                    <thead>
                        <tr style="background:#f0f4f8;">
                            <th style="padding:8px;text-align:left;border-bottom:1px solid #ddd;">Month</th>
                            <th style="padding:8px;text-align:right;border-bottom:1px solid #ddd;">Credit</th>
                            <th style="padding:8px;text-align:right;border-bottom:1px solid #ddd;">Debit</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($bank_statements as $bs): ?>
                        <tr>
                            <td style="padding:8px;border-bottom:1px solid #eee;"><?php echo htmlspecialchars($bs->month); ?></td>
                            <td style="padding:8px;text-align:right;border-bottom:1px solid #eee;"><?php echo $currency->currency_code; ?> <?php echo number_format($bs->credit, 2); ?></td>
                            <td style="padding:8px;text-align:right;border-bottom:1px solid #eee;"><?php echo $currency->currency_code; ?> <?php echo number_format($bs->debit, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>

                <?php if (!empty($loan->bank_statement_notes)): ?>
                <div class="bank-notes">
                    <h4 style="font-family: 'Playfair Display', serif; color: var(--primary-blue); margin-bottom: 0.75rem;">Notes on Bank Statements</h4>
                    <div class="bank-notes-content">
                        <?php echo $loan->bank_statement_notes; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="dd-card full-width">
                    <div class="dd-card-content">
                        <p style="text-align: center; color: var(--text-muted);">No bank statement notes available.</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Section 5: About the Transaction -->
        <section class="section">
            <div class="section-header">
                <span class="section-number">5</span>
                <h2 class="section-title">About the Transaction</h2>
            </div>
            <div class="section-body">
                <?php if (!empty($loan->about_transaction)): ?>
                <div class="dd-card full-width">
                    <div class="dd-card-content">
                        <?php echo $loan->about_transaction; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="dd-card full-width">
                    <div class="dd-card-content">
                        <p style="text-align: center; color: var(--text-muted);">No transaction details provided.</p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Loan Narration -->
                <?php if (!empty($loan->narration)): ?>
                <div class="dd-card full-width" style="margin-top: 1.5rem;">
                    <h3 class="dd-card-title">Loan Purpose / Narration</h3>
                    <div class="dd-card-content">
                        <?php echo $loan->narration; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Section 6: Pricing -->
        <section class="section">
            <div class="section-header">
                <span class="section-number">6</span>
                <h2 class="section-title">Pricing</h2>
            </div>
            <div class="section-body">
                <div class="pricing-display">
                    <div class="pricing-label">Interest Rate</div>
                    <div class="pricing-rate"><?php echo $loan_rate; ?><sup>%</sup></div>
                    <div style="color: rgba(255,255,255,0.6); font-size: 0.9rem;"><?php echo ucfirst($loan->calculation_type ?? 'Flat'); ?> Rate</div>
                </div>

                <div class="pricing-details">
                    <div class="pricing-detail">
                        <div class="detail-label">Principal Amount</div>
                        <div class="detail-value"><?php echo $currency->currency_code; ?> <?php echo number_format($loan_amount, 2); ?></div>
                    </div>
                    <div class="pricing-detail">
                        <div class="detail-label">Total Interest</div>
                        <div class="detail-value"><?php echo $currency->currency_code; ?> <?php echo number_format($total_interest ?? 0, 2); ?></div>
                    </div>
                    <div class="pricing-detail">
                        <div class="detail-label">Processing Fee</div>
                        <div class="detail-value"><?php echo ($loan->processing_fee ?? 0); ?>%</div>
                    </div>
                    <div class="pricing-detail">
                        <div class="detail-label">Total Repayment</div>
                        <div class="detail-value"><?php echo $currency->currency_code; ?> <?php echo number_format($total_repayment ?? ($loan_amount + ($total_interest ?? 0)), 2); ?></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 7: Risk Analysis -->
        <section class="section">
            <div class="section-header">
                <span class="section-number">7</span>
                <h2 class="section-title">Risk Analysis</h2>
            </div>
            <div class="section-body">
                <?php if (!empty($loan->risk_analysis)): ?>
                <div class="risk-content">
                    <?php echo $loan->risk_analysis; ?>
                </div>
                <?php else: ?>
                <div class="risk-grid">
                    <div class="risk-item">
                        <div class="risk-type">Fraudulent Risk</div>
                        <div class="risk-mitigation">Documentation verification and customer due diligence conducted.</div>
                    </div>
                    <div class="risk-item">
                        <div class="risk-type">Delay in Payment</div>
                        <div class="risk-mitigation">Payment schedule aligned with customer's cash flow cycle.</div>
                    </div>
                    <div class="risk-item">
                        <div class="risk-type">Risk of Diversion</div>
                        <div class="risk-mitigation">Loan purpose verified and monitored through documentation.</div>
                    </div>
                    <div class="risk-item">
                        <div class="risk-type">Default Risk</div>
                        <div class="risk-mitigation">Security measures in place as per facility terms.</div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($loan->security_notes)): ?>
                <div style="margin-top: 1.5rem;">
                    <h4 style="font-family: 'Playfair Display', serif; color: var(--primary-blue); margin-bottom: 1rem;">Security / Collateral Notes</h4>
                    <div class="dd-card full-width">
                        <div class="dd-card-content">
                            <?php echo $loan->security_notes; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Section 8: Regulatory Environment -->
        <section class="section">
            <div class="section-header">
                <span class="section-number">8</span>
                <h2 class="section-title">Regulatory Environment</h2>
            </div>
            <div class="section-body" style="padding: 0;">
                <table class="reg-table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Requirement</th>
                            <th style="text-align: center; width: 80px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="category" rowspan="4">Large Loan Exposure<br><small style="font-weight: normal; color: var(--text-muted);">SI No. 96 of 1996</small></td>
                            <td>Falls under category of large loan exposure</td>
                            <td style="text-align: center;"><span class="compliance-badge no">N</span></td>
                        </tr>
                        <tr>
                            <td>Exposure exceeds 10% of Regulatory Capital</td>
                            <td style="text-align: center;"><span class="compliance-badge no">N</span></td>
                        </tr>
                        <tr>
                            <td>Facilities total to more than 25% of RC</td>
                            <td style="text-align: center;"><span class="compliance-badge no">N</span></td>
                        </tr>
                        <tr>
                            <td>Aggregate of all large loans exceeds 600% of RC</td>
                            <td style="text-align: center;"><span class="compliance-badge no">N</span></td>
                        </tr>
                        <tr>
                            <td class="category" rowspan="3">Insider Lending<br><small style="font-weight: normal; color: var(--text-muted);">SI No. 97 of 1996</small></td>
                            <td>Falls under category of insider lending</td>
                            <td style="text-align: center;"><span class="compliance-badge no">&mdash;</span></td>
                        </tr>
                        <tr>
                            <td>This insider exposure totals to more than 10% of RC</td>
                            <td style="text-align: center;"><span class="compliance-badge no">N</span></td>
                        </tr>
                        <tr>
                            <td>All insider lending exceeds 100% of RC</td>
                            <td style="text-align: center;"><span class="compliance-badge no">N</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Recommendation & Signatures -->
        <section class="section">
            <div class="section-header">
                <span class="section-number"><i class="fa fa-check"></i></span>
                <h2 class="section-title">Recommendation & Verification</h2>
            </div>
            <div class="section-body">
                <div style="background: rgba(45, 106, 79, 0.08); border-left: 4px solid var(--success-green); padding: 1.5rem; border-radius: 0 8px 8px 0; margin-bottom: 2rem;">
                    <p style="font-size: 1.1rem; color: var(--text-dark); line-height: 1.7;">
                        <?php if (!empty($previous_loans) && count($previous_loans) > 0): ?>
                        Based on the comprehensive due diligence conducted, <?php echo $customer_name; ?> has a history of <strong><?php echo count($previous_loans); ?> previous facility(ies)</strong> with this institution.
                        <?php else: ?>
                        This is a new facility application from <?php echo $customer_name; ?>.
                        <?php endif; ?>
                        The requested facility amount is <strong><?php echo $currency->currency_code; ?> <?php echo number_format($loan_amount, 2); ?></strong> for a period of <strong><?php echo $loan_term; ?> <?php echo $term_unit_display; ?></strong>.
                    </p>
                </div>

                <?php if (!empty($approvers) && count($approvers) > 0): ?>
                <div class="signatures-grid">
                    <?php $approval_num = 1; foreach ($approvers as $approver): ?>
                    <div class="signature-block">
                        <div class="signature-role"><?php echo $approver->approval_level ?? 'Level '.$approval_num++; ?> Approval</div>
                        <div class="signature-name"><?php echo $approver->first_name.' '.$approver->last_name; ?></div>
                        <div class="signature-line"></div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">
                            <?php echo date('d M Y, H:i', strtotime($approver->date_stamp ?? $approver->approved_at ?? 'now')); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="signatures-grid">
                    <div class="signature-block">
                        <div class="signature-role">Relationship Manager</div>
                        <div class="signature-name"><?php echo $created_by->Firstname.' '.$created_by->Lastname; ?></div>
                        <div class="signature-line"></div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Signature</div>
                    </div>
                    <div class="signature-block">
                        <div class="signature-role">Credit Appraiser</div>
                        <div class="signature-name">_________________</div>
                        <div class="signature-line"></div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Signature</div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="report-footer">
        <div class="footer-brand"><?php echo $company_name ?? 'Fundit Capital Solutions'; ?></div>
        <div class="footer-text">
            <?php if (!empty($company_phone) || !empty($company_email)): ?>
            <?php echo $company_phone; ?><?php echo (!empty($company_phone) && !empty($company_email)) ? ' &bull; ' : ''; ?><?php echo $company_email; ?><br>
            <?php endif; ?>
            Confidential Credit Appraisal Report &bull; Generated <?php echo date('d F Y'); ?>
        </div>
    </footer>
</body>
</html>
