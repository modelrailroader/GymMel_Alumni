<?php
namespace src;

use A1phanumeric\DBPDO;
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Algorithm;

class User 
{
    private DBPDO $dbclient;
    
    private TwoFactorAuth $twofactor;
    
    public bool $loggedIn = false;
    
    private string $username;
    
    private string $email;
    
    private int $userid;
    
    private bool $twofactorEnabled;
    
    private string $secret;
    
    public function __construct() 
    {
        include dirname(__DIR__, 1) . '/constants.php';
        $this->dbclient = new DBPDO($db_host, $db_name, $db_user, $db_password);
        $this->twofactor = new TwoFactorAuth(null, 6, 30, Algorithm::Sha1);
    }
    
    // Provides the data of a special user to be used from another function.
    public function getUserById(int $id): bool
    {
        $query = sprintf('SELECT * FROM alumni_users WHERE `userid`="%d"',
                $id);
        
        if (!is_null($response = $this->dbclient->fetchAll($query))) {
            $this->username = (string) $response[0]['username'];
            $this->email = (string) $response[0]['email'];
            $this->userid = $id;
            $this->twofactorEnabled = (bool) $response[0]['2fa'];
            $this->secret = (string) $response[0]['secret'];
            return true;
        }
        else {
            return false;
        }
    }
    
    // Provides the data of a special user to be used from another function.
    public function getUserByUsername(string $username): bool
    {
        $query = sprintf('SELECT * FROM alumni_users WHERE `username`="%s"',
                $username);
        
        if (!is_null($response = $this->dbclient->fetchAll($query))) {
            $this->userid = (string) $response[0]['userid'];
            $this->email = (string) $response[0]['email'];
            $this->username = $username;
            $this->twofactorEnabled = (bool) $response[0]['2fa'];
            $this->secret = (string) $response[0]['secret'];
            return true;
        }
        else {
            return false;
        }
    }
    
    public function isLoggedIn(): bool
    {
        return $this->loggedIn;
    }
    
    public function getUsername(): string
    {
        return $this->username;
    }
    
    public function getEmailAdress(): string
    {
        return $this->email;
    }
    
    public function getUserId(): int
    {
        return $this->userid;
    }
    
    public function isTwofactorEnabled(): bool 
    {
        return $this->twofactorEnabled;
    }
    
    public function setLoggedIn(bool $status): void 
    {
        $this->loggedIn = $status;
    }
    
    // Checks user credentials.
    public function authenticate(string $username, string $password): bool
    {
        $query = sprintf('SELECT * FROM alumni_users WHERE `username`="%s" OR `email`="%s"',
            $username,
            $username);
        
        if (!empty($response = $this->dbclient->fetchAll($query))) {
            if($this->checkLoginTries($username)) {
                return false;
            }
            if (isset($username) && password_verify($password, $response[0]['password'])) {
                if(password_needs_rehash($response[0]['password'], PASSWORD_DEFAULT)) {
                    $newPassword = password_hash($password, PASSWORD_DEFAULT);
                    $this->setNewPassword($response[0]['userid'], $newPassword);
                }
                
                if($this->checkIfTwofactorEnabled($response[0]['userid'])) {
                    $this->getUserById($response[0]['userid']);
                    return true;
                }
                else {
                    $this->updateLoginTries($username, true);
                    $_SESSION['userid'] = $response[0]['userid'];
                    $session_id = $this->generateSessionId();
                    $_SESSION['session_id'] = $session_id;
                    $this->saveSessionId($response[0]['userid'], $session_id);
                    $this->setLoggedIn(true);
                    $this->getUserById($response[0]['userid']);
                    return true;
                }
            } 
            else {
                $this->updateLoginTries($username);
                return false;
            }
        }
        else {
            return false;
        }
    }
    
    // Checks if a user tries to login for the tenth time.
    private function checkLoginTries(string $username): bool
    {
        if(!$this->checkIfUsernameAlreadyExists($username)) {
            return false;
        }
        else {
            $query = sprintf("SELECT `login_tries` FROM `alumni_users` WHERE `username` = '%s'",
                    $username);
            $result = $this->dbclient->fetch($query);
            if((int) $result['login_tries'] > 10) {
                return true;
            }
            else {
                return false;
            }
        }
    }
    
    // Updates login tries for a special user; set tries to null if passwordCorrect is true.
    private function updateLoginTries(string $username, bool $passwordCorrect = false): bool
    {
        if(!$this->checkIfUsernameAlreadyExists($username)) {
            return false;
        }
        else {
            $query = sprintf("SELECT `login_tries` FROM `alumni_users` WHERE `username` = '%s'",
                    $username);
            $result = $this->dbclient->fetch($query);
            if($passwordCorrect) {
                $login_tries = 0;
            }
            else {
                $login_tries = (int) $result['login_tries'] + 1;
            }
            $newQuery = sprintf("UPDATE `alumni_users` SET `login_tries` = %d WHERE `username`= '%s'",
                    $login_tries,
                    $username);
            $this->dbclient->execute($newQuery);
            return true;
        }
    }
    
