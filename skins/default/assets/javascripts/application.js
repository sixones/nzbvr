var $nzbVR = new nzbVR();

function max(a, b) {
	if (a > b) return a;
	return b;
}

function min(a, b) {
	if (a < b) return a;
	return b;
}

var verbose_output = true;

function trace(text) {
	if (verbose_output && window.console != null) console.log(text);
}

$(document).ready(function() {
	$("a.content").click(function(e) {
		$nzbVR.view.content($nzbVR.utils.parse_hash(this.href));
	});
	
	$nzbVR.view.capture_links();
	
	if ($nzbVR.utils.current_hash() != null) {
		$nzbVR.view.content($nzbVR.utils.current_hash());
	} else {
		$nzbVR.view.content("dashboard");
	}
});

function nzbVR() {
	this.html = new nzbVRHTML();
	this.series = new nzbVRSeries();
	this.search = new nzbVRSearch();
	this.utils = new nzbVRUtils();
	this.view = new nzbVRView();
};

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

var $data = null;

function nzbVRSeries() {
	this._request = null,
	this._index = -1,
	
	this.applyEpisodeList = function() {
		$("section.episode-list dl dd.season span.head").click(function(e) {
			$("dl.episodes", this.parentNode.parentNode).slideToggle(); //.css("display", "block");
		});
	},
	
	this.downloadEpisode = function(watcher, season, episode, parent) {
		$("nav", parent).fadeOut(200);
		$("span.state", parent).text("Checking ...");
		$("span.state", parent).fadeIn(400);
		
		$.post("/series/download/"+watcher+"/"+season+"/"+episode+".json", null, function(data) {
			if (data.result == null) {
				$("span.state", parent).text("Could not find a report to download");
			} else {
				$("span.state", parent).text("Sent report to SABnzbd");
				
				$(parent).addClass("downloaded");
			}
		}, "json");
		
		return false;
	},
	
	this.applySearch = function() {
		this._index = -1;
		
		//$nzbVR.view.allow_submit(false);
	
		$("section fieldset.search.series input#name").keyup(function(e) {
			var c = $(this).val().length;

			if (c >= 2 && (e.which != 38 && e.which != 40 && e.which != 13)) {
				$nzbVR.series.search($(this).val());
			}
			
			if (e.which == 38 || e.which == 40) {
				$nzbVR.series.change_result(e);
			}
			
			if (e.which == 13 && $nzbVR.series._index >= 0) {
				$nzbVR.series.change_result(e);
			}
			
			if (e.which == 13) {
				return false;
			}
		});
		
		$("section fieldset.search.series input#name").focus(function() {
			if ($(this).val() == "Search ...") {
				$(this).val("");
			}
		});
		
		$("section fieldset.search.series input#name").blur(function() {
			if ($(this).val() == "") {
				$(this).val("Search ...");
			}
		});
	},
	
	this.change_result = function(e) {
		var children = $("ul#results").children();
		
		if (e.which == 40 && (this._index < children.length - 1)) {
			this._index++;
		} else if (e.which == 38 && this._index != -1) {
			this._index--;
		}
		
		$("ul#results li").removeClass("selected");
		
		for (var i = 0; i < children.length; i++) {
			if (i == this._index) {
				$(children[i]).addClass("selected");
				
				if (e.which == 13) {
					this.use_result(e, children[i]);
				}
			}
		}
	},
	
	this.use_result = function(e, row) {
		$("ul#results").slideUp(800);
		
		$("input#name").blur();
		
		var id = $("input.tvrage_id", row).val();
		var name = $("span.name", row).val();
		
		$("form input#tvrage_id").val(id);
		
		$("form fieldset.info").removeClass("hide");
		$("form div.buttons").removeClass("hide");
		
		$nzbVR.view._allow_submit = true;
	},

	this.search = function (name) {
		if (this._request != null) {
			this._request.abort();
		}
		
		$("form fieldset.info").addClass("hide");
		$("form div.buttons.info").addClass("hide");
		
		$("fieldset.search input#name").addClass("searching");
		$("fieldset.search input#name").css("background", "#FFFFFF url("+SKIN_URL+"images/ticker.gif) no-repeat 99%");
	
		
		$nzbVR.view._allow_submit = false;
		
		this._request = $.get(BASE_URL+"series/search/"+name+".json", null, function(data) {
			//trace(data);
			
			$data = data;
			
			$("div#content div.container section form fieldset ul#results").empty();
			
			for (var i = 0; i < min(10, data.series.length); i++) {
				var s = data.series[i];
				
				//trace('-> '+ s.name);
				
				var li = document.createElement("li");
				
				li.appendChild($nzbVR.html.create("span", "name", s.name));
				li.appendChild($nzbVR.html.create("span", "first_aired", s.first_aired));
				li.appendChild($nzbVR.html.create_input("hidden", "tvrage_id", s.id));
				
				li.onclick = function(e) {
					$(this).addClass("selected");
					
					$nzbVR.series.use_result(e, this);
				};
				
				$("div#content div.container section form fieldset ul#results").append(li);
			}
			
			$("ul#results").slideDown(800);
			
			$("fieldset.search input#name").removeClass("searching");
			$("fieldset.search input#name").css("background", "#FFFFFF");
		}, "json");
	}
};

