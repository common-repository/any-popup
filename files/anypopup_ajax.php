<?php
//sanitizing and validating input before any action
function anypopupSanitizeAjaxField($optionValue,  $isTextField = false) {
	/*TODO: Extend function for other sanitization and validation actions*/
	if(!$isTextField) {
		return sanitize_text_field($optionValue);
	}
}

function anypopupDelete()
{
	check_ajax_referer('anypopupAnyPopupDeleteNonce', 'ajaxNonce');
	$id = (int)@$_POST['popup_id'];

	if($id == 0 || !$id) {
		return;
	}

	require_once(ANYPOPUP_APP_POPUP_CLASSES.'/ANYPOPUP.php');
	ANYPOPUP::delete($id);
	ANYPOPUP::removePopupFromPages($id);

	$args = array('popupId'=> $id);
	do_action('anypopupDelete', $args);
}

add_action('wp_ajax_delete_popup', 'anypopupDelete');

function anypopuppbNewYear()
{
	check_ajax_referer('anypopupAnyPopupNewYear', 'nonce');

	echo update_option('anypopupAnyPopupNewYear', 1);
	wp_die();
}

add_action('wp_ajax_anypopuppbNewYear', 'anypopuppbNewYear');

function anypopupFrontend()
{
	global $wpdb;
	check_ajax_referer('anypopupAnyPopupSubsNonce', 'subsSecurity');
	parse_str($_POST['subsribers'], $subsribers);
	if(!empty($subsribers['anypopup-subs-hidden-checker'])) {
		return 'Bot';
	}
	$email = sanitize_email($subsribers['subs-email-name']);
	$firstName = anypopupSanitizeAjaxField($subsribers['subs-first-name']);
	$lastName = anypopupSanitizeAjaxField($subsribers['subs-last-name']);
	$title = sanitize_title($subsribers['subs-popup-title']);

	$query = $wpdb->prepare("SELECT id FROM ". $wpdb->prefix ."anypopup_subscribers WHERE email = %s AND subscriptionType = %s", $email, $title);
	$list = $wpdb->get_row($query, ARRAY_A);
	if(!isset($list['id'])) {
		$sql = $wpdb->prepare("INSERT INTO ".$wpdb->prefix."anypopup_subscribers (firstName, lastName, email, subscriptionType, status) VALUES (%s, %s, %s, %s,%d)", $firstName, $lastName, $email, $title, 0);
		$res = $wpdb->query($sql);
	}
	die();
}

add_action('wp_ajax_nopriv_subs_send_mail', 'anypopupFrontend');
add_action('wp_ajax_subs_send_mail', 'anypopupFrontend');

function anypopuppbAddToCounter()
{
	check_ajax_referer('anypopupPbNonce', 'ajaxNonce');
	$popupParams = $_POST['params'];
	$popupId = (int)$popupParams['popupId'];
	$popupsCounterData = get_option('AnypopuppbCounter');

	if($popupsCounterData === false) {
		$popupsCounterData = array();
	}

	if(empty($popupsCounterData[$popupId])) {
		$popupsCounterData[$popupId] = 0;
	}
	$popupsCounterData[$popupId] += 1;

	update_option('AnypopuppbCounter', $popupsCounterData);
	die();
}

add_action('wp_ajax_nopriv_send_to_open_counter', 'anypopuppbAddToCounter');
add_action('wp_ajax_send_to_open_counter', 'anypopuppbAddToCounter');

function anypopupContactForm()
{
	global $wpdb;
	parse_str($_POST['contactParams'], $params);
	//CSRF CHECK
	check_ajax_referer('anypopupAnyPopupContactNonce', 'contactSecurity');
	if(!empty($params['anypopup-hidden-checker'])) {
		return 'Bot';
	}
	$adminMail = sanitize_email($_POST['receiveMail']);
	$popupTitle = sanitize_title($_POST['popupTitle']);
	$name = anypopupSanitizeAjaxField($params['contact-name']);
	$subject = anypopupSanitizeAjaxField($params['contact-subject']);
	$userMessage = anypopupSanitizeAjaxField($params['content-message']);
	$mail = sanitize_email($params['contact-email']);


	$message = '';
	if(isset($name)) {
		if($name == '') {
			$name = 'Not provided';
		}
		$message .= '<b>Name</b>: '.$name."<br>";
	}

	$message .= '<b>E-mail</b>: '.$mail."<br>";
	if(isset($subject)) {
		if($subject == '') {
			$subject = 'Not provided';
		}
		$message .= '<b>Subject</b>: '.$subject."<br>";
	}

	$message .= '<b>Message</b>: '.$userMessage."<br>";
	$headers  = 'MIME-Version: 1.0'."\r\n";
	$headers  = 'From: '.$adminMail.''."\r\n";
	$headers .= 'Content-type: text/html; charset=UTF-8'."\r\n"; //set UTF-8

	$sendStatus = wp_mail($adminMail, $popupTitle.'- Popup contact form', $message, $headers); //return true or false
	echo $sendStatus;
	die();
}

