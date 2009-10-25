<?php

class ViewHelper {
	public static function javascript($src) {
		$src = self::assetLink("javascripts/{$src}");
		$src .= "?v=".PicnicUtils::random();
		
		return "<script type=\"text/javascript\" src=\"{$src}\"></script>\n";
	}
	
	public static function stylesheet($src, $media = "all") {
		$src = self::assetLink("stylesheets/{$src}");
		$src .= "?v=".PicnicUtils::random();
		
		return "<link rel=\"stylesheet\" href=\"{$src}\" media=\"$media\" type=\"text/css\" />\n";
	}
	
	public static function image($src, $alt = null, $class = null) {
		$src = self::assetLink("images/{$src}");
		$tag = "<img src=\"{$src}\" ";
		
		if ($alt != null) {
			$tag .= "alt=\"{$alt}\" ";
		}
		
		if ($class != null) {
			$tag .= "class=\"{$class}\" ";
		}
		
		$tag .= "/>";
		
		return "{$tag}\n";
	}
	
	public static function select($id, $array, $selected = null, $head = true) {
		echo "<select id=\"{$id}\" name=\"{$id}\">\n";
		
		if ($head) {
			echo "<option".($selected == null ? " selected=\"selected\"" : "")." value=\"\">".(is_string($head) ? $head : "")."</option>\n";
		}
		
		if (!PicnicUtils::isAssociativeArray($array)) {
			foreach ($array as $val) {
				echo "<option".($val == $selected ? " selected=\"selected\"" : "").">{$val}</option>\n";
			}
		} else {
			foreach ($array as $key => $val) {
				echo "<option".($key == $selected ? " selected=\"selected\"" : "")." value=\"{$key}\">{$val}</option>\n";
			}
		}
		
		echo "</select>\n";
	}
	
	public static function link($url, $removeScript = true) {
		$base = nzbVR::instance()->settings->base_url;
		
		if ($removeScript && stripos($base, "/index.php") !== false) {
			$base = str_replace("/index.php", "", $base);
		}
		
		if ($removeScript && stripos($base, "?req=") !== false) {
			$base = str_replace("?req=", "", $base);
		}
	
		return $base.$url; //nzbVR::instance()->config->get("base_url")
	}
	
	public static function assetLink($url) {
		return self::link("skins/".nzbVR::instance()->skin()."/assets/".$url, true);
	}
}

?>