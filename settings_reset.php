<?php include_once 'init.php';

if (!empty($_POST['what'])) {
	$what = $_POST['what'];
	if ($what == 'queries' || $what == 'all') {
		Queries::reset();
	}
	if ($what == 'templates' || $what == 'all') {
		Templates::reset();
	}
	if ($what == 'guides' || $what == 'all') {
		Guides::reset();
	}
	if ($what == 'settings' || $what == 'all') {
		Conf::reset();
	}
	header('Location: index.php');
} else {
	header('Location: settings.php');
}
