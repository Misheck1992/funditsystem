<?php
$users = get_all('employees');
$products = get_all('loan');
?>
<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">All arrears report</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">-</a>
				<span class="breadcrumb-item active">All loans  arrears report</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
			<form action="<?php echo base_url('reports/arrears') ?>" method="get">
				<fieldset>
					<legend>Report filter</legend>
					<div id="controlgroup">
						Loan :

						<select name="loan" class="sselect">
							<option value="All">All loans</option>
							<?php
							foreach ($products as $product){
								?>
								<option value="<?php echo $product->loan_id;?>" <?php if($product->loan_id==$this->input->get('loan')){echo 'selected';}  ?>><?php echo $product->loan_number;?></option>
								<?php
							}

							?>
						</select>
						Date from:<input type="text" class="dpicker" name="from" value="<?php  echo $this->input->get('from')?>" >
						Date to:<input type="text" class="dpicker" name="to" value="<?php  echo $this->input->get('to')?>" >
						<button type="submit" name="search" value="filter">Filter</button>
						<button type="submit" name="search" value="pdf"><i class="fa fa-file-pdf text-danger"></i></button>
<!--						<button type="submit" name="search" value="excel"><i class="fa fa-file-excel text-success"></i></button>-->
					</div>
				</fieldset>
			</form>
			<hr>
			<p>Search results</p>
			<table class="table tab-content" id="data-table">
				<thead>
				<tr>
					<th>#</th>
					<th>Loan Customer</th>
					<th>Loan Number</th>
					<th>Type</th>
					<th>Due Date</th>
					<th>Original Amount</th>
					<th>Amount Due (with interest)</th>
					<th>Days Overdue</th>
					<th>Action</th>

				</tr>
				</thead>
				<tbody>
				<?php
				$n = 1;

				foreach ($loan_data as $loan)
				{
					// Determine customer name and URL based on customer type
					if($loan->customer_type == 'group'){
						$group = get_by_id('customer_groups','id',$loan->loan_customer);
						$customer_name = !empty($group) ? $group->group_name.' ('.$group->group_code.')' : 'N/A';
						$customer_url = base_url('Customer_groups/members/').$loan->loan_customer;
					}elseif($loan->customer_type == 'individual'){
						$customer_name = $loan->ind_firstname.' '.$loan->ind_lastname;
						$customer_url = base_url('individual_customers/view/').$loan->ind_id;
					}elseif($loan->customer_type == 'institution'){
						$inst = get_by_id('corporate_customers','id',$loan->loan_customer);
						$customer_name = !empty($inst) ? $inst->EntityName.' - '.$inst->RegistrationNumber.' ('.$inst->entity_type.')' : 'N/A';
						$customer_url = base_url('Corporate_customers/read/').$loan->loan_customer;
					}else{
						$customer_name = 'Unknown';
						$customer_url = '#';
					}

					// Calculate actual amount due for bullet loans with compound interest on OUTSTANDING balance
					$original_amount = $loan->amount;
					$amount_due = $loan->amount;
					$days_overdue = max(0, floor((strtotime(date('Y-m-d')) - strtotime($loan->payment_schedule)) / 86400));
					$is_bullet = (isset($loan->calculation_type) && $loan->calculation_type == 'Bullet Payment');

					if ($is_bullet && $days_overdue > 0) {
						// Bullet loan in arrears: compound interest on OUTSTANDING balance (after payments)
						$principal = floatval($loan->loan_principal);
						$monthly_rate = floatval($loan->loan_interest) / 100;
						$term = intval($loan->loan_period);

						// Total at maturity
						$maturity_total = $principal + ($principal * $monthly_rate * $term);

						// Get payments made (reduces balance BEFORE compounding)
						$paid = floatval($loan->paid_amount ?? 0);
						$outstanding_at_maturity = $maturity_total - $paid;
						if ($outstanding_at_maturity < 0) $outstanding_at_maturity = 0;

						// Days past maturity
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
						<td><a href="<?php echo $customer_url?>"><?php echo $customer_name?></a></td>

						<td><a href="<?php echo base_url('loan/view/').$loan->loan_id?>"><?php echo $loan->loan_number ?></a></td>

						<td>
							<?php if($is_bullet): ?>
								<span style="background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">Bullet</span>
							<?php else: ?>
								<span style="font-size: 0.8rem; color: #6b7280;"><?php echo $loan->calculation_type ?? 'Regular'; ?></span>
							<?php endif; ?>
						</td>

						<td><?php echo $loan->payment_schedule ?></td>
						<td><?php echo number_format($original_amount, 2) ?></td>
						<td>
							<?php if($is_bullet && $amount_due > $original_amount): ?>
								<span style="color: #dc2626; font-weight: 600;"><?php echo number_format($amount_due, 2) ?></span>
							<?php else: ?>
								<?php echo number_format($amount_due, 2) ?>
							<?php endif; ?>
						</td>
						<td>
							<?php if($days_overdue > 30): ?>
								<span style="color: #dc2626; font-weight: 600;"><?php echo $days_overdue ?> days</span>
							<?php else: ?>
								<?php echo $days_overdue ?> days
							<?php endif; ?>
						</td>

						<td><a href="<?php echo base_url('loan/view/').$loan->loan_id?>" class="btn btn-sm btn-primary" style="border-radius: 6px;">View</a></td>

					</tr>
					<?php
					$n ++;
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
</div>
