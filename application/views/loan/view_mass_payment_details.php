<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">All Loan repayments details</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">All repayments details</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
            <div>
                <?php
                $products = get_all_by_id('mass_repayment_requests_details','mass_repayment_request',$this->uri->segment(3));
                
                ?>

            </div>

            <div class="double-scroll">
                <table  id="data-table" class="table">
                    <thead>
                    <tr>

                        <th>#</th>
                        <th>loan_number</th>
                        <th>amount</th>

                        <th>Payment Date</th>


                        <th>Status</th>

                    </tr>
                    </thead>
                    <tbody><?php
                    $n = 1;

                    foreach ($products as $loan)
                    {


                        ?>
                        <tr >

                            <td><?php echo $n ?></td>
                            <td><?php echo $loan->loan_number ?><?php if($loan->loan_id=="error"){echo "<font color='red'>Loan number does not exists</font>";} ?></td>
                            <td><?php echo $loan->amount ?></td>
                            <td><?php echo $loan->payment_date ?></td>
                            <td><?php echo $loan->status ?></td>


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
