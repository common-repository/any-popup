function ANYPOPUP() {

	this.positionLeft = '';
	this.positionTop = '';
	this.positionBottom = '';
	this.positionRight = '';
	this.initialPositionTop = '';
	this.initialPositionLeft = '';
	this.isOnLoad = '';
	this.openOnce = '';
	this.numberLimit = '';
	this.popupData = new Array();
	this.popupEscKey = true;
	this.popupOverlayClose = true;
	this.popupContentClick = false;
	this.popupCloseButton = true;
	this.anypopupTrapFocus = true;
	this.popupType = '';
	this.popupClassEvents = ['hover'];
	this.eventExecuteCountByClass = 0;
	this.anypopupEventExecuteCount = 0;
	this.resizeTimer = null;
	this.anypopupColorboxContentTypeReset();
}

ANYPOPUP.prototype.anypopupColorboxContentTypeReset = function () {

	/*colorbox settings mode*/
	this.anypopupColorboxHtml = false;
	this.anypopupColorboxPhoto = false;
	this.anypopupColorboxIframe = false;
	this.anypopupColorboxHref = false;
	this.anypopupColorboxInline = false;
};

/*Popup thems default paddings where key is theme number value padding*/
ANYPOPUP.anypopupColorBoxDeafults = {1 : 70, 2: 34, 3: 30, 4 : 70, 5 : 62, 6: 70};

ANYPOPUP.prototype.popupOpenById = function (popupId) {

	var anypopupOnScrolling = (ANYPOPUP_DATA [popupId]['onScrolling']) ? ANYPOPUP_DATA [popupId]['onScrolling'] : '';
	var anypopupInActivity = (ANYPOPUP_DATA [popupId]['inActivityStatus']) ? ANYPOPUP_DATA [popupId]['inActivityStatus'] : '';
	var autoClosePopup = (ANYPOPUP_DATA [popupId]['autoClosePopup']) ? ANYPOPUP_DATA [popupId]['autoClosePopup'] : '';

	if (anypopupOnScrolling) {
		this.onScrolling(popupId);
	}
	else if (anypopupInActivity) {
		this.showPopupAfterInactivity(popupId);
	}
	else {
		this.showPopup(popupId, true);
	}
};

ANYPOPUP.getCookie = function (cName) {

	var name = cName + "=";
	var ca = document.cookie.split(';');
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	return "";
};

ANYPOPUP.deleteCookie = function (name) {

	document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
};

ANYPOPUP.setCookie = function (cName, cValue, exDays, cPageLevel) {

	var expirationDate = new Date();
	var cookiePageLevel = '';
	var cookieExpirationData = 1;
	if (!exDays || isNaN(exDays)) {
		exDays = 365 * 50;
	}
	if (typeof cPageLevel == 'undefined') {
		cPageLevel = false;
	}
	expirationDate.setDate(expirationDate.getDate() + exDays);
	cookieExpirationData = expirationDate.toString();
	var expires = 'expires='+cookieExpirationData;

	if (exDays == -1) {
		expires = '';
	}

	if (cPageLevel) {
		cookiePageLevel = 'path=/;';
	}

	var value = cValue + ((exDays == null) ? ";" : "; " + expires + ";" + cookiePageLevel);
	document.cookie = cName + "=" + value;
};

ANYPOPUP.prototype.init = function () {

	var that = this;

	this.onCompleate();
	this.popupOpenByCookie();
	this.attacheShortCodeEvent();
	this.attacheClickEvent();
	this.popupOpenByClickUrl();
	this.attacheIframeEvent();
	this.attacheConfirmEvent();
	this.popupClassEventsTrigger();
};

ANYPOPUP.prototype.popupOpenByClickUrl = function () {

	var that = this;
	if(jQuery("a[href*=anypopup-popup-id-]").length) {
		jQuery("a[href*=anypopup-popup-id-]").each(function () {
			jQuery(this).bind('click', function (e) {
				e.preventDefault();
				var href = jQuery(this).attr('href');
				var splitData = href.split("anypopup-popup-id-");

				if(typeof splitData[1] != 'undefined') {
					var popupId = parseInt(splitData[1]);
					that.showPopup(popupId, false);
				}
			})
		});
	}
};

ANYPOPUP.prototype.attacheShortCodeEvent = function () {

	var that = this;

	jQuery(".anypopup-show-popup").each(function () {
		var popupEvent = jQuery(this).attr("data-popup-event");
		if (typeof popupEvent == 'undefined') {
			popupEvent = 'click';
		}
		/* For counting execute and did it one time for popup open */
		that.anypopupEventExecuteCount = 0;
		jQuery(this).bind(popupEvent, function () {
			that.anypopupEventExecuteCount += 1;
			if (that.anypopupEventExecuteCount > 1) {
				return;
			}
			var anypopupID = jQuery(this).attr("data-anypopuppopupid");
			that.showPopup(anypopupID, false);
		});
	});
};

ANYPOPUP.prototype.attacheConfirmEvent = function () {

	var that = this;

	jQuery("[class*='anypopup-confirm-popup-']").each(function () {
		jQuery(this).bind("click", function (e) {
			e.preventDefault();
			var currentLink = jQuery(this);
			var className = jQuery(this).attr("class");

			var anypopupId = that.findPopupIdFromClassNames(className, "anypopup-confirm-popup-");

			jQuery('#anypopupcolorbox').bind("anypopupClose", function () {
				var target = currentLink.attr("target");

				if (typeof target == 'undefined') {
					target = "self";
				}
				var href = currentLink.attr("href");

				if (target == "_blank") {
					window.open(href);
				}
				else {
					window.location.href = href;
				}
			});
			that.showPopup(anypopupId, false);
		})
	});
};

