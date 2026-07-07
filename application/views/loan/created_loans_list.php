<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">Created Loans (Awaiting Completion)</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">Loans</a>
				<span class="breadcrumb-item active">Created Loans</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">

			<div class="alert alert-info mb-4" style="background-color: #d1ecf1; border-color: #bee5eb; border-radius: 8px;">
				<div class="d-flex align-items-center">
					<i class="fas fa-info-circle" style="font-size: 24px; color: #0c5460; margin-right: 15px;"></i>
					<div>
						<h5 class="alert-heading mb-2" style="color: #0c5460; font-weight: 600;">
							<i class="fas fa-clipboard-list"></i> API-Created Loans
						</h5>
						<p class="mb-0" style="color: #0c5460;">
							These loans were created via the API and require completion before entering the approval workflow. Click <strong>"Complete"</strong> to fill in missing details (appraisal, documents, bank statements, collaterals).
						</p>
					</div>
				</div>
			</div>

            <div style="overflow-y: auto">
            <table id="data-table1" class="tableCss">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Loan Number</th>
                    <th>Loan Product</th>
                    <th>Loan Customer</th>
                    <th>Loan Date</th>
                    <th>Loan Principal</th>
                    <th>Loan Period</th>
                    <th>Interest</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Date Created</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody><?php
                $n = 1;

                foreach ($loan_data as $loan)
                {
                    if($loan->customer_type=='group'){
                        $group = $this->Groups_model->get_by_id($loan->loan_customer);
                        $customer_name = $group->group_name.'('.$group->group_code.')';
                        $preview_url = "Customer_groups/members/";
                    }elseif($loan->customer_type=='individual'){
                        $indi = $this->Individual_customers_model->get_by_id($loan->loan_customer);
                        $customer_name = $indi->Firstname.' '.$indi->Lastname;
                        $preview_url = "Individual_customers/view/";
                    }
                    elseif($loan->customer_type=='institution'){
                        $inst = get_by_id('corporate_customers','id',$loan->loan_customer);
                        $customer_name = $inst->EntityName.' - '.$inst->RegistrationNumber;
                        $preview_url = "Corporate_customers/read/";
                    }
                    $currency = get_by_id('currencies','currency_id',$loan->currency);
                    ?>
                    <tr>
                        <td><?php echo $n ?></td>
                        <td><?php echo $loan->loan_number ?></td>
                        <td><?php echo $loan->product_name ?></td>
                        <td><a href="<?php echo base_url($preview_url).$loan->loan_customer?>"><?php echo $customer_name?></a></td>
                        <td><?php echo $loan->loan_date ?></td>
                        <td><?php echo $currency->currency_code ?> <?php echo number_format($loan->loan_principal,2) ?></td>
                        <td><?php echo $loan->loan_period ?> <?php echo $loan->period_type ?></td>
                        <td><?php echo $loan->loan_interest ?>%</td>
                        <td><?php echo $currency->currency_code ?> <?php echo number_format($loan->loan_amount_total,2) ?></td>
                        <td><span class="badge badge-warning">CREATED</span></td>
                        <td><?php echo $loan->loan_added_date ?></td>
                        <td>
                            <a href="<?php echo base_url('Loan/complete_loan/').$loan->loan_id?>" class="btn btn-sm btn-success">
                                <i class="fa fa-pencil"></i> Complete
                            </a>
                        </td>
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
