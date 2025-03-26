<?php
$b = $this->Branches_model->get_all();
$group_cat = $this->Group_categories_model->get_all();

$get_c = get_all('individual_customers');
?>
<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">Groups</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">-</a>
				<span class="breadcrumb-item active">groups form</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" style="border: thick green solid;border-radius: 14px;">
        <form action="" method="post" >

			<div class="row">
				<div class="col-lg-8">
					<div class="row">
					<div class="form-group col-3">
						<label for="varchar">Group Name <?php echo form_error('group_name') ?></label><br/>
						<?php echo $group_name; ?>" 
					</div>


					<div class="form-group col-3">
						<label for="int">Village/Market *</label><br/>

							<?php echo $group_village; ?>
				
					</div>
					<div class="form-group col-3">
						<label for="int">TA * </label><br/>

							<?php echo $group_ta ?>
				
					</div>
						<div class="form-group col-3">
						<label for="int">District * </label><br/>

					<?php 
					
				
     
      $custdistrict=get_by_id('districts','district_id', $group_district );
								if(!empty($custdistrict)){
								echo  $custdistrict->district_name;
								}
					 ?>
					
					</div>
					
					<div class="form-group col-6">
						<label for="varchar">Address </label><br/>
					
					<?php echo $group_address; ?>
						</div>


				
					
					
						<div class="form-group col-6">
						<label for="int">contacts * <?php echo form_error('group_contact') ?></label><br/>

						<?php echo $group_contact; ?>
			</div>
			<div class="form-group col-6">
						<label for="int">Email  <?php echo form_error('group_email') ?></label><br/>

							<?php echo $group_email; ?>
			</div>



  <div class="form-group col-6">
                                <label for="int">Branch * </label><br/>

                               	<?php  
                               
     
      $custdistrict=get_by_id('branches','id',$branch );
								if(!empty($custdistrict)){
								echo  $custdistrict->BranchName;
								}
                               	
                               	?>
                            </div
                            <div class="form-group col-4">
						<br>

						<a href="<?php echo base_url('uploads/').$file?>">Download Agreement file</a>

					</div>
				
<div class="form-group col-12">
						<label for="group_description">Group Description </label>
						<textarea class="form-control" rows="3" name="group_description" id="group_description" placeholder="Group Description"><?php echo $group_description; ?></textarea>

					</div>
					
				
				</div>
				<div class="col-lg-4">
					<div class="row">
					<div class="form-group col-12">
						<label for="varchar">Members</label><br />
						<?php  
                               
     
      $cust=get_all_by_id('customer_groups','group_id',$group_id );
								if(!empty($cust)){
							
								
								
								foreach ($cust as $m)
								{
								    $custgroup=get_by_id('individual_customers','id',$m->customer );
								if(!empty( $custgroup)){
								echo   $custgroup->Firstname."   ".$custgroup->Lastname." <br />" ;
								}
                               	
								}
								
								
								}
                               	
                               	?>
						
					</div>
					</div>
				</div>
			</div>

<div class="row">
	<div class="col-lg-12">
		<input type="hidden" name="group_id" value="<?php echo $group_id; ?>" />
		
	</div>
</div>

	</form>
		</div></div>
</div>
