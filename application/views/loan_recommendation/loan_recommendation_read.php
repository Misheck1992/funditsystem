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
        <h2 style="margin-top:0px">Loan_recommendation Read</h2>
        <table class="table">
	    <tr><td>Loan Id</td><td><?php echo $loan_id; ?></td></tr>
	    <tr><td>Application Form</td><td><?php echo $application_form; ?></td></tr>
	    <tr><td>Application Form Comment</td><td><?php echo $application_form_comment; ?></td></tr>
	    <tr><td>Letter From Auth</td><td><?php echo $letter_from_auth; ?></td></tr>
	    <tr><td>Letter From Auth Comment</td><td><?php echo $letter_from_auth_comment; ?></td></tr>
	    <tr><td>Commitment Fee</td><td><?php echo $commitment_fee; ?></td></tr>
	    <tr><td>Commitment Fee Comment</td><td><?php echo $commitment_fee_comment; ?></td></tr>
	    <tr><td>Land Evidence</td><td><?php echo $land_evidence; ?></td></tr>
	    <tr><td>Land Evidence Comment</td><td><?php echo $land_evidence_comment; ?></td></tr>
	    <tr><td>Offtaker Evidence</td><td><?php echo $offtaker_evidence; ?></td></tr>
	    <tr><td>Offtaker Evidence Comment</td><td><?php echo $offtaker_evidence_comment; ?></td></tr>
	    <tr><td>Training Recieved</td><td><?php echo $training_recieved; ?></td></tr>
	    <tr><td>Training Recieved Comment</td><td><?php echo $training_recieved_comment; ?></td></tr>
	    <tr><td>Loans Owed</td><td><?php echo $loans_owed; ?></td></tr>
	    <tr><td>Loans Owed Comment</td><td><?php echo $loans_owed_comment; ?></td></tr>
	    <tr><td>Community Character</td><td><?php echo $community_character; ?></td></tr>
	    <tr><td>Community Character Comment</td><td><?php echo $community_character_comment; ?></td></tr>
	    <tr><td>Date Stamp</td><td><?php echo $date_stamp; ?></td></tr>
	    <tr><td></td><td><a href="<?php echo site_url('loan_recommendation') ?>" class="btn btn-default">Cancel</a></td></tr>
	</table>
        </body>
</html>