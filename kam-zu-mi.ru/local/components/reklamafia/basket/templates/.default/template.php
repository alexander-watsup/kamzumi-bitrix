<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->addExternalJS($templateFolder . '/js/app.bundle.js');

/*
$arResult['RM'] = [
    'basketFormatted'=>$basketFormatted,
    'deliveryCities'=>$deliveryCities,
    'pickupPoints'=>$pickupPoints
];
*/

?>

<div id="basket-application"></div>

<script>
    BX.ready(
        function() {
            new window.Basket({
                basket: <?= json_encode($arResult['RM']['basketFormatted'], JSON_UNESCAPED_UNICODE) ?>,
                deliveryCities: <?= json_encode($arResult['RM']['deliveryCities'], JSON_UNESCAPED_UNICODE) ?>,
                pickupPoints: <?= json_encode($arResult['RM']['pickupPoints'], JSON_UNESCAPED_UNICODE) ?>
            });
        }
    );
</script>