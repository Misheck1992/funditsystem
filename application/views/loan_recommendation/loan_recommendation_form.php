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
        <h2 style="margin-top:0px">Loan_recommendation <?php echo $button ?></h2>
        <form action="<?php echo $action; ?>" method="post">
	    <div class="form-group">
            <label for="int">Loan Id <?php echo form_error('loan_id') ?></label>
            <input type="text" class="form-control" name="loan_id" id="loan_id" placeholder="Loan Id" value="<?php echo $loan_id; ?>" />
        </div>
	    <div class="form-group">
            <label for="int">Application Form <?php echo form_error('application_form') ?></label>
            <input type="text" class="form-control" name="application_form" id="application_form" placeholder="Application Form" value="<?php echo $application_form; ?>" />
        </div>
	    <div class="form-group">
            <label for="application_form_comment">Application Form Comment <?php echo form_error('application_form_comment') ?></label>
            <textarea class="form-control" rows="3" name="application_form_comment" id="application_form_comment" placeholder="Application Form Comment"><?php echo $application_form_comment; ?></textarea>
        </div>
	    <div class="form-group">
            <label for="int">Letter From Auth <?php echo form_error('letter_from_auth') ?></label>
            <input type="text" class="form-control" name="letter_from_auth" id="letter_from_auth" placeholder="Letter From Auth" value="<?php echo $letter_from_auth; ?>" />
        </div>
	    <div class="form-group">
            <label for="letter_from_auth_comment">Letter From Auth Comment <?php echo form_error('letter_from_auth_comment') ?></label>
            <textarea class="form-control" rows="3" name="letter_from_auth_comment" id="letter_from_auth_comment" placeholder="Letter From Auth Comment"><?php echo $letter_from_auth_comment; ?></textarea>
        </div>
	    <div class="form-group">
            <label for="int">Commitment Fee <?php echo form_error('commitment_fee') ?></label>
            <input type="text" class="form-control" name="commitment_fee" id="commitment_fee" placeholder="Commitment Fee" value="<?php echo $commitment_fee; ?>" />
        </div>
	    <div class="form-group">
            <label for="commitment_fee_comment">Commitment Fee Comment <?php echo form_error('commitment_fee_comment') ?></label>
            <textarea class="form-control" rows="3" name="commitment_fee_comment" id="commitment_fee_comment" placeholder="Commitment Fee Comment"><?php echo $commitment_fee_comment; ?></textarea>
        </div>
	    <div class="form-group">
            <label for="int">Land Evidence <?php echo form_error('land_evidence') ?></label>
            <input type="text" class="form-control" name="land_evidence" id="land_evidence" placeholder="Land Evidence" value="<?php echo $land_evidence; ?>" />
        </div>
	    <div class="form-group">
            <label for="land_evidence_comment">Land Evidence Comment <?php echo form_error('land_evidence_comment') ?></label>
            <textarea class="form-control" rows="3" name="land_evidence_comment" id="land_evidence_comment" placeholder="Land Evidence Comment"><?php echo $land_evidence_comment; ?></textarea>
        </div>
	    <div class="form-group">
            <label for="int">Offtaker Evidence <?php echo form_error('offtaker_evidence') ?></label>
            <input type="text" class="form-control" name="offtaker_evidence" id="offtaker_evidence" placeholder="Offtaker Evidence" value="<?php echo $offtaker_evidence; ?>" />
        </div>
	    <div class="form-group">
            <label for="offtaker_evidence_comment">Offtaker Evidence Comment <?php echo form_error('offtaker_evidence_comment') ?></label>
            <textarea class="form-control" rows="3" name="offtaker_evidence_comment" id="offtaker_evidence_comment" placeholder="Offtaker Evidence Comment"><?php echo $offtaker_evidence_comment; ?></textarea>
        </div>
	    <div class="form-group">
            <label for="int">Training Recieved <?php echo form_error('training_recieved') ?></label>
            <input type="text" class="form-control" name="training_recieved" id="training_recieved" placeholder="Training Recieved" value="<?php echo $training_recieved; ?>" />
        </div>
	    <div class="form-group">
            <label for="training_recieved_comment">Training Recieved Comment <?php echo form_error('training_recieved_comment') ?></label>
            <textarea class="form-control" rows="3" name="training_recieved_comment" id="training_recieved_comment" placeholder="Training Recieved Comment"><?php echo $training_recieved_comment; ?></textarea>
        </div>
	    <div class="form-group">
            <label for="int">Loans Owed <?php echo form_error('loans_owed') ?></label>
            <input type="text" class="form-control" name="loans_owed" id="loans_owed" placeholder="Loans Owed" value="<?php echo $loans_owed; ?>" />
        </div>
	    <div class="form-group">
            <label for="loans_owed_comment">Loans Owed Comment <?php echo form_error('loans_owed_comment') ?></label>
            <textarea class="form-control" rows="3" name="loans_owed_comment" id="loans_owed_comment" placeholder="Loans Owed Comment"><?php echo $loans_owed_comment; ?></textarea>
        </div>
	    <div class="form-group">
            <label for="int">Community Character <?php echo form_error('community_character') ?></label>
            <input type="text" class="form-control" name="community_character" id="community_character" placeholder="Community Character" value="<?php echo $community_character; ?>" />
        </div>
	    <div class="form-group">
            <label for="community_character_comment">Community Character Comment <?php echo form_error('community_character_comment') ?></label>
            <textarea class="form-control" rows="3" name="community_character_comment" id="community_character_comment" placeholder="Community Character Comment"><?php echo $community_character_comment; ?></textarea>
        </div>
	    <div class="form-group">
            <label for="datetime">Date Stamp <?php echo form_error('date_stamp') ?></label>
            <input type="text" class="form-control" name="date_stamp" id="date_stamp" placeholder="Date Stamp" value="<?php echo $date_stamp; ?>" />
        </div>
	    <input type="hidden" name="loan_recomendation_id" value="<?php echo $loan_recomendation_id; ?>" /> 
	    <button type="submit" class="btn btn-primary"><?php echo $button ?></button> 
	    <a href="<?php echo site_url('loan_recommendation') ?>" class="btn btn-default">Cancel</a>
	</form>
    </body>
</html>