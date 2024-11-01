<?php
function anypopup_admin_style($hook) {
	if ('toplevel_page_AnyPopup' != $hook && 
		'any-popup_page_anypopup-create-popup' != $hook &&
		'any-popup_page_anypopup-edit-popup' != $hook && 
		'any-popup_page_anypopupMenu' != $hook && 
		'any-popup_page_anypopup-more-plugins' != $hook && 
		'any-popup_page_anypopup-popup-settings' != $hook && 
		'any-popup_page_anypopup-subscribers' != $hook &&
		'any-popup_page_anypopup-newsletter' != $hook) {
        return;
    }
	wp_register_style('anypopup_style', ANYPOPUP_APP_POPUP_URL . '/style/anypopup_style.css', false, '1.0.0');
	wp_enqueue_style('anypopup_style');
	wp_register_style('anypopup_review_panel_style', ANYPOPUP_APP_POPUP_URL . '/style/anypopup_review_panel.css', false, '1.0.0');
	wp_enqueue_style('anypopup_review_panel_style');
	wp_register_style('anypopup_animate', ANYPOPUP_APP_POPUP_URL . '/style/animate.css');
	wp_enqueue_style('anypopup_animate');
	if (ANYPOPUP_PKG > ANYPOPUP_PKG_SILVER) {
		wp_register_style('font_awesome', ANYPOPUP_APP_POPUP_URL . "/style/jssocial/font-awesome.min.css");
		wp_enqueue_style('font_awesome');
		wp_register_style('jssocials_main_css', ANYPOPUP_APP_POPUP_URL . "/style/jssocial/jssocials.css");
		wp_enqueue_style('jssocials_main_css');
		wp_register_style('jssocials_theme_tm', ANYPOPUP_APP_POPUP_URL . "/style/jssocial/jssocials-theme-classic.css");
		wp_enqueue_style('jssocials_theme_tm');
		wp_register_style('anypopup_flipclock_css', ANYPOPUP_APP_POPUP_URL . "/style/anypopup_flipclock.css");
		wp_enqueue_style('anypopup_flipclock_css');
		wp_register_style('anypopup_jqueryUi_css', ANYPOPUP_APP_POPUP_URL . "/style/jquery-ui.min.css");
		wp_enqueue_style('anypopup_jqueryUi_css');
	}
	if(ANYPOPUP_PKG != ANYPOPUP_PKG_FREE) {
		wp_register_style('anypopup_datetimepicker_css', ANYPOPUP_APP_POPUP_URL . "/style/jquery.datetimepicker.min.css");
		wp_enqueue_style('anypopup_datetimepicker_css');
	}
	if(ANYPOPUP_PKG == ANYPOPUP_PKG_PLATINUM) {
		wp_register_style('anypopup_bootstrap_input', ANYPOPUP_APP_POPUP_URL . "/style/bootstrap-tagsinput.css");
		wp_enqueue_style('anypopup_bootstrap_input');
	}
}
add_action('admin_enqueue_scripts', 'anypopup_admin_style');

function anypopup_style($hook) {
	if ('admin.php' != $hook) {
		return;
	}
	wp_register_style('anypopup_animate', ANYPOPUP_APP_POPUP_URL . '/style/animate.css');
	wp_enqueue_style('anypopup_animate');

	wp_register_style('anypopup_style', ANYPOPUP_APP_POPUP_URL . '/style/anypopup_style.css', false);
	wp_enqueue_style('anypopup_style');
}

add_action('admin_enqueue_scripts', 'anypopup_style');
