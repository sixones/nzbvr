<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class DashboardController extends ApplicationController {
	public function index() {
		$this->setSkinMode("main");
	
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
		
		$series = array();
		$movies = array();
		$generics = array();
		
		foreach ($this->_watchers->watchers as $watcher) {
			if ($watcher != null) {
				if ($watcher instanceof SeriesWatcher) {
					$watcher->load();
				
					$series[] = $watcher;
				} else if ($watcher instanceof MovieWatcher) {
					$watcher->load();
				
					$movies[] = $watcher;
				} else {
					$generics[] = $watcher;
				}
			}
		}
		
		uasort($series, "Series::sortByUpcomingWatcher");
		uasort($movies, "Movie::sortByUpcomingWatcher");

		$this->sortedWatchers = array_merge($series, $movies, $generics);
	}

	public function mobile() {
		// set skin mode
		$this->setSkinMode("mobile");
	
		$this->useTemplate("index");
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