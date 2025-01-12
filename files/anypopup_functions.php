<?php
class ANYPOPUPFunctions
{
	public static function showInfo()
	{
		$anypopupInfo = '';
		$divisor = "<span class=\"info-vertical-divisor\">|</span>";
		$anypopupInfo .= "<span>If you like the plugin, please <a href=\"https://wordpress.org/support/view/plugin-reviews/any-popup?filter=5\" target=\"_blank\">rate it 5 stars</a></span>".$divisor;
		$anypopupInfo .= "<a href=\"https://wordpress.org/support/plugin/any-popup\" target=\"_blank\">Support</a>".$divisor;
		$anypopupInfo .= "<a href=\"https://www.youtube.com/watch?v=3ZwRKPhHMzY\" target=\"_blank\">How to create a popup</a>";
		echo $anypopupInfo;
	}

	public static function getMaxOpenPopupId() {

		$popupsCounterData = get_option('AnypopuppbCounter');
		if(!$popupsCounterData) {
			return 0;
		}

		$counters = array_values($popupsCounterData);
		$maxCount = max($counters);
		$popupId  = array_search($maxCount, $popupsCounterData);

		$maxPopupData = array(
			'popupId' => $popupId,
			'maxCount' => $maxCount
		);

		return $maxPopupData;
	}

	public static function getPopupMainTableCreationDate()
	{
		global $wpdb;

		$query = $wpdb->prepare('SELECT table_name,create_time FROM information_schema.tables WHERE table_schema="%s" AND  table_name="%s"', DB_NAME, $wpdb->prefix.'any_popup');
		$results = $wpdb->get_results($query, ARRAY_A);

		if(empty($results)) {
			return 0;
		}

		$createTime = $results[0]['create_time'];
		$createTime = strtotime($createTime);
		update_option('ANYPOPUPInstallDate', $createTime);
		$diff = time()-$createTime;
		$days  = floor($diff/(60*60*24));

		return $days;
	}

	public static function shouldOpenReviewPopupForDays()
	{
		$shouldOpen = true;
		$dontShowAgain = get_option('ANYPOPUPCloseReviewPopup');
		$periodNextTime = get_option('ANYPOPUPOpenNextTime');

		if($dontShowAgain) {
			return false;
		}

		/*When period next time does not exits it means the user is old*/
		if(!$periodNextTime) {

			$usageDays = self::getPopupMainTableCreationDate();
			update_option('ANYPOPUPUsageDays', $usageDays);
			/*When very old user*/
			if($usageDays > ANYPOPUP_REVIEW_POPUP_PERIOD && !$dontShowAgain) {
				return $shouldOpen;
			}
			$remainingDays = ANYPOPUP_REVIEW_POPUP_PERIOD - $usageDays;

			$popupTimeZone = @AnypopupGetData::getPopupTimeZone();
			$timeDate = new DateTime('now', new DateTimeZone($popupTimeZone));
			$timeDate->modify('+'.$remainingDays.' day');

			$timeNow = strtotime($timeDate->format('Y-m-d H:i:s'));
			update_option('ANYPOPUPOpenNextTime', $timeNow);

			return false;
		}

		$currentData = new DateTime('now');
		$timeNow = $currentData->format('Y-m-d H:i:s');
		$timeNow = strtotime($timeNow);

		if($periodNextTime > $timeNow) {
			return false;
		}

		return $shouldOpen;
	}

	public static function shouldOpenForMaxOpenPopupMessage()
	{
		$counterMaxPopup = self::getMaxOpenPopupId();

		if(empty($counterMaxPopup)) {
			return false;
		}
		$dontShowAgain = get_option('ANYPOPUPCloseReviewPopup');
		$maxCountDefine = get_option('ANYPOPUPMaxOpenCount');
		if(!$maxCountDefine) {
			$maxCountDefine = ANYPOPUP_SHOW_COUNT;
		}
		return $counterMaxPopup['maxCount'] >= $maxCountDefine && !$dontShowAgain;
	}

	public static function getPopupUsageDays()
	{
		$installDate = get_option('ANYPOPUPInstallDate');

		$timeDate = new DateTime('now');
		$timeNow = strtotime($timeDate->format('Y-m-d H:i:s'));

		$diff = $timeNow-$installDate;

		$days  = floor($diff/(60*60*24));

		return $days;
	}

