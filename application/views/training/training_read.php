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
        <h2 style="margin-top:0px">Training Read</h2>
        <table class="table">
	    <tr><td>Trainings Id</td><td><?php echo $trainings_id; ?></td></tr>
	    <tr><td>Training</td><td><?php echo $training; ?></td></tr>
	    <tr><td>Customer</td><td><?php echo $customer; ?></td></tr>
	    <tr><td></td><td><a href="<?php echo site_url('training') ?>" class="btn btn-default">Cancel</a></td></tr>
	</table>
        </body>
</html>