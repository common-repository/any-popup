<?php
$extensionManagerObj = new ANYPOPUPExtensionManager();

$popupType = @sanitize_text_field($_GET['type']);
if (!$popupType) {
	$popupType = 'html';
}
$popupCount = get_option('ANYPOPUPMaxOpenCount');

//Get current paths for popup, for addons it different
$paths = AnyPopupIntegrateExternalSettings::getCurrentPopupAppPaths($popupType);
//Get current form action, for addons it different
$currentActionName = AnyPopupIntegrateExternalSettings::getCurrentPopupAdminPostActionName($popupType);

$popupAppPath = $paths['app-path'];
$popupFilesPath = $paths['files-path'];

$popupName = "ANYPOPUP".ucfirst(strtolower($popupType));
$popupClassName = $popupName."Popup";
require_once($popupAppPath ."/classes/".$popupClassName.".php");
$obj = new $popupClassName();

global $removeOptions;
$removeOptions = $obj->getRemoveOptions();

if (isset($_GET['id'])) {
	$id = (int)$_GET['id'];
	$result = call_user_func(array($popupClassName, 'findById'), $id);
	if (!$result) {
		$redirectUrl = add_query_arg( array(
			'type'  => $popupType,
		), ANYPOPUP_APP_POPUP_ADMIN_URL."admin.php?page=anypopup-edit-popup");

		wp_safe_redirect($redirectUrl);
	}

	switch ($popupType) {
		case 'iframe':
			$anypopupDataIframe = $result->getUrl();
			break;
		case 'video':
			$anypopupDataVideo = $result->getRealUrl();
			$anypopupVideoOptions = $result->getVideoOptions();
			break;
		case 'image':
			$anypopupDataImage = $result->getUrl();
			break;
		case 'html':
			//We cannot escape this input because the data is raw HTML
			$anypopupDataHtml = $result->getContent();
			break;
		case 'fblike':
			//We cannot escape this input because the data is raw HTML
			$anypopupDataFblike = $result->getContent();
			$anypopupFlikeOptions = $result->getFblikeOptions();
			break;
		case 'shortcode':
			$anypopupDataShortcode = $result->getShortcode();
			break;
		case 'ageRestriction':
			//We cannot escape this input because the data is raw HTML
			$anypopupAgeRestriction = ($result->getContent());
			$anypopupYesButton = anypopupSafeStr($result->getYesButton());
			$anypopupNoButton = anypopupSafeStr($result->getNoButton());
			$anypopupRestrictionUrl = anypopupSafeStr($result->getRestrictionUrl());
			break;
		case 'countdown':
			$anypopupCoundownContent = $result->getCountdownContent();
			$countdownOptions = json_decode(anypopupSafeStr($result->getCountdownOptions()),true);
			$anypopupCountdownNumbersBgColor = $countdownOptions['countdownNumbersBgColor'];
			$anypopupCountdownNumbersTextColor = $countdownOptions['countdownNumbersTextColor'];
			$anypopupDueDate = $countdownOptions['anypopup-due-date'];
			@$anypopupGetCountdownType = $countdownOptions['anypopup-countdown-type'];
			$anypopupCountdownLang = $countdownOptions['counts-language'];
			@$anypopupCountdownPosition = $countdownOptions['coundown-position'];
			@$anypopupSelectedTimeZone = $countdownOptions['anypopup-time-zone'];
			@$anypopupCountdownAutoclose = $countdownOptions['countdown-autoclose'];
			break;
		case 'social':
			$anypopupSocialContent = ($result->getSocialContent());
			$anypopupSocialButtons = anypopupSafeStr($result->getButtons());
			$anypopupSocialOptions = anypopupSafeStr($result->getSocialOptions());
			break;
		case 'exitIntent':
			$anypopupExitIntentContent = $result->getContent();
			$exitIntentOptions = $result->getExitIntentOptions();
			break;
		case 'subscription':
			$anypopupSunbscriptionContent = $result->getContent();
			$subscriptionOptions = $result->getSubscriptionOptions();
			break;
		case 'contactForm':
			$params = $result->getParams();
			$anypopupContactFormContent = $result->getContent();
			break;
	}

	$title = $result->getTitle();
	$jsonData = json_decode($result->getOptions(), true);

	$anypopupEscKey = @$jsonData['escKey'];
	$anypopupScrolling = @$jsonData['scrolling'];
	$anypopupDisablePageScrolling = @$jsonData['disable-page-scrolling'];
	$anypopupScaling = @$jsonData['scaling'];
	$anypopupCloseButton = @$jsonData['closeButton'];
	$anypopupReposition = @$jsonData['reposition'];
	$anypopupOverlayClose = @$jsonData['overlayClose'];
	$anypopupReopenAfterSubmission = @$jsonData['reopenAfterSubmission'];
	$anypopupOverlayColor = @$jsonData['anypopupOverlayColor'];
	$anypopupContentBackgroundColor = @$jsonData['anypopup-content-background-color'];
	$anypopupContentClick = @$jsonData['contentClick'];
	$anypopupContentClickBehavior = @$jsonData['content-click-behavior'];
	$anypopupClickRedirectToUrl = @$jsonData['click-redirect-to-url'];
	$anypopupRedirectToNewTab = @$jsonData['redirect-to-new-tab'];
	$anypopupOpacity = @$jsonData['opacity'];
	$anypopupBackgroundOpacity = @$jsonData['popup-background-opacity'];
	$anypopupFixed = @$jsonData['popupFixed'];
	$anypopupFixedPostion = @$jsonData['fixedPostion'];
	$anypopupOnScrolling = @$jsonData['onScrolling'];
	$anypopupInActivityStatus = @$jsonData['inActivityStatus'];
	$anypopupInactivityTimout = @$jsonData['inactivity-timout'];
	$beforeScrolingPrsent = @$jsonData['beforeScrolingPrsent'];
	$duration = @$jsonData['duration'];
	$delay = @$jsonData['delay'];

	$anypopupCloseButtonDelay = @$jsonData['buttonDelayValue'];
	$anypopupTheme3BorderColor = @$jsonData['anypopupTheme3BorderColor'];
	$anypopupTheme3BorderRadius = @$jsonData['anypopupTheme3BorderRadius'];
	$anypopupThemeCloseText = @$jsonData['theme-close-text'];
	$effect =@$jsonData['effect'];
	$anypopupInitialWidth = @$jsonData['initialWidth'];
	$anypopupInitialHeight = @$jsonData['initialHeight'];
	$anypopupWidth = @$jsonData['width'];
	$anypopupHeight = @$jsonData['height'];
	$anypopupDimensionMode = @$jsonData['popup-dimension-mode'];
	$anypopupResponsiveDimensionMeasure = @$jsonData['popup-responsive-dimension-measure'];
	$anypopupMaxWidth = @$jsonData['maxWidth'];
	$anypopupMaxHeight = @$jsonData['maxHeight'];
	$anypopupForMobile = @$jsonData['forMobile'];
	$anypopupOpenOnMobile = @$jsonData['openMobile'];
	$anypopupAllPagesStatus = @$jsonData['allPagesStatus'];
	$anypopupAllPostsStatus = @$jsonData['allPostsStatus'];
	$anypopupAllCustomPostsStatus = @$jsonData['allCustomPostsStatus'];
	$anypopupPostsAllCategories = @$jsonData['posts-all-categories'];
	$anypopupRepeatPopup = @$jsonData['repeatPopup'];
	$anypopupRepetitivePopup = @$jsonData['repetitivePopup'];
	$anypopupAppearNumberLimit = @$jsonData['popup-appear-number-limit'];
	$anypopupRepetitivePopupPeriod = @$jsonData['repetitivePopupPeriod'];
	$anypopupCookiePageLevel = @$jsonData['save-cookie-page-level'];
	$anypopupDisablePopup = @$jsonData['disablePopup'];
	$anypopupDisablePopupOverlay = @$jsonData['disablePopupOverlay'];
	$anypopupClosingTimer = @$jsonData['popupClosingTimer'];
	$anypopupAutoClosePopup = @$jsonData['autoClosePopup'];
	$anypopupRandomPopup = @$jsonData['randomPopup'];
	$anypopupOpenSound = @$jsonData['popupOpenSound'];
	$anypopupOpenSoundFile = @$jsonData['popupOpenSoundFile'];
	$anypopupContentBgImage = @$jsonData['popupContentBgImage'];
	$anypopupContentBgImageUrl = @$jsonData['popupContentBgImageUrl'];
	$anypopupContentBackgroundSize = @$jsonData['popupContentBackgroundSize'];
	$anypopupContentBackgroundRepeat = @$jsonData['popupContentBackgroundRepeat'];
	$anypopupCountryStatus = @$jsonData['countryStatus'];
	$anypopupAllSelectedPages = @$jsonData['allSelectedPages'];
	$anypopupAllSelectedCustomPosts = @$jsonData['allSelectedCustomPosts'];
	$anypopupAllPostStatus = @$jsonData['showAllPosts'];
	$anypopupAllSelectedPosts = @$jsonData['allSelectedPosts'];
	$anypopupAllowCountries = @$jsonData['allowCountries'];
	$anypopupAllPages = @$jsonData['showAllPages'];
	$anypopupAllPosts = @$jsonData['showAllPosts'];
	$anypopupAllCustomPosts = @$jsonData['showAllCustomPosts'];
	$anypopupAllCustomPostsType = @$jsonData['all-custom-posts'];
	$anypopupLogedUser = @$jsonData['loggedin-user'];
	$anypopupUserSeperate = @$jsonData['anypopup-user-status'];
	$anypopupCountryName = @$jsonData['countryName'];
	$anypopupCountryIso = @$jsonData['countryIso'];
	$anypopupTimerStatus = @$jsonData['popup-timer-status'];
	$anypopupScheduleStatus = @$jsonData['popup-schedule-status'];
	$anypopupScheduleStartWeeks = @$jsonData['schedule-start-weeks'];
	$anypopupScheduleStartTime = @$jsonData['schedule-start-time'];
	$anypopupScheduleEndTime = @$jsonData['schedule-end-time'];
	$anypopupFinishTimer = @$jsonData['popup-finish-timer'];
	$anypopupStartTimer = @$jsonData['popup-start-timer'];
	$anypopupColorboxTheme = @$jsonData['theme'];
	$anypopupOverlayCustomClasss = @$jsonData['anypopupOverlayCustomClasss'];
	$anypopupContentCustomClasss = @$jsonData['anypopupContentCustomClasss'];
	$anypopupZIndex = @$jsonData['popup-z-index'];
	$anypopupContentPadding = @$jsonData['popup-content-padding'];
	$anypopupOnceExpiresTime = @$jsonData['onceExpiresTime'];
	$anypopupRestrictionAction = @$jsonData['restrictionAction'];
	$yesButtonBackgroundColor = @anypopupSafeStr($jsonData['yesButtonBackgroundColor']);
	$noButtonBackgroundColor = @anypopupSafeStr($jsonData['noButtonBackgroundColor']);
	$yesButtonTextColor = @anypopupSafeStr($jsonData['yesButtonTextColor']);
	$noButtonTextColor = @anypopupSafeStr($jsonData['noButtonTextColor']);
	$yesButtonRadius = @anypopupSafeStr($jsonData['yesButtonRadius']);
	$noButtonRadius = @anypopupSafeStr($jsonData['noButtonRadius']);
	$anypopupRestrictionExpirationTime = @anypopupSafeStr($jsonData['anypopupRestrictionExpirationTime']);
	$anypopupRestrictionCookeSavingLevel = @anypopupSafeStr($jsonData['restrictionCookeSavingLevel']);
	$anypopupSocialOptions = json_decode(@$anypopupSocialOptions,true);
	$anypopupShareUrl = $anypopupSocialOptions['anypopupShareUrl'];
	$shareUrlType = @anypopupSafeStr($anypopupSocialOptions['shareUrlType']);
	$fbShareLabel = @anypopupSafeStr($anypopupSocialOptions['fbShareLabel']);
	$lindkinLabel = @anypopupSafeStr($anypopupSocialOptions['lindkinLabel']);
	$googLelabel = @anypopupSafeStr($anypopupSocialOptions['googLelabel']);
	$twitterLabel = @anypopupSafeStr($anypopupSocialOptions['twitterLabel']);
	$pinterestLabel = @anypopupSafeStr($anypopupSocialOptions['pinterestLabel']);
	$anypopupMailSubject = @anypopupSafeStr($anypopupSocialOptions['anypopupMailSubject']);
	$anypopupMailLable = @anypopupSafeStr($anypopupSocialOptions['anypopupMailLable']);
	$anypopupSocialButtons = json_decode(@$anypopupSocialButtons,true);
	$anypopupTwitterStatus = @anypopupSafeStr($anypopupSocialButtons['anypopupTwitterStatus']);
	$anypopupFbStatus = @anypopupSafeStr($anypopupSocialButtons['anypopupFbStatus']);
	$anypopupEmailStatus = @anypopupSafeStr($anypopupSocialButtons['anypopupEmailStatus']);
	$anypopupLinkedinStatus = @anypopupSafeStr($anypopupSocialButtons['anypopupLinkedinStatus']);
	$anypopupGoogleStatus = @anypopupSafeStr($anypopupSocialButtons['anypopupGoogleStatus']);
	$anypopupPinterestStatus = @anypopupSafeStr($anypopupSocialButtons['anypopupPinterestStatus']);
	$anypopupSocialTheme = @anypopupSafeStr($anypopupSocialOptions['anypopupSocialTheme']);
	$anypopupSocialButtonsSize = @anypopupSafeStr($anypopupSocialOptions['anypopupSocialButtonsSize']);
	$anypopupSocialLabel = @anypopupSafeStr($anypopupSocialOptions['anypopupSocialLabel']);
	$anypopupSocialShareCount = @anypopupSafeStr($anypopupSocialOptions['anypopupSocialShareCount']);
	$anypopupRoundButton = @anypopupSafeStr($anypopupSocialOptions['anypopupRoundButton']);
	$anypopupPushToBottom = @anypopupSafeStr($jsonData['pushToBottom']);
	$exitIntentOptions = json_decode(@$exitIntentOptions, true);
	$anypopupExitIntentTpype = @$exitIntentOptions['exit-intent-type'];
	$anypopupExitIntntExpire = @$exitIntentOptions['exit-intent-expire-time'];
	$anypopupExitIntentAlert = @$exitIntentOptions['exit-intent-alert'];
	$anypopupVideoOptions = json_decode(@$anypopupVideoOptions, true);
	$anypopupVideoType = @$anypopupVideoOptions['video-type'];
	$anypopupVideoAutoplay = @$anypopupVideoOptions['video-autoplay'];
	$anypopupVideoFullscreen = @$anypopupVideoOptions['video-fullscreen'];
	$anypopupFlikeOptions = json_decode(@$anypopupFlikeOptions, true);
	$anypopupFblikeurl = @$anypopupFlikeOptions['fblike-like-url'];
	$anypopupFbLikeLayout = @$anypopupFlikeOptions['fblike-layout'];
	$anypopupFblikeDontShowShareButton = @$anypopupFlikeOptions['fblike-dont-show-share-button'];
	$anypopupFblikeClosePopupAfterLike = @$anypopupFlikeOptions['fblike-close-popup-after-like'];
	$subscriptionOptions = json_decode(@$subscriptionOptions, true);
	$anypopupSubsFirstNameStatus = $subscriptionOptions['subs-first-name-status'];
	$anypopupSubsLastNameStatus = $subscriptionOptions['subs-last-name-status'];
	$anypopupSubscriptionEmail = @$subscriptionOptions['subscription-email'];
	$anypopupSubsFirstName = @$subscriptionOptions['subs-first-name'];
	$anypopupSubsLastName = @$subscriptionOptions['subs-last-name'];
	$anypopupSubsButtonBgColor = @$subscriptionOptions['subs-button-bgColor'];
	$anypopupSubsBtnWidth = @$subscriptionOptions['subs-btn-width'];
	$anypopupSubsBtnHeight = @$subscriptionOptions['subs-btn-height'];
	$anypopupSubsTextHeight = @$subscriptionOptions['subs-text-height'];
	$anypopupSubsBtnTitle = @$subscriptionOptions['subs-btn-title'];
	$anypopupSubsTextInputBgColor = @$subscriptionOptions['subs-text-input-bgColor'];
	$anypopupSubsTextBorderColor = @$subscriptionOptions['subs-text-borderColor'];
	$anypopupSubsTextWidth = @$subscriptionOptions['subs-text-width'];
	$anypopupSubsButtonColor = @$subscriptionOptions['subs-button-color'];
	$anypopupSubsInputsColor = @$subscriptionOptions['subs-inputs-color'];
	$anypopupSubsPlaceholderColor = @$subscriptionOptions['subs-placeholder-color'];
	$anypopupSubsValidateMessage = @$subscriptionOptions['subs-validation-message'];
	$anypopupSuccessMessage = @$subscriptionOptions['subs-success-message'];
	$anypopupSubsBtnProgressTitle = @$subscriptionOptions['subs-btn-progress-title'];
	$anypopupSubsTextBorderWidth = @$subscriptionOptions['subs-text-border-width'];
	$anypopupSubsSuccessBehavior = @$subscriptionOptions['subs-success-behavior'];
	$anypopupSubsSuccessRedirectUrl = @$subscriptionOptions['subs-success-redirect-url'];
	$anypopupSubsSuccessPopupsList = @$subscriptionOptions['subs-success-popups-list'];
	$anypopupSubsFirstNameRequired = @$subscriptionOptions['subs-first-name-required'];
	$anypopupSubsLastNameRequired = @$subscriptionOptions['subs-last-name-required'];
	$anypopupSubsSuccessRedirectNewTab = @$subscriptionOptions['subs-success-redirect-new-tab'];
	$contactFormOptions = json_decode(@$params, true);
	$anypopupContactNameLabel = @$contactFormOptions['contact-name'];
	$anypopupContactNameStatus = @$contactFormOptions['contact-name-status'];
	$anypopupShowFormToTop = @$contactFormOptions['show-form-to-top'];
	$anypopupContactNameRequired = @$contactFormOptions['contact-name-required'];
	$anypopupContactSubjectLabel = @$contactFormOptions['contact-subject'];
	$anypopupContactSubjectStatus = @$contactFormOptions['contact-subject-status'];
	$anypopupContactSubjectRequired = @$contactFormOptions['contact-subject-required'];
	$anypopupContactEmailLabel = @$contactFormOptions['contact-email'];
	$anypopupContactMessageLabel = @$contactFormOptions['contact-message'];
	$anypopupContactValidationMessage = @$contactFormOptions['contact-validation-message'];
	$anypopupContactSuccessMessage = @$contactFormOptions['contact-success-message'];
	$anypopupContactInputsWidth = @$contactFormOptions['contact-inputs-width'];
	$anypopupContactInputsHeight = @$contactFormOptions['contact-inputs-height'];
	$anypopupContactInputsBorderWidth = @$contactFormOptions['contact-inputs-border-width'];
	$anypopupContactTextInputBgcolor = @$contactFormOptions['contact-text-input-bgcolor'];
	$anypopupContactTextBordercolor = @$contactFormOptions['contact-text-bordercolor'];
	$anypopupContactInputsColor = @$contactFormOptions['contact-inputs-color'];
	$anypopupContactPlaceholderColor = @$contactFormOptions['contact-placeholder-color'];
	$anypopupContactBtnWidth = @$contactFormOptions['contact-btn-width'];
	$anypopupContactBtnHeight = @$contactFormOptions['contact-btn-height'];
	$anypopupContactBtnTitle = @$contactFormOptions['contact-btn-title'];
	$anypopupContactBtnProgressTitle = @$contactFormOptions['contact-btn-progress-title'];
	$anypopupContactButtonBgcolor = @$contactFormOptions['contact-button-bgcolor'];
	$anypopupContactButtonColor = @$contactFormOptions['contact-button-color'];
	$anypopupContactAreaWidth = @$contactFormOptions['contact-area-width'];
	$anypopupContactAreaHeight = @$contactFormOptions['contact-area-height'];
	$anypopupContactResize = @$contactFormOptions['anypopup-contact-resize'];
	$anypopupContactValidateEmail = @$contactFormOptions['contact-validate-email'];
	$anypopupContactResiveEmail = @$contactFormOptions['contact-receive-email'];
	$anypopupContactFailMessage = @$contactFormOptions['contact-fail-message'];
	$anypopupContactSuccessBehavior = @$contactFormOptions['contact-success-behavior'];
	$anypopupContactSuccessRedirectUrl = @$contactFormOptions['contact-success-redirect-url'];
	$anypopupContactSuccessPopupsList = @$contactFormOptions['contact-success-popups-list'];
	$anypopupDontShowContentToContactedUser = @$contactFormOptions['dont-show-content-to-contacted-user'];
	$anypopupContactSuccessFrequencyDays = @$contactFormOptions['contact-success-frequency-days'];
	$anypopupContactSuccessRedirectNewTab = @$contactFormOptions['contact-success-redirect-new-tab'];
}

