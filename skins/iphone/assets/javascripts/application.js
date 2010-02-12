function slide_panel(url, page){
	if (url == "back") {
		url = $back_url;
	}
	
	if (url == "new") {
		url = $new_url;
	}

	$back_button = "";
	$new_button = "";
	
	console.log("url -> "+ url);
	
	$.post(url, function(data) {
		$('section#content div.container').show(0);
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
		
		if ($new_button != "") {
			$new_url = $new_button;
			$("span.new-btn").fadeIn(400);
		} else {
			$new_url = "";
			$("span.new-btn").fadeOut(400);
		}
		
		capture_links($('section#content div.container'));
	}, "html");

}

function update_cache() {
	//if (navigator.onLine != undefined && navigator.onLine) {
		//window.applicationCache.update();
		//window.applicationCache.swapCache();
	//}
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

function apply_season_list() {
	$("section.episode-list dl dd.season h4").click(function(e) {
			$("dl.episodes", this.parentNode).slideToggle(); //.css("display", "block");
	});
}

function download_episode(watcher, season, episode, parent) {
	var result = confirm("Do you want to download this episode?");
	
	if (result) {
		//$("nav", parent).fadeOut(200);
		//$("span.state", parent).text("Checking ...");
		//$("span.state", parent).fadeIn(400);
		
		$.post("/series/download/"+watcher+"/"+season+"/"+episode+".json", null, function(data) {
			if (data.result == null) {
				//$("span.state", parent).text("Could not find a report to download");
				
				alert("Unable to find report for episode");
			} else {
				//$("span.state", parent).text("Sent report to SABnzbd");
				
				$(parent).addClass("downloaded");
				
				alert("Report sent to SABnzbd successfully");
			}
		}, "json");
		
		return false;
	}
}

$(document).ready(function() {
	capture_links();
	
	slide_panel("dashboard", 0);
	
	if (navigator.onLine != undefined) {
		//update_cache();
		
		var cache = window.applicationCache;
		
		cache.addEventListener("updateready", update_cache, false);
		cache.addEventListener("error", function(e) { console.log(e); alert("Failed to update offline cache."); }, false);
	}
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

var $new_button = "";
var $new_url = "";