<?php

error_reporting(E_ALL);

session_start();

if (!defined("DS")) define("DS", DIRECTORY_SEPARATOR);
if (!defined("ROOT_PATH")) define("ROOT_PATH", dirname(__FILE__).DS);

if (!defined("PICNIC_HOME")) define("PICNIC_HOME", ROOT_PATH."picnic".DS."picnic".DS);
if (!defined("APPLICATION_DIR")) define("APPLICATION_DIR", ROOT_PATH."application".DS);

require_once(PICNIC_HOME."class.picnic.php");

// application defined bootstrap file
if (file_exists(ROOT_PATH."bootstrap.php")) {
	require_once(ROOT_PATH."bootstrap.php");
}

$picnic = Picnic::getInstance();
$picnic->loadApplication(APPLICATION_DIR);

// render picnic
$picnic->render();

?>