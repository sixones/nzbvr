<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class WatchersController extends ApplicationController {
	public function index() {	
		
	}
	
	public function check() {
		foreach ($this->_watchers->watchers as $watcher) {
			$watcher->check();
		}
	}
}

?>