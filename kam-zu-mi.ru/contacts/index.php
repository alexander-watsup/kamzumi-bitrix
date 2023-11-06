<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "Стоматологический магазин по оптово-розничной продаже стоматологического и зуботехнического оборудования, стоматологических  и зуботехнических материалов");
$APPLICATION->SetTitle("Эквилибриум Мед +7 (4012) 50-77-44");
$APPLICATION->SetPageProperty("title", "Контакты");
?>
<? $APPLICATION->IncludeComponent(
    "bitrix:breadcrumb",
    "",
    array(
        "PATH" => "",
        "SITE_ID" => "s1",
        "START_FROM" => "0"
    )
); ?>
<div class="contact">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section-header">
                    <h1>Контакты</h1>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
                <div class="phone">
                    <a href="tel:+74012507744" class="contact-link">+7 (4012) 50-77-44</a>
                </div>
                <div class="email">
                    <a href="mailto:info@equilibrium-med.ru" class="contact-link">info@equilibrium-med.ru</a>
                </div>
                <div class="address">
                    Калининград, ул. Нарвская, 49Е
                </div>

                <div class="contact-wrapper">
                    <div class="contact-box">
                        <div class="contact-item">
                            <div class="contact-item-text">
                                Телефон/Факс: <a href="tel:+74012507744" class="contact-link">+7 (4012) 50-77-44
                                </a>
                            </div>
                            <div class="contact-item-text">
                                E-mail: <a href="mailto:info@equilibrium-med.ru" class="contact-link">info@equilibrium-med.ru</a>
                            </div>
                            <div class="contact-item-text">
                                Адрес: <span class="contact-item-address">г. Калининград, Нарвская улица, 49Е</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 details">
                    ООО «Эквилибриум»<br />
                    ИНН 3906399372 КПП 390601001 ОГРН 1213900001864<br />
                    Юридический адрес: 236034 Россия, Калининградская область, г. Калининград, ул. Нарвская, д 49Е, литер Г, Г2, офис 12<br />
                    Генеральный директор: Толюпа Валерия Сергеевна
                </div>
            </div>


            <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 contact-order">
                <div class="map-wrapper">
                    <iframe src="https://yandex.ru/map-widget/v1/?z=12&ol=biz&oid=150368090321" width="100%" height="400" frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>