<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">Charges configuration</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">-</a>
				<span class="breadcrumb-item active">Configure charges</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
<form action="<?php echo $action; ?>" method="post">
	    <div class="form-group">
            <label for="varchar">Name <?php echo form_error('name') ?></label>
            <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="<?php echo $name; ?>" />
        </div>
	    <div class="form-group">
            <label for="enum">Charge Type <?php echo form_error('charge_type') ?></label>
			<select name="charge_type" id="charge_type" class="form-control">
				<option value="Fixed" <?php  if($charge_type=="Fixed"){echo "selected";}?>>Fixed</option>
				<option value="Variable" <?php  if($charge_type=="Variable"){echo "selected";}?>>Variable</option>
			</select>
        </div>
	    <div class="form-group">
            <label for="decimal">Fixed Amount <?php echo form_error('fixed_amount') ?></label>
            <input type="text" <?php  if($charge_type=="Variable"){echo "readonly";}?> class="form-control" name="fixed_amount" id="fixed_amount" placeholder="Fixed Amount" value="<?php echo $fixed_amount; ?>" />
        </div>
	    <div class="form-group">
            <label for="int">Variable Value percentage in decimal (ie 10%=0.1) of loan principal <?php echo form_error('variable_value') ?></label>
            <input type="text" <?php  if($charge_type=="Fixed"){echo "readonly";}?> class="form-control" name="variable_value" id="variable_value" placeholder="Variable Value" value="<?php echo $variable_value; ?>" />
        </div>
	    <input type="hidden" name="charge_id" value="<?php echo $charge_id; ?>" /> 
	    <button type="submit" class="btn btn-primary"><?php echo $button ?></button> 
	    <a href="<?php echo site_url('charges') ?>" class="btn btn-default">Cancel</a>
</form>
		</div>
	</div>
</div>

<script>

</script>
