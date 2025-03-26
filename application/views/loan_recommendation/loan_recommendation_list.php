<!doctype html>
<html>
    <head>
        <title>harviacode.com - codeigniter crud generator</title>
        <link rel="stylesheet" href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css') ?>"/>
        <style>
            body{
                padding: 15px;
            }
        </style>
    </head>
    <body>
        <h2 style="margin-top:0px">Loan_recommendation List</h2>
        <div class="row" style="margin-bottom: 10px">
            <div class="col-md-4">
                <?php echo anchor(site_url('loan_recommendation/create'),'Create', 'class="btn btn-primary"'); ?>
            </div>
            <div class="col-md-4 text-center">
                <div style="margin-top: 8px" id="message">
                    <?php echo $this->session->userdata('message') <> '' ? $this->session->userdata('message') : ''; ?>
                </div>
            </div>
            <div class="col-md-1 text-right">
            </div>
            <div class="col-md-3 text-right">
                <form action="<?php echo site_url('loan_recommendation/index'); ?>" class="form-inline" method="get">
                    <div class="input-group">
                        <input type="text" class="form-control" name="q" value="<?php echo $q; ?>">
                        <span class="input-group-btn">
                            <?php 
                                if ($q <> '')
                                {
                                    ?>
                                    <a href="<?php echo site_url('loan_recommendation'); ?>" class="btn btn-default">Reset</a>
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
		<th>Loan Id</th>
		<th>Application Form</th>
		<th>Application Form Comment</th>
		<th>Letter From Auth</th>
		<th>Letter From Auth Comment</th>
		<th>Commitment Fee</th>
		<th>Commitment Fee Comment</th>
		<th>Land Evidence</th>
		<th>Land Evidence Comment</th>
		<th>Offtaker Evidence</th>
		<th>Offtaker Evidence Comment</th>
		<th>Training Recieved</th>
		<th>Training Recieved Comment</th>
		<th>Loans Owed</th>
		<th>Loans Owed Comment</th>
		<th>Community Character</th>
		<th>Community Character Comment</th>
		<th>Date Stamp</th>
		<th>Action</th>
            </tr><?php
            foreach ($loan_recommendation_data as $loan_recommendation)
            {
                ?>
                <tr>
			<td width="80px"><?php echo ++$start ?></td>
			<td><?php echo $loan_recommendation->loan_id ?></td>
			<td><?php echo $loan_recommendation->application_form ?></td>
			<td><?php echo $loan_recommendation->application_form_comment ?></td>
			<td><?php echo $loan_recommendation->letter_from_auth ?></td>
			<td><?php echo $loan_recommendation->letter_from_auth_comment ?></td>
			<td><?php echo $loan_recommendation->commitment_fee ?></td>
			<td><?php echo $loan_recommendation->commitment_fee_comment ?></td>
			<td><?php echo $loan_recommendation->land_evidence ?></td>
			<td><?php echo $loan_recommendation->land_evidence_comment ?></td>
			<td><?php echo $loan_recommendation->offtaker_evidence ?></td>
			<td><?php echo $loan_recommendation->offtaker_evidence_comment ?></td>
			<td><?php echo $loan_recommendation->training_recieved ?></td>
			<td><?php echo $loan_recommendation->training_recieved_comment ?></td>
			<td><?php echo $loan_recommendation->loans_owed ?></td>
			<td><?php echo $loan_recommendation->loans_owed_comment ?></td>
			<td><?php echo $loan_recommendation->community_character ?></td>
			<td><?php echo $loan_recommendation->community_character_comment ?></td>
			<td><?php echo $loan_recommendation->date_stamp ?></td>
			<td style="text-align:center" width="200px">
				<?php 
				echo anchor(site_url('loan_recommendation/read/'.$loan_recommendation->loan_recomendation_id),'Read'); 
				echo ' | '; 
				echo anchor(site_url('loan_recommendation/update/'.$loan_recommendation->loan_recomendation_id),'Update'); 
				echo ' | '; 
				echo anchor(site_url('loan_recommendation/delete/'.$loan_recommendation->loan_recomendation_id),'Delete','onclick="javasciprt: return confirm(\'Are You Sure ?\')"'); 
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
    </body>
</html>