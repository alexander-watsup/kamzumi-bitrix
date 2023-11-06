<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die(); 

use Redsign\Tuning\Tuning;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;

if (empty($arResult['OPTIONS']))
    return;

$svgSettings = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 25"><path d="M17.016,12.959A4.806,4.806,0,0,0,15.539,9.4,4.869,4.869,0,0,0,12,7.944,4.8,4.8,0,0,0,8.438,9.42a4.866,4.866,0,0,0-1.453,3.539,4.8,4.8,0,0,0,1.477,3.562A4.865,4.865,0,0,0,12,17.975,4.828,4.828,0,0,0,15.539,16.5a4.827,4.827,0,0,0,1.476-3.539h0Zm-1.828,0A3.2,3.2,0,0,1,12,16.147a3.2,3.2,0,0,1-3.187-3.187A3.2,3.2,0,0,1,12,9.772,3.2,3.2,0,0,1,15.188,12.959Zm-2.672,12a3.8,3.8,0,0,0,.469-0.023l0.375-.047a2.52,2.52,0,0,1,.281-0.023h0.094a0.845,0.845,0,0,0,.8-0.8l0.281-1.828a10.9,10.9,0,0,0,1.641-.656l1.5,1.172a0.935,0.935,0,0,0,1.125,0l0.188-.141q0.187-.187.516-0.469t0.609-.562l0.094-.094q0.281-.281.563-0.609t0.422-.516l0.188-.187a0.856,0.856,0,0,0,0-1.125L20.531,17.6a11.642,11.642,0,0,0,.7-1.688l1.875-.234a0.9,0.9,0,0,0,.8-0.8V14.741a1.867,1.867,0,0,1,.023-0.258c0.015-.109.031-0.234,0.047-0.375A4.352,4.352,0,0,0,24,13.639V12.467A4.36,4.36,0,0,0,23.977,12c-0.016-.141-0.032-0.265-0.047-0.375a1.849,1.849,0,0,1-.023-0.258V11.225a0.845,0.845,0,0,0-.8-0.8l-1.828-.281a10.981,10.981,0,0,0-.656-1.641l1.125-1.5a0.937,0.937,0,0,0,0-1.125l-0.187-.187q-0.141-.187-0.422-0.516t-0.562-.609l-0.094-.094Q20.2,4.194,19.875,3.912t-0.516-.422L19.172,3.3a0.855,0.855,0,0,0-1.125,0L16.594,4.428a8.664,8.664,0,0,0-1.641-.7L14.672,1.85a0.845,0.845,0,0,0-.8-0.8H13.781A2.361,2.361,0,0,1,13.5,1.03l-0.375-.047A3.941,3.941,0,0,0,12.656.959H12a4.92,4.92,0,0,0-1.734.141,0.827,0.827,0,0,0-.8.75L9.188,3.678a8.626,8.626,0,0,0-1.641.7l-1.5-1.172a0.937,0.937,0,0,0-1.125,0L4.734,3.4q-0.188.141-.516,0.422t-0.609.563l-0.094.094q-0.281.281-.562,0.609T2.531,5.6l-0.187.188a0.855,0.855,0,0,0,0,1.125L3.469,8.366a8.667,8.667,0,0,0-.7,1.641l-1.875.281a0.845,0.845,0,0,0-.8.8v0.094a2.527,2.527,0,0,1-.023.281c-0.016.125-.031,0.25-0.047,0.375A3.792,3.792,0,0,0,0,12.3v0.656a4.919,4.919,0,0,0,.141,1.734,0.826,0.826,0,0,0,.75.8l1.828,0.281a8.629,8.629,0,0,0,.7,1.641l-1.172,1.5a0.935,0.935,0,0,0,0,1.125l0.188,0.188q0.141,0.188.422,0.516T3.422,21.35l0.094,0.094q0.281,0.281.609,0.563t0.516,0.422l0.188,0.188a0.856,0.856,0,0,0,1.125,0l1.453-1.125a8.629,8.629,0,0,0,1.641.7l0.281,1.875a0.844,0.844,0,0,0,.8.8h0.094a2.548,2.548,0,0,1,.281.023l0.375,0.047a3.787,3.787,0,0,0,.469.023h1.172ZM10.828,21.4a0.868,0.868,0,0,0-.7-0.8,8.983,8.983,0,0,1-2.156-.891A0.823,0.823,0,0,0,6.8,19.663L5.438,20.694a5.625,5.625,0,0,1-.609-0.516l-0.094-.141-0.562-.562L5.2,18.116a0.724,0.724,0,0,1,.141-0.187l0.047-.141a0.9,0.9,0,0,0-.094-0.8,7.278,7.278,0,0,1-.937-2.156V14.788a0.828,0.828,0,0,0-.75-0.8l-1.734-.234V12.069l1.734-.234a0.948,0.948,0,0,0,.8-0.75,8.786,8.786,0,0,1,.938-2.2l0.047-.047a1.023,1.023,0,0,0-.047-1.078L4.266,6.4q0.093-.093.281-0.3c0.125-.141.218-0.242,0.281-0.3l0.094-.094a6.837,6.837,0,0,1,.609-0.562L6.891,6.162a0.538,0.538,0,0,0,.375.188,0.876,0.876,0,0,0,.7-0.094,7.212,7.212,0,0,1,2.2-.937,0.827,0.827,0,0,0,.8-0.75L11.2,2.787A4.682,4.682,0,0,1,12,2.741h0.094a4.688,4.688,0,0,1,.8.047l0.234,1.734a0.947,0.947,0,0,0,.75.8,8.813,8.813,0,0,1,2.2.938,0.794,0.794,0,0,0,1.078,0l1.406-1.031a6.259,6.259,0,0,1,.563.516l0.141,0.141c0.062,0.063.156,0.164,0.281,0.3s0.2,0.227.234,0.258L18.75,7.85a0.83,0.83,0,0,0-.141.891,0.267,0.267,0,0,0,.094.188,8.488,8.488,0,0,1,.938,2.3,0.845,0.845,0,0,0,.75.7l1.734,0.234V13.85l-1.734.234a0.962,0.962,0,0,0-.8.8,8.8,8.8,0,0,1-.938,2.2,0.841,0.841,0,0,0,.047,1.031l1.031,1.406a6.058,6.058,0,0,1-.516.563l-0.141.141c-0.063.063-.164,0.157-0.3,0.281s-0.227.2-.258,0.234l-1.406-1.031a1.035,1.035,0,0,0-.375-0.187,1.425,1.425,0,0,0-.7.094,7.108,7.108,0,0,1-2.3.984,0.845,0.845,0,0,0-.7.75L12.8,23.084H11.063Z"></path></svg>';
$svgClose = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 19.59 19.59"><path class="cls-1" d="M18.59,19.09a.53.53,0,0,1-.36-.15L9.79,10.5,1.35,18.94a.51.51,0,0,1-.35.15.51.51,0,0,1-.35-.15.49.49,0,0,1-.15-.35.49.49,0,0,1,.15-.36L9.09,9.79.65,1.35a.48.48,0,0,1,0-.7A.47.47,0,0,1,1,.5a.47.47,0,0,1,.35.15L9.79,9.09,18.23.65A.49.49,0,0,1,18.59.5h0a.49.49,0,0,1,.35.15.51.51,0,0,1,.15.35.51.51,0,0,1-.15.35L10.5,9.79l8.44,8.44a.51.51,0,0,1-.35.86Z"></path><path d="M18.59,1h0L11.21,8.38,18.58,1h0m0-1a1,1,0,0,0-.71.29L9.79,8.38,1.71.29A1,1,0,0,0,.29.29a1,1,0,0,0,0,1.42L8.38,9.79.29,17.88a1,1,0,1,0,1.42,1.41l8.08-8.08,8.09,8.08a1,1,0,0,0,1.41,0,1,1,0,0,0,0-1.41L11.21,9.79l8.08-8.08A1,1,0,0,0,18.59,0Z"></path></svg>';
$preloader = '<div class="preloader-wrapper big active"><div class="spinner-layer"><div class="circle-clipper left"><div class="circle"></div></div><div class="gap-patch"><div class="circle"></div></div><div class="circle-clipper right"><div class="circle"></div></div></div></div>';
?>

