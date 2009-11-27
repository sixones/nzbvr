<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class ApplicationController extends PicnicController {
	protected $_watchers = array();

	public $notifications = array();
	
	protected $_currentUser = null;
	
	public function __construct() {
		parent::__construct();
		
		if (!class_exists("PicnicUpdateRequiredException", false)) {
			throw new PicnicMissingRequirementException("You need to update the picnic framework to continue;\n<pre><code>cd nzbvr/picnic\n\ngit pull origin master</code></pre>", 0, "ApplicationController", "__construct");
		}

		if (isset($_SESSION["nzbvr-skin-mode"])) {
			$mode = $_SESSION["nzbvr-skin-mode"];
			
			$this->setSkinMode($mode, false);
		} else {
			$this->setSkinMode("main");
		}
		
		// load all watchers
		$this->_watchers = new Watchers();
		$this->_watchers->load();
		
		if (nzbVR::instance()->localStore->size() < $this->_watchers->size()) {
			$this->notification("You need to <a href=\"#watchers/update.html?type=series\" class=\"content slow\">update</a> your series data to get cached artwork.<br /><br /><em>This can take awhile as nzbVR will download artwork for each series.</em>");
		}
	}
	
	public function setSkinMode($mode, $setSession = true) {
		nzbVR::instance()->setSkinMode($mode);
		
		if ($setSession) {
			$_SESSION["nzbvr-skin-mode"] = $mode;
		}
		
		$this->view()->useTemplateFolder(ROOT_PATH."skins/".nzbVR::instance()->skin()."/views");
	}
	
	public function requiresAuthentication() {
		// checks if request is authentication
		// if not redirect to login
	}
	
	public function notification($message, $class = "info") {
		$notification = new StandardObject();
		$notification->message = $message;
		$notification->className = $class;
	
		$this->notifications[] = $notification;
	}
}

?>