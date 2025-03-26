<!-- Content Wrapper START -->
<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">Audit trail</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">Audit trail</a>
				<span class="breadcrumb-item active">System users trails</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" >
		    
		        <?php
               

                ?>
                <form action="<?php echo base_url('Activity_logger/filteractivity') ?>" method="get">
                    
                     </select> Status: <select name="status" id="" class="select2">
                        <option value="logging">Log in</option>
                        <option value="customer_registration">customer registration</option>
                         <option value="customer_approval">customer approved</option>
                          <option value="group_registration">group registration</option>
                           <option value="group_approval">group approval</option>
                        <option value="loan_registration">loan registration</option>
                        <option value="loan_recomendation">loan recommendated </option>
                        <option value="loan_approval">loan approved</option>
                        <option value="loan_disbursement">Loan disbursed</option>
                        <option value="loan_repayment">loan payments</option>
                         <option value="adding">Adding actions</option>
                         <option value="updating">Updating actions</option>
                          <option value="updating">Deletion actions</option>
                       
                       
                    </select> 
                   Date from:
                    <input type="date" name="from"> Date to: <input type="date" name="to"> 
                    <input type="submit" value="filter" name="search">
                    
                   
                    
                            
                       
                </form>
            </div>
            <br>
			<div class="m-t-25">
				<table id="data-table" class="table">
					<thead>
            <tr>
	<th>#</th>
		<th>User Id</th>
		<th>Activity</th>
		<th>System Time</th>
		<th>Server Time</th>

            </tr>
					</thead>
					<tbody>
			<?php
			
			$start=0;
            foreach ($activity_logger_data as $activity_logger)
            {
                ?>
                <tr>
			<td><?php echo ++$start ?></td>
			<td><?php echo $activity_logger->Firstname ." ".$activity_logger->Lastname?></td>
			<td><?php echo $activity_logger->activity ?></td>
			<td><?php echo $activity_logger->system_time ?></td>
			<td><?php echo $activity_logger->server_time ?></td>

		</tr>
                <?php
            }
            ?>
					</tbody>
        </table>
		</div>
	</div>
</div>
