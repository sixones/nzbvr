function slide_panel(url, page){
	console.log(url);

	//Using an AJAX call, retrieve the new content to be placed in the DIV
	$('#content').load(url, { Page:page }, function(){
		//On successful loading of the content
		//Set the DIV's position to relative (to allow for moving it)
		$(this).css('position', 'relative');
		
		//Move the DIV to the right 400px
		$(this).css('left', 400);
		
		//Now animate the DIV to move back to the Left 400px
		//Back to its original position
		$(this).animate({ left:0 }, 'fast', 'linear', function(){
			//On completion of the animation set the DIV back to static
			$(this).css('position', 'static');
		});
	});
}

$(document).ready(function() {
	$("a.content").click(function(e) {
		var url = $utils.parse_hash($(this)[0].href);
		slide_panel(url, 0);
		
		return false;
	});
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