<?php
include 'constants.php';
include_once(__DIR__.'/vendor/autoload.php');
include_once(__DIR__.'/src/User.php');
include_once(__DIR__.'/src/DataHelper.php');
include_once(__DIR__.'/src/Alert.php');
include_once(__DIR__.'/src/Logs.php');

use src\User;
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

$logs = new Logs();

$alert = new Alert();

$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = filter_input(INPUT_POST, 'password');
$twofactor = filter_input(INPUT_POST, '2fa');

if(!is_null($username) && !is_null($password) && !is_null($email)) {
    if($user->createUser($username, $password, $email, ($twofactor === 'on') ? 1 : 0)) {
        $success_message = $alert->successAlert('Der Benutzer wurde erfolgreich erstellt!');
    }
    else {
        $success_message = $alert->dangerAlert('Dieser Benutzername existiert bereits!');
    }
    $logs->addLogEntry('The user ' . $username . ' was successfully created.');
}

include 'header.php';
?>
            
            <div class="container mt-5">
            <?php if(isset($success_message)) { print($success_message); } ?>
            <h1>Benutzer erstellen</h1>
            <div style="margin-top: 40px"></div>
                <form action="<?php print($_SERVER['PHP_SELF']) ?>" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">
                            <i class="bi bi-person"></i>&nbsp; Benutzername:
                        </label>
                        <div class="required-field-block">
                            <input type="text" class="form-control" id="username" name="username" required>
                            <div class="required-icon">
                                <div class="text">*</div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="bi bi-envelope-at"></i>&nbsp; E-Mail-Adresse:
                        </label>
                        <div class="required-field-block">
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="required-icon">
                                <div class="text">*</div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3" id="passwordDiv">
                      <label for="password" class="form-label">
                            <i class="bi bi-key"></i>&nbsp; Passwort:
                      </label>
                      <div class="required-field-block">
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="required-icon">
                            <div class="text">*</div>
                        </div>
                        <small id="helpTextPassword" name="helpTextPassword" style="color: red"></small>
                      </div>
                    </div>
                    <div class="mb-3" id="confirmPasswordDiv">
                      <label for="confirmPassword" class="form-label">
                            <i class="bi bi-key"></i>&nbsp; Passwort bestätigen:
                      </label>
                      <div class="required-field-block">
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                        <div class="help-block with-errors"></div>
                        <div class="required-icon">
                            <div class="text">*</div>
                        </div>
                        <small id="helpTextConfirmPassword" name="helpTextConfirmPassword" style="color: red"></small>
                      </div>
                    </div>
                    <div class="mb-3">
                        <input type="checkbox" class="form-check-input" id="2fa" name="2fa">
                        <label class="form-check-label" for="2fa">2-Faktor-Authentifizierung</label>
                    </div>
                    <div style="margin-top: 20px"></div>

                    <button type="submit" id="submit" name="submit" class="btn btn-primary">Benutzer erstellen</button>
                </form>
            </div>

        <div style="margin-top: 50px;"></div>
        <script src="assets/dist/main.js"></script>
        <script>
            document.title = 'Benutzer erstellen | Alumni-Datenbank';
        </script>

<?php
include 'footer.php';

