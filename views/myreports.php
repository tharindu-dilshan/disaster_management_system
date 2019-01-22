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
                <li class="active"><a href="myreports.php">My Reports</a></li>
                <li><a href="newreport.php">New Report</a></li>

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

    <h2 class="coloured-banner1 text-center">My reports</h2>
    <div class="container">
        <div id="accordion" class="panel-group">
<?php
    require_once('config/connect.php');

    $stmt = $pdo->prepare("SELECT * FROM web_reports INNER JOIN web_report_details on web_reports.id=web_report_details.report_id WHERE submitter_id=:sid ORDER BY time_submitted DESC");
    $stmt->bindParam(':sid', $_SESSION['user_id']);
    if ($stmt->execute()) {
        while ($row = $stmt->fetch()) {
            $imgStmt = $pdo->prepare("SELECT path FROM web_report_images WHERE report_id=:rid");
            $imgStmt->bindParam(':rid', $row['id']);
            $reportClosed = $row["status"] == 'Closed';
            if ($imgStmt->execute()) {
                $images = $imgStmt->fetchAll();
            }

            // format times
            $submitDate = date('d/n @ H:i', strtotime($row["time_submitted"]));
            $closeDate = date('d/n @ H:i', strtotime($row["time_closed"]));

            // find user who closed the report
            if ($reportClosed) {
                $adminStmt = $pdo->prepare("SELECT user_name FROM web_users WHERE user_id = " . $row["closer_id"]);
                if ($adminStmt->execute()) {
                    $adminRow = $adminStmt->fetch(PDO::FETCH_ASSOC);
                    $admin = $adminRow["user_name"];
                }
            }

?>

    <article class="report panel panel-<?php if($reportClosed){echo "success";}else if($row["status"] == 'Submited'){echo "warning";}else{echo "danger";} ?>" id="report-<?php echo $row['id']; ?>">
        <div class="panel-heading" data-toggle="collapse" data-target="#report-<?php echo $row['id']; ?>-content" data-parent="#accordion">
        <h3 class="panel-title"><?php echo $row['title']; ?> <span class="small pull-right"><?php echo $submitDate; ?></span></h3>
        </div>
        <div id="report-<?php echo $row['id']; ?>-content" class="collapse">
        <div class="panel-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3">
                        <div class="small-map-data hidden">
                            <span data-lat="<?php echo $row['latitude']; ?>"></span>
                            <span data-long="<?php echo $row['longitude']; ?>"></span>
                        </div>
                        <div class="small-map-view"></div>
                    </div>
                    <div class="col-md-9">
                        <p>
                        <?php echo $row['description']; ?>
                        </p>
                        <div class="image-grid clearfix">
                            <?php
                            $imgId = 0;
                            foreach($images as $reportImage) {
                            $imgId++;
                            ?>
                            <a href="<?php echo $reportImage['path']; ?>" data-lightbox="report-<?php echo $row['id']; ?>"><img src="<?php echo $reportImage['path'];?>" alt=""></a>
                            <?php } ?>
                        </div>
<?php
                            if ($reportClosed) {
?>
                        <blockquote class="comment">
                            <p><?php echo $row["comment"]; ?></p>
                            <footer><?php echo $admin; ?>, <?php echo $closeDate; ?></footer>
                        </blockquote>
                        <span class="label label-success">Closed</span>
<?php
                            } else if($row["status"] == 'Submited'){
								?>
								<span class="label label-warning">Submited</span>
								<?php
								}else{
?>
                        <span class="label label-danger">Open</span>
<?php
                            }
?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </article>

<?php
        }
    }
?>
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

	<div id="stats">
	</div>

    <div class="page-footer page-footer-red">
        <p>© 2018 ABC Technology. All Rights Reserved.</p>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="views/js/lightbox.min.js"></script>
    <script src="views/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBodIPKAMDeUfjIqOPvqploWLkR_RI5wvQ"sensor=false"></script>
    <script src="views/js/myreports.js"></script>
  </body>
</html>
