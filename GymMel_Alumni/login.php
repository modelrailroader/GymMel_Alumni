<?php
include 'constants.php';
include_once(__DIR__ . '/vendor/autoload.php');
include_once('autoload.php');

use src\User;
use src\Template;

$user = new User();
if(session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($user->authenticateWithSession()) {
    header('Location: index.php');
    exit();
}

include 'header.php';

$template = new Template('./assets/templates');
$template->setTemplate('login.twig');

$templateVars = [
    'success_message' => isset($success_message) ? $success_message : ''
];

echo $template->render($templateVars);

include 'footer.php';
