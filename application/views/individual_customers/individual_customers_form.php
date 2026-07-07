<?php
$b = $this->Branches_model->get_all();
$countryd = $this->Geo_countries_model->get_all();

$zambian_provinces = [
    'Central' => 'Central Province',
    'Copperbelt' => 'Copperbelt Province',
    'Eastern' => 'Eastern Province',
    'Luapula' => 'Luapula Province',
    'Lusaka' => 'Lusaka Province',
    'Muchinga' => 'Muchinga Province',
    'Northern' => 'Northern Province',
    'Northwestern' => 'North-Western Province',
    'Southern' => 'Southern Province',
    'Western' => 'Western Province'
];
?>

<style>
.customer-form-container {
    max-width: 1400px;
    margin: 0 auto;
}
.form-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    margin-bottom: 1.5rem;
    overflow: hidden;
}
.form-card-header {
    background: #1e3a5f;
    color: #fff;
    padding: 1rem 1.5rem;
    font-weight: 600;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.form-card-header i {
    font-size: 1.1rem;
}
.form-card-body {
    padding: 1.5rem;
}
.form-section-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: #1e3a5f;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e5e7eb;
}
.form-label {
    font-weight: 600;
    color: #374151;
    font-size: 0.85rem;
    margin-bottom: 0.4rem;
}
.form-label .text-danger {
    color: #dc2626;
}
.form-control, .select2-container--default .select2-selection--single {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
    transition: all 0.2s ease;
    height: auto;
}
.form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    outline: none;
}
.form-control::placeholder {
    color: #9ca3af;
}
textarea.form-control {
    min-height: 80px;
    resize: vertical;
}
.form-group {
    margin-bottom: 1rem;
}

