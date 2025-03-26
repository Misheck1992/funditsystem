<?php

?>
<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">All transactions</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">All transactions</span>
            </nav>
        </div>
    </div>
    <div class="card">



        <table  id="data-table" class="table">
            <thead>
            <tr>

                <th>#</th>
                <th> Reference Number</th>
                <th>credit
                </th>
                <th>Debit</th>
                <th>Balance</th>
                <th>Date </th>
                <th>Action </th>




            </tr>
            </thead>
            <tbody>
            <?php
            $loannumber="";
            $n = 1;
            foreach ($loan_data as $ll) {


                $loanNd = get_by_id('loan', 'loan_id', $ll->loan_id);
                $loandata = get_all_by_id('transaction', 'account_number', $loanNd->loan_number);

                foreach ($loandata as $l) {
                    ?>
                    <tr>
                        <td><?php echo $n; ?></td>
                        <td><?php echo $l->transaction_id; ?></td>

                        <td><?php echo $l->credit; ?></td>
                        <td><?php echo $l->debit; ?></td>
                        <td><?php echo $l->balance; ?></td>
                        <td><?php echo $l->system_time; ?></td>
                        <td><?php


                            $loandata = get_by_id('loan', 'loan_number', $l->account_number);

                            if (!empty($loandata)) {
                                echo anchor(site_url('tellering/update_approve_delete/' . $loandata->loan_id), 'Approve', 'onclick="javasciprt: return confirm(\'Are You Sure you want Approve deletion of these payments?\')"');
                            } else {
                                echo "No transaction was made";
                            }
                            ?></td>


                    </tr>

                    </tr>

                    <?php

                    $loannumber = $l->account_number;
                }
                $n++;
            }
            ?>
            </tbody>


        </table>
    </div>
</div>
</div>
