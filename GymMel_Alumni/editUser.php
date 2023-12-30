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

            <script>
                const newPassword = document.getElementById('newPassword');
                const passwordDiv = document.getElementById('passwordDiv');
                const confirmPasswordDiv = document.getElementById('confirmPasswordDiv');
                const submitButton = document.getElementById('submit');
                const helpTextConfirmPasswordInput = document.getElementById('helpTextConfirmPassword');
                const helpTextPasswordInput = document.getElementById('helpTextPassword');

                newPassword.addEventListener('change', function () {
                    if (this.checked) {
                        passwordDiv.style.display = 'block';
                        confirmPasswordDiv.style.display = 'block';
                        if (helpTextConfirmPasswordInput.textContent !== '' || helpTextPasswordInput.textContent !== '') {
                            submitButton.disabled = true;
                        }
                    } else {
                        passwordDiv.style.display = 'none';
                        confirmPasswordDiv.style.display = 'none';
                        submitButton.disabled = false;
                    }
                });

                const twofactor_active = document.getElementById('2fa');
                const twofactor_new = document.getElementById('new_2fa');

                if (twofactor_new) {
                    twofactor_new.addEventListener('change', function () {
                        if (this.checked) {
                            twofactor_active.checked = false;
                        }
                    });

                    twofactor_active.addEventListener('change', function () {
                        if (this.checked) {
                            twofactor_new.checked = false;
                        }
                    });
                }

                const confirmPasswordInput = document.getElementById('confirmPassword');
                const passwordInput = document.getElementById('password');

                function validatePassword(password) {
                    if (password.length < 8) {
                        return 'Dein Passwort muss mindestens 8 Zeichen enthalten.';
                    }
                    else if (!/[A-Z]/.test(password)) {
                        return 'Dein Passwort muss Groß- und Kleinbuchstaben enthalten.';
                    }
                    else if (!/[0-9]/.test(password)) {
                        return 'Dein Passwort muss mindestens 1 Ziffer enthalten.';
                    }
                    else if (!/[^A-Za-z0-9]/.test(password)) {
                        return 'Dein Passwort muss mindestens 1 Sonderzeichen enthalten.';
                    }
                    else {
                        return '';
                    }
                }

                passwordInput.addEventListener('keyup', function () {
                    helpTextPasswordInput.textContent = validatePassword(passwordInput.value);
                });
                
                submitButton.addEventListener('click', function() {
                    if(newPassword.checked) {
                        if(validatePassword(passwordInput.value) !== '') {
                            event.preventDefault();
                        }
                        if(passwordInput.value !== confirmPasswordInput.value) {
                            helpTextConfirmPasswordInput.textContent = 'Die Passwörter stimmen nicht überein.';
                            event.preventDefault();
                        } 
                    }
                    
                });
                
                document.title = 'Benutzer bearbeiten | Alumni-Datenbank';
            </script>

<?php
include 'footer.php';
