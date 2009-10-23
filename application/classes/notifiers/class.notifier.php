<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class Notifiers {
	protected $_notifiers = array();
	
	public function __construct() {
		if (nzbVR::instance()->settings->xbmc_address != '') {
			//$this->_notifiers[] = new XBMC();
		}
		
		if (nzbVR::instance()->settings->prowl_apikey != '') {
			//$this->_notifiers[] = new Prowl();
		}
		
		if (nzbVR::instance()->settings->growl_address != '') {
			//$this->_notifiers[] = new Growl();
		}
	}
	
	public function notification($message, $title) {
		foreach ($this->_notifiers as $n) {
			//$n->notification($message, $title);
		}
	}
}

abstract class Notifier {
	public function notification($message, $title) {
		
	}
}

?>