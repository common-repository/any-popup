<?php
class ANYPOPUPAnyPopupMain {

	public function init() {

		$this->filters();
		$this->anypopuppbActions();
	}

	public function anypopuppbActions() {
		
		add_action("admin_menu",array($this, "anypopupAddMenu"));
	}

	public function anypopupAddMenu($args) {
		$showCurrentUser = ANYPOPUPFunctions::isShowMenuForCurrentUser();
		if(!$showCurrentUser) {
			return false;
		}
		add_menu_page("Any Popup", "Any Popup", "read","any_popup",  array($this, "anypopupMenu"),"dashicons-welcome-widgets-menus");
		add_submenu_page("any_popup", "All Popups", "All Popups", 'read', "any_popup", array($this, "anypopupMenu"));
		add_submenu_page("any_popup", "Add New", "Add New", 'read', "anypopup-create-popup", array($this,"anypopupCreatePopup"));
		add_submenu_page("any_popup", "Edit Popup", "Edit Popup", 'read', "anypopup-edit-popup", array($this,"anypopupEditPopup"));
		add_submenu_page("any_popup", "Settings", "Settings", 'read', "anypopup-popup-settings", array($this,"anypopupSettings"));
		if (ANYPOPUP_PKG > ANYPOPUP_PKG_SILVER) {
			add_submenu_page("any_popup", "Subscribers", "Subscribers", 'read', "anypopup-subscribers", array($this,"anypopupSubscribers"));
			add_submenu_page("any_popup", "Newsletter", "Newsletter", 'read', "anypopup-newsletter", array($this,"anypopupNewsletter"));
		}
		
	}

	public function anypopupMenu() {

		require_once( ANYPOPUP_APP_POPUP_FILES . '/anypopup_main.php');
	}

	public function anypopupCreatePopup() {

		require_once( ANYPOPUP_APP_POPUP_FILES . '/anypopup_create.php'); // here is inculde file in the first sub menu
	}

	public function anypopupSettings() {

		require_once( ANYPOPUP_APP_POPUP_FILES . '/anypopup_settings.php');
	}

	public function anypopupEditPopup() {

		require_once( ANYPOPUP_APP_POPUP_FILES . '/anypopup_create_new.php');
	}

	public function anypopupSubscribers() {

		require_once( ANYPOPUP_APP_POPUP_FILES . '/anypopup_subscribers.php');
	}

	public function anypopupNewsletter() {
		
		require_once( ANYPOPUP_APP_POPUP_FILES . '/anypopup_newsletter.php');
	}

	
	public function filters() {

		add_filter('plugin_action_links_'. ANYPOPUP_BASENAME, array($this, 'popupPluginActionLinks'));
	}

	public function popupPluginActionLinks($links) {

		$popupActionLinks = array(
			'<a href="' . ANYPOPUP_EXTENSION_URL . '" target="_blank">Extensions</a>'
		);

		if(ANYPOPUP_PKG == ANYPOPUP_PKG_FREE) {
			array_push($popupActionLinks, '<a href="' . ANYPOPUP_PRO_URL . '" target="_blank">Pro</a>');
		}
		
		return array_merge( $links, $popupActionLinks );
	}
}