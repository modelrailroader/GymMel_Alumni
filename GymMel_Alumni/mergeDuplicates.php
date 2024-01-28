<?php
/**
 * Controller file for merging duplicates.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   GymMel_Alumni
 * @author    Jan Harms <model_railroader@gmx-topmail.de>
 * @copyright 2023-2024 Gymnasium Melle
 * @license   https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @since     2024-01-28
 */

include 'constants.php';
include_once(__DIR__.'/vendor/autoload.php');
include_once('autoload.php');

use src\User;
use src\Logs;
use src\DataHelper;

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = new User();
if(!$user->authenticateWithSession()) {
    require 'accessDenied.php';
    exit();
}

$dataHelper = new DataHelper();
$logs = new Logs();

$data = json_decode(file_get_contents('php://input'), true);

if ($success = $dataHelper->mergeDuplicates($data['allIds'], $data['alumniId'])) {
    $logs->addLogEntry('The duplicates ' . json_encode($data['allIds']) . ' were successfully merged to ' . $data['alumniId'] . '.');
}

$response = array('success' => $success);

echo json_encode($response);
exit();