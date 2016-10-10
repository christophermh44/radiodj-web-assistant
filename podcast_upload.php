<?php include_once 'init.php';

if (empty($_POST['id_guide']) || empty($_POST['oauth_token']) || empty($_POST['redirect_uri']) || count($_FILES) != 2) die;
$id = $_POST['id_guide'];
$oauth_token = $_POST['oauth_token'];
$redirect_uri = $_POST['redirect_uri'];

if (!Guides::exists($id)) die;
$guide = Guides::load($id);

$file = $_FILES['file'];
$photo = $_FILES['photo'];

$result = Podcasts::upload($guide, $file, $photo, $oauth_token, $redirect_uri);
?>
<?php include 'parts/head.php'; ?>

<main class="wrapper">
	<div class="inner">
		<h2><?= T::ranslate('Podcasts') ?></h2>
	</div>

	<div class="inner">
		<?php if (property_exists($result, 'result') && $result->result->success): ?>
		<p class="inner success">
			<?= T::ranslate('Podcast just has been published!') ?>
			<a href="http://mixcloud.com<?= $result->result->key ?>"><?= T::ranslate('See it on Mixcloud') ?></a>
		</p>
		<?php else: ?>
		<p class="inner error">
			<?= T::ranslate('An error occured when publishing podcast.') ?>
		</p>
		<?php highlight_string('<?php $error_details = '.var_export($result->error, true)."; ?>") ?>
		<?php endif; ?>
	</div>
</main>

<?php include 'parts/tail.php'; ?>