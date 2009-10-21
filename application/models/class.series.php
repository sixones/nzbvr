<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class SeriesWatcher extends Watcher implements ILoadableWatcher, ISavableWatcher {	
	protected $_series = null;
	
	public $category = 8;
	
	public function __construct($id = null, $name = null, array $language = array("English"), array $format = array("x264"), array $source = array()) {
		parent::__construct($id, $name, $language, $format, $source, 8);
	}
	
	public function series() {
		return $this->_series;
	}
	
	public function load() {
		// load $this->_series
		$this->_series = new Series($this->id, $this);
		$this->_series->load();
	}
	
	public function update() {
		$this->series()->update();
	}
	
	public function mark(array $reports) {
		foreach ($reports as $report) {
			$report->marker->downloaded = true;
			$report->marker->downloaded_at = time();
		}
	}
	
	public function save() {
		$this->_series->save();
	}
	
	public function getMarker() {
		if ($this->_series != null) {
			return $this->_series->nextDownload();
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
		if ($this->_series != null) {
			$next = $this->_series->nextDownload();
			
			if ($next != null) {
				return "^\"{$this->name}\" - {$next->identifier()}";
			}
		}
		
		return null;
	}
}

class Series extends XMLModel {
	public $id;
	
	public $tvrage_id = 0;
	public $tvdb_id = 0;
	public $imdb_id = 0;
	
	// tvdb stuff
	public $overview = "";
	public $poster;
	public $banner;
	public $fanart;
	
	// tvrage
	public $name;
	public $country;
	public $first_aired;
	public $last_aired;
	public $network;
	public $season_count;
	public $airs_time;
	public $airs_day;
	public $runtime;
	public $rage_url;
	public $status = "Continuing";
	
	public $seasons = array();
	
	private $_parent = null;
	
	public function __construct($id = null, $parent = null) {
		if ($id != null) {
			$this->id = $id;
		}
		
		parent::__construct("{$this->id}.xml", "series");
		
		$this->_parent = $parent;
	}
	
	public function storagePath() {
		return "{$this->id}.xml";
	}
	
	protected function findTVDB() {
		$tvdb = new TVDB();
		$results = $tvdb->search($this->_parent->name);
		$result = null;
		
		foreach ($results as $series) {
			if ($series->name == "") continue;
		
			if ($series->name == $this->_parent->name) {
				$result = $series;
				break;
			}
			
			$i = strpos($series->name, $this->_parent->name);

			if ($i !== false && $i == 0) {
				$result = $series;
				break;
			}
		}
		
		if ($result == null && sizeof($results) > 0) {
			$result = $results[0];
		}
			
		if ($result != null) {
			$this->tvdb_id = (string)$result->id;
		}
	}
	
	public function update() {		
		// update info + episodes from tvrage
		$tvrage = new TVRage();
		$tvrage->update($this);
		
		if ($this->tvdb_id == 0) {
			$this->findTVDB();
		}
		
		if ($this->tvdb_id != 0) {
			// get info from tvdb
			$tvdb = new TVDB();
			$tvdb->update($this);
		}
		
		// cache images
		$localStore = nzbVR::instance()->localStore;
		$shouldSave = false;
		
		if ($this->banner != null) {
			$localStore->storeImage("series/banners", TVDB::imageURL($this->banner));
			$shouldSave = true;
		}
		
		if ($this->poster != null) {
			$localStore->storeImage("series/poster", TVDB::imageURL($this->poster));
			$shouldSave = true;
		}
		
		if ($this->fanart != null) {
			$localStore->storeImage("series/fanart", TVDB::imageURL($this->fanart));
			$shouldSave = true;
		}
		
		if ($shouldSave) {
			$localStore->save();
		}
	}
	
	public function tvdbURL() {
		return "http://thetvdb.com/index.php?tab=series&id={$this->tvdb_id}";
	}
	
	public function imdbURL() {
		return "http://www.imdb.com/title/{$this->imdb_id}/";
	}
	
	public function posterUrl() {
		return nzbVR::instance()->localStore->getImageURL("http://thetvdb.com/banners/{$this->poster}");
	}
	
	public function bannerUrl() {
		return nzbVR::instance()->localStore->getImageURL("http://thetvdb.com/banners/{$this->banner}");
	}
	
	public function fanartUrl() {
		return nzbVR::instance()->localStore->getImageURL("http://thetvdb.com/banners/{$this->fanart}");
	}
	
	public function next() {
		$nextEpisode = null;
		$between = 0;
		$last = $this->last();
		
		for ($i = (sizeof($this->seasons) - 1); $i > 0; $i--) {
			$season = $this->seasons[$i];
			
			foreach ($season->episodes as $episode) {
				if ($episode->airs_date != null && strtotime($episode->airs_date) > strtotime($last->airs_date)) {
					$nextEpisode = $episode;
					return $nextEpisode;
				}
			}
		}
		
		return null;
	}
	
	public function nextDownload($afterEpisode = null, $beforeToday = true) {
		$nextEpisode = null;
		$between = 0;
		$last = ($afterEpisode == null ? $this->lastDownload() : $afterEpisode);

		// $i = (sizeof($this->seasons) - 1); $i >= 0; $i--
		for ($i = 0; $i < sizeof($this->seasons); $i++) {
			$season = $this->seasons[$i];
			
			foreach ($season->episodes as $episode) {
				$airdate = strtotime($episode->airs_date);

				if ($episode->airs_date != null && $airdate > strtotime($last->airs_date)) {
					if ($afterEpisode == null || $afterEpisode != null && (($beforeToday && $airdate <= $this->_parent->created) || !$beforeToday)) {
						$nextEpisode = $episode;
						return $nextEpisode;
					}
				}
			}
		}
		
		return null;
	}
	
	public function lastDownload() {
		$lastEpisode = null;
		$diff = 10000000000000000000;
		
		for ($i = (sizeof($this->seasons) - 1); $i >= 0; $i--) {
			$season = $this->seasons[$i];
			
			for ($j = (sizeof($season->episodes) - 1); $j >= 0; $j--) {
				$episode = $season->episodes[$j];
			
				$airdate = strtotime($episode->airs_date);
				
				if ($episode->airs_date != null && ($episode->downloaded == true)) {
					$currentDiff = $this->_parent->created - $airdate;
					
					if ($currentDiff < $diff) {
						$diff = $currentDiff;
						$lastEpisode = $episode;
					}
					
				}
			}
		}
		
		if ($lastEpisode == null) {
			$lastEpisode = $this->last();
		}
		
		return $lastEpisode;
	}
	
	public function last() {
		$lastEpisode = null;
		$diff = 10000000000000000000;
		
		for ($i = (sizeof($this->seasons) - 1); $i >= 0; $i--) {
			$season = $this->seasons[$i];
			
			foreach ($season->episodes as $episode) {
				$airdate = strtotime($episode->airs_date);

				if ($episode->airs_date != null && $airdate < $this->_parent->created) {
					$currentDiff = $this->_parent->created - $airdate;
					
					if ($currentDiff < $diff) {
						$diff = $currentDiff;
						$lastEpisode = $episode;
					}
					
				}
			}
		}

		return $lastEpisode;
	}
}

?>