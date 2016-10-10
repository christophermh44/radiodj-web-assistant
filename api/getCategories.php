<?php include_once '../init.php';

$val = urldecode($_GET['value']);

$q = Query::getInstance();
$categories = $q->process('SELECT ID, name FROM category WHERE name LIKE :val LIMIT 8', [
	'val' => "%$val%"
]);

$items = [];

foreach ($categories as $category) {
	$items[] = ['ID' => $category->ID, 'name' => $category->name];
}

echo json_encode($items);