<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Доставка и оплата");

?><div class="intec-content">
	<div class="intec-content-wrapper">
		<div class="content-page-delivery-payment">
			<div class="delivery-info">
				<h2>Зоны и стоимость доставки</h2>
				<div>
					 <iframe src="https://www.google.com/maps/d/embed?mid=1xbmaykjT5v-89s45uI2bIbFT28H4KEHV" width="100%" height="480"></iframe>
				</div>
				<div>
					<p>
						 Мы принимаем заказы на доставку с 11.00 до 22.45. Зону покрытия нашей доставки вы можете посмотреть на карте. В ближайшее время зона доставки будет только расширяться!
					</p>
					<p>
 <b>Зона 1 (зелёная).</b> При заказе на сумму от 700 рублей - доставка бесплатная. Менее 700 руб. - доставка 100 рублей.<br>
 <b>Зона 2 (жёлтая).</b> При заказе на сумму от 1200 рублей - доставка бесплатная. Менее 1200 руб. - доставка 150 рублей.<br>
 <b>Зона 3 (оранжевая).</b> Минимальная сумма заказа 1200 рублей, доставка платная 100 рублей.<br>
 <b>Зона 4 (фиолетовая).</b> Минимальная сумма заказа 1200 рублей, доставка платная 150 рублей.
					</p>
					<p>
						 Точную стоимость и время доставки уточняйте у администратора по телефону +7 (4012) 31-31-02.
					</p>
				</div>
			</div>
			<div class="payments-info">
				<h2>Способы оплаты</h2>
				<div class="payments-items intec-grid intec-grid-wrap intec-grid-i-15">
					<div class="payments-item intec-grid-item-5 intec-grid-item-768-3 intec-grid-item-425-2">
						<div class="payments-item-icon">
 <img src="/images/payments-card.jpg">
						</div>
						<div class="payments-item-name">
							 Банковской картой курьеру
						</div>
						<div class="payments-item-description">
							 Вы можете оплатить заказ курьеру при получении банковской картой Visa или Mastercard.
						</div>
					</div>
					<div class="payments-item intec-grid-item-5 intec-grid-item-768-3 intec-grid-item-425-2">
						<div class="payments-item-icon">
 <img src="/images/payments-cash.jpg">
						</div>
						<div class="payments-item-name">
							 Наличными курьеру
						</div>
						<div class="payments-item-description">
							 Оплата производится курьеру при доставке заказа. При оформлении заказа укажите сумму, с которой Вам необходима сдача.
						</div>
					</div>
					<div class="payments-item intec-grid-item-5 intec-grid-item-768-3 intec-grid-item-425-2">
						<div class="payments-item-icon">
 <img src="/images/payments-card.jpg">
						</div>
						<div class="payments-item-name">
							 Банковской картой на сайте
						</div>
						<div class="payments-item-description">
							 Вы можете оплатить заказ на сайте банковской картой МИР, VISA, Mastercard или JCB.
						</div>
					</div>
				</div>
			</div>
		</div>
		<div>
			<h2>Особенности оплаты на сайте</h2>
			<p>
				 Для выбора оплаты товара с помощью банковской карты на соответствующей странице необходимо нажать кнопку Оплата заказа банковской картой. Оплата происходит через ПАО СБЕРБАНК с использованием банковских карт следующих платёжных систем:
			</p>
			<div>
 <img src="/images/oplata.png">
			</div>
			<p>
				 Для оплаты (ввода реквизитов Вашей карты) Вы будете перенаправлены на платёжный шлюз ПАО СБЕРБАНК. Соединение с платёжным шлюзом и передача информации осуществляется в защищённом режиме с использованием протокола шифрования SSL. В случае если Ваш банк поддерживает технологию безопасного проведения интернет-платежей Verified By Visa, MasterCard SecureCode, MIR Accept, J-Secure, для проведения платежа также может потребоваться ввод специального пароля.
			</p>
			<p>
				 Настоящий сайт поддерживает 256-битное шифрование. Конфиденциальность сообщаемой персональной информации обеспечивается ПАО СБЕРБАНК. Введённая информация не будет предоставлена третьим лицам за исключением случаев, предусмотренных законодательством РФ. Проведение платежей по банковским картам осуществляется в строгом соответствии с требованиями платёжных систем МИР, Visa Int., MasterCard Europe Sprl, JCB.
			</p>
		</div>
	</div>
</div>
<style>
    .content-page-delivery-payment h2 {
        margin-top: 0;
        margin-bottom: 32px;
    }

    .content-page-delivery-payment .delivery-advantages {
        margin-bottom: 48px;
    }

    .content-page-delivery-payment .delivery-advantage:not(:last-child) {
        margin-bottom: 16px;
    }

    .content-page-delivery-payment .delivery-icon {
        width: 45px;
        font-size: 46px;
        margin-right: 24px;
    }

    .content-page-delivery-payment .delivery-header {
        font-size: 18px;
        font-weight: 500;
    }

    .content-page-delivery-payment .delivery-description {
        margin-top: 4px;
        font-size: 16px;
    }

    .content-page-delivery-payment .delivery-zone {
        margin-bottom: 48px;
    }

    .content-page-delivery-payment .payments-item-name {
        margin-top: 16px;
        margin-bottom: 12px;
        font-size: 20px;
        font-weight: 500;
    }

    .content-page-delivery-payment .payments-item-description {
        font-size: 16px;
    }
</style><?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php") ?>