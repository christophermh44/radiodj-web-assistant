<?php include 'init.php'; ?>
<?php
$q = Query::getInstance();
$results = $q->process('SELECT ID, name FROM events');
$events = [];
foreach ($results as $r) {
	$events[$r->ID] = $r->name;
}
$queries = Queries::load();
?>
<?php include 'parts/head.php'; ?>

<main class="wrapper" ng-controller="templateEditor">
	<div class="inner">
		<h2><?= T::ranslate('Template editor') ?></h2>
	</div>

	<div class="inner">
		<div ng-hide="loaded">
			<?= T::ranslate('Loading…') ?>
		</div>

		<form action="template_save.php" method="post" class="loading">
			<input type="hidden" name="serial" value="{{ getSerial() }}">
			<input type="hidden" name="id" value="{{ id }}">

			<fieldset>
				<legend><?= T::ranslate('Informations') ?></legend>

				<p class="form-control">
					<label for="template-title"><?= T::ranslate('Template name') ?></label>
					<input type="text" name="template-title" id="template-title" ng-model="title">
				</p>

				<p class="form-control">
					<label for="template-description"><?= T::ranslate('Template description') ?></label>
					<input type="text" name="template-description" id="template-description" ng-model="description" class="large">
				</p>

				<p class="form-control">
					<label for="template-start"><?= T::ranslate('Start time') ?></label>
					<uib-timepicker ng-model="start" show-meridian="false" show-seconds="true" show-spinners="false" mousewheel="false"></uib-timepicker>
				</p>
			</fieldset>

			<table class="items col-12">
				<thead>
					<tr>
						<td colspan="6">
							<button type="button" class="btn" ng-click="newLine();"><?= T::ranslate('Add line') ?></button>
						</td>
					</tr>
					<tr>
						<th style="width:2%;">#</th>
						<th style="width:36%;"><?= T::ranslate('Label') ?></th>
						<th style="width:30%;"><?= T::ranslate('Resource') ?></th>
						<th style="width:16%;"><?= T::ranslate('Duration') ?></th>
						<th style="width:8%;"><?= T::ranslate('Starts at') ?></th>
						<th style="width:8%;"><?= T::ranslate('Actions') ?></th>
					</tr>
				</thead>

				<tfoot>
					<tr>
						<td colspan="6">
							<button type="button" class="btn" ng-click="newLine();"><?= T::ranslate('Add line') ?></button>
						</td>
					</tr>
				</tfoot>
				
				<tbody>
					<tr ng-repeat="line in lines track by $index">
						<td>{{ $index + 1 }}</td>

						<td>
							<p class="form-control">
								<label for="text-designation-{{ $index }}" class="hidden"><?= T::ranslate('Label') ?></label>
								<input type="text" ng-model="line.params.topic" name="text-designation" id="text-designation-{{ $index }}" class="large">
							</p>
						</td>
						
						<td>
							<p class="form-control">
								<label for="item-ressource-{{ $index }}" class="hidden"><?= T::ranslate('Resource') ?></label>
								<select name="item-ressource" id="item-ressource-{{ $index }}" ng-model="line.itemRessource" ng-options="key as value.label for (key, value) in lineRessources"></select>
							</p>

							<p class="form-control" ng-show="line.itemRessource == 'fixed'">
								<label for="item-choosen-{{ $index }}"><?= T::ranslate('Resource') ?></label>
							    <input type="text" ng-model="line.params.choosen" uib-typeahead="song as song.name for song in getSongs($viewValue)" ng-model-options="{debounce:250}" typeahead-on-select="setDuration($index, $item);" class="large" />
							</p>

							<p class="form-control" ng-show="line.itemRessource == 'query'">
								<label for="item-event-{{ $index }}"><?= T::ranslate('Query') ?></label>
								<select ng-model="line.params.query" class="large">
									<?php foreach ($queries as $key => $value): ?>
										<option value="<?= $key ?>"><?= $value['alias'] ?></option>
									<?php endforeach; ?>
								</select>
							</p>
						</td>

						<td>
							<p class="form-control">
								<label for="item-type-{{ $index }}" class="hidden"><?= T::ranslate('Settings') ?></label>
								<select name="item-type" id="item-type-{{ $index }}" ng-model="line.itemType">
									<option value="fixed"><?= T::ranslate('Fixed') ?></option>
									<option value="elastic"><?= T::ranslate('Elastic') ?></option>
									<option value="driven" ng-if="line.itemRessource != 'nope'"><?= T::ranslate('From resource') ?></option>
								</select>
							</p>

							<p class="form-control" ng-show="line.itemType == 'elastic'">
								<label for="item-ends-at-{{ $index }}"><?= T::ranslate('Finish at') ?></label>
								<uib-timepicker ng-model="line.params.endsAt" hour-step="1" minute-step="1" show-meridian="false" show-seconds="true" show-spinners="false" mousewheel="false"></uib-timepicker>
							</p>

							<p class="form-control">
								<label class="hidden"><?= T::ranslate('Duration') ?></label>
								<uib-durationpicker class="duration" show-spinners="false" mousewheel="false" ng-model="line.duration" ng-disabled="line.itemType == 'elastic' || (line.itemType == 'driven' &amp;&amp; line.itemRessource != 'query')"></uib-durationpicker>
								<span class="description" ng-show="line.itemRessource == 'query' &amp;&amp; line.itemType == 'driven'" title="<?= T::ranslate('This duration allows you to build a template based on theorical durations. Here, you have to give an estimated duration for this item.') ?>"><?= T::ranslate('Estimated duration') ?></span>
							</p>
						</td>

						<td>
							{{ startAt($index)|date:'HH:mm:ss' }}
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
				<a class="btn" href="template_remove.php?tid={{ id }}" onclick="return confirm('<?= T::ranslate('Do you really want to remove this template?') ?>');"><?= T::ranslate('Delete') ?></a>
			</div>
		</form>
	</div>
</main>


<?php include 'parts/tail.php'; ?>