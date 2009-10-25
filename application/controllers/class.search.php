<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class SearchController extends ApplicationController {
	public function index() {	
		$query = $this->params()->get("query");
		$category = $this->params()->get("category");
		$format = $this->params()->get("format");
		$source = $this->params()->get("source");
		$language = $this->params()->get("language");
		
		//$id = null, $name = null, array $language = null, array $format = null, array $source = null, $category = -1)
		if ($query != null) {
			$watcher = new SearchWatcher(null, $query, array($language), array($format), array($source), array($category));

			$newzbin = new Newzbin();
			$this->results = $newzbin->search($watcher);
			
			$this->picnic()->view()->useTemplate("search/results");
		}
	}
}

?>