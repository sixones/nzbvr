<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class MovieDBResult {
	public $id;
	public $name;
	public $alternative_name;
	public $imdb_id;
	public $rating;
	public $overview;
	public $tvdb_url;
}

class MovieDB extends TVScraper {
	public function __construct() {
		
	}
	
	public function search($name) {
		$url = str_replace("{show_name}", urlencode($name), "http://api.themoviedb.org/2.1/Movie.search/en/xml/79574dad8d32843a34126c7afc20fdc2/{show_name}");
		
		$xml = new SimpleXMLElement($url, null, true);
		
		$results = array();
		
		for ($i = 0; $i < sizeof($xml->movies->movie); $i++) {
			$item = $xml->movies->movie[$i];
			
			$movie = new MovieDBResult();
			$movie->id = (string)$item->id;
			$movie->imdb_id = (string)$item->imdb_id;
			$movie->name = (string)$item->name;
			$movie->alternative_name = (string)$item->alternative_name;
			$movie->released = (string)$item->released;

			$results[] = $movie;
		}
		
		return $results;
	}
	
	public function update($movie) {
		$url = "http://api.themoviedb.org/2.1/Movie.getInfo/en/xml/79574dad8d32843a34126c7afc20fdc2/{$movie->moviedb_id}";

		$item = new SimpleXMLElement($url, NULL, true);

		$item = $item->movies[0]->movie;

		$movie->moviedb_id = (string)$item->id;
		$movie->imdb_id = (string)$item->imdb_id;
		
		$movie->name = (string)$item->name;
		$movie->alternative_name = (string)$item->alternative_name;
		
		$movie->overview = (string)$item->overview;
		
		$movie->runtime = (string)$item->runtime;
		$movie->moviedb_url = (string)$item->url;
		
		$movie->released = (string)$item->released;
		
		$movie->homepage = (string)$item->homepage;
		$movie->trailer = (string)$item->trailer;
		
		// parse images
		for ($i = 0; $i < sizeof($item->images); $i++) {
			$image = $item->images->image[$i];
			
			if ($image != null) {
				if ((string)$image->attributes()->type == "poster") {
					$movie->poster = (string)$image->attributes()->url;
				
					if ((string)$image->attributes()->size == "original") {
						break;
					}
				}
			}
		}
		
		return $movie;
	}
}

?>