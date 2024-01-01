<?php
include 'constants.php';
include_once('./vendor/autoload.php');
include_once('./src/User.php');
include_once('./src/Template.php');

use src\User;
use src\Template;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

$loader = new FilesystemLoader('./assets/templates');
$twig = new Environment($loader);

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = new User();
if(!$user->authenticateWithSession()) {
    require 'accessDenied.php';
    exit();
}
include 'header.php';

$template = new Template('./assets/templates');
$template->setTemplate('backup.twig');

$templateVars = [
    'success_message' => $success_message
];

echo $template->render($templateVars);

include 'footer.php';

