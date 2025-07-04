
<div class="main-content">
	<div class="page-header">
		<h2 class="header-title">Individual customers</h2>
		<div class="header-sub-title">
			<nav class="breadcrumb breadcrumb-dash">
				<a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
				<a class="breadcrumb-item" href="#">-</a>
				<span class="breadcrumb-item active">All individual customers</span>
			</nav>
		</div>
	</div>
	<div class="card">
		<div class="card-body" style="border: thick #24C16B solid;border-radius: 14px;">
            <div>
                <?php
                $products = get_all('geo_countries');
                $officer = get_all('employees');

                ?>
                <form action="<?php echo base_url('Individual_customers/index') ?>" method="get">
                    Keyword: <input name="q" placeholder="ie customer name" value="<?php echo $this->input->get('q');  ?>">
                    Country: <select name="country" id="" class="select2">
                        <option value="">Any Country</option>
                        <?php

                        foreach ($products as $product){
                            ?>
                            <option value="<?php  echo $product->code; ?>" <?php if($this->input->get('country')==$product->code){ echo "selected"; }?>><?php echo $product->name; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                    Gender: <select name="gender" id="">
                        <option value="">Any Gender</option>
                        <option value="MALE" <?php if($this->input->get('gender')=='MALE'){echo "selected";} ?>>MALE</option>
                        <option value="FEMALE" <?php if($this->input->get('gender')=='FEMALE'){echo "selected";} ?>>FEMALE</option>

                    </select>
                    Status: <select name="status" id="">
                        <option value="">Any Status</option>
                        <option value="Approved" <?php if($this->input->get('status')=='Approved'){echo "selected";} ?>>Approved</option>
                        <option value="Not Approved" <?php if($this->input->get('status')=='Not Approved'){echo "selected";} ?>>Not Approved</option>
                        <option value="Rejected" <?php if($this->input->get('status')=='Rejected'){echo "selected";} ?>>Rejected</option>
                        <option value="Archived' <?php if($this->input->get('status')=='Archived'){echo "selected";} ?>">Archived</option>
                  

                    </select>
                    Officer: <select name="user" id="" class="select2">
                        <option value="">All officers</option>
                        <?php

                        foreach ($officer as $item){
                            ?>
                            <option value="<?php  echo $item->id; ?>" <?php if($this->input->get('user')==$item->id){echo "selected";} ?>><?php echo $item->Firstname." ".$item->Lastname ?></option>
                            <?php
                        }
                        ?>
                    </select> Registered from:
                    <input type="date" name="from" <?php  echo $this->input->get('from'); ?>> Registered to: <input type="date" name="to" value="<?php  echo $this->input->get('to'); ?>"> <input type="submit" value="filter" name="search"><input type="submit" value="export excel" name="search"><input type="submit" name="search" value="export pdf">
                </form>
            </div>
            <div class="double-scroll">
                    <table id="data-tablem" class="table">
                        <thead>
                        <tr>

                            <th>ClientId</th>
                            <th>Title</th>
                            <th>First name</th>
                            <th>Middle name</th>
                            <th>Last name</th>
                            <th>Gender</th>
                            <th>DateOfBirth</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Marital status</th>
                            <th>Officer</th>
                            <th>Customer Status</th>

                            <th>CreatedOn</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($individual_customers_data as $individual_customers)
                        {
                            ?>
                            <tr>

                                <td><?php echo $individual_customers->ClientId ?></td>
                                <td><?php echo $individual_customers->Title ?></td>
                                <td><?php echo $individual_customers->cfname ?></td>
                                <td><?php echo $individual_customers->cmname ?></td>
                                <td><?php echo $individual_customers->clname ?></td>
                                <td><?php echo $individual_customers->cgender ?></td>
                                <td><?php echo $individual_customers->cdob ?></td>
                                <td><?php echo $individual_customers->cemail  ?></td>
                                <td><?php echo $individual_customers->cphonee  ?></td>
                                <td><?php echo $individual_customers->cmarital   ?></td>
                                <td><?php echo $individual_customers->efname." ".$individual_customers->elname ?></td>

                                <td><?php if($individual_customers->approval_status=="Approved"){echo "<font color='green'>".$individual_customers->approval_status."</font>";}elseif($individual_customers->approval_status=="Not Approved"){echo "<font color='orange'>".$individual_customers->approval_status."</font>";}elseif($individual_customers->approval_status=="Rejected"){echo "<font color='red'>".$individual_customers->approval_status."</font>";}else{echo "<font color='gray'>".$individual_customers->approval_status."</font>";} ?></td>
                                <td><?php echo $individual_customers->CreatedOn ?></td>
                                <td style="text-align:center" width="200px">
                                    <a href="<?php echo base_url('individual_customers/view/'.$individual_customers->id)?>" class="btn btn-info"><i class="os-icon os-icon-eye"></i>View</a>

                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
            </div>


            <div class="row">
                <div class="col-md-6">
                    <a href="#" class="btn btn-primary">Total Record : <?php echo $total_rows ?></a>
                    <?php echo anchor(site_url('individual_customers/excel'), 'Excel', 'class="btn btn-primary"'); ?>
                </div>
                <div class="col-md-6 text-right">
                    <?php echo $pagination ?>
                </div>
            </div>
		</div>
	</div>
</div>
