<?php
$products = get_all('loan');
$currencies = get_all('currencies');
$loan_products = get_all('loan_products');
?>
<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">Portfolio Listing Report</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">Reports</a>
				<span class="breadcrumb-item active">Portfolio Listing</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
			<form action="<?php echo base_url('reports/portfolio_listing') ?>" method="get">
				<fieldset>
					<legend>Report Filters</legend>
					<div id="controlgroup" style="margin-bottom: 15px;">
						<label>Loan Status:</label>
						<select name="loan_status" class="sselect">
							<option value="All" <?php if($filters['loan_status']=='All'){echo 'selected';}  ?>>All Statuses</option>
							<option value="ACTIVE" <?php if($filters['loan_status']=='ACTIVE'){echo 'selected';}  ?>>ACTIVE</option>
							<option value="APPROVED" <?php if($filters['loan_status']=='APPROVED'){echo 'selected';}  ?>>APPROVED</option>
							<option value="DISBURSED" <?php if($filters['loan_status']=='DISBURSED'){echo 'selected';}  ?>>DISBURSED</option>
							<option value="CLOSED" <?php if($filters['loan_status']=='CLOSED'){echo 'selected';}  ?>>CLOSED</option>
							<option value="INITIATED" <?php if($filters['loan_status']=='INITIATED'){echo 'selected';}  ?>>INITIATED</option>
							<option value="RECOMMENDED" <?php if($filters['loan_status']=='RECOMMENDED'){echo 'selected';}  ?>>RECOMMENDED</option>
						</select>

						<label>Currency:</label>
						<select name="currency" class="sselect">
							<option value="All" <?php if($filters['currency']=='All'){echo 'selected';}  ?>>All Currencies</option>
							<?php foreach ($currencies as $currency){ ?>
								<option value="<?php echo $currency->currency_id;?>" <?php if($filters['currency']==$currency->currency_id){echo 'selected';}  ?>>
									<?php echo $currency->currency_name.' ('.$currency->currency_code.')';?>
								</option>
							<?php } ?>
						</select>

						<label>Customer Type:</label>
						<select name="customer_type" class="sselect">
							<option value="All" <?php if($filters['customer_type']=='All'){echo 'selected';}  ?>>All Types</option>
							<option value="individual" <?php if($filters['customer_type']=='individual'){echo 'selected';}  ?>>Individual</option>
							<option value="institution" <?php if($filters['customer_type']=='institution'){echo 'selected';}  ?>>Corporate</option>
							<option value="group" <?php if($filters['customer_type']=='group'){echo 'selected';}  ?>>Group</option>
						</select>

						<label>Facility Type:</label>
						<select name="loan_product" class="sselect">
							<option value="All" <?php if($filters['loan_product']=='All'){echo 'selected';}  ?>>All Products</option>
							<?php foreach ($loan_products as $lp){ ?>
								<option value="<?php echo $lp->loan_product_id;?>" <?php if($filters['loan_product']==$lp->loan_product_id){echo 'selected';}  ?>>
									<?php echo $lp->product_name;?>
								</option>
							<?php } ?>
						</select>

						<br><br>
						<label>Date From:</label>
						<input type="text" class="dpicker" name="from_date" value="<?php echo $filters['from_date']?>" placeholder="Start Date">

						<label>Date To:</label>
						<input type="text" class="dpicker" name="to_date" value="<?php echo $filters['to_date']?>" placeholder="End Date">

						<br><br>
						<button type="submit" name="search" value="filter">Apply Filters</button>
						<button type="submit" name="search" value="pdf"><i class="fa fa-file-pdf text-danger"></i> PDF</button>
						<button type="submit" name="search" value="excel"><i class="fa fa-file-excel text-success"></i> Excel</button>
						<a href="<?php echo base_url('reports/portfolio_listing')?>" class="btn btn-sm btn-secondary">Reset Filters</a>
					</div>
				</fieldset>
			</form>
			<hr>
			<p>Portfolio Listing Report - Total Records: <?php echo count($loan_data); ?></p>
			<div style="overflow-x: auto;">
				<table class="table tab-content" id="data-table">
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
							$customer_url = base_url('individual_customers/view/').$loan->ind_id;
							$offtaker = '-';
							$industry = $loan->ind_profession ? $loan->ind_profession : '-';
						}elseif($loan->customer_type == 'institution'){
							$customer_name = $loan->corp_name ? $loan->corp_name : 'N/A';
							$customer_url = base_url('Corporate_customers/read/').$loan->corp_id;
							// Display category (client or off_taker)
							$offtaker = $loan->corp_category ? ucfirst(str_replace('_', ' ', $loan->corp_category)) : '-';
							$industry = $loan->corp_industry ? $loan->corp_industry : '-';
						}else{
							$customer_name = 'Unknown';
							$customer_url = '#';
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
							<td><a href="<?php echo $customer_url?>"><?php echo $customer_name?></a></td>
							<td><a href="<?php echo base_url('loan/view/').$loan->loan_id?>"><?php echo $loan->loan_number ?></a></td>
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
							<td><span class="badge badge-<?php
								if($loan->loan_status == 'ACTIVE') echo 'success';
								elseif($loan->loan_status == 'APPROVED') echo 'primary';
								elseif($loan->loan_status == 'DISBURSED') echo 'info';
								elseif($loan->loan_status == 'CLOSED') echo 'secondary';
								else echo 'warning';
							?>"><?php echo $loan->loan_status ?></span></td>
							<td><?php echo $offtaker ?></td>
							<td><?php echo $industry ?></td>
						</tr>
						<?php
						$n ++;
					}
					?>
					</tbody>
					<tfoot>
					<tr style="font-weight: bold; background-color: #f0f0f0;">
						<td colspan="3">Total</td>
						<td><?php echo number_format($total_disbursed, 2) ?></td>
						<td><?php echo number_format($total_outstanding, 2) ?></td>
						<td><?php echo number_format($total_interest_amount, 2) ?></td>
						<td>0.00</td>
						<td><?php echo number_format($total_realized_interest, 2) ?></td>
						<td><?php echo number_format($total_amount_repaid, 2) ?></td>
						<td colspan="8"></td>
					</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>