ANYPOPUP.prototype.attacheIframeEvent = function () {

	var that = this;
	/* When user set popup by class name */
	jQuery("[class*='anypopup-iframe-popup-']").each(function () {
		var currentLink = jQuery(this);
		jQuery(this).bind("click", function (e) {
			e.preventDefault();
			var className = jQuery(this).attr("class");

			var anypopupId = that.findPopupIdFromClassNames(className, "anypopup-iframe-popup-");

			/*This update for dynamic open iframe url for same popup*/
			var linkUrl = currentLink.attr("href");

			if (typeof linkUrl == 'undefined') {
				var childLinkTag = currentLink.find('a');
				linkUrl = childLinkTag.attr("href");
			}

			ANYPOPUP_DATA[anypopupId]['iframe'] = linkUrl;

			that.showPopup(anypopupId, false);
		});
	});
};

ANYPOPUP.prototype.attacheClickEvent = function () {

	var that = this;
	/* When user set popup by class name */
	jQuery("[class*='anypopup-popup-id-']").each(function () {
		jQuery(this).bind("click", function (e) {
			e.preventDefault();
			var className = jQuery(this).attr("class");
			var anypopupId = that.findPopupIdFromClassNames(className, "anypopup-popup-id-");

			that.showPopup(anypopupId, false);
		})
	});
};

ANYPOPUP.prototype.popupClassEventsTrigger = function () {

	var popupEvents = this.popupClassEvents;
	var that = this;

	if(popupEvents.length > 0) {

		for (var i in popupEvents) {
			var eventName = popupEvents[i];

			that.attacheCustomEvent(eventName);
		}
	}
};

ANYPOPUP.prototype.attacheCustomEvent = function (eventName) {

	if(typeof eventName == 'undefined' ||  typeof eventName == 'function' || eventName == '') {
		return;
	}
	var that = this;

	jQuery("[class*='anypopup-popup-"+eventName+"-']").each(function () {
		var eventCount = that.eventExecuteCountByClass;
		jQuery(this).bind(eventName, function () {
			eventCount = ++that.eventExecuteCountByClass;
			if (eventCount > 1) {
				return;
			}
			var className = jQuery(this).attr("class");
			var anypopupId = that.findPopupIdFromClassNames(className, 'anypopup-popup-'+eventName+'-');

			that.showPopup(anypopupId, false);
		})
	});
};

ANYPOPUP.prototype.popupOpenByCookie = function () {

	var popupId = ANYPOPUP.getCookie("anypopupSubmitReloadingForm");
	popupId = parseInt(popupId);

	if (typeof popupId == 'number') {
		this.showPopup(popupId, false);
	}
};

ANYPOPUP.prototype.findPopupIdFromClassNames = function (className, classKey) {

	var classSplitArray = className.split(classKey);
	var classIdString = classSplitArray['1'];
	/*Get first all number from string*/
	var popupId = classIdString.match(/^\d+/);

	return popupId;
};

ANYPOPUP.prototype.hexToRgba = function (hex, opacity){

	var c;
	if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
		c = hex.substring(1).split('');

		if(c.length == 3){
			c= [c[0], c[0], c[1], c[1], c[2], c[2]];
		}
		c = '0x'+c.join('');
		return 'rgba('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+','+opacity+')';
	}
	throw new Error('Bad Hex');
};


ANYPOPUP.prototype.anypopupCustomizeThemes = function (popupId) {

	var popupData = ANYPOPUP_DATA[popupId];
	var borderRadiues = popupData['anypopup3ThemeBorderRadiues'];
	var popupContentOpacity = popupData['popup-background-opacity'];
	var popupContentColor = jQuery('#anypopupcboxContent').css('background-color');
	var contentBackgroundColor = popupData['anypopup-content-background-color'];
	var changedColor = popupContentColor.replace(')', ', '+popupContentOpacity+')').replace('rgb', 'rgba');

	if(typeof contentBackgroundColor != 'undefined' && contentBackgroundColor != '') {
		changedColor = this.hexToRgba(contentBackgroundColor, popupContentOpacity);
	}


	if (popupData['theme'] == "colorbox3.css") {
		var borderColor = popupData['anypopupTheme3BorderColor'];
		var borderRadiues = popupData['anypopupTheme3BorderRadius'];
		jQuery("#anypopupcboxLoadedContent").css({'border-color': borderColor});
		jQuery("#anypopupcboxLoadedContent").css({'border-radius': borderRadiues + "%"});
		jQuery("#anypopupcboxContent").css({'border-radius': borderRadiues + "%"})
	}

	jQuery('#anypopupcboxContent').css({'background-color': changedColor});
	jQuery('#anypopupcboxLoadedContent').css({'background-color': changedColor})

};

ANYPOPUP.prototype.onCompleate = function () {

	jQuery("#anypopupcolorbox").bind("anypopupColorboxOnCompleate", function () {

		/* Scroll only inside popup */
		jQuery('#anypopupcboxLoadedContent').isolatedScroll();
	});
	this.isolatedScroll();
};

ANYPOPUP.prototype.isolatedScroll = function () {

	jQuery.fn.isolatedScroll = function () {
		this.bind('mousewheel DOMMouseScroll', function (e) {
			var delta = e.wheelDelta || (e.originalEvent && e.originalEvent.wheelDelta) || -e.detail,
				bottomOverflow = this.scrollTop + jQuery(this).outerHeight() - this.scrollHeight >= 0,
				topOverflow = this.scrollTop <= 0;

			if ((delta < 0 && bottomOverflow) || (delta > 0 && topOverflow)) {
				e.preventDefault();
			}
		});
		return this;
	};
};

