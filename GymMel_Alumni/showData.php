<?php
/**
 * Backend for showing an overview of all network members.
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
use src\DataHelper;
use src\Alert;
use src\Logs;
use src\Template;

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
$alert = new Alert();

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS);

if($action === 'delete') {
    $deleted_alumni = $dataHelper->getAlumniData($id)['name'];
    if($dataHelper->deleteAlumniById($id)) {
        $success_message = $alert->successAlert('Der Datensatz wurde erfolgreich gelöscht.');
        $logs->addLogEntry('The data of the alumni ' . $deleted_alumni . ' was succesfully deleted.');
    }
}

$data = $dataHelper->getAllAlumniData();

include 'header.php';

$template = new Template('./assets/templates');
$template->setTemplate('showData.twig');

$templateVars = [
    'data' => $data,
    'success_message' => isset($success_message) ? $success_message : ''
];

echo $template->render($templateVars);

include 'footer.php';