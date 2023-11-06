<?php if (defined("B_PROLOG_INCLUDED") && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Loader;
use intec\Core;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$this->setFrameMode(true);

$oRequest = Core::$app->request;
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$bIsBase = Loader::includeModule('catalog') && Loader::includeModule('sale');
$bIsLite = !$bIsBase && Loader::includeModule('intec.startshop');
$bIsAjax = false;
$bSeo = Loader::includeModule('intec.seo');

if ($oRequest->getIsAjax()) {
    $bIsAjax = $oRequest->get('catalog');
    $bIsAjax = ArrayHelper::getValue($bIsAjax, 'ajax') === 'Y';
}

$arParams = ArrayHelper::merge([
    'SECTIONS_CHILDREN_SECTION_DESCRIPTION_SHOW' => 'Y',
    'SECTIONS_CHILDREN_SECTION_DESCRIPTION_POSITION' => 'top',
    'SECTIONS_CHILDREN_CANONICAL_URL_USE' => 'N',
    'SECTIONS_CHILDREN_CANONICAL_URL_TEMPLATE' => null,
    'SECTIONS_CHILDREN_MENU_SHOW' => 'Y',
    'SECTIONS_CHILDREN_EXTENDING_USE' => 'N',
    'SECTIONS_CHILDREN_EXTENDING_COUNT' => 30,
    'SECTIONS_CHILDREN_EXTENDING_PROPERTY' => 'UF_SECTIONS',
    'SECTIONS_CHILDREN_EXTENDING_TEMPLATE' => null,
    'FILTER_AJAX' => 'N',
    'SECTIONS_LAYOUT' => '1'
], $arParams);

$sLayout = ArrayHelper::fromRange([1, 2], $arParams['SECTIONS_LAYOUT']);

include(__DIR__.'/parts/sort.php');

$arIBlock = $arResult['IBLOCK'];
$arSection = $arResult['SECTION'];
$arSeo = null;
$arCanonicalUrl = [
    'USE' => $arParams['SECTIONS_CHILDREN_CANONICAL_URL_USE'] === 'Y',
    'TEMPLATE' => $arParams['SECTIONS_CHILDREN_CANONICAL_URL_TEMPLATE']
];

if (empty($arCanonicalUrl['TEMPLATE']) || empty($arSection))
    $arCanonicalUrl['USE'] = false;

$arDescription = [
    'SHOW' => $arParams['SECTIONS_CHILDREN_SECTION_DESCRIPTION_SHOW'] === 'Y',
    'POSITION' => ArrayHelper::fromRange([
        'top',
        'bottom'
    ], $arParams['SECTIONS_CHILDREN_SECTION_DESCRIPTION_POSITION']),
    'VALUE' => !empty($arSection) ? $arSection['DESCRIPTION'] : null
];

if (empty($arDescription['VALUE']))
    $arDescription['SHOW'] = false;

$sLevel = 'CHILDREN';

include(__DIR__.'/parts/menu.php');
include(__DIR__.'/parts/tags.php');
include(__DIR__.'/parts/filter.php');
include(__DIR__.'/parts/sections.php');
include(__DIR__.'/parts/elements.php');

$arSectionsExtending['COUNT'] = $arSectionsExtending['COUNT'] - $arElements['COUNT'];
$arSectionsExtending['PARAMETERS']['FILTER_NAME'] = 'arCatalogSectionsExtendingFilter';

if ($arSectionsExtending['COUNT'] <= 0)
    $arSectionsExtending['SHOW'] = false;

$arTags['SHOWED'] = false;
$arMenu['SHOW'] = $arMenu['SHOW'] && $arParams['SECTIONS_CHILDREN_MENU_SHOW'] === 'Y';
$arColumns = [
    'SHOW' => $arMenu['SHOW'] || ($arFilter['SHOW'] && $arFilter['TYPE'] === 'vertical')
];

if ($arColumns['SHOW']) {
    $arSections['PARAMETERS']['WIDE'] = 'N';
    $arElements['PARAMETERS']['WIDE'] = 'N';
    $arSectionsExtending['PARAMETERS']['WIDE'] = 'N';
}

if ($arParams['SECTIONS_LAYOUT'] == 2) {
    $arSections['PARAMETERS']['WIDE'] = 'Y';
}

if ($arCanonicalUrl['USE'])
    $APPLICATION->SetPageProperty('canonical', CIBlock::ReplaceSectionUrl(
        $arCanonicalUrl['TEMPLATE'],
        $arSection,
        true,
        'S'
    ));

if (!empty($arSection['PICTURE'])) {
    $sPicture = CFile::GetPath($arSection['PICTURE']);
    $APPLICATION->SetPageProperty('og:image', Core::$app->request->getHostInfo() . $sPicture);
    unset($sPicture);
}

if ($arTags['SHOW'] && !$bSeo) {
    $this->SetViewTarget($sTemplateId.'-tags');

    $APPLICATION->IncludeComponent(
        'intec.universe:tags.list',
        $arTags['TEMPLATE'],
        $arTags['PARAMETERS'],
        $component
    );

    $this->EndViewTarget();
}

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog',
        'c-catalog-catalog-1',
        'p-section'
    ],
    'data' => [
        'layout' => $arParams['SECTIONS_LAYOUT']
    ]
]) ?>
    <div class="catalog-wrapper intec-content intec-content-visible">
        <div class="catalog-wrapper-2 intec-content-wrapper">
            <?php if ($sLayout === '2') { ?>
                <?php $APPLICATION->ShowViewContent($sTemplateId.'-description-top') ?>
                <?php if ($arSections['SHOW']) { ?>
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.section.list',
                        $arSections['TEMPLATE'],
                        $arSections['PARAMETERS'],
                        $component
                    ) ?>
                <?php } ?>
                <?php $arTags['SHOWED'] = true ?>
                <?php $APPLICATION->ShowViewContent($sTemplateId.'-tags') ?>
            <?php } ?>
            <?php if ($arFilter['SHOW'] && $arFilter['TYPE'] === 'horizontal') { ?>
                <?php if ($sLayout === '1') { ?>
                    <?php $arTags['SHOWED'] = true ?>
                    <?php $APPLICATION->ShowViewContent($sTemplateId.'-tags') ?>
                <?php } ?>
                    <!--noindex-->
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.smart.filter',
                        $arFilter['TEMPLATE'],
                        $arFilter['PARAMETERS'],
                        $component
                    ) ?>
                    <!--/noindex-->
            <?php } ?>
            <?= Html::beginTag('div', [
                'class' => 'catalog-content',
                'data' => [
                    'role' => !$arColumns['SHOW'] ? 'catalog.content' : null
                ]
            ]) ?>
                <?php if ($arColumns['SHOW']) { ?>
                    <div class="catalog-content-left intec-content-left">
                        <?php if ($arFilter['SHOW'] && $arFilter['TYPE'] === 'vertical') { ?>
                            <!--noindex-->
                            <?php $APPLICATION->IncludeComponent(
                                'bitrix:catalog.smart.filter',
                                $arFilter['TEMPLATE'],
                                $arFilter['PARAMETERS'],
                                $component
                            ) ?>
                            <!--/noindex-->
                        <?php } ?>
                        <?php if ($arMenu['SHOW']) { ?>
                            <div class="catalog-menu">
                                <?php $APPLICATION->IncludeComponent(
                                    'bitrix:menu',
                                    $arMenu['TEMPLATE'],
                                    $arMenu['PARAMETERS'],
                                    $component
                                ) ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="catalog-content-right intec-content-right">
                        <div class="catalog-content-right-wrapper intec-content-right-wrapper" data-role="catalog.content">
                <?php } ?>
                <?php if ($sLayout === '1') { ?>
                    <?php if (!$arTags['SHOWED']) { ?>
                        <?php $arTags['SHOWED'] = true ?>
                        <?php $APPLICATION->ShowViewContent($sTemplateId.'-tags') ?>
                    <?php } ?>
                <?php } ?>
                <?php if ($bIsAjax) $APPLICATION->RestartBuffer() ?>
                <?php if ($sLayout === '1') { ?>
                    <?php $APPLICATION->ShowViewContent($sTemplateId.'-description-top') ?>
                    <?php if ($arSections['SHOW']) { ?>
                        <?php $APPLICATION->IncludeComponent(
                            'bitrix:catalog.section.list',
                            $arSections['TEMPLATE'],
                            $arSections['PARAMETERS'],
                            $component
                        ) ?>
                    <?php } ?>
                <?php } ?>
                <?php if ($arElements['SHOW']) { ?>
                    <?php include(__DIR__.'/parts/panel.php') ?>
                    <?php
                        foreach ($arSort['PROPERTIES'] as $arSortProperty) {
                            if ($arSortProperty['ACTIVE']) {
                                $arElements['PARAMETERS']['ELEMENT_SORT_FIELD'] = $arSortProperty['FIELD'];
                                $arElements['PARAMETERS']['ELEMENT_SORT_ORDER'] = $arSort['ORDER'];

                                break;
                            }
                        }

                        unset($arSortProperty);
                    ?>
                <?php } ?>
                <?php if ($arElements['SHOW'] || !empty($arElements['TEMPLATE'])) { ?>
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.section',
                        $arElements['TEMPLATE'],
                        $arElements['PARAMETERS'],
                        $component
                    ) ?>
                    <?php if ($arSectionsExtending['SHOW']) { ?>
                        <?php $arSectionsExtending['RESULT'] = $APPLICATION->IncludeComponent('intec.seo:iblocks.section.extend.filter', '', [
                            'IBLOCK_ID' => $arIBlock['ID'],
                            'SECTION_ID' => $arSection['ID'],
                            'SECTIONS_ID' => ArrayHelper::getValue($arSection, $arSectionsExtending['PROPERTY']),
                            'COUNT' => $arSectionsExtending['COUNT'],
                            'FILTER_NAME' => 'arCatalogSectionsExtendingFilter',
                            'INCLUDE_SUBSECTIONS' => 'Y',
                            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                            'CACHE_TIME' => $arParams['CACHE_TIME']
                        ], $component) ?>
                        <?php if (!empty($arSectionsExtending['RESULT']) && !empty($arSectionsExtending['RESULT']['FILTER'])) { ?>
                            <?php if (!empty($arSectionsExtending['TITLE']) || Type::isNumeric($arSectionsExtending['TITLE'])) { ?>
                                <div class="catalog-title intec-ui-markup-h3">
                                    <?= $arSectionsExtending['TITLE'] ?>
                                </div>
                            <?php } ?>
                            <?php $APPLICATION->IncludeComponent(
                                'bitrix:catalog.section',
                                $arSectionsExtending['TEMPLATE'],
                                $arSectionsExtending['PARAMETERS'],
                                $component
                            ) ?>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
                <?php $APPLICATION->ShowViewContent($sTemplateId.'-description-bottom') ?>
                <?php if ($bSeo) { ?>
                    <?php $APPLICATION->IncludeComponent('intec.seo:iblocks.metadata.loader', '', [
                        'IBLOCK_ID' => $arIBlock['ID'],
                        'SECTION_ID' => $arSection['ID'],
                        'TYPE' => 'section',
                        'MODE' => 'single',
                        'METADATA_SET' => 'Y',
                        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                        'CACHE_TIME' => $arParams['CACHE_TIME']
                    ], $component) ?>
                    <?php $arSeo = $APPLICATION->IncludeComponent('intec.seo:filter.meta', '', [
                        'IBLOCK_ID' => $arIBlock['ID'],
                        'SECTION_ID' => $arSection['ID'],
                        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                        'CACHE_TIME' => $arParams['CACHE_TIME']
                    ], $component) ?>
                    <?php if ($arTags['USE']) {
                        $this->SetViewTarget($sTemplateId.'-tags');

                        $APPLICATION->IncludeComponent('intec.seo:filter.tags', '', [
                            'IBLOCK_ID' => $arIBlock['ID'],
                            'SECTION_ID' => $arSection['ID'],
                            'INCLUDE_SUBSECTIONS' => $arParams['INCLUDE_SUBSECTIONS'],
                            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                            'CACHE_TIME' => $arParams['CACHE_TIME']
                        ], $component);

                        $this->EndViewTarget();
                    } ?>
                <?php } ?>
                <?php if (!empty($arSeo) && !empty($arSeo['META']['descriptionTop']) || $arDescription['SHOW'] && $arDescription['POSITION'] === 'top') { ?>
                    <?php $this->SetViewTarget($sTemplateId.'-description-top') ?>
                    <div class="<?= Html::cssClassFromArray([
                        'catalog-description',
                        'catalog-description-top',
                        'intec-ui-markup-text'
                    ]) ?>"><?= !empty($arSeo) && !empty($arSeo['META']['descriptionTop']) ? $arSeo['META']['descriptionTop'] : $arDescription['VALUE'] ?></div>
                    <?php $this->EndViewTarget() ?>
                <?php } ?>
                <?php if (!empty($arSeo) && !empty($arSeo['META']['descriptionBottom']) || $arDescription['SHOW'] && $arDescription['POSITION'] === 'bottom') { ?>
                    <?php $this->SetViewTarget($sTemplateId.'-description-bottom') ?>
                    <div class="<?= Html::cssClassFromArray([
                        'catalog-description',
                        'catalog-description-bottom',
                        'intec-ui-markup-text'
                    ]) ?>"><?= !empty($arSeo) && !empty($arSeo['META']['descriptionBottom']) ? $arSeo['META']['descriptionBottom'] : $arDescription['VALUE'] ?></div>
                    <?php $this->EndViewTarget() ?>
                <?php } ?>
                <?php if ($bIsAjax) exit() ?>
                <?php if ($arColumns['SHOW']) { ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                <?php } ?>
            <?= Html::endTag('div') ?>
        </div>
    </div>
    <script type="text/javascript">
        (function () {
            var root = $(<?= JavaScript::toObject('#'.$sTemplateId) ?>);
            var filter = $('[data-role="catalog.filter"]', root);
            var content = $('[data-role="catalog.content"]', root);

            filter.state = false;
            filter.button = $('[data-role="catalog.filter.button"]', root);
            filter.button.on('click', function () {
                if (filter.state) {
                    filter.hide();
                } else {
                    filter.show();
                }

                filter.state = !filter.state;
            });

            content.refresh = function (url) {
                var panel = $('[data-role="catalog.panel"]', content);

                if (url == null)
                    url = null;

                $.ajax({
                    'url': url,
                    'data': {
                        'catalog': {
                            'ajax': 'Y'
                        }
                    },
                    'cache': false,
                    'success': function (response) {
                        panel.detach();
                        filter.detach();

                        content.html(response);
                        content.find('[data-role="catalog.panel"]').replaceWith(panel);
                        content.find('[data-role="catalog.filter"]').replaceWith(filter);
                        universe.basket.update();
                    }
                });
            };

            <?php if ($arFilter['SHOW'] && $arFilter['AJAX']) { ?>
                (function () {
                    var updater = function (event, url) {
                        if (window.history.enabled || window.history.pushState != null) {
                            window.history.pushState(null, document.title, url);
                        } else {
                            window.location.href = url;
                        }

                        content.refresh(url);
                    };

                    if (smartFilter && smartFilter.on)
                        smartFilter.on('refresh', updater);

                    if (smartFilterMobile && smartFilterMobile.on)
                        smartFilterMobile.on('refresh', updater);
                })();
            <?php } ?>
        })();
    </script>
<?= Html::endTag('div') ?>