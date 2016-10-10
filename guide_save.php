<?php include_once 'init.php';

if (empty($_POST['serial']) || empty($_POST['id'])) die;

$id = $_POST['id'];
$data = json_decode($_POST['serial'], true);

Guides::save($id, $data);

header('Location: guides.php');