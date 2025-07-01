<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Shareholders</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin') ?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">All Shareholders List</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick #24C16B solid; border-radius: 14px;">
            <h2 style="margin-top:0px">Shareholders List</h2>
            <div style="overflow-y: auto"">
            <table class="table table-bordered" id ="data-table1" style="margin-bottom: 10px">
              <thead>
			  <tr>
				  <th>No</th>
				  <th>Title</th>
				  <th>First Name</th>
				  <th>Last Name</th>
				  <th>Gender</th>
				  <th>Approval Status</th>
				  <th>Nationality</th>
				  <th>Phone Number</th>
				  <th>Email Address</th>
				  <th>Full Address</th>
				  <th>ID Type</th>
				  <th>Identity Number</th>
				  <th>Corporation </th>
				  <th>Action</th>
			  </tr>
			  </thead>
							  <tbody>
                <?php
                $start = 0;
                foreach ($shareholders_data as $shareholder) {


                        $inst = get_by_id('corporate_customers','id',$shareholder->corporate_id);
                        $customer_name = $inst->EntityName.' - '.$inst->RegistrationNumber.' ('.$inst->	entity_type.')';
                        $preview_url = "Corporate_customers/read/";

                    ?>
                    <tr>
                        <td width="80px"><?php echo ++$start ?></td>
                        <td><?php echo $shareholder->title ?></td>
                        <td><?php echo $shareholder->first_name ?></td>
                        <td><?php echo $shareholder->last_name ?></td>
                        <td><?php echo $shareholder->gender ?></td>
                        <td><?php echo $shareholder->approval_status ?></td>
                        <td><?php echo $shareholder->nationality ?></td>
                        <td><?php echo $shareholder->phone_number ?></td>
                        <td><?php echo $shareholder->email_address ?></td>
                        <td><?php echo $shareholder->full_address ?></td>
                        <td><?php echo isset($shareholder->idtype) ? $shareholder->idtype : 'N/A' ?></td>
                        <td><?php echo isset($shareholder->idnumber) ? $shareholder->idnumber : 'N/A' ?></td>
                        <td><?php   echo  $customer_name ?></td>
                        <td style="text-align:center" width="200px">
                            <a href="<?php echo base_url('shareholders/read/' . $shareholder->id) ?>" class="btn btn-info"><i class="os-icon os-icon-eye"></i> More</a>
                            <a href="<?php echo base_url('shareholders/update/' . $shareholder->id ) ?>" class="btn btn-success"><i class="os-icon os-icon-pencil-12"></i>Edit</a>
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
