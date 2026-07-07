<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="fd-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="page-title"><?php echo $page_title; ?></h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo site_url('Fixed_deposits'); ?>">Fixed Deposits</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo site_url('Fixed_deposits/customers'); ?>">Customers</a></li>
                    <li class="breadcrumb-item active"><?php echo isset($id) ? 'Edit' : 'Add'; ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <form method="post" action="<?php echo $action; ?>" enctype="multipart/form-data">
        <?php if (isset($id)): ?>
            <input type="hidden" name="id" value="<?php echo $id; ?>">
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8">
                <!-- Personal Information -->
                <div class="fd-card">
                    <div class="fd-card-header">
                        <span class="fd-card-title">Personal Information</span>
                    </div>
                    <div class="fd-card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Customer Number</label>
                                    <input type="text" name="customer_number" class="form-control" value="<?php echo $customer_number; ?>" readonly style="background: #f8f9fa;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>First Name <span style="color: #d93025;">*</span></label>
                                    <input type="text" name="first_name" class="form-control" value="<?php echo $first_name; ?>" placeholder="First name" required>
                                    <?php echo form_error('first_name'); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Last Name <span style="color: #d93025;">*</span></label>
                                    <input type="text" name="last_name" class="form-control" value="<?php echo $last_name; ?>" placeholder="Last name" required>
                                    <?php echo form_error('last_name'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Gender <span style="color: #d93025;">*</span></label>
                                    <select name="gender" class="form-control" required>
                                        <option value="">-- Select Gender --</option>
                                        <option value="Male" <?php echo $gender == 'Male' ? 'selected' : ''; ?>>Male</option>
                                        <option value="Female" <?php echo $gender == 'Female' ? 'selected' : ''; ?>>Female</option>
                                    </select>
                                    <?php echo form_error('gender'); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Date of Birth</label>
                                    <input type="date" name="date_of_birth" class="form-control" value="<?php echo $date_of_birth; ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Province <span style="color: #d93025;">*</span></label>
                                    <select name="province" class="form-control" required>
                                        <option value="">-- Select Province --</option>
                                        <?php
                                        $provinces = array('Central', 'Copperbelt', 'Eastern', 'Luapula', 'Lusaka', 'Muchinga', 'Northern', 'North-Western', 'Southern', 'Western');
                                        foreach ($provinces as $p): ?>
                                            <option value="<?php echo $p; ?>" <?php echo $province == $p ? 'selected' : ''; ?>><?php echo $p; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php echo form_error('province'); ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>District</label>
                                    <input type="text" name="district" class="form-control" value="<?php echo $district; ?>" placeholder="District">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>City/Town</label>
                                    <input type="text" name="city" class="form-control" value="<?php echo $city; ?>" placeholder="City or Town">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Physical Address</label>
                                    <input type="text" name="address" class="form-control" value="<?php echo $address; ?>" placeholder="Street address">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="fd-card">
                    <div class="fd-card-header">
                        <span class="fd-card-title">Contact Information</span>
                    </div>
                    <div class="fd-card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Phone Number <span style="color: #d93025;">*</span></label>
                                    <input type="text" name="phone_number" class="form-control" value="<?php echo $phone_number; ?>" placeholder="e.g., 0971234567" required>
                                    <?php echo form_error('phone_number'); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Alternative Phone</label>
                                    <input type="text" name="alt_phone_number" class="form-control" value="<?php echo $alt_phone_number; ?>" placeholder="Alternative number">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="<?php echo $email; ?>" placeholder="email@example.com">
                                    <?php echo form_error('email'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employment / Source of Funds -->
                <div class="fd-card">
                    <div class="fd-card-header">
                        <span class="fd-card-title">Employment / Source of Funds</span>
                    </div>
                    <div class="fd-card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Occupation</label>
                                    <input type="text" name="occupation" class="form-control" value="<?php echo $occupation; ?>" placeholder="e.g., Business Owner">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Employer / Business Name</label>
                                    <input type="text" name="employer" class="form-control" value="<?php echo $employer; ?>" placeholder="Company or business name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Source of Funds</label>
                                    <select name="source_of_funds" class="form-control">
                                        <option value="">-- Select Source --</option>
                                        <option value="Employment" <?php echo $source_of_funds == 'Employment' ? 'selected' : ''; ?>>Employment Income</option>
                                        <option value="Business" <?php echo $source_of_funds == 'Business' ? 'selected' : ''; ?>>Business Income</option>
                                        <option value="Investment" <?php echo $source_of_funds == 'Investment' ? 'selected' : ''; ?>>Investment Returns</option>
                                        <option value="Savings" <?php echo $source_of_funds == 'Savings' ? 'selected' : ''; ?>>Personal Savings</option>
                                        <option value="Pension" <?php echo $source_of_funds == 'Pension' ? 'selected' : ''; ?>>Pension/Retirement</option>
                                        <option value="Inheritance" <?php echo $source_of_funds == 'Inheritance' ? 'selected' : ''; ?>>Inheritance</option>
                                        <option value="Other" <?php echo $source_of_funds == 'Other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Identification & Documents -->
                <div class="fd-card">
                    <div class="fd-card-header">
                        <span class="fd-card-title">Identification & Documents</span>
                    </div>
                    <div class="fd-card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>ID Type</label>
                                    <select name="id_type" class="form-control" id="id_type" onchange="toggleExpiryDate()">
                                        <option value="">-- Select ID Type --</option>
                                        <option value="NRC" <?php echo $id_type == 'NRC' ? 'selected' : ''; ?>>NRC</option>
                                        <option value="Passport" <?php echo $id_type == 'Passport' ? 'selected' : ''; ?>>Passport</option>
                                        <option value="Drivers License" <?php echo $id_type == 'Drivers License' ? 'selected' : ''; ?>>Driver's License</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>ID Number</label>
                                    <input type="text" name="id_number" class="form-control" value="<?php echo $id_number; ?>" placeholder="ID number">
                                </div>
                            </div>
                            <div class="col-md-4" id="expiry_date_group" style="<?php echo ($id_type == 'NRC' || empty($id_type)) ? 'display:none;' : ''; ?>">
                                <div class="form-group">
                                    <label>ID Expiry Date</label>
                                    <input type="date" name="id_expiry_date" class="form-control" value="<?php echo $id_expiry_date; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>NRC/ID Photo (JPG, PNG, PDF - Max 5MB)</label>
                                    <?php if (!empty($nrc_photo)): ?>
                                        <div style="margin-bottom: 8px;">
                                            <a href="<?php echo base_url($nrc_photo); ?>" target="_blank" class="btn btn-default btn-sm">
                                                <i class="fas fa-eye mr-1"></i> View Current
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="nrc_photo" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                    <small style="color: #5f6368;">Upload clear copy of NRC or ID document</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Proof of Income (JPG, PNG, PDF - Max 5MB)</label>
                                    <?php if (!empty($proof_of_income)): ?>
                                        <div style="margin-bottom: 8px;">
                                            <a href="<?php echo base_url($proof_of_income); ?>" target="_blank" class="btn btn-default btn-sm">
                                                <i class="fas fa-eye mr-1"></i> View Current
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="proof_of_income" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                    <small style="color: #5f6368;">Payslip, bank statement, or business registration</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Next of Kin -->
                <div class="fd-card">
                    <div class="fd-card-header">
                        <span class="fd-card-title">Next of Kin Details</span>
                    </div>
                    <div class="fd-card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input type="text" name="nok_name" class="form-control" value="<?php echo $nok_name; ?>" placeholder="Next of kin full name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Relationship</label>
                                    <select name="nok_relationship" class="form-control">
                                        <option value="">-- Select Relationship --</option>
                                        <option value="Spouse" <?php echo $nok_relationship == 'Spouse' ? 'selected' : ''; ?>>Spouse</option>
                                        <option value="Parent" <?php echo $nok_relationship == 'Parent' ? 'selected' : ''; ?>>Parent</option>
                                        <option value="Child" <?php echo $nok_relationship == 'Child' ? 'selected' : ''; ?>>Child</option>
                                        <option value="Sibling" <?php echo $nok_relationship == 'Sibling' ? 'selected' : ''; ?>>Sibling</option>
                                        <option value="Relative" <?php echo $nok_relationship == 'Relative' ? 'selected' : ''; ?>>Other Relative</option>
                                        <option value="Friend" <?php echo $nok_relationship == 'Friend' ? 'selected' : ''; ?>>Friend</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="text" name="nok_phone" class="form-control" value="<?php echo $nok_phone; ?>" placeholder="Phone number">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>NRC/ID Number</label>
                                    <input type="text" name="nok_id_number" class="form-control" value="<?php echo $nok_id_number; ?>" placeholder="Next of kin ID number">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Address</label>
                                    <input type="text" name="nok_address" class="form-control" value="<?php echo $nok_address; ?>" placeholder="Physical address">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bank Details -->
                <div class="fd-card">
                    <div class="fd-card-header">
                        <span class="fd-card-title">Bank Details (for payouts)</span>
                    </div>
                    <div class="fd-card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Bank Name</label>
                                    <select name="bank_name" class="form-control">
                                        <option value="">-- Select Bank --</option>
                                        <?php
                                        $banks = array('ABSA Bank', 'Access Bank', 'Atlas Mara', 'Bank of China', 'Citibank', 'Ecobank', 'FNB Zambia', 'Indo Zambia Bank', 'Investrust Bank', 'Stanbic Bank', 'Standard Chartered', 'United Bank for Africa', 'Zambia Industrial Commercial Bank', 'Zambia National Commercial Bank');
                                        foreach ($banks as $bank): ?>
                                            <option value="<?php echo $bank; ?>" <?php echo $bank_name == $bank ? 'selected' : ''; ?>><?php echo $bank; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Account Number</label>
                                    <input type="text" name="bank_account_number" class="form-control" value="<?php echo $bank_account_number; ?>" placeholder="Bank account number">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Branch</label>
                                    <input type="text" name="bank_branch" class="form-control" value="<?php echo $bank_branch; ?>" placeholder="Branch name">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="fd-card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> <?php echo $button; ?>
                        </button>
                        <a href="<?php echo site_url('Fixed_deposits/customers'); ?>" class="btn btn-default ml-2">Cancel</a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Required Fields -->
                <div class="fd-card">
                    <div class="fd-card-header">
                        <span class="fd-card-title">Required Fields</span>
                    </div>
                    <div class="fd-card-body">
                        <ul style="font-size: 13px; color: #5f6368; padding-left: 20px; margin: 0;">
                            <li style="margin-bottom: 4px;">First Name & Last Name</li>
                            <li style="margin-bottom: 4px;">Gender & Province</li>
                            <li>Phone Number</li>
                        </ul>
                    </div>
                </div>

                <!-- KYC Checklist -->
                <div class="fd-card">
                    <div class="fd-card-header">
                        <span class="fd-card-title">KYC Checklist</span>
                    </div>
                    <div class="fd-card-body">
                        <?php
                        $kyc_items = array(
                            array('label' => 'ID Number', 'done' => !empty($id_number)),
                            array('label' => 'NRC/ID Photo', 'done' => !empty($nrc_photo)),
                            array('label' => 'Proof of Income', 'done' => !empty($proof_of_income)),
                            array('label' => 'Next of Kin', 'done' => !empty($nok_name)),
                            array('label' => 'Bank Details', 'done' => !empty($bank_account_number))
                        );
                        foreach ($kyc_items as $item): ?>
                            <div style="display: flex; align-items: center; gap: 8px; padding: 6px 0; font-size: 13px;">
                                <i class="fas fa-<?php echo $item['done'] ? 'check-circle' : 'circle'; ?>"
                                   style="color: <?php echo $item['done'] ? '#1e8e3e' : '#dadce0'; ?>;"></i>
                                <span style="color: <?php echo $item['done'] ? '#202124' : '#5f6368'; ?>;"><?php echo $item['label']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if (isset($id)): ?>
                    <!-- Quick Actions -->
                    <div class="fd-card">
                        <div class="fd-card-header">
                            <span class="fd-card-title">Quick Actions</span>
                        </div>
                        <div class="fd-card-body compact">
                            <ul class="fd-action-list">
                                <li><a href="<?php echo site_url('Fixed_deposits/customer_view/' . $id); ?>"><i class="fas fa-eye"></i> View Profile</a></li>
                                <li><a href="<?php echo site_url('Fixed_deposits/deposit_create/' . $id); ?>"><i class="fas fa-plus"></i> New Deposit</a></li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<script>
function toggleExpiryDate() {
    var idType = document.getElementById('id_type').value;
    var expiryGroup = document.getElementById('expiry_date_group');
    if (idType === 'NRC' || idType === '') {
        expiryGroup.style.display = 'none';
    } else {
        expiryGroup.style.display = 'block';
    }
}
</script>
