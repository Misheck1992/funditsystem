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

        <!-- SMTP Email Configuration Section -->
        <div style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 25px 0;">
            <h4 style="margin: 0 0 20px 0; color: #1e3a5f; border-bottom: 2px solid #1e3a5f; padding-bottom: 10px;">
                <i class="fa fa-envelope" style="margin-right: 8px;"></i>SMTP Email Configuration
            </h4>
            <p style="color: #6b7280; font-size: 13px; margin-bottom: 20px;">
                Configure your SMTP settings to enable email notifications from the system.
            </p>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="protocal">Protocol <?php echo form_error('protocal') ?></label>
                        <select class="form-control" name="protocal" id="protocal">
                            <option value="smtp" <?php echo (isset($protocal) && $protocal == 'smtp') ? 'selected' : ''; ?>>SMTP</option>
                            <option value="mail" <?php echo (isset($protocal) && $protocal == 'mail') ? 'selected' : ''; ?>>PHP Mail</option>
                            <option value="sendmail" <?php echo (isset($protocal) && $protocal == 'sendmail') ? 'selected' : ''; ?>>Sendmail</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email_host">SMTP Host <?php echo form_error('email_host') ?></label>
                        <input type="text" class="form-control" name="email_host" id="email_host" placeholder="e.g., smtp.gmail.com" value="<?php echo isset($email_host) ? $email_host : ''; ?>" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="email_port">SMTP Port <?php echo form_error('email_port') ?></label>
                        <select class="form-control" name="email_port" id="email_port">
                            <option value="587" <?php echo (isset($email_port) && $email_port == '587') ? 'selected' : ''; ?>>587 (TLS - Recommended)</option>
                            <option value="465" <?php echo (isset($email_port) && $email_port == '465') ? 'selected' : ''; ?>>465 (SSL)</option>
                            <option value="25" <?php echo (isset($email_port) && $email_port == '25') ? 'selected' : ''; ?>>25 (Unencrypted)</option>
                            <option value="2525" <?php echo (isset($email_port) && $email_port == '2525') ? 'selected' : ''; ?>>2525 (Alternative)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="email_user">SMTP Username <?php echo form_error('email_user') ?></label>
                        <input type="text" class="form-control" name="email_user" id="email_user" placeholder="your-email@domain.com" value="<?php echo isset($email_user) ? $email_user : ''; ?>" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="email_pass">SMTP Password <?php echo form_error('email_pass') ?></label>
                        <input type="password" class="form-control" name="email_pass" id="email_pass" placeholder="<?php echo (!empty($email_pass)) ? '********' : 'Enter password'; ?>" value="" />
                        <small class="text-muted">Leave empty to keep current password</small>
                    </div>
                </div>
            </div>
            <div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 6px; padding: 12px; margin-top: 15px;">
                <strong style="color: #92400e;"><i class="fa fa-info-circle"></i> Common SMTP Settings:</strong>
                <ul style="margin: 10px 0 0 20px; color: #92400e; font-size: 12px;">
                    <li><strong>Gmail:</strong> smtp.gmail.com, Port 587, Use App Password</li>
                    <li><strong>Outlook/Office 365:</strong> smtp.office365.com, Port 587</li>
                    <li><strong>Yahoo:</strong> smtp.mail.yahoo.com, Port 587</li>
                </ul>
            </div>
        </div>

        <!-- Test Email Button -->
        <div class="form-group">
            <button type="button" class="btn btn-info" onclick="testEmail()" id="test_email_btn">
                <i class="fa fa-paper-plane"></i> Send Test Email
            </button>
            <span id="test_email_result" style="margin-left: 10px;"></span>
        </div>

	    <input type="hidden" name="settings_id" value="<?php echo $settings_id; ?>" />
	    <button type="submit" class="btn btn-primary"><?php echo $button ?></button>
	    <a href="<?php echo site_url('settings') ?>" class="btn btn-default">Cancel</a>
	</form>

<script>
function testEmail() {
    var btn = document.getElementById('test_email_btn');
    var result = document.getElementById('test_email_result');

    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';
    result.innerHTML = '';

    $.ajax({
        url: '<?php echo base_url("settings/test_email"); ?>',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-paper-plane"></i> Send Test Email';

            if (response.success) {
                result.innerHTML = '<span class="text-success"><i class="fa fa-check-circle"></i> ' + response.message + '</span>';
            } else {
                result.innerHTML = '<span class="text-danger"><i class="fa fa-times-circle"></i> ' + response.message + '</span>';
            }
        },
        error: function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-paper-plane"></i> Send Test Email';
            result.innerHTML = '<span class="text-danger"><i class="fa fa-times-circle"></i> Error sending test email</span>';
        }
    });
}
</script>
		</div>
	</div>
</div>
