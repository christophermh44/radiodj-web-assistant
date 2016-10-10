<?php include 'init.php'; ?>
<?php include 'parts/head.php'; ?>

<main class="wrapper">
	<div class="inner">
		<h2><?= ErrorPage::receive() ?></h2>
	</div>

	<div class="inner row">
		<a href="javascript:history.back();"><?= T::ranslate('Back') ?></a>
	</div>
</main>


<?php include 'parts/tail.php'; ?>