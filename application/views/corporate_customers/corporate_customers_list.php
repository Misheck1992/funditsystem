<style>
    .nav-tabs .nav-link {
        color: #24C16B;
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        background-color: #24C16B;
        border-color: #24C16B;
        color: white;
    }
    .tab-content {
        margin-top: 20px;
    }
    .badge-count {
        background-color: #6c757d;
        margin-left: 8px;
    }
</style>

<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Individual customers</h2>
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
            <h2 style="margin-top:0px">Corporate customers List</h2>

            <!-- Tab Navigation -->
            <ul class="nav nav-tabs" id="customerTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="offtaker-tab" data-toggle="tab" href="#offtaker" role="tab" aria-controls="offtaker" aria-selected="true">
                        <i class="fas fa-industry me-2"></i>Off-takers
                        <span class="badge badge-count" id="offtaker-count">
                            <?php
                            $offtaker_count = 0;
                            foreach ($corporate_customers_data as $customer) {
                                if ($customer->category == 'off_taker') $offtaker_count++;
                            }
                            echo $offtaker_count;
                            ?>
                        </span>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="client-tab" data-toggle="tab" href="#client" role="tab" aria-controls="client" aria-selected="false">
                        <i class="fas fa-users me-2"></i>Clients
                        <span class="badge badge-count" id="client-count">
                            <?php
                            $client_count = 0;
                            foreach ($corporate_customers_data as $customer) {
                                if ($customer->category == 'client') $client_count++;
                            }
                            echo $client_count;
                            ?>
                        </span>
                    </a>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="customerTabContent">
                <!-- Off-takers Tab -->
                <div class="tab-pane fade show active" id="offtaker" role="tabpanel" aria-labelledby="offtaker-tab">
                    <table class="table table-bordered" id="offtaker-table" style="margin-bottom: 10px">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>EntityName</th>
                            <th>Status</th>
                            <th>Category</th>
                            <th>CreatedOn</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $offtaker_start = 0;
                        foreach ($corporate_customers_data as $corporate_customers) {
                            if ($corporate_customers->category == 'off_taker') {
                                ?>
                                <tr>
                                    <td width="80px"><?php echo ++$offtaker_start ?></td>
                                    <td><?php echo $corporate_customers->EntityName ?></td>
                                    <td><?php echo $corporate_customers->Status ?></td>
                                    <td><?php echo $corporate_customers->category ?></td>
                                    <td><?php echo $corporate_customers->CreatedOn ?></td>
                                    <td style="text-align:center" width="200px">
                                        <a href="<?php echo base_url('corporate_customers/read/'.$corporate_customers->id) ?>" class="btn btn-info">
                                            <i class="os-icon os-icon-eye"></i> More
                                        </a>
                                        <a href="<?php echo base_url('corporate_customers/update/'.$corporate_customers->id) ?>" class="btn btn-success">
                                            <i class="os-icon os-icon-pencil-12"></i>Edit
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>

                <!-- Clients Tab -->
                <div class="tab-pane fade" id="client" role="tabpanel" aria-labelledby="client-tab">
                    <table class="table table-bordered" id="client-table" style="margin-bottom: 10px">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>EntityName</th>
                            <th>Status</th>
                            <th>Category</th>
                            <th>CreatedOn</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $client_start = 0;
                        foreach ($corporate_customers_data as $corporate_customers) {
                            if ($corporate_customers->category == 'client') {
                                ?>
                                <tr>
                                    <td width="80px"><?php echo ++$client_start ?></td>
                                    <td><?php echo $corporate_customers->EntityName ?></td>
                                    <td><?php echo $corporate_customers->Status ?></td>
                                    <td><?php echo $corporate_customers->category ?></td>
                                    <td><?php echo $corporate_customers->CreatedOn ?></td>
                                    <td style="text-align:center" width="200px">
                                        <a href="<?php echo base_url('corporate_customers/read/'.$corporate_customers->id) ?>" class="btn btn-info">
                                            <i class="os-icon os-icon-eye"></i> More
                                        </a>
                                        <a href="<?php echo base_url('corporate_customers/update/'.$corporate_customers->id) ?>" class="btn btn-success">
                                            <i class="os-icon os-icon-pencil-12"></i>Edit
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