add_action('wp_ajax_nopriv_contact_send_mail', 'anypopupContactForm');
add_action('wp_ajax_contact_send_mail', 'anypopupContactForm');

function anypopupImportPopups()
{
	global $wpdb;
	check_ajax_referer('anypopupAnyPopupImportNonce', 'ajaxNonce');
	$url = anypopupSanitizeAjaxField($_POST['attachmentUrl']);

	$contents = unserialize(base64_decode(file_get_contents($url)));

	/* For tables wich they are not popup tables child ex. subscribers */
	foreach ($contents['customData'] as $tableName => $datas) {
		$columns = '';

		$columsArray = array();
		foreach ($contents['customTablesColumsName'][$tableName] as $key => $value) {
			$columsArray[$key] = $value['Field'];
		}
		$columns .= implode(array_values($columsArray), ', ');
		foreach ($datas as $key => $data) {
			$values = "'".implode(array_values($data), "','")."'";
			$customInsertSql = $wpdb->prepare("INSERT INTO ".$wpdb->prefix.$tableName."($columns) VALUES ($values)");
			$wpdb->query($customInsertSql);
		}
	}

	foreach ($contents['wpOptions'] as $key => $option) {
		update_option($key,$option);
	}

	foreach ($contents['exportArray'] as $content) {
		//Main popup table data
		$popupData = $content['mainPopupData'];
		$popupId = $popupData['id'];
		$popupType = $popupData['type'];
		$popupTitle = $popupData['title'];
		$popupOptions = $popupData['options'];

		//Insert popup
		$sql = $wpdb->prepare("INSERT INTO ".$wpdb->prefix.PopupInstaller::$mainTableName."(id, type, title, options) VALUES (%d, %s, %s, %s)", $popupId, $popupType, $popupTitle, $popupOptions);
		$res = $wpdb->query($sql);
		//Get last insert popup id
		$lastInsertId = $wpdb->insert_id;

		//Child popup data
		$childPopupTableName = $content['childTableName']; // change it Tbale to Table
		$childPopupData = $content['childData']; //change it child

		//Foreach throw child popups
		foreach ($childPopupData as $childPopup) {
			//Child popup table columns
			$values = '';
			$columns = implode(array_keys($childPopup), ', ');
			foreach (array_values($childPopup) as $value) {
				$values .= "'".addslashes($value)."', ";
			}
			$values = rtrim($values, ', ');

			$queryValues = str_repeat("%s, ", count(array_keys($childPopup)));
			$queryValues = "%d, ".rtrim($queryValues, ', ');

			$queryStr = 'INSERT INTO '.$wpdb->prefix.$childPopupTableName.'(id, '.$columns.') VALUES ('.$lastInsertId.','. $values.')';

			$resa = (int)$wpdb->query($queryStr);

			echo 'ChildRes: '.$resa;
		}
		echo 'MainRes: '.$res;
	}
}

add_action('wp_ajax_import_popups', 'anypopupImportPopups');

function anypopupCloseReviewPanel()
{
	check_ajax_referer('anypopupAnyPopupReview', 'ajaxNonce');
	update_option('ANYPOPUP_COLOSE_REVIEW_BLOCK', true);
	die();
}
add_action('wp_ajax_close_review_panel', 'anypopupCloseReviewPanel');

function anypopupDontShowReviewPopup()
{
	check_ajax_referer('anypopupAnyPopupReview', 'ajaxNonce');
	update_option('ANYPOPUPCloseReviewPopup', true);
	die();
}
add_action('wp_ajax_dont_show_review_popup', 'anypopupDontShowReviewPopup');

