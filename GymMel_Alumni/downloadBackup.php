<?php
/**
 * Provision file for downloading a database backup.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   GymMel_Alumni
 * @author    Jan Harms <model_railroader@gmx-topmail.de>
 * @copyright 2023-2024 Gymnasium Melle
 * @license   https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @since     2023-09-24
 */

include 'constants.php';
include_once(__DIR__.'/vendor/autoload.php');
include_once('autoload.php');

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

echo $backup->getBackupStream();
$backup->deleteTemporaryBackupFile();

$logs = new Logs();
$logs->addLogEntry('A database-backup was successfully created and provided for download.');

exit();



