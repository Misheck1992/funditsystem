<?php

$b = $this->Branches_model->get_all();
$countryd = $this->Geo_countries_model->get_all();
?>

<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Group customers</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">All group customers form</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick #24C16B solid;border-radius: 14px;">
            <h2 style="margin-top:0px">Group customer <?php echo $button ?></h2>
            <form action="<?php echo base_url('individual_customers/create_action_group')?>" method="post" >
                <hr>
                <h5>DEMOGRAPHIC DATA</h5>

                <div class="row">
                    <div class="col-lg-8 border-right">
                        <div class="row">
                            <div class="form-group col-3">
                                <label for="varchar">Title <?php echo form_error('Title') ?></label>
                                <select class="form-control" name="Title" id="Title" required>
                                    <option value="">Select</option>
                                    <option value="Mr" <?php if($Title=="Mr"){echo "selected";} ?>>Mr</option>
                                    <option value="Mrs" <?php if($Title=="Mrs"){echo "selected";} ?>>Mrs</option>
                                    <option value="Miss" <?php if($Title=="Miss"){echo "selected";} ?>>Miss</option>
                                    <option value="Dr" <?php if($Title=="Dr"){echo "selected";} ?>>Dr</option>
                                    <option value="Rev" <?php if($Title=="Rev"){echo "selected";} ?>>Rev</option>
                                </select>

                            </div>
                            <div class="form-group col-3">
                                <label for="varchar">First name <?php echo form_error('Firstname') ?></label>
                                <input type="text" class="form-control" name="Firstname" id="Firstname" placeholder="Firstname" value="<?php echo $Firstname; ?>" required />
                            </div>
                            <div class="form-group col-3">
                                <label for="varchar">Middlename <?php echo form_error('Middlename') ?></label>
                                <input type="text" class="form-control" name="Middlename" id="Middlename" placeholder="Middlename" value="<?php echo $Middlename; ?>" />
                            </div>
                            <div class="form-group col-3">
                                <label for="varchar">Lastname <?php echo form_error('Lastname') ?></label>
                                <input type="text" class="form-control" name="Lastname" id="Lastname" placeholder="Lastname" value="<?php echo $Lastname; ?>" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-4">
                                <label for="enum">Gender <?php echo form_error('Gender') ?></label>
                                <select class="form-control" name="Gender" id="Gender" required>
                                    <option value="">Select</option>
                                    <option value="MALE" <?php if($Gender=="MALE"){echo "selected";} ?>>MALE</option>
                                    <option value="FEMALE" <?php if($Gender=="FEMALE"){echo "selected";} ?>>FEMALE</option>
                                    <option value="OTHER" <?php if($Gender=="OTHER"){echo "selected";} ?>>OTHER</option>
                                </select>

                            </div>
                            <div class="form-group col-4">
                                <label for="enum">Marital status <?php echo form_error('marital') ?></label>
                                <select class="form-control" name="marital" id="marital" required>
                                    <option value="">Select</option>
                                    <option value="Single" <?php if($Gender=="Single"){echo "selected";} ?>>Single</option>
                                    <option value="Married" <?php if($Gender=="Married"){echo "selected";} ?>>Married</option>
                                    <option value="Separated" <?php if($Gender=="Separated"){echo "selected";} ?>>Separated</option>
                                    <option value="Divorced" <?php if($Gender=="Divorced"){echo "selected";} ?>>Divorced</option>
                                    <option value="Widowed" <?php if($Gender=="Widowed"){echo "selected";} ?>>Widowed</option>
                                </select>

                            </div>
                            <div class="form-group col-4">
                                <label for="date">Date Of Birth <?php echo form_error('DateOfBirth') ?></label>
                                <input  required type="date" class="form-control" name="DateOfBirth" id="DateOfBirth" placeholder="DateOfBirth" value="<?php echo $DateOfBirth; ?>" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-4">
                                <label for="varchar">EmailAddress <?php echo form_error('EmailAddress') ?></label>
                                <input type="email" class="form-control" name="EmailAddress" id="EmailAddress" placeholder="EmailAddress" value="<?php echo $EmailAddress; ?>" />
                            </div>
                            <div class="form-group col-4">
                                <label for="varchar">PhoneNumber <?php echo form_error('PhoneNumber') ?></label>
                                <input type="text" class="form-control" name="PhoneNumber" id="PhoneNumber" placeholder="PhoneNumber" value="<?php echo $PhoneNumber; ?>" />
                            </div>
                            <div class="form-group col-4">
                                <label for="enum">Phone number on WhatsApp <?php echo form_error('noonwhatsap') ?></label>
                                <select class="form-control" name="noonwhatsap" id="noonwhatsap" >
                                    <option value="">Select</option>
                                    <option value="No" <?php if($Gender=="No"){echo "selected";} ?>>No</option>
                                    <option value="Yes" <?php if($Gender=="Yes"){echo "selected";} ?>>Yes</option>

                                </select>

                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="varchar">Guarantee full name <?php echo form_error('kinFullname') ?></label>
                                <input type="text" class="form-control" name="kinFullname" id="kinFullname" placeholder="Full name" value="<?php echo $kinFullname; ?>" />
                            </div>
                            <div class="form-group col-6">
                                <label for="varchar">Guarantee PhoneNumber <?php echo form_error('kinPhonenumber') ?></label>
                                <input type="text" class="form-control" name="kinPhonenumber" id="kinPhoneNumber" placeholder="PhoneNumber" value="<?php echo $kinPhonenumber; ?>" />
                            </div>

                        </div>
                        <div class="row">
                            <div class="form-group col-4">
                                <label for="varchar">Postal Address <?php echo form_error('AddressLine1') ?></label>
                                <textarea class="form-control" name="AddressLine1" id="AddressLine1" placeholder="Address" cols="30" rows="10"><?php echo $AddressLine1; ?></textarea>

                            </div>
                            <div class="form-group col-4">
                                <label for="varchar">Physical Address <?php echo form_error('AddressLine2') ?></label>
                                <textarea type="text" class="form-control" name="AddressLine2" id="AddressLine2" placeholder="Physical Address "cols="30" rows="10"><?php echo $AddressLine2; ?></textarea>

                            </div>
                            <div class="form-group col-4">
                                <label for="varchar">Guarantee Address <?php echo form_error('AddressLine3') ?></label>
                                <textarea type="text" class="form-control" name="AddressLine3" id="AddressLine3" placeholder="Next of Keen Address "cols="30" rows="10"><?php echo $AddressLine3; ?></textarea>

                            </div>
                        </div>
                        <div class="row">


                            <div class="form-group col-4">
                                <?php
                                $dist = get_all('districts');
                                ?>
                                <label for=" ">District <?php echo form_error('City') ?></label>
                                <select name="City" id="City" placeholder="District"  class="form-control" onchange="get_ta()" required>
                                    <option value="">--select district--</option>
                                    <?php

                                    foreach ($dist as $d){
                                        ?>
                                        <option value="<?php echo $d->district_id?>"><?php echo $d->district_name?></option>
                                        <?php
                                    }
                                    ?>
                                </select>


                            </div>
                            <?php
                            $tas = get_all('ta');
                            ?>
                            <div class="form-group col-4">
                                <label for="varchar">TA<?php echo form_error('Province') ?></label>
                                <select name="Province" id="Province" class="form-control" required>
                                    <option value="">--select TA--</option>
                                    <?php

                                    foreach ($tas as $ta){
                                        ?>
                                        <option value="<?php echo $ta->ta_id?>"><?php echo $ta->ta_name?></option>
                                        <?php
                                    }
                                    ?>

                                </select>

                            </div>
                            <div class="form-group col-4">
                                <label for="varchar">Village <?php echo form_error('village') ?></label>
                                <input type="text" class="form-control" name="village" id="village" placeholder="village" value="<?php echo $village; ?>" />
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-12">
                                <h2>Bank details</h2>
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-3">
                                <label for="varchar">Account Name  <?php echo form_error('account_name') ?></label>
                                <input  type="text" class="form-control" name="account_name" id="Profession" placeholder="account_name" value="<?php echo $account_name; ?>" />
                            </div>
                            <div class="form-group col-3">
                                <label for="varchar">Account Number <?php echo form_error('account_number') ?></label>
                                <input type="text" class="form-control" name="account_number" id="account_number" placeholder="Account number" value="<?php echo $account_number; ?>" />
                            </div>
                            <div class="form-group col-3">
                                <label for="decimal">Bank Name <?php echo form_error('bank_name') ?></label>
                                <input type="text" class="form-control" name="bank_name" id="bank_name" placeholder="bank_name" value="<?php echo $bank_name; ?>" />
                            </div>


                            <div class="form-group col-3">
                                <?php
                                $dist = get_all('districts');
                                ?>
                                <label for=" ">Bank Branch <?php echo form_error('bank_branch') ?></label>
                                <select name="bank_branch" id="branch" placeholder="Bank branch"  class="form-control" >
                                    <option value="">--select district--</option>
                                    <?php

                                    foreach ($dist as $d){
                                        ?>
                                        <option value="<?php echo $d->district_id?>"><?php echo $d->district_name?></option>
                                        <?php
                                    }
                                    ?>
                                </select>


                            </div>




                        </div>

                        <div class="row">
                            <div class="col-12">
                                <h2>ECONOMIC STATUS /TRAININGS/LOANS/MARKETS</h2>
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-3">
                                <label for="varchar">Profession <?php echo form_error('Profession') ?></label>
                                <input required type="text" class="form-control" name="Profession" id="Profession" placeholder="Profession" value="<?php echo $Profession; ?>" />
                            </div>
                            <div class="form-group col-3">
                                <label for="varchar">Source Of Income <?php echo form_error('SourceOfIncome') ?></label>
                                <input required type="text" class="form-control" name="SourceOfIncome" id="SourceOfIncome" placeholder="SourceOfIncome" value="<?php echo $SourceOfIncome; ?>" />
                            </div>
                            <div class="form-group col-3">
                                <label for="decimal">Gross Monthly Income <?php echo form_error('GrossMonthlyIncome') ?></label>
                                <input required type="text" class="form-control" name="GrossMonthlyIncome" id="GrossMonthlyIncome" placeholder="GrossMonthlyIncome" value="<?php echo $GrossMonthlyIncome; ?>" />
                            </div>
                            <div class="form-group col-3">
                                <label for="enum">Residential Status <?php echo form_error('ResidentialStatus') ?></label>
                                <select required name="ResidentialStatus" id="ResidentialStatus" class="form-control">
                                    <option value="">--select--</option>
                                    <option value="Owned" <?php  if($ResidentialStatus=='Owned'){ echo "selected"; } ?>>Owned</option>
                                    <option value="Rented" <?php  if($ResidentialStatus=='Rented'){ echo "selected"; } ?>>Rented</option>
                                </select>
                            </div>



                        </div>


                        <div class="row">

                            <div class="form-group col-12">
                                <label for="int">Branch <?php echo form_error('Branch') ?></label>
                                <select class="form-control select2" name="Branch" id="Branch" required>
                                    <option value="">select</option>
                                    <?php
                                    foreach ($b as $br){
                                        ?>
                                        <option value="<?php echo $br->Code ?>" <?php if($br->Code==$Branch){echo "selected";}  ?>><?php echo $br->BranchName?></option>
                                        <?php
                                    }
                                    ?>
                                </select>

                            </div>
                        </div>

                    </div>

                    <div class="col-lg-4">

                        <h5>KYC Information</h5>
                        <hr>
                        <div class="row">
                            <div class="form-group col-6">
                                <input type="text" hidden name="ClientId" id="Client">
                                <label for="enum">IDType </label>
                                <select class="form-control select2" name="IDType" id="IDType"  >
                                    <option value="">--select--</option>
                                    <option value="NATIONAL_IDENTITY_CARD">NATIONAL IDENTITY CARD</option>
                                    <option value="DRIVING_LISENCE">DRIVING LICENCE</option>
                                    <option value="PASSPORT">PASSPORT</option>
                                    <option value="WORK_PERMIT">WORK PERMIT</option>
                                    <option value="VOTER_REGISTRATION">VOTER REGISTRATION</option>
                                    <option value="PUBLIC_STATE_OFFICIAL_LETTER">PUBLIC STATE OFFICIAL LETTER</option>

                                </select>
                            </div>
                            <div class="form-group col-6">
                                <label for="varchar">IDNumber </label>
                                <input type="text" class="form-control" name="IDNumber" id="IDNumber" placeholder="IDNumber"   />
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="date">IssueDate </label>
                                <input type="date" class="form-control" name="IssueDate" id="IssueDate" placeholder="IssueDate"  />
                            </div>
                            <div class="form-group col-6">
                                <label for="date">ExpiryDate * </label>
                                <input type="date" class="form-control" name="ExpiryDate" id="ExpiryDate" placeholder="ExpiryDate"   />
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="id_front" class="custom-file-upload"> Upload front photo of ID </label>
                                <input type="file"  onchange="uploadcommon('id_front')"   id="id_front"  />
                                <input type="text" id="id_front1"  name="id_front" hidden >
                                <div style="    height: 20px;
    background-color: #ececec;
    border-radius: 50px;
    margin-bottom: 20px;
    min-width: 50px;">
                                    <div class="bar"></div >
                                    <div class="percent" id="id_front3">0%</div >
                                </div>
                                <div id="id_front2">
                                    <img src="<?php echo base_url('uploads/holder.PNG')?>" alt="" height="100" width="100">
                                </div>
                            </div>

                            <div class="form-group col-6">
                                <label for="Id_back" class="custom-file-upload"> Upload Back photo of ID * </label>
                                <input type="file" class="upload-btn-wrapper"  onchange="uploadcommon('Id_back')"  id="Id_back" placeholder="Id Back"  />
                                <input type="text" id="Id_back1" name="Id_back" hidden >
                                <div style="    height: 20px;
    background-color: #ececec;
    border-radius: 50px;
    margin-bottom: 20px;
    min-width: 50px;">
                                    <div class="bar"></div >
                                    <div class="percent" id="Id_back3">0%</div >
                                </div>
                                <div id="Id_back2">
                                    <img src="<?php echo base_url('uploads/holder.PNG')?>" alt="" height="100" width="100">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="photograph"  class="custom-file-upload">Upload Photograph </label>
                                <input type="file"  onchange="uploadcommon('photograph')"    id="photograph" placeholder="Photograph"  />
                                <input type="text" id="photograph1" name="photograph" hidden >
                                <div style="    height: 20px;
    background-color: #ececec;
    border-radius: 50px;
    margin-bottom: 20px;
    min-width: 50px;">
                                    <div class="bar"></div >
                                    <div class="percent" id="photograph3">0%</div >
                                </div>
                                <div id="photograph2">
                                    <img src="<?php echo base_url('uploads/holder.PNG')?>" alt="" height="100" width="100">
                                </div>
                            </div>


                            <div class="form-group col-6">
                                <label for="signature" class="custom-file-upload"> Upload Signature </label>
                                <input type="file" onchange="uploadcommon('signature')"    id="signature" placeholder="Signature" />
                                <input type="text" id="signature1" name="signature" hidden >
                                <div style="    height: 20px;
    background-color: #ececec;
    border-radius: 50px;
    margin-bottom: 20px;
    min-width: 50px;">
                                    <div class="bar"></div>
                                    <div class="percent" id="signature3">0%</div >
                                </div>
                                <div id="signature2">
                                    <img src="<?php echo base_url('uploads/holder.PNG')?>" alt="" height="100" width="100">
                                </div>
                            </div>


                        </div>

                    </div>



                    <div class="row">
                        <div class="col-lg-12">
                            <input type="hidden" name="id" value="<?php echo $id; ?>" />
                            <button type="submit" class="btn btn-primary"><?php echo $button ?></button>
                        </div>

                    </div>




            </form>
        </div>
    </div>
</div>
