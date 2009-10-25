<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class nzbVR {
	const VERSION = "0.33";
	
	public $settings = null;
	public $localStore = null;
	
	public static $languages = array("English", "French", "Spanish", "German", "Italian", "Danish", "Dutch", "Japanese", "Cantonese", "Mandarin", "Korean", "Russian", "Polish", "Vietnamese", "Swedish", "Norwegian", "Finnish", "Turkish", "Unknown");
	
	public static $formats = array("DivX", "XviD", "DVD", "Blu-Ray", "HD-DVD", "HD.TS", "H.264", "x264", "AVCHD", "SVCD", "VCD", "WMV", "iPod", "PSP", "ratDVD", "Other", "720p", "1080i", "1080p");
	
	public static $sources = array("CAM", "Screener", "TeleCine", "R5Retail", "TeleSync", "Workprint", "VHS", "DVD", "HD-DVD", "Blu-Ray", "TVCap", "HDTV", "Unknown");
	
	public static $categories = array(
										"-1" => "Everything",
										"11" => "Anime",
										"1" => "Apps",
										"13" => "Books",
										"2" => "Consoles",
										"15" => "Discussions",
										"10" => "Emulation",
										"4" => "Games",
										"5" => "Misc",
										"6" => "Movies",
										"7" => "Music",
										"12" => "PDA",
										"14" => "Resources",
										"8" => "TV"
									 );
	
	private static $__instance = null;
	
	private $_skinMode = "main";
	
	public function __construct() {
		self::$__instance = $this;
		
		$this->settings = new Settings();
		$this->settings->load();
		
		$path = ROOT_PATH."config.php";
		
		if (file_exists($path)) {
			$this->settings->loadFile($path);
		}
		
		$this->localStore = new LocalStore();
	}

	public function setSkinMode($mode) {
		$this->_skinMode = $mode;
	}
	
	public function skin() {
		if ($this->_skinMode == "main") {
			$skin = $this->settings->main_skin;
		} else {
			$skin = $this->settings->mobile_skin;
		}

		return $skin;
	}
	
	public static function instance() {
		if (self::$__instance == null) {
			new nzbVR();
		}
		
		return self::$__instance;
	}
}

?>