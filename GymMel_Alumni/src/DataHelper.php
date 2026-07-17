<?php
/**
 * DataHelper class for creating, editing and deleting network data.
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

use A1phanumeric\DBPDO;
use DateTime;

class DataHelper
{
    private DBPDO $dbclient;

    public function __construct()
    {
        include dirname(__DIR__, 1) . '/constants.php';
        $this->dbclient = new DBPDO($db_host, $db_name, $db_user, $db_password);
    }

    // Returns an array of the entire Alumni data.
    public function getAllAlumniData(): array
    {
        $query = "SELECT `name`, `email`, `birthday`, `graduation_year`, `studies`, `job`, `company`, `date_registered`, `transfer_privacy`, `id` FROM `alumni_data`";
        return $this->dbclient->fetchAll($query);
    }

    // Returns an array of the Alumni data of a special person defined by it's id.
    public function getAlumniData(int $id): array
    {
        $query = sprintf("SELECT `name`, `email`, `birthday`, `graduation_year`, `studies`, `job`, `company`, `date_registered`, `transfer_privacy`, `id` FROM `alumni_data` WHERE `id`=%d",
            $id);
        return $this->dbclient->fetch($query);
    }

    public function checkIfIdExists(int $id)
    {
        $query = sprintf("SELECT `id` FROM `alumni_data` WHERE `id`=%d",
            $id
        );
        return $this->dbclient->fetch($query) !== null;
    }

    // Saves new data in the database.
    public function updateData(array $data): bool
    {
        if ($this->getAlumniData($data['id'])['transfer_privacy'] !== $data['transfer_privacy']) {
            if ($data['transfer_privacy'] === 1) {
                $transfer_privacy_agreed = ",`date_transfer_privacy_agreed`='" . time() . "'";
            } else {
                $transfer_privacy_agreed = ",`date_transfer_privacy_agreed`=0";
            }
        } else {
            $transfer_privacy_agreed = '';
        }
        $query = sprintf("UPDATE `alumni_data` SET `id` = %d, `name` = '%s', `email` = '%s', `birthday` = '%s',"
            . "`graduation_year` = %d, `studies` = '%s', `job` = '%s', `company` = '%s',"
            . "`transfer_privacy` = %d, `date_last_changed` = '%s' %s WHERE `id` = %d",
            $data['id'],
            $data['name'],
            $data['email'],
            $data['birthday'],
            $data['graduation_year'],
            $data['studies'],
            $data['job'],
            $data['company'],
            $data['transfer_privacy'],
            date('Y-m-d H:i:s', time()),
            $transfer_privacy_agreed,
            $data['id']);
        return (bool)$this->dbclient->execute($query);
    }

    // Deletes a special person defined by it's id.
    public function deleteAlumniById(int $id): bool
    {
        $query = sprintf("DELETE FROM `alumni_data` WHERE `id` = %d",
            $id);
        return (bool)$this->dbclient->execute($query);
    }

    // Creates a new alumni and saves it's data in the database.
    public function saveNewAlumni(string $name, string $email, string $birthday, int $graduation_year, string $studies, string $job, string $company, int $transfer): bool
    {
        $query = sprintf("INSERT INTO `alumni_data`(name, email, birthday, graduation_year, studies, job, company, date_registered, transfer_privacy, date_transfer_privacy_agreed) "
            . "VALUES ('%s', '%s', '%s', %d, '%s', '%s', '%s', %d, %d, %d)",
            $name,
            $email,
            $birthday,
            $graduation_year,
            $studies,
            $job,
            $company,
            time(),
            $transfer,
            ($transfer === 1) ? time() : null);
        return (bool)$this->dbclient->execute($query);
    }

    public function findDuplicates(): array
    {
        $query = 'SELECT name, email FROM alumni_data';
        $data = $this->dbclient->fetchAll($query);

        $duplicates = [];
        $checkedItems = [];

        foreach ($data as $item) {
            $name = $item['name'];
            $email = $item['email'];
            $key = $name . '|' . $email;

            if (in_array($key, $checkedItems) && !in_array($item, $duplicates)) {
                $duplicates[] = $item;
            } else {
                $checkedItems[] = $key;
            }
        }
        return $duplicates;
    }

    public function getDuplicateDetails(array $duplicate): array
    {
        $query = sprintf(
            "SELECT id, name, email, birthday, graduation_year, job, studies, company, date_registered FROM alumni_data WHERE name='%s' AND email='%s'",
            $duplicate['name'],
            $duplicate['email']
        );
        return $this->dbclient->fetchAll($query);

    }

    public function mergeDuplicates(array $duplicates, int $alumniId): bool
    {
        $success = [];
        foreach ($duplicates as $alumni) {
            if (is_array($this->getAlumniData($alumni))) {
                if ($alumni !== (string)$alumniId) {
                    $success[] = $this->deleteAlumniById($alumni);
                }
            } else {
                return false;
            }
        }
        return !in_array(false, $success);
    }

    public function requestEmailTokenForDataChange(int $id): bool
    {
        $alumniData = $this->getAlumniData($id);

        // Generate token and save it to database
        $token = $this->generateEmailToken();
        $this->saveTokenToDatabase($token, $id);
        $_SESSION['tokenCreated'] = time();

        $emailBody = sprintf(
            "<p>Du möchtest deine bei der Alumni-Datenbank hinterlegten Daten ändern oder löschen und hast einen Verifizierungscode angefordert.</p>"
            . "<p>Dein Code lautet:</p><p style='font-size: 28px; margin-left: 30px'><b>%s</b></p><p>Dein Code ist 10 Minuten gültig.</p><p>Du hast diesen Code nicht angefordert? Dann kannst du diese E-Mail einfach ignorieren.</p>",
            $token
        );
        $emailAltBody = sprintf(
            "Du möchtest deine bei der Alumni-Datenbank hinterlegten Daten ändern oder löschen und hast einen Verifizierungscode angefordert.\n\n"
            . "Dein Code lautet:\n"
            . "%s\n\n"
            . "Dein Code ist 10 Minuten gültig.\n\n"
            . "Du hast diesen Code nicht angefordert? Dann kannst du diese E-Mail einfach ignorieren.",
            $token
        );

        $mailer = new Mail();
        $mailer->addAddress($alumniData['email'], $alumniData['name']);
        $mailer->addSubject('Dein Verifizierungscode');
        $mailer->addBody($emailBody, true);
        $mailer->addAltBody($emailAltBody);
        return $mailer->send();
    }

    private function saveTokenToDatabase(string $token, int $id): bool
    {
        $query = sprintf("UPDATE `alumni_data` SET `token` = '%s', `token_generation_time` = '%s' WHERE `id` = %d",
            $token,
            date('Y-m-d H:i:s', time()),
            $id
        );
        return (bool)$this->dbclient->execute($query);
    }

    private function generateEmailToken(): string
    {
        return sprintf('%03d-%03d', random_int(0, 999), random_int(0, 999));
    }

    public function verifyEmailToken(int $id, string $token): bool
    {
        $query = sprintf('SELECT `token`, `token_generation_time` FROM `alumni_data` WHERE `id` = %d',
            $id
        );
        $response = $this->dbclient->fetch($query);

        $tokenGenerationTime = new DateTime($response['token_generation_time']);

        // Token is valid for 10 minutes (600s)
        if ($token === $response['token'] && (time() - $tokenGenerationTime->getTimestamp() < 600)) {
            $_SESSION['id'] = $id;
            $_SESSION['alumniVerified'] = true;
            $_SESSION['verificationTime'] = time();
            return true;
        } else {
            return false;
        }
    }

    public function getIdByEmail(string $email): int|false
    {
        $query = sprintf("SELECT `id` FROM `alumni_data` WHERE `email` = '%s'",
                $email
        );
        $response = $this->dbclient->fetch($query);

        return is_null($response) ? false : (int)$response['id'];
    }

    public function checkIfAlumniIsLoggedInForDataChange(?int $id = null): bool
    {
        if (is_null($id)) {
            // If there is already an id saved in session, continue checking
            if (isset($_SESSION['id']) && $this->checkIfIdExists($_SESSION['id'])) {
                $id = $_SESSION['id'];
            } else {
                // If not, alumni is not logged in for data change
                return false;
            }
        }
        if (isset($_SESSION['alumniVerified']) && $_SESSION['alumniVerified']) {
            if ($_SESSION['id'] === $id) {
                // Alumni is logged out after 30 minutes (1800s)
                if (time() - $_SESSION['verificationTime'] < 1800) {
                    return true;
                }
            }
        }
        return false;
    }

    public function anonymizeEmailAddress(string $email): string
    {
        [$localPart, $domain] = explode('@', $email, 2);

        if (strlen($localPart) <= 3) {
            $maskedLocalPart = substr($localPart, 0, 1) . str_repeat('*', max(1, strlen($localPart) - 1));
        } else {
            $maskedLocalPart = substr($localPart, 0, 3) . str_repeat('*', strlen($localPart) - 3);
        }

        return $maskedLocalPart . '@' . $domain;
    }
}
