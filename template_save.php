<?php include_once 'init.php';

if (empty($_POST['serial'])) die;

$id = $_POST['id'];
if (empty($id)) $id = Templates::create();
$data = json_decode($_POST['serial'], true);

Templates::save($id, $data);

header('Location: templates.php');