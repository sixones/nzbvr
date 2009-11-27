<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class SABnzbd {
	protected $_address;
	protected $_apikey;
	
	public function __construct() {
		$this->_address = nzbVR::instance()->settings->sabnzbd_address;
		$this->_apikey = base64_decode(nzbVR::instance()->settings->sabnzbd_apikey);
		
		$username = base64_decode(nzbVR::instance()->settings->sabnzbd_username);
		$password = base64_decode(nzbVR::instance()->settings->sabnzbd_password);
	}
	
	public function send(array $reports) {
		foreach ($reports as $report) {
			if ($report != null) {
				$this->sendID($report->newzbin_id);
			}
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
		$url = "http://{$this->_address}/api/{$cmd}";
		
		$username = base64_decode(nzbVR::instance()->settings->sabnzbd_username);
		$password = base64_decode(nzbVR::instance()->settings->sabnzbd_password);
		
		if ($username != null && $password != null) {
			$url .= "&ma_username={$username}&ma_password={$password}";
		}
		
		$curl = new NetworkRequest();
		$curl->set($url, array(CURLOPT_RETURNTRANSFER => 1));
		
		$result = $curl->execute();
		
		$curl->close();
	}
}

?>