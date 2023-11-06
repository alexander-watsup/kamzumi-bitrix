<?

use Bitrix\Main\Loader;

Loader::includeModule("highloadblock");

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;
use Bitrix\Main\EventManager;
use Bitrix\Main\Diag\Debug;


function ppr($a)
{
	echo "<pre>";
	print_r($a);
	echo "</pre>";
}
function plural_form($number, $after)
{
	$cases = array(2, 0, 1, 1, 1, 2);
	echo $number . ' ' . $after[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
}
function plural_form_before($number, $before, $after)
{
	$cases = array(2, 0, 1, 1, 1, 2);
	echo $before[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]] . ' ' . $number . ' ' . $after[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
}

