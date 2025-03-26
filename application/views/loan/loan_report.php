<?php
$users = get_all('employees');
$products = get_all('loan_products');
?>
<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">All Loan report</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">-</a>
				<span class="breadcrumb-item active">All loans report</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
			<form action="<?php echo base_url('loan/loan_report_search') ?>" method="get">
			<fieldset>
				<legend>Report filter</legend>
				<div id="controlgroup">
					Employee:
					<select name="user">
						<option value="All">All Officers</option>
						<?php
						foreach ($users as $user){
							?>
							<option value="<?php echo $user->id;?>" <?php if($user->id==$this->input->get('user')){echo 'selected';}  ?>><?php echo $user->Firstname." ".$user->Lastname;?></option>
						<?php
						}

						?>



					</select>
					Loan product:
					<select name="product">
						<option value="All">All products</option>
						<?php
						foreach ($products as $product){
							?>
							<option value="<?php echo $product->loan_product_id;?>" <?php if($product->loan_product_id==$this->input->get('product')){echo 'selected';}  ?>><?php echo $product->product_name;?></option>
							<?php
						}

						?>
					</select>
					Loan status :
					<select name="status">
						<option value="All">Any status</option>
						<option value="ACTIVE" <?php if($this->input->get('status')=="ACTIVE"){echo 'selected';}  ?>>Active</option>
						<option value="CLOSED" <?php if($this->input->get('status')=="CLOSED"){echo 'selected';}  ?>>Closed</option>
						<option value="INITIATED" <?php if($this->input->get('status')=="INITIATED"){echo 'selected';}  ?>>Initiated</option>
						<option value="APPROVED" <?php if($this->input->get('status')=="APPROVED"){echo 'selected';}  ?>>Approved</option>
						<option value="REJECTED" <?php if($this->input->get('status')=="REJECTED"){echo 'selected';}  ?>>Rejected</option>
						<option value="DEFAULTED" <?php if($this->input->get('status')=="DEFAULTED"){echo 'selected';}  ?>>Defaulted</option>
					</select>
					Date from:<input type="text" class="dpicker" name="from" value="<?php  echo $this->input->get('from')?>" required>
					Date to:<input type="text" class="dpicker" name="to" value="<?php  echo $this->input->get('to')?>" required>
					<button type="submit" name="search" value="filter">Filter</button>
					<button type="submit" name="search" value="pdf"><i class="fa fa-file-pdf text-danger"></i></button>
					<button type="submit" name="search" value="excel"><i class="fa fa-file-excel text-success"></i></button>
				</div>
			</fieldset>
			</form>
			<hr>
			<p>Search results</p>
			<table  id="data-table" class="tableCss">
				<thead>
				<tr>

					<th>#</th>
					<th>Loan Number</th>
					<th>Loan Product</th>
					<th>Loan Customer</th>
					<th>Loan Date</th>
					<th>Loan Principal</th>
					<th>Loan Period</th>
					<th>Period Type</th>
					<th>Loan Officer</th>
					<th>Loan Amount Total</th>


					<th>Loan Status</th>
					<th>Loan Added Date</th>
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
						<td><?php echo $loan->loan_number ?></td>
						<td><?php echo $loan->product_name ?></td>
						<td><a href="<?php echo base_url('individual_customers/view/').$loan->loan_customer ?>""><?php


                            if ($loan->customer_type == 'group') {
                                $custgroup = get_by_id('groups', 'group_id', $loan->loan_customer);
                                if (!empty($custgroup)) {
                                    echo $custgroup->group_name;
                                }

                            }
                            else{
                                $custgroup = get_by_id('individual_customers', 'id', $loan->loan_customer);
                                if (!empty($custgroup)) {
                                    echo $custgroup->Firstname." ".$custgroup->Lastname;
                                }

                            }


                          ?></a></td>
						<td><?php echo $loan->loan_date ?></td>
						<td>MK<?php echo number_format($loan->loan_principal,2) ?></td>
						<td><?php echo $loan->loan_period ?></td>
						<td><?php echo $loan->period_type ?></td>
						<td><?php echo $loan->efname." ".$loan->elname ?></td>
						<td>MK<?php echo number_format($loan->loan_amount_total,2) ?></td>
						<td><?php echo $loan->loan_status ?></td>
						<td><?php echo $loan->loan_added_date ?></td>
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
