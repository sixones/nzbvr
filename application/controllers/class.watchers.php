<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class WatchersController extends ApplicationController {
	public function index() {	
		$this->watchers = array();
	}
	
	public function check() {
		$this->results = array();
	
		foreach ($this->_watchers->watchers as $watcher) {
			$watcher->load();
			
			$results = $watcher->check();
			
			if ($results != null && is_array($results)) {
				$this->results = array_merge($this->results, $results);
				
				$watcher->save();
			}
		}
		
		if ($this->results != null && sizeof($this->results) > 0) {
			$sabnzbd = new SABnzbd(nzbVR::instance()->settings->sabnzbd_address, nzbVR::instance()->settings->sabnzbd_apikey);
			$sabnzbd->send($this->results);
		}
		
		$this->redirect("index");
	}
}

?>