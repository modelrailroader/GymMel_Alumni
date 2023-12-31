<?php
include 'constants.php';
include_once(__DIR__ . '/vendor/autoload.php');
include_once(__DIR__ . '/src/User.php');
include_once(__DIR__ . '/src/Alert.php');
include_once(__DIR__ . '/src/Logs.php');

use src\User;
use src\Alert;
use src\Logs;

$alert = new Alert();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = new User();
if (!$user->authenticateWithSession()) {
    require 'accessDenied.php';
    exit();
}

$logs = new Logs();

$id_get = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (isset($id_get)) {
    $data = $user->getUserDataById($id_get);
}

$id = filter_input(INPUT_POST, 'userid', FILTER_VALIDATE_INT);
if (isset($id)) {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $twofactor_active = filter_input(INPUT_POST, '2fa');
    if (filter_input(INPUT_POST, 'newPassword') === 'on') {
        $password = filter_input(INPUT_POST, 'password');
        $user->setNewPassword($id, $password);
        $logs->addLogEntry('The password of user ' . $username . ' was successfully changed.');
    }
    if (filter_input(INPUT_POST, 'new_2fa') === 'on') {
        $user->overwriteTwofactor($id);
        $logs->addLogEntry('The 2fa of user ' . $username . ' was successfully deleted.');
    }
    if($username === $user->getUserDataById($id)['username']) {
        $user->updateUserData($id, $username, $email, ($twofactor_active === null) ? 0 : 1);
        $success_message = $alert->successAlert('Die Benutzerdaten wurden erfolgreich geändert!');
        $logs->addLogEntry('The user data of user ' . $username . ' were successfully changed.');
    }
    else {
        if($user->checkIfUsernameAlreadyExists($username)) {
           $success_message = $alert->dangerAlert('Dieser Benutzername existiert bereits!');
        }
        else {
            $user->updateUserData($id, $username, $email, ($twofactor_active === null) ? 0 : 1);
            $success_message = $alert->successAlert('Die Benutzerdaten wurden erfolgreich geändert!');
            $logs->addLogEntry('The user data of user ' . $username . ' were successfully changed.');
        }
    }
    $data = $user->getUserDataById($id);
}

include 'header.php';
?>

            <div class="container mt-5">
                <?php
                if (isset($success_message)) {
                    print($success_message);
                }
                ?>
                <h1>Benutzer bearbeiten</h1>
                <div style="margin-top: 40px"></div>
                <form action="<?php print($_SERVER['PHP_SELF']) ?>" method="post">
                    <input type="hidden" value="<?php
                    if (isset($id_get)) {
                        print($id_get);
                    } else {
                        print($id);
                    }
                    ?>" id="userid" name="userid">
                    <div class="mb-3">
                        <label for="username" class="form-label">
                            <i class="bi bi-person"></i>&nbsp; Benutzername:
                        </label>
                        <div class="required-field-block">
                            <input type="text" class="form-control" id="username" name="username" value="<?php print($data['username']) ?>" required>
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
                            <input type="text" class="form-control" id="email" name="email" value="<?php print($data['email']) ?>" required>
                            <div class="required-icon">
                                <div class="text">*</div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <input type="checkbox" class="form-check-input" id="newPassword" name="newPassword">
                        <label class="form-check-label" for="newPassword">Neues Passwort?</label>
                    </div>
                    <div class="mb-3" id="passwordDiv" style="display: none">
                        <label for="password" class="form-label">
                            <i class="bi bi-key"></i>&nbsp; Passwort:
                        </label>
                        <div class="required-field-block">
                            <input type="password" class="form-control" id="password" name="password">
                            <div class="required-icon"><div class="text">*</div></div>
                        </div>
                        <small id="helpTextPassword" name="helpTextPassword" style="color: red"></small>
                    </div>
                    <div class="mb-3" id="confirmPasswordDiv" style="display: none">
                        <label for="confirmPassword" class="form-label">
                            <i class="bi bi-key"></i>&nbsp; Passwort bestätigen:
                        </label>
                        <div class="required-field-block">
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword">
                            <div class="required-icon"><div class="text">*</div></div>
                        </div>
                        <small id="helpTextConfirmPassword" name="helpTextConfirmPassword" style="color: red"></small>
                    </div>
                    <div class="mb-3">
                        <input type="checkbox" class="form-check-input" id="2fa" name="2fa" <?php
                    if ($data['2fa'] === 1) {
                        print('checked');
                    }
                    ?>>
                        <label class="form-check-label" for="2fa">2-Faktor-Authentifizierung</label>
                    </div>
                    <?php
                    if ($data['2fa'] === 1) {
                        print('
                    <div class="mb-3">
                        <input type="checkbox" class="form-check-input" id="new_2fa" name="new_2fa">
                        <label class="form-check-label" for="new_2fa">Aktuelle 2-Faktor-Authentifizierung löschen?</label>
                    </div>');
                    }
                    ?>
                    <div style="margin-top: 20px"></div>

                    <button type="submit" id="submit" name="submit" class="btn btn-primary">Speichern</button>
                </form>
            </div>

            <div style="margin-top: 50px;"></div>
            <script src="assets/dist/main.js"></script>

<?php
include 'footer.php';
