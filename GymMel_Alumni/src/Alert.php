<?php
/**
 * Alert class to create Bootstrap-alerts.
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