$dataPopupId = @$id;
/* For layze loading get selected data */
if(!isset($id)) {
	$dataPopupId = "-1";
}

/* FREE options default values */
$anypopup = array(
	'escKey'=> true,
	'closeButton' => true,
	'scrolling'=> true,
	'disable-page-scrolling'=> true,
	'scaling'=> false,
	'opacity'=> 0.8,
	'popup-background-opacity'=> 1,
	'reposition' => true,
	'width' => '640px',
	'height' => '480px',
	'popup-dimension-mode' => 'customMode',
	'popup-responsive-dimension-measure' => 'auto',
	'initialWidth' => '300',
	'initialHeight' => '100',
	'maxWidth' => false,
	'maxHeight' => false,
	'overlayClose' => true,
	'reopenAfterSubmission' => false,
	'contentClick'=>false,
	'repetitivePopup' => false,
	'fixed' => false,
	'top' => false,
	'right' => false,
	'bottom' => false,
	'left' => false,
	'duration' => 1,
	'delay' => 0,
	'buttonDelayValue' => 0,
	'theme-close-text' => 'Close',
	'content-click-behavior' => 'close',
	'anypopupTheme3BorderRadius' => 0,
	'popup-z-index' => 9999,
	'popup-content-padding' => 0,
	'fblike-dont-show-share-button' => false,
	'fblike-close-popup-after-like' => false,
	'video-type' => false,
	'video-autoplay' => false,
	'video-fullscreen' => false,
	'closeType' => false,
	'onScrolling' => false,
	'inactivity-timout' => '0',
	'inActivityStatus' => false,
	'video-autoplay' => false,
	'forMobile' => false,
	'openMobile' => false,
	'repetPopup' => false,
	'disablePopup' => false,
	'disablePopupOverlay' => false,
	'redirect-to-new-tab' => true,
	'autoClosePopup' => false,
	'randomPopup' => false,
	'popupOpenSound' => false,
	'popupContentBgImage' => false,
	'popupOpenSoundFile' => ANYPOPUP_APP_POPUP_URL.'/files/lib/popupOpenSound.wav',
	'popupContentBgImageUrl' => '',
	'popupContentBackgroundSize' => 'cover',
	'popupContentBackgroundRepeat' => 'no-repeat',
	'fbStatus' => true,
	'twitterStatus' => true,
	'emailStatus' => true,
	'linkedinStatus' => true,
	'googleStatus' => true,
	'pinterestStatus' => true,
	'anypopupSocialLabel'=>true,
	'roundButtons'=>false,
	'anypopupShareUrl' => '',
	'pushToBottom' => true,
	'allPages' => "all",
	'allPosts' => "all",
	'allCustomPosts' => "all",
	'allPagesStatus' => false,
	'allPostsStatus' => false,
	'allCustomPostsStatus' => false,
	'onceExpiresTime' => 7,
	'popup-appear-number-limit' => 1,
	'repetitivePopupPeriod' => 60,
	'save-cookie-page-level' => false,
	'overlay-custom-classs' => 'anypopup-popup-overlay',
	'content-custom-classs' => 'anypopup-popup-content',
	'countryStatus' => false,
	'anypopup-user-status' => false,
	'allowCountries' => 'allow',
	'loggedin-user' => 'true',
	'anypopupRestrictionExpirationTime' => 365,
	'restrictionCookeSavingLevel' => '',
	'countdownNumbersTextColor' => '',
	'countdownNumbersBgColor' => '',
	'countDownLang' => 'English',
	'popup-timer-status' => false,
	'popup-schedule-status' => false,
	'countdown-position' => true,
	'countdown-autoclose' => true,
	'time-zone' => 'Etc/GMT',
	'due-date' => date('Y-m-d H:i', strtotime(' +1 day')),
	'popup-start-timer' => date('M d y H:i'),
	'schedule-start-time' => date("H:i"),
	'exit-intent-type' => "soft",
	'exit-intent-expire-time' => '1',
	'subs-first-name-status' => true,
	'subs-last-name-status' => true,
	'subscription-email' => 'Email *',
	'subs-first-name' => 'First name',
	'subs-last-name' => 'Last name',
	'subs-button-bgColor' => '#239744',
	'subs-button-color' => '#FFFFFF',
	'subs-text-input-bgColor' => '#FFFFFF',
	'subs-inputs-color' => '#000000',
	'subs-placeholder-color' => '#CCCCCC',
	'subs-text-borderColor' => '#CCCCCC',
	'subs-btn-title' => 'Subscribe',
	'subs-text-height' => '30px',
	'subs-btn-height' => '30px',
	'subs-text-width' => '200px',
	'subs-btn-width' => '200px',
	'subs-text-border-width' => '2px',
	'subs-success-message' =>'You have successfully subscribed to the newsletter',
	'subs-validation-message' => 'This field is required.',
	'subs-btn-progress-title' => 'Please wait...',
	'subs-success-behavior' => 'showMessage',
	'subs-success-redirect-url' => '',
	'subs-success-popups-list' => '',
	'subs-first-name-required' => '',
	'subs-last-name-required' => '',
	'subs-success-redirect-new-tab' => false,
	'contact-name' => 'Name *',
	'contact-name-required' => true,
	'contact-name-status' => true,
	'show-form-to-top' => false,
	'contact-subject-status' => true,
	'contact-subject-required' => true,
	'contact-email' => 'E-mail *',
	'contact-message' => 'Message *',
	'contact-subject' => 'Subject *',
	'contact-success-message' => 'Your message has been successfully sent.',
	'contact-btn-title' => 'Contact',
	'contact-validate-email' => 'Please enter a valid email.',
	'contact-receive-email' => get_option('admin_email'),
	'contact-fail-message' => 'Unable to send.',
	'contact-success-behavior' => 'showMessage',
	'contact-success-redirect-url' => '',
	'contact-success-popups-list' => 0,
	'dont-show-content-to-contacted-user' => '',
	'contact-success-frequency-days' => 365,
	'contact-success-redirect-new-tab' => false
);

