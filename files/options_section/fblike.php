<div id="special-options">
	<div id="post-body" class="metabox-holder columns-2">
		<div id="postbox-container-2" class="postbox-container">
			<div id="normal-sortables" class="meta-box-sortables ui-sortable">
				<div class="postbox any-popup-special-postbox">
					<div class="handlediv js-special-title" title="Click to toggle"><br></div>
					<h3 class="hndle ui-sortable-handle js-special-title">
						<span>
						<?php
							global $POPUP_TITLES;
							$popupTypeTitle = $POPUP_TITLES[$popupType];
							echo $popupTypeTitle." <span>options</span>";
						?>
						</span>
					</h3>
					<div class="special-options-content">
						<span class="liquid-width">Url:</span>
						<input class="input-width-static" type="text" name="fblike-like-url" value="<?php echo esc_url(@$anypopupFblikeurl); ?>">
						<span class="liquid-width">Layout:</span>
						<?php echo anypopupCreateSelect($anypopupFbLikeButtons,'fblike-layout',esc_html(@$anypopupFbLikeLayout)); ?>
						<span class="liquid-width">Don't show share button:</span>
						<input type="checkbox" name="fblike-dont-show-share-button" <?php echo $anypopupFblikeDontShowShareButton;?>><br>
						<span class="liquid-width">Close popup after like:</span>
						<input type="checkbox" name="fblike-close-popup-after-like" <?php echo $anypopupFblikeClosePopupAfterLike; ?>><br>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>