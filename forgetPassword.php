<?php
include 'constants.php';
include_once(__DIR__.'/vendor/autoload.php');
include_once(__DIR__.'/src/User.php');
include_once(__DIR__.'/src/Alert.php');
include_once(__DIR__.'/src/Logs.php');

use src\User;
use src\Alert;
use src\Logs;

$user = new User();
$alert = new Alert();
$logs = new Logs();

if(session_status() === PHP_SESSION_NONE) {
    session_start();
}
if($user->authenticateWithSession()) {
    require 'index.php';
    exit();
}

$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);

if(!is_null($username)) {
    if($user->sendNewPassword($username)) {
        $success_message = $alert->successAlert('Das Passwort wurde erfolgreich verschickt. Prüfe auch deinen Spam-Ordner!');
    }
    else {
        $success_message = $alert->dangerAlert('Das Versenden des neuen Passworts ist fehlgeschlagen. Probieren Sie es später erneut oder wenden Sie sich an den Administrator.');
    }
}

include 'header.php';
?>

            <div class="container">
            <?php
            if(isset($success_message)) {
                print($success_message);
            } ?>
            <div class="container mt-5 justify-content-center align-items-center" style="max-width: 500px; display: flex">
                <div class="login-form" style="width: 100%; border: 0.5px solid #ccc; border-radius: 10px; padding: 40px">
                    <h2>Passwort vergessen</h2>
                    <div style="margin-top: 20px"></div>
                    <p>Wenn du dein Passwort vergessen hast, kannst du hier deinen Benutzernamen eingeben, um ein neues Passwort zugesendet zu bekommen.</p>
                    <div style="margin-top: 20px"></div>
                    <form action="<?php print($_SERVER['PHP_SELF']) ?>" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-user"></i> Benutzername:
                            </label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div style="margin-top: 20px;"></div>
                        <button type="submit" id="submit" name="submit" class="btn btn-primary">Absenden</button>
                    </form>
                </div>
              </div>
            </div>

            <div style="margin-top: 50px;"></div>

            <script>
                document.title = 'Passwort vergessen | Alumni-Datenbank'
            </script>
<?php
include 'footer.php';
