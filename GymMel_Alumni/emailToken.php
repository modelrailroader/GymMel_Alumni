<?php
/**
 * Alumnis that are requesting to change their date have to validate their identity through an email token
 * which can be entered in this form.
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

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dataHelper = new DataHelper();

// If emailToken.php is called without id parameter
if (!$id) {
    header('Location: index.php');
    exit();
} else {
    // if id parameter is located, check if alumni is verified and send them to changeData.php
    if ($dataHelper->checkIfAlumniIsLoggedInForDataChange($id)) {
        header('Location: changeData.php?id=' . $id);
    }
}

$alumniData = $dataHelper->getAlumniData($id);

include 'header.php';

$template = new Template('./assets/templates');
$template->setTemplate('emailToken.twig');

$templateVars = [
    'id' => $id,
    'anonymousMail' => $dataHelper->anonymizeEmailAddress($alumniData['email'])
];

echo $template->render($templateVars);

include 'footer.php';