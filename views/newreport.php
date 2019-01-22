<?php include('_header.php');?>
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
                <li><a href="index.php">Incident Map</a></li>
                <li><a href="myreports.php">My Reports</a></li>
                <li class="active"><a href="newreport.php">New Report</a></li>
<?php if ($login->isUserAdmin() == true) {
    echo '<li><a href="dashboard.php">Dashboard</a></li>';
}?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                <a href="#" class="navbar-brand dropdown-toggle user-img" data-toggle="dropdown"><?php echo $_SESSION['user_name'] . " " . $login->user_gravatar_image_tag?></a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li role="presentation"><a class="editUser" data-toggle="modal" data-target="#edit-modal" role="menuitem" data-tabindex="-1" href="#">Edit Profile</a></li>
                        <li role="presentation" class="divider"></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?logout">Log out</a></li>
                    </ul>
                </li>
            </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
        </nav>
    </header>

    <h2 class="coloured-banner2 text-center">New Report</h2>
    <div class="container push-down">
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3">
                <form method="post" enctype="multipart/form-data" action="newreport.php" id="ajaxform" name="ajaxform" role="form">
                    <div class="form-group">
                        <!--<label for="title_field">Τίτλος</label>-->
                        <input placeholder="Title" class="form-control" id="title_field" type="text" name="title">
                    </div>
                    <div class="form-group">
                        <!--<label for="category_field">Κατηγορία</label>-->
                        <select id="category_field" class="form-control" name="category">
                        <option value="" disabled selected>Category</option>
                        <?php
                            require_once('config/connect.php');

                            $stmt = $pdo->prepare("SELECT * FROM web_categories");
                            if ($stmt->execute()) {
								$stmt->fetch(); //do not show the first uncategorized category
                                while ($row = $stmt->fetch()) {
                        ?>
                            <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                        <?php
                                }
                            }
                        ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <!--<label for="description_field">Περιγραφή</label>-->
                        <textarea placeholder="Description" rows="4" class="form-control" id="description_field" name="description"></textarea>
                    </div>
                    <div class="form-group">
                        <div id="map-container" style="height: 300px; width: 100%;">
                        </div>
                        <input class="hidden" id="lat_field1" type="text" name="latitude">
                        <input class="hidden" id="lon_field1" type="text" name="longitude">
                        <span id="debug"></span>
                    </div>
                    <div class="form-group clearfix" id="addImages">
                        <span id="add-file-input" class="fontawesome-plus pull-left"></span>
                        <input type="file" accept="image/*;capture=camera" name="images[]">
                    </div>
                    <button type="submit" class="btn btn-primary" name="newreport">Submit</button>
                </form>
                <div class="alert alert-danger hidden" id="errors">
                    errors
                </div>
            </div>
        </div>
    </div>

	<!-- Modal -->
	<div class="modal fade" id="edit-modal" tabindex="-1" role="dialog" aria-labelledby="edit-modal-label" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 id="modalUserHeader" class="modal-title" id="myModalLabel">Edit</h4>
		</div>
		<div id="modalUserForm" class="modal-body">
		</div>
		</div> <!-- close modal-content -->
	</div> <!-- close modal-dialog -->
	</div> <!-- close modal -->

    <div class="page-footer page-footer-oceangreen">
        <p>© 2018 ABC Technology. All Rights Reserved.</p>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="views/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBodIPKAMDeUfjIqOPvqploWLkR_RI5wvQ"sensor=false"></script>
    <script src="views/js/newreport.js"></script>
  </body>
</html>