<div <?
    ?>id="rstuning" <?
    ?>class="rstuning <?=(!empty($arResult['TABS']) && count($arResult['TABS']['ITEMS']) > 1 ? 'mod-tabs' : '')?> <?=($arResult['COOKIE_OPEN'] == 'Y' ? 'open' : 'closed')?> js-rstuning" <?
    ?>data-reload="N" <?
    ?>data-fromSession="<?=$arResult['FROM_SESSION']?>" <?
    ?>data-siteid="<?=SITE_ID?>" <?
    ?>data-cookieopen="<?=$arResult['COOKIE_OPEN']?>" <?
    ?>data-firsttabid="<?=$arResult['TABS']['FIRST_TAB_ID']?>" <?
    ?>>
    <form class="rstuning__form rstuning__body" action="<?=$arResult['FORM_ACTION']?>" method="POST" enctype="multipart/form-data" id="rstuning-form">
        <input type="hidden" name="rstuning_ajax" value="Y">
        <input type="hidden" name="site_id" value="<?=SITE_ID?>">

        <div class="rstuning__overlay js-rstuning__main-overlay"></div>

        <div class="rstuning__open-button"><a class="rstuning__open-button__link js-rstuning__open-button" href="#rstuning"><?=$svgSettings?></a></div>

        <div class="rstuning__preloader js-rstuning__preloader"><?=$preloader?></div>

        <?php if(!empty($arResult['TABS']['ITEMS'])): ?>
        <div class="rstuning__sidebar js-rstuning__sidebar">
            <div class="rstuning__sidebar__back-button"><div class="rstuning__hamburger js-rstuning__toggle-sidebar"><div class="rstuning__hamburger-box"><div class="rstuning__hamburger-inner"></div></div></div></div>
            <div class="rstuning__scroll">
                <ul class="rstuning__tabs__nav js-rstuning__tab-switcher">
                    <?php if (!empty($arResult['TABS']['ITEMS'])):
                        foreach ($arResult['TABS']['ITEMS'] as $tabId => $arTab): ?><?
                        ?><li class="rstuning__tabs__item"><a class="rstuning__tabs__link<?=($arResult['TABS']['FIRST_TAB_ID'] == $tabId ? ' active' : '')?> js-rstuning-nav" href="#<?=$tabId?>" data-tabid="<?=$tabId?>" data-name="<?=$arTab['NAME']?>"><span><?=$arTab['NAME']?></span></a></li><?
                        ?><?php endforeach;
                    endif; ?>
                    <li class="rstuning__tabs__item"><a class="rstuning__tabs__link mod-default js-tuning-default-settings" href="#"><span><?=Loc::getMessage('RS.TUNING.BUTTON.DEFAULT_SETTINGS')?></span></a></li>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <div class="rstuning__content js-rstuning__content">
            <?php if(!empty($arResult['TABS']['ITEMS'])): ?>
            <div class="rstuning__content__toggle-sidedebar"><div class="rstuning__hamburger js-rstuning__toggle-sidebar"><div class="rstuning__hamburger-box"><div class="rstuning__hamburger-inner"></div></div></div></div>
            <div class="rstuning__content-overlay js-rstuning__content-overlay"></div>
            <?php endif; ?>

            <div class="rstuning__close-button"><a class="rstuning__close-button__link js-rstuning__close-button" href="#"><?=$svgClose?></a></div>
            <div class="rstuning__scroll">
                <?php if (!empty($arResult['TABS'])): ?>
                    <div class="rstuning__tabs__content js-rstuning__tab-content">
                    <?php foreach ($arResult['TABS']['ITEMS'] as $tabId => $arTab): ?>
                        <div class="rstuning__tabs__pane <?=($arResult['TABS']['FIRST_TAB_ID'] == $tabId ? 'active' : '')?> js-rstuning__tab-pane" data-tabid="<?=$tabId?>">
                            <div class="rstuning-row"><?php
                            if (!empty($arTab['OPTIONS'])): 
                                foreach ($arTab['OPTIONS'] as $optionId): 
                                    if (!empty($arResult['OPTIONS'][$optionId])):
                                        echo $arResult['OPTIONS'][$optionId]['DISPLAY_HTML'];
                                    endif;
                                endforeach;
                            endif;
                            ?></div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="rstuning-row"><?php
                    if (!empty($arResult['OPTIONS'])):
                        foreach ($arResult['OPTIONS'] as $arOption):
                            echo $arOption['DISPLAY_HTML'];
                        endforeach;
                    endif;
                    ?></div>
                <?php endif; ?>
            </div>
        </div>

    </form>
</div><!-- /.rstuning -->

<div id="rstuning_styles"></div>
<script>
var rsTuning = new RS.Tuning(),
    rsTuningComponent = new RS.TuningComponent();
rsTuning.setColorMacrosContent("<?=CUtil::JSEscape($arResult['FILE_COLOR_MACROS_CONTENT'])?>");
</script>
