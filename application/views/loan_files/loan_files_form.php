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
        <h2 style="margin-top:0px">Loan_files <?php echo $button ?></h2>
        <form action="<?php echo $action; ?>" method="post">
	    <div class="form-group">
            <label for="varchar">Loan Id <?php echo form_error('loan_id') ?></label>
            <input type="text" class="form-control" name="loan_id" id="loan_id" placeholder="Loan Id" value="<?php echo $loan_id; ?>" />
        </div>
	    <div class="form-group">
            <label for="varchar">File Name <?php echo form_error('file_name') ?></label>
            <input type="text" class="form-control" name="file_name" id="file_name" placeholder="File Name" value="<?php echo $file_name; ?>" />
        </div>
	    <div class="form-group">
            <label for="varchar">Real File <?php echo form_error('real_file') ?></label>
            <input type="text" class="form-control" name="real_file" id="real_file" placeholder="Real File" value="<?php echo $real_file; ?>" />
        </div>
	    <div class="form-group">
            <label for="datetime">File Stamp <?php echo form_error('file_stamp') ?></label>
            <input type="text" class="form-control" name="file_stamp" id="file_stamp" placeholder="File Stamp" value="<?php echo $file_stamp; ?>" />
        </div>
	    <input type="hidden" name="file_id" value="<?php echo $file_id; ?>" /> 
	    <button type="submit" class="btn btn-primary"><?php echo $button ?></button> 
	    <a href="<?php echo site_url('loan_files') ?>" class="btn btn-default">Cancel</a>
	</form>
    </body>
</html>