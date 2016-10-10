<?php include_once '../init.php';

$songs = Songs::getNewSongs();

echo json_encode($songs);