    // Checks the code of 2fa for a special user and loggs in if the code is correct.
    public function validateTwofactorCode(int $userid, string $code): bool 
    {
        if($this->twofactor->verifyCode($this->getSecret($userid), $code)) {
            $query = sprintf('SELECT * FROM alumni_users WHERE `userid`="%s"',
                $userid);
            $response = $this->dbclient->fetchAll($query);
            $this->updateLoginTries($response[0]['username'], true);
            $_SESSION['userid'] = $response[0]['userid'];
            $session_id = $this->generateSessionId();
            $_SESSION['session_id'] = $session_id;
            $this->saveSessionId($response[0]['userid'], $session_id);
            $this->setLoggedIn(true);
            $this->getUserById($response[0]['userid']);
            return true;
        }
        else {
            return false;
        }
    }
    
    // Authenticates a user with the use of session information.
    public function authenticateWithSession(): bool
    {
        if(isset($_SESSION['userid']) && isset($_SESSION['session_id'])) {
            if(($session_id = $this->getSessionId($_SESSION['userid'])) === false) {
                return false;
            }
            else {
                if($_SESSION['session_id'] === $session_id) {
                    $this->setLoggedIn(true);
                    return $this->getUserById($_SESSION['userid']);
                }
                else {
                    return false;
                }
            }
        }
        else {
            return false;
        }
    }
    
    // Returns all Users.
    public function getAllUsers(): array 
    {
        $query = 'SELECT `username`, `email`, `2fa` AS `twofactor`, `userid`, `last_login` FROM `alumni_users`';
        return $this->dbclient->fetchAll($query);
    }

    //Returns the user data as an array for a special user.
    public function getUserDataById(int $id): array 
    {
        $query = 'SELECT `username`, `email`, `2fa`, `last_login`, `password`, `secret` FROM `alumni_users` WHERE `userid` = ' . $id;
        return $this->dbclient->fetch($query);
    }
    
    //Creates a new user.
    public function createUser(string $username, string $password, string $email, string|int $twofactor): bool
    {
        if($this->checkIfUsernameAlreadyExists($username)) {
            return false;
        }
        $query = sprintf("INSERT INTO alumni_users (username, password, email, secret, 2fa) VALUES ('%s', '%s', '%s', '%s', %d)",
                $username,
                password_hash($password, PASSWORD_DEFAULT),
                $email,
                $this->twofactor->createSecret(),
                ($twofactor === 'on') ? 1 : 0);
        $this->dbclient->execute($query);
        return true;
    }
    
    //Deletes a special user identified by the userid.
    public function deleteUser($id): bool
    {
        $query = sprintf('DELETE FROM `alumni_users` WHERE `userid` = %d',
                $id);
        $this->dbclient->execute($query);
        return true;
    }
    
    // Crypts a given password.
    private function cryptPassword(string $password): string 
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    // Sets a new password for a special user and saves it to the database.
    public function setNewPassword(int $id, string $password) : bool
    {
        $query = sprintf("UPDATE `alumni_users` SET `password` = '%s' WHERE `userid` = %d",
                $this->cryptPassword($password),
                $id);
        $this->dbclient->execute($query);
        return true;
    }
    
    // Deletes the 2fa-credentials for a special user.
    public function overwriteTwofactor(int $id): bool 
    {
        $query = sprintf("UPDATE `alumni_users` SET `2fa` = 0, `secret` = '%s' WHERE `userid` = %d",
                $this->twofactor->createSecret(),
                $id);
        $this->dbclient->execute($query);
        return true;
    }
    
    // Updates the user data for a special user.
    public function updateUserData(int $id, string $username, string $email, int $twofactor): bool
    {
        $query = sprintf("UPDATE `alumni_users` SET `username` = '%s', `email` = '%s', `2fa` = %d WHERE `userid` = %d",
                $username,
                $email,
                $twofactor,
                $id);
        $this->dbclient->execute($query);
        return true;
    }
    
    // Updates the password of a special user.
    public function updateUserPassword(int $id, string $password): bool 
    {
        $query = sprintf("UPDATE `alumni_users` SET `password` = '%s' WHERE `userid` = %d",
                password_hash($password, PASSWORD_DEFAULT),
                $id);
        $this->dbclient->execute($query);
        return true;
    }
    
    // Returns the 2fa-secret for a special user. 
    public function getSecret(int $userid = null): string
    {
        if(is_null($userid)) {
            return $this->secret;
        }
        else {
            $query = sprintf("SELECT `secret` FROM `alumni_users` WHERE `userid` = %d",
                    $userid);
            return $this->dbclient->fetch($query)['secret'];
        }
    }
    
