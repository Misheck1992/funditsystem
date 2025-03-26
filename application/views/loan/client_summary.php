<?php
$next_payment_details = $this->Payement_schedules_model->get_next($next_payment_id,$loan_id);
$currency = get_by_id('currencies','currency_id',$currency);
?>
<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Loan view</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">Loan Details</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
            <div class="row">
                <div class="col-lg-3 border-right">
                    <h2>Loan Info</h2>
                    <hr>
                    <table>
                        <tr>
                            <td style="text-align: right;padding-right: 10px;">Loan #</td>
                            <td><?php echo $loan_number?></td>
                        </tr>
                        <tr>
                            <td style="text-align: right;padding-right: 10px;">Loan Type</td>
                            <td><?php echo $loan_product?></td>
                        </tr>
                        <tr >
                            <td style="text-align: right;padding-right: 10px;">Loan Customer</td>
                            <td><a href="<?php echo base_url($preview_url).$customer_id?>"><?php echo $loan_customer?></a></td>
                        </tr>
                        <tr>
                            <td style="text-align: right;padding-right: 10px;">Loan Status</td>
                            <td><?php echo $loan_status?></td>
                        </tr>

                        <?php
                        if($customer_type=='institution'){

                            $inst = get_by_id('corporate_customers','id',$off_taker);
                            if($inst){
                                $customer_name = $inst->EntityName.' - '.$inst->RegistrationNumber.' ('.$inst->	entity_type.')';
                                $preview_url = "Corporate_customers/read/";



                        ?>
                        <tr >
                            <td style="text-align: right;padding-right: 10px;">Off Taker</td>
                            <td><a href="<?php 

                                echo base_url($preview_url). $inst->id ?>"><?php echo  $customer_name ?></a></td>
                        </tr>
                        <?php
                            }
                        }

                        ?>


                       
                    </table>
                    <br>

                </div>
                <div class="col-lg-9 border-right">
                    <h2>Overview</h2>
                    <hr>
                    <div class="double-scroll">
                    <table style="width: 100%;border-collapse: collapse;"     id="data-table-cs" >
                        <thead   >
                        <tr style="border: 1px solid black;">

                            <th>Month</th>
                            <th>Payment Amount</th>
                            <th>arrears Amount</th>
                            <th>Elapsed Days</th>
                            <th>Penalty 20% default</th>
                            <th>Loan Outstanding</th>
                            <th>Amount Paid</th>
                            <th>Variance</th>



                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $variance  = 0;
                        $penalty = 0;
                        $late_fee_amount = 0;
                        $arrears_total = 0;
                        $penalty_total = 0;
                        $amount_paid_total = 0;
                        foreach ($payments as $p){
                            ?>
                            <?php
                            //change color depending on it's status
                            $css = '';
                            $xstatus = '';
                            $arrear = false;
                            if($p->payment_schedule < date('Y-m-d')   AND $p->status == 'NOT PAID') {
                                $css = ' class="due"';
                                $xstatus = ' | OVER DUE';
                                $arrear = true;
                            } elseif($p->status=='PAID') {
                                $css = 'class="paid"';
                            } elseif($p->payment_schedule == date('Y-m-d')  AND $p->status == 'NOT PAID') {
                                $css = ' class="due_now"';
                                $xstatus = ' | DUE TODAY';
                            }
                            $currentDate = date('Y-m-d');
                            $real_scheduled_date = findFifthDayOfNextMonth($p->payment_schedule);
                            $days_diff = getDaysDifference($real_scheduled_date,$currentDate);

                            $oustanding = $p->amount + $variance +  $late_fee_amount;
                            $variance = $oustanding - $p->paid_amount;
                            $late_fee_amount = calculateLateFeeAmount($variance,$days_diff);
                            ?>
                            <!--							<tr style="border: 1px solid black;background-color: #F3D8D8;">-->
                            <tr style="font-weight: <?php echo !empty($xstatus)?'900':'200'; ?>;">

                                <td <?php echo $css; ?>><?php echo $p->payment_schedule;

                                    $date = new DateTime($p->payment_schedule);

                                    $outputDate = $date->format("M d");
                                    echo "($outputDate)";

                                    ?></td>
                                <td <?php echo $css; ?>><?php echo $currency->currency_code ?><?php  echo number_format($p->amount,2) ?></td>
                                <td <?php echo $css; ?>> <?php echo $currency->currency_code ?> <?php if($arrear){ $arrears_total +=$p->amount; echo number_format($p->amount,2);}else{} ?></td>
                                <td <?php echo $css; ?>><?php if($arrear){     echo $days_diff ;}else{} ?> Days</td>
                                <td <?php echo $css; ?>> <?php echo $currency->currency_code ?> <?php if($arrear){
                                        $penalty_total +=$late_fee_amount;

                                    echo number_format($late_fee_amount,2);

                                    }else{} ?></td>
                                <td <?php echo $css; ?>> <?php echo $currency->currency_code ?> <?php if ($arrear){ echo number_format(($p->amount+$penalty)+($oustanding-$p->paid_amount),2); }else{}?></td>
                                <td <?php echo $css; ?>> <?php echo $currency->currency_code ?> <?php $amount_paid_total += $p->paid_amount; echo number_format($p->paid_amount,2)?></td>
                                <td <?php echo $css; ?>> <?php echo $currency->currency_code ?> <?php echo number_format($variance,2); ?></td>

                                            </tr>
                            <?php
                            $penalty =$late_fee_amount;
                        }
                        ?>



                        </tbody>
                        <tfoot>
                        <tr style="border: 1px solid black;">

                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>

                        </tr>
                        <tr style="border: 1px solid black;">

                            <th>GRAND TOTAL</th>
                            <th></th>
                            <th><?php  echo  number_format($arrears_total,2)?></th>
                            <th></th>
                            <th><?php  echo  number_format($penalty_total,2)?></th>
                            <th></th>
                            <th><?php  echo  number_format($amount_paid_total,2)?></th>
                            <th></th>



                        </tr>
                        <tr style="border: 1px solid black;">

                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>

                        </tr>
                        <tr style="border: 1px solid black;">

                            <th>OUTSTANDING BALANCE</th>
                            <th></th>
                            <th>AMOUNT IN ZMW</th>
                            <th></th>
                            <th>AMOUNT IN MWK</th>
                            <th></th>
                            <th></th>
                            <th></th>



                        </tr>
                        <tr style="border: 1px solid black;">

                            <th>ARREARS DUE + PENALTY DUE</th>
                            <th></th>
                            <th><?php echo number_format($arrears_total+$penalty_total,2)?></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>



                        </tr>
                        <tr style="border: 1px solid black;">

                            <th>LESS AMOUNT PAID</th>
                            <th></th>
                            <th><?php echo number_format($amount_paid_total,2)?></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>



                        </tr>
                        <tr style="border: 1px solid black;">

                            <th>DEFAULT AMOUNT PAID</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th><?php  echo  number_format(($arrears_total+$penalty_total)-$amount_paid_total, 2); ?></th>
                            <th></th>
                            <th></th>
                            <th></th>



                        </tr>
                        <tr style="border: 1px solid black;">

                            <th>ADD PENALTY 5 % DEFAULT</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>

                        </tr>
                        </tfoot>
                    </table>
                    </div>
                    <table class="table">
                        <tr>
                            <td>Prepared By:</td>
                            <td>_________________________________________</td>
                        </tr>
                        <tr>
                            <td>DATE:</td>
                            <td>_________________________________________</td>
                        </tr>
                        <tr>
                            <td>Signature:</td>
                            <td>_________________________________________</td>
                        </tr>
                    </table>
                    <br><br>
                    <table class="table table-data">
                        <tr>
                            <td>Reviewed By:</td>
                            <td>_________________________________________</td>
                        </tr>
                        <tr>
                            <td>DATE:</td>
                            <td>_________________________________________</td>
                        </tr>
                        <tr>
                            <td>Signature:</td>
                            <td>_________________________________________</td>
                        </tr>
                    </table>
                    <hr>

                </div>
                <div class="col-lg-1">
                    <a href="<?php echo base_url('loan/report_client_summary/').$loan_id ?>" style="color: red;"><i class="fa fa-file-pdf fa-2x"></i>Report</a>
                    <button class="btn btn-sm btn-primary rounded-0 btn-primary bg-gradient bg-primary" id="exportclient"><i class="fa fa-file-excel fa-2x"></i>Export </button>

                    <hr>


                </div>
            </div>

        </div>
    </div>

</div>


