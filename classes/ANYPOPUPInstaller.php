<?php
class AnypopupInstaller {

	public static $mainTableName = "any_popup";

	public static function createTables($blogId = '') {

		global $wpdb;
		update_option('ANYPOPUP_VERSION', ANYPOPUP_VERSION);
		$anypopupBase = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix.$blogId."any_popup (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`type` varchar(255) NOT NULL,
			`title` varchar(255) NOT NULL,
			`options` LONGTEXT NOT NULL,
			PRIMARY KEY (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8; ";
		$anypopupSettingsBase = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix.$blogId."anypopup_settings (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`options` LONGTEXT NOT NULL,
			PRIMARY KEY (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8; ";
		$optionsDefault = AnypopupGetData::getDefaultValues();
		$anypopupInsertSettingsSql = $wpdb->prepare("INSERT IGNORE ". $wpdb->prefix.$blogId."anypopup_settings (id, options) VALUES(%d,%s) ", 1, json_encode($optionsDefault['settingsParams']));

		$anypopupImageBase = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix.$blogId."anypopup_image_popup (
				`id` int(11) NOT NULL,
				`url` varchar(255) NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8; ";
		$anypopupHtmlBase = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix.$blogId."anypopup_html_popup (
				`id` int(11) NOT NULL,
				`content` text NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$anypopupFblikeBase = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix.$blogId."anypopup_fblike_popup (
				`id` int(11) NOT NULL,
				`content` text NOT NULL,
				`options` text NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$anypopupShortcodeBase =  "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix.$blogId."anypopup_shortCode_popup (
				`id` int(12) NOT NULL,
				`url` text NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		$anypopupVideoBase =  "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix.$blogId."anypopup_video_popup (
				`id` int(12) NOT NULL,
				`url` text NOT NULL,
				`type` text NOT NULL,
				`options` TEXT NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

		$anypopupAddon = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix.$blogId."anypopup_addons (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`name` varchar(255) NOT NULL UNIQUE,
			`paths` TEXT NOT NULL,
			`type` varchar(255) NOT NULL,
			`options` TEXT NOT NULL,
			`isEvent` TINYINT UNSIGNED NOT NULL,
			PRIMARY KEY (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8; ";

		$addonsConnectionTable = "CREATE TABLE IF NOT EXISTS ". $wpdb->prefix.$blogId."anypopup_addons_connection (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`popupId` int(11) NOT NULL,
			`extensionKey` TEXT NOT NULL,
			`content` TEXT NOT NULL,
			`extensionType` varchar(255) NOT NULL,
			`options` TEXT NOT NULL,
			PRIMARY KEY (id)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8; ";

		$wpdb->query($anypopupBase);
		$wpdb->query($anypopupSettingsBase);
		$wpdb->query($anypopupInsertSettingsSql);
		$wpdb->query($anypopupImageBase);
		$wpdb->query($anypopupHtmlBase);
		$wpdb->query($anypopupFblikeBase);
		$wpdb->query($anypopupShortcodeBase);
		$wpdb->query($anypopupVideoBase);
		$wpdb->query($anypopupAddon);
		$wpdb->query($addonsConnectionTable);

		$columnInfo = $wpdb->query("SHOW COLUMNS FROM ".$wpdb->prefix.$blogId.ANYPOPUPExtension::ANYPOPUP_ADDON_TABLE_NAME." LIKE 'isEvent'");
		if(!$columnInfo) {
			$alterQuery = "ALTER TABLE ".$wpdb->prefix.$blogId.ANYPOPUPExtension::ANYPOPUP_ADDON_TABLE_NAME." ADD isEvent TINYINT UNSIGNED NOT NULL";
			$wpdb->query($alterQuery);
		}
	}

	public static function install() {

		self::createTables();

		self::setupInstallationsDateConfig();

		/*get_current_blog_id() == 1 When plugin activated inside the child of multisite instance*/
		if(is_multisite() && get_current_blog_id() == 1) {
			global $wp_version;

			if($wp_version > '4.6.0') {
				$sites = get_sites();
			}
			else {
				$sites = wp_get_sites();
			}

			foreach($sites as $site) {

				if($wp_version > '4.6.0') {
					$blogId = $site->blog_id."_";
				}
				else {
					$blogId = $site['blog_id']."_";
				}
				if($blogId != 1) {
					self::createTables($blogId);
				}
			}
		}
	}

	public static function setupInstallationsDateConfig()
	{
		$usageDays = get_option('ANYPOPUPUsageDays');
		if(!$usageDays) {
			update_option('ANYPOPUPUsageDays', 0);

			$timeDate = new DateTime('now');
			$installTime = strtotime($timeDate->format('Y-m-d H:i:s'));
			update_option('ANYPOPUPInstallDate', $installTime);
			$timeDate->modify('+'.ANYPOPUP_REVIEW_POPUP_PERIOD.' day');

			$timeNow = strtotime($timeDate->format('Y-m-d H:i:s'));
			update_option('ANYPOPUPOpenNextTime', $timeNow);
		}
		$maxPopupCount = get_option('ANYPOPUPMaxOpenCount');
		if(!$maxPopupCount) {
			update_option('ANYPOPUPMaxOpenCount', ANYPOPUP_SHOW_COUNT);
		}
	}

	public static function uninstallTables($blogId = '') {

		global $wpdb;
		$delete = "DELETE FROM ".$wpdb->prefix.$blogId."postmeta WHERE meta_key = 'anypopup_promotional_popup' ";
		$wpdb->query($delete);

		$popupTable = $wpdb->prefix.$blogId."any_popup";
		$popupSql = "DROP TABLE ". $popupTable;

		$popupImageTable = $wpdb->prefix.$blogId."anypopup_image_popup";
		$popupImageSql = "DROP TABLE ". $popupImageTable;

		$popupHtmlTable = $wpdb->prefix.$blogId."anypopup_html_popup";
		$popupHtmlSql = "DROP TABLE ". $popupHtmlTable;

		$popupFblikeTable = $wpdb->prefix.$blogId."anypopup_fblike_popup";
		$popupFblikeSql = "DROP TABLE ". $popupFblikeTable;

		$popupShortcodeTable = $wpdb->prefix.$blogId."anypopup_shortCode_popup";
		$popupShortcodeSql = "DROP TABLE ". $popupShortcodeTable;

		$popupAddonDrop = $wpdb->prefix.$blogId."anypopup_addons";
		$popupAddonSql = "DROP TABLE ". $popupAddonDrop;

		$popupSettingsDrop = $wpdb->prefix.$blogId."anypopup_settings";
		$popupSettingsSql = "DROP TABLE ". $popupSettingsDrop;

		$addonsConnectionTableName = $wpdb->prefix.$blogId."anypopup_addons_connection";
		$deleteAddonsConnectionTable = "DROP TABLE ". $addonsConnectionTableName;

		$wpdb->query($popupSql);
		$wpdb->query($popupImageSql);
		$wpdb->query($popupHtmlSql);
		$wpdb->query($popupFblikeSql);
		$wpdb->query($popupShortcodeSql);
		$wpdb->query($popupAddonSql);
		$wpdb->query($popupSettingsSql);
		$wpdb->query($deleteAddonsConnectionTable);
	}

	public static function deleteAnypopupOptions($blogId = '') {

		global $wpdb;
		$deleteANYPOPUP = "DELETE FROM ".$wpdb->prefix.$blogId."options WHERE option_name LIKE '%ANYPOPUP_POPUP%'";
		$wpdb->query($deleteANYPOPUP);
	}

	public static function uninstall() {

		self::removeCustomOptions();

		$obj = new self();
		self::uninstallTables();
		$obj->deleteAnypopupOptions();

		if(is_multisite()) {
			global $wp_version;
			if($wp_version > '4.6.0') {
				$sites = get_sites();
			}
			else {
				$sites = wp_get_sites();
			}

			foreach($sites as $site) {

				if($wp_version > '4.6.0') {
					$blogId = $site->blog_id."_";
				}
				else {
					$blogId = $site['blog_id']."_";
				}

				self::uninstallTables($blogId);
				$obj->deleteAnypopupOptions($blogId);
			}
		}
	}

	public static function removeCustomOptions()
	{
		delete_option('ANYPOPUPUsageDays');
		delete_option('ANYPOPUPOpenNextTime');
		delete_option('ANYPOPUPMaxOpenCount');
		delete_option('ANYPOPUPCloseReviewPopup');
		delete_option('AnypopuppbCounter');
		delete_option('ANYPOPUPInstallDate');
		delete_option('popupPreviewId');
	}
}