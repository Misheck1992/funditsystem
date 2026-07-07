<?php
$loans = get_all('loan');
$settings = get_by_id('settings','settings_id','1');
?>
<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">Collection Sheet Report</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">Reports</a>
				<span class="breadcrumb-item active">Collection Sheet</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
			<form action="<?php echo base_url('reports/collection_sheet') ?>" method="get">
				<fieldset>
					<legend>Report Filter</legend>
					<div class="row mb-3">
						<div class="col-md-3">
							<label>Period Type:</label>
							<select name="period" id="period_select" class="form-control" onchange="toggleCustomDates()">
								<option value="daily" <?php echo ($period_type == 'daily') ? 'selected' : ''; ?>>Daily (Today)</option>
								<option value="weekly" <?php echo ($period_type == 'weekly') ? 'selected' : ''; ?>>Weekly (This Week)</option>
								<option value="monthly" <?php echo ($period_type == 'monthly') ? 'selected' : ''; ?>>Monthly (This Month)</option>
								<option value="custom" <?php echo ($period_type == 'custom') ? 'selected' : ''; ?>>Custom Range</option>
								<option value="all" <?php echo ($period_type == 'all') ? 'selected' : ''; ?>>All Upcoming</option>
							</select>
						</div>

						<div class="col-md-3" id="custom_dates_div" style="<?php echo ($period_type == 'custom') ? '' : 'display:none;'; ?>">
							<label>Date From:</label>
							<input type="text" class="form-control dpicker" name="from" value="<?php echo $from_date; ?>" placeholder="Select start date">
						</div>

						<div class="col-md-3" id="custom_dates_to_div" style="<?php echo ($period_type == 'custom') ? '' : 'display:none;'; ?>">
							<label>Date To:</label>
							<input type="text" class="form-control dpicker" name="to" value="<?php echo $to_date; ?>" placeholder="Select end date">
						</div>

						<div class="col-md-3">
							<label>Loan:</label>
							<select name="loan" class="form-control sselect">
								<option value="All">All Loans</option>
								<?php foreach ($loans as $loan): ?>
									<option value="<?php echo $loan->loan_id; ?>" <?php if($loan->loan_id == $this->input->get('loan')){echo 'selected';} ?>>
										<?php echo $loan->loan_number; ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<button type="submit" name="search" value="filter" class="btn btn-primary">
								<i class="fa fa-filter"></i> Filter
							</button>
							<button type="submit" name="search" value="pdf" class="btn btn-danger">
								<i class="fa fa-file-pdf"></i> Export PDF
							</button>
						</div>
					</div>
				</fieldset>
			</form>

			<hr>

			<!-- Summary Section -->
			<div class="row mb-3">
				<div class="col-md-12">
					<h4>Summary</h4>
					<p>
						<strong>Period:</strong>
						<?php
						switch($period_type) {
							case 'daily':
								echo 'Today ('.date('d M Y').')';
								break;
							case 'weekly':
								echo 'This Week';
								break;
							case 'monthly':
								echo 'This Month ('.date('F Y').')';
								break;
							case 'custom':
								echo !empty($from_date) && !empty($to_date) ? date('d M Y', strtotime($from_date)).' to '.date('d M Y', strtotime($to_date)) : 'Custom Range';
								break;
							default:
								echo 'All Upcoming Payments';
						}
						?>
						<br>
						<strong>Total Records:</strong> <?php echo count($loan_data); ?>
						<br>
						<strong>Total Amount Due:</strong> <?php echo $settings->currency; ?>
						<?php
						$total_amount = 0;
						foreach($loan_data as $payment){
							$total_amount += $payment->amount - $payment->paid_amount;
						}
						echo number_format($total_amount, 2);
						?>
					</p>
				</div>
			</div>

			<!-- Table Container with Horizontal Scroll -->
			<div style="overflow-x: auto; overflow-y: auto;">
				<table class="table table-bordered" id="data-table">
				<thead>
				<tr>
					<th>#</th>
					<th>Customer</th>
					<th>Loan Number</th>
					<th>Due Date</th>
					<th>Payment No.</th>
					<th>Principal</th>
					<th>Interest</th>
					<th>Amount Due</th>
					<th>Paid Amount</th>
					<th>Balance</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
				</thead>
				<tbody>
				<?php
				$n = 1;
				if(!empty($loan_data)){
					foreach ($loan_data as $payment) {
						// Determine customer name and URL based on customer type
						if($payment->customer_type == 'group'){
							$group = get_by_id('customer_groups','id',$payment->loan_customer);
							$customer_name = !empty($group) ? $group->group_name.' ('.$group->group_code.')' : 'N/A';
							$customer_url = base_url('Customer_groups/members/').$payment->loan_customer;
						}elseif($payment->customer_type == 'individual'){
							$customer_name = $payment->ind_firstname.' '.$payment->ind_lastname;
							$customer_url = base_url('individual_customers/view/').$payment->ind_id;
						}elseif($payment->customer_type == 'institution'){
							$inst = get_by_id('corporate_customers','id',$payment->loan_customer);
							$customer_name = !empty($inst) ? $inst->EntityName.' - '.$inst->RegistrationNumber : 'N/A';
							$customer_url = base_url('Corporate_customers/read/').$payment->loan_customer;
						}else{
							$customer_name = 'Unknown';
							$customer_url = '#';
						}

						$balance = $payment->amount - $payment->paid_amount;
						?>
						<tr>
							<td><?php echo $n; ?></td>
							<td><a href="<?php echo $customer_url; ?>"><?php echo $customer_name; ?></a></td>
							<td><a href="<?php echo base_url('loan/view/').$payment->loan_id; ?>"><?php echo $payment->loan_number; ?></a></td>
							<td><?php echo date('d M Y', strtotime($payment->payment_schedule)); ?></td>
							<td><?php echo $payment->payment_number; ?></td>
							<td><?php echo $settings->currency.' '.number_format($payment->principal, 2); ?></td>
							<td><?php echo $settings->currency.' '.number_format($payment->interest, 2); ?></td>
							<td><?php echo $settings->currency.' '.number_format($payment->amount, 2); ?></td>
							<td><?php echo $settings->currency.' '.number_format($payment->paid_amount, 2); ?></td>
							<td><?php echo $settings->currency.' '.number_format($balance, 2); ?></td>
							<td>
								<?php if($payment->status == 'PAID'): ?>
									<span class="badge badge-success">PAID</span>
								<?php elseif($payment->partial_paid == 'YES'): ?>
									<span class="badge badge-warning">PARTIAL</span>
								<?php else: ?>
									<span class="badge badge-danger">NOT PAID</span>
								<?php endif; ?>
							</td>
							<td>
								<a href="<?php echo base_url('loan/view/').$payment->loan_id; ?>" class="btn btn-sm btn-info">
									<i class="fa fa-eye"></i> View
								</a>
							</td>
						</tr>
						<?php
						$n++;
					}
				} else {
					?>
					<tr>
						<td colspan="12" class="text-center">No payments found for the selected period</td>
					</tr>
					<?php
				}
				?>
				</tbody>
			</table>
			</div>
		</div>
	</div>
</div>

<script>
function toggleCustomDates() {
	var period = document.getElementById('period_select').value;
	var customDiv = document.getElementById('custom_dates_div');
	var customToDiv = document.getElementById('custom_dates_to_div');

	if(period === 'custom') {
		customDiv.style.display = 'block';
		customToDiv.style.display = 'block';
	} else {
		customDiv.style.display = 'none';
		customToDiv.style.display = 'none';
	}
}
</script>
