<?php
namespace src;

use A1phanumeric\DBPDO;

class DataHelper
{
    private DBPDO $dbclient;
    
    public function __construct() {
        include dirname(__DIR__, 1) . '/constants.php';
        $this->dbclient = new DBPDO($db_host, $db_name, $db_user, $db_password);
    }

    // Returns an array of the entire Alumni data.
    public function getAllAlumniData(): array
    {
        $query = "SELECT `name`, `email`, `studies`, `job`, `company`, `date_registered`, `transfer_privacy`, `id` FROM `alumni_data`";
        return $this->dbclient->fetchAll($query);
    }
    
    // Returns an array of the Alumni data of a special person defined by it's id.
    public function getAlumniData(int $id): array 
    {
        $query = sprintf("SELECT `name`, `email`, `studies`, `job`, `company`, `date_registered`, `transfer_privacy`, `id` FROM `alumni_data` WHERE `id`=%d",
                         $id);
        return $this->dbclient->fetch($query);
    }
    
    // Returns 'checked' if the transfer_privacy of a given data-array is true.
    public function getTransferPrivacyFromData(array $data): string 
    {
        if($data['transfer_privacy'] === 1) {
            return 'checked';
        }
        else {
            return '';
        }
    }
    
    // Saves new data in the database.
    public function updateData(array $data): bool 
    {
        if($this->getAlumniData($data['id'])[0]['transfer_privacy'] !== $data['transfer_privacy']) {
            if($data['transfer_privacy'] === 1) {
                $transfer_privacy_agreed = ",`date_transfer_privacy_agreed`='" . time() . "'";
            }
            else {
                $transfer_privacy_agreed = ",`date_transfer_privacy_agreed`=0";
            }
        }
        else {
            $transfer_privacy_agreed = '';
        }
        $query = sprintf("UPDATE `alumni_data` SET `id` = %d, `name` = '%s', `email` = '%s', `studies` = '%s', "
                    . "`job` = '%s', `company` = '%s', `transfer_privacy` = %d %s WHERE `id` = %d",
                    $data['id'],
                    $data['name'],
                    $data['email'],
                    $data['studies'],
                    $data['job'],
                    $data['company'],
                    $data['transfer_privacy'],
                    $transfer_privacy_agreed,
                    $data['id']);
        return (bool) $this->dbclient->execute($query);
    }
    
    // Deletes a special person defined by it's id.
    public function deleteAlumniById(int $id): bool 
    {
        $query = sprintf("DELETE FROM `alumni_data` WHERE `id` = %d",
                $id);
        $this->dbclient->execute($query);
        return true;
    }
    
    // Creates a new alumni and saves it's data in the database.
    public function saveNewAlumni(string $name, string $email, string $studies, string $job, string $company, int $transfer) 
    {
        $query = sprintf("INSERT INTO `alumni_data`(name, email, studies, job, company, date_registered, transfer_privacy, date_transfer_privacy_agreed) "
            . "VALUES ('%s', '%s', '%s', '%s', '%s', %d, %d, %d)",
            $name,
            $email,
            $studies,
            $job,
            $company,
            time(),
            $transfer,
            ($transfer===1) ? time() : null);
    $this->dbclient->execute($query);
    }
}

