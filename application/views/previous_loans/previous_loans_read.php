<?php
$file = get_all_by_id('documents','p_lid', $p_lid);


?>

<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">Previous loan</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">Loans</a>
				<span class="breadcrumb-item active">Previous loan read</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" style="border: thick #24C16B  solid;border-radius: 14px;">
        <table class="table">
	    <tr><td>Customer Id</td><td><?php echo $customer_id; ?></td></tr>


	    <tr><td>Loan Effective Date</td><td><?php echo $loan_effective_date; ?></td></tr>
	    <tr><td>Loan End Date</td><td><?php echo $loan_end_date; ?></td></tr>
	    <tr><td>Amount taken</td><td><?php echo $amount; ?></td></tr>
	    <tr><td>Amount Paid</td><td><?php echo $amount_paid; ?></td></tr>
	    <tr><td>Group desc</td><td><?php echo $description; ?></td></tr>
	    <tr><td>Status</td><td><?php echo $status; ?></td></tr>

	    <tr><td>Stamp</td><td><?php echo $stamp; ?></td></tr>
	    <tr><td></td><td><a href="<?php echo site_url('previous_loans') ?>" class="btn btn-default">Cancel</a></td></tr>
	</table>
			<table class="table">
				<tr>
					<th>File name</th>
					<th>file link</th>

				</tr>
				<tbody>
				<?php

				foreach ($file as $ff){
					?>
					<tr>
						<td><?php echo $ff->document_name;?></td>
						<td><a href="<?php echo base_url('uploads/').$ff->file_link;?>">Download file</a></td>
					</tr>
				<?php
				}
				?>

				</tbody>
			</table>
		</div>
	</div>
</div>
