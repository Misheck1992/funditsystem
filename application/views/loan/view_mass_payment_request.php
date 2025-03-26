<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">All Loan repayments</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">All repayments tracker</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
            <div>
                <?php
                $products = get_all_order('mass_repayment_requests','mass_repayment_request_id');


                ?>

            </div>

            <div class="double-scroll">
                <table  id="data-table" class="table">
                    <thead>
                    <tr>

                        <th>#</th>
                        <th>File</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>Date</th>


                        <th>Action</th>

                    </tr>
                    </thead>
                    <tbody><?php
                    $n = 1;

                    foreach ($products as $loan)
                    {
$user = get_by_id('employees','id',$loan->user);
if($loan->status=="Has Errors"){
    $css = "#F3D8D8";
    $color = "red";
}else{
    $css = "";
    $color = "";
}

                        ?>
                        <tr style="background-color: <?php echo $css; ?>">

                            <td><?php echo $n ?></td>
                            <td><a href="<?php echo base_url('uploads/').$loan->file?>">Download file</a></td>
                            <td style="color: <?php echo  $color; ?>"><?php echo $loan->status ?></td>

                            <td><?php echo $user->Firstname." ".$user->Lastname ?></td>


                            <td><?php echo $loan->stamp ?></td>
                            <td>
                                <a href="<?php echo base_url('loan/repayment_request_details/'). urlencode($loan->mass_repayment_request_id );?>" class="btn btn-primary">View  </a>
                                <?php
                                if($loan->status=="Has Errors"){

                                }else{


                                ?>
                                <a href="#"class="btn btn-warning" onclick="start_mass_payment(1)">Commit payment</a>
                                <?php
                                }
                                ?>
                            </td>


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
