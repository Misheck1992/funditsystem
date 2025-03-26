<?php
$users = get_all('transaction_type');
$products = get_active_loan_products();
?>
<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">All Payment collections report</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">-</a>
				<span class="breadcrumb-item active">All payments collections report</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
			<form action="<?php echo base_url('reports/payments') ?>" method="get">
				<fieldset>
					<legend>Report filter</legend>
					<div id="controlgroup">
						Transaction type
						<select name="transaction_type_id">
							<option value="All">All transaction types</option>
							<?php
							foreach ($users as $user){
								?>
								<option value="<?php echo $user->transaction_type_id ;?>" <?php if($user->transaction_type_id ==$this->input->get('transaction_type_id')){echo 'selected';}  ?>><?php echo $user->name;?></option>
								<?php
							}

							?>

						</select>
						Loan product:
						<select name="loan">
							<option value="All">All loans products</option>
							<?php
							foreach ($products as $product){
								?>
								<option value="<?php echo $product->loan_product_id;?>" <?php if($product->loan_product_id ==$this->input->get('loan_product_id')){echo 'selected';}  ?>><?php echo $product->product_name;?></option>
								<?php
							}

							?>
						</select>

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
			<table  id="data-table" class="table">
				<thead>
				<tr>

					<th>#</th>
					<th>Transaction refID</th>
					<th>Loan Number</th>
					<th>Transactions type</th>
					<th>Payment number(when applicable)</th>
					<th>Amount</th>
					<th>Proof</th>
					<th>Payment date</th>
					<th>Officer</th>


				</tr>
				</thead>
				<tbody>
				<?php
				$n = 1;
foreach ($loan_data as $l){
	?>
				<tr>
					<td><?php echo $n;?></td>
					<td><?php echo $l->ref;?></td>
					<td><a href="<?php echo base_url('loan/view/').$l->loan_id  ?>"><?php echo $l->loan_number;?></a></td>
					<td><?php echo $l->name;?></td>
					<td><?php echo $l->payment_number;?></td>
					<td><?php echo $l->amount;?></td>
					<td><a href="<?php echo base_url('Transactions/download/').$l->transaction_id  ?>"><i class="fa fa-download"></i>Download proof</a></td>
					<td><?php echo $l->date_stamp;?></td>
					<td><a href="<?php echo base_url('employees/read/').$l->id  ?>"><?php echo $l->Firstname." ".$l->Lastname;?></a></td>

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
