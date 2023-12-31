<?php
/**
 * Backend for editing a network data item.
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

include 'constants.php';
include_once(__DIR__ . '/vendor/autoload.php');
include_once('autoload.php');

use src\User;
use src\DataHelper;
use src\Logs;
use src\Template;
use src\Alert;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = new User();
if (!$user->authenticateWithSession()) {
    require 'accessDenied.php';
    exit();
}

$dataHelper = new DataHelper();
$logs = new Logs();
$alert = new Alert();

$dataid = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS);
$studies = filter_input(INPUT_POST, 'studies', FILTER_SANITIZE_SPECIAL_CHARS);
$job = filter_input(INPUT_POST, 'job', FILTER_SANITIZE_SPECIAL_CHARS);
$company = filter_input(INPUT_POST, 'company', FILTER_SANITIZE_SPECIAL_CHARS);
$transfer_privacy = filter_input(INPUT_POST, 'transfer_privacy');
$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$submit = filter_input(INPUT_POST, 'submit');

if(!is_null($dataid)) {
    $data = $dataHelper->getAlumniData($dataid);
}

if(isset($submit)) {
    $data_change = array(
        'id' => $id,
        'name' => $name,
        'email' => $email,
        'studies' => $studies,
        'job' => $job,
        'company' => $company,
        'transfer_privacy' => ($transfer_privacy === 'on') ? 1 : 0
    );
    if($dataHelper->updateData($data_change)) {
        $success_message = $alert->successAlert('Die Daten wurden erfolgreich geändert!');
        $logs->addLogEntry('The data of the alumni ' . $name . ' was successfully changed.');
    }
    $data = $dataHelper->getAlumniData($id);
    $dataid = $id;
}

if(is_null($submit) & is_null($dataid)) {
    require 'showData.php';
    exit();
}

include 'header.php';

$template = new Template('./assets/templates');
$template->setTemplate('editData.twig');

$templateVars = [
    'php_self' => filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_SPECIAL_CHARS),
    'data' => $data,
    'dataid' => $dataid,
    'success_message' => isset($success_message) ? $success_message : ''
];

echo $template->render($templateVars);

include 'footer.php';



