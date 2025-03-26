<?php
$linkk = base_url('admin_assets/images/pattern.png');
$imgg = 'data:image;base64,'.base64_encode(file_get_contents($linkk))
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head>
    <style>

        p {
            text-align: justify;
            margin:0;
        }
        table {width:100%;}
        table.collapse {
            border-collapse: collapse;
        }

        tr td, tr th {
            text-align: right;
        }

        tr.total {
            font-weight: 900;
        }
        hr {
            margin: 15px 0;
        }
        h1 {
            margin:0;
        }
        .title {
            color: #000;
            font-size: 18px;
            font-weight: normal;
        }

        .section {
            border-bottom: 1px #D4D4D4 solid;
            padding: 10px 0;
            margin-bottom: 20px;
        }

        .section .content {
            margin-left: 10px;
        }

        #hor-minimalist-b
        {
            font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
            font-size: 12px;
            background: #fff;
            width: 480px;
            border-collapse: collapse;
            text-align: center;
        }
        #hor-minimalist-b th
        {
            font-size: 14px;
            font-weight: 900;
            padding: 10px 8px;
            border-bottom: 2px solid #000;
            text-align: center;
        }
        #hor-minimalist-b td
        {
            border-bottom: 1px solid #ccc;
            padding: 6px 8px;
        }

        #pattern-style-a
        {
            font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
            font-size: 12px;
            width: 100%;
            text-align: left;
            border-collapse: collapse;
            background: url('<?php echo $imgg; ?>');;
        }

        #pattern-style-a th
        {
            font-size: 13px;
            font-weight: normal;
            padding: 8px;
            border-bottom: 1px solid #fff;
            color: #039;
        }
        #pattern-style-a td
        {
            padding: 3px;
            border-bottom: 1px solid #fff;
            color: #000;
            border-top: 1px solid transparent;
        }
        #pattern-style-a tbody tr:hover td
        {
            color: #339;
            background: #fff;
        }

        * {
            box-sizing: border-box;
        }

        html {
            font-family: sans-serif;
        }


    </style>

</head><body>



<div class="section">
    <div class="content">
        <h1 style="text-align: center;"><?php
            $settings = get_by_id('settings','settings_id','1');
            echo $settings->company_name ?></h1>
        <table width="100%">
            <?php

            $link = base_url('uploads/').$settings->logo;
            $img = 'data:image;base64,'.base64_encode(file_get_contents($link))
            ?>
            <tr>
                <td style="float: left;padding-right: 5em; margin-left: 1em;">
                    <img src="<?php echo $img; ?>" alt="">
                </td>
                <td style="float: right;margin-left: 5em;">
                    <?php echo $settings->address ?>
                    <?php echo $settings->company_email ?>/<?php echo $settings->phone_number ?>
                </td>
            </tr>
        </table>
        <hr>
        <h2 style="text-align: center;">Loan Summary</h2>

   
    </div>
</div>

<div class="section">
    <div class="title">Summary</div>
    <br>
    <div class="content">
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
                    <td <?php echo $css; ?>>MK<?php  echo number_format($p->amount,2) ?></td>
                    <td <?php echo $css; ?>>MK<?php if($arrear){ $arrears_total +=$p->amount; echo number_format($p->amount,2);}else{} ?></td>
                    <td <?php echo $css; ?>><?php if($arrear){     echo $days_diff ;}else{} ?> Days</td>
                    <td <?php echo $css; ?>>MK<?php if($arrear){
                            $penalty_total +=$late_fee_amount;

                            echo number_format($late_fee_amount,2);

                        }else{} ?></td>
                    <td <?php echo $css; ?>>MK<?php if ($arrear){ echo number_format(($p->amount+$penalty)+($oustanding-$p->paid_amount),2); }else{}?></td>
                    <td <?php echo $css; ?>>MK<?php $amount_paid_total += $p->paid_amount; echo number_format($p->paid_amount,2)?></td>
                    <td <?php echo $css; ?>>MK<?php echo number_format($variance,2); ?></td>

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
                <th>AMOUNT IN MWK</th>
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
    </div>
</div>
<div style="margin: auto"><strong>********** NOTHING FOLLOWS **********</strong></div>


</body></html>
