<?php include_once '../init.php';

$val = urldecode($_GET['value']);
$parentId = urldecode($_GET['pid']);

$q = Query::getInstance();
$categories = $q->process('SELECT ID, name FROM subcategory WHERE name LIKE :val AND parentid = :pid LIMIT 8', [
	'val' => "%$val%",
	'pid' => $parentId
]);

$items = [];

foreach ($categories as $category) {
	$items[] = ['ID' => $category->ID, 'name' => $category->name];
}

echo json_encode($items);