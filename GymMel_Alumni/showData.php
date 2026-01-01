<?php
/**
 * Backend for showing an overview of all network members.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   GymMel_Alumni
 * @author    Jan Harms <model_railroader@gmx-topmail.de>
 * @copyright 2023-2026 Gymnasium Melle
 * @license   https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @since     2023-09-24
 */

include 'constants.php';
include_once(__DIR__.'/vendor/autoload.php');
include_once('autoload.php');

use src\User;
use src\DataHelper;
use src\Template;

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = new User();
if(!$user->authenticateWithSession()) {
    require 'accessDenied.php';
    exit();
}

$dataHelper = new DataHelper();
$data = $dataHelper->getAllAlumniData();

// Handle Duplicates
$duplicates = $dataHelper->findDuplicates();
$pages = [];
foreach ($duplicates as $duplicate) {
    $pages[] = $dataHelper->getDuplicateDetails($duplicate);
}

include 'header.php';

$template = new Template('./assets/templates');
$template->setTemplate('showData.twig');

$templateVars = [
    'data' => $data,
    'pages' => $pages,
    'number_of_pages' => count($pages),
    'duplicatesFound' => empty($pages) ? 'disabled' : ''
];

echo $template->render($templateVars);

include 'footer.php';