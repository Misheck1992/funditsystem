<?php

$countryd = $this->Geo_countries_model->get_all();
?>

<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Shareholders</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin') ?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">Shareholders Form</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick #0268bc solid; border-radius: 14px;">

            <h2 style="margin-top:0px">Shareholder <?php echo $button ?></h2>
            <form action="<?php echo $action; ?>" method="post" class="form-row">

                <div class="form-group col-6">
                    <label for="title">Title <?php echo form_error('title') ?></label>
                    <select class="form-control" name="title" id="title">
                        <option value="">Select Title</option>
                        <option value="Mr" <?php echo ($title == 'Mr') ? 'selected' : ''; ?>>Mr</option>
                        <option value="Ms" <?php echo ($title == 'Ms') ? 'selected' : ''; ?>>Ms</option>
                        <option value="Mrs" <?php echo ($title == 'Mrs') ? 'selected' : ''; ?>>Mrs</option>
                        <option value="Dr" <?php echo ($title == 'Dr') ? 'selected' : ''; ?>>Dr</option>
                    </select>
                </div>

                <div class="form-group col-6">
                    <label for="first_name">First Name <?php echo form_error('first_name') ?></label>
                    <input type="text" class="form-control" name="first_name" id="first_name" placeholder="First Name" value="<?php echo $first_name; ?>" />
                </div>

                <div class="form-group col-6">
                    <label for="last_name">Last Name <?php echo form_error('last_name') ?></label>
                    <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Last Name" value="<?php echo $last_name; ?>" />
                </div>

                <div class="form-group col-6">
                    <label for="gender">Gender <?php echo form_error('gender') ?></label>
                    <select class="form-control" name="gender" id="gender">
                        <option value="">Select Gender</option>
                        <option value="Male" <?php echo ($gender == 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($gender == 'Female') ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>



                <div class="form-group col-6">
                    <label for="nationality">Nationality <?php echo form_error('nationality') ?></label>

                    <select class="form-control" name="nationality" id="nationality">
                        <option value="">--select--</option>
                        <?php

                        foreach ($countryd as $item){
                            ?>
                            <option value="<?php echo $item->code; ?>" <?php if($item->code==$nationality) ?>><?php echo $item->name?></option>

                            <?php
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group col-6">
                    <label for="phone_number">Phone Number <?php echo form_error('phone_number') ?></label>
                    <input type="text" class="form-control" name="phone_number" id="phone_number" placeholder="Phone Number" value="<?php echo $phone_number; ?>" />
                </div>

                <div class="form-group col-6">
                    <label for="email_address">Email Address <?php echo form_error('email_address') ?></label>
                    <input type="email" class="form-control" name="email_address" id="email_address" placeholder="Email Address" value="<?php echo $email_address; ?>" />
                </div>

                <div class="form-group col-12">
                    <label for="full_address">Full Address <?php echo form_error('full_address') ?></label>
                    <textarea class="form-control" name="full_address" id="full_address" placeholder="Full Address"><?php echo $full_address; ?></textarea>
                </div>

                <input type="hidden" name="id" value="<?php echo $id; ?>" />

                <div class="form-group col-6">
                    <button type="submit" class="btn btn-primary"><?php echo $button ?></button>
                </div>
                <div class="form-group col-6">
                    <a href="<?php echo site_url('shareholders') ?>" class="btn btn-default">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
