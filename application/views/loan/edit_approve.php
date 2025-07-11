<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">All Edit approval request</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">All requests</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
            <div style="overflow-y: auto"">
            <?php
            $loand = get_all_where('approval_edits','type = "Loan edit" AND state ="recommended"','approval_edits_id','DESC');


            ?>
            <table  id="data-table" class="table">
                <thead>
                <tr>

                    <th>#</th>
                    <th>Loan Number</th>
                    <!--                    <th>Loan Product</th>-->
                    <th>Loan Customer</th>
                    <th>Loan Date</th>
                    <th>Loan Principal</th>
                    <th>Loan Period</th>
                    <!--		<th>Period Type</th>-->
                    <th>Loan Interest</th>

                    <th>Loan Amount Total</th>
                    <th>Loan File</th>
                    <th>Loan Status</th>
                    <th>Loan Edit Status</th>
                    <th>Action</th>


                </tr>
                </thead>
                <tbody><?php
                $n =1;
                foreach ($loand as $loans)
                {
                    $loan = get_by_id('loan','loan_id',$loans->id);
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

                        <td><?php echo $n ?></td>
                        <td><?php echo $loan->loan_number ?></td>
                        <!--                        <td>--><?php //echo $loan->product_name ?><!--</td>-->
                        <td><a href="<?php echo base_url($preview_url).$loan->loan_customer?>""><?php echo $customer_name?></a></td>
                        <td><?php echo $loan->loan_date ?></td>
                        <td>MK<?php echo number_format($loan->loan_principal,2) ?></td>
                        <td><?php echo $loan->loan_period ?></td>
                        <!--			<td>--><?php //echo $loan->period_type ?><!--</td>-->
                        <td><?php echo $loan->loan_interest ?>%</td>
                        
                        <td>MK<?php echo number_format($loan->loan_amount_total,2) ?></td>

                        <td><a href="<?php echo base_url('uploads/').$loan->worthness_file?>" download >Download file <i class="fa fa-download fa-flip"></i></a></td>

                        <td><?php echo $loan->loan_status ?></td>
                        <td><?php echo $loans->state ?></td>


                        <td width="500">
                            <a href="<?php echo base_url('loan/view/').$loan->loan_id?>" class="btn btn-sm btn-info">View loan</a>
                            <a href="<?php echo base_url('Approval_general/auth_data/').$loans->approval_edits_id."/edit_recommend/edit_approve";  ?>" class="btn btn-sm btn-warning">Approve/Reject</a>
                        </td>

                    </tr>
                    <?php
                    $n++;
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
<div aria-hidden="true" class="onboarding-modal modal fade" id="recommendgeneral" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg modal-centered" role="document">
        <div class="modal-content text-center">
            <button style="float: right;" aria-label="Close" class="close" data-dismiss="modal" type="button"><span class="close-label">Close</span><span class="anticon anticon-close"></span></button>
            <div class="onboarding-content" style="padding: 1em;">
                <h4 class="onboarding-title">Are you sure you want to recommend this?</h4>

                <form action="<?php echo base_url('Approval_general/recommend')?>" method="post" class="form-row" >
                    <input type="text" name="id" id="edit_id"  hidden required>
                    <div class="form-group col-12">
                        <label for="officer">Search New officer</label><br/>
                        <textarea name="comment" id="" cols="30" rows="10" class="form-control" placeholder="write comment"></textarea>
                    </div>
                    <div class="form-group col-12">
                        <button type="submit" class="btn btn-primary">Send Changes</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
