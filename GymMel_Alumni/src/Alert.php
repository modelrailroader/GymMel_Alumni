<?php
namespace src;

class Alert 
{
    public function dangerAlert(string $message) {
        $alert = sprintf('<div class="alert alert-danger" role="alert">%s</div>',
                $message);
        return $alert;
    }
    
    public function successAlert(string $message) {
        $alert = sprintf('<div class="alert alert-success" role="alert">%s</div>',
                $message);
        return $alert;
    }
}

