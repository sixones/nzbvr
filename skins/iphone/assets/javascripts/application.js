function slide_panel(url, page){
	if (url == "back") {
		url = $back_url;
	}

	$back_button = "";
	
	console.log("url -> "+ url);
	
	$.post(url, function(data) {
		$('section#content div.container').html(data);
		
		$('section#content div.container').css('position', 'relative');
		$('section#content div.container').css('left', 400);
		
		$('section#content div.container').animate({ left:0 }, 'fast', 'linear', function(){
			$(this).css('position', 'static');
		});
		
		if ($back_button != "") {
			$back_url = $back_button;
			$("span.back-btn").fadeIn(400);
		} else {
			$back_url = "";
			$("span.back-btn").fadeOut(400);
		}
		
		capture_links($('section#content div.container'));
	}, "html");

}

function capture_links(doc) {
	if (doc == undefined || doc == null) {
		doc = document;
	}

	$("a.content", doc).click(function(e) {
		var url = $utils.parse_hash($(this)[0].href);
		slide_panel(url, 0);
		
		return false;
	});
}

$(document).ready(function() {
	capture_links();
	
	slide_panel("dashboard", 0);
});

function nzbVRUtils() {
	this.current_hash = function() {
		if (window.location.hash) {
			return this.parse_hash(window.location.hash);
		}
		
		return null;
	}
	
	this.parse_hash = function(url) {
		if (url.indexOf("#") != -1) {
			return url.substr(url.indexOf("#") + 1);
		}
		
		return null;
	}
};

var $utils = new nzbVRUtils();
var $back_button = "";
var $back_url = "";