<?php
$loan_types = $this->Loan_products_model->get_all();
$get_settings = get_by_id('settings','settings_id', '1');
?>

<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">Loan Management</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">-</a>
				<span class="breadcrumb-item active">Add loan</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
			<div class="row">
				<div class="col-lg-5 border-right">
					<form action="<?php echo base_url('loan/create_act')?>" method="POST" enctype="multipart/form-data">
						<table class="table">
							<tr>
								<td>Customer</td>
								<td>
                                    <input type="text" name="customer_type" value="individual" hidden >
									<select name="customer" id="customer_loan" class="select2">
										<option value="">--select customer</option>
										<?php

										foreach ($customers as $c){
											?>
											<option value="<?php  echo  $c->id;?>"><?php echo $c->Firstname. " ".$c->Lastname?></option>
											<?php
										}
										?>
									</select>

							</tr>

							<tr>
								<td>Loan Amount:</td>
								<td><input type="text" name="amount" value="<?php echo set_value('amount'); ?>" width="50" /></td>
							</tr>
							<tr>
								<td>Loan Term:</td>
								<td><input type="text" name="months" value="<?php echo set_value('months'); ?>" /></td>
							</tr>
                            <tr>
								<td>Loan Interests (in %):</td>
								<td><input type="text" name="interest" value="<?php echo set_value('interest'); ?>" /></td>
							</tr>
							<tr>
								<td>Select Loan Type:</td>
								<td><select name="loan_type" id="" class="select2" >
										<option value="">--select--</option>
										<?php

										foreach ($loan_types as $lt){
											?>
											<option value="<?php echo $lt->loan_product_id ?>"><?php echo $lt->product_name; ?></option>
											<?php
										}
										?>
									</select></td>
							</tr>
							<tr>
								<td>Interest minumum:</td>
								<td><input read-only type="text" name="interest_min" id="interest_min"  value="" /></td>
							</tr>
                            <tr>
								<td>Interest maximum:</td>
								<td><input  read-only type="text" name="interest_max"   id="interest_max"   value="" /></td>
							</tr>
                              <tr>
								<td>Interest </td>
								<td><input   type="text" name="interest"   id="interest"   value="" /></td>
							</tr>
                            
                                <tr>
								<td>Loan Date:</td>
								<td><input type="date" name="loan_date"  value="<?php echo set_value('loan_date'); ?>" /></td>
							</tr>
                            <tr>
								<td>Loan Date:</td>
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
							<tr>
								<td></td>
								<td></td>
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
                        <tr>
                            <td></td>
                            <td>

                                <?php
                                if($get_settings->require_reg_fee=="Yes"){

                                    ?>
                                    <hr>
                                    <h5>Registration fees section</h5>
                                    <div class="form-group col-12">
                                        <label for="date">Total registration fee amount MK</label>
                                        <input type="text" class="form-control" name="fee_amount" id="fee_amount" readonly  />
                                    </div>

                                    <div class="form-group col-12">
                                        <label for="payment_method">Payment Method</label>
                                        <?php

                                        $methods = get_all('payment_method')
                                        ?>
                                        <select name="payment_method" id="payment_method1" class="form-control">
                                            <option value="">-select--</option>
                                            <option value="01">Deduct from principal amount</option>
                                           
                                            <?php

                                            foreach ($methods as $method){
                                                ?>
                                                <option value="<?php  echo $method->payment_method ?>"><?php  echo $method->payment_method_name ?></option>
                                                <?php

                                            }
                                            ?>

                                        </select>
                                    </div>
                                    <div class="form-group col-12">
                                        <label for="payment_method">Payment Reference number</label>
                                        <input type="text" class="form-control" name="reference" id="reference1"   />
                                    </div>
                                    <div class="form-group col-12" id="llshos">
                                        <label for="signature" class="custom-file-upload"> Upload payment Attachment if available </label>
                                        <input type="file" onchange="uploadcommon('refidd')"    id="refidd" placeholder="Reference attachment" />
                                        <input type="text" id="refidd1" name="referencedoc" hidden >

                                    </div>

                                    <?php

                                }else{

                                    }
                                ?>
                                <input type="submit" name="submit_loan" value="Create Loan" class="btn btn-danger btn-sm btn-block"  onclick="confirm('Are you sure you want to create loan?')"/>

                            </td>
                        </tr>
					</form>
				</div>
				<div class="col-lg-4">
					<h3>Results</h3>

					<div style="padding: 1em;">
						<div class="row">
							<div class="col-6">
								<div id="customer-results">
									<p>Customer search results details</p>

								</div>
							</div>
							<div class="col-6">

							</div>
						</div>
					</div>
					<div>
						<h4>Booked loan products</h4>
						<ul id="loandd">

						</ul>
					</div>
				</div>
				<div class="col-lg-3">
					<h2>KYC</h2>
					<hr>
					<table class="table" id="kyc_data">

					</table>
				</div>

			</div>
		</div>
	</div>
</div>
</div>
