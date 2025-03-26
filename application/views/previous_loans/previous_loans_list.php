<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">Previous loan</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">Loans</a>
				<span class="breadcrumb-item active">Previous loan</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" style="border: thick #24C16B  solid;border-radius: 14px;">
        <div class="row" style="margin-bottom: 10px">
            <div class="col-md-4">
                <?php echo anchor(site_url('previous_loans/create'),'Create', 'class="btn btn-primary"'); ?>
            </div>
            <div class="col-md-4 text-center">
                <div style="margin-top: 8px" id="message">
                    <?php echo $this->session->userdata('message') <> '' ? $this->session->userdata('message') : ''; ?>
                </div>
            </div>
            <div class="col-md-1 text-right">
            </div>
            <div class="col-md-3 text-right">
                <form action="<?php echo site_url('previous_loans/index'); ?>" class="form-inline" method="get">
                    <div class="input-group">
                        <input type="text" class="form-control" name="q" value="<?php echo $q; ?>">
                        <span class="input-group-btn">
                            <?php 
                                if ($q <> '')
                                {
                                    ?>
                                    <a href="<?php echo site_url('previous_loans'); ?>" class="btn btn-default">Reset</a>
                                    <?php
                                }
                            ?>
                          <button class="btn btn-primary" type="submit">Search</button>
                        </span>
                    </div>
                </form>
            </div>
        </div>
        <table class="table table-bordered" style="margin-bottom: 10px">
            <tr>
                <th>No</th>
		<th>Customer Id</th>
		<th>Loan Effective Date</th>
		<th>Loan End Date</th>
		<th>Amount</th>
		<th>Status</th>
	
		<th>Stamp</th>
		<th>Action</th>
            </tr><?php

            foreach ($previous_loans_data as $previous_loans)
            {
                ?>
                <tr>
			<td width="80px"><?php echo ++$start ?></td>
			<td><?php echo $previous_loans->customer_id?></td>
			<td><?php echo $previous_loans->loan_effective_date ?></td>
			<td><?php echo $previous_loans->loan_end_date ?></td>
			<td><?php echo $previous_loans->amount ?></td>
			<td><?php echo $previous_loans->status ?></td>
<!--			<td>--><?php //echo $previous_loans->added_by ?><!--</td>-->
			<td><?php echo $previous_loans->stamp ?></td>
			<td style="text-align:center" width="200px">
				<?php 
				echo anchor(site_url('previous_loans/read/'.$previous_loans->p_lid),'Read'); 
				echo ' | '; 

				echo anchor(site_url('previous_loans/delete/'.$previous_loans->p_lid),'Delete','onclick="javasciprt: return confirm(\'Are You Sure ?\')"'); 
				?>
			</td>
		</tr>
                <?php
            }
            ?>
        </table>
        <div class="row">
            <div class="col-md-6">
                <a href="#" class="btn btn-primary">Total Record : <?php echo $total_rows ?></a>
	    </div>
            <div class="col-md-6 text-right">
                <?php echo $pagination ?>
            </div>
        </div>
		</div>
	</div>
</div>
