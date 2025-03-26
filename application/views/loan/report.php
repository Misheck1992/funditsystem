<?php
$linkk = base_url('admin_assets/images/pattern.png');
$imgg = 'data:image;base64,'.base64_encode(file_get_contents($linkk));
$currency = get_by_id('currencies','currency_id',$currency);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head>
	<style>

		p {
			text-align: justify;
			margin:0;
		}
		table {width:100%;}
		table.collapse {
			border-collapse: collapse;
		}

		tr td, tr th {
			text-align: right;
		}

		tr.total {
			font-weight: 900;
		}
		hr {
			margin: 15px 0;
		}
		h1 {
			margin:0;
		}
		.title {
			color: #000;
			font-size: 18px;
			font-weight: normal;
		}

		.section {
			border-bottom: 1px #D4D4D4 solid;
			padding: 10px 0;
			margin-bottom: 20px;
		}

		.section .content {
			margin-left: 10px;
		}

		#hor-minimalist-b
		{
			font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
			font-size: 12px;
			background: #fff;
			width: 480px;
			border-collapse: collapse;
			text-align: center;
		}
		#hor-minimalist-b th
		{
			font-size: 14px;
			font-weight: 900;
			padding: 10px 8px;
			border-bottom: 2px solid #000;
			text-align: center;
		}
		#hor-minimalist-b td
		{
			border-bottom: 1px solid #ccc;
			padding: 6px 8px;
		}

		#pattern-style-a
		{
			font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
			font-size: 12px;
			width: 100%;
			text-align: left;
			border-collapse: collapse;
			background: url('<?php echo $imgg; ?>');;
		}

		#pattern-style-a th
		{
			font-size: 13px;
			font-weight: normal;
			padding: 8px;
			border-bottom: 1px solid #fff;
			color: #039;
		}
		#pattern-style-a td
		{
			padding: 3px;
			border-bottom: 1px solid #fff;
			color: #000;
			border-top: 1px solid transparent;
		}
		#pattern-style-a tbody tr:hover td
		{
			color: #339;
			background: #fff;
		}

		* {
			box-sizing: border-box;
		}

		html {
			font-family: sans-serif;
		}


	</style>

