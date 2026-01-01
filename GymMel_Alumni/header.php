<?php
/**
 * Main header for frontend/backend. This file is included in all webpages.
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
include_once(__DIR__ . '/vendor/autoload.php');
include_once('autoload.php');

use src\User;
use src\Template;

if (!isset($user)) {
    $user = new User();
}

$template = new Template('./assets/templates');
$template->setTemplate('header.twig');

$templateVars = [
    'userIsLoggedIn' => $user->isLoggedIn(),
    'username' => ($user->authenticateWithSession()) ? $user->getUsername() : ''
];

echo $template->render($templateVars);
