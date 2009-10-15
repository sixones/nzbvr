<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class SeriesController extends ApplicationController {
	public function index() {
		$this->series = array();
		
		foreach ($this->_watchers->watchers as $watcher) {
			if ($watcher != null && $watcher instanceof SeriesWatcher) {
				$watcher->load();
				
				$series = $watcher->series();
				$series->watcher = $watcher;
				
				$this->series[] = $series;
			}
		}
		
		$mode = $this->params()->get("mode");
		
		if ($mode != null) {
			nzbVR::instance()->settings->set("series_display_mode", $mode);
			nzbVR::instance()->settings->save();
		} else {
			$mode = nzbVR::instance()->settings->series_display_mode;
		}
		
		$this->useTemplate("{$mode}.html");
		
		//$this->redirect($mode."Display");
	}
	
	public function listDisplay() { }
	public function thumbnailsDisplay() { }
	
	public function check() {
		foreach ($this->_watchers->watchers as $watcher) {
			if ($watcher != null && $watcher instanceof SeriesWatcher) {
				$watcher->check();
			}
		}
		
		$this->redirect("index");
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
	
	public function create() {	
		// create a new Series watcher based on; tvrage_id
		$tvrageId = $this->params()->get("tvrage_id");
		$name = $this->params()->get("name");
		$language = $this->params()->get("language");
		$format = $this->params()->get("format");
		$source = $this->params()->get("source");
		
		if ($tvrageId != null && $name != null) {
			$watcher = new SeriesWatcher(null, $name, array($language), array($format), array($source));
			$watcher->load();
			$watcher->series()->tvrage_id = $tvrageId;
			$watcher->save();
		
			$this->_watchers->add($watcher);
		
			$this->_watchers->save();
			
			$this->watcher = $watcher;
			
			$this->updateWatcher($this->watcher);
			
			return $this->redirect("index");
		}
	}
	
	public function search() {
		$name = urldecode($this->picnic()->currentRoute()->getSegment(0));
		
		$tvrage = new TVRage();
		$results = $tvrage->search($name);
		
		$this->series = $results;
	}
	
	protected function updateWatcher($watcher) {
		$watcher->load();
		
		$watcher->series()->update();
		$watcher->series()->save();
	}
	
	public function update() {
		$this->series = array();

		foreach ($this->_watchers->watchers as $watcher) {
			if ($watcher != null) {
				if ($watcher instanceof SeriesWatcher) {
					$this->updateWatcher($watcher);

					$this->series[] = $watcher->series();
				}
			}
		}
		
		$this->_watchers->save();
		
		if ($this->picnic()->router()->outputType() == "html") {
			$this->redirect("index");
		}
	}
}

?>