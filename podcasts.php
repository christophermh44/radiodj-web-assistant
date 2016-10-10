<?php include 'init.php'; ?>
<?php include 'parts/head.php'; ?>

<main class="wrapper">
	<div class="inner">
		<h2><?= T::ranslate('Podcasts') ?></h2>
	</div>

	<div class="inner">
		<div class="frame">
			<h3><?= T::ranslate('Last published') ?></h3>
			<ul>
				<?php foreach (Podcasts::getPodcasts(Conf::get('mc_channel'), 5) as $podcast): ?>
					<li>
						<a href="<?= $podcast->url ?>">
							<?= $podcast->name ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<div class="frame">
			<h3><?= T::ranslate('Publishing space') ?></h3>
			<?php if (!isset($_GET['code'])): ?>
				<p>
					<a href="https://www.mixcloud.com/oauth/authorize?client_id=<?= Conf::get('mc_clientid') ?>&amp;redirect_uri=<?= "http://$_SERVER[HTTP_HOST]".explode('?', $_SERVER['REQUEST_URI'])[0] ?>"><?= T::ranslate('Generate OAuth Token') ?></a>
				</p>
			<?php else: ?>
				<p>
					<a href="https://www.mixcloud.com/oauth/authorize?client_id=<?= Conf::get('mc_clientid') ?>&amp;redirect_uri=<?= "http://$_SERVER[HTTP_HOST]".explode('?', $_SERVER['REQUEST_URI'])[0] ?>"><?= T::ranslate('Generate new OAuth Token') ?></a>
				</p>
				<form enctype="multipart/form-data" action="podcast_upload.php" method="post">
					<input type="hidden" name="oauth_token" value="<?= $_GET['code'] ?>">
					<input type="hidden" name="redirect_uri" value="<?= "http://$_SERVER[HTTP_HOST]".explode('?', $_SERVER['REQUEST_URI'])[0] ?>">

					<p class="form-control">
						<label for="id_guide"><?= T::ranslate('Guide') ?></label>
						<select name="id_guide" id="id_guide">
							<?php foreach (Guides::getList() as $key => $value): ?>
								<option value="<?= $key ?>">
									<?php
										$guide = Guides::load($key);
									?>
									<?= $guide['title'] ?> (<?= date('Y-m-d', $value['date'] + 3600) ?> <?= date('H:i:s', strtotime($value['template']['start']) + 3600) ?>)
								</option>
							<?php endforeach; ?>
						</select>
					</p>

					<p class="form-control">
						<label for="file"><?= T::ranslate('MP3 file') ?> (500Mb. <?= T::ranslate('max') ?>)</label>
						<input type="hidden" name="MAX_FILE_SIZE" value="524288000" />
						<input type="file" name="file" id="file">
					</p>

					<p class="form-control">
						<label for="photo"><?= T::ranslate('Picture file') ?> (10Mb. <?= T::ranslate('max') ?>)</label>
						<input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
						<input type="file" name="photo" id="photo">
					</p>

					<div class="form-actions">
						<p class="inner warning">
							<?= T::ranslate('This operation could take a few minutes. Please be patient and do not close this tab while the upload is not finished. A confirmation will be displayed when the upload is over.') ?>
						</p>
						<button class="btn" type="submit"><?= T::ranslate('Publish') ?></button>
					</div>
				</form>
			<?php endif; ?>
		</div>
	</div>
</main>

<?php include 'parts/tail.php'; ?>