<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class Settings extends XMLModel {
	// nzbVR settings
	public $base_url = "/";
	public $installed = false;
	
	public $main_skin = "default";
	public $mobile_skin = "iphone";
	
	public $series_display_mode = "list";
	public $remember_sortorder = false;
	
	// newzbin
	public $newzbin_username = "";
	public $newzbin_password = "";
	
	// sabnzbd
	public $sabnzbd_address = "";
	public $sabnzbd_apikey = "";
	public $sabnzbd_username = "";
	public $sabnzbd_password = "";
	
	// xbmc
	public $xbmc_address = "";
	public $xbmc_username = "";
	public $xbmc_password = "";
	
	// prowl
	public $prowl_apikey = "";
	
	// growl
	public $growl_address = "";
	public $growl_password = "";
	
	protected $_rawConfiguration = array();
	
	public function __construct() {
		parent::__construct("settings.xml");
	}
	
	public function rawConfiguration() {
		return $this->_rawConfiguration;
	}
	
	public function set($key, $value) {
		$this->$key = $value;
	}
	
	public function loadFile($path) {
		include($path);
		
		$this->_rawConfiguration = $c;
		
		foreach ($this->_rawConfiguration as $key => $val) {
			$this->set($key, $val);
		}
	}
}

?>