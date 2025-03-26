<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">All Loan Imported</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">All loans Imported</span>
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

            </div>
            <br>
            <hr>
            <div style="overflow-y: auto"" >
            <table  id="data-table" class="tableCss">
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


                    <th>Loan Status</th>
                    <th>Loan Added Date</th>


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
                        if(!empty($indi)) {
                            $customer_name = $indi->Firstname . ' ' . $indi->Lastname;
                            $preview_url = "Individual_customers/view/";
                        }
                    }
                    ?>
                    <tr>

                        <td><?php echo $n ?></td>
                        <td><?php echo $loan->loan_number ?></td>
                        <td><?php
                             $indi = $this->Loan_products_model->get_by_id($loan->loan_product);
                        if(!empty($indi)) {
                            echo $indi->product_name;
                        }

                            ?></td>
                        <td><a href="<?php echo base_url($preview_url).$loan->loan_customer?>"><?php echo $customer_name?></a></td>
                        <!--			<td><a href="--><?php //echo base_url('individual_customers/view/').$loan->id?><!--"">--><?php //echo $loan->Firstname." ".$loan->Lastname?><!--</a></td>-->
                        <td><?php echo $loan->loan_date ?></td>
                        <td>MK<?php echo number_format($loan->loan_principal,2) ?></td>
                        <td><?php echo $loan->loan_period ?></td>
                        <td><?php echo $loan->period_type ?></td>
                        <td><?php echo $loan->loan_interest ?>%</td>
                        <td>MK<?php echo number_format($loan->loan_amount_total,2) ?></td>



                        <td><?php echo $loan->loan_status ?></td>
                        <td><?php echo $loan->loan_added_date ?></td>

                    </tr>
                    <?php
                    $n ++;
                }
                ?>
                </tbody>
                <thead>
                <tr>

                    <th colspan="12">



                        <a href="<?php echo base_url('loan/mass_repayments_process_data')?>" class="btn btn-primary btn-sm btn-block"  onclick="confirm('Are you sure you want to process  all imported loans?')">Process All loans</a>
                    </th>


                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
</div>
