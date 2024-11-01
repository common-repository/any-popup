<?php
	if (ANYPOPUP_PKG !== ANYPOPUP_PKG_FREE) {
		require_once(ANYPOPUP_APP_POPUP_FILES ."/anypopup_pro.php");
	}

	$anypopupAllSelectedPages = @implode(',', $anypopupAllSelectedPages);
	$anypopupAllSelectedPosts = @implode(',', $anypopupAllSelectedPosts);
	$anypopupAllSelectedCustomPosts = @implode(',', $anypopupAllSelectedCustomPosts);

?>
<div id="pro-options">
	<div id="post-body" class="metabox-holder columns-2">
		<div id="postbox-container-2" class="postbox-container">
			<div id="normal-sortables" class="meta-box-sortables ui-sortable">
				<div class="postbox js-advanced-postbox">
					<div class="handlediv js-advanced-title" title="Click to toggle"><br></div>
					<h3 class="hndle ui-sortable-handle js-advanced-title">
						<span>Advanced options</span>
					</h3>
					<div class="advanced-options-content">
						<span class="liquid-width">Show on selected pages:</span><input class="input-width-static js-on-all-pages" type="checkbox" name="allPagesStatus" <?php echo @$anypopupAllPagesStatus;?>>
						<span class="dashicons dashicons-info  same-image-style"></span><span class="infoForMobile samefontStyle">Select page where popup should be shown.</span><br>
						<div class="js-all-pages-content acordion-main-div-content">
							<?php echo createRadiobuttons($pagesRadio, ANYPOPUP_POST_TYPE_PAGE, true, esc_html($anypopupAllPages), "liquid-width"); ?>
							<div class="js-pages-selectbox-content acordion-main-div-content">
								<span class="liquid-width anypopup-pages-title">pages</span>
								<select class="js-all-pages js-multiselect" multiple data-slectbox="all-selected-page" data-sorce="<?php echo ANYPOPUP_POST_TYPE_PAGE; ?>" size="10" class="any-popup-multiselect">

								</select>
									<img src="<?php echo plugins_url('img/wpAjax.gif', dirname(__FILE__).'../../../../'); ?>" alt="gif" class="spiner-allPages js-anypopup-spinner anypopup-hide-element js-anypopup-import-gif">
								<input type="hidden" class="js-anypopup-selected-pages" name="all-selected-page" value="<?php echo $anypopupAllSelectedPages;?>">
							</div>
						</div>
						<span class="liquid-width">Show on selected posts:</span><input class="input-width-static js-on-all-posts" type="checkbox" name="allPostsStatus" <?php echo @$anypopupAllPostsStatus;?>>
						<span class="dashicons dashicons-info  same-image-style"></span><span class="infoForMobile samefontStyle">Select post where popup should be shown.</span><br>
						<div class="js-all-posts-content acordion-main-div-content">
							<?php echo createRadiobuttons($postsRadio, ANYPOPUP_POST_TYPE_POST, true, esc_html($anypopupAllPosts), "liquid-width"); ?>
							<div class="js-posts-selectbox-content acordion-main-div-content">
								<span class="liquid-width anypopup-pages-title">posts</span>
								<select class="js-all-posts js-multiselect" multiple data-slectbox="all-selected-posts" data-sorce="<?php echo ANYPOPUP_POST_TYPE_POST; ?>" size="10" class="any-popup-multiselect">

								</select>
								<img src="<?php echo plugins_url('img/wpAjax.gif', dirname(__FILE__).'../../../../'); ?>" alt="gif" class="spiner-allPosts js-anypopup-spinner anypopup-hide-element js-anypopup-import-gif">
								<input type="hidden" class="js-anypopup-selected-posts" name="all-selected-posts" value="<?php echo $anypopupAllSelectedPosts; ?>">
							</div>
							<div class="js-all-categories-content acordion-main-div-content">
								<span class="liquid-width anypopup-pages-title">all categories</span>
								<?php
								$categories = AnypopupGetData::getPostsAllCategories();
								echo ANYPOPUPFunctions::createSelectBox($categories, @$anypopupPostsAllCategories, array("name"=>"posts-all-categories[]","multiple"=>"multiple","size"=>10,"class"=>"any-popup-multiselect")); ?>
							</div>
						</div>
						
						<span class="liquid-width">Show on selected Custom posts:</span><input class="input-width-static js-on-all-custom-posts" type="checkbox" name="allCustomPostsStatus" <?php echo @$anypopupAllCustomPostsStatus;?>>
						<span class="dashicons dashicons-info  same-image-style"></span><span class="infoForMobile samefontStyle">Select post where popup should be shown.</span><br>
						<div class="js-all-custom-posts-content acordion-main-div-content">
							<span class="liquid-width anypopup-pages-title">custom Posts</span>
							<?php
								$allCustomPosts = AnypopupGetData::getAllCustomPosts();
								echo ANYPOPUPFunctions::createSelectBox($allCustomPosts, @$anypopupAllCustomPostsType, array("name"=>"all-custom-posts[]","multiple"=>"multiple","size"=>10,"class"=>"any-popup-multiselect"))."<br>";
								echo createRadiobuttons($customPostsRadio, 'allCustomPosts', true, esc_html(@$anypopupAllCustomPosts), "liquid-width");

							?>
							<div class="js-all-custompost-content acordion-main-div-content">
								<span class="liquid-width anypopup-pages-title"></span><select class="js-all-custom-posts js-multiselect" multiple data-slectbox="all-selected-custom-posts" data-sorce="<?php echo ANYPOPUP_POST_TYPE_POST; ?>" size="10" ></select>
								<img src="<?php echo plugins_url('img/wpAjax.gif', dirname(__FILE__).'../../../../'); ?>" alt="gif" class="spiner-allCustomPosts js-anypopup-spinner anypopup-hide-element js-anypopup-import-gif">
								<input type="hidden" class="js-anypopup-selected-custom-posts" name="all-selected-custom-posts" value="<?php echo $anypopupAllSelectedCustomPosts; ?>">
							</div>
						</div>

						<?php if (!anypopupRemoveOption('onScrolling')): ?>
						<span class="liquid-width">Show while scrolling:</span><input id="js-scrolling-event-inp" class="input-width-static js-checkbox-acordion" type="checkbox" name="onScrolling" <?php echo @$anypopupOnScrolling;?> >
						<span id="scrollingEvent" class="dashicons dashicons-info same-image-style"></span><span class="infoScrollingEvent samefontStyle">Show the popup whenever the user scrolls the page.</span><br>
						<div class="js-scrolling-content acordion-main-div-content">
							<span class="liquid-width">show popup after scrolling</span><input class="before-scroling-percent improveOptionsstyle" type="text" name="beforeScrolingPrsent" value="<?php echo esc_attr(@$beforeScrolingPrsent); ?>">
							<span class="span-percent">%</span>
						</div>
						<?php endif; ?>
						<span class="liquid-width">Show after inactivity</span><input id="js-inactivity-event-inp" class="input-width-static js-checkbox-acordion" type="checkbox" name="inActivityStatus" <?php echo $anypopupInActivityStatus;?> >
						<span id="scrollingEvent" class="dashicons dashicons-info same-image-style"></span><span class="infoScrollingEvent samefontStyle">Show the popup whenever the user scrolls the page.</span><br>
						<div class="js-inactivity-content acordion-main-div-content">
							<span class="liquid-width">show popup after</span><input class="improveOptionsstyle before-scroling-percent" type="number" name="inactivity-timout" value="<?php echo esc_attr(@$anypopupInactivityTimout); ?>">
							<span class="span-percent">sec</span>
						</div>


						<span class="liquid-width">Hide on mobile devices:</span><input class="input-width-static" type="checkbox" name="forMobile" <?php echo @$anypopupForMobile;?>>
						<span class="dashicons dashicons-info  same-image-style"></span><span class="infoForMobile samefontStyle">Don't show the popup for mobile.</span><br>

						<span class="liquid-width">Show only on mobile devices:</span><input class="input-width-static" type="checkbox" name="openMobile" <?php echo @$anypopupOpenOnMobile;?> />
						<span class="dashicons dashicons-info  same-image-style"></span><span class="infoForMobile samefontStyle">If this option is active the popup will appear only on mobile devices.</span><br>

						<span class="liquid-width">Show popup in date range:</span><input class="input-width-static js-checkbox-acordion" type="checkbox" name="popup-timer-status" <?php echo $anypopupTimerStatus;?>>
						<span class="dashicons dashicons-info repeatPopup same-image-style"></span><span class="infoSelectRepeat samefontStyle">Show popup for selected date range. If current date is in selected date range then popup will appear.</span><br>
						<div class="acordion-main-div-content">
							<span class="liquid-width">start date</span><input class="popup-start-timer" type="text" name="popup-start-timer" value="<?php echo esc_attr(@$anypopupStartTimer)?>"><br>
							<span class="liquid-width">end date</span><input class="popup-finish-timer" type="text" name="popup-finish-timer" value="<?php echo esc_attr(@$anypopupFinishTimer)?>"><br>
							<div class="anypopuppb-time-zone-info-wrapper">
								<span>Ensure that your popup time zone is setup correctly <a href="<?php echo ANYPOPUP_APP_POPUP_ADMIN_URL; ?>?page=popup-settings">here</a>.</span>
							</div>
						</div>
						<span class="liquid-width">Schedule:</span><input class="input-width-static js-checkbox-acordion" type="checkbox" name="popup-schedule-status" <?php echo $anypopupScheduleStatus;?>>
						<span class="dashicons dashicons-info repeatPopup same-image-style"></span><span class="infoSelectRepeat samefontStyle">Show popup for selected date range. If current date is in selected date range then popup will appear.</span><br>
						<div class="acordion-main-div-content schedule-main-div-content">
							<div class="liquid-width anypopup-label-div">
							</div><div class="anypopup-options-content-div">
								<h3 class="anypopup-h3">Every</h3>
								<?php  echo ANYPOPUPFunctions::createSelectBox($anypopupWeekDaysArray, @$anypopupScheduleStartWeeks, array('name'=>'schedule-start-weeks[]', 'class' => 'schedule-start-selectbox anypopup-margin0', 'multiple'=> 'multiple', 'size'=>7));?>
								<h3 class="anypopup-h3">From</h3>
								<input type="text" class="anypopup-time-picker anypopup-time-picker-style" name="schedule-start-time" value="<?php echo esc_attr(@$anypopupScheduleStartTime)?>">
								<h3 class="anypopup-h3">To</h3>
								<input type="text" class="anypopup-time-picker anypopup-time-picker-style" name="schedule-end-time" value="<?php echo esc_attr(@$anypopupScheduleEndTime)?>">
							</div>

						</div>

						<span class="liquid-width">Show popup by user status:</span><input class="js-checkbox-acordion js-user-seperator" type="checkbox" name="anypopup-user-status" <?php echo $anypopupUserSeperate; ?>>
						<span class="dashicons dashicons-info repeatPopup same-image-style"></span><span class="infoSelectRepeat samefontStyle">Show Popup if the user is logged in to your site or vice versa.</span><br>
						<div class="acordion-main-div-content js-user-seperator-content">
							<span class="liquid-width">user is</span><?php echo ANYPOPUPFunctions::anypopupCreateRadioElements($usersGroup, @$anypopupLogedUser);?>
						</div>

						<span class="liquid-width">Disable popup closing:</span><input class="input-width-static" type="checkbox" name="disablePopup" <?php echo $anypopupDisablePopup;?>>
						<span class="dashicons dashicons-info same-image-style"></span><span class="infoDisablePopup samefontStyle">Disable popup closing in any possible way.</span><br>

						<span class="liquid-width">Disable popup overlay:</span><input class="input-width-static" type="checkbox" name="disablePopupOverlay" <?php echo $anypopupDisablePopupOverlay;?>>
						<span class="dashicons dashicons-info same-image-style"></span><span class="infoDisablePopup samefontStyle">Disable popup overlay.</span><br>

						<span class="liquid-width">Add to random popup list:</span><input class="input-width-static" type="checkbox" name="randomPopup" <?php echo $anypopupRandomPopup;?>>
						<span class="dashicons dashicons-info same-image-style"></span><span class="infoDisablePopup samefontStyle">If this option is enabled and you have multiple popups on the same page one of the random popups will be opened..</span><br>

						<span class="liquid-width">Auto close popup:</span><input id="js-auto-close" class="input-width-static js-checkbox-acordion" type="checkbox" name="autoClosePopup" <?php echo $anypopupAutoClosePopup;?>>
						<span class="dashicons dashicons-info same-image-style"></span><span class="infoAutoClose samefontStyle">Close popup automatically.</span><br>
						<div class="js-auto-close-content acordion-main-div-content">
							<span class="liquid-width" >after</span><input class="popupTimer improveOptionsstyle" type="text" name="popupClosingTimer" value="<?php echo esc_attr(@$anypopupClosingTimer);?>"><span class="scroll-percent">seconds</span>
						</div>
						<span class="liquid-width">Filter popup for selected countries:</span><input id="js-countris" class="input-width-static js-checkbox-acordion" type="checkbox" name="countryStatus" <?php echo @$anypopupCountryStatus;?>>
						<span class="dashicons dashicons-info same-image-style"></span><span class="infoAutoClose samefontStyle">Select country where popup should be shown/hidden.</span><br>
						<div class="js-countri-content">
							<span class="liquid-width" ></span><?php echo ANYPOPUPFunctions::anypopupCreateRadioElements($countriesRadio, @$anypopupAllowCountries);?>
							<span class="liquid-width" ></span><?php echo ANYPOPUPFunctions::countrisSelect(); ?>
							<input type="button" value="Add" class="addCountry">
							<span class="liquid-width"></span><input type="text" name="countryName" id="countryName" data-role="tagsinput" id="countryName" value="<?php echo esc_attr(@$anypopupCountryName);?>">
							<span class="liquid-width"></span><input type="hidden" name="countryIso"  id="countryIso" value="<?php echo esc_attr(@$anypopupCountryIso);?>">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
