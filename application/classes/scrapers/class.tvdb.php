<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

// retrieve mirror list --> http://thetvdb.com/api/1DBF0A2C61EDB18C/mirrors.xml

// search for series --> http://www.thetvdb.com/api/GetSeries.php?seriesname=<NAME>

// with series id stored:
	// last checked == null || last checked is bigger than 15 days
		// retrieve all show info + all episodes --> http://thetvdb.com/api/1DBF0A2C61EDB18C/series/<SERIESID>/all/en.xml
		// store time checked --> http://www.thetvdb.com/api/Updates.php?type=none
	// else
		// retrieve list of updated series --> http://www.thetvdb.com/api/Updates.php?type=series&time=<TIMESTAMP>
		// loop through series list
		// if series is stored by nzbvr
			// update show info + episodes --> http://thetvdb.com/api/1DBF0A2C61EDB18C/series/<SERIESID>/all/en.xml


// images:
	// base image url: http://thetvdb.com/banners/<IMAGEURL>
		// http://thetvdb.com/banners/fanart/vignette/73244-6.jpg
		// http://thetvdb.com/banners/episodes/73244/1112621.jpg
		
class TVDBSeries {
	public $id;
	public $imdb_id;
	public $name;
	public $overview;
	public $language;
	public $first_aired;
	public $banner;

	public $network;
	public $airs_day;
	public $airs_time;
	public $runtime;
	public $fanart_url;
	public $poster_url;
	public $status;
}

class TVDB extends TVScraper {
	protected $_lang;
	
	protected static $__mirrors = array("xml" => "", "banner" => "", "zip" => "");
	
	public function __construct() {
		// retrieve list of mirrors
		// TODO: move to static creation / caching
		
		self::getMirrors();
	}
	
	public static function getMirrors() {
		if (!is_array(self::$__mirrors["xml"])) {
			$xml = new SimpleXMLElement("http://thetvdb.com/api/1DBF0A2C61EDB18C/mirrors.xml", null, true);
		
			if (!$xml) {
				return null;
			}
		
			foreach ($xml->Mirror as $mirror) {
				if ($mirror->typemask > 0) {
					self::$__mirrors["xml"][] = (string)$mirror->mirrorpath;
				}
			
				if ($mirror->typemask > 1) {
					self::$__mirrors["banner"][] = (string)$mirror->mirrorpath;
				}
			
				if ($mirror->typemask > 3) {
					self::$__mirrors["zip"][] = (string)$mirror->mirrorpath;
				}
			}
			
			$xml = null;
		}
	}

	public static function imageURL($imageUrl) {
		self::getMirrors();
		
		return self::$__mirrors["banner"][0]."/banners/".$imageUrl;
	}

	public function search($name) {
		$url = self::$__mirrors["xml"][0]."/api/GetSeries.php?seriesname=".urlencode($name);
		
		$xml = new SimpleXMLElement($url, null, true);
		$results = array();
		
		foreach ($xml->Series as $show) {
			$series = new TVDBSeries();
			
			$series->id = (string)$show->id;
			$series->imdb_id = (string)$show->IMDB_ID;
			$series->name = (string)$show->SeriesName;
			$series->overview = (string)$show->Overview;
			$series->first_aired = (string)$show->FirstAired;
			$series->language = (string)$show->language;
			$series->banner = (string)$show->banner;

			$results[] = $series;
		}
		
		return $results;
	}
	
	public function update($series) {
		$url = self::$__mirrors["xml"][0]."/api/1DBF0A2C61EDB18C/series/".urlencode($series->tvdb_id)."/all/en.xml";
		$xml = new SimpleXMLElement($url, null, true);
		
		$series->tvdb_id = (string)$xml->Series->id;
		$series->imdb_id = (string)$xml->Series->IMDB_ID;
		//$show->name = (string)$xml->Series->SeriesName;
		//$show->network = (string)$xml->Series->Network;
		$series->overview = (string)$xml->Series->Overview;
		//$show->airs_day = (string)$xml->Series->Airs_DayOfWeek;
		//$show->airs_time = (string)$xml->Series->Airs_Time;
		//$show->first_aired = (string)$xml->Series->FirstAired;
		//$show->language = (string)$xml->Series->Language;
		//$show->runtime = (string)$xml->Series->Runtime;
		$series->banner = (string)$xml->Series->banner;
		$series->fanart = (string)$xml->Series->fanart;
		$series->poster = (string)$xml->Series->poster;
		//$show->status = (string)$xml->Series->Status;
		
		return $series;
	}
}

/*
include("../models/class.episode.php");
include("../models/class.watcher.php");
include("../models/class.tvshow.php");

$tvdb = new TVDB();

$search_results = $tvdb->search("The Office");

$show = $search_results[0];

echo "<pre>";
var_dump($show);
echo "</pre>";

$tvdb->update($show);

echo "<pre>";
var_dump($show);
echo "</pre>";
*/
?>