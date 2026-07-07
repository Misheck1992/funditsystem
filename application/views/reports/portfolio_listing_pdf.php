<!DOCTYPE html>
<html>
<head>
	<title>Portfolio Listing Report</title>
	<style>
		body {
			font-family: Arial, sans-serif;
			font-size: 9px;
		}
		h2 {
			text-align: center;
			margin-bottom: 20px;
		}
		table {
			width: 100%;
			border-collapse: collapse;
			margin-top: 10px;
		}
		th {
			background-color: #153505;
			color: white;
			padding: 6px;
			text-align: left;
			border: 1px solid #ddd;
			font-size: 8px;
		}
		td {
			padding: 5px;
			border: 1px solid #ddd;
			font-size: 8px;
		}
		tfoot td {
			font-weight: bold;
			background-color: #f0f0f0;
		}
		.header-info {
			margin-bottom: 15px;
		}
	</style>
</head>
<body>
	<h2>Portfolio Listing Report</h2>
	<div class="header-info">
		<p><strong>Generated:</strong> <?php echo date('Y-m-d H:i:s') ?></p>
		<p><strong>Report:</strong> All Active Loans</p>
	</div>

	<table>
		<thead>
		<tr>
			<th>#</th>
			<th>Client Name</th>
			<th>Loan Number</th>
			<th>Amount Disbursed</th>
			<th>Amount Outstanding</th>
			<th>Interest Amount</th>
			<th>Rollover Fees</th>
			<th>Realized Interest</th>
			<th>Amount Repaid</th>
			<th>% of Loan Book</th>
			<th>Currency</th>
			<th>Tenor (Days)</th>
			<th>Days to Maturity</th>
			<th>Facility Type</th>
			<th>Loan Status</th>
			<th>Offtaker</th>
			<th>Client Industry</th>
		</tr>
		</thead>
		<tbody>
		<?php
		$n = 1;
		$total_disbursed = 0;
		$total_outstanding = 0;
		$total_interest_amount = 0;
		$total_realized_interest = 0;
		$total_amount_repaid = 0;

		foreach ($loan_data as $loan)
		{
			// Determine customer name and details based on customer type
			if($loan->customer_type == 'individual'){
				$customer_name = $loan->ind_firstname.' '.$loan->ind_lastname;
				$offtaker = '-';
				$industry = $loan->ind_profession ? $loan->ind_profession : '-';
			}elseif($loan->customer_type == 'institution'){
				$customer_name = $loan->corp_name ? $loan->corp_name : 'N/A';
				// Display category (client or off_taker)
				$offtaker = $loan->corp_category ? ucfirst(str_replace('_', ' ', $loan->corp_category)) : '-';
				$industry = $loan->corp_industry ? $loan->corp_industry : '-';
			}else{
				$customer_name = 'Unknown';
				$offtaker = '-';
				$industry = '-';
			}

			// Calculate outstanding balance
			$outstanding = $loan->total_scheduled - $loan->total_paid;

			// Amount Repaid (total paid amount)
			$amount_repaid = $loan->total_paid ? $loan->total_paid : 0;

			// Realized Interest
			$realized_interest = $loan->realized_interest ? $loan->realized_interest : 0;

			// Interest Amount
			$interest_amount = $loan->loan_interest_amount ? $loan->loan_interest_amount : 0;

			// Calculate tenor in days based on frequency
			$tenor_days = 0;
			$frequency = $loan->period_type ? $loan->period_type : $loan->loan_frequency;
			if($loan->loan_period && $frequency){
				switch($frequency){
					case 'Monthly':
						$tenor_days = $loan->loan_period * 30;
						break;
					case '2 Weeks':
						$tenor_days = $loan->loan_period * 15;
						break;
					case 'Weekly':
						$tenor_days = $loan->loan_period * 7;
						break;
					default:
						$tenor_days = $loan->loan_period * 30;
				}
			}

			// Calculate days to maturity
			$days_to_maturity = '-';
			if($loan->last_payment_date){
				$today = new DateTime();
				$maturity_date = new DateTime($loan->last_payment_date);
				$interval = $today->diff($maturity_date);

				if($maturity_date < $today){
					$days_to_maturity = 'Overdue (' . $interval->days . ' days)';
				}else{
					$days_to_maturity = $interval->days . ' days';
				}
			}

			// Currency display
			$currency = $loan->currency_name ? $loan->currency_name : ($loan->currency_code ? $loan->currency_code : '-');

			// Facility type
			$facility_type = $loan->facility_type ? $loan->facility_type : '-';

			// Accumulate totals
			$total_disbursed += $loan->loan_principal;
			$total_outstanding += $outstanding;
			$total_interest_amount += $interest_amount;
			$total_realized_interest += $realized_interest;
			$total_amount_repaid += $amount_repaid;
			?>
			<tr>
				<td><?php echo $n ?></td>
				<td><?php echo $customer_name?></td>
				<td><?php echo $loan->loan_number ?></td>
				<td><?php echo number_format($loan->loan_principal, 2) ?></td>
				<td><?php echo number_format($outstanding, 2) ?></td>
				<td><?php echo number_format($interest_amount, 2) ?></td>
				<td>0.00</td>
				<td><?php echo number_format($realized_interest, 2) ?></td>
				<td><?php echo number_format($amount_repaid, 2) ?></td>
				<td><?php echo $loan->loan_interest ?>%</td>
				<td><?php echo $currency ?></td>
				<td><?php echo $tenor_days ?></td>
				<td><?php echo $days_to_maturity ?></td>
				<td><?php echo $facility_type ?></td>
				<td><?php echo $loan->loan_status ?></td>
				<td><?php echo $offtaker ?></td>
				<td><?php echo $industry ?></td>
			</tr>
			<?php
			$n ++;
		}
		?>
		</tbody>
		<tfoot>
		<tr>
			<td colspan="3"><strong>Total</strong></td>
			<td><strong><?php echo number_format($total_disbursed, 2) ?></strong></td>
			<td><strong><?php echo number_format($total_outstanding, 2) ?></strong></td>
			<td><strong><?php echo number_format($total_interest_amount, 2) ?></strong></td>
			<td><strong>0.00</strong></td>
			<td><strong><?php echo number_format($total_realized_interest, 2) ?></strong></td>
			<td><strong><?php echo number_format($total_amount_repaid, 2) ?></strong></td>
			<td colspan="8"></td>
		</tr>
		</tfoot>
	</table>
</body>
</html>
