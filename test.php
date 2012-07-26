<?php

// Files we need

require_once 'lib/Sabre/autoload.php';

// Now we're creating a whole bunch of objects

// Change public to something else, if you are using a different directory for your files
$rootDirectory = new Sabre_DAV_FS_Directory('public');

// The server object is responsible for making sense out of the WebDAV protocol
$server = new Sabre_DAV_Server($rootDirectory);

// If your server is not on your webroot, make sure the following line has the correct information

// $server->setBaseUri('/~evert/mydavfolder'); // if its in some kind of home directory
$server->setBaseUri('/test.php/'); // if you can't use mod_rewrite, use server.php as a base uri
// $server->setBaseUri('/'); // ideally, SabreDAV lives on a root directory with mod_rewrite sending every request to server.php

// The lock manager is reponsible for making sure users don't overwrite each others changes. Change 'data' to a different 
// directory, if you're storing your data somewhere else.
$lockBackend = new Sabre_DAV_Locks_Backend_File('data/locks');
$lockPlugin = new Sabre_DAV_Locks_Plugin($lockBackend);

$server->addPlugin($lockPlugin);

// All we need to do now, is to fire up the server
$server->exec();