<?php
$shareholders = get_all('shareholders');
$b = $this->Branches_model->get_all();
$countryd = $this->Geo_countries_model->get_all();

// Zambian provinces array
$zambian_provinces = [
    'Central' => 'Central Province',
    'Copperbelt' => 'Copperbelt Province',
    'Eastern' => 'Eastern Province',
    'Luapula' => 'Luapula Province',
    'Lusaka' => 'Lusaka Province',
    'Muchinga' => 'Muchinga Province',
    'Northern' => 'Northern Province',
    'North-Western' => 'North-Western Province',
    'Southern' => 'Southern Province',
    'Western' => 'Western Province'
];
?>

<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Corporate Customers</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">All Corporate Customers form</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick #0268bc solid;border-radius: 14px;">

            <h2 style="margin-top:0px">Corporate_customers <?php echo $button ?></h2>

            <form id="corporateForm"  action="<?php echo $action; ?>" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-lg-6">
                        <p>Corporate Customer Information</p>
                        <div class="shareholder-container form-row">
                            <div class="form-group col-6">
                                <label for="varchar">Entity Name * <?php echo form_error('EntityName') ?></label>
                                <input type="text" class="form-control" name="EntityName" id="EntityName" placeholder="EntityName" value="<?php echo $EntityName; ?>" required />
                            </div>
                            <div class="form-group col-6">
                                <label for="entity_type">Entity Type * <?php echo form_error('entity_type') ?></label>
                                <select class="form-control" name="entity_type" id="entity_type" required>
                                    <option value="">Select Entity Type</option>
                                    <option value="Sole Proprietorship" >Sole Proprietorship</option>
                                    <option value="Partnership" >Partnership</option>
                                    <option value="Limited Company">Limited Company</option>
                                    <option value="Government Entity">Government Entity</option>
                                    <option value="Non Government Entity">Non Government Entity</option>
                                </select>
                            </div>
                            <div class="form-group col-6">
                                <label for="entity_type">Category * <?php echo form_error('category') ?></label>
                                <select class="form-control" name="category" id="category" required>
                                    <option value="">Select category</option>
                                    <option value="client" <?php echo ($category == 'client') ? 'selected' : ''; ?>>Client</option>
                                    <option value="off_taker" <?php echo ($category == 'off_taker') ? 'selected' : ''; ?>>Off taker</option>
                                </select>
                                <small class="form-text text-muted">Note: Shareholder information is not required for Off taker category</small>
                            </div>
                            <div class="form-group col-6">
                                <label for="date">Date Of Incorporation  <?php echo form_error('DateOfIncorporation') ?></label>
                                <input type="date" class="form-control" name="DateOfIncorporation" id="DateOfIncorporation" placeholder="DateOfIncorporation" value="<?php echo $DateOfIncorporation; ?>" />
                            </div>
                            <div class="form-group col-6">
                                <label for="varchar">Registration Number <?php echo form_error('RegistrationNumber') ?></label>
                                <input type="text" class="form-control" name="RegistrationNumber" id="RegistrationNumber" placeholder="RegistrationNumber" value="<?php echo $RegistrationNumber; ?>" />
                            </div>

                            <div class="form-group col-6">
                                <label for="varchar">Tax Identification Number <?php echo form_error('TaxIdentificationNumber') ?></label>
                                <input type="text" class="form-control" name="TaxIdentificationNumber" id="TaxIdentificationNumber" placeholder="TaxIdentificationNumber" value="<?php echo $TaxIdentificationNumber; ?>" />
                            </div>
                            <!-- Updated Nature of Business Select -->
                            <div class="form-group col-6">
                                <label for="nature_of_business">Nature of business <?php echo form_error('nature_of_business') ?></label>
                                <select class="form-control select2" name="nature_of_business" id="nature_of_business">
                                    <option value="" <?php echo ($nature_of_business == '') ? 'selected' : ''; ?>>-- Select Nature of Business --</option>
                                    <option value="Agriculture" <?php echo ($nature_of_business == 'Agriculture') ? 'selected' : ''; ?>>Agriculture</option>
                                    <option value="Mining" <?php echo ($nature_of_business == 'Mining') ? 'selected' : ''; ?>>Mining</option>
                                    <option value="Manufacturing" <?php echo ($nature_of_business == 'Manufacturing') ? 'selected' : ''; ?>>Manufacturing</option>
                                    <option value="Construction" <?php echo ($nature_of_business == 'Construction') ? 'selected' : ''; ?>>Construction</option>
                                    <option value="Retail Trade" <?php echo ($nature_of_business == 'Retail Trade') ? 'selected' : ''; ?>>Retail Trade</option>
                                    <option value="Wholesale Trade" <?php echo ($nature_of_business == 'Wholesale Trade') ? 'selected' : ''; ?>>Wholesale Trade</option>
                                    <option value="Transportation" <?php echo ($nature_of_business == 'Transportation') ? 'selected' : ''; ?>>Transportation & Logistics</option>
                                    <option value="Hospitality" <?php echo ($nature_of_business == 'Hospitality') ? 'selected' : ''; ?>>Hospitality & Tourism</option>
                                    <option value="Financial Services" <?php echo ($nature_of_business == 'Financial Services') ? 'selected' : ''; ?>>Financial Services</option>
                                    <option value="Real Estate" <?php echo ($nature_of_business == 'Real Estate') ? 'selected' : ''; ?>>Real Estate</option>
                                    <option value="Information Technology" <?php echo ($nature_of_business == 'Information Technology') ? 'selected' : ''; ?>>Information Technology</option>
                                    <option value="Healthcare" <?php echo ($nature_of_business == 'Healthcare') ? 'selected' : ''; ?>>Healthcare</option>
                                    <option value="Education" <?php echo ($nature_of_business == 'Education') ? 'selected' : ''; ?>>Education</option>
                                    <option value="Energy" <?php echo ($nature_of_business == 'Energy') ? 'selected' : ''; ?>>Energy & Utilities</option>
                                    <option value="Telecommunications" <?php echo ($nature_of_business == 'Telecommunications') ? 'selected' : ''; ?>>Telecommunications</option>
                                    <option value="Media" <?php echo ($nature_of_business == 'Media') ? 'selected' : ''; ?>>Media & Entertainment</option>
                                    <option value="Professional Services" <?php echo ($nature_of_business == 'Professional Services') ? 'selected' : ''; ?>>Professional Services</option>
                                    <option value="Food & Beverage" <?php echo ($nature_of_business == 'Food & Beverage') ? 'selected' : ''; ?>>Food & Beverage</option>
                                    <option value="Textile & Clothing" <?php echo ($nature_of_business == 'Textile & Clothing') ? 'selected' : ''; ?>>Textile & Clothing</option>
                                    <option value="Import/Export" <?php echo ($nature_of_business == 'Import/Export') ? 'selected' : ''; ?>>Import/Export</option>
                                    <option value="Non-profit Organization" <?php echo ($nature_of_business == 'Non-profit Organization') ? 'selected' : ''; ?>>Non-profit Organization</option>
                                    <option value="Other" <?php echo ($nature_of_business == 'Other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>

                            <!-- Industry Sector Select -->
                            <div class="form-group col-6">
                                <label for="industry_sector">Industry sector <?php echo form_error('industry_sector') ?></label>
                                <select class="form-control select2" name="industry_sector" id="industry_sector">
                                    <option value="" <?php echo ($industry_sector == '') ? 'selected' : ''; ?>>-- Select Industry Sector --</option>
                                    <option value="Agriculture" <?php echo ($industry_sector == 'Agriculture') ? 'selected' : ''; ?>>Agriculture, Forestry & Fishing</option>
                                    <option value="Mining" <?php echo ($industry_sector == 'Mining') ? 'selected' : ''; ?>>Mining & Extraction</option>
                                    <option value="Manufacturing" <?php echo ($industry_sector == 'Manufacturing') ? 'selected' : ''; ?>>Manufacturing</option>
                                    <option value="Construction" <?php echo ($industry_sector == 'Construction') ? 'selected' : ''; ?>>Construction</option>
                                    <option value="Wholesale Trade" <?php echo ($industry_sector == 'Wholesale Trade') ? 'selected' : ''; ?>>Wholesale Trade</option>
                                    <option value="Retail Trade" <?php echo ($industry_sector == 'Retail Trade') ? 'selected' : ''; ?>>Retail Trade</option>
                                    <option value="Transportation" <?php echo ($industry_sector == 'Transportation') ? 'selected' : ''; ?>>Transportation & Logistics</option>
                                    <option value="Information Technology" <?php echo ($industry_sector == 'Information Technology') ? 'selected' : ''; ?>>Information Technology</option>
                                    <option value="Finance" <?php echo ($industry_sector == 'Finance') ? 'selected' : ''; ?>>Finance & Insurance</option>
                                    <option value="Real Estate" <?php echo ($industry_sector == 'Real Estate') ? 'selected' : ''; ?>>Real Estate</option>
                                    <option value="Professional Services" <?php echo ($industry_sector == 'Professional Services') ? 'selected' : ''; ?>>Professional & Business Services</option>
                                    <option value="Education" <?php echo ($industry_sector == 'Education') ? 'selected' : ''; ?>>Education Services</option>
                                    <option value="Healthcare" <?php echo ($industry_sector == 'Healthcare') ? 'selected' : ''; ?>>Healthcare & Social Assistance</option>
                                    <option value="Arts" <?php echo ($industry_sector == 'Arts') ? 'selected' : ''; ?>>Arts, Entertainment & Recreation</option>
                                    <option value="Hospitality" <?php echo ($industry_sector == 'Hospitality') ? 'selected' : ''; ?>>Accommodation & Food Services</option>
                                    <option value="Public Administration" <?php echo ($industry_sector == 'Public Administration') ? 'selected' : ''; ?>>Public Administration</option>
                                    <option value="Energy" <?php echo ($industry_sector == 'Energy') ? 'selected' : ''; ?>>Energy & Utilities</option>
                                    <option value="Telecommunications" <?php echo ($industry_sector == 'Telecommunications') ? 'selected' : ''; ?>>Telecommunications</option>
                                    <option value="Media" <?php echo ($industry_sector == 'Media') ? 'selected' : ''; ?>>Media & Communications</option>
                                    <option value="Other" <?php echo ($industry_sector == 'Other') ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            <div class="form-group col-6">
                                <label for="varchar">Country <?php echo form_error('Country') ?></label>
                                <select class="form-control select2" name="Country">
                                    <option value="">--select--</option>
                                    <?php

                                    foreach ($countryd as $item){
                                        ?>
                                        <option value="<?php echo $item->code; ?>"><?php echo $item->name?></option>

                                        <?php
                                    }
                                    ?>
                                </select>

                            </div>
                            <div class="form-group col-6">
                                <label for="int">Branch <?php echo form_error('Branch') ?></label>

                                <select  class="form-control" name="Branch" id="Branch">
                                    <option value="">--Select branch --</option>
                                    <?php

                                    foreach ($b as $item1){
                                        ?>
                                        <option value="<?php echo $item1->id; ?>" <?php if($item1->id==$Branch){echo "selected";} ?>><?php echo $item1->BranchName?></option>

                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group col-6">
                                <label for="int">Street <?php echo form_error('street') ?></label>
                                <input type="text" class="form-control" name="street" id="street" placeholder="Street" value="<?php echo $street; ?>" />
                            </div>
                            <div class="form-group col-6">
                                <label for="int">City/Town <?php echo form_error('city_town') ?></label>
                                <input type="text" class="form-control" name="city_town" id="city_town" placeholder="city_town" value="<?php echo $city_town; ?>" />
                            </div>
                            <!-- Updated Province dropdown -->
                            <div class="form-group col-6">
                                <label for="province">Province <?php echo form_error('province') ?></label>
                                <select class="form-control select2" name="province" id="province">
                                    <option value="">-- Select Province --</option>
                                    <?php foreach ($zambian_provinces as $code => $name): ?>
                                        <option value="<?php echo $code; ?>" <?php echo ($province == $code) ? 'selected' : ''; ?>><?php echo $name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-6">
                                <label for="int">Postal/Code <?php echo form_error('postal_code') ?></label>
                                <input type="text" class="form-control" name="postal_code" id="postal_code" placeholder="postal_code" value="<?php echo $postal_code; ?>" />
                            </div>
                            <div class="form-group col-6">
                                <label for="int">Contact Email <?php echo form_error('contact_email') ?></label>
                                <input type="text" class="form-control" name="contact_email" id="contact_email" placeholder="province" value="<?php echo $contact_email; ?>" />
                            </div>

                            <div class="form-group col-6">
                                <label for="int">phone_number * <?php echo form_error('phone_number') ?></label>
                                <input type="text" class="form-control" name="corporate_phone" id="corporate_phone" placeholder="Phone number" value="<?php echo $phone_number; ?>" required />
                            </div>

                            <div class="form-group col-6">
                                <label for="int">Website <?php echo form_error('website') ?></label>
                                <input type="text" class="form-control" name="website" id="website" placeholder="website" value="<?php echo $website; ?>" />
                            </div>
                            <!-- Add textarea for Key Management Info -->
                            <div class="form-group col-12">
                                <label for="key_management_info">Key Management Info <?php echo form_error('key_management_info') ?></label>
                                <textarea class="form-control" name="key_management_info" id="key_management_info" rows="3" placeholder="Enter key management information"><?php echo isset($key_management_info) ? $key_management_info : ''; ?></textarea>
                            </div>
                            <!-- Add textarea for Business Info -->
                            <div class="form-group col-12">
                                <label for="business_info">Business Info <?php echo form_error('business_info') ?></label>
                                <textarea class="form-control" name="business_info" id="business_info" rows="3" placeholder="Enter business information"><?php echo isset($business_info) ? $business_info : ''; ?></textarea>
                            </div>
                            <!-- Updated file upload sections - made optional -->
                            <div class="form-group col-4">
                                <br>
                                <label for="ifid">Upload Company Certificate <span class="text-muted">(Optional)</span></label>
                                <input type="file" name="company_certificate" style="display: block" placeholder="Attachment" class="form-control">
                            </div>
                            <div class="form-group col-4">
                                <br>
                                <label for="ifid">Upload Proof physical address <span class="text-muted">(Optional)</span></label>
                                <input type="file" name="proof_physical_address" style="display: block" placeholder="Attachment" class="form-control">
                            </div>
                            <div class="form-group col-4">
                                <br>
                                <label for="ifid">Upload Financial Statements (last 2 years) <span class="text-muted">(Optional)</span></label>
                                <input type="file" name="financial_statement" style="display: block" placeholder="Attachment" class="form-control">
                            </div>

                            <div class="form-group col-4">
                                <br>
                                <label for="ifid">Upload Tax Clearance Certificate <span class="text-muted">(Optional)</span></label>
                                <input type="file" name="tax_id_doc" style="display: block" placeholder="Attachment" class="form-control">
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-6">
                        <p>Shareholder Information</p>
                        <div id="shareholdersTable">
                            <div class="shareholder-container">
                                <div class="shareholder-title">Shareholder #1</div>
                                <table>

                                    <tr>
                                        <td>Title *</td>
                                        <td><select class="form-control" name="title[]" required>
                                                <option value="">--select--</option>
                                                <option value="Mr">Mr</option>
                                                <option value="Mrs">Mrs</option>
                                                <option value="Miss">Miss</option>
                                                <option value="Dr">Dr</option>
                                                <option value="Prof">Prof</option>
                                                <option value="Rev">Rev</option>
                                                <option value="Bishop">Bishop</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>First Name *</td>
                                        <td><input type="text" class="form-control" name="first_name[]" required></td>
                                    </tr>
                                    <tr>
                                        <td>Last Name *</td>
                                        <td><input type="text" class="form-control" name="last_name[]" required></td>
                                    </tr>
                                    <tr>
                                        <td>Gender *</td>
                                        <td>
                                            <select class="form-control" name="gender[]" >
                                                <option value="">--select--</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Nationality *</td>
                                        <td>
                                            <select class="form-control select2" type="text" name="nationality[]" required>
                                                <option value="">--select country--</option>
                                                <?php

                                                foreach ($countryd as $c){
                                                    ?>
                                                    <option value="<?php echo $c->code; ?>"><?php echo $c->name; ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td>Phone Number *</td>
                                        <td><input class="form-control" type="text" name="phone_number[]" required></td>
                                    </tr>
                                    <tr>
                                        <td>Email Address *</td>
                                        <td><input class="form-control" type="email" name="email_address[]" required></td>
                                    </tr>
                                    <tr>
                                        <td>Full Address </td>
                                        <td><textarea class="form-control" type="text" name="full_address[]" >Address here</textarea></td>
                                    </tr>
                                    <tr>
                                        <td>ID Type *</td>
                                        <td>
                                            <select class="form-control" name="idtype[]" required>
                                                <option value="NATIONAL_IDENTITY_CARD">National ID</option>
                                                <option value="PASSPORT">Passport</option>
                                                <option value="WORK_PERMIT">Work Permit</option>
                                                <option value="DRIVER_LICENSE">Driver's License</option>
                                                <option value="NONE">None</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Identity Number *</td>
                                        <td><input class="form-control" type="text" name="idnumber[]" placeholder="Enter ID number" required></td>
                                    </tr>
                                    <tr>
                                        <td>ID File <span class="text-muted">(Optional)</span></td>
                                        <td><input class="form-control" style="display: block !important;"  type="file" name="idfile[]"></td>
                                    </tr>
                                    <tr>
                                        <td>Ownership share value (%) *</td>
                                        <td><input class="form-control" style="display: block !important;"  type="number" name="percentage_value[]" required></td>
                                    </tr>

                                </table>
                            </div>
                        </div>
                        <button type="button" id="addRow">Add Shareholder</button>

                    </div>
                </div>
                <button type="submit" class="btn btn-danger">Submit</button>
            </form>

        </div>
    </div>
</div>
