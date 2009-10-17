<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

abstract class XMLModel {
	protected $_storagePath;
	protected $_storagePathPrefix = "";
	protected $_storageParser;
	
	public function __construct($storagePath = "", $pathPrefix = "") {
		$this->_storagePath = $storagePath;
		$this->_storagePathPrefix = ($pathPrefix == "" ? "" : $pathPrefix."/");
		
		$this->_storageParser = new PicnicXMLParser();
	}
	
	public function storageName() {
		return $this->_storageName;
	}
	
	public function storagePath() {
		return $this->_storagePath;
	}
	
	public function storagePathPrefix() {
		return $this->_storagePathPrefix;
	}
	
	public function fullStoragePath() {
		return ROOT_PATH."data/{$this->storagePathPrefix()}{$this->storagePath()}";
	}
	
	public function load() {
		if (file_exists($this->fullStoragePath())) {
			$this->_storageParser->loadXMLFile($this->fullStoragePath());
			$data = $this->_storageParser->read();
		
			if ($data == null) {
				// error'd
				exit("ERROR'D");
			}
		
			$properties = get_object_vars($data);
		
			foreach ($properties as $key => $val) {
				$this->$key = $val;
			}
		} else {
			$h = fopen($this->fullStoragePath(), "x");
			fclose($h);
			
			$this->save();
		}
		
		return $this;
	}
	
	public function delete() {
		unlink($this->fullStoragePath());
	}
	
	public function save($diskWrite = true) {
		$this->_storageParser->loadObjects($this);
		$this->_storageParser->write();
		
		if ($diskWrite) {
			$this->_storageParser->writeXMLFile($this->fullStoragePath());
		}
		
		return $this;
	}
	
	public function toXML() {
		$this->_storageParser->save(false);
		
		return $this->_storageParser->getXML();
	}
}

abstract class Watcher {
	public $id;
	public $name;
	public $category = -1;
	public $language = null;
	public $format = null;
	public $source = null;
	public $created;
	
	/*
		should we;
			- save watchers to xml, "link" to type xml for extra info (tv shows etc...)
				(implement in parent class):
					- have a method; $this->read() --- reads from data/series/{$this->link_id}.xml
															or data/movies/{$this->link_id}.xml
					- have a method; $this->write() --- writes data to the xml files
					(if required by parent class - i.e. movies (to get the latest info) and tv shows to get the latest episodes)
					- have a method; $this->update() --- updates the info 
	 */
	
	public function __construct($id = null, $name = null, array $language = null, array $format = null, array $source = null, $category = -1) {
		if ($id != null) {
			$this->id = $id;
		} else {
			$this->id = PicnicUtils::random();
		}

		$this->name = $name;
		$this->language = $language;
		$this->format = $format;
		$this->source = $source;
		
		$this->category = $category;
		
		$this->created = strtotime(date("Y-m-d"));
	}
	
	public function getMarker() {
		return null;
	}
	
	public function check() {
		$term = $this->toSearchTerm();
		
		if ($term != null && $term != "") {
			$marker = $this->getMarker();
		
			// search newzbin
			$newzbin = new Newzbin();
			$results = $newzbin->search($this, $marker);
			
			$result = null;
			$r = array();
			
			foreach ($results as $report) {
				if ($report->state == "Report is complete") {
					$result = $report;
					break;
				}
			}
			
			
			
			if ($result != null) {
				$r[] = $result;
				
				// need to mark the episode as downloaded
				$this->mark($result);
			}
			
			if ($this->toSearchTerm() != $term) {
				return array_merge($r, $this->check());
			}
			
			return $r;
		} else {
			return null;
		}
	}
	
	public function mark($report) {
		// mark as downloaded
	}
	
	public function delete() {
		
	}
	
	public function updateInformation() {
		// called when the watcher should get information about it self from third party sources
	}
	
	public function toNewzbinQuery() {
		$q = $this->toSearchTerm()." ".$this->toNewzbinParams();
		$q = rtrim(ltrim($q));
		
		return $q;
	}
	
	public function toSearchTerm() {
		return null; // "^\"{$this->name}\"";
	}
	
	public function toNewzbinParams() {
		$params = array();
		
		if ($this->language != null) {
			foreach ($this->language as $language) {
				$params[] = "Attr:Language~{$language}";
			}
		}
		
		if ($this->format != null) {
			foreach ($this->format as $format) {
				$params[] = "Attr:VideoFormat~{$format}";
			}
		}
		
		if ($this->source != null) {
			foreach ($this->source as $source) {
				if ($source != "") {
					$params[] = "Attr:VideoSource~{$source}";
				}
			}
		}
		
		return implode(" ", $params);
	}
}

?>