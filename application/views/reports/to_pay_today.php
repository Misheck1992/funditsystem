<?php
$users = get_all('employees');
$products = get_all('loan');
?>
<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">All today collection report</h2>
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
			<form action="<?php echo base_url('reports/to_pay_today') ?>" method="get">
				<fieldset>
					<legend>Report export</legend>
					<div id="controlgroup">

						<button type="submit" name="search" value="pdf"><i class="fa fa-file-pdf text-danger"></i></button>
						<button type="submit" name="search" value="excel"><i class="fa fa-file-excel text-success"></i></button>
					</div>
				</fieldset>
			</form>
			<hr>
			<p>Search results</p>
			<h5>Collection Sheet Today</h5>
			<table class="table tab-content" id="data-table">
				<thead>
				<tr>
					<th>#</th>
					<th>Loan Customer</th>
					<th>Loan Number</th>
					<th>Check Date</th>
					<th>Amount to collect</th>
					<th>Payment number</th>
					<th>Action</th>

				</tr>
				</thead>
				<tbody>
				<?php
				$n = 1;

				foreach ($loan_data as $loan)
				{
					?>
					<tr>

						<td><?php echo $n ?></td>
						<td><a href="<?php echo base_url('individual_customers/view/').$loan->id?>""><?php echo $loan->Firstname." ".$loan->Firstname?></a></td>

						<td><a href="<?php echo base_url('loan/view/').$loan->loan_id?>"><?php echo $loan->loan_number ?></a></td>

						<td><?php echo $loan->payment_schedule ?></td>
<!--						<td>MK--><?php //echo number_format($loan->loan_principal,2) ?><!--</td>-->
						<td><?php echo $loan->amount ?></td>
						<td><?php echo $loan->payment_number ?></td>

						<td><a href="<?php echo base_url('loan/view/').$loan->loan_id?>">View</a></td>

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
