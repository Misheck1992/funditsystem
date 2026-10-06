<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">Branch</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">-</a>
				<span class="breadcrumb-item active">Branche form</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" style="border: thick orange solid;border-radius: 14px;">
        <form action="<?php echo $action; ?>" method="post" class="form-row">
	    <div class="form-group col-6">
            <label for="BranchCode">Branch Code <span class="text-danger">*</span> <?php echo form_error('BranchCode') ?></label>
            <input type="number" min="1" step="1" class="form-control" name="BranchCode" id="BranchCode" placeholder="Enter branch code" value="<?php echo html_escape($BranchCode); ?>" required />
        </div>
	    <div class="form-group col-6">
            <label for="BranchName">Branch Name <span class="text-danger">*</span> <?php echo form_error('BranchName') ?></label>
            <input type="text" maxlength="255" class="form-control" name="BranchName" id="BranchName" placeholder="Enter branch name" value="<?php echo html_escape($BranchName); ?>" required />
        </div>
	    <div class="form-group col-6">
            <label for="branchAddressLine1">Address Line 1 <?php echo form_error('branch_address_line1') ?></label>
            <input type="text" maxlength="200" class="form-control" name="branch_address_line1" id="branchAddressLine1" placeholder="Enter address line 1" value="<?php echo html_escape($AddressLine1); ?>" />
        </div>
	    <div class="form-group col-6">
            <label for="branchAddressLine2">Address Line 2 <?php echo form_error('branch_address_line2') ?></label>
            <input type="text" maxlength="200" class="form-control" name="branch_address_line2" id="branchAddressLine2" placeholder="Enter address line 2" value="<?php echo html_escape($AddressLine2); ?>" />
        </div>
	    <div class="form-group col-12">
            <label for="City">City <span class="text-danger">*</span> <?php echo form_error('City') ?></label>
            <input type="text" maxlength="255" class="form-control" name="City" id="City" placeholder="Enter city" value="<?php echo html_escape($City); ?>" required />
        </div>

	    <input type="hidden" name="id" value="<?php echo $id; ?>" /> 
	    <button type="submit" class="btn btn-primary"><?php echo $button ?></button> 
	    <a href="<?php echo site_url('branches') ?>" class="btn btn-default">Cancel</a>
	</form>
		</div>
	</div>
</div>
