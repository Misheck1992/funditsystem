<?php
$linkk = base_url('admin_assets/images/pattern.png');
$imgg = 'data:image;base64,'.base64_encode(file_get_contents($linkk))
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head>
	<style>

		p {
			text-align: justify;
			margin:0;
		}
		table {width:100%;}
		table.collapse {
			border-collapse: collapse;
		}

		tr td, tr th {
			text-align: right;
		}

		tr.total {
			font-weight: 900;
		}
		hr {
			margin: 15px 0;
		}
		h1 {
			margin:0;
		}
		.title {
			color: #000;
			font-size: 18px;
			font-weight: normal;
		}

		.section {
			border-bottom: 1px #D4D4D4 solid;
			padding: 10px 0;
			margin-bottom: 20px;
		}

		.section .content {
			margin-left: 10px;
		}

		#hor-minimalist-b
		{
			font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
			font-size: 12px;
			background: #fff;
			width: 480px;
			border-collapse: collapse;
			text-align: center;
		}
		#hor-minimalist-b th
		{
			font-size: 14px;
			font-weight: 900;
			padding: 10px 8px;
			border-bottom: 2px solid #000;
			text-align: center;
		}
		#hor-minimalist-b td
		{
			border-bottom: 1px solid #ccc;
			padding: 6px 8px;
		}

		#pattern-style-a
		{
			font-family: "Lucida Sans Unicode", "Lucida Grande", Sans-Serif;
			font-size: 12px;
			width: 100%;
			text-align: left;
			border-collapse: collapse;
			background: url('<?php echo $imgg; ?>');;
		}

		#pattern-style-a th
		{
			font-size: 13px;
			font-weight: normal;
			padding: 8px;
			border-bottom: 1px solid #fff;
			color: #039;
		}
		#pattern-style-a td
		{
			padding: 3px;
			border-bottom: 1px solid #fff;
			color: #000;
			border-top: 1px solid transparent;
		}
		#pattern-style-a tbody tr:hover td
		{
			color: #339;
			background: #fff;
		}

		* {
			box-sizing: border-box;
		}

		html {
			font-family: sans-serif;
		}


	</style>

</head><body>



<div class="section">
	<div class="content">
		<h1 style="text-align: center;"><?php
			$settings = get_by_id('settings','settings_id','1');
			echo $settings->company_name ?></h1>
		<table width="100%">
			<?php

			$link = base_url('uploads/').$settings->logo;
			$img = 'data:image;base64,'.base64_encode(file_get_contents($link))
			?>
			<tr>
				<td style="float: left;padding-right: 5em; margin-left: 1em;">
					<img src="<?php echo $img; ?>" alt="">
				</td>
				<td style="float: right;margin-left: 5em;">
					<?php echo $settings->address ?>
					<?php echo $settings->company_email ?>/<?php echo $settings->phone_number ?>
				</td>
			</tr>
		</table>
		<hr>
		<h2 style="text-align: center;">Customer Info report</h2>


	</div>
</div>

<div class="section">
	<div class="title">Summary</div>
	<br>
	<div class="content">
		<table class="collapse" id="pattern-style-a">
        
                <tr><td style="text-align: right;padding-right: 10px;" >ClientId</td><td><?php echo $ClientId; ?></td></tr>
                <tr><td style="text-align: right;padding-right: 10px;" >Title</td><td><?php echo $Title; ?></td></tr>
                <tr><td style="text-align: right;padding-right: 10px;" >Firstname</td><td><?php echo $Firstname; ?></td></tr>
                <tr><td style="text-align: right;padding-right: 10px;" >Middle name</td><td><?php echo $Middlename; ?></td></tr>
                <tr><td style="text-align: right;padding-right: 10px;" >Lastname</td><td><?php echo $Lastname; ?></td></tr>
                <tr><td style="text-align: right;padding-right: 10px;" >Gender</td><td><?php echo $Gender; ?></td></tr>
                <tr><td style="text-align: right;padding-right: 10px;" >DateOfBirth</td><td><?php echo $DateOfBirth; ?></td></tr>
                <tr><td style="text-align: right;padding-right: 10px;" >EmailAddress</td><td><?php echo $EmailAddress; ?></td></tr>
                <tr><td style="text-align: right;padding-right: 10px;" >PhoneNumber</td><td><?php echo $PhoneNumber; ?></td></tr>
                <tr><td style="text-align: right;padding-right: 10px;" >AddressLine1</td><td><?php echo $AddressLine1; ?></td></tr>
                <tr><td style="text-align: right;padding-right: 10px;" >AddressLine2</td><td><?php echo $AddressLine2; ?></td></tr>
                <tr><td style="text-align: right;padding-right: 10px;" >AddressLine3</td><td><?php echo $AddressLine3; ?></td></tr>
                <tr><td style="text-align: right;padding-right: 10px;" >Province</td><td><?php echo $Province; ?></td></tr>
                <tr><td style="text-align: right;padding-right: 10px;" >City</td><td><?php echo $City; ?></td></tr>
                <tr><td style="text-align: right;padding-right: 10px;" >Country</td><td><?php echo $Country; ?></td></tr>
                <tr><td style="text-align: right;padding-right: 10px;" >Profession</td><td><?php echo $Profession; ?></td></tr>
                <tr><td style="text-align: right;padding-right: 10px;" >SourceOfIncome</td><td><?php echo $SourceOfIncome; ?></td></tr>
                <tr><td style="text-align: right;padding-right: 10px;" >GrossMonthlyIncome</td><td><?php echo $GrossMonthlyIncome; ?></td></tr>

                <tr><td style="text-align: right;padding-right: 10px;" >LastUpdatedOn</td><td><?php echo $LastUpdatedOn; ?></td></tr>
                <tr><td style="text-align: right;padding-right: 10px;" >CreatedOn</td><td><?php echo $CreatedOn; ?></td></tr>

            </table>
	</div>
</div>
<div style="margin: auto"><strong>********** NOTHING FOLLOWS **********</strong></div>


</body></html>