</head><body>



	<div class="section">
		<div class="content">
			<h1 style="text-align: center;"><?php
				$settings = get_by_id('settings','settings_id','1');
				echo $settings->company_name ?></h1>
			<table width="100%">
				<?php

				$link = base_url('uploads/').$settings->logo;
				$img = 'data:image;base64,'.base64_encode(file_get_contents($link))
				?>
				<tr>
					<td style="float: left;padding-right: 5em; margin-left: 1em;">
						<img src="<?php echo $img; ?>" alt="">
					</td>
					<td style="float: right;margin-left: 5em;">
						<?php echo $settings->address ?>
						<?php echo $settings->company_email ?>/<?php echo $settings->phone_number ?>
					</td>
				</tr>
			</table>
			<hr>
			<h2 style="text-align: center;">Loan Summary Report</h2>

			<table id="pattern-style-a">
				<tr>
					<td colspan="2">
						<table>
							<tr><td width="40%">Borrower Name:</td><td><strong><?= $loan_customer ?></strong></td></tr>
							<tr><td>Principal Amount:</td><td><strong><?php echo $currency->currency_code ?> &nbsp;<?= number_format($loan_principal,2) ?></strong></td></tr>
							<tr><td>Total loan Amount:</td><td><strong><?php echo $currency->currency_code ?> &nbsp;<?= number_format($loan_amount_total,2) ?></strong></td></tr>
							<tr><td>Interest rate:</td><td><strong><?= $loan_interest ?>%</strong></td></tr>
							<tr><td>Loan term:</td><td><strong><?= $loan_period ?> <?= $period_type ?></strong></td></tr>
							<tr><td>Amortization:</td><td><strong><?php echo $currency->currency_code ?> &nbsp;<?= number_format($loan_amount_term,2) ?></strong></td></tr>
						</table>
					</td>
					<td colspan="4"></td>
					<td colspan="2">
						<table>
							<tr><td>Loan ID:</td><td><strong><?php echo $loan_number ?></strong></td></tr>
							<tr><td>Loan Date:</td><td><strong><?= $loan_date ?></strong></td></tr>
							<tr><td>Maturity Date:</td><td><strong><?= $maturity_date ?></strong></td></tr>
							<tr><td>Last Deduction:</td><td><strong><?php echo $currency->currency_code ?> &nbsp;<?= number_format($maturity_pay,2) ?></strong></td></tr>
							<tr><td>First Deduction:</td><td><strong><?php echo $currency->currency_code ?> &nbsp;<?=  number_format($first_payment,2)?></strong></td></tr>
							<tr><td>Deduction Date:</td><td><strong><?=  $first_payment_date ?></strong></td></tr>

						</table>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<div class="section">
		<div class="title">Summary</div>
		<br>
		<div class="content">
			<table class="collapse" id="pattern-style-a">
				<thead>
				<tr>
					<th>Payment #</th>
					<th>Check Date</th>
					<th>Principal(<?php echo $currency->currency_code ?>)</th>
					<th>Interest(<?php echo $currency->currency_code ?>)</th>
					<th>Amount(<?php echo $currency->currency_code ?>)</th>
					<th>Amount Paid(<?php echo $currency->currency_code ?>)</th>
					<th>Loan Balance(<?php echo $currency->currency_code ?>)</th>
					<th>Status</th>

				</tr>
				</thead>
				<tbody>
				<?php
				foreach ($payments as $p){
					?>
					<?php
					//change color depending on it's status
					$css = '';
					$xstatus = '';
					if($p->payment_schedule < date('Y-m-d')   AND $p->status == 'NOT PAID') {
						$css = ' class="due"';
						$xstatus = ' | OVER DUE';
					} elseif($p->status=='PAID') {
						$css = 'class="paid"';
					} elseif($p->payment_schedule == date('Y-m-d')  AND $p->status == 'NOT PAID') {
						$css = ' class="due_now"';
						$xstatus = ' | DUE TODAY';
					}
					?>
					<!--							<tr style="border: 1px solid black;background-color: #F3D8D8;">-->
					<tr>
						<td <?php echo $css; ?>><?php  echo $p->payment_number?></td>
						<td <?php echo $css; ?>><?php  echo $p->payment_schedule?></td>
						<td <?php echo $css; ?>><?php  echo number_format($p->principal,2) ?></td>
						<td <?php echo $css; ?>><?php  echo number_format($p->interest,2) ?></td>
						<td <?php echo $css; ?>><?php  echo number_format($p->amount,2) ?></td>
						<td <?php echo $css; ?>><?php  echo number_format($p->paid_amount,2)?></td>
						<td <?php echo $css; ?>><?php  echo number_format($p->loan_balance,2)?></td>
						<td><?php echo $p->status.$xstatus; ?></td>
					</tr>
					<?php
				}
				?>


				</tbody>
			</table>
            <hr>
            <h4>Deposit/Payment History</h4>
            <div style="overflow: scroll;">
                <table  class="collapse" id="pattern-style-a">
                    <thead>
                    <tr style="border: 1px solid black;">
                        <th>Deposit amount</th>
                        <th>Transaction Ref</th>

                        <th>Payment Date</th>
                        <th>Cashier account</th>


                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $trans = get_all_where('transaction','account_number = "'.$loan_number.'" AND credit !=0');
                    foreach ($trans as $history)
                    {

                        ?>
                        <tr>
                            <td><?php echo $currency->currency_code ?> &nbsp;<?php echo number_format($history->credit,2); ?></td>
                            <td><?php echo $history->transaction_id; ?></td>
                              <td><?php echo $history->system_time; ?></td>
                            <td><?php echo $history->coresponding_account; ?></td>

                        </tr>

                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
		</div>
	</div>
	<div style="margin: auto"><strong>********** NOTHING FOLLOWS **********</strong></div>


</body></html>