ANYPOPUP.prototype.anypopupScalingDimensions = function () {

	var popupWrapper = jQuery("#anypopupcboxWrapper").outerWidth();
	var screenWidth = jQuery(window).width();
	/*popupWrapper != 9999  for resizing case when colorbox is calculated popup dimensions*/
	if (popupWrapper > screenWidth && popupWrapper != 9999) {
		var scaleDegree = screenWidth / popupWrapper;
		jQuery("#anypopupcboxWrapper").css({
			"transform-origin": "0 0",
			'transform': "scale(" + scaleDegree + ", 1)"
		});
		popupWrapper = 0;
	}
	else {
		jQuery("#anypopupcboxWrapper").css({
			"transform-origin": "0 0",
			'transform': "scale(1, 1)"
		})
	}
};

ANYPOPUP.prototype.anypopupScaling = function () {

	var that = this;
	jQuery("#anypopupcolorbox").bind("anypopupColorboxOnCompleate", function () {
		that.anypopupScalingDimensions();
	});
	jQuery(window).resize(function () {
		setTimeout(function () {
			that.anypopupScalingDimensions();
		}, 1000);
	});
};

ANYPOPUP.prototype.varToBool = function (optionName) {

	var returnValue = (optionName) ? true : false;
	return returnValue;
};

ANYPOPUP.prototype.canOpenPopup = function (id, openOnce, isOnLoad) {

	if (!isOnLoad) {
		return true;
	}

	var currentCookies = ANYPOPUP.getCookie('anypopupCookieList');
	if (currentCookies) {
		currentCookies = JSON.parse(currentCookies);

		for (var cookieIndex in currentCookies) {
			var cookieName = currentCookies[cookieIndex];
			var cookieData = ANYPOPUP.getCookie(cookieName + id);

			if (cookieData) {
				return false;
			}
		}
	}

	var popupCookie = ANYPOPUP.getCookie('anypopupDetails' + id);
	var popupType = this.popupType;

	/*for popup this often case */
	if (openOnce && popupCookie != '') {
		return this.canOpenOnce(id);
	}

	return true;
};

ANYPOPUP.prototype.canOpenOnce = function(id) {

	var cookieData = ANYPOPUP.getCookie('anypopupDetails'+id);
	if(!cookieData) {
		return true;
	}
	var cookieData = JSON.parse(cookieData);

	if(cookieData.popupId == id && cookieData.openCounter >= this.numberLimit) {
		return false;
	}
	else {
		return true
	}

};


ANYPOPUP.prototype.setFixedPosition = function (anypopupPositionLeft, anypopupPositionTop, anypopupPositionBottom, anypopupPositionRight, anypopupFixedPositionTop, anypopupFixedPositionLeft) {

	this.positionLeft = anypopupPositionLeft;
	this.positionTop = anypopupPositionTop;
	this.positionBottom = anypopupPositionBottom;
	this.positionRight = anypopupPositionRight;
	this.initialPositionTop = anypopupFixedPositionTop;
	this.initialPositionLeft = anypopupFixedPositionLeft;
};

ANYPOPUP.prototype.percentToPx = function (percentDimention, screenDimension) {

	var dimension = parseInt(percentDimention) * screenDimension / 100;
	return dimension;
};

ANYPOPUP.prototype.getPositionPercent = function (needPercent, screenDimension, dimension) {

	var anypopupPosition = (((this.percentToPx(needPercent, screenDimension) - dimension / 2) / screenDimension) * 100) + "%";
	return anypopupPosition;
};

ANYPOPUP.prototype.showPopup = function (id, isOnLoad) {

	var that = this;

	/*When id does not exist*/
	if (!id) {
		return;
	}

	if (typeof ANYPOPUP_DATA[id] == "undefined") {
		return;
	}
	this.popupData = ANYPOPUP_DATA[id];
	this.popupType = this.popupData['type'];
	this.isOnLoad = isOnLoad;
	this.openOnce = this.varToBool(this.popupData['repeatPopup']);
	this.numberLimit = this.popupData['popup-appear-number-limit'];

	if (typeof that.removeCookie !== 'undefined') {
		that.removeCookie(this.openOnce);
	}

	if (!this.canOpenPopup(this.popupData['id'], this.openOnce, isOnLoad)) {
		return;
	}

	popupColorboxUrl = ANYPOPUP_APP_POPUP_URL + '/style/anypopupcolorbox/anypopupthemes.css';
	head = document.getElementsByTagName('head')[0];
	link = document.createElement('link');
	link.type = "text/css";
	link.id = "anypopup_colorbox_theme-css";
	link.rel = "stylesheet";
	link.href = popupColorboxUrl;
	document.getElementsByTagName('head')[0].appendChild(link);
	var img = document.createElement('img');
	anypopupAddEvent(img, "error", function () {
		that.anypopupShowColorboxWithOptions();
	});
	setTimeout(function () {
		img.src = popupColorboxUrl;
	}, 0);
};

ANYPOPUP.setToPopupsCookiesList = function (cookieName) {

	var currentCookies = ANYPOPUP.getCookie('anypopupCookieList');

	if (!currentCookies) {
		currentCookies = [];
	}
	else {
		currentCookies = JSON.parse(currentCookies);
	}

	if (jQuery.inArray(cookieName, currentCookies) == -1) {
		cookieName = currentCookies.push(cookieName);
	}

	ANYPOPUP.deleteCookie('anypopupCookieList');
	var currentCookies = JSON.stringify(currentCookies);
	ANYPOPUP.setCookie('anypopupCookieList', currentCookies, 365, true);
};

