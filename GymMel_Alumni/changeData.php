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

$alumniData = $dataHelper->getAlumniData($id);

// Generate min and max birthdate
$minBirthDate = new DateTime('first day of january this year');
$minBirthDate->modify('-100 years');

$maxBirthDate = new DateTime('first day of january this year');
$maxBirthDate->modify('-16 years');

include 'header.php';

$template = new Template('./assets/templates');
$template->setTemplate('changeData.twig');

$templateVars = [
    'data' => $alumniData,
    'minBirthDate' => date('YYYY-m-d', $minBirthDate->getTimestamp()),
    'maxBirthDate' => date('YYYY-m-d', $maxBirthDate->getTimestamp())
];

echo $template->render($templateVars);

include 'footer.php';