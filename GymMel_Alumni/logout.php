<?php
include 'constants.php';
include_once(__DIR__ . '/vendor/autoload.php');
include_once('autoload.php');

use src\User;
use src\Logs;

$user = new User();
$logs = new Logs();

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!$user->authenticateWithSession()) {
    require 'accessDenied.php';
    exit();
}

$user->logout();
include 'index.php';
exit();

