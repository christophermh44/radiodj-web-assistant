<?php include_once 'init.php';
ob_start();

if (empty($_GET['gid'])) die;
$id = $_GET['gid'];

$guide = Guides::load($id);
$filename = Guides::getFileName($guide, 'pdf');

function getIntro($line) {
	if (is_string($line['additionnals'])) {
		return false;
	}
	$cues = explode('&', $line['additionnals']['cue_times']);
	$cues = array_map(function($item) {
		if (!$item) return null;
		$kv = explode('=', $item);
		if (count($kv) == 2) {
			$key = $kv[0];
			$value = $kv[1];
			return [
				'key' => $key,
				'value' => $value
			];
		} else return null;
	}, $cues);
	$cues = array_filter($cues, function($item) {
		return !empty($item) && $item['key'] == 'int';
	});
	if (count($cues) == 1) {
		return array_values($cues)[0]['value'];
	}
	return false;
}

require_once 'libs/dompdf/autoload.inc.php'; 
$dompdf = new \Dompdf\Dompdf(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<title><?= $filename ?></title>
	<style>
	html {
		font: 400 1rem/1.5 Arial, sans-serif;
	}

	h1 {
		border-bottom: 1px solid #888;
		color: #888;
		display: inline-block;
		font-size: 1rem;
		font-weight: normal;
		left: 0;
		margin-bottom: 1rem;
		padding-bottom: 0.5rem;
		right: 0;
		top: 0;
		width: 100%;
	}

	h1 .title {
		font-weight: bold;
	}

	h1 .radio-name {
		display: block;
		position: absolute;
		right: 0;
	}

	main {
		display: block;
		padding-bottom: 2rem;
		width: 100%;
	}

	table {
		border-collapse: collapse;
		width: 100%;
	}

	table th,
	table td {
		padding: 0.25rem;
		vertical-align: top;
	}

	table thead {
		display: table-header-group;
	}

	table thead tr {
		background: #000;
		color: #fff;
		text-transform: uppercase;
	}

	table tbody td {
		text-align: left;
	}

	table tbody td .description {
		color: #888;
		font-size: 75%;
		margin: 0;
		padding: 0;
	}

	table tbody tr:nth-child(even) > * {
		background-color: #eee;
	}

	table tbody td.number {
		text-align: right;
	}

	table tbody td.time {
		text-align: center;
	}

	table tbody td.time .description {
		margin-top: 1.5rem;
	}

	table tbody td p {
		margin: 0;
	}

	table tbody td p.tags {
		border-top: 1px solid #ddd;
		margin-top: 0.5rem;
		padding-bottom: 0.25rem;
		padding-top: 0.5rem;
	}

	table tbody td p.designation {
		font-weight: bold;
	}

	footer {
		background-color: #eee;
		margin-top: 2rem;
		padding: 1rem;
	}

	footer strong {
		display: inline-block;
		margin-bottom: 1rem;
	}
	</style>
</head>
<body>
	<header>
		<h1>
			<span class="radio-name"><?= Conf::get('radio_name') ?></span>
			<span class="title">[<?= $guide['template']['title'] ?>] <?= $guide['title'] ?></span>
			<br>
			<?= date('Y.m.d', $guide['date']) ?> -
			<?= date('H:i:s', strtotime($guide['template']['start'])) ?>
		</h1>
	</header>

	<main>
		<table>
			<thead>
				<tr>
					<th style="width:5%;">#</th>
					<th style="width:15%;"><?= T::ranslate('Starts at') ?></th>
					<th style="width:65%;"><?= T::ranslate('Label') ?></th>
					<th style="width:15%;"><?= T::ranslate('Duration') ?></th>
				</tr>
			</thead>

			<tbody>
				<?php $index = 1;
				$start = strtotime($guide['template']['start']);
				foreach ($guide['lines'] as $value):
					$duration = $value['duration']; ?>
					<tr>
						<th class="number"><?= $index ?></th>
						<td class="time"><?= date('H:i:s', $start) ?></td>
						<td>
							<p class="designation">
								<?= $value['designation'] ?>
							</p>
							<?php if (!is_string($value['additionnals'])): ?>
							<p class="tags">
								<?= $value['additionnals']['artist'] ?> - <?= $value['additionnals']['title'] ?>
							</p>
							<p class="description">
								ID : <?= $value['ID'] ?>
								| <?= T::ranslate('Category') ?> : <?= $value['additionnals']['category']['name'] ? $value['additionnals']['category']['name'] : '-' ?>
								| <?= T::ranslate('Subcategory') ?> : <?= $value['additionnals']['subCategory']['name'] ? $value['additionnals']['subCategory']['name'] : '-' ?>
								| <?= T::ranslate('Genre') ?> : <?= $value['additionnals']['genre']['name'] ? $value['additionnals']['genre']['name'] : '-' ?>
							</p>
							<?php endif; ?>
						</td>
						<td class="time">
							<?= date('H:i:s', $duration - 3600)?>
							<?php if (getIntro($value)): ?>
							<p class="description">
								Intro : <?= round(getIntro($value)) ?> <?= T::ranslate('sec.') ?>
							</p>
							<?php endif; ?>
						</td>
					</tr>
				<?php $index++;
				$start+= $duration;
				endforeach; ?>
			</tbody>
		</table>

		<footer>
			<strong>Notes :</strong>
			<div>
				<?= $guide['details'] ?>
			</div>
		</footer>
	</main>
</body>
</html>
<?php $out = ob_get_clean();
$dompdf->load_html($out);
$dompdf->render();
$canvas = $dompdf->get_canvas();
$font = (new \DomPdf\FontMetrics($canvas, new \DomPdf\Options))->get_font('helvetica', 'normal');
$size = 9;
$w = $canvas->get_width();
$h = $canvas->get_height();
$x = 36;
$y = $h - 48;
$canvas->page_text($x, $y, '{PAGE_NUM}/{PAGE_COUNT}' . ' | ' . Conf::get('radio_name') . ' > [' . $guide['template']['title'] . '] ' . $guide['title'] . ' - ' . date('Y.m.d', $guide['date']) . ' - ' . date('H:i:s', strtotime($guide['template']['start'])), $font, $size);
$dompdf->stream($filename, ['Attachment' => 0]);
//echo $out;