    // Returns the DataUri of the QR-Code which is used to transmit the 2fa-secret of the user to a Authenticator-App.
    public function getQrCode(): string 
    {
        $label = 'Alumni-Netzwerk Gymnasium Melle:' . $this->getEmailAdress();
        return (string) $this->twofactor->getQRCodeImageAsDataUri($label, $this->getSecret());
    }
    
    // Returns true if a username already exists in the database.
    public function checkIfUsernameAlreadyExists(string $username): bool 
    {
        $query = 'SELECT username FROM alumni_users';
        $response = $this->dbclient->fetchAll($query);
        foreach($response as $item) {
            if($username === $item['username']) {
                return true;
            }
        }
        return false;
    }
    
    // Logout the current user.
    public function logout(): bool 
    {
        if(($userid = $_SESSION['userid']) === $this->getUserId()) {
            unset($_SESSION['userid']);
            unset($_SESSION['session_id']);
            $this->deleteSessionId($userid);
            return true;
        }
    }
    
    // Generates a random session id. 
    private function generateSessionId(): string 
    {
        $numbers = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
        $chars = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        $output = '';
        for($i=1; $i<=10; $i++) {
            $numberschars = rand(1,2);
            if($numberschars === 1) {
                $numbers_rand = rand(0, 9);
                $output .= (string) $numbers[$numbers_rand];
            }
            if($numberschars === 2) {
                $chars_rand = rand(0, 25);
                $output .= $chars[$chars_rand];
            }
        }
        return $output;
    }
    
    // Saves a given session id for special user in the database.
    private function saveSessionId(int $userid, string $session_id): bool 
    {
        $query1 = sprintf("SELECT * FROM `alumni_sessions` WHERE `userid` = %d",
                $userid);
        if(is_array($response1 = $this->dbclient->fetchAll($query1))) {
            $this->deleteSessionId($userid);
        }
        $query = sprintf("INSERT INTO `alumni_sessions`(userid, session_id) VALUES (%d, '%s')",
                $userid,
                $session_id);
        $this->dbclient->execute($query);
        return true;
    }
    
    // Returns the session id of a given user.
    private function getSessionId(int $userid): string|bool
    {
        $query = sprintf("SELECT `session_id` FROM `alumni_sessions` WHERE `userid` = %d",
                $userid);
        $response = $this->dbclient->fetch($query);
        return $response['session_id'];
    }
    
    // Deletes the session id of a given user when logging out.
    private function deleteSessionId(int $userid): bool 
    {
        $query = sprintf("DELETE FROM `alumni_sessions` WHERE `userid` = %d",
                $userid);
        $this->dbclient->execute($query);
        return true;
    }
    
    // Checks the database if 2fa is enabled for a given user.
    public function checkIfTwofactorEnabled(int $userid): bool 
    {
        $query = sprintf("SELECT `2fa` FROM `alumni_users` WHERE `userid` = %d",
                $userid);
        $response = $this->dbclient->fetch($query);
        if($response['2fa'] === 1) {
            return true;
        }
        else {
            return false;
        }
    }
    
    // Generates a random new password.
    private function generateNewPassword(): string 
    {
        $numbers = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
        $chars = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        $output = '';
        for($i=1; $i<=10; $i++) {
            $numberschars = rand(1,2);
            if($numberschars === 1) {
                $numbers_rand = rand(0, 9);
                $output .= (string) $numbers[$numbers_rand];
            }
            if($numberschars === 2) {
                $chars_rand = rand(0, 25);
                $output .= $chars[$chars_rand];
            }
        }
        return strtolower($output);
    }
    
    // Saves a random generated password for a user in the database and sends an email with the new password to the user.
    public function sendNewPassword(string $username): bool 
    {
        if(!$this->checkIfUsernameAlreadyExists($username)) {
            return false;
        }
        else {
            $this->getUserByUsername($username);
            $password = $this->generateNewPassword();
            $message = sprintf("Liebe/-r %s, du hast ein neues Passwort angefordert. Dein neues Passwort lautet: '%s' Bitte melde dich online mit diesem Passwort an "
                    . "und lege im Persönlichen Bereich ein neues Passwort fest. Wenn du diese Mail nicht angefordert hast, kannst du diese ignorieren.",
                    $username,
                    $password);
            if(mail($this->email, 'Dein neues Passwort', $message, 'From: Alumni-Netzwerk Gymnasium Melle')) {
                $this->overwriteTwofactor($this->userid);
                $this->setNewPassword($this->userid, $password);
                $this->updateLoginTries($username, true);
                return true;
            }
            else {
                return false;
            }    
        }
    }
}