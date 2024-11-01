<?php

if( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit();

require_once(dirname(__FILE__)."/config.php");
require_once(ANYPOPUP_APP_POPUP_CLASSES .'/ANYPOPUPInstaller.php'); //cretae tables
require_once(ANYPOPUP_APP_POPUP_FILES .'/anypopup_functions.php');


if (ANYPOPUP_PKG > ANYPOPUP_PKG_FREE) {
	require_once( ANYPOPUP_APP_POPUP_CLASSES .'/PopupProInstaller.php'); //uninstall tables
}

$deleteStatus = ANYPOPUPFunctions::popupTablesDeleteSatus();

if($deleteStatus) {
	AnypopupInstaller::uninstall();
	if (ANYPOPUP_PKG > ANYPOPUP_PKG_FREE) {
		PopupProInstaller::uninstall();
	}
}