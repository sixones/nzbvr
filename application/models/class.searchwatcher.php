<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class SearchWatcher extends Watcher {
	public $downloaded = false;
	public $reports = null;
	public $downloaded_at = null;
	
	public function getDownloadedDateTime() {
		return PicnicDateTime::createFromTimestamp($this->downloaded_at);
	}

	public function toSearchTerm() {
		return $this->name;
	}
	
	public function mark(array $reports) {
		$this->downloaded = true;
		$this->reports = $reports;
		$this->downloaded_at = time();
	}
	
	public function check() {
		if (!$this->downloaded) {
			return parent::check();
		}
	}
}

?>