<?php
/**
 * Backup backend.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   GymMel_Alumni
 * @author    Jan Harms <model_railroader@gmx-topmail.de>
 * @copyright 2023-2025 Gymnasium Melle
 * @license   https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @since     2023-09-24
 */

include 'constants.php';
include_once('./vendor/autoload.php');
include_once('autoload.php');

use src\User;
use src\Template;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

$loader = new FilesystemLoader('./assets/templates');
$twig = new Environment($loader);

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = new User();
if(!$user->authenticateWithSession()) {
    require 'accessDenied.php';
    exit();
}
include 'header.php';

$template = new Template('./assets/templates');
$template->setTemplate('backup.twig');

$templateVars = [
    'success_message' => isset($success_message) ? $success_message : ''
];

echo $template->render($templateVars);

include 'footer.php';