	public static function getMaxOpenDaysMessage()
	{
		$getUsageDays = self::getPopupUsageDays();
		$firstHeader = '<h1 class="anypopuppb-review-h1"><strong class="anypopuprb-review-strong">Wow!</strong> You’ve been using Any Popup on your site for '.$getUsageDays.' days</h1>';
		$popupContent = self::getMaxOepnPopupContent($firstHeader, 'days');

		$popupContent .= self::showReviewBlockJs();

		return $popupContent;
	}

	public static function getMaxOpenPopupsMessage()
	{
		$counterMaxPopup = self::getMaxOpenPopupId();
		$popupTitle = '';
		$maxCountDefine = get_option('ANYPOPUPMaxOpenCount');
		$popupData = ANYPOPUP::findById($counterMaxPopup['popupId']);

		if(!empty($counterMaxPopup['maxCount'])) {
			$maxCountDefine = $counterMaxPopup['maxCount'];
		}

		if(!empty($popupData)) {
			$popupTitle = $popupData->getTitle();
		}

		$firstHeader = '<h1 class="anypopuppb-review-h1"><strong class="anypopuprb-review-strong">Wow!</strong> <b>Any Popup</b> plugin helped you to share your message via <strong class="anypopuprb-review-strong">'.$popupTitle.'</strong> popup with your users for <strong class="anypopuprb-review-strong">'.$maxCountDefine.' times!</strong></h1>';
		$popupContent = self::getMaxOepnPopupContent($firstHeader, 'count');

		$popupContent .= self::showReviewBlockJs();

		return $popupContent;
	}

	public static function showReviewBlockJs()
	{
		ob_start();
		?>
		<script type="text/javascript">
			jQuery('.anypopup-already-did-review').each(function () {
				jQuery(this).on('click', function () {
					var ajaxNonce = jQuery(this).attr('data-ajaxnonce');

					var data = {
						action: 'dont_show_review_popup',
						ajaxNonce: ajaxNonce
					};
					jQuery.post(ajaxurl, data, function(response,d) {
						if(typeof jQuery.anypopupcolorbox != 'undefined') {
							jQuery.anypopupcolorbox.close();
						}
						if(jQuery('.anypopuppb-review-block').length) {
							jQuery('.anypopuppb-review-block').remove();
						}
					});
				});
			});

			jQuery('.anypopup-show-popup-period').on('click', function () {
				var ajaxNonce = jQuery(this).attr('data-ajaxnonce');
				var messageType = jQuery(this).attr('data-message-type');

				var data = {
					action: 'change_review_popup_show_period',
					messageType: messageType,
					ajaxNonce: ajaxNonce
				};
				jQuery.post(ajaxurl, data, function(response,d) {
					if(typeof jQuery.anypopupcolorbox != 'undefined') {
						jQuery.anypopupcolorbox.close();
					}
					if(jQuery('.anypopuppb-review-block').length) {
						jQuery('.anypopuppb-review-block').remove();
					}
				});
			});
		</script>
		<?php
		$content = ob_get_clean();

		return $content;
	}

	public static function showReviewPopup()
	{
		$popupContent = '';

		$maxOpenPopupStatus = self::shouldOpenForMaxOpenPopupMessage();

		if($maxOpenPopupStatus) {
			$popupContent = self::getMaxOpenPopupsMessage();
			self::addContentToFooter($popupContent);
			self::openReviewPopup();
			return;
		}

		$shouldOpenForDays = self::shouldOpenReviewPopupForDays();

		if($shouldOpenForDays) {
			$popupContent = self::getMaxOpenDaysMessage();
			self::addContentToFooter($popupContent);
			self::openReviewPopup();
			return;
		}
	}

	public static function addContentToFooter($popupContent)
	{
		add_action('admin_footer', function() use ($popupContent){
			$popupContent = "<div style=\"display:none\"><div id=\"anypopup-popup-content-wrapper\">$popupContent</div></div>";
			echo $popupContent;
		}, 1);
	}

