<div class="content-i">
	<div class="content-box">
		<div class="row">
			<div class="col-lg-12">
				<div class="element-wrapper">
					<h4 class="element-header">Cash Operations- Vault to Cashier- acceptance</h4>
					<div class="element-box">
						<div class="row">
							<div class="col-lg-2"></div>
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
											<td><a class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to accept this?')" href="<?php echo base_url('Accounts/accept/').$r->cvpid?>"><i class="fa fa-check"></i>Accept</a><a class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want reject this')" href="<?php echo base_url('Cashier_vault_pends/reject/').$r->cvpid?>"><i class="fa fa-recycle"></i>Reject</a></td>
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