ANYPOPUP.prototype.popupThemeDefaultMeasure = function () {

	var themeName = this.popupData['theme'];
	var defaults = ANYPOPUP.anypopupColorBoxDeafults;
	/*return theme id*/
	var themeId = themeName.replace( /(^.+\D)(\d+)(\D.+$)/i,'$2');

	return defaults[themeId];
};

ANYPOPUP.prototype.changePopupSettings = function () {

	var popupData = this.popupData;
	var popupDimensionMode = popupData['popup-dimension-mode'];
	var maxWidth = popupData['maxWidth'];
	var screenWidth = jQuery(window).width();
	var popupResponsiveDimensionMeasure = popupData['popup-responsive-dimension-measure'];
	var isMaxWidthInPercent = maxWidth.indexOf("%") != -1 ? true: false;

	if(popupDimensionMode == 'responsiveMode') {

		if(popupResponsiveDimensionMeasure == 'auto') {
			this.popupMaxWidth = '100%';

			/*When max with in px*/
			if(maxWidth && !isMaxWidthInPercent && parseInt(maxWidth) < screenWidth) {
				this.popupMaxWidth = parseInt(maxWidth);
			}
			else if(isMaxWidthInPercent && parseInt(maxWidth) < 100) { /*For example when max width is 800% */
				this.popupMaxWidth = maxWidth;
			}

		}
	}
};

ANYPOPUP.prototype.resizePopup = function (settings) {

	var that = this;


	function resizeColorBox () {
		var window = jQuery(this);
		var windowWidth = window.width();
		var windowHeight = window.height();

		var maxWidth = that.popupData['maxWidth'];
		var maxHeight = that.popupData['maxHeight'];

		if(!maxWidth) {
			maxWidth = '100%';
		}

		if(!maxHeight) {
			maxHeight = '100%';
		}

		if (that.resizeTimer) clearTimeout(that.resizeTimer);
		that.resizeTimer = setTimeout(function() {
			if (jQuery('#anypopupcboxOverlay').is(':visible')) {
				var shouldResize = true;
				if (maxWidth.indexOf("%") != -1) {
					maxWidth = that.percentToPx(maxWidth, windowWidth);
				}
				else {
					maxWidth = parseInt(maxWidth);
				}

				if (maxHeight.indexOf("%") != -1) {
					maxHeight = that.percentToPx(maxHeight, windowHeight);
				}
				else {
					maxHeight = parseInt(maxHeight);
				}

				if(maxWidth > windowWidth) {
					maxWidth = windowWidth;
				}

				if(maxHeight > windowHeight) {
					maxHeight = windowHeight;
				}

				settings.maxWidth = maxWidth;
				settings.maxHeight = maxHeight;
				var hasFocusedInput = false;
				jQuery('#anypopupcboxLoadedContent input,#anypopupcboxLoadedContent textarea').each(function() {

					if(jQuery(this).is(':focus')) {
						hasFocusedInput = true;
					}
				});
				/*For mobile when has some focused input popup does not resize*/
				if( /Android|webOS|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) && hasFocusedInput ) {
					shouldResize = false;
				}
				if(shouldResize) {
					jQuery.anypopupcolorbox(settings);
					jQuery('#anypopupcboxLoadingGraphic').css({'display': 'none'});
				}
			}
		}, 500);
	}

	jQuery(window).resize(resizeColorBox);
	window.addEventListener("orientationchange", resizeColorBox, false);
};

ANYPOPUP.prototype.resizeAfterContentResizing = function () {

	var visibilityClasses = [".js-validate-required", ".js-anypopuppb-visibility"];
	var maxHeight = this.popupData['maxHeight'];
	var diffContentHight = jQuery("#anypopupcboxWrapper").height() - jQuery("#anypopupcboxLoadedContent").height();
	for(var index in visibilityClasses) {
		jQuery(visibilityClasses[index]).visibilityChanged({
			callback: function(element, visible) {
				if(maxHeight !== '' && parseInt(maxHeight) < (jQuery("#anypopupcboxLoadedContent").prop('scrollHeight') + diffContentHight)) {
					return false;
				}
				jQuery.anypopupcolorbox.resize();
			},
			runOnLoad: false,
			frequency: 2000
		});
	}

	new ResizeSensor(jQuery('#anypopupcboxLoadedContent'), function(){
		if(maxHeight !== '' && parseInt(maxHeight) < (jQuery("#anypopupcboxLoadedContent").prop('scrollHeight') + diffContentHight)) {
			return false;
		}
		jQuery.anypopupcolorbox.resize();
	});

};

ANYPOPUP.prototype.anypopupColorboxContentMode = function() {

	var that = this;

	this.anypopupColorboxContentTypeReset();
	var popupType = this.popupData['type'];
	var popupHtml = (this.popupData['html'] == '') ? '&nbsp;' : this.popupData['html'];
	var popupImage = this.popupData['image'];
	var popupIframeUrl = this.popupData['iframe'];
	var popupVideo = this.popupData['video'];
	var popupId = this.popupData['id'];

	popupImage = (popupImage) ? popupImage : false;
	popupVideo = (popupVideo) ? popupVideo : false;
	popupIframeUrl = (popupIframeUrl) ? popupIframeUrl : false;

	if(popupType == 'image') {
		this.anypopupColorboxPhoto = true;
		this.anypopupColorboxHref = popupImage;
	}

	if(popupIframeUrl) {
		this.anypopupColorboxIframe = true;
		this.anypopupColorboxHref = popupIframeUrl;
	}

	if(popupVideo) {
		this.anypopupColorboxIframe = true;
		this.anypopupColorboxHref = popupVideo;
	}

	/*this condition jQuery('#anypopup-popup-content-wrapper-'+popupId).length != 0 for backward compatibility*/
	if(popupHtml && jQuery('#anypopup-popup-content-wrapper-'+popupId).length != 0) {
		this.anypopupColorboxInline = true;
		this.anypopupColorboxHref = '#anypopup-popup-content-wrapper-'+popupId;
	}
	else {
		this.anypopupColorboxHtml = popupHtml;
	}
};