	public static function openReviewPopup()
	{
		wp_register_script('anypopup_colorbox', ANYPOPUP_APP_POPUP_URL . '/javascript/jquery.anypopupcolorbox-min.js', array('jquery'), ANYPOPUP_VERSION);
		wp_enqueue_script('anypopup_colorbox');
		wp_register_style('anypopup_colorbox_theme', ANYPOPUP_APP_POPUP_URL . "/style/anypopupcolorbox/anypopupthemes.css", array(), ANYPOPUP_VERSION);
		wp_enqueue_style('anypopup_colorbox_theme');
		$ajaxNonce = wp_create_nonce("anypopupAnyPopupReview");

		echo "<script>
			jQuery(document).ready(function() {
				ANYPOPUP_AJAX_NONCE = '".$ajaxNonce."';
				var ANYPOPUP_SETTINGS = {
					inline: true,
					escKey: false,
					closeButton: false,
					overlayClose: false,
					href: '#anypopup-popup-content-wrapper',
					onOpen: function() {
						jQuery('#anypopupcboxOverlay').addClass('anypopupcboxOverlayBg');
					},
					onCleanup: function () {
						jQuery('#anypopupcolorbox').trigger('anypopupCleanup', []);
					},
					maxWidth: 640
				};
				jQuery.anypopupcolorbox(ANYPOPUP_SETTINGS);
			});
		</script>";
	}

	public static function getMaxOepnPopupContent($firstHeader, $type) {
		$ajaxNonce = wp_create_nonce("anypopupAnyPopupReview");
		ob_start();
		?>
		<style>
			.anypopuppb-buttons-wrapper .press{
				box-sizing:border-box;
				cursor:pointer;
				display:inline-block;
				font-size:1em;
				margin:0;
				padding:0.5em 0.75em;
				text-decoration:none;
				transition:background 0.15s linear
			}
			.anypopuppb-buttons-wrapper .press-grey {
				background-color:#9E9E9E;
				border:2px solid #9E9E9E;
				color: #FFF;
			}
			.anypopuppb-buttons-wrapper .press-lightblue {
				background-color:#03A9F4;
				border:2px solid #03A9F4;
				color: #FFF;
			}
			.anypopuppb-review-wrapper{
				text-align: center;
				padding: 20px;
			}
			.anypopuppb-review-wrapper p {
				color: black;
			}
			.anypopuppb-review-h1 {
				font-size: 22px;
				font-weight: normal;
				line-height: 1.384;
			}
			.anypopuprb-review-h2{
				font-size: 20px;
				font-weight: normal;
			}
			:root {
				--main-bg-color: #1ac6ff;
			}
			.anypopuprb-review-strong{
				color: var(--main-bg-color);
			}
			.anypopuprb-review-mt20{
				margin-top: 20px
			}
		</style>
		<div class="anypopuppb-review-wrapper">
			<div class="anypopuppb-review-description">
				<?php echo $firstHeader; ?>
				<h2 class="anypopuprb-review-h2">This is really great for your website score.</h2>
				<p class="anypopuprb-review-mt20">Have your input in the development of our plugin, and we’ll provide better conversions for your site!<br /> Leave your 5-star positive review and help us go further to the perfection!</p>
			</div>
			<div class="anypopuppb-buttons-wrapper">
				<button class="press press-grey anypopuppb-button-1 anypopup-already-did-review" data-ajaxnonce="<?php echo esc_attr($ajaxNonce); ?>">I already did</button>
				<button class="press press-lightblue anypopuppb-button-3 anypopup-already-did-review" data-ajaxnonce="<?php echo esc_attr($ajaxNonce); ?>" onclick="window.open('<?php echo ANYPOPUP_REVIEW_URL; ?>')">You worth it!</button>
				<button class="press press-grey anypopuppb-button-2 anypopup-show-popup-period" data-ajaxnonce="<?php echo esc_attr($ajaxNonce); ?>" data-message-type="<?php echo $type; ?>">Maybe later</button></div>
			<div> </div>
		</div>
		<?php
		$popupContent = ob_get_clean();

		return $popupContent;
	}

	public static function anypopupDataSanitize($anypopupData)
	{
		/*Remove iframe tag and empty line*/
		$pattern = '/\s+(<iframe.*?>.*?<\/iframe>)/';

		return preg_replace($pattern, '', $anypopupData);
	}

	public static function getPopupsDataList($restrictParams = array()) {

		$orderBy = 'id DESC';
		$popupsData = ANYPOPUP::findAll($orderBy);
		$dataList = array();

		foreach ($popupsData as $popupData) {

			if(empty($popupData)) {
				continue;
			}

			$title = $popupData->getTitle();
			$type = $popupData->getType();
			$id = $popupData->getId();
			if(is_array($restrictParams)) {
				if(isset($restrictParams['type']) && $type == $restrictParams['type']) {
					continue;
				}
				if(isset($restrictParams['id']) && $id == $restrictParams['id']) {
					continue;
				}
			}
			$dataList[$id] = $title.' - '.$type;
		}

		return $dataList;

	}

