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
	
	public function download() {
		$id = $this->picnic()->currentRoute()->getSegment(0);
		
		$watcher = $this->_watchers->get($id);
		
		$this->result = null;
		
		if ($watcher != null) {
			$season_id = $this->picnic()->currentRoute()->getSegment(1);
			$episode_id = $this->picnic()->currentRoute()->getSegment(2);
			
			$watcher->load();
			
			$ep = null;
			
			foreach ($watcher->series()->seasons as $season) {
				if ($season->num == $season_id) {
					
					foreach ($season->episodes as $episode) {
						if ($episode->num == $episode_id) {
							$ep = $episode;
							
							break;
						}
					}
					
					break;
				}
			}
			
			$this->episode = $ep;
			
			$newzbin = new Newzbin();
			$results = $newzbin->rawSearch("{$watcher->name} {$ep->identifier()} ".$watcher->toNewzbinParams(), $watcher);
			
			
			if ($results != null && is_array($results) && sizeof($results) > 0) {
			
				$this->result = $watcher->findSuitableReport($results);
				
				$sabnzbd = new SABnzbd(nzbVR::instance()->settings->sabnzbd_address, nzbVR::instance()->settings->sabnzbd_apikey);
				$sabnzbd->send(array($this->result));
				
				$this->episode->downloaded = true;
				$this->episode->downloaded_at = time();
				
				$watcher->save();
			}			
		}
	}
	
	public function show() {
		$id = $this->picnic()->currentRoute()->getSegment(0);
		
		if ($id != null && $id != 0) {
			foreach ($this->_watchers->watchers as $watcher) {
				if ($watcher != null && $watcher instanceof SeriesWatcher && $watcher->id == $id) {
					$watcher->load();
					
					$this->watcher = $watcher;
					$this->series = $watcher->series();
					
					return;
				}
			}
		}
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

			$this->updateWatcher($watcher);
			
			$watcher->save();
			
			$this->_watchers->add($watcher);
		
			$this->_watchers->save();
			
			$this->watcher = $watcher;
			
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
		//$watcher->load();
		
		$watcher->series()->update();
		$watcher->series()->save();
		
		// get images
		$localStore = nzbVR::instance()->localStore;
		
		$localStore->storeImage("series/banners", TVDB::imageURL($watcher->series()->banner));
		$localStore->storeImage("series/poster", TVDB::imageURL($watcher->series()->poster));
		$localStore->storeImage("series/fanart", TVDB::imageURL($watcher->series()->fanart));
		
		$localStore->save();
	}
	
	public function update() {
		$this->series = array();

		foreach ($this->_watchers->watchers as $watcher) {
			if ($watcher != null) {
				if ($watcher instanceof SeriesWatcher) {
					$watcher->load();
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