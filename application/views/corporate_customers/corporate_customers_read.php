<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Corporate Customers</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">Corporate Customer Details</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
            <div class="row">
                <div class="col-lg-4 border-right">
                    <h2>Basic Information</h2>
                    <hr>
                    <table class="table">
                        <tr>
                            <th style="text-align: right; width: 50%;">Entity Name</th>
                            <td><?php echo $EntityName; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Entity Type</th>
                            <td><?php echo $entity_type; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Category</th>
                            <td><?php echo $category; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Registration Number</th>
                            <td><?php echo $RegistrationNumber; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Tax ID Number</th>
                            <td><?php echo $TaxIdentificationNumber; ?></td>
                        </tr>
                    </table>
                    <!-- Show Key Management Info -->
                    <div class="mt-3">
                        <strong>Key Management Info:</strong>
                        <div style="white-space: pre-line;"><?php echo isset($key_management_info) ? $key_management_info : ''; ?></div>
                    </div>
                </div>

                <div class="col-lg-4 border-right">
                    <h2>Business Details</h2>
                    <hr>
                    <table class="table">
                        <tr>
                            <th style="text-align: right; width: 50%;">Nature of Business</th>
                            <td><?php echo $nature_of_business; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Industry Sector</th>
                            <td><?php echo $industry_sector; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Country</th>
                            <td><?php echo $Country; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Branch</th>
                            <td><?php echo $Branch; ?></td>
                        </tr>
                    </table>
                    <!-- Show Business Info -->
                    <div class="mt-3">
                        <strong>Business Info:</strong>
                        <div style="white-space: pre-line;"><?php echo isset($business_info) ? $business_info : ''; ?></div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <h2>Contact Information</h2>
                    <hr>
                    <table class="table">
                        <tr>
                            <th style="text-align: right; width: 50%;">Street</th>
                            <td><?php echo $street; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">City/Town</th>
                            <td><?php echo $city_town; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Phone Number</th>
                            <td><?php echo $phone_number; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Email</th>
                            <td><?php echo $contact_email; ?></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Website</th>
                            <td><?php echo $website; ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-12">
                    <h2>Required Documents</h2>
                    <hr>
                    <table class="table">
                        <tr>
                            <th style="text-align: right; width: 25%;">Company Certificate</th>

                            <td><a href="<?php echo base_url('uploads/').$company_certificate ?>">Download attachment</a></td>


                        </tr>
                        <tr>
                            <th style="text-align: right;">Proof of Address</th>

                            <td><a href="<?php echo base_url('uploads/'). $proof_physical_address ?>">Download attachment</a></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Financial Statements</th>

                            <td><a href="<?php echo base_url('uploads/'). $financial_statement ?>">Download attachment</a></td>
                        </tr>
                        <tr>
                            <th style="text-align: right;">Tax Clearance</th>
                           
                            <td><a href="<?php echo base_url('uploads/'). $tax_id_doc ?>">Download attachment</a></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-lg-12">
                    <h2>Shareholder Details </h2>
                    <hr>
                    <div class="double-scroll">
                    <table class="table table-bordered" style="margin-bottom: 10px">
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
                            <th>% Ownership </th>
                            <th>KYC file </th>
                           
                        </tr>
                        <?php
                        $shareholders_data=get_all_shareholders($id);
                        $start = 0;
                        foreach ($shareholders_data as $shareholder) {




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
                                <td><?php echo $shareholder->percentage_value ?></td>
                                <td><a href="<?php echo base_url('uploads/').'147detailed_approach.pdf' ?>">Download KYC</a></td>

                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <a href="<?php echo site_url('corporate_customers') ?>" class="btn btn-default">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
