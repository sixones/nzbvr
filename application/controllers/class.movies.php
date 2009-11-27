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
		
		uasort($this->movies, "Movie::sortByUpcoming");
	}
	
	public function check() {
		foreach ($this->_watchers->watchers as $watcher) {
			$watcher->check();
		}
		
		$this->redirect("index");
	}
	
	public function create() {	
		$moviedbId = $this->params()->get("moviedb_id");
		$name = $this->params()->get("name");
		$language = $this->params()->get("language");
		$format = $this->params()->get("format");
		$source = $this->params()->get("source");
		
		if ($moviedbId != null && $name != null) {
			$watcher = new MovieWatcher(null, $name, array($language), array($format), array($source));
			$watcher->load();
			$watcher->movie()->moviedb_id = $moviedbId;

			$watcher->update();
			
			$watcher->save();
			
			$this->_watchers->add($watcher);
		
			$this->_watchers->save();
			
			$this->watcher = $watcher;
			
			return $this->redirect("index");
		}
	}
	
	public function search() {
		$name = urldecode($this->route()->getSegment(0));
		
		$moviedb = new MovieDB();
		$results = $moviedb->search($name);
		
		$this->movies = $results;
	}
	
	public function delete() {
		$id = $this->route()->getSegment(0);
		
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