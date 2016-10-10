<?php include 'init.php'; ?>
<?php include 'parts/head.php'; ?>

<main class="wrapper" ng-controller="queryEditor">
	<div class="inner">
		<h2><?= T::ranslate('Queries') ?></h2>
	</div>

	<!-- 
		TODO : select by
		- 1 category
		- 0-1 genre (0 = any)
		- 0-1 subcategory (0 = any)
		- MODE: (LRP,MRP,LFP,MFP,RANDOM)
		- by artist and title count and time (date_played, tdate_played, artist_played, tartist_played, count_played, …)
		- force ENABLED = true
		- force START_DATE|END_DATE = inside
		- force select * weight
	-->

	<div class="inner">
		<form action="queries_save.php" method="post">
			<table class="items col-12">
				<thead>
					<tr>
						<th style="width:16%;"><?= T::ranslate('Alias') ?></th>
						<th style="width:16%;"><?= T::ranslate('Category') ?></th>
						<th style="width:16%;"><?= T::ranslate('Subcategory') ?></th>
						<th style="width:16%;"><?= T::ranslate('Genre') ?></th>
						<th style="width:16%;"><?= T::ranslate('Mode') ?></th>
						<th style="width:5%;" title="<?= T::ranslate('Repeat delay for the same artist') ?>"><?= T::ranslate('RDSA') ?></th>
						<th style="width:5%;" title="<?= T::ranslate('Repeat delay for the same title') ?>"><?= T::ranslate('RDST') ?></th>
						<th class="actions" style="width:10%;"><?= T::ranslate('Actions') ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="3">
							<button type="button" class="btn" ng-click="createQuery();"><?= T::ranslate('Add query') ?></button>
						</td>
					</tr>
				</tfoot>
				<tbody>
					<tr ng-repeat="query in queries track by $index">
						<td>
							<p class="form-control">
								<input type="text" ng-model="query.alias">
							</p>
						</td>
						<td>
							<p class="form-control">
								<input type="text" ng-model="query.category" uib-typeahead="category as category.name for category in getCategories($viewValue)" ng-model-options="{debounce:250}" typeahead-on-select="query.subCategory = '';" />
							</p>
						</td>
						<td>
							<p class="form-control">
								<input type="text" placeholder="(facultatif)" ng-model="query.subCategory" uib-typeahead="subCategory as subCategory.name for subCategory in getSubCategories($viewValue, query.category)" ng-model-options="{debounce:250}" />
							</p>
						</td>
						<td>
							<p class="form-control">
								<input type="text" placeholder="(facultatif)" ng-model="query.genre" uib-typeahead="genre as genre.name for genre in getGenres($viewValue)" ng-model-options="{debounce:250}" />
							</p>
						</td>
						<td>
							<p class="form-control">
								<select name="item-mode" id="item-mode-{{ $index }}" ng-model="query.mode" ng-options="key as value.label for (key, value) in modes"></select>
							</p>
						</td>
						<td>
							<p class="form-control">
								<input type="number" ng-model="query.sard" min="0">
							</p>
						</td>
						<td>
							<p class="form-control">
								<input type="number" ng-model="query.ssrd" min="0">
							</p>
						</td>
						<td class="actions">
							<button type="button" class="btn" ng-click="deleteQuery($index);"><?= T::ranslate('Delete') ?></button>
						</td>
					</tr>
				</tbody>
			</table>

			<input type="hidden" name="queries" value="{{ getSerial() }}">
			<button type="submit" class="btn"><?= T::ranslate('Save') ?></button>
		</form>
	</div>
</main>

<?php include 'parts/tail.php'; ?>