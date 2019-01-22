<?php
	require_once('../config/config.php');
        require_once('../config/human_dates.php');
	try {
		$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
	} catch (PDOException $e) {
		echo $e->getMessage();
	}

	// 1. Fetch reports
	$max = 20;
	$count = 0;
	if (isset($_POST['page']))
		$page = $_POST['page'];
	else{
		$page = 1;
		$sql = "SELECT COUNT(*) FROM web_reports WHERE status='Open'";
		$all = $pdo->prepare($sql);
		//den sou xei pei i mama sou na min peirazeis ta pragmata apo ta alla paidakia?
		if($all->execute()){
			$allRes = $all->fetch();
			$count = $allRes[0];
		}
	}

	$sql1 = "
		SELECT web_reports.id, title, time_submitted, web_categories.name, description, latitude, longitude FROM `web_reports` INNER JOIN web_report_details ON web_reports.id=web_report_details.report_id INNER JOIN web_categories ON web_categories.id = web_report_details.category_id WHERE status='Open' ORDER BY category_id, time_submitted DESC LIMIT ". $max*($page-1) . "," . $max;
	$stmt = $pdo->prepare($sql1);
?>

<h3>Open Reports</h3>
<div id="totalopen" class="hidden"><?php echo ceil($count/$max); ?></div>
<div class="table-responsive">
    <table class="table table-striped">
    	<thead>
    		<tr>
    			<th>Reference Number</th>
    			<th>Title</th>
    			<th>Submission Time</th>
    			<th>Category</th>
    			<th>Actions</th>
    		</tr>
    	</thead>
    	<tbody>

    <?php
        if ($stmt->execute()) {
    		$index = 0;
            while ($row = $stmt->fetch()) {
                $imgStmt = $pdo->prepare("SELECT path FROM web_report_images WHERE report_id=:rid");
                $imgStmt->bindParam(':rid', $row['id']);
                if ($imgStmt->execute()) {
                    $images = $imgStmt->fetchAll();
                }
    ?>
    		<tr>
    			<td><?php echo $row["id"]; ?></td>
    			<td><?php echo $row["title"]; ?></td>
    			<td title="<?php echo $row['time_submitted']; ?>">before <?php echo get_date_diff($row["time_submitted"], time()); ?></td>
    			<td><?php echo $row["name"]; ?></td>
    			<td><a data-toggle="modal" data-target="#<?php echo $index; ?>" href="#">Show</a></td>
    		</tr>
    		<!-- Modal -->
    		<div class="modal fade report-modal" id="<?php echo $index; ?>" tabindex="-1" role="dialog" aria-labelledby="<?php echo $row["id"]; ?>label" aria-hidden="true">
    		<div class="modal-dialog">
    			<div class="modal-content">
    			<div class="modal-header">
    				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    				<h4 class="modal-title" id="myModalLabel"><?php echo $row["title"]; ?></h4>
    			</div>
    			<div class="modal-body">
    <!-- to content tou modal einai opws einai sto my reports otan allakseis auto tha allaksw kai auto
    opote to afinw xima gia tin ora, BASIC FUNCTIONALITY ITHELES PARTO -->
    				<div class="small-map-data hidden">
    					<span data-lat="<?php echo $row['latitude']; ?>"></span>
    					<span data-long="<?php echo $row['longitude']; ?>"></span>
    				</div>
    				<div class="small-map-view" id="map<?php echo $index; ?>"></div>
    				<br>
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
    				<span class="label label-danger">Open</span>
    				<br>
    <!--
    				<form id="test" class="modalform" method="post" action="dashboard.php" role="form">
    					<input type="hidden" name="report_id" value="<?php echo $row['id']; ?>">
    					<div class="form-group">
    						<textarea rows="3" class="form-control" name="comment" placeholder="Πρόσθήκη σχολίου"></textarea>
    					</div>
    					<div class="form-group">
    						<button type="submit" class="closebtn btn btn-danger form-control" name="markClosed">Κλείσιμο αναφοράς</button>
    					</div>
    				</form>
    -->
    				<div class="modalform">
    					<span data-id="<?php echo $row['id']; ?>" class="hidden"></span>
    					<div class="form-group">
    						<textarea rows="3" class="form-control" name="comment" placeholder="Add comment"></textarea>
    					</div>
    					<div class="form-group">
    						<button class="reportClosebtn btn btn-success form-control" name="markClosed" >Close Report</button>
    					</div>
    				</div>
    			</div>
    			</div> <!-- close modal-content -->
    		</div> <!-- close modal-dialog -->
    		</div> <!-- close modal -->
    <?php
    			$index++;
            }
        }
    ?>
    <tr class="hidden warning">
        <td colspan="5" class="text-center">There are no reports! :(</td>
    </tr>
        </tbody>
    </table>
</div>
