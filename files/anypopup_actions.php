<?php
class anypopupActions {

	public function __construct() {

		$this->actions();
	}

	private function actions() {

		add_action('anypopupDelete', array($this, 'anypopupDeleteAction'));
		add_action('admin_notices', array($this, 'anypopupShowReviewNotice'));
		add_action('network_admin_notices', array($this, 'anypopupShowReviewNotice'));
		add_action('user_admin_notices', array($this, 'anypopupShowReviewNotice'));
	}

	public function anypopupDeleteAction($args) {

		$extensionManagerObj = new ANYPOPUPExtensionManager();
		$popupCount = get_option('ANYPOPUPMaxOpenCount');
		$popupId = $args['popupId'];

		if(is_array($popupCount) && isset($popupCount[$popupId])) {
			unset($popupCount[$popupId]);
			update_option('ANYPOPUPMaxOpenCount', $popupId);
		}
		$extensionManagerObj->deletePopupFromConnection($popupId);
		$this->deletePopupFromAllPostTypes($popupId);
	}

	private function deletePopupFromAllPostTypes($popupId) {

		$popupId = (int)$popupId;
		$allPosts = get_option("ANYPOPUP_ALL_POSTS");
		$popupKey = ANYPOPUPFunctions::getCurrentPopupIdFromOptions($popupId);

		if(!$popupKey) {
			unset($allPosts[$popupKey]);
			update_option("ANYPOPUP_ALL_POSTS", $allPosts);
		}
	}

	public function newYear()
	{
		$messageContent = ANYPOPUPFunctions::newYear();

		echo $messageContent;
	}

	public function anypopupShowReviewNotice()
	{
		$this->newYear();
		$messageContent = '';
		$maxOpenPopupStatus = ANYPOPUPFunctions::shouldOpenForMaxOpenPopupMessage();
		$shouldOpenForDays = ANYPOPUPFunctions::shouldOpenReviewPopupForDays();

		if($maxOpenPopupStatus) {
			$messageContent = ANYPOPUPFunctions::getMaxOpenPopupsMessage();
		}
		else if($shouldOpenForDays) {
			$messageContent = ANYPOPUPFunctions::getMaxOpenDaysMessage();
		}

		if(empty($messageContent)) {
			return $messageContent;
		}
		ob_start();
		?>
		<div id="welcome-panel" class="welcome-panel anypopuppb-review-block">
			<div class="welcome-panel-content">
				<?php echo $messageContent; ?>
			</div>
		</div>
		<?php
		$content = ob_get_clean();

		echo $content;
		return '';
	}
}

$actionsObj = new anypopupActions();

function anypopupPluginLoaded() {

	$versionPopup = get_option('ANYPOPUP_VERSION');
	if (!$versionPopup || $versionPopup < ANYPOPUP_VERSION ) {
		update_option('ANYPOPUP_VERSION', ANYPOPUP_VERSION);
		PopupInstaller::install();
	}
}

add_action('plugins_loaded', 'anypopupPluginLoaded');

