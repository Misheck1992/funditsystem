<?php
$groups = get_all('groups');

?>
<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">Previous loan</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">Loans</a>
				<span class="breadcrumb-item active">Previous loan create</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" style="border: thick #24C16B  solid;border-radius: 14px;">

        <form action="<?php echo $action; ?>" method="post">
			<div class="row">
				<div class="col-lg-5">
					<div class="row">


							<div class="form-group col-12">
								<label for="int">Group name  <?php echo form_error('customer_id') ?></label>
								<input type="text" name="customer_id" id="customer_loan" required class="form-control">


						</div>
					</div>
					<div class="row">
						<div class="form-group col-12">
							<label for="int">Group description </label>
							<textarea name="description" id="" cols="30" rows="10" class="form-control"></textarea>
						</div>
					</div>
						<div class="row">
							<div class="form-group col-6">
								<label for="date">Loan Effective Date <?php echo form_error('loan_effective_date') ?></label>
								<input type="date" class="form-control" name="loan_effective_date" id="loan_effective_date" placeholder="Loan Effective Date" value="<?php echo $loan_effective_date; ?>" />
							</div>
							<div class="form-group col-6">
								<label for="date">Loan End Date <?php echo form_error('loan_end_date') ?></label>
								<input type="date" class="form-control" name="loan_end_date" id="loan_end_date" placeholder="Loan End Date" value="<?php echo $loan_end_date; ?>" />
							</div>
						</div>
						<div class="row">
							<div class="form-group col-4">
								<label for="decimal">Amount  taken<?php echo form_error('amount') ?></label>
								<input type="text" class="form-control" name="amount" id="amount" placeholder="Amount Taken" value="<?php echo $amount; ?>" />
							</div>
							<div class="form-group col-4">
								<label for="decimal">Amount paid <?php echo form_error('amount_paid') ?></label>
								<input type="text" class="form-control" name="amount_paid" id="amount_paid" placeholder="Amount Paid" value="<?php echo $amount_paid; ?>" />
							</div>
							<div class="form-group col-4">
								<label for="enum">Payment Status <?php echo form_error('status') ?></label>
								<select name="status" id="" class="form-control">
									<option value="">--Select status</option>
									<option value="Paid">Paid</option>
									<option value="Partial Paid">Partial Paid</option>
									<option value="Not Paid">Not Paid</option>
									<option value="Written off">Written of</option>
								</select>

							</div>
						</div>

				</div>
				<div class="col-lg-7">
					<table class="table " id="user_table">
						<thead>
						<tr>
							<th width="35%">Document Name</th>
							<th width="35%">File</th>
							<th width="30%">Action</th>
						</tr>
						</thead>
						<tbody id="addf">

						</tbody>

					</table>

				</div>
			</div>




	    <input type="hidden" name="p_lid" value="<?php echo $p_lid; ?>" /> 
	    <button type="submit" class="btn btn-primary"><?php echo $button ?></button> 

	</form>
		</div>
	</div>
</div>
