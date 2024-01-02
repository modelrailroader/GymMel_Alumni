<?php
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
    'success_message' => isset($success_message) ? $success_message : ''
];

echo $template->render($templateVars);

include 'footer.php';
