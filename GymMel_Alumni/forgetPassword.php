<?php
/**
 * Forget Password page.
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

use src\User;
use src\Alert;
use src\Logs;
use src\Template;

$user = new User();
$alert = new Alert();
$logs = new Logs();

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
if($user->authenticateWithSession()) {
    require 'index.php';
    exit();
}

$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);

if(!is_null($username)) {
    if($user->sendNewPassword($username)) {
        $success_message = $alert->successAlert('Das Passwort wurde erfolgreich verschickt. Prüfe auch deinen Spam-Ordner!');
    }
    else {
        $success_message = $alert->dangerAlert('Das Versenden des neuen Passworts ist fehlgeschlagen. Probieren Sie es später erneut oder wenden Sie sich an den Administrator.');
    }
}

include 'header.php';

$template = new Template('./assets/templates');
$template->setTemplate('forgetPassword.twig');

$templateVars = [
    'php_self' => filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_SPECIAL_CHARS),
    'success_message' => isset($success_message) ? $success_message : ''
];

echo $template->render($templateVars);

include 'footer.php';
