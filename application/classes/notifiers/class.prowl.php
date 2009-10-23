<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class Prowl extends Notifier {
	protected $_apikey;
	
	public function __construct() {
		$this->_apikey = base64_decode(nzbVR::instance()->settings->prowl_apikey);
	}
	
	public function notification($message, $title = "nzbVR notification") {
		$params = array(
					"apikey" => $this->_apikey,
					"application" => "nzbVR",
					"event" => $title,
					"description" => $message);
		
		$this->request("add", $params);
	}
	
	public function request($cmd, $params) {
		$url = "https://prowl.weks.net/publicapi/".$cmd;

		$curl = new NetworkRequest();
		$curl->set($url, array(
						CURLOPT_USERAGENT => "nzbVR-".nzbVR::VERSION,
						CURLOPT_HTTPAUTH => CURLAUTH_ANY,
						CURLOPT_SSL_VERIFYPEER => FALSE,
						CURLOPT_SSL_VERIFYHOST => FALSE,
						CURLOPT_RETURNTRANSFER => 1,
						CURLOPT_FRESH_CONNECT => TRUE,
						CURLOPT_POST => TRUE,
						CURLOPT_POSTFIELDS => $params)
					);
		
		$result = $curl->execute();
		
		$curl->close();
	}
}

?>