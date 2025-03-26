<?php
$b = $this->Branches_model->get_all();
$group_cat = $this->Group_categories_model->get_all();

$get_c = get_all('individual_customers');
?>
<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Groups</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">groups form</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick green solid;border-radius: 14px;">
            <form action="<?php echo $action; ?>" method="post"  enctype="multipart/form-data" >

                <div class="row">
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="form-group col-3">
                                <label for="varchar">Group Name <?php echo form_error('group_name') ?></label>
                                <input type="text" class="form-control" name="group_name" id="group_name" placeholder="Group Name" value="<?php echo $group_name; ?>" />
                            </div>


                            <div class="form-group col-3">
                                <label for="int">Village/Market * <?php echo form_error('group_village') ?></label>

                                <input type="text" class="form-control" name="group_village" id="group_name" placeholder="Group Village" value="<?php echo $group_village; ?>" required//>

                            </div>
                            <div class="form-group col-4">
                                <?php
                                $dist = get_all('districts');
                                ?>
                                <label for=" ">District <?php echo form_error('group_district') ?></label>
                                <select name="group_district" id="City" placeholder="group_district"  class="form-control" onchange="get_ta()" required>
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
                                <label for="varchar">TA<?php echo form_error('group_ta') ?></label>
                                <select name="group_ta" id="Province" class="form-control" required>
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




                            <div class="form-group col-6">
                                <label for="varchar">Address <?php echo form_error('group_address') ?></label>

                                <textarea class="form-control" rows="3" name="group_address" id="group_address" placeholder="Group Description"><?php echo $group_address; ?></textarea>
                            </div>





                            <div class="form-group col-6">
                                <label for="int">contacts * <?php echo form_error('group_contact') ?></label>

                                <input type="text" class="form-control" name="group_contact" id="group_contact" placeholder="Separate with comma" value="<?php echo $group_contact; ?>" required/>
                            </div>
                            <div class="form-group col-6">
                                <label for="int">Email  <?php echo form_error('group_email') ?></label>

                                <input type="text" class="form-control" name="group_email" id="group_email" placeholder="email address" value="<?php echo $group_email; ?>" />
                            </div>



                            <div class="form-group col-4">
                                <label for="int">Branch * <?php echo form_error('branch') ?></label>

                                <select class="form-control" name="branch" id="Branch">
                                    <option value="">select</option>
                                    <?php
                                    foreach ($b as $br){
                                        ?>
                                        <option value="<?php echo $br->id ?>" <?php if($br->Code==$branch){echo "selected";}  ?>><?php echo $br->BranchName?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div
                            <div class="form-group col-4">
                                <br>

                                <label for="id_front" id="ppp" class="custom-file-upload">Upload Group agreements file <i class="fa fa-upload fa-flip"></i></label>
                                <input type="file"  onchange="uploadprofile('id_front')"   id="id_front" accept=
                                "application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoint,text/plain, application/pdf" />
                                <input type="text" id="id_front1"  name="file" hidden  >

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
                                    <input  type="text" class="form-control" name="account_number" id="account_number" placeholder="Account number" value="<?php echo $account_number; ?>" />
                                </div>
                                <div class="form-group col-3">
                                    <label for="decimal">Bank Name <?php echo form_error('bank_name') ?></label>
                                    <input  type="text" class="form-control" name="bank_name" id="bank_name" placeholder="bank_name" value="<?php echo $bank_name; ?>" />
                                </div>


                                <div class="form-group col-3">
                                    <?php
                                    $dist = get_all('districts');
                                    ?>
                                    <label for=" ">Bank Branch <?php echo form_error('bank_branch') ?></label>
                                    <select name="bank_branch" id="branch" placeholder="Bank branch"  class="form-control"  >
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

                            <div class="form-group col-12">
                                <label for="group_description">Group Description <?php echo form_error('group_description') ?></label>
                                <textarea class="form-control" rows="3" name="group_description" id="group_description" placeholder="Group Description"><?php echo $group_description; ?></textarea>

                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="row">
                                <div class="form-group col-12">
                                    <label for="varchar">Select/search group members (You can select multiple members)</label>
                                    <select name="customer[]" id="" class="form-control select2"  multiple>
                                        <option value="">--select customer--</option>
                                        <?php

                                        foreach ($get_c as $item){
                                            ?>
                                            <option value="<?php echo $item->id ?>"> <?php echo $item->Firstname.' '.$item->Lastname ?></option>
                                            <?php

                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group col-12">
                                    <div class="form-group">
                                        <label for="excel_file">Members Excel File:</label>
                                        <input type="file" style="display: block"  name="excelfile"  placeholder="Import member excel file" >
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <input type="hidden" name="group_id" value="<?php echo $group_id; ?>" />
                            <button type="submit" class="btn btn-primary"><?php echo $button ?></button>

                        </div>
                    </div>

            </form>
        </div></div>
</div>
