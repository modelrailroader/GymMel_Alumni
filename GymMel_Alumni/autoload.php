<?php
spl_autoload_register(function ($className) {
    $classPath = str_replace('\\', '/', $className) . '.php';
    include_once $classPath;
});