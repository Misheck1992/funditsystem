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
        <h2 style="margin-top:0px">Payment_method Read</h2>
        <table class="table">
	    <tr><td>Payment Method Name</td><td><?php echo $payment_method_name; ?></td></tr>
	    <tr><td>Description</td><td><?php echo $description; ?></td></tr>
	    <tr><td>Payment Method Stamp</td><td><?php echo $payment_method_stamp; ?></td></tr>
	    <tr><td></td><td><a href="<?php echo site_url('payment_method') ?>" class="btn btn-default">Cancel</a></td></tr>
	</table>
        </body>
</html>