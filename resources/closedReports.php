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
		$sql = "SELECT COUNT(*) FROM web_reports WHERE status='Closed'";
		$all = $pdo->prepare($sql);
		if($all->execute()) {
                    $allRes = $all->fetch();
                    $count = $allRes[0];
                }
	}

	$sql1 = "
                    SELECT web_reports.id, title, time_closed, web_categories.name, closer_id FROM `web_reports` INNER JOIN web_report_details ON web_reports.id=web_report_details.report_id INNER JOIN web_categories ON web_categories.id = web_report_details.category_id WHERE status='Closed' ORDER BY category_id, time_closed DESC LIMIT ". $max*($page-1) . "," . $max;
	$stmt = $pdo->prepare($sql1);
?>

<h3>Closed Reports</h3>
<div id="totalclosed" class="hidden"><?php echo ceil($count/$max); ?></div>
<div class="table-responsive">
    <table class="table table-striped">
    	<thead>
    		<tr>
    			<th>Reference Number</th>
    			<th>Title</th>
    			<th>Category</th>
    			<th>Resolved Date</th>
    			<th>Admin</th>
    		</tr>
    	</thead>
    	<tbody>

    <?php
        if ($stmt->execute()) {
            while ($row = $stmt->fetch()) {
                // find admin who closed report
                $adminStmt = $pdo->prepare("SELECT user_name FROM web_users WHERE user_id=" . $row["closer_id"]);
                if ($adminStmt->execute()) {
                    $adminRow = $adminStmt->fetch(PDO::FETCH_ASSOC);
                    $admin = $adminRow["user_name"];
                }
    ?>
    		<tr>
    			<td><?php echo $row["id"]; ?></td>
    			<td><?php echo $row["title"]; ?></td>
    			<td><?php echo $row["name"]; ?></td>
    			<td title="<?php echo $row["time_closed"]; ?>">before <?php echo get_date_diff($row["time_closed"], time()); ?></td>
                            <td><?php echo $admin; ?></td>
    		</tr>
    <?php
    		}
        }
    ?>
    <tr class="hidden warning">
        <td colspan="5" class="text-center">There are no reports! :(</td>
    </tr>
        </tbody>
    </table>
</div>
