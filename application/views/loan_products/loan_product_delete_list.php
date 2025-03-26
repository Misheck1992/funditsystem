<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">All Loan loan products</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">All loan products</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
            <div style="overflow-y: auto">
                <table  id="data-table" class="tableCss">
            <thead>
            <tr>
                <th>No</th>
		<th>Product Name</th>
		<th>Interest</th>
		<th>Frequency</th>

		<th>Penalty Threshold</th>
		<th>Penalty Charge Type above</th>
		<th>Penalty Charge Type below</th>
		<th>Penalty Fixed Charge Below</th>
		<th>Penalty Variable Charge Below</th>
		<th>Penalty Fixed Charge Above</th>
		<th>Penalty Variable Charge Above</th>
		<th>Loan Processing Fee Threshold</th>
		<th>Processing Charge Type above</th>
		<th>Processing Charge Type below</th>
		<th>Processing Fixed Charge Above</th>
		<th>Processing Variable Charge Above</th>
		<th>Processing Fixed Charge Below</th>
		<th>Processing Variable Charge Below</th>
		<th>Minimum Principal</th>
		<th>Maximum Principal</th>
		<th>Grace Period</th>
		<th>Added By</th>
		<th>Date Created</th>
			<th>Actions</th>
		
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($loan_products_data as $loan_products)
            {
                ?>
                <tr>
			<td width="80px"><?php echo ++$start ?></td>
			<td><?php echo $loan_products->product_name ?></td>
			<td><?php echo $loan_products->interest ?></td>
			<td><?php echo $loan_products->frequency ?>%</td>

			<td><?php echo $loan_products->penalty_threshold ?></td>
			<td><?php echo $loan_products->penalty_charge_type_above ?></td>
			<td><?php echo $loan_products->penalty_charge_type_above ?></td>
			<td><?php echo $loan_products->penalty_fixed_charge_below ?></td>
			<td><?php echo $loan_products->penalty_variable_charge_below ?>%</td>
			<td><?php echo $loan_products->penalty_fixed_charge_above ?></td>
			<td><?php echo $loan_products->penalty_variable_charge_above ?>%</td>
			<td><?php echo $loan_products->loan_processing_fee_threshold ?></td>
			<td><?php echo $loan_products->processing_charge_type_above ?></td>
			<td><?php echo $loan_products->processing_charge_type_below ?></td>
			<td><?php echo $loan_products->processing_fixed_charge_above ?></td>
			<td><?php echo $loan_products->processing_variable_charge_above ?>%</td>
			<td><?php echo $loan_products->processing_fixed_charge_below ?></td>
			<td><?php echo $loan_products->processing_variable_charge_below ?>%</td>
			<td><?php echo $loan_products->minimum_principal ?></td>
			<td><?php echo $loan_products->maximum_principal ?></td>
			<td><?php echo $loan_products->grace_period ?></td>
			<td><?php echo $loan_products->added_by ?></td>
			<td><?php echo $loan_products->date_created ?></td>
			<td style="text-align:center" width="200px">
				<?php 
			
				echo anchor(site_url('loan_products/delete/'.$loan_products->loan_product_id),'Delete'); 
				?>
			</td>

		</tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        </div>
    </div>
</div>