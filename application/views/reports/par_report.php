<?php
$users = get_all('employees');
$products = get_all('loan_products');

?>
<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">All par report</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">All loans par report</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
            <form action="<?php echo base_url('reports/par_report') ?>" method="get">
                <fieldset>
                    <legend>Report filter</legend>
                    <div id="controlgroup">
                        loan number : <input type="text" name="loan_number" value="<?php  echo $this->input->get('loan_number'); ?>">
                        Officer:
                        <select name="officer">
                            <option value="">All Officers</option>
                            <?php
                            foreach ($users as $user){
                                ?>
                                <option value="<?php echo $user->id;?>" <?php if($user->id==$this->input->get('id')){echo 'selected';}  ?>><?php echo $user->Firstname." ".$user->Lastname;?></option>
                                <?php
                            }

                            ?>



                        </select>
                        Product:
                        <select name="product">
                            <option value="">All Products</option>
                            <?php
                            foreach ($products as $product){
                                ?>
                                <option value="<?php echo $product->loan_product_id;?>" <?php if($product->loan_product_id==$this->input->get('product')){echo 'selected';}  ?>><?php echo $product->product_name?></option>
                                <?php
                            }

                            ?>



                        </select>
                        <button type="submit" name="search" value="filter">Filter</button>
                    </div>
                </fieldset>
            </form>
            <hr>
            <div  class="frm_inputs" style="overflow-x:auto;">
                <div class="double-scroll">
                    <table id="resulta"  class="table" cellspacing="1">
                        <thead>
                        <tr>
                            <th>Loan #</th>
                            <th>Customer</th>
                            <th>Officer</th>
                            <th>Loan Products</th>
                            <th>Loan Amount Taken</th>
                            <th>Total Payments in arrears</th>

                            <th>Outstanding Principal</th>
                            <th>Outstanding Interest</th>
                            <th>Outstanding Balance</th>
                            <th>Aged 0-7 days</th>
                            <th>Aged 8-30 days</th>
                            <th>Aged 31-60 days</th>
                            <th>Aged 61-90 days</th>
                            <th>Aged 91-120 days</th>
                            <th>Aged 121-180 days</th>
                            <th>Aged 181-366 days</th>
                            <th>Aged 367+ days</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $tarrears=0;
                        $totalprincipal=0;
                        $tzerotoseven=0;
                        $morethanseven=0;
                        $morethanthirty=0;
                        $morethansixty=0;
                        $morethanninety=0;
                        $morethanonetwenty=0;
                        $morethanoneeighty=0;
                        $morethanthreesixty=0;
