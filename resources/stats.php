<?php
   require_once('../config/config.php');
    try {
        $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) as total FROM web_reports WHERE status!=\'Submited\'');
    if ($stmt->execute()) {
        $res = $stmt->fetch();
        $totalReports = $res['total'];
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) as open FROM web_reports WHERE status=\'Open\'');
    if ($stmt->execute()) {
        $res = $stmt->fetch();
        $openReports = $res['open'];
    }

    // average time in minutes
    $stmt = $pdo->prepare('SELECT ROUND(AVG(TIME_TO_SEC(TIMEDIFF(time_closed, time_submitted)) / 60), 2) as average_time FROM web_reports WHERE status=\'Closed\'');
    if ($stmt->execute()) {
        $res = $stmt->fetch();
        $averageTime = $res['average_time'];
        if (empty($averageTime)) {
            $averageTime = 'NaN';
        }
    }

?>
<article class="stats">
	<div class="container-fluid">
	<!-- Example row of columns -->
	<div class="row">
		<div class="col-xs-5 col-sm-3 stat">
			<div class="content">
				<h1><span class="fontawesome-folder-close-alt"></span> <?php echo $totalReports; ?> </h1>
				<h4>Total Reports</h4>
			</div>
		</div>
		<div class="col-xs-5 col-sm-3 stat">
		<div class="content">
			<h1><span class="fontawesome-folder-open-alt"></span> <?php echo $openReports; ?> </h1>
			<h4>Open Reports</h4>
		</div>
		</div>
		<div class="col-xs-5 col-sm-3 stat">
		<div class="content">
			<h1><span class="fontawesome-ok"></span> <?php echo ($totalReports - $openReports); ?> </h1>
			<h4>Closed Reports</h4>
		</div>
		</div>
		<div class="col-xs-5 col-sm-3 stat">
		<div class="content">
			<h1><span class="fontawesome-bar-chart"></span> <?php echo $averageTime; ?> <small>minutes</small> </h1>
			<h4>Average time for resolving reports</h4>
		</div>

		</div>

		<hr>

		</div>
	</div>
</article>
