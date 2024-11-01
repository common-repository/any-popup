<?php
add_action('admin_post_save_popup', 'anypopupSave');

function anypopupSanitize($optionsKey, $isTextField = false)
{
	if (isset($_POST[$optionsKey])) {
		if ($optionsKey == "anypopup_html"||
			$optionsKey == "anypopup_ageRestriction"||
			$optionsKey == "anypopup_countdown"||
			$optionsKey == "anypopup_social" ||
			$optionsKey == "anypopup-exit-intent" ||
			$optionsKey == "anypopup_fblike" ||
			$optionsKey == "anypopup_subscription" ||
			$optionsKey == "anypopup_contactForm" ||
			$optionsKey == "all-selected-page" ||
			$optionsKey == "all-selected-posts" ||
			$optionsKey == "anypopup_mailchimp" ||
			$optionsKey == "anypopup_aweber" ||
			$optionsKey == "anypopup-mailchimp-form" ||
			$isTextField == true
			) {
			if(ANYPOPUP_PKG > ANYPOPUP_PKG_FREE) {
				$anypopupData = $_POST[$optionsKey];
				return $anypopupData;
				/*require_once(ANYPOPUP_APP_POPUP_FILES ."/anypopup_pro.php");
				return AnypopupPro::anypopupDataSanitize($anypopupData);*/
			}
			return ANYPOPUPFunctions::anypopupDataSanitize($_POST[$optionsKey]);
		}
		return sanitize_text_field($_POST[$optionsKey]);
	}
	else {
		return "";
	}
}

