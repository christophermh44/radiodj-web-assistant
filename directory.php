<?php include 'init.php'; ?>
<?php include 'parts/head.php'; ?>

<main class="wrapper" ng-controller="directory">
	<div class="inner">
		<h2><?= T::ranslate('Directory') ?></h2>
	</div>

	<div class="inner">
		<div class="directory-container">
			<table ng-class="{expanded: directoryExpanded, items: true, directory: true}">
				<caption>
					<button type="button" class="btn" ng-click="cleanSelection();"><?= T::ranslate('Remove selection') ?></button>
					<button type="button" class="btn" ng-click="prepareDownload();"><?= T::ranslate('Download selected songs') ?></button>
				</caption>
				<thead>
					<tr>
						<th>
							<label for="select-all">
								<span class="label"></span>
								<input type="checkbox" id="select-all">
							</label>
						</th>
						<th class="column--id" style="width: 5%;">
							<label for="filter-id">
								<span class="label"><?= T::ranslate('ID') ?></span>
								<input type="text" id="filter-id" ng-model="filters.ID">
							</label>
						</th>
						<th style="width: 25%;">
							<label for="filter-artist">
								<span class="label"><?= T::ranslate('Artist') ?></span>
								<input type="text" id="filter-artist" ng-model="filters.artist">
							</label>
						</th>
						<th style="width: 25%;">
							<label for="filter-title">
								<span class="label"><?= T::ranslate('Title') ?></span>
								<input type="text" id="filter-title" ng-model="filters.title">
							</label>
						</th>
						<th style="width: 10%;">
							<label for="filter-category">
								<span class="label"><?= T::ranslate('Category') ?></span>
								<input type="text" id="filter-category" ng-model="filters.category.name">
							</label>
						</th>
						<th style="width: 10%;">
							<label for="filter-subcategory">
								<span class="label"><?= T::ranslate('Subcategory') ?></span>
								<input type="text" id="filter-subcategory" ng-model="filters.subCategory.name">
							</label>
						</th>
						<th  style="width: 10%;">
							<label for="filter-genre">
								<span class="label"><?= T::ranslate('Genre') ?></span>
								<input type="text" id="filter-genre" ng-model="filters.genre.name">
							</label>
						</th>
						<th style="width: 10%;">
							<label for="filter-type">
								<span class="label"><?= T::ranslate('Type') ?></span>
								<input type="text" id="filter-type" ng-model="filters.type">
							</label>
						</th>
						<th style="width: 5%;">
							<span class="label"><?= T::ranslate('Duration') ?></span>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="(key, value) in directoryData|filter:filters" ng-click="value.checked = !value.checked" ng-class="{selected: value.checked}">
						<td>
							<input type="checkbox" ng-model="value.checked">
						</td>
						<td>
							{{ value.ID }}<br>
							<a href="directory_edit.php?id={{ value.ID }}"><?= T::ranslate('Edit') ?></a>
						</td>
						<td>
							{{ value.artist }}
						</td>
						<td>
							{{ value.title }}
						</td>
						<td>
							{{ value.category.name }}
						</td>
						<td>
							{{ value.subCategory.name }}
						</td>
						<td>
							{{ value.genre.name }}
						</td>
						<td>
							{{ value.typeName }}
						</td>
						<td>
							{{ formatTime(value.duration) }}
						</td>
					</tr>
				</tbody>
			</table>
			<div ng-hide="directoryData.length > 0">
					<?= T::ranslate('Songs are loading… This operation could take seconds.') ?>
			</div>
		</div>
	</div>
</main>

<?php include 'parts/tail.php'; ?>