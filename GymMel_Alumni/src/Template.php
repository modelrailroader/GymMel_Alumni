<?php
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


