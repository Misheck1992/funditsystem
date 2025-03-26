<?php
$loan = get_all_by_id('approval_edits','type','Loan edit');

?>

<div class="main-content">
    <div class="page-header">
        <h2 class="header-title"> Approval</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active"> loan</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
            <div class="row">
                <div class="col-lg-12 border-right">
                        <div class="row">
                            <div class="col-md-4">
                                <ul id="tree1">
                                    <li><a href="#">Loan Management auth</a>

                                        <ul>

                                            <li>Loan update Authorisation
                                                <ul>

                                                    <?php
                                                    foreach ($loan as $cu){
                                                        ?>
                                                        <li><a href="<?php echo base_url('Auth/auth_data/'.$cu->approval_edits)?>">Loan edit-auth # <?php echo $cu->id;?>-&nbsp<?php echo $cu->summary;?>: current status: ( <?php echo $cu->state?>)</a></li>
                                                        <?php
                                                    }
                                                    ?>

                                                </ul>
                                            </li>


                                        </ul>
                                    </li>


                                </ul>
                            </div>

                        </div>
                </div>
                </div>
        </div>
    </div>
</div>
