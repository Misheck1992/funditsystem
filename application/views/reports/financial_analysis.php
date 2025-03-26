
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
			<form action="<?php echo base_url('reports/financial_analysis') ?>" method="get">
				<fieldset>
					<legend>Report filter</legend>
					<div id="controlgroup">

						Date from:<input type="text" class="dpicker" name="from" value="<?php  echo $this->input->get('from')?>" >
						Date to:<input type="text" class="dpicker" name="to" value="<?php  echo $this->input->get('to')?>" >
						<button type="submit" name="search" value="filter">Filter</button>
						<button type="submit" name="search" value="pdf"><i class="fa fa-file-pdf text-danger"></i></button>
						<button type="submit" name="search" value="excel"><i class="fa fa-file-excel text-success"></i></button>
					</div>
				</fieldset>
			</form>
			<hr>
			<p>Search results</p>
			<table class="table table-bordered">
				<tr>
					<td>(a)interests income from Loans</td>
					<td style="text-align: left;"> ZMW <?php echo number_format($interests_income->interest,2) ?></td>
				</tr>
				<tr>
					<td>(b) Income from Administration Fee</td>
					<td style="text-align: left;">ZMW<?php
						echo number_format($admin_income->amount,2) ;
						?></td>
				</tr>
				<tr>
					<td>(c)Commissions</td>
					<td style="text-align: left;">ZMW<?php
						echo number_format($commissions,2);
						?></td>
				</tr>
				<tr>
					<td>(d)Other loan related income  (penalties ie late payment charges)</td>
					<td style="text-align: left;">ZMW<?php echo number_format($late_fee->amount,2)?></td>
				</tr>
				<tr>
					<td>(e) GROSS INCOME FROM MONEY LENDING (a+b+c+d)</td>
					<td style="text-align: left;">ZMW<?php  echo number_format($interests_income->interest+$admin_income->amount+$commissions+$late_fee->amount,2)?></td>
				</tr>
				<tr>
					<td>(f)Bad Debts</td>
					<td style="text-align: left;">ZMW<?php echo number_format($bad_debits->principal,2)?></td>
				</tr>
				<tr>
					<td>(g)Interest paid (cost of funding)</td>
					<td style="text-align: left;">ZMW <?php echo number_format($interest_paid->interest_paid,2)?></td>
				</tr>
				<tr>
					<td>(h)NET INCOME FROM MICROCREDIT OPERATIONS (e-f-g)</td>
					<td style="text-align: left;">ZMW <?php  echo number_format(($interests_income->interest+$admin_income->amount+$commissions+$late_fee->amount)-$bad_debits->principal-$interest_paid->interest_paid,2)?></td>
				</tr>
				<tr>
					<td>(i)TOTAL OPERATION EXPENSES RELATED TO CREDIT TRANSACTIONS</td>
					<td style="text-align: left;">ZMW <?php echo number_format($expenses->amount,2)?></td>
				</tr>
				<tr>
					<td>(j)NET PROFIT BEFORE TAX (h-i)</td>
					<td style="text-align: left;">ZMW<?php echo number_format((($interests_income->interest+$admin_income->amount+$commissions+$late_fee->amount)-$bad_debits->principal-$interest_paid->interest_paid)-$expenses->amount,2)  ?></td>
				</tr>
				<tr>
					<td>(k)Less tax Paid</td>
					<td style="text-align: left;">ZMW<?php
						$g = get_by_id('settings','settings_id ',1);
						$a = (($interests_income->interest+$admin_income->amount+$commissions+$late_fee->amount)-$bad_debits->principal-$interest_paid->interest_paid)-$expenses->amount;
						$b = ((($interests_income->interest+$admin_income->amount+$commissions+$late_fee->amount)-$bad_debits->principal-$interest_paid->interest_paid)-$expenses->amount)*($g->tax/100);

					echo number_format(((($interests_income->interest+$admin_income->amount+$commissions+$late_fee->amount)-$bad_debits->principal-$interest_paid->interest_paid)-$expenses->amount)*($g->tax/100),2)
						?></td>
				</tr>
				<tr>
					<td>(l)NET PROFIT AFTER TAX (j-k)</td>
					<td style="text-align: left;">ZMW <?php echo number_format(($a-$b),2)?></td>
				</tr>
			</table>
		</div>
	</div>
</div>
