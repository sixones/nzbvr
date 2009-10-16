<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class DashboardController extends ApplicationController {
	public function index() {	
		// check for new version?
		if (false) {
			$this->notification("An update to nzbVR is available, you should <a href=\"/upgrade.html\">upgrade now</a>.", "warning");
		}
		
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
		$this->commands = array();
	
		chdir(ROOT_PATH);
		
		$this->commands[] = $this->execute("git pull");
		
		chdir(PICNIC_HOME);
		
		$this->commands[] = $this->execute("git pull origin master");
	}
	
	private function execute($command) {
		ob_start();
		
		passthru($command, $state);
		
		$result = ob_get_contents();
		
		ob_end_clean();
		
		var_dump($state);
		var_dump($command);
		var_dump($result);
		
		return $result;
	}
}

?>