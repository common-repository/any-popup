<?php
function anypopupMeta()
{
	$showCurrentUser = ANYPOPUPFunctions::isShowMenuForCurrentUser();
	if(!$showCurrentUser) {return;}

	$screens = array('post', 'page');
	foreach ( $screens as $screen ) {
		add_meta_box( 'jcap_meta', __('Select popup on page load', 'jc-anypopup'), 'anypopupCallback', $screen, 'normal');
	}
}
add_action('add_meta_boxes', 'anypopupMeta');

function anypopupCallback($post)
{
	wp_nonce_field( basename( __FILE__ ), 'jcap_nonce' );
	$jcap_stored_meta = get_post_meta( $post->ID );
	?>
	<p class="preview-paragaraph">
		<?php
		global $wpdb;
		$proposedTypes = array();
		$orderBy = 'id DESC';

		$proposedTypes = ANYPOPUP::findAll($orderBy);
		function anypopupCreateSelect($options,$name,$selecteOption) {

			$popupPreviewId = get_option('popupPreviewId');
			$str = "";
			$str .= "<select class=\"choose-popup-type\" name=\"$name\">";
			$str .= "<option value='-1'>Not selected</option>";
			foreach($options as $option) {

				$selected ='';

				if ($option) {
					$title = $option->getTitle();
					$type = $option->getType();
					$id = $option->getId();
					if($id == $popupPreviewId) {
						continue;
					}
					if ($selecteOption == $id) {
						$selected = "selected";
					}
					$str .= "<option value='".$id."' disable='".$id."' ".esc_attr($selected)." >".esc_html($title .'-'. $type)."</option>";
				}
			}
			$str .="</select>" ;
			return $str;
		}
		global $post;
		$page = (int)$post->ID;
		$popup = "anypopup_promotional_popup";

		$popupId = 0;
		$postMetaSavedValue = get_post_meta($post->ID, 'anypopup_promotional_popup');
		if(!empty($postMetaSavedValue[0])) $popupId = (int)$postMetaSavedValue[0];

		echo anypopupCreateSelect($proposedTypes,'anypopup_promotional_popup',$popupId);
		$ANYPOPUP_APP_POPUP_URL = ANYPOPUP_APP_POPUP_URL;
		?>
	</p>
	<input type="hidden" value="<?php echo $ANYPOPUP_APP_POPUP_URL;?>" id="ANYPOPUP_APP_POPUP_URL">
	<?php
}

function anypopupSelectPopupSaved($post_id)
{
	$post_id = (int)$post_id;
	if(isset($_POST['anypopup_promotional_popup']) && $_POST['anypopup_promotional_popup'] == -1) {
		delete_post_meta($post_id, 'anypopup_promotional_popup');
		return false;
	}
	else if(isset($_POST['anypopup_promotional_popup'])) {
		update_post_meta($post_id, 'anypopup_promotional_popup' , (int)$_POST['anypopup_promotional_popup']);
	}
}

add_action('save_post','anypopupSelectPopupSaved');