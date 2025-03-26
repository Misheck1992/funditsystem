<?php
$loan_types = $this->Loan_products_model->get_all();
$corporate = get_all_by_id('corporate_customers','category','client');
$offtakercorporate = get_all_by_id('corporate_customers','category','off_taker');
$currencies  = get_all('currencies ');
$get_settings = get_by_id('settings','settings_id', '1');

?>

<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Loan Management for Corporate</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">Add loan to Corporate Customer</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick #0268bc solid;border-radius: 14px;">
            <div class="row">
                <div class="col-lg-6">
                    <form action="<?php echo base_url('loan/create_act')?>" method="POST" enctype="multipart/form-data">
                        <table class="table">
                            <tr>
                                <td>Corporate</td>
                                <td>
                                    <input type="text" name="customer_type" value="institution" hidden >
                                    <select name="customer" id="group_c" class="select2" required>
                                        <option value="">--select Corporate customer--</option>
                                        <?php

                                        foreach ($corporate as $c){
                                            ?>
                                            <option value="<?php  echo  $c->id;?>"><?php echo $c->EntityName. " ".$c->RegistrationNumber?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>

                            </tr>

                            <tr>
                                <td>Loan Amount/Principal:</td>
                                <td><input type="text" name="amount" value="<?php echo set_value('amount'); ?>" width="50" /></td>
                            </tr>
                            <tr>
                                <td>Select Loan  Principal Currency:</td>
                                <td><select name="currency"  class="select2" >
                                        <option value="">--select currency--</option>
                                        <?php

                                        foreach ($currencies  as $cu){
                                            ?>
                                            <option value="<?php echo $cu->currency_id ?>"><?php echo $cu->currency_code.'('.$cu->country_name.')'; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Select Loan Type/Product:</td>
                                <td>
                                    <select name="loan_type" id="loan_type" class="select2" >
                                        <option value="">--select--</option>
                                        <?php
                                        foreach ($loan_types as $lt) {
                                            ?>
                                            <option value="<?php echo $lt->loan_product_id; ?>" <?php if ($lt->loan_product_id == $this->input->get('loan_type')) {
                                                echo "selected";
                                            } ?>>
                                                <?php echo $lt->product_name . " (" . $lt->frequency . "-" . $lt->calculation_type . ")"; ?>
                                            </option>
                                            <?php
                                        }
                                        ?>
                                    </select></td>
                            </tr>
                            <tr>
                                <td id="loan_term_label">Loan Term/ # of Terms:</td>
                                <td><input type="text" name="months" value="<?php echo set_value('months'); ?>" /></td>
                            </tr>
                            <tr>
                                <td>Loan interest in (%):</td>
                                <td><input type="text" name="interest" value="<?php echo set_value('interest'); ?>" /></td>
                            </tr>
                               <tr>
                                <td>Processing Fee in (%):</td>
                                <td><input type="text" name="processing_fee" value="<?php echo set_value('interest'); ?>" /></td>
                            </tr>
                            <tr>
                                <td>Loan Start  Date:</td>
                                <td><input type="date" name="loan_date"  value="<?php echo set_value('loan_date'); ?>" /></td>
                            </tr>
                            <tr>
                                <td>Credit details files</td>
                                <td>
                                    <div id="loan_forms" >
                                    <div class="row">
                                        <div class="col-6"><br><input type="text" name="file_name[]" placeholder="File name" class="form-control" required></div>
                                        <div class="col-6 "><label for="llsid"  >upload file</label><input id="llsid" type="file" name="loan_files[]" style="display: block" placeholder="Attachment" class="form-control"></div>

                                    </div>

                                    <button type="button" class="btn btn-primary" onclick="addloan_files();"><i class="fa fa-plus"></i>Add more files</button>

                                </td>
                            </tr>
                            <tr>
                                <td>Narration</td>
                                <td><textarea name="narration" id="" cols="30" rows="6"></textarea></td>
                            </tr>

                            <?php if (validation_errors()) : ?>
                                <tr>
                                    <td>

                                    </td>
                                    <td>
                                        <?php echo validation_errors(); ?>
                                    </td>
                                </tr>
                            <?php endif;?>
                            <?php if (isset($error)) : ?>
                                <tr>
                                    <td>

                                    </td>
                                    <td>
                                        <?php echo $error; ?>
                                    </td>
                                </tr>
                            <?php endif;?>
                        </table>
                        <br>
                        <hr>
                        <h5>Collateral section</h5>
                        <div id="forms" >
                            <div class="row">
                                <div class="col-6 mt-2"><input type="text" name="coname[]" placeholder="collateral name" class="form-control"></div>
                                <div class="col-6 mt-2"><input type="text" name="type[]" placeholder="collateral type" class="form-control"></div>
                            </div>
                            <div class="row">
                                <div class="col-6 mt-2"><input type="text" name="serial[]" placeholder="serial number" class="form-control"></div>
                                <div class="col-6 mt-2"><input type="text" name="cvalue[]" placeholder="collateral value" class="form-control"></div>
                            </div>
                            <div class="row">
                                <div class="col-6 mt-2"><label for="ifid"  >upload attachment</label><input type="file" name="collateralfiles[]" style="display: block" placeholder="Attachment" class="form-control"></div>
                            </div>
                            <div class="row">
                                <div class="col-12 mt-2"><textarea class="form-control" name="desc[]" id="" cols="30" rows="6"></textarea></div>
                            </div>
                        </div>
                        <br />
                        <button type="button" class="btn btn-primary" onclick="addField();"><i class="fa fa-plus"></i>Add more collateral items</button>
                        <br /><br /><br />

                        <tr>
                            <td>Off taker Corporate</td>
                            <td>

                                <select  class="form-control" name="off_taker" id="off_taker" class="select2" required>
                                    <option value="">--select Corporate customer--</option>
                                    <?php

                                    foreach ($offtakercorporate as $c){
                                        ?>
                                        <option value="<?php  echo  $c->id;?>"><?php echo $c->EntityName. " ".$c->RegistrationNumber?></option>
                                        <?php
                                    }
                                    ?>
                                </select>

                        </tr>
                        <br/><br/>
                        <tr>
                            <td></td>
                            <td><input type="submit" name="submit_loan" value="Create Loan" class="btn btn-danger btn-sm btn-block"  onclick="confirm('Are you sure you want to create loan?')"/></td>
                        </tr>

                    </form>
                </div>
                <div class="col-lg-6">
                    <h3>Results</h3>

                    <div style="padding: 1em;">
                        <div class="row">
                            <div class="col-8">
                                <div >
                                    <p>Customer search results details</p>
                                    <ul id="customer_loan" style="">

                                    </ul>

                                </div>
                            </div>

                        </div>
                    </div>
                    <div>
                        <h4>Booked loan products</h4>
                        <ul id="loandd">

                        </ul>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
</div>