function anypopupSave()
{
	
	
	global $wpdb;

	if(isset($_POST)) {
		check_admin_referer('anypopupAnyPopupSave');
	}
	/*Removing all added slashes*/
	$_POST = stripslashes_deep($_POST);
	$postData = $_POST;
	$socialButtons = array();
	$socialOptions = array();
	$countdownOptions = array();
	$fblikeOptions = array();
	$subscriptionOptions = array();
	$options = array();
	$contactFormOptions = array();
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
	if(!empty($selectedPages)) {
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
		'video-type' => anypopupSanitize('video-type'),
		'video-autoplay' => anypopupSanitize('video-autoplay'),
		'video-fullscreen' => anypopupSanitize('video-fullscreen')
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
		'showAllPages' => $showAllPages,
		'showAllPosts' => $showAllPosts,
		'showAllCustomPosts' => $showAllCustomPosts,
		'allSelectedPages' => $allSelectedPages,
		'allSelectedPosts' => $allSelectedPosts,
		'allSelectedCustomPosts' => $allSelectedCustomPosts,
		'allSelectedCategories'=> $allSelectedCategories,
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
	$exitIntent = stripslashes(anypopupSanitize('anypopup-exit-intent'));
	$type = anypopupSanitize('type');
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

	if (empty($title)) {
		$redirectUrl = add_query_arg( array(
			'titleError' => 1,
			'type'  => $type,
		), ANYPOPUP_APP_POPUP_ADMIN_URL."admin.php?page=anypopup-edit-popup");

		wp_safe_redirect($redirectUrl);
		exit();
	}
	
	$popupName = "ANYPOPUP".sanitize_text_field(ucfirst(strtolower($_POST['type'])));
	$popupClassName = $popupName."Popup";
	
	require_once(ANYPOPUP_APP_POPUP_PATH ."/classes/".$popupClassName.".php");

	if ($id == "") {
		global $wpdb;

		call_user_func(array($popupClassName, 'create'), $data);
		$lastId = $wpdb->get_var("SELECT LAST_INSERT_ID() FROM ".  $wpdb->prefix."any_popup");
		$postData['saveMod'] = '';
		$postData['popupId'] = $lastId;
		$extensionManagerObj = new ANYPOPUPExtensionManager();
		$extensionManagerObj->setPostData($postData);
		$extensionManagerObj->save();
		
		
		if(ANYPOPUP_PKG != ANYPOPUP_PKG_FREE) {
			ANYPOPUP::removePopupFromPages($lastId,'page');
			ANYPOPUP::removePopupFromPages($lastId,'categories');
			if($options['allPagesStatus']) {
				if(!empty($showAllPages) && $showAllPages != 'all') {
					setPopupForAllPages($lastId, $allSelectedPages, 'page');
				}
				else {

					AnyPopupupdatePopupOptions($lastId, array('page'), true);
				}
			}
			
			if($options['allPostsStatus']) {
				if(!empty($showAllPosts) && $showAllPosts == "selected") {

					setPopupForAllPages($lastId, $allSelectedPosts, 'page');
				}
				else if($showAllPosts == "all") {
					AnyPopupupdatePopupOptions($lastId, array('post'), true);
				}
				if($showAllPosts == "allCategories") {
					setPopupForAllPages($lastId, $allSelectedCategories, 'categories');
				}
			}

			if($options['allCustomPostsStatus']) {
				if(!empty($showAllCustomPosts) && $showAllCustomPosts == "selected") {
					setPopupForAllPages($lastId, $allSelectedCustomPosts, 'page');
				}
				else if($showAllCustomPosts == "all") {
					AnyPopupupdatePopupOptions($lastId, $options['all-custom-posts'], true);
				}
			}
			
		}
		
		setOptionPopupType($lastId, $type);

		$redirectUrl = add_query_arg( array(
			'id'    => $lastId,
			'saved' => 1,
			'type'  => $type,
		), ANYPOPUP_APP_POPUP_ADMIN_URL."admin.php?page=anypopup-edit-popup");

		wp_safe_redirect($redirectUrl);
		exit();
	}
	else {
		$popup = ANYPOPUP::findById($id);
		$popup->setTitle($title);
		$popup->setId($id);
		$popup->setType($type);
		$popup->setOptions($jsonDataArray);

		switch ($popupName) {
			case 'ANYPOPUPImage':
				$popup->setUrl($image);
				break;
			case 'ANYPOPUPIframe':
				$popup->setUrl($iframe);
				break;
			case 'ANYPOPUPVideo':
				$popup->setUrl($video);
				$popup->setVideoOptions(json_encode($videoOptions));
				break;
			case 'ANYPOPUPHtml':
				$popup->setContent($html);
				break;
			case 'ANYPOPUPFblike':
				$popup->setContent($fblike);
				$popup->setFblikeOptions(json_encode($fblikeOptions));
				break;
			case 'ANYPOPUPShortcode':
				$popup->setShortcode($shortCode);
				break;
			case 'ANYPOPUPAgerestriction':
				$popup->setContent($ageRestriction);
				$popup->setYesButton($options['yesButtonLabel']);
				$popup->setNoButton($options['noButtonLabel']);
				$popup->setRestrictionUrl($options['restrictionUrl']);
				break;
			case 'ANYPOPUPCountdown':
				$popup->setCountdownContent($countdown);
				$popup->setCountdownOptions(json_encode($countdownOptions));
				break;
			case 'ANYPOPUPSocial':
				$popup->setSocialContent($social);
				$popup->setButtons(json_encode($socialButtons));
				$popup->setSocialOptions(json_encode($socialOptions));
				break;
			case 'ANYPOPUPExitintent':
				$popup->setContent($exitIntent);
				$popup->setExitIntentOptions(json_encode($exitIntentOptions));
				break;
			case 'ANYPOPUPSubscription':
				$popup->setContent($subscription);
				$popup->setSubscriptionOptions(json_encode($subscriptionOptions));
				break;
			case 'ANYPOPUPContactform':
				$popup->setContent($anypopupContactForm);
				$popup->steParams(json_encode($contactFormOptions));
			break;
		}
		if(ANYPOPUP_PKG != ANYPOPUP_PKG_FREE) {
			ANYPOPUP::removePopupFromPages($id, 'page');
			ANYPOPUP::removePopupFromPages($id, 'categories');
			if(!empty($options['allPagesStatus'])) {
				if($showAllPages && $showAllPages != 'all') {
					AnyPopupupdatePopupOptions($id, array('page'), false);
					setPopupForAllPages($id, $allSelectedPages, 'page');
				}
				else {
					AnyPopupupdatePopupOptions($id, array('page'), true);
				}
			}
			else  {
				AnyPopupupdatePopupOptions($id, array('page'), false);
			}

			if(!empty($options['allPostsStatus'])) {
				if(!empty($showAllPosts) && $showAllPosts == "selected") {
					AnyPopupupdatePopupOptions($id, array('post'), false);
					setPopupForAllPages($id, $allSelectedPosts, 'page');
				}
				else if($showAllPosts == "all"){
					AnyPopupupdatePopupOptions($id, array('post'), true);
				}
				if($showAllPosts == "allCategories") {
					setPopupForAllPages($id, $allSelectedCategories, 'categories');
				}
			}
			else {
				AnyPopupupdatePopupOptions($id, array('post'), false);
			}

			if(!empty($options['allCustomPostsStatus'])) {
				if(!empty($showAllCustomPosts) && $showAllCustomPosts == "selected") {
					AnyPopupupdatePopupOptions($id, $options['all-custom-posts'], false);
					setPopupForAllPages($id, $allSelectedCustomPosts, 'page');
				}
				else if($showAllCustomPosts == "all") {
					AnyPopupupdatePopupOptions($id, $options['all-custom-posts'], true);
				}
			}
			else {
				AnyPopupupdatePopupOptions($id, $options['all-custom-posts'], false);
			}
		}
	
		setOptionPopupType($id, $type);
		$postData['saveMod'] = '1';
		$postData['popupId'] = $id;
		$extensionManagerObj = new ANYPOPUPExtensionManager();
		$extensionManagerObj->setPostData($postData);
		$extensionManagerObj->save();
		$popup->save();

		$redirectUrl = add_query_arg( array(
			'id'    => $id,
			'saved' => 1,
			'type'  => $type,
		), ANYPOPUP_APP_POPUP_ADMIN_URL."admin.php?page=anypopup-edit-popup");

		wp_safe_redirect($redirectUrl);
		exit();
	}

}

/**
 * Save data to wp options
 *
 * @since 3.2.2
 *
 * @param int $id popup id number
 * @param array $postTypes page post types
 * @param bool $isInsert true for insert false for remove
 *
 * @return void
 *
 */

function AnyPopupupdatePopupOptions($id, $postTypes, $isInsert) {

	/*getting wp option data*/
	$allPosts = get_option("ANYPOPUP_ALL_POSTS");
	$key = false;

	if(!$allPosts) {
		$allPosts = array();
	}

	if(empty($postTypes)) {
		$postTypes = array();
	}

	if($allPosts && !empty($allPosts)) {
		/*Get current popup id key from assoc array*/
		$key = ANYPOPUPFunctions::getCurrentPopupIdFromOptions($id);
	}

	/*When isset like id data in wp options*/
	if($key !== false) {
		$popupPostTypes = $allPosts[$key]['popstTypes'];
		if(empty($popupPostTypes)) {
			$popupPostTypes = array();
		}

		/*Insert or remove from exist post types*/
		if($isInsert) {
			$popupPostTypes = array_merge($popupPostTypes, $postTypes);
			$popupPostTypes = array_unique($popupPostTypes);
		}
		else {
			if(!empty($postTypes)) {
				$popupPostTypes = array_diff($popupPostTypes, $postTypes);
			}

		}

		/*After modificition remove popup id from all post types or cghanged exist value*/
		if(empty($popupPostTypes)) {
			unset($allPosts[$key]);
		}
		else {
			$allPosts[$key]['popstTypes'] = $popupPostTypes;
			if(defined('ICL_LANGUAGE_CODE')){
				$allPosts[$key]['lang'] = ICL_LANGUAGE_CODE;
			}
		}

	}
	else if($isInsert && !empty($postTypes)) {
		$data = array('id'=>$id, 'popstTypes'=>$postTypes);
		if(defined('ICL_LANGUAGE_CODE')){
			$data['lang'] = ICL_LANGUAGE_CODE;
		}
		if(is_array($allPosts)) {
			array_push($allPosts, $data);
		}
	}

	update_option("ANYPOPUP_ALL_POSTS", $allPosts);
}
