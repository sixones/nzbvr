<?php

class ViewHelper {
	public static function javascript($src) {
		$src = self::assetLink("javascripts/{$src}");
		
		return "<script type=\"text/javascript\" src=\"{$src}\"></script>\n";
	}
	
	public static function stylesheet($src, $media = "all") {
		$src = self::assetLink("stylesheets/{$src}");
		
		return "<link rel=\"stylesheet\" href=\"{$src}\" media=\"$media\" type=\"text/css\" />\n";
	}
	
	public static function image($src, $alt = null) {
		$src = self::assetLink("images/{$src}");
		$tag = "<img src=\"{$src}\" ";
		
		if ($alt != null) {
			$tag .= "alt=\"{$alt}\" ";
		}
		
		$tag .= "/>";
		
		return "{$tag}\n";
	}
	
	public static function select($id, $array, $selected = null, $head = true) {
		echo "<select id=\"{$id}\" name=\"{$id}\">\n";
		
		if ($head) {
			echo "<option".($selected == null ? " selected=\"selected\"" : "").">".(is_string($head) ? $head : "")."</option>\n";
		}
		
		foreach ($array as $val) {
			echo "<option".($val == $selected ? " selected=\"selected\"" : "").">{$val}</option>\n";
		}
		
		echo "</select>\n";
	}
	
	public static function link($url) {
		return nzbVR::instance()->settings->base_url.$url; //nzbVR::instance()->config->get("base_url")
	}
	
	public static function assetLink($url) {
		return self::link("skins/".nzbVR::instance()->skin()."/assets/".$url);
	}
}

?>