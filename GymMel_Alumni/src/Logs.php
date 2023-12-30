<?php
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


