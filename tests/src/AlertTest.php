<?php
/**
 * TestClass for testing the Alert class.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   GymMel_Alumni
 * @author    Jan Harms <model_railroader@gmx-topmail.de>
 * @copyright 2023-2024 Gymnasium Melle
 * @license   https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @since     2024-01-03
 */

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