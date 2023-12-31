<?php
include 'constants.php';
include_once(__DIR__.'/vendor/autoload.php');
include_once(__DIR__.'/src/User.php');
include_once(__DIR__.'/src/DataHelper.php');
include_once(__DIR__.'/src/Alert.php');
include_once(__DIR__.'/src/Logs.php');

use src\User;
use src\DataHelper;
use src\Alert;
use src\Logs;

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = new User();
if(!$user->authenticateWithSession()) {
    require 'accessDenied.php';
    exit();
}

$dataHelper = new DataHelper();
$alert = new Alert();
$logs = new Logs();

$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS);
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if($action === 'delete') {
    $username_deleted = $user->getUserDataById($id)['username'];
    $user->deleteUser($id);
    $success_message = $alert->successAlert('Der Benutzer wurde erfolgreich gelöscht!');
    $logs->addLogEntry('The user ' . $username_deleted . ' was successfully deleted.');
}

include 'header.php';
?>
            
            <div class="container mt-5">
            <?php 
            if (isset($success_message)) {
                print($success_message);
            }
            ?>
            <h1>Benutzerübersicht</h1>
            <div style="margin-top: 40px"></div>
            
            <div class="d-flex justify-content-end" style="margin-bottom: 20px">
                <a href="createUser.php"><button class="btn btn-primary">Benutzer erstellen</button></a>
            </div>

            <table id="usersTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th data-priority="4">User-ID</th>
                        <th data-priority="1">Benutzername</th>
                        <th data-priority="2">E-Mail-Adresse</th>
                        <th>2-Faktor-Authentifizierung</th>
                        <th>Zuletzt eingeloggt</th>
                        <th data-priority="3" data-orderable="false">Bearbeiten</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    print($user->renderTree());
                    ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 50px;"></div>
        
        <footer class="text-black text-center py-3" id="background-color-1">
            <p>Created by Jan Harms | <?php print(date('Y')) ?> &copy; <a href="https://www.melle-gymnasium.de">Gymnasium Melle</a> | <a href="https://www.melle-gymnasium.de/kontakt/#impressum">Impressum</a> | <a href="privacy.php">Datenschutz</a></p>
        </footer>
        <script src="assets/dist/main.js"></script>
    </body>
</html>

