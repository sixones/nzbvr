<?php

require_once(APPLICATION_DIR . "classes/class.nzbvr.php");
require_once(APPLICATION_DIR . "classes/class.newzbin.php");
require_once(APPLICATION_DIR . "classes/class.sabnzbd.php");

require_once(APPLICATION_DIR . "classes/scrapers/class.scraper.php");
require_once(APPLICATION_DIR . "classes/scrapers/class.tvdb.php");
require_once(APPLICATION_DIR . "classes/scrapers/class.tvrage.php");

require_once(APPLICATION_DIR . "helpers/class.view.php");

require_once(APPLICATION_DIR . "controllers/class.application.php");
require_once(APPLICATION_DIR . "controllers/class.dashboard.php");
require_once(APPLICATION_DIR . "controllers/class.movies.php");
require_once(APPLICATION_DIR . "controllers/class.search.php");
require_once(APPLICATION_DIR . "controllers/class.series.php");
require_once(APPLICATION_DIR . "controllers/class.settings.php");
require_once(APPLICATION_DIR . "controllers/class.watchers.php");

require_once(APPLICATION_DIR . "models/class.watcher.php");
require_once(APPLICATION_DIR . "models/class.episode.php");
require_once(APPLICATION_DIR . "models/class.movie.php");
require_once(APPLICATION_DIR . "models/class.searchwatcher.php");
require_once(APPLICATION_DIR . "models/class.series.php");
require_once(APPLICATION_DIR . "models/class.settings.php");
require_once(APPLICATION_DIR . "models/class.watchers.php");

date_default_timezone_set("Europe/London");

?>