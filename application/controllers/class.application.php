<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class ApplicationController extends PicnicController {
	protected $_watchers = array();
	
	public function __construct() {
		parent::__construct();
		
		$this->setSkinMode("main");
		
		// load all watchers
		$this->_watchers = new Watchers();
		$this->_watchers->load();
	}
	
	public function setSkinMode($mode) {
		nzbVR::instance()->setSkinMode($mode);
		
		Picnic::getInstance()->view()->useTemplateFolder(ROOT_PATH."skins/".nzbVR::instance()->skin()."/views");
	}
}

?>