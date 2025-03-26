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
        <h2 style="margin-top:0px">Rescheduled_payments <?php echo $button ?></h2>
        <form action="<?php echo $action; ?>" method="post">
	    <div class="form-group">
            <label for="varchar">Loan Id <?php echo form_error('loan_id') ?></label>
            <input type="text" class="form-control" name="loan_id" id="loan_id" placeholder="Loan Id" value="<?php echo $loan_id; ?>" />
        </div>
	    <div class="form-group">
            <label for="varchar">Customer Id <?php echo form_error('customer_id') ?></label>
            <input type="text" class="form-control" name="customer_id" id="customer_id" placeholder="Customer Id" value="<?php echo $customer_id; ?>" />
        </div>
	    <div class="form-group">
            <label for="varchar">Customer Type <?php echo form_error('customer_type') ?></label>
            <input type="text" class="form-control" name="customer_type" id="customer_type" placeholder="Customer Type" value="<?php echo $customer_type; ?>" />
        </div>
	    <div class="form-group">
            <label for="int">Payment Number <?php echo form_error('payment_number') ?></label>
            <input type="text" class="form-control" name="payment_number" id="payment_number" placeholder="Payment Number" value="<?php echo $payment_number; ?>" />
        </div>
	    <div class="form-group">
            <label for="decimal">Payment Amount <?php echo form_error('payment_amount') ?></label>
            <input type="text" class="form-control" name="payment_amount" id="payment_amount" placeholder="Payment Amount" value="<?php echo $payment_amount; ?>" />
        </div>
	    <div class="form-group">
            <label for="date">Payment Date <?php echo form_error('payment_date') ?></label>
            <input type="text" class="form-control" name="payment_date" id="payment_date" placeholder="Payment Date" value="<?php echo $payment_date; ?>" />
        </div>
	    <div class="form-group">
            <label for="enum">Pay Status <?php echo form_error('pay_status') ?></label>
            <input type="text" class="form-control" name="pay_status" id="pay_status" placeholder="Pay Status" value="<?php echo $pay_status; ?>" />
        </div>
	    <div class="form-group">
            <label for="decimal">Paid Amount <?php echo form_error('paid_amount') ?></label>
            <input type="text" class="form-control" name="paid_amount" id="paid_amount" placeholder="Paid Amount" value="<?php echo $paid_amount; ?>" />
        </div>
	    <div class="form-group">
            <label for="datetime">P Stamp <?php echo form_error('p_stamp') ?></label>
            <input type="text" class="form-control" name="p_stamp" id="p_stamp" placeholder="P Stamp" value="<?php echo $p_stamp; ?>" />
        </div>
	    <input type="hidden" name="rescheduled_payments_id" value="<?php echo $rescheduled_payments_id; ?>" /> 
	    <button type="submit" class="btn btn-primary"><?php echo $button ?></button> 
	    <a href="<?php echo site_url('rescheduled_payments') ?>" class="btn btn-default">Cancel</a>
	</form>
    </body>
</html>