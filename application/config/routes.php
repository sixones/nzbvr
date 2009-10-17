<?php if (!defined("PICNIC")) { header("Location: /"); exit(1); }

$r->add(new PicnicRoute("/api/sabnzbd/send/(\d.+)", "APIController", "sendID"));

$r->add(new PicnicRoute("/movies/new", "MoviesController", "create"));
$r->add(new PicnicRoute("/movies/update", "MoviesController", "update"));
$r->add(new PicnicRoute("/movies/search/(\w.+)", "MoviesController", "search"));
$r->add(new PicnicRoute("/movies", "MoviesController", "index"));

$r->add(new PicnicRoute("/series/new", "SeriesController", "create"));
$r->add(new PicnicRoute("/series/check", "SeriesController", "check"));
$r->add(new PicnicRoute("/series/update", "SeriesController", "update"));
$r->add(new PicnicRoute("/series/search/(\w.+)", "SeriesController", "search"));
$r->add(new PicnicRoute("/series/delete/(\d.+)", "SeriesController", "delete"));
$r->add(new PicnicRoute("/series/show/(\d.+)", "SeriesController", "show"));
$r->add(new PicnicRoute("/series", "SeriesController", "index"));

$r->add(new PicnicRoute("/settings/save", "SettingsController", "save"));
$r->add(new PicnicRoute("/settings", "SettingsController", "index"));

$r->add(new PicnicRoute("/watchers/new", "WatchersController", "create"));
$r->add(new PicnicRoute("/watchers/check", "WatchersController", "check"));
$r->add(new PicnicRoute("/watchers/delete/(\d.+)", "WatchersController", "delete"));
$r->add(new PicnicRoute("/watchers", "WatchersController", "index"));

//$r->add(new PicnicRoute("/search/(\w.+)", "SearchController", "index"));
$r->add(new PicnicRoute("/search", "SearchController", "index"));

$r->add(new PicnicRoute("/dashboard", "DashboardController", "show"));

$r->add(new PicnicRoute("/upgrade", "DashboardController", "upgrade"));

$r->add(new PicnicRoute("/mobile", "DashboardController", "mobile"));
$r->add(new PicnicRoute("/m", "DashboardController", "mobile"));

$r->add(new PicnicRoute("/", "DashboardController", "index"));

?>