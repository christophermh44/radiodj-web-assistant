<?php include_once '../init.php';

$ids = explode(',', $_GET['ids']);

$paths = [];
$infos = [];
$prefix = Conf::get('ftp_bind');

function cleanPath($prefix, $path) {
    $upperPrefix = strtoupper($prefix);
    $upperPath = strtoupper($path);
    $out = $path;
    if (substr($upperPath, 0, strlen($upperPrefix)) == $upperPrefix) {
        $out = substr($path, strlen($prefix));
    }
    return implode('/', explode('\\', $out));
}

function getInfos($song) {
	$cues = [];
	foreach ($song->cues as $key => $value) {
		$cues[] = $key.'='.$value;
	}
	return 
	'- ID: '.$song->ID."\n".
	'  path: '.$song->path."\n".
	'  artist: '.$song->artist."\n".
	'  title: '.$song->title."\n".
	'  category: '.$song->category->name."\n".
	'  subcategory: '.$song->subCategory->name."\n".
	'  id_subcat: '.$song->id_subcat."\n".
	'  genre: '.($song->genre !== null ? $song->genre->name : '-')."\n".
	'  duration: '.$song->duration."\n".
	'  cues: '.implode('&', $cues)."\n";
}

foreach ($ids as $id) {
	$song = Songs::getById($id);
	$paths[] = cleanPath($prefix, $song->path);
	$infos[] = getInfos($song);
}

$ftp = new ImplicitFtp(Conf::get('ftp_host').':'.Conf::get('ftp_port'), Conf::get('ftp_user'), Conf::get('ftp_pass'));

$uid = uniqid();
$tmpPath = '/tmp/conducteur-'.$uid.'/';
$zipName = 'pack-'.$uid.'.zip';
mkdir($tmpPath);

foreach ($paths as $path) {
	$ftp->download($path, $tmpPath.basename($path));
}

file_put_contents($tmpPath.'__SONGS.yml', implode('', $infos));
shell_exec('cd '.$tmpPath.' ; zip -9r '.$zipName.' *');

rename($tmpPath.$zipName, __DIR__.'/../cache/'.$zipName);
header('Location: ../cache/'.$zipName);
shell_exec('rm -fr '.$tmpPath);