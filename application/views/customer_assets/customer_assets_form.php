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
        <h2 style="margin-top:0px">Customer_assets <?php echo $button ?></h2>
        <form action="<?php echo $action; ?>" method="post">
	    <div class="form-group">
            <label for="int">Customer <?php echo form_error('customer') ?></label>
            <input type="text" class="form-control" name="customer" id="customer" placeholder="Customer" value="<?php echo $customer; ?>" />
        </div>
	    <div class="form-group">
            <label for="int">Asset <?php echo form_error('asset') ?></label>
            <input type="text" class="form-control" name="asset" id="asset" placeholder="Asset" value="<?php echo $asset; ?>" />
        </div>
	    <input type="hidden" name="customer_asset_id" value="<?php echo $customer_asset_id; ?>" /> 
	    <button type="submit" class="btn btn-primary"><?php echo $button ?></button> 
	    <a href="<?php echo site_url('customer_assets') ?>" class="btn btn-default">Cancel</a>
	</form>
    </body>
</html>