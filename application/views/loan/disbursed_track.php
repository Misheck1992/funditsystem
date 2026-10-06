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
                    <input type="date" name="from" value="<?php echo html_escape($this->input->get('from')); ?>"> Date to: <input type="date" name="to" value="<?php echo html_escape($this->input->get('to')); ?>"> <button type="submit" value="filter" name="search" class="btn btn-primary">Filter</button> <button type="submit" value="excel" name="search" class="btn btn-success">Export Excel</button> <button type="submit" value="pdf" name="search" class="btn btn-danger">Export PDF</button>
                </form>
            </div>
            <br>
            <hr>
            <div style="overflow-y: auto">
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
                        $customer_name = 'Unknown customer';
                        $preview_url = '';
                        $branch_reference = null;
                        if ($l->customer_type == 'group') {
                            $customer = $this->Groups_model->get_by_id($l->loan_customer);
                            if ($customer) {
                                $customer_name = $customer->group_name . ' (' . $customer->group_code . ')';
                                $preview_url = 'Customer_groups/members/';
                                $branch_reference = $customer->Branch ?? null;
                            }
                        } elseif ($l->customer_type == 'individual') {
                            $customer = $this->Individual_customers_model->get_by_id($l->loan_customer);
                            if ($customer) {
                                $customer_name = trim($customer->Firstname . ' ' . $customer->Lastname);
                                $preview_url = 'Individual_customers/view/';
                                $branch_reference = $customer->Branch ?? null;
                            }
                        } elseif ($l->customer_type == 'institution') {
                            $customer = get_by_id('corporate_customers', 'id', $l->loan_customer);
                            if ($customer) {
                                $customer_name = $customer->EntityName . ' - ' . $customer->RegistrationNumber;
                                $preview_url = 'Corporate_customers/read/';
                                $branch_reference = $customer->Branch ?? null;
                            }
                        }
                        $bank = $this->Bank_model->check($l->loan_customer);
                        ?>
                        <tr>
                            <td><?php echo $n; ?></td>
                            <td><?php echo html_escape($l->branch_name ?? 'Not assigned'); ?></td>
                            <td><?php echo html_escape($bank->account_number ?? '-'); ?></td>
                            <td><?php echo html_escape($bank->account_name ?? '-'); ?></td>
                            <td><?php echo html_escape($bank->bank_name ?? '-'); ?></td>
                            <td><?php if ($preview_url !== '') { ?><a href="<?php echo base_url($preview_url) . $l->loan_customer; ?>"><?php echo html_escape($customer_name); ?></a><?php } else { echo html_escape($customer_name); } ?></td>
                            <td><?php echo html_escape($l->product_name ?? '-'); ?></td>
                            <td><?php echo number_format((float) ($l->disbursed_amount ?? 0), 2); ?></td>
                            <td><?php echo html_escape($l->disbursed_date ?? '-'); ?></td>
                            <td><?php echo html_escape($l->loan_number ?? '-'); ?></td>
                            <td><?php echo html_escape($l->narration ?? '-'); ?></td>
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
