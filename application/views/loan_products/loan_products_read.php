<!doctype html>
<html>
    <head>
        <title>harviacode.com - codeigniter crud generator</title>
        <link rel="stylesheet" href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css') ?>"/>
        <style>
            body{
                padding: 15px;
            }
        </style>
    </head>
    <body>
        <h2 style="margin-top:0px">Loan_products Read</h2>
        <table class="table">
	    <tr><td>Product Name</td><td><?php echo $product_name; ?></td></tr>
	    <tr><td>Interest</td><td><?php echo $interest; ?></td></tr>
	    <tr><td>Frequency</td><td><?php echo $frequency; ?></td></tr>
	    <tr><td>Penalty</td><td><?php echo $penalty; ?></td></tr>
	    <tr><td>Penalty Threshold</td><td><?php echo $penalty_threshold; ?></td></tr>
	    <tr><td>Penalty Charge Type</td><td><?php echo $penalty_charge_type; ?></td></tr>
	    <tr><td>Penalty Fixed Charge Below</td><td><?php echo $penalty_fixed_charge_below; ?></td></tr>
	    <tr><td>Penalty Variable Charge Below</td><td><?php echo $penalty_variable_charge_below; ?></td></tr>
	    <tr><td>Penalty Fixed Charge Above</td><td><?php echo $penalty_fixed_charge_above; ?></td></tr>
	    <tr><td>Penalty Variable Charge Above</td><td><?php echo $penalty_variable_charge_above; ?></td></tr>
	    <tr><td>Loan Processing Fee Threshold</td><td><?php echo $loan_processing_fee_threshold; ?></td></tr>
	    <tr><td>Processing Charge Type</td><td><?php echo $processing_charge_type; ?></td></tr>
	    <tr><td>Processing Fixed Charge Above</td><td><?php echo $processing_fixed_charge_above; ?></td></tr>
	    <tr><td>Processing Variable Charge Above</td><td><?php echo $processing_variable_charge_above; ?></td></tr>
	    <tr><td>Processing Fixed Charge Below</td><td><?php echo $processing_fixed_charge_below; ?></td></tr>
	    <tr><td>Processing Variable Charge Below</td><td><?php echo $processing_variable_charge_below; ?></td></tr>
	    <tr><td>Minimum Principal</td><td><?php echo $minimum_principal; ?></td></tr>
	    <tr><td>Maximum Principal</td><td><?php echo $maximum_principal; ?></td></tr>
	    <tr><td>Added By</td><td><?php echo $added_by; ?></td></tr>
	    <tr><td>Date Created</td><td><?php echo $date_created; ?></td></tr>
	    <tr><td></td><td><a href="<?php echo site_url('loan_products') ?>" class="btn btn-default">Cancel</a></td></tr>
	</table>
        </body>
</html>