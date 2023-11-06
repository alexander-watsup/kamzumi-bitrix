<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var CMain $APPLICATION
 */

CJSCore::Init(['ajax']);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];
$arSvg = [
    'CONTROLS' => [
        'BUTTON' => FileHelper::getFileData(__DIR__.'/svg/control.button.icon.svg')
    ],
    'AVATAR' => FileHelper::getFileData(__DIR__.'/svg/avatar.svg')
];

$bSuccess = $arResult['FORM_RESULT_STATUS'] === 'success';
$bError = $arResult['FORM_RESULT_STATUS'] === 'error';

?>
<div class="widget c-reviews-sender c-reviews-sender-template-2" id="<?= $sTemplateId ?>">
    <?php if ($arVisual['POST']['USE']) { ?>
        <div class="widget-content">
            <?php if (!$bSuccess) {
                include(__DIR__.'/parts/controls.php');

                if ($bError)
                    include(__DIR__.'/parts/notification.php');

                if ($arVisual['POST']['ALLOW'])
                    include(__DIR__.'/parts/post.php');
            } else {
                include(__DIR__.'/parts/notification.php');
            } ?>
        </div>
    <?php } ?>
    <div class="widget-content">
        <div class="widget-items">
            <?php include(__DIR__.'/parts/items.php') ?>
        </div>
    </div>
    <?php if ($arVisual['POST']['USE'] && $arVisual['POST']['ALLOW'])
        include(__DIR__.'/parts/script.php');
    ?>
</div>