<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class DashboardController extends ApplicationController {
	public function index() {	
		
	}
	
	public function show() {
		$this->watchers = $this->_watchers;
	}
}

?>