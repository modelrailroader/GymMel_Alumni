<?php

//
// Database Server Configuration
//
// Database host, e.g. localhost / 127.0.0.1
$db_host = '';
// Port of your database host, by default 3306
$db_port = '3306';
// Username of your database user. Note that the user needs read and write permissions.
$db_user = '';
// Password of your database user.
$db_password = '';
// The name of the database. You have to create one before entering here. The application wont created a database with this name.
$db_name = '';

//
// SMTP Server Configuration
//
// Mail host
$mail_host = '';
// Mail username
$mail_username = '';
// Mail From-Mail
$mail_from = '';
// Mail password
$mail_password = '';
// Mail port for SSL/TLS Encryption
$mail_port = 465;

// DEBUG-Modus:
// True: Error reporting is set to All.
// False: No errors are reported.
$DEBUG = true;

if($DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}
else {
    error_reporting(0);
    ini_set('display_errors', 0);
}