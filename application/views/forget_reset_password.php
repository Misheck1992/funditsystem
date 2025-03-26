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

</head>

<body>
<?php
$settings = get_by_id('settings','settings_id','1');
?>
<div class="app">
    <div class="container-fluid p-h-0 p-v-20 bg full-height d-flex" style="background-image: url('<?php echo base_url('admin_assets')?>/images/others/login-3.png')">
        <div class="d-flex flex-column justify-content-between w-100">
            <div class="container d-flex h-100">
                <div class="row align-items-center w-100">
                    <div class="col-md-7 col-lg-5 m-h-auto">
                        <div class="card shadow-lg">
                            <div class="card-body" style="padding:2em; border: solid #24C16B thick;border-radius: 50px 0px 50px 0px;">
                                <div class="d-flex align-items-center justify-content-between m-b-30">
                                    <img class="img-fluid" alt="" src="<?php echo base_url('uploads/'.$settings->logo)?>">
                                    <h2 class="m-b-0">Password reset</h2>
                                </div>


                                <span id="password_error"></span>
                                </div>
                                    <div class="alert-success" style="padding: 2em;">
                                    Enter new  password
                                </div>
                                <form method="post" action="<?php echo base_url('Auth/reset_password_user')?>"  id="passwordForm" >
                                    <div class="form-group">
                                        <label class="font-weight-semibold" for="userName">Password :</label>
                                        <div class="input-affix">
                                            <i class="prefix-icon anticon anticon-user"></i>
                                            <input  required type="password"  name="password" class="form-control" id="password" placeholder="Password">

                                            <input  type="hidden" name="employeeid" class="form-control" value="<?php echo $employeeid ?>">
                                            <input  type="hidden" name="acccesscode" class="form-control" value="<?php echo $acccesscode ?>">
                                        </div>

                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-semibold" for="password">Password Again:</label>

                                        <div class="input-affix m-b-10">
                                            <i class="prefix-icon anticon anticon-lock"></i>
                                            <input  required type="password"   id="confirm_password" name="confirm_password"  class="form-control"  placeholder=" Confirm Password">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="d-flex align-items-center justify-content-between">

                                            <button class="btn  btn-block btn-sm" style="border:thin #24C16B solid; padding: 1em; background-color: #24C16B; color: white;">Submit</button>
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

<!-- JavaScript code -->
<script>
    function validatePassword() {
        var password = document.getElementById("password").value;
        var confirmPassword = document.getElementById("confirm_password").value;

        // Check if passwords match
        if (password !== confirmPassword) {
            document.getElementById("password_error").innerHTML = "<div class='alert-danger' style='padding: 2em'>Passwords do not match</div>";
            return false;
        }

        // Check password length
        if (password.length < 8) {
            document.getElementById("password_error").innerHTML = "<div class='alert-danger' style='padding: 2em'>Password must be at least 8 characters long</div>";
            return false;
        }

        // Check password complexity
        var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/;
        if (!passwordRegex.test(password)) {
            document.getElementById("password_error").innerHTML = "<div class='alert-danger' style='padding: 2em'>Password must contain at least one uppercase letter, one lowercase letter, one number, and one symbol</div>";
            return false;
        }

        // Password is valid
        return true;
    }

    document.getElementById("passwordForm").addEventListener("submit", function(event) {
        if (!validatePassword()) {
            event.preventDefault(); // Prevent the form from submitting if validation fails
        }
    });
</script>


</body>
</html>
