<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class SABnzbd {
	protected $_address;
	protected $_apikey;
	
	public function __construct() {
		$this->_address = nzbVR::instance()->settings->sabnzbd_address;
		$this->_apikey = base64_decode(nzbVR::instance()->settings->sabnzbd_apikey);
	}
	
	public function send(array $reports) {
		foreach ($reports as $report) {
			$this->sendID($report->newzbin_id);
		}
	}
	
	public function sendID($id) {
		$params = array(
			"mode=addid",
			"name={$id}"
		);
			
		$params[] = "apikey={$this->_apikey}";
					
		$this->request("?".implode("&", $params));
	}
	
	public function request($cmd) {
		$url = "http://".$this->_address."/api/".$cmd;
		
		$curl = new NetworkRequest();
		$curl->set($url, array(CURLOPT_RETURNTRANSFER => 1));
		
		$result = $curl->execute();
		
		$curl->close();
	}
}

?>