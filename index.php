<?php include 'init.php'; ?>
<?php
$templates = Templates::getList(5);
$guides = Guides::getList(5);
$checkConnection = Query::getInstance()->checkConnection();
?>
<?php include 'parts/head.php'; ?>

<main class="wrapper">
	<div class="inner">
		<h2><?= T::ranslate('Welcome') ?>, <?= Conf::get('radio_name') ?> !</h2>
		<?= Conf::get('welcome_message') ?>
	</div>

	<div class="inner row">
		<section class="col-6">
			<div class="frame">
				<h3><?= T::ranslate('Templates') ?></h3>
				<div class="frame-contents">
					<?php if (count($templates) > 0): ?>
					<ul class="items">
						<?php foreach ($templates as $key => $value): ?>
						<li>
							<span class="title"><?= $value['title'] ?></span>
							<a class="action" href="guide_edit.php?tid=<?= $key ?>"><?= T::ranslate('Use') ?></a>
						</li>
						<?php endforeach; ?>
					</ul>
					<?php endif; ?>
					<a href="templates.php" class="btn action"><?= T::ranslate('See all templates') ?></a>
					<div class="clearfix"></div>
				</div>
			</div>
		</section>

		<section class="col-6">
			<div class="frame">
				<h3><?= T::ranslate('Guides') ?></h3>
				<div class="frame-contents">
					<?php if (count($guides) > 0): ?>
					<ul class="items">
						<?php foreach ($guides as $key => $value): ?>
						<li>
							<span class="title"><?= $value['title'] ?> (<?= date('Y-m-d', $value['date']) ?> <?= date('H:i:s', strtotime($value['template']['start'])) ?>)</span>
							<a href="guide_pdf.php?gid=<?= $key ?>" class="action">PDF</a>
						</li>
						<?php endforeach; ?>
					</ul>
					<?php endif; ?>
					<a href="guides.php" class="btn action"><?= T::ranslate('See all guides') ?></a>
					<div class="clearfix"></div>
				</div>
			</div>
		</section>

		<section class="col-6">
			<div class="frame">
				<h3><?= T::ranslate('Database status') ?></h3>
				<div class="frame-contents">
					<dl>
						<dt><?= T::ranslate('Connection status') ?></dt>
						<dd><span class="led <?= $checkConnection ? 'success' : 'error' ?>"></span></dd>
						<?php if ($checkConnection): ?>
						<dt><?= T::ranslate('Musics') ?></dt>
						<dd><?= Songs::countSongs() ?></dd>
						<dt><?= T::ranslate('Artists') ?></dt>
						<dd><?= Songs::countArtists() ?></dd>
						<?php endif; ?>
					</dl>
					<a href="settings.php" class="btn action"><?= T::ranslate('Go to settings') ?></a>
					<div class="clearfix"></div>
				</div>
			</div>
		</section>

		<section class="col-6">
			<div class="frame">
				<h3><?= T::ranslate('Stats') ?></h3>
				<div class="frame-contents">
					<dl>
						<dt><?= T::ranslate('Queries presets') ?></dt>
						<dd><?= count(Queries::load()) ?></dd>
						<dt><?= T::ranslate('Existing templates') ?></dt>
						<dd><?= count(Templates::getList()) ?></dd>
						<dt><?= T::ranslate('Generated guides') ?></dt>
						<dd><?= count(Guides::getList()) ?></dd>
					</dl>
					<div class="clearfix"></div>
				</div>
			</div>
		</section>
	</div>
</main>


<?php include 'parts/tail.php'; ?>