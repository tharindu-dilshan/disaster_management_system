<?php
require_once('../config/config.php');
require_once('../config/human_dates.php');
try {
    $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8', DB_USER, DB_PASS);
} catch (PDOException $e) {
    echo $e->getMessage();
}
header("Content-Type: application/xml; charset=utf-8");

$xml = '<?xml version="1.0" encoding="UTF-8" ?>';
$xml .= '<last20>';

$sql = "SELECT report_id, title, description, name, time_submitted, status, latitude, longitude
    FROM web_reports, web_report_details, web_categories WHERE
    web_reports.id=web_report_details.report_id and web_report_details.category_id=web_categories.id
    ORDER BY time_submitted DESC LIMIT 20";
$stmt = $pdo->prepare($sql);
if ($stmt->execute()) {
    while ($row = $stmt->fetch()) {
        $xml .='<marker title="'.$row['title'].'" description="'.$row['description'].
            '" status="'.$row['status'].'" latitude="'.$row['latitude'].
            '" longitude="'.$row['longitude'].'" category="'.$row['name'].
            '" pubDate="'.date('r', strtotime($row['time_submitted'])).
            '" timeDiff="'.get_date_diff(strtotime($row['time_submitted']), time()).
            '" id="'.$row['report_id'].'"></marker>';
    }
    $xml .='</last20>';
}
echo $xml;
?>
