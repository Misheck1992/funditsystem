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
        <h2 style="margin-top:0px">Working_days <?php echo $button ?></h2>
        <form action="<?php echo $action; ?>" method="post">
	    <div class="form-group">
            <label for="varchar">Day <?php echo form_error('day') ?></label>
            <input type="text" class="form-control" name="day" id="day" placeholder="Day" value="<?php echo $day; ?>" />
        </div>
	    <div class="form-group">
            <label for="enum">Value <?php echo form_error('value') ?></label>
            <input type="text" class="form-control" name="value" id="value" placeholder="Value" value="<?php echo $value; ?>" />
        </div>
	    <input type="hidden" name="id" value="<?php echo $id; ?>" /> 
	    <button type="submit" class="btn btn-primary"><?php echo $button ?></button> 
	    <a href="<?php echo site_url('working_days') ?>" class="btn btn-default">Cancel</a>
	</form>
    </body>
</html>