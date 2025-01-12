<?php
class AnypopupGetData {

	public static function getDefaultValues() {

		$settingsParams = array(
			'tables-delete-status' => 'on',
			'plugin_users_role' => array(),
			'anypopup-popup-time-zone' => 'Pacific/Midway'
		);

		$usersRoleList = self::getAllUserRoles();

		$defaultParams = array(
			'settingsParams' =>  $settingsParams,
			'usersRoleList' => $usersRoleList
		);

		return $defaultParams;
	}

	public static function getValue($optionName,$optionType) {

		$optionType = strtolower($optionType);
		$optionFunctionName = 'get'.ucfirst($optionType).'Options';
		$options = self::$optionFunctionName();
	

		if(isset($options[$optionName])) {
			return $options[$optionName];
		}
		
		$deafaultValues = self::getDefaultValues();
		$deafultSettings = $deafaultValues[$optionType.'Params'];

		return $deafultSettings[$optionName];
	}

	public static function getSettingsOptions() {

		global $wpdb;

		$st = $wpdb->prepare("SELECT options FROM ". $wpdb->prefix ."anypopup_settings WHERE id = %d",1);
		$options = $wpdb->get_row($st, ARRAY_A);

		/*Option can be null when ex settings table does now exists for old users*/
		if(is_null($options)) {
			return array();
		}
		$options = json_decode($options['options'], true);

		return $options;
	}

	public static function getPopupTimeZone() {

		$options = self::getSettingsOptions();

		$popupImeZone = @$options['anypopup-popup-time-zone'];

		if(!isset($popupImeZone) || empty($popupImeZone)) {
			$popupImeZone = 'Asia/Yerevan';
		}
		
		return $popupImeZone;
	}

	public static function getPostsAllCategories() {

		 $cats =  get_categories(
			array(
				"hide_empty" => 0,
				"type"      => "post",      
				"orderby"   => "name",
				"order"     => "ASC"
			)
		);
		$catsParams = array();
		foreach ($cats as $cat) {

			$id = $cat->term_id;
			$name = $cat->name;
			$catsParams[$id] = $name;
		}

		return $catsParams;
	}

	public static function anypopupSetChecked($value) {

		if($value == '') {
			return '';
		}
		return 'checked';
	}

	public static function getAllUserRoles() {

		$rulesArray = array();
		if(!function_exists('get_editable_roles')){
			return $rulesArray;
		}

		$roles = get_editable_roles();
		foreach ($roles as $role_name => $role_info) {
			if($role_name == 'administrator') {
				continue;
			}
			$rulesArray["anypopuppb_".$role_name] = $role_name;

		}
		return $rulesArray;
	}

	public static function getCurrentUserRole() {

		$role = 'administrator';

		if(is_multisite()) {

			$getUsersObj = get_users(
				array(
					'blog_id' => get_current_blog_id()
				)
			);
			if(is_array($getUsersObj)) {
				foreach ($getUsersObj as $key => $userData) {
					if($userData->ID == get_current_user_id()) {
						$roles = $userData->roles;
						if(is_array($roles) && !empty($roles)) {
							$role = $roles[0];
						}
					}
				}
			}

			return "anypopuppb_".$role;
		}

		global $current_user, $wpdb;
		$userRoleKey = $wpdb->prefix . 'capabilities';
		$usersRoles = array_keys($current_user->$userRoleKey);

		if(is_array($usersRoles) && !empty($usersRoles)) {
			$role = $usersRoles[0];
		}

		return "anypopuppb_".$role;
	}

	public static function getAllSubscriptionForms() {
		global $wpdb;
		$st = "SELECT title FROM ". $wpdb->prefix ."any_popup WHERE type='subscription'";
		$subsriptionForms = $wpdb->get_results($st, ARRAY_A);
		$subsFormList = array();

		foreach ($subsriptionForms as $subsriptionForm) {
			$value = $subsriptionForm['title'];
			$subsFormList[$value] = $value;
		}
		return $subsFormList;
	}

	public static function isActivePopup($id) {
		
		$obj = ANYPOPUP::findById($id);
		if(empty($obj)) {
			return '';
		}
		$options = $obj->getOptions();
		$options = json_decode($options, true);

		if(!isset($options['isActiveStatus']) || $options['isActiveStatus'] == 'on') {
			return "checked";
		}
		return "";
	}

	public static function getAllCustomPosts() {

		$args = array(
			'public' => true,
			'_builtin' => false
		);

		$allCustomPosts = get_post_types($args);

		return $allCustomPosts;
	}

	public static function getPageUrl()
	{
		$args = array(
			'sort_order' => 'asc',
			'sort_column' => 'post_title',
			'hierarchical' => 0,
			'exclude' => '',
			'include' => '',
			'meta_key' => '',
			'meta_value' => '',
			'authors' => '',
			'child_of' => 0,
			'parent' => -1,
			'exclude_tree' => '',
			'number' => 2,
			'offset' => 0,
			'post_type' => 'page',
			'post_status' => 'publish'
		);
		$pages = get_pages($args);

		if(empty($pages[0])) {
			return "";
		}

		$pageId  = $pages[0]->ID;
		$pageUrl = get_permalink($pageId);
		return $pageUrl;
	}
}