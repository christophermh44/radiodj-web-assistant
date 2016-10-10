<?php include 'init.php'; ?>
<?php
	if ((!isset($_GET['tid']) || !Templates::exists($_GET['tid'])) && !isset($_GET['gid'])) {
		ErrorPage::send(T::ranslate('No valid guide was choosen.'));
	}

	if (!isset($_GET['gid'])) {
		$gid = Guides::create($_GET['tid']);
		header('Location: guide_edit.php?gid='.$gid);
	}

	$gid = $_GET['gid'];

	$queries = Queries::load();
?>
<?php include 'parts/head.php'; ?>

<main class="wrapper" ng-controller="guideEditor">
	<div class="inner">
		<h2><?= T::ranslate('Guide editor') ?></h2>
	</div>

	<div class="inner">
		<div ng-hide="loaded">
			<?= T::ranslate('Loading…') ?>
		</div>
		<form action="guide_save.php" method="post" class="loading">
			<input type="hidden" name="serial" value="{{ getSerial() }}">
			<input type="hidden" name="id" value="{{ id }}">
			<fieldset>
				<legend><?= T::ranslate('Information') ?></legend>
				<p class="form-control">
					<span class="label"><?= T::ranslate('Template') ?></span>
					<span class="title">{{ template.title }}</span>
					<span class="description">{{ template.description }}</span>
				</p>

				<p class="form-control">
					<label for="guide-title"><?= T::ranslate('Guide name') ?></label>
					<input type="text" required name="guide-title" id="guide-title" ng-model="title">
				</p>

				<p class="form-control">
					<label for="guide-date"><?= T::ranslate('Guide date') ?></label>
					<input type="text" required name="date" id="date" ng-model="date" class="input-date-picker" uib-datepicker-popup is-open="datePopupPicker.opened" datepicker-options="dateOptions" ng-required="true" clear-text="Effacer" close-text="Fermer" current-text="Aujourd'hui" />
	            	<button type="button" class="btn" ng-click="openDatePopupPicker()">&#9660;</button>
				</p>

				<p class="form-control">
					<label for="template-start"><?= T::ranslate('Start time') ?></label>
					<uib-timepicker required ng-model="template.start" show-meridian="false" show-seconds="true" show-spinners="false" mousewheel="false"></uib-timepicker>
				</p>

				<p class="form-control">
					<label for="guide-details"><?= T::ranslate('Details') ?></label>
					<textarea name="details" id="guide-details" ng-model="details" class="large"></textarea>
				</p>
			</fieldset>

			<table class="items col-12">
				<thead>
					<tr>
						<td colspan="5">
							<button type="button" class="btn" ng-click="newLine();"><?= T::ranslate('Add line') ?></button>
						</td>
					</tr>
					<tr>
						<th style="width:2%;">#</th>
						<th style="width:8%;" class="actions"><?= T::ranslate('Starts at') ?></th>
						<th style="width:67%;"><?= T::ranslate('Label') ?></th>
						<th style="width:15%;"><?= T::ranslate('Duration') ?></th>
						<th style="width:8%;" class="actions"><?= T::ranslate('Actions') ?></th>
					</tr>
				</thead>

				<tfoot>
					<tr>
						<td colspan="5">
							<button type="button" class="btn" ng-click="newLine();"><?= T::ranslate('Add line') ?></button>
						</td>
					</tr>
				</tfoot>

				<tbody>
					<tr ng-repeat="line in lines track by $index">
						<td>
							<div class="action">
								{{ $index + 1 }}
							</div>
						</td>
						<td class="action">
							{{ startAt($index)|date:'HH:mm:ss' }}
						</td>
						<td>
							<div ng-hide="line.showQueryForm">
								<input type="text" ng-model="line.designation" class="large" />
								<input type="text" ng-model="line.additionnals" uib-typeahead="song.additionnals as song.designation for song in getItems($viewValue)" ng-model-options="{debounce:250}" typeahead-on-select="setInfos($index, $item);" class="large" ng-change="unlock($index)" />
								<button class="btn" type="button" ng-click="line.showQueryForm = true;"><?= T::ranslate('Query') ?></button>
								<span class="description" ng-show="line.ID != '' &amp;&amp; line.locked">
									<?= T::ranslate('ID') ?> : {{ line.ID }}
									| <?= T::ranslate('Category') ?> : {{ line.additionnals.category.name ? line.additionnals.category.name : '-' }}
									| <?= T::ranslate('Subcategory') ?> : {{ line.additionnals.subCategory.name ? line.additionnals.subCategory.name : '-' }}
									| <?= T::ranslate('Genre') ?> : {{ line.additionnals.genre.name ? line.additionnals.genre.name : '-' }}
								</span>
							</div>
							
							<div ng-show="line.showQueryForm">
								<p class="form-control">
									<label for="item-event-{{ $index }}"><?= T::ranslate('Query') ?></label>
									<select class="large" ng-model="line.newQueryId">
										<?php foreach ($queries as $key => $value): ?>
											<option value="<?= $key ?>"><?= $value['alias'] ?></option>
										<?php endforeach; ?>
									</select>
									<button type="button" class="btn" ng-click="querySong($index);"><?= T::ranslate('Choose') ?></button>
									<button type="button" class="btn" ng-click="line.showQueryForm = false;"><?= T::ranslate('Close') ?></button>
								</p>
							</div>
						</td>
						<td>
							<uib-durationpicker class="duration" show-spinners="false" mousewheel="false" ng-model="line.duration"></uib-durationpicker>
							<p class="description" ng-show="getIntro($index) &amp;&amp; line.locked">
								Intro : {{ getIntro($index) }} <?= T::ranslate('sec.') ?>
							</p>
						</td>
						<td>
							<button type="button" class="btn action" ng-click="moveTop($index);">&#9650;</button>
							<button type="button" class="btn action" ng-click="moveBottom($index);">&#9660;</button>
							<button type="button" class="btn action" ng-click="removeLine($index);">&times;</button>
						</td>
					</tr>
				</tbody>
			</table>

			<div class="form-actions">
				<button class="btn" type="submit"><?= T::ranslate('Save') ?></button>
				<a class="btn" href="guide_remove.php?gid={{ id }}" onclick="return confirm('Êtes-vous vraiment sûr de vouloir supprimer ce conducteur ?');"><?= T::ranslate('Delete') ?></a>
			</div>
		</form>
	</div>
</main>


<?php include 'parts/tail.php'; ?>