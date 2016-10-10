<?php include 'init.php'; ?>
<?php
$templates = Templates::getList();
?>
<?php include 'parts/head.php'; ?>

<main class="wrapper">
	<div class="inner">
		<h2><?= T::ranslate('Templates') ?></h2>
	</div>

	<div class="inner">
		<?php if (count($templates) > 0): ?>
			<table class="items col-12">
				<thead>
					<tr>
						<th style="width:70%;"><?= T::ranslate('Template name') ?></th>
						<th class="actions" style="width:30%;"><?= T::ranslate('Actions') ?></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($templates as $key => $value): ?>
					<tr>
						<td>
							<div class="title"><?= $value['title'] ?></div>
							<div class="description"><?= $value['description'] ?></div>
						</td>
						<td class="actions">
							<a href="template_edit.php?tid=<?= $key ?>"><?= T::ranslate('Edit') ?></a> |
							<a href="template_duplicate.php?tid=<?= $key ?>"><?= T::ranslate('Duplicate') ?></a> |
							<a href="guide_edit.php?tid=<?= $key ?>"><?= T::ranslate('Use') ?></a> |
							<a href="template_remove.php?tid=<?= $key ?>" onclick="return confirm('Êtes-vous vraiment sûr de vouloir supprimer ce modèle ?');"><?= T::ranslate('Delete') ?></a>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
		<a href="template_edit.php" class="btn"><?= T::ranslate('New template') ?></a>
	</div>
</main>


<?php include 'parts/tail.php'; ?>