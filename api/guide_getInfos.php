<?php include_once '../init.php';

$id = $_GET['id'];

if (empty($id)) die;

echo json_encode(Guides::load($id));
?>