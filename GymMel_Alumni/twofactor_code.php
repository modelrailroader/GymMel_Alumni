<?php
/**
 * Frontend for entering a 2fa-token if enabled.
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

include_once(__DIR__.'/vendor/autoload.php');
include_once('autoload.php');

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

use src\User;
use src\Alert;
use src\Template;

$user = new User();
$alert = new Alert();

if ($user->authenticateWithSession()) {
    header('Location: index.php');
    exit();
}

$userid = filter_input(INPUT_GET, 'userid', FILTER_VALIDATE_INT);
$error = filter_input(INPUT_GET, 'error', FILTER_VALIDATE_INT);

if($error === 1) {
    $success_message = $alert->dangerAlert('Der Code war nicht korrekt!');
}

include 'header.php';

$template = new Template('./assets/templates');
$template->setTemplate('twofactor_code.twig');

$templateVars = [
    'success_message' => isset($success_message) ? $success_message : '',
    'userid' => $userid
];

echo $template->render($templateVars);

include 'footer.php';
