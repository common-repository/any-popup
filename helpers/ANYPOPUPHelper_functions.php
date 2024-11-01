<?php
class ANYPOPUPHelperFunctions {

	private function checkPhpVersion() {

		if (version_compare(PHP_VERSION, ANYPOPUP_MINIMUM_PHP_VERSION, '<')) {
			wp_die('Any Popup plugin requires PHP version >= '.ANYPOPUP_MINIMUM_PHP_VERSION.' version required. You server using PHP version = '.PHP_VERSION);
		}
	}

	public static function checkRequirements() {

		$helperObj = new self();
		$helperObj->checkPhpVersion();
	}
}