	public static function popupTablesDeleteSatus() {

		global $wpdb;

		$st = $wpdb->prepare("SELECT * FROM ". $wpdb->prefix ."anypopup_settings WHERE id = %d",1);
		$arr = $wpdb->get_row($st,ARRAY_A);

		if(empty($arr)) {
			return true;
		}

		$options = json_decode($arr['options'], true);
		$deleteStatus = ($options['tables-delete-status'] == 'on' ? true: false);

		return $deleteStatus;
	}

	public static function addReview()
	{
		$ajaxNonce = wp_create_nonce("anypopupAnyPopupReview");
		ob_start();
		?>
		<div class="anypopup-info-panel-wrapper">
			<div class="anypopup-info-panel-row">
				<div class="anypopup-info-panel-col-3">
					<p class="anypopup-info-text anypopup-info-logo">
						<span class="anypopup-info-text-white">Any</span><span class="anypopup-info-text-highlight">Popup</span>
					</p>
					<p class="anypopup-info-text">
						If you have any difficulties in using the options, please follow the link to <a href="https://jcodex.com/any-popup" class="anypopup-info-link">Knowledge Base</a>
					</p>
				</div>
				<div class="anypopup-info-panel-col-3 anypopup-info-text-center">
					<?php if (ANYPOPUP_PKG == ANYPOPUP_PKG_FREE): ?>
						<a class="anypopup-info-upgrade-pro anypopup-info-blink" href="http://any-popup.com" target="_blank">
							Upgrade NOW
						</a>
						<p class="anypopup-info-text">
							Want to upgrade to PRO version?<br> Just click on "Upgrade NOW".
						</p>
					<?php endif; ?>
					<?php if (ANYPOPUP_PKG != ANYPOPUP_PKG_FREE): ?>
						<p class="anypopup-social-headline">Follow us</p>
						<div class="anypopup-social-icons-wrapper">
							<a class="anypopup-social-icon social-icon-fb" href="https://www.facebook.com/wppopupmagic/" target="_blank"></a>
							<a class="anypopup-social-icon social-icon-tw" href="https://twitter.com/wppopupmagic" target="_blank"></a>
							<a class="anypopup-social-icon social-icon-gp" href="https://plus.google.com/u/0/+AnyPopupcom" target="_blank"></a>
						</div>
					<?php endif; ?>
				</div>
				<div class="anypopup-info-panel-col-3">
					<ul class="anypopup-info-menu anypopup-info-text">
						<li>
							<a class="anypopup-info-links" target="_blank" href="https://wordpress.org/support/plugin/any-popup/reviews/?filter=5"><span class="dashicons dashicons-heart anypopup-info-text-white"></span><span class="anypopup-info-text"> Rate Us</span></a>
						</li>
						<li>
							<a class="anypopup-info-links" target="_blank" href="https://sygnoos.ladesk.com/submit_ticket"><span class="dashicons dashicons-megaphone anypopup-info-text-white"></span></span> Submit Ticket</a>
						</li>
						<li>
							<a class="anypopup-info-links" target="_blank" href="https://wordpress.org/support/plugin/any-popup"><span class="dashicons dashicons-admin-plugins anypopup-info-text-white"></span> Support</a>
						</li>
						<li>
							<a class="anypopup-info-links" target="_blank" href="https://wordpress.org/plugins/any-popup/faq/"><span class="dashicons dashicons-editor-help anypopup-info-text-white"></span> FAQ</a>
						</li>
						<li>
							<a class="anypopup-info-links" target="_blank" href="mailto:support@any-popup.com?subject=Hello"><span class="dashicons dashicons-email-alt anypopup-info-text-white"></span></span> Contact</a>
						</li>
					</ul>
				</div>
			</div>
			<div>
				<span class="anypopup-info-close">+</span>
				<?php echo '<span class="anypopup-dont-show-agin" data-ajaxnonce="'.esc_attr($ajaxNonce).'">Don’t show again.</span>'; ?>
			</div>
		</div>
		<?php
		$reviewPanel = ob_get_contents();
		ob_end_clean();

		return $reviewPanel;
	}

