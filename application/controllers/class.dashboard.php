<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class DashboardController extends ApplicationController {
	public function index() {	
		// check for new version?
		$this->notification("An update to nzbVR is available, you should <a href=\"/upgrade.html\">upgrade now</a>.", "warning");
		
		if ($this->params()->get("applied") == "update") {
			$this->notification("Updated applied successfully!", "success");
		}
	}
	
	public function show() {
		$this->watchers = $this->_watchers;
	}
	
	public function mobile() {
		// set skin mode
		$this->setSkinMode("mobile");
	
		$this->redirect("index");
	}
	
	public function upgrade() {
		echo exec("pushd ".ROOT_PATH)."\n"."\n";
		echo exec("git pull origin master")."\n"."\n";
		
		echo exec("pushd ".PICNIC_HOME)."\n"."\n";
		echo exec("git pull origin master")."\n"."\n";
	}
}

?>