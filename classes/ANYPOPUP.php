<?php

abstract class ANYPOPUP {
	protected $id;
	protected $type;
	protected $title;
	protected $width;
	protected $height;
	protected $delay;
	protected $effectDuration;
	protected $effect;
	protected $initialWidth;
	protected $initialHeight;
	protected $options;
	public static $registeredScripts = false;

	public function setType($type){
		$this->type = $type;
	}
	public function getType() {
		return $this->type;
	}
	public function setTitle($title){
		$this->title = $title;
	}
	public function getTitle() {
		return $this->title;
	}
	public function setId($id){
		$this->id = $id;
	}
	public function getId() {
		return $this->id;
	}
	public function setWidth($width){
		$this->width = $width;
	}
	public function getWidth() {
		return $this->width;
	}
	public function setHeight($height){
		$this->height = $height;
	}
	public function getHeight() {
		return $this->height;
	}
	public function setDelay($delay){
		$this->delay = $delay;
	}
	public function getDelay() {
		return $this->delay;
	}
	public function setEffectDuration($effectDuration){
		$this->effectDuration = $effectDuration;
	}
	public function getEffectDuration() {
		return $this->effectDuration;
	}
	public function setEffect($effect){
		$this->effect = $effect;
	}
	public function getEffect() {
		return $this->effect;
	}
	public function setInitialWidth($initialWidth){
		$this->initialWidth = $initialWidth;
	}
	public function getInitialWidth() {
		return $this->initialWidth;
	}
	public function setInitialHeight($initialHeight){
		$this->initialHeight = $initialHeight;
	}
	public function getInitialHeight() {
		return $this->initialHeight;
	}
	public function setOptions($options) {
		$this->options = $options;
	}
	public function getOptions() {
		return $this->options;
	}
	public static function findById($id) {

		global $wpdb;
		$st = $wpdb->prepare("SELECT * FROM ". $wpdb->prefix ."any_popup WHERE id = %d",$id);
		$arr = $wpdb->get_row($st,ARRAY_A);
		if(!$arr) return false;
		return self::popupObjectFromArray($arr);

	}

	abstract protected function setCustomOptions($id);

	abstract protected function getExtraRenderOptions();

	private static function popupObjectFromArray($arr, $obj = null) {

		$jsonData = json_decode($arr['options'], true);

		$type = anypopupSafeStr($arr['type']);

		if ($obj===null) {
			$className = "ANYPOPUP".ucfirst(strtolower($type)).'Popup';
			/* get current popup app path */
			$paths = AnyPopupIntegrateExternalSettings::getCurrentPopupAppPaths($type);

			$popupAppPath = $paths['app-path'];
			if(!file_exists($popupAppPath.'/classes/'.$className.'.php')) {
				return false;
			}
			require_once($popupAppPath.'/classes/'.$className.'.php');
			$obj = new $className();
		}
	
	
		$obj->setType(anypopupSafeStr($type));
		$obj->setTitle(anypopupSafeStr($arr['title']));
		if (@$arr['id']) $obj->setId($arr['id']);
		$obj->setWidth(anypopupSafeStr(@$jsonData['width']));
		$obj->setHeight(anypopupSafeStr(@$jsonData['height']));
		$obj->setDelay(anypopupSafeStr(@$jsonData['delay']));
		$obj->setEffectDuration(anypopupSafeStr(@$jsonData['duration']));
		$obj->setEffect(anypopupSafeStr($jsonData['effect']));
		$obj->setInitialWidth(anypopupSafeStr(@$jsonData['initialWidth']));
		$obj->setInitialHeight(anypopupSafeStr(@$jsonData['initialHeight']));
		$obj->setOptions(anypopupSafeStr($arr['options']));

		if (@$arr['id']) $obj->setCustomOptions($arr['id']);

		return $obj;
	}

