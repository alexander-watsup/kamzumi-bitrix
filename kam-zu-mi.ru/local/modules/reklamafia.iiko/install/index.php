<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;
 
Loc::loadMessages(__FILE__); 
 
Class reklamafia_iiko extends CModule
{
 
    var $MODULE_ID;
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
	var $PARTNER_NAME;
	var $PARTNER_URI;
    var $errors;
 
    function __construct()
    {        
		if(file_exists(__DIR__."/version.php")) {
			$arModuleVersion = array();
			include_once(__DIR__."/version.php");

			$this->MODULE_ID            = str_replace("_", ".", get_class($this));
			$this->MODULE_VERSION       = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
			$this->MODULE_NAME          = Loc::getMessage("REKLAMAFIA_IIKO_MODULE_NAME");
			$this->MODULE_DESCRIPTION  = Loc::getMessage("REKLAMAFIA_IIKO_MODULE_DESCRIPTION");
			$this->PARTNER_NAME     = Loc::getMessage("REKLAMAFIA_IIKO_MODULE_PARTNER_NAME");
			$this->PARTNER_URI      = Loc::getMessage("REKLAMAFIA_IIKO_MODULE_PARTNER_URI");
		}

		return false;
    }
 
    function DoInstall()
    {        
		global $APPLICATION;
		
		if(CheckVersion(ModuleManager::getVersion("main"), "14.00.00")) {
			$this->InstallEvents();
			$this->InstallFiles();
			ModuleManager::RegisterModule($this->MODULE_ID);
		} else {
			$APPLICATION->ThrowException(Loc::getMessage("REKLAMAFIA_IIKO_INSTALL_ERROR_VERSION"));
		}
		
		/*
		$APPLICATION->IncludeAdminFile(
      Loc::getMessage("FALBAR_TOTOP_INSTALL_TITLE")." \"".Loc::getMessage("FALBAR_TOTOP_NAME")."\"",
      __DIR__."/step.php"
		);
		*/
        
        return true;
    }
 
    function DoUninstall()
    {        
        $this->UnInstallEvents();
        $this->UnInstallFiles();
        ModuleManager::UnRegisterModule($this->MODULE_ID);
		
		/*
			$APPLICATION->IncludeAdminFile(
			Loc::getMessage("FALBAR_TOTOP_UNINSTALL_TITLE")." \"".Loc::getMessage("FALBAR_TOTOP_NAME")."\"",
			__DIR__."/unstep.php"
		);
		*/
		
        return true;
    }
 
	public function InstallDB(){

		return true;
	}

	public function UnInstallDB(){

		return true;
	}
     
    public function InstallEvents()
    {
		$res = CAgent::AddAgent( "\\reklamafia\\iiko\\CatalogUpdater::update();", "reklamafia.iiko", "N", 10 * 24 * 3600, "", "Y");
		
		//AddMessage2Log($res);
				
		/*
		EventManager::getInstance()->registerEventHandler(
			'support',
			'OnBeforeTicketAdd',
			$this->MODULE_ID,
			'Redsign\\B2BPortal\\ClaimsEventHandlers',
			'onBeforeTicketAdd'
		);
		*/
		
		EventManager::getInstance()->registerEventHandler(
			'sale',
			'OnSaleOrderSaved',
			$this->MODULE_ID,
			'reklamafia\\iiko\\OrderUpdater',
			'OnOrderAddHandler'
		);

		EventManager::getInstance()->registerEventHandler(
			'sale',
			'OnSaleComponentOrderResultPrepared',
			$this->MODULE_ID,
			'reklamafia\\iiko\\Order',
			'OnSaleComponentOrderResultPreparedHandler'
		);

		EventManager::getInstance()->registerEventHandler(
			'sale',
			'onSaleDeliveryServiceCalculate',
			$this->MODULE_ID,
			'reklamafia\\iiko\\Order',
			'onSaleDeliveryServiceCalculateHandler'
		);

		



		//RegisterModuleDependences("main", "OnOrderAdd", "reklamafia.iiko", "reklamafia\iiko\OrderUpdater", "OnOrderAddHandler");
		//RegisterModuleDependences("main", "OnOrderAdd", "reklamafia.iiko", "OrderUpdater", "OnOrderAddHandler");
		
		
        return true;
    }
 
    public function UnInstallEvents()
    {
		CAgent::RemoveModuleAgents("reklamafia.iiko");
		
		/*EventManager::getInstance()->unRegisterEventHandler(
			'support',
			'OnBeforeTicketAdd',
			$this->MODULE_ID,
			'Redsign\\B2BPortal\\ClaimsEventHandlers',
			'onBeforeTicketAdd'
		);
		
		*/
		EventManager::getInstance()->unRegisterEventHandler(
			'sale',
			'OnSaleOrderSaved',
			$this->MODULE_ID,
			'reklamafia\\iiko\\OrderUpdater',
			'OnOrderAddHandler'
		);

		EventManager::getInstance()->unRegisterEventHandler(
			'sale',
			'OnSaleComponentOrderResultPrepared',
			$this->MODULE_ID,
			'reklamafia\\iiko\\Order',
			'OnSaleComponentOrderResultPreparedHandler'
		);

		EventManager::getInstance()->unRegisterEventHandler(
			'sale',
			'onSaleDeliveryServiceCalculate',
			$this->MODULE_ID,
			'reklamafia\\iiko\\Order',
			'onSaleDeliveryServiceCalculateHandler'
		);
		
		/*
		UnRegisterModuleDependences("main", "OnOrderAdd", "reklamafia.iiko", "reklamafia\iiko\OrderUpdater", "OnOrderAddHandler");
		UnRegisterModuleDependences("main", "OnOrderAdd", "reklamafia.iiko", "OrderUpdater", "OnOrderAddHandler");
		*/
		
		/*
		 EventManager::getInstance()->unRegisterEventHandler(
     "main",
       "OnOrderAdd",
       $this->MODULE_ID,
        "reklamafia\iiko\OrderUpdater",
        "OnOrderAddHandler"
 );*/
		
        return true;
    }
 
    public function InstallFiles()
    {
        return true;
    }
 
    public function UnInstallFiles()
    {
        return true;
    }
}