$popupProDefaultValues = array(
	'closeType' => false,
	'onScrolling' => false,
	'inactivity-timout' => '0',
	'inActivityStatus' => false,
	'video-autoplay' => false,
	'forMobile' => false,
	'openMobile' => false,
	'repetPopup' => false,
	'disablePopup' => false,
	'disablePopupOverlay' => false,
	'redirect-to-new-tab' => true,
	'autoClosePopup' => false,
	'randomPopup' => false,
	'popupOpenSound' => false,
	'popupContentBgImage' => false,
	'popupOpenSoundFile' => ANYPOPUP_APP_POPUP_URL.'/files/lib/popupOpenSound.wav',
	'popupContentBgImageUrl' => '',
	'popupContentBackgroundSize' => 'cover',
	'popupContentBackgroundRepeat' => 'no-repeat',
	'fbStatus' => true,
	'twitterStatus' => true,
	'emailStatus' => true,
	'linkedinStatus' => true,
	'googleStatus' => true,
	'pinterestStatus' => true,
	'anypopupSocialLabel'=>true,
	'roundButtons'=>false,
	'anypopupShareUrl' => '',
	'pushToBottom' => true,
	'allPages' => "all",
	'allPosts' => "all",
	'allCustomPosts' => "all",
	'allPagesStatus' => false,
	'allPostsStatus' => false,
	'allCustomPostsStatus' => false,
	'onceExpiresTime' => 7,
	'popup-appear-number-limit' => 1,
	'repetitivePopupPeriod' => 60,
	'save-cookie-page-level' => false,
	'overlay-custom-classs' => 'anypopup-popup-overlay',
	'content-custom-classs' => 'anypopup-popup-content',
	'countryStatus' => false,
	'anypopup-user-status' => false,
	'allowCountries' => 'allow',
	'loggedin-user' => 'true',
	'anypopupRestrictionExpirationTime' => 365,
	'restrictionCookeSavingLevel' => '',
	'countdownNumbersTextColor' => '',
	'countdownNumbersBgColor' => '',
	'countDownLang' => 'English',
	'popup-timer-status' => false,
	'popup-schedule-status' => false,
	'countdown-position' => true,
	'countdown-autoclose' => true,
	'time-zone' => 'Etc/GMT',
	'due-date' => date('Y-m-d H:i', strtotime(' +1 day')),
	'popup-start-timer' => date('M d y H:i'),
	'schedule-start-time' => date("H:i"),
	'exit-intent-type' => "soft",
	'exit-intent-expire-time' => '1',
	'subs-first-name-status' => true,
	'subs-last-name-status' => true,
	'subscription-email' => 'Email *',
	'subs-first-name' => 'First name',
	'subs-last-name' => 'Last name',
	'subs-button-bgColor' => '#239744',
	'subs-button-color' => '#FFFFFF',
	'subs-text-input-bgColor' => '#FFFFFF',
	'subs-inputs-color' => '#000000',
	'subs-placeholder-color' => '#CCCCCC',
	'subs-text-borderColor' => '#CCCCCC',
	'subs-btn-title' => 'Subscribe',
	'subs-text-height' => '30px',
	'subs-btn-height' => '30px',
	'subs-text-width' => '200px',
	'subs-btn-width' => '200px',
	'subs-text-border-width' => '2px',
	'subs-success-message' =>'You have successfully subscribed to the newsletter',
	'subs-validation-message' => 'This field is required.',
	'subs-btn-progress-title' => 'Please wait...',
	'subs-success-behavior' => 'showMessage',
	'subs-success-redirect-url' => '',
	'subs-success-popups-list' => '',
	'subs-first-name-required' => '',
	'subs-last-name-required' => '',
	'subs-success-redirect-new-tab' => false,
	'contact-name' => 'Name *',
	'contact-name-required' => true,
	'contact-name-status' => true,
	'show-form-to-top' => false,
	'contact-subject-status' => true,
	'contact-subject-required' => true,
	'contact-email' => 'E-mail *',
	'contact-message' => 'Message *',
	'contact-subject' => 'Subject *',
	'contact-success-message' => 'Your message has been successfully sent.',
	'contact-btn-title' => 'Contact',
	'contact-validate-email' => 'Please enter a valid email.',
	'contact-receive-email' => get_option('admin_email'),
	'contact-fail-message' => 'Unable to send.',
	'contact-success-behavior' => 'showMessage',
	'contact-success-redirect-url' => '',
	'contact-success-popups-list' => 0,
	'dont-show-content-to-contacted-user' => '',
	'contact-success-frequency-days' => 365,
	'contact-success-redirect-new-tab' => false
);

$escKey = anypopupBoolToChecked($anypopup['escKey']);
$closeButton = anypopupBoolToChecked($anypopup['closeButton']);
$scrolling = anypopupBoolToChecked($anypopup['scrolling']);
$disablePageScrolling = anypopupBoolToChecked($anypopup['disable-page-scrolling']);
$scaling = anypopupBoolToChecked($anypopup['scaling']);
$reposition	= anypopupBoolToChecked($anypopup['reposition']);
$overlayClose = anypopupBoolToChecked($anypopup['overlayClose']);
$reopenAfterSubmission = anypopupBoolToChecked($anypopup['reopenAfterSubmission']);
$contentClick = anypopupBoolToChecked($anypopup['contentClick']);
$repetitivePopup = anypopupBoolToChecked($anypopup['repetitivePopup']);
$fblikeDontShowShareButton = anypopupBoolToChecked($anypopup['fblike-dont-show-share-button']);
$fblikeClosePopupAfterLike = anypopupBoolToChecked($anypopup['fblike-close-popup-after-like']);

$buttonDelayValue = $anypopup['buttonDelayValue'];
$contentClickBehavior = $anypopup['content-click-behavior'];
$theme3BorderRadius = $anypopup['anypopupTheme3BorderRadius'];
$popupZIndex = $anypopup['popup-z-index'];
$popupContentPadding = $anypopup['popup-content-padding'];

