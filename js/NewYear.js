function ANYPOPUPNewYear() {

}

ANYPOPUPNewYear.prototype.init = function() {

	var dontSowAgain = jQuery('.anypopuppb-new-year-dont-show');

	if (!dontSowAgain.length) {
		return false;
	}

	dontSowAgain.bind('click', function() {
		jQuery('.anypopup-info-panel-wrapper').remove();
		var nonce = jQuery(this).attr('data-ajaxnonce');
		var data = {
			nonce: nonce,
			action: 'anypopuppbNewYear'
		};

		jQuery.post(ajaxurl, data, function() {

		})
	});
};

jQuery(document).ready(function() {
	var newYearObj = new ANYPOPUPNewYear();
	newYearObj.init();
});