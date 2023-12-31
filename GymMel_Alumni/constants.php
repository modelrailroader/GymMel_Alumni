<?php

$db_host = 'localhost';
$db_port = '3306';
$db_user = 'root';
$db_password = 'root';
$db_name = 'alumni_dev';

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

