<?php
$te = $this->Tellering_model->get_allm($this->session->userdata('user_id'));
$vat = $this->Currency_model->get_all();
$vt = $this->Accounts_model->get_account();
$data =  $this->Vault_cashier_pends_model->get_my_cash($this->session->userdata('user_id'));
?>
<div class="content-i">
	<div class="content-box">
		<div class="row">
			<div class="col-lg-12">
				<div class="element-wrapper">
					<h4 class="element-header">Cash Operations-   Cashier to Vault</h4>
					<div class="element-box">
						<div class="row">
							<div class="col-6">
								<form action="<?php  echo base_url('Accounts/credit_vault')?>" method="post">
									<input hidden type="text" name="vault_account" id="vault_account">
									<input hidden type="text" name="account2" id="teller_account">
									<div class="form-group">
										<label for="">My Teller Account</label>
										<select name="account" id="misheck" class="form-control">
											<option value="">--select--</option>
											<?php

											foreach ($te as $value){
												?>
												<option value="<?php  echo $value->id ?>"><?php echo $value->Firstname." ".$value->Lastname."(-".$value->name." ".$value->AccountNumber.")"?></option>
												<?php
											}
											?>
										</select>
									</div>
									<div id="teller_display" style="border: thick solid skyblue; height: 200px; border-radius: 15px; padding: 2em;">
										<h5 style="color: red;">Select currency to your right to get started</h5>
									</div>
									<h6>Amount In <i id="cvalue"></i></h6>

									<div class="form-group">
										<label for="">Total amount </label>
										<input type="text" id="tt" name="amount" class="form-control" placeholder="Enter amount">
									</div>

									<input type="submit" name="submit" value="save changes">
								</form>
							</div>
							<div class="col-6">
								<div class="form-group">
									<label for="">Vault Account</label>

								</div>
								<div  style="border: thick solid black; height: 250px; border-radius: 15px; padding: 2em;">

									<table class="table table-striped" id="vactb">
									</table>
								</div>

							</div>

						</div>
						<div class="row">
							<div class="col-lg-2">
							</div>
							<div class="col-lg-10">
								<p>Pending Requests</p>
								<table class="table table-striped table-responsive" id="example5">
									<thead>
									<tr>
										<th>Vault Account</th>
										<th>Teller Account</th>
										<th>Amount</th>
										<th>Created By</th>
										<th>Created date</th>
										<th>status</th>
										<th>Action</th>
									</tr>
									</thead>
									<tbody>
									<?php
									foreach ($data as $r){
										?>
										<tr>
											<td><?php echo $r->vault_account ?></td>
											<td><?php echo $r->teller_account ?></td>
											<td><?php echo $r->amount ?></td>
											<td><?php echo $r->Firstname." ". $r->Lastname ?></td>
											<td><?php echo $r->sd ?></td>
											<td><?php echo $r->status ?></td>
											<td><a class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this?')" href="<?php echo base_url('vault_cashier_pends/delete/').$r->cvpid?>"><i class="fa fa-trash"></i>Delete</a></td>
										</tr>
										<?php
									}
									?>

									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>

	</div>
</div>