	public static function create($data, $obj)
	{
		self::popupObjectFromArray($data, $obj);
		return $obj->save();
	}
	public function save($data = array()) {

		$id = $this->getId();
		$type = $this->getType();
		$title = $this->getTitle();
		$options = $this->getOptions();

		global $wpdb;

		if($id  == '') {
				$sql = $wpdb->prepare( "INSERT INTO ". $wpdb->prefix ."any_popup(type,title,options) VALUES (%s,%s,%s)",$type,$title,$options);
				$res = $wpdb->query($sql);


			if ($res) {
				$id = $wpdb->insert_id;
				$this->setId($id);
			}
			return $res;

		}
		else {
			$sql = $wpdb->prepare("UPDATE ". $wpdb->prefix ."any_popup SET type=%s,title=%s,options=%s WHERE id=%d",$type,$title,$options,$id);
			$res = $wpdb->query($sql);
			if(!$wpdb->show_errors()) {
				$res = 1;
			}

			return $res;
		}
	}
	public static function findAll($orderBy = null, $limit = null, $offset = null) {

		global $wpdb;

		$query = "SELECT * FROM ". $wpdb->prefix ."any_popup";

		if ($orderBy) {
			$query .= " ORDER BY ".$orderBy;
		}

		if ($limit) {
			$query .= " LIMIT ".intval($offset).','.intval($limit);
		}

		//$st = $wpdb->prepare($query, array());
		$popups = $wpdb->get_results($query, ARRAY_A);

		$arr = array();
		foreach ($popups as $popup) {
			$arr[] = self::popupObjectFromArray($popup);
		}

		return $arr;
	}
	public static function delete($id) {
			$pop = self::findById($id);
			if(empty($pop)) {
				return false;
			}
			$type =  $pop->getType();
			$table = 'anypopup_'.$type.'_popup';

			if($type == 'mailchimp' || $type == 'aweber') {
				$table = 'anypopup_'.$type;
			}
			if($type == 'shortcode') {
				$table = 'anypopup_shortCode_popup';
			}
			else if($type == 'ageRestriction') {
				$table = 'anypopup_age_restriction_popup';
			}
			else if($type == 'contactForm') {
				$table = 'anypopup_contact_form_popup';
			}

			global $wpdb;
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM ". $wpdb->prefix ."$table WHERE id = %d"
					,$id
				)
			);
			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM ". $wpdb->prefix ."any_popup WHERE id = %d"
					,$id
				)
			);

			$wpdb->query(
				$wpdb->prepare(
					"DELETE FROM ". $wpdb->prefix ."postmeta WHERE meta_value = %d and meta_key = 'wp_any_popup'"
					,$id
				)
			);

		return true;
	}

	public static function setPopupForPost($post_id, $popupId) {
		update_post_meta($post_id, 'wp_any_popup' , $popupId);
	}

	public function getRemoveOptions() {
		return array();
	}

	public function improveContent($content) {
		$hasSameShortcode = strpos($content,'any_popup id="'.$this->getId().'"');

		if(ANYPOPUP_PKG !== ANYPOPUP_PKG_FREE && !$hasSameShortcode) {
			require_once(ANYPOPUP_APP_POPUP_FILES ."/anypopup_pro.php");
			return AnypopupPro::anypopupExtraSanitize($content);
		}
		return $content;
	}

	public function hasPopupContentShortcode($content) {

		global $shortcode_tags;

		if(ANYPOPUP_PKG == ANYPOPUP_PKG_FREE) {
			return false;
		}

		preg_match_all( '@\[([^<>&/\[\]\x00-\x20=]++)@', $content, $matches );
		$tagnames = array_intersect( array_keys( $shortcode_tags ), $matches[1] );

		/* If tagnames is empty it's mean content does not have shortcode */
		if (empty($tagnames)) {
			return false;
		}
		return true;

	}

	private function addPopupStyles() {
		$styles = '';
		$popupId = $this->getId();
		$options = $this->getOptions();
		$options = json_decode($options, true);
		$contentPadding = 0;
		if(empty($options)) {
			return '';
		}

		/*When popup z index does not exist we give to z - index max value*/
		if(empty($options['popup-z-index'])) {
			$popupZIndex = '2147483647';
		}
		else {
			$popupZIndex = $options['popup-z-index'];
		}

		if(!empty($options['popup-content-padding'])) {
			$contentPadding = $options['popup-content-padding'];
		}

		$styles .= '<style type="text/css">';

		$styles .= '.anypopup-popup-overlay-'.$popupId.',
					.anypopup-popup-content-'.$popupId.' {
						z-index: '.$popupZIndex.' !important;
					}
					#anypopup-popup-content-wrapper-'.$popupId.' {
						padding: '.$contentPadding.'px !important;
					}';

		/* if popup close button has delay,hide it */
		if ($options['closeButton'] && $options['buttonDelayValue']) {
			$styles .= '.anypopup-popup-content-'.$popupId.' #anypopupcboxClose {
				display: none !important;
			}';
		}

		$styles .= '</style>';

		echo $styles;
	}

	public function render() {
		/* When have popup with same id in the same page */
		$registryInstance = AnypopupRegistry::getInstance();
		$currentPopups = $registryInstance->getCurrentPopupsId();

		if(true || !in_array($this->getId(), $currentPopups)) {
			$popupId = $this->getId();
			$this->addPopupStyles();
			$hasPopupEvent = ANYPOPUPExtension::hasPopupEvent($popupId);
			$eventOptions = array('customEvent' => $hasPopupEvent);
			$parentOption = array('id'=>$this->getId(),'title'=>$this->getTitle(),'type'=>$this->getType(),'effect'=>$this->getEffect(),'width',$this->getWidth(),'height'=>$this->getHeight(),'delay'=>$this->getDelay(),'duration'=>$this->getEffectDuration(),'initialWidth',$this->getInitialWidth(),'initialHeight'=>$this->getInitialHeight());
			$extraOptionsArray = $this->getExtraRenderOptions();
			$extensionsDataArray = ANYPOPUPExtension::getExtensionsOptions($this->getId());

			$registryInstance->setCurrentPopupId($this->getId());

			$options = json_decode($this->getOptions(),true);
			if(empty($options)) $options = array();
			$popupOptions = array_merge($parentOption, $options, $extraOptionsArray, $extensionsDataArray, $eventOptions);
			$anypopupVars = 'ANYPOPUP_DATA['.$this->getId().'] ='.@json_encode($popupOptions, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE).';';

			return $anypopupVars;
		}
		return '';

	}
	public static function getTotalRowCount() {
		global $wpdb;
		$res =  $wpdb->get_var( "SELECT COUNT(id) FROM ". $wpdb->prefix ."any_popup" );
		return $res;
	}

	public static function getPagePopupId($page,$popup) {
		global $wpdb;
		$sql = $wpdb->prepare('SELECT meta_value FROM '. $wpdb->prefix .'postmeta WHERE post_id = %d AND meta_key = %s',$page,$popup);
		$row = $wpdb->get_row($sql);
		$id = 0;
		if($row) {
			$id =  (int)@$row->meta_value;
		}
		return $id;
	}

	public static function showPopupForCounrty($popupId) {

 		$obj = ANYPOPUP::findById($popupId);

 		if(!$obj) {
 			return true;
 		}

 		$isInArray = true;
 		$options = json_decode($obj->getOptions(), true);

 		$countryStatus = $options['countryStatus'];
 		$countryIso = $options['countryIso'];
 		$allowCountries = $options['allowCountries'];
 		$countryIsoArray = explode(',', $countryIso);

 		if($countryStatus) {

			$ip = ANYPOPUPFunctions::getUserIpAdress();

			$counrty = ANYPOPUPFunctions::getCountryName($ip);

 			if($allowCountries == 'allow') {
				$isInArray = in_array($counrty, $countryIsoArray);
 			}

 			if($allowCountries == 'disallow') {
				$isInArray = !in_array($counrty, $countryIsoArray);
 			}
 		}
 		return $isInArray;
 	}

	public static function addPopupForAllPages($id = '', $selectedData = array(), $type) {

		global $wpdb;

		$insertPreapre = array();
		$insertQuery = 'INSERT INTO '. $wpdb->prefix .'anypopup_in_pages(popupId, pageId, type) VALUES ';

		foreach ($selectedData as $value) {
			$insertPreapre[] .= $wpdb->prepare( "(%d,%s,%s)", $id, $value, $type);
		}
		$insertQuery .= implode( ",\n", $insertPreapre );
		$wpdb->query($insertQuery);
	}

	public static function removePopupFromPages($popupId, $type)
	{
		global $wpdb;
		/*Remove all pages and posts from the array*/
		self::removeFromAllPages($popupId);
		$query = $wpdb->prepare('DELETE FROM '.$wpdb->prefix.'anypopup_in_pages WHERE popupId = %d and type=%s', $popupId, $type);
		$wpdb->query($query);
	}

	public static function removeFromAllPages($id) {
		$allPages = get_option("ANYPOPUP_ALL_PAGES");
		$allPosts = get_option("ANYPOPUP_ALL_POSTS");

		if(is_array($allPages)) {
			$key = array_search($id, $allPages);

			if ($key !== false) {
				unset($allPages[$key]);
			}
			update_option("ANYPOPUP_ALL_PAGES", $allPages);
		}
		if(is_array($allPosts)) {
			$key = array_search($id, $allPosts);

			if ($key !== false) {
				unset($allPosts[$key]);
			}
			update_option("ANYPOPUP_ALL_POSTS", $allPosts);
		}

	}

	public static function deleteAllPagesPopup($selectedPages) {
		global $wpdb;

		$deletePrepare = array();
		$deleteQuery = 'DELETE FROM '. $wpdb->prefix .'anypopup_in_pages WHERE pageId IN (';

		foreach ($selectedPages as $value) {
			$deletePrepare[] .= $wpdb->prepare("%d", $value );
		}

		$deleteQuery .= implode( ",\n", $deletePrepare ).")";

		$deleteRes = $wpdb->query($deleteQuery);
	}

	public static function findInAllSelectedPages($pageId, $type) {
		global $wpdb;

		$st = $wpdb->prepare('SELECT * FROM '. $wpdb->prefix .'anypopup_in_pages WHERE pageId = %s and type=%s', $pageId, $type);
		$arr = $wpdb->get_results($st, ARRAY_A);
		if(!$arr) return false;
		return $arr;
	}

	/**
	 * Add popup data to footer
	 *
	 * @since 2.5.2
	 *
	 * @param string $content popup html content
	 * @param int $popupId popup Id
	 *
	 * @return void
	 *
	 */

	public function anypopupAddPopupContentToFooter($content, $popupId) {

		add_action('wp_footer', function() use ($content, $popupId){
			$content = apply_filters('anypopup_content', $content, $popupId);
			if(empty($content)) {
				$content = '';
			}
			$popupContent = "<div style=\"display:none\"><div id=\"anypopup-popup-content-wrapper-$popupId\">$content</div></div>";
			echo $popupContent;
		}, 1);
	}

	public function getSiteLocale() {

		$locale = get_bloginfo('language');
		$locale = str_replace('-', '_', $locale);

		return $locale;
	}

	protected function changeDimension($dimension) {

		if(empty($dimension)) {
			return 'inherit';
		}

		$size = (int)$dimension.'px';

		if(strpos($dimension, '%') || strpos($dimension, 'px')) {
			$size = $dimension;
		}

		return $size;
	}
}

function anypopupSafeStr ($param) {
	return ($param===null?'':$param);
}
