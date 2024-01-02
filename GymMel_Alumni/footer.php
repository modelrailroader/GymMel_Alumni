<?php
include_once('autoload.php');

use src\Template;

$template = new Template('./assets/templates');
$template->setTemplate('footer.twig');

echo $template->render([]);
