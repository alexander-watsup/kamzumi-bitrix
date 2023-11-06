<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

CJSCore::Init(array("jquery"));

foreach ($arResult['ITEMS'] as $item) {
	$params = [
		'id' => '#rm-popup-' . $item['ID'],
		'frequency' => $item['PROPERTIES']['FREQUENCY']['VALUE'],
		'shedule' => [
			$item['PROPERTIES']['SHEDULE_MONDAY']['VALUE'],
			$item['PROPERTIES']['SHEDULE_TUESDAY']['VALUE'],
			$item['PROPERTIES']['SHEDULE_WEDNESDAY']['VALUE'],
			$item['PROPERTIES']['SHEDULE_THURSDAY']['VALUE'],
			$item['PROPERTIES']['SHEDULE_FRIDAY']['VALUE'],
			$item['PROPERTIES']['SHEDULE_SATURDAY']['VALUE'],
			$item['PROPERTIES']['SHEDULE_SUNDAY']['VALUE'],
		]
	];
?>
	<div style="display: none;" class="rm-popup" id="rm-popup-<?= $item['ID'] ?>">
		<div class="img">
			<img src="<?= \CFile::GetPath($item['PROPERTIES']['IMG']['VALUE']) ?>" class="logo" alt="Логотип Kam-Zu-Mi" />
		</div>
		<div class="l1"><?= $item['PROPERTIES']['TITLE_1']['~VALUE'] ?></div>
		<div class="l2"><?= $item['PROPERTIES']['TITLE_2']['~VALUE'] ?></div>
		<div class="l3"><?= $item['PROPERTIES']['TITLE_3']['~VALUE'] ?></div>
		<div data-fancybox-close class="button"><?= $item['PROPERTIES']['BUTTON_TEXT']['VALUE'] ?></div>
	</div>
	<script>
		createPopup(<?= json_encode($params) ?>);
	</script>
<?
}
?>