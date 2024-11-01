<?php

function anypopup_set_admin_url($hook) {
	if ('any-popup_page_anypopup-create-popup' == $hook) {
		echo '<script type="text/javascript">ANYPOPUP_ADMIN_URL = "'.admin_url()."admin.php?page=anypopup-create-popup".'";</script>';
	}
}

function anypopup_admin_scripts($hook) {
	
    if ( 'any-popup_page_anypopup-edit-popup' == $hook
    	|| 'any-popup_page_anypopup-create-popup' == $hook
    	|| 'any-popup_page_anypopup-subscribers' == $hook
    	|| 'any-popup_page_anypopup-newsletter' == $hook) {

		wp_enqueue_media();
		wp_register_script('javascript', ANYPOPUP_APP_POPUP_URL . '/js/anypopup_backend.js', array('jquery', 'wp-color-picker'));
		wp_enqueue_script('jquery');
		wp_enqueue_script('javascript');
		$localizedData = array(
		    'ajaxNonce'	=> wp_create_nonce('any-popup-ajax', 'anypopuppbAjaxNonce')
		);
		wp_localize_script('javascript', 'backendLocalizedData', $localizedData);
		
		if(ANYPOPUP_PKG > ANYPOPUP_PKG_FREE) {
			wp_register_script('anypopup_pro', ANYPOPUP_APP_POPUP_URL . '/js/anypopup_backend_pro.js');
			wp_enqueue_script('anypopup_pro');
		}
    }
	else if('toplevel_page_AnyPopup' == $hook  || $hook == 'toplevel_page_anypopup-popup-settings'){
		wp_register_script('javascript', ANYPOPUP_APP_POPUP_URL . '/js/anypopup_backend.js', array('jquery', 'wp-color-picker'));
		wp_enqueue_script('jquery');
		wp_enqueue_script('javascript');
		if(ANYPOPUP_PKG > ANYPOPUP_PKG_FREE) {
			wp_register_script('anypopup_pro', ANYPOPUP_APP_POPUP_URL . '/js/anypopup_backend_pro.js');
			wp_enqueue_script('anypopup_pro');
			wp_enqueue_media();
		}
		wp_enqueue_script('jquery');
	}
	if('any-popup_page_anypopup-edit-popup' == $hook) {
		wp_register_script('anypopup_rangeslider', ANYPOPUP_APP_POPUP_URL . '/js/anypopup_rangeslider.js', array('jquery'));
		wp_enqueue_script('anypopup_rangeslider');
		wp_enqueue_script('jquery');
		if (ANYPOPUP_PKG == ANYPOPUP_PKG_PLATINUM) {
			wp_register_script('anypopup_tagsinput', ANYPOPUP_APP_POPUP_URL . '/js/bootstrap-tagsinput.js', array('jquery'));
			wp_enqueue_script('anypopup_tagsinput');
		}
		if (ANYPOPUP_PKG > ANYPOPUP_PKG_SILVER) {
			wp_register_script('jssocials.min', ANYPOPUP_APP_POPUP_URL . '/js/jssocials.min.js');
			wp_enqueue_script('jssocials.min');
			wp_register_script('anypopup_social_backend', ANYPOPUP_APP_POPUP_URL . '/js/anypopup_social_backend.js',array('jquery'));
			wp_enqueue_script('anypopup_social_backend');
		}
		if(ANYPOPUP_PKG > ANYPOPUP_PKG_FREE) {
			wp_register_script('datetimepicker', ANYPOPUP_APP_POPUP_URL . '/js/jquery.datetimepicker.full.min.js');
			wp_enqueue_script('datetimepicker');
			wp_enqueue_script('jquery-ui-datepicker');
			wp_enqueue_script( 'anypopup_libs_handle', plugins_url('js/anypopup_datapickers.js',dirname(__FILE__)), array('wp-color-picker'));
			wp_register_script('anypopup_pro', ANYPOPUP_APP_POPUP_URL . '/js/anypopup_backend_pro.js');
			wp_enqueue_script('anypopup_pro');
		}
		wp_enqueue_style( 'wp-color-picker' );
		
	}
}

add_action('admin_enqueue_scripts', 'anypopup_set_admin_url');
add_action('admin_enqueue_scripts', 'anypopup_admin_scripts');

