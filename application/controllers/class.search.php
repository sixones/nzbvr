<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class SearchController extends ApplicationController {
	public function index() {	
		$query = $this->params()->get("query");
		$format = $this->params()->get("format");
		$source = $this->params()->get("source");
		$language = $this->params()->get("language");
		
		if ($query != null) {
			$watcher = new SearchWatcher(null, $query, $language, $format, $source);

			$newzbin = new Newzbin();
			$this->results = $newzbin->search($watcher);
			
			$this->picnic()->view()->useTemplate("search/results");
		}
	}
}

?>