$closeType = anypopupBoolToChecked($popupProDefaultValues['closeType']);
$onScrolling = anypopupBoolToChecked($popupProDefaultValues['onScrolling']);
$inActivityStatus = anypopupBoolToChecked($popupProDefaultValues['inActivityStatus']);
$userSeperate = anypopupBoolToChecked($popupProDefaultValues['anypopup-user-status']);
$forMobile = anypopupBoolToChecked($popupProDefaultValues['forMobile']);
$openMobile = anypopupBoolToChecked($popupProDefaultValues['openMobile']);
$popupTimerStatus = anypopupBoolToChecked($popupProDefaultValues['popup-timer-status']);
$popupScheduleStatus = anypopupBoolToChecked($popupProDefaultValues['popup-schedule-status']);
$repetPopup = anypopupBoolToChecked($popupProDefaultValues['repetPopup']);
$disablePopup = anypopupBoolToChecked($popupProDefaultValues['disablePopup']);
$disablePopupOverlay = anypopupBoolToChecked($popupProDefaultValues['disablePopupOverlay']);
$autoClosePopup = anypopupBoolToChecked($popupProDefaultValues['autoClosePopup']);
$randomPopup = anypopupBoolToChecked($popupProDefaultValues['randomPopup']);
$popupOpenSound = anypopupBoolToChecked($popupProDefaultValues['popupOpenSound']);
$popupContentBgImage = anypopupBoolToChecked($popupProDefaultValues['popupContentBgImage']);
$fbStatus = anypopupBoolToChecked($popupProDefaultValues['fbStatus']);
$twitterStatus = anypopupBoolToChecked($popupProDefaultValues['twitterStatus']);
$emailStatus = anypopupBoolToChecked($popupProDefaultValues['emailStatus']);
$linkedinStatus = anypopupBoolToChecked($popupProDefaultValues['linkedinStatus']);
$googleStatus = anypopupBoolToChecked($popupProDefaultValues['googleStatus']);
$pinterestStatus = anypopupBoolToChecked($popupProDefaultValues['pinterestStatus']);
$socialLabel = anypopupBoolToChecked($popupProDefaultValues['anypopupSocialLabel']);
$roundButtons = anypopupBoolToChecked($popupProDefaultValues['roundButtons']);
$countdownAutoclose = anypopupBoolToChecked($popupProDefaultValues['countdown-autoclose']);
$shareUrl = $popupProDefaultValues['anypopupShareUrl'];
$pushToBottom = anypopupBoolToChecked($popupProDefaultValues['pushToBottom']);
$allPages = $popupProDefaultValues['allPages'];
$allPosts = $popupProDefaultValues['allPosts'];
$allCustomPosts = $popupProDefaultValues['allCustomPosts'];
$allPagesStatus = anypopupBoolToChecked($popupProDefaultValues['allPagesStatus']);
$allPostsStatus = anypopupBoolToChecked($popupProDefaultValues['allPostsStatus']);
$allCustomPostsStatus = anypopupBoolToChecked($popupProDefaultValues['allCustomPostsStatus']);
$contactNameStatus = anypopupBoolToChecked($popupProDefaultValues['contact-name-status']);
$showFormToTop = anypopupBoolToChecked($popupProDefaultValues['show-form-to-top']);
$subsSuccessRedirectNewTab = anypopupBoolToChecked($popupProDefaultValues['subs-success-redirect-new-tab']);
$contactNameRequired = anypopupBoolToChecked($popupProDefaultValues['contact-name-required']);
$contactSubjectStatus = anypopupBoolToChecked($popupProDefaultValues['contact-subject-status']);
$contactSubjectRequired = anypopupBoolToChecked($popupProDefaultValues['contact-subject-required']);
$saveCookiePageLevel = anypopupBoolToChecked($popupProDefaultValues['save-cookie-page-level']);
$onceExpiresTime = $popupProDefaultValues['onceExpiresTime'];
$popupAppearNumberLimit = $popupProDefaultValues['popup-appear-number-limit'];
$repetitivePopupPeriod = $popupProDefaultValues['repetitivePopupPeriod'];
$countryStatus = anypopupBoolToChecked($popupProDefaultValues['countryStatus']);
$allowCountries = $popupProDefaultValues['allowCountries'];
$logedUser = $popupProDefaultValues['loggedin-user'];
$restrictionExpirationTime = $popupProDefaultValues['anypopupRestrictionExpirationTime'];
$restrictionCookeSavingLevel = anypopupBoolToChecked($popupProDefaultValues['restrictionCookeSavingLevel']);
$countdownNumbersTextColor = $popupProDefaultValues['countdownNumbersTextColor'];
$countdownNumbersBgColor = $popupProDefaultValues['countdownNumbersBgColor'];
$countdownLang = $popupProDefaultValues['countDownLang'];
$countdownPosition = $popupProDefaultValues['countdown-position'];
$timeZone = $popupProDefaultValues['time-zone'];
$dueDate = $popupProDefaultValues['due-date'];
$popupStartTimer = $popupProDefaultValues['popup-start-timer'];
$scheduleStartTime = $popupProDefaultValues['schedule-start-time'];
$inactivityTimout = $popupProDefaultValues['inactivity-timout'];
$exitIntentType = $popupProDefaultValues['exit-intent-type'];
$exitIntentExpireTime = $popupProDefaultValues['exit-intent-expire-time'];
$subsFirstNameStatus = anypopupBoolToChecked($popupProDefaultValues['subs-first-name-status']);
$subsLastNameStatus = anypopupBoolToChecked($popupProDefaultValues['subs-last-name-status']);
$subscriptionEmail = $popupProDefaultValues['subscription-email'];
$subsFirstName = $popupProDefaultValues['subs-first-name'];
$subsLastName = $popupProDefaultValues['subs-last-name'];
$subsButtonBgColor = $popupProDefaultValues['subs-button-bgColor'];
$subsButtonColor = $popupProDefaultValues['subs-button-color'];
$subsInputsColor = $popupProDefaultValues['subs-inputs-color'];
$subsBtnTitle = $popupProDefaultValues['subs-btn-title'];
$subsPlaceholderColor = $popupProDefaultValues['subs-placeholder-color'];
$subsTextHeight = $popupProDefaultValues['subs-text-height'];
$subsBtnHeight = $popupProDefaultValues['subs-btn-height'];
$subsSuccessMessage = $popupProDefaultValues['subs-success-message'];
$subsValidationMessage = $popupProDefaultValues['subs-validation-message'];
$subsTextWidth = $popupProDefaultValues['subs-text-width'];
$subsBtnWidth = $popupProDefaultValues['subs-btn-width'];
$subsBtnProgressTitle = $popupProDefaultValues['subs-btn-progress-title'];
$subsTextBorderWidth = $popupProDefaultValues['subs-text-border-width'];
$subsTextBorderColor = $popupProDefaultValues['subs-text-borderColor'];
$subsTextInputBgColor = $popupProDefaultValues['subs-text-input-bgColor'];
$subsSuccessBehavior = $popupProDefaultValues['subs-success-behavior'];
$subsSuccessPopupsList = $popupProDefaultValues['subs-success-popups-list'];
$subsSuccessRedirectUrl = $popupProDefaultValues['subs-success-redirect-url'];
$subsFirstNameRequired = $popupProDefaultValues['subs-first-name-required'];
$subsLastNameRequired = $popupProDefaultValues['subs-last-name-required'];
$contactName = $popupProDefaultValues['contact-name'];
$contactEmail = $popupProDefaultValues['contact-email'];
$contactMessage = $popupProDefaultValues['contact-message'];
$contactSubject = $popupProDefaultValues['contact-subject'];
$contactSuccessMessage = $popupProDefaultValues['contact-success-message'];
$contactBtnTitle = $popupProDefaultValues['contact-btn-title'];
$contactValidateEmail = $popupProDefaultValues['contact-validate-email'];
$contactResiveEmail = $popupProDefaultValues['contact-receive-email'];
$contactFailMessage = $popupProDefaultValues['contact-fail-message'];
$overlayCustomClasss = $popupProDefaultValues['overlay-custom-classs'];
$contentCustomClasss = $popupProDefaultValues['content-custom-classs'];
$contactSuccessBehavior = $popupProDefaultValues['contact-success-behavior'];
$contactSuccessRedirectUrl = $popupProDefaultValues['contact-success-redirect-url'];
$contactSuccessPopupsList = $popupProDefaultValues['contact-success-popups-list'];
$redirectToNewTab = $popupProDefaultValues['redirect-to-new-tab'];
$dontShowContentToContactedUser = anypopupBoolToChecked($popupProDefaultValues['dont-show-content-to-contacted-user']);
$contactSuccessFrequencyDays = $popupProDefaultValues['contact-success-frequency-days'];
$contactSuccessRedirectNewTab = $popupProDefaultValues['contact-success-redirect-new-tab'];
$popupOpenSoundFile = $popupProDefaultValues['popupOpenSoundFile'];
$popupContentBgImageUrl = $popupProDefaultValues['popupContentBgImageUrl'];
$popupContentBackgroundSize = $popupProDefaultValues['popupContentBackgroundSize'];
$popupContentBackgroundRepeat = $popupProDefaultValues['popupContentBackgroundRepeat'];

function anypopupBoolToChecked($var)
{
	return ($var?'checked':'');
}

function anypopupRemoveOption($option)
{
	global $removeOptions;
	return isset($removeOptions[$option]);
}

$width = $anypopup['width'];
$height = $anypopup['height'];
$popupDimensionMode = $anypopup['popup-dimension-mode'];
$popupResponsiveDimensionMeasure = $anypopup['popup-responsive-dimension-measure'];
$opacityValue = $anypopup['opacity'];
$popupBackgroundOpacity = $anypopup['popup-background-opacity'];
$top = $anypopup['top'];
$right = $anypopup['right'];
$bottom = $anypopup['bottom'];
$left = $anypopup['left'];
$initialWidth = $anypopup['initialWidth'];
$initialHeight = $anypopup['initialHeight'];
$maxWidth = $anypopup['maxWidth'];
$maxHeight = $anypopup['maxHeight'];
$deafultFixed = $anypopup['fixed'];
$defaultDuration = $anypopup['duration'];
$defaultDelay = $anypopup['delay'];
$defaultButtonDelayValue = $anypopup['buttonDelayValue'];
$themeCloseText = $anypopup['theme-close-text'];

$anypopupCloseButton = @anypopupSetChecked($anypopupCloseButton, $closeButton);
$anypopupEscKey = @anypopupSetChecked($anypopupEscKey, $escKey);
$anypopupContentClick = @anypopupSetChecked($anypopupContentClick, $contentClick);
$anypopupOverlayClose = @anypopupSetChecked($anypopupOverlayClose, $overlayClose);
$anypopupReopenAfterSubmission = @anypopupSetChecked($anypopupReopenAfterSubmission, $reopenAfterSubmission);
$anypopupReposition = @anypopupSetChecked($anypopupReposition, $reposition);
$anypopupScrolling = @anypopupSetChecked($anypopupScrolling, $scrolling);
$anypopupDisablePageScrolling = @anypopupSetChecked($anypopupDisablePageScrolling, $disablePageScrolling);
$anypopupScaling = @anypopupSetChecked($anypopupScaling, $scaling);
$anypopupCountdownAutoclose = @anypopupSetChecked($anypopupCountdownAutoclose, $countdownAutoclose);
$anypopupFblikeDontShowShareButton = @anypopupSetChecked($anypopupFblikeDontShowShareButton, $fblikeDontShowShareButton);
$anypopupFblikeClosePopupAfterLike = @anypopupSetChecked($anypopupFblikeClosePopupAfterLike, $fblikeClosePopupAfterLike);

$anypopupCloseType = @anypopupSetChecked($anypopupCloseType, $closeType);
$anypopupOnScrolling = @anypopupSetChecked($anypopupOnScrolling, $onScrolling);
$anypopupInActivityStatus = @anypopupSetChecked($anypopupInActivityStatus, $inActivityStatus);
$anypopupForMobile = @anypopupSetChecked($anypopupForMobile, $forMobile);
$anypopupOpenOnMobile = @anypopupSetChecked($anypopupOpenOnMobile, $openMobile);
$anypopupCookiePageLevel = @anypopupSetChecked($anypopupCookiePageLevel, $saveCookiePageLevel);
$anypopupUserSeperate = @anypopupSetChecked($anypopupUserSeperate, $userSeperate);
$anypopupTimerStatus = @anypopupSetChecked($anypopupTimerStatus, $popupTimerStatus);
$anypopupScheduleStatus = @anypopupSetChecked($anypopupScheduleStatus, $popupScheduleStatus);
$anypopupRepeatPopup = @anypopupSetChecked($anypopupRepeatPopup, $repetPopup);
$anypopupRepetitivePopup = @anypopupSetChecked($anypopupRepetitivePopup, $repetitivePopup);
$anypopupDisablePopup = @anypopupSetChecked($anypopupDisablePopup, $disablePopup);
$anypopupDisablePopupOverlay = @anypopupSetChecked($anypopupDisablePopupOverlay, $disablePopupOverlay);
$anypopupAutoClosePopup = @anypopupSetChecked($anypopupAutoClosePopup, $autoClosePopup);
$anypopupRandomPopup = @anypopupSetChecked($anypopupRandomPopup, $randomPopup);
$anypopupOpenSound = @anypopupSetChecked($anypopupOpenSound, $popupOpenSound);
$anypopupContentBgImage = @anypopupSetChecked($anypopupContentBgImage, $popupContentBgImage);
$anypopupFbStatus = @anypopupSetChecked($anypopupFbStatus, $fbStatus);
$anypopupTwitterStatus = @anypopupSetChecked($anypopupTwitterStatus, $twitterStatus);
$anypopupEmailStatus = @anypopupSetChecked($anypopupEmailStatus, $emailStatus);
$anypopupLinkedinStatus = @anypopupSetChecked($anypopupLinkedinStatus, $linkedinStatus);
$anypopupGoogleStatus = @anypopupSetChecked($anypopupGoogleStatus, $googleStatus);
$anypopupPinterestStatus = @anypopupSetChecked($anypopupPinterestStatus, $pinterestStatus);
$anypopupRoundButtons = @anypopupSetChecked($anypopupRoundButton, $roundButtons);
$anypopupSocialLabel = @anypopupSetChecked($anypopupSocialLabel, $socialLabel);
$anypopupFixed = @anypopupSetChecked($anypopupFixed, $deafultFixed);
$anypopupPushToBottom = @anypopupSetChecked($anypopupPushToBottom, $pushToBottom);
$anypopupRestrictionCookeSavingLevel = @anypopupSetChecked($anypopupRestrictionCookeSavingLevel, $restrictionCookeSavingLevel);
$anypopupSubsFirstNameRequired = @anypopupSetChecked($anypopupSubsFirstNameRequired, $subsFirstNameRequired);
$anypopupSubsLastNameRequired = @anypopupSetChecked($anypopupSubsLastNameRequired, $subsLastNameRequired);
$anypopupSubsSuccessRedirectNewTab = @anypopupSetChecked($anypopupSubsSuccessRedirectNewTab, $subsSuccessRedirectNewTab);
$anypopupContactSuccessRedirectNewTab = @anypopupSetChecked($anypopupContactSuccessRedirectNewTab, $contactSuccessRedirectNewTab);

$anypopupAllPagesStatus = @anypopupSetChecked($anypopupAllPagesStatus, $allPagesStatus);
$anypopupAllPostsStatus = @anypopupSetChecked($anypopupAllPostsStatus, $allPostsStatus);
$anypopupAllCustomPostsStatus = @anypopupSetChecked($anypopupAllCustomPostsStatus, $allCustomPostsStatus);
$anypopupCountdownPosition = @anypopupSetChecked($anypopupCountdownPosition, $countdownPosition);
$anypopupVideoAutoplay = @anypopupSetChecked($anypopupVideoAutoplay, $videoAutoplay);
$anypopupVideoFullscreen = @anypopupSetChecked($anypopupVideoFullscreen, $videoFullscreen);
$anypopupSubsLastNameStatus = @anypopupSetChecked($anypopupSubsLastNameStatus, $subsLastNameStatus);
$anypopupSubsFirstNameStatus = @anypopupSetChecked($anypopupSubsFirstNameStatus, $subsFirstNameStatus);
$anypopupCountryStatus = @anypopupSetChecked($anypopupCountryStatus, $countryStatus);
/* Contact popup otions */
$anypopupContactNameStatus = @anypopupSetChecked($anypopupContactNameStatus, $contactNameStatus);
$anypopupContactNameRequired = @anypopupSetChecked($anypopupContactNameRequired, $contactNameRequired);
$anypopupContactSubjectStatus = @anypopupSetChecked($anypopupContactSubjectStatus, $contactSubjectStatus);
$anypopupContactSubjectRequired = @anypopupSetChecked($anypopupContactSubjectRequired, $contactSubjectRequired);
$anypopupShowFormToTop = @anypopupSetChecked($anypopupShowFormToTop, $showFormToTop);
$anypopupRedirectToNewTab = @anypopupSetChecked($anypopupRedirectToNewTab, $redirectToNewTab);
$anypopupDontShowContentToContactedUser = @anypopupSetChecked($anypopupDontShowContentToContactedUser, $dontShowContentToContactedUser);

