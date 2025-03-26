<?php
$users = get_all('transaction_type');
$products = get_active_loan();
?>
<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Collateral  report</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">Collateral reports </span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
            <form action="<?php echo base_url('reports/track_collateral') ?>" method="POST">
                <fieldset>
                    <legend>Track Collateral</legend>


                    Loan Number:<input type="text"  name="loannumber" value=""  placeholder="Copy paste loan number here">
                    <button type="submit" name="search" value="filter">Filter</button>
                    <button type="submit" name="search" value="pdf"><i class="fa fa-file-pdf text-danger"></i></button>
                    <button type="submit" name="search" value="excel"><i class="fa fa-file-excel text-success"></i></button>
        </div>
        </fieldset>
        </form>
        <hr>
    </div>
</div>
</div>