ANYPOPUP.prototype.addToCounter = function (popupId) {

	var params = {};
	params.popupId = popupId;

	var data = {
		action: 'send_to_open_counter',
		ajaxNonce: ANYPOPUPParams.ajaxNonce,
		params: params
	};

	jQuery.post(ANYPOPUPParams.ajaxUrl, data, function(response,d) {

	});
};
ANYPOPUP.soundValue = 1;

ANYPOPUP.prototype.contentClickRedirect = function () {

	var popupData = this.popupData;
	var contentClickBehavior = popupData['content-click-behavior'];
	var clickRedirectToUrl = popupData['click-redirect-to-url'];
	var redirectToNewTab = popupData['redirect-to-new-tab'];

	/* If has url for redirect */
	if ((contentClickBehavior !== 'close' || clickRedirectToUrl !== '') && typeof contentClickBehavior !== 'undefined') {
		jQuery('#anypopupcolorbox').css({
			"cursor": 'pointer'
		});
	}

	jQuery(".anypopup-current-popup-" + popupData['id']).bind('click', function () {
		if (contentClickBehavior == 'close' || clickRedirectToUrl == '' || typeof contentClickBehavior == 'undefined') {
			jQuery.anypopupcolorbox.close();
		}
		else {
			if (!redirectToNewTab) {
				window.location = clickRedirectToUrl;
			}
			else {
				window.open(clickRedirectToUrl);
			}
		}

	});
};

ANYPOPUP.prototype.closeButtonDelay = function (buttonDelayValue) {
	setTimeout(function(){
		jQuery('#anypopupcboxClose').attr('style', 'display: block !important;');
	},
	buttonDelayValue * 1000 /* received values covert to seconds */
	);
}

ANYPOPUP.prototype.htmlIframeFilterForOpen = function (popupEventName) {

	var popupId = this.popupData['id'];
	var popupContentWrapper = jQuery('#anypopup-popup-content-wrapper-'+popupId);

	if(!popupContentWrapper.length || typeof this.popupData['htmlIframeUrl'] == 'undefined') {
		return;
	}

	if(!popupContentWrapper.find('iframe').length) {
		return;
	}

	if(popupEventName == 'open') {
		var iframeUrl = this.popupData['htmlIframeUrl'];
		popupContentWrapper.find('iframe').attr('src', iframeUrl);
		return;
	}

	popupContentWrapper.find('iframe').attr('src', ' ');
	return;
};

ANYPOPUP.prototype.getSearchDataFromContent = function(content)
{
	var pattern = /\[(\[?)(pbvariable)(?![\w-])([^\]\/]*(?:\/(?!\])[^\]\/]*)*?)(?:(\/)\]|\](?:([^\[]\*+(?:\[(?!\/\2\])[^\[]\*+)\*+)\[\/\2\])?)(\]?)/gi;
	var match;
	var collectedData = [];

	while (match = pattern.exec(content)) {
		var currentSearchData = [];
		currentSearchData['replaceString'] = match[0];
		var parseAttributes = /\s(\w+?)="(.+?)"/g;
		var attributes;
		var attributesKeyValue = [];
		while (attributes = parseAttributes.exec(match[3])) {
			attributesKeyValue[attributes[1]] = attributes[2];
		}
		currentSearchData['searchData'] = attributesKeyValue;
		collectedData.push(currentSearchData);
	}

	return collectedData;
};

ANYPOPUP.prototype.replaceWithCustomShortcode = function(popupId)
{
	var currentHtmlContent = jQuery('#anypopup-popup-content-wrapper-'+popupId).html();
	var searchData = this.getSearchDataFromContent(currentHtmlContent);
	var that = this;

	if (!searchData.length) {
		return false;
	}

	for (var index in searchData) {
		var currentSearchData = searchData[index];
		var searchAttributes = currentSearchData['searchData'];

		if (typeof searchAttributes['selector'] == 'undefined' || typeof searchAttributes['attribute'] == 'undefined') {
			that.replaceShortCode(currentSearchData['replaceString'], '');
			continue;
		}

		try {
			if (!jQuery(searchAttributes['selector']).length) {
				that.replaceShortCode(currentSearchData['replaceString'], '');
				continue;
			}
		}
		catch (e) {
			that.replaceShortCode(currentSearchData['replaceString'], '');
			continue;
		}

		var replaceName = jQuery(searchAttributes['selector']).attr(searchAttributes['attribute']);

		if (typeof replaceName == 'undefined') {
			that.replaceShortCode(currentSearchData['replaceString'], '');
			continue;
		}

		that.replaceShortCode(currentSearchData['replaceString'], replaceName);
	}
};

ANYPOPUP.prototype.replaceShortCode = function(shortCode, replaceText)
{
	var popupId = parseInt(this.popupData['id']);
	var popupContentWrapper = jQuery('#anypopup-popup-content-wrapper-'+popupId);

	if (!popupContentWrapper.length) {
		return false;
	}
	
	var currentHtmlContent = popupContentWrapper.contents();

	if (!currentHtmlContent.length) {
		return false;
	}

	for (var index in currentHtmlContent) {
		var currentChild = currentHtmlContent[index];
		var currentChildNodeValue = currentChild.nodeValue;
		var currentChildNodeType = currentChild.nodeType;

		if (currentChildNodeType != Node.TEXT_NODE) {
			continue;
		}

		if (currentChildNodeValue.indexOf(shortCode) != -1) {
			currentChild.nodeValue =  currentChildNodeValue.replace(shortCode, replaceText);
		}
	}

	return true;
};

