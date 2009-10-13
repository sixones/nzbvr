<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class nzbVR {
	const VERSION = "0.2";
	
	public $settings = null;
	
	public static $languages = array("English", "French", "Spanish", "German", "Italian", "Danish", "Dutch", "Japanese", "Cantonese", "Mandarin", "Korean", "Russian", "Polish", "Vietnamese", "Swedish", "Norwegian", "Finnish", "Turkish", "Unknown");
	
	public static $formats = array("DivX", "XviD", "DVD", "Blu-Ray", "HD-DVD", "HD.TS", "H.264", "x264", "AVCHD", "SVCD", "VCD", "WMV", "iPod", "PSP", "ratDVD", "Other", "720p", "1080i", "1080p");
	
	public static $sources = array("CAM", "Screener", "TeleCine", "R5Retail", "TeleSync", "Workprint", "VHS", "DVD", "HD-DVD", "Blu-Ray", "TVCap", "HDTV", "Unknown");
	
	private static $__instance = null;
	
	public function __construct() {
		self::$__instance = $this;
		
		$this->settings = new Settings();
		$this->settings->load();
	}
	
	public function skin() {
		return $this->settings->main_skin;
	}
	
	public static function instance() {
		if (self::$__instance == null) {
			new nzbVR();
		}
		
		return self::$__instance;
	}
}

?>