	public static function newYear()
	{
		$reviewPanel = '';
		wp_register_style('blackFriday', ANYPOPUP_APP_POPUP_URL . '/style/newYear.css');
		wp_enqueue_style('blackFriday');
		wp_register_script('blackFriday', ANYPOPUP_APP_POPUP_URL . '/javascript/NewYear.js', array(), ANYPOPUP_VERSION);
		wp_enqueue_script('blackFriday');
		$ajaxNonce = wp_create_nonce("anypopupAnyPopupNewYear");
		$newYearShow = get_option('anypopupAnyPopupNewYear');
		$timeDate = new DateTime('now', new DateTimeZone('Asia/Yerevan'));
		$timeNow = strtotime($timeDate->format('Y-m-d H:i:s'));
		$startTime = '1513343100';
		$endData = '1515369540';
		$currentUser = wp_get_current_user();
		$niceName = '';

		if (!empty($currentUser)) {
			$niceName = $currentUser->nickname;
		}
		if(!($timeNow > $startTime && $timeNow < $endData) || $newYearShow) {
			return $reviewPanel;
		}
		ob_start();
		?>
		<div style="margin-right: 15px;">
			<div class="anypopup-info-panel-wrapper anypopup-info-panel-new-year">

				<div class="anypopup-new-year-text-wrapper">
					<p>Hi <?php echo $niceName; ?></p>
					<p>Merry Christmas and Happy New Year</p>
					<p>Take your 25% sale right now!</p>
					<button onclick="window.open('https://any-popup.com/')" class="anypopuppb-happy-button" >USE THIS CODE: HAPPY 25</button>
				</div>
				<span data-ajaxnonce="<?php echo $ajaxNonce; ?>" class="anypopuppb-new-year-dont-show">Don’t show again</span>
			</div>
		</div>
		<?php
		$newYearPanel = ob_get_contents();
		ob_end_clean();

		return $newYearPanel;
	}

	public static function noticeForShortcode() {
		$notice = '<span class="shortcode-use-info">NOTE: Shortcodes doesn\'t work inside the HTML Popup. Please use <a href="'.ANYPOPUP_APP_POPUP_ADMIN_URL.'admin.php?page=anypopup-edit-popup&type=shortcode">Shortcode Popup</a> instead.</span>';
		return $notice;
	}

	public static function createSelectBox($data, $selectedValue, $attrs) {

		$attrString = '';
		$selected = '';

		if(!empty($attrs) && isset($attrs)) {

			foreach ($attrs as $attrName => $attrValue) {
				$attrString .= ''.$attrName.'="'.$attrValue.'" ';
			}
		}

		$selectBox = '<select '.$attrString.'>';

		foreach ($data as $value => $label) {

			/*When is multiselect*/
			if(is_array($selectedValue)) {
				$isSelected = in_array($value, $selectedValue);
				if($isSelected) {
					$selected = 'selected';
				}
			}
			else if($selectedValue == $value) {
				$selected = 'selected';
			}
			else if(is_array($value) && in_array($selectedValue, $value)) {
				$selected = 'selected';
			}

			$selectBox .= '<option value="'.$value.'" '.$selected.'>'.$label.'</option>';
			$selected = '';
		}

		$selectBox .= '</select>';

		return $selectBox;
	}

	public static function anypopupCreateRadioElements($radioElements, $checkedValue)
	{
		$content = '';
		for ($i = 0; $i < count($radioElements); $i++) {
			$checked = '';
			$br = '';
			$radioElement = @$radioElements[$i];
			$name = @$radioElement['name'];
			$label = @$radioElement['label'];
			$value = @$radioElement['value'];
			$brValue = @$radioElement['newline'];
			$additionalHtml = @$radioElement['additionalHtml'];
			$labelId = 'anypopup-select-'.$name.'-'.$i;
			if($checkedValue == $value) {
				$checked = 'checked';
			}
			if($brValue) {
				$br = "<br>";
			}
			$content .= '<label for="'.$labelId.'"><input id="'.$labelId.'" class="radio-btn-fix" type="radio" name="'.$name.'" value="'.$value.'" '.$checked.'>';
			$content .= $additionalHtml.$br;
		}
		return $content;
	}

	public static function getUserIpAdress() {
		$ipaddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
		else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
		else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
		else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
		else if(getenv('HTTP_FORWARDED'))
			$ipaddress = getenv('HTTP_FORWARDED');
		else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}

	public static function getCountryName($ip)
	{
		if(empty($_COOKIE['ANYPOPUP_USER_COUNTRY_NAME'])) {
			require_once(ANYPOPUP_APP_POPUP_FILES."/lib/SxGeo/SxGeo.php");

			$SxGeo = new SxGeo(ANYPOPUP_APP_POPUP_FILES."/lib/SxGeo/SxGeo.dat");
			$counrty = $SxGeo->getCountry($ip);

			/*When Ip addres does not correct*/
			if($counrty == '') {
				return true;
			}
		}
		else {
			$counrty = $_COOKIE['ANYPOPUP_USER_COUNTRY_NAME'];
		}

		return $counrty;
	}

