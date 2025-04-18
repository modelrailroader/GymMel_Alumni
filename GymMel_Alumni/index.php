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
 * @copyright 2023-2025 Gymnasium Melle
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

$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$studies = filter_input(INPUT_POST, 'studies', FILTER_SANITIZE_SPECIAL_CHARS);
$job = filter_input(INPUT_POST, 'job', FILTER_SANITIZE_SPECIAL_CHARS);
$company = filter_input(INPUT_POST, 'company', FILTER_SANITIZE_SPECIAL_CHARS);
$privacy_checkbox = filter_input(INPUT_POST, 'data-privacy');
$transfer_checkbox = filter_input(INPUT_POST, 'transfer-privacy');
$submit = filter_input(INPUT_POST, 'submit');

$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
$password = filter_input(INPUT_POST, 'password');

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

// If alumni-form-data is submitted
if(isset($submit) && isset($privacy_checkbox)) {
    if(isset($transfer_checkbox)) {
        $transfer = 1;
    }
    else {
        $transfer = 0;
    }
    $dataHelper->saveNewAlumni($name, $email, $studies, $job, $company, $transfer);

    // Ability to switch easily between to possible success alerts
    //$success_message = $alert->successAlert('Danke fürs Eingeben deiner Daten!<br>Hast du schon unseren Image-Film für die Ehemaligen-Party gesehen? Nein? Dann <a href="https://www.melle-gymnasium.de/Schulfilm">hier</a> entlang!');
    $success_message = $alert->successAlert('Danke fürs Eingeben deiner Daten!');
}

include 'header.php';

$template = new Template('./assets/templates');
$template->setTemplate('index.twig');

$templateVars = [
    'success_message' => isset($success_message) ? $success_message : '',
    'php_self' => filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_SPECIAL_CHARS),
];

echo $template->render($templateVars);

include 'footer.php';
