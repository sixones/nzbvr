<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class Settings extends XMLModel {
	// nzbVR settings
	public $base_url = "/";
	public $installed = false;
	
	public $main_skin = "default";
	public $mobile_skin = "iphone";
	
	// newzbin
	public $newzbin_username = "";
	public $newzbin_password = "";
	
	// sabnzbd
	public $sabnzbd_address = "";
	public $sabnzbd_apikey = "";
	
	// xbmc
	public $xbmc_address = "";
	public $xbmc_username = "";
	public $xbmc_password = "";
	
	public function __construct() {
		parent::__construct("settings.xml");
	}
	
	public function set($key, $value) {
		$this->$key = ($value == null ? $this->$key : $value);
	}
}

?>