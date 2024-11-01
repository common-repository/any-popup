<?php
/**
* Plugin Name: Any Popup - Popup Forms, Optins & Ads
* Plugin URI: 
* Description: This plugin is made for layman users easy to use. You can create all types of popup image, html, video etc. what ever you want.
* Version: 1.0
* Author: Jcodex
* Author URI: https://jcodex.com
* License: GPLv2
*/

// Create a helper function for easy SDK access.
function ap_fs() {
    global $ap_fs;

    if ( ! isset( $ap_fs ) ) {
        // Include Freemius SDK.
        require_once dirname(__FILE__) . '/freemius/start.php';

        $ap_fs = fs_dynamic_init( array(
            'id'                  => '1848',
            'slug'                => 'any-popup',
            'type'                => 'plugin',
            'public_key'          => 'pk_3a6c472640e7a49715088606218bc',
            'is_premium'          => false,
            'has_addons'          => false,
            'has_paid_plans'      => true,
            'menu'                => array(
                'slug'           => 'any_popup',
                'first-path'     => 'admin.php?page=any_popup',
                'support'        => false,
            ),
            // Set the SDK to work in a sandbox mode (for development & testing).
            // IMPORTANT: MAKE SURE TO REMOVE SECRET KEY BEFORE DEPLOYMENT.
            'secret_key'          => 'sk_9f*T0;l<PHTWNOC:^:1G:?{l1G8Pr',
        ) );
    }

    return $ap_fs;
}

// Init Freemius.
ap_fs();
// Signal that SDK was initiated.
do_action( 'ap_fs_loaded' );

require_once(dirname(__FILE__)."/config.php");
require_once(ANYPOPUP_APP_POPUP_HELPERS .'/ANYPOPUPHelper_functions.php');
ANYPOPUPHelperFunctions::checkRequirements();

require_once(ANYPOPUP_APP_POPUP_PATH ."/classes/ANYPOPUPExtensionManager.php");
require_once(ANYPOPUP_APP_POPUP_PATH . "/classes/ANYPOPUPExtensionsConnector.php");
require_once(ANYPOPUP_APP_POPUP_CLASSES .'/ANYPOPUPAnyPopupMain.php');

$mainPopupObj = new ANYPOPUPAnyPopupMain();
$mainPopupObj->init();

require_once(ANYPOPUP_APP_POPUP_CLASSES .'/AnypopupRegistry.php');
require_once(ANYPOPUP_APP_POPUP_CLASSES .'/ANYPOPUP.php');
require_once(ANYPOPUP_APP_POPUP_CLASSES .'/ANYPOPUPExtension.php');
require_once(ANYPOPUP_APP_POPUP_FILES .'/anypopup_functions.php');
require_once(ANYPOPUP_APP_POPUP_HELPERS .'/anypopup_Integrate_external_settings.php');
require_once(ANYPOPUP_APP_POPUP_HELPERS .'/AnyPopupGetData.php');

require_once(ANYPOPUP_APP_POPUP_CLASSES .'/ANYPOPUPInstaller.php'); //cretae tables

if (ANYPOPUP_PKG > ANYPOPUP_PKG_FREE) {
	require_once( ANYPOPUP_APP_POPUP_CLASSES .'/PopupProInstaller.php'); //uninstall tables
	require_once(ANYPOPUP_APP_POPUP_FILES ."/anypopup_pro.php"); // Pro functions
}
require_once(ANYPOPUP_APP_POPUP_PATH .'/style/anypopup_style.php' ); //include our css file
require_once(ANYPOPUP_APP_POPUP_JS .'/anypopup_javascript.php' ); //include our js file
require_once(ANYPOPUP_APP_POPUP_FILES .'/anypopup_page_selection.php' );  // include here in page  button for select popup every page

register_activation_hook(__FILE__, 'anypopupActivate');

add_action('wpmu_new_blog', 'anypopupNewBlogPopup', 10, 6);

function anypopupNewBlogPopup()
{
	AnypopupInstaller::install();
	if (ANYPOPUP_PKG > ANYPOPUP_PKG_FREE) {
		
		PopupProInstaller::install();
	}
}

function anypopupActivate()
{
	update_option('ANYPOPUP_VERSION', ANYPOPUP_VERSION);
	AnypopupInstaller::install();
	if ( ANYPOPUP_PKG > ANYPOPUP_PKG_FREE ) {
		try {
			PopupProInstaller::addExtensionToPluginSection();
		}
		catch (Exception $e) {
			echo $e->getMessage();
		}
		PopupProInstaller::install();
	}

	$extensionConnectionObj = new ANYPOPUPExtensionsConnector();
	$extensionConnectionObj->activate(true);
}

