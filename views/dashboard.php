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
                <li><a href="index.php">Incident Map</a></li>
                <li><a href="myreports.php#">My Reports</a></li>
                <li><a href="newreport.php">New Report</a></li>
                <li class="active"><a href="#">Dashboard</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                <a href="#" class="navbar-brand dropdown-toggle user-img" data-toggle="dropdown"><?php echo $_SESSION['user_name'] . " " . $login->user_gravatar_image_tag?></a>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li role="presentation"><a class="editUser" data-toggle="modal" data-target="#edit-modal" role="menuitem" data-tabindex="-1" href="#">Edit profile</a></li>
                        <li role="presentation" class="divider"></li>
                        <li role="presentation"><a role="menuitem" tabindex="-1" href="index.php?logout">Logout</a></li>
                    </ul>
                </li>
            </ul>
            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
        </nav>
    </header>

	<body>
    <h2 class="coloured-banner3 text-center">Dashboard</h2>
    <div class="container">
		<div class="row">
            <div class="col-md-12">
                <div id="content-submited"></div>
                <div id="page-selection-submited"></div>
            </div>
        </div>
       
        <div class="row">
            <div class="col-md-10">
                <div id="content-open"></div>
                <div id="page-selection-open"></div>
            </div>
            <div class="col-md-2">
                <h3>Categories <small>Click here to edit</small></h3>
				<div id="categories" class="form-group">
<?php
foreach($controls->categories as $name){
?>
	<div class="btn-group btn-group-sm btn-group-xs">
		<button id="<?php echo $name['id']; ?>" type="button" class="category btn btn-default"><span class="editable"><?php echo $name['name']; ?></span></button>
    	<button type="button" class="catRemove btn btn-danger"><span class="glyphicon glyphicon-remove"></span></button>
	</div>
<?php
}
?>
				</div>
                <form id="categoryForm" role="form" method="post">
                    <div class="form-group">
                        <label for="categoryNameId">New Category Name</label>
                        <input type="text" id="categoryNameId" name="categoryName" class="form-control">
                    </div>
                    <button type="submit" name="categorySubmit" class="btn btn-primary">Add</button>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div id="content-closed"></div>
                <div id="page-selection-closed"></div>
            </div>
        </div>
		<div class ="row">
			<div class="col-md-12">
				<h3>Users</h3>
				<div class="table-responsive">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Id</th>
								<th>Username</th>
								<th>e-mail</th>
								<th>Registration Date</th>
                                <th class="hidden">Full name</th>
                                <th class="hidden">Phone</th>
								<th>User Type</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
<?php
foreach($controls->users as $user){
?>
								<tr>
									<td class="uid"><?php echo $user["user_id"]; ?></td>
									<td class="name"><?php echo $user["user_name"]; ?></td>
									<td class="email"><?php echo $user["user_email"]; ?></td>
									<td><?php echo $user["user_registration_datetime"]; ?></td>

                                    <td class="fname hidden"><?php echo $user["user_fullname"]; ?></td>
                                    <td class="phone hidden"><?php echo $user["user_phone"]; ?></td>

									<td><?php if ($user["user_type"] == 1)echo "Admin";else echo "Assigned User"; ?></td>
									<td>
										<a class="editUser btn btn-warning" data-toggle="modal" data-target="#edit-modal" href="#"><span class="glyphicon glyphicon-pencil"></span></a>
										<a class="delUser btn btn-danger" href="#"><span class="glyphicon glyphicon-remove"></span></a>
									</td>
								</tr>
<?php
}
?>
						</tbody>
					</table>
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
			<h4 id="modalUserHeader" class="modal-title" id="myModalLabel">Edit profile</h4>
		</div>
		<div id="modalUserForm" class="modal-body">
		</div>
		</div> <!-- close modal-content -->
	</div> <!-- close modal-dialog -->
	</div> <!-- close modal -->

    <div class="page-footer page-footer-green">
        <p>© 2018 ABC Technology. All Rights Reserved.</p>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="views/js/bootstrap.min.js"></script>
    <script src="views/js/lightbox.min.js"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBodIPKAMDeUfjIqOPvqploWLkR_RI5wvQ"sensor=false"></script>
    <script src="views/js/jquery.bootpag.min.js"></script>
    <script src="views/js/dashboard.js"></script>
  </body>
</html>
