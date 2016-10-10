<?php include_once '../init.php';

$actualTemplate = json_decode($_GET['actualTemplate'], true)['actualTemplate'] ?? "";
$startAt = $_GET['startAt'] ?? "";
$newQueryId = $_GET['newQueryId'] ?? "";

if ($actualTemplate == '' || $newQueryId == '' || $startAt == '') die;

$criteria = Queries::getCriteria($newQueryId);
$song = Songs::pickSong($criteria, $startAt, $actualTemplate);
$song = Songs::getById($song->songID);

echo json_encode($song);