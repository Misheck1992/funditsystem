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
        <h2 style="margin-top:0px">Documents <?php echo $button ?></h2>
        <form action="<?php echo $action; ?>" method="post">
	    <div class="form-group">
            <label for="int">P Lid <?php echo form_error('p_lid') ?></label>
            <input type="text" class="form-control" name="p_lid" id="p_lid" placeholder="P Lid" value="<?php echo $p_lid; ?>" />
        </div>
	    <div class="form-group">
            <label for="varchar">Document Name <?php echo form_error('document_name') ?></label>
            <input type="text" class="form-control" name="document_name" id="document_name" placeholder="Document Name" value="<?php echo $document_name; ?>" />
        </div>
	    <div class="form-group">
            <label for="varchar">File Link <?php echo form_error('file_link') ?></label>
            <input type="text" class="form-control" name="file_link" id="file_link" placeholder="File Link" value="<?php echo $file_link; ?>" />
        </div>
	    <div class="form-group">
            <label for="timestamp">Stamp <?php echo form_error('stamp') ?></label>
            <input type="text" class="form-control" name="stamp" id="stamp" placeholder="Stamp" value="<?php echo $stamp; ?>" />
        </div>
	    <input type="hidden" name="id" value="<?php echo $id; ?>" /> 
	    <button type="submit" class="btn btn-primary"><?php echo $button ?></button> 
	    <a href="<?php echo site_url('documents') ?>" class="btn btn-default">Cancel</a>
	</form>
    </body>
</html>