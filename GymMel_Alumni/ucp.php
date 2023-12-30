<?php
include 'constants.php';
include_once(__DIR__ . '/vendor/autoload.php');
include_once(__DIR__ . '/src/User.php');
include_once(__DIR__ . '/src/DataHelper.php');
include_once(__DIR__ . '/src/Alert.php');
include_once(__DIR__ . '/src/Logs.php');

use src\User;
use src\Alert;
use src\Logs;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = new User();
if (!$user->authenticateWithSession()) {
    require 'accessDenied.php';
    exit();
}

$logs = new Logs();
$alert = new Alert();

$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = filter_input(INPUT_POST, 'password');
$twofactor = filter_input(INPUT_POST, '2fa');

if (!is_null($username) && !is_null($email) && !is_null($password)) {
    if ($user->getUsername() !== $username) {
        if ($user->checkIfUsernameAlreadyExists($username)) {
            $success_message = $alert->dangerAlert('Dieser Benutzername existiert bereits!');
        } 
        else {
            $user->updateUserData($user->getUserId(), $username, $email, ($twofactor === 'on') ? 1 : 0);
            $user->updateUserPassword($user->getUserId(), $password);
            $logs->addLogEntry('The user has changed his user data in the ucp.');
            $success_message = $alert->successAlert('Die Änderungen waren erfolgreich!');
        }
    } 
    else {
        $user->updateUserData($user->getUserId(), $username, $email, ($twofactor === 'on') ? 1 : 0);
        $user->updateUserPassword($user->getUserId(), $password);
        $logs->addLogEntry('The user has changed his user data in the ucp.');
        $success_message = $alert->successAlert('Die Änderungen waren erfolgreich!');
    }
    $user->getUserById($user->getUserId());
}

include 'header.php';
?>
            <div class="container mt-5">
                <?php
                if (isset($success_message)) {
                    print($success_message);
                }
                ?>
                <h1>Persönlicher Bereich</h1>
                <div style="margin-top: 40px"></div>
                <form action="<?php print($_SERVER['PHP_SELF']) ?>" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">
                            <i class="bi bi-person"></i>&nbsp; Benutzername:
                        </label>
                        <div class="required-field-block">
                            <input type="text" class="form-control" id="username" name="username" value="<?php print($user->getUsername()) ?>" required>
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
                            <input type="email" class="form-control" id="email" name="email" value="<?php print($user->getEmailAdress()) ?>" required>
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
                            <div class="required-icon">
                                <div class="text">*</div>
                            </div>
                            <small id="helpTextConfirmPassword" name="helpTextConfirmPassword" style="color: red"></small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <input type="checkbox" class="form-check-input" id="2fa" name="2fa" <?php
                if ($user->isTwofactorEnabled()) {
                    print('checked');
                }
                ?>>
                        <label class="form-check-label" for="2fa">2-Faktor-Authentifizierung</label>
                    </div>
                    <div class="mb-3" id="divTwofactorConfig">
                        <button type="button" class="btn btn-primary" id="twofactorConfigButton">2-Faktor-Authentifizierung konfigurieren</button>
                    </div>
                    <div style="margin-top: 20px"></div>

                    <button type="submit" id="submit" name="submit" class="btn btn-primary">Speichern</button>
                </form>

                <!-- Twofactor Config Modal -->
                <div class="modal fade" id="twofactorConfig" tabindex="-1" aria-labelledby="twofactor_configLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="twofactor_configLabel">2-Faktor-Authentifizierung konfigurieren</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <img class="rounded mx-auto d-block" src="<?php print($user->getQrCode()) ?>" width="200" height="200" alt="QR-Code Secret"/>
                                <p class="text-center">Secret-Key: <?php print($user->getSecret()) ?></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Speichern</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top: 50px;"></div>

            <script>
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

                const passwordInput = document.getElementById('password');
                const helpTextPassword = document.getElementById('helpTextPassword');
                const submitButton = document.getElementById('submit');

                passwordInput.addEventListener('keyup', function () {
                    helpTextPassword.textContent = validatePassword(passwordInput.value);
                });

                const confirmPasswordInput = document.getElementById('confirmPassword');
                const helpTextConfirmPassword = document.getElementById('helpTextConfirmPassword');

                const twofactorConfigButton = document.getElementById('twofactorConfigButton');

                twofactorConfigButton.addEventListener('click', function () {
                    $('#twofactorConfig').modal('show');
                });

                const divTwofactorConfig = document.getElementById('divTwofactorConfig');
                const twofactorActive = document.getElementById('2fa');

                twofactorActive.addEventListener('change', function () {
                    if (this.checked) {
                        divTwofactorConfig.style.display = 'block';
                    } else {
                        divTwofactorConfig.style.display = 'none';
                    }
                });

                if (twofactorActive.checked) {
                    divTwofactorConfig.style.display = 'block';
                } else {
                    divTwofactorConfig.style.display = 'none';
                }
                
                submitButton.addEventListener('click', function() {
                    if(validatePassword(passwordInput.value) !== '') {
                        event.preventDefault();
                    }
                    if(passwordInput.value !== confirmPasswordInput.value) {
                        helpTextConfirmPassword.textContent = 'Die Passwörter stimmen nicht überein.';
                        event.preventDefault();
                    }
                });
                
                document.title = 'Persönlicher Bereich | Alumni-Datenbank';
            </script>
<?php
include 'footer.php';

