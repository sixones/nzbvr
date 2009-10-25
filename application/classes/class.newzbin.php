<?php

class NewzbinSearchRequestFailed extends PicnicException { }

class NZBReport {
	public $newzbin_id;
	public $name;
	public $link;
	public $groups;
	public $category;
	public $state;
	public $more_info;
	public $format;
	public $source;
	public $language;
	public $nzb;
	public $bytesize;
	public $postdate;
	public $views;
	public $comments;
}

class Newzbin {
	protected $_authentication = null;
	
	public function __construct() {
		$username = base64_decode(nzbVR::instance()->settings->newzbin_username);
		$password = base64_decode(nzbVR::instance()->settings->newzbin_password);
		
		$this->_authentication = "{$username}:{$password}";
	}
	
	public function rawSearch($query, $watcher) {
		$url = $this->createSearchURL($query, $watcher->category);

		$results = $this->searchRequest($url);

		return $results;
	}
	
	public function search($watcher, $marker = null) {
		$url = $this->createSearchURL($watcher->toNewzbinQuery(), $watcher->category);

		$results = $this->searchRequest($url, $marker);

		return $results;
	}
	
	protected function findReportAttribute($attributes, $type) {
		$result = array();
		
		foreach ($attributes as $node) {
			$attr = $node->attributes();

			if ((string)$attr["type"] == $type) {
				$result[] = (string)$node;
			}
		}
		
		return $result;
	}
	
	protected function searchRequest($url, $marker = null) {
		$xml = null;
		$results = array();
		
		try {
			$xml = new SimpleXMLElement($url, null, true);
		} catch (Exception $ex) {
			$xml = null;
			throw new NewzbinSearchRequestFailed("Newzbin down or provided authentication is incorrect.");
		}
		
		for ($i = 0; $i < sizeof($xml->entry); $i++) {
			$item = $xml->entry[$i];
			
			$report = new NZBReport();
			$report->newzbin_id = (string)PicnicUtils::getIndex($item->xpath("report:id"), 0);
			$report->name = (string)$item->title;
			$report->link = (string)$item->id;
			$report->groups = null;
			$report->category = (string)PicnicUtils::getIndex($item->xpath("report:category"), 0);
			$report->state = (string)PicnicUtils::getIndex($item->xpath("report:progress"), 0);
			$report->more_info = (string)PicnicUtils::getIndex($item->xpath("report:moreinfo"), 0);
			$report->marker = $marker;
			//var_dump($item->xpath("report:attributes/report:attribute"));
			
			$attributes = $item->xpath("report:attributes/report:attribute");
			
			$report->format = $this->findReportAttribute($attributes, "Video Fmt");
			$report->source = $this->findReportAttribute($attributes, "Source");
			$report->language = $this->findReportAttribute($attributes, "Language");
			$report->nzb = (string)PicnicUtils::getIndex($item->xpath("report:nzb"), 0);
			$report->bytesize = (string)PicnicUtils::getIndex($item->xpath("report:size"), 0);
			$report->postdate = (string)PicnicUtils::getIndex($item->xpath("report:postdate"), 0);
			$report->views = (string)PicnicUtils::getIndex($item->xpath("report:stats/report:views"), 0);
			$report->comments = (string)PicnicUtils::getIndex($item->xpath("report:stats/report:comments"), 0);
			
			$results[] = $report;
		}

		return $results;
	}
	
	public function createSearchURL($query, $category) {
		// "u_post_results_amt=1",
		$params = array(
				"q=".urlencode($query),
				"searchaction=Search",
				"category=".$category,
				'fpn=p',
				"area=-1",
				'u_nfo_posts_only=0',
				'u_url_posts_only=0',
				'u_comment_posts_only=0',
				"sort=ps_edit_date",
				"order=desc",
				"feed=atom",
				"areadone=-1"
			);

		return "http://".$this->_authentication."@www.newzbin.com/search?".implode("&", $params);
	}
}

?>