<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class XBMC extends Notifier {
	protected $_address;
	protected $_authentication;
	
	public function __construct() {
		$this->_address = nzbVR::instance()->settings->xbmc_address;
		
		$u = base64_decode(nzbVR::instance()->settings->xbmc_username);
		$p = base64_decode(nzbVR::instance()->settings->xbmc_password);
		
		$this->_authentication = "{$u}:{$p}";
	}
	
	public function notification($message, $title= "nzbVR Notification") {
		$message = urlencode($message);
		$title = urlencode($title);
	
		$cmd = "/xbmcCmds/xbmcHttp?command=ExecBuiltIn&parameter=Notification({$title},{$message})";
		
		$this->request($cmd);	
	}
	
	public function request($cmd) {
		$url = "http://".$this->_address.$cmd;
		
		$curl = new NetworkRequest();
		$curl->set($url, array(CURLOPT_RETURNTRANSFER => 1, CURLOPT_USERPWD => $this->_authentication));
		
		$result = $curl->execute();
		
		$curl->close();
	}
}

?>