ANYPOPUP.prototype.colorboxEventsListener = function ()
{
	var that = this;
	var disablePageScrolling = this.varToBool(this.popupData['disable-page-scrolling']);
	var popupOpenSound = this.varToBool(this.popupData['popupOpenSound']);
	var popupContentBgImage = this.varToBool(this.popupData['popupContentBgImage']);
	var popupOpenSoundFile = this.popupData['popupOpenSoundFile'];
	var popupContentBgImageUrl = this.popupData['popupContentBgImageUrl'];
	var popupContentBackgroundSize = this.popupData['popupContentBackgroundSize'];
	var popupContentBackgroundRepeat = this.popupData['popupContentBackgroundRepeat'];
	var repetitivePopup = this.popupData['repetitivePopup'];
	var repetitivePopupPeriod = this.popupData['repetitivePopupPeriod'];
	var buttonDelayValue = this.popupData['buttonDelayValue'];
	/*this.popupCloseButton*/
	repetitivePopupPeriod = parseInt(repetitivePopupPeriod)*1000;
	var repetitiveTimeout = null;

	if(popupOpenSound && popupOpenSoundFile && typeof that.audio == 'undefined') {
		that.audio = new Audio(popupOpenSoundFile);
	}
	jQuery('#anypopupcolorbox').one("anypopupColorboxOnOpen", function (e, args) {
		var popupId = args.popupId;
		that.replaceWithCustomShortcode(popupId);
		if(disablePageScrolling) {
			jQuery('html').addClass('anypopuppb-disable-page-scrolling');
		}
	});

	jQuery('#anypopupcolorbox').one("anypopupColorboxOnCompleate", function (e, args) {

		var popupId = args.popupId;
		that.addToCounter(popupId);

		if(that.popupData['type'] == 'html') {
			that.htmlIframeFilterForOpen('open');
		}

		/* if close button is set to be shown and has delay value */
		if (that.popupCloseButton && buttonDelayValue) {
			that.closeButtonDelay(buttonDelayValue);
		}

		if(that.popupContentClick) {
			that.contentClickRedirect();
		}

		clearInterval(repetitiveTimeout);
		if(that.varToBool(that.popupData['popupOpenSound']) && popupOpenSoundFile) {
			/*
			 * ANYPOPUP.soundValue -> 1 sound should play
			 * ANYPOPUP.soundValue -> 2 sound should pause
			 * */
			if (ANYPOPUP.soundValue == 1) {

				that.audio.play();
				ANYPOPUP.soundValue = 2;

			} else if (ANYPOPUP.soundValue == 2) {
				that.audio.pause();
				ANYPOPUP.soundValue = 1;
			}
		}
		if(popupContentBgImage) {
			jQuery('#anypopupcboxLoadedContent').css({
				'background-image': 'url('+popupContentBgImageUrl+')',
				'background-size': popupContentBackgroundSize,
				'background-repeat': popupContentBackgroundRepeat
			});
		}
	});

	jQuery('#anypopupcolorbox').one("anypopupCleanup", function () {
		if(repetitivePopup) {
			repetitiveTimeout = setTimeout(function() {
				var anypopupPoupFrontendObj = new ANYPOPUP();
				anypopupPoupFrontendObj.popupOpenById(that.popupData['id']);
			}, repetitivePopupPeriod);
		}
		if(that.varToBool(that.popupData['popupOpenSound']) && popupOpenSoundFile) {
			if(typeof that.audio != 'undefined') {
				that.audio.pause();
				delete that.audio;
			}

			ANYPOPUP.soundValue = 1;
		}
	});

	jQuery('#anypopupcolorbox').one("anypopupClose", function () {
		if(disablePageScrolling) {
			jQuery('html').removeClass('anypopuppb-disable-page-scrolling');
		}
		if(that.popupData['type'] == 'html') {
			that.htmlIframeFilterForOpen('close');
		}
	});
};

