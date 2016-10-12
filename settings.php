<?php include 'init.php'; ?>
<?php
if (isset($_GET['code'])) {
	$newToken = $_GET['code'];
	Conf::set('mc_oauthtoken', $newToken);
} ?>
<?php include 'parts/head.php'; ?>

<main class="wrapper">
	<div class="inner">
		<h2><?= T::ranslate('Settings') ?></h2>
	</div>

	<div class="inner">
		<form action="settings_save.php" method="post">
			<fieldset>
				<legend><?= T::ranslate('General settings') ?></legend>
				<p class="form-control">
					<label for="radio_name"><?= T::ranslate('Your radio name') ?></label>
					<input type="text" name="radio_name" id="radio_name" value="<?= Conf::get('radio_name') ?>">
				</p>
				<p class="form-control">
					<label for="language"><?= T::ranslate('Language') ?></label>
					<select name="language" id="language">
						<?php foreach (Data::listFrom('languages') as $key => $value) { ?>
							<option value="<?= $key ?>" <?= Conf::get('language') == $key ? 'selected="selected"' : '' ?>><?= $key ?></option>
						<?php } ?>
					</select>
				</p>
				<p class="form-control">
					<label for="welcome_message"><?= T::ranslate('Welcome message') ?></label>
					<textarea class="large" name="welcome_message" id="welcome_message" cols="30" rows="10"><?= Conf::get('welcome_message') ?></textarea>
					<p>
						<?= T::ranslate('If filled, this message will be displayed on home page.') ?>
						<?= T::ranslate('It allows HTML code, so take care of what you are puting in this field.') ?>
					</p>
				</p>
				<p class="form-control">
					<label for="footer_data"><?= T::ranslate('Footer zone') ?></label>
					<textarea class="large" name="footer_data" id="footer_data" cols="30" rows="10"><?= Conf::get('footer_data') ?></textarea>
					<p>
						<?= T::ranslate('If filled, this message will be displayed inside the footer zone.') ?>
						<?= T::ranslate('It allows HTML code, so take care of what you are puting in this field.') ?>
					</p>
				</p>
			</fieldset>

			<fieldset>
				<legend><?= T::ranslate('Database connection') ?></legend>
				<p class="form-control">
					<label for="db_base"><?= T::ranslate('Database name') ?></label>
					<input type="text" name="db_base" id="db_base" value="<?= Conf::get('db_base') ?>">
				</p>
				<p class="form-control">
					<label for="db_host"><?= T::ranslate('Host') ?></label>
					<input type="text" name="db_host" id="db_host" value="<?= Conf::get('db_host') ?>">
				</p>
				<p class="form-control">
					<label for="db_port"><?= T::ranslate('Port') ?></label>
					<input type="text" name="db_port" id="db_port" value="<?= Conf::get('db_port') ?>">
				</p>
				<p class="form-control">
					<label for="db_user"><?= T::ranslate('User name') ?></label>
					<input type="text" name="db_user" id="db_user" value="<?= Conf::get('db_user') ?>">
				</p>
				<p class="form-control">
					<label for="db_pass"><?= T::ranslate('Password') ?></label>
					<input type="password" onmouseenter="this.type='text';" onmouseleave="this.type='password';" name="db_pass" id="db_pass" value="<?= Conf::get('db_pass') ?>">
				</p>
			</fieldset>

			<fieldset>
				<legend><?= T::ranslate('FTP connection') ?></legend>
				<p class="form-control">
					<label for="ftp_host"><?= T::ranslate('Host') ?></label>
					<input type="text" name="ftp_host" id="ftp_host" value="<?= Conf::get('ftp_host') ?>">
				</p>
				<p class="form-control">
					<label for="ftp_port"><?= T::ranslate('Port') ?></label>
					<input type="text" name="ftp_port" id="ftp_port" value="<?= Conf::get('ftp_port') ?>">
				</p>
				<p class="form-control">
					<label for="ftp_user"><?= T::ranslate('User name') ?></label>
					<input type="text" name="ftp_user" id="ftp_user" value="<?= Conf::get('ftp_user') ?>">
				</p>
				<p class="form-control">
					<label for="ftp_pass"><?= T::ranslate('Password') ?></label>
					<input type="password" onmouseenter="this.type='text';" onmouseleave="this.type='password';" name="ftp_pass" id="ftp_pass" value="<?= Conf::get('ftp_pass') ?>">
				</p>
				<p class="form-control">
					<label for="ftp_ssl"><?= T::ranslate('SSL Connection') ?></label>
					<select name="ftp_ssl" id="ftp_ssl">
						<option value="0" <?= Conf::get('ftp_ssl') == '0' ? 'selected="selected"' : '' ?>><?= T::ranslate('No') ?></option>
						<option value="1" <?= Conf::get('ftp_ssl') == '1' ? 'selected="selected"' : '' ?>><?= T::ranslate('Yes') ?></option>
					</select>
				</p>
				<p class="form-control">
					<label for="ftp_bind"><?= T::ranslate('Path prefix') ?></label>
					<input type="text" name="ftp_bind" id="ftp_bind" value="<?= Conf::get('ftp_bind') ?>">
				</p>
			</fieldset>

			<fieldset>
				<legend><?= T::ranslate('Mixcloud app') ?></legend>
				<p class="form-control">
					<label for="mc_channel"><?= T::ranslate('Channel') ?></label>
					<input type="text" name="mc_channel" id="mc_channel" value="<?= Conf::get('mc_channel') ?>">
				</p>
				<p class="form-control">
					<label for="mc_clientid"><?= T::ranslate('Client ID') ?></label>
					<input type="text" name="mc_clientid" id="mc_clientid" value="<?= Conf::get('mc_clientid') ?>">
				</p>
				<p class="form-control">
					<label for="mc_clientsecret"><?= T::ranslate('Client Secret') ?></label>
					<input type="text" name="mc_clientsecret" id="mc_clientsecret" value="<?= Conf::get('mc_clientsecret') ?>">
				</p>
			</fieldset>

			<button type="submit" class="btn"><?= T::ranslate('Save') ?></button>
		</form>

		<form action="settings_reset.php" method="post" onsubmit="return confirm('<?= T::ranslate('You are about to reset everything (data / settings). Are you shure about that?') ?>');">
			<fieldset class="danger">
				<legend><?= T::ranslate('Danger zone') ?></legend>
				<p>
					<a class="danger" href="settings_clean_zip.php"><?= T::ranslate('Clean generated zip files') ?></a>
				</p>

				<p class="form-control">
					<label for="what"><?= T::ranslate('Reset') ?></label>
					<select name="what" id="what">
						<option value="">- <?= T::ranslate('Your choice') ?> -</option>
						<option value="queries"><?= T::ranslate('Queries') ?></option>
						<option value="templates"><?= T::ranslate('Templates') ?></option>
						<option value="guides"><?= T::ranslate('Guides') ?></option>
						<option value="settings"><?= T::ranslate('Settings') ?></option>
						<option value="all"><?= T::ranslate('Everything') ?></option>
					</select>
				</p>
				<p class="form-control">
					<button type="submit" class="btn"><?= T::ranslate('Reset') ?></button>
				</p>
				<p>
					<?= T::ranslate('Warning! Reset will remove definitively stored data. Be sure of what you are doing.') ?>
				</p>
			</fieldset>
		</form>
	</div>
</main>

<?php include 'parts/tail.php'; ?>
