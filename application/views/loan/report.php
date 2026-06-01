<?php
$linkk = base_url('admin_assets/images/pattern.png');
$imgg = 'data:image;base64,'.base64_encode(file_get_contents($linkk));
$currency = get_by_id('currencies','currency_id',$currency);
$cc = $currency->currency_code;

// Force-close vars (passed from controller; default to safe values)
$is_fc   = !empty($is_force_closed);
$fcl_amt = isset($fcl_amount_paid)       ? (float)$fcl_amount_paid       : 0;
$fcl_dt  = isset($fcl_payment_date)      ? $fcl_payment_date              : null;
$acc_int = isset($accrued_at_settlement) ? (float)$accrued_at_settlement  : 0;

$principal_f  = (float)$loan_principal;
$mat_interest = (float)($loan_amount_total - $loan_principal); // contract interest
$monthly_rate = (float)$loan_interest / 100;
$term         = (int)$loan_period;

$fcl_int_paid  = max(0, $fcl_amt - $principal_f);
$interest_waived_amt = ($acc_int > 0) ? max(0, $acc_int - $fcl_int_paid) : max(0, $mat_interest - $fcl_int_paid);
$int_unaccrued = ($acc_int > 0) ? max(0, $mat_interest - $acc_int) : 0;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head>
<style>
p { text-align:justify; margin:0; }
table { width:100%; }
table.collapse { border-collapse:collapse; }
tr td, tr th { text-align:right; }
tr.total { font-weight:900; }
hr { margin:15px 0; }
h1 { margin:0; }
.title { color:#000; font-size:18px; font-weight:normal; }
.section { border-bottom:1px #D4D4D4 solid; padding:10px 0; margin-bottom:20px; }
.section .content { margin-left:10px; }

#hor-minimalist-b {
    font-family:"Lucida Sans Unicode","Lucida Grande",Sans-Serif;
    font-size:12px; background:#fff; width:480px;
    border-collapse:collapse; text-align:center;
}
#hor-minimalist-b th { font-size:14px; font-weight:900; padding:10px 8px; border-bottom:2px solid #000; text-align:center; }
#hor-minimalist-b td { border-bottom:1px solid #ccc; padding:6px 8px; }

#pattern-style-a {
    font-family:"Lucida Sans Unicode","Lucida Grande",Sans-Serif;
    font-size:12px; width:100%; text-align:left;
    border-collapse:collapse;
    background:url('<?php echo $imgg; ?>');
}
#pattern-style-a th { font-size:13px; font-weight:normal; padding:8px; border-bottom:1px solid #fff; color:#039; }
#pattern-style-a td { padding:3px; border-bottom:1px solid #fff; color:#000; border-top:1px solid transparent; }
#pattern-style-a tbody tr:hover td { color:#339; background:#fff; }

