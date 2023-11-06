<?

use Bitrix\Main\ModuleManager;

$aMenuLinks = Array(
	Array(
		"Личные данные", 
		"/personal/profile/user/",
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Мои заказы", 
		"/personal/profile/orders/",
		Array(), 
		Array(), 
		"" 
	),
	Array(
		"Сменить пароль", 
		"/personal/profile/user/pass.php",
		Array(), 
		Array(), 
		"" 
	)
);

if (ModuleManager::isModuleInstalled('subscribes')) {
    $aMenuLinks[] = Array(
        "Рассылки",
        "/personal/profile/mailing/",
        Array(),
        Array(),
        ""
    );
}

?>