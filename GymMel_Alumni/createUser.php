<?php
include 'constants.php';
include_once(__DIR__.'/vendor/autoload.php');
include_once(__DIR__.'/src/User.php');
include_once(__DIR__.'/src/DataHelper.php');
include_once(__DIR__.'/src/Alert.php');
include_once(__DIR__.'/src/Logs.php');
include_once(__DIR__.'/src/Template.php');

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
    'success_message' => $success_message
];

echo $template->render($templateVars);

include 'footer.php';

