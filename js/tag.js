jQuery(document).ready(function ($) {
	var settings = WPVARS.settings;
	var isClosed = rs_readCookie('tagclosed') === 'true';
	var height = 330;
	var width = 150;

	var closedStyle = {'top': 20 - height, 'opacity': 0.5};
	var openStyle = {'top': -5, 'opacity': 1.0};

	var adminBar = $('#wpadminbar');
	if (adminBar.length > 0) {
		closedStyle.top += adminBar.height();
		openStyle.top += adminBar.height();
	}

	var container = $('<div>').attr('id', 'tnt-tag-container');
	container.css(isClosed ? closedStyle : openStyle);

	if (settings.checkbox_tag_enableanimation == 1) {
		container.addClass('swingEffect');
	}

	$('body').prepend(container);

	if (settings.checkbox_tag_showonleft) {
		container.css('left', width - parseInt(container.width()) + settings.number_tag_offset + 'px');
	} else {
		container.css('right', settings.number_tag_offset + 'px');
	}
	
	var tag = $('<form>').attr('id', 'tnt-tag').attr('method', 'GET');
	var closeTag = $('<a>').addClass('close-tag').attr('href', '#').appendTo(tag);

	$('<span>').addClass('tag-title').text('Reserveren').css('color', settings.color_tag_text).appendTo(tag);

	$('<span>').text('Aantal personen:').css('color', settings.color_tag_text).appendTo(tag);

	$('<input />').attr('type', 'number').attr({
		'name': 'aantal_personen',
		'min': 1,
		'value': 2
	}).appendTo(tag);

	var submitButton = $('<button>').attr('type', 'submit').text('Boek nu').css({
		'background-color': settings.color_tag_button,
		'color': settings.color_tag_buttontext
	}).appendTo(tag);

	tag.submit(function () {
		popupwindow(this, 'Reserveren');
		return false;
	});

	submitButton.click(function (event) {
		if (isClosed) {
			event.preventDefault();
		}
	}).appendTo(tag);

	// minimizing the floating tag
	closeTag.click(function (event) {
		if (!isClosed) {
			isClosed = true;
			container.animate(closedStyle);
			rs_createCookie('tagclosed', 'true');

			event.stopPropagation();
		}
	});

	// maximizing the floating tag
	tag.click(function () {
		if (isClosed) {
			isClosed = false;
			container.animate(openStyle);
			rs_createCookie('tagclosed', 'false');
		}
	});

	// when the floating tag is closed, show an animation when the user hovers over the floating tag
	tag.hover(function () {
		if (isClosed) {
			container.animate({'opacity': openStyle.opacity}, 'fast');
		}
	}, function () {
		if (isClosed) {
			container.animate({'opacity': closedStyle.opacity}, 'fast');
		}
	});

	hideShowTag();
	$(window).resize(hideShowTag);

	function hideShowTag() {
		if ($(window).width() >= settings.number_tag_minwidth) {
			container.show();
		} else {
			container.hide();
		}
	}

	container.append(tag);
});

function rs_createCookie(name, value, days) {
	var expires = '';
	if (days) {
		var date = new Date();
		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
		expires = '; expires=' + date.toGMTString();
	}
	document.cookie = name + '=' + value + expires + '; path=/';
}

function rs_readCookie(name) {
	var nameEQ = name + '=';
	var ca = document.cookie.split(';');
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') c = c.substring(1, c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
	}
	return null;
}