<?php

/**
 * AnyPopup extensions connection
 *
 * @since 2.5.3
 *
 */
class ANYPOPUPExtensionsConnector {

	public $activeExtensions;
	public $deactive;
	//redirect url for activation hook
	public $redirectUrl = '';
	//bool $networkWide Whether to enable the plugin for all sites in the network.
	public $networkWide = false;
	//bool $silent Prevent calling activation hooks.
	public $silent = false;
	public $prepareData;

	public static $POPUPEXTENSIONS = array(
		'any-popup-mailchimp/any-popup-mailchimp.php',
		'any-popup-aweber/any-popup-aweber.php',
		'any-popup-exit-intent/any-popup-exit-intent.php',
		'any-popup-analytics/any-popup-analytics.php',
		'any-popup-ad-block/any-popup-add-block.php'
	);

	public function __construct() {

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if(is_multisite()) {
			$this->networkWide = true;
		}
	}

	/**
	 * Check extensions connection
	 *
	 * @since 2.5.3
	 *
	 * @param bool $activeStatus if true check is active extension
	 *
	 * @return void
	 *
	 */

	private function extensionCheck($activeStatus) {

		$extensions = array();
		$allExtensions = ANYPOPUPExtensionsConnector::$POPUPEXTENSIONS;

		if(empty($allExtensions)) {
			return;
		}

		foreach($allExtensions as $extensionKey) {
			$isActive = is_plugin_active($extensionKey);

			if($isActive && $activeStatus) {
				$extensions[] = $extensionKey;
			}
			else if(!$isActive && !$activeStatus) {
				$extensions[] = $extensionKey;
			}
		}

		if($activeStatus) {
			$this->activeExtensions = $extensions;
		}
		else {
			$this->deactive = $extensions;
		}

	}

	private function packageChecker() {

		$originalExtension = ANYPOPUP_APP_POPUP_FILES.'/extensions/any-popup-exit-intent';
		$passedExtension =  WP_PLUGIN_DIR.'/any-popup-exit-intent';

		if(file_exists($originalExtension) && file_exists($passedExtension)) {
			$exitIntentPackage = array('any-popup-exit-intent/any-popup-exit-intent.php');
			$this->deletePlugin($exitIntentPackage);
		}
	}

	/**
	 * Current All active extensions
	 *
	 * @since 2.5.3
	 *
	 * @return array $activeExtension
	 *
	 */

	private function getActiveExtensions() {

		$this->extensionCheck(true);
		$activeExtension = $this->activeExtensions;

		return $activeExtension;
	}

	/**
	 * Current all deactive extensions
	 *
	 * @since 2.5.3
	 *
	 * @return array $deactivateExtensions
	 *
	 */

	private function getDeactivatePlugins() {

		$this->extensionCheck(false);
		$deactivateExtensions = $this->deactive;

		return $deactivateExtensions;
	}

	private function prepareToActivate() {

		$this->getActiveExtensions();
		$this->getDeactivatePlugins();
		// Deactivate all active extensions
		$this->deactivate();
		$this->packageChecker();
	}

	/**
	 * Activate all extensions
	 *
	 * @since 2.5.3
	 *
	 * @param bool status if true activate all else active only active extensions
	 *
	 * @return void
	 *
	 */

	public function activate($status = false) {

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$doActivate = $this->activeExtensions;

		$this->prepareToActivate();

		if($status) {
			$doActivate = ANYPOPUPExtensionsConnector::$POPUPEXTENSIONS;
		}
		if(ANYPOPUP_PKG > ANYPOPUP_PKG_SILVER) {
			@$this->anypopupRunActivatePlugin('any-popup-exit-intent/any-popup-exit-intent.php');
		}
		$this->doActivate($doActivate);
	}

	/**
	 * Activate plugins
	 *
	 * @since 2.5.3
	 *
	 * @param string|array $plugins Single plugin or list of plugins.
	 *
	 * @return void
	 */

	public function doActivate($plugins) {

		$redirectUrl = $this->redirectUrl;
		$networkWide = $this->networkWide;
		$silent = $this->silent;

		activate_plugins($plugins, $redirectUrl, $networkWide, $silent);
	}

	/**
	 * Deactivate all extensions
	 *
	 * @since 2.5.3
	 *
	 * @return void
	 *
	 */

	public function deactivate() {

		$doDeActivate = $this->activeExtensions;

		$this->doDeactivate($doDeActivate);
	}

	/**
	 * Deactivate plugins
	 *
	 * @since 2.5.3
	 *
	 * @param string|array $plugins Single plugin or list of plugins.
	 *
	 * @return void
	 *
	 */

	public function doDeactivate($plugins) {

		$networkWide = $this->networkWide;
		$silent = $this->silent;

		deactivate_plugins($plugins,$silent,$networkWide);
	}

	/**
	 * Delete plugin from plugins section
	 *
	 * @since 2.5.3
	 *
	 * @param array  $plugins List of plugins to delete.
	 *
	 * @return void
	 *
	 */

	private function deletePlugin($plugins) {

		delete_plugins($plugins);
	}

	public function anypopupRunActivatePlugin($plugin) {
		$current = get_option( 'active_plugins' );
		$plugin = plugin_basename( trim( $plugin ) );

		if ( !in_array( $plugin, $current ) ) {
			$current[] = $plugin;
			sort( $current );
			do_action( 'activate_plugin', trim( $plugin ) );
			update_option( 'active_plugins', $current );
			do_action( 'activate_' . trim( $plugin ) );
			do_action( 'activated_plugin', trim( $plugin) );
		}

		return null;
	}
}