<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class SeriesWatcher extends Watcher {	
	protected $_series = null;
	
	public function __construct($id = null, $name = null, $language = "English", $format = "x264", $source = null) {
		parent::__construct($id, $name, $language, $format, $source);
	}
	
	public function series() {
		return $this->_series;
	}
	
	public function load() {
		// load $this->_series
		$this->_series = new Series($this->id);
		$this->_series->load();
	}
	
	public function save() {
		$this->_series->save();
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
	
	public function __construct($id = null) {
		if ($id != null) {
			$this->id = $id;
		}
		
		parent::__construct("{$this->id}.xml", "series");
	}
	
	public function storagePath() {
		return "{$this->id}.xml";
	}
	
	protected function findTVDB() {
		$tvdb = new TVDB();
		$results = $tvdb->search($this->name);
		
		$this->tvdb_id = (string)$results[0]->id;
	}
	
	public function update() {		
		// update info + episodes from tvrage
		$tvrage = new TVRage();
		$tvrage->update($this);
		
		if ($this->tvdb_id == 0) {
			$this->findTVDB();
		}
		
		// get info from tvdb
		$tvdb = new TVDB();
		$tvdb->update($this);
	}
	
	public function tvdbURL() {
		return "http://thetvdb.com/index.php?tab=series&id={$this->tvdb_id}";
	}
	
	public function imdbURL() {
		return "http://www.imdb.com/title/{$this->imdb_id}/";
	}
	
	public function next() {
		$nextEpisode = null;
		$between = 0;
		$last = $this->last();
		
		for ($i = (sizeof($this->seasons) - 1); $i > 0; $i--) {
			$season = $this->seasons[$i];
			
			foreach ($season->episodes as $episode) {
				$today = strtotime(date("Y-m-d"));
				$airdate = strtotime($episode->airs_date);

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
		
		//PicnicUtils::dump($last);
		
		for ($i = (sizeof($this->seasons) - 1); $i > 0; $i--) {
			$season = $this->seasons[$i];
			
			foreach ($season->episodes as $episode) {
				$today = strtotime(date("Y-m-d"));
				$airdate = strtotime($episode->airs_date);

				if ($episode->airs_date != null && $airdate > strtotime($last->airs_date)) {
					if ($afterEpisode == null || $afterEpisode != null && (($beforeToday && $airdate <= $today) || !$beforeToday)) {
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
		$diff = 10000000000;
		
		for ($i = (sizeof($this->seasons) - 1); $i > 0; $i--) {
			$season = $this->seasons[$i];
			
			foreach ($season->episodes as $episode) {
				$today = strtotime(date("Y-m-d"));
				$airdate = strtotime($episode->airs_date);

				if ($episode->downloaded && $episode->airs_date != null && strtotime($episode->airs_date) < strtotime(date("Y-m-d"))) {
					$currentDiff = $today - $airdate;
					
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
		$diff = 10000000000;
		
		for ($i = (sizeof($this->seasons) - 1); $i > 0; $i--) {
			$season = $this->seasons[$i];
			
			foreach ($season->episodes as $episode) {
				$today = strtotime(date("Y-m-d"));
				$airdate = strtotime($episode->airs_date);

				if ($episode->airs_date != null && strtotime($episode->airs_date) < strtotime(date("Y-m-d"))) {
					$currentDiff = $today - $airdate;
					
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