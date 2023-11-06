<?php

use intec\core\bitrix\components\IBlockElements;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
use intec\core\net\Url;

class IntecMainAboutComponent extends IBlockElements
{
    /**
     * @inheritdoc
     */
    public function onPrepareComponentParams($arParams) {
        if (!Type::isArray($arParams))
            $arParams = [];

        $arParams = ArrayHelper::merge([
            'IBLOCK_TYPE' => null,
            'IBLOCK_ID' => null,
            'SECTIONS_MODE' => 'id',
            'SECTION' => null,
            'ELEMENTS_MODE' => 'id',
            'ELEMENT' => null,
            'PICTURE_SOURCES' => [],
            'PROPERTY_LINK' => null,
            'PROPERTY_TITLE' => null,
            'PROPERTY_VIDEO' => null,
            'SORT_BY' => 'SORT',
            'ORDER_BY' => 'ASC',
            'FILTER' => []
        ], $arParams);

        if (!Type::isArray($arParams['PICTURE_SOURCES']))
            $arParams['PICTURE_SOURCES'] = [];

        if (!Type::isArray($arParams['FILTER']))
            $arParams['FILTER'] = [];

        return $arParams;
    }

    /**
     * @inheritdoc
     */
    public function executeComponent() {
        global $USER;

        if ($this->startResultCache(false, $USER->GetGroups())) {
            $arParams = $this->arParams;
            $arResult = [
                'VISUAL' => [],
                'VIDEO' => [],
                'LINK' => [],
                'PICTURE' => [],
                'ITEM' => []
            ];

            $arSort = [];

            if (!empty($arParams['SORT_BY']) && !empty($arParams['ORDER_BY'])) {
                $arSort = [
                    $arParams['SORT_BY'] => $arParams['ORDER_BY']
                ];
            }

            $arFilter = ArrayHelper::merge([
                'IBLOCK_LID' => $this->getSiteId(),
                'ACTIVE' => 'Y',
                'ACTIVE_DATE' => 'Y',
                'CHECK_PERMISSIONS' => 'Y',
                'MIN_PERMISSION' => 'R'
            ], $arParams['FILTER']);

            $this->setIBlockType($arParams['IBLOCK_TYPE']);
            $this->setIBlockId($arParams['IBLOCK_ID']);

            if ($arParams['SECTIONS_MODE'] === 'code') {
                $this->setSectionsCode($arParams['SECTION']);
            } else {
                $this->setSectionsId($arParams['SECTION']);
            }

            if ($arParams['ELEMENTS_MODE'] === 'code') {
                $this->setElementsCode($arParams['ELEMENT']);
            } else {
                $this->setElementsId($arParams['ELEMENT']);
            }

            $arItem = $this->getElements($arSort, $arFilter, 1);

            unset($arFilter, $arSort);

            if (!empty($arItem)) {
                $arItem = ArrayHelper::getFirstValue($arItem);

                $arResult['ITEM'] = $arItem;

                $arPictureSources = [
                    'PREVIEW' => ArrayHelper::isIn('preview', $arParams['PICTURE_SOURCES']),
                    'DETAIL' => ArrayHelper::isIn('detail', $arParams['PICTURE_SOURCES'])
                ];

                if (empty($arParams['PICTURE_SOURCES'])) {
                    $arPictureSources = [
                        'PREVIEW' => true,
                        'DETAIL' => true
                    ];
                }

                $arService = [];

                if (!empty($arParams['PROPERTY_LINK'])) {
                    $arProperty = ArrayHelper::getValue($arItem, [
                        'PROPERTIES',
                        $arParams['PROPERTY_LINK']
                    ]);

                    if (!empty($arProperty['VALUE'])) {
                        if (Type::isArray($arProperty['VALUE']))
                            $arProperty['VALUE'] = ArrayHelper::getFirstValue($arProperty['VALUE']);

                        if (!empty($arProperty['VALUE'])) {
                            $arResult['LINK'] = $arProperty['VALUE'];
                        }
                    }

                    unset($arProperty);
                }

                if (!empty($arParams['PROPERTY_TITLE'])) {
                    $arProperty = ArrayHelper::getValue($arItem, [
                        'PROPERTIES',
                        $arParams['PROPERTY_TITLE']
                    ]);

                    if (!empty($arProperty['VALUE'])) {
                        if (Type::isArray($arProperty['VALUE']))
                            $arProperty['VALUE'] = ArrayHelper::getFirstValue($arProperty['VALUE']);

                        if (!empty($arProperty['VALUE'])) {
                            $arResult['TITLE'] = $arProperty['VALUE'];
                        }
                    }

                    unset($arProperty);
                }

                if (!empty($arParams['PROPERTY_VIDEO'])) {
                    $arProperty = ArrayHelper::getValue($arItem, [
                        'PROPERTIES',
                        $arParams['PROPERTY_VIDEO']
                    ]);

                    if (!empty($arProperty['VALUE'])) {
                        if (Type::isArray($arProperty['VALUE']))
                            $arProperty['VALUE'] = ArrayHelper::getFirstValue($arProperty['VALUE']);

                        if (!empty($arProperty['VALUE'])) {
                            $arResult['VIDEO'] = $arProperty['VALUE'];
                        }
                    }

                    unset($arProperty);
                }

                if ($arPictureSources['DETAIL'] && !empty($arItem['DETAIL_PICTURE'])) {
                    $arResult['PICTURE'] = $arItem['DETAIL_PICTURE'];
                    $arResult['PICTURE']['SOURCE'] = 'detail';
                } else if ($arPictureSources['PREVIEW'] && !empty($arItem['PREVIEW_PICTURE'])) {
                    $arResult['PICTURE'] = $arItem['PREVIEW_PICTURE'];
                    $arResult['PICTURE']['SOURCE'] = 'preview';
                } else {
                    $arResult['PICTURE'] = [
                        'SRC' => null,
                        'SOURCE' => 'none'
                    ];
                }

                unset($arPictureSources);
            }

            $this->arResult = $arResult;

            unset($arResult, $arParams, $arItem, $arService);

            $this->includeComponentTemplate();
        }

        return null;
    }
}