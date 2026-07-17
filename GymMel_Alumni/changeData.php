<?php
/**
 * A form for changing the alumni data in the frontend
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
use src\Template;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$dataHelper = new DataHelper();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!is_null($id)) {
    if ($dataHelper->checkIfAlumniIsLoggedInForDataChange($id)) {
        $alumniData = $dataHelper->getAlumniData($id);
        $alumniVerified = true;
    } else {
        // Send user to emailToken.php if they are not already verified
        $dataHelper->requestEmailTokenForDataChange($id);
        header('Location: emailToken.php?id=' . $id);
        exit();
    }
} else {
    $alumniVerified = $dataHelper->checkIfAlumniIsLoggedInForDataChange();
}

// Generate min and max birthdate
$minBirthDate = new DateTime('first day of january this year');
$minBirthDate->modify('-100 years');

$maxBirthDate = new DateTime('first day of january this year');
$maxBirthDate->modify('-16 years');

include 'header.php';

$template = new Template('./assets/templates');
$template->setTemplate('changeData.twig');

$templateVars = [
    'data' => $alumniData ?? null,
    'minBirthDate' => date('YYYY-m-d', $minBirthDate->getTimestamp()),
    'maxBirthDate' => date('YYYY-m-d', $maxBirthDate->getTimestamp()),
    'alumniVerified' => $alumniVerified
];

echo $template->render($templateVars);

include 'footer.php';