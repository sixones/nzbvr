var $nzbVR = new nzbVR();

function max(a, b) {
	if (a > b) return a;
	return b;
}

function min(a, b) {
	if (a < b) return a;
	return b;
}

$(document).ready(function() {
	$("a.content").click(function(e) {
		$nzbVR.view.content($nzbVR.utils.parse_hash(this.href));
	});
	
	$nzbVR.view.capture_links();
	
	if ($nzbVR.utils.current_hash() != null) {
		$nzbVR.view.content($nzbVR.utils.current_hash());
	}
	//$nzbVR.view.content("/dashboard.html");
});

function nzbVR() {
	this.html = new nzbVRHTML();
	this.series = new nzbVRSeries();
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
	
	this.applySearch = function() {
		$("section input#name").keyup(function(e) {
			var c = $(this).val().length;

			if (c > 3 && (e.which != 38 && e.which != 40 && e.which != 13)) {
				$nzbVR.series.search($(this).val());
			}
			
			if (e.which == 38 || e.which == 40 || e.which == 13) {
				$nzbVR.series.change_result(e);
			}
			
			if (e.which == 13) {
				return false;
			}
		});
		
		$("section input#name").focus(function() {
			if ($(this).val() == "Search ...") {
				$(this).val("");
			}
		});
		
		$("section input#name").blur(function() {
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
		
		$nzbVR.view._allow_submit = false;
		
		this._request = $.get("/series/search/"+name+".json", null, function(data) {
			console.log(data);
			
			$data = data;
			
			$("section#content div.container section form fieldset ul#results").empty();
			
			for (var i = 0; i < min(10, data.series.length); i++) {
				var s = data.series[i];
				
				console.log('-> '+ s.name);
				
				
				var li = document.createElement("li");
				li.appendChild($nzbVR.html.create("span", "name", s.name));
				li.appendChild($nzbVR.html.create("span", "first_aired", s.first_aired));
				li.appendChild($nzbVR.html.create_input("hidden", "tvrage_id", s.id));
				
				$("section#content div.container section form fieldset ul#results").append(li);
			}
			
			$("ul#results").slideDown(800);
		}, "json");
	}
}

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
		$("section#content div.container a.content").click(function(e) {
			$nzbVR.view.content($nzbVR.utils.parse_hash(this.href));
		});
		
		$("section#content div.container form").submit(function(e) {
			if ($nzbVR.view._allow_submit) {
				var params = new Object();

				for (var i = 0; i < $("input, select", this).length; i++) {
					var el = $("input, select", this)[i];
					
					if (el.id != undefined && el.id != null) {
						console.log(el);
						var id = el.id;
						params[id] = el.value;
					}
				}
				
				console.log(params);
				
				$nzbVR.view.content(this.action, params);
				
				document.location.hash = null;
				
				return false;
			} else {
				return false;
			}
		});
	},
	
	this.allow_submit = function(submit) {
		this._allow_submit = submit;
	}
	
	this.content = function(url, params) {
		console.log("Loading content from '"+url+".html"+"'");

		this.hide_content();
		
		this.content_url = url;
		this.content_params = params;
		
		$("section#content div.container").queue(function() {
			$.post($nzbVR.view.content_url+".html", $nzbVR.view.content_params, function(data) {
				$("section#content div.container").html(data);
				
				$nzbVR.view.capture_links();

				$nzbVR.view.hide_loader();
				$nzbVR.view.show_content();
			});
			
			$(this).dequeue();
		});
		
		this.show_loader();
	},
	
	this.show_loader = function() {
		$("section#content div.loader").slideDown(500);
	},
	
	this.hide_loader = function() {
		$("section#content div.loader").slideUp(600);
	}
	
	this.show_content = function() {
		$("section#content div.container").slideDown(1000);
	},
	
	this.hide_content = function() {
		$("section#content div.container").slideUp(600);
	}
};

