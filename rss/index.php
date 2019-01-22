<?php
   require_once('../config/config.php');
    try {
        $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
   header("Content-Type: application/xml; charset=utf-8");

   $rssfeed =
'<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">
<channel>
  <title>Most recent reports</title>
  <link>http://localhost/erms</link>
  <description> Display the most recently submitted reports</description>';

    $sql = "SELECT title, description, name, time_submitted, status, latitude, longitude
             FROM web_reports, web_report_details, web_categories WHERE
            web_reports.id=web_report_details.report_id and web_report_details.category_id=web_categories.id
            ORDER BY time_submitted DESC LIMIT 20";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute()) {
        while ($row = $stmt->fetch()) {
            $rssfeed .='
  <item>
    <title>'.$row['title'].'</title>
    <link>http://localhost/erms</link>
    <description>'.$row['description'].
                    ' status: '.$row['status'].
                    ' location: '.$row['latitude'].','.$row['longitude'].'</description>
    <category>'.$row['name'].'</category>
    <pubDate>'.date('r', strtotime($row['time_submitted'])).'</pubDate>
  </item>';
        }
        $rssfeed .='
</channel>
</rss>';
    }
    echo $rssfeed;
?>
