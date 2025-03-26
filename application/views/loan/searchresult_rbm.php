<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">All Loans</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">-</a>
				<span class="breadcrumb-item active">All loans Applied</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" style="border: thick #153505 solid;border-radius: 14px;">
		     <div>
                <?php
               

                ?>
                <form action="<?php echo base_url('loan/filterloan') ?>" method="get">
                   Date from:
                    <input type="date" name="from"> Date to: <input type="date" name="to"> 
                    <input type="submit" value="filter" name="search">
                    
                   
                    
                                    <button class="btn btn-sm btn-primary rounded-0 btn-primary bg-gradient bg-primary" id="exportTableCSV">Export to Excel</button>
                       
                </form>
            </div>
            <br>
		    
                 
            <div style="overflow-y: auto"">
            <form name="frmUser" method="post" action="">
            <table  id="data-table" class="tableCss">
                <thead>
                <tr>
                       <th>#</th>
                   
                    <th>Salutation   </th>
               <th>Surname   </th>
               <th>First Name   </th>
               <th>Middle Name   </th>
               <th>	Maiden Name   </th>
               <th>Gender   </th>
               <th>	Marital Status   </th>
               <th>No. of Dependents   </th>
               <th>Date of Birth   </th>
               <th>National ID No.  </th>
               <th>ID Type   </th>	
               <th>ID No.  </th>
               <th>	Nationality  </th>
               <th>	Village   </th>	
               <th> T/A   </th>
              <th> Home District  </th>
              <th>Resident Permit No.  </th>
              <th>Phone No.   </th>
              <th>Postal Address   </th>
              <th>Email Address   </th>
              <th>Residential Address</th>
              <th>Residential District  </th>
              <th>Plot No.	  </th>
              <th>Profession/Occupation  </th>	
              <th>Employer Name   </th>
              <th>Employer Address  </th>
              <th>	Employer Phone No.  </th>
              <th>Employment Date</th>
              <th>	Branch Code/Name  </th>
              <th>	Loan Reference No.  </th>	
              <th>Old Loan Reference No.  </th>
              <th>Currency  </th>
              <th>Approved Amount  </th>
              <th>	Approved Amount(MWK)  </th>
              <th>Disbursed   </th>
              <th>Amount   </th>
              <th>Disbursed Amount (MWK)   </th>
             <th> Disbursement Date  </th>
             <th>Maturity Date  </th>
            <th> Borrower Type  </th>
            <th>Group Name  </th>
           <th> Group No.  </th>
           <th>Product Type  </th>
           <th>Payment Terms  </th>
           <th>Collateral Status  </th>
           <th>Reserve Bank Classification  </th>
           <th>	Account Status  </th>
           <th>	Account Status Change Date   </th>
          <th> Scheduled Repayment Amount  </th>
          <th>Scheduled Repayment Amount(MWK)  </th>
          <th>Total Amount Paid To Date  </th>
          <th>Total Amount Paid To Date(MWK)</th>
          <th>Current Balance	Current Balance(MWK)  </th>
          <th>	Available Credit  </th>
          <th>	Available Credit(MWK)  </th>
          <th>Amount In Arrears  </th>	
          <th>Amount In Arrears(MWK)  </th>
          <th>	Days In Arrears	  </th> 
          <th>No. of Installments In Arrears   </th>
          <th>	Default Date  </th>
         <th> Pay Off/Termination</th>
         <th>Date	Reason For Closure  </th>
         <th>First Payment Date  </th>
         <th>	Last Payment Date  </th>	
         <th>Last Payment Amount</th>
         <th>Last Payment Amount (MWK)</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $n=0;
                
                foreach ($loanreports as $r){
                    
                    
                  $payments=get_all_by_id('payement_schedules','loan_id',$r->loan_id);
                  
                ?>
                
                	<td <?php echo ++$n; ?></td>
                	
                	
                 

     <td>   <?php  echo $r->Title ?></td>
     <td>   <?php  echo $r->Lastname?></td>
     <td>   <?php  echo $r->Firstname?></td>
     <td>   <?php  echo $r->Middlename?></td>
     <td></td>
    <td><?php  echo $r->Gender ?> </td>
         <td><?php  echo $r->marital ?> </td>
          <td></td>
     <td>   <?php  echo $r->DateOfBirth ?></td>
     <td>   <?php  echo $r->IDNumber ?></td>
     <td>   <?php  echo $r->IDType?></td>
     <td>   <?php  echo $r->IDNumber ?></td>
     <td>   <?php  echo $r->Country ?></td>
     <td>   <?php  echo $r->village ?></td>
     <td>   <?php  
     
     
     echo $r->Province ?></td>
     <td>   <?php 
     
      $custdistrict=get_by_id('districts','district_id',$r->City );
								if(!empty($custdistrict)){
								echo  $custdistrict->district_name;
								}
					
     
     ?></td>
     <td>   <?php  echo $r->AddressLine2 ?></td>
     <td>   <?php  echo $r->PhoneNumber ?></td>
     <td>   <?php  echo $r->AddressLine1 ?></td>
     <td>   <?php  echo $r->EmailAddress ?></td>
     <td>   <?php  echo $r->AddressLine2 ?></td>
     <td>   <?php  echo $r->AddressLine2 ?></td>
     <td>   <?php  echo $r->plot_number ?></td>
     <td>   <?php  echo $r->Profession ?></td>
        <td> </td>
        <td></td>
        <td> </td>
        <td></td>
     <td>   <?php  
     
      $custbranch=get_by_id('branches','Code',$r->Branch );
								if(!empty($custbranch)){
								echo  $custbranch->BranchName;
								}
								
					
    ?></td>
     <td>   <?php  echo $r->loan_number ?></td>
            <td>
            
            <?php  
     
      $previousloan=get_previous_loan($r->loan_customer );
								if(!empty( $previousloan)){
								echo   $previousloan->loan_number;
								}
								
					
    ?>
            
            </td>
     <td>   <?php  echo $r->loan_principal ?></td>
     <td>   <?php  echo $r->loan_principal ?></td>
     <td>   <?php  echo $r->loan_principal ?></td>
     <td>   <?php  
     echo $r->loan_principal;
      ?></td>
       <td></td>  
            <td> <?php  
     echo $r->loan_principal;
      ?></td>
            
            <td> <?php  
     echo $r->disbursed_date;
      ?></td>
      <td>
      
      	<?php
                	 $paymentslast=get_allLoanPayRBM_by_id($r->loan_id,$r->loan_period);
								if(!empty($paymentslast)){
								echo $paymentslast->payment_schedule;
								}
								?>
      </td> 
       <td>   <?php  echo $r->customer_type ?></td>
     <td>   <?php 
     
     if( $r->customer_type=='group'){
          $custgroup=get_by_id('groups','group_id',$r->loan_customer );
								if(!empty($custgroup)){
								echo  $custgroup->group_name;
								}
								
     }
      ?></td>
     <td>   <?php 
     

								if(!empty($custgroup)){
								echo  $custgroup->group_code;
								}
								
     
      ?></td>
        <td> <?php 
     
          $custproducts=get_by_id('loan_products','loan_product_id',$r->loan_product );
								if(!empty($custproducts)){
								echo  $custproducts->product_name;
								}
								
     
      ?></td>
           
            
                 <td>   <?php 
                    
                
                    echo $r->loan_period ?></td>
                     <td></td>
                    <td> </td>
        <td></td>
        <td> </td>
     <td>   <?php  echo $r->loan_amount_term ?></td>
     <td>   <?php  echo $r->loan_amount_term ?></td>
     <td>   <?php  echo $r->loan_amount_total ?></td>
                	<td>
                	
               	<?php
								$total_p = 0;
								foreach ($payments as $pp){
									if($pp->status == "PAID"){
										$total_p +=$pp->amount;
									}

								}
								echo number_format($total_p,2);
								?>
                	
                	</td>
                		<td>
                			<?php
								$total_p = 0;
								foreach ($payments as $pp){
									if($pp->status == "PAID"){
										$total_p +=$pp->amount;
									}

								}
								echo number_format($total_p,2);
								?></td>
                	<td> <?php
								$total_b = 0;
								foreach ($payments as $ppp){
									if($ppp->status == "NOT PAID"){
										$total_b +=$pp->amount;
									}

								}
								echo number_format($total_b,2);
								?></td>
                		<td></td>
                	<td> </td>
                			<td>
                	
                	<?php
                	 $arreasamount=get_amount_of_arreas($r->loan_id,1);
                	 if(!empty($arreasamount))
								{
									echo number_format( $arreasamount-> amount_arrears,2);
								}
								?>
								</td>
                		<td>
                		
                		<?php
 $arreasamount=get_amount_of_arreas($r->loan_id,1);
                	 if(!empty($arreasamount))
								{
									echo number_format( $arreasamount-> amount_arrears,2);
								}
								?>
                		</td>
                	<td>
                	
                	<?php
                	 $arreasdays=get_days_of_arreas($r->loan_id,1);
                	 if(!empty($arreasdays))
								{
								echo  $arreasdays->days_in_arrears;
								}
								?></td>
                		<td>
                		<?php
                	 $arreasinstall=get_number_of_arreas($r->loan_id,1);
                	 if(!empty($arreasinstall))
								{
								echo  $arreasinstall->num_arrears;
								}
								?>
                		
                		
                		</td>
                	<td> </td>
                	
                		<td></td>
                
                		
                		<td> </td>
                	
                	<td> <?php
                	 $paymentsFirst=get_allLoanPayRBM_by_id($r->loan_id,1);
                	 if(!empty($paymentsFirst))
								{
								echo  $paymentsFirst->payment_schedule;
								}
								?>
								</td>
                		<td>
                		<?php
                	 $paymentslast=get_allLoanPayRBM_by_id($r->loan_id,$r->loan_period);
								if(!empty($paymentslast)){
								echo $paymentslast->payment_schedule;
								}
								?></td>
                	<td> 	<?php
                		if(!empty($paymentslast)){
									echo number_format($paymentslast->amount,2);
                		}
								?></td>
                		<td>	<?php
                	
									if(!empty($paymentslast)){
								echo number_format($paymentslast->amount,2);
									}
								?></td>
                	</tr>
                	
                	<?php
                	
                }
                ?>
							               </tbody>
            </table>
            
           
            </form>
        </div>

		</div>
	</div>
</div>
