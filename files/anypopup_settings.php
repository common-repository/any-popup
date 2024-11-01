<?php
$defaultVaules = AnypopupGetData::getDefaultValues();
$tableDeleteValue = AnypopupGetData::getValue('tables-delete-status','settings');
$usrsSelectedRoles = AnypopupGetData::getValue('plugin_users_role', 'settings');
$anypopupSelectedTimeZone = AnypopupGetData::getValue('anypopup-popup-time-zone','settings');
$tableDeleteSatatus =  AnypopupGetData::anypopupSetChecked($tableDeleteValue);

if (isset($_GET['saved']) && $_GET['saved']==1) {
	echo '<div id="default-message" class="updated notice notice-success is-dismissible" ><p>Popup updated.</p></div>';
}
?>
<div class="crud-wrapper">
<div class="anypopup-settings-wrapper">
	<div id="special-options">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="postbox-container-2" class="postbox-container">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
					<div class="postbox any-popup-special-postbox">
						<div class="handlediv js-special-title" title="Click to toggle"><br></div>
						<h3 class="hndle ui-sortable-handle js-special-title">
							<span>General Settings</span>
						</h3>
						<div class="special-options-content">
							<form method="POST" action="<?php echo ANYPOPUP_APP_POPUP_ADMIN_URL;?>admin-post.php?action=save_settings" id="anypopup-settings-form">
								<?php
									if(function_exists('wp_nonce_field')) {
										wp_nonce_field('anypopupAnyPopupSettings');
									}
								?>
								<span class="liquid-width">Delete popup data:</span>
								<input type="checkbox" name="tables-delete-status" <?php echo $tableDeleteSatatus;?>>
								<br><span class="liquid-width anypopup-aligin-with-multiselect">User role who can use plugin:</span>
								<?php echo ANYPOPUPFunctions::createSelectBox($defaultVaules['usersRoleList'], @$usrsSelectedRoles, array("name"=>"plugin_users_role[]","multiple"=>"multiple","class"=>"anypopup-selectbox","size"=>count($defaultVaules['usersRoleList']))); ?><br>
							
								<?php if(ANYPOPUP_PKG != ANYPOPUP_PKG_FREE) {
									require_once(ANYPOPUP_APP_POPUP_FILES ."/anypopup_params_arrays.php");
								 ?>
									<span class="liquid-width">Popup time zone:</span><?php echo ANYPOPUPFunctions::createSelectBox($anypopupTimeZones,@$anypopupSelectedTimeZone, array('name'=>'anypopup-popup-time-zone','class'=>'anypopup-selectbox'))?>
								<?php }?>
								<div class="setting-submit-wrraper">
									<input type="submit" class="button-primary" value="<?php echo 'Save Changes'; ?>">
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
