<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class Store extends XMLModel {
	public $stored_objects;
	
	public function __construct() {
		parent::__construct("store.xml");
	}
	
	public function get($id) {
		foreach ($this->stored_objects as $obj) {
			if ($obj->id == $id) {
				return $obj;
			}
		}
		
		return null;
	}
	
	public function getUrl($id) {
		$obj = $this->get($id);
		
		if ($obj != null) {
			return $obj->getPath();
		}
		
		return "UNKNOWN!";
	}
	
	public function store($id, $filename, $type, $prefix = "") {
		$obj = new StoredObject();
		$obj->id = $id;
		$obj->filename = $filename;
		$obj->prefix = $prefix;
		$obj->type = $type;
		
		$this->stored_objects[] = $obj;
	}
}

class StoredObject {
	public $id;
	public $filename;
	public $prefix;
	public $type;
	
	public function getPath() {
		return "{$this->getFolderPath()}/{$this->filename}";
	}
	
	public function getFolderPath() {
		return "{$this->type}/{$this->prefix}";
	}
}

?>