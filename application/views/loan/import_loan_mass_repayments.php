<?php
$loan_types = $this->Loan_products_model->get_all();
$get_settings = get_by_id('settings','settings_id', '1');
?>

<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Mass upload form</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">Upload excel file</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
            <div class="row">
                <div class="col-lg-12 border-right">
                    <form action="<?php echo base_url('loan/masspaymentscreate')?>" method="POST" enctype="multipart/form-data">

                        <div class="col-lg-12">
                            <label for="excel_file">Excel File:</label>
                            <input type="file" style="display: block"  id="excel_file" name="excelfile">
                        </div>
                        <br/>
                        <button type="submit" class="btn btn-primary">Import</button>
                        <div class="progress" style="display:none;">
                            <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                                <span class="sr-only">0% Complete</span>
                            </div>
                        </div>
                        <div id="result"></div>
                    </form>
                </div>


            </div>
        </div>
    </div>
</div>
</div>
