<?php
$linkk = base_url('admin_assets/images/pattern.png');
$imgg = 'data:image;base64,'.base64_encode(file_get_contents($linkk))
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
		<h2 style="text-align: center;">Loan Arrears  Report</h2>

		<table id="pattern-style-a">
			<tr>
				<td colspan="2">
					<table>
						<tr><td width="40%">Loan Report date:</td><td><strong><?= date('Y-m-d') ?></strong></td></tr>
						<tr><td>Loan Number:</td><td><strong><?= $product ?></strong></td></tr>

					</table>
				</td>
				<td colspan="4"></td>
				<td colspan="2">
					<table>
						<tr><td>Date from:</td><td><strong><?= $from ?></strong></td></tr>
						<tr><td>Date to:</td><td><strong><?= $to ?></strong></td></tr>

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
				<th>#</th>
				<th>Loan Customer</th>
				<th>Loan Number</th>
				<th>Type</th>
				<th>Due Date</th>
				<th>Original Amount</th>
				<th>Amount Due</th>
				<th>Days Overdue</th>

			</tr>
			</thead>
			<tbody>
			<?php
			$n = 1;

			foreach ($loan_data as $loan)
			{
				// Determine customer name
				if($loan->customer_type == 'individual'){
					$customer_name = $loan->ind_firstname.' '.$loan->ind_lastname;
				}elseif($loan->customer_type == 'institution'){
					$inst = get_by_id('corporate_customers','id',$loan->loan_customer);
					$customer_name = !empty($inst) ? $inst->EntityName : 'N/A';
				}elseif($loan->customer_type == 'group'){
					$group = get_by_id('customer_groups','id',$loan->loan_customer);
					$customer_name = !empty($group) ? $group->group_name : 'N/A';
				}else{
					$customer_name = 'Unknown';
				}

				$original_amount = $loan->amount;
				$amount_due = $loan->amount;
				$days_overdue = max(0, floor((strtotime(date('Y-m-d')) - strtotime($loan->payment_schedule)) / 86400));
				$is_bullet = (isset($loan->calculation_type) && $loan->calculation_type == 'Bullet Payment');

				if ($is_bullet && $days_overdue > 0) {
					$principal = floatval($loan->loan_principal);
					$monthly_rate = floatval($loan->loan_interest) / 100;
					$term = intval($loan->loan_period);
					$maturity_total = $principal + ($principal * $monthly_rate * $term);

					// Get payments made (reduces balance BEFORE compounding)
					$paid = floatval($loan->paid_amount ?? 0);
					$outstanding_at_maturity = $maturity_total - $paid;
					if ($outstanding_at_maturity < 0) $outstanding_at_maturity = 0;

					$maturity_date = date('Y-m-d', strtotime("+{$term} months", strtotime($loan->loan_date)));
					$days_past_maturity = max(0, floor((strtotime(date('Y-m-d')) - strtotime($maturity_date)) / 86400));

					if ($days_past_maturity > 0 && $outstanding_at_maturity > 0) {
						$full_months = floor($days_past_maturity / 30);
						$remaining_days = $days_past_maturity % 30;
						// Compound on outstanding balance
						$running_balance = $outstanding_at_maturity;
						for ($m = 0; $m < $full_months; $m++) {
							$running_balance *= (1 + $monthly_rate);
						}
						if ($remaining_days > 0) {
							$daily_int = ($running_balance * $monthly_rate) / 30;
							$running_balance += $daily_int * $remaining_days;
						}
						$amount_due = round($running_balance, 2);
					} else {
						$amount_due = $outstanding_at_maturity;
					}
				}
				?>
				<tr>

					<td><?php echo $n ?></td>
					<td><?php echo $customer_name; ?></td>

					<td><?php echo $loan->loan_number ?></td>

					<td><?php echo $is_bullet ? 'Bullet' : ($loan->calculation_type ?? 'Regular'); ?></td>
					<td><?php echo $loan->payment_schedule ?></td>
					<td><?php echo number_format($original_amount, 2) ?></td>
					<td style="<?php echo ($is_bullet && $amount_due > $original_amount) ? 'font-weight:bold;color:#dc2626;' : ''; ?>"><?php echo number_format($amount_due, 2) ?></td>
					<td><?php echo $days_overdue ?></td>

				</tr>
				<?php
				$n ++;
			}
			?>
			</tbody>
		</table>
	</div>
</div>
<div style="margin: auto"><strong>********** NOTHING FOLLOWS **********</strong></div>


</body></html>
