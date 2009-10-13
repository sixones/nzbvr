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
	public $language;
	public $format;
	public $source;

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
	
	public function __construct($id = null, $name = null, $language = "English", $format = "x264", $source = null) {
		if ($id != null) {
			$this->id = $id;
		} else {
			$this->id = PicnicUtils::random();
		}

		$this->name = $name;
		$this->language = $language;
		$this->format = $format;
		$this->source = $source;
	}
	
	public function check() {
		
	}
	
	public function delete() {
		
	}
	
	public function updateInformation() {
		// called when the watcher should get information about it self from third party sources
	}
	
	public function toNewzbinQuery() {
		return "{$this->name} ".$this->toNewzbinParams();
	}
	
	public function toNewzbinParams() {
		$params = array();
		
		if ($this->language != null) {
			$params[] = "Attr:Language~{$this->language}";
		}
		
		if ($this->format != null) {
			$params[] = "Attr:VideoFormat~{$this->format}";
		}
		
		if ($this->source != null) {
			$params[] = "Attr:VideoSource~{$this->source}";
		}
		
		return implode(" ", $params);
	}
}

?>