ANYPOPUP.prototype.anypopupShowColorboxWithOptions = function () {

	var that = this;
	setTimeout(function () {

		that.colorboxEventsListener();
		var anypopupFixed = that.varToBool(that.popupData['popupFixed']);
		var popupId = that.popupData['id'];
		that.popupOverlayClose = that.varToBool(that.popupData['overlayClose']);
		that.popupContentClick = that.varToBool(that.popupData['contentClick']);
		var popupReposition = that.varToBool(that.popupData['reposition']);
		var popupScrolling = that.varToBool(that.popupData['scrolling']);
		var popupScaling = that.varToBool(that.popupData['scaling']);
		that.popupEscKey = that.varToBool(that.popupData['escKey']);
		that.popupCloseButton = that.varToBool(that.popupData['closeButton']);
		var countryStatus = that.varToBool(that.popupData['countryStatus']);
		var popupForMobile = that.varToBool(that.popupData['forMobile']);
		var onlyMobile = that.varToBool(that.popupData['openMobile']);
		var popupCantClose = that.varToBool(that.popupData['disablePopup']);
		var disablePopupOverlay = that.varToBool(that.popupData['disablePopupOverlay']);
		var popupAutoClosePopup = that.varToBool(that.popupData['autoClosePopup']);
		var saveCookiePageLevel = that.varToBool(that.popupData['save-cookie-page-level']);
		var popupClosingTimer = that.popupData['popupClosingTimer'];

		if (popupScaling) {
			that.anypopupScaling();
		}
		if (popupCantClose) {
			that.cantPopupClose();
		}
		that.popupMaxWidth = (!that.popupData['maxWidth']) ? '100%' : that.popupData['maxWidth'];
		var popupPosition = anypopupFixed ? that.popupData['fixedPostion'] : '';
		var popupVideo = that.popupData['video'];
		var popupOverlayColor = that.popupData['anypopupOverlayColor'];
		var contentBackgroundColor = that.popupData['anypopup-content-background-color'];
		var popupDimensionMode = that.popupData['popup-dimension-mode'];
		var popupResponsiveDimensionMeasure = that.popupData['popup-responsive-dimension-measure'];
		var popupWidth = that.popupData['width'];
		var popupHeight = that.popupData['height'];
		var popupOpacity = that.popupData['opacity'];
		var popupMaxHeight = (!that.popupData['maxHeight']) ? '100%' : that.popupData['maxHeight'];
		var popupInitialWidth = that.popupData['initialWidth'];
		var popupInitialHeight = that.popupData['initialHeight'];
		var popupEffectDuration = that.popupData['duration'];
		var popupEffect = that.popupData['effect'];
		var pushToBottom = that.popupData['pushToBottom'];
		var onceExpiresTime = parseInt(that.popupData['onceExpiresTime']);
		var anypopupType = that.popupData['type'];
		var overlayCustomClass = that.popupData['anypopupOverlayCustomClasss'];
		var contentCustomClass = that.popupData['anypopupContentCustomClasss'];
		var popupTheme = that.popupData['theme'];
		var themeStringLength = popupTheme.length;
		var customClassName = popupTheme.substring(0, themeStringLength - 4);
		var closeButtonText = that.popupData['theme-close-text'];

		that.anypopupColorboxContentMode();

		if(popupDimensionMode == 'responsiveMode') {

			popupWidth = '';
			if(popupResponsiveDimensionMeasure != 'auto') {
				popupWidth = parseInt(popupResponsiveDimensionMeasure)+'%';
			}

			if(that.popupData['type'] != 'iframe' && that.popupData['type'] != 'video') {
				popupHeight = '';
			}
		}

		var anypopupScreenWidth = jQuery(window).width();
		var anypopupScreenHeight = jQuery(window).height();

		var anypopupIsWidthInPercent = popupWidth.indexOf("%");
		var anypopupIsHeightInPercent = popupHeight.indexOf("%");
		var anypopupHeightPx = popupHeight;
		var anypopupWidthPx = popupWidth;
		if (anypopupIsWidthInPercent != -1) {
			anypopupWidthPx = that.percentToPx(popupWidth, anypopupScreenWidth);
		}
		if (anypopupIsHeightInPercent != -1) {
			anypopupHeightPx = that.percentToPx(popupHeight, anypopupScreenHeight);
		}
		/*for when width or height in px*/
		anypopupWidthPx = parseInt(anypopupWidthPx);
		anypopupHeightPx = parseInt(anypopupHeightPx);

		var staticPositionWidth = anypopupWidthPx;
		if(staticPositionWidth > anypopupScreenWidth) {
			staticPositionWidth = anypopupScreenWidth;
		}

		var popupPositionTop = that.getPositionPercent("50%", anypopupScreenHeight, anypopupHeightPx);
		var popupPositionLeft = that.getPositionPercent("50%", anypopupScreenWidth, staticPositionWidth);
		var posTopForSettings = popupPositionTop;

		if (popupPosition == 1) {
			that.setFixedPosition('0%', '3%', false, false, 0, 0);
		} else if (popupPosition == 2) {
			that.setFixedPosition(popupPositionLeft, '3%', false, false, 0, 50);
		} else if (popupPosition == 3) {
			that.setFixedPosition(false, '3%', false, '0%', 0, 90);
		} else if (popupPosition == 4) {

			if(isNaN(popupPositionTop)) {
				posTopForSettings = false;
			}
			that.setFixedPosition('0%', posTopForSettings, false, false, posTopForSettings, 0);
		} else if (popupPosition == 5) {
			anypopupFixed = true;
			that.setFixedPosition(false, false, false, false, 50, 50);
		} else if (popupPosition == 6) {
			if(isNaN(popupPositionTop)) {
				posTopForSettings = false;
			}
			that.setFixedPosition('0%', posTopForSettings, false, '0%', 50, 90);
		} else if (popupPosition == 7) {
			that.setFixedPosition('0%', false, '0%', false, 90, 0);
		} else if (popupPosition == 8) {
			that.setFixedPosition(popupPositionLeft, false, '0%', false, 90, 50);
		} else if (popupPosition == 9) {
			that.setFixedPosition(false, false, '0%', '0%', 90, 90);
		}
		if (!anypopupFixed) {
			that.setFixedPosition(false, false, false, false, 50, 50);
		}

		var userDevice = false;
		if (popupForMobile) {
			userDevice = that.forMobile();
		}

		if (popupAutoClosePopup) {
			setTimeout(that.autoClosePopup, popupClosingTimer * 1000);
		}

		if (disablePopupOverlay) {
			that.anypopupTrapFocus = false;
			that.disablePopupOverlay();
		}

		if (onlyMobile) {
			var openOnlyMobile = false;
			openOnlyMobile = that.forMobile();
			if (openOnlyMobile == false) {
				return;
			}
		}

		if (userDevice) {
			return;
		}
		that.changePopupSettings();
		ANYPOPUP_SETTINGS = {
			popupId: popupId,
			html: that.anypopupColorboxHtml,
			photo: that.anypopupColorboxPhoto,
			iframe: that.anypopupColorboxIframe,
			href: that.anypopupColorboxHref,
			inline: that.anypopupColorboxInline,
			width: popupWidth,
			height: popupHeight,
			className: customClassName,
			close: closeButtonText,
			overlayCutsomClassName: overlayCustomClass,
			contentCustomClassName: contentCustomClass,
			onOpen: function () {
				if(that.anypopupColorboxInline) {
					var contentImage = jQuery(that.anypopupColorboxHref).find('img').first();
					if(contentImage.length) {
						var height = contentImage.attr('height');
						height = parseInt(height);
						contentImage.attr('style', 'height: '+height+' !important');
					}
				}
				jQuery('#anypopupcolorbox').removeAttr('style');
				jQuery('#anypopupcolorbox').removeAttr('left');
				jQuery('#anypopupcolorbox').css('top', '' + that.initialPositionTop + '%');
				jQuery('#anypopupcolorbox').css('left', '' + that.initialPositionLeft + '%');
				jQuery('#anypopupcolorbox').css('animation-duration', popupEffectDuration + "s");
				jQuery('#anypopupcolorbox').css('-webkit-animation-duration', popupEffectDuration + "s");
				jQuery("#anypopupcolorbox").addClass('anypopup-animated ' + popupEffect + '');
				jQuery("#anypopupcboxOverlay").addClass("anypopupcboxOverlayBg");
				jQuery("#anypopupcboxOverlay").removeAttr('style');

				if (popupOverlayColor) {
					jQuery("#anypopupcboxOverlay").css({'background': 'none', 'background-color': popupOverlayColor});
				}
				var openArgs = {
					popupId: popupId
				};

				jQuery('#anypopupcolorbox').trigger("anypopupColorboxOnOpen", openArgs);

			},
			onLoad: function () {
			},
			onComplete: function () {
				if (contentBackgroundColor) {
					jQuery("#anypopupcboxLoadedContent").css({'background-color': contentBackgroundColor});
				}
				jQuery("#anypopupcboxLoadedContent").addClass("anypopup-current-popup-" + that.popupData['id']);
				var completeArgs = {
					pushToBottom: pushToBottom,
					popupId: popupId
				};

				jQuery('#anypopupcolorbox').trigger("anypopupColorboxOnCompleate", completeArgs);

				var anypopuppopupInit = new AnypopupInit(that.popupData);
				anypopuppopupInit.overallInit();
				/* For specific popup Like Countdown AgeRestcion popups */
				anypopuppopupInit.initByPopupType();
				that.anypopupCustomizeThemes(that.popupData['id']);
				if(popupDimensionMode == 'responsiveMode') {
					/* it's temporary deactivated  for colorbox resize good work that.resizeAfterContentResizing(); */
					that.resizePopup(ANYPOPUP_SETTINGS);
					jQuery('#anypopupcboxLoadingGraphic').remove()
				}
			},
			onCleanup: function () {
				jQuery('#anypopupcolorbox').trigger("anypopupCleanup", []);
			},
			onClosed: function () {
				jQuery("#anypopupcboxLoadedContent").removeClass("anypopup-current-popup-" + that.popupData['id']);
				jQuery('#anypopupcolorbox').trigger("anypopupClose", []);
			},
			trapFocus: that.anypopupTrapFocus,
			opacity: popupOpacity,
			escKey: that.popupEscKey,
			closeButton: that.popupCloseButton,
			fixed: anypopupFixed,
			top: that.positionTop,
			bottom: that.positionBottom,
			left: that.positionLeft,
			right: that.positionRight,
			scrolling: popupScrolling,
			reposition: popupReposition,
			overlayClose: that.popupOverlayClose,
			maxWidth: that.popupMaxWidth,
			maxHeight: popupMaxHeight,
			initialWidth: popupInitialWidth,
			initialHeight: popupInitialHeight
		};

		if(popupDimensionMode == 'responsiveMode') {
			/*colorbox open speed*/
			ANYPOPUP_SETTINGS.speed = 10;
		}
		jQuery.anypopupcolorbox(ANYPOPUP_SETTINGS);


		if (countryStatus == true && typeof AnypopupUserData != "undefined") {
			jQuery.cookie("ANYPOPUP_USER_COUNTRY_NAME", AnypopupUserData.countryIsoName, {expires: 365});
		}
		/* Cookie can't be set here as it's set in Age Restriction popup when the user clicks "yes" */
		if (that.popupData['id'] && that.isOnLoad == true && that.openOnce != '' && that.popupData['type'] != "ageRestriction") {
			var anypopupCookieData = '';

			var currentCookie = ANYPOPUP.getCookie('anypopupDetails' + that.popupData['id']);

			if (!currentCookie) {
				var openCounter = 1;
			}
			else {
				var currentCookie = JSON.parse(currentCookie);
				var openCounter = currentCookie.openCounter += 1;
			}
			anypopupCookieData = {
				'popupId': that.popupData['id'],
				'openCounter': openCounter,
				'openLimit': that.numberLimit
			};

			/*!saveCookiePageLevel it's mean for all site level*/
			ANYPOPUP.setCookie("anypopupDetails"+that.popupData['id'],JSON.stringify(anypopupCookieData), onceExpiresTime, !saveCookiePageLevel);
		}


		jQuery('#anypopupcolorbox').bind('anypopupClose', function (e) {
			/* reset event execute count for popup open */
			that.anypopupEventExecuteCount = 0;
			that.eventExecuteCountByClass = 0;
			jQuery('#anypopupcolorbox').removeClass(customClassName);
			/* Remove custom class for another popup */
			jQuery('#anypopupcboxOverlay').removeClass(customClassName);
			jQuery('#anypopupcolorbox').removeClass(popupEffect);
			/* Remove animated effect for another popup */
		});

	}, this.popupData['delay'] * 1000);
};

jQuery(document).ready(function ($) {

	var popupObj = new ANYPOPUP();
	popupObj.init();
});
