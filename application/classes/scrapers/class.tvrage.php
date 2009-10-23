<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class TVRageSeries {
	public $id;
	public $name;
	public $country;
	public $first_aired;
	public $last_aired;
	public $network;
	public $season_count;
	public $airs_time;
	public $airs_day;
	public $runtime;
	public $status;
	public $rage_url;
}

class TVRage extends TVScraper {
	public function __construct() {
		
	}
	
	protected function parseDate($originalDate) {
		return PicnicDateTime::createFromString($originalDate);
		
		$pieces = explode("/", $originalDate);
		$c = sizeof($pieces);
		
		$datetime = null;
		$date = "";
		
		switch ($c) {
			case 1:
				// just a year
				$datetime = DateTime::createFromFormat("Y", $originalDate);
				break;
			case 2:
				// month + year
				$datetime = DateTime::createFromFormat("M/Y", $originalDate);
				break;
			case 3:
				// month + day + year
				$datetime = DateTime::createFromFormat("M/d/Y", $originalDate);
				break;
		}
		
		//PicnicUtils::dump($datetime);
		
		if ($datetime != null) {
			$date = $datetime->date;
			
		}
		
		return $date;
	}
	
	public function search($name) {
		$url = str_replace("{show_name}", urlencode($name), "http://services.tvrage.com/feeds/full_search.php?show={show_name}");
		
		$xml = new SimpleXMLElement($url, null, true);
		
		$results = array();
		
		for ($i = 0; $i < sizeof($xml); $i++) {
			$item = $xml->show[$i];
			
			$show = new TVRageSeries();
			$show->id = (string)$item->showid;
			$show->name = (string)$item->name;
			$show->country = (string)$item->country;
			
			$show->first_aired = (string)$item->started;
			$show->last_aired = (string)$item->ended;
			$show->airs_time = (string)$item->airtime;
			$show->airs_day = (string)$item->airday;
			$show->runtime = (string)$item->runtime;
			$show->season_count = (string)$item->seasons;
			$show->network = (string)$item->network;
			$show->rage_url = (string)$item->link;
			$show->status = (string)$item->status;
			
			$results[] = $show;
		}
		
		return $results;
	}
	
	public function update($series) {
		$url = "http://services.tvrage.com/feeds/full_show_info.php?sid={$series->tvrage_id}";

		$item = new SimpleXMLElement($url, NULL, true);
		
		$series->tvrage_id = (string)$item->showid;
		$series->name = (string)$item->name;
		$series->country = (string)$item->origin_country;
		$series->first_aired = (string)$item->started;
		$series->last_aired = (string)$item->ended;
		$series->network = (string)$item->network;
		$series->season_count = (string)$item->totalseasons;
		$series->airs_time = (string)$item->airtime;
		$series->airs_day = (string)$item->airday;
		$series->runtime = (string)$item->runtime;
		$series->rage_url = (string)$item->showlink;
		$series->status = (string)$item->status;
		
		//for ($i = 0; $i < sizeof($item->Episodelist->Season); $i++) {
		//	$season = $item->Episodelist->Season[$i];
		foreach ($item->Episodelist->Season as $season) {
			$s = 0;
			
			foreach ($season->attributes() as $key => $val) {
				if ($key == 'no') $s = (string)$val;
			}
			
			$episodeSeason = null;
			$seasonExists = false;
			
			foreach ($series->seasons as $showSeason) {
				if ($showSeason->num == $s) {
					$episodeSeason = $showSeason;
					$seasonExists = true;
				}
			}
			
			if ($episodeSeason == null) {
				$episodeSeason = new Season();
				$episodeSeason->num = $s;
			}
			
			for ($j = 0; $j < sizeof($season->episode); $j++) {
				$item = $season->episode[$j];
				
				$episode = null;
				$episodeExists = false;
				
				foreach ($episodeSeason->episodes as $ep) {
					if ($ep->series_num == (string)$item->epnum) {
						$episode = $ep;
						$episodeExists = true;
					} 
				}
				
				if ($episode == null) {
					$episode = new Episode();
				}

				$episode->season = $s;
				$episode->num = (string)$item->seasonnum;
				$episode->series_num = (string)$item->epnum;
				$episode->name = (string)$item->title;
				$episode->airs_date = PicnicDateTime::createFromString((string)$item->airdate)->format('Y-m-d H:i:s');//date('Y-m-d H:i:s', strtotime((string)$item->airdate));
				$episode->rage_url = (string)$item->link;
				$episode->poster_url = (string)$item->screencap;
				
				if (!$episodeExists) {
					$episodeSeason->episodes[] = $episode;
				}
			}
			
			if (!$seasonExists) {
				$series->seasons[] = $episodeSeason;
			}
		}
		
		return $series;
	}
}

?>