function anypopupSetChecked($optionsParam,$defaultOption)
{
	if (isset($optionsParam)) {
		if ($optionsParam == '') {
			return '';
		}
		else {
			return 'checked';
		}
	}
	else {
		return $defaultOption;
	}
}

$anypopupTheme3BorderRadius = @anypopupGetValue($anypopupTheme3BorderRadius, $theme3BorderRadius);
$anypopupOpenSoundFile = @anypopupGetValue($anypopupOpenSoundFile, $popupOpenSoundFile);
$anypopupContentBackgroundSize = @anypopupGetValue($anypopupContentBackgroundSize, $popupContentBackgroundSize);
$anypopupContentBackgroundRepeat = @anypopupGetValue($anypopupContentBackgroundRepeat, $popupContentBackgroundRepeat);
$anypopupContentBgImageUrl = @anypopupGetValue($anypopupContentBgImageUrl, $popupContentBgImageUrl);
$anypopupOpacity = @anypopupGetValue($anypopupOpacity, $opacityValue);
$anypopupBackgroundOpacity = @anypopupGetValue($anypopupBackgroundOpacity, $popupBackgroundOpacity);
$anypopupWidth = @anypopupGetValue($anypopupWidth, $width);
$anypopupHeight = @anypopupGetValue($anypopupHeight, $height);
$anypopupZIndex = @anypopupGetValue($anypopupZIndex, $popupZIndex);
$anypopupContentPadding = @anypopupGetValue($anypopupContentPadding, $popupContentPadding);
$anypopupDimensionMode = @anypopupGetValue($anypopupDimensionMode, $popupDimensionMode);
$anypopupResponsiveDimensionMeasure = @anypopupGetValue($anypopupResponsiveDimensionMeasure, $popupResponsiveDimensionMeasure);
$anypopupInitialWidth = @anypopupGetValue($anypopupInitialWidth, $initialWidth);
$anypopupInitialHeight = @anypopupGetValue($anypopupInitialHeight, $initialHeight);
$anypopupMaxWidth = @anypopupGetValue($anypopupMaxWidth, $maxWidth);
$anypopupMaxHeight = @anypopupGetValue($anypopupMaxHeight, $maxHeight);
$anypopupThemeCloseText = @anypopupGetValue($anypopupThemeCloseText, $themeCloseText);
$duration = @anypopupGetValue($duration, $defaultDuration);
$anypopupOnceExpiresTime = @anypopupGetValue($anypopupOnceExpiresTime, $onceExpiresTime);
$anypopupAppearNumberLimit = @anypopupGetValue($anypopupAppearNumberLimit, $popupAppearNumberLimit);
$anypopupRepetitivePopupPeriod = @anypopupGetValue($anypopupRepetitivePopupPeriod, $repetitivePopupPeriod);
$delay = @anypopupGetValue($delay, $defaultDelay);
$anypopupCloseButtonDelay = @anypopupGetValue($anypopupCloseButtonDelay, $buttonDelayValue);

$anypopupInactivityTimout = @anypopupGetValue($anypopupInactivityTimout, $inactivityTimout);
$anypopupContentClickBehavior = @anypopupGetValue($anypopupContentClickBehavior, $contentClickBehavior);
$anypopupStartTimer = @anypopupGetValue($anypopupStartTimer, $popupStartTimer);
$anypopupFinishTimer = @anypopupGetValue($anypopupFinishTimer, '');
$anypopupScheduleStartTime = @anypopupGetValue($anypopupScheduleStartTime, $scheduleStartTime);
$anypopupDataIframe = @anypopupGetValue($anypopupDataIframe, '');
$anypopupShareUrl = @anypopupGetValue($anypopupShareUrl, $shareUrl);
$anypopupDataHtml = @anypopupGetValue($anypopupDataHtml, '');
$anypopupDataImage = @anypopupGetValue($anypopupDataImage, '');
$anypopupAllowCountries = @anypopupGetValue($anypopupAllowCountries, $allowCountries);
$anypopupAllPages = @anypopupGetValue($anypopupAllPages, $allPages);
$anypopupAllPosts = @anypopupGetValue($anypopupAllPosts, $allPosts);
$anypopupAllCustomPosts = @anypopupGetValue($anypopupAllCustomPosts, $allCustomPosts);
$anypopupLogedUser = @anypopupGetValue($anypopupLogedUser, $logedUser);
$anypopupRestrictionExpirationTime = @anypopupGetValue($anypopupRestrictionExpirationTime, $restrictionExpirationTime);
$anypopupCountdownNumbersTextColor = @anypopupGetValue($anypopupCountdownNumbersTextColor, $countdownNumbersTextColor);
$anypopupCountdownNumbersBgColor = @anypopupGetValue($anypopupCountdownNumbersBgColor, $countdownNumbersBgColor);
$anypopupCountdownLang = @anypopupGetValue($anypopupCountdownLang, $countdownLang);
$anypopupSelectedTimeZone  = @anypopupGetValue($anypopupSelectedTimeZone, $timeZone);
$anypopupDueDate = @anypopupGetValue($anypopupDueDate, $dueDate);
$anypopupExitIntentTpype = @anypopupGetValue($anypopupExitIntentTpype, $exitIntentType);
$anypopupExitIntntExpire = @anypopupGetValue($anypopupExitIntntExpire, $exitIntentExpireTime);
$anypopupSubsTextWidth = @anypopupGetValue($anypopupSubsTextWidth, $subsTextWidth);
$anypopupSubsBtnWidth = @anypopupGetValue($anypopupSubsBtnWidth, $subsBtnWidth);
$anypopupSubsTextInputBgColor = @anypopupGetValue($anypopupSubsTextInputBgColor, $subsTextInputBgColor);
$anypopupSubsButtonBgColor  = @anypopupGetValue($anypopupSubsButtonBgColor, $subsButtonBgColor);
$anypopupSubsTextBorderColor = @anypopupGetValue($anypopupSubsTextBorderColor, $subsTextBorderColor);
$anypopupSubscriptionEmail = @anypopupGetValue($anypopupSubscriptionEmail, $subscriptionEmail);
$anypopupSubsFirstName = @anypopupGetValue($anypopupSubsFirstName, $subsFirstName);
$anypopupSubsLastName = @anypopupGetValue($anypopupSubsLastName, $subsLastName);
$anypopupSubsButtonColor = @anypopupGetValue($anypopupSubsButtonColor, $subsButtonColor);
$anypopupSubsInputsColor = @anypopupGetValue($anypopupSubsInputsColor, $subsInputsColor);
$anypopupSubsBtnTitle = @anypopupGetValue($anypopupSubsBtnTitle, $subsBtnTitle);
$anypopupSubsPlaceholderColor = @anypopupGetValue($anypopupSubsPlaceholderColor, $subsPlaceholderColor);
$anypopupSubsTextHeight = @anypopupGetValue($anypopupSubsTextHeight, $subsTextHeight);
$anypopupSubsBtnHeight = @anypopupGetValue($anypopupSubsBtnHeight, $subsBtnHeight);
$anypopupSuccessMessage = @anypopupGetValue($anypopupSuccessMessage, $subsSuccessMessage);
$anypopupSubsValidateMessage = @anypopupGetValue($anypopupSubsValidateMessage, $subsValidationMessage);
$anypopupSubsBtnProgressTitle = @anypopupGetValue($anypopupSubsBtnProgressTitle, $subsBtnProgressTitle);
$anypopupSubsTextBorderWidth = @anypopupGetValue($anypopupSubsTextBorderWidth, $subsTextBorderWidth);
$anypopupSubsSuccessBehavior = @anypopupGetValue($anypopupSubsSuccessBehavior, $subsSuccessBehavior);
$anypopupSubsSuccessRedirectUrl = @anypopupGetValue($anypopupSubsSuccessRedirectUrl, $subsSuccessRedirectUrl);
$anypopupSubsSuccessPopupsList = @anypopupGetValue($anypopupSubsSuccessPopupsList, $subsSuccessPopupsList);
$anypopupContactNameLabel = @anypopupGetValue($anypopupContactNameLabel, $contactName);
$anypopupContactSubjectLabel = @anypopupGetValue($anypopupContactSubjectLabel, $contactSubject);
$anypopupContactEmailLabel = @anypopupGetValue($anypopupContactEmailLabel, $contactEmail);
$anypopupContactMessageLabel = @anypopupGetValue($anypopupContactMessageLabel, $contactMessage);
$anypopupContactValidationMessage = @anypopupGetValue($anypopupContactValidationMessage, $subsValidationMessage);
$anypopupContactSuccessMessage = @anypopupGetValue($anypopupContactSuccessMessage, $contactSuccessMessage);
$anypopupContactInputsWidth = @anypopupGetValue($anypopupContactInputsWidth, $subsTextWidth);
$anypopupContactInputsHeight = @anypopupGetValue($anypopupContactInputsHeight, $subsTextHeight);
$anypopupContactInputsBorderWidth = @anypopupGetValue($anypopupContactInputsBorderWidth, $subsTextBorderWidth);
$anypopupContactTextInputBgcolor = @anypopupGetValue($anypopupContactTextInputBgcolor, $subsTextInputBgColor);
$anypopupContactTextBordercolor = @anypopupGetValue($anypopupContactTextBordercolor, $subsTextBorderColor);
$anypopupContactInputsColor = @anypopupGetValue($anypopupContactInputsColor, $subsInputsColor);
$anypopupContactPlaceholderColor = @anypopupGetValue($anypopupContactPlaceholderColor, $subsPlaceholderColor);
$anypopupContactBtnWidth = @anypopupGetValue($anypopupContactBtnWidth, $subsBtnWidth);
$anypopupContactBtnHeight = @anypopupGetValue($anypopupContactBtnHeight, $subsBtnHeight);
$anypopupContactBtnTitle = @anypopupGetValue($anypopupContactBtnTitle, $contactBtnTitle);
$anypopupContactBtnProgressTitle = @anypopupGetValue($anypopupContactBtnProgressTitle, $subsBtnProgressTitle);
$anypopupContactButtonBgcolor = @anypopupGetValue($anypopupContactButtonBgcolor, $subsButtonBgColor);
$anypopupContactButtonColor = @anypopupGetValue($anypopupContactButtonColor, $subsButtonColor);
$anypopupContactAreaWidth = @anypopupGetValue($anypopupContactAreaWidth, $subsTextWidth);
$anypopupContactAreaHeight = @anypopupGetValue($anypopupContactAreaHeight, '');
$anypopupContactValidateEmail = @anypopupGetValue($anypopupContactValidateEmail, $contactValidateEmail);
$anypopupContactResiveEmail = @anypopupGetValue($anypopupContactResiveEmail, $contactResiveEmail);
$anypopupContactFailMessage = @anypopupGetValue($anypopupContactFailMessage, $contactFailMessage);
$anypopupOverlayCustomClasss = @anypopupGetValue($anypopupOverlayCustomClasss, $overlayCustomClasss);
$anypopupContentCustomClasss = @anypopupGetValue($anypopupContentCustomClasss, $contentCustomClasss);
$anypopupContactSuccessBehavior = @anypopupGetValue($anypopupContactSuccessBehavior, $contactSuccessBehavior);
$anypopupContactSuccessRedirectUrl = @anypopupGetValue($anypopupContactSuccessRedirectUrl, $contactSuccessRedirectUrl);
$anypopupContactSuccessPopupsList = @anypopupGetValue($anypopupContactSuccessPopupsList, $contactSuccessPopupsList);
$anypopupContactSuccessFrequencyDays = @anypopupGetValue($anypopupContactSuccessFrequencyDays, $contactSuccessFrequencyDays);
$anypopupAllSelectedPages = @anypopupGetValue($anypopupAllSelectedPages, array());
$anypopupAllSelectedPosts = @anypopupGetValue($anypopupAllSelectedPosts, array());
$anypopupAllSelectedCustomPosts = @anypopupGetValue($anypopupAllSelectedCustomPosts, array());

