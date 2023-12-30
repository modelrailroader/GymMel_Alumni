<?php
include 'constants.php';
include_once(__DIR__.'/vendor/autoload.php');
include_once(__DIR__.'/src/Logs.php');
include_once(__DIR__.'/src/Backup.php');

use src\User;

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = new User();
if(!$user->authenticateWithSession()) {
    require 'accessDenied.php';
    exit();
}
include 'header.php';
?>

            <div class="container mt-5">
            <?php if(isset($success_message)) { print($success_message); } ?>
            <h1>Backup erstellen</h1>
            <div style="margin-top: 40px"></div>
            <p>Auf dieser Seite kann ein Backup der Datenbank heruntergeladen werden.
            </br>Für den Import des Backups beachten Sie bitte die Dokumentation.</p>
            <div style="margin-top: 30px"></div>

            <a href="downloadBackup.php"><button type="submit" class="btn btn-primary">Backup herunterladen</button></a>
            
            </div>
            

        <div style="margin-top: 50px;"></div>
        <script>
            document.title = 'Backup erstellen | Alumni-Datenbank';
        </script>
<?php
include 'footer.php';