//                        $summary = $this->Loan_model->get_summaryu($this->session->userdata('officerid'));
                        $t_payment = 0; $t_principal = 0; $t_interest = 0; $t_balance = 0; $t_amount=0;$u_payment=0;$uga=0;
                        ?>
                        <?php if(!empty($summary)) {  ?>
                            <?php foreach ($summary as $row) {

                                $due = 'class="due"' ;


                                        $totalprincipal=$t_principal + $row->total_principal_not_paid;
                                        $tarrears=$u_payment + $row->total_amount_not_paid;
                                        if($row->customer_type=='group'){

                                            $preview_url = "Customer_groups/members/";
                                        }elseif($row->customer_type=='individual'){

                                            $preview_url = "Individual_customers/view/";
                                        }
                                        ?>

                                        <tr>
                                            <td<?php  ?>><a href="<?php echo base_url();?>loan/view/<?php echo $row->loan_id ;?>"><?php echo $row->loan_number ;?></a></td>
                                            <td><a href="<?php echo base_url($preview_url).$row->loan_customer?>""><?php echo $row->customer_name?></a></td>
                                            <td><a href="<?php  echo base_url().'Employees/read/'.$row->loan_added_by;?>"><?php echo $row->eFirstname.' '.$row->eLastname ?></a></td>
                                           <td><?php echo $row->product_name; ?></td>
                                            <td<?php  ?>><?php echo $this->config->item('currency_symbol') . number_format($row->loan_principal, 2, '.', ','); $t_payment += $row->loan_principal; ?></td>

                                            <td<?php  ?>><?php echo $this->config->item('currency_symbol') . number_format($row->total_amount_not_paid, 2, '.', ','); $u_payment += $row->total_amount_not_paid; ?></td>

                                            <td<?php  ?>><?php echo $this->config->item('currency_symbol') . number_format($row->total_principal_not_paid, 2, '.', ','); $t_principal += $row->total_principal_not_paid;?></td>
                                            <td<?php  ?>><?php echo $this->config->item('currency_symbol') . number_format($row->total_interest_not_paid, 2, '.', ','); $t_interest += $row->total_interest_not_paid; ?></td>
                                            <td<?php  ?>><?php echo $this->config->item('currency_symbol') . number_format($row->total_amount_not_paid, 2, '.', ','); $t_balance += $row->total_amount_not_paid; ?></td>
                                            <td><?php

                                                $u=$uga + $row->total_amount_not_paid;
                                                $date=date("Y-m-d H:i:s");
                                                $dateOne = new DateTime($date);
                                                $dateTwo = new DateTime($row->max_date);

                                                $diff = $dateTwo->diff($dateOne)->format("%a");

                                                if($diff>=0 && $diff<=7){
                                                    $tzerotoseven+=$u;
                                                    echo number_format( $u,2);
                                                }else{
                                                    echo '0';
                                                }
                                                ?></td>
                                            <td><?php  if($diff>=8 && $diff<=30){
                                                    $morethanseven +=$u;
                                                    echo number_format( $u,2);
                                                }else{
                                                    echo '0';
                                                } ?></td>
                                            <td><?php  if($diff>=31 && $diff<=60){
                                                    $morethanthirty +=$u;
                                                    echo number_format( $u,2);
                                                }else{
                                                    echo '0';
                                                } ?></td>
                                            <td><?php  if($diff>=61 && $diff<=90){
                                                    $morethansixty +=$u;
                                                    echo number_format( $u,2);
                                                }else{
                                                    echo '0';
                                                } ?></td>
                                            <td><?php  if($diff>=91 && $diff<=120){
                                                    $morethanninety +=$u;
                                                    echo number_format( $u,2);
                                                }else{
                                                    echo '0';
                                                }  ?></td>
                                            <td><?php  if($diff>=121 && $diff<=180){
                                                    $morethanonetwenty +=$u;
                                                    echo number_format( $u,2);
                                                }else{
                                                    echo '0';
                                                }  ?></td>
                                            <td><?php  if($diff>=181 && $diff<=366){
                                                    $morethanoneeighty +=$u;
                                                    echo number_format( $u,2);
                                                }else{
                                                    echo '0';
                                                }  ?></td>
                                            <td><?php  if($diff>=367){
                                                    $morethanthreesixty += $u;
                                                    echo number_format( $u,2);
                                                }else{
                                                    echo '0';
                                                }  ?></td>
                                        </tr>

                            <?php } ?>
                        <?php } ?>
                        <?php
                        $totaldisb =0;

                        $total_outstanding =$this->Loan_model->sum_total_par();
                        if(!empty($total_outstanding)){
                            foreach ($total_outstanding as $total_outstanding_balance){
                                $totaldisb += $total_outstanding_balance->portfolio_outstanding;
                            }
                        }


                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td>TOTAL</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td><?php echo "ZMW" .number_format($tarrears,2); ?></td>

                            <td><?php echo "ZMW" .number_format($totalprincipal,2); ?></td>
                            <td>-</td>
                            <td><?php echo "ZMW".number_format($t_balance,2); ?></td>
                            <td><?php echo "ZMW" .number_format($tzerotoseven,2); ?></td>
                            <td><?php echo "ZMW" .number_format($morethanseven,2); ?></td>
                            <td><?php echo "ZMW" .number_format($morethanthirty,2); ?></td>
                            <td><?php echo "ZMW" .number_format($morethansixty,2); ?></td>
                            <td><?php echo "ZMW" .number_format($morethanninety,2); ?></td>
                            <td><?php echo "ZMW" .number_format($morethanonetwenty,2); ?></td>
                            <td><?php echo "ZMW" .number_format($morethanoneeighty,2); ?></td>
                            <td><?php echo "ZMW" .number_format($morethanthreesixty,2); ?></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>


                        <tr>
                            <td>TOTAL OUTSTANDING PORTFOLIO BALANCES</td>
                            <td></td>
                            <td></td>
                            <td><?php echo "ZMW" .number_format($totaldisb,2); ?></td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>TOTAL OUTSTANDING PORTFOLIO BALANCES AT RISK</td>
                            <td></td>
                            <td></td>
                            <td>ZMW<?php echo number_format($t_balance,2)?></td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>PORTFOLIO AT RISK</td>
                            <td></td>
                            <td></td>
                            <td><?php if($totaldisb !=0){echo round(($t_balance/$totaldisb)*100,2);}else{echo 0; }; ?>%</td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>MORE THAN 0 DAYS</td>
                            <td></td>
                            <td></td>
                            <td><?php if($totaldisb !=0){echo round(($t_balance/$totaldisb)*100,2);}else{echo 0; }; ?>%</td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>MORE THAN 7 DAYS</td>
                            <td></td>
                            <td></td>
                            <td><?php if($totaldisb !=0){echo round(($morethanseven/$totaldisb)*100,2);}else{echo 0; }; ?>%</td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>MORE THAN 30 DAYS</td>
                            <td></td>
                            <td></td>
                            <td><?php if($totaldisb !=0){echo round(($morethanthirty/$totaldisb)*100,2);}else{echo 0; }; ?>%</td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>MORE THAN 60 DAYS</td>
                            <td></td>
                            <td></td>
                            <td><?php if($totaldisb !=0){echo round(($morethansixty/$totaldisb)*100,2);}else{echo 0; }; ?>%</td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>MORE THAN 90 DAYS</td>
                            <td></td>
                            <td></td>
                            <td><?php if($totaldisb !=0){echo round(($morethanninety/$totaldisb)*100,2);}else{echo 0; }; ?>%</td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>MORE THAN 120 DAYS</td>
                            <td></td>
                            <td></td>
                            <td><?php if($totaldisb !=0){echo round(($morethanonetwenty/$totaldisb)*100,2);}else{echo 0; }; ?>%</td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>MORE THAN 180 DAYS</td>
                            <td></td>
                            <td></td>
                            <td><?php if($totaldisb !=0){echo round(($morethanoneeighty/$totaldisb)*100,2);}else{echo 0; }; ?>%</td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>MORE THAN 366 DAYS</td>
                            <td></td>
                            <td></td>
                            <td><?php if($totaldisb !=0){echo round(($morethanthreesixty/$totaldisb)*100,2);}else{echo 0; }; ?>%</td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
