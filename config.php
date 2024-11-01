<?php
if(!class_exists('AnyPopupConfig')) {
	class AnyPopupConfig
	{

		public function __construct()
		{
			$this->init();
		}

		private function init()
		{

			if (!defined('ABSPATH')) {
				exit();
			}

			define('ANYPOPUP_APP_POPUP_PATH', dirname(__FILE__));
			define('ANYPOPUP_APP_POPUP_URL', plugins_url('', __FILE__));
			define('ANYPOPUP_APP_POPUP_ADMIN_URL', admin_url());
			define('ANYPOPUP_APP_POPUP_FILE', plugin_basename(__FILE__));
			define('ANYPOPUP_APP_POPUP_FILES', ANYPOPUP_APP_POPUP_PATH . '/files');
			define('ANYPOPUP_APP_POPUP_CLASSES', ANYPOPUP_APP_POPUP_PATH . '/classes');
			define('ANYPOPUP_APP_POPUP_JS', ANYPOPUP_APP_POPUP_PATH . '/js');
			define('ANYPOPUP_APP_POPUP_HELPERS', ANYPOPUP_APP_POPUP_PATH . '/helpers/');
			define('ANYPOPUP_APP_POPUP_TABLE_LIMIT', 15);
			define('ANYPOPUP_VERSION', 1.0);
			define('ANYPOPUP_PRO_VERSION', 3.379);
			define('ANYPOPUP_PRO_URL', 'https://any-popup.com/');
			define('ANYPOPUP_EXTENSION_URL', 'https://any-popup.com/extensions');
			define('ANYPOPUP_MAILCHIMP_EXTENSION_URL', 'https://any-popup.com/downloads/mailchimp/');
			define('ANYPOPUP_ANALYTICS_EXTENSION_URL', 'https://any-popup.com/downloads/analytics/');
			define('ANYPOPUP_AWEBER_EXTENSION_URL', 'https://any-popup.com/downloads/aweber/');
			define('ANYPOPUP_EXITINTENT_EXTENSION_URL', 'https://any-popup.com/downloads/exit-intent/');
			define('ANYPOPUP_ADBLOCK_EXTENSION_URL', 'https://any-popup.com/downloads/adblock/');
			define('ANYPOPUP_IP_TO_COUNTRY_SERVICE_TIMEOUT', 2);
			define('ANYPOPUP_SHOW_POPUP_REVIEW', get_option("ANYPOPUP_COLOSE_REVIEW_BLOCK"));
			define('ANYPOPUP_POSTS_PER_PAGE', 1000);
			define('ANYPOPUP_MINIMUM_PHP_VERSION', '5.3.3');
			define('ANYPOPUP_SHOW_COUNT', 80);
			define('ANYPOPUP_REVIEW_POPUP_PERIOD', 30);
			define('ANYPOPUP_REVIEW_URL' , '');
			/*Example 1 minute*/
			define('ANYPOPUP_FILTER_REPEAT_INTERVAL', 1);
			define('ANYPOPUP_POST_TYPE_PAGE', 'allPages');
			define('ANYPOPUP_POST_TYPE_POST', 'allPosts');

			define('ANYPOPUP_PKG_FREE', 1);
			define('ANYPOPUP_PKG_SILVER', 2);
			define('ANYPOPUP_PKG_GOLD', 3);
			define('ANYPOPUP_PKG_PLATINUM', 4);

			global $POPUP_TITLES;
			global $POPUP_ADDONS;

			$POPUP_TITLES = array(
				'image' => 'Image',
				'html' => 'HTML',
				'fblike' => 'Facebook',
				'iframe' => 'Iframe',
				'video' => 'Video',
				'shortcode' => 'Shortcode',
				'ageRestriction' => 'Age Restriction',
				'countdown' => 'Countdown',
				'social' => 'Social',
				'exitIntent' => 'Exit Intent',
				'subscription' => 'Subscription',
				'contactForm' => 'Contact Form'
			);

			$POPUP_ADDONS = array(
				'aweber',
				'mailchimp',
				'analytics',
				'exitIntent',
				'adBlock'
			);


			require_once(dirname(__FILE__) . '/config-pkg.php');

		}

		public static function popupJsDataInit()
		{

			$anypopupVersion = ANYPOPUP_VERSION;
			if (ANYPOPUP_PKG > ANYPOPUP_PKG_FREE) {
				$anypopupVersion = ANYPOPUP_PRO_VERSION;
			}

			$dataString = "<script type='text/javascript'>
							ANYPOPUP_POPUPS_QUEUE = [];
							ANYPOPUP_DATA = [];
							ANYPOPUP_APP_POPUP_URL = '" . ANYPOPUP_APP_POPUP_URL . "';
							ANYPOPUP_VERSION='" . $anypopupVersion . "_" . ANYPOPUP_PKG . ";';
							
							function anypopupAddEvent(element, eventName, fn) {
								if (element.addEventListener)
									element.addEventListener(eventName, fn, false);
								else if (element.attachEvent)
									element.attachEvent('on' + eventName, fn);
							}
						</script>";

			return $dataString;
		}

		public static function getFrontendScriptLocalizedData()
		{
			$localizedData = array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'ajaxNonce' => wp_create_nonce('anypopupPbNonce')
			);

			return $localizedData;
		}
	}

	$popupConf = new AnyPopupConfig();
}