function anypopupChangeReviewPopupPeriod()
{
	check_ajax_referer('anypopupAnyPopupReview', 'ajaxNonce');
	$messageType = sanitize_text_field($_POST['messageType']);

	if($messageType == 'count') {
		$maxPopupCount = get_option('ANYPOPUPMaxOpenCount');
		if(!$maxPopupCount) {
			$maxPopupCount = ANYPOPUP_SHOW_COUNT;
		}
		$maxPopupData = ANYPOPUPFunctions::getMaxOpenPopupId();
		if(!empty($maxPopupData['maxCount'])) {
			$maxPopupCount = $maxPopupData['maxCount'];
		}

		$maxPopupCount += ANYPOPUP_SHOW_COUNT;
		update_option('ANYPOPUPMaxOpenCount', $maxPopupCount);
		die();
	}

	$popupTimeZone = @AnypopupGetData::getPopupTimeZone();
	$timeDate = new DateTime('now', new DateTimeZone($popupTimeZone));
	$timeDate->modify('+'.ANYPOPUP_REVIEW_POPUP_PERIOD.' day');

	$timeNow = strtotime($timeDate->format('Y-m-d H:i:s'));
	update_option('ANYPOPUPOpenNextTime', $timeNow);
	$usageDays = get_option('ANYPOPUPUsageDays');
	$usageDays += ANYPOPUP_REVIEW_POPUP_PERIOD;
	update_option('ANYPOPUPUsageDays', $usageDays);
	die();
}

add_action('wp_ajax_change_review_popup_show_period', 'anypopupChangeReviewPopupPeriod');

function AnypopupaddToSubscribers() {

	global $wpdb;
	check_ajax_referer('anypopupAnyPopupAddSubsToListNonce', 'ajaxNonce');
	$firstName = anypopupSanitizeAjaxField($_POST['firstName']);
	$lastName = anypopupSanitizeAjaxField($_POST['lastName']);
	$email = sanitize_email($_POST['email']);
	$subsType = array_map( 'sanitize_text_field', $_POST['subsType']);

	foreach ($subsType as $subType) {
		$selectSql = $wpdb->prepare('SELECT id FROM '.$wpdb->prefix.'anypopup_subscribers WHERE email = %s AND subscriptionType = %s', $email, $subType);
		$res = $wpdb->get_row($selectSql, ARRAY_A);
		if(empty($res)) {
			$sql = $wpdb->prepare('INSERT INTO '.$wpdb->prefix.'anypopup_subscribers (firstName, lastName, email, subscriptionType) VALUES (%s, %s, %s, %s) ', $firstName, $lastName, $email, $subType);
			$wpdb->query($sql);
		}
		else {
			$sql = $wpdb->prepare('UPDATE '.$wpdb->prefix.'anypopup_subscribers SET firstName = %s, lastName = %s, email = %s, subscriptionType = %s WHERE id = %s', $firstName, $lastName, $email, $subType, $res['id']);
			$wpdb->query($sql);
		}
	}

	die();
}
add_action('wp_ajax_add_to_subsribers', 'AnypopupaddToSubscribers');

function anypopupDeleteSubscribers() {

	global $wpdb;
	check_ajax_referer('anypopupAnyPopupAddSubsNonce', 'ajaxNonce');
	$subsribersId = array_map( 'sanitize_text_field', $_POST['subsribersId']);
	foreach ($subsribersId as $subsriberId) {
		$prepareSql = $wpdb->prepare("DELETE FROM ". $wpdb->prefix ."anypopup_subscribers WHERE id = %d",$subsriberId);
		$wpdb->query($prepareSql);
	}
	die();
}

add_action('wp_ajax_subsribers_delete', 'anypopupDeleteSubscribers');

function anypopupIsHaveErrorLog() {

	global $wpdb;
	check_ajax_referer('anypopupAnyPopupSubsLogNonce', 'ajaxNonce');
	$countRows = '';
	$popupType = anypopupSanitizeAjaxField($_POST['subsType']);

	$getErrorCounteSql = $wpdb->prepare("SELECT count(*) FROM ". $wpdb->prefix ."anypopup_subscription_error_log WHERE popupType=%s",$popupType);
	$countRows = $wpdb->get_var($getErrorCounteSql);
	echo $countRows;
	die();
}

add_action('wp_ajax_subs_error_log_count', 'anypopupIsHaveErrorLog');

