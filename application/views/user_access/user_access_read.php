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
        <h2 style="margin-top:0px">User_access Read</h2>
        <table class="table">
	    <tr><td>Password</td><td><?php echo $Password; ?></td></tr>
	    <tr><td>Employee</td><td><?php echo $Employee; ?></td></tr>
	    <tr><td>Status</td><td><?php echo $Status; ?></td></tr>
	    <tr><td></td><td><a href="<?php echo site_url('user_access') ?>" class="btn btn-default">Cancel</a></td></tr>
	</table>
        </body>
</html>