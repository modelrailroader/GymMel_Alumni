<?php
/**
 * Backup class to create a backup of the database.
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

use PDO;

class Backup 
{
    private PDO $dbclient;
    
    private array $tables = array();
    
    private string $filename = 'backup.sql';
    
    public function __construct() {
        include dirname(__DIR__, 1) . '/constants.php';
        $this->dbclient = new PDO("mysql:host=$db_host;dbname=$db_name; charset=utf8", $db_user, $db_password);
    }
    
    // Author: https://gist.github.com/fotan/4accf4587e93c6bf4062
    // Creates a temporary backup file, that is later used to provide the backup file for download. 
    private function createTemporaryBackupFile(): bool
    {
        $this->dbclient->setAttribute(PDO::ATTR_ORACLE_NULLS, PDO::NULL_NATURAL);

        //create/open files
        $handle = fopen($this->filename, 'a+');

        //array of all database field types which just take numbers
        $numtypes = array('tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'float', 'double', 'decimal', 'real');

        //get all of the tables
        if (empty($this->tables)) {
            $pstm1 = $this->dbclient->query('SHOW TABLES');
            while ($row = $pstm1->fetch(PDO::FETCH_NUM)) {
                $this->tables[] = $row[0];
            }
        } else {
            $this->tables = is_array($this->tables) ? $this->tables : explode(',', $this->tables);
        }

        //cycle through the table(s)

        foreach ($this->tables as $table) {
            $result = $this->dbclient->query("SELECT * FROM $table");
            $num_fields = $result->columnCount();
            $num_rows = $result->rowCount();

            $return = "";
            //uncomment below if you want 'DROP TABLE IF EXISTS' displayed
            $return .= 'DROP TABLE IF EXISTS `' . $table . '`;';

            //table structure
            $pstm2 = $this->dbclient->query("SHOW CREATE TABLE $table");
            $row2 = $pstm2->fetch(PDO::FETCH_NUM);
            $ifnotexists = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $row2[1]);
            $return .= "\n\n" . $ifnotexists . ";\n\n";

            fwrite($handle, $return);

            $return = "";

            //insert values
            if ($num_rows) {
                $return = 'INSERT INTO `' . "$table" . "` (";
                $pstm3 = $this->dbclient->query("SHOW COLUMNS FROM $table");
                $count = 0;
                $type = array();

                while ($rows = $pstm3->fetch(PDO::FETCH_NUM)) {

                    if (stripos($rows[1], '(')) {
                        $type[$table][] = stristr($rows[1], '(', true);
                    } else
                        $type[$table][] = $rows[1];

                    $return .= "`" . $rows[0] . "`";
                    $count++;
                    if ($count < ($pstm3->rowCount())) {
                        $return .= ", ";
                    }
                }

                $return .= ")" . ' VALUES';

                fwrite($handle, $return);

                $return = "";
            }
            $count = 0;
            while ($row = $result->fetch(PDO::FETCH_NUM)) {
                $return = "\n\t(";

                for ($j = 0; $j < $num_fields; $j++) {

                    //$row[$j] = preg_replace("\n","\\n",$row[$j]);


                    if (isset($row[$j])) {

                        //if number, take away "". else leave as string
                        if ((in_array($type[$table][$j], $numtypes)) && (!empty($row[$j])))
                            $return .= $row[$j];
                        else
                            $return .= $this->dbclient->quote($row[$j]);
                    } else {
                        $return .= 'NULL';
                    }
                    if ($j < ($num_fields - 1)) {
                        $return .= ',';
                    }
                }
                $count++;
                if ($count < ($result->rowCount())) {
                    $return .= "),";
                } else {
                    $return .= ");";
                }
                
                fwrite($handle, $return);

                $return = "";
            }
            $return = "\n\n-- ------------------------------------------------ \n\n";
            fwrite($handle, $return);

            $return = "";
        }
            
        return fclose($handle);
    }
    
    // Returns the content of the temporarily created backup file.
    public function getBackupStream(): string 
    {
        if($this->createTemporaryBackupFile()) {
            return file_get_contents($this->filename);
        }
    }
    
    // Deletes temporarily created backup file
    public function deleteTemporaryBackupFile(): bool 
    {
        return unlink($this->filename);
    }
    
    // Returns the filename of the backup file.
    public function getFilename(): string 
    {
        return $this->filename;
    }
}

