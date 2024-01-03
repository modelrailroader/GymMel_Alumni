<?php
/**
 * Backend for creating a new user.
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

$logs = new Logs();
$alert = new Alert();

$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = filter_input(INPUT_POST, 'password');
$twofactor = filter_input(INPUT_POST, '2fa');

if(!is_null($username) && !is_null($password) && !is_null($email)) {
    if($user->createUser($username, $password, $email, ($twofactor === 'on') ? 1 : 0)) {
        $success_message = $alert->successAlert('Der Benutzer wurde erfolgreich erstellt!');
    }
    else {
        $success_message = $alert->dangerAlert('Dieser Benutzername existiert bereits!');
    }
    $logs->addLogEntry('The user ' . $username . ' was successfully created.');
}

include 'header.php';

$template = new Template('./assets/templates');
$template->setTemplate('createUser.twig');

$templateVars = [
    'php_self' => filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_SPECIAL_CHARS),
    'success_message' => isset($success_message) ? $success_message : ''
];

echo $template->render($templateVars);

include 'footer.php';

