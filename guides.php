<?php include 'init.php'; ?>
<?php
$guides = Guides::getList();
$templates = Templates::getList();
?>
<?php include 'parts/head.php'; ?>

<main class="wrapper">
	<div class="inner">
		<h2><?= T::ranslate('Guides') ?></h2>
	</div>

	<div class="inner">
		<?php if (count($guides) > 0): ?>
			<table class="items col-12">
				<thead>
					<tr>
						<th style="width:50%;"><?= T::ranslate('Guide') ?></th>
						<th style="width:15%;"><?= T::ranslate('Date') ?></th>
						<th class="actions" style="width:35%;"><?= T::ranslate('Actions') ?></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($guides as $key => $value): ?>
					<tr>
						<td>
							<div class="title"><?= $value['title'] ?></div>
							<div class="description"><?= $value['template']['title'] ?></div>
						</td>
						<td>
							<?= date('Y-m-d', $value['date']) ?>
							<?= date('H:i:s', strtotime($value['template']['start'])) ?>
						</td>
						<td class="actions">
							<a href="guide_edit.php?gid=<?= $key ?>"><?= T::ranslate('Edit') ?></a> |
							<a href="guide_duplicate.php?gid=<?= $key ?>"><?= T::ranslate('Duplicate') ?></a> |
							<a href="guide_pdf.php?gid=<?= $key ?>">PDF</a> |
							<a href="guide_m3u.php?gid=<?= $key ?>">M3U</a> |
							<a href="guide_pack.php?gid=<?= $key ?>">Pack MP3</a> |
							<a href="guide_remove.php?gid=<?= $key ?>" onclick="return confirm('<?= T::ranslate('Are you sure to delete this guide?') ?>');"><?= T::ranslate('Delete') ?></a>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
		<form action="guide_edit.php" method="get">
			<select name="tid">
				<?php foreach ($templates as $key => $value): ?>
					<option value="<?= $key ?>"><?= $value['title'] ?></option>
				<?php endforeach; ?>
			</select>
			<button type="submit" class="btn"><?= T::ranslate('New guide') ?></button>
		</form>
	</div>
</main>


<?php include 'parts/tail.php'; ?>