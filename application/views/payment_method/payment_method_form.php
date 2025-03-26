<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Payment methods</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">Payment methods</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
        <h2 style="margin-top:0px">Payment_method <?php echo $button ?></h2>
        <form action="<?php echo $action; ?>" method="post">
	    <div class="form-group">
            <label for="varchar">Payment Method Name <?php echo form_error('payment_method_name') ?></label>
            <input type="text" class="form-control" name="payment_method_name" id="payment_method_name" placeholder="Payment Method Name" value="<?php echo $payment_method_name; ?>" />
        </div>
	    <div class="form-group">
            <label for="description">Description <?php echo form_error('description') ?></label>
            <textarea class="form-control" rows="3" name="description" id="description" placeholder="Description"><?php echo $description; ?></textarea>
        </div>
	   
	    <input type="hidden" name="payment_method" value="<?php echo $payment_method; ?>" /> 
	    <button type="submit" class="btn btn-primary"><?php echo $button ?></button> 

	</form>
        </div>
    </div>
</div>