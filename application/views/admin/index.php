<?php
$logs = get_logs('activity_logger','user_id',$this->session->userdata('user_id'));
$settings = get_by_id('settings','settings_id','1');
// Get loan stats for dashboard
$initiated_stats = get_loan_stats_by_status('initiated');
$active_stats = get_loan_stats_by_status('active');
$closed_stats = get_loan_stats_by_status('closed');
// Total principal disbursed to date (active + closed + written-off + defaulted)
$disbursed_stats = get_total_disbursed_stats();
// Total outstanding balance (principal + accrued interest only, excludes unaccrued interest)
$dashboard_outstanding = get_total_outstanding_balance();
$dashboard_outstanding_total = $dashboard_outstanding ? $dashboard_outstanding->outstanding_balance : 0;
// Get all loan status counts for the strip
$status_counts = get_all_loan_status_counts();
?>
<!-- Main Dashboard Content -->
<div class="main-content">
	<div class="page-header no-gutters has-tab" style="margin-bottom: 2px !important;">
		<h2 class="font-weight-normal">WELCOME- <?php echo $this->session->userdata('Firstname')?></h2>
	</div>

	<!-- Loan Status Strip -->
	<style>
		.status-strip {
			display: flex;
			flex-wrap: wrap;
			gap: 10px;
			margin-bottom: 20px;
			padding: 15px;
			background: #fff;
			border-radius: 8px;
			box-shadow: 0 2px 4px rgba(0,0,0,0.05);
		}
		.status-item {
			display: flex;
			align-items: center;
			padding: 8px 15px;
			border-radius: 6px;
			text-decoration: none;
			transition: all 0.2s ease;
			min-width: 120px;
		}
		.status-item:hover {
			transform: translateY(-2px);
			box-shadow: 0 4px 8px rgba(0,0,0,0.1);
			text-decoration: none;
		}
		.status-item .count {
			font-size: 20px;
			font-weight: 700;
			margin-right: 10px;
			min-width: 35px;
		}
		.status-item .label {
			font-size: 11px;
			font-weight: 500;
			text-transform: uppercase;
			line-height: 1.2;
		}
		.status-item.created { background: #e8f4fd; color: #1976d2; }
		.status-item.pending-rec { background: #fff3e0; color: #f57c00; }
		.status-item.pending-app { background: #fce4ec; color: #c2185b; }
		.status-item.pending-sign { background: #f3e5f5; color: #7b1fa2; }
		.status-item.pending-disb { background: #e0f2f1; color: #00796b; }
		.status-item.disbursed { background: #e8f5e9; color: #388e3c; }
		.status-item.closed { background: #eceff1; color: #546e7a; }
		.status-item.written-off { background: #ffebee; color: #d32f2f; }
		@media (max-width: 768px) {
			.status-item {
				min-width: calc(50% - 10px);
				flex: 1 1 calc(50% - 10px);
			}
		}
		@media (max-width: 480px) {
			.status-item {
				min-width: 100%;
				flex: 1 1 100%;
			}
		}
	</style>

	<div class="status-strip">
		<a href="<?php echo base_url('loan/initiated'); ?>" class="status-item created">
			<span class="count"><?php echo $status_counts['initiated']; ?></span>
			<span class="label">Created<br>Loans</span>
		</a>
		<a href="<?php echo base_url('loan/recommend'); ?>" class="status-item pending-rec">
			<span class="count"><?php echo $status_counts['initiated']; ?></span>
			<span class="label">Pending<br>Recommendation</span>
		</a>
		<a href="<?php echo base_url('loan/unified_approval'); ?>" class="status-item pending-app">
			<span class="count"><?php echo $status_counts['recommended']; ?></span>
			<span class="label">Pending<br>Approval</span>
		</a>
		<a href="<?php echo base_url('loan/track?status=APPROVED'); ?>" class="status-item pending-sign">
			<span class="count"><?php echo $status_counts['approved']; ?></span>
			<span class="label">Pending<br>Client Signing</span>
		</a>
		<a href="<?php echo base_url('loan/approved'); ?>" class="status-item pending-disb">
			<span class="count"><?php echo $status_counts['client_signed']; ?></span>
			<span class="label">Pending<br>Disbursement</span>
		</a>
		<a href="<?php echo base_url('loan/active'); ?>" class="status-item disbursed">
			<span class="count"><?php echo $status_counts['active']; ?></span>
			<span class="label">Disbursed<br>(Active)</span>
		</a>
		<a href="<?php echo base_url('loan/closed'); ?>" class="status-item closed">
			<span class="count"><?php echo $status_counts['closed']; ?></span>
			<span class="label">Closed<br>Loans</span>
		</a>
		<a href="<?php echo base_url('loan/written_off'); ?>" class="status-item written-off">
			<span class="count"><?php echo $status_counts['written_off']; ?></span>
			<span class="label">Written<br>Off</span>
		</a>
	</div>

	<!-- Quick Stats - Combined Card -->
	<style>
		/* Quick Stats Combined Card */
		.quick-stats-card {
			background: #fff;
			border-radius: 8px;
			padding: 15px 20px;
			margin-bottom: 20px;
			box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
		}
		.quick-stats-card .card-header {
			display: flex;
			align-items: center;
			margin-bottom: 15px;
			padding-bottom: 10px;
			border-bottom: 1px solid #eee;
		}
		.quick-stats-card .card-header h3 {
			margin: 0;
			font-size: 14px;
			font-weight: 600;
			color: #2c3e50;
		}
		.quick-stats-grid {
			display: flex;
			flex-wrap: wrap;
			gap: 15px;
		}
		.quick-stat-item {
			flex: 1;
			min-width: 150px;
			display: flex;
			align-items: center;
			padding: 10px 12px;
			border-radius: 6px;
			background: #f8f9fa;
			text-decoration: none;
			transition: all 0.2s ease;
		}
		.quick-stat-item:hover {
			background: #e9ecef;
			text-decoration: none;
		}
		.quick-stat-icon {
			width: 36px;
			height: 36px;
			border-radius: 8px;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 16px;
			color: #fff;
			margin-right: 10px;
			flex-shrink: 0;
		}
		.quick-stat-icon.purple { background: linear-gradient(135deg, #8E44AD 0%, #9b59b6 100%); }
		.quick-stat-icon.blue { background: linear-gradient(135deg, #3498db 0%, #5dade2 100%); }
		.quick-stat-icon.teal { background: linear-gradient(135deg, #16a085 0%, #1abc9c 100%); }
		.quick-stat-icon.green { background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%); }
		.quick-stat-content {
			flex: 1;
			min-width: 0;
		}
		.quick-stat-value {
			font-size: 14px;
			font-weight: 700;
			color: #2c3e50;
			line-height: 1.2;
			white-space: nowrap;
			overflow: hidden;
			text-overflow: ellipsis;
		}
		.quick-stat-label {
			font-size: 10px;
			color: #7f8c8d;
			font-weight: 500;
		}
		.quick-stat-count {
			font-size: 9px;
			color: #95a5a6;
		}
		@media (max-width: 768px) {
			.quick-stat-item {
				min-width: calc(50% - 10px);
			}
		}
		@media (max-width: 480px) {
			.quick-stat-item {
				min-width: 100%;
			}
		}
	</style>

	<div class="quick-stats-card">
		<div class="card-header">
			<h3><i class="fa fa-bar-chart m-r-10"></i> Quick Stats</h3>
		</div>
		<div class="quick-stats-grid">
			<a class="quick-stat-item" href="<?php echo base_url('loan/initiated'); ?>">
				<div class="quick-stat-icon purple">
					<i class="fa fa-usd"></i>
				</div>
				<div class="quick-stat-content">
					<div class="quick-stat-value"><?php echo $settings->currency?> <?php echo number_format($initiated_stats['total'],2); ?></div>
					<span class="quick-stat-label">Total Initiated Loans</span>
					<div class="quick-stat-count">Count: <?php echo $initiated_stats['count']; ?></div>
				</div>
			</a>
			<a class="quick-stat-item" href="<?php echo base_url('loan/active'); ?>">
				<div class="quick-stat-icon blue">
					<i class="fa fa-bar-chart-o"></i>
				</div>
				<div class="quick-stat-content">
					<div class="quick-stat-value"><?php echo $settings->currency?> <?php echo number_format($disbursed_stats['total'],2); ?></div>
					<span class="quick-stat-label">Total Disbursed Loans</span>
					<div class="quick-stat-count">Count: <?php echo $disbursed_stats['count']; ?></div>
				</div>
			</a>
			<a class="quick-stat-item" href="<?php echo base_url('loan/active'); ?>">
				<div class="quick-stat-icon teal">
					<i class="fa fa-credit-card"></i>
				</div>
				<div class="quick-stat-content">
					<div class="quick-stat-value"><?php echo $settings->currency?> <?php echo number_format($active_stats['total'],2); ?></div>
					<span class="quick-stat-label">Total Active Loans</span>
					<div class="quick-stat-count">Count: <?php echo $active_stats['count']; ?></div>
				</div>
			</a>
			<a class="quick-stat-item" href="<?php echo base_url('loan/active'); ?>">
				<div class="quick-stat-icon orange">
					<i class="fa fa-balance-scale"></i>
				</div>
				<div class="quick-stat-content">
					<div class="quick-stat-value"><?php echo $settings->currency?> <?php echo number_format($dashboard_outstanding_total,2); ?></div>
					<span class="quick-stat-label">Outstanding Balance</span>
					<div class="quick-stat-count">Principal + accrued interest</div>
				</div>
			</a>
			<a class="quick-stat-item" href="<?php echo base_url('loan/closed'); ?>">
				<div class="quick-stat-icon green">
					<i class="fa fa-money"></i>
				</div>
				<div class="quick-stat-content">
					<div class="quick-stat-value"><?php echo $settings->currency?> <?php echo number_format($closed_stats['total'],2); ?></div>
					<span class="quick-stat-label">Total Closed Loans</span>
					<div class="quick-stat-count">Count: <?php echo $closed_stats['count']; ?></div>
				</div>
			</a>
		</div>
	</div>

	<!-- Additional color variants for icons -->
	<style>
		.quick-stat-icon.orange { background: linear-gradient(135deg, #e67e22 0%, #f39c12 100%); }
		.quick-stat-icon.red { background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); }
		.quick-stat-icon.yellow { background: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%); }
		.quick-stat-icon.navy { background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); }
		.quick-stat-icon.maroon { background: linear-gradient(135deg, #8e44ad 0%, #9b59b6 100%); }
		.quick-stat-icon.olive { background: linear-gradient(135deg, #7f8c8d 0%, #95a5a6 100%); }
	</style>

	<!-- Product Active Portfolio Card -->
	<div class="quick-stats-card">
		<div class="card-header">
			<h3><i class="fa fa-briefcase m-r-10"></i> Product Active Portfolio</h3>
		</div>
		<div class="quick-stats-grid">
			<?php
			if(!empty($loan_products)) {
				$colors = ['purple', 'blue', 'teal', 'green', 'purple', 'blue'];
				$index = 0;
				foreach($loan_products as $product) {
					$product_data = get_total_loan_amount_product_by_id('active', $product->loan_product_id);
					$amount = (!empty($product_data) && $product_data->total_amount_product !== null)
						? number_format(round($product_data->total_amount_product), 2)
						: number_format(0, 2);
					$color = $colors[$index % count($colors)];
					$index++;
			?>
			<a class="quick-stat-item" href="#">
				<div class="quick-stat-icon <?php echo $color; ?>">
					<i class="fa fa-file-text-o"></i>
				</div>
				<div class="quick-stat-content">
					<div class="quick-stat-value"><?php echo $settings->currency?> <?php echo $amount; ?></div>
					<span class="quick-stat-label"><?php echo $product->product_name; ?></span>
				</div>
			</a>
			<?php
				}
			}
			?>
		</div>
	</div>

	<!-- Microfinance Stats Card -->
	<div class="quick-stats-card">
		<div class="card-header">
			<h3><i class="fa fa-line-chart m-r-10"></i> Microfinance Stats</h3>
		</div>
		<div class="quick-stats-grid">
			<a class="quick-stat-item" href="#">
				<div class="quick-stat-icon orange">
					<i class="fa fa-users"></i>
				</div>
				<div class="quick-stat-content">
					<div class="quick-stat-value"><?php
						$total_borrowers = get_total_microfinance_borrowers();
						echo number_format($total_borrowers);
					?></div>
					<span class="quick-stat-label">Total Active Borrowers</span>
				</div>
			</a>
			<a class="quick-stat-item" href="#">
				<div class="quick-stat-icon red">
					<i class="fa fa-exclamation-triangle"></i>
				</div>
				<div class="quick-stat-content">
					<div class="quick-stat-value"><?php echo $settings->currency?> <?php
						$overdue_amount = get_microfinance_overdue_amount();
						echo number_format($overdue_amount, 2);
					?></div>
					<span class="quick-stat-label">Overdue Amount</span>
					<div class="quick-stat-count">PAR > 30 days</div>
				</div>
			</a>
			<a class="quick-stat-item" href="#">
				<div class="quick-stat-icon yellow">
					<i class="fa fa-pie-chart"></i>
				</div>
				<div class="quick-stat-content">
					<div class="quick-stat-value"><?php
						$par_ratio = get_microfinance_par_ratio();
						echo number_format($par_ratio, 2);
					?>%</div>
					<span class="quick-stat-label">Portfolio at Risk (PAR)</span>
				</div>
			</a>
			<a class="quick-stat-item" href="#">
				<div class="quick-stat-icon teal">
					<i class="fa fa-calendar-check-o"></i>
				</div>
				<div class="quick-stat-content">
					<div class="quick-stat-value"><?php echo $settings->currency?> <?php
						$collection_today = get_microfinance_collection_today();
						echo number_format($collection_today, 2);
					?></div>
					<span class="quick-stat-label">Collections Today</span>
				</div>
			</a>
			<a class="quick-stat-item" href="#">
				<div class="quick-stat-icon purple">
					<i class="fa fa-line-chart"></i>
				</div>
				<div class="quick-stat-content">
					<div class="quick-stat-value"><?php echo $settings->currency?> <?php
						$avg_loan_size = get_microfinance_avg_loan_size();
						echo number_format($avg_loan_size, 2);
					?></div>
					<span class="quick-stat-label">Average Loan Size</span>
				</div>
			</a>
			<a class="quick-stat-item" href="#">
				<div class="quick-stat-icon navy">
					<i class="fa fa-refresh"></i>
				</div>
				<div class="quick-stat-content">
					<div class="quick-stat-value"><?php
						$repayment_rate = get_microfinance_repayment_rate();
						echo number_format($repayment_rate, 2);
					?>%</div>
					<span class="quick-stat-label">Repayment Rate</span>
				</div>
			</a>
			<a class="quick-stat-item" href="#">
				<div class="quick-stat-icon maroon">
					<i class="fa fa-group"></i>
				</div>
				<div class="quick-stat-content">
					<div class="quick-stat-value"><?php
						$group_loans = get_microfinance_group_loans_count();
						echo number_format($group_loans);
					?></div>
					<span class="quick-stat-label">Active Group Loans</span>
				</div>
			</a>
			<a class="quick-stat-item" href="#">
				<div class="quick-stat-icon olive">
					<i class="fa fa-bank"></i>
				</div>
				<div class="quick-stat-content">
					<div class="quick-stat-value"><?php echo $settings->currency?> <?php
						$cash_at_hand = get_microfinance_cash_at_hand();
						echo number_format($cash_at_hand, 2);
					?></div>
					<span class="quick-stat-label">Cash at Hand</span>
				</div>
			</a>
		</div>
	</div>

	<!-- Active Loans Principal by Product Card -->
	<div class="quick-stats-card">
		<div class="card-header">
			<h3><i class="fa fa-money m-r-10"></i> Active Loans Principal by Product</h3>
		</div>
		<div class="quick-stats-grid">
			<?php
			if(!empty($loan_products)) {
				$colors = ['purple', 'blue', 'teal', 'green', 'purple', 'blue'];
				$index = 0;
				foreach($loan_products as $product) {
					$principal_data = get_active_loans_principal_by_product($product->loan_product_id);
					$principal_amount = !empty($principal_data) ? number_format($principal_data->total_principal, 2) : number_format(0, 2);
					$count = !empty($principal_data) ? $principal_data->count : 0;
					$color = $colors[$index % count($colors)];
					$index++;
			?>
			<a class="quick-stat-item" href="#">
				<div class="quick-stat-icon <?php echo $color; ?>">
					<i class="fa fa-file-text-o"></i>
				</div>
				<div class="quick-stat-content">
					<div class="quick-stat-value"><?php echo $settings->currency?> <?php echo $principal_amount; ?></div>
					<span class="quick-stat-label"><?php echo $product->product_name; ?></span>
					<div class="quick-stat-count">Count: <?php echo $count; ?></div>
				</div>
			</a>
			<?php
				}
			}
			$total_principal = get_total_active_loans_principal();
			?>
			<a class="quick-stat-item" href="#">
				<div class="quick-stat-icon red">
					<i class="fa fa-calculator"></i>
				</div>
				<div class="quick-stat-content">
					<div class="quick-stat-value"><?php echo $settings->currency?> <?php echo !empty($total_principal) ? number_format($total_principal->total_principal, 2) : number_format(0, 2); ?></div>
					<span class="quick-stat-label">TOTAL PRINCIPAL</span>
					<div class="quick-stat-count"><?php echo !empty($total_principal) ? 'Count: '.$total_principal->count : 'Count: 0'; ?></div>
				</div>
			</a>
		</div>
	</div>

	<!-- Outstanding Balances by Product Card -->
	<div class="quick-stats-card">
		<div class="card-header">
			<h3><i class="fa fa-balance-scale m-r-10"></i> Outstanding Balances by Product</h3>
		</div>
		<div class="quick-stats-grid">
			<?php
			if(!empty($loan_products)) {
				$colors = ['orange', 'yellow', 'teal', 'blue', 'orange', 'yellow'];
				$index = 0;
				foreach($loan_products as $product) {
					$balance_data = get_outstanding_balance_by_product($product->loan_product_id);
					$outstanding_balance = !empty($balance_data) ? number_format($balance_data->outstanding_balance, 2) : number_format(0, 2);
					$color = $colors[$index % count($colors)];
					$index++;
			?>
			<a class="quick-stat-item" href="#">
				<div class="quick-stat-icon <?php echo $color; ?>">
					<i class="fa fa-file-text-o"></i>
				</div>
				<div class="quick-stat-content">
					<div class="quick-stat-value"><?php echo $settings->currency?> <?php echo $outstanding_balance; ?></div>
					<span class="quick-stat-label"><?php echo $product->product_name; ?></span>
					<div class="quick-stat-count">Outstanding Balance</div>
				</div>
			</a>
			<?php
				}
			}
			$total_balance = get_total_outstanding_balance();
			?>
			<a class="quick-stat-item" href="#">
				<div class="quick-stat-icon red">
					<i class="fa fa-exclamation-triangle"></i>
				</div>
				<div class="quick-stat-content">
					<div class="quick-stat-value"><?php echo $settings->currency?> <?php echo !empty($total_balance) ? number_format($total_balance->outstanding_balance, 2) : number_format(0, 2); ?></div>
					<span class="quick-stat-label">TOTAL OUTSTANDING</span>
					<div class="quick-stat-count">All Active Loans</div>
				</div>
			</a>
		</div>
	</div>

</div>
