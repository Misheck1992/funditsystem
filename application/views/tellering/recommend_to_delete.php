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
        <div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
            <form action="<?php echo base_url('Tellering/track_transaction') ?>" method="POST">
                <fieldset>
                    <legend>Transactions history</legend>


                    Loan Number:<input type="text"  name="loannumber" value=""  placeholder="Copy paste loan number here">
                    <button type="submit" name="search" value="filter">Filter</button>

        </div>
        </fieldset>
        </form>


        </form>

        </div>

        <hr>
        <p>Search results</p>
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




            </tr>
            </thead>
            <tbody>
            <?php
            $loannumber="";
            $n = 1;
            foreach ($loan_data as $l){
                ?>
                <tr>
                    <td><?php echo $n;?></td>
                    <td><?php echo $l->transaction_id;?></td>

                    <td><?php echo $l->credit;?></td>
                    <td><?php echo $l->debit;?></td>
                    <td><?php echo $l->balance;?></td>
                    <td colspan="2"><?php echo $l->system_time;?></td>

                </tr>

                <?php
                $n ++;
                $loannumber=$l->account_number;
            }

            ?>
            <tfoot><th> <?php


                $loandata=get_by_id('loan','loan_number',$loannumber );
          
                if(!empty($loandata)){
                echo anchor(site_url('tellering/update_recommend_delete/'.$loandata->loan_id),'Recommend','onclick="javasciprt: return confirm(\'Are You Sure you want Recommend deletion of these payments?\')"');
                 }else{
                    echo "No transaction was made";
                }
                ?>
            </th></tfoot>
            </tbody>
        </table>
    </div>
</div>
</div>