function nzbVRHTML() {
	this.create = function(type, klass, content) {
		var el = document.createElement(type);
		el.className = klass;
		
		if (content != null) {
			el.innerHTML = content;
		}
		
		return el;
	},
	
	this.create_input = function(type, klass, val) {
		var el = document.createElement("input");
		el.type = type;
		el.className = klass;
		el.value = val;
		
		return el;
	}
}

function nzbVRView() {
	this.content_url = "",
	this.content_params = null,
	this._allow_submit = false,
	
	this.capture_links = function() {
		$("div#content div.container a.content").click(function(e) {
			$nzbVR.view.content($nzbVR.utils.parse_hash(this.href));
		});
		
		$("div#content div.container a.notify").click(function(e) {
			$nzbVR.view.notify($nzbVR.utils.parse_hash(this.href));
			
			return false;
		});
		
		$("div#content div.container form").submit(function(e) {
			if ($nzbVR.view._allow_submit) {
				var params = new Object();

				for (var i = 0; i < $("input, select", this).length; i++) {
					var el = $("input, select", this)[i];
					
					if (el.id != undefined && el.id != null) {
						//trace(el);
						var id = el.id;
						params[id] = el.value;
					}
				}

				if ($(this).hasClass("slow")) {
					$("div#content div.loader").addClass("slow");
				}
				
				//alert("url -> " + $nzbVR.utils.parse_hash(this.action));
				
				$nzbVR.view.content($nzbVR.utils.parse_hash(this.action), params);
				
				return false;
			} else {
				return false;
			}
		});
	},
	
	this.allow_submit = function(submit) {
		this._allow_submit = submit;
	},
	
	this.set_active_link = function(hash) {
		var links = $("header nav a");
		
		$("header nav a").removeClass("selected");
		
		var hash2 = hash;
		var split = hash.indexOf("/");
		
		if (split != -1) {
			hash2 = hash.substring(split, 0);
		}
		
		for (var i = 0; i < links.length; i++) {
			if (links[i].hash.substring(hash.length+1, 0) == "#"+hash2) {
				$(links[i]).addClass("selected");
			}
		}
	},
	
	this.notify = function(url, params) {
		$.get(url, params, function(data) {
			if (data.state == "OK") {
				alert("Report sent to SABnzbd!");
			} else {
				alert("FAIL: "+data.message);
			}
		}, "json");
	},
	
	this.content = function(url, params) {
		this.hide_content();
		
		this.content_hash = url;
		this.content_url = BASE_URL+url+".html";
		this.content_params = params;
		
		trace("Loading content from '"+url+"'");
		
		$("div#content div.container").queue(function() {
			$.post($nzbVR.view.content_url, $nzbVR.view.content_params, function(data) {
				$("div#content div.container").html(data);
				
				$nzbVR.view.set_active_link($nzbVR.view.content_hash);
				
				$nzbVR.view.capture_links();

				$nzbVR.view.hide_loader();
				$nzbVR.view.show_content();

				$("div#content div.loader").removeClass("slow");
			});
			
			$(this).dequeue();
		});

		this.show_loader();
	},
	
	this.show_loader = function() {
		$("div#content div.loader").slideDown(500);
	},
	
	this.hide_loader = function() {
		$("div#content div.loader").slideUp(600);
	}
	
	this.show_content = function() {
		$("div#content div.container").slideDown(1000);
	},
	
	this.hide_content = function() {
		$("div#content div.container").slideUp(600);
	}
};

var d2 = null;

function nzbVRSearch() {
	this.apply = function() {
		$("form fieldset.search input#query").keyup(function(e) { 
			if (e.which == 13) {
				
				$nzbVR.search.query();
				
				return false;
			}
		});
		
		$("form fieldset.search input#query").focus(function() {
			if ($(this).val() == "Search ...") {
				$(this).val("");
			}
		});
		
		$("form fieldset.search input#query").blur(function() {
			if ($(this).val() == "") {
				$(this).val("Search ...");
			}
		});
	},

	this.query = function() {
		this.hide_content();
	
		var params = Object();
		
		for (var i = 0; i < $("input, select", $("form fieldset.search")).length; i++) {
			var el = $("input, select", $("form fieldset.search"))[i];
			
			if (el.id != undefined && el.id != null) {
				//trace(el);
				var id = el.id;
				params[id] = el.value;
			}
		}
		
		$("div#search_results section#results").queue(function() {
			$.post("/search", params, function(data) {
				$("div#search_results section#results").html(data);
			
				$nzbVR.view.capture_links();
			
				$nzbVR.search.hide_loader();
				$nzbVR.search.show_content();
			});
			
			$(this).dequeue();
		});
	
		this.show_loader();
	},
	
	this.show_loader = function() {
		//$("div#search_results div.small_loader").slideDown(500);
		$("fieldset.search input#query").addClass("searching");
		$("fieldset.search input#query").css("background", "#FFFFFF url("+SKIN_URL+"images/ticker.gif) no-repeat 99%");
	},
	
	this.hide_loader = function() {
		//$("div#search_results div.small_loader").slideUp(600);
		$("fieldset.search input#query").removeClass("searching");
		$("fieldset.search input#query").css("background", "#FFFFFF");
	}
	
	this.show_content = function() {
		$("div#search_results section#results").slideDown(1000);
	},
	
	this.hide_content = function() {
		$("div#search_results section#results").slideUp(600);
	}
};