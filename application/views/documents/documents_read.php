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
        <h2 style="margin-top:0px">Documents Read</h2>
        <table class="table">
	    <tr><td>P Lid</td><td><?php echo $p_lid; ?></td></tr>
	    <tr><td>Document Name</td><td><?php echo $document_name; ?></td></tr>
	    <tr><td>File Link</td><td><?php echo $file_link; ?></td></tr>
	    <tr><td>Stamp</td><td><?php echo $stamp; ?></td></tr>
	    <tr><td></td><td><a href="<?php echo site_url('documents') ?>" class="btn btn-default">Cancel</a></td></tr>
	</table>
        </body>
</html>