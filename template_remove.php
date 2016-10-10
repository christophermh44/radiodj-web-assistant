<?php include_once 'init.php';

$id = $_GET['tid'];

if (!Templates::exists($id)) {
	ErrorPage::send(T::ranslate('Template was not found.'));
	die;
}

$newId = Templates::remove($id);
header('Location: templates.php');