function anypopupGetValue($getedVal,$defValue)
{
	if (!isset($getedVal)) {
		return $defValue;
	}
	else {
		return $getedVal;
	}
}

$radioElements = array(
	array(
		'name'=>'shareUrlType',
		'value'=>'activeUrl',
		'additionalHtml'=>''.'<span>'.'Use active URL'.'</span></span>
							<span class="span-width-static"></span><span class="dashicons dashicons-info scrollingImg sameImageStyle anypopup-active-url"></span><span class="info-active-url samefontStyle">If this option is active Share URL will be current page URL.</span>'
	),
	array(
		'name'=>'shareUrlType',
		'value'=>'shareUrl',
		'additionalHtml'=>''.'<span>'.'Share url'.'</span></span>'.' <input class="input-width-static anypopup-active-url" type="text" name="anypopupShareUrl" value="'.@$anypopupShareUrl.'">'
	)
);

$countriesRadio = array(
	array(
		'name'=>'allowCountries',
		'value'=>'allow',
		'additionalHtml'=>'<span class="countries-radio-text allow-countries">allow</span>',
		'newline' => false
	),
	array(
		'name'=>'allowCountries',
		'value'=>'disallow',
		'additionalHtml'=>'<span class="countries-radio-text">disallow</span>',
		'newline' => true
	)
);

$usersGroup = array(
	array(
		'name'=>'loggedin-user',
		'value'=>'true',
		'additionalHtml'=>'<span id="anypopup-radio-logged-in" class="countries-radio-text allow-countries">logged in</span></label>',
		'newline' => false
	),
	array(
		'name'=>'loggedin-user',
		'value'=>'false',
		'additionalHtml'=>'<span id="anypopup-radio-not-logged-in" class="countries-radio-text">not logged in</span></label>',
		'newline' => true
	)
);

function anypopupCreateRadioElements($radioElements,$checkedValue)
{
	$content = '';
	for ($i = 0; $i < count($radioElements); $i++) {
		$checked = '';
		$radioElement = @$radioElements[$i];
		$name = @$radioElement['name'];
		$label = @$radioElement['label'];
		$value = @$radioElement['value'];
		$additionalHtml = @$radioElement['additionalHtml'];
		if ($checkedValue == $value) {
			$checked = 'checked';
		}
		$content .= '<span  class="liquid-width"><input class="radio-btn-fix" type="radio" name="'.esc_attr($name).'" value="'.esc_attr($value).'" '.esc_attr($checked).'>';
		$content .= $additionalHtml."<br>";
	}
	return $content;
}

$contentClickOptions = array(
	array(
		"title" => "close Popup:",
		"value" => "close",
		"info" => ""
	),
	array(
		"title" => "redirect:",
		"value" => "redirect",
		"info" => ""
	)
);

$ajaxNonce = wp_create_nonce("anypopupAnyPopupPageNonce");
$ajaxNoncePages = wp_create_nonce("anypopupAnyPopupPagesNonce");
$pagesRadio = array(
	array(
		"title" => "show on all pages:",
		"value" => "all",
		"info" => ""
	),
	array(
		"title" => "show on selected pages:",
		"value" => "selected",
		"info" => "",
		"data-attributes" => array(
			"data-name" => ANYPOPUP_POST_TYPE_PAGE,
			"data-popupid" => $dataPopupId,
			"data-loading-number" => 0,
			"data-selectbox-role" => "js-all-pages",
			"data-ajaxNoncePages" => $ajaxNoncePages
		)
	)
);

$postsRadio = array(
	array(
		"title" => "show on all posts:",
		"value" => "all",
		"info" => ""
	),
	array(
		"title" => "show on selected post:",
		"value" => "selected",
		"info" => "",
		"data-attributes" => array(
			"data-name" => ANYPOPUP_POST_TYPE_POST,
			"data-popupid" => $dataPopupId,
			"data-loading-number" => 0,
			"data-selectbox-role" => "js-all-posts",
			"data-ajaxNonce" => $ajaxNonce
		)

	),
	array(
		"title" => "show on selected categories",
		"value" => "allCategories",
		"info" => "",
		"data-attributes" => array(
			"class" => 'js-all-categories',
			"data-ajaxNonce" => $ajaxNonce
		)
	)
);

function getResponsiveData($popupType = '') {
	$responsiveDataAttrs = array(
		"class" => "js-responsive-mode"
	);

	if($popupType == 'iframe' || $popupType == 'video') {
		$responsiveDataAttrs['disabled'] = true;
	}

	$responsiveMode = array(
		array(
			"title" => "Responsive mode:",
			"value" => "responsiveMode",
			"info" => "",
			"data-attributes" => $responsiveDataAttrs
		),
		array(
			"title" => "Custom mode:",
			"value" => "customMode",
			"info" => "",
			"data-attributes" => array(
				"class" => "js-custom-mode"
			)

		)
	);

	return $responsiveMode;
}



$subsSuccessBehavior = array(
	array(
		"title" => "Show success message:",
		"value" => "showMessage",
		"info" => "",
		"data-attributes" => array(
			"class" => "js-subs-success-message"
		)

	),
	array(
		"title" => "Redirect to url:",
		"value" => "redirectToUrl",
		"info" => "",
		"data-attributes" => array(
			"class" => "js-subs-success-redirect"
		)

	),
	array(
		"title" => "Open popup",
		"value" => "openPopup",
		"info" => "",
		"data-attributes" => array(
			"class" => "js-subs-success-redirect"
		)
	),
	array(
		"title" => "Hide popup",
		"value" => "hidePopup",
		"info" => "",
		"data-attributes" => array(
			"class" => ""
		)
	)
);

$customPostsRadio = array(
	array(
		"title" => "show on all custom posts:",
		"value" => "all",
		"info" => ""
	),
	array(
		"title" => "show on selected custom post:",
		"value" => "selected",
		"info" => "",
		"data-attributes" => array(
			"data-name" => 'allCustomPosts',
			"data-popupid" => $dataPopupId,
			"data-loading-number" => 0,
			"data-selectbox-role" => "js-all-custom-posts"
		)

	)
);

function createRadiobuttons($elements, $name, $newLine, $selectedInput, $class)
{
	$str = "";

	foreach ($elements as $key => $element) {
		$breakLine = "";
		$infoIcon = "";
		$title = "";
		$value = "";
		$infoIcon = "";
		$checked = "";

		if(isset($element["title"])) {
			$title = $element["title"];
		}
		if(isset($element["value"])) {
			$value = $element["value"];
		}
		if($newLine) {
			$breakLine = "<br>";
		}
		if(isset($element["info"])) {
			$infoIcon = $element['info'];
		}
		if($element["value"] == $selectedInput) {
			$checked = "checked";
		}
		$attrStr = '';
		if(isset($element['data-attributes'])) {
			foreach ($element['data-attributes'] as $key => $dataValue) {
				$attrStr .= $key.'="'.esc_attr($dataValue).'" ';
			}
		}

		$str .= "<span class=".$class.">".$element['title']."</span>
				<input type=\"radio\" name=".esc_attr($name)." ".$attrStr." value=".esc_attr($value)." $checked>".$infoIcon.$breakLine;
	}

	echo $str;
}

$anypopupEffects = array(
	'No effect' => 'No Effect',
	'anypopuppb-flip' => 'flip',
	'anypopuppb-shake' => 'shake',
	'anypopuppb-wobble' => 'wobble',
	'anypopuppb-swing' => 'swing',
	'anypopuppb-flash' => 'flash',
	'anypopuppb-bounce' => 'bounce',
	'anypopuppb-bounceInRight' => 'bounceInRight',
	'anypopuppb-bounceIn' => 'bounceIn',
	'anypopuppb-pulse' => 'pulse',
	'anypopuppb-rubberBand' => 'rubberBand',
	'anypopuppb-tada' => 'tada',
	'anypopuppb-slideInUp' => 'slideInUp',
	'anypopuppb-jello' => 'jello',
	'anypopuppb-rotateIn' => 'rotateIn',
	'anypopuppb-fadeIn' => 'fadeIn'
);

$anypopupBgSizes = array(
	'auto' => 'Auto',
	'cover' => 'Cover',
	'contain' => 'Contain'
);

$anypopupBgRepeat = array(
	'repeat' => 'Repeat',
	'repeat-x' => 'Repeat x',
	'repeat-y' => 'Repeat y',
	'no-repeat' => 'Not repeat'
);

$anypopupTheme = array(
	'colorbox1.css',
	'colorbox2.css',
	'colorbox3.css',
	'colorbox4.css',
	'colorbox5.css',
	'colorbox6.css'
);

$anypopupFbLikeButtons = array(
	'standard' => 'Standard',
	'box_count' => 'Box with count',
	'button_count' => 'Button with count',
	'button' => 'Button'
);

$anypopupTheme = array(
	'flat' => 'flat',
	'classic' => 'classic',
	'minima' => 'minima',
	'plain' => 'plain'
);

$anypopupResponsiveMeasure = array(
	'auto' => 'Auto',
	'10' => '10%',
	'20' => '20%',
	'30' => '30%',
	'40' => '40%',
	'50' => '50%',
	'60' => '60%',
	'70' => '70%',
	'80' => '80%',
	'90' => '90%',
	'100' => '100%'
);

$anypopupThemeSize = array(
	'8' => '8',
	'10' => '10',
	'12' => '12',
	'14' => '14',
	'16' => '16',
	'18' => '18',
	'20' => '20',
	'24' => '24'
);

$anypopupSocialCount = array(
	'true' => 'True',
	'false' => 'False',
	'inside' => 'Inside'
);

$anypopupCountdownType = array(
	1 => 'DD:HH:MM:SS',
	2 => 'DD:HH:MM'
);

$anypopupCountdownlang = array(
	'English' => 'English',
	'German' => 'German',
	'Spanish' => 'Spanish',
	'Arabic' => 'Arabic',
	'Italian' => 'Italian',
	'Italian' => 'Italian',
	'Dutch' => 'Dutch',
	'Norwegian' => 'Norwegian',
	'Portuguese' => 'Portuguese',
	'Russian' => 'Russian',
	'Swedish' => 'Swedish',
	'Chinese' => 'Chinese'
);

$anypopupTextAreaResizeOptions = array(
	'both' => 'Both',
	'horizontal' => 'Horizontal',
	'vertical' => 'Vertical',
	'none' => 'None',
	'inherit' => 'Inherit'
);

$anypopupWeekDaysArray = array(
	'Mon' => 'Monday',
	'Tue' => 'Tuesday',
	'Wed' => 'Wendnesday',
	'Thu' => 'Thursday',
	'Fri' => 'Friday',
	'Sat' => 'Saturday',
	'Sun' => 'Sunday'
);

if (ANYPOPUP_PKG != ANYPOPUP_PKG_FREE) {
	require_once(ANYPOPUP_APP_POPUP_FILES ."/anypopup_params_arrays.php");
	$popupDefaultData = AnypopupParamsArray::defaultDataArray();
}

function anypopupCreateSelect($options,$name,$selecteOption)
{
	$selected ='';
	$str = "";
	$checked = "";
	if ($name == 'theme' || $name == 'restrictionAction') {

		$popup_style_name = 'popup_theme_name';
		$firstOption = array_shift($options);
		$i = 1;
		foreach ($options as $key) {
			$checked ='';

			if ($key == $selecteOption) {
				$checked = "checked";
			}
			$i++;
			$str .= "<input type='radio' name=\"$name\" value=\"$key\" $checked class='popup_theme_name' anypopupPoupNumber=".$i.">";

		}
		if ($checked == ''){
			$checked = "checked";
		}
		$str = "<input type='radio' name=\"".esc_attr($name)."\" value=\"".esc_attr($firstOption)."\" $checked class='popup_theme_name' anypopupPoupNumber='1'>".$str;
		return $str;
	}
	else {
		@$popup_style_name = ($popup_style_name) ? $popup_style_name : '';
		$str .= "<select name=$name class=$popup_style_name input-width-static >";
		foreach ($options as $key => $option) {

			$selected ='';

			if ($key == $selecteOption) {
				$selected = 'selected';
			}

			$str .= "<option value='".esc_attr($key)."' ".$selected." >$option</potion>";
		}

		$str .="</select>" ;
		return $str;

	}

}

if(!ANYPOPUP_SHOW_POPUP_REVIEW) {
	//echo ANYPOPUPFunctions::addReview();
}

if (isset($_GET['saved']) && $_GET['saved']==1) {
	echo '<div id="default-message" class="updated notice notice-success is-dismissible" ><p>Popup updated.</p></div>';
}
if (isset($_GET["titleError"])): ?>
	<div class="error notice" id="title-error-message">
		<p>Invalid Title</p>
	</div>
