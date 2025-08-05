<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Loan Delete Approve requests</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">All  delete request to approval</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
            <div style="overflow-y: auto"">
            <form name="frmrecommend" method="post" action="">
                <table  id="data-table" class="tableCss">
                    <thead>
                    <tr>
                        <th><input type="checkbox" id="cc" onclick="javascript:checkAll(this)"/>
                            <label for="cc">Check All</label></th>
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
                        <th>Loan File</th>
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
                            $customer_name = $indi->Firstname.' '.$indi->Lastname;
                            $preview_url = "Individual_customers/view/";
                        }
                        ?>
                        <tr>
                            <td><input type="checkbox" name="loans[]" value="<?php  echo $loan->loan_id ?>"> </td>
                            <td><?php echo $n ?></td>
                            <td><?php echo $loan->loan_number ?></td>
                            <td><?php echo $loan->product_name ?></td>
                            <td><a href="<?php echo base_url($preview_url).$loan->loan_customer?>""><?php echo $customer_name?></a></td>
                            <!--			<td><a href="--><?php //echo base_url('individual_customers/view/').$loan->id?><!--"">--><?php //echo $loan->Firstname." ".$loan->Lastname?><!--</a></td>-->
                            <td><?php echo $loan->loan_date ?></td>
                            <td>MK<?php echo number_format($loan->loan_principal,2) ?></td>
                            <td><?php echo $loan->loan_period ?></td>
                            <td><?php echo $loan->period_type ?></td>
                            <td><?php echo $loan->loan_interest ?>%</td>
                            <td>MK<?php echo number_format($loan->loan_amount_total,2) ?></td>

                            <td><a href="<?php echo base_url('uploads/').$loan->worthness_file?>" download >Download file <i class="fa fa-download fa-flip"></i></a></td>

                            <td><?php echo $loan->loan_status ?></td>
                            <td><?php echo $loan->loan_added_date ?></td>

                            <td width="600px"><a href="<?php echo base_url('loan/view/').$loan->loan_id.'?action=delete_approve' ?>" class="btn btn-sm btn-success">View/Approve Delete</a>
                            </td>


                        </tr>
                        <?php
                        $n ++;
                    }
                    ?>
                    </tbody>
                </table>
                <input type="text" name="minutes" id="minutes1" hidden>
                <input type="button" name="Approve" value="Approve all"  class="btn btn-danger"  /> <input type="button" name="Reject" value="Reject"  class="btn btn-warning" />
            </form>
        </div>

    </div>
</div>
</div>
