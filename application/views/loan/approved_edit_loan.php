<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">All Loan Applications</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">All loans Applied</span>
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
                        if(!empty($indi)) {
                            $customer_name = $indi->Firstname . ' ' . $indi->Lastname;
                            $preview_url = "Individual_customers/view/";
                        }
                    }
                    ?>
                    <tr>

                        <td><?php echo $n ?></td>
                        <td><?php echo $loan->loan_number ?></td>
                        <td><?php echo $loan->product_name ?></td>
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
                        <td>
                         <a href="<?php echo base_url('loan/update_edit_loan/').$loan->loan_id?>" class="btn btn-primary btn-sm btn-block"  onclick="confirm('Are you sure you want to approve to edit loan number?')">Approve</a>
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