	public static function getUserLocationData($popupId) {
		$obj = ANYPOPUP::findById($popupId);
		$countryStatus = '';
		$countryName = '';

		if($obj) {
			$options = json_decode($obj->getOptions(), true);
			$countryStatus = $options['countryStatus'];
		}

		if(!empty($countryStatus)) {
			$ip = ANYPOPUPFunctions::getUserIpAdress();
			$countryName = ANYPOPUPFunctions::getCountryName($ip);
		}
		if(!empty($countryName)) {
			return $countryName;
		}
		return false;
	}

	public static function isShowMenuForCurrentUser() {
		$usersSelectedRoles = AnypopupGetData::getValue('plugin_users_role', 'settings');

		$currentUserRole = AnypopupGetData::getCurrentUserRole();

		if((!empty($usersSelectedRoles) && !in_array($currentUserRole, $usersSelectedRoles)) && !is_super_admin()) {
			return false;
		}

		return true;
	}

	public static function getCurrentPopupIdFromOptions($id) {

		$allPosts = get_option("ANYPOPUP_ALL_POSTS");

		if(!is_array($allPosts)) {
			return false;
		}

		foreach ($allPosts as $key => $post) {
			if($post['id'] == $id) {
				return $key;
			}
		}

		return false;
	}

	public static function findInAllPostTypeData($popupId, $allPosts)
	{
		$searchData = array();

		if(empty($allPosts)) {
			return $searchData;
		}

		foreach ($allPosts as $key => $postData) {

			if(!empty($postData['id']) && $postData['id'] == $popupId) {
				$searchData = $postData;
				break;
			}
		}

		return $searchData;
	}

