<?php
$next_payment_details = $this->Payement_schedules_model->get_next($next_payment_id,$loan_id);
$currency = get_by_id('currencies','currency_id',$currency);
?>
<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">Loan view</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">-</a>
				<span class="breadcrumb-item active">Loan Details</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
			<div class="row">
				<div class="col-lg-3 border-right">
					<h2>Loan Info</h2>
					<hr>
					<table>
						<tr>
							<td style="text-align: right;padding-right: 10px;">Loan #</td>
							<td><?php echo $loan_number?></td>
						</tr>
						<tr>
							<td style="text-align: right;padding-right: 10px;">Loan Type</td>
							<td><?php echo $loan_product?></td>
						</tr>
						<tr >
							<td style="text-align: right;padding-right: 10px;">Loan Customer</td>
                            <td><a href="<?php echo base_url($preview_url).$customer_id?>"><?php echo $loan_customer?></a></td>
						</tr>
						<tr>
							<td style="text-align: right;padding-right: 10px;">Loan Status</td>
							<td><?php echo $loan_status?></td>
						</tr>
						<tr>
							<td style="text-align: right;padding-right: 10px;">Loan Principal</td>
							<td><?php echo $currency->currency_code ?> &nbsp;<?php echo number_format($loan_principal,2)?></td>
						</tr>
						<tr>
							<td style="text-align: right;padding-right: 10px;">Total interest</td>
							<td><?php echo $currency->currency_code ?> &nbsp;<?php echo number_format($loan_interest_amount,2)?></td>
						</tr>
                        <tr>
                            <td style="text-align: right;padding-right: 10px;">Processing fee %</td>
                            <td><?php echo $processing_fee ?> &nbsp;</td>
                        </tr>
						
						<tr>
							<td style="text-align: right;padding-right: 10px;">Total loan amount</td>
							<td><?php echo $currency->currency_code ?> &nbsp; <?php echo number_format($loan_amount_total,2)?></td>
						</tr>
						<tr>
							<td style="text-align: right;padding-right: 10px;">Payments Made</td>
							<td><?php echo $currency->currency_code ?> &nbsp;
								<?php
								$total_p = 0;
								$paymentsmade=0;
								foreach ($payments as $pp){
                                    if($pp->status == "PAID" ){
                                        $total_p +=$pp->amount;

                                    }
                                    if($pp->partial_paid  == "YES"){

                                        $total_p +=$pp->paid_amount;

                                    }


								}
                                $paymentsmade=$total_p;
								echo number_format($total_p,2);


								?>

							</td>
						</tr>


						<tr>
							<td style="text-align: right;padding-right: 10px;">Remaining Balance to Pay
							</td>
							<td><?php echo $currency->currency_code ?> &nbsp;

								<?php
                                $remainingbalance=0;
                                $remainingbalance= $loan_amount_total-$paymentsmade;

                                echo number_format($remainingbalance,2);
								?>
							</td>
						</tr>
					</table>
					<br>
					<h2>Schedule Payment</h2>
					<hr>
					<?php
					if(empty($next_payment_details)){
						echo "No more payments to make";
					}else{
					?>
					<table>
						<tr>
							<td style="text-align: right;padding-right: 10px;" width="150">Payment #</td>
							<td><?php echo $next_payment_id ?></td>
						</tr>
						<tr>
							<td style="text-align: right;padding-right: 10px;">Pay principal amt</td>
							<td><?php echo $currency->currency_code ?> &nbsp; <?php echo number_format($next_payment_details->principal,2)?></td>
						</tr>
						<tr >
							<td style="text-align: right;padding-right: 10px;">Pay interest amt</td>
							<td><a href="#"><?php echo $currency->currency_code ?> &nbsp; <?php echo number_format($next_payment_details->interest,2)?></a></td>
						</tr>
						<tr>
							<td style="text-align: right;padding-right: 10px;">Payment amt</td>
							<td><?php echo $currency->currency_code ?> &nbsp; <?php echo number_format($next_payment_details->amount,2)?></td>
						</tr>
						<tr>
							<td style="text-align: right;padding-right: 10px;">Due date</td>
							<td><?php echo $next_payment_details->payment_schedule;?></td>
						</tr>
						<tr>
							<td style="text-align: right;padding-right: 10px;">Payment status</td>
							<td>

								<?php

								$oved = '';
								if($next_payment_details->payment_schedule < date('Y-m-d')   AND $next_payment_details->status == 'NOT PAID') {
									$oved = ' | OVER DUE';
								} elseif($next_payment_details->status=='PAID') {

								} elseif($next_payment_details->payment_schedule == date('Y-m-d')  AND $next_payment_details->status == 'NOT PAID') {

									$oved = ' | DUE TODAY';
								}
								?>
								<span style="color:<?php echo $next_payment_details->status=='PAID' ? 'GREEN' : 'RED'?>"><?php echo $next_payment_details->status.$oved; ?></span>
							</td>
						</tr>


					</table>
					<?php
					}
					?>

				</div>
				<div class="col-lg-7 border-right">
					<?php if($calculation_type == 'Bullet Payment'): ?>
						<?php if (!empty($acrued) && $acrued['status'] == 'success' && isset($acrued['debug'])): ?>
							<div class="card mb-3 collapse" id="payoffDetails">
								<div class="card-header bg-light">
									<h6>Payoff Calculation Details</h6>
								</div>
								<div class="card-body">
									<ul class="list-group">
										<li class="list-group-item d-flex justify-content-between align-items-center">
											Loan Start Date Date
											<span><?= $acrued['debug']['loan_date'] ?></span>
										</li>
										<li class="list-group-item d-flex justify-content-between align-items-center">
											Current Date
											<span><?= $acrued['debug']['payoff_date'] ?></span>
										</li>
										<li class="list-group-item d-flex justify-content-between align-items-center">
											Daily interest Accrual Days Elapsed  After First Month.
											<span><?= $acrued['debug']['elapsed_days'] ?>Days</span>
										</li>
										<li class="list-group-item d-flex justify-content-between align-items-center">
											Monthly Interest Rate
											<span><?= number_format($acrued['debug']['monthly_interest_rate'] * 100, 2) ?>%</span>
										</li>
										<li class="list-group-item d-flex justify-content-between align-items-center">
											Daily Interest amount
											<span><?= $currency->currency_code ?> <?= number_format($acrued['debug']['daily_interest'], 2) ?></span>
										</li>
										<li class="list-group-item d-flex justify-content-between align-items-center">
											Current Accrued Interest amount
											<span><?= $currency->currency_code ?> <?= number_format($acrued['accrued_interest'], 2) ?></span>
										</li>
										<li class="list-group-item d-flex justify-content-between align-items-center">
											Loan Principal  amount
											<span><?= $currency->currency_code ?> <?= number_format($acrued['debug']['loan_principal'], 2) ?></span>
										</li>
										<li class="list-group-item d-flex justify-content-between align-items-center">
											Loan Payments  Made
											<span><?= $currency->currency_code ?> <?=  number_format($total_p,2); ?></span>
										</li>
										<li class="list-group-item d-flex justify-content-between align-items-center">
											Current Accrued Interest Balance amount
											<span><?= $currency->currency_code ?> <?= number_format($acrued['accrued_interest_balance'], 2) ?></span>
										</li>
										<li class="list-group-item d-flex justify-content-between align-items-center">
											Current Principal Balance amount
											<span><?= $currency->currency_code ?> <?= number_format($acrued['current_balance'], 2) ?></span>
										</li>
										<li class="list-group-item d-flex justify-content-between align-items-center">
											Total Payoff amount
											<span><?= $currency->currency_code ?> <?= number_format($acrued['total_payoff'], 2) ?></span>
										</li>
									</ul>
									<div class="mt-3">
										<pre class="bg-light p-2 rounded"><?= $acrued['debug']['calculation_explanation'] ?></pre>
									</div>
								</div>
							</div>
							<p><a class="btn btn-sm btn-outline-info" data-toggle="collapse" href="#payoffDetails">Show/Hide Calculation Details</a></p>
						<?php endif; ?>
					<?php endif; ?>
					<hr>
					<h2>Contract Summary</h2>
                    <div class="double-scroll">
					<table style="width: 100%;border-collapse: collapse;">
						<thead>
						<tr style="border: 1px solid black;">
							<th>Payment #</th>
							<th>Check Date</th>

							<th>Principal</th>
							<th>Interest</th>

							<th>Pay Amount</th>
							<th>Amount Paid</th>
							<th>Loan Balance</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
						</thead>
						<tbody>
						<?php
						foreach ($payments as $p){
							?>
							<?php
							//change color depending on it's status
							$css = '';
							$xstatus = '';
							if($p->payment_schedule < date('Y-m-d')   AND $p->status == 'NOT PAID') {
								$css = ' class="due"';
								$xstatus = ' | OVER DUE';
							} elseif($p->status=='PAID') {
								$css = 'class="paid"';
							} elseif($p->payment_schedule == date('Y-m-d')  AND $p->status == 'NOT PAID') {
								$css = ' class="due_now"';
								$xstatus = ' | DUE TODAY';
							}
							?>
<!--							<tr style="border: 1px solid black;background-color: #F3D8D8;">-->
							<tr style="font-weight: <?php echo !empty($xstatus)?'900':'200'; ?>;">
								<td <?php echo $css; ?>><?php  echo $p->payment_number?></td>
								<td <?php echo $css; ?>><?php  echo $p->payment_schedule?></td>

								<td <?php echo $css; ?>><?php echo $currency->currency_code ?> &nbsp;<?php  echo number_format($p->principal,2) ?></td>
								<td <?php echo $css; ?>><?php echo $currency->currency_code ?> &nbsp;<?php  echo number_format($p->interest,2) ?></td>

								<td <?php echo $css; ?>><?php echo $currency->currency_code ?> &nbsp;<?php  echo number_format($p->amount,2) ?></td>
								<td <?php echo $css; ?>><?php echo $currency->currency_code ?> &nbsp;<?php  echo number_format($p->paid_amount,2)?></td>
								<td <?php echo $css; ?>><?php echo $currency->currency_code ?> &nbsp;<?php  echo number_format($p->loan_balance,2)?></td>
								<td width="150" <?php echo $css; ?>><span style="color:<?php echo $p->status=='PAID' ? 'GREEN' : 'RED'?>"><?php echo $p->status.$xstatus; if($p->partial_paid=="YES"){echo "-<font color='green'>(Partial paid)</font>";}?></span></td>
								<td <?php echo $css; ?> width="70"><?php if($xstatus !="") { ?> <?php  } ?></td>
							</tr>
						<?php
						}
						?>


