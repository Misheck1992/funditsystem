<?php
$logs = get_logs('activity_logger','user_id',$this->session->userdata('user_id'));
$settings = get_by_id('settings','settings_id','1');
?>
<div class="main-content">
    <div class="page-header no-gutters has-tab" style="margin-bottom: 2px !important;">


    </div>
    <?php
    $show = false;
    foreach ($this->session->userdata('access') as $r) {
        if ($r->controllerid == 113) {
            $show = true;
            break;
        }
    }
    ?>
    <?php
    if($show){
        ?>
        <div class="row">
            <div class="col-lg-12">
                <h2 class="heading">Loan summary for  <?php echo $product_name  ?> </h2>
                <hr class="dash" >
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <a class="dashboard-stat purple" href="#">
                    <div class="visual">
                        <i class="fa fa-usd"></i>
                    </div>
                    <div class="details">
                        <div class="number">
                        <span><?php echo $settings->currency?> <?php
                            $ip_b = 0;
                            $ip = get_total_loan_amount('initiated',$product_id);


                            echo number_format(round($ip ->total_amount),2);
                            ?></span>
                        </div>
                        <div class="desc">Total Initiated loan</div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <a class="dashboard-stat blue" href="#">
                    <div class="visual">
                        <i class="fa fa-bar-chart-o"></i>
                    </div>
                    <div class="details">
                        <div class="number">
                        <span><?php echo $settings->currency?> <?php
                            $ip_i = 0;

                            $ipd = get_total_loan_amount('disbursed' ,$product_id);


                            echo number_format(round($ipd ->total_amount),2);
                            ?></span>
                        </div>
                        <div class="desc">Total disbursed loans</div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <a class="dashboard-stat hoki" href="#">
                    <div class="visual">
                        <i class="fa fa-credit-card"></i>
                    </div>
                    <div class="details">
                        <div class="number">
                        <span><?php echo $settings->currency?> <?php
                            $ip_a = 0;

                            $ipc = get_total_loan_amount('active', $product_id);


                            echo number_format(round($ipc ->total_amount),2);
                            ?></span>
                        </div>
                        <div class="desc">Total Active loan</div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <a class="dashboard-stat hoki" href="#">
                    <div class="visual">
                        <i class="fa fa-credit-card"></i>
                    </div>
                    <div class="details">
                        <div class="number">
                        <span><?php echo $settings->currency?> <?php
                            $ip_a = 0;

                            $ipc = get_total_loan_amount('closed', $product_id);


                            echo number_format(round($ipc ->total_amount),2);
                            ?></span>
                        </div>
                        <div class="desc">Total Closed loan</div>
                    </div>
                </a>
            </div>

        </div>

        <div class="row">
            <div class="col-lg-12">
                <h2 class="heading">Product Portfolio summary</h2>
                <hr class="dash" >
            </div>
        </div>



        <div class="row">
            <div class="col-lg-12">
                <h2 class="heading float-left">Arrears</h2> <h2 class="heading float-right">Payments Due</h2>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 border-right border-success">

                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <a class="dashboard-stat red" href="#">
                            <div class="visual">
                                <i class="fa fa-usd"></i>
                            </div>
                            <div class="details">
                                <div class="numberr">
                        <span><?php echo $settings->currency?> <?php
                            $today_b = 0;
                            $today = institutional_arrears_today_by_id($product_id);

                            foreach ($today as $td){
                                $today_b += $td->amount;
                            }

                            echo number_format(round($today_b),2);
                            ?></span>
                                </div>
                                <div class="desc">One day arrears</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <a class="dashboard-stat red" href="#">
                            <div class="visual">
                                <i class="fa fa-bar-chart-o"></i>
                            </div>
                            <div class="details">
                                <div class="numberr">
                        <span><?php echo $settings->currency?> <?php
                            $three_d = 0;
                            $threed = institutional_arrears_threedays_by_id($product_id);

                            foreach ($threed as $d3){
                                $three_d += $d3->amount;
                            }
                            echo number_format(round($three_d),2);
                            ?></span>
                                </div>
                                <div class="desc">Three days Arrears</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <a class="dashboard-stat red" href="#">
                            <div class="visual">
                                <i class="fa fa-credit-card"></i>
                            </div>
                            <div class="details">
                                <div class="numberr">
                        <span><?php echo $settings->currency?> <?php
                            $week_d = 0;


                            $week = institutional_arrears_week_by_id($product_id);
                            foreach ($week as $w){
                                $week_d += $w->amount;

                            }

                            echo number_format(round($week_d),2);
                            ?></span>
                                </div>
                                <div class="desc">One week Arrears</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <a class="dashboard-stat red" href="#">
                            <div class="visual">
                                <i class="fa fa-credit-card"></i>
                            </div>
                            <div class="details">
                                <div class="numberr">
                        <span><?php echo $settings->currency?> <?php
                            $month_d = 0;

                            $mo = institutional_arrears_month_by_id($product_id);
                            foreach ($mo as $m){
                                $month_d += $m->amount;

                            }

                            echo number_format(round($month_d),2);
                            ?></span>
                                </div>
                                <div class="desc">One Month Arrears</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <a class="dashboard-stat red" href="#">
                            <div class="visual">
                                <i class="fa fa-credit-card"></i>
                            </div>
                            <div class="details">
                                <div class="numberr">
                        <span><?php echo $settings->currency?> <?php
                            $one_m = 0;

                            $onem = institutional_arrears_2month_by_id($product_id);
                            foreach ($onem as $m1){
                                $one_m += $m1->amount;
                            }
                            echo number_format(round($one_m),2);
                            ?></span>
                                </div>
                                <div class="desc">Two Months Arrears</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <a class="dashboard-stat red" href="#">
                            <div class="visual">
                                <i class="fa fa-credit-card"></i>
                            </div>
                            <div class="details">
                                <div class="numberr">
                        <span><?php echo $settings->currency?> <?php
                            $two_m = 0;

                            $twom = institutional_arrears_3month_by_id($product_id);
                            foreach ($twom as $m2){
                                $two_m += $m2->amount;
                            }
                            echo number_format(round($two_m),2);
                            ?></span>
                                </div>
                                <div class="desc">Three Months Arrears</div>
                            </div>
                        </a>
                    </div>

                </div>
            </div>
            <div class="col-lg-6">

                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <a class="dashboard-stat orange" href="#">
                            <div class="visual">
                                <i class="fa fa-usd"></i>
                            </div>
                            <div class="details">
                                <div class="numberr">
                        <span><?php echo $settings->currency?> <?php
                            $payment_d = 0;
                            $payment_today = payments_today_by_id($product_id);

                            foreach ($payment_today as $pd){
                                $payment_d += $pd->amount;
                            }

                            echo number_format(round($payment_d),2);
                            ?></span>
                                </div>
                                <div class="desc">Payments due today</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <a class="dashboard-stat orange" href="#">
                            <div class="visual">
                                <i class="fa fa-bar-chart-o"></i>
                            </div>
                            <div class="details">
                                <div class="numberr">
                        <span><?php echo $settings->currency?> <?php
                            $payment_week = 0;
                            $pw = payments_week_by_id($product_id);

                            foreach ($pw as $pww){
                                $payment_week += $pww->amount;
                            }
                            echo number_format(round($payment_week),2);
                            ?></span>
                                </div>
                                <div class="desc">Payment due this week</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                        <a class="dashboard-stat orange" href="#">
                            <div class="visual">
                                <i class="fa fa-credit-card"></i>
                            </div>
                            <div class="details">
                                <div class="numberr">
                        <span><?php echo $settings->currency?> <?php
                            $p_m = 0;


                            $payment_month = payments_month_by_id($product_id);
                            foreach ($payment_month as $pmm){
                                $p_m += $pmm->amount;

                            }

                            echo number_format(round($p_m),2);
                            ?></span>
                                </div>
                                <div class="desc">Payment due this month</div>
                            </div>
                        </a>
                    </div>


                </div>
            </div>
        </div>




        <?php
    }else{
        ?>
        <ul class="nav nav-tabs" >
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#tab-account">Account</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tab-network">Recent activity logs</a>
            </li>

        </ul>
        <div class="container">
            <div class="tab-content m-t-15">
                <div class="tab-pane fade show active" id="tab-account" >
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Basic Information</h4>
                        </div>
                        <div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
                            <div class="media align-items-center">
                                <div class="avatar avatar-image  m-h-10 m-r-15" style="height: 80px; width: 80px">
                                    <img src="<?php echo base_url('uploads')?>/avatar-3.png" alt="">
                                </div>

                            </div>
                            <hr class="m-v-25">
                            <form>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label class="font-weight-semibold" for="userName">User Name:</label>
                                        <input type="text" class="form-control" id="userName" disabled placeholder="User Name" value="<?php  echo  $this->session->userdata('username')?>">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="font-weight-semibold" for="email">Full name:</label>
                                        <input type="text"  disabled class="form-control" id="email" placeholder="email" value="<?php echo  $this->session->userdata('Firstname')."".$this->session->userdata('Lastname') ?>">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label class="font-weight-semibold" for="phoneNumber">Designation:</label>
                                        <input type="text" class="form-control" disabled id="phoneNumber" placeholder="Phone Number" value="<?php echo  $this->session->userdata('RoleName') ?>">
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label class="font-weight-semibold" for="dob">Date Joined:</label>
                                        <input type="text" class="form-control" disabled id="dob" placeholder="<?php echo $this->session->userdata('stamp');?>">
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>


                </div>
                <div class="tab-pane fade" id="tab-network">
                    <div class="row">
                        <div class="col-md-8 mx-auto">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">My system logs</h4>
                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th>Date time</th>
                                            <th>Activity</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php

                                        foreach ($logs as $log){
                                            ?>
                                            <tr>
                                                <td><?php  echo $log->server_time; ?></td>
                                                <td><?php echo $log->activity; ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <?php
    }
    ?>

    <!---->
</div>