function anypopupnewslatter_repeat_function($args) {

	global $wpdb;
	/*Args is json from newsletter form parameters*/
	$params= json_decode($args, true);

	$subscriptionType = $params['subsFormType'];
	$sendingLimit = $params['emailsOneTime'];
	$emailMessage = $params['messageBody'];
	$mailSubject = $params['newsletterSubject'];
	$fromEmail = $params['fromEmail'];
	if (!preg_match("/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+.[a-zA-Z0-9-.]+$/", $fromEmail)) {
		$fromEmail = "anypopup@gmail.com";
	}
	$successMails = 0;
	$allData = array();
	$adminEmail = get_option('admin_email');

	$sql = $wpdb->prepare("select id from ".$wpdb->prefix."anypopup_subscribers  where status=0 and subscriptionType = %s limit 1",$subscriptionType);
	$result = $wpdb->get_row($sql, ARRAY_A);
	$id = (int)$result['id'];
	$getTotalSql = $wpdb->prepare("select count(*) from ".$wpdb->prefix."anypopup_subscribers  where  subscriptionType = %s ", $subscriptionType);
	$totalSubscribers = $wpdb->get_var($getTotalSql);

	/*Id = 0 when all emails status = 1*/
	if($id == 0) {
		/*Clear schedule hook*/
		$headers  = 'MIME-Version: 1.0'."\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8'."\r\n";
		$successTotal = get_option("ANYPOPUP_NEWSLETTER_".$subscriptionType);
		if(!$successTotal) {
			$successTotal = 0;
		}
		$faildTotal = $totalSubscribers - $successTotal;

		$emailMessageCustom = 'Your mail list '.$subscriptionType.' delivered successfully!
						'.$successTotal.' of the '.$totalSubscribers.' emails succeeded, '.$faildTotal.' failed.
						For more details, please download log file inside the plugin.

						This email was generated via Any Popup plugin.';

		$mailStatus = wp_mail($adminEmail, $subscriptionType.' list has been successfully delivered!', $emailMessageCustom, $headers);
		delete_option("ANYPOPUP_NEWSLETTER_".$subscriptionType);
		wp_clear_scheduled_hook("anypopupnewsletter_send_messages", array(json_encode($params)));
		return;
	}
	else {
		$getAllDataSql = $wpdb->prepare("select firstName,lastName,email from ".$wpdb->prefix."anypopup_subscribers where id>=$id and subscriptionType = %s limit $sendingLimit",$subscriptionType);
		$allData = $wpdb->get_results($getAllDataSql, ARRAY_A);
	}

	/*Mail Headers*/
	$blogInfo = get_bloginfo();
	$headers = array(
		'From: "'.$blogInfo.'" <'.$fromEmail.'>' ,
		'MIME-Version: 1.0' ,
		'Content-type: text/html; charset=iso-8859-1'
	);

	foreach ($allData as $data) {

		$patternFirstName = '/\[First name]/';
		$patternLastName = '/\[Last name]/';
		$patternBlogName = '/\[Blog name]/';
		$patternUserName = '/\[User name]/';
		$replacementFirstName = $data['firstName'];
		$replacementLastName = $data['lastName'];
		$replacementBlogName = get_bloginfo("name");
		$replacementUserName = wp_get_current_user()->user_login;
		/*Replace First name and Last name form email message*/
		$emailMessageCustom = preg_replace($patternFirstName, $replacementFirstName, $emailMessage);
		$emailMessageCustom = preg_replace($patternLastName, $replacementLastName, $emailMessageCustom);
		$emailMessageCustom = preg_replace($patternBlogName, $replacementBlogName, $emailMessageCustom);
		$emailMessageCustom = preg_replace($patternUserName, $replacementUserName, $emailMessageCustom);
		$emailMessageCustom = stripslashes($emailMessageCustom);

		$mailStatus = wp_mail($data['email'], $mailSubject, $emailMessageCustom, $headers);
		if(!$mailStatus) {
			$errorLogSql = $wpdb->prepare('INSERT INTO '. $wpdb->prefix .'anypopup_subscription_error_log(`popupType`, `email`, `date`) VALUES (%s, %s, %s)', $subscriptionType, $data['email'], date('Y-m-d H:i'));
			$wpdb->query($errorLogSql);
		}
		/*Sending status*/
		$successCount = get_option("ANYPOPUP_NEWSLETTER_".$subscriptionType);
		if(!$successCount) {
			update_option("ANYPOPUP_NEWSLETTER_".$subscriptionType, 1);
		}
		else {
			update_option("ANYPOPUP_NEWSLETTER_".$subscriptionType, ++$successCount);
		}

	}
	/*Update all mails status which has been sent*/
	$updateStatusQuery = $wpdb->prepare("UPDATE ". $wpdb->prefix ."anypopup_subscribers SET status=1 where id>=$id and subscriptionType = %s limit $sendingLimit",$subscriptionType);
	$wpdb->query($updateStatusQuery);
}
add_action ('anypopupnewsletter_send_messages', 'anypopupnewslatter_repeat_function', 10, 1);

