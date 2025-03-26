<?php
$loan_types = $this->Loan_products_model->get_all();
?>

<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">Loan Calculator</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">-</a>
				<span class="breadcrumb-item active">Loan calculator</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" style="border: thick green solid;border-radius: 14px;">
			<div class="row">
				<div class="col-lg-6">

					<form action="<?php echo base_url('loan/calculate')?>" method="get">
						<table class="table">
							<tr>
								<td>Loan Amount:</td>
								<td><input type="text" name="amount" value="<?php echo $this->input->get('amount'); ?>" /></td>
							</tr>

                            <tr>
                                <td>Select Loan Type/Product:</td>
                                <td>
                                    <select name="loan_type" id="loan_type">
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
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td id="loan_term_label">Loan Term:</td>
                                <td>
                                    <input type="text" name="months" value="<?php echo $this->input->get('months'); ?>" />
                                </td>
                            </tr>


                            <tr>
                                <td>Loan interest in (%):</td>
                                <td><input type="text" name="interest" value="<?php echo $this->input->get('interest'); ?>" /></td>
                            </tr>
							<tr>
								<td>Loan start Date:</td>
								<td><input type="date" name="loan_date" class="datepicker" value="<?php echo $this->input->get('loan_date'); ?>" /></td>
							</tr>
							<tr>
								<td></td>
								<td><input type="submit" name="submit_loan" value="Submit" /></td>
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
					</form>
				</div>
				<div class="col-lg-6">
						<h3>Results</h3>


					<?php if (isset($result)): ?>
					<div class="clearFix"></div>
					<div class="contentBody w500" style="overflow-x:auto;">

						<div class="clearFix"></div>

						<?php echo $result; ?>
					</div>
					<div class="clearFix"></div>
				</div>
				<?php endif;?>
				</div>
			</div>
		</div>
	</div>
</div>
