<?php
namespace src;

include(dirname(__DIR__, 2) . '/GymMel_Alumni/autoload.php');

use PHPUnit\Framework\TestCase;
use src\Alert;

class AlertTest extends TestCase {
    public function testDangerAlert()
    {
        $alert = new Alert();
        $message = "This is a danger alert";
        $expected = '<div class="alert alert-danger" role="alert">This is a danger alert</div>';
        $this->assertEquals($expected, $alert->dangerAlert($message));
    }

    public function testSuccessAlert()
    {
        $alert = new Alert();
        $message = "This is a success alert";
        $expected = '<div class="alert alert-success" role="alert">This is a success alert</div>';
        $this->assertEquals($expected, $alert->successAlert($message));
    }
}