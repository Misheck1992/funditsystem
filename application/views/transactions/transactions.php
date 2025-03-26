<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">Transactions</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">-</a>
				<span class="breadcrumb-item active">All select transactions</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" style="border: thick green solid;border-radius: 14px;">
			<?php
			//$customer = get_all_by_id('individual_customers','customer_type','individual');
            $products = get_all('loan');
			?>
			<form action="<?php echo base_url('transactions/search') ?>" method="GET">

                Loan :

                <select name="loan" class="sselect">
                    <option value="All">All loans</option>
                    <?php
                    foreach ($products as $product){
                        ?>
                        <option value="<?php echo $product->loan_id;?>" <?php if($product->loan_id==$this->input->get('loan')){echo 'selected';}  ?>><?php echo $product->loan_number;?></option>
                        <?php
                    }

                    ?>
                </select>

				<input type="submit" class="btn btn-sm btn-info" value="Search transactions">
			</form>

			<hr>
			<p>
			<form action="<?php echo base_url('transactions/report') ?>" method="GET">
				<input type="text" name="loan_id" value="<?php echo $this->input->get('loan_id') ?>" hidden required>
				Results- <button type="submit" class="btn btn-success"><i class="fa fa-file-pdf text-danger"></i>Print</button>
			</form>
			</p>


			<table class="table table-bordered" id="data-table" >
				<thead>
				<tr>

					<th>Ref ID</th>
					<th>Loan number</th>

					<th>Paid amount</th>
					<th>Payment number</th>
					<th>Deposit Slip</th>
					<th>Added By</th>
					<th>Date</th>

				</tr>
				</thead>
				<tbody>
<?php
foreach ($data as $trans)
{
	?>
<tr>
	<td><?php echo $trans->ref  ?></td>
	<td><?php echo $trans->loan_number  ?></td>
	<td><?php echo $trans->amount  ?></td>
	<td><?php echo $trans->payment_number  ?></td>
	<td><a href="<?php echo base_url('uploads/').$trans->payment_proof?>" download >Deposit Slip <i class="fa fa-download fa-flip"></i></a></td>

	<td><?php echo $trans->Firstname.' '.$trans->Lastname;  ?></td>
	<td><?php echo $trans->date_stamp  ?></td>

</tr>
<?php


}
	?>
				</tbody>
			</table>
		</div>
	</div>
</div>
