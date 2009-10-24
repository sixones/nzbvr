<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class ScraperRequestFailed extends PicnicException { }

abstract class TVScraper {
	public function search($name) {
		// searches for a show using the name as the search argument
		// creates an Array of Show objects with basic information set (at a minimum: id + name to use in this->updateShow())
		// returns an Array of Show objects
	}
	
	public function update($show) {
		// takes a Show object and updates the information and episodes
		// if the show is empty the infomation will be populated
		// returns the updated Show object
	}
}

?>