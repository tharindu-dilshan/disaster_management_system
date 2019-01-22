<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WebProject - Disaster Management System</title>

    <!-- Bootstrap -->
    <link href="views/css/bootstrap.min.css" rel="stylesheet">

    <link href="views/css/styles.css" rel="stylesheet">
    <link href="views/css/lightbox.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700" rel="stylesheet">

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
        // show potential errors / feedback (from login object)
        if (isset($login)) {
            if ($login->errors) {
                foreach ($login->errors as $error) {
                    echo '<div class="popup alert-danger"><strong>Oh snap! </strong>'.$error.'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button></div>';
                }
            }
            if ($login->messages) {
                foreach ($login->messages as $message) {
                    echo '<div class="popup alert-info">'.$message.'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button></div>';
                }
            }
        }
        // show potential errors / feedback (from report object)
        if (isset($report)) {
            if ($report->errors) {
                foreach ($report->errors as $error) {
                    echo '<div class="popup alert-danger"><strong>Oh snap! </strong>'.$error.'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button></div>';
                }
            }
            if ($report->messages) {
                foreach ($report->messages as $message) {
                    echo '<div class="popup alert-info">'.$message.'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button></div>';
                }
            }
        }
        // show potential errors / feedback (from adminControls object)
        if (isset($controls)) {
            if ($controls->errors) {
                foreach ($controls->errors as $error) {
                    echo '<div class="popup alert-danger"><strong>Oh snap! </strong>'.$error.'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button></div>';
                }
            }
            if ($controls->messages) {
                foreach ($controls->messages as $message) {
                    echo '<div class="popup alert-info">'.$message.'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button></div>';
                }
            }
        }

        // show potential errors / feedback (from Registration object)
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
