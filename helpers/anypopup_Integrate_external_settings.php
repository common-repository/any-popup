<?php

Class AnyPopupIntegrateExternalSettings {

	public static function getAllExternalPlugins() {

		global $wpdb;

		$query = "SELECT name FROM ". $wpdb->prefix ."anypopup_addons WHERE type='plugin'";
		$addons = $wpdb->get_results($query, ARRAY_A);

		if(empty($addons)) {
			return false;
		}
		return $addons;
	}

	public static function getAllAddons() {

		global $wpdb;

		$query = "SELECT name FROM ". $wpdb->prefix ."anypopup_addons";
		$addons = $wpdb->get_results($query, ARRAY_A);

		if(empty($addons)) {
			return false;
		}
		return $addons;
	}

	public static function doesntHaveAnyActiveExtensions() {

		global  $POPUP_ADDONS;

		$addons = self::getAllAddons();

		if(empty($addons)) {
			return true;
		}

		$activeExtensionsCount = count($addons);
		$allSizeOf = count($POPUP_ADDONS);

		return $allSizeOf > $activeExtensionsCount;
	}

	public static function isExtensionExists($extensionName) {

		global $wpdb;
		$sql = $wpdb->prepare("SELECT * FROM ". $wpdb->prefix ."anypopup_addons WHERE name=%s", $extensionName);
		$ressults = $wpdb->get_results($sql, ARRAY_A);

		if(empty($ressults)) {
			return false;
		}
		return true;
	}

	/* retrun All paths */
	public static function getCurrentPopupAppPaths($popupType) {

		$pathsArray = array();

		global $wpdb;
		$sql = $wpdb->prepare("SELECT paths FROM ". $wpdb->prefix ."anypopup_addons WHERE name=%s", $popupType);
		$ressults = $wpdb->get_results($sql, ARRAY_A);

		if(empty($ressults)) {
			$pathsArray['app-path'] = ANYPOPUP_APP_POPUP_PATH;
			$pathsArray['files-path'] = ANYPOPUP_APP_POPUP_FILES;
		}
		else {
			$addonPaths = json_decode($ressults['0']['paths'], true);
			$pathsArray = $addonPaths;
		}
		return $pathsArray;
	}

	public static function getCurrentPopupAdminPostActionName($popupType) {

		global $wpdb;
		$getcurrentAddonSql = $wpdb->prepare("SELECT id FROM ". $wpdb->prefix ."anypopup_addons WHERE name=%s and type='plugin'", $popupType);
		$addonId = $wpdb->get_results($getcurrentAddonSql, ARRAY_A);

		if(!empty($addonId)) {
			return $popupType;
		}
		return "save_popup";
	}

	public static function getPopupGeneralOptions($params) {

		$options = array(
			'width' => anypopupSanitize('width'),
			'height' => anypopupSanitize('height'),
			'delay' => (int)anypopupSanitize('delay'),
			'buttonDelayValue' => (int)anypopupSanitize('buttonDelayValue'),
			'duration' => (int)anypopupSanitize('duration'),
			'effect' => anypopupSanitize('effect'),
			'escKey' => anypopupSanitize('escKey'),
			'isActiveStatus' => anypopupSanitize('isActiveStatus'),
			'scrolling' => anypopupSanitize('scrolling'),
			'disable-page-scrolling' => anypopupSanitize('disable-page-scrolling'),
			'scaling' => anypopupSanitize('scaling'),
			'reposition' => anypopupSanitize('reposition'),
			'overlayClose' => anypopupSanitize('overlayClose'),
			'reopenAfterSubmission' => anypopupSanitize('reopenAfterSubmission'),
			'contentClick' => anypopupSanitize('contentClick'),
			'content-click-behavior' => anypopupSanitize('content-click-behavior'),
			'click-redirect-to-url' => anypopupSanitize('click-redirect-to-url'),
			'redirect-to-new-tab' => anypopupSanitize('redirect-to-new-tab'),
			'opacity' => anypopupSanitize('opacity'),
			'popup-background-opacity' => anypopupSanitize('popup-background-opacity'),
			'anypopupOverlayColor' => anypopupSanitize('anypopupOverlayColor'),
			'anypopup-content-background-color' => anypopupSanitize('anypopup-content-background-color'),
			'popupFixed' => anypopupSanitize('popupFixed'),
			'fixedPostion' => anypopupSanitize('fixedPostion'),
			'popup-dimension-mode' => anypopupSanitize('popup-dimension-mode'),
			'popup-responsive-dimension-measure' => anypopupSanitize('popup-responsive-dimension-measure'),
			'maxWidth' => anypopupSanitize('maxWidth'),
			'maxHeight' => anypopupSanitize('maxHeight'),
			'initialWidth' => anypopupSanitize('initialWidth'),
			'initialHeight' => anypopupSanitize('initialHeight'),
			'closeButton' => anypopupSanitize('closeButton'),
			'theme' => anypopupSanitize('theme'),
			'anypopupTheme3BorderColor'=> anypopupSanitize("anypopupTheme3BorderColor"),
			'anypopupTheme3BorderRadius'=> anypopupSanitize("anypopupTheme3BorderRadius"),
			'onScrolling' => anypopupSanitize('onScrolling'),
			'inActivityStatus' => anypopupSanitize('inActivityStatus'),
			'inactivity-timout' => anypopupSanitize('inactivity-timout'),
			'beforeScrolingPrsent' => (int)anypopupSanitize('beforeScrolingPrsent'),
			'forMobile' => anypopupSanitize('forMobile'),
			'openMobile' => anypopupSanitize('openMobile'), // open only for mobile
			'repeatPopup' => anypopupSanitize('repeatPopup'),
			'popup-appear-number-limit' => anypopupSanitize('popup-appear-number-limit'),
			'save-cookie-page-level' => anypopupSanitize('save-cookie-page-level'),
			'autoClosePopup' => anypopupSanitize('autoClosePopup'),
			'countryStatus' => anypopupSanitize('countryStatus'),
			'showAllPages' => $params['showAllPages'],
			'allPagesStatus' => anypopupSanitize('allPagesStatus'),
			'allPostsStatus' => anypopupSanitize('allPostsStatus'),
			'allCustomPostsStatus' => anypopupSanitize('allCustomPostsStatus'),
			'allSelectedPages' => $params['allSelectedPages'],
			'showAllPosts' => $params['showAllPosts'],
			'showAllCustomPosts' => $params['showAllCustomPosts'],
			'allSelectedPosts' => $params['allSelectedPosts'],
			'allSelectedCustomPosts' => $params['allSelectedCustomPosts'],
			'posts-all-categories'=> $params['allSelectedCategories'],
			'all-custom-posts' => anypopupSanitize('all-custom-posts', true),
			'anypopup-user-status' => anypopupSanitize('anypopup-user-status'),
			'loggedin-user' => anypopupSanitize('loggedin-user'),
			'popup-timer-status' => anypopupSanitize('popup-timer-status'),
			'popup-schedule-status' => anypopupSanitize('popup-schedule-status'),
			'popup-start-timer' => anypopupSanitize('popup-start-timer'),
			'popup-finish-timer' => anypopupSanitize('popup-finish-timer'),
			'schedule-start-weeks' => anypopupSanitize('schedule-start-weeks', true),
			'schedule-start-time' => anypopupSanitize('schedule-start-time'),
			'schedule-end-time' => anypopupSanitize('schedule-end-time'),
			'allowCountries' => anypopupSanitize('allowCountries'),
			'countryName' => anypopupSanitize('countryName'),
			'countryIso' => anypopupSanitize('countryIso'),
			'disablePopup' => anypopupSanitize('disablePopup'),
			'disablePopupOverlay' => anypopupSanitize('disablePopupOverlay'),
			'popupClosingTimer' => anypopupSanitize('popupClosingTimer'),
			'yesButtonLabel' => anypopupSanitize('yesButtonLabel', true),
			'noButtonLabel' => anypopupSanitize('noButtonLabel', true),
			'restrictionUrl' => anypopupSanitize('restrictionUrl'),
			'yesButtonBackgroundColor' => anypopupSanitize('yesButtonBackgroundColor'),
			'noButtonBackgroundColor' => anypopupSanitize('noButtonBackgroundColor'),
			'yesButtonTextColor' => anypopupSanitize('yesButtonTextColor'),
			'noButtonTextColor' => anypopupSanitize('noButtonTextColor'),
			'yesButtonRadius' => (int)anypopupSanitize('yesButtonRadius'),
			'noButtonRadius' => (int)anypopupSanitize('noButtonRadius'),
			'anypopupRestrictionExpirationTime' => (int)anypopupSanitize('anypopupRestrictionExpirationTime'),
			'restrictionCookeSavingLevel' => anypopupSanitize('restrictionCookeSavingLevel'),
			'pushToBottom' => anypopupSanitize('pushToBottom'),
			'onceExpiresTime' => anypopupSanitize('onceExpiresTime'),
			'anypopupOverlayCustomClasss' => anypopupSanitize('anypopupOverlayCustomClasss'),
			'anypopupContentCustomClasss' => anypopupSanitize('anypopupContentCustomClasss'),
			'popup-z-index' => anypopupSanitize('popup-z-index'),
			'popup-content-padding' => anypopupSanitize('popup-content-padding'),
			'theme-close-text' => anypopupSanitize('theme-close-text'),
			'socialButtons' => json_encode($params['socialButtons']),
			'socialOptions' => json_encode($params['socialOptions']),
			'countdownOptions' => json_encode($params['countdownOptions']),
			'exitIntentOptions' => json_encode($params['exitIntentOptions']),
			'videoOptions' => json_encode($params['videoOptions']),
			'fblikeOptions' => json_encode($params['fblikeOptions']),
			'repetitivePopup' => anypopupSanitize('repetitivePopup'),
			'repetitivePopupPeriod' => anypopupSanitize('repetitivePopupPeriod'),
			'randomPopup' => anypopupSanitize('randomPopup'),
			'popupOpenSound' => anypopupSanitize('popupOpenSound'),
			'popupOpenSoundFile' => anypopupSanitize('popupOpenSoundFile'),
			'popupContentBgImage' => anypopupSanitize('popupContentBgImage'),
			'popupContentBgImageUrl' => anypopupSanitize('popupContentBgImageUrl'),
			'popupContentBackgroundSize' => anypopupSanitize('popupContentBackgroundSize'),
			'popupContentBackgroundRepeat' => anypopupSanitize('popupContentBackgroundRepeat')
		);

		return $options;
	}
}
