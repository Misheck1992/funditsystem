<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">All Loan approved</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">-</a>
				<span class="breadcrumb-item active">All loans approved</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
		    <div>
                <?php
                $products = get_all('loan_products');
                $officer = get_all('employees');

                ?>
                <form action="<?php echo base_url('loan/track') ?>" method="get">
                    Product: <select name="product" id="" class="select2">
                        <option value="All">All Products</option>
                        <?php

                        foreach ($products as $product){
                            ?>
                            <option value="<?php  echo $product->loan_product_id; ?>"><?php echo $product->product_name; ?></option>
                            <?php
                        }
                        ?>
                    </select> Status: <select name="status" id="">
                       
                       
                        <option value="APPROVED">APPROVED</option>
                       
                    </select>  Officer: <select name="user" id="" class="select2">
                        <option value="All">All officers</option>
                        <?php

                        foreach ($officer as $item){
                            ?>
                            <option value="<?php  echo $item->id; ?>"><?php echo $item->Firstname." ".$item->Lastname?></option>
                            <?php
                        }
                        ?>
                    </select> Date from:
                    <input type="date" name="from"> Date to: <input type="date" name="to">
					<input type="submit" value="filter" name="search">
                </form>
            </div>
            <br>
            <hr>
            <div style="overflow-y: auto"">
          
			<table  id="data-table1" class="tableCss">
				<thead>
				<tr>

                    <th>#</th>
                    <th>Loan Number</th>
                    <th>Loan Product</th>
                    <th>Loan Customer</th>
                    <th>Loan Date</th>
                    <th>Loan Principal</th>
                    <th>Loan Period</th>
                    <th>Period Type</th>
                    <th>Loan Interest</th>
                    <th>Loan Amount Total</th>
                    <th>Loan Disburse amount</th>
                    <th>Loan File</th>
                    <th>Loan Status</th>
                    <th>Loan Added Date</th>
                    <th>Action</th>

				</tr>
				</thead>
				<tbody><?php
				$n = 1;

				$mandate_fees = FALSE;
				foreach ($loan_data as $loan)
				{
					$pays = FALSE;
					$paid = get_by_id('charges','charge_id','1');

						$mandate_fees = TRUE;
						$get_transactions = check_paid_fees($loan->loan_id);

						if(!empty($get_transactions)){
							$pays = TRUE;

						}

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
                        $customer_name = $inst->EntityName.' - '.$inst->RegistrationNumber.' ('.$inst->	entity_type.')';
                        $preview_url = "Corporate_customers/read/";
                    }
                    $currency = get_by_id('currencies','currency_id',$loan->currency);
					?>
					<tr>

                        <td><?php echo $n ?></td>
                        <td><?php echo $loan->loan_number ?></td>
                        <td><?php echo $loan->product_name ?></td>
                        <td><a href="<?php echo base_url($preview_url).$loan->loan_customer?>""><?php echo $customer_name?></a></td>
                        <!--			<td><a href="--><?php //echo base_url('individual_customers/view/').$loan->id?><!--"">--><?php //echo $loan->Firstname." ".$loan->Lastname?><!--</a></td>-->
                        <td><?php echo $loan->loan_date ?></td>
                        <td><?php echo $currency->currency_code ?><?php echo number_format($loan->loan_principal,2) ?></td>
                        <td><?php echo $loan->loan_period ?></td>
                        <td><?php echo $loan->period_type ?></td>
                        <td><?php echo $loan->loan_interest ?>%</td>
                        <td><?php echo $currency->currency_code ?><?php echo number_format($loan->loan_amount_total,2) ?></td>
                        <td><?php echo $currency->currency_code ?><?php echo number_format($loan->loan_principal-$loan->disbursed_amount,2) ?></td>

                        <td><a href="#" onclick="get_loan_files('<?php  echo $loan->loan_id ;?>')" >Download file <i class="fa fa-download fa-flip"></i></a></td>

                        <td><?php echo $loan->loan_status ?></td>
                        <td><?php echo $loan->loan_added_date ?></td>
						<td width="250">

								<a class="btn btn-sm btn-danger" href="#" onclick="disburse_loan_charge_pre_paid('<?php echo $loan->loan_id; ?>','<?php echo $loan->loan_date ?>')">Disburse (processing fee charged)</a>
						

						<a href="<?php echo base_url('loan/view/').$loan->loan_id?>" class="btn btn-sm btn-info">View loan</a>
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