<?php endif; ?>
	<form method="POST" action="<?php echo ANYPOPUP_APP_POPUP_ADMIN_URL;?>admin-post.php" id="add-form">
		<?php
			if(function_exists('wp_nonce_field')) {
				wp_nonce_field('anypopupAnyPopupSave');
			}
		?>
		<input type="hidden" name="action" value="<?php echo $currentActionName;?>">
		<div class="crud-wrapper">
			<div class="cereate-title-wrapper">
				<div class="anypopup-title-crud">
					<?php if (isset($id)): ?>
						<h2>Edit popup</h2>
					<?php else: ?>
						<h2>Create new popup</h2>
					<?php endif; ?>
	                <?php $pageUrl = AnypopupGetData::getPageUrl(); ?>
                </div>
                <div class="button-wrapper">
                	<div class="anypopup-tooltip">
                		<input type="submit" id="anypopup-save-button" class="button-primary" value="<?php echo 'Save Changes'; ?>">
                		<?php if( !empty($pageUrl)): ?>
							<input type="button" class="anypopup-popup-preview button-primary anypopup-popup-general-option" data-page-url="<?php echo $pageUrl; ?>" value="Preview">
						<?php endif; ?>
                		<span class="anypopup-tooltip-text">
                			Liked the preview of your popup?
                			<a href="https://sygnoos.ladesk.com/377214-How-to-insert-popups-on-a-pagepost">Don't forget to insert it in any post/page</a>.
                		</span>
                	</div>
	               

                </div>
            </div>
            <div class="clear"></div>
            <div class="general-wrapper">
                <div id="titlediv">
                    <div id="titlewrap">
                        <input  id="title" class="anypopup-js-popup-title" type="text" name="title" size="30" value="<?php echo esc_attr(@$title)?>" spellcheck="true" autocomplete="off" required = "required"  placeholder='Enter title here'>
                    </div>
                </div>
                <div class="anypopup-full-width">
                    <div id="anypopup-general">
                        <div id="post-body" class="metabox-holder columns-2">
                            <div id="postbox-container-2" class="postbox-container">
                                <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                                    <div class="postbox anypopup_general_postbox anypopupSameWidthPostBox" style="display: block;">
                                        <div class="handlediv generalTitle" title="Click to toggle"><br></div>
                                        <h3 class="hndle ui-sortable-handle generalTitle" style="cursor: pointer"><span>General</span></h3>
                                        <div class="generalContent anypopupSameWidthPostBox">
											<?php require_once($popupFilesPath."/main_section/".$popupType.".php");?>
											<input type="hidden" name="type" value="<?php echo $popupType;?>">
											
											<?php //echo  anypopupCreateSelect($anypopupTheme,'theme',esc_html(@$anypopupColorboxTheme));?>
											<div class="theme1 anypopup-hide"></div>
											<div class="theme2 anypopup-hide"></div>
											<div class="theme3 anypopup-hide"></div>
											<div class="theme4 anypopup-hide"></div>
											<div class="theme5 anypopup-hide"></div>
											<div class="theme6 anypopup-hide"></div>
											<div class="anypopup-popup-theme-3 themes-suboptions anypopup-hide">
												<span class="liquid-width">Border color:</span>
												<div id="color-picker"><input  class="anypopupOverlayColor" id="anypopupOverlayColor" type="text" name="anypopupTheme3BorderColor" value="<?php echo esc_attr(@$anypopupTheme3BorderColor); ?>" /></div>
												<br><span class="liquid-width">Border radius:</span>
												<input class="input-width-percent" type="number" min="0" max="50" name="anypopupTheme3BorderRadius" value="<?php echo esc_attr(@$anypopupTheme3BorderRadius); ?>">
												<span class="span-percent">%</span>
											</div>
											<div class="anypopup-popup-theme-4 themes-suboptions anypopup-hide">
												<span class="liquid-width">Close button text:</span>
												<input type="text" name="theme-close-text" value="<?php echo esc_attr($anypopupThemeCloseText);?>">
											</div>
										</div>
									</div>

								</div>
							</div>
						</div>
					</div>
					
					
					<div id="effect">
						<div id="post-body" class="metabox-holder columns-2">
							<div id="postbox-container-2" class="postbox-container">
								<div id="normal-sortables" class="meta-box-sortables ui-sortable">
									<div class="postbox anypopup_effect_postbox anypopupSameWidthPostBox" style="display: block;">
										<div class="handlediv effectTitle" title="Click to toggle"><br></div>
										<h3 class="hndle ui-sortable-handle effectTitle" style="cursor: pointer"><span>Effects</span></h3>
										<div class="effectsContent">
											<span class="liquid-width">Effect type:</span>
											<?php echo  anypopupCreateSelect($anypopupEffects,'effect',esc_html(@$effect));?>
											<span class="js-preview-effect"></span>
											<div class="effectWrapper"><div id="effectShow" ></div></div>

											<span  class="liquid-width">Effect duration:</span>
											<input class="input-width-static" type="text" name="duration" value="<?php echo esc_attr($duration); ?>" pattern = "\d+" title="It must be number" /><span class="dashicons dashicons-info contentClick infoImageDuration sameImageStyle"></span><span class="infoDuration samefontStyle">Specify how long the popup appearance animation should take (in sec).</span></br>

											<span class="liquid-width">Popup open sound:</span>
											<div class="input-width-static anypopup-display-inline">
												<input class="input-width-static js-checkbox-sound-option" type="checkbox" name="popupOpenSound" <?php echo $anypopupOpenSound;?>></div><span class="dashicons dashicons-info repeatPopup same-image-style"></span><span class="infoSelectRepeat samefontStyle">If this option enabled a sound will play after popup opened.Sound option is not available on mobile devices, as there are restrictions on sound auto-play options for mobile devices.</span><br>
											<div class="acordion-main-div-content js-checkbox-sound-option-wrapper">
												<div class="sound-uploader-wrapper">
													<div class="liquid-width-div anypopup-vertical-top"><input id="js-upload-open-sound-button" class="button" type="button" value="Change the sound">
														<button data-default-song="<?php echo $popupOpenSoundFile; ?> " id="reset-to-default" class="button">Reset</button>
													</div>
													<input class="input-width-static anypopup-margin-top-0" id="js-upload-open-sound" type="text" size="36" name="popupOpenSoundFile" value="<?php echo esc_attr($anypopupOpenSoundFile); ?>" required readonly>
													<span class="dashicons dashicons-controls-volumeon anypopup-preview-sound"></span>
												</div>
											</div>

											<span  class="liquid-width">Popup opening delay:</span>
											<input class="input-width-static" type="text" name="delay" value="<?php echo esc_attr($delay);?>"  pattern = "\d+" title="It must be number"/><span class="dashicons dashicons-info contentClick infoImageDelay sameImageStyle"></span><span class="infoDelay samefontStyle">Specify how long the popup appearance should be delayed after loading the page (in sec).</span></br>
										</div>
									</div>

								</div>
							</div>
						</div>
					</div>
					<?php
						require_once($popupFilesPath."/options_section/".$popupType.".php");
						echo $extensionManagerObj->optionsInclude($popupType);
					?>
				</div>
				<div class="anypopup-full-width">
					<div id="right-main">
						<div id="dimentions">
							<div id="post-body" class="metabox-holder columns-2">
								<div id="postbox-container-2" class="postbox-container">
									<div id="normal-sortables" class="meta-box-sortables ui-sortable">
										<div class="postbox anypopup_dimention_postbox anypopupSameWidthPostBox" style="display: block;">
											<div class="handlediv dimentionsTitle" title="Click to toggle"><br></div>
											<h3 class="hndle ui-sortable-handle dimentionsTitle" style="cursor: pointer"><span>Dimensions</span></h3>
											<div class="dimensionsContent">
												<div class="anypopup-radio-option-behavior">
													<?php $responsiveMode = getResponsiveData($popupType);?>
													<?php createRadiobuttons($responsiveMode, 'popup-dimension-mode', true, esc_html($anypopupDimensionMode), "liquid-width");?>
												</div>
												<div class="js-accordion-responsiveMode js-radio-accordion anypopup-accordion-content">
													<span class="liquid-width">size</span>
													<?php echo  anypopupCreateSelect($anypopupResponsiveMeasure,'popup-responsive-dimension-measure',esc_html(@$anypopupResponsiveDimensionMeasure));?>
												</div>
												<div class="js-accordion-customMode js-radio-accordion anypopup-accordion-content">
													<span class="liquid-width">Width:</span>
													<input class="input-width-static" type="text" name="width" value="<?php echo esc_attr($anypopupWidth); ?>"  pattern = "\d+(([px]+|%)|)" title="It must be number  + px or %" /><img class='errorInfo' src="<?php echo plugins_url('img/info-error.png', dirname(__FILE__).'../') ?>"><span class="validateError">It must be a number + px or %</span><br>
													<span class="liquid-width">Height:</span>
													<input class="input-width-static" type="text" name="height" value="<?php echo esc_attr($anypopupHeight);?>" pattern = "\d+(([px]+|%)|)" title="It must be number  + px or %" /><img class='errorInfo' src="<?php echo plugins_url('img/info-error.png', dirname(__FILE__).'../') ?>"><span class="validateError">It must be a number + px or %</span><br>
													<span class="liquid-width">Initial width:</span>
													<input class="input-width-static" type="text" name="initialWidth" value="<?php echo esc_attr($anypopupInitialWidth);?>"  pattern = "\d+(([px]+|%)|)" title="It must be number  + px or %" /><img class='errorInfo' src="<?php echo plugins_url('img/info-error.png', dirname(__FILE__).'../') ?>"><span class="validateError">It must be a number + px or %</span><br>
													<span class="liquid-width">Initial height:</span>
													<input class="input-width-static" type="text" name="initialHeight" value="<?php echo esc_attr($anypopupInitialHeight);?>"  pattern = "\d+(([px]+|%)|)" title="It must be number  + px or %" /><img class='errorInfo' src="<?php echo plugins_url('img/info-error.png', dirname(__FILE__).'../') ?>"><span class="validateError">It must be a number + px or %</span><br>
												</div>
												<span class="liquid-width">Max width:</span>
												<input class="input-width-static" type="text" name="maxWidth" value="<?php echo esc_attr($anypopupMaxWidth);?>"  pattern = "\d+(([px]+|%)|)" title="It must be number  + px or %" /><img class='errorInfo' src="<?php echo plugins_url('img/info-error.png', dirname(__FILE__).'../') ?>"><span class="validateError">It must be a number + px or %</span><br>
												<span class="liquid-width">Max height:</span>
												<input class="input-width-static" type="text" name="maxHeight" value="<?php echo esc_attr(@$anypopupMaxHeight);?>"   pattern = "\d+(([px]+|%)|)" title="It must be number  + px or %" /><img class='errorInfo' src="<?php echo plugins_url('img/info-error.png', dirname(__FILE__).'../') ?>"><span class="validateError">It must be a number + px or %</span><br>
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>
						<div id="options">
							<div id="post-body" class="metabox-holder columns-2">
								<div id="postbox-container-2" class="postbox-container">
									<div id="normal-sortables" class="meta-box-sortables ui-sortable">
										<div class="postbox anypopup_options_postbox anypopupSameWidthPostBox" style="display: block;">
											<div class="handlediv optionsTitle" title="Click to toggle"><br></div>
											<h3 class="hndle ui-sortable-handle optionsTitle" style="cursor: pointer"><span>Options</span></h3>
											<div class="optionsContent">
												<span class="liquid-width">Dismiss on &quot;esc&quot; key:</span><input class="input-width-static" type="checkbox" name="escKey"  <?php echo $anypopupEscKey;?>/>
												<span class="dashicons dashicons-info escKeyImg sameImageStyle"></span><span class="infoEscKey samefontStyle">The popup will be dismissed when user presses on 'esc' key.</span></br>

												<span class="liquid-width" id="createDescribeClose">Show &quot;close&quot; button:</span><input class="input-width-static js-checkbox-acordion" type="checkbox" name="closeButton" <?php echo $anypopupCloseButton;?> />
												<span class="dashicons dashicons-info CloseImg sameImageStyle"></span><span class="infoCloseButton samefontStyle">The popup will contain 'close' button.</span><br>

												<div class="acordion-main-div-content">
													<span class="liquid-width" style="margin-left: 10px;">&quot;close&quot; button delay:</span>
													<input class="input-width-static anypopup-close-button-delay" type="number" min="0" name="buttonDelayValue" value="<?php echo esc_attr($anypopupCloseButtonDelay);?>" title="It must be number"/>
													<span class="dashicons dashicons-info contentClick infoImageDelay sameImageStyle"></span>
													<span class="infoDelay samefontStyle">Add seconds after which the close button will appear.If no seconds are mentioned, the close button will be shown by default.</span></br>
												</div>

												<span class="liquid-width">Enable content scrolling:</span><input class="input-width-static" type="checkbox" name="scrolling" <?php echo $anypopupScrolling;?> />
												<span class="dashicons dashicons-info scrollingImg sameImageStyle"></span><span class="infoScrolling samefontStyle">If the content is larger than the specified dimensions, then the content will be scrollable.</span><br>

												<span class="liquid-width">Disable page scrolling:</span><input class="input-width-static" type="checkbox" name="disable-page-scrolling" <?php echo $anypopupDisablePageScrolling; ?>>
												<span class="dashicons dashicons-info scrollingImg sameImageStyle"></span><span class="infoScrolling samefontStyle">If this option is enabled, the page scrolling will be disabled when the popup is open.</span><br>

												<span class="liquid-width">Enable reposition:</span><input class="input-width-static" type="checkbox" name="reposition" <?php echo $anypopupReposition;?> />
												<span class="dashicons dashicons-info repositionImg sameImageStyle"></span><span class="infoReposition samefontStyle">The popup will be resized/repositioned automatically when window is being resized.</span><br>

												<span class="liquid-width">Enable scaling:</span><input class="input-width-static" type="checkbox" name="scaling" <?php echo $anypopupScaling;?> />
												<span class="dashicons dashicons-info scrollingImg sameImageStyle"></span><span class="infoScaling samefontStyle">Resize popup according to screen size</span><br>

												<span class="liquid-width">Dismiss on overlay click:</span><input class="input-width-static" type="checkbox" name="overlayClose" <?php echo $anypopupOverlayClose;?> />
												<span class="dashicons dashicons-info overlayImg sameImageStyle"></span><span class="infoOverlayClose samefontStyle">The popup will be dismissed when user clicks beyond of the popup area.</span><br>

												<?php if(!anypopupRemoveOption('contentClick')): ?>
												<span class="liquid-width">Dismiss on content click:</span><input class="input-width-static js-checkbox-contnet-click" type="checkbox" name="contentClick" <?php echo $anypopupContentClick;?> />
												<span class="dashicons dashicons-info contentClick sameImageStyle"></span><span class="infoContentClick samefontStyle">The popup will be dismissed when user clicks inside popup area.</span><br>

												<div class="anypopup-hide anypopup-full-width js-content-click-wrraper">
													<?php echo createRadiobuttons($contentClickOptions, "content-click-behavior", true, esc_html($anypopupContentClickBehavior), "liquid-width"); ?>
													<div class="anypopup-hide js-readio-buttons-acordion-content anypopup-full-width">
														<span class="liquid-width">URL:</span><input class="input-width-static" type="text" name='click-redirect-to-url' value="<?php echo esc_attr(@$anypopupClickRedirectToUrl); ?>">
														<span class="liquid-width">redirect to new tab:</span><input type="checkbox" name="redirect-to-new-tab" <?php echo $anypopupRedirectToNewTab; ?> >
													</div>
												</div>
												<?php endif;?>

												<span class="liquid-width">Reopen after form submission:</span><input class="input-width-static" type="checkbox" name="reopenAfterSubmission" <?php echo $anypopupReopenAfterSubmission;?> />
												<span class="dashicons dashicons-info overlayImg sameImageStyle"></span><span class="infoReopenSubmiting samefontStyle">If checked, the popup will reopen after form submission.</span><br>

	                                            <?php if(!anypopupRemoveOption('showOnlyOnce')): ?>
		                                            <span class="liquid-width">Show popup this often:</span><input class="input-width-static js-checkbox-acordion" id="js-popup-only-once" type="checkbox" name="repeatPopup" <?php echo $anypopupRepeatPopup;?>>
		                                            <span class="dashicons dashicons-info repeatPopup same-image-style"></span><span class="infoSelectRepeat samefontStyle">Show the popup to a user only once.</span><br>
		                                            <div class="acordion-main-div-content js-popup-only-once-content">
			                                            <span class="liquid-width">show popup</span><input class="before-scroling-percent" type="number" min="1" name="popup-appear-number-limit" value="<?php echo esc_attr(@$anypopupAppearNumberLimit); ?>">
			                                            <span class="span-percent">time(s) for same user</span><br>
			                                            <span class="liquid-width">expire time</span><input class="before-scroling-percent improveOptionsstyle" type="number" min="1" name="onceExpiresTime" value="<?php echo esc_attr(@$anypopupOnceExpiresTime); ?>">
			                                            <span class="span-percent">days</span><br>
			                                            <span class="liquid-width">page level cookie saving</span>
			                                            <input type="checkbox" name="save-cookie-page-level" <?php echo $anypopupCookiePageLevel; ?>>
			                                            <span class="dashicons dashicons-info repeatPopup same-image-style"></span><span class="infoSelectRepeat samefontStyle">If this option is checked popup's cookie will be saved for a current page.By default cookie is set for all site.</span>
		                                            </div>
	                                            <?php endif;?>

	                                            <?php if(!anypopupRemoveOption('repetitivePopup')): ?>
		                                            <span class="liquid-width">Repetitive popup:</span><input class="input-width-static js-checkbox-acordion" id="js-popup-only-once" type="checkbox" name="repetitivePopup" <?php echo $anypopupRepetitivePopup;?>>
		                                            <span class="dashicons dashicons-info repeatPopup same-image-style"></span><span class="infoSelectRepeat samefontStyle">If this option enabled the same popup will be opened after each X seconds you have defined after the closing.</span><br>
		                                            <div class="acordion-main-div-content js-popup-only-once-content">
			                                            <span class="liquid-width">show popup</span>
			                                            <input type="number" class="before-scroling-percent" name="repetitivePopupPeriod" min="10" value="<?php echo esc_attr($anypopupRepetitivePopupPeriod); ?>">
			                                            <span class="span-percent">after X seconds</span>
		                                            </div>
	                                            <?php endif;?>

	                                            <?php if(!anypopupRemoveOption('popupContentBgImage')): ?>
		                                            <span class="liquid-width">Popup background image:</span><input class="input-width-static js-popup-content-bg-image" type="checkbox" name="popupContentBgImage" <?php echo $anypopupContentBgImage;?>><span class="dashicons dashicons-info repeatPopup same-image-style"></span><span class="infoSelectRepeat samefontStyle">Enable this option if you need to have background image for popup.</span><br>
		                                            <div class="acordion-main-div-content js-popup-content-bg-image-wrapper">
			                                            <span  class="liquid-width">Background size:</span>
			                                            <?php echo  anypopupCreateSelect($anypopupBgSizes,'popupContentBackgroundSize',esc_html(@$anypopupContentBackgroundSize));?>
			                                            <span  class="liquid-width">Background repeat:</span>
			                                            <?php echo  anypopupCreateSelect($anypopupBgRepeat,'popupContentBackgroundRepeat',esc_html(@$anypopupContentBackgroundRepeat));?>

			                                            <div class="anypopup-wp-editor-container">
				                                            <div class="liquid-width-div anypopup-vertical-top">
					                                            <input id="js-upload-image-button" class="button popup-content-bg-image-btn" type="button" value="Select image">
				                                            </div>
				                                            <input class="input-width-static popup-content-bg-image-url" id="js-upload-image" type="text" size="36" name="popupContentBgImageUrl" value="<?php echo esc_attr($anypopupContentBgImageUrl); ?>" >
				                                            <span class="liquid-width-div"></span>
				                                            <div class="show-image-contenier popup-content-bg-image-preview">
					                                            <span class="no-image">(No image selected)</span>
				                                            </div>
			                                            </div>

		                                            </div>
	                                            <?php endif; ?>

                                                <span class="liquid-width">Change overlay color:</span><div id="color-picker"><input  class="anypopupOverlayColor" id="anypopupOverlayColor" type="text" name="anypopupOverlayColor" value="<?php echo esc_attr(@$anypopupOverlayColor); ?>" /></div><br>

                                                <span class="liquid-width">Change background color:</span><div id="color-picker"><input  class="anypopupOverlayColor" id="anypopupOverlayColor" type="text" name="anypopup-content-background-color" value="<?php echo esc_attr(@$anypopupContentBackgroundColor); ?>" /></div><br>

	                                            <span class="liquid-width">Background opacity:</span>
	                                            <div class="slider-wrapper">
		                                            <input type="text" class="js-popup-content-opacity" value="<?php echo esc_attr($anypopupBackgroundOpacity);?>" rel="<?php echo esc_attr($anypopupBackgroundOpacity);?>" name="popup-background-opacity">
		                                            <div id="js-popup-content-opacity" data-init="false" class="display-box"></div>
	                                            </div><br>

                                                <span class="liquid-width" id="createDescribeOpacitcy">Background overlay opacity:</span>
	                                            <div class="slider-wrapper">
                                                    <input type="text" class="js-decimal" value="<?php echo esc_attr($anypopupOpacity);?>" rel="<?php echo esc_attr($anypopupOpacity);?>" name="opacity"/>
                                                    <div id="js-decimal" data-init="false" class="display-box"></div>
                                                </div><br>

                                                <span class="liquid-width">Overlay custom class:</span><input class="input-width-static" type="text" name="anypopupOverlayCustomClasss" value="<?php echo esc_attr(@$anypopupOverlayCustomClasss);?>">
                                                <br>

                                                <span class="liquid-width">Content custom class:</span><input class="input-width-static" type="text" name="anypopupContentCustomClasss" value="<?php echo esc_attr(@$anypopupContentCustomClasss);?>">
                                                <br>

	                                            <span class="liquid-width">Popup z-index:</span><input class="input-width-static" type="number" name="popup-z-index" value="<?php echo esc_attr($anypopupZIndex);?>">
                                                <br>

												<?php if (!anypopupRemoveOption('popup-content-padding')): ?>
												<span class="liquid-width">Content padding:</span><input class="input-width-static" type="number" name="popup-content-padding" value="<?php echo esc_attr($anypopupContentPadding);?>">
												<br>
												<?php endif; ?>

												<span  class="liquid-width" id="createDescribeFixed">Popup location:</span><input class="input-width-static js-checkbox-acordion" type="checkbox" name="popupFixed" <?php echo $anypopupFixed;?> />
												<div class="js-popop-fixeds">
													<span class="fix-wrapper-style" >&nbsp;</span>
													<div class="fixed-wrapper">
														<div class="js-fixed-position-style" id="fixed-position1" data-anypopupvalue="1"></div>
														<div class="js-fixed-position-style" id="fixed-position2"data-anypopupvalue="2"></div>
														<div class="js-fixed-position-style" id="fixed-position3" data-anypopupvalue="3"></div>
														<div class="js-fixed-position-style" id="fixed-position4" data-anypopupvalue="4"></div>
														<div class="js-fixed-position-style" id="fixed-position5" data-anypopupvalue="5"></div>
														<div class="js-fixed-position-style" id="fixed-position6" data-anypopupvalue="6"></div>
														<div class="js-fixed-position-style" id="fixed-position7" data-anypopupvalue="7"></div>
														<div class="js-fixed-position-style" id="fixed-position8" data-anypopupvalue="8"></div>
														<div class="js-fixed-position-style" id="fixed-position9" data-anypopupvalue="9"></div>
													</div>
												</div>
												<input type="hidden" name="fixedPostion" class="js-fixed-postion" value="<?php echo esc_attr(@$anypopupFixedPostion);?>">
											</div>
										</div>
									
									</div>
								</div>
							</div>
						</div>
						
						
						
						
					</div>
				</div>
				<div class="clear"></div>
				<?php
				$isActivePopup = AnypopupGetData::isActivePopup(@$id);
				if(!@$id) $isActivePopup = 'checked';
				?>
				<input class="anypopup-hide-element" name="isActiveStatus" data-switch-id="'.$id.'" type="checkbox" <?php echo $isActivePopup; ?> >
				<input type="hidden" class="button-primary" value="<?php echo esc_attr(@$id);?>" name="hidden_popup_number" />
			</div>
		</div>
	</form>
<?php
//ANYPOPUPFunctions::showInfo();
