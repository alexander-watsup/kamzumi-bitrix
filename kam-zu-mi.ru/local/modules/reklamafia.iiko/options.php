<?
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Reklamafia\Iiko\IikoApi;
use Reklamafia\Iiko\IikoOrganization;
use Reklamafia\Iiko\IikoProduct;
use Reklamafia\Iiko\CatalogUpdater;

Loc::loadMessages(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();
$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);
Loader::includeModule($module_id);


if($request->get('agent_refresh')=='full')
{
	$APPLICATION->RestartBuffer();	
	
	$catalogUpdater = new CatalogUpdater;
	$catalogUpdater->Update();
	
	echo \Bitrix\Main\Web\Json::encode(array(
		'update' => 'ok', 
	));		
	
	die();
}

?>
<script>
   grain_iiko_agent_refresh = function() {
      var url = '<?= $APPLICATION->GetCurPageParam() ?>';
      url += '&agent_refresh=full';
      BX.ajax.loadJSON(
         url,
         function(data) {
            console.log('Refresh SUCCESS: ', data);
         },
         function(err) {
            console.log('Refresh ERROR: ', err);
         }
      );
   }
</script>
<?




if($request->isPost() && check_bitrix_sessid()) {

	Option::set($module_id, 'switch_on', $request->getPost('switch_on'));
	Option::set($module_id, 'iiko_user', $request->getPost('iiko_user'));
   Option::set($module_id, 'iiko_password', $request->getPost('iiko_password'));
   Option::set($module_id, 'payment_type_code', $request->getPost('payment_type_code'));

	LocalRedirect($APPLICATION->GetCurPage()."?mid=".$module_id."&lang=".LANG);
}


$aTabs = array(
	array("DIV" => "main", "TAB" => Loc::getMessage("REKLAMAFIA_IIKO_ADMIN_OPTIONS_TAB_TITLE"), "ICON"=>"main_user_edit", "TITLE"=>Loc::getMessage("REKLAMAFIA_IIKO_ADMIN_OPTIONS_TAB_TITLE")),
	array("DIV" => "test_connection", "TAB" => Loc::getMessage("REKLAMAFIA_IIKO_ADMIN_OPTIONS_TAB_TESTCONNECTION_TITLE"), "ICON"=>"main_user_edit", "TITLE"=>Loc::getMessage("REKLAMAFIA_IIKO_ADMIN_OPTIONS_TAB_TESTCONNECTION_TITLE")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);





$tabControl->Begin();

?>
<form action="<? echo($APPLICATION->GetCurPage()); ?>?mid=<? echo($module_id); ?>&lang=<? echo(LANG); ?>" method="post">
   <?
	// =================   	
	$tabControl->BeginNextTab();
   
	$tabOptions = array(
		Loc::getMessage("REKLAMAFIA_IIKO_ADMIN_OPTIONS_TAB_COMMON_TITLE"),
		array(
			"switch_on",
			Loc::getMessage("REKLAMAFIA_IIKO_ADMIN_OPTIONS_TAB_COMMON_ON"),
			"Y",
			array("checkbox")
		),
		array(
			"iiko_user",
			Loc::getMessage("REKLAMAFIA_IIKO_ADMIN_OPTIONS_TAB_COMMON_USER"),
			"",
			array("text")
		),
		array(
			"iiko_password",
			Loc::getMessage("REKLAMAFIA_IIKO_ADMIN_OPTIONS_TAB_COMMON_PASSWORD"),
			"",
			array("text")
      ),
      array(
			"payment_type_code",
			Loc::getMessage("REKLAMAFIA_IIKO_ADMIN_OPTIONS_TAB_COMMON_PAYMENTCODE"),
			"",
			array("text")
		),
	 );
	  __AdmSettingsDrawList($module_id, $tabOptions);
   
   // ===================
   	
	$tabControl->BeginNextTab();   
   
	$iiko = new IikoApi();

   echo '<b>Base URL:</b> https://iiko.biz:9900/api/0/';
   echo '<br/><b>Token:</b> '.$iiko->getToken().'<br/>';

   echo '<br/><br/>';
   
     // ==   
	$orgList = $iiko->getOrganizationList();
	$organization = new IikoOrganization($orgList[0]);
	
	echo "<b>Organization:</b><br/>";
	echo 'Id.: '.$organization->id.'<br/>';
   echo 'Name.: '.$organization->name.'<br/>';
   echo '<br/><br/>';

    // ==
   //$paymentTypes = $iiko->getPaymentTypes();

   echo '<b>PaymentTypes:</b>&nbsp;<a href="https://iiko.biz:9900/api/0/rmsSettings/getPaymentTypes?access_token='.$iiko->getToken().'&organization='.$organization->id.'" target="_blank">show</a><br/>';
   echo '<b>Cities:</b>&nbsp;<a href="https://iiko.biz:9900/api/0/cities/cities?access_token='.$iiko->getToken().'&organization='.$organization->id.'" target="_blank">show</a><br/>';
   echo '<br/><br/>';

	// ==
	echo "<b>Groups:</b><br/>";
	$iiko->setOrganization($organization);
	$groups = $iiko->getGroups();
	echo '<pre style="word-break: break-all;white-space: normal;">'.json_encode($groups[0], JSON_UNESCAPED_UNICODE).'</pre>';
	echo '<div style="display:none">';	
	foreach($groups as $group) {
		echo '<br/>'.json_encode($group, JSON_UNESCAPED_UNICODE);
	}
	echo '</div>';

   echo '<br/><br/>';
	// == 
	echo "<b>Products:</b><br/>";      
	$menu = $iiko->getNomenclature();
	echo '<pre style="word-break: break-all;white-space: normal;">'.json_encode($menu[0], JSON_UNESCAPED_UNICODE).'</pre>';

	echo '<div style="display:none">';	
	foreach($menu as $product) {
		echo '<br/><b>'.$product->name.'</b> <br><span style="font-size:0.9em;">'.json_encode($product, JSON_UNESCAPED_UNICODE).'</span>';
	}
	echo '</div>';
	
	
	
		?>
   <div class="adm-info-message-wrap" align="center">
      <div class="adm-info-message">
         <span id="">Last sync: no info<br><a href="#" onclick="grain_iiko_agent_refresh(); return false;">Do sync</a></span>

      </div>
   </div>

   <?
   
  
   $tabControl->Buttons();
?>
   <input type="submit" name="apply" value="<? echo(Loc::GetMessage(" REKLAMAFIA_IIKO_ADMIN_OPTIONS_APPLY")); ?>" class="adm-btn-save" />
   <input type="submit" name="default" value="<? echo(Loc::GetMessage(" REKLAMAFIA_IIKO_ADMIN_OPTIONS_DEFAULT")); ?>" />
   <?
   echo(bitrix_sessid_post());
 ?>
</form>

<?
	$tabControl->End();