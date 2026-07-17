<?php
/**
 * External API for handling all public user post actions.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   GymMel_Alumni
 * @author    Jan Harms <model_railroader@gmx-topmail.de>
 * @copyright 2026 Gymnasium Melle
 * @license   https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @since     2026-07-15
 */

include 'constants.php';
include_once(__DIR__ . '/vendor/autoload.php');
include_once('autoload.php');

use src\DataHelper;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dataHelper = new DataHelper();

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
$postData = json_decode(file_get_contents('php://input'));

$response = [];

switch ($action) {
    case 'editAlumni':
        $name = filter_var($postData->name, FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_var($postData->email, FILTER_VALIDATE_EMAIL);
        $birthday = filter_var($postData->birthday, FILTER_SANITIZE_SPECIAL_CHARS);
        $graduation_year = filter_var($postData->graduation_year, FILTER_VALIDATE_INT);
        $studies = filter_var($postData->studies, FILTER_SANITIZE_SPECIAL_CHARS);
        $job = filter_var($postData->job, FILTER_SANITIZE_SPECIAL_CHARS);
        $company = filter_var($postData->company, FILTER_SANITIZE_SPECIAL_CHARS);
        $transfer_privacy = filter_var($postData->transfer_privacy, FILTER_VALIDATE_BOOL);
        $id = filter_var($postData->id, FILTER_VALIDATE_INT);

        if ($dataHelper->checkIfAlumniIsLoggedInForDataChange($id)) {
            $data_change = array(
                'id' => $id,
                'name' => $name,
                'email' => $email,
                'birthday' => $birthday,
                'graduation_year' => $graduation_year,
                'studies' => $studies,
                'job' => $job,
                'company' => $company,
                'transfer_privacy' => ($transfer_privacy === true) ? 1 : 0
            );
            if ($stored = $dataHelper->updateData($data_change)) {
                $message = "Deine Daten wurden erfolgreich geändert.";
            } else {
                $message = 'Es ist ein Fehler aufgetreten. Bitte versuche es später erneut.';
            }
        } else {
            $stored = false;
            $message = 'Der Alumni konnte nicht verifiziert werden.';
        }

        $response = [
            'stored' => $stored,
            'message' => $message
        ];
        break;
    case 'emailToken':
        $id = filter_var($postData->id, FILTER_VALIDATE_INT);
        $code = filter_var($postData->code, FILTER_SANITIZE_SPECIAL_CHARS);

        if ($dataHelper->verifyEmailToken($id, $code)) {
            $success = true;
            $message = '';
        } else {
            $success = false;
            $message = 'Der eingegebene Code war nicht korrekt.';
        }

        $response = [
            'success' => $success,
            'message' => $message
        ];
        break;
    case 'resendToken':
        $id = filter_var($postData->id, FILTER_VALIDATE_INT);

        if (isset($_SESSION['tokenCreated'])) {
            // Resend token only after 1 minute (60s)
            if (time() - $_SESSION['tokenCreated'] > 60) {
                $success = $dataHelper->requestEmailTokenForDataChange($id);
            }
        }

        $response = [
            'success' => $success ?? false,
            'message' => !$success ? 'Es ist ein Fehler aufgetreten. Bitte versuche es später erneut.' : ''
        ];

        break;
    case 'requestData':
        $email = filter_var($postData->email, FILTER_VALIDATE_EMAIL);

        $id = $dataHelper->getIdByEmail($email);

        if (!$id) {
            $success = false;
        } else {
            $success = $dataHelper->requestEmailTokenForDataChange($id);
        }

        $response = [
            'success' => $success,
            'message' => !$success ? 'Deine E-Mail-Adresse befindet sich nicht in unserer Datenbank.' : '',
            'id' => $id
        ];
        break;
    case 'deleteAlumni':
        $id = filter_var($postData->id, FILTER_VALIDATE_INT);

        if ($dataHelper->checkIfAlumniIsLoggedInForDataChange($id)) {
            $success = $dataHelper->deleteAlumniById($id);

            $response = [
                'success' => $success,
                'message' => $success ? 'Die Daten wurden erfolgreich gelöscht.' : 'Es ist ein Fehler aufgetreten. Bitte versuche es später erneut.'
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Der Alumni konnte nicht verifiziert werden.'
            ];
        }
        break;
    case 'addAlumni':
        $name = filter_var($postData->name, FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_var($postData->email, FILTER_VALIDATE_EMAIL);
        $birthday = filter_var($postData->birthday, FILTER_SANITIZE_SPECIAL_CHARS);
        $graduation_year = filter_var($postData->graduation_year, FILTER_VALIDATE_INT);
        $studies = filter_var($postData->studies, FILTER_SANITIZE_SPECIAL_CHARS);
        $job = filter_var($postData->job, FILTER_SANITIZE_SPECIAL_CHARS);
        $company = filter_var($postData->company, FILTER_SANITIZE_SPECIAL_CHARS);
        $transfer = filter_var($postData->transfer_privacy, FILTER_VALIDATE_BOOL);

        if ($dataHelper->checkIfEmailExists($email)) {
            $response = [
                'stored' => false,
                'message' => 'Deine E-Mail-Adresse ist bereits in unserer Datenbank registriert. Möchtest du stattdessen
                deine Daten ändern? Dann klicke <a href="changeData.php?email=' . $email . '">hier</a>.'
            ];
            break;
        }

        $success = $dataHelper->saveNewAlumni($name, $email, $birthday, $graduation_year, $studies, $job, $company, $transfer);

        $response = [
            'stored' => $success,
            'message' => $success ? 'Danke fürs Eingeben deiner Daten!' : 'Es ist ein Fehler aufgetreten. Bitte versuche es später erneut.'
        ];
        break;
}

echo json_encode($response);