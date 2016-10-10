<?php include_once 'init.php';

if (empty($_POST['id'])) die;

extract($_POST);

Songs::save($_POST['id'], [
	'enabled' => $enabled,
	'title' => $title,
	'artist' => $artist,
	'id_subcat' => $id_subcat,
	'id_genre' => $id_genre,
	'cue_times' => $cue_times,
	'song_type' => $song_type,
]);

header('Location: directory.php');