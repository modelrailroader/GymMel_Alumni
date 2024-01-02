<?php
include 'constants.php';
include_once(__DIR__.'/vendor/autoload.php');
include_once(__DIR__.'/src/User.php');
include_once(__DIR__.'/src/Alert.php');
include_once(__DIR__.'/src/Logs.php');
include_once(__DIR__.'/src/Template.php');

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
