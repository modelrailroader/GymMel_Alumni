<?php

$db_host = '';
$db_port = '';
$db_user = '';
$db_password = '';
$db_name = '';

// DEBUG-Modus:
// True: Alle Fehler werden angezeigt
// False: Keine Fehler werden angezeigt
$DEBUG = false;

if($DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