function anypopupRegisterScripts()
{
	ANYPOPUP::$registeredScripts = true;
	wp_register_style('anypopup_animate', ANYPOPUP_APP_POPUP_URL . '/style/animate.css');
	wp_enqueue_style('anypopup_animate');
	wp_register_script('anypopup_frontend', ANYPOPUP_APP_POPUP_URL . '/js/anypopup_frontend.js', array('jquery', 'anypopup_resize'), ANYPOPUP_VERSION);
	wp_enqueue_script('anypopup_frontend');
	wp_localize_script('anypopup_frontend', 'ANYPOPUPParams',AnyPopupConfig::getFrontendScriptLocalizedData());
	wp_register_script('anypopup_resize', ANYPOPUP_APP_POPUP_URL . '/js/anypopup_resize.js', array('jquery'), ANYPOPUP_VERSION);
	wp_enqueue_script('anypopup_resize');
	wp_register_script('anypopup_init', ANYPOPUP_APP_POPUP_URL . '/js/anypopup_init.js', array('jquery'), ANYPOPUP_VERSION);
	wp_enqueue_script('anypopup_init');
	wp_enqueue_script('jquery');
	wp_register_script('anypopup_colorbox', ANYPOPUP_APP_POPUP_URL . '/js/jquery.anypopupcolorbox-min.js', array('jquery'), ANYPOPUP_VERSION);
	wp_enqueue_script('anypopup_colorbox');
	if (ANYPOPUP_PKG > ANYPOPUP_PKG_FREE) {
		wp_register_script('anypopupPro', ANYPOPUP_APP_POPUP_URL . '/js/anypopup_pro.js', array(), ANYPOPUP_VERSION);
		wp_enqueue_script('anypopupPro');
		wp_register_script('anypopup_cookie', ANYPOPUP_APP_POPUP_URL . '/js/jquery_cookie.js', array('jquery'), ANYPOPUP_VERSION);
		wp_enqueue_script('anypopup_cookie');
		wp_register_script('anypopup_queue', ANYPOPUP_APP_POPUP_URL . '/js/anypopup_queue.js', array(), ANYPOPUP_VERSION);
		wp_enqueue_script('anypopup_queue');
	}
	/* For ajax case */
	if (defined( 'DOING_AJAX' ) && DOING_AJAX  && !is_admin()) {
		wp_print_scripts('anypopup_frontend');
		wp_print_scripts('anypopup_colorbox');
		wp_print_scripts('anypopup_support_plugins');
		wp_print_scripts('anypopupPro');
		wp_print_scripts('anypopup_cookie');
		wp_print_scripts('anypopup_queue');
		wp_print_scripts('anypopup_animate');
		wp_print_scripts('anypopup_init');
	}
}

function anypopupRenderPopupScript($id)
{
	if (ANYPOPUP::$registeredScripts==false) {
		anypopupRegisterScripts();
	}
	$extensionManagerObj = new ANYPOPUPExtensionManager();
	$extensionManagerObj->includeExtensionScripts($id);
	wp_register_style('anypopup_colorbox_theme', ANYPOPUP_APP_POPUP_URL . "/style/anypopupcolorbox/anypopupthemes.css", array(), ANYPOPUP_VERSION);
	wp_enqueue_style('anypopup_colorbox_theme');
	anypopupFindPopupData($id);
}

function anypopupFindPopupData($id)
{
	$obj = ANYPOPUP::findById($id);
	if (!empty($obj)) {
		$content = $obj->render();
	}

	if (ANYPOPUP_PKG == ANYPOPUP_PKG_PLATINUM) {
		$userCountryIso = ANYPOPUPFunctions::getUserLocationData($id);
		if(!is_bool($userCountryIso)) {
			echo "<script type='text/javascript'>AnypopupUserData = {
				'countryIsoName': '$userCountryIso'
			}</script>";
		}
	}

	echo "<script type='text/javascript'>";
	echo @$content;
	echo "</script>";
}

