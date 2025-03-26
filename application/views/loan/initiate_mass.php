
<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Loan  mass repayment</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <a class="breadcrumb-item" href="#">-</a>
                <span class="breadcrumb-item active">Loan mass repayment</span>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-body" style="border: thick green solid;border-radius: 14px;">
            <div class="row">
                <form action="<?php  echo  base_url('loan/upload_excel');?>" enctype="multipart/form-data" method="post">
                    <input type="file" name="excel_file">
                    <label for="excel_file"  >upload file</label>
                    <input id="excel_file" type="file" name="excel_file" style="display: block" placeholder="Attachment" class="form-control">
                    <input type="submit">
                </form>
            </div>
        </div>
    </div>
</div>