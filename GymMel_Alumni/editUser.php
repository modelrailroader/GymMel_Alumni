<?php
/**
 * Backend for editing a user.
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
include_once(__DIR__ . '/vendor/autoload.php');
include_once('autoload.php');

use src\User;
use src\Alert;
use src\Logs;
use src\Template;

$alert = new Alert();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = new User();
if (!$user->authenticateWithSession()) {
    require 'accessDenied.php';
    exit();
}

$logs = new Logs();

$id_get = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (isset($id_get)) {
    $data = $user->getUserDataById($id_get);
}

$id = filter_input(INPUT_POST, 'userid', FILTER_VALIDATE_INT);
if (isset($id)) {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $twofactor_active = filter_input(INPUT_POST, '2fa');
    if (filter_input(INPUT_POST, 'newPassword') === 'on') {
        $password = filter_input(INPUT_POST, 'password');
        $user->setNewPassword($id, $password);
        $logs->addLogEntry('The password of user ' . $username . ' was successfully changed.');
    }
    if (filter_input(INPUT_POST, 'new_2fa') === 'on') {
        $user->overwriteTwofactor($id);
        $logs->addLogEntry('The 2fa of user ' . $username . ' was successfully deleted.');
    }
    if($username === $user->getUserDataById($id)['username']) {
        $user->updateUserData($id, $username, $email, ($twofactor_active === null) ? 0 : 1);
        $success_message = $alert->successAlert('Die Benutzerdaten wurden erfolgreich geändert!');
        $logs->addLogEntry('The user data of user ' . $username . ' were successfully changed.');
    }
    else {
        if($user->checkIfUsernameAlreadyExists($username)) {
           $success_message = $alert->dangerAlert('Dieser Benutzername existiert bereits!');
        }
        else {
            $user->updateUserData($id, $username, $email, ($twofactor_active === null) ? 0 : 1);
            $success_message = $alert->successAlert('Die Benutzerdaten wurden erfolgreich geändert!');
            $logs->addLogEntry('The user data of user ' . $username . ' were successfully changed.');
        }
    }
    $data = $user->getUserDataById($id);
}

include 'header.php';

$template = new Template('./assets/templates');
$template->setTemplate('editUser.twig');

$templateVars = [
    'success_message' => isset($success_message) ? $success_message : '',
    'php_self' => filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_SPECIAL_CHARS),
    'username' => $data['username'],
    'email' => $data['email'],
    'twofactor_activated' => ($data['2fa'] === 1) ? 'checked' : '',
    'userid' => isset($id_get) ? $id_get : $id
];

echo $template->render($templateVars);

include 'footer.php';