<?php

if(!ANYPOPUP_SHOW_POPUP_REVIEW) {
	//echo ANYPOPUPFunctions::addReview();
}

$externalPlugins = AnyPopupIntegrateExternalSettings::getAllExternalPlugins();
$doesntHaveAnyActiveExtensions = AnyPopupIntegrateExternalSettings::doesntHaveAnyActiveExtensions();
?>
    <h2>Add New Popup</h2>
    <div class="popups-wrapper">
	<ul>
	<li>
	<a class="create-popup-link" href="<?php echo ANYPOPUP_APP_POPUP_ADMIN_URL?>admin.php?page=anypopup-edit-popup&type=image">
            <div class="popups-div image-popup">
			</div>
        </a>
		
	</li>
	
	<li>
	<a class="create-popup-link" href="<?php echo ANYPOPUP_APP_POPUP_ADMIN_URL?>admin.php?page=anypopup-edit-popup&type=html">
             <div class="popups-div html-popup">
			 </div>
        </a>
	</li>
	<li>
	<a class="create-popup-link" href="<?php echo ANYPOPUP_APP_POPUP_ADMIN_URL?>admin.php?page=anypopup-edit-popup&type=shortcode">
          <div class="popups-div shortcode-popup">
		  </div>
           
    </a>
	</li>
	
	
	<li>
	<a class="create-popup-link" href="<?php echo ANYPOPUP_APP_POPUP_ADMIN_URL?>admin.php?page=anypopup-edit-popup&type=video">
           <div class="popups-div video-popup">
		   </div>
           
    </a>
	</li>
		
	</ul>
        
       
		<?php if(ANYPOPUP_PKG >= ANYPOPUP_PKG_SILVER): ?>
            <a class="create-popup-link" href="<?php echo ANYPOPUP_APP_POPUP_ADMIN_URL?>admin.php?page=anypopup-edit-popup&type=iframe">
                <div class="popups-div iframe-popup">
                </div>
            </a>
            <a class="create-popup-link" href="<?php echo ANYPOPUP_APP_POPUP_ADMIN_URL?>admin.php?page=anypopup-edit-popup&type=video">
                <div class="popups-div video-popup">
                </div>
            </a>
			<?php if(ANYPOPUP_PKG > ANYPOPUP_PKG_SILVER): ?>
                <a class="create-popup-link" href="<?php echo ANYPOPUP_APP_POPUP_ADMIN_URL?>admin.php?page=anypopup-edit-popup&type=ageRestriction">
                    <div class="popups-div age-restriction">
                    </div>
                </a>
                <a class="create-popup-link" href="<?php echo ANYPOPUP_APP_POPUP_ADMIN_URL?>admin.php?page=anypopup-edit-popup&type=countdown">
                    <div class="popups-div countdown">
                    </div>
                </a>
                <a class="create-popup-link" href="<?php echo ANYPOPUP_APP_POPUP_ADMIN_URL?>admin.php?page=anypopup-edit-popup&type=social">
                    <div class="popups-div anypopup-social">
                    </div>
                </a>
                <a class="create-popup-link" href="<?php echo ANYPOPUP_APP_POPUP_ADMIN_URL?>admin.php?page=anypopup-edit-popup&type=subscription">
                    <div class="popups-div anypopup-subscription">
                    </div>
                </a>
                <a class="create-popup-link" href="<?php echo ANYPOPUP_APP_POPUP_ADMIN_URL?>admin.php?page=anypopup-edit-popup&type=contactForm">
                    <div class="popups-div anypopup-contact-form">
                    </div>
                </a>
			<?php endif; ?>
		<?php endif; ?>
		<?php
		if(!empty($externalPlugins)) {
			foreach ($externalPlugins as  $externalPlugin) { ?>
                <a class="create-popup-link" href="<?php echo ANYPOPUP_APP_POPUP_ADMIN_URL?>admin.php?page=anypopup-edit-popup&type=<?php echo $externalPlugin['name']?>">
                    <div class="popups-div <?php echo $externalPlugin['name'].'-image';?>">
                    </div>
                </a>
			<?php }
		}
		?>
		<?php if (ANYPOPUP_PKG == ANYPOPUP_PKG_FREE): ?>
          

		<?php endif; ?>

    </div>
