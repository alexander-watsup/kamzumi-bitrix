<?

require_once($_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/lib/vendor/autoload.php');

\Bitrix\Main\Loader::registerAutoLoadClasses(null, [
    'Reklamafia\Exchange\IIKO' => '/local/php_interface/lib/Reklamafia/IIKO/IIKO.php'
]);

\Bitrix\Main\Loader::registerAutoLoadClasses(null, [
    'Reklamafia\Agents\IIKO' => '/local/php_interface/lib/Reklamafia/Agents/IIKO.php'
]);

\Bitrix\Main\Loader::registerAutoLoadClasses(null, [
    'Reklamafia\Messaging\Telegram' => '/local/php_interface/lib/Reklamafia/Messaging/Telegram.php'
]);
