<?php
$linkk = base_url('admin_assets/images/pattern.png');
$imgg = 'data:image;base64,'.base64_encode(file_get_contents($linkk))
?>
<style>
	#pattern-style-a
	{
		font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
		font-size: 12px;
		width: 100%;
		text-align: left;
		border-collapse: collapse;
		background: url('<?php echo $imgg; ?>');;
	}
</style>
<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">Groups members</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">-</a>
				<span class="breadcrumb-item active">All groups</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" style="border: thick orange solid;border-radius: 14px;">
			<a href="<?php  echo base_url('Customer_groups/print_group/').$group_id?>" class="btn btn-danger" ><i class="fa fa-file-pdf"></i>Export</a>
			<div class="m-t-25">
<table id="pattern-style-a">
	<tr>
		<td colspan="2">
			<table>
				<tr><td width="40%">Group Name:</td><td><strong><?php  echo  $group->group_name;?></strong></td></tr>
					<tr><td width="40%">Address:</td><td><strong><?php  echo  $group->group_address;?></strong></td></tr>
						<tr><td width="40%">Contact:</td><td><strong><?php  echo  $group->group_contact;?></strong></td></tr>
							<tr><td width="40%">Email:</td><td><strong><?php  echo  $group->group_email;?></strong></td></tr>
				<tr><td>Registered date:</td><td><strong><?php echo $group->group_registered_date; ?></strong></td></tr>

			</table>
		</td>
		<td colspan="4"></td>
		<td colspan="2">
			<table>
			    <tr><td>Village:</td><td><strong><?php echo $group->group_village; ?></strong></td></tr>
				<tr><td>TA:</td><td><strong><?php echo $group->group_ta; ?></strong></td></tr>
					<tr><td>District:</td><td><strong><?php 
					
					 $custdistrict=get_by_id('districts','district_id', $group->group_district );
								if(!empty($custdistrict)){
								echo  $custdistrict->district_name;
								}
					?></strong></td></tr>
				<tr><td>Added by:</td><td><strong><?php echo $group->Firstname." ".$group->Lastname; ?></strong></td></tr>

			</table>
		</td>
	</tr>
</table>
	<table class="table table-bordered"  >
					<thead>
            <tr>
                
		<th>Male</th>

		<th>female</th>

            </tr>
					</thead>
					<tbody>
			<?php
			$malecount=0;
			$female=0;
			
	    $custgender=get_all_customersGroup($group->group_id );
            foreach ($custgender as $g)
            {
                if($g->Gender=="Male")
                {
                   $malecount++; 
                }
                else {
                    	$female++;
                    
                }
                
            }
                ?>
                <tr>
		
			<td>
			    <?php
			    
		
							
								echo  $malecount;
								
								
								
			   ?></td>

			<td><?php   
			
		
			
			
			echo $female;
			
			?></td>

		</tr>
                
					</tbody>
        </table>
				<br>
				<br>
				<br>
				<hr>
				<table class="table table-bordered" id="data-table" >
					<thead>
            <tr>
                <th>No</th>
		<th>Customer</th>

		<th>Date Joined</th>

            </tr>
					</thead>
					<tbody>
			<?php
			$start = 1;
            foreach ($customer_groups_data as $customer_groups)
            {
                ?>
                <tr>
			<td width="80px"><?php echo $start ?></td>
			<td><a href="<?php  echo base_url('individual_customers/view/').$customer_groups->id?>"><?php echo $customer_groups->Firstname.' '.$customer_groups->Lastname ?></a></td>

			<td><?php echo $customer_groups->date_joined ?></td>

		</tr>
                <?php
				$start ++;
            }
            ?>
					</tbody>
        </table>

			</div>
		</div>
	</div>
</div>
