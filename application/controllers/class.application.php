<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class ApplicationController extends PicnicController {
	protected $_watchers = array();
	
	public $notifications = array();
	
	public function __construct() {
		parent::__construct();
		
		if (isset($_SESSION["nzbvr-skin-mode"])) {
			$mode = $_SESSION["nzbvr-skin-mode"];
			
			$this->setSkinMode($mode, false);
		} else {
			$this->setSkinMode("main");
		}
		
		// load all watchers
		$this->_watchers = new Watchers();
		$this->_watchers->load();
	}
	
	public function setSkinMode($mode, $setSession = true) {
		nzbVR::instance()->setSkinMode($mode);
		
		if ($setSession) {
			$_SESSION["nzbvr-skin-mode"] = $mode;
		}
		
		Picnic::getInstance()->view()->useTemplateFolder(ROOT_PATH."skins/".nzbVR::instance()->skin()."/views");
	}
	
	public function notification($message, $class = "info") {
		$notification = new StandardObject();
		$notification->message = $message;
		$notification->className = $class;
	
		$this->notifications[] = $notification;
	}
}

?>