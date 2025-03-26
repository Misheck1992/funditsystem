<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">All Loan products</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">All loan products </span>
            </nav>
        </div><div class="main-content">
            <div class="page-header">
                <h2 class="header-title">All Loan products</h2>
                <div class="header-sub-title">
                    <nav class="breadcrumb breadcrumb-dash">
                        <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                        <a class="breadcrumb-item" href="#">-</a>
                        <span class="breadcrumb-item active">All loan products </span>
                    </nav>
                </div>
            </div>
            <div class="card">
                <div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
                    <p><?php echo   $interest; ?></p>
                    <form action="<?php echo $action; ?>" method="post">
                        <div class="row">
                            <div class="form-group col-3">
                                <label for="varchar">Product Name <?php echo form_error('product_name') ?></label>
                                <input type="text" class="form-control" name="product_name" id="product_name" placeholder="Product Name" value="<?php echo $product_name; ?>" required />
                            </div>
                            <div class="form-group col-3">
                                <label for="varchar">Product Name Abbreviation<?php echo form_error('abbreviation') ?></label>
                                <input type="text" class="form-control" name="abbreviation" id="abbreviation" placeholder="Product Name abbreviation" value="<?php echo $abbreviation; ?>" required />
                            </div>
                            <div class="form-group col-3">
                                <label for="number">Interest <?php echo form_error('interest') ?></label>
                                <input type="text" class="form-control" name="interest" id="interest" placeholder="Interest" value="<?php echo $interest; ?>" required />
                            </div>
                            <div class="form-group col-3">
                                <label for="enum">Frequency <?php echo form_error('frequency') ?></label>

                                <select name="frequency" id="" class="form-control" required>
                                    <option value="">--select--</option>
                                    <option value="monthly" <?php if($frequency=='monthly'){ echo "selected"; } ?>>Monthly</option>
                                    <option value="Weekly" <?php if($frequency=='Weekly'){ echo "selected"; } ?> >Weekly</option>
                                    <option value="2 Weeks" <?php if($frequency=='2 Weeks'){ echo "selected"; } ?>>2 weeks</option>
                                </select>


                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-6">
                                <label for="enum">Calculation Type <?php echo form_error('calculation_type') ?></label>

                                <select name="calculation_type" id="" class="form-control" required>
                                    <option value="">--select--</option>
                                    <option value="Straight Line" <?php if($frequency=='Straight Line'){ echo "selected"; } ?>>Straight Line</option>
                                    <option value="Reducing Balance" <?php if($frequency=='Reducing Balance'){ echo "selected"; } ?> >Reducing Balance</option>

                                </select>


                            </div>
                            <div class="form-group col-6">
                                <label for="decimal">Penalty Threshold <?php echo form_error('penalty_threshold') ?></label>
                                <input type="text" class="form-control" name="penalty_threshold" id="penalty_threshold" placeholder="Penalty Threshold" value="0" required />
                            </div>

                        </div>
                        <div class="row">
                            <div class="form-group col-4">
                                <label for="enum">Penalty Charge Type Above threshold <?php echo form_error('penalty_charge_type') ?></label>
                                <select name="penalty_charge_type_above" id="penalty_charge_type_above" class="form-control" onchange="penalty_toggler_above()" >
                                    <option value="">-Select-</option>
                                    <option value="Fixed">Fixed</option>
                                    <option value="Variable">Variable</option>

                                </select>

                            </div>
                            <div class="form-group col-4">
                                <label for="decimal">Penalty Fixed Charge Above threshold<?php echo form_error('penalty_fixed_charge_above') ?></label>
                                <input type="number" class="form-control" name="penalty_fixed_charge_above" id="penalty_fixed_charge_above" placeholder="Penalty Fixed Charge Above" value="<?php echo 0; ?>" />
                            </div>
                            <div class="form-group col-4">
                                <label for="number">Penalty Variable Charge Above threshold<?php echo form_error('penalty_variable_charge_above') ?></label>
                                <input type="number" class="form-control" name="penalty_variable_charge_above" id="penalty_variable_charge_above" placeholder="Penalty Variable Charge Above" value="<?php echo 0; ?>" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-4">
                                <label for="enum">Penalty Charge Type below threshold <?php echo form_error('penalty_charge_type') ?></label>
                                <select name="penalty_charge_type_below" id="penalty_charge_type_below" class="form-control" onchange="penalty_toggler_below()" >
                                    <option value="">-Select-</option>
                                    <option value="Fixed">Fixed</option>
                                    <option value="Variable">Variable</option>

                                </select>

                            </div>
                            <div class="form-group col-4">
                                <label for="decimal">Penalty Fixed Charge below  threshold<?php echo form_error('penalty_fixed_charge_below') ?></label>
                                <input type="number" class="form-control" name="penalty_fixed_charge_below" id="penalty_fixed_charge_below" placeholder="Penalty Fixed Charge Below" value="<?php echo 0; ?>" />
                            </div>
                            <div class="form-group col-4">
                                <label for="number">Penalty Variable Charge below threshold <?php echo form_error('penalty_variable_charge_below') ?></label>
                                <input type="number" class="form-control" name="penalty_variable_charge_below" id="penalty_variable_charge_below" placeholder="Penalty Variable Charge below" value="<?php echo 0; ?>" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <h4>Loan processing configurations</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-12">
                                <label for="decimal">Loan Processing Fee Threshold <?php echo form_error('loan_processing_fee_threshold') ?></label>
                                <input type="decimal" class="form-control" name="loan_processing_fee_threshold" id="loan_processing_fee_threshold" placeholder="Loan Processing Fee Threshold" value="<?php echo $loan_processing_fee_threshold; ?>" />
                            </div>


                        </div>
                        <div class="row">
                            <div class="form-group col-4">
                                <label for="enum">Processing Charge type above threshold <?php echo form_error('processing_charge_type_above') ?></label>
                                <select name="processing_charge_type_above" id="processing_charge_type_above" class="form-control" onchange="penalty_toggler2_above()" >
                                    <option value="">-Select-</option>
                                    <option value="Fixed">Fixed</option>
                                    <option value="Variable">Variable</option>

                                </select>

                            </div>
                            <div class="form-group col-4">
                                <label for="decimal">Processing Fixed Charge Above <?php echo form_error('processing_fixed_charge_above') ?></label>
                                <input type="number" class="form-control" name="processing_fixed_charge_above" id="processing_fixed_charge_above" placeholder="Processing Fixed Charge Above" value="<?php echo 0; ?>" />
                            </div>
                            <div class="form-group col-4">
                                <label for="number">Processing Variable Charge Above <?php echo form_error('processing_variable_charge_above') ?></label>
                                <input type="number" class="form-control" name="processing_variable_charge_above" id="processing_variable_charge_above" placeholder="Processing Variable Charge Above" value="<?php echo 0; ?>" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-4">
                                <label for="enum">Processing Charge Type Below threshold <?php echo form_error('processing_charge_type_below') ?></label>
                                <select name="processing_charge_type_below" id="processing_charge_type_below" class="form-control" onchange="penalty_toggler2_below()" >
                                    <option value="">-Select-</option>
                                    <option value="Fixed">Fixed</option>
                                    <option value="Variable">Variable</option>

                                </select>

                            </div>
                            <div class="form-group col-4">
                                <label for="decimal">Processing Fixed Charge Below <?php echo form_error('processing_fixed_charge_below') ?></label>
                                <input type="number" class="form-control" name="processing_fixed_charge_below" id="processing_fixed_charge_below" placeholder="Processing Fixed Charge Below" value="<?php echo 0; ?>" />
                            </div>
                            <div class="form-group col-4">
                                <label for="number">Processing Variable Charge Below <?php echo form_error('processing_variable_charge_below') ?></label>
                                <input type="number" class="form-control" name="processing_variable_charge_below" id="processing_variable_charge_below" placeholder="Processing Variable Charge Below" value="<?php echo 0; ?>" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="decimal">Minimum Principal <?php echo form_error('minimum_principal') ?></label>
                                <input type="text" class="form-control" name="minimum_principal" id="minimum_principal" placeholder="Minimum Principal" value="<?php echo $minimum_principal; ?>" />
                            </div>
                            <div class="form-group col-6">
                                <label for="decimal">Maximum Principal <?php echo form_error('maximum_principal') ?></label>
                                <input type="text" class="form-control" name="maximum_principal" id="maximum_principal" placeholder="Maximum Principal" value="<?php echo $maximum_principal; ?>" />
                            </div> <div class="form-group col-6">
                                <label for="decimal">Grace period terms (NB put 0 for no grace) <?php echo form_error('grace_period') ?></label>
                                <input type="number" class="form-control" name="grace_period" id="grace_period" placeholder="Grace period" value="<?php echo $grace_period; ?>" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="decimal">Minimum Interest <?php echo form_error('interest_min') ?></label>
                                <input type="text" class="form-control" name="interest_min" id="interest_min" placeholder="Minimum Interest" value="<?php echo $interest_min; ?>" />
                            </div>
                            <div class="form-group col-6">
                                <label for="decimal">Maximum Interest <?php echo form_error('interest_max') ?></label>
                                <input type="text" class="form-control" name="interest_max" id="interest_max" placeholder="Maximum Interest" value="<?php echo $interest_max; ?>" />

                            </div>

                            <input type="hidden" name="loan_product_id" value="<?php echo $loan_product_id; ?>" />
                            <button type="submit" class="btn btn-primary"><?php echo $button ?></button>

                    </form>
                </div>
            </div>
        </div>

    </div>
    <div class="card">
        <div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
            <p><?php echo   $interest; ?></p>
            <form action="<?php echo $action; ?>" method="post">
                <div class="row">
                    <div class="form-group col-4">
                        <label for="varchar">Product Name <?php echo form_error('product_name') ?></label>
                        <input type="text" class="form-control" name="product_name" id="product_name" placeholder="Product Name" value="<?php echo $product_name; ?>" />
                    </div>
                    <div class="form-group col-4">
                        <label for="double">Interest <?php echo form_error('interest') ?></label>
                        <input type="text" class="form-control" name="interest" id="interest" placeholder="Interest" value="<?php echo $interest; ?>" />
                    </div>
                    <div class="form-group col-4">
                        <label for="enum">Frequency <?php echo form_error('frequency') ?></label>

                        <select name="frequency" id="" class="form-control">
                            <option value="">--select--</option>
                            <option value="monthly" <?php if($frequency=='monthly'){ echo "selected"; } ?>>Monthly</option>
                            <option value="Weekly" <?php if($frequency=='Weekly'){ echo "selected"; } ?> >Weekly</option>
                            <option value="2 Weeks" <?php if($frequency=='2 Weeks'){ echo "selected"; } ?>>2 weeks</option>
                        </select>


                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-6">
                        <label for="decimal">Penalty Threshold <?php echo form_error('penalty_threshold') ?></label>
                        <input type="text" class="form-control" name="penalty_threshold" id="penalty_threshold" placeholder="Penalty Threshold" value="<?php echo $penalty_threshold; ?>" />
                    </div>
                    <div class="form-group col-6">
                        <label for="enum">Penalty Charge Type <?php echo form_error('penalty_charge_type') ?></label>
                        <select name="penalty_charge_type" id="penalty_charge_type" class="form-control" onchange="penalty_toggler()" >
                            <option value="">-Select-</option>
                            <option value="Fixed">Fixed</option>
                            <option value="Variable">Variable</option>

                        </select>

                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-6">
                        <label for="decimal">Penalty Fixed Charge Above threshold<?php echo form_error('penalty_fixed_charge_above') ?></label>
                        <input type="text" class="form-control" name="penalty_fixed_charge_above" id="penalty_fixed_charge_above" placeholder="Penalty Fixed Charge Above" value="<?php echo $penalty_fixed_charge_above; ?>" />
                    </div>
                    <div class="form-group col-6">
                        <label for="double">Penalty Variable Charge Above threshold<?php echo form_error('penalty_variable_charge_above') ?></label>
                        <input type="text" class="form-control" name="penalty_variable_charge_above" id="penalty_variable_charge_above" placeholder="Penalty Variable Charge Above" value="<?php echo $penalty_variable_charge_above; ?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-6">
                        <label for="decimal">Penalty Fixed Charge below  threshold<?php echo form_error('penalty_fixed_charge_below') ?></label>
                        <input type="text" class="form-control" name="penalty_fixed_charge_below" id="penalty_fixed_charge_below" placeholder="Penalty Fixed Charge Below" value="<?php echo $penalty_fixed_charge_above; ?>" />
                    </div>
                    <div class="form-group col-6">
                        <label for="double">Penalty Variable Charge below threshold <?php echo form_error('penalty_variable_charge_below') ?></label>
                        <input type="text" class="form-control" name="penalty_variable_charge_below" id="penalty_variable_charge_below" placeholder="Penalty Variable Charge below" value="<?php echo $penalty_variable_charge_above; ?>" />
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <h4>Loan processing configurations</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-6">
                        <label for="decimal">Loan Processing Fee Threshold <?php echo form_error('loan_processing_fee_threshold') ?></label>
                        <input type="text" class="form-control" name="loan_processing_fee_threshold" id="loan_processing_fee_threshold" placeholder="Loan Processing Fee Threshold" value="<?php echo $loan_processing_fee_threshold; ?>" />
                    </div>
                    <div class="form-group col-6">
                        <label for="enum">Processing Charge Type <?php echo form_error('processing_charge_type') ?></label>
                        <select name="processing_charge_type" id="processing_charge_type" class="form-control" onchange="penalty_toggler2()" >
                            <option value="">-Select-</option>
                            <option value="Fixed">Fixed</option>
                            <option value="Variable">Variable</option>

                        </select>

                    </div>

                </div>
                <div class="row">
                    <div class="form-group col-6">
                        <label for="decimal">Processing Fixed Charge Above <?php echo form_error('processing_fixed_charge_above') ?></label>
                        <input type="text" class="form-control" name="processing_fixed_charge_above" id="processing_fixed_charge_above" placeholder="Processing Fixed Charge Above" value="<?php echo $processing_fixed_charge_above; ?>" />
                    </div>
                    <div class="form-group col-6">
                        <label for="double">Processing Variable Charge Above <?php echo form_error('processing_variable_charge_above') ?></label>
                        <input type="text" class="form-control" name="processing_variable_charge_above" id="processing_variable_charge_above" placeholder="Processing Variable Charge Above" value="<?php echo $processing_variable_charge_above; ?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-6">
                        <label for="decimal">Processing Fixed Charge Below <?php echo form_error('processing_fixed_charge_below') ?></label>
                        <input type="text" class="form-control" name="processing_fixed_charge_below" id="processing_fixed_charge_below" placeholder="Processing Fixed Charge Below" value="<?php echo $processing_fixed_charge_below; ?>" />
                    </div>
                    <div class="form-group col-6">
                        <label for="double">Processing Variable Charge Below <?php echo form_error('processing_variable_charge_below') ?></label>
                        <input type="text" class="form-control" name="processing_variable_charge_below" id="processing_variable_charge_below" placeholder="Processing Variable Charge Below" value="<?php echo $processing_variable_charge_below; ?>" />
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-6">
                        <label for="decimal">Minimum Principal <?php echo form_error('minimum_principal') ?></label>
                        <input type="text" class="form-control" name="minimum_principal" id="minimum_principal" placeholder="Minimum Principal" value="<?php echo $minimum_principal; ?>" />
                    </div>
                    <div class="form-group col-6">
                        <label for="decimal">Maximum Principal <?php echo form_error('maximum_principal') ?></label>
                        <input type="text" class="form-control" name="maximum_principal" id="maximum_principal" placeholder="Maximum Principal" value="<?php echo $maximum_principal; ?>" />
                    </div>
                </div>

                <input type="hidden" name="loan_product_id" value="<?php echo $loan_product_id; ?>" />
                <button type="submit" class="btn btn-primary"><?php echo $button ?></button>

            </form>
        </div>
    </div>
</div>
