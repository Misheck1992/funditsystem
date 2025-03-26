
<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Corporate customers</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">All Corporate customers List</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick #24C16B solid;border-radius: 14px;">
            <h2 style="margin-top:0px">Corporate_customers List</h2>

            <table class="table table-bordered" style="margin-bottom: 10px">
                <tr>
                    <th>No</th>
                    <th>EntityName</th>

                    <th>Status</th>


                    <th>CreatedOn</th>

                </tr><?php
                $start=0;
                foreach ($corporate_customers_data as $corporate_customers)
                {

                    ?>
                    <tr>
                        <td width="80px"><?php echo ++$start ?></td>
                        <td><?php echo $corporate_customers->EntityName ?></td>

                        <td><?php echo $corporate_customers->Status ?></td>


                        <td><?php echo $corporate_customers->CreatedOn ?></td>


                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
    </div>
</div>
</div>
</div>

