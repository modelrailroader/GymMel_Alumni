<?php
include 'constants.php';
include_once(__DIR__.'/vendor/autoload.php');
include_once(__DIR__.'/src/Logs.php');
include_once(__DIR__.'/src/Backup.php');

use src\User;
use src\Logs;
use src\Backup;

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = new User();
if(!$user->authenticateWithSession()) {
    require 'accessDenied.php';
    exit();
}

$backup = new Backup();

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $backup->getFilename() . '"');

echo($backup->getBackupStream());
$backup->deleteTemporaryBackupFile();

$logs = new Logs();
$logs->addLogEntry('A database-backup was successfully created and provided for download.');

exit();



