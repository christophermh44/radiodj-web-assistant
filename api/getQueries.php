<?php include_once '../init.php';

$items = Queries::load();

echo json_encode($items);