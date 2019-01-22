<?php include('_header.php'); ?>

    <header>
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main-nav">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">Disaster Management System</a>
            </div>

            <div class="collapse navbar-collapse" id="main-nav">
            <ul class="nav navbar-nav navbar-left">
                <li class="active"><a href="index.php">Incident Map</a></li>
                <li><a href="myreports.php#">My Reports</a></li>
                <li><a href="newreport.php">New Report</a></li>

<?php if ($login->isUserAdmin() == true) {
    echo '<li><a href="dashboard.php">Dashboard</a></li>';
}?>

            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="rss/index.php" title="RSS Feed"><span class="fontawesome-rss"></span></a></li>
                <li class="dropdown">
                <a href="#" class="navbar-brand dropdown-toggle user-img" data-toggle="dropdown"><?php echo $_SESSION['user_name'] . " " . $login->user_gravatar_image_tag?></a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li role="presentation"><a class="editUser" data-toggle="modal" data-target="#edit-modal" role="menuitem" data-tabindex="-1" href="#">Edit Profile</a></li>
                        <li role="presentation" class="divider"></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?logout">Log Out</a></li>
                    </ul>
                </li>
            </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
        </nav>
    </header>
    <div class="modal fade" id="registerModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Sign Up</h4>
                </div>
                <div class="modal-body">
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
                            <label for="user_password_new">New Password: </label>
                            <input class="form-control" id="user_password_new" type="password" name="user_password_new" pattern=".{6,}" required autocomplete="off" >
                            <p class="help-block">Alphanumeric with at least 6 characters.</p>
                        </div>

                        <div class="form-group">
                            <label for="user_password_repeat">Password (Repeat): </label>
                            <input class="form-control" id="user_password_repeat" type="password" name="user_password_repeat" pattern=".{6,}" required autocomplete="off" >
                        </div>

                        <button class="btn btn-primary" type="submit" name="register">Sign Up</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

<?php include('_footer.php'); ?>
