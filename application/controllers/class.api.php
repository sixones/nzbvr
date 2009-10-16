<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class APIController extends ApplicationController {
	public function __construct() {
		parent::__construct();
		
		// erm ...
		if ($this->picnic()->router()->outputType() == "html") {
			$this->picnic()->router()->outputType("xml");
		}
	}

	public function sendID() {
		$id = $this->picnic()->currentRoute()->getSegment(0);

		if ($id != null) {
			$sabnzbd = new SABnzbd();
			$sabnzbd->sendID($id);
		
			$this->state = "OK";
		} else {
			$this->state = "Fail";
			$this->message = "You must provide an ID.";
		}
	}
}

?>