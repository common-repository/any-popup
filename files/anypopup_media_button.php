<?php
function anypopupMediaButton()
{
	global $pagenow, $typenow;

	$showCurrentUser = ANYPOPUPFunctions::isShowMenuForCurrentUser();
	if(!$showCurrentUser) {return;}
	$buttonTitle = 'Insert popup';
	$output = '';

	$pages = array(
		'post.php',
		'page.php',
		'post-new.php',
		'post-edit.php',
		'widgets.php'
	);


	/* For show in plugins page when package is pro */
	if(ANYPOPUP_PKG !== ANYPOPUP_PKG_FREE) {
		array_push($pages, "admin.php");
	}

	$checkPage = in_array(
		$pagenow,
		$pages
	);

	if ($checkPage && $typenow != 'download') {

		wp_enqueue_script('jquery-ui-dialog');
		wp_register_style('anypopup_jQuery_ui', ANYPOPUP_APP_POPUP_URL . "/style/jQueryDialog/jquery-ui.css");
		wp_enqueue_style('anypopup_jQuery_ui');
		$img = '<span class="dashicons dashicons-welcome-widgets-menus" id="anypopup-popup-media-button" style="padding: 3px 2px 0px 0px"></span>';
		$output = '<a href="javascript:void(0);" onclick="jQuery(\'#anypopuppb-thickbox\').dialog({ width: 450, modal: true, title: \'Insert the shortcode\', dialogClass: \'anypopup-any-popup\' });"  class="button" title="'.$buttonTitle.'" style="padding-left: .4em;">'. $img.$buttonTitle.'</a>';
	}
	echo $output;
}

add_action('media_buttons', 'anypopupMediaButton', 11);

function anypopuppbPopupVariable()
{
	$showCurrentUser = ANYPOPUPFunctions::isShowMenuForCurrentUser();
	if (!$showCurrentUser) {
		return;
	}

	$buttonTitle = 'Insert custom JS variable';
	$output = '';

	require_once(ABSPATH .'wp-admin/includes/screen.php');
	$currentScreen = get_current_screen();
	$currentPageParams = get_object_vars($currentScreen);

	if ($currentPageParams['id'] != 'any-popup_page_edit-popup') {
		return '';
	}
	wp_enqueue_script('jquery-ui-dialog');
	wp_register_style('anypopup_jQuery_ui', ANYPOPUP_APP_POPUP_URL . "/style/jQueryDialog/jquery-ui.css");
	wp_enqueue_style('anypopup_jQuery_ui');

	$img = '<span class="dashicons dashicons-welcome-widgets-menus" id="anypopup-popup-js-variable" style="padding: 3px 2px 0px 0px"></span>';
	$output = '<a href="javascript:void(0);" onclick="jQuery(\'#anypopuppb-js-variable-thickbox\').dialog({ width: 500, modal: true, title: \'Insert JS variable\', dialogClass: \'anypopup-any-popup\' });"  class="button" title="'.$buttonTitle.'" style="padding-left: .4em;">'. $img.$buttonTitle.'</a>';

	echo $output;
	return '';
}

add_action('media_buttons', 'anypopuppbPopupVariable', 11);

function anypopupJsVariableThickbox() {

	require_once(ABSPATH .'wp-admin/includes/screen.php');
	$currentScreen = get_current_screen();
	$currentPageParams = get_object_vars($currentScreen);

	if($currentPageParams['id'] != 'any-popup_page_edit-popup') {
		return '';
	}
	?>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {
			$('#anypopuppb-insert-variable').on('click', function (e) {
				var jsVariableSelector = jQuery('.anypopuppb-js-variable-selector').val();
				var jsVariableAttribute = jQuery('.anypopuppb-js-variable-attribute').val();

				if (jsVariableSelector == '' || jsVariableAttribute == '') {
					alert('Please, fill in all the fields.');
					return;
				}
				window.send_to_editor('[pbvariable selector="' + jsVariableSelector + '" attribute="'+jsVariableAttribute+'"]');
				jQuery('#anypopuppb-js-variable-thickbox').dialog('close')
			});
		});
	</script>
	<div id="anypopuppb-js-variable-thickbox" style="display: none;">
		<div class="wrap">
			<p>Insert JS variable inside the popup.</p>
			<div>
				<div style="margin-bottom: 5px;">
					<span>Selector</span>
					<input type="text" class="anypopuppb-js-variable-selector">
					<span>Ex. #myselector or .myselector</span>
				</div>
				<div>
					<span>Attribute</span>
					<input type="text" class="anypopuppb-js-variable-attribute">
					<span>Ex. value or data-name</span>
				</div>
			</div>
			<p class="submit">
				<input type="button" id="anypopuppb-insert-variable" class="button-primary dashicons-welcome-widgets-menus" value="Insert"/>
				<a id="anypopuppb-cancel" class="button-secondary" onclick="jQuery('#anypopuppb-js-variable-thickbox').dialog( 'close' )" title="Cancel">Cancel</a>
			</p>
		</div>
	</div>
	<?php
}

