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
        <h2 style="margin-top:0px">Rescheduled_payments Read</h2>
        <table class="table">
	    <tr><td>Loan Id</td><td><?php echo $loan_id; ?></td></tr>
	    <tr><td>Customer Id</td><td><?php echo $customer_id; ?></td></tr>
	    <tr><td>Customer Type</td><td><?php echo $customer_type; ?></td></tr>
	    <tr><td>Payment Number</td><td><?php echo $payment_number; ?></td></tr>
	    <tr><td>Payment Amount</td><td><?php echo $payment_amount; ?></td></tr>
	    <tr><td>Payment Date</td><td><?php echo $payment_date; ?></td></tr>
	    <tr><td>Pay Status</td><td><?php echo $pay_status; ?></td></tr>
	    <tr><td>Paid Amount</td><td><?php echo $paid_amount; ?></td></tr>
	    <tr><td>P Stamp</td><td><?php echo $p_stamp; ?></td></tr>
	    <tr><td></td><td><a href="<?php echo site_url('rescheduled_payments') ?>" class="btn btn-default">Cancel</a></td></tr>
	</table>
        </body>
</html>