/* Ledger table */
#ledger { font-family:"Lucida Sans Unicode","Lucida Grande",Sans-Serif; font-size:11px; width:100%; border-collapse:collapse; }
#ledger th { background:#1e3a5f; color:#fff; padding:7px 6px; text-align:left; font-size:11px; font-weight:bold; }
#ledger th.num { text-align:right; }
#ledger td { padding:5px 6px; border-bottom:1px solid #e0e0e0; font-size:11px; }
#ledger td.num { text-align:right; }
#ledger tr.debit  td { background:#fff8f8; }
#ledger tr.credit td { background:#f0fff4; }
#ledger tr.waiver td { background:#fffbeb; }
#ledger tr.closing td { background:#1e3a5f; color:#fff; font-weight:bold; }
#ledger tr.closing td.num { text-align:right; }
.cr { color:#1a7a1a; }
.dr { color:#000; }
.neg { color:#b45309; }

/* Settlement analysis box */
.settle-box { border:1px solid #ccc; border-radius:4px; margin-bottom:14px; overflow:hidden; }
.settle-box-header { background:#7f1d1d; color:#fff; padding:6px 10px; font-size:13px; font-weight:bold; }
.settle-box-header.normal { background:#064e3b; }
.settle-row { display:table; width:100%; border-collapse:collapse; }
.settle-cell { display:table-cell; width:25%; padding:6px 8px; border-right:1px solid #eee; font-size:11px; vertical-align:top; }
.settle-cell:last-child { border-right:none; }
.settle-label { color:#666; font-size:10px; text-transform:uppercase; margin-bottom:2px; }
.settle-value { font-weight:bold; font-size:12px; color:#1e3a5f; }
.settle-note  { font-size:9px; color:#999; }
.settle-value.red { color:#991b1b; }
.settle-value.green { color:#065f46; }

* { box-sizing:border-box; }
html { font-family:sans-serif; }
</style>
</head><body>

<!-- HEADER -->
<div class="section">
    <div class="content">
        <h1 style="text-align:center;"><?php
            $settings = get_by_id('settings','settings_id','1');
            echo $settings->company_name; ?></h1>
        <table width="100%">
            <?php
            $link = base_url('uploads/').$settings->logo;
            $img  = 'data:image;base64,'.base64_encode(file_get_contents($link));
            ?>
            <tr>
                <td style="float:left;padding-right:5em;margin-left:1em;"><img src="<?php echo $img; ?>" alt=""></td>
                <td style="float:right;margin-left:5em;"><?php echo $settings->address ?><br><?php echo $settings->company_email ?>/<?php echo $settings->phone_number ?></td>
            </tr>
        </table>
        <hr>
        <h2 style="text-align:center;">Loan Statement<?php if ($is_fc): ?> &mdash; <span style="color:#991b1b;">FORCED CLOSE</span><?php endif; ?></h2>

        <table id="pattern-style-a">
            <tr>
                <td colspan="2">
                    <table>
                        <tr><td width="40%">Borrower Name:</td><td><strong><?= $loan_customer ?></strong></td></tr>
                        <tr><td>Principal Amount:</td><td><strong><?= $cc ?> <?= number_format($loan_principal,2) ?></strong></td></tr>
                        <?php
                        if (isset($calculation_type) && $calculation_type == 'Bullet Payment'):
                        ?>
                        <tr><td>Interest on Maturity (<?= $loan_interest ?>% x <?= $loan_period ?> mo):</td><td><strong><?= $cc ?> <?= number_format($mat_interest,2) ?></strong></td></tr>
                        <?php if ($is_fc && $acc_int > 0): ?>
                        <tr><td>Interest Accrued to Settlement:</td><td><strong><?= $cc ?> <?= number_format($acc_int,2) ?></strong></td></tr>
                        <tr><td>Total at Settlement Date:</td><td><strong><?= $cc ?> <?= number_format($principal_f + $acc_int,2) ?></strong></td></tr>
                        <?php else: ?>
                        <tr><td>Total Loan Amount (at maturity):</td><td><strong><?= $cc ?> <?= number_format($loan_amount_total,2) ?></strong></td></tr>
                        <?php endif; ?>
                        <?php else: ?>
                        <tr><td>Total Loan Amount:</td><td><strong><?= $cc ?> <?= number_format($loan_amount_total,2) ?></strong></td></tr>
                        <?php endif; ?>
                        <tr><td>Interest Rate:</td><td><strong><?= $loan_interest ?>% per <?= $period_type ?></strong></td></tr>
                        <tr><td>Loan Term:</td><td><strong><?= $loan_period ?> <?= $period_type ?></strong></td></tr>
                        <?php if ($is_fc): ?>
                        <tr><td>Settlement Type:</td><td><strong style="color:#991b1b;">FORCED CLOSE</strong></td></tr>
                        <?php if ($fcl_dt): ?><tr><td>Settlement Date:</td><td><strong><?= date('d M Y', strtotime($fcl_dt)) ?></strong></td></tr><?php endif; ?>
                        <tr><td>Amount Paid at Settlement:</td><td><strong><?= $cc ?> <?= number_format($fcl_amt,2) ?></strong></td></tr>
                        <?php endif; ?>
                    </table>
                </td>
                <td colspan="4"></td>
                <td colspan="2">
                    <table>
                        <tr><td>Loan ID:</td><td><strong><?= $loan_number ?></strong></td></tr>
                        <tr><td>Loan Date:</td><td><strong><?= $loan_date ?></strong></td></tr>
                        <tr><td>Maturity Date:</td><td><strong><?= $maturity_date ?></strong></td></tr>
                        <tr><td>Status:</td><td><strong><?= $loan_status ?></strong></td></tr>
                        <tr><td>First Instalment:</td><td><strong><?= $cc ?> <?= number_format($first_payment,2) ?></strong></td></tr>
                        <tr><td>Last Instalment:</td><td><strong><?= $cc ?> <?= number_format($maturity_pay,2) ?></strong></td></tr>
                        <tr><td>Instalment Date:</td><td><strong><?= $first_payment_date ?></strong></td></tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</div>

<?php if ($is_fc): ?>
<!-- INTEREST SETTLEMENT ANALYSIS BOX -->
<div class="section">
    <div class="title">Interest Settlement Analysis</div>
    <br>
    <div class="content">
        <div class="settle-box">
            <div class="settle-box-header">Force Close &mdash; Interest Breakdown</div>
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="width:25%;padding:8px;border-right:1px solid #eee;vertical-align:top;">
                        <div style="color:#666;font-size:10px;text-transform:uppercase;margin-bottom:3px;">Interest on Maturity</div>
                        <div style="font-weight:bold;font-size:13px;color:#1e3a5f;"><?= $cc ?> <?= number_format($mat_interest,2) ?></div>
                        <div style="font-size:9px;color:#999;">As per contract (<?= $loan_interest ?>% x <?= $loan_period ?> mo)</div>
                    </td>
                    <?php if ($acc_int > 0): ?>
                    <td style="width:25%;padding:8px;border-right:1px solid #eee;vertical-align:top;background:<?= $acc_int > $mat_interest ? '#fff8f8' : '#f8faff'; ?>;">
                        <div style="color:#666;font-size:10px;text-transform:uppercase;margin-bottom:3px;">Interest Accrued</div>
                        <div style="font-weight:bold;font-size:13px;color:<?= $acc_int > $mat_interest ? '#991b1b' : '#1e3a5f'; ?>;"><?= $cc ?> <?= number_format($acc_int,2) ?></div>
                        <div style="font-size:9px;color:#999;">To <?= $fcl_dt ? date('d M Y', strtotime($fcl_dt)) : 'settlement' ?><?= $acc_int > $mat_interest ? ' (incl. arrears)' : '' ?></div>
                    </td>
                    <?php endif; ?>
                    <td style="width:25%;padding:8px;border-right:1px solid #eee;vertical-align:top;background:#f0fff4;">
                        <div style="color:#666;font-size:10px;text-transform:uppercase;margin-bottom:3px;">Interest Paid</div>
                        <div style="font-weight:bold;font-size:13px;color:#065f46;"><?= $cc ?> <?= number_format($fcl_int_paid,2) ?></div>
                        <div style="font-size:9px;color:#999;">Collected at settlement</div>
                    </td>
                    <td style="width:25%;padding:8px;vertical-align:top;background:#fffbeb;">
                        <div style="color:#666;font-size:10px;text-transform:uppercase;margin-bottom:3px;">Interest Waived</div>
                        <div style="font-weight:bold;font-size:13px;color:#b45309;"><?= $cc ?> <?= number_format($interest_waived_amt,2) ?></div>
                        <div style="font-size:9px;color:#999;">Accrued but not collected</div>
                    </td>
                </tr>
                <?php if ($int_unaccrued > 0): ?>
                <tr>
                    <td colspan="4" style="padding:6px 8px;background:#f8fafc;border-top:1px solid #eee;font-size:11px;color:#374151;">
                        <em>Note: <?= $cc ?> <?= number_format($int_unaccrued,2) ?> of future interest was also saved — this had not yet accrued at the time of settlement (unaccrued interest = contract interest minus accrued interest).</em>
                    </td>
                </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- AMORTISATION SCHEDULE -->
<div class="section">
    <div class="title">Amortisation Schedule (Contract)</div>
    <br>
    <div class="content">
        <table class="collapse" id="pattern-style-a">
            <thead>
            <tr>
                <th>#</th>
                <th>Due Date</th>
                <?php if(isset($calculation_type) && $calculation_type == 'Bullet Payment'): ?>
                <th>Interest on Maturity (<?= $cc ?>)</th>
                <th>Accrued Interest (<?= $cc ?>)</th>
                <th>Amount Due (<?= $cc ?>)</th>
                <th>Amount Paid (<?= $cc ?>)</th>
                <th>Balance (<?= $cc ?>)</th>
                <?php else: ?>
                <th>Principal (<?= $cc ?>)</th>
                <th>Interest (<?= $cc ?>)</th>
                <th>Scheduled Amount (<?= $cc ?>)</th>
                <th>Amount Paid (<?= $cc ?>)</th>
                <th>Balance (<?= $cc ?>)</th>
                <?php endif; ?>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($payments as $p):
                $css    = '';
                $xstatus = '';
                if ($p->payment_schedule < date('Y-m-d') && $p->status == 'NOT PAID') {
                    $css = ' class="due"'; $xstatus = ' | OVERDUE';
                } elseif ($p->status == 'PAID') {
                    $css = 'class="paid"';
                } elseif ($p->payment_schedule == date('Y-m-d') && $p->status == 'NOT PAID') {
                    $css = ' class="due_now"'; $xstatus = ' | DUE TODAY';
                }

                // Determine display amounts for force-closed rows
                $display_amt_paid = (float)$p->paid_amount;
                $display_status   = $p->status.$xstatus;
                $is_fc_row = $is_fc && $p->status == 'PAID' && $display_amt_paid == 0;
                if ($is_fc_row) {
                    // Show actual FCL amount if it's the only installment or a force-settled row
                    $display_amt_paid = ($calculation_type == 'Bullet Payment') ? $fcl_amt : 0;
                    $display_status   = 'FORCE SETTLED';
                }
            ?>
            <tr>
                <td <?= $css ?>><?= $p->payment_number ?></td>
                <td <?= $css ?>><?= $p->payment_schedule ?></td>
                <?php if(isset($calculation_type) && $calculation_type == 'Bullet Payment'): ?>
                <td <?= $css ?>><?= number_format($p->interest, 2) ?></td>
                <td <?= $css ?>><?php
                    if ($p->status == 'PAID') echo number_format($acc_int > 0 ? $acc_int : $total_paid_interest, 2);
                    else echo isset($acrued['accrued_interest']) ? number_format($acrued['accrued_interest'],2) : number_format($p->interest,2);
                ?></td>
                <td <?= $css ?>><strong><?php
                    if ($p->status == 'PAID') echo number_format($principal_f + ($acc_int > 0 ? $acc_int : $total_paid_interest), 2);
                    else echo isset($acrued['total_payoff']) ? number_format($acrued['total_payoff'],2) : number_format($p->amount,2);
                ?></strong></td>
                <td <?= $css ?>><?= number_format($display_amt_paid, 2) ?></td>
                <td <?= $css ?>><strong><?= ($p->status == 'PAID') ? '0.00' : (isset($acrued['total_payoff']) ? number_format($acrued['total_payoff'] - $p->paid_amount,2) : number_format($p->loan_balance,2)) ?></strong></td>
                <?php else: ?>
                <td <?= $css ?>><?= number_format($p->principal,2) ?></td>
                <td <?= $css ?>><?= number_format($p->interest,2) ?></td>
                <td <?= $css ?>><?= number_format($p->amount,2) ?></td>
                <td <?= $css ?>><?= number_format($display_amt_paid,2) ?></td>
                <td <?= $css ?>><?= ($p->status=='PAID') ? '0.00' : number_format($p->amount - $p->paid_amount,2) ?></td>
                <?php endif; ?>
                <td><?= $display_status ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
            <?php if(!isset($calculation_type) || $calculation_type != 'Bullet Payment'): ?>
            <tfoot>
            <tr style="font-weight:bold;border-top:2px solid #000;background:#f5f5f5;">
                <td colspan="2">Totals</td>
                <td><?= isset($total_schedule_principal) ? number_format($total_schedule_principal,2) : '' ?></td>
                <td><?= isset($total_schedule_interest)  ? number_format($total_schedule_interest,2)  : '' ?></td>
                <td><?= isset($total_schedule_amount)    ? number_format($total_schedule_amount,2)    : '' ?></td>
                <td></td><td></td><td></td>
            </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php if ($is_fc): ?>
<!-- LOAN ACCOUNT STATEMENT (LEDGER) -->
<div class="section">
    <div class="title">Loan Account Statement</div>
    <br>
    <div class="content">
        <p style="font-size:11px;color:#555;margin-bottom:8px;">
            Double-entry ledger showing all charges (Dr) and settlements (Cr) from disbursement to closure.
            Closing balance confirms the account is fully settled at zero.
        </p>
        <table id="ledger">
            <thead>
                <tr>
                    <th style="width:12%;">Date</th>
                    <th style="width:38%;text-align:left;">Description</th>
                    <th class="num" style="width:16%;">Debit (Dr)</th>
                    <th class="num" style="width:16%;">Credit (Cr)</th>
                    <th class="num" style="width:18%;">Balance</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $bal = 0.0;

            // 1. Loan disbursed
            $bal += $principal_f;
            echo "<tr class='debit'>"
               . "<td>{$loan_date}</td>"
               . "<td>Loan Disbursed &ndash; Principal</td>"
               . "<td class='num dr'>".number_format($principal_f,2)."</td>"
               . "<td class='num'>&ndash;</td>"
               . "<td class='num dr'>".number_format($bal,2)." Dr</td>"
               . "</tr>";

            if ($calculation_type == 'Bullet Payment') {
                // 2. Interest on maturity
                $bal += $mat_interest;
                $mat_dt_str = date('Y-m-d', strtotime("+{$term} months", strtotime($loan_date)));
                echo "<tr class='debit'>"
                   . "<td>{$mat_dt_str}</td>"
                   . "<td>Interest on Maturity &ndash; {$loan_interest}% &times; {$term} month(s)</td>"
                   . "<td class='num dr'>".number_format($mat_interest,2)."</td>"
                   . "<td class='num'>&ndash;</td>"
                   . "<td class='num dr'>".number_format($bal,2)." Dr</td>"
                   . "</tr>";

                // 3. Arrears interest (if settled after maturity)
                if ($fcl_dt && strtotime($fcl_dt) > strtotime($mat_dt_str)) {
                    $mat_obj  = new DateTime($mat_dt_str);
                    $fcl_obj  = new DateTime(date('Y-m-d', strtotime($fcl_dt)));
                    $dp       = $mat_obj->diff($fcl_obj)->days;
                    $mpa      = (int)floor($dp / 30);
                    $rd       = $dp % 30;
                    $arr_bal  = $bal; // outstanding at maturity

                    for ($m = 1; $m <= $mpa; $m++) {
                        $m_int = round($arr_bal * $monthly_rate, 2);
                        $arr_bal += $m_int;
                        $bal     += $m_int;
                        $mo = clone $mat_obj; $mo->modify("+{$m} months");
                        echo "<tr class='debit'>"
                           . "<td>".$mo->format('Y-m-d')."</td>"
                           . "<td>Arrears Interest &ndash; Month {$m} (compound on outstanding)</td>"
                           . "<td class='num dr'>".number_format($m_int,2)."</td>"
                           . "<td class='num'>&ndash;</td>"
                           . "<td class='num dr'>".number_format($bal,2)." Dr</td>"
                           . "</tr>";
                    }
                    if ($rd > 0) {
                        $d_int = round(($arr_bal * $monthly_rate / 30) * $rd, 2);
                        $bal  += $d_int;
                        echo "<tr class='debit'>"
                           . "<td>".date('Y-m-d', strtotime($fcl_dt))."</td>"
                           . "<td>Arrears Interest &ndash; {$rd} day(s) pro-rata</td>"
                           . "<td class='num dr'>".number_format($d_int,2)."</td>"
                           . "<td class='num'>&ndash;</td>"
                           . "<td class='num dr'>".number_format($bal,2)." Dr</td>"
                           . "</tr>";
                    }
                }

                // 4. Payment received
                $bal -= $fcl_amt;
                echo "<tr class='credit'>"
                   . "<td>".date('Y-m-d', strtotime($fcl_dt))."</td>"
                   . "<td><strong>Payment Received &ndash; Force Close Settlement</strong></td>"
                   . "<td class='num'>&ndash;</td>"
                   . "<td class='num cr'>(".number_format($fcl_amt,2).")</td>"
                   . "<td class='num ".($bal > 0.005 ? 'dr' : 'cr')."'>".number_format(max(0,$bal),2).($bal > 0.005 ? " Dr" : "")."</td>"
                   . "</tr>";

                // 5. Interest waived (if balance still > 0)
                if ($bal > 0.005) {
                    $waived = $bal;
                    $bal    = 0;
                    echo "<tr class='waiver'>"
                       . "<td>".date('Y-m-d', strtotime($fcl_dt))."</td>"
                       . "<td><em>Interest Waived &ndash; Accrued interest not recovered (Force Close)</em></td>"
                       . "<td class='num'>&ndash;</td>"
                       . "<td class='num neg'>(".number_format($waived,2).")</td>"
                       . "<td class='num'>0.00</td>"
                       . "</tr>";
                }
            } else {
                // Non-bullet ledger:
                // Step 2 – charge interest per instalment on its due date
                foreach ($payments as $p) {
                    $int_amt = (float)$p->interest;
                    if ($int_amt > 0) {
                        $bal += $int_amt;
                        echo "<tr class='debit'>"
                           . "<td>".$p->payment_schedule."</td>"
                           . "<td>Interest Charged &ndash; Instalment #".$p->payment_number."</td>"
                           . "<td class='num dr'>".number_format($int_amt,2)."</td>"
                           . "<td class='num'>&ndash;</td>"
                           . "<td class='num dr'>".number_format($bal,2)." Dr</td>"
                           . "</tr>";
                    }
                }

                // Step 3 – normal cash payments (exclude FCL- entries to avoid double-count)
                $normal_pmts = get_all_where('transaction', 'account_number = "'.$loan_number.'" AND credit != 0 AND transaction_id NOT LIKE \'FCL-%\'');
                foreach ($normal_pmts as $tx) {
                    $bal -= (float)$tx->credit;
                    echo "<tr class='credit'>"
                       . "<td>".date('Y-m-d', strtotime($tx->system_time))."</td>"
                       . "<td>Payment Received &ndash; ".$tx->transaction_id."</td>"
                       . "<td class='num'>&ndash;</td>"
                       . "<td class='num cr'>(".number_format($tx->credit,2).")</td>"
                       . "<td class='num ".($bal > 0.005 ? 'dr' : 'cr')."'>".number_format(max(0,$bal),2).($bal > 0.005 ? " Dr" : "")."</td>"
                       . "</tr>";
                }

                // Step 4 – FCL payment
                if ($fcl_amt > 0) {
                    $bal -= $fcl_amt;
                    echo "<tr class='credit'>"
                       . "<td>".($fcl_dt ? date('Y-m-d', strtotime($fcl_dt)) : '')."</td>"
                       . "<td><strong>Payment Received &ndash; Force Close Settlement</strong></td>"
                       . "<td class='num'>&ndash;</td>"
                       . "<td class='num cr'>(".number_format($fcl_amt,2).")</td>"
                       . "<td class='num ".($bal > 0.005 ? 'dr' : 'cr')."'>".number_format(max(0,$bal),2).($bal > 0.005 ? " Dr" : "")."</td>"
                       . "</tr>";
                }

                // Step 5 – write off remaining balance as interest waived
                if ($bal > 0.005) {
                    $waived = $bal; $bal = 0;
                    echo "<tr class='waiver'>"
                       . "<td>".($fcl_dt ? date('Y-m-d', strtotime($fcl_dt)) : date('Y-m-d'))."</td>"
                       . "<td><em>Interest Waived &ndash; Accrued interest not recovered (Force Close)</em></td>"
                       . "<td class='num'>&ndash;</td>"
                       . "<td class='num neg'>(".number_format($waived,2).")</td>"
                       . "<td class='num'>0.00</td>"
                       . "</tr>";
                }
            }
            ?>
            </tbody>
            <tfoot>
                <tr class="closing">
                    <td colspan="4">Closing Balance &mdash; Account Fully Settled</td>
                    <td class="num">0.00</td>
                </tr>
            </tfoot>
        </table>
        <p style="font-size:10px;color:#888;margin-top:6px;">
            Dr = Debit (amount owed). Cr = Credit (amount paid or waived). Figures in parentheses ( ) are credits (negative postings).
        </p>
    </div>
</div>
<?php endif; ?>

<!-- DEPOSIT / PAYMENT HISTORY -->
<div class="section">
    <div class="title">Deposit / Payment History</div>
    <br>
    <div class="content">
        <table class="collapse" id="pattern-style-a">
            <thead>
            <tr>
                <th>Deposit Amount</th>
                <th>Transaction Ref</th>
                <th>Payment Date</th>
                <th>Cashier Account</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $trans = get_all_where('transaction','account_number = "'.$loan_number.'" AND credit !=0');
            $dep_total = 0;
            foreach ($trans as $history):
                $dep_total += (float)$history->credit;
            ?>
            <tr>
                <td><?= $cc ?> <?= number_format($history->credit,2) ?></td>
                <td><?= $history->transaction_id ?></td>
                <td><?= $history->system_time ?></td>
                <td><?= $history->coresponding_account ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (count($trans) > 1): ?>
            <tr style="font-weight:bold;border-top:2px solid #000;background:#f5f5f5;">
                <td>Total: <?= $cc ?> <?= number_format($dep_total,2) ?></td>
                <td colspan="3"></td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div style="margin:auto;"><strong>********** NOTHING FOLLOWS **********</strong></div>

</body></html>