function anypopupMediaButtonThickboxs()
{
	global $pagenow, $typenow;
	require_once(ABSPATH .'wp-admin/includes/screen.php');
	$currentScreen = get_current_screen();
	$currentPageParams = get_object_vars($currentScreen);

	$showCurrentUser = ANYPOPUPFunctions::isShowMenuForCurrentUser();
	if(!$showCurrentUser) {return;}

	$pages = array(
		'post.php',
		'page.php',
		'post-new.php',
		'post-edit.php',
		'widgets.php'
	);

	if(ANYPOPUP_PKG !== ANYPOPUP_PKG_FREE) {
		array_push($pages, "admin.php");
	}

	$checkPage = in_array(
		$pagenow,
		$pages
	);


	if ($checkPage && $typenow != 'download') :
		$orderBy = 'id DESC';
		$allPopups = ANYPOPUP::findAll($orderBy);
		$popupPreviewId = get_option('popupPreviewId');
		?>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {

				$('#anypopup-ptp-popup-insert').on('click', function () {
					var id = $('#anypopup-insert-popup-id').val();
					if ('' === id) {
						alert('Select your popup');
						return;
					}
					var appearEvent = jQuery("#openEvent").val();

					var selectionText = '';
					if (typeof(tinyMCE.editors.content) != "undefined") {
						selectionText = (tinyMCE.activeEditor.selection.getContent()) ? tinyMCE.activeEditor.selection.getContent() : '';
					}
					/* For plugin editor selected text */
					else if(typeof(tinyMCE.editors[0]) != "undefined") {
						var pluginEditorId = tinyMCE.editors[0]['id'];
						selectionText = (tinyMCE['editors'][pluginEditorId].selection.getContent()) ? tinyMCE['editors'][pluginEditorId].selection.getContent() : '';
					}
					if(appearEvent == 'onload') {
						selectionText = '';
					}
					<?php if( $currentPageParams['id'] == 'any-popup_page_edit-popup'){ ?>
					window.send_to_editor('[any_popup id="' + id + '" insidePopup="on"]'+selectionText+"[/any_popup]");
					<?php }
					else { ?>
						window.send_to_editor('[any_popup id="' + id + '" event="'+appearEvent+'"]'+selectionText+"[/any_popup]");
					<?php } ?>
					jQuery('#anypopuppb-thickbox').dialog( "close" );
				});
			});
		</script>

		<div id="anypopuppb-thickbox" style="display: none;">
			<div class="wrap">
				<p>Insert the shortcode for showing a Popup.</p>
				<div>
					<div class="anypopup-select-popup">
						<span>Select Popup</span>
						<select id="anypopup-insert-popup-id" style="margin-bottom: 5px;">
							<option value="">Please select...</option>
							<?php
								foreach ($allPopups as $popup) :

									if(empty($popup)) {
										continue;
									}
									$popupId = (int)$popup->getId();
									$popupType = $popup->getType();
									$popupTitle = $popup->getTitle();

									if(empty($popupId) || empty($popupType) || $popupId == $popupPreviewId) {
										continue;
									}

									/*Inside popup*/
									if((isset($_GET['id']) && $popupId == (int)@$_GET['id'] || $popupType == 'exitIntent') && $currentPageParams['id'] == 'any-popup_page_edit-popup') {
										continue;
									}
								?>
									<option value="<?php echo $popupId; ?>"><?php echo $popupTitle;?><?php echo " - ".$popupType;?></option>;
								<?php endforeach; ?>
						</select>
					</div>
					<?php /* Becouse in popup content must be have only click */
					   		if($pagenow !== 'admin.php'): ?>
					<div class="anypopup-select-popup">
						<span>Select Event</span>
						<select id="openEvent">
							<option value="onload">On load</option>
							<option value="click">Click</option>
							<option value="hover">Hover</option>
						</select>
					</div>
				<?php endif;?>
				</div>
				<p class="submit">
					<input type="button" id="anypopup-ptp-popup-insert" class="button-primary dashicons-welcome-widgets-menus" value="Insert"/>
					<a id="anypopup_cancel" class="button-secondary" onclick="jQuery('#anypopuppb-thickbox').dialog( 'close' )" title="Cancel">Cancel</a>
				</p>
			</div>
		</div>
	<?php endif;
}

add_action('admin_footer', 'anypopupMediaButtonThickboxs');
add_action('admin_footer', 'anypopupJsVariableThickbox');
