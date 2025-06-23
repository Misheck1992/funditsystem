<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Finance Realm</title>

	<!-- Favicon -->
	<link rel="shortcut icon" href="<?php echo base_url('admin_assets')?>/images/logo/favicon.png">

	<!-- page css -->

	<!-- Core css -->
	<link href="<?php echo base_url('admin_assets')?>/css/app.min.css" rel="stylesheet">
	<link href="<?php echo base_url('admin_assets')?>/css/style.css" rel="stylesheet">
	<style>
		.login-container {
			min-height: 100vh;
			background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
			display: flex;
			align-items: center;
			justify-content: center;
		}

		.login-card {
			border-radius: 16px;
			border: none;
			box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
			overflow: hidden;
			max-width: 450px;
			width: 100%;
		}

		.card-header {
			background-color: #0268bc;
			padding: 25px;
			text-align: center;
		}

		.card-header img {
			max-height: 60px;
		}

		.card-body {
			padding: 35px;
			background-color: #fff;
		}

		.form-group {
			margin-bottom: 25px;
		}

		.form-control {
			height: 50px;
			border-radius: 8px;
			border: 1px solid #e0e0e0;
			padding-left: 15px;
			transition: all 0.3s;
		}

		.form-control:focus {
			border-color: #0268bc;
			box-shadow: 0 0 0 0.2rem rgba(2, 104, 188, 0.15);
		}

		.input-icon {
			position: relative;
		}

		.input-icon i {
			position: absolute;
			left: 15px;
			top: 50%;
			transform: translateY(-50%);
			color: #0268bc;
		}

		.input-icon input {
			padding-left: 45px;
		}

		.btn-primary {
			background-color: #0268bc;
			border-color: #0268bc;
			border-radius: 8px;
			height: 50px;
			font-weight: 600;
			letter-spacing: 0.5px;
			transition: all 0.3s;
		}

		.btn-primary:hover {
			background-color: #0257a1;
			border-color: #0257a1;
			transform: translateY(-2px);
			box-shadow: 0 5px 10px rgba(2, 104, 188, 0.2);
		}

		.alert {
			border-radius: 8px;
			padding: 15px;
		}

		.forget-link {
			color: #0268bc;
			font-weight: 500;
			transition: all 0.3s;
		}

		.forget-link:hover {
			color: #0257a1;
			text-decoration: none;
		}

		.footer {
			text-align: center;
			padding: 15px;
			color: #666;
			font-size: 14px;
		}
	</style>

</head>

<body>
<?php
$settings = get_by_id('settings','settings_id','1');
?>
<div class="app">
	<div class="container-fluid p-h-0 p-v-20 bg full-height d-flex" style="border: thin #0268bc solid style="background-image: url('<?php echo base_url('admin_assets')?>/images/others/login-3.png')">
		<div class="d-flex flex-column justify-content-between w-100">
			<div class="container d-flex h-100">
				<div class="row align-items-center w-100">
					<div class="col-md-7 col-lg-5 m-h-auto">
						<div class="card shadow-lg">
							<div class="card-body" style="padding:2em; border: solid #0268bc thick;border-radius: 50px 0px 50px 0px;">
								<div class="d-flex align-items-center justify-content-between m-b-30">
									<img class="img-fluid" alt="" src="<?php echo base_url('uploads/'.$settings->logo)?>">
									<h2 class="m-b-0">Login</h2>
								</div>
							<?php
								if($this->session->flashdata('active')){
									?>
									<div class="alert-danger" style="padding: 2em;">
										Sorry another session with your credentials is on,Please log out or ask admin to clear you
									</div>
									<?php
								}
								?>
								<?php
								if($this->session->flashdata('error')){
									?>
									<div class="alert alert-danger text-black-50" role="alert">
										Sorry !, Either email or password is not correct;
									</div>
									<?php
								}
								?>
								<form method="post" action="<?php echo base_url('Auth/authenticate')?>">
									<div class="form-group">
										<label class="font-weight-semibold" for="userName">Username:<?php echo form_error('username') ?></label>
										<div class="input-affix">
											<i class="prefix-icon anticon anticon-user"></i>
											<input required type="text" name="username" class="form-control" id="userName" placeholder="Enter username">
										</div>
									</div>
									<div class="form-group">
										<label class="font-weight-semibold" for="password">Password:<?php echo form_error('username') ?></label>
										<a class="float-right font-size-13 text-muted" href="<?php echo base_url('Auth/forget_password')?>">Forget Password?</a>
										<div class="input-affix m-b-10">
											<i class="prefix-icon anticon anticon-lock"></i>
											<input  required type="password"  name="password" class="form-control" id="password" placeholder="Password">
										</div>
									</div>
									<div class="form-group">
										<div class="d-flex align-items-center justify-content-between">

											<button class="btn  btn-block btn-sm" style="border:thin #0268bc solid; padding: 1em; background-color: #0268bc; color: white;">Sign In</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="d-none d-md-flex p-h-40 justify-content-between">
				<span class="">©    </span>
				<ul class="list-inline">
					<li class="list-inline-item">
						<a class="text-dark text-link" href="#">  </a>
					</li>
					<li class="list-inline-item">
						<a class="text-dark text-link" href="#">   </a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>


<!-- Core Vendors JS -->
<script src="<?php echo base_url('admin_assets')?>/js/vendors.min.js"></script>

<!-- page js -->

<!-- Core JS -->
<script src="<?php echo base_url('admin_assets')?>/js/app.min.js"></script>

</body>
</html>
