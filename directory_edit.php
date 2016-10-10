<?php include 'init.php'; ?>
<?php
$id = $_GET['id'];
$song = Songs::getById($id);
$categories = Songs::getSubCategories();
$genres = Songs::getGenres();
$types = Songs::getTypes();
?>
<?php include 'parts/head.php'; ?>
<main class="wrapper" ng-controller="audioEditor">
	<div class="inner">
		<h2><?= T::ranslate('Song editor') ?></h2>
	</div>

	<div class="inner">
		<form action="directory_save.php" method="post" ng-submit="submit()">
			<input type="hidden" name="id" value="<?= $id ?>">

			<fieldset>
				<legend><?= T::ranslate('Information') ?></legend>
				<p class="form-control">
					<label for="enabled"><?= T::ranslate('Enabled') ?></label>
					<select name="enabled" id="enabled">
						<option value="0" <?= $song->enabled == '0' ? 'selected="selected"' : '' ?>><?= T::ranslate('No') ?></option>
						<option value="1" <?= $song->enabled == '1' ? 'selected="selected"' : '' ?>><?= T::ranslate('Yes') ?></option>
					</select>
				</p>
				<p class="form-control">
					<label for="title"><?= T::ranslate('Artist') ?></label>
					<input type="text" required name="artist" id="artist" value="<?= $song->artist ?>">
				</p>
				<p class="form-control">
					<label for="title"><?= T::ranslate('Title') ?></label>
					<input type="text" required name="title" id="title" value="<?= $song->title ?>">
				</p>
				<p class="form-control">
					<label for="subcategory"><?= T::ranslate('Category') ?>/<?= T::ranslate('Subcategory') ?></label>
					<select name="id_subcat" id="subcategory">
						<?php foreach ($categories as $category) {
							$key = $category->ID;
							$value = $category->category_name; ?>
							<option <?= $key == $song->id_subcat ? 'selected="selected"' : '' ?> value="<?= $key ?>"><?= $value ?></option><?php
						} ?>
					</select>
				</p>
				<p class="form-control">
					<label for="genre"><?= T::ranslate('Genre') ?></label>
					<select name="id_genre" id="genre">
						<?php foreach ($genres as $key => $genre) {
							$value = $genre->name; ?>
							<option <?= $key == $song->id_genre ? 'selected="selected"' : '' ?> value="<?= $key ?>"><?= $value ?></option><?php
						} ?>
					</select>
				</p>
				<p class="form-control">
					<label for="type"><?= T::ranslate('Type') ?></label>
					<select name="song_type" id="type">
						<?php foreach ($types as $key => $value) { ?>
							<option <?= $key == $song->song_type ? 'selected="selected"' : '' ?> value="<?= $key ?>"><?= $value ?></option><?php
						} ?>
					</select>
				</p>
			</fieldset>

			<fieldset>
				<legend><?= T::ranslate('Cue points') ?></legend>
				<p class="form-control">
					<div class="audio-wrapper wrapper" data-src="api/directory_audio.php?id=<?= $id ?>"></div>
				</p>
				<p class="form-control" class="cue-point">
					<label for="guide-title">Start</label>
					<input type="text" name="sta" id="sta" ng-model="cue.cues['sta']" ng-init="cue.cues['sta'] = <?= isset($song->cues['sta']) ? $song->cues['sta'] : "''" ?>">
					<button class="btn small" type="button" ng-click="cue.previous('sta')"><?= T::ranslate('Backwards') ?></button>
					<button class="btn small" type="button" ng-click="cue.now('sta')"><?= T::ranslate('Now') ?></button>
					<button class="btn small" type="button" ng-click="cue.next('sta')"><?= T::ranslate('Forward') ?></button>
					<button class="btn small" type="button" ng-click="cue.play('sta')"><?= T::ranslate('Play here') ?></button>
				</p>
				<p class="form-control" class="cue-point">
					<label for="guide-title">End</label>
					<input type="text" name="end" id="end" ng-model="cue.cues['end']" ng-init="cue.cues['end'] = <?= isset($song->cues['end']) ? $song->cues['end'] : "''" ?>">
					<button class="btn small" type="button" ng-click="cue.previous('end')"><?= T::ranslate('Backwards') ?></button>
					<button class="btn small" type="button" ng-click="cue.now('end')"><?= T::ranslate('Now') ?></button>
					<button class="btn small" type="button" ng-click="cue.next('end')"><?= T::ranslate('Forward') ?></button>
					<button class="btn small" type="button" ng-click="cue.play('end')"><?= T::ranslate('Play here') ?></button>
				</p>
				<p class="form-control" class="cue-point">
					<label for="guide-title">Intro</label>
					<input type="text" name="int" id="int" ng-model="cue.cues['int']" ng-init="cue.cues['int'] = <?= isset($song->cues['int']) ? $song->cues['int'] : "''" ?>">
					<button class="btn small" type="button" ng-click="cue.previous('int')"><?= T::ranslate('Backwards') ?></button>
					<button class="btn small" type="button" ng-click="cue.now('int')"><?= T::ranslate('Now') ?></button>
					<button class="btn small" type="button" ng-click="cue.next('int')"><?= T::ranslate('Forward') ?></button>
					<button class="btn small" type="button" ng-click="cue.play('int')"><?= T::ranslate('Play here') ?></button>
				</p>
				<p class="form-control" class="cue-point">
					<label for="guide-title">Outro</label>
					<input type="text" name="out" id="out" ng-model="cue.cues['out']" ng-init="cue.cues['out'] = <?= isset($song->cues['out']) ? $song->cues['out'] : "''" ?>">
					<button class="btn small" type="button" ng-click="cue.previous('out')"><?= T::ranslate('Backwards') ?></button>
					<button class="btn small" type="button" ng-click="cue.now('out')"><?= T::ranslate('Now') ?></button>
					<button class="btn small" type="button" ng-click="cue.next('out')"><?= T::ranslate('Forward') ?></button>
					<button class="btn small" type="button" ng-click="cue.play('out')"><?= T::ranslate('Play here') ?></button>
				</p>
				<p class="form-control" class="cue-point">
					<label for="guide-title">Loop in</label>
					<input type="text" name="lin" id="lin" ng-model="cue.cues['lin']" ng-init="cue.cues['lin'] = <?= isset($song->cues['lin']) ? $song->cues['lin'] : "''" ?>">
					<button class="btn small" type="button" ng-click="cue.previous('lin')"><?= T::ranslate('Backwards') ?></button>
					<button class="btn small" type="button" ng-click="cue.now('lin')"><?= T::ranslate('Now') ?></button>
					<button class="btn small" type="button" ng-click="cue.next('lin')"><?= T::ranslate('Forward') ?></button>
					<button class="btn small" type="button" ng-click="cue.play('lin')"><?= T::ranslate('Play here') ?></button>
				</p>
				<p class="form-control" class="cue-point">
					<label for="guide-title">Loop out</label>
					<input type="text" name="lou" id="lou" ng-model="cue.cues['lou']" ng-init="cue.cues['lou'] = <?= isset($song->cues['lou']) ? $song->cues['lou'] : "''" ?>">
					<button class="btn small" type="button" ng-click="cue.previous('lou')"><?= T::ranslate('Backwards') ?></button>
					<button class="btn small" type="button" ng-click="cue.now('lou')"><?= T::ranslate('Now') ?></button>
					<button class="btn small" type="button" ng-click="cue.next('lou')"><?= T::ranslate('Forward') ?></button>
					<button class="btn small" type="button" ng-click="cue.play('lou')"><?= T::ranslate('Play here') ?></button>
				</p>
				<p class="form-control" class="cue-point">
					<label for="guide-title">Hook in</label>
					<input type="text" name="hin" id="hin" ng-model="cue.cues['hin']" ng-init="cue.cues['hin'] = <?= isset($song->cues['hin']) ? $song->cues['hin'] : "''" ?>">
					<button class="btn small" type="button" ng-click="cue.previous('hin')"><?= T::ranslate('Backwards') ?></button>
					<button class="btn small" type="button" ng-click="cue.now('hin')"><?= T::ranslate('Now') ?></button>
					<button class="btn small" type="button" ng-click="cue.next('hin')"><?= T::ranslate('Forward') ?></button>
					<button class="btn small" type="button" ng-click="cue.play('hin')"><?= T::ranslate('Play here') ?></button>
				</p>
				<p class="form-control" class="cue-point">
					<label for="guide-title">Hook out</label>
					<input type="text" name="hou" id="hou" ng-model="cue.cues['hou']" ng-init="cue.cues['hou'] = <?= isset($song->cues['hou']) ? $song->cues['hou'] : "''" ?>">
					<button class="btn small" type="button" ng-click="cue.previous('hou')"><?= T::ranslate('Backwards') ?></button>
					<button class="btn small" type="button" ng-click="cue.now('hou')"><?= T::ranslate('Now') ?></button>
					<button class="btn small" type="button" ng-click="cue.next('hou')"><?= T::ranslate('Forward') ?></button>
					<button class="btn small" type="button" ng-click="cue.play('hou')"><?= T::ranslate('Play here') ?></button>
				</p>
				<p class="form-control" class="cue-point">
					<label for="guide-title">Fade in</label>
					<input type="text" name="fin" id="fin" ng-model="cue.cues['fin']" ng-init="cue.cues['fin'] = <?= isset($song->cues['fin']) ? $song->cues['fin'] : "''" ?>">
					<button class="btn small" type="button" ng-click="cue.previous('fin')"><?= T::ranslate('Backwards') ?></button>
					<button class="btn small" type="button" ng-click="cue.now('fin')"><?= T::ranslate('Now') ?></button>
					<button class="btn small" type="button" ng-click="cue.next('fin')"><?= T::ranslate('Forward') ?></button>
					<button class="btn small" type="button" ng-click="cue.play('fin')"><?= T::ranslate('Play here') ?></button>
				</p>
				<p class="form-control" class="cue-point">
					<label for="guide-title">Fade out</label>
					<input type="text" name="fou" id="fou" ng-model="cue.cues['fou']" ng-init="cue.cues['fou'] = <?= isset($song->cues['fou']) ? $song->cues['fou'] : "''" ?>">
					<button class="btn small" type="button" ng-click="cue.previous('fou')"><?= T::ranslate('Backwards') ?></button>
					<button class="btn small" type="button" ng-click="cue.now('fou')"><?= T::ranslate('Now') ?></button>
					<button class="btn small" type="button" ng-click="cue.next('fou')"><?= T::ranslate('Forward') ?></button>
					<button class="btn small" type="button" ng-click="cue.play('fou')"><?= T::ranslate('Play here') ?></button>
				</p>
				<p class="form-control" class="cue-point">
					<label for="guide-title">Next</label>
					<input type="text" name="xta" id="xta" ng-model="cue.cues['xta']" ng-init="cue.cues['xta'] = <?= isset($song->cues['xta']) ? $song->cues['xta'] : "''" ?>">
					<button class="btn small" type="button" ng-click="cue.previous('xta')"><?= T::ranslate('Backwards') ?></button>
					<button class="btn small" type="button" ng-click="cue.now('xta')"><?= T::ranslate('Now') ?></button>
					<button class="btn small" type="button" ng-click="cue.next('xta')"><?= T::ranslate('Forward') ?></button>
					<button class="btn small" type="button" ng-click="cue.play('xta')"><?= T::ranslate('Play here') ?></button>
				</p>
			</fieldset>

			<div class="form-actions">
				<button class="btn" type="submit"><?= T::ranslate('Save') ?></button>
			</div>
		</form>
	</div>
</main>
<?php include 'parts/tail.php'; ?>