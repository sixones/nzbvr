<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class WatchersController extends ApplicationController {
	public $watchers = null;

	public function index() {
		$this->watchers = array();
		
		foreach ($this->_watchers->watchers as $watcher) {
			if ($watcher != null && $watcher instanceof SearchWatcher) {
				$this->watchers[] = $watcher;
			}
		}
	}
	
	public function create() {	
		$name = $this->params()->get("name");
		$language = $this->params()->get("language");
		$format = $this->params()->get("format");
		$source = $this->params()->get("source");
		
		if ($name != null) {
			$watcher = new SearchWatcher(null, $name, array($language), array($format), array($source));

			$this->_watchers->add($watcher);
		
			$this->_watchers->save();
			
			$this->watcher = $watcher;
			
			return $this->redirect("index");
		}
	}
	
	public function check() {
		$this->results = array();
		
		$this->_watchers->last_benchmark = new WatcherBenchmark();
		$this->_watchers->last_benchmark->mark("start");
	
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
		
		$this->_watchers->last_benchmark->downloads = sizeof($this->results);
		
		$this->_watchers->last_benchmark->mark("end");
		$this->_watchers->save();
		
		if ($this->picnic()->router()->outputType() == "html") {
			$this->redirect("index");
		}
	}
	
	public function delete() {
		$id = $this->picnic()->currentRoute()->getSegment(0);
		
		$copy = $this->_watchers->watchers;
		
		$this->_watchers->watchers = array();
		
		foreach ($copy as $watcher) {
			if ($watcher->id != $id) {
				$this->_watchers->add($watcher);
			} else {
				$watcher->delete();
			}
		}
		
		$this->_watchers->save();
		
		$this->redirect("index");
	}
}

?>