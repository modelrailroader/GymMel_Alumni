<?php
/**
 * User control panel for every user to change the password, user data or enabling
 * twofactor-authentication.
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

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = new User();
if (!$user->authenticateWithSession()) {
    require 'accessDenied.php';
    exit();
}

$logs = new Logs();
$alert = new Alert();

$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = filter_input(INPUT_POST, 'password');
$twofactor = filter_input(INPUT_POST, '2fa');

if (!is_null($username) && !is_null($email) && !is_null($password)) {
    if ($user->getUsername() !== $username) {
        if ($user->checkIfUsernameAlreadyExists($username)) {
            $success_message = $alert->dangerAlert('Dieser Benutzername existiert bereits!');
        } 
        else {
            $user->updateUserData($user->getUserId(), $username, $email, ($twofactor === 'on') ? 1 : 0);
            $user->updateUserPassword($user->getUserId(), $password);
            $logs->addLogEntry('The user has changed his user data in the ucp.');
            $success_message = $alert->successAlert('Die Änderungen waren erfolgreich!');
        }
    } 
    else {
        $user->updateUserData($user->getUserId(), $username, $email, ($twofactor === 'on') ? 1 : 0);
        $user->updateUserPassword($user->getUserId(), $password);
        $logs->addLogEntry('The user has changed his user data in the ucp.');
        $success_message = $alert->successAlert('Die Änderungen waren erfolgreich!');
    }
    $user->getUserById($user->getUserId());
}

include 'header.php';

$template = new Template('./assets/templates');
$template->setTemplate('ucp.twig');

$templateVars = [
    'success_message' => isset($success_message) ? $success_message : '',
    'username' => $user->getUsername(),
    'email' => $user->getEmailAdress(),
    'twofactorEnabled' => $user->isTwofactorEnabled(),
    'twofactor_secret' => $user->getSecret(),
    'twofactor_qrcode' => $user->getQrCode(),
    'php_self' => filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_SPECIAL_CHARS)
];

echo $template->render($templateVars);

include 'footer.php';