	public static function countrisSelect() {

		return '<select id="sameWidthinputs" name="countris" class="optionsCountry"  data-role="tagsinput">
					<option value="AF">Afghanistan</option>
					<option value="AX">Åland Islands</option>
					<option value="AL">Albania</option>
					<option value="DZ">Algeria</option>
					<option value="AS">American Samoa</option>
					<option value="AD">Andorra</option>
					<option value="AO">Angola</option>
					<option value="AI">Anguilla</option>
					<option value="AQ">Antarctica</option>
					<option value="AG">Antigua and Barbuda</option>
					<option value="AR">Argentina</option>
					<option value="AM">Armenia</option>
					<option value="AW">Aruba</option>
					<option value="AU">Australia</option>
					<option value="AT">Austria</option>
					<option value="AZ">Azerbaijan</option>
					<option value="BS">Bahamas</option>
					<option value="BH">Bahrain</option>
					<option value="BD">Bangladesh</option>
					<option value="BB">Barbados</option>
					<option value="BY">Belarus</option>
					<option value="BE">Belgium</option>
					<option value="BZ">Belize</option>
					<option value="BJ">Benin</option>
					<option value="BM">Bermuda</option>
					<option value="BT">Bhutan</option>
					<option value="BO">Bolivia Plurinational State</option>
					<option value="BQ">Bonaire Sint Eustatius and Saba</option>
					<option value="BA">Bosnia and Herzegovina</option>
					<option value="BW">Botswana</option>
					<option value="BV">Bouvet Island</option>
					<option value="BR">Brazil</option>
					<option value="IO">British Indian Ocean Territory</option>
					<option value="BN">Brunei Darussalam</option>
					<option value="BG">Bulgaria</option>
					<option value="BF">Burkina Faso</option>
					<option value="BI">Burundi</option>
					<option value="KH">Cambodia</option>
					<option value="CM">Cameroon</option>
					<option value="CA">Canada</option>
					<option value="CV">Cape Verde</option>
					<option value="KY">Cayman Islands</option>
					<option value="CF">Central African Republic</option>
					<option value="TD">Chad</option>
					<option value="CL">Chile</option>
					<option value="CN">China</option>
					<option value="CX">Christmas Island</option>
					<option value="CC">Cocos (Keeling) Islands</option>
					<option value="CO">Colombia</option>
					<option value="KM">Comoros</option>
					<option value="CG">Congo</option>
					<option value="CD">Congo the Democratic Republic</option>
					<option value="CK">Cook Islands</option>
					<option value="CR">Costa Rica</option>
					<option value="CI">Côte d\'Ivoire</option>
					<option value="HR">Croatia</option>
					<option value="CU">Cuba</option>
					<option value="CW">Curaçao</option>
					<option value="CY">Cyprus</option>
					<option value="CZ">Czech Republic</option>
					<option value="DK">Denmark</option>
					<option value="DJ">Djibouti</option>
					<option value="DM">Dominica</option>
					<option value="DO">Dominican Republic</option>
					<option value="EC">Ecuador</option>
					<option value="EG">Egypt</option>
					<option value="SV">El Salvador</option>
					<option value="GQ">Equatorial Guinea</option>
					<option value="ER">Eritrea</option>
					<option value="EE">Estonia</option>
					<option value="ET">Ethiopia</option>
					<option value="FK">Falkland Islands (Malvinas)</option>
					<option value="FO">Faroe Islands</option>
					<option value="FJ">Fiji</option>
					<option value="FI">Finland</option>
					<option value="FR">France</option>
					<option value="GF">French Guiana</option>
					<option value="PF">French Polynesia</option>
					<option value="TF">French Southern Territories</option>
					<option value="GA">Gabon</option>
					<option value="GM">Gambia</option>
					<option value="GE">Georgia</option>
					<option value="DE">Germany</option>
					<option value="GH">Ghana</option>
					<option value="GI">Gibraltar</option>
					<option value="GR">Greece</option>
					<option value="GL">Greenland</option>
					<option value="GD">Grenada</option>
					<option value="GP">Guadeloupe</option>
					<option value="GU">Guam</option>
					<option value="GT">Guatemala</option>
					<option value="GG">Guernsey</option>
					<option value="GN">Guinea</option>
					<option value="GW">Guinea-Bissau</option>
					<option value="GY">Guyana</option>
					<option value="HT">Haiti</option>
					<option value="HM">Heard Island and McDonald Islands</option>
					<option value="VA">Holy See (Vatican City State)</option>
					<option value="HN">Honduras</option>
					<option value="HK">Hong Kong</option>
					<option value="HU">Hungary</option>
					<option value="IS">Iceland</option>
					<option value="IN">India</option>
					<option value="ID">Indonesia</option>
					<option value="IR">Iran Islamic Republic of</option>
					<option value="IQ">Iraq</option>
					<option value="IE">Ireland</option>
					<option value="IM">Isle of Man</option>
					<option value="IL">Israel</option>
					<option value="IT">Italy</option>
					<option value="JM">Jamaica</option>
					<option value="JP">Japan</option>
					<option value="JE">Jersey</option>
					<option value="JO">Jordan</option>
					<option value="KZ">Kazakhstan</option>
					<option value="KE">Kenya</option>
					<option value="KI">Kiribati</option>
					<option value="KP">Korea Democratic People\'s Republic</option>
					<option value="KR">Korea</option>
					<option value="KW">Kuwait</option>
					<option value="KG">Kyrgyzstan</option>
					<option value="LA">Lao People\'s Democratic Republic</option>
					<option value="LV">Latvia</option>
					<option value="LB">Lebanon</option>
					<option value="LS">Lesotho</option>
					<option value="LR">Liberia</option>
					<option value="LY">Libya</option>
					<option value="LI">Liechtenstein</option>
					<option value="LT">Lithuania</option>
					<option value="LU">Luxembourg</option>
					<option value="MO">Macao</option>
					<option value="MK">Macedonia the former Yugoslav Republic</option>
					<option value="MG">Madagascar</option>
					<option value="MW">Malawi</option>
					<option value="MY">Malaysia</option>
					<option value="MV">Maldives</option>
					<option value="ML">Mali</option>
					<option value="MT">Malta</option>
					<option value="MH">Marshall Islands</option>
					<option value="MQ">Martinique</option>
					<option value="MR">Mauritania</option>
					<option value="MU">Mauritius</option>
					<option value="YT">Mayotte</option>
					<option value="MX">Mexico</option>
					<option value="FM">Micronesia Federated States</option>
					<option value="MD">Moldova</option>
					<option value="MC">Monaco</option>
					<option value="MN">Mongolia</option>
					<option value="ME">Montenegro</option>
					<option value="MS">Montserrat</option>
					<option value="MA">Morocco</option>
					<option value="MZ">Mozambique</option>
					<option value="MM">Myanmar</option>
					<option value="NA">Namibia</option>
					<option value="NR">Nauru</option>
					<option value="NP">Nepal</option>
					<option value="NL">Netherlands</option>
					<option value="NC">New Caledonia</option>
					<option value="NZ">New Zealand</option>
					<option value="NI">Nicaragua</option>
					<option value="NE">Niger</option>
					<option value="NG">Nigeria</option>
					<option value="NU">Niue</option>
					<option value="NF">Norfolk Island</option>
					<option value="MP">Northern Mariana Islands</option>
					<option value="NO">Norway</option>
					<option value="OM">Oman</option>
					<option value="PK">Pakistan</option>
					<option value="PW">Palau</option>
					<option value="PS">Palestinian Territory</option>
					<option value="PA">Panama</option>
					<option value="PG">Papua New Guinea</option>
					<option value="PY">Paraguay</option>
					<option value="PE">Peru</option>
					<option value="PH">Philippines</option>
					<option value="PN">Pitcairn</option>
					<option value="PL">Poland</option>
					<option value="PT">Portugal</option>
					<option value="PR">Puerto Rico</option>
					<option value="QA">Qatar</option>
					<option value="RE">Réunion</option>
					<option value="RO">Romania</option>
					<option value="RU">Russian Federation</option>
					<option value="RW">Rwanda</option>
					<option value="BL">Saint Barthélemy</option>
					<option value="SH">Saint Helena Ascension and Tristan da Cunha</option>
					<option value="KN">Saint Kitts and Nevis</option>
					<option value="LC">Saint Lucia</option>
					<option value="MF">Saint Martin (French part)</option>
					<option value="PM">Saint Pierre and Miquelon</option>
					<option value="VC">Saint Vincent and the Grenadines</option>
					<option value="WS">Samoa</option>
					<option value="SM">San Marino</option>
					<option value="ST">Sao Tome and Principe</option>
					<option value="SA">Saudi Arabia</option>
					<option value="SN">Senegal</option>
					<option value="RS">Serbia</option>
					<option value="SC">Seychelles</option>
					<option value="SL">Sierra Leone</option>
					<option value="ANYPOPUP">Singapore</option>
					<option value="SX">Sint Maarten (Dutch part)</option>
					<option value="SK">Slovakia</option>
					<option value="SI">Slovenia</option>
					<option value="SB">Solomon Islands</option>
					<option value="SO">Somalia</option>
					<option value="ZA">South Africa</option>
					<option value="GS">South Georgia and the South Sandwich Islands</option>
					<option value="SS">South Sudan</option>
					<option value="ES">Spain</option>
					<option value="LK">Sri Lanka</option>
					<option value="SD">Sudan</option>
					<option value="SR">Suriname</option>
					<option value="SJ">Svalbard and Jan Mayen</option>
					<option value="SZ">Swaziland</option>
					<option value="SE">Sweden</option>
					<option value="CH">Switzerland</option>
					<option value="SY">Syrian Arab Republic</option>
					<option value="TW">Taiwan Province of China</option>
					<option value="TJ">Tajikistan</option>
					<option value="TZ">Tanzania United Republic of</option>
					<option value="TH">Thailand</option>
					<option value="TL">Timor-Leste</option>
					<option value="TG">Togo</option>
					<option value="TK">Tokelau</option>
					<option value="TO">Tonga</option>
					<option value="TT">Trinidad and Tobago</option>
					<option value="TN">Tunisia</option>
					<option value="TR">Turkey</option>
					<option value="TM">Turkmenistan</option>
					<option value="TC">Turks and Caicos Islands</option>
					<option value="TV">Tuvalu</option>
					<option value="UG">Uganda</option>
					<option value="UA">Ukraine</option>
					<option value="AE">United Arab Emirates</option>
					<option value="GB">United Kingdom</option>
					<option value="US">United States</option>
					<option value="UM">United States Minor Outlying Islands</option>
					<option value="UY">Uruguay</option>
					<option value="UZ">Uzbekistan</option>
					<option value="VU">Vanuatu</option>
					<option value="VE">Venezuela Bolivarian Republic</option>
					<option value="VN">Viet Nam</option>
					<option value="VG">Virgin Islands British</option>
					<option value="VI">Virgin Islands U.S.</option>
					<option value="WF">Wallis and Futuna</option>
					<option value="EH">Western Sahara</option>
					<option value="YE">Yemen</option>
					<option value="ZM">Zambia</option>
					<option value="ZW">Zimbabwe</option>
				</select>';
	}
}
