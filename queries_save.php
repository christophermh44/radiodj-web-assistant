<?php include_once 'init.php';

if (empty($_POST['queries'])) die;

$data = json_decode($_POST['queries'], true);

Queries::save($data);

header('Location: queries.php');