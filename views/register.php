<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>WebProject - Disaster Management System</title>

<!-- Bootstrap -->
<link href="views/css/bootstrap.min.css" rel="stylesheet">

<link href="views/css/reportr.css" rel="stylesheet">
<link rel="stylesheet" href="http://weloveiconfonts.com/api/?family=fontawesome">
<link href='http://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,greek' rel='stylesheet' type='text/css'>

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

<div id="errors">
<?php

// show potential errors / feedback (from registration object)
if (isset($registration)) {
    if ($registration->errors) {
        foreach ($registration->errors as $error) {
            echo '<div class="popup alert-danger"><strong>Oh snap! </strong>'.$error.'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button></div>';
        }
    }
    if ($registration->messages) {
        foreach ($registration->messages as $message) {
            echo '<div class="popup alert-info">'.$message.'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button></div>';
        }
    }
}
?>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <!-- show registration form, but only if we didn't submit already -->
<?php if (!$registration->registration_successful ) { ?>
            <form method="post" action="register.php" name="registerform">
                <div class="form-group">
                    <label for="user_name">Username: </label>
                    <input class="form-control" id="user_name" type="text" pattern="[a-zA-Z0-9]{2,64}" name="user_name" required >
                </div>

                <div class="form-group">
                    <label for="user_email">E-mail: </label>
                    <input class="form-control" id="user_email" type="email" name="user_email" required >
                </div>

                <div class="form-group">
                    <label for="user_password_new">Password: </label>
                    <input class="form-control" id="user_password_new" type="password" name="user_password_new" pattern=".{6,}" required autocomplete="off" >
                    <p class="help-block">Alphanumeric with at least 6 characters long.</p>
                </div>

                <div class="form-group">
                    <label for="user_password_repeat">Password (Repeat): </label>
                    <input class="form-control" id="user_password_repeat" type="password" name="user_password_repeat" pattern=".{6,}" required autocomplete="off" >
                </div>

                <button class="btn btn-primary" type="submit" name="register">Sign Up</button>
            </form>
<?php } ?>

                <a href="index.php">Go back to home </a>
            </div>
        </div>
</div>

</body>
</html>
