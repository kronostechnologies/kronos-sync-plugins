<?php
// Files we need
require_once 'lib/Sabre/autoload.php';
require_once 'config/config.php';
require_once 'include/KronosBasicAuthBackend.php';
require_once 'include/KronosPrincipalBackend.php';
require_once 'include/Debug.php';

// settings
date_default_timezone_set('Canada/Eastern');

// If you want to run the SabreDAV server in a custom location (using mod_rewrite for instance)
// You can override the baseUri here.
// $baseUri = '/';

/* Database */
$pdo = new PDO('mysql:host='.MYSQL_DBHOST.';dbname='.MYSQL_DBNAME, MYSQL_DBUSER, MYSQL_DBPASSWORD);
$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

//Mapping PHP errors to exceptions
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("exception_error_handler");

// Backends
$authBackend = new KronosBasicAuthBackend($pdo);
$calendarBackend = new Sabre_CalDAV_Backend_PDO($pdo);
$carddavBackend = new Sabre_CardDAV_Backend_PDO($pdo);
$principalBackend = new KronosPrincipalBackend($pdo);

// Directory structure 
$nodes = array(
    new Sabre_DAVACL_PrincipalCollection($principalBackend),
	new Sabre_CalDAV_CalendarRootNode($principalBackend, $calendarBackend),
    new Sabre_CardDAV_AddressBookRoot($principalBackend, $carddavBackend),
);

$server = new Sabre_DAV_Server($nodes);

if (isset($baseUri))
    $server->setBaseUri($baseUri);

/* Server Plugins */
$authPlugin = new Sabre_DAV_Auth_Plugin($authBackend,'SabreDAV');
$server->addPlugin($authPlugin);

$aclPlugin = new Sabre_DAVACL_Plugin();
$server->addPlugin($aclPlugin);

$caldavPlugin = new Sabre_CalDAV_Plugin();
$server->addPlugin($caldavPlugin);

$carddavPlugin = new Sabre_CardDAV_Plugin();
$server->addPlugin($carddavPlugin);

// Support for html frontend
$browser = new Sabre_DAV_Browser_Plugin();
$server->addPlugin($browser);

// And off we go!
$server->exec();
