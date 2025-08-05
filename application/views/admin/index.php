<?php
$logs = get_logs('activity_logger','user_id',$this->session->userdata('user_id'));
$settings = get_by_id('settings','settings_id','1');
?>
<!-- Main Dashboard Content -->
<div class="main-content">
	<div class="page-header no-gutters has-tab" style="margin-bottom: 2px !important;">
		<h2 class="font-weight-normal">WELCOME- <?php echo $this->session->userdata('Firstname')?></h2>
	</div>

	<!-- Admin Dashboard Stats -->
	<div class="row">
		<div class="col-lg-12">
			<h2 class="heading">Quick Stats</h2>
			<hr class="dash">
		</div>
	</div>

	<!-- Key Metrics -->
	<div class="row">
		<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
			<a class="dashboard-stat purple" href="#">
				<div class="visual">
					<i class="fa fa-usd"></i>
				</div>
				<div class="details">
					<div class="number" style="font-size: 16px">
						<span><?php echo $settings->currency?>  <?php
							$ip_b = 0;
							$ip = get_total_loan_amount('initiated');

if(!empty($ip)){
	echo number_format(round($ip ->total_amount),2);
}else{
	echo 0;
}

							?></span>
					</div>
					<div class="desc">Total Initiated loan</div>
				</div>
			</a>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
			<a class="dashboard-stat blue" href="#">
				<div class="visual">
					<i class="fa fa-bar-chart-o"></i>
				</div>
				<div class="details">
					<div class="number" style="font-size: 16px">
						<span><?php echo $settings->currency?> <?php
							$ip_i = 0;

							$ipd = get_total_loan_amount('active');

if(!empty($ipd)){
	echo number_format(round($ipd ->total_amount),2);
}else{
	echo 0;
}

							?></span>
					</div>
					<div class="desc">Total disbursed loans</div>
				</div>
			</a>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
			<a class="dashboard-stat hoki" href="#">
				<div class="visual">
					<i class="fa fa-credit-card"></i>
				</div>
				<div class="details">
					<div class="number" style="font-size: 16px">
						  <span><?php echo $settings->currency?> <?php
							  $ip_a = 0;

							  $ipc = get_total_loan_amount('active');

if(!empty($ipc)){
	echo number_format(round($ipc ->total_amount),2);
}else{
	echo 0;
}

							  ?></span>
					</div>
					<div class="desc">Total Active loan</div>
				</div>
			</a>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
			<a class="dashboard-stat green" href="#">
				<div class="visual">
					<i class="fa fa-money"></i>
				</div>
				<div class="details">
					<div class="number" style="font-size: 16px">
						<span><?php echo $settings->currency?> <?php echo number_format(round($ipc->total_amount),2); ?></span>
					</div>
					<div class="desc">Total Closed loan</div>
				</div>
			</a>
		</div>
	</div>

	<!-- Loan Performance Charts -->
	

	<!-- Product Active Portfolio -->
	<div class="row mt-4">
		<div class="col-lg-12">
			<h2 class="heading">Product Active Portfolio</h2>
			<hr class="dash">
		</div>
	</div>

	<div class="row">
		<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
			<a class="dashboard-stat green" href="#">
				<div class="visual">
					<i class="fa fa-file-text-o"></i>
				</div>
				<div class="details">
					<div class="number" style="font-size: 16px">
						<span><?php echo $settings->currency?> <?php echo number_format(0, 2); ?></span>
					</div>
					<div class="desc">Invoice Discounting</div>
				</div>
			</a>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
			<a class="dashboard-stat green" href="#">
				<div class="visual">
					<i class="fa fa-shopping-cart"></i>
				</div>
				<div class="details">
					<div class="number" style="font-size: 16px">
						<span><?php echo $settings->currency?> <?php echo number_format(0, 2); ?></span>
					</div>
					<div class="desc">Order Finance</div>
				</div>
			</a>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
			<a class="dashboard-stat green" href="#">
				<div class="visual">
					<i class="fa fa-calendar"></i>
				</div>
				<div class="details">
					<div class="number" style="font-size: 16px">
						<span><?php echo $settings->currency?> <?php echo number_format(0, 2); ?></span>
					</div>
					<div class="desc">Term Loans</div>
				</div>
			</a>
		</div>
	</div>

	<div class="row mt-2">
		<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
			<a class="dashboard-stat green" href="#">
				<div class="visual">
					<i class="fa fa-users"></i>
				</div>
				<div class="details">
					<div class="number" style="font-size: 16px">
						<span><?php echo $settings->currency?> <?php echo number_format(0, 2); ?></span>
					</div>
					<div class="desc">Payroll-backed Loans</div>
				</div>
			</a>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
			<a class="dashboard-stat green" href="#">
				<div class="visual">
					<i class="fa fa-building"></i>
				</div>
				<div class="details">
					<div class="number" style="font-size: 16px">
						<span><?php echo $settings->currency?> <?php echo number_format(0, 2); ?></span>
					</div>
					<div class="desc">SME Product</div>
				</div>
			</a>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
			<a class="dashboard-stat green" href="#">
				<div class="visual">
					<i class="fa fa-bullseye"></i>
				</div>
				<div class="details">
					<div class="number" style="font-size: 16px">
						<span><?php echo $settings->currency?> <?php echo number_format(0, 2); ?></span>
					</div>
					<div class="desc">New Bullet Test</div>
				</div>
			</a>
		</div>
	</div>


</div>
