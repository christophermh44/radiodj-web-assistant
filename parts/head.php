<!DOCTYPE html>
<html lang="fr" ng-app="conducteur">
<head>
	<meta charset="UTF-8">
	<title><?= T::ranslate('RadioDJ Web Assistant') ?> | <?= Conf::get('radio_name') ?></title>
	<link rel="stylesheet" href="style.css">
</head>
<body>
	<header class="wrapper">
		<div class="inner">
			<h1>
				<a href="index.php">
					<?= T::ranslate('RadioDJ Web Assistant') ?> | <?= Conf::get('radio_name') ?>
				</a>
			</h1>
			<nav>
				<ul>
					<li>
						<a href="queries.php">
							<?= T::ranslate('Queries') ?>
						</a>
					</li>
					<li>
						<a href="templates.php">
							<?= T::ranslate('Templates') ?>
						</a>
					</li>
					<li>
						<a href="guides.php">
							<?= T::ranslate('Guides') ?>
						</a>
					</li>
					<li>
						<a href="podcasts.php">
							<?= T::ranslate('Podcasts') ?>
						</a>
					</li>
					<li>
						<a href="directory.php">
							<?= T::ranslate('Directory') ?>
						</a>
					</li>
					<li>
						<a href="settings.php">
							<?= T::ranslate('Settings') ?>
						</a>
					</li>
				</ul>
			</nav>
		</div>
	</header>