<?php
/**
 * Logs class for logging admin actions.
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at https://mozilla.org/MPL/2.0/.
 *
 * @package   GymMel_Alumni
 * @author    Jan Harms <model_railroader@gmx-topmail.de>
 * @copyright 2023-2024 Gymnasium Melle
 * @license   https://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @since     2023-09-24
 */

namespace src;
include_once('User.php');

use A1phanumeric\DBPDO;
use src\User;

class Logs 
{
    private string $path;
    
    private DBPDO $dbclient;
    
    private User $user;
    
    public function __construct() {
        $this->path = dirname(__DIR__, 1) . '/data/adminlog.log';
        if (!file_exists($this->path)) {
            $handle = fopen($this->path, 'w');
            if ($handle) {
                fclose($handle);
            }
        }
        include dirname(__DIR__, 1) . '/constants.php';
        $this->dbclient = new DBPDO($db_host, $db_name, $db_user, $db_password);
        $this->user = new User();
    }
    
    // Adds a log entry to the adminlog.log-file of a given event.
    public function addLogEntry(string $message): bool 
    {
        $file = fopen($this->path, 'a+');
        if(!$file) {
            return false;
        }
        
        $log = sprintf('[%s], %s, %s, UserId: %d, User: %s',
                date('d.m.Y - H:i:s', time()),
                $_SERVER['REMOTE_ADDR'],
                $message,
                $_SESSION['userid'],
                $this->user->getUserDataById($_SESSION['userid'])['username']);
        
        if(!fwrite($file, $log)) {
            return false;
        }
        else {
            fwrite($file, "\n");
            fclose($file);
            return true;
        }
    }
    
    // Updates the last_login-entry in the database for a given user.
    public function updateLastLogin(int $userid): bool
    {
        $query = sprintf('UPDATE `alumni_users` SET `last_login` = %d WHERE `userid` = %d',
                time(),
                $userid);
        $this->dbclient->execute($query);
        return true;
    }
}


