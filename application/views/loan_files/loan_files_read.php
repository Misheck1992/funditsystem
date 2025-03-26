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
        <h2 style="margin-top:0px">Loan_files Read</h2>
        <table class="table">
	    <tr><td>Loan Id</td><td><?php echo $loan_id; ?></td></tr>
	    <tr><td>File Name</td><td><?php echo $file_name; ?></td></tr>
	    <tr><td>Real File</td><td><?php echo $real_file; ?></td></tr>
	    <tr><td>File Stamp</td><td><?php echo $file_stamp; ?></td></tr>
	    <tr><td></td><td><a href="<?php echo site_url('loan_files') ?>" class="btn btn-default">Cancel</a></td></tr>
	</table>
        </body>
</html>