<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class SearchWatcher extends Watcher {
	public $downloaded = false;
	public $report = null;

	public function toSearchTerm() {
		return $this->name;
	}
	
	public function mark(array $reports) {
		$this->downloaded = true;
		$this->reports = $reports;
	}
	
	public function check() {
		if (!$this->downloaded) {
			parent::check();
		}
	}
}

?>