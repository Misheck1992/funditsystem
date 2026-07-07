<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Profile - <?= $customer->first_name . ' ' . $customer->last_name ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        .header h2 {
            font-size: 14px;
            font-weight: normal;
            color: #666;
        }

        .customer-number {
            font-size: 12px;
            font-weight: bold;
            margin-top: 10px;
            color: #0066cc;
        }

        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .section-title {
            background-color: #f5f5f5;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: bold;
            border-left: 4px solid #0066cc;
            margin-bottom: 10px;
        }

        .info-grid {
            display: table;
            width: 100%;
        }

        .info-row {
            display: table-row;
        }

        .info-cell {
            display: table-cell;
            padding: 5px 10px;
            width: 50%;
            vertical-align: top;
        }

        .info-item {
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            color: #666;
            font-size: 10px;
            text-transform: uppercase;
        }

        .info-value {
            font-size: 11px;
            color: #333;
            margin-top: 2px;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background-color: #f8d7da;
            color: #721c24;
        }

        .deposits-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }

        .deposits-table th,
        .deposits-table td {
            border: 1px solid #ddd;
            padding: 6px 8px;
            text-align: left;
        }

        .deposits-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .deposits-table tr:nth-child(even) {
            background-color: #fafafa;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
        }

        .footer-row {
            display: flex;
            justify-content: space-between;
        }

        .kyc-checklist {
            margin-top: 10px;
        }

        .kyc-item {
            padding: 4px 0;
            font-size: 10px;
        }

        .kyc-item .check {
            color: #28a745;
            font-weight: bold;
        }

        .kyc-item .missing {
            color: #dc3545;
            font-weight: bold;
        }

        .no-print {
            margin-bottom: 20px;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; background: #0066cc; color: white; border: none; cursor: pointer; margin-right: 10px;">Print PDF</button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #666; color: white; border: none; cursor: pointer;">Close</button>
    </div>

    <div class="header">
        <h1><?= $this->session->userdata('company_name') ?: 'FUNDIT Capital Solutions Limited' ?></h1>
        <h2>Fixed Deposit Customer Profile</h2>
        <div class="customer-number">Customer No: <?= $customer->customer_number ?></div>
    </div>

    <!-- Personal Information -->
    <div class="section">
        <div class="section-title">Personal Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-item">
                        <div class="info-label">Full Name</div>
                        <div class="info-value"><?= $customer->first_name . ' ' . $customer->last_name ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Gender</div>
                        <div class="info-value"><?= $customer->gender ?: 'N/A' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Date of Birth</div>
                        <div class="info-value"><?= !empty($customer->date_of_birth) ? date('d M Y', strtotime($customer->date_of_birth)) : 'N/A' ?></div>
                    </div>
                </div>
                <div class="info-cell">
                    <div class="info-item">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <span class="status-badge <?= $customer->status == 'ACTIVE' ? 'status-active' : 'status-inactive' ?>">
                                <?= $customer->status ?>
                            </span>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Registration Date</div>
                        <div class="info-value"><?= date('d M Y', strtotime($customer->created_at)) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="section">
        <div class="section-title">Contact Information</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-item">
                        <div class="info-label">Phone Number</div>
                        <div class="info-value"><?= $customer->phone_number ?: 'N/A' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Alt. Phone Number</div>
                        <div class="info-value"><?= !empty($customer->alt_phone_number) ? $customer->alt_phone_number : 'N/A' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email Address</div>
                        <div class="info-value"><?= $customer->email ?: 'N/A' ?></div>
                    </div>
                </div>
                <div class="info-cell">
                    <div class="info-item">
                        <div class="info-label">Province</div>
                        <div class="info-value"><?= $customer->province ?: 'N/A' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">District</div>
                        <div class="info-value"><?= !empty($customer->district) ? $customer->district : 'N/A' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">City</div>
                        <div class="info-value"><?= !empty($customer->city) ? $customer->city : 'N/A' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Physical Address</div>
                        <div class="info-value"><?= $customer->address ?: 'N/A' ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employment / Source of Funds -->
    <div class="section">
        <div class="section-title">Employment / Source of Funds</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-item">
                        <div class="info-label">Occupation</div>
                        <div class="info-value"><?= !empty($customer->occupation) ? $customer->occupation : 'N/A' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Employer</div>
                        <div class="info-value"><?= !empty($customer->employer) ? $customer->employer : 'N/A' ?></div>
                    </div>
                </div>
                <div class="info-cell">
                    <div class="info-item">
                        <div class="info-label">Source of Funds</div>
                        <div class="info-value"><?= !empty($customer->source_of_funds) ? $customer->source_of_funds : 'N/A' ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Identification Documents -->
    <div class="section">
        <div class="section-title">Identification Documents</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-item">
                        <div class="info-label">ID Type</div>
                        <div class="info-value"><?= $customer->id_type ?: 'N/A' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">ID Number</div>
                        <div class="info-value"><?= $customer->id_number ?: 'N/A' ?></div>
                    </div>
                </div>
                <div class="info-cell">
                    <div class="info-item">
                        <div class="info-label">ID Expiry Date</div>
                        <div class="info-value"><?= !empty($customer->id_expiry_date) ? date('d M Y', strtotime($customer->id_expiry_date)) : 'N/A' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">NRC/ID Photo</div>
                        <div class="info-value"><?= !empty($customer->nrc_photo) ? 'On File' : 'Not Uploaded' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Proof of Income</div>
                        <div class="info-value"><?= !empty($customer->proof_of_income) ? 'On File' : 'Not Uploaded' ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Next of Kin Details -->
    <div class="section">
        <div class="section-title">Next of Kin Details</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-item">
                        <div class="info-label">Full Name</div>
                        <div class="info-value"><?= !empty($customer->nok_name) ? $customer->nok_name : 'N/A' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Relationship</div>
                        <div class="info-value"><?= !empty($customer->nok_relationship) ? $customer->nok_relationship : 'N/A' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">ID Number</div>
                        <div class="info-value"><?= !empty($customer->nok_id_number) ? $customer->nok_id_number : 'N/A' ?></div>
                    </div>
                </div>
                <div class="info-cell">
                    <div class="info-item">
                        <div class="info-label">Phone Number</div>
                        <div class="info-value"><?= !empty($customer->nok_phone) ? $customer->nok_phone : 'N/A' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Address</div>
                        <div class="info-value"><?= !empty($customer->nok_address) ? $customer->nok_address : 'N/A' ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bank Details -->
    <div class="section">
        <div class="section-title">Bank Details (for Payouts)</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-item">
                        <div class="info-label">Bank Name</div>
                        <div class="info-value"><?= !empty($customer->bank_name) ? $customer->bank_name : 'N/A' ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Account Number</div>
                        <div class="info-value"><?= !empty($customer->bank_account_number) ? $customer->bank_account_number : 'N/A' ?></div>
                    </div>
                </div>
                <div class="info-cell">
                    <div class="info-item">
                        <div class="info-label">Branch</div>
                        <div class="info-value"><?= !empty($customer->bank_branch) ? $customer->bank_branch : 'N/A' ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KYC Checklist -->
    <div class="section">
        <div class="section-title">KYC Compliance Checklist</div>
        <div class="kyc-checklist">
            <?php
            $kyc_items = [
                'ID Number' => !empty($customer->id_number),
                'Phone Number' => !empty($customer->phone_number),
                'Email Address' => !empty($customer->email),
                'Physical Address' => !empty($customer->address),
                'NRC/ID Photo' => !empty($customer->nrc_photo),
                'Proof of Income' => !empty($customer->proof_of_income),
                'Next of Kin Name' => !empty($customer->nok_name),
                'Next of Kin Phone' => !empty($customer->nok_phone),
                'Bank Details' => !empty($customer->bank_name) && !empty($customer->bank_account_number),
            ];
            $completed = array_filter($kyc_items);
            $percentage = count($kyc_items) > 0 ? round((count($completed) / count($kyc_items)) * 100) : 0;
            ?>
            <div style="margin-bottom: 10px;">
                <strong>KYC Completion: <?= $percentage ?>%</strong>
                (<?= count($completed) ?> of <?= count($kyc_items) ?> items)
            </div>
            <?php foreach ($kyc_items as $item => $complete): ?>
                <div class="kyc-item">
                    <span class="<?= $complete ? 'check' : 'missing' ?>"><?= $complete ? '[X]' : '[ ]' ?></span>
                    <?= $item ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Deposits Summary -->
    <?php if (!empty($deposits)): ?>
    <div class="section">
        <div class="section-title">Fixed Deposits Summary</div>
        <table class="deposits-table">
            <thead>
                <tr>
                    <th>Deposit No.</th>
                    <th>Currency</th>
                    <th class="text-right">Principal</th>
                    <th>Interest Rate</th>
                    <th>Start Date</th>
                    <th>Maturity Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_principal = 0;
                foreach ($deposits as $deposit):
                    $total_principal += $deposit->current_principal;
                ?>
                <tr>
                    <td><?= $deposit->deposit_number ?></td>
                    <td><?= $deposit->currency ?? 'ZMW' ?></td>
                    <td class="text-right"><?= number_format($deposit->current_principal, 2) ?></td>
                    <td><?= $deposit->interest_rate ?>%</td>
                    <td><?= date('d M Y', strtotime($deposit->start_date)) ?></td>
                    <td><?= date('d M Y', strtotime($deposit->maturity_date)) ?></td>
                    <td>
                        <span class="status-badge <?= $deposit->status == 'ACTIVE' ? 'status-active' : 'status-inactive' ?>">
                            <?= $deposit->status ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2">Total</th>
                    <th class="text-right"><?= number_format($total_principal, 2) ?></th>
                    <th colspan="4"></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php endif; ?>

    <div class="footer">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%;">
                    <strong>Generated On:</strong> <?= date('d M Y H:i:s') ?>
                </td>
                <td style="width: 50%; text-align: right;">
                    <strong>Generated By:</strong> <?= $this->session->userdata('username') ?>
                </td>
            </tr>
        </table>
        <div style="margin-top: 10px; text-align: center; font-size: 9px; color: #999;">
            This is a system-generated document. For official purposes, please contact the office.
        </div>
    </div>
</body>
</html>
