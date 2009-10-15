<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class MoviesController extends ApplicationController {
	public function index() {
		$this->movies = array();
		
		foreach ($this->_watchers->watchers as $watcher) {
			if ($watcher != null && $watcher instanceof MovieWatcher) {
				$watcher->load();
				
				$movie = $watcher->movie();
				$movie->watcher = $watcher;
				
				$this->movies[] = $movie;
			}
		}
	}
	
	public function check() {
		foreach ($this->_watchers->watchers as $watcher) {
			if ($watcher != null && $watcher instanceof MovieWatcher) {
				$watcher->check();
			}
		}
		
		$this->redirect("index");
	}
	
	public function create() {	
		$name = $this->params()->get("name");
		$language = $this->params()->get("language");
		$format = $this->params()->get("format");
		$source = $this->params()->get("source");
		
		if ($name != null) {
			$watcher = new MovieWatcher(null, $name, $language, $format, $source);
			$watcher->load();
			$watcher->save();
		
			$this->_watchers->add($watcher);
		
			$this->_watchers->save();
			
			$this->watcher = $watcher;
		}
	}
	
	public function search() {
		$name = urldecode($this->picnic()->currentRoute()->getSegment(0));
		
		$tvrage = new TVRage();
		$results = $tvrage->search($name);
		
		$this->series = $results;
	}
	
	public function update() {
		$this->movies = array();

		foreach ($this->_watchers->watchers as $watcher) {
			if ($watcher != null) {
				if ($watcher instanceof MovieWatcher) {
					$watcher->load();
					
					$watcher->movie()->update();
					$watcher->movie()->save();

					$this->movies[] = $watcher->movie();
				}
			}
		}
		
		$this->_watchers->save();
	}
}

?>