function anypopupShowShortCode($args, $content)
{
	ob_start();
	$obj = ANYPOPUP::findById($args['id']);
	if (!$obj) {
		return $content;
	}
	/*When inside popup short code content is empty*/
	if(isset($args['insidepopup']) && empty($content)) {
		return;
	}
	if(!empty($content)) {
		anypopupRenderPopupScript($args['id']);
		$attr = '';
		$eventName = @$args['event'];

		if(isset($args['insidepopup'])) {
			$attr .= 'insidePopup="on"';
		}
		if(@$args['event'] == 'onload') {
			$content = '';
		}
		if(!isset($args['event'])) {
			$eventName = 'click';
		}
		if(isset($args["wrap"])) {
			echo "<".$args["wrap"]." class='anypopup-show-popup' data-anypopuppopupid=".@$args['id']." $attr data-popup-event=".$eventName.">".$content."</".$args["wrap"]." >";
		} else {
			echo "<a href='javascript:void(0)' class='anypopup-show-popup' data-anypopuppopupid=".@$args['id']." $attr data-popup-event=".$eventName.">".$content."</a>";
		}
	}
	else {
		/* Free user does not have QUEUE possibility */
		if(ANYPOPUP_PKG > ANYPOPUP_PKG_FREE) {
			$page = get_queried_object_id();
			$popupsId = AnypopupPro::allowPopupInAllPages($page,'page');

			/* Add shordcode popup id in the QUEUE for php side */
			array_push($popupsId,$args['id']);
			/* Add shordcode popup id at the first in the QUEUE for javascript side */
			echo "<script type=\"text/javascript\">ANYPOPUP_POPUPS_QUEUE.splice(0, 0, ".$args['id'].");</script>";
			update_option("ANYPOPUP_MULTIPLE_POPUP",$popupsId);
			AnyPopupshowPopupInPage($args['id']);
			
		}
		else {
			echo AnyPopupshowPopupInPage($args['id']);
		}

	}
	$shortcodeContent = ob_get_contents();
	ob_end_clean();
	return do_shortcode($shortcodeContent);
}

add_shortcode('any_popup', 'anypopupShowShortCode');

function anypopupRenderPopupOpen($popupId)
{
	anypopupRenderPopupScript($popupId);

	echo "<script type=\"text/javascript\">

			anypopupAddEvent(window, 'load',function() {
				var anypopupPoupFrontendObj = new ANYPOPUP();
				anypopupPoupFrontendObj.popupOpenById($popupId)
			});
		</script>";
}

function AnyPopupshowPopupInPage($popupId) {


	$isActivePopup = AnypopupGetData::isActivePopup($popupId);

	if(!$isActivePopup) {
		return false;
	}

	if(ANYPOPUP_PKG > ANYPOPUP_PKG_FREE) {

		$popupInTimeRange = AnypopupPro::popupInTimeRange($popupId);

		if(!$popupInTimeRange) {
			return false;
		}

		$isInSchedule = AnypopupPro::popupInSchedule($popupId);

		if(!$isInSchedule) {
			return;
		}

		$showUser = AnypopupPro::showUserResolution($popupId);
		if(!$showUser) return false;

		if(!ANYPOPUP::showPopupForCounrty($popupId)) { /* Sended popupId and function return true or false */
			return;
		}
	}
	AnyPopupredenderScriptMode($popupId);
}

function AnyPopupredenderScriptMode($popupId)
{
	/* If user delete popup */
	$obj = ANYPOPUP::findById($popupId);

	if(empty($obj)) {
		return;
	}

	$multiplePopup = get_option('ANYPOPUP_MULTIPLE_POPUP');
	$hasEvent = ANYPOPUPExtension::hasPopupEvent($popupId);

	if($hasEvent != 0) {
		anypopupRenderPopupScript($popupId);
		return;
	}
	if($multiplePopup && @in_array($popupId, $multiplePopup)) {
		anypopupRenderPopupScript($popupId);
		return;
	}


	anypopupRenderPopupOpen($popupId);
}

function AnyPopupgetPopupIdFromContentByClass($content)
{
	$popupsID = array();
	$popupClasses = array(
		'anypopup-popup-id-',
		'anypopup-iframe-popup-',
		'anypopup-confirm-popup-',
		'anypopup-popup-hover-'
	);

	foreach ($popupClasses as $popupClassName) {

		preg_match_all("/".$popupClassName."+[0-9]+/i", $content, $matchers);

		foreach ($matchers['0'] as $value) {
			$ids = explode($popupClassName, $value);
			$id = @$ids[1];

			if(!empty($id)) {
				array_push($popupsID, $id);
			}
		}
	}

	return $popupsID;
}

function AnyPopupgetPopupIdInPageByClass($pageId) {

	$postContentObj = get_post($pageId);

	if(isset($postContentObj)) {
		$content = $postContentObj->post_content;

		/*this will return template for the current page*/
		$templatePath = get_page_template();

		if(!empty($templatePath)) {
			$content .= file_get_contents($templatePath);
		}

		if(isset($postContentObj->post_excerpt)) {
			$content .= $postContentObj->post_excerpt;
		}
		return AnyPopupgetPopupIdFromContentByClass($content);
	}

	return 0;
}


