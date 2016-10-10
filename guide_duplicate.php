<?php include_once 'init.php';

if (!isset($_GET['gid'])) die;

$gid = $_GET['gid'];
$newId = Guides::duplicate($gid);

header('Location: guide_edit.php?gid='.$newId);
