<?php include_once 'init.php';

$id = $_GET['gid'];

if (!Guides::exists($id)) {
	ErrorPage::send(T::ranslate('This guide does not exist.'));
	die;
}

$newId = Guides::remove($id);
header('Location: guides.php');