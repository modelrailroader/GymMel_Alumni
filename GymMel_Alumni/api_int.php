<?php
/**
 * Internal API for handling all internal user post actions.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   GymMel_Alumni
 * @author    Jan Harms <model_railroader@gmx-topmail.de>
 * @copyright 2023-2024 Gymnasium Melle
 * @license   https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @since     2024-02-23
 */

include 'constants.php';
include_once(__DIR__ . '/vendor/autoload.php');
include_once('autoload.php');

use src\User;
use src\Logs;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = new User();
if (!$user->authenticateWithSession()) {
    require 'accessDenied.php';
    exit();
}

$logs = new Logs();

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
$postData = json_decode(file_get_contents('php://input'));

switch ($action) {
    case 'createUser':
        $username = filter_var($postData->username, FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_var($postData->email, FILTER_VALIDATE_EMAIL);
        $password = filter_var($postData->password, FILTER_UNSAFE_RAW);

        if (!is_null($username) && !is_null($password) && !is_null($email)) {
            if ($stored = $user->createUser($username, $password, $email)) {
                $message = 'Der Benutzer wurde erfolgreich erstellt!';
            } else {
                $message = 'Dieser Benutzername existiert bereits!';
            }
            $logs->addLogEntry('The user ' . $username . ' was successfully created.');
            $response = [
                'stored' => $stored,
                'message' => $message
            ];
        }
        break;
    default:
        $response = [
            'message' => 'This is the internal API of GymMel_Alumni. This request was not successful. Please check your request and try again.'
        ];
        break;
    case 'deleteUser':
        $userid = filter_var($postData->userid, FILTER_VALIDATE_INT);
        $username_deleted = filter_var($postData->username_deleted, FILTER_SANITIZE_SPECIAL_CHARS);

        if (!is_null($userid)) {
            $user->deleteUser($userid);
            $message = 'Der Benutzer ' . $username_deleted . ' wurde erfolgreich gelöscht!';
            $logs->addLogEntry('The user ' . $username_deleted . ' was successfully deleted.');

            $response = [
                'deleted' => true,
                'message' => $message
            ];
        } else {
            $response = [
                'deleted' => false,
                'message' => "The request didn't contain a userid."
            ];
        }
        break;
}

header('Content-Type: application/json');
echo json_encode($response);
