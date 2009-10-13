<?php

error_reporting(E_ALL);

if (!defined("DS")) define("DS", DIRECTORY_SEPARATOR);
if (!defined("ROOT_PATH")) define("ROOT_PATH", dirname(__FILE__).DS);

if (!defined("PICNIC_HOME")) define("PICNIC_HOME", "..".DS."picnic".DS."picnic".DS);

require_once(PICNIC_HOME."class.picnic.php");

# mock the route ...
$picnic = Picnic::getInstance();
$picnic->mock("/watchers/check.xml");

require_once("index.php");


?>