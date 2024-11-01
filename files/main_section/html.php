<div class="anypopup-wp-editor-container">
<?php
	if(ANYPOPUP_PKG == ANYPOPUP_PKG_FREE) {
		echo ANYPOPUPFunctions::noticeForShortcode();
	}

	$content = @$anypopupDataHtml;
	$editorId = 'anypopup_html';
	$settings = array(
		'wpautop' => false,
		'tinymce' => array(
			'width' => '100%'
		),
		'textarea_rows' => '6',
		'media_buttons' => true
	);
	wp_editor($content, $editorId, $settings);
?>
</div>