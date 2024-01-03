<?php
/**
 * Template class for handling template rendering with Twig.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   GymMel_Alumni
 * @author    Jan Harms <model_railroader@gmx-topmail.de>
 * @copyright 2023-2024 Gymnasium Melle
 * @license   https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @since     2024-01-02
 */

namespace src;

use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;


class Template 
{    
    private FilesystemLoader $loader;
    
    private Environment $twig;
    
    private string $template;
    
    public function __construct(string $templatePath)
    {
        $this->loader = new FilesystemLoader($templatePath);
        $this->twig = new Environment($this->loader);
    }
    
    public function setTemplate(string $templateName): bool
    {
        $this->template = $templateName;
        return true;
    }
    
    public function render(array $templateVars): string
    {
        return $this->twig->render($this->template, $templateVars);
    }
}


