
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

            <table class="table table-bordered" id="data-table1" style="margin-bottom: 10px">
				<thead>
				<tr>
					<th>No</th>
					<th>EntityName</th>

					<th>Status</th>


					<th>CreatedOn</th>
					<th>Action</th>
				</tr>
				</thead>
				<tbody>
             <?php
                $start=0;
                foreach ($corporate_customers_data as $corporate_customers)
                {

                    ?>
                    <tr>
                        <td width="80px"><?php echo ++$start ?></td>
                        <td><?php echo $corporate_customers->EntityName ?></td>

                        <td><?php echo $corporate_customers->Status ?></td>


                        <td><?php echo $corporate_customers->CreatedOn ?></td>
                        <td style="text-align:center" width="300px">
                            <a href="<?php echo base_url('corporate_customers/read/'.$corporate_customers->id)?>" class="btn btn-info" ><i class="os-icon os-icon-check"></i>View</a>
                            <a href="<?php echo base_url('corporate_customers/approval_action/'.$corporate_customers->id)?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to approve this customer?')"><i class="os-icon os-icon-check"></i>Approve</a>
                            <a href="<?php echo base_url('corporate_customers/reject_action/'.$corporate_customers->id)?>" class="btn btn-warning" onclick="return confirm('Are you sure you want to reject this customer?')"><i class="fa fa-recycle"></i>Reject</a>

                        </td>


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