function anypopupChangePopupStatus() {
	check_ajax_referer('anypopupAnyPopupDeactivateNonce', 'ajaxNonce');
	$popupId = (int)$_POST['popupId'];
	$obj = ANYPOPUP::findById($popupId);
	$options = json_decode($obj->getOptions(), true);
	$options['isActiveStatus'] = anypopupSanitizeAjaxField($_POST['popupStatus']);
	$obj->setOptions(json_encode($options));
	$obj->save();
}
add_action('wp_ajax_change_popup_status', 'anypopupChangePopupStatus');

function anypopupGetPagesList(){
	check_ajax_referer('anypopupAnyPopupPagesListNonce', 'ajaxNonce');
	
	$pages = get_pages(); 
	
	echo json_encode($pages);
	die();
}

add_action('wp_ajax_get_pages_list', 'anypopupGetPagesList');

function AnyPopupsavePopupPreviewData() {
	check_ajax_referer('any-popup-ajax', 'ajaxNonce');

	$formSerializedData = $_POST['popupDta'];
	if(get_option('popupPreviewId')) {
		$id = (int)get_option('popupPreviewId');

		if($id == 0 || !$id) {
			return;
		}

		require_once(ANYPOPUP_APP_POPUP_CLASSES.'/ANYPOPUP.php');
		$delete = ANYPOPUP::delete($id);
		if(!$delete) {
			delete_option('popupPreviewId');
		}

		$args = array('popupId'=> $id);
		do_action('anypopupDelete', $args);
	}

	parse_str($formSerializedData, $popupPreviewPostData);
	$popupPreviewPostData['allPagesStatus'] = '';
	$popupPreviewPostData['allPostsStatus'] = '';
	$popupPreviewPostData['allCustomPostsStatus'] = '';
	$popupPreviewPostData['onScrolling'] = '';
	$popupPreviewPostData['inActivityStatus'] = '';
	$popupPreviewPostData['popup-timer-status'] = '';
	$popupPreviewPostData['popup-schedule-status'] = '';
	$popupPreviewPostData['anypopup-user-status'] = '';
	$popupPreviewPostData['countryStatus'] = '';
	$popupPreviewPostData['forMobile'] = '';
	$popupPreviewPostData['openMobile'] = '';
	$popupPreviewPostData['hidden_popup_number'] = '';
	$popupPreviewPostData['repeatPopup'] = '';
	$_POST += $popupPreviewPostData;

	$showAllPages = anypopupSanitize('allPages');
	$showAllPosts = anypopupSanitize('allPosts');
	$showAllCustomPosts = anypopupSanitize('allCustomPosts');
	$allSelectedPages = "";
	$allSelectedPosts = "";
	$allSelectedCustomPosts = "";
	$allSelectedCategories = anypopupSanitize("posts-all-categories", true);

	$selectedPages = anypopupSanitize('all-selected-page');
	$selectedPosts = anypopupSanitize('all-selected-posts');
	$selectedCustomPosts = anypopupSanitize('all-selected-custom-posts');

	/* if popup check for all pages it is not needed for save all pages all posts */
	if($showAllPages !== "all" && !empty($selectedPages)) {
		$allSelectedPages = explode(",", $selectedPages);
	}

	if($showAllPosts !== "all" && !empty($selectedPosts)) {
		$allSelectedPosts = explode(",", $selectedPosts);
	}
	if($showAllCustomPosts !== "all" && !empty($selectedCustomPosts)) {
		$allSelectedCustomPosts = explode(",", $selectedCustomPosts);
	}

	$socialOptions = array(
		'anypopupSocialTheme' => anypopupSanitize('anypopupSocialTheme'),
		'anypopupSocialButtonsSize' => anypopupSanitize('anypopupSocialButtonsSize'),
		'anypopupSocialLabel' => anypopupSanitize('anypopupSocialLabel'),
		'anypopupSocialShareCount' => anypopupSanitize('anypopupSocialShareCount'),
		'anypopupRoundButton' => anypopupSanitize('anypopupRoundButton'),
		'fbShareLabel' => anypopupSanitize('fbShareLabel'),
		'lindkinLabel' => anypopupSanitize('lindkinLabel'),
		'anypopupShareUrl' => esc_url_raw(@$_POST['anypopupShareUrl']),
		'shareUrlType' => anypopupSanitize('shareUrlType'),
		'googLelabel' => anypopupSanitize('googLelabel'),
		'twitterLabel' => anypopupSanitize('twitterLabel'),
		'pinterestLabel' => anypopupSanitize('pinterestLabel'),
		'anypopupMailSubject' => anypopupSanitize('anypopupMailSubject'),
		'anypopupMailLable' => anypopupSanitize('anypopupMailLable')
	);

	$socialButtons = array(
		'anypopupTwitterStatus' => anypopupSanitize('anypopupTwitterStatus'),
		'anypopupFbStatus' => anypopupSanitize('anypopupFbStatus'),
		'anypopupEmailStatus' => anypopupSanitize('anypopupEmailStatus'),
		'anypopupLinkedinStatus' => anypopupSanitize('anypopupLinkedinStatus'),
		'anypopupGoogleStatus' => anypopupSanitize('anypopupGoogleStatus'),
		'anypopupPinterestStatus' => anypopupSanitize('anypopupPinterestStatus'),
		'pushToBottom' => anypopupSanitize('pushToBottom')
	);

	$countdownOptions = array(
		'pushToBottom' => anypopupSanitize('pushToBottom'),
		'countdownNumbersBgColor' => anypopupSanitize('countdownNumbersBgColor'),
		'countdownNumbersTextColor' => anypopupSanitize('countdownNumbersTextColor'),
		'anypopup-due-date' => anypopupSanitize('anypopup-due-date'),
		'countdown-position' => anypopupSanitize('countdown-position'),
		'counts-language'=> anypopupSanitize('counts-language'),
		'anypopup-time-zone' => anypopupSanitize('anypopup-time-zone'),
		'anypopup-countdown-type' => anypopupSanitize('anypopup-countdown-type'),
		'countdown-autoclose' => anypopupSanitize('countdown-autoclose')
	);

	$videoOptions = array(
		'video-autoplay' => anypopupSanitize('video-autoplay')
	);

	$exitIntentOptions = array(
		'exit-intent-type' => anypopupSanitize('exit-intent-type'),
		'exit-intent-expire-time' => anypopupSanitize('exit-intent-expire-time'),
		'exit-intent-alert' => anypopupSanitize('exit-intent-alert')
	);

	$subscriptionOptions = array(
		'subs-first-name-status' => anypopupSanitize('subs-first-name-status'),
		'subs-last-name-status' => anypopupSanitize('subs-last-name-status'),
		// email input placeholder text
		'subscription-email' => anypopupSanitize('subscription-email'),
		'subs-first-name' => anypopupSanitize('subs-first-name'),
		'subs-last-name' => anypopupSanitize('subs-last-name'),
		'subs-text-width' => anypopupSanitize('subs-text-width'),
		'subs-button-bgColor' => anypopupSanitize('subs-button-bgColor'),
		'subs-btn-width' => anypopupSanitize('subs-btn-width'),
		'subs-btn-title' => anypopupSanitize('subs-btn-title'),
		'subs-text-input-bgColor' => anypopupSanitize('subs-text-input-bgColor'),
		'subs-text-borderColor' => anypopupSanitize('subs-text-borderColor'),
		'subs-button-color' => anypopupSanitize('subs-button-color'),
		'subs-inputs-color' => anypopupSanitize('subs-inputs-color'),
		'subs-btn-height' => anypopupSanitize('subs-btn-height'),
		'subs-text-height' => anypopupSanitize('subs-text-height'),
		'subs-placeholder-color' => anypopupSanitize('subs-placeholder-color'),
		'subs-validation-message' => anypopupSanitize('subs-validation-message'),
		'subs-success-message' => anypopupSanitize('subs-success-message'),
		'subs-btn-progress-title' => anypopupSanitize('subs-btn-progress-title'),
		'subs-text-border-width' => anypopupSanitize('subs-text-border-width'),
		'subs-success-behavior' => anypopupSanitize('subs-success-behavior'),
		'subs-success-redirect-url' => esc_url_raw(@$_POST['subs-success-redirect-url']),
		'subs-success-popups-list' => anypopupSanitize('subs-success-popups-list'),
		'subs-first-name-required' => anypopupSanitize('subs-first-name-required'),
		'subs-last-name-required' => anypopupSanitize('subs-last-name-required'),
		'subs-success-redirect-new-tab' => anypopupSanitize('subs-success-redirect-new-tab')
	);

	$contactFormOptions = array(
		'contact-name' => anypopupSanitize('contact-name'),
		'contact-name-status' => anypopupSanitize('contact-name-status'),
		'contact-name-required' => anypopupSanitize('contact-name-required'),
		'contact-subject' => anypopupSanitize('contact-subject'),
		'contact-subject-status' => anypopupSanitize('contact-subject-status'),
		'contact-subject-required' => anypopupSanitize('contact-subject-required'),
		// email input placeholder text(string)
		'contact-email' => anypopupSanitize('contact-email'),
		'contact-message' => anypopupSanitize('contact-message'),
		'contact-validation-message' => anypopupSanitize('contact-validation-message'),
		'contact-success-message' => anypopupSanitize('contact-success-message'),
		'contact-inputs-width' => anypopupSanitize('contact-inputs-width'),
		'contact-inputs-height' => anypopupSanitize('contact-inputs-height'),
		'contact-inputs-border-width' => anypopupSanitize('contact-inputs-border-width'),
		'contact-text-input-bgcolor' => anypopupSanitize('contact-text-input-bgcolor'),
		'contact-text-bordercolor' => anypopupSanitize('contact-text-bordercolor'),
		'contact-inputs-color' => anypopupSanitize('contact-inputs-color'),
		'contact-placeholder-color' => anypopupSanitize('contact-placeholder-color'),
		'contact-btn-width' => anypopupSanitize('contact-btn-width'),
		'contact-btn-height' => anypopupSanitize('contact-btn-height'),
		'contact-btn-title' => anypopupSanitize('contact-btn-title'),
		'contact-btn-progress-title' => anypopupSanitize('contact-btn-progress-title'),
		'contact-button-bgcolor' => anypopupSanitize('contact-button-bgcolor'),
		'contact-button-color' => anypopupSanitize('contact-button-color'),
		'contact-area-width' => anypopupSanitize('contact-area-width'),
		'contact-area-height' => anypopupSanitize('contact-area-height'),
		'anypopup-contact-resize' => anypopupSanitize('anypopup-contact-resize'),
		'contact-validate-email' => anypopupSanitize('contact-validate-email'),
		'contact-receive-email' => sanitize_email(@$_POST['contact-receive-email']),
		'contact-fail-message' => anypopupSanitize('contact-fail-message'),
		'show-form-to-top' => anypopupSanitize('show-form-to-top'),
		'contact-success-behavior' => anypopupSanitize('contact-success-behavior'),
		'contact-success-redirect-url' => anypopupSanitize('contact-success-redirect-url'),
		'contact-success-popups-list' => anypopupSanitize('contact-success-popups-list'),
		'dont-show-content-to-contacted-user' => anypopupSanitize('dont-show-content-to-contacted-user'),
		'contact-success-frequency-days' => anypopupSanitize('contact-success-frequency-days'),
		'contact-success-redirect-new-tab' => anypopupSanitize('contact-success-redirect-new-tab')
	);

	$fblikeOptions = array(
		'fblike-like-url' => esc_url_raw(@$_POST['fblike-like-url']),
		'fblike-layout' => anypopupSanitize('fblike-layout'),
		'fblike-dont-show-share-button' => anypopupSanitize('fblike-dont-show-share-button'),
		'fblike-close-popup-after-like' => anypopupSanitize('fblike-close-popup-after-like')
	);

	$addToGeneralOptions = array(
		'showAllPages' => array(),
		'showAllPosts' => array(),
		'showAllCustomPosts' => array(),
		'allSelectedPages' => array(),
		'allSelectedPosts' => array(),
		'allSelectedCustomPosts' => array(),
		'allSelectedCategories'=> array(),
		'fblikeOptions'=> $fblikeOptions,
		'videoOptions'=>$videoOptions,
		'exitIntentOptions'=> $exitIntentOptions,
		'countdownOptions'=> $countdownOptions,
		'socialOptions'=> $socialOptions,
		'socialButtons'=> $socialButtons
	);

	$options = AnyPopupIntegrateExternalSettings::getPopupGeneralOptions($addToGeneralOptions);

	$html = stripslashes(anypopupSanitize("anypopup_html"));
	$fblike = stripslashes(anypopupSanitize("anypopup_fblike"));
	$ageRestriction = stripslashes(anypopupSanitize('anypopup_ageRestriction'));
	$social = stripslashes(anypopupSanitize('anypopup_social'));
	$image = anypopupSanitize('ad_image');
	$countdown = stripslashes(anypopupSanitize('anypopup_countdown'));
	$subscription = stripslashes(anypopupSanitize('anypopup_subscription'));
	$anypopupContactForm = stripslashes(anypopupSanitize('anypopup_contactForm'));
	$iframe = anypopupSanitize('iframe');
	$video = anypopupSanitize('video');
	$shortCode = stripslashes(anypopupSanitize('shortcode'));
	$mailchimp = stripslashes(anypopupSanitize('anypopup_mailchimp'));
	$aweber = stripslashes(anypopupSanitize('anypopup_aweber'));
	$exitIntent = stripslashes(anypopupSanitize('anypopup-exit-intent'));
	$type = anypopupSanitize('type');

	if($type == 'mailchimp') {

		$mailchimpOptions = array(
			'mailchimp-disable-double-optin' => anypopupSanitize('mailchimp-disable-double-optin'),
			'mailchimp-list-id' => anypopupSanitize('mailchimp-list-id'),
			'anypopup-mailchimp-form' => stripslashes(anypopupSanitize('anypopup-mailchimp-form')),
			'mailchimp-required-error-message' => anypopupSanitize('mailchimp-required-error-message'),
			'mailchimp-email-validate-message' => anypopupSanitize('mailchimp-email-validate-message'),
			'mailchimp-error-message' => anypopupSanitize('mailchimp-error-message'),
			'mailchimp-submit-button-bgcolor' => anypopupSanitize('mailchimp-submit-button-bgcolor'),
			'mailchimp-form-aligment' => anypopupSanitize('mailchimp-form-aligment'),
			'mailchimp-label-aligment' => anypopupSanitize('mailchimp-label-aligment'),
			'mailchimp-success-message' => anypopupSanitize('mailchimp-success-message'),
			'mailchimp-only-required' => anypopupSanitize('mailchimp-only-required'),
			'mailchimp-show-form-to-top' => anypopupSanitize('mailchimp-show-form-to-top'),
			'mailchimp-label-color' => anypopupSanitize('mailchimp-label-color'),
			'mailchimp-input-width' => anypopupSanitize('mailchimp-input-width'),
			'mailchimp-input-height' => anypopupSanitize('mailchimp-input-height'),
			'mailchimp-input-border-radius' => anypopupSanitize('mailchimp-input-border-radius'),
			'mailchimp-input-border-width' => anypopupSanitize('mailchimp-input-border-width'),
			'mailchimp-input-border-color' => anypopupSanitize('mailchimp-input-border-color'),
			'mailchimp-input-bg-color' => anypopupSanitize('mailchimp-input-bg-color'),
			'mailchimp-input-text-color' => anypopupSanitize('mailchimp-input-text-color'),
			'mailchimp-submit-width' => anypopupSanitize('mailchimp-submit-width'),
			'mailchimp-submit-height' => anypopupSanitize('mailchimp-submit-height'),
			'mailchimp-submit-border-width' => anypopupSanitize('mailchimp-submit-border-width'),
			'mailchimp-submit-border-radius' => anypopupSanitize('mailchimp-submit-border-radius'),
			'mailchimp-submit-border-color' => anypopupSanitize('mailchimp-submit-border-color'),
			'mailchimp-submit-color' => anypopupSanitize('mailchimp-submit-color'),
			'mailchimp-submit-title' => anypopupSanitize('mailchimp-submit-title'),
			'mailchimp-email-label' => anypopupSanitize('mailchimp-email-label'),
			'mailchimp-indicates-required-fields' => anypopupSanitize('mailchimp-indicates-required-fields'),
			'mailchimp-asterisk-label' => anypopupSanitize('mailchimp-asterisk-label'),
			'mailchimp-success-behavior' => anypopupSanitize('mailchimp-success-behavior'),
			'mailchimp-success-redirect-url' => anypopupSanitize('mailchimp-success-redirect-url'),
			'mailchimp-success-popups-list' => anypopupSanitize('mailchimp-success-popups-list'),
			'mailchimp-success-redirect-new-tab' => anypopupSanitize('mailchimp-success-redirect-new-tab'),
			'mailchimp-close-popup-already-subscribed' => anypopupSanitize('mailchimp-close-popup-already-subscribed')
		);

		$options['mailchimpOptions'] = json_encode($mailchimpOptions);
	}

	if($type == 'aweber') {
		$aweberOptions = array(
			'anypopup-aweber-webform' => anypopupSanitize('anypopup-aweber-webform'),
			'anypopup-aweber-list' => anypopupSanitize('anypopup-aweber-list'),
			'aweber-custom-success-message' => anypopupSanitize('aweber-custom-success-message'),
			'aweber-success-message' => anypopupSanitize('aweber-success-message'),
			'aweber-custom-invalid-email-message' => anypopupSanitize('aweber-custom-invalid-email-message'),
			'aweber-invalid-email' => anypopupSanitize('aweber-invalid-email'),
			'aweber-custom-error-message' => anypopupSanitize('aweber-custom-error-message'),
			'aweber-error-message' => anypopupSanitize('aweber-error-message'),
			'aweber-custom-subscribed-message' => anypopupSanitize('aweber-custom-subscribed-message'),
			'aweber-already-subscribed-message' => anypopupSanitize('aweber-already-subscribed-message'),
			'aweber-validate-email-message' => anypopupSanitize('aweber-validate-email-message'),
			'aweber-required-message' => anypopupSanitize('aweber-required-message'),
			'aweber-success-behavior' => anypopupSanitize('aweber-success-behavior'),
			'aweber-success-redirect-url' => anypopupSanitize('aweber-success-redirect-url'),
			'aweber-success-popups-list' => anypopupSanitize('aweber-success-popups-list'),
			'aweber-success-redirect-new-tab' => anypopupSanitize('aweber-success-redirect-new-tab')
		);
		$options['aweberOptions'] = json_encode($aweberOptions);
	}


	$title = stripslashes(anypopupSanitize('title'));
	$id = anypopupSanitize('hidden_popup_number');
	$jsonDataArray = json_encode($options);

	$data = array(
		'id' => $id,
		'title' => $title,
		'type' => $type,
		'image' => $image,
		'html' => $html,
		'fblike' => $fblike,
		'iframe' => $iframe,
		'video' => $video,
		'shortcode' => $shortCode,
		'ageRestriction' => $ageRestriction,
		'countdown' => $countdown,
		'exitIntent' => $exitIntent,
		'anypopup_subscription' => $subscription,
		'anypopup_contactForm' => $anypopupContactForm,
		'social' => $social,
		'mailchimp' => $mailchimp,
		'aweber' => $aweber,
		'options' => $jsonDataArray,
		'subscriptionOptions' => json_encode($subscriptionOptions),
		'contactFormOptions' => json_encode($contactFormOptions)
	);

	function setPopupForAllPages($id, $data, $type) {
		//-1 is the home page key
		if(is_array($data) && $data[0] == -1 && defined('ICL_LANGUAGE_CODE')) {
			$data[0] .='_'.ICL_LANGUAGE_CODE;
		}
		ANYPOPUP::addPopupForAllPages($id, $data, $type);
	}

	function setOptionPopupType($id, $type) {
		update_option("ANYPOPUP_".strtoupper($type)."_".$id,$id);
	}

	$popupName = "ANYPOPUP".sanitize_text_field(ucfirst(strtolower($popupPreviewPostData['type'])));
	$popupClassName = $popupName."Popup";
	$classPath = ANYPOPUP_APP_POPUP_PATH;

	if($type == 'mailchimp' || $type == 'aweber') {

		$currentActionName1 = AnyPopupIntegrateExternalSettings::getCurrentPopupAppPaths($type);
		$classPath = $currentActionName1['app-path'];
	}

	require_once($classPath ."/classes/".$popupClassName.".php");

	if ($id == "") {
		global $wpdb;

		call_user_func(array($popupClassName, 'create'), $data);

		$lastId = $wpdb->get_var("SELECT LAST_INSERT_ID() FROM ".  $wpdb->prefix."any_popup");
		$postData['saveMod'] = '';
		$postData['popupId'] = $lastId;
		$extensionManagerObj = new ANYPOPUPExtensionManager();
		$extensionManagerObj->setPostData($postData);
		$extensionManagerObj->save();
		update_option('popupPreviewId', $lastId);
		setOptionPopupType($lastId, $type);
		echo $lastId;
		die();
	}

	die();
}

add_action('wp_ajax_save_popup_preview_data', 'AnyPopupsavePopupPreviewData');

