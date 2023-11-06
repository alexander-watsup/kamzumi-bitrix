<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var bool $bSuccess
 * @var bool $bError
 */

?>
<?= Html::tag('div', $arResult['FROM_RESULT'], [
    'class' => Html::cssClassFromArray([
        'widget-notification' => true,
        'widget-notification-success' => $bSuccess,
        'widget-notification-error' => $bError
    ], true)
]) ?>