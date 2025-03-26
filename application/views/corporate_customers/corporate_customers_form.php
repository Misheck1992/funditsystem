<?php
$shareholders = get_all('shareholders');
$b = $this->Branches_model->get_all();
$countryd = $this->Geo_countries_model->get_all();
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
			<div class="form-group col-6">
				<label for="int">Branch <?php echo form_error('Branch') ?></label>
                <input type="text" class="form-control" name="Branch" id="Branch" placeholder="Branch" value="<?php echo $Branch; ?>" />


            </div>
            <div class="form-group col-6">
                <label for="int">Street <?php echo form_error('street') ?></label>
                <input type="text" class="form-control" name="street" id="street" placeholder="Street" value="<?php echo $street; ?>" />
            </div>
            <div class="form-group col-6">
                <label for="int">City/Town <?php echo form_error('city_town') ?></label>
                <input type="text" class="form-control" name="city_town" id="city_town" placeholder="city_town" value="<?php echo $city_town; ?>" />
            </div>
            <div class="form-group col-6">
                <label for="int">Province <?php echo form_error('province') ?></label>
                <input type="text" class="form-control" name="province" id="province" placeholder="province" value="<?php echo $province; ?>" />
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
                <label for="int">phone_number <?php echo form_error('phone_number') ?></label>
                <input type="text" class="form-control" name="corporate_phone" id="corporate_phone" placeholder="Phone number" value="<?php echo $phone_number; ?>" />


            </div>

            <div class="form-group col-6">
                <label for="int">Website <?php echo form_error('website') ?></label>
                <input type="text" class="form-control" name="website" id="website" placeholder="website" value="<?php echo $website; ?>" />


            </div>

            <div class="form-group col-4">
                <br>



               <label for="ifid"  > Upload Company Certificate </label><input type="file" name="company_certificate" style="display: block" placeholder="Attachment" class="form-control">

            </div>
            <div class="form-group col-4">
                <br>

                <label for="ifid"  > Upload Proof physical address  </label><input type="file" name="proof_physical_address" style="display: block" placeholder="Attachment" class="form-control">

            </div>
            <div class="form-group col-4">
                <br>

                <label for="ifid"  > Upload
                    Financial Statements (last 2 years) </label><input type="file" name="financial_statement" style="display: block" placeholder="Attachment" class="form-control">

            </div>

            <div class="form-group col-4">
                <br>

                <label for="ifid" > Upload Tax Clearance Certificate </label><input type="file" name="tax_id_doc" style="display: block" placeholder="Attachment" class="form-control">

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
                        <td>Title</td>
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
                        <td>First Name</td>
                        <td><input type="text" class="form-control" name="first_name[]" required></td>
                    </tr>
                    <tr>
                        <td>Last Name</td>
                        <td><input type="text" class="form-control" name="last_name[]" required></td>
                    </tr>
                    <tr>
                        <td>Gender</td>
                        <td>
                            <select class="form-control" name="gender[]">
                                <option value="">--select--</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <td>Nationality</td>
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
                        <td>Phone Number</td>
                        <td><input class="form-control" type="text" name="phone_number[]" required></td>
                    </tr>
                    <tr>
                        <td>Email Address</td>
                        <td><input class="form-control" type="email" name="email_address[]" required></td>
                    </tr>
                    <tr>
                        <td>Full Address</td>
                        <td><textarea class="form-control" type="text" name="full_address[]" required>Address here</textarea></td>
                    </tr>
                    <tr>
                        <td>ID Type</td>
                        <td>
                            <select class="form-control" name="idtype[]">
                                <option value="NATIONAL_IDENTITY_CARD">National ID</option>
                                <option value="PASSPORT">Passport</option>
                                <option value="WORK_PERMIT">Work Permit</option>
                                <option value="DRIVER_LICENSE">Driver's License</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>ID File</td>
                        <td><input class="form-control" style="display: block !important;"  type="file" name="idfile[]" required></td>
                    </tr>
                    <tr>
                        <td>Ownership share value (%)</td>
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
