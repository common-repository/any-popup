<p><label><span class="liquid-width">Media Video: <input type="radio" name="video-type" <?php if(!empty($anypopupVideoType) && $anypopupVideoType == 'media'){echo ' Checked '; } ?> value="media"></span></label>
<label><span class="liquid-width">Youtube Video: <input type="radio" name="video-type"  <?php if(!empty($anypopupVideoType) && $anypopupVideoType == 'youtube'){echo ' Checked '; } ?>value="youtube"></span></label>
</p>
<span class="liquid-width">Enter Video URL:</span>
<input class="input-width-static" type="text" name="video" value="<?php echo esc_attr(@$anypopupDataVideo);?>"><br>