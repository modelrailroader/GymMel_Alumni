<?php
/**
 * Backend page for showing an overview of all users.
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
$alert = new Alert();
$logs = new Logs();

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if($action === 'delete') {
    $username_deleted = $user->getUserDataById($id)['username'];
    $user->deleteUser($id);
    $success_message = $alert->successAlert('Der Benutzer wurde erfolgreich gelöscht!');
    $logs->addLogEntry('The user ' . $username_deleted . ' was successfully deleted.');
}

include 'header.php';

$data = $user->getAllUsers();

$template = new Template('./assets/templates');
$template->setTemplate('users.twig');

$templateVars = [
    'success_message' => isset($success_message) ? $success_message : '',
    'userData' => $data
];

echo $template->render($templateVars);

include 'footer.php';