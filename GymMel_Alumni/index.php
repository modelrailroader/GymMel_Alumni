<?php
/**
 * Main page. New members of the network are able to apply for the network.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   GymMel_Alumni
 * @author    Jan Harms <model_railroader@gmx-topmail.de>
 * @copyright 2023-2026 Gymnasium Melle
 * @license   https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @since     2023-09-24
 */

include 'constants.php';
include_once(__DIR__.'/vendor/autoload.php');
include_once('autoload.php');

use A1phanumeric\DBPDO;
use src\User;
use src\Logs;
use src\Alert;
use src\DataHelper;
use src\Template;

$logs = new Logs();
$alert = new Alert();
$dataHelper = new DataHelper();

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dbclient = new DBPDO($db_host, $db_name, $db_user, $db_password);
$return = null;

$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
$password = filter_input(INPUT_POST, 'password');
$redirectPage = filter_input(INPUT_POST, 'redirect-page', FILTER_SANITIZE_SPECIAL_CHARS);

$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
$userid = filter_input(INPUT_POST, 'userid', FILTER_VALIDATE_INT);
$twofactor_code = filter_input(INPUT_POST, 'twofactor_code', FILTER_VALIDATE_INT);

$user = new User();
$user->authenticateWithSession();

// If login-form-data is submitted
if(isset($username) && isset($password)) {
    // try to login
    if(($user->authenticate($username, $password))) {
        if($user->isTwofactorEnabled()) {
            header('Location: twofactor_code.php?userid=' . $user->getUserId());
            exit();
        }
        else {
            $logs->addLogEntry('The user was logged in.');
            $logs->updateLastLogin($_SESSION['userid']);
            if ($redirectPage !== '') {
                header('Location: ' . $redirectPage);
                exit();
            }
        }
    }
    else {
        $success_message = $alert->dangerAlert('Der eingegebene Benutzername oder das Passwort ist falsch! Sollten Sie sich bereits 10 Mal erfolglos angemeldet haben, ist der Login gesperrt. '
                . 'In diesem Fall fordern Sie bitte ein neues Passwort über <a href="forgetPassword.php">Passwort vergessen</a> an.');
        include 'login.php';
        exit();
          
    }
}

// If twofactor code is submitted
if($action === 'twofactor') {
    if(!$user->validateTwofactorCode($userid, $twofactor_code)) {
        $success_message = $alert->dangerAlert('Der Code war nicht korrekt!');
        header('Location: twofactor_code.php?userid=' . $userid . '&error=1');
        exit();
    }
}

// Generate min and max birthdate
$minBirthDate = new DateTime('first day of january this year');
$minBirthDate->modify('-100 years');

$maxBirthDate = new DateTime('first day of january this year');
$maxBirthDate->modify('-16 years');

include 'header.php';

$template = new Template('./assets/templates');
$template->setTemplate('index.twig');

$templateVars = [
    'success_message' => isset($success_message) ? $success_message : '',
    'php_self' => filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_SPECIAL_CHARS),
    'minBirthDate' => date('Y-m-d', $minBirthDate->getTimestamp()),
    'maxBirthDate' => date('Y-m-d', $maxBirthDate->getTimestamp())
];

echo $template->render($templateVars);

include 'footer.php';
