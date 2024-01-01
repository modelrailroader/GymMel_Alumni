<?php
include_once(__DIR__.'/src/Template.php');

use src\Template;

$template = new Template('./assets/templates');
$template->setTemplate('footer.twig');

echo $template->render([]);
