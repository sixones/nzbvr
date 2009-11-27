<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class SettingsController extends ApplicationController {
	public function index() {	
		$this->settings = nzbVR::instance()->settings;
		
		// find all the skins
		$skinDirs = scandir(ROOT_PATH."skins/");
		$this->skins = array();
		
		foreach ($skinDirs as $skinDir) {
			if ($skinDir != "." && $skinDir != "..") {
				if (is_dir(ROOT_PATH."skins/{$skinDir}")) {
					$this->skins[] = (string)$skinDir;
				}
			}
		}
	}
	
	public function save() {
		nzbVR::instance()->settings->set("base_url", $this->params()->get("base_url"));
		
		nzbVR::instance()->settings->set("main_skin", $this->params()->get("main_skin"));
		nzbVR::instance()->settings->set("mobile_skin", $this->params()->get("mobile_skin"));
		
		nzbVR::instance()->settings->set("newzbin_username", base64_encode($this->params()->get("newzbin_username")));
		nzbVR::instance()->settings->set("newzbin_password", base64_encode($this->params()->get("newzbin_password")));
		
		nzbVR::instance()->settings->set("sabnzbd_address", $this->params()->get("sabnzbd_address"));
		nzbVR::instance()->settings->set("sabnzbd_apikey", base64_encode($this->params()->get("sabnzbd_apikey")));
		nzbVR::instance()->settings->set("sabnzbd_username", base64_encode($this->params()->get("sabnzbd_username")));
		nzbVR::instance()->settings->set("sabnzbd_password", base64_encode($this->params()->get("sabnzbd_password")));
		
		nzbVR::instance()->settings->set("xbmc_address", $this->params()->get("xbmc_address"));
		nzbVR::instance()->settings->set("xbmc_username", base64_encode($this->params()->get("xbmc_username")));
		nzbVR::instance()->settings->set("xbmc_password", base64_encode($this->params()->get("xbmc_password")));
		
		nzbVR::instance()->settings->set("prowl_apikey", base64_encode($this->params()->get("prowl_apikey")));
	
		nzbVR::instance()->settings->set("growl_password", base64_encode($this->params()->get("growl_password")));
		
		if ($this->params()->get("growl_address") != nzbVR::instance()->settings->growl_address) {
			nzbVR::instance()->settings->set("growl_address", $this->params()->get("growl_address"));
		
			if (nzbVR::instance()->settings->growl_address != "") {
				$growl = new Growl();
				$growl->addNotification("Report found for watcher", true);
				$growl->register();
			}
		}
	
		nzbVR::instance()->settings->save();
		
		//$this->notification("Settings saved successfully", "success");
		
		$this->redirect("index");
	}
}

?>