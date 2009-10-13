<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

class Season {
	public $num;
	
	public $episodes = array();
}

class Episode {
	public $season;
	public $num;
	public $series_num;
	public $name;
	public $airs_date;
	public $rage_url;
	public $poster_url;
	public $downloaded = false;
	public $ignored = false;
	
	public function identifier() {
		$num = sprintf("%02d", $this->num);
		return "{$this->season}x{$num}";
	}
}

?>