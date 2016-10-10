<?php include_once 'init.php';

if (empty($_GET['gid'])) die;
$id = $_GET['gid'];
$guide = Guides::load($id);

$ids = [];

foreach ($guide['lines'] as $key => $value) {
	if (!is_string($value['additionnals'])) {
		$ids[] = $value['additionnals']['ID'];
	}
}

header('Location: api/downloadSongs.php?ids='.implode(',', $ids));