<!--						<tr style="border: 1px solid black;background-color: #D1EFD1;">-->
<!--							<td>2</td>-->
<!--							<td>2021-06-22</td>-->
<!--							<td>MK 20,9393</td>-->
<!--							<td>MK 20,9393</td>-->
<!--							<td>MK 20,9393</td>-->
<!--							<td>ACTIVE</td>-->
<!--							<td><a href="" class="btn btn-sm btn-danger">Pay</a></td>-->
<!--						</tr>-->
						</tbody>
					</table>
                    </div>
                    <hr>
                    <h4>Deposit/Payment History</h4>
                    <div style="overflow: scroll;">
                        <table style="width: 100%;border-collapse: collapse;">
                            <thead>
                            <tr style="border: 1px solid black;">
                                <th>Deposit amount</th>
                                <th>Transaction Ref</th>
                                <th>Transaction Reason</th>

                                <th>Payment Date</th>
                                <th>Cashier account</th>
								<th>Proof File</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $trans = get_all_where('transaction','account_number = "'.$loan_number.'" AND credit !=0');
                            foreach ($trans as $history)
                            {

                                ?>
                                <tr>
                                    <td><?php echo $currency->currency_code ?> &nbsp;<?php echo number_format($history->credit,2); ?></td>
                                    <td><?php echo $history->transaction_id; ?></td>
                                    <td><?php echo $history->reason; ?></td>
                                      <td><?php echo $history->system_time; ?></td>
                                    <td><?php echo $history->coresponding_account; ?></td>
									<td><a href="<?php echo base_url('uploads/').$history->proof; ?>" target="_blank"><i class="bi bi-download"></i></a></td>
									<td><a href="">Deposit Usage</a></td>
                                </tr>

                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
				</div>
				<div class="col-lg-2">
					<a href="<?php echo base_url('loan/report/').$loan_id ?>" style="color: red;"><i class="fa fa-file-pdf fa-2x"></i>Report</a>

					<hr>

					<!-- Action Buttons Based on Context -->
					<?php if (isset($action) && !empty($action)): ?>
						<div class="action-buttons mb-3">
							<h5>Actions</h5>
							<?php if ($action == 'approve_first'): ?>
								<a href="<?php echo base_url('loan/approval_action?id=').$loan_id."&action=APPROVED_FIRST"?>"
								   onclick="return confirm('Are you sure you want to approve this loan?')"
								   class="btn btn-sm btn-warning mb-2 d-block">Approve</a>
								<a href="<?php echo base_url('loan/approval_action?id=').$loan_id."&action=REJECT"?>"
								   class="btn btn-sm btn-danger mb-2 d-block"
								   onclick="return confirm('Are you sure you want to reject?')">Reject</a>
							<?php elseif ($action == 'recommend'): ?>
								<a href="<?php echo base_url('loan/approval_action?id=').$loan_id."&action=RECOMMENDED"?>"
								   onclick="return confirm('Are you sure you want to recommend this loan?')"
								   class="btn btn-sm btn-warning mb-2 d-block">Recommend</a>
								<a href="<?php echo base_url('loan/approval_action?id=').$loan_id."&action=REJECT"?>"
								   class="btn btn-sm btn-danger mb-2 d-block"
								   onclick="return confirm('Are you sure you want to reject?')">Reject</a>
							<?php elseif ($action == 'approve_second'): ?>
								<a href="<?php echo base_url('loan/approval_action?id=').$loan_id."&action=APPROVED"?>"
								   onclick="return confirm('Are you sure you want to approve this loan?')"
								   class="btn btn-sm btn-success mb-2 d-block">Final Approve</a>
								<a href="<?php echo base_url('loan/approval_action?id=').$loan_id."&action=REJECT"?>"
								   class="btn btn-sm btn-danger mb-2 d-block"
								   onclick="return confirm('Are you sure you want to reject?')">Reject</a>
							<?php elseif ($action == 'disburse'): ?>
								<a class="btn btn-sm btn-danger mb-2 d-block" href="#" onclick="disburse_loan_charge_pre_paid('<?php echo $loan_id; ?>','<?php echo $loan_date ?>')">Disburse (processing fee charged)</a>
							<?php elseif ($action == 'delete_recommend'): ?>
								<a href="<?php echo base_url('Loan/delete_recommend/').$loan_id  ?>"
								   class="btn btn-sm btn-warning mb-2 d-block">Recommend Delete</a>
								<a href="<?php echo base_url('Loan/delete_reject/').$loan_id  ?>"
								   class="btn btn-sm btn-primary mb-2 d-block">Reject Delete</a>
							<?php elseif ($action == 'delete_approve'): ?>
								<a href="<?php echo base_url('Loan/delete_approve/').$loan_id  ?>"
								   class="btn btn-sm btn-warning mb-2 d-block">Approve Delete</a>
								<a href="<?php echo base_url('Loan/delete_reject/').$loan_id  ?>"
								   class="btn btn-sm btn-primary mb-2 d-block">Reject Delete</a>
							<?php endif; ?>
						</div>
						<hr>
					<?php endif; ?>

					<?php
					if(empty($next_payment_details)){
						echo "No more payments to make";
					}else{
					?>
<!--<h4>Pay</h4>-->
						<?php if($p->loan_status=='ACTIVE'){
							?>
<!--							<a href="#" class="btn btn-sm btn-success" onclick="pay_current()">To current schedule</a> |-->
<!--							<a href="#" onclick="advance_payment()" class="btn btn-small btn-danger">Advance payment</a>-->

						<?php }
						?>
						<?php

					}
					?>

				</div>
			</div>
            <div class="row">
                <h1>Narration</h1>
                <div class="col-lg-12">
                    <?php echo $narration ?>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <h1>Loan Files</h1>
                    <table class="table">
                        <thead>
                        <th>File name</th>
                        <th>Action</th>
                        </thead>
                        <tbody>
                        <?php

                        foreach ($files as $file){
                            ?>
                            <tr>
                                <td><?php echo $file->file_name ?></td>
                                <td><a href="<?php echo base_url('uploads/').$file->real_file ?>" download>Download</a></td>
                            </tr>
                        <?php
                        }
                        ?>

                        </tbody>

                    </table>
                </div>
                <div class="col-lg-6">
                    <h1>Collateral</h1>

                    <table  class="table">
                        <thead>
                        <tr>

                            <th>#</th>
                            <th> Name</th>
                            <th>Type
                            </th>
                            <th>Serial</th>
                            <th>Value</th>
                            <th>Files </th>



                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $loan_co_data=get_all_by_id(' loan_collaterals','collateral_loan_id',$loan_id);
                        $n = 1;
                        foreach ($loan_co_data as $l){
                            ?>
                            <tr>
                                <td><?php echo $n;?></td>
                                <td><?php echo $l->collateral_name;?></td>

                                <td><?php echo $l->collateral_type;?></td>
                                <td><?php echo $l->collateral_serial;?></td>
                                <td><?php echo $l->collateral_value;?></td>
                                <td><a href="<?php echo base_url('uploads/').$l->collateral_file ?>" download>Download</a></td>

                            </tr>

                            <?php
                            $n ++;
                        }

                        ?>
                        </tbody>
                    </table>
                 
                </div>
            </div>
		</div>
	</div>

</div>


<div aria-hidden="true" class="onboarding-modal modal fade" id="payment_modal" role="dialog" tabindex="-1">
	<div class="modal-dialog modal-lg modal-centered" role="document">
		<div class="modal-content text-center">
			<span></span><button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
			<div class="onboarding-content" style="padding: 1em;">
				<h4 class="onboarding-title" >Payments</h4>
				<p style="color: red;">Are you sure you want to pay the current payment of loan ?</p>
				<table>
					<tr>
						<td style="text-align: right;padding-right: 10px;" width="150">Loan #</td>
						<td><?php echo $loan_number  ?></td>
					</tr>
					<tr>
						<td style="text-align: right;padding-right: 10px;" width="150">Payment #</td>
						<td><?php echo $next_payment_id ?></td>
					</tr>

					<tr>
						<td style="text-align: right;padding-right: 10px;">Payment amt</td>
						<td><?php echo $currency->currency_code ?> &nbsp; <?php echo number_format($next_payment_details->amount,2)?></td>
					</tr>


				</table>
				<form action="<?php echo base_url('loan/pay_loan')?>" class="form-row" method="POST" >

					<div class="form-group col-lg-12" style="padding: 5em;">
						<label for="date">To pay amount in <?php echo $currency->currency_code ?> &nbsp; </label>
						<input type="text" name="loan_id" value="<?php echo $loan_id?>" hidden>
						<input type="text" name="payment_number" value="<?php echo $next_payment_id ?>" hidden>


						<input style="border: thin red solid;" type="text" class="form-control" name="amount"  value="<?php echo $next_payment_details->amount?>" placeholder="Enter pay amount" readonly required />
						<br/>
                        <br> <label for="paid_date">Paid date</label><br>
                        <input style="border: thin red solid;"  type="date" name="paid_date"  required/><br/>

                        <label for="id_front" > Upload proof of payment (only image) </label>
						<input type="file"  onchange="uploadcommon('id_front')"   style="display: block;" />


					</div>
					<button class="btn btn-sm btn-block btn-danger" type="submit">Submit Payment</button>
				</form>
			</div>
		</div>
	</div>
</div>
<div aria-hidden="true" class="onboarding-modal modal fade" id="advance_payment_modal" role="dialog" tabindex="-1">
	<div class="modal-dialog modal-lg modal-centered" role="document">
		<div class="modal-content text-center">
			<span></span><button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
			<div class="onboarding-content" style="padding: 1em;">
				<h4 class="onboarding-title" >Loan advance payments</h4>
				<p style="color: red;">Are you sure you want to make advance payments ?</p>
				<form action="<?php echo base_url('loan/pay_advance')?>" class="form-row" method="POST" >
				<table style="width: 100%;border-collapse: collapse;">
					<thead>
					<tr style="border: 1px solid black;">
						<th>Payment #</th>
						<th>Check Date</th>
						<th>Amount</th>
						<th>Amount Paid</th>
						<th>Loan Balance</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach ($payments as $p){
						?>
						<?php
						//change color depending on it's status
						$css = '';
						$xstatus = '';
						if($p->payment_schedule < date('Y-m-d')   AND $p->status == 'NOT PAID') {
							$css = ' class="due"';
							$xstatus = ' | OVER DUE';
						} elseif($p->status=='PAID') {
							$css = 'class="paid"';
						} elseif($p->payment_schedule == date('Y-m-d')  AND $p->status == 'NOT PAID') {
							$css = ' class="due_now"';
							$xstatus = ' | DUE TODAY';
						}
						?>
						<!--							<tr style="border: 1px solid black;background-color: #F3D8D8;">-->
						<tr style="font-weight: <?php echo !empty($xstatus)?'900':'200'; ?>;">
							<td <?php echo $css; ?>><?php  echo $p->payment_number?></td>
							<td <?php echo $css; ?>><?php  echo $p->payment_schedule?></td>
							<td <?php echo $css; ?>><?php echo $currency->currency_code ?> &nbsp;<?php  echo number_format($p->amount,2) ?></td>
							<td <?php echo $css; ?>><?php echo $currency->currency_code ?> &nbsp;<?php  echo number_format($p->paid_amount,2)?></td>
							<td <?php echo $css; ?>><?php echo $currency->currency_code ?> &nbsp;<?php  echo number_format($p->loan_balance,2)?></td>
							<td width="150" <?php echo $css; ?>><span style="color:<?php echo $p->status=='PAID' ? 'GREEN' : 'RED'?>"><?php echo $p->status.$xstatus; if($p->partial_paid=="YES"){echo "-<font color='green'>(Partial paid)</font>";}?></span></td>
							<td <?php echo $css; ?> width="70"><?php if($p->status == 'NOT PAID') { ?>  <input type="checkbox" name="payment_number[]" value="<?php echo $p->payment_number  ?>" class="check-cls"><?php  } ?></td>
						</tr>
						<?php
					}
					?>



					</tbody>
				</table>

					<input type="text" name="loan_id" value="<?php echo $loan_id?>" hidden>

					<input style="border: thin red solid;" type="text" class="form-control" name="amount"  value="<?php echo $next_payment_details->amount?>" hidden />
                    <br> <label for="paid_date">paid date</label><br>
                    <input style="border: thin red solid;"  type="date" name="paid_date"  required/> <br/><br/>
					<div class="form-group col-lg-12" style="padding: 2em;">
			<span class="tool-tip" data-toggle="tooltip" data-placement="top" title="You need to choose at least one option">
    <button class="btn btn-sm  btn-danger submit-btn" style="border: red solid thin;" disabled="disabled" type="submit">Submit Payments</button>
			</span>

		</div>

				</form>

			</div>
		</div>
	</div>
</div>

<div aria-hidden="true" class="onboarding-modal modal fade" id="late_payment_modal" role="dialog" tabindex="-1">
	<div class="modal-dialog modal-lg modal-centered" role="document">
		<div class="modal-content text-center">
			<span></span><button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
			<div class="onboarding-content" style="padding: 1em;">
				<h4 class="onboarding-title" >Payments</h4>
				<p style="color: red;">Are you sure you want to pay  loan  as below details?</p>
				<table>
					<tr>
						<td style="text-align: right;padding-right: 10px;" width="150">Loan #</td>
						<td><?php echo $loan_number ?></td>
					</tr>
					<tr>
						<td style="text-align: right;padding-right: 10px;" width="150">Payment #</td>
						<td id="spn"></td>
					</tr>

					<tr>
						<td style="text-align: right;padding-right: 10px;">Payment amt</td>
						<td id="slm"></td>
					</tr>
					<tr>
						<td style="text-align: right;padding-right: 10px;">Payment Charge</td>
						<td id="spc"></td>
					</tr>


				</table>
				<form action="<?php echo base_url('loan/pay_late_loan')?>" class="form-row" method="POST" id="latepaymentform">

					<div class="form-group col-lg-12" style="padding: 5em;">
						<label for="date">To pay amount in <?php echo $currency->currency_code ?> &nbsp;  </label>
						<input type="text" name="loan_id" value="<?php echo $loan_id?>" hidden>
						<input type="text" name="payment_number" id="pn" hidden required>
                        
						<input style="border: thin red solid;" type="text" class="form-control" id="lm_late" name="amount"  readonly required />



						<label for="late_charge_amount">Late payment charge</label>
						<input style="border: thin red solid;" type="text" class="form-control" id="late_charge_amount" name="lamount"   required />
						<br> <label for="paid_date">paid date</label><br>
                        <input style="border: thin red solid;"  type="date" name="paid_date"  required/> <br/><br/>
                        <div class="form-group col-12">
                            <label for="payment_method">Payment Method</label>
                            <?php

                            $methods = get_all('payment_method')
                            ?>
                            <select name="payment_method" id="payment_method" class="form-control">
                                <option value="">-select--</option>
                                <option value="0">Institution's Bank savings</option>
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
                            <input type="text" class="form-control" name="reference" id="reference"  required />
                        </div>
                        <label for="id_front" > Upload proof of payment  </label>
                        <input type="file"  name="file"  style="display: block;"    />

                    </div>
					<button class="btn btn-sm btn-block btn-danger" type="submit">Submit Payment</button>
				</form>
			</div>
		</div>
	</div>
</div>
