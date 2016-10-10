<?php include_once 'init.php';

$id = $_GET['tid'];

if (!Templates::exists($id)) {
	ErrorPage::send(T::ranslate('Template was not found.'));
	die;
}

$newId = Templates::duplicate($id);
header('Location: template_edit.php?tid=' . $newId);