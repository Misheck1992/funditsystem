<?php

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

            <h2 style="margin-top:0px">Corporate_customers <?php ?></h2>
            <form action="<?php   ?>" method="post" class="form-row">
                <div class="form-group col-6">
                    <label for="varchar">Entity Name <?php echo form_error('EntityName') ?></label>
                    <input type="text" class="form-control" name="EntityName" id="EntityName" placeholder="EntityName" value="<?php echo $EntityName; ?>" />
                </div>
                <div class="form-group col-6">
                    <label for="entity_type">Entity Type <?php echo form_error('entity_type') ?></label>
                    <select class="form-control" name="entity_type" id="entity_type">
                        <option value="">Select Entity Type</option>
                        <option value="Sole Proprietorship" >Sole Proprietorship</option>
                        <option value="Partnership" >Partnership</option>
                        <option value="Limited Company">Limited Company</option>
                    </select>
                </div>
                <div class="form-group col-6">
                    <label for="entity_type">category <?php echo form_error('category') ?></label>
                    <select class="form-control" name="category" id="category">
                        <option value="">Select category</option>
                        <option value="client" >Client</option>
                        <option value="off_taker" >Off taker </option>

                    </select>
                </div>
                <div class="form-group col-6">
                    <label for="date">Date Of Incorporation <?php echo form_error('DateOfIncorporation') ?></label>
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
                <div class="form-group col-6">
                    <label for="varchar">Nature of business <?php echo form_error('nature_of_business') ?></label>
                    <input type="text" class="form-control" name="nature_of_business" id="nature_of_business" placeholder="nature_of_business" value="<?php echo $nature_of_business; ?>" />
                </div>

                <div class="form-group col-6">
                    <label for="varchar">Industry sector <?php echo form_error('industry_sector') ?></label>
                    <input type="text" class="form-control" name="industry_sector" id="industry_sector" placeholder="industry_sector" value="<?php echo $industry_sector; ?>" />
                </div>
                <div class="form-group col-6">
                    <label for="varchar">Country <?php echo form_error('Country') ?></label>
                    <select class="form-control" name="Country">
                        <option value="">--select--</option>
                        <?php

                        foreach ($countryd as $item){
                            ?>
                            <option value="<?php echo $item->code; ?>" <?php if($item->code==$Country) ?>><?php echo $item->name?></option>

                            <?php
                        }
                        ?>
                    </select>

                </div>
                <div class="form-group col-12">
                    <label for="int">Branch <?php echo form_error('Branch') ?></label>
                    <input type="text" class="form-control" name="Branch" id="Branch" placeholder="Branch" value="<?php echo $Branch; ?>" />


                </div>
                <div class="form-group col-12">
                    <label for="int">Street <?php echo form_error('street') ?></label>
                    <input type="text" class="form-control" name="street" id="street" placeholder="contact_email" value="<?php echo $street; ?>" />
                </div>
                <div class="form-group col-12">
                    <label for="int">City/Town <?php echo form_error('city_town') ?></label>
                    <input type="text" class="form-control" name="city_town" id="city_town" placeholder="city_town" value="<?php echo $city_town; ?>" />
                </div>
                <div class="form-group col-12">
                    <label for="int">Province <?php echo form_error('province') ?></label>
                    <input type="text" class="form-control" name="province" id="province" placeholder="province" value="<?php echo $city_town; ?>" />
                </div>
                <div class="form-group col-12">
                    <label for="int">Postal/Code <?php echo form_error('postal_code') ?></label>
                    <input type="text" class="form-control" name="postal_code" id="postal_code" placeholder="postal_code" value="<?php echo $postal_code; ?>" />
                </div>
                <div class="form-group col-12">
                    <label for="int">Contact Email <?php echo form_error('contact_email') ?></label>
                    <input type="text" class="form-control" name="contact_email" id="contact_email" placeholder="province" value="<?php echo $province; ?>" />
                </div>


                <div class="form-group col-12">
                    <label for="int">phone_number <?php echo form_error('phone_number') ?></label>
                    <input type="text" class="form-control" name="phone_number" id="phone_number" placeholder="Branch" value="<?php echo $phone_number; ?>" />


                </div>
                <div class="form-group col-12">
                    <label for="int">contact_email <?php echo form_error('contact_email') ?></label>
                    <input type="text" class="form-control" name="contact_email" id="contact_email" placeholder="contact_email" value="<?php echo $contact_email; ?>" />
                </div>
                <div class="form-group col-12">
                    <label for="int">Website <?php echo form_error('website') ?></label>
                    <input type="text" class="form-control" name="website" id="website" placeholder="website" value="<?php echo $website; ?>" />


                </div>

                <div class="form-group col-4">
                    <br>
                    <label for="company_certificate_input" id="company_certificate_label" class="custom-file-upload">
                        Company Certificate <i class="fa fa-upload fa-flip"></i>
                    </label>
                    <input type="file"
                           id="company_certificate_input"
                           onchange="uploadprofileCompany('company_certificate')"
                           accept="application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoint, text/plain, application/pdf" />
                    <input type="text"
                           id="company_certificate_hidden"
                           name="company_certificate"
                           hidden
                    >
                </div>
                <div class="form-group col-4">
                    <br>
                    <label for="proof_physical_address_input" id="proof_physical_address_label" class="custom-file-upload">
                        Proof of Physical Address (e.g., utility bill, rental agreement)<i class="fa fa-upload fa-flip"></i>
                    </label>
                    <input type="file"
                           id="proof_physical_address_input"
                           onchange="uploadprofileCompany('proof_physical_address')"
                           accept="application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoint, text/plain, application/pdf" />
                    <input type="text"
                           id="proof_physical_address_hidden"
                           name="proof_physical_address"
                           hidden
                    >
                </div>
                <div class="form-group col-4">
                    <br>
                    <label for="financial_statement_input" id="financial_statement_label" class="custom-file-upload">
                        Financial Statements (last 2 years) <i class="fa fa-upload fa-flip"></i>
                    </label>
                    <input type="file"
                           id="financial_statement_input"
                           onchange="uploadprofileCompany('financial_statement')"
                           accept="application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoint, text/plain, application/pdf" />
                    <input type="text"
                           id="financial_statement_hidden"
                           name="financial_statement"
                           hidden
                    >
                </div>

                <div class="form-group col-4">
                    <br>
                    <label for="tax_id_doc_input" id="tax_id_doc_label" class="custom-file-upload">
                        Tax Clearance Certificate <i class="fa fa-upload fa-flip"></i>
                    </label>
                    <input type="file"
                           id="tax_id_doc_input"
                           onchange="uploadprofileCompany('tax_id_doc')"
                           accept="application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoint, text/plain, application/pdf" />
                    <input type="text"
                           id="tax_id_doc_hidden"
                           name="tax_id_doc"
                           hidden
                    >
                </div>




                <br>

                <input type="hidden" name="id" value="<?php echo $id; ?>" />

                <div class="form-group col-6">
                    <a href="<?php echo site_url('corporate_customers') ?>" class="btn btn-default">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
