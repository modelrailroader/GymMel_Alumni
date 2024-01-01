<?php
include 'constants.php';
include_once(__DIR__.'/vendor/autoload.php');
include_once(__DIR__.'/src/User.php');
include_once(__DIR__.'/src/Template.php');

use src\User;
use src\Template;

if(!isset($user)) {
    $user = new User();
}

$template = new Template('./assets/templates');
$template->setTemplate('header.twig');

$templateVars = [
    'userIsLoggedIn' => $user->isLoggedIn(),
    'username' => $user->getUsername()
];

echo $template->render($templateVars);
