<?php

?>
<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Summary</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">Client summary</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
            <form action="<?php echo base_url('reports/client_summary') ?>" method="get">
                <fieldset>
                    <legend>Report filter</legend>
                    <div id="controlgroup">

                        Loan Number:    <input type="text"  name="loannumber" value=""  placeholder="Copy paste loan number here">
                        <button type="submit" name="search" value="filter">Filter</button>

                        <button  id="exportTableClientSummary" value="excel"><i class="fa fa-file-excel text-success"></i></button>
                    </div>
                </fieldset>
            </form>
            <hr>
            <p>Search results</p>
            <table  id="resultclientsummary" class="table">
                <thead>
                <tr>


                    <th>client name </th>
                    <th colspan="2">

                        <?php


                        $r=get_by_id('loan','loan_number',$loannumber );
                        if( $r->customer_type=='group'){
                            $custgroup=get_by_id('groups','group_id',$r->loan_customer );
                            if(!empty($custgroup)){
                                echo  $custgroup->group_name;
                            }

                        }
                        else {
                            $cust=get_by_id('individual_customers','id',$r->loan_customer );
                            if(!empty($cust)){
                                echo  $cust->Firstname."   ".$cust->Lastname ;
                            }



                        }

                        ?>
                    </th>
                    <th>Loan number </th>
                    <th colspan="2">

                        <?php

                        echo $loannumber;
                        ?>
                    </th>



                </tr>

                <tr>


                    <th>Date</th>
                    <th>ARREARS DUE</th>
                    <th>PENALTY 20% DEFAULT</th>
                    <th>LOAN OUSTANDING</th>
                    <th>AMOUNT PAID</th>
                    <th>VARIANCE</th>



                </tr>
                </thead>
                <tbody>


                <?php
                $n = 1;
                foreach ($loan_data as $l){
                    ?>
                    <tr>

                        <td><?php echo $l->payment_schedule;

                            $date = new DateTime($l->payment_schedule);

                            $outputDate = $date->format("M d");
                            echo "($outputDate)";

                            ?></td>


                        <td><?php echo $l->amount;?></td>
                        <td></td>
                        <td><?php echo $l->loan_balance;?></td>
                        <td><?php echo $l->paid_amount;?></td>
                        <td><?php echo $l->amount-$l->paid_amount;?></td>

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
