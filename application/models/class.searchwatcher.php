<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class SearchWatcher extends Watcher {
	public function toSearchTerm() {
		return $this->name;
	}
}

?>