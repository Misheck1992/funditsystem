<?php
$users = get_all('transaction_type');
$products = get_active_loan();
?>
<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">All Collateral report</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">Collateral report</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
            <form action="<?php echo base_url('Tellering/track_transaction') ?>" method="POST">
                <fieldset>
                    <legend>Collateral report</legend>


                    Loan Number:<input type="text"  name="loannumber" value=""  placeholder="Copy paste loan number here">
                    <button type="submit" name="search" value="filter">Filter</button>
                    <button type="submit" name="search" value="pdf"><i class="fa fa-file-pdf text-danger"></i></button>
                    <button type="submit" name="search" value="excel"><i class="fa fa-file-excel text-success"></i></button>
        </div>
        </fieldset>
        </form>
        <hr>
        <p>Search results</p>
        <table  id="data-table" class="table">
            <thead>
            <tr>

                <th>#</th>
                <th> Name</th>
                <th>Type
                </th>
                <th>Serial</th>
                <th>Value</th>
                <th>Files </th>



            </tr>
            </thead>
            <tbody>
            <?php
            $n = 1;
            foreach ($loan_data as $l){
                ?>
                <tr>
                    <td><?php echo $n;?></td>
                    <td><?php echo $l->collateral_name;?></td>

                    <td><?php echo $l->collateral_type;?></td>
                    <td><?php echo $l->collateral_serial;?></td>
                    <td><?php echo $l->collateral_value;?></td>
                    <td><a href="#" onclick="get_loan_co_files('<?php  echo $l->collateral_loan_id ;?>')" >Download file <i class="fa fa-download fa-flip"></i></a></td>

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