/* Upload Cards */
.upload-card {
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
    background: #f9fafb;
    cursor: pointer;
    transition: all 0.2s ease;
    min-height: 120px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.upload-card:hover {
    border-color: #3b82f6;
    background: #eff6ff;
}
.upload-card .upload-icon {
    font-size: 1.5rem;
    color: #9ca3af;
    margin-bottom: 0.5rem;
}
.upload-card:hover .upload-icon {
    color: #3b82f6;
}
.upload-card .upload-text {
    font-size: 0.8rem;
    color: #6b7280;
    margin: 0;
}
.upload-card input[type="file"] {
    display: none;
}
.upload-preview {
    margin-top: 0.5rem;
}
.upload-preview img {
    max-width: 100%;
    max-height: 80px;
    border-radius: 6px;
    border: 1px solid #e5e7eb;
}
.progress-container {
    margin-top: 0.5rem;
    display: none;
}
.progress {
    height: 6px;
    border-radius: 3px;
    background: #e5e7eb;
    overflow: hidden;
}
.progress-bar {
    background: #3b82f6;
    transition: width 0.3s ease;
}
.progress-text {
    font-size: 0.7rem;
    color: #6b7280;
    margin-top: 0.25rem;
    text-align: center;
}
.upload-success {
    color: #059669;
    font-size: 0.75rem;
    margin-top: 0.25rem;
    display: none;
}

/* Bank Details Section */
.bank-details-section {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}
.bank-details-section:last-child {
    margin-bottom: 0;
}

/* Buttons */
.btn-primary-custom {
    background: #1e3a5f;
    border: none;
    color: #fff;
    padding: 0.6rem 1.5rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.2s ease;
}
.btn-primary-custom:hover {
    background: #153050;
    color: #fff;
}
.btn-success-custom {
    background: #059669;
    border: none;
    color: #fff;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.85rem;
}
.btn-success-custom:hover {
    background: #047857;
    color: #fff;
}
.btn-danger-custom {
    background: #dc2626;
    border: none;
    color: #fff;
    padding: 0.4rem 0.75rem;
    border-radius: 6px;
    font-weight: 500;
    font-size: 0.8rem;
}
.btn-danger-custom:hover {
    background: #b91c1c;
    color: #fff;
}

/* Responsive */
@media (max-width: 992px) {
    .col-lg-8, .col-lg-4 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}
@media (max-width: 768px) {
    .form-card-body {
        padding: 1rem;
    }
    .col-md-3, .col-md-4, .col-md-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    .bank-details-section .col-3 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}
@media (max-width: 576px) {
    .col-3, .col-4, .col-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}
</style>

<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Register New Customer</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="<?php echo base_url('individual_customers')?>">Customers</a>
                <span class="breadcrumb-item active">New Customer</span>
            </nav>
        </div>
    </div>

    <div class="customer-form-container">
        <form action="<?php echo $action; ?>" method="post" id="customerForm">
            <input type="hidden" name="id" value="<?php echo $id; ?>" />

            <div class="row">
                <!-- Left Column - Main Form -->
                <div class="col-lg-8">
                    <!-- Personal Information -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <i class="fa fa-user"></i> Personal Information
                        </div>
                        <div class="form-card-body">
                            <div class="row">
                                <div class="col-md-2 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Title <span class="text-danger">*</span></label>
                                        <select class="form-control" name="Title" id="Title" required>
                                            <option value="">Select</option>
                                            <option value="Mr" <?php if($Title=="Mr"){echo "selected";} ?>>Mr</option>
                                            <option value="Mrs" <?php if($Title=="Mrs"){echo "selected";} ?>>Mrs</option>
                                            <option value="Miss" <?php if($Title=="Miss"){echo "selected";} ?>>Miss</option>
                                            <option value="Dr" <?php if($Title=="Dr"){echo "selected";} ?>>Dr</option>
                                            <option value="Rev" <?php if($Title=="Rev"){echo "selected";} ?>>Rev</option>
                                        </select>
                                        <?php echo form_error('Title') ?>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="Firstname" id="Firstname" placeholder="Enter first name" value="<?php echo $Firstname; ?>" required />
                                        <?php echo form_error('Firstname') ?>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Middle Name</label>
                                        <input type="text" class="form-control" name="Middlename" id="Middlename" placeholder="Enter middle name" value="<?php echo $Middlename; ?>" />
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="Lastname" id="Lastname" placeholder="Enter last name" value="<?php echo $Lastname; ?>" required />
                                        <?php echo form_error('Lastname') ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Gender <span class="text-danger">*</span></label>
                                        <select class="form-control" name="Gender" id="Gender" required>
                                            <option value="">Select Gender</option>
                                            <option value="MALE" <?php if($Gender=="MALE"){echo "selected";} ?>>Male</option>
                                            <option value="FEMALE" <?php if($Gender=="FEMALE"){echo "selected";} ?>>Female</option>
                                            <option value="OTHER" <?php if($Gender=="OTHER"){echo "selected";} ?>>Other</option>
                                        </select>
                                        <?php echo form_error('Gender') ?>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Marital Status</label>
                                        <select class="form-control" name="marital" id="marital">
                                            <option value="">Select Status</option>
                                            <option value="Single" <?php if($marital=="Single"){echo "selected";} ?>>Single</option>
                                            <option value="Married" <?php if($marital=="Married"){echo "selected";} ?>>Married</option>
                                            <option value="Separated" <?php if($marital=="Separated"){echo "selected";} ?>>Separated</option>
                                            <option value="Divorced" <?php if($marital=="Divorced"){echo "selected";} ?>>Divorced</option>
                                            <option value="Widowed" <?php if($marital=="Widowed"){echo "selected";} ?>>Widowed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="DateOfBirth" id="DateOfBirth" value="<?php echo $DateOfBirth; ?>" required />
                                        <?php echo form_error('DateOfBirth') ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="EmailAddress" id="EmailAddress" placeholder="email@example.com" value="<?php echo $EmailAddress; ?>" required />
                                        <?php echo form_error('EmailAddress') ?>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <select class="form-control" name="phone_country_code" id="phone_country_code" style="max-width: 110px; flex: 0 0 110px;">
                                                <option value="+260">+260 ZM</option>
                                                <option value="+27">+27 ZA</option>
                                                <option value="+263">+263 ZW</option>
                                                <option value="+265">+265 MW</option>
                                                <option value="+258">+258 MZ</option>
                                                <option value="+267">+267 BW</option>
                                                <option value="+264">+264 NA</option>
                                                <option value="+243">+243 CD</option>
                                                <option value="+255">+255 TZ</option>
                                                <option value="+254">+254 KE</option>
                                            </select>
                                            <?php
                                            // Extract phone number without country code for display
                                            $phone_display = '';
                                            if (!empty($PhoneNumber)) {
                                                $phone_display = preg_replace('/^\+\d{1,3}/', '', $PhoneNumber);
                                            }
                                            ?>
                                            <input type="text" class="form-control" name="phone_number_input" id="phone_number_input"
                                                   placeholder="9XXXXXXXX" value="<?php echo $phone_display; ?>"
                                                   pattern="[789][0-9]{3,}" minlength="4" required
                                                   title="Enter at least 4 digits starting with 7, 8, or 9" />
                                        </div>
                                        <small class="text-muted">At least 4 digits starting with 7, 8, or 9</small>
                                        <?php echo form_error('PhoneNumber') ?>
                                    </div>
                                </div>
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">WhatsApp Available?</label>
                                        <select class="form-control" name="noonwhatsap" id="noonwhatsap">
                                            <option value="">Select</option>
                                            <option value="Yes" <?php if($noonwhatsap=="Yes"){echo "selected";} ?>>Yes</option>
                                            <option value="No" <?php if($noonwhatsap=="No"){echo "selected";} ?>>No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Guarantor Information -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <i class="fa fa-user-friends"></i> Guarantor Information
                        </div>
                        <div class="form-card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Guarantor Full Name</label>
                                        <input type="text" class="form-control" name="kinFullname" id="kinFullname" placeholder="Enter guarantor's full name" value="<?php echo $kinFullname; ?>" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Guarantor Phone Number</label>
                                        <div class="input-group">
                                            <select class="form-control" name="kin_phone_country_code" id="kin_phone_country_code" style="max-width: 110px; flex: 0 0 110px;">
                                                <option value="+260">+260 ZM</option>
                                                <option value="+27">+27 ZA</option>
                                                <option value="+263">+263 ZW</option>
                                                <option value="+265">+265 MW</option>
                                                <option value="+267">+267 BW</option>
                                                <option value="+264">+264 NA</option>
                                                <option value="+254">+254 KE</option>
                                            </select>
                                            <?php
                                            $kin_phone_display = '';
                                            if (!empty($kinPhonenumber)) {
                                                $kin_phone_display = preg_replace('/^\+\d{1,3}/', '', $kinPhonenumber);
                                            }
                                            ?>
                                            <input type="text" class="form-control" name="kin_phone_input" id="kin_phone_input"
                                                   placeholder="9XXXXXXXX" value="<?php echo $kin_phone_display; ?>"
                                                   pattern="[0-9]{4,}" minlength="4" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <i class="fa fa-map-marker-alt"></i> Address Information
                        </div>
                        <div class="form-card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Postal Address</label>
                                        <textarea class="form-control" name="AddressLine1" id="AddressLine1" placeholder="Enter postal address" rows="3"><?php echo $AddressLine1; ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Physical Address</label>
                                        <textarea class="form-control" name="AddressLine2" id="AddressLine2" placeholder="Enter physical address" rows="3"><?php echo $AddressLine2; ?></textarea>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Guarantor Address</label>
                                        <textarea class="form-control" name="AddressLine3" id="AddressLine3" placeholder="Enter guarantor's address" rows="3"><?php echo $AddressLine3; ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Country <span class="text-danger">*</span></label>
                                        <select class="form-control select2" name="Country" id="Country" required>
                                            <option value="">Select Country</option>
                                            <?php foreach ($countryd as $country): ?>
                                                <option value="<?php echo $country->name; ?>" <?php if(isset($Country) && $Country == $country->name){ echo "selected"; } elseif($country->name == 'Zambia'){ echo "selected"; } ?>><?php echo $country->name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php echo form_error('Country') ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Province <span class="text-danger">*</span></label>
                                        <select class="form-control select2" name="Province" id="Province" required>
                                            <option value="">-- Select Province --</option>
                                            <?php foreach ($zambian_provinces as $code => $name): ?>
                                                <option value="<?php echo $code; ?>" <?php echo (isset($Province) && $Province == $code) ? 'selected' : ''; ?>><?php echo $name; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php echo form_error('Province') ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">City/Town <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="City" placeholder="Enter city/town" value="<?php echo $City; ?>" required />
                                        <?php echo form_error('City') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">District</label>
                                        <input type="text" class="form-control" name="district" id="district" placeholder="Enter district" value="<?php echo isset($district) ? $district : ''; ?>" />
                                        <?php echo form_error('district') ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Chief/TA</label>
                                        <input type="text" class="form-control" name="chiefta" id="chiefta" placeholder="Enter Chief/TA" value="<?php echo isset($chiefta) ? $chiefta : ''; ?>" />
                                        <?php echo form_error('chiefta') ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Street</label>
                                        <input type="text" class="form-control" name="village" id="village" placeholder="Enter street" value="<?php echo $village; ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bank Details -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <i class="fa fa-university"></i> Bank Details
                        </div>
                        <div class="form-card-body">
                            <div id="bank-details-container">
                                <div class="bank-details-section">
                                    <div class="row">
                                        <div class="col-md-3 col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label">Account Name</label>
                                                <input type="text" class="form-control" name="account_name[]" placeholder="Account holder name" required />
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label">Account Number</label>
                                                <input type="text" class="form-control" name="account_number[]" placeholder="Account number" required />
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label">Bank Name</label>
                                                <input type="text" class="form-control" name="bank_name[]" placeholder="Bank name" required />
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="form-group">
                                                <label class="form-label">Province</label>
                                                <select name="bank_branch[]" class="form-control" required>
                                                    <option value="">Select Province</option>
                                                    <?php foreach ($zambian_provinces as $code => $name): ?>
                                                        <option value="<?php echo $code; ?>"><?php echo $name; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <button type="button" class="btn btn-danger-custom remove-bank-details">
                                            <i class="fa fa-trash mr-1"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="add-bank-details" class="btn btn-success-custom mt-2">
                                <i class="fa fa-plus mr-1"></i> Add Bank Account
                            </button>
                        </div>
                    </div>

                    <!-- Employment & Income -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <i class="fa fa-briefcase"></i> Employment & Income
                        </div>
                        <div class="form-card-body">
                            <div class="row">
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Profession <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="Profession" id="Profession" placeholder="Enter profession" value="<?php echo $Profession; ?>" required />
                                        <?php echo form_error('Profession') ?>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Source of Income <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="SourceOfIncome" id="SourceOfIncome" placeholder="Enter income source" value="<?php echo $SourceOfIncome; ?>" required />
                                        <?php echo form_error('SourceOfIncome') ?>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Monthly Income <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="GrossMonthlyIncome" id="GrossMonthlyIncome" placeholder="Enter amount" value="<?php echo $GrossMonthlyIncome; ?>" required />
                                        <?php echo form_error('GrossMonthlyIncome') ?>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label">Residential Status</label>
                                        <select name="ResidentialStatus" id="ResidentialStatus" class="form-control">
                                            <option value="">Select Status</option>
                                            <option value="Owned" <?php if($ResidentialStatus=='Owned'){ echo "selected"; } ?>>Owned</option>
                                            <option value="Rented" <?php if($ResidentialStatus=='Rented'){ echo "selected"; } ?>>Rented</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Branch <span class="text-danger">*</span></label>
                                        <select class="form-control select2" name="Branch" id="Branch" required>
                                            <option value="">Select Branch</option>
                                            <?php foreach ($b as $br): ?>
                                                <option value="<?php echo $br->Code ?>" <?php if($br->Code==$Branch){echo "selected";}?>><?php echo $br->BranchName?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php echo form_error('Branch') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - KYC -->
                <div class="col-lg-4">
                    <div class="form-card">
                        <div class="form-card-header">
                            <i class="fa fa-id-card"></i> KYC Documents
                        </div>
                        <div class="form-card-body">
                            <input type="hidden" name="ClientId" id="Client">

                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label">ID Type <span class="text-danger">*</span></label>
                                        <select class="form-control" name="IDType" id="IDType" required onchange="toggleExpiryDateField()">
                                            <option value="">Select ID Type</option>
                                            <option value="NATIONAL_IDENTITY_CARD">National Identity Card</option>
                                            <option value="PASSPORT">Passport</option>
                                            <option value="DRIVING_LICENCE">Driving Licence</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">ID Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="IDNumber" id="IDNumber" placeholder="Enter ID number" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Issue Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="IssueDate" id="IssueDate" required />
                                    </div>
                                </div>
                            </div>

                            <div class="row" id="expiryDateRow" style="display: none;">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label">Expiry Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="ExpiryDate" id="ExpiryDate" />
                                    </div>
                                </div>
                            </div>

                            <div class="form-section-title mt-3">Document Uploads</div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="form-label">ID Front</label>
                                        <div class="upload-card" onclick="document.getElementById('id_front').click()">
                                            <input type="file" id="id_front" accept="image/*" onchange="uploadWithProgress('id_front')">
                                            <div class="upload-icon"><i class="fa fa-id-card"></i></div>
                                            <p class="upload-text">Click to upload</p>
                                        </div>
                                        <input type="hidden" id="id_front1" name="id_front">
                                        <div class="progress-container" id="id_front_progress">
                                            <div class="progress"><div class="progress-bar" style="width: 0%"></div></div>
                                            <div class="progress-text">0%</div>
                                        </div>
                                        <div class="upload-success" id="id_front_success"><i class="fa fa-check-circle"></i> Uploaded</div>
                                        <div class="upload-preview" id="id_front2"></div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="form-label">ID Back</label>
                                        <div class="upload-card" onclick="document.getElementById('Id_back').click()">
                                            <input type="file" id="Id_back" accept="image/*" onchange="uploadWithProgress('Id_back')">
                                            <div class="upload-icon"><i class="fa fa-id-card"></i></div>
                                            <p class="upload-text">Click to upload</p>
                                        </div>
                                        <input type="hidden" id="Id_back1" name="Id_back">
                                        <div class="progress-container" id="Id_back_progress">
                                            <div class="progress"><div class="progress-bar" style="width: 0%"></div></div>
                                            <div class="progress-text">0%</div>
                                        </div>
                                        <div class="upload-success" id="Id_back_success"><i class="fa fa-check-circle"></i> Uploaded</div>
                                        <div class="upload-preview" id="Id_back2"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="form-label">Photograph</label>
                                        <div class="upload-card" onclick="document.getElementById('photograph').click()">
                                            <input type="file" id="photograph" accept="image/*" onchange="uploadWithProgress('photograph')">
                                            <div class="upload-icon"><i class="fa fa-camera"></i></div>
                                            <p class="upload-text">Click to upload</p>
                                        </div>
                                        <input type="hidden" id="photograph1" name="photograph">
                                        <div class="progress-container" id="photograph_progress">
                                            <div class="progress"><div class="progress-bar" style="width: 0%"></div></div>
                                            <div class="progress-text">0%</div>
                                        </div>
                                        <div class="upload-success" id="photograph_success"><i class="fa fa-check-circle"></i> Uploaded</div>
                                        <div class="upload-preview" id="photograph2"></div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="form-label">Signature</label>
                                        <div class="upload-card" onclick="document.getElementById('signature').click()">
                                            <input type="file" id="signature" accept="image/*" onchange="uploadWithProgress('signature')">
                                            <div class="upload-icon"><i class="fa fa-signature"></i></div>
                                            <p class="upload-text">Click to upload</p>
                                        </div>
                                        <input type="hidden" id="signature1" name="signature">
                                        <div class="progress-container" id="signature_progress">
                                            <div class="progress"><div class="progress-bar" style="width: 0%"></div></div>
                                            <div class="progress-text">0%</div>
                                        </div>
                                        <div class="upload-success" id="signature_success"><i class="fa fa-check-circle"></i> Uploaded</div>
                                        <div class="upload-preview" id="signature2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-card">
                        <div class="form-card-body text-center">
                            <button type="submit" class="btn btn-primary-custom btn-lg btn-block">
                                <i class="fa fa-save mr-2"></i> <?php echo $button ?> Customer
                            </button>
                            <p class="text-muted mt-2 mb-0" style="font-size: 0.8rem;">
                                <i class="fa fa-info-circle mr-1"></i> All fields marked with <span class="text-danger">*</span> are required
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Global function to toggle expiry date field visibility
function toggleExpiryDateField() {
    var idType = document.getElementById('IDType').value;
    var expiryRow = document.getElementById('expiryDateRow');
    var expiryInput = document.getElementById('ExpiryDate');

    if (idType === 'NATIONAL_IDENTITY_CARD' || idType === '') {
        expiryRow.style.display = 'none';
        expiryInput.required = false;
    } else {
        expiryRow.style.display = 'block';
        expiryInput.required = true;
    }
}

$(document).ready(function() {
    // Trigger on page load to set initial state
    toggleExpiryDateField();

    // Add more bank details
    $('#add-bank-details').click(function() {
        var newBankSection = `
        <div class="bank-details-section">
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        <label class="form-label">Account Name</label>
                        <input type="text" class="form-control" name="account_name[]" placeholder="Account holder name" required />
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        <label class="form-label">Account Number</label>
                        <input type="text" class="form-control" name="account_number[]" placeholder="Account number" required />
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        <label class="form-label">Bank Name</label>
                        <input type="text" class="form-control" name="bank_name[]" placeholder="Bank name" required />
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="form-group">
                        <label class="form-label">Province</label>
                        <select name="bank_branch[]" class="form-control" required>
                            <option value="">Select Province</option>
                            <?php foreach ($zambian_provinces as $code => $name): ?>
                                <option value="<?php echo $code; ?>"><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <button type="button" class="btn btn-danger-custom remove-bank-details">
                    <i class="fa fa-trash mr-1"></i> Remove
                </button>
            </div>
        </div>
        `;
        $('#bank-details-container').append(newBankSection);
    });

    // Remove bank details section
    $(document).on('click', '.remove-bank-details', function() {
        if ($('.bank-details-section').length > 1) {
            $(this).closest('.bank-details-section').remove();
        } else {
            alert('At least one bank account is required.');
        }
    });
});

// Upload with progress
function uploadWithProgress(fieldId) {
    var fileInput = document.getElementById(fieldId);
    var file = fileInput.files[0];
    if (!file) return;

    var progressContainer = document.getElementById(fieldId + '_progress');
    var progressBar = progressContainer.querySelector('.progress-bar');
    var progressText = progressContainer.querySelector('.progress-text');
    var successMsg = document.getElementById(fieldId + '_success');
    var preview = document.getElementById(fieldId + '2');
    var hiddenInput = document.getElementById(fieldId + '1');

    progressContainer.style.display = 'block';
    successMsg.style.display = 'none';
    progressBar.style.width = '0%';
    progressText.textContent = '0%';

    var formData = new FormData();
    formData.append('file', file);

    var xhr = new XMLHttpRequest();

    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            var percent = Math.round((e.loaded / e.total) * 100);
            progressBar.style.width = percent + '%';
            progressText.textContent = percent + '%';
        }
    });

    xhr.addEventListener('load', function() {
        if (xhr.status === 200) {
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    setTimeout(function() {
                        progressContainer.style.display = 'none';
                        successMsg.style.display = 'block';
                        hiddenInput.value = response.data.file_name;
                        preview.innerHTML = '<img src="<?php echo base_url('uploads/')?>' + response.data.file_name + '" alt="Preview">';
                        setTimeout(function() { successMsg.style.display = 'none'; }, 3000);
                    }, 300);
                } else {
                    alert('Upload failed: ' + response.message);
                    progressContainer.style.display = 'none';
                }
            } catch (e) {
                alert('Upload failed. Please try again.');
                progressContainer.style.display = 'none';
            }
        }
    });

    xhr.addEventListener('error', function() {
        alert('Upload failed. Please check your connection.');
        progressContainer.style.display = 'none';
    });

    xhr.open('POST', '<?php echo base_url('Proofofidentity/upload')?>');
    xhr.send(formData);
}
</script>
