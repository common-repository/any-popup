<?php
function anypopupGetCsvFile() {
	global $wpdb;
	$content = '';
	$rows = array('id', 'firstName', 'lastName', 'email', 'subscriptionType');
	foreach ($rows as $value) {

		$content .= $value.',';
	}
	$content .= "\n";

	$sql = "SELECT id, firstName, lastName, email, subscriptionType FROM ". $wpdb->prefix ."anypopup_subscribers";
	$subscribers = $wpdb->get_results($sql, ARRAY_A);

	foreach($subscribers as $values) {
		foreach ($values as  $value) {
			$content .= $value.',';
		}
		$content .= "\n";
	}

	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false);
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=\"subscribersList.csv\";" );
	header("Content-Transfer-Encoding: binary");
	echo $content;
}

add_action('admin_post_csv_file', 'anypopupGetCsvFile');

function anypopupClone() {
	$id = (int)$_GET['id'];
	$allPosts = get_option('ANYPOPUP_ALL_POSTS');


	$obj = ANYPOPUP::findById($id);
	$title = $obj->getTitle();
	$title .= "(clone)";
	$obj->setId("");
	$obj->setTitle($title);

	$options = $obj->getOptions();
	$options = json_decode($options, true);
	$obj->save();

	$cloneId = $obj->getId();
	/* For save popupIn pages table */
	if($options['allPagesStatus'] && (!empty($options['showAllPages']) && $options['showAllPages'] != 'all')) {
		ANYPOPUP::addPopupForAllPages($cloneId, $options['allSelectedPages'], 'page');
	}
	if($options['allPostsStatus'] && (!empty($options['showAllPosts']) && $options['showAllPosts'] != "all")) {
		ANYPOPUP::addPopupForAllPages($cloneId, $options['allSelectedPosts'], 'page');
	}
	if($options['allCustomPostsStatus'] && (!empty($options['allSelectedCustomPosts']) && $options['showAllCustomPosts'] == "selected")) {
		ANYPOPUP::addPopupForAllPages($cloneId, $options['allSelectedCustomPosts'], 'page');
	}

	$parentAllPostData = ANYPOPUPFunctions::findInAllPostTypeData($id, $allPosts);
	if(!empty($parentAllPostData)) {
		$parentAllPostData['id'] = $cloneId;
		$allPosts = get_option("ANYPOPUP_ALL_POSTS");
		$allPosts[] = $parentAllPostData;
		update_option("ANYPOPUP_ALL_POSTS", $allPosts);
	}
	wp_redirect(ANYPOPUP_APP_POPUP_ADMIN_URL."admin.php?page=any_popup");
}

add_action('admin_post_popup_clone', 'anypopupClone');

