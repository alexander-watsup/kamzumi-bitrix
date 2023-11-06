<?php
//require 'vendor/autoload.php';

CModule::AddAutoloadClasses(
    'reklamafia.iiko',
    array(
        'Reklamafia\\Iiko\\CatalogUpdater' => 'lib/catalogupdater.php',
		//'reklamafia\\iiko\\OrderUpdater' => 'lib/OrderUpdater.php',
		//'Reklamafia\\Iiko\\IikoApi' => 'lib/iikoapi.php'
    )
);
