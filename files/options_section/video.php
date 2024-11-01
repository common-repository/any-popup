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
						
						<span class="liquid-width">Auto Play:</span>
						<input type="checkbox" name="video-autoplay" <?php echo $anypopupVideoAutoplay;?>><br>
						<span class="liquid-width">Full Screen:</span>
						<input type="checkbox" name="video-fullscreen" <?php echo $anypopupVideoFullscreen; ?>><br>
						
					</div>
				</div>
			</div>
		</div>
	</div>
</div>