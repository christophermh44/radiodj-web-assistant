<?php include_once '../init.php';

function cleanPath($prefix, $path) {
	$upperPrefix = strtoupper($prefix);
	$upperPath = strtoupper($path);
	$out = $path;
	if (substr($upperPath, 0, strlen($upperPrefix)) == $upperPrefix) {
		$out = substr($path, strlen($prefix));
	}
	return implode('/', explode('\\', $out));
}

$id = $_GET['id'];
$prefix = Conf::get('ftp_bind');
$song = Songs::getById($id);
$path = cleanPath($prefix, $song->path);

$ftp = new ImplicitFtp(Conf::get('ftp_host').':'.Conf::get('ftp_port'), Conf::get('ftp_user'), Conf::get('ftp_pass'));

$tmpFile = $ftp->download($path);
$filename = basename($tmpFile);
rename($tmpFile, __DIR__.'/../cache/'.$filename);
echo '../cache/'.$filename;