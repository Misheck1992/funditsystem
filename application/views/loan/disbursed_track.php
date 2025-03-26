<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">All Disbursed Loans</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">All loans Disbursed</span>
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
                <form action="<?php echo base_url('loan/disbursed_loans') ?>" method="get">
                    Product: <select name="product" id="" class="select2">
                        <option value="All">All Products</option>
                        <?php

                        foreach ($products as $product){
                            ?>
                            <option value="<?php  echo $product->loan_product_id; ?>"><?php echo $product->product_name; ?></option>
                            <?php
                        }
                        ?>
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
                    <input type="date" name="from"> Date to: <input type="date" name="to"> <input type="submit" value="filter" name="search"><input type="submit" value="export excel" name="search"><input type="submit" name="search" value="export pdf">
                </form>
            </div>
            <br>
            <hr>
            <div style="overflow-y: auto"">
            <table  id="data-table" class="tableCss">
                <thead>
                <tr>


                    <th>#</th>
                    <th>Branch</th>
                    <th>Account Number</th>
                    <th>Account Name</th>
                    <th>Bank Name</th>
                    <th>Customer Name</th>
                    <th>Loan product</th>
                    <th>Transaction Amount</th>
                    <th>Date</th>
                    <th>Payments/Receipts</th>
                    <th>Narrative</th>

                </tr>
                </thead>
                <tbody>
                <?php
                $n = 1;
                if(!empty($loan_data)){


                    foreach ($loan_data as $l){
                        ?>
                        <tr>
                            <td><?php echo $n;?></td>
                            <td><?php //echo $n;?></td>

                            <td>
                                <?php
                                if($l->customer_type=='group'){
                                    $group = $this->Groups_model->get_by_id($l->loan_customer);

                                    $customer_name = $group->group_name.'('.$group->group_code.')';
                                    $preview_url = "Customer_groups/members/";
                                }elseif($l->customer_type=='individual'){
                                    $indi = $this->Individual_customers_model->get_by_id($l->loan_customer);
                                    if(!empty($indi)) {
                                        $customer_name = $indi->Firstname . ' ' . $indi->Lastname;
                                        $preview_url = "Individual_customers/view/";
                                    }
                                }
                                elseif($l->customer_type=='institution'){
                                    $inst = get_by_id('corporate_customers','id',$l->loan_customer);
                                    $customer_name = $inst->EntityName.' - '.$inst->RegistrationNumber.' ('.$inst->	entity_type.')';
                                    $preview_url = "Corporate_customers/read/";
                                }
                                ?>
                            </td>

                            <td><?php //echo $n;?></td>
                            <td><?php //echo $n;?></td>
                            <td><a href="<?php echo base_url($preview_url).$l->loan_customer?>""><?php echo $customer_name?></a></td>
                           <td><?php echo  $l->product_name;?></td>
                            <td><?php echo $l->disbursed_amount;?></td>
                            <td><?php echo $l->	disbursed_date;?></td>
                            <td><?php //echo $l->	disbursed_date;?></td>
                            <td><?php //echo $l->	disbursed_date;?></td>


                        </tr>

                        <?php
                        $n ++;
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
