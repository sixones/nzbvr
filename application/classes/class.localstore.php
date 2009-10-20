<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class LocalStore {
	protected $_store;
	
	public function __construct() {
		$this->_store = new Store();
		$this->_store->load();
	}
	
	public function save() {
		$this->_store->save();
	}
	
	public function storeImage($prefix, $url) {
		// download image
		// cache image
		// make a new store object
		// save
		
		$stored = new StoredObject();
		
		if (!@$reader = fopen($url, "rb")) {
			throw new PicnicDiskWriteFailureException("Unable to copy from `{$url}`.");
		}
		
		$stored->filename = basename($url);
		$stored->id = $url;
		$stored->type = "image";
		$stored->prefix = $prefix;
		
		PicnicUtils::mkdirR(ROOT_PATH."data/{$stored->getFolderPath()}");
		
		$writer = fopen(ROOT_PATH."data/{$stored->getPath()}", "wb");
		
		while (!feof($reader)) {
			$line = fread($reader, 1028);
			fwrite($writer, $line);
		}
		
		fclose($writer);
		fclose($reader);
		
		$this->_store->stored_objects[] = $stored;
	}
	
	public function getImageUrl($id) {
		$obj = $this->_store->get($id);
		
		return nzbVR::instance()->settings->base_url."data/".$obj->getPath();
	}
	
	public function size() {
		return sizeof($this->_store->stored_objects);
	}
}

?>