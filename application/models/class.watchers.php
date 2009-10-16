<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class Watchers extends XMLModel {
	public $watchers = array();
	public $last_benchmark = null;
	
	public function __construct() {
		parent::__construct("watchers.xml");
	}
	
	public function get($id) {
		foreach ($this->watchers as $watcher) {
			if ($watcher->id == $id) {
				return $watcher;
			}
		}
		
		return null;
	}
	
	public function add($watcher) {
		$this->watchers[] = $watcher;
	}
	
	public function size($type = null) {
		$watchers = array();
		
		if ($type != null) {
			foreach ($this->watchers as $w) {
				if ($w instanceof $type) {
					$watchers[] = $w;
				}
			}
		} else {
			$watchers = $this->watchers;
		}
		
		return sizeof($watchers);
	}
}

class WatcherBenchmark extends PicnicBenchmark {
	public $downloads = 0;
}

?>