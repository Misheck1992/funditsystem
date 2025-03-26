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
        <h2 style="margin-top:0px">Groups Read</h2>
        <table class="table">
	    <tr><td>Group Code</td><td><?php echo $group_code; ?></td></tr>
	    <tr><td>Group Name</td><td><?php echo $group_name; ?></td></tr>
	    <tr><td>Group Category</td><td><?php echo $group_category; ?></td></tr>
	    <tr><td>Branch</td><td><?php echo $branch; ?></td></tr>
	    <tr><td>Group Description</td><td><?php echo $group_description; ?></td></tr>
	    <tr><td>Group Added By</td><td><?php echo $group_added_by; ?></td></tr>
	    <tr><td>Group Registered Date</td><td><?php echo $group_registered_date; ?></td></tr>
	    <tr><td></td><td><a href="<?php echo site_url('groups') ?>" class="btn btn-default">Cancel</a></td></tr>
	</table>
        </body>
</html>