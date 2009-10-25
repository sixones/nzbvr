<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class MovieWatcher extends Watcher implements ILoadableWatcher, ISavableWatcher {
	protected $_movie = null;
	
	public $downloaded = false;
	public $reports = null;
	public $downloaded_at = null;
	public $category = 6;
	
	public function __construct($id = null, $name = null, array $language = array("English"), array $format = array("x264"), array $source = array()) {
		parent::__construct($id, $name, $language, $format, $source, 6);
	}
	
	public function getDownloadedDateTime() {
		return PicnicDateTime::createFromTimestamp($this->downloaded_at);
	}
	
	public function movie() {
		return $this->_movie;
	}
	
	public function update() {
		$this->movie()->update();
	}
	
	public function check() {
		if (!$this->downloaded) {
			return parent::check();
		}
	}
	
	public function load() {
		// load $this->_movie
		$this->_movie = new Movie($this->id, $this);
		$this->_movie->load();
	}
	
	public function mark(array $reports) {
		foreach ($reports as $report) {
			$this->downloaded = true;
			$this->downloaded_at = time();
			$this->report = $report;
		}
	}
	
	public function save() {
		$this->_movie->save();
	}
	
	public function getMarker() {
		if ($this->_movie != null) {
			return $this->_movie;
		}
		
		return null;
	}
	
	public function isReportSuitable($report) {
		if (stripos($report->name, "(Deleted") === false) {
			if (stripos($report->name, "(Password") === false) {
				return true;
			}
		}
		
		return false;
	}
	
	public function toSearchTerm() {
		$q = "^\"{$this->name}\"";
		
		if ($this->movie()->year != null) {
			$q .= " ({$this->movie()->year})";
		}
		
		return $q;
	} 
}

class Movie extends XMLModel {
	public $id;
	public $tvdb_id = 0;
	public $imdb_id = 0;
	public $name;
	public $alternative_name;
	public $overview;
	public $runtime;
	public $moviedb_url;
	public $released;
	public $year = null;
	public $homepage;
	public $trailer;
	public $poster;
	
	public function __construct($id = null) {
		if ($id != null) {
			$this->id = $id;
		}
		
		parent::__construct("{$this->id}.xml", "movies");
	}
	
	public static function sortByUpcoming($a, $b) {
		if ($a == null) {
			return 1;
		}

		if ($b == null) {
			return -1;
		}

		$aT = strtotime($a->released);
		$bT = strtotime($b->released);

		if ($aT == $bT) {
			return 0;
		}

		return ($aT < $bT) ? -1 : 1;
	}
	
	public function storagePath() {
		return "{$this->id}.xml";
	}
	
	public function posterUrl() {
		return nzbVR::instance()->localStore->getImageURL($this->poster);
	}
	
	protected function findMovieDB() {
		$moviedb = new MovieDB();
		$results = $moviedb->search($this->name);
		$result = null;
		
		foreach ($results as $movie) {
			if ($movie->name == "") continue;
		
			if ($movie->name == $this->name) {
				$result = $movie;
				break;
			}
			
			$i = strpos($movie->name, $this->name);

			if ($i !== false && $i == 0) {
				$result = $movie;
				break;
			}
		}
		
		if ($result == null && sizeof($results) > 0) {
			$result = $results[0];
		}
		
		if ($result != null) {
			$this->moviedb_id = (string)$result->id;
		}
	}
	
	public function update() {
		if ($this->moviedb_id == 0) {
			$this->findMovieDB();
		}
		
		if ($this->moviedb_id != 0) {
			$moviedb = new MovieDB();
			$moviedb->update($this);
		}
		
		if ($this->poster != null) {
			$localStore = nzbVR::instance()->localStore;
			$localStore->storeImage("movies/poster", $this->poster);
			$localStore->save();
		}
	}
}

?>