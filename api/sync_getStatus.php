<?php include_once '../init.php';

function getBaseName($path) {
	$pathParts = explode('\\', $path);
	$basename = end($pathParts);
	return $basename;
}

function getLocalFile($path) {
	$basename = getBaseName($path);
	return __DIR__.'/../data/songs/'.$basename;
}

$songs = Songs::getAll();
$out = [];

foreach ($songs as $key => $value) {
	if (strpos($value->path, '://') === false) {
		$localFile = getLocalFile($value->path);
		$song = [
			'filename' => getBaseName($value->path),
			'artist' => $value->artist,
			'title' => $value->title,
			'song_type' => $value->song_type,
			'id_subcat' => $value->id_subcat,
			'id_genre' => $value->id_genre,
			'duration' => $value->duration,
			'cue_times' => $value->cue_times,
			'SHA1' => file_exists($localFile) ? sha1_file($localFile) : null
		];
		$out[$key] = $song;
	}
}

echo json_encode($out, JSON_PRETTY_PRINT);