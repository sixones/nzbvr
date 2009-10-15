<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class MovieWatcher extends Watcher {
	protected $_movie = null;
	
	public function __construct($id = null, $name = null, $language = "English", $format = "x264", $source = null) {
		parent::__construct($id, $name, $language, $format, $source, 6);
	}
	
	public function movie() {
		return $this->_movie;
	}
	
	public function load() {
		// load $this->_series
		$this->_movie = new Movie($this->id);
		$this->_movie->load();
	}
	
	public function save() {
		$this->_movie->save();
	}
}

class Movie extends XMLModel {
	public $id;
	
	public $imdb_id = 0;
	
	public function __construct($id = null) {
		if ($id != null) {
			$this->id = $id;
		}
		
		parent::__construct("{$this->id}.xml", "movies");
	}
	
	public function storagePath() {
		return "{$this->id}.xml";
	}
	
	public function update() {		
		$this->imdb_id = "222";
	}
}

?>