<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">Settings</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">-</a>
				<span class="breadcrumb-item active">Settings edit actions</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
        <h2 style="margin-top:0px">Settings Edit</h2>
        <form action="<?php echo $action; ?>" method="post">
	    <div class="form-group">
			<label for="Id_back" class="custom-file-upload" id="ppp"> Upload logo *  </label>(should be 140 x 70 and white background)
			<input type="file" class="upload-btn-wrapper"  onchange="uploadpro('Id_back')"  id="Id_back" placeholder="Id Back"  />
			<input type="text" id="Id_back1" name="logo" value="<?php echo $logo;?>" hidden required>
			<div id="prev_data">
				<img src="<?php echo base_url('uploads/').$logo?>" alt="" height="100" width="100">
			</div>
        </div>
	    <div class="form-group">
            <label for="address">Address <?php echo form_error('address') ?></label>
            <textarea class="form-control" rows="3" name="address" id="address" placeholder="Address"><?php echo $address; ?></textarea>
        </div>
	    <div class="form-group">
            <label for="varchar">Phone Number <?php echo form_error('phone_number') ?></label>
            <input type="text" class="form-control" name="phone_number" id="phone_number" placeholder="Phone Number" value="<?php echo $phone_number; ?>" />
        </div>
	    <div class="form-group">
            <label for="varchar">Company Name <?php echo form_error('company_name') ?></label>
            <input type="text" class="form-control" name="company_name" id="company_name" placeholder="Company Name" value="<?php echo $company_name; ?>" />
        </div>
	    <div class="form-group">
            <label for="varchar">Company Email <?php echo form_error('company_email') ?></label>
            <input type="text" class="form-control" name="company_email" id="company_email" placeholder="Company Email" value="<?php echo $company_email; ?>" />
        </div>
	    <div class="form-group">
            <label for="varchar">Currency <?php echo form_error('currency') ?></label>
            <input type="text" class="form-control" name="currency" id="currency" placeholder="Currency" value="<?php echo $currency; ?>" />
        </div>
			<div class="form-group">
            <label for="varchar">Tax (in %) <?php echo form_error('tax') ?></label>
            <input type="text" class="form-control" name="tax" id="currency" placeholder="Tax" value="<?php echo $tax; ?>" />
        </div>
            <div class="form-group">
            <label for="varchar">New Customer Reg fee <?php echo form_error('reg_fee_new') ?></label>
            <input type="text" class="form-control" name="reg_fee_new" id="reg_fee_new" placeholder="reg_fee_new" value="<?php echo $reg_fee_new; ?>" />
        </div>
        <div class="form-group">
            <label for="varchar">Old Applicant reg fee <?php echo form_error('reg_fee_old') ?></label>
            <input type="text" class="form-control" name="reg_fee_old" id="reg_fee_old" placeholder="reg_fee_old" value="<?php echo $reg_fee_old; ?>" />
        </div>
            <div class="form-group">
            <label for="varchar">Force Reg fee <?php echo form_error('require_reg_fee') ?></label>
                <select name="require_reg_fee" id="" class="form-control">
                    <option value="Yes">Yes</option>
                    <option value="Yes">No</option>
                </select>
<!--            <input type="text" class="form-control" name="reg_fee_old" id="reg_fee_old" placeholder="reg_fee_old" value="--><?php //echo $reg_fee_old; ?><!--" />-->
        </div>
        <div class="form-group">
            <label for="varchar">Loan defaults after how long (in days) <?php echo form_error('defaulter_durations') ?></label>
            <input type="text" class="form-control" name="defaulter_durations" id="currency" placeholder="defaulter_durations" value="<?php echo $defaulter_durations; ?>" />
        </div>
            <div class="form-group">
            <label for="varchar">Arrears grace time (in days) <?php echo form_error('arrears_grace') ?></label>
            <input type="text" class="form-control" name="arrears_grace" id="arrears_grace" placeholder="arrears_grace" value="<?php echo $arrears_grace; ?>" />
        </div>
	    <div class="form-group">
            <label for="varchar">Time Zone <?php echo form_error('time_zone') ?></label>

            <input type="text" class="form-control" name="time_zone" id="time_zone" placeholder="Time Zone" value="<?php echo $time_zone; ?>" />
        </div>
	    <input type="hidden" name="settings_id" value="<?php echo $settings_id; ?>" /> 
	    <button type="submit" class="btn btn-primary"><?php echo $button ?></button> 
	    <a href="<?php echo site_url('settings') ?>" class="btn btn-default">Cancel</a>
	</form>
		</div>
	</div>
</div>