function anypopupDataExport() {
	global $wpdb;
	
	$allData = array();
	$exportArray = array();
	$wpOptions = array();
	$optionsName = array(
		"ANYPOPUP_ALL_PAGES",
		"ANYPOPUP_ALL_POSTS",
		"ANYPOPUP_MULTIPLE_POPUP"
	);

	$mainTable = PopupInstaller::$mainTableName;

	$popupDataSql = "SELECT * FROM ".$wpdb->prefix.$mainTable;
	$getAllPopupData = $wpdb->get_results($popupDataSql, ARRAY_A);
	foreach ($getAllPopupData as $popupData) {
		$type = $popupData['type'];
		$id = $popupData['id'];
		if ($type == 'ageRestriction') {
			$type = "age_restriction";
		}
		else if($type == 'exitIntent') {
			$type = "exit_intent";
		}
		else if($type == 'contactForm') {
			$type = "contact_form";
		}
		else if($type == 'shortcode') {
			$type = "shortCode";
		}
		$table = "anypopup_".$type."_popup";
		$tableName = $wpdb->prefix.$table;

		$chieldPopupDataSql = "SELECT * FROM ".$tableName;
		$chieldPopupData = $wpdb->get_results($chieldPopupDataSql, ARRAY_A);

		$getRowsSql = "SHOW COLUMNS FROM ".$tableName;
		$chiledRows = $wpdb->get_results($getRowsSql, ARRAY_A);

		unset($chieldPopupData[0]['id']);
		//unset($chiledRows[0]);

		$exportArray[] = array(
			'mainPopupData' => $popupData,
			'childData' => $chieldPopupData,
			'chiledColums' => $chiledRows,
			'childTableName' => $table
		);
	}
	$customTables['anypopup_in_pages'] = $wpdb->prefix."anypopup_in_pages";
	$customTables['anypopup_subscribers'] = $wpdb->prefix."anypopup_subscribers";
	$customTablesColumsName = array();
	$customTablesData = array();

	foreach ($customTables as $key => $tableName) {

		$showColumnsSql = "SHOW COLUMNS FROM ".$tableName;
		$colums = $wpdb->get_results($showColumnsSql, ARRAY_A);
		$customTablesColumsName[$key] = $colums;

		$getCustomDataSql = "SELECT * FROM ".$tableName;
		$getCustomData = $wpdb->get_results($getCustomDataSql, ARRAY_A);
		$customTablesData[$key] = $getCustomData;
	}

	foreach ($optionsName as $optionName) {
		if(get_option($optionName)) {
			$wpOptions[$optionName] = get_option($optionName);
		}
	}

	$allData['exportArray'] = $exportArray;
	$allData['customData'] = $customTablesData;
	$allData['customTablesColumsName'] = $customTablesColumsName;
	$allData['wpOptions'] = $wpOptions;
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false);
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=\"anypopupexportdata.txt\";" );
	header("Content-Transfer-Encoding: binary");
	echo base64_encode(serialize($allData));
}

add_action('admin_post_popup_export', 'anypopupDataExport');
function anypopupSanitizeField($key, $isTextField = false) {

	if (isset($_POST[$key])) {
		if($isTextField) {
			return wp_kses_post($_POST[$key]);
		}
		return sanitize_text_field($_POST[$key]);
	}
	return "";
}

function anypopupSaveSettings() {

	global $wpdb;
	if(isset($_POST)) {
		check_admin_referer('anypopupAnyPopupSettings');
	}
	$st = $wpdb->prepare("SELECT options FROM ". $wpdb->prefix ."anypopup_settings WHERE id = %d",1);
	$options = $wpdb->get_row($st, ARRAY_A);
	
	$settingsOptions = array(
		'plugin_users_role' => anypopupSanitizeField('plugin_users_role', true),
		'tables-delete-status' => anypopupSanitizeField('tables-delete-status'),
		'anypopup-popup-time-zone' => anypopupSanitizeField('anypopup-popup-time-zone')
	);
	
	$settingsOptions = json_encode($settingsOptions);
	if(is_null($options) || empty($options)) {

		$sql = $wpdb->prepare( "INSERT INTO ". $wpdb->prefix ."anypopup_settings (id, options) VALUES (%d,%s)",'1',$settingsOptions);
		$res = $wpdb->query($sql);
	}
	else {
		$sql = $wpdb->prepare("UPDATE ". $wpdb->prefix ."anypopup_settings SET options=%s WHERE id=%d",$settingsOptions,1);
		$res = $wpdb->query($sql);
	}
	wp_redirect(ANYPOPUP_APP_POPUP_ADMIN_URL."admin.php?page=popup-settings&saved=1");
}

add_action('admin_post_save_settings', 'anypopupSaveSettings');

function anypopupSubsErrorList() {
	global $wpdb;
	$content = '';
	$sql = "SHOW COLUMNS FROM ". $wpdb->prefix ."anypopup_subscription_error_log";
	$rows = $wpdb->get_results($sql, ARRAY_A);
	foreach ($rows as $value) {
		$content .= $value['Field'].",";
	}
	$content .= "\n";

	$sql = "Select * from ". $wpdb->prefix ."anypopup_subscription_error_log";
	$subscribers = $wpdb->get_results($sql, ARRAY_A);

	foreach($subscribers as $values) {
		foreach ($values as  $value) {
			$content .= $value.',';
		}
		$content .= "\n";
	}

	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false);
	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=\"MailErrorLog.csv\";" );
	header("Content-Transfer-Encoding: binary");
	echo $content;
}

add_action('admin_post_subs_error_csv', 'anypopupSubsErrorList');

