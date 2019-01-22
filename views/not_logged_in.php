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
            <ul class="nav navbar-nav navbar-right">
                <li><a href="rss/index.php" title="RSS Feed"><span class="fontawesome-rss"></span></a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Login</a>
                    <ul class="dropdown-menu dropdown-login">
                        <form class="form" method="post" action="index.php" name="loginform">
                            <div class="form-group">
                            <input type="text" placeholder="Email" class="form-control" id="user_email" name="user_email">
                            </div>
                            <div class="form-group">
                            <input type="password" placeholder="Password" class="form-control" id="user_password" name="user_password">
                            </div>
                            <div class="form-group fix">
                                <?php
                                //require_once('/opt/lampp/htdocs/reportr/libraries/recaptchalib.php');
                                //$publickey = "6LfM3PESAAAAANjOP00eJDS1h2ScuONJrXjXJFqn"; // you got this from the signup page
                                //echo recaptcha_get_html($publickey);
                                //?>
                            </div>
                            <label class="checkbox">
                                <input type="checkbox" id="user_rememberme" name="user_rememberme" value="1"> Remember me </input>
                            </label>
                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#registerModal">Sign Up</button>
                            <button type="submit" name="login" class="btn btn-primary pull-right" >Login</button>
                        </form>
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
                    <form method="post" action="index.php" name="registerform">
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
                            <p class="help-block">Alphanumeric with at least 6 characters</p>
                        </div>

                        <div class="form-group">
                            <label for="user_password_repeat">Password (Repeat): </label>
                            <input class="form-control" id="user_password_repeat" type="password" name="user_password_repeat" pattern=".{6,}" required autocomplete="off" >
						</div>

						<div class="form-group">
                            <label for="user_fullname">Full Name: </label>
                            <input class="form-control" id="user_fullname" type="text" name="user_fullname" autocomplete="off" placeholder="Optional">
                        </div>

                        <div class="form-group">
                            <label for="user_phone">Phone: </label>
                            <input class="form-control" id="user_phone" type="text" name="user_phone" pattern="\d*" autocomplete="off" placeholder="Optional">
                        </div>


                        <button class="btn btn-primary" type="submit" name="register">Sign up</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

<?php include('_footer.php'); ?>
