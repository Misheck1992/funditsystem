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
        <h2 style="margin-top:0px">Training <?php echo $button ?></h2>
        <form action="<?php echo $action; ?>" method="post">
	    <div class="form-group">
            <label for="int">Trainings Id <?php echo form_error('trainings_id') ?></label>
            <input type="text" class="form-control" name="trainings_id" id="trainings_id" placeholder="Trainings Id" value="<?php echo $trainings_id; ?>" />
        </div>
	    <div class="form-group">
            <label for="int">Training <?php echo form_error('training') ?></label>
            <input type="text" class="form-control" name="training" id="training" placeholder="Training" value="<?php echo $training; ?>" />
        </div>
	    <div class="form-group">
            <label for="int">Customer <?php echo form_error('customer') ?></label>
            <input type="text" class="form-control" name="customer" id="customer" placeholder="Customer" value="<?php echo $customer; ?>" />
        </div>
	    <input type="hidden" name="" value="<?php echo $; ?>" /> 
	    <button type="submit" class="btn btn-primary"><?php echo $button ?></button> 
	    <a href="<?php echo site_url('training') ?>" class="btn btn-default">Cancel</a>
	</form>
    </body>
</html>