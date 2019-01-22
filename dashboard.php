<?php

// check for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit('Sorry, this script does not run on a PHP version smaller than 5.3.7 !');
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once('libraries/password_compatibility_library.php');
}
// include the config
require_once('config/config.php');

// load the login class
require_once('classes/Login.php');

// load the login class
require_once('classes/adminControls.php');

// create a login object. when this object is created, it will do all login/logout stuff automatically
// so this single line handles the entire login process.
$login = new Login();

$controls = new adminControls();

// ... ask if we are logged in here:
if ($login->isUserLoggedIn() == true) {
    // the user is logged in. you can do whatever you want here.
	// for demonstration purposes, we simply show the "you are logged in" view.
	if ($controls->showpage){//else just returns to ajax posts
		if ($login->isUserAdmin() == true) {
			include("views/dashboard.php");
		} else {
			header("Location:index.php");
			die();
		}
	}
} else {
    // the user is not logged in. you can do whatever you want here.
    // for demonstration purposes, we simply show the "you are not logged in" view.
    //include("views/not_logged_in.php");
    header("Location:index.php");
    die();
}