/**
 * Get popup id from url
 *
 * @since 3.1.5
 *
 * @return  int if popup not found->0 otherwise->popupId
 *
 */

function AnyPopupgetPopupIdFromUrl() {

	$popupId = 0;
	if(!isset($_SERVER['REQUEST_URI'])) {
		return $popupId;
	}

	$pageUrl = @$_SERVER['REQUEST_URI'];

	preg_match("/anypopup_id=+[0-9]+/i", $pageUrl, $match);

	if(!empty($match)) {
		$matchingNumber = explode("=", $match[0]);
		if(!empty($matchingNumber[1])) {
			$popupId = (int)$matchingNumber[1];
			return $popupId;
		}
		return 0;
	}

	return 0;
}

function anypopupOnloadPopup()
{
	$page = get_queried_object_id();
	$postType = get_post_type();
	echo AnyPopupConfig::popupJsDataInit();
	$popup = "anypopup_promotional_popup";
	/* If popup is set on page load */
	$popupId = ANYPOPUP::getPagePopupId($page, $popup);
	/* get all popups id which set in current page by class */
	$popupsIdByClass = AnyPopupgetPopupIdInPageByClass($page);
	/* get popup id in page url */
	$popupIdInPageUrl = AnyPopupgetPopupIdFromUrl();

	if(ANYPOPUP_PKG > ANYPOPUP_PKG_FREE){
		delete_option("ANYPOPUP_MULTIPLE_POPUP");

		/* Retrun all popups id width selected On All Pages */
		$popupsId = AnypopupPro::allowPopupInAllPages($page,'page');
		$categories = AnypopupPro::allowPopupInAllCategories($page);

		$popupsId = array_merge($popupsId,$categories);

		$anypopuppbAllPosts = get_option("ANYPOPUP_ALL_POSTS");

		$popupsInAllPosts = AnypopupPro::popupsIdInAllCategories($postType);
		$popupsId = array_merge($popupsInAllPosts, $popupsId);

		/* $popupsId[0] its last selected popup id */
		if(isset($popupsId[0])) {
			if(count($popupsId) > 0) {
				update_option("ANYPOPUP_MULTIPLE_POPUP",$popupsId);
			}
			foreach ($popupsId as $queuePupupId) {
				
				AnyPopupshowPopupInPage($queuePupupId);
			}

			$popupsId = json_encode($popupsId);
		}
		else {
			$popupsId = json_encode(array());
		}
		$popupsId = AnypopupPro::filterForRandomIds($popupsId);

		echo '<script type="text/javascript">
			ANYPOPUP_POPUPS_QUEUE = '.$popupsId.'</script>';
	}

	//If popup is set for all pages
	if($popupId != 0) {
		AnyPopupshowPopupInPage($popupId);
	}

	
	
	
	if(!empty($popupsIdByClass)) {
		foreach ($popupsIdByClass as $popupId) {
			anypopupRenderPopupScript($popupId);
		}
	}
	if($popupIdInPageUrl) {
		AnyPopupshowPopupInPage($popupIdInPageUrl);
	}
	return false;
}

add_filter('wp_nav_menu_items', 'AnyPopupgetPopupIdByClassFromMenu');
function AnyPopupgetPopupIdByClassFromMenu ($items) {
	$popupsID =  AnyPopupgetPopupIdFromContentByClass($items);
	if(!empty($popupsID)) {
		foreach ($popupsID as $popupId) {
			anypopupRenderPopupScript($popupId);
		}
	}
	return $items;
}

add_action('wp_head','anypopupOnloadPopup');
require_once( ANYPOPUP_APP_POPUP_FILES . '/anypopup_media_button.php');
require_once( ANYPOPUP_APP_POPUP_FILES . '/anypopup_save.php'); // saving form data
require_once( ANYPOPUP_APP_POPUP_FILES . '/anypopup_ajax.php');
if (ANYPOPUP_PKG > ANYPOPUP_PKG_FREE) {
	require_once(ANYPOPUP_APP_POPUP_FILES . '/anypopup_ajax_pro.php');
}
require_once( ANYPOPUP_APP_POPUP_FILES . '/anypopup_admin_post.php');
require_once( ANYPOPUP_APP_POPUP_FILES . '/anypopup_filetrs.php');
require_once( ANYPOPUP_APP_POPUP_FILES . '/anypopup_actions.php');