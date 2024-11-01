<?php
require_once(ANYPOPUP_APP_POPUP_CLASSES.'/anypopupDataTable/ANYPOPUPTable.php');
$allData = ANYPOPUP::findAll();

if(!ANYPOPUP_SHOW_POPUP_REVIEW) {
	//echo ANYPOPUPFunctions::addReview();
}
echo ANYPOPUPFunctions::showReviewPopup();
$ajaxNonce = wp_create_nonce("anypopupAnyPopupImportNonce");
?>
<div class="wrap">
	<div class="headers-wrapper">
	<h2 class="add-new-buttons">Popups <a href="<?php echo admin_url();?>admin.php?page=anypopup-create-popup" class="add-new-h2">Add New</a></h2>
		
		<?php if(ANYPOPUP_PKG != ANYPOPUP_PKG_FREE): ?>
			<div class="export-import-buttons-wrraper">
				<?php if(!empty($allData)):?>
					<a href= "admin-post.php?action=popup_export" ><input type="button" value="Export" class="button"></a>
				<?php endif;?>
				<input id="js-upload-export-file" data-ajaxNonce="<?php echo esc_attr($ajaxNonce); ?>" class="button" type="button" value="Import"><img src="<?php echo plugins_url('img/wpAjax.gif', dirname(__FILE__).'../'); ?>" alt="gif" class="anypopup-hide-element js-anypopup-import-gif">
			</div>
			<div class="clear"></div>
		<?php endif; ?>
	</div>
	<?php
		$table = new ANYPOPUP_PopupsView();
		echo $table;
		//ANYPOPUPFunctions::showInfo();
	?>
</div>
