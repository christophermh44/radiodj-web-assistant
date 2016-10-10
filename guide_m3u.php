#EXTM3U
<?php include_once 'init.php';

if (empty($_GET['gid'])) die;
$id = $_GET['gid'];
$guide = Guides::load($id);
header('Content-disposition: attachment; filename="'.Guides::getFileName($guide, 'm3u').'"');
header('Content-type: audio/x-mpegurl');

function formatCues($value) {
	$cue_times = explode('&', $value['additionnals']['cue_times']);
	$cues = [];
	foreach ($cue_times as $cue_time) {
		$kv = explode('=', $cue_time);
		if (count($kv) == 2) {
			$key = $kv[0];
			$value = $kv[1];
			$cues[$key] = $value;
		}
	}
	$formatedCues = [];
	$formatedCues[] = round($cues['sta'] ?? 0, 3);
	$formatedCues[] = round($cues['int'] ?? 0, 3);
	$formatedCues[] = round($cues['xta'] ?? 0, 3);
	$formatedCues[] = round($cues['end'] ?? 0, 3);
	$formatedCues[] = round($cues['fin'] ?? 0, 3);
	$formatedCues[] = round($cues['fou'] ?? 0, 3);
	return implode(':', $formatedCues);
}

foreach ($guide['lines'] as $key => $value) {
	if (!is_string($value['additionnals'])) { ?>
#EXTINF:<?= round($value['additionnals']['duration']) ?>,<?= $value['additionnals']['artist'] ?> - <?= $value['additionnals']['title'] ?><?= "\n" ?>
<?= $value['additionnals']['path'] ?><?= "\n" ?>
#RDJDATA:<?= $value['additionnals']['ID'] ?>:<?= formatCues($value) ?>:-100:-100:-100:-100:<?= "\n" ?><?php
	} else {
